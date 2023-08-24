<?php

namespace Drupal\page_popup\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\page_popup\PagePopupEntityInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Controller routines for AJAX callbacks for domain actions.
 */
class PagePopupController extends ControllerBase {

  /**
   * The Messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * The logger factory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $logger;

  /**
   * PagePopupMessageController constructor.
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger.
   * @param \Drupal\Core\Logger\LoggerChannelInterface $logger
   *   The logger.
   */
  public function __construct(MessengerInterface $messenger, LoggerChannelInterface $logger) {
    $this->messenger = $messenger;
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('messenger'),
      $container->get('logger.factory')->get('page_popup')
    );
  }

  /**
   * Handles AJAX operations from the overview form.
   *
   * @param \Drupal\page_popup\PagePopupEntityInterface $page_popup_entity
   *   A Page Popup Message Rule object.
   * @param string|null $op
   *   The operation being performed, either 'enable' to Page Popup Message Rule
   *   or 'disable' to disable the Page Popup Message Rule.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   A redirect response to redirect back to the Page Popup Message Rule list.
   *
   * @see \Drupal\page_popup\Controller\PagePopupEntityListBuilder
   */
  public function ajaxOperation(PagePopupEntityInterface $page_popup_entity, $op = NULL) {
    $message = $this->t("The operation '%op' to '%label' failed.",
      ['%op' => $op, '%label' => $page_popup_entity->label()]
    );
    try {
      switch ($op) {
        case 'enable':
          $page_popup_entity->enable();
          $message = $this->t("Page Popup Entity '%label' has been enabled.",
            ['%label' => $page_popup_entity->label()]
          );
          break;

        case 'disable':
          $page_popup_entity->disable();
          $message = $this->t("Page Popup Rule '%label' has been disabled.",
            ['%label' => $page_popup_entity->label()]
          );
          break;
      }
      $page_popup_entity->save();
      $this->messenger->addStatus($message);
      $this->logger->notice($message);
    }
    catch (EntityStorageException $e) {
      $this->messenger->addStatus($message);
      $this->logger->error($message);
    }

    // Return to the invoking page.
    $url = Url::fromRoute('page_popup.admin', [], ['absolute' => TRUE]);
    return new RedirectResponse($url->toString(), 302);
  }

}
