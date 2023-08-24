/** @format */

(function ($, Drupal, drupalSettings) {
  "use strict";

  function countTotal(status, operation = '+') {
    status = (status + '').replace(' ', '');
    let selectorTotal = $('.status-' + status + ' .total-status');
    let total = parseInt(selectorTotal.text());
    if (operation == '+') {
      total += 1;
    } else {
      total -= 1;
    }
    selectorTotal.text(total);
  }

  function countPoint(status, point, operation = '+') {
    status = (status + '').replace(' ', '');
    point = parseInt(point);
    let selectorTotal = $('.status-' + status + ' .card-header .total .badge');
    let total = parseInt(selectorTotal.text());
    if (operation == '+') {
      total += point;
    } else {
      total -= point;
    }
    selectorTotal.text(total);
  }

  Drupal.behaviors.Kanban = {
    attach: function attach(context) {
      $(once('Kanban', ".views-view-kaban", context))
        .each(function () {
          let kanbanHeight = $(".views-view-kaban").height();
          $(".panel-body [droppable=true]").css("min-height", kanbanHeight - 180 + "px");
          if(drupalSettings.views_kanban !== undefined && drupalSettings.views_kanban.permission_drag){
            draggableInit();
          }
          // Detect variable to open
          let params = new window.URLSearchParams(window.location.search);
          if(params.get('kanbanTicket')) {
            $('#viewkanban' + params.get('kanbanTicket')).click();
          }
        });

      function draggableInit() {
        let entityId, type, currentStatus, currentDrag;

        $(".views-view-kaban [draggable=true]").bind("dragstart", function (event) {
          entityId = $(this).data("id");
          type = $(this).data("type");
          currentStatus = $(this).data("value");
          currentDrag = $(this).attr('id');
          countTotal(currentStatus, '-');
          countPoint(currentStatus, $(this).data("point"), '-');
          event.originalEvent.dataTransfer.setData("text/plain", event.target.getAttribute("id"));
        });

        $(".views-view-kaban .panel-body").bind("dragover", function (event) {
          $(this).addClass("bg-info");
          event.preventDefault();
        });
        $(".views-view-kaban .panel-body").bind("dragleave", function () {
          $(this).removeClass("bg-info");
        });

        $(".views-view-kaban [droppable=true]").bind("drop", function (event) {
          let view_kanban = $(this).closest(".views-view-kaban");
          let view_id = view_kanban.data("view_id");
          let display_id = view_kanban.data("display_id");
          let stateValue = $(this).data("value");

          let spinners =
            '<div class="spinners d-flex justify-content-center" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="' + Drupal.t('Loading') + '…' + '">' +
            '<div class="spinner-border" role="status"><span class="sr-only"></span></div>' +
            '</div>';
          if (currentStatus != stateValue) {
            let elementId = event.originalEvent.dataTransfer.getData("text/plain");

            $(this).prepend(spinners);
            //before post
            if (type && entityId && stateValue) {
              $('#' + currentDrag).data('value', stateValue);
              // Generate URL for AJAX call.
              let url = "views-kanban/update-state/" + view_id + "/" + display_id + "/" + entityId + "/" + stateValue;
              let article = $("#" + elementId).detach();
              $(this).prepend(article);
              $(this).parent().removeClass("bg-info");
              countTotal(stateValue, '+');
              countPoint(stateValue, article.data('point'), '+');
              let that = $(this);
              $.ajax({
                url: Drupal.url(url),
                success: function (result) {
                  that.find(".spinners").remove();
                },
                error: function (xhr, status, error) {
                  alert(
                    Drupal.t("An error occurred during the update of the entity. Please consult the watchdog.")
                  );
                },
              });
            }
          }

          event.preventDefault();
        });
      }
    },
  };
})(jQuery, Drupal, drupalSettings);
