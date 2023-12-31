<?php

/**
 * @file
 * Theme for Kanban views.
 */

use Drupal\Component\Serialization\Json;
use Drupal\Core\Link;
use Drupal\Core\Render\Markup;
use Drupal\Core\Url;
use Drupal\state_machine\Plugin\Workflow\WorkflowState;
use Drupal\workflows\State;
use Drupal\user\Entity\User;
use Drupal\Core\Template\Attribute;

/**
 * Template preprocess views kanban.
 *
 * @param array $variables
 *   Array variable.
 */
function template_preprocess_views_view_kanban(array &$variables) {
  $view = $variables['view'];
  $rows = $variables['rows'];
  $style = $view->style_plugin;
  $options = $style->options;
  $entityTypeManager = \Drupal::entityTypeManager();
  $requestStack = \Drupal::request();
  $currentUser = \Drupal::currentUser();
  $dateFormatter = \Drupal::service('date.formatter');
  $urlGenerator = \Drupal::service('file_url_generator');
  $variables["view_id"] = $view->storage->id();
  $variables["display_id"] = $variables["view"]->current_display;

  $colors = [
    'primary',
    'warning',
    'success',
    'danger',
    'info',
    'dark',
    'secondary',
  ];

  $variables['view']->element['#attached']['library'][] = 'views_kanban/kanban';
  $variables['view']->element['#attached']['library'][] = 'core/drupal.dialog.ajax';
  $columns = [];

  // Dialog width.
  $dialog_width = !empty($options["dialog_width"]) ? $options["dialog_width"] : '80%';
  $entity_type_id = 'node';
  if (!empty($options["status_field"]) && !empty($rows)) {
    $entity = current($rows)->_entity;
    $entity_type_id = $entity->getEntityTypeId();
    $createNew = 'admin/people/create';
    $permissionEdit = $permissionAdd = 'administer users';
    $entityType = $prepopulate = '';
    if (method_exists($entity, 'getType')) {
      $entityType = $entity->getType();
      $createNew = $entity_type_id . '/add/' . $entityType;
    }
    if (!empty($entityType)) {
      $permissionAdd = 'create ' . $entityType . ' content';
      $permissionEdit = 'edit ' . $entityType . ' content';
      $permissionAnyEdit = 'edit any ' . $entityType . ' content';
      $permissionOwnEdit = 'edit own ' . $entityType . ' content';
    }
    $permissionDragDrop = FALSE;
    if ($currentUser->hasPermission($permissionEdit) ||
      (!empty($permissionOwnEdit) && $currentUser->hasPermission($permissionOwnEdit)) ||
      (!empty($permissionAnyEdit) && $currentUser->hasPermission($permissionAnyEdit))) {
      $permissionDragDrop = TRUE;
    }
    $variables['view']->element['#attached']['drupalSettings']['views_kanban']['permission_drag'] = $permissionDragDrop;
    $extractStatus = explode(':', $options["status_field"]);
    if (!empty($extractStatus[1])) {
      $options["status_field"] = $extractStatus[0];
      $options["workflow_id"] = $extractStatus[1];
    }
    $status_field = $entity->get($options["status_field"]);
    $field_status_settings = $status_field->getFieldDefinition()->getSettings();
    $status_values = [];
    if (!empty($field_status_settings["allowed_values"])) {
      $status_values = $field_status_settings["allowed_values"];
      $prepopulate = 'edit' . '[' . $options["status_field"] . '][widget][0][value]';
    }
    if (!empty($field_status_settings["target_type"]) && $field_status_settings["target_type"] == 'taxonomy_term' && !empty($field_status_settings["handler_settings"]["target_bundles"])) {
      $vid = current($field_status_settings["handler_settings"]["target_bundles"]);
      $entity_storage = $entityTypeManager->getStorage($field_status_settings["target_type"]);
      $query_result = $entity_storage->getQuery()
        ->accessCheck(FALSE)
        ->condition('vid', $vid)
        ->sort('weight', 'ASC')
        ->execute();
      // Load the terms.
      $terms = $entity_storage->loadMultiple($query_result);
      foreach ($terms as $term) {
        $status_values[$term->id()] = $term->getName();
      }
      $prepopulate = 'edit' . '[' . $options["status_field"] . '][widget][0][target_id]';
    }
    if (!empty($options["workflow_id"])) {
      $workflow = $entityTypeManager->getStorage('workflow')
        ->load($options["workflow_id"]);
      $status_values = array_map([
        State::class,
        'labelCallback',
      ], $workflow->getTypePlugin()->getStates());
    }
    $field_type = $status_field->getFieldDefinition()->getType();
    // Support Workflow.
    if ($field_type == 'workflow' && !empty($field_status_settings['workflow_type'])) {
      $workflow_type = $field_status_settings['workflow_type'];
      $states = WorkflowState::loadMultiple([], $workflow_type);
      $status_values = [];
      foreach ($states as $state) {
        if ($state->isActive() && strpos($state->id(), 'creation') === FALSE) {
          $status_values[$state->id()] = $state->label();
        }
      }
    }
    // Support State Machine.
    if ($field_type == 'state' && !empty($field_status_settings['workflow'])) {
      $workflow_manager = \Drupal::service('plugin.manager.workflow');
      $workflow = $workflow_manager->createInstance($field_status_settings['workflow']);
      $states = $workflow->getStates();
      $status_values = array_map(function (WorkflowState $state) {
        return $state->getLabel();
      }, $states);
    }
    $i = -1;
    $linkAdd = Markup::create('<i class="bi bi-plus"></i> ' . t('Add'));
    foreach ($status_values as $id => $status_value) {
      if (empty($colors[++$i])) {
        $i = 0;
      }
      $linkOptions = [
        'attributes' => ['class' => ['btn', 'btn-' . $colors[$i]]],
        'absolute' => TRUE,
        'query' => $requestStack->query->all(),
      ];
      $linkOptions['query']['destination'] = $requestStack->getRequestUri();
      if (!empty($prepopulate)) {
        $linkOptions['query'][$prepopulate] = $id;
      }
      $columns[$id] = [
        'header' => $status_value,
        'color' => $colors[$i],
        'rows' => [],
      ];
      if ($currentUser->hasPermission($permissionAdd) && $entity_type_id != 'paragraph') {
        $columns[$id]['add'] = [
          '#title' => $linkAdd,
          '#type' => 'link',
          '#attributes' => [
            'class' => ['use-ajax'],
            'data-dialog-type' => 'modal',
            'data-dialog-options' => Json::encode(['width' => $dialog_width]),
          ],
          '#url' => Url::fromUri('internal:/' . $createNew, $linkOptions),
        ];
      }
      if (!empty($options["total_field"]) && !empty($view->field[$options["total_field"]])) {
        $columns[$id]['total'] = [
          'label' => $view->field[$options["total_field"]]->options['label'],
          'value' => 0,
        ];
      }
    }
  }
  $variables['default_row_class'] = !empty($options['default_row_class']);

  $linkViewOptions = [
    'attributes' => [
      'class' => ['use-ajax', 'btn', 'btn-sm', 'btn-default'],
      'data-dialog-type' => 'modal',
      'data-dialog-options' => Json::encode(['width' => $dialog_width]),
    ],
    'absolute' => TRUE,
    'query' => $requestStack->query->all(),
  ];
  $linkViewOptions['query']['destination'] = $requestStack->getRequestUri();

  foreach ($rows as $id => $row) {
    $entity = $row->_entity;
    $getFieldStatus = $entity->get($options["status_field"]);
    $status = current($getFieldStatus->getValue());
    $statusValue = 0;
    if (!empty($status['target_id'])) {
      $statusValue = $status['target_id'];
    }
    if (!empty($status['value'])) {
      $statusValue = $status['value'];
    }
    if (!empty($columns[$statusValue]) && !empty($columns[$statusValue]['color'])) {
      $linkViewOptions['attributes']['class'][3] = 'btn-' . $columns[$statusValue]['color'];
    }
    $entity_id = $entity->id();
    $linkViewOptions['attributes']['id'] = 'viewkanban' . $entity_id;
    $entity_type = $entity->getEntityType()->id();
    $variables['rows'][$id] = [
      'entity_id' => $entity_id,
      'entity_type' => $entity_type,
      'attributes' => new Attribute(),
      'content' => [
        '#row' => $row,
        '#view' => $view,
        '#options' => ['default_field_elements' => FALSE],
        '#theme' => [
          'views_view_fields__kanban',
          'views_view_fields',
        ],
      ],
    ];
    if ($entity_type_id == 'user') {
      $authorUid = $entity->id();
    }
    if (method_exists($entity, 'getOwnerID')) {
      $authorUid = $entity->getOwnerID();
    }
    $assignValues = [];
    if (!empty($authorUid)) {
      $author = User::load($authorUid);
      $assignValues = [$authorUid];
    }
    if (!empty($author)) {
      $variables['rows'][$id]['author'] = $author->getDisplayName();
    }
    if (method_exists($entity, 'getCreatedTime')) {
      $variables['rows'][$id]['date'] = $dateFormatter->format($entity->getCreatedTime(), 'short');
    }

    $linkView = Markup::create('<i class="bi bi-eye"></i> ' . t('View'));
    $linkEdit = Markup::create('<i class="bi bi-pencil"></i> ' . t('Edit'));
    if ($entity_type_id != 'paragraph') {
      if ($entity->access('view', $currentUser)) {
        $variables['rows'][$id]['view'] = $entity->toLink($linkView, 'canonical', $linkViewOptions);
      }
      if ($entity->access('edit', $currentUser)) {
        $variables['rows'][$id]['edit'] = $entity->toLink($linkEdit, "edit-form", $linkViewOptions);
      }
    }
    elseif (\Drupal::service('module_handler')->moduleExists('paragraphs_table')) {
      $urlView = Url::fromRoute('entity.paragraphs_item.canonical', ['paragraph' => $entity->id()], $linkViewOptions);
      $variables['rows'][$id]['view'] = Link::fromTextAndUrl($linkView, $urlView);
      $urlEdit = Url::fromRoute('entity.paragraphs_item.edit_form', ['paragraph' => $entity->id()], $linkViewOptions);
      $variables['rows'][$id]['edit'] = Link::fromTextAndUrl($linkEdit, $urlEdit);
    }

    // Title.
    if (!empty($options["title_field"])) {
      $variables['rows'][$id]['title'] = $style->getFieldValue($id, $options["title_field"]);
    }

    // Total field.
    if (!empty($totalField = $options["total_field"]) && !empty($columns[$statusValue]) && is_numeric($entity->get($totalField)->value)) {
      if (empty($columns[$statusValue]['total'])) {
        $columns[$statusValue]['total']['value'] = 0;
      }
      $columns[$statusValue]['total']['value'] += $entity->get($totalField)->value;
      $variables['rows'][$id]['total'] = $entity->get($totalField)->value;
    }

    // Progress bar.
    if (!empty($options["progress_field"])) {
      $variables['rows'][$id]['progress'] = $style->getFieldValue($id, $options["progress_field"]);
    }

    if ($row_class = $style->getRowClass($id)) {
      $variables['rows'][$id]['attributes']->addClass($row_class);
    }

    if (!empty($options["assign_field"])) {
      $assignors = $style->getFieldValue($id, $options["assign_field"]);
      if (!empty($assignors) && is_numeric($assignors)) {
        $assignors = [$assignors];
      }
      if (is_array($assignors)) {
        $assignValues = array_merge($assignValues, $assignors);
        if (in_array($currentUser->id(), $assignors)) {
          $variables['rows'][$id]['attributes']->addClass('bg-light')
            ->addClass('bg-gradient');
        }
      }
      $assignValues = array_unique($assignValues);
    }
    // Get user picture.
    foreach ($assignValues as $uid) {
      $assignor = User::load($uid);
      if (empty($assignor)) {
        continue;
      }
      // Initial name.
      $extractName = explode(' ', $assignor->getDisplayName());
      $acronym = $extractName[0][0];
      if (count($extractName) > 1) {
        $acronym .= end($extractName)[0];
      }
      $assign = [
        'name' => $assignor->getDisplayName(),
        'uid' => $uid,
        'acronym' => mb_strtoupper($acronym),
      ];
      if (!empty($assignor->user_picture) && !$assignor->user_picture->isEmpty()) {
        $avatarUri = $assignor->user_picture->entity->getFileUri();
        $thumbnail = $entityTypeManager->getStorage('image_style')
          ->load('thumbnail');
        $thumbnailAvatar = $thumbnail->buildUri($avatarUri);
        if (!file_exists($thumbnailAvatar)) {
          $thumbnail->createDerivative($avatarUri, $thumbnailAvatar);
        }
        $assign['avatar'] = $urlGenerator->generateAbsoluteString($thumbnailAvatar);
      }
      $variables['rows'][$id]['assign'][] = $assign;
    }
    $columns[$statusValue]['rows'][$id] = $variables['rows'][$id];
  }
  $variables['columns'] = $columns;
}
