<?php

namespace Drupal\n1ed\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\ckeditor\CKEditorPluginConfigurableInterface;
use Drupal\ckeditor\CKEditorPluginContextualInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\editor\Entity\Editor;

/**
 * Defines plugin.
 *
 * @CKEditorPlugin(
 *   id = "N1ED-editor",
 *   label = @Translation("N1ED"),
 *   module = "n1ed"
 * )
 */
class N1ED extends CKEditorPluginBase implements CKEditorPluginConfigurableInterface, CKEditorPluginContextualInterface {

  /**
   * {@inheritdoc}
   */
  public function getButtons() {

    $buttons = [];

    $buttonsManual = json_decode(file_get_contents(__DIR__ . '/buttonsManual.json'), TRUE);
    for ($i = 0; $i < count($buttonsManual); $i++) {
      $button = $buttonsManual[$i];
      $buttons[$button['name']] = [
        'image' => 'https://n1ed.com/cdn/buttons-ckeditor/' . $button['name'] . '.png',
        'label' => isset($button['hint']) ? $button['hint'] : (isset($button['title']) ? $button['title'] : NULL),
      ];
    }

    return $buttons;
  }

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return Url::fromRoute('n1ed.plugin')->toString();

    /*
    $settings = \Drupal::config('n1ed.settings');
    $apiKey = n1ed_get_api_key("CKEditor");

    $version = $settings->get('version') ?: '';
    if ($version === '') {
      $version = 'latest';
    }
    $urlCache = $settings->get('urlCache') ?: '';
    if ($urlCache === '') {
      // Fix for: https://www.drupal.org/project/n1ed/issues/3111919
      // Do not start URL with "https:" prefix.
      // Notice about cookies: developers use it to specify debug server to use,
      // all other users will use old known cloud.n1ed.com address.
      return '//' . (isset($_COOKIE["N1ED_PREFIX"]) ? ($_COOKIE["N1ED_PREFIX"] . ".") : "") . 'cloud.n1ed.com/cdn/' . $apiKey . '/' . $version . '/ckeditor/plugins/N1EDEco/plugin.js';
    }
    else {
      // Fix for: https://www.drupal.org/project/n1ed/issues/3111919
      // Do not start URL with "https:" prefix.
      if (strpos($urlCache, "http:") === 0) {
        $urlCache = substr($urlCache, 5);
      }
      elseif (strpos($urlCache, "https:") === 0) {
        $urlCache = substr($urlCache, 6);
      }
      return $urlCache . $apiKey . '/' . $version . '/ckeditor/plugins/N1EDEco/plugin.js';
    }*/
  }

  /**
   * {@inheritdoc}
   */
  public function getDependencies(Editor $editor) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getLibraries(Editor $editor) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function isInternal() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function isEnabled(Editor $editor) {
    return TRUE;
  }

  /**
   * Returnes a value of parameter or default value.
   */
  protected function getConfigParam($settings, $name, $default, $type) {
    if (isset($settings[$name]) && is_string($settings[$name])
      && strlen($settings[$name]) > 0) {
      $value = $settings[$name];
    }
    else {
      $value = $default;
    }
    if (isset($type) && $type == 'number') {
      $value = intval($value);
    }
    else {
      if (isset($type) && $type == 'boolean') {
        if ($value === 'true' || $value === 1 || $value === TRUE) {
          $value = TRUE;
        }
        else {
          if ($value === 'false' || $value === 0 || $value === FALSE) {
            $value = FALSE;
          }
        }
      }
      else {
        if (isset($type) && $type == 'json') {
          if ($value != '') {
            $value = json_decode($value);
          }
        }
      }
    }

    return $value;
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    $settings = [];
    if (isset($editor->getSettings()['plugins']['N1ED-editor'])) {
      $settings = $editor->getSettings()['plugins']['N1ED-editor'];
    }
    $settings["apiKey"] = n1ed_get_api_key("CKEditor");
    $settings["integration"] = "drupal8";
    $settings["urlSetApiKeyAndToken"] = Url::fromRoute('n1ed.setApiKey')->toString();
    $settings["urlSetApiKeyAndToken__CSRF"] = "drupal8";

    $settings["Flmngr"]["urlFileManager"] = Url::fromRoute(
      \Drupal::config('n1ed.settings')->get('useLegacyFlmngrBackend') != 1 ? 'n1ed.flmngr' : 'n1ed.flmngrLegacy'
    )->toString();
    $settings["Flmngr"]["urlFileManager__CSRF"] = "drupal8";

    // Get path to /sites/SITE/files/flmngr.
    $settings["Flmngr"]["urlFiles"] = n1ed_get_files_url_path();
    $settings["Flmngr"]["dirUploads"] = '/';

    $urlCdn = \Drupal::config('n1ed.settings')
      ->get('urlCache') ?: '';
    if ($urlCdn !== '') {
      $settings["urlCdn"] = $urlCdn;
    }

    $version = \Drupal::config('n1ed.settings')
      ->get('version') ?: '';
    if ($version !== '') {
      $settings["version"] = $version;
    }

    if (\Drupal::currentUser()->hasPermission(
      "administer n1ed configuration"
    )) {
      $settings["token"] = \Drupal::config('n1ed.settings')
        ->get('token') ?: NULL;
    }

    return $settings;
  }

  /**
   * Adds boolean value widget to the form.
   */
  protected function addBooleanToForm(&$form, $settings, $param, $default) {
    $form[$param] = [
      '#type' => 'textfield',
      '#title' => $param,
      '#title_display' => 'invisible',
      '#default_value' => $this->getConfigParam(
        $settings,
        $param,
        $default,
        'boolean'
      ) ? "true" : "false",
      '#attributes' => [
        'style' => 'display: none!important',
        'data-n1ed-eco-param-name' => $param,
        'data-n1ed-eco-param-type' => 'boolean',
        'data-n1ed-eco-param-default' => $default ? "true" : "false",
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(
    array $form,
    FormStateInterface $form_state,
    Editor $editor
  ) {
    $config = $this->getConfig($editor);

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
    $form['#attached']['drupalSettings']['n1edApiKey'] = n1ed_get_api_key("CKEditor");

    $form['#attached']['drupalSettings']['n1edIntegrationType'] = n1ed_get_integration_type("CKEditor");
    $form['#attached']['drupalSettings']['n1edEditor'] = "ckeditor";

    $form['#attached']['drupalSettings']['useFlmngrOnFileFields'] = \Drupal::config('n1ed.settings')->get('useFlmngrOnFileFields') ? 1 : 0;
    $form['#attached']['drupalSettings']['useLegacyFlmngrBackend'] = \Drupal::config('n1ed.settings')->get('useLegacyFlmngrBackend') ? 1 : 0;

    $form['#attached']['drupalSettings']['n1edToken'] =
      \Drupal::config('n1ed.settings')->get('token') ?: NULL;

    $this->addBooleanToForm($form, $config, "enableN1EDEcoSystem", TRUE);

    return $form;
  }

}
