<?php

namespace Drupal\views_kanban\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Drupal\Core\Entity\EntityInterface;

/**
 * Wraps a node insertion demo event for event listeners.
 */
class KanbanNodeInsertEvent extends Event {

  const KANBAN_NODE_INSERT = 'event_subscriber_kanban.node.insert';

  /**
   * Node entity.
   *
   * @var \Drupal\Core\Entity\EntityInterface
   */
  protected $entity;

  /**
   * Constructs a node insertion demo event object.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   Entity.
   */
  public function __construct(EntityInterface $entity) {
    $this->entity = $entity;
  }

  /**
   * Get the inserted entity.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   Return Entity.
   */
  public function getEntity() {
    // @todo send mail, notification to assignor.
    return $this->entity;
  }

}
