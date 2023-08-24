<?php

namespace Drupal\address_suggestion;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Service Class to query.
 *
 * @package Drupal\mymodule\Services
 */
class QueryService {

  /**
   * {@inheritDoc}
   */
  protected $providerManager;

  /**
   * {@inheritDoc}
   */
  public function __construct(AddressProviderManager $provider_manager) {
    $this->providerManager = $provider_manager;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.address_provider')
    );
  }

  /**
   * {@inheritDoc}
   */
  public function getData($entity_type, $bundle, $field_name, $query) {
    $form_mode = 'default';
    $form_display = \Drupal::entityTypeManager()
      ->getStorage('entity_form_display')
      ->load($entity_type . '.' . $bundle . '.' . $form_mode)
      ->getComponent($field_name);
    $settings = $form_display['settings'] ?? [];
    $country = \Drupal::state()->get(
      $stateField = implode('|', [$entity_type, $bundle, $field_name])
    );
    if (!empty($country)) {
      $settings['country'] = $country;
      $settings['countryName'] = \Drupal::state()->get($stateField . '|Country');
    }
    return $this->getProviderResults($query, $settings);
  }

  /**
   * Get Provider Results.
   *
   * @inheritDoc
   */
  public function getProviderResults($string, $settings = []) {
    $plugin_id = $settings['provider'];
    $plugin = $this->providerManager->createInstance($plugin_id);
    return $plugin->processQuery($string, $settings);
  }

}
