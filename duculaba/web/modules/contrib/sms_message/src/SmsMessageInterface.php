<?php

namespace Drupal\sms_message;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining a sms message entity type.
 */
interface SmsMessageInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
