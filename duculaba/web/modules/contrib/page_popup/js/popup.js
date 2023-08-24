/**
 * @file
 * Jquery code.
 */

(function ($, Drupal, drupalSettings) {
  'use strict';

  let popupStatus = 0;

  /**
   * Disabling popup with jQuery.
   */
  function popup_message_disable_popup() {
    // Disables popup only if it is enabled.
    /*if (popupStatus === 1) {*/
      jQuery('#popup-message-background').fadeOut('slow');
      jQuery('#popup-message-window').fadeOut('slow');
      jQuery('#popup-message-content').empty().remove();
   /*   popupStatus = 0;
    }*/
  }

  /**
   * Display popup message.
   *
   * @param {string} popup_message_title
   *   Message title.
   * @param {string} popup_message_body
   *   Message body.
   * @param {number} width
   *   Message box width.
   * @param {number} height
   *   Message box height.
   */
  function popup_message_display_popup(popup_message_title, popup_message_body , text_color, bg_color, layout, delay, width, height, font_size) {
    let windowWidth = document.documentElement.clientWidth;
    let windowHeight = document.documentElement.clientHeight;
    if (!popup_message_title.trim()) {
      // is empty or whitespace
      jQuery('body').append("<div id='popup-message-window'><a id='popup-message-close'>X</a><div id='popup-message-content'>" + popup_message_body + "</div></div><div id='popup-message-background'></div>");
    }
    else {
      jQuery('body').append("<div id='popup-message-window'><a id='popup-message-close'>X</a><h1 class='popup-message-title'>" + popup_message_title + "</h1><div id='popup-message-content'>" + popup_message_body + "</div></div><div id='popup-message-background'></div>");
    }
    // Inject layout class.
    switch (layout) {
      // Center.
      case '0':
        $('#popup-message-window').addClass('popup_center')
        $('#popup-message-window').css({
          'top': windowHeight / 2 - height / 2,
          'left': windowWidth / 2 - width / 2
        });
        break
      // Top left.
      case '1':
        $('#popup-message-window').addClass('popup_top_left')
        break
      // Top right.
      case '2':
        $('#popup-message-window').addClass('popup_top_right')
        break
      // Bottom left.
      case '3':
        $('#popup-message-window').addClass('popup_bottom_left')
        break
      // Bottom right.
      case '4':
        $('#popup-message-window').addClass('popup_bottom_right')
        break
    }
    $('#popup-message-window').css({
      'position' : 'absolute',
      'width': width + 'px',
      'height': height + 'px',
      'color': text_color,
      'background-color': bg_color,
      'font-size' : font_size,
      'top': windowHeight / 2 - height / 2,
      'left': windowWidth / 2 - width / 2
    });
    $('#popup-message-window h1.popup-message-title').css({
      'color': text_color
    });
    // Loading popup.
    //popup_message_load_popup();
    if (delay > 0 && delay !== '' && delay !== null) {
      var delays = delay * 1000;
      setTimeout(function () {
        jQuery('#popup-message-background').fadeIn('slow');
      }, delays);
      $('#popup-message-window').delay(delays).fadeIn('slow')
    }
    else if (delay == 0) {
      jQuery('#popup-message-background').fadeIn('slow');
      jQuery('#popup-message-window').fadeIn('slow');
    }
    // Closing popup.
    // Click the x event!
    jQuery('#popup-message-close').click(function () {
      popup_message_disable_popup();
    });
    // Click out event!
    jQuery('#popup-message-background').click(function () {
      popup_message_disable_popup();
    });
  }

  Drupal.behaviors.pagePopup = {
    attach: function (context) {
      jQuery('body', context).once('popupMessageBehavior').each(function () {
        let popup_message_title = drupalSettings.pagePopup.title;
        let popup_message_body = drupalSettings.pagePopup.body;
        let text_color = drupalSettings.pagePopup.text_color;
        let bg_color = drupalSettings.pagePopup.bg_color;
        let layout = drupalSettings.pagePopup.layout;
        let delay = drupalSettings.pagePopup.delay;
        let width = drupalSettings.pagePopup.width;
        let height = drupalSettings.pagePopup.height;
        let font_size = drupalSettings.pagePopup.font_size;
        popup_message_display_popup(popup_message_title,popup_message_body, text_color, bg_color, layout, delay, width, height, font_size);
      });
    }
  };

})(jQuery, Drupal, drupalSettings);
