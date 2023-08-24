(function ($, Drupal, once) {
  Drupal.behaviors.address_suggestion = {
    attach: function (context, settings) {

      $(once('initiate-autocomplete', 'input.address-line1', context)).each(function () {
        var form_wrapper = $(this).closest('.js-form-wrapper');
        var form_page = $(this).closest('form');
        $(this).attr('role','presentation'); // remove display cached autocomplete
        let ui_autocomplete = $(this).data('ui-autocomplete');

        ui_autocomplete.options.select = function (event, ui) {
          event.preventDefault();
          form_wrapper.find('input.address-line1').val(ui.item.street_name);
          form_wrapper.find('input.address-line2').val(ui.item.district);
          form_wrapper.find('input.organization').val(ui.item.name);
          form_wrapper.find('input.postal-code').val(ui.item.zip_code);
          form_wrapper.find('input.locality').val(ui.item.town_name);
          form_wrapper.find('select.administrative-area').val(ui.item.state);
          if("location_field" in settings.address_suggestion && 'location' in  ui.item){
            let location_field = settings.address_suggestion.location_field;
            let type_field = settings.address_suggestion.type_field;
            let longitude = ui.item.location.longitude;
            let latitude = ui.item.location.latitude;
            if(type_field == 'geolocation'){
              form_page.find("input[name*='" + location_field + "[0][lat]']").val(latitude);
              form_page.find("input[name*='" + location_field + "[0][lng]']").val(longitude);
            }
            if(type_field == 'geofield'){
              form_page.find("input[name*='" + location_field + "[0][value][lat]']").val(latitude);
              form_page.find("input[name*='" + location_field + "[0][value][lon]']").val(longitude);
            }
          }
        };

      });
    }
  };
}(jQuery, Drupal, once));
