<?php

namespace Drupal\sms_message\Plugin\Action;

use Drupal\Core\Action\Plugin\Action\UnpublishAction;

/**
 * Delete sms message action.
 *
 * @Action(
 *   id = "sms_message_sent_action",
 *   label = @Translation("Mark sent SMS"),
 *   type = "sms_message"
 * )
 */
class SmsMarkSentAction extends UnpublishAction {
}
