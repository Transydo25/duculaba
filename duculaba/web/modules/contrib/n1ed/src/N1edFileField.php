<?php

namespace Drupal\n1ed;

use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\Core\Url;

/**
 * Implements logic of attaching Flmngr file manager to file fields.
 */
class N1edFileField implements TrustedCallbackInterface {

  /**
   * Get trusted callbacks list.
   */
  public static function trustedCallbacks() {
    return ['preRenderWidget'];
  }

  /**
   * Processes widget form.
   */
  public static function processWidget(
    $element,
    $form_state,
    $form
  ) {
    $element['n1ed_file_field'] = [
      '#type' => 'hidden',
      '#attributes' => [
        'class' => ['n1ed-file-field-filefield'],
        'data-extensions' =>
        $element['#upload_validators']['file_validate_extensions'][0],
        'data-multiple' => $element['#multiple'] ? 1 : 0,
      ],
    ];
    $element['#attached']['library'][] = 'n1ed/drupal.n1ed.filefield';
    $element['#pre_render'][] = [get_called_class(), 'preRenderWidget'];

    return $element;
  }

  /**
   * Pre-renders widget form.
   */
  public static function preRenderWidget($element) {
    // Hide elements if there is already an uploaded file.
    if (!empty($element['#value']['fids'])) {
      $element['n1ed_file_field']['#access'] = FALSE;
    }
    return $element;
  }

}
