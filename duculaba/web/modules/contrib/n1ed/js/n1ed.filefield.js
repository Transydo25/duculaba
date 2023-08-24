(function (Drupal) {
  "use strict";

  /**
    * @file
    * Integrates Flmngr into file field widgets.
    */

  /**
    * Flmngr reusable instance (API object)
    */
  var flmngr = null;

  /**
   * Listeners waiting for standalone Flmngr initialization
   */
  var onFlmngrAndImgPenLoadedListeners = [];

  /**
   * Drupal behavior to handle Flmngr widget file field integration.
   */

  Drupal.behaviors.n1edFileField = {
    attach: function (elRoot, settings) {

      if (!!flmngr) {

        // Flmngr is already initialized
        attachWidget();

      } else {

        // Need to initialize Flmngr - from CKEditor or standalone

        var ckeditorInstance = null;
        if (window.CKEDITOR && window.CKEDITOR.instances) {
          for (var i=0; i<window.CKEDITOR.instances.length && !ckeditorInstance; i++)
            ckeditorInstance = window.CKEDITOR.instances[i];
        }

        if (ckeditorInstance) {

          // Get Flmngr from the first CKEditor instance
          flmngr = ckeditorInstance.getFlmngr();
          attachWidget();

        } else {

          // Add JSs manually
          if (!window.onFlmngrAndImgPenLoadedArray)
            window.onFlmngrAndImgPenLoadedArray = [];
          window.onFlmngrAndImgPenLoadedArray.push(function () {

            flmngr = window.flmngr.create({
              integration: "drupal8",
              urlFileManager: window.drupalSettings.n1ed.Flmngr.urlFileManager,
              urlFileManager__CSRF: getCSRFToken,
              urlFiles: window.drupalSettings.n1ed.Flmngr.urlFiles,
            });
            for (var i=0; i<onFlmngrAndImgPenLoadedListeners.length; i++)
              onFlmngrAndImgPenLoadedListeners[i]();
            onFlmngrAndImgPenLoadedListeners = [];

          });
          onFlmngrAndImgPenLoadedListeners.push(function() {
            attachWidget();
          });


          // Fix for: https://www.drupal.org/project/n1ed/issues/3290918
          // Some forms do not have $form['#attached']['drupalSettings'] on the backend
          // so we are unable to attach N1ED/Flmngr due to no API key would never arrive to here.
          // See fix for same bug in "n1ed.module"
          if (!!window.drupalSettings.n1ed && !!window.drupalSettings.n1ed.apiKey) {

            // Fix for: https://www.drupal.org/project/n1ed/issues/3111919
            // Do not start URL with "https:" prefix.
            // Notice about cookies: developers use it to specify debug server to use,
            // all other users will use old known cloud.n1ed.com address.
            var url_base = '//cloud.flmngr.com/cdn/' + window.drupalSettings.n1ed.apiKey + '/';
            includeJS(url_base + 'flmngr.js');
            includeJS(url_base + 'imgpen.js');

          }

        }

      }

      function attachWidget() {
        elRoot.querySelectorAll(".n1ed-file-field-filefield")
            .forEach(function (elWidget) {

              if (!elWidget.classList.contains("iff-processed")) {
                elWidget.classList.add("iff-processed");

                var widget;
                var fieldId = elWidget.getAttribute("data-drupal-selector").split("-n1ed-file-field")[0];
                if (elWidget.parentNode.parentNode.querySelector("label")) {
                  elWidget.parentNode.parentNode.querySelector("label").removeAttribute("for");
                } else if (elWidget.parentNode.parentNode.parentNode.querySelector("label")) {
                  elWidget.parentNode.parentNode.parentNode
                      .querySelector("label")
                      .removeAttribute("for");
                }

                if (fieldId) {
                  var widget = createWidget(
                      elWidget.dataset.extensions,
                      fieldId,
                      elWidget.dataset.multiple == 1
                  );
                  elWidget.parentNode.prepend(widget);
                  widget.parentNode.className += " n1ed-file-field-filefield-parent";
                }
              }

            });

      }

      function includeJS(urlJS, doc, callback) {
        if (!doc) {
          doc = document;
        }
        var scripts = doc.getElementsByTagName("script");
        var alreadyExists = false;
        var existingScript = null;
        for (var i = 0; i < scripts.length; i++) {
          var src = scripts[i].getAttribute("src");
          if (src && src.indexOf(urlJS) !== -1) {
            alreadyExists = true;
            existingScript = scripts[i];
          }
        }
        if (!alreadyExists) {
          var script = doc.createElement("script");
          script.type = "text/javascript";
          if (callback != null) {
            if (script.readyState) {
              // IE
              script.onreadystatechange = function () {
                if (
                  script.readyState === "loaded" ||
                  script.readyState === "compvare"
                ) {
                  script.onreadystatechange = null;
                  callback(false);
                }
              };
            } else {
              // Others
              script.onload = function () {
                callback(false);
              };
            }
          }
          script.src = urlJS;
          doc.getElementsByTagName("head")[0].appendChild(script);
          return script;
        } else {
          if (callback != null) {
            callback(true);
          }
          return null;
        }
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

        req.open(
          "GET",
          window.drupalSettings.path.baseUrl + "session/token",
          true
        );
        req.send();
      }

      function createWidget(allowExtensions, fieldId, isMultiple) {
        var elBrowseButton = document.createElement("button");
        elBrowseButton.classList.add("button");
        var elBrowseButtonImg = document.createElement("img");
        elBrowseButtonImg.src ="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAAAXNSR0IArs4c6QAAAJxJREFUSEvtlcEJhTAQRJ8V/BLUTn4pWpmWYinagR0oAwoRld0VvARzzexO9oVMCl5excv9ycvgD3RAdYOtB9oo0hTRCJRGg7BJarBsza/updmm8wygg2rSQWKvgbRRkzpq4Dm9NAcSkQk+gyM7L48L3XcHJryMEc3AzwTgE0x7KqcvWXGttLQS1bJQc+XWKeyswkf7eX2ZjxBYRSs0+SQZxa4XtgAAAABJRU5ErkJggg==";
        var elBrowseButtonSpan = document.createElement("span");
        elBrowseButtonSpan.innerText = "Browse";
        elBrowseButton.append(elBrowseButtonImg);
        elBrowseButton.append(elBrowseButtonSpan);

        var fileIdCleaned = fieldId.replace("-n1ed-file-field", "");
        fileIdCleaned = fileIdCleaned.replace("edit-", "");
        var elOriginalFileInput;
        if (isMultiple) {
          elOriginalFileInput = document.querySelector(
              "input[name='files[" + fileIdCleaned.replace(/-/g, "_") + "][]']"
          );
        } else {
          elOriginalFileInput = document.querySelector(
              "input[name='files[" + fileIdCleaned.replace(/-/g, "_") + "]']"
          );
        }
        elOriginalFileInput.style.display = "none";
        elBrowseButton.addEventListener("click", function (ev) {
          ev.preventDefault();
          flmngr.pickFiles({
            isMultiple: isMultiple,
            acceptExtensions: allowExtensions.split(" "),
            onFinish: function(files) {
              onFilesPicked(fieldId, isMultiple, files.map(function(f) {

                /*
                   Do NOT do this. Such URL will go to a final page.
                   Drupal form field incorrectly detects extension of selected file
                   if it contains URL parameters inside its URL.
                   Workaround: we add its ext into the end of URL (join to a value of "no-cache" parameter).
                let ext = "";
                let i = f.url.lastIndexOf(".");
                if (i > -1)
                  ext = f.url.substr(i);
                return flmngr.getNoCacheUrl(f.url) + ext;
                 */

                return f.url;
              }));
            }
          });
        });

        var elUploadButton = document.createElement("button");
        elUploadButton.classList.add("button");
        var elUploadButtonImg = document.createElement("img");
        elUploadButtonImg.src ="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAAAXNSR0IArs4c6QAAAKJJREFUSEvtlMENgCAMRT+buImr6GQ6mptofmKTBktbDpyQk0b6XvmABYNHGczHfIINwApgz0bbExHhxws+s5KsQMOl+ZQkI7DgaUkk0HDmLhHpZ3clnqCGE3S/rbMutSctgQUnWwv4HkosQQtuCUKJJ2DOjEWPegXyTZr61LQiWgBcxmVqCTjVrIlOUe3wBObl/gXhP2++iMJI6gm9EU0oeAAkVygZ24LglAAAAABJRU5ErkJggg==";
        var elUploadButtonSpan = document.createElement("span");
        elUploadButtonSpan.innerText = "Upload";
        elUploadButton.append(elUploadButtonImg);
        elUploadButton.append(elUploadButtonSpan);

        elUploadButton.addEventListener("click", function (ev) {
          ev.preventDefault();
          var event = document.createEvent("MouseEvents");
          event.initEvent("click", true, false);
          elOriginalFileInput.dispatchEvent(event);
        });

        var result = document.createElement("div");
        result.classList.add("n1ed-file-field");
        result.append(elBrowseButton);
        result.append(elUploadButton);

        return result;
      }

      function onFilesPicked(fieldId, isMultiple, urls) {
        fieldId = fieldId.replace("-n1ed-file-field", "");
        fieldId = fieldId.replace("edit-", "");
        var promises = [];
        var dt = new DataTransfer();
        urls.map(function (url, i) {
          var p = new Promise(function (res, rej) {
            fetch(url)
                .then(function (response) {
                  return response.blob();
                })
                .then(function (blob) {
                  var filename = url.replace(/^.*[\\\/]/, "");
                  var file = new File([blob], filename, {
                    lastModified: new Date(),
                    type: blob.type,
                  });
                  dt.items.add(file);
                  res();
                });
          });
          promises.push(p);
        });
        Promise.all(promises).then(function () {
          if (isMultiple)
            document.querySelector("input[name='files[" + fieldId.replace(/-/g, "_") + "][]']").files = dt.files;
          else
            document.querySelector("input[name='files[" + fieldId.replace(/-/g, "_") + "]']").files = dt.files;
          document.querySelector("[data-drupal-selector='edit-" + fieldId + "-upload-button']").dispatchEvent(new Event("mousedown"));
        });
      }


    },
  };


})(Drupal);
