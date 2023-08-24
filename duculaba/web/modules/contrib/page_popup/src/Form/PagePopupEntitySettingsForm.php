<?php

namespace Drupal\page_popup\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Executable\ExecutableManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Plugin\Context\ContextRepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form handler for the PagePopupEntity add and edit forms.
 */
class PagePopupEntitySettingsForm extends EntityForm {

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
   * The language manager service.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

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
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   */
  public function __construct(MessengerInterface $messenger, LoggerChannelInterface $logger, ExecutableManagerInterface $condition_plugin_manager, ContextRepositoryInterface $context_repository, LanguageManagerInterface $language_manager) {
    $this->messenger = $messenger;
    $this->logger = $logger;
    $this->conditionPluginManager = $condition_plugin_manager;
    $this->contextRepository = $context_repository;
    $this->languageManager = $language_manager;
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
      $container->get('language_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $available_contexts = $this->contextRepository->getAvailableContexts();
    $form_state->setTemporaryValue('gathered_contexts', $available_contexts);

    /** @var \Drupal\page_popup_message\Entity\PagePopupEntity $entity */
    $entity = $this->entity;
    $form['#tree'] = TRUE;
    $form['popup_text_color'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Text Color'),
      '#maxlength' => 7,
      '#size' => 10,
      '#default_value' => !empty($entity->getTextColor()) ? $entity->getTextColor() : '#000000',
      '#description' => $this->t('Popup text color.'),
    ];
    $form['text_color_picker'] = [
      '#prefix' => '<div class="text-color-picker">',
      '#suffix' => '</div>',
    ];
    $form['popup_bg_color'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Background Color'),
      '#maxlength' => 7,
      '#size' => 10,
      '#default_value' => !empty($entity->getBgColor()) ? $entity->getBgColor() : '#FFFFFF',
      '#description' => $this->t('Popup background color.'),
    ];
    $form['bg_color_picker'] = [
      '#prefix' => '<div class="bg-color-picker">',
      '#suffix' => '</div>',
    ];
    $form['popup_layout'] = [
      '#type' => 'radios',
      '#title' => $this->t('Choose layout'),
      '#default_value' => !empty($entity->getLayout()) ? $entity->getLayout() : 0,
      '#options' => [
        0 => $this->t('Center'),
        1 => $this->t('Top left'),
        2 => $this->t('Top Right'),
        3 => $this->t('Bottom left'),
        4 => $this->t('Bottom Right'),
      ],
    ];
    $form['popup_delay'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Delays'),
      '#size' => 5,
      '#maxlength' => 1,
      '#default_value' => !empty($entity->getDelay()) ? $entity->getDelay() : 0,
      '#description' => $this->t("Show popup after this seconds. 0 will show immediately after the page load."),
    ];
    $form['popup_width'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Width'),
      '#size' => 6,
      '#maxlength' => 3,
      '#default_value' => !empty($entity->getWidth()) ? $entity->getWidth() : 400,
      '#description' => $this->t("Add popup width in pixel."),
    ];
    $form['popup_height'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Height'),
      '#size' => 6,
      '#maxlength' => 3,
      '#default_value' => !empty($entity->getHeight()) ? $entity->getHeight() : 400,
      '#description' => $this->t("Add popup width in pixel."),
    ];
    $form['popup_fontsize'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font Size'),
      '#size' => 6,
      '#maxlength' => 2,
      '#default_value' => !empty($entity->getFontsize()) ? $entity->getFontsize() : 20,
      '#description' => $this->t("Add popup font size in pixel."),
    ];
    $form['#attached']['library'][] = 'page_popup/popup.color';
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

    /** @var \Drupal\theme_switcher\Entity\PagePopupEntity $entity */
    $entity = $this->entity;
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
    $form_state->setValue('popup_delay', (int) $form_state->getValue('popup_delay'));
    $form_state->setValue('popup_width', (int) $form_state->getValue('popup_width'));
    $form_state->setValue('popup_height', (int) $form_state->getValue('popup_height'));
    $form_state->setValue('popup_fontsize', (int) $form_state->getValue('popup_fontsize'));
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
