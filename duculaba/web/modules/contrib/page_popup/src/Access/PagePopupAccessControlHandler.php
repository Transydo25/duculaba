<?php

namespace Drupal\page_popup\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access controller for the page_popup_entity entity type.
 */
class PagePopupAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  public function checkAccess(EntityInterface $entity, $operation, AccountInterface $account = NULL) {
    $account = $this->prepareUser($account);

    // Check the global permission.
    if ($account->hasPermission('administer page popup')) {
      return AccessResult::allowed();
    }

    if ($operation == 'view' && $account->hasPermission('view page popup Entity')) {
      return AccessResult::allowed();
    }
    elseif ($operation == 'update' && $account->hasPermission('edit page popup Entity')) {
      return AccessResult::allowed();
    }
    elseif ($operation == 'delete' && $account->hasPermission('delete page popup Entity')) {
      return AccessResult::allowed();
    }
    return AccessResult::forbidden();
  }

}
