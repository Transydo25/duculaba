<?php

namespace Drupal\address_suggestion\Plugin\Field\FieldWidget;

use Drupal\address\Plugin\Field\FieldWidget\AddressDefaultWidget;
use Drupal\Core\Field\FieldConfigInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'address_autocomplete' widget.
 *
 * @FieldWidget(
 *   id = "address_suggestion",
 *   label = @Translation("Address suggestion"),
 *   field_types = {
 *     "address"
 *   }
 * )
 */
class AddressSuggestionWidget extends AddressDefaultWidget {

  /**
   * {@inheritDoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);
    $element['address']['#type'] = 'address_suggestion';
    $fieldDefinitions = $items->getFieldDefinition();
    $element['address']['#entity_type'] = $fieldDefinitions->getTargetEntityTypeId();
    $element['address']['#bundle'] = $fieldDefinitions->getTargetBundle();
    $element['address']['#field_name'] = $fieldDefinitions->getName();
    $settings = $this->getSettings();
    if (!empty($this->getSetting('location_field'))) {
      $entity_type = $this->fieldDefinition->getTargetEntityTypeId();
      $bundle = $this->fieldDefinition->getTargetBundle();
      $entityFieldManager = \Drupal::service('entity_field.manager');
      $fieldDefinitions = $entityFieldManager->getFieldDefinitions($entity_type, $bundle);
      $field_name = $this->getSetting('location_field');
      $settings['type_field'] = $fieldDefinitions[$field_name]->getType();
    }
    $form['#attached']['drupalSettings']['address_suggestion'] = $settings;

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'location_field' => '',
      'provider' => '',
      'endpoint' => '',
      'api_key' => '',
      'username' => '',
      'password' => '',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $options = [];
    $entity_type = $form["#entity_type"];
    $bundle = $form["#bundle"];
    $entityFieldManager = \Drupal::service('entity_field.manager');
    $fieldDefinitions = $entityFieldManager->getFieldDefinitions($entity_type, $bundle);
    $userInput = $form_state->getUserInput();
    $typSupport = ['geofield_latlon', 'geolocation_latlng'];
    if (!empty($userInput["fields"])) {
      foreach ($userInput["fields"] as $field_name => $field_widget) {
        if (!empty($field_widget['type']) &&
          in_array($field_widget['type'], $typSupport)) {
          $options[$field_name] = (string) $fieldDefinitions[$field_name]->getLabel();
        }
      }
    }
    $moduleSupport = ['geolocation', 'geofield'];
    if (empty($options)) {
      foreach ($fieldDefinitions as $field_name => $field_definition) {
        if ($field_definition instanceof FieldConfigInterface &&
          in_array($field_definition->getType(), $moduleSupport)) {
          $options[$field_name] = (string) $field_definition->getLabel();
        }
      }
    }
    if (!empty($options)) {
      $element['location_field'] = [
        '#type' => 'select',
        '#title' => $this->t('Location field'),
        '#default_value' => $this->getSetting('location_field'),
        '#options' => $options,
        "#empty_option" => $this->t('- Select field -'),
        "#description" => $this->t('You can attach a location field to get the coordinates'),
      ];
    }

    $options = [
      "vnpost" => $this->t("Vietnam Post"),
      "photon" => $this->t("Photon Komoot"),
      "nominatim" => $this->t("Nominatim Openstreetmap"),
      "france_address" => $this->t("France Address"),
      "google_maps" => $this->t("Google Maps"),
      "mapbox_geocoding" => $this->t("Mapbox Geocoding"),
      "post_ch" => $this->t("Post CH"),
    ];
    $element['provider'] = [
      '#type' => 'select',
      '#title' => $this->t('Provider'),
      '#default_value' => $this->getSetting('provider'),
      '#options' => $options,
      "#empty_option" => $this->t('- Select provider -'),
    ];
    $endPointUrl = [
      $this->t("Vietnam Post: %endPoint", ['%endPoint' => 'https://maps.vnpost.vn/api/autocomplete']),
      $this->t("Photon Komoot: %endPoint", ['%endPoint' => 'https://photon.komoot.io/api/']),
      $this->t("Nominatim Openstreetmap: %endPoint", ['%endPoint' => 'http://nominatim.openstreetmap.org/search/']),
      $this->t("France Address: %endPoint", ['%endPoint' => 'https://api-adresse.data.gouv.fr/search/']),
      $this->t("Mapbox Geocoding: %endPoint", ['%endPoint' => 'https://api.mapbox.com/geocoding/v5/mapbox.places/']),
      $this->t("Post CH: %endPoint", ['%endPoint' => 'https://post.ch/']),
      $this->t("Google Maps: %endPoint", ['%endPoint' => 'https://maps.googleapis.com/maps/api/geocode/json']),
    ];
    $element['endpoint'] = [
      '#type' => 'url',
      '#title' => $this->t('Custom API Address endpoint'),
      '#default_value' => $this->getSetting('endpoint'),
      "#description" => implode('<br/>', $endPointUrl),
    ];
    $element['api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API Key'),
      '#default_value' => $this->getSetting('api_key'),
      "#description" => $this->t('Required for provider:') . implode(', ', [
        $this->t("Vietnam Post"),
        $this->t("Mapbox Geocoding"),
        $this->t("Google Maps"),
      ]),
    ];
    $element['username'] = [
      '#type' => 'textfield',
      '#title' => $this->t('post.ch API username'),
      '#default_value' => $this->getSetting('username'),
      '#states' => [
        'visible' => [
          ':input[name="fields[field_address][settings_edit_form][settings][provider]"]' => ['value' => 'post_ch'],
        ],
      ],
    ];
    $element['password'] = [
      '#type' => 'password',
      '#title' => $this->t('post.ch API password'),
      '#default_value' => $this->getSetting('password'),
      '#states' => [
        'visible' => [
          ':input[name="fields[field_address][settings_edit_form][settings][provider]"]' => ['value' => 'post_ch'],
        ],
      ],
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    if (!empty($this->getSetting('location_field'))) {
      $summary[] = $this->t('Location field: @field', ['@field' => $this->getSetting('location_field')]);
    }
    if (!empty($this->getSetting('provider'))) {
      $summary[] = $this->t('Provider: @provider', ['@provider' => $this->getSetting('provider')]);
    }
    return $summary;
  }

}
