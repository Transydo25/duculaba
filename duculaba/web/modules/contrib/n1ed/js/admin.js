/**
 * @file
 * 'n1ed' plugin admin behavior.
 */
(function () {
  "use strict";

  var elInputIsN1EDEnabled;

  var elCheckBoxHtmlFilter;
  var isHtmlFilterInitiallyEnabled;

  var elCheckBoxPlainFilter;
  var isPlainFilterInitiallyEnabled;

  // Always recalculate this element - Drupal recreates it
  /*function getElEnableImageUploadInCKEditor5() {
    return document.querySelector(
        '[data-drupal-selector="edit-editor-settings-plugins-ckeditor5-imageupload-status"]'
    );
  }
  var isImageUploadEnabledInCKEditor5;*/

  function isN1EDCurrentlyEnabled() {
    return elInputIsN1EDEnabled.value != "false";
  }

  function getCurrentApiKey() {
    return window.drupalSettings.n1edApiKey;
  }

  function getCurrentToken() {
    return window.drupalSettings.n1edToken;
  }

  function onChange(
    isEnabled,
    apiKey,
    token,
    integrationType
  ) {
    window.drupalSettings.n1edApiKey = apiKey;
    window.drupalSettings.n1edToken = token;
    window.drupalSettings.n1edIntegrationType = integrationType;

    elInputIsN1EDEnabled.value = isEnabled ? "true" : "false";

    if (elCheckBoxHtmlFilter) {
      if (isEnabled) {
        elCheckBoxHtmlFilter.disabled = true;
        elCheckBoxHtmlFilter.checked = false;
      } else {
        elCheckBoxHtmlFilter.disabled = false;
        elCheckBoxHtmlFilter.checked = isHtmlFilterInitiallyEnabled;
      }
    }

    if (elCheckBoxPlainFilter) {
      if (isEnabled) {
        elCheckBoxPlainFilter.disabled = true;
        elCheckBoxPlainFilter.checked = false;
      } else {
        elCheckBoxPlainFilter.disabled = false;
        elCheckBoxPlainFilter.checked = isPlainFilterInitiallyEnabled;
      }
    }

    /*try {
      if (getElEnableImageUploadInCKEditor5()) {
        if (isEnabled) {
          if (!getElEnableImageUploadInCKEditor5().checked) {
            getElEnableImageUploadInCKEditor5().click(); // click, do not just check
          }
          getElEnableImageUploadInCKEditor5().disabled = true; // disable after click
          setTimeout(function () {
            getElEnableImageUploadInCKEditor5().disabled = true; // disable after click, wait for Ajax finished
          }, 1000); // probably 1 sec is enough
        } else {
          getElEnableImageUploadInCKEditor5().disabled = false; // enable before click
          if (
              (getElEnableImageUploadInCKEditor5().checked && !isImageUploadEnabledInCKEditor5) ||
              (!getElEnableImageUploadInCKEditor5().checked && isImageUploadEnabledInCKEditor5)
          ) {
            getElEnableImageUploadInCKEditor5().click(); // click, do not just uncheck
          }
        }
      }
    } catch (e) {
      console.error(e);
    }*/

  }

  function getCookie(name) {
    var a = document.cookie.match("(^|;)\\s*" + name + "\\s*=\\s*([^;]+)");
    return a ? a.pop() : null;
  }

  function getCSRFToken(onSuccess, onError) {
    var req = new XMLHttpRequest();

    req.onreadystatechange = function () {
      if (req.readyState == XMLHttpRequest.DONE) {
        if (req.status == 200) {
          onSuccess({
            headers: {
              "X-CSRF-Token": req.responseText,
            },
          });
        } else {
          onError();
        }
      }
    };

    req.open("GET", window.drupalSettings.path.baseUrl + "session/token", true);
    req.send();
  }

  function attachCmsConf() {

    if (!window.n1edEcoControlPanelLoaded) {
      elInputIsN1EDEnabled = document.querySelector(
          '[data-n1ed-eco-param-name="enableN1EDEcoSystem"]'
      );

      elCheckBoxHtmlFilter = document.querySelector(
          ".js-form-item-filters-filter-html-status > input"
      );
      isHtmlFilterInitiallyEnabled =
          elCheckBoxHtmlFilter && elCheckBoxHtmlFilter.checked;

      elCheckBoxPlainFilter = document.querySelector(
          ".js-form-item-filters-filter-html-escape-status > input"
      );
      isPlainFilterInitiallyEnabled =
          elCheckBoxPlainFilter && elCheckBoxPlainFilter.checked;

      /*isImageUploadEnabledInCKEditor5 =
          getElEnableImageUploadInCKEditor5() && getElEnableImageUploadInCKEditor5().checked;*/

      // For updating HTML filter checkbox
      onChange(isN1EDCurrentlyEnabled(), getCurrentApiKey(), getCurrentToken());

      window.n1edEcoControlPanelLoaded = true;

      var elFieldSet = document.getElementById("editor-settings-wrapper");
      var elConf = document.createElement("div");
      elConf.setAttribute("id", "n1ed-conf");
      elConf.setAttribute(
          "style",
          "border: 1px solid #c0c0c0; border-radius: 3px;padding: 70px; display: flex; justify-content: center; align-items: center; background-color: #fcfcfa"
      );

      elConf.innerHTML =
          '<svg xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.0" width="16px" height="16px" viewBox="0 0 128 128" xml:space="preserve"><g><path d="M75.4 126.63a11.43 11.43 0 0 1-2.1-22.65 40.9 40.9 0 0 0 30.5-30.6 11.4 11.4 0 1 1 22.27 4.87h.02a63.77 63.77 0 0 1-47.8 48.05v-.02a11.38 11.38 0 0 1-2.93.37z" fill="#007FFF"/><animateTransform attributeName="transform" type="rotate" from="0 64 64" to="360 64 64" dur="800ms" repeatCount="indefinite"></animateTransform></g></svg>' +
          '<div style="margin-left:10px">Loading the control panel...</div>';

      elFieldSet.parentElement.insertBefore(elConf, elFieldSet);

      var elScript = document.createElement("script");

      // Notice about cookies: developers use it to specify debug server to use,
      // all other users will use old known n1ed.com address
      var prefix = getCookie("N1ED_PREFIX");
      elScript.src =
          "//" + (prefix ? prefix + "." : "") + "n1ed.com/js/n1ed-cms-conf-3.js";

      elScript.addEventListener("load", function () {
        elConf.setAttribute("style", "");
        elConf.innerHTML = "";

        window.attachN1EDCmsConf({
          el: elConf,
          urlSetApiKeyAndToken:
              window.drupalSettings.path.baseUrl + "admin/config/n1ed/setApiKey",
          urlSetApiKeyAndToken__CSRF: getCSRFToken,
          apiKey: getCurrentApiKey(),
          token: getCurrentToken(),
          editorName: window.drupalSettings.n1edEditor,
          integration: "drupal8",
          integrationType: integrationType,
          isCheckBoxN1EDEcoEnabledVisible: true,
          checkBoxN1EDEcoEnabledTitle: "Enable %INTEGRATION_TYPE_TITLE% for this text format",
          checkBoxN1EDEcoEnabledValue: isN1EDCurrentlyEnabled(),
          onN1EDEcoEnabledChange: function (value, onFinished) {
            onChange(value, getCurrentApiKey(), getCurrentToken(), undefined);
            onFinished(null);
          },
          onApiKeyChange: function (apiKey, token, integrationType) {
            onChange(isN1EDCurrentlyEnabled(), apiKey, token, integrationType);
          },
          onToolbarChange: function (
              isAutoToolbar,
              allPossibleManualButtons,
              availableManualButtons,
              autoToolbars,
              autoToolbarVersion
          ) {

            var elsPanels = [];
            for (var i = 0; i < allPossibleManualButtons.length; i++) {
              var elBtn = document.querySelector("[data-drupal-ckeditor-button-name='" + allPossibleManualButtons[i].name + "']");
              if (!!elBtn) {
                var isVisible = !isAutoToolbar && !!availableManualButtons.find(function (b) {
                  return b.name === allPossibleManualButtons[i].name;
                });
                elBtn.style.display = isVisible ? 'block' : 'none';
                if (elBtn.parentElement !== null && elBtn.parentElement.tagName === "UL" && elBtn.parentElement.classList.contains("ckeditor-buttons")) {
                  elsPanels.push(elBtn.parentElement);
                }
              }
            }

            for (var i = 0; i < elsPanels.length; i ++) {
              var hasVisibleChildren = false;
              for (var j = 0; j < elsPanels[i].children.length; j ++) {
                hasVisibleChildren = hasVisibleChildren || elsPanels[i].children.item(j).style.display !== 'none';
              }
              elsPanels[i].style.display = hasVisibleChildren ? 'block' : 'none';
            }

          },

          onAttachToSaveButton: function (
              funcSave // : (onFinished: (isOk: boolean)) => void
          ) {

            var btn = document.querySelector("#edit-actions-submit"); // TODO:
            var form = document.querySelector("#filter-format-edit-form"); // TODO:
            if (!!btn && !!form) {
              btn.addEventListener("click", function (ev) {
                funcSave(
                    function (isOk) {
                      form.submit();
                    }
                );
                ev.preventDefault();
                return false;
              });
              return true;
            } else {
              return false;
            }

          }

        });


        var elFieldset = document.createElement("fieldset");
        elConf.appendChild(elFieldset);
        elFieldset.setAttribute("class", "fieldgroup form-composite js-form-item form-item js-form-wrapper form-wrapper");

        var elFieldsetWrapper = document.createElement("div");
        elFieldset.appendChild(elFieldsetWrapper);
        elFieldsetWrapper.setAttribute("class", "fieldset-wrapper");

        var elCheckboxes = document.createElement("div");
        elFieldsetWrapper.appendChild(elCheckboxes);
        elCheckboxes.setAttribute("class", "form-checkboxes");


        var elDivUseFlmngrOnFileFields = document.createElement("div");
        elCheckboxes.appendChild(elDivUseFlmngrOnFileFields);
        elDivUseFlmngrOnFileFields.className = "js-form-item form-item js-form-type-checkbox form-type-checkbox";
        elDivUseFlmngrOnFileFields.style = "margin-top: 0.4em; margin-bottom: 0.4em;";

        var elInputUseFlmngrOnFileFields = document.createElement("input");
        elDivUseFlmngrOnFileFields.appendChild(elInputUseFlmngrOnFileFields);
        elInputUseFlmngrOnFileFields.setAttribute("type", "checkbox");
        elInputUseFlmngrOnFileFields.checked = window.drupalSettings.useFlmngrOnFileFields;
        elInputUseFlmngrOnFileFields.className = "form-checkbox";
        elInputUseFlmngrOnFileFields.style = "margin-right: 5px;";

        var elLabelUseFlmngrOnFileFields = document.createElement("label");
        elDivUseFlmngrOnFileFields.appendChild(elLabelUseFlmngrOnFileFields);
        elLabelUseFlmngrOnFileFields.className = "option";
        elLabelUseFlmngrOnFileFields.innerText = "Use Flmngr for file and image fields";

        elInputUseFlmngrOnFileFields.addEventListener("change", function () {
          fetch(
              window.drupalSettings.path.baseUrl +
              "admin/config/n1ed/toggleUseFlmngrOnFileFields",
              {
                method: "POST",
                body: JSON.stringify({
                  useFlmngrOnFileFields: elInputUseFlmngrOnFileFields.checked,
                }),
              }
          );
        });


        var elDivUseFlmngrLegacyBackend = document.createElement("div");
        elCheckboxes.appendChild(elDivUseFlmngrLegacyBackend);
        elDivUseFlmngrLegacyBackend.className = "js-form-item form-item js-form-type-checkbox form-type-checkbox";
        elDivUseFlmngrLegacyBackend.style = "margin-top: 0.4em; margin-bottom: 0.4em;";

        var elInputUseFlmngrLegacyBackend = document.createElement("input");
        elDivUseFlmngrLegacyBackend.appendChild(elInputUseFlmngrLegacyBackend);
        elInputUseFlmngrLegacyBackend.setAttribute("type", "checkbox");
        elInputUseFlmngrLegacyBackend.checked = window.drupalSettings.useLegacyFlmngrBackend;
        elInputUseFlmngrLegacyBackend.className = "form-checkbox";
        elInputUseFlmngrLegacyBackend.style = "margin-right: 5px;";

        var elLabelUseFlmngrLegacyBackend = document.createElement("label");
        elDivUseFlmngrLegacyBackend.appendChild(elLabelUseFlmngrLegacyBackend);
        elLabelUseFlmngrLegacyBackend.className = "option";
        elLabelUseFlmngrLegacyBackend.innerText = "Use legacy Flmngr backend";

        elInputUseFlmngrLegacyBackend.addEventListener("change", function () {
          fetch(
              window.drupalSettings.path.baseUrl +
              "admin/config/n1ed/toggleUseLegacyFlmngrBackend",
              {
                method: "POST",
                body: JSON.stringify({
                  useLegacyFlmngrBackend: elInputUseFlmngrLegacyBackend.checked,
                }),
              }
          );
        });

      });
      elScript.addEventListener("error", function () {
        elConf.innerHTML =
            '<svg role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 352 512" width="12" height="12"><path fill="#fe5c4b" d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"></path></svg>' +
            '<div style="margin-left:10px">Unable to load N1ED configuration panel. Please try to reload page.</div>';
      });

      var elBody = document.querySelector("body");
      elBody.appendChild(elScript);
    }
  }

  var integrationType = null;
  function waitForN1EDIntegrationAndAttachCmsConf() {
    if (!!window.drupalSettings.n1edIntegrationType || !!window.n1edIntegrationType || !!window.CKEditor5) {
      integrationType = window.drupalSettings.n1edIntegrationType || window.n1edIntegrationType ||
          ("CKEditor5" in window ? "flmngr" : undefined) || "n1ed";
      attachCmsConf();
    } else
      setTimeout(function() { waitForN1EDIntegrationAndAttachCmsConf(); }, 100);
  }
  waitForN1EDIntegrationAndAttachCmsConf();

})();
