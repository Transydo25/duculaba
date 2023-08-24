<?php

namespace Drupal\address_suggestion\Controller;

use Drupal\address_suggestion\AddressProviderManager;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\State\StateInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a route controller for watches autocomplete form elements.
 */
class AddressSuggestion extends ControllerBase {

  /**
   * {@inheritDoc}
   */
  protected $providerManager;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The state service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * {@inheritDoc}
   */
  public function __construct(AddressProviderManager $provider_manager, EntityTypeManagerInterface $entity_type_manager, StateInterface $state = NULL) {
    $this->providerManager = $provider_manager;
    $this->entityTypeManager = $entity_type_manager;
    $this->state = $state;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.address_provider'),
      $container->get('entity_type.manager'),
      $container->get('state'),
    );
  }

  /**
   * Handler for the autocomplete request.
   */
  public function handleAutocomplete(Request $request, $entity_type, $bundle, $field_name) {
    $results = [];
    $input = Xss::filter($request->query->get('q'));

    if (empty($input)) {
      return new JsonResponse($results);
    }
    $form_mode = 'default';
    $form_display = $this->entityTypeManager->getStorage('entity_form_display')
      ->load($entity_type . '.' . $bundle . '.' . $form_mode)
      ->getComponent($field_name);
    $settings = $form_display['settings'] ?? [];
    $country = $this->state->get(
      $stateField = implode('|', [$entity_type, $bundle, $field_name])
    );
    if (!empty($country)) {
      $settings['country'] = $country;
      $settings['countryName'] = $this->state->get($stateField . '|Country');
    }
    $results = $this->getProviderResults($input, $settings);
    return new JsonResponse($results);
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
