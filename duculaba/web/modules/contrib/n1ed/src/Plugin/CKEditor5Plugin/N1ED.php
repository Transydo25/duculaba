<?php

namespace Drupal\n1ed\Plugin\CKEditor5Plugin;

use Drupal\ckeditor5\Plugin\CKEditor5PluginConfigurableTrait;
use Drupal\ckeditor5\Plugin\CKEditor5PluginDefault;
use Drupal\ckeditor5\Plugin\CKEditor5PluginConfigurableInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\editor\EditorInterface;
use Drupal\Core\Url;

/**
 * @internal
 *   Plugin classes are internal.
 */
class N1ED extends CKEditor5PluginDefault implements CKEditor5PluginConfigurableInterface {

  use CKEditor5PluginConfigurableTrait;

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return ['enableN1EDEcoSystem' => "false"];
  }

  public function getDynamicPluginConfig(array $static_plugin_config, EditorInterface $editor): array {

    if (!isset($static_plugin_config["flmngr"]))
      $static_plugin_config["flmngr"] = [];
    if (!isset($static_plugin_config["flmngr"]["options"]))
      $static_plugin_config["flmngr"]["options"] = [];

    $static_plugin_config["flmngr"]["integration"] = "drupal8";

    $static_plugin_config["flmngr"]["options"]["urlFileManager"] = Url::fromRoute(
      \Drupal::config('n1ed.settings')->get('useLegacyFlmngrBackend') != 1 ? 'n1ed.flmngr' : 'n1ed.flmngrLegacy'
    )->toString();
    $static_plugin_config["flmngr"]["options"]["urlFileManager__CSRF"] = "drupal8";

    // Get path to /sites/SITE/files/flmngr.
    $static_plugin_config["flmngr"]["options"]["urlFiles"] = n1ed_get_files_url_path();
    $static_plugin_config["flmngr"]["options"]["dirUploads"] = '/';

    error_log(print_r($static_plugin_config, TRUE));

    return $static_plugin_config;
  }

  /**
   * Adds boolean value widget to the form.
   */
  protected function addBooleanToForm(&$form) {
    $form['enableN1EDEcoSystem'] = [
      '#type' => 'textfield',
      '#title' => "enableN1EDEcoSystem",
      '#title_display' => 'invisible',
      '#default_value' => (isset($this->configuration['enableN1EDEcoSystem']) && $this->configuration['enableN1EDEcoSystem'] == "true") ? "true" : "false",
      '#attributes' => [
        'style' => 'display: none!important',
        'data-n1ed-eco-param-name' => 'enableN1EDEcoSystem',
        'data-n1ed-eco-param-type' => 'boolean',
        'data-n1ed-eco-param-default' => "true",
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {

    $settings = $this->configuration;
    error_log(print_r($settings, TRUE) . "\n");
    $v = (isset($this->configuration['enableN1EDEcoSystem']) && $this->configuration['enableN1EDEcoSystem'] == "true") ? "true" : "false";
    error_log("Value: " . $v . "\n\n");

    $form['info'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attributes' => [
        'data-n1ed-eco-plugin' => 'N1ED-editor',
        'style' => 'display: inline-block;margin-top: 13px;margin-left: 10px;',
      ],
      '#value' => '<a href="#n1ed-conf">' . t('Configure add-on') . '</a>',
    ];

    $form['#attached']['library'][] = 'n1ed/n1ed';
    $form['#attached']['drupalSettings']['n1edApiKey'] = n1ed_get_api_key("CKEditor 5");

    $form['#attached']['drupalSettings']['n1edIntegrationType'] = n1ed_get_integration_type("CKEditor 5");
    $form['#attached']['drupalSettings']['n1edEditor'] = "ckeditor5";

    $form['#attached']['drupalSettings']['useFlmngrOnFileFields'] = \Drupal::config('n1ed.settings')->get('useFlmngrOnFileFields') ? 1 : 0;
    $form['#attached']['drupalSettings']['useLegacyFlmngrBackend'] = \Drupal::config('n1ed.settings')->get('useLegacyFlmngrBackend') ? 1 : 0;

    $form['#attached']['drupalSettings']['n1edToken'] =
      \Drupal::config('n1ed.settings')->get('token') ?: NULL;

    $this->addBooleanToForm($form);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    $form_value = $form_state->getValue('enableN1EDEcoSystem');
    $form_state->setValue('enableN1EDEcoSystem', $form_value == "true" ? "true" : "false");
    error_log("Validate " . $form_value);
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    error_log("Submit " . $form_state->getValue('enableN1EDEcoSystem'));
    $this->configuration['enableN1EDEcoSystem'] = $form_state->getValue('enableN1EDEcoSystem') == "true" ? "true" : "false";
  }

}
