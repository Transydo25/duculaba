<?php

namespace Drupal\views_kanban\EventSubscriber;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\views_kanban\Event\KanbanNodeInsertEvent;

/**
 * Logs the creation of a new node.
 */
class KanbanNodeInsertSubscriber implements EventSubscriberInterface {

  use StringTranslationTrait;

  /**
   * The message to send.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  private $messenger;

  /**
   * Service date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  private $dateFormatter;

  /**
   * Logger Factory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactory
   */
  protected $loggerFactory;

  /**
   * KabanEventSubscriber constructor.
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The message to send notification.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   Date formatter service.
   * @param \Drupal\views_kanban\EventSubscriber\LoggerChannelFactory $loggerFactory
   *   Logger Service.
   */
  public function __construct(MessengerInterface $messenger, DateFormatterInterface $date_formatter, LoggerChannelFactory $loggerFactory) {
    $this->loggerFactory = $loggerFactory->get('event_subscriber_kanban');
    $this->messenger = $messenger;
    $this->dateFormatter = $date_formatter;
  }

  /**
   * Log the creation of a new node.
   *
   * @param \Drupal\views_kanban\Event\KanbanNodeInsertEvent $event
   *   Event.
   */
  public function onKanbanNodeInsert(KanbanNodeInsertEvent $event) {
    // @todo send mail notification when ticket have change status.
    $entity = $event->getEntity();
    $this->loggerFactory->notice(
      'New @type: @title. Created by: @owner',
      [
        '@type' => $entity->getType(),
        '@title' => $entity->label(),
        '@owner' => $entity->getOwner()->getDisplayName(),
      ]
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KanbanNodeInsertEvent::KANBAN_NODE_INSERT][] = ['onKanbanNodeInsert'];
    return $events;
  }

}
