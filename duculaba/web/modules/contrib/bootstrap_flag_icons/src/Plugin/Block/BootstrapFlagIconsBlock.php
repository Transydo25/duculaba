<?php

namespace Drupal\bootstrap_flag_icons\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\language\Plugin\Block\LanguageBlock;

/**
 * Provides an example block.
 *
 * @Block(
 *   id = "bootstrap_flag_icons_block",
 *   admin_label = @Translation("Bootstrap Language switcher"),
 *   category = @Translation("System"),
 *   deriver =
 *   "Drupal\bootstrap_flag_icons\Plugin\Derivative\BootstrapFlagIconsBlock"
 * )
 */
class BootstrapFlagIconsBlock extends LanguageBlock {

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $config = $this->getConfiguration();
    $settings = $config['bootstrap_language'];

    $form['bootstrap_language'] = [
      '#type' => 'details',
      '#title' => $this->t('Bootstrap settings'),
      '#open' => TRUE,
    ];

    $form['bootstrap_language']['dropdown_style'] = [
      '#type' => 'select',
      '#title' => $this->t('Dropdown display style'),
      '#options' => [
        'all' => $this->t('Icons and text'),
        'icons' => $this->t('Only icons'),
      ],
      '#default_value' => !empty($settings['dropdown_style']) ? $settings['dropdown_style'] : 'all',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();
    $this->configuration['bootstrap_language'] = $values['bootstrap_language'];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];

    $settings = $this->configuration['bootstrap_language'];

    $route_name = $this->pathMatcher->isFrontPage() ? '<front>' : '<current>';
    $type = $this->getDerivativeId();
    $links = $this->languageManager->getLanguageSwitchLinks($type, Url::fromRoute($route_name));

    if (isset($links->links)) {
      foreach ($links->links as &$langLinks) {
        $langLinks["attributes"]['data-mode'] = $settings['dropdown_style'];
      }
      $build = [
        '#theme' => 'links__bootstrap_flag_icons_block',
        '#links' => $links->links,
        '#attributes' => [
          'class' => [
            'dropdown',
            "language-switcher-{$links->method_id}",
            !empty($settings['dropdown_style']) ? "{$settings['dropdown_style']}-dropdown-style" : 'all-dropdown-style',
          ],
        ],
        '#set_active_class' => TRUE,
        '#attached' => [
          'library' => [
            'bootstrap_flag_icons/bootstrap_flag_icons',
          ],
        ],
      ];
    }
    return $build;
  }

}
