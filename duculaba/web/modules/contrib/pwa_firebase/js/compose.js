(function ($, Drupal) {

  Drupal.behaviors.pwa_firebase_compose = {
    attach: function () {
      $("#checkAll").change(function () {
        $(this).closest('details').find(".form-type--checkbox:visible input:checkbox").prop('checked', $(this).prop("checked"));
      });

      $(".phonebook_search").keyup(function () {
        var txt = $(this).val();
        $(this).closest('details').find('.form-type--checkbox').hide();
        $(this).closest('details').find('.form-type--checkbox').each(function () {
          if ($(this).text().toUpperCase().indexOf(txt.toUpperCase()) != -1) {
            $(this).show();
          }
        });
      });
    }
  };
}(jQuery, Drupal));
