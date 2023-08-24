<?php

namespace Drupal\address_suggestion;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use GuzzleHttp\Client;

/**
 * Base class for Address provider plugins.
 */
abstract class AddressProviderBase extends PluginBase implements AddressProviderInterface, ContainerFactoryPluginInterface {

  /**
   * {@inheritDoc}
   */
  protected $client;

  /**
   * {@inheritDoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->client = new Client();
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    // Swap config with a values from config object.
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
    );
  }

  /**
   * {@inheritDoc}
   */
  public function defaultConfiguration() {
    return [
      'plugin_id' => $this->pluginId,
    ];
  }

}
