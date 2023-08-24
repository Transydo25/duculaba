<?php

namespace Drupal\sms_message\Plugin\Action;

use Drupal\Core\Action\Plugin\Action\PublishAction;

/**
 * Delete sms message action.
 *
 * @Action(
 *   id = "sms_message_unsend_action",
 *   label = @Translation("Mark unsend SMS"),
 *   type = "sms_message"
 * )
 */
class SmsMarkUnsendAction extends PublishAction {
}
