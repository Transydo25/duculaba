<?php

namespace Drupal\views_kanban\Controller;

use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Url;
use Drupal\state_machine\Plugin\Workflow\WorkflowState;
use Drupal\views\Views;
use Drupal\workflows\State;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Kanban Controller.
 */
class KanbanController extends ControllerBase {

  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * The mail manager.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mailManager;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * The file URL generator.
   *
   * @var \Drupal\Core\File\FileUrlGeneratorInterface
   */
  protected $fileUrlGenerator;

  /**
   * {@inheritDoc}
   */
  public function __construct(DateFormatter $date_formatter, MailManagerInterface $mail_manager, RendererInterface $renderer, FileUrlGeneratorInterface $file_url_generator) {
    $this->dateFormatter = $date_formatter;
    $this->mailManager = $mail_manager;
    $this->renderer = $renderer;
    $this->fileUrlGenerator = $file_url_generator;
    $this->entityTypeManager = $this->entityTypeManager();
    $this->currentUser = $this->currentUser();
    $this->moduleHandler = $this->moduleHandler();
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('date.formatter'),
      $container->get('plugin.manager.mail'),
      $container->get('renderer'),
      $container->get('file_url_generator'),
    );
  }

  /**
   * Update state.
   *
   * @param string $view_id
   *   View id.
   * @param string $display_id
   *   Display id.
   * @param int $entity_id
   *   Entity id.
   * @param string $state_value
   *   State value.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Return json.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function updateState($view_id, $display_id, $entity_id = 0, $state_value = '') {
    $message = NULL;
    $data = [
      'success' => FALSE,
      'message' => $message,
    ];
    if (!$state_value && !is_numeric($entity_id)) {
      return new JsonResponse($data);
    }
    $view = Views::getView($view_id);
    $handler = $view->getHandler($display_id, 'filter', 'type');
    $entity_type = !empty($handler['entity_type']) ? $handler['entity_type'] : 'user';
    $style_plugin = $view->display_handler->getPlugin('style');
    $status_field = $style_plugin->options["status_field"];
    $entity = $this->entityTypeManager->getStorage($entity_type)
      ->load($entity_id);
    $message = $this->getHistoryMessage($entity, $status_field, $state_value);
    $statusName = $this->getStatusName($entity, $status_field, $state_value);

    if (!array_key_exists($state_value, $this->getAllowedValues($entity, $status_field))) {
      $data['message'] = $this->t(
        'New state @state is not a valid', ['@state' => $state_value]
      );
      return new JsonResponse($data);
    }
    $extractStatus = explode(':', $status_field);
    if (!empty($extractStatus[1])) {
      $status_field = $extractStatus[0];
    }
    // Save new status.
    $entity->set($status_field, $state_value);

    // Save history.
    if (!empty($historyFieldName = $style_plugin->options["history_field"])) {
      $historyField = $entity->get($style_plugin->options["history_field"]);
      $historyType = $historyField->getFieldDefinition()->getType();
      $historyValue = $this->dateFormatter->format(strtotime('now')) . ' ' . $message;
      if ($historyType == 'double_field') {
        $historyValue = [
          'first' => date('Y-m-d\TH:i:s'),
          'second' => $message,
        ];
      }
      $entity->$historyFieldName->appendItem($historyValue);
    }

    $entity->save();
    $url = Url::fromRoute(implode('.', [
      'view',
      $view_id,
      $view->current_display,
    ]));
    // Send email, notification to assignor.
    if (!empty($style_plugin->options["send_email"]) ||
      !empty($style_plugin->options["send_notification"])) {
      $user = $this->entityTypeManager->getStorage('user')
        ->load($this->currentUser->id());
      $email = $user->getEmail();
      $name = $user->getDisplayName();
      $author_initial = implode('', array_map(function ($v) {
        return $v[0];
      },
        explode(' ', $name)));
      $assignValues[$uid = $entity->getOwnerID()] = $uid;
      if (!empty($style_plugin->options["assign_field"])) {
        $assignors = $entity->get($style_plugin->options["assign_field"])
          ->getValue();
        foreach ($assignors as $assignor) {
          $assignValues[$assignor['target_id']] = $assignor['target_id'];
        }
      }
      $author_avatar = '';
      if (!empty($user->user_picture) && !$user->user_picture->isEmpty()) {
        $avatarUri = $user->user_picture->entity->getFileUri();
        $thumbnail = $this->entityTypeManager->getStorage('image_style')
          ->load('thumbnail');
        $thumbnailAvatar = $thumbnail->buildUri($avatarUri);
        if (!file_exists($thumbnailAvatar)) {
          $thumbnail->createDerivative($avatarUri, $thumbnailAvatar);
        }
        $author_avatar = $this->fileUrlGenerator->generateAbsoluteString($thumbnailAvatar);
      }
      $key = $view_id . '-' . $view->current_display;
      $link = $url->setOption('absolute', TRUE)
        ->setOption('query', ['kanbanTicket' => $entity_id])
        ->toString();
      foreach ($assignValues as $uid => $userID) {
        // Send Email.
        if (!empty($style_plugin->options["send_email"])) {
          $assignor = $this->entityTypeManager->getStorage('user')->load($uid);
          if (empty($assignor)) {
            continue;
          }
          $to = $assignor->getEmail();

          // Set up email template.
          $body_data = [
            '#theme' => 'views_email_kanban',
            '#message' => $message,
            '#author_initial' => mb_strtoupper($author_initial),
            '#author_avatar' => $author_avatar,
            '#type' => $statusName['new'],
            '#author_name' => $name,
            '#title' => $this->t("Change status") . " - " . $entity->label(),
            '#assignator' => $assignor->getDisplayName(),
            '#btn_text' => $this->t('View'),
            '#link' => $link,
          ];
          $messageSend = [
            'id' => $key,
            'headers' => [
              'Content-type' => 'text/html; charset=UTF-8; format=flowed; delsp=yes',
              'Reply-to' => $name . '<' . $email . '>',
              'Return-Path' => $email,
              'Content-Transfer-Encoding' => '8Bit',
              'MIME-Version' => '1.0',
            ],
            'subject' => $view->getTitle() . ' ' . $this->config('system.site')->get('name'),
            'to' => $to,
            'body' => $this->renderer->render($body_data),
          ];
          $this->mailManager->getInstance([
            'module' => 'views_kanban',
            'key' => $key,
          ])->mail($messageSend);
        }
        // Send notification.
        if (!empty($style_plugin->options["send_notification"]) &&
          $this->moduleHandler->moduleExists('pwa_firebase')) {
          \Drupal::service('pwa_firebase.send')
            ->sendMessageToUser($uid, $view->getTitle(), $message, $url->toString());
        }
      }
    }
    $data = [
      'success' => TRUE,
      'message' => $message,
    ];
    return new JsonResponse($data);
  }

  /**
   * Get the value allowed in the state field.
   *
   * @param object $entity
   *   Entity.
   * @param string $fieldName
   *   Field name.
   *
   * @return array
   *   Allow values.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getAllowedValues($entity, $fieldName) {
    $extractStatus = explode(':', $fieldName);
    if (!empty($extractStatus[1])) {
      // $fieldName = $extractStatus[0];
      $workflow_id = $extractStatus[1];
      $workflow = $this->entityTypeManager->getStorage('workflow')
        ->load($workflow_id);
      return array_map([
        State::class,
        'labelCallback',
      ], $workflow->getTypePlugin()->getStates());
    }
    $statusFieldDefinition = $entity->get($fieldName)->getFieldDefinition();
    $statusFieldValues = $statusFieldDefinition->getSettings();
    $allowed_values = [];
    if (!empty($statusFieldValues["workflow"])) {
      $workflow_manager = \Drupal::service('plugin.manager.workflow');
      $workflow = $workflow_manager->createInstance($statusFieldValues["workflow"]);
      $states = $workflow->getStates();
      $allowed_values = array_map(function (WorkflowState $state) {
        return $state->getLabel();
      }, $states);
    }
    if (!empty($statusFieldValues["allowed_values"])) {
      $allowed_values = $statusFieldValues["allowed_values"];
    }
    if (!empty($statusFieldValues["target_type"])) {
      $vid = current($statusFieldValues["handler_settings"]["target_bundles"]);
      $loadTermStatus = $this->entityTypeManager->getStorage('taxonomy_term')
        ->loadTree($vid);
      foreach ($loadTermStatus as $term) {
        $allowed_values[$term->tid] = $term->name;
      }
    }
    return $allowed_values;
  }

  /**
   * Get message for log.
   *
   * @param object $entity
   *   Entity.
   * @param string $status_field
   *   Status field.
   * @param string $newStatus
   *   New status.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   Array text history status.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getHistoryMessage($entity, $status_field, $newStatus) {
    $statusName = $this->getStatusName($entity, $status_field, $newStatus);
    return $this->t('@user change from @old to @new', [
      '@user' => $this->currentUser->getDisplayName(),
      '@old' => $statusName['old'],
      '@new' => $statusName['new'],
    ]);
  }

  /**
   * Get status name.
   */
  protected function getStatusName($entity, $status_field, $newStatus) {
    $statusList = $this->getAllowedValues($entity, $status_field);
    $extractStatus = explode(':', $status_field);
    if (!empty($extractStatus[1])) {
      $status_field = $extractStatus[0];
    }
    $currentStatus = $entity->get($status_field)->getString();
    if (is_array($currentStatus)) {
      $currentStatus = current($currentStatus);
    }
    if (!empty($statusList[$currentStatus])) {
      $currentStatus = $statusList[$currentStatus];
    }
    if (!empty($statusList[$newStatus])) {
      $newStatus = $statusList[$newStatus];
    }
    return [
      'old' => $currentStatus,
      'new' => $newStatus,
    ];
  }

}
