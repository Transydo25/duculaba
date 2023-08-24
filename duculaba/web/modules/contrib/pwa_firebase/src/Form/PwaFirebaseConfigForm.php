<?php

namespace Drupal\pwa_firebase\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configuration form firebase.
 */
class PwaFirebaseConfigForm extends ConfigFormBase {

  /**
   * Name of the config.
   *
   * @var string
   */
  public static $configName = 'pwa_firebase.settings';

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [self::$configName];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'pwa_firebase_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(self::$configName);
    $form['firebase_endpoint'] = [
      '#type' => 'textfield',
      '#title' => t('Firebase endpoint'),
      '#description' => t('Google Firebase Cloud Messaging endpoint.'),
      '#default_value' => $config->get('firebase_endpoint'),
      '#required' => TRUE,
    ];
    $form['general'] = [
      '#type' => 'details',
      '#title' => t('General'),
      '#description' => t('https://console.firebase.google.com/u/0/project/[your-project]/settings/general'),
    ];
    $form['general']['firebase_project_id'] = [
      '#type' => 'textfield',
      '#title' => t('Firebase project id'),
      '#description' => t('Google Firebase: Project Settings -> Setting -> Project ID'),
      '#default_value' => $config->get('firebase_project_id'),
      '#required' => TRUE,
    ];
    $form['cloud_messageing'] = [
      '#type' => 'details',
      '#title' => t('Cloud Messaging'),
      '#description' => t('https://console.firebase.google.com/u/0/project/[your-project]/settings/cloudmessaging'),
    ];
    $form['cloud_messageing']['firebase_server_key'] = [
      '#type' => 'textarea',
      '#title' => t('Firebase Server Key'),
      '#description' => t('This is the server key. <em>Do not confuse with API Key</em>'),
      '#default_value' => $config->get('firebase_server_key'),
      '#required' => TRUE,
    ];
    $form['cloud_messageing']['firebase_sender_id'] = [
      '#type' => 'textfield',
      '#title' => t('Firebase sender id'),
      '#description' => t('Sender id: Project Settings -> Cloud Messaging -> Project credentials -> Sender ID'),
      '#default_value' => $config->get('firebase_sender_id'),
      '#required' => TRUE,
    ];
    $form['cloud_messageing']['firebase_vap_id'] = [
      '#type' => 'textfield',
      '#title' => t('Web Push certificates'),
      '#rows' => 2,
      '#description' => t('Web Push certificates: Project Settings -> Cloud Messaging -> Web configuration -> Key pair'),
      '#default_value' => $config->get('firebase_vap_id'),
    ];

    $form['your_apps'] = [
      '#type' => 'details',
      '#title' => t('Your apps - Web apps Information'),
      '#description' => t('https://console.firebase.google.com/u/0/project/[your-project]/settings/general/web'),
    ];
    $form['your_apps']['firebase_apiKey_id'] = [
      '#type' => 'textfield',
      '#title' => t('Firebase API Key'),
      '#description' => t('Your apps: Project Settings -> General -> Web API Key'),
      '#default_value' => $config->get('firebase_apiKey_id'),
    ];
    $form['your_apps']['firebase_app_id'] = [
      '#type' => 'textfield',
      '#title' => t('Firebase app Key'),
      '#description' => t('Your apps: Project Settings -> General -> Your apps / App ID'),
      '#default_value' => $config->get('firebase_app_id'),
    ];

    $form['your_apps']['firebase_measurement_id'] = [
      '#type' => 'textfield',
      '#title' => t('Firebase analytics'),
      '#description' => t('If you want to integrate google analytic. Google Firebase Project Settings -> General -> Your apps -> measurementId'),
      '#default_value' => $config->get('firebase_measurement_id'),
    ];
    $form['your_apps']['firebase_version'] = [
      '#type' => 'textfield',
      '#title' => t('Firebase version'),
      '#description' => t('You should use the stable version x.xx.0'),
      '#default_value' => $config->get('firebase_version'),
    ];

    $form['firebase_enable_authorized'] = [
      '#type' => 'checkbox',
      '#title' => t('Use only with authorized user'),
      '#description' => t('Checked which project is not public, it is active when user has logged in'),
      '#default_value' => $config->get('firebase_enable_authorized'),
    ];

    $form['firebase_message_id'] = [
      '#type' => 'textfield',
      '#title' => t('Custom selector of div that display notification'),
      '#description' => t('You can add &lt;div id="message"> in your site'),
      '#default_value' => $config->get('firebase_message_id'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $this->config(self::$configName)
      ->set('firebase_endpoint', $form_state->getValue('firebase_endpoint'))
      ->set('firebase_server_key', $form_state->getValue('firebase_server_key'))
      ->set('firebase_sender_id', $form_state->getValue('firebase_sender_id'))
      ->set('firebase_project_id', $form_state->getValue('firebase_project_id'))
      ->set('firebase_apiKey_id', $form_state->getValue('firebase_apiKey_id'))
      ->set('firebase_app_id', $form_state->getValue('firebase_app_id'))
      ->set('firebase_vap_id', $form_state->getValue('firebase_vap_id'))
      ->set('firebase_measurement_id', $form_state->getValue('firebase_measurement_id'))
      ->set('firebase_version', $form_state->getValue('firebase_version'))
      ->set('firebase_enable_authorized', $form_state->getValue('firebase_enable_authorized'))
      ->set('firebase_message_id', $form_state->getValue('firebase_message_id'))
      ->save();
    return parent::submitForm($form, $form_state);
  }

}
