/**
 * @file
 * The problem: CKEditor loads longer with N1ED and we need to show this
 * loading process.
 *
 * We can not implement showing starting of loading as a CKEditor plugin,
 * because any of plugins is loaded too late to show the progress,
 * so we will attach new behaviour for "Drupal.editorAttach" call and add
 * the placeholder right when we just try to load CKEditor. The new div
 * with class "n1ed_loading" will be removed by N1ED after CKEditor instance
 * is ready.
 *
 * This loading progress will be applied to CKEditor powered up with N1ED only
 * and will not affect to any other CKEditor instances (like comment forms).
 */

(function () {
  var originalFunc = Drupal.editorAttach;

  Drupal.editorAttach = function (el, format) {
    if (format.editor === "ckeditor") {
      if (
        !(
          !format.editorSettings ||
          !format.editorSettings.extraPlugins ||
          format.editorSettings.extraPlugins.indexOf("N1ED-editor") === -1 ||
          format.editorSettings.enableN1EDEcoSystem == "false"
        )
      ) {
        if (document.querySelector("#comment-form")) {
          setTimeout(function() {
            var evt = document.createEvent("HTMLEvents");
            evt.initEvent("change", false, true);
            var element = document
              .querySelector("#comment-form")
              .parentElement.querySelector(".form-select");
            if (element) {
              element.value = drupalSettings.N1EDFreeFormat
                ? drupalSettings.N1EDFreeFormat
                : "basic_html";
              element.dispatchEvent(evt);
            }
          }, 1);
        } else {
          var elStyle = document.createElement("style");
          document.getElementsByTagName("head")[0].appendChild(elStyle);
          elStyle.innerHTML =
            ".n1ed_loading {" +
            "    font-weight: bold;" +
            "    display: flex;" +
            "    justify-content: center;" +
            "    align-items: center;" +
            "    height: 200px;" +
            "    border: 1px solid #CCC;" +
            "}" +
            ".n1ed_loading img {" +
            "    margin-right: 20px;" +
            "}" +
            "" +
            ".n1ed_loading + .form-textarea-wrapper {" +
            "    display: none !important;" +
            "}" +
            ".visually-hidden { " +
            "    top: 0 !important; " +
            "}";

          if (el.parentElement) {
            var elParent = el.parentElement;

            var elLoading = document.createElement("div");
            elLoading.className = "n1ed_loading";
            var urlImage = "data:image/svg+xml,%3C%3Fxml version='1.0' encoding='UTF-8' standalone='no'%3F%3E%3Csvg xmlns:svg='http://www.w3.org/2000/svg' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' version='1.0' width='16px' height='16px' viewBox='0 0 128 128' xml:space='preserve'%3E%3Cg%3E%3Cpath d='M75.4 126.63a11.43 11.43 0 0 1-2.1-22.65 40.9 40.9 0 0 0 30.5-30.6 11.4 11.4 0 1 1 22.27 4.87h.02a63.77 63.77 0 0 1-47.8 48.05v-.02a11.38 11.38 0 0 1-2.93.37z' fill='%23007fff'/%3E%3CanimateTransform attributeName='transform' type='rotate' from='0 64 64' to='360 64 64' dur='800ms' repeatCount='indefinite'%3E%3C/animateTransform%3E%3C/g%3E%3C/svg%3E";
            elLoading.innerHTML =
              '<img src="' + urlImage + '" style="width:16px;height:16px"/> Editor is loading...';
            elParent.parentElement.insertBefore(elLoading, elParent);
          }
        }
      }
    }

    originalFunc.call(Drupal.editorAttach, el, format);
  };
})();
