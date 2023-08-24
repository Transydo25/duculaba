<?php

namespace Drupal\pwa_firebase\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Template\Attribute;

/**
 * Provides a block for user disable notification.
 *
 * @Block(
 *   id = "notification_manager",
 *   admin_label = @Translation("Notification permission"),
 *   category = @Translation("User")
 * )
 */
class NotificationManagerBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = \Drupal::config('pwa_firebase.settings');
    $id = 'message';
    if (!empty($config->get('firebase_message_id'))) {
      $id = str_replace('#', '', $config->get('firebase_message_id'));
    }
    $att = new Attribute();
    $att['id'] = $id;
    $att['class'] = ['notification_manager'];
    return [
      '#theme' => 'firebase_notification',
      '#attributes' => $att,
    ];
  }

}
