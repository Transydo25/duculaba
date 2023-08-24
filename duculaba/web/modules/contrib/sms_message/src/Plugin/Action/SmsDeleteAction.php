<?php

namespace Drupal\sms_message\Plugin\Action;

use Drupal\Core\Action\Plugin\Action\DeleteAction;

/**
 * Delete sms message action.
 *
 * @Action(
 *   id = "sms_message_delete_action",
 *   label = @Translation("Delete SMS"),
 *   type = "sms_message"
 * )
 */
class SmsDeleteAction extends DeleteAction {

  /**
   * {@inheritdoc}
   */
  public function executeMultiple(array $entities) {
    /** @var \Drupal\Core\Entity\EntityInterface[] $entities */
    foreach ($entities as $entity) {
      $entity->delete();
    }
  }

}
