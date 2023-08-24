<?php

namespace Drupal\bootstrap5_admin;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Theme\ThemeManagerInterface;

/**
 * Sub theme settings manager.
 */
class SettingsManager {

  use StringTranslationTrait;

  /**
   * The theme manager.
   *
   * @var \Drupal\Core\Theme\ThemeManagerInterface
   */
  protected $themeManager;

  /**
   * Constructs a WebformThemeManager object.
   *
   * @param \Drupal\Core\Theme\ThemeManagerInterface $theme_manager
   *   The theme manager.
   */
  public function __construct(ThemeManagerInterface $theme_manager) {
    $this->themeManager = $theme_manager;
  }

  /**
   * Alters theme settings form.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param string $form_id
   *   The form id.
   *
   * @see hook_form_alter()
   */
  public function themeSettingsAlter(array &$form, FormStateInterface $form_state, $form_id) {
    if (isset($form_id)) {
      return;
    }
    $options_theme = [
      'none' => 'do not apply theme',
      'light' => 'light (dark text/links against a light background)',
      'dark' => 'dark (light/white text/links against a dark background)',
    ];

    $options_colour = [
      'none' => 'do not apply colour',
      'primary' => 'primary',
      'secondary' => 'secondary',
      'light' => 'light',
      'dark' => 'dark',
    ];

    // Populating options for top container.
    $options_top_container = [
      'container-fluid m-0' => 'fluid with padding',
      'container-fluid m-0 p-0' => 'fluid',
      'container' => 'fixed',
    ];

    // Populating extra options for top container.
    if (!empty($container_config = theme_get_setting('b5_top_container_config'))) {
      foreach (explode("\n", $container_config) as $line) {
        $values = explode("|", trim($line));
        if (is_array($values) && (count($values) == 2)) {
          $options_top_container += [trim($values[0]) => trim($values[1])];
        }
      }
    }

    $form['body_details'] = [
      '#type' => 'details',
      '#title' => $this->t('Body options'),
      '#description' => $this->t("Combination of theme/background colour may affect background colour/text colour contrast. To fix any contrast issues, override corresponding variables in scss(refer to dist/boostrap/scss/_variables.scss)"),
      '#open' => TRUE,
    ];

    $form['body_details']['b5_top_container'] = [
      '#type' => 'select',
      '#title' => $this->t('Website container type'),
      '#default_value' => theme_get_setting('b5_top_container'),
      '#description' => $this->t("Type of top level container: fluid (eg edge to edge) or fixed width"),
      '#options' => $options_top_container,
    ];

    $form['body_details']['b5_top_container_config'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Website container type configuration'),
      '#default_value' => theme_get_setting('b5_top_container_config'),
      '#description' => $this->t("Format: <classes|label> on each line, e.g. <br><pre>container|fixed<br />container-fluid m-0 p-0|fluid</pre>"),
    ];

    $form['body_details']['b5_body_schema'] = [
      '#type' => 'select',
      '#title' => $this->t('Body theme:'),
      '#default_value' => theme_get_setting('b5_body_schema'),
      '#description' => $this->t("Text colour theme of the body."),
      '#options' => $options_theme,
    ];

    $form['body_details']['b5_body_bg_schema'] = [
      '#type' => 'select',
      '#title' => $this->t('Body background:'),
      '#default_value' => theme_get_setting('b5_body_bg_schema'),
      '#description' => $this->t("Background color of the body."),
      '#options' => $options_colour,
    ];

    $form['nav_details'] = [
      '#type' => 'details',
      '#title' => $this->t('Navbar options'),
      '#description' => $this->t("Combination of theme/background colour may affect background colour/text colour contrast. To fix any contrast issues, override \$navbar-light-*/\$navbar-dark-* variables (refer to dist/boostrap/scss/_variables.scss)"),
      '#open' => TRUE,
    ];

    $form['nav_details']['b5_navbar_schema'] = [
      '#type' => 'select',
      '#title' => $this->t('Navbar theme:'),
      '#default_value' => theme_get_setting('b5_navbar_schema'),
      '#description' => $this->t("Text colour theme of the navbar."),
      '#options' => $options_theme,
    ];

    $form['nav_details']['b5_navbar_bg_schema'] = [
      '#type' => 'select',
      '#title' => $this->t('Navbar background:'),
      '#default_value' => theme_get_setting('b5_navbar_bg_schema'),
      '#description' => $this->t("Background color of the navbar."),
      '#options' => $options_colour,
    ];

    $form['footer_details'] = [
      '#type' => 'details',
      '#title' => $this->t('Footer options'),
      '#description' => $this->t("Combination of theme/background colour may affect background colour/text colour contrast. To fix any contrast issues, override corresponding variables in scss (refer to dist/boostrap/scss/_variables.scss)"),
      '#open' => TRUE,
    ];

    $form['footer_details']['b5_footer_schema'] = [
      '#type' => 'select',
      '#title' => $this->t('Footer theme:'),
      '#default_value' => theme_get_setting('b5_footer_schema'),
      '#description' => $this->t("Text colour theme of the footer."),
      '#options' => $options_theme,
    ];

    $form['footer_details']['b5_footer_bg_schema'] = [
      '#type' => 'select',
      '#title' => $this->t('Footer background:'),
      '#default_value' => theme_get_setting('b5_footer_bg_schema'),
      '#description' => $this->t("Background color of the footer."),
      '#options' => $options_colour,
    ];
    $form['subtheme'] = [
      '#type' => 'details',
      '#title' => $this->t('Subtheme'),
      '#description' => $this->t("Create subtheme."),
      '#open' => FALSE,
    ];

    $form['subtheme']['subtheme_folder'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Subtheme location'),
      '#default_value' => 'themes/custom',
      '#description' => $this->t("Relative path to the webroot <em>%root</em>. No trailing slash.", [
        '%root' => DRUPAL_ROOT,
      ]),
    ];

    $form['subtheme']['subtheme_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Subtheme name'),
      '#default_value' => 'Bootstrap 5 admin subtheme',
      '#description' => $this->t("If name is empty, machine name will be used."),
    ];

    $form['subtheme']['subtheme_machine_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Subtheme machine name'),
      '#default_value' => 'bootstrap_subtheme',
      '#description' => $this->t("Use lowercase characters, numbers and underscores. Name must start with a letter."),
    ];

    $form['subtheme']['create'] = [
      '#type' => 'submit',
      '#name' => 'subtheme_create',
      '#value' => $this->t('Create'),
      '#button_type' => 'danger',
      '#attributes' => [
        'class' => ['btn btn-danger'],
      ],
      '#submit' => ['bootstrap_form_system_theme_settings_subtheme_submit'],
      '#validate' => ['bootstrap_form_system_theme_settings_subtheme_validate'],
    ];

  }

}
