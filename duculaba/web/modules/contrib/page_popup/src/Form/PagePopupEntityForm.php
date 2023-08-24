<?php

namespace Drupal\page_popup\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Executable\ExecutableManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\SubformState;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Plugin\Context\ContextRepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\State\StateInterface;

/**
 * Form handler for the PagePopupEntity add and edit forms.
 */
class PagePopupEntityForm extends EntityForm {

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
   * The ConditionManager for building the visibility UI.
   *
   * @var \Drupal\Core\Executable\ExecutableManagerInterface
   */
  protected $conditionPluginManager;

  /**
   * The context repository service.
   *
   * @var \Drupal\Core\Plugin\Context\ContextRepositoryInterface
   */
  protected $contextRepository;

  /**
   * The state keyvalue collection.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * Constructs an SwitchThemeRuleForm object.
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger.
   * @param \Drupal\Core\Logger\LoggerChannelInterface $logger
   *   The logger.
   * @param \Drupal\Core\Executable\ExecutableManagerInterface $condition_plugin_manager
   *   The ConditionManager for building the visibility UI.
   * @param \Drupal\Core\Plugin\Context\ContextRepositoryInterface $context_repository
   *   The lazy context repository service.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state.
   */
  public function __construct(MessengerInterface $messenger, LoggerChannelInterface $logger, ExecutableManagerInterface $condition_plugin_manager, ContextRepositoryInterface $context_repository, StateInterface $state) {
    $this->messenger = $messenger;
    $this->logger = $logger;
    $this->conditionPluginManager = $condition_plugin_manager;
    $this->contextRepository = $context_repository;
    $this->state = $state;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('messenger'),
      $container->get('logger.factory')->get('page_popup'),
      $container->get('plugin.manager.condition'),
      $container->get('context.repository'),
      $container->get('state')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $available_contexts = $this->contextRepository->getAvailableContexts();
    $form_state->setTemporaryValue('gathered_contexts', $available_contexts);

    /** @var \Drupal\page_popup\Entity\PagePopupEntity $entity */
    $entity = $this->entity;
    $form['#tree'] = TRUE;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Popup Name'),
      '#maxlength' => 255,
      '#default_value' => $entity->label(),
      '#description' => $this->t('The human-readable name is shown in the Page Popup list.'),
      '#required' => TRUE,
    ];
    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $entity->id(),
      '#machine_name' => [
        'source' => ['label'],
        'exists' => [$this, 'exist'],
      ],
      '#disabled' => !$entity->isNew(),
    ];
    $form['status'] = [
      '#type' => 'radios',
      '#title' => $this->t('Popup status'),
      '#options' => [
        1 => $this->t('Active'),
        0 => $this->t('Inactive'),
      ],
      '#default_value' => (int) $entity->status(),
      '#description' => $this->t('The Page Popup will only work if the active option is set.'),
    ];
    $form['weight'] = [
      '#type' => 'weight',
      '#title' => $this->t('Weight'),
      '#access' => FALSE,
      '#default_value' => $entity->getWeight(),
      '#description' => $this->t('The sort order for this record. Lower values display first.'),
    ];
    $form['message_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Popup Title'),
      '#maxlength' => 255,
      '#default_value' => $entity->getMessageTitle(),
      '#description' => $this->t('Popup title is shown in the page.'),
      '#required' => TRUE,
    ];
    $form['exclude_title'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Exclude title from popup'),
      '#default_value' => $this->state->get('page_popup.mode'),
    ];
    $form['message_body'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Popup Body'),
      '#maxlength' => 255,
      '#default_value' => $entity->getMessageBody(),
      '#description' => $this->t('Popup body is shown in the page.'),
      '#required' => TRUE,
    ];

    // Build the visibility UI form and follow this
    // https://www.drupal.org/node/2284687
    $form['visibility'] = [
      'visibility_tabs' => [
        '#type' => 'vertical_tabs',
        '#title' => $this->t('Condition'),
        '#parents' => ['visibility_tabs'],
      ],
    ];
    $visibility = $entity->getVisibility();
    $definitions = $this->conditionPluginManager->getFilteredDefinitions(
      'page_popup_ui',
      $form_state->getTemporaryValue('gathered_contexts'),
      ['page_popup_entity' => $entity]
    );

    foreach ($definitions as $condition_id => $definition) {

      /** @var \Drupal\Core\Condition\ConditionInterface $condition */
      $condition = $this->conditionPluginManager->createInstance(
          $condition_id, $visibility[$condition_id] ?? []
      );
      if ($condition->getPluginDefinition()['label'] == "Request Path") {
        $form_state->set(['condition', $condition_id], $condition);
        $condition_form = $condition->buildConfigurationForm([], $form_state);
        unset($condition_form['negate']);
        $form['visibility'][$condition_id] = [
          '#type' => 'details',
          '#title' => $condition->getPluginDefinition()['label'],
          '#group' => 'visibility_tabs',
        ] + $condition_form;
      }
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   *
   * The settings conditions context mappings is now the plugin responsibility
   * so we can avoid doing it here. From 8.2 the class ConditionPluginBase do
   * the job on submitConfigurationForm().
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    /** @var \Drupal\theme_switcher\Entity\PagePopupMessageEntity $entity */
    $entity = $this->entity;
    $condition = $form_state->get(['condition', 'request_path']);
    $subform = SubformState::createForSubform($form['visibility']['request_path'], $form, $form_state);
    $condition->submitConfigurationForm($form['visibility']['request_path'], $subform);

    // Update the visibility conditions on the block.
    $entity->getVisibilityConditions()->addInstanceId(
        'request_path', $condition->getConfiguration()
    );
    if ($form_state->getValue('exclude_title')) {
      $this->state->set('page_popup.mode', $form_state->getValue('exclude_title'));
    }
    else {
      $this->state->delete('page_popup.mode');
    }
    // Save the settings of the plugin.
    $status = $entity->save();

    $message = $this->t("The Page Popup Entity '%label' has been %op.", [
      '%label' => $entity->label(),
      '%op' => ($status == SAVED_NEW) ? 'created' : 'updated',
    ]);
    $this->messenger->addStatus($message);
    $this->logger->notice($message);

    $form_state->setRedirect('page_popup.admin');
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // Validate the weight.
    $form_state->setValue('weight', (int) $form_state->getValue('weight'));

    // Validate visibility condition settings.
    foreach ($form_state->getValue('visibility') as $condition_id => $values) {
      // Allow the condition to validate the form.
      $condition = $form_state->get(['condition', $condition_id]);
      $subform = SubformState::createForSubform($form['visibility'][$condition_id], $form, $form_state);
      $condition->validateConfigurationForm($form['visibility'][$condition_id], $subform);
    }
  }

  /**
   * Checks whether a page_popup_entity exists.
   *
   * @param string $id
   *   The page_popup_entity machine name.
   *
   * @return bool
   *   Whether the page_popup_entity exists.
   */
  public function exist($id) {
    $entity = $this->entityTypeManager->getStorage('page_popup_entity')
      ->getQuery()->condition('id', $id)->execute();
    return (bool) $entity;
  }

}
