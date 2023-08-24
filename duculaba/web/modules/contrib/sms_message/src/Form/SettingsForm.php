<?php

namespace Drupal\sms_message\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Configure Sms message settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'sms_message_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['sms_message.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $token = $this->config('sms_message.settings')->get('token');
    $url = '';
    if (empty($token)) {
      $token = bin2hex(random_bytes(9));
    }
    else {
      $options = ['absolute' => TRUE];
      $route = Url::fromRoute('sms_message.api.get', ['token' => $token], $options);
      $url = Link::fromTextAndUrl($route->toString(), $route)->toString();
    }
    $form['token'] = [
      '#type' => 'textfield',
      '#title' => $this->t('token'),
      '#default_value' => $token,
      '#description' => $url,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('sms_message.settings')
      ->set('token', $form_state->getValue('token'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
