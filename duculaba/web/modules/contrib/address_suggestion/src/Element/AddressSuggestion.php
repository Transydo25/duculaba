<?php

namespace Drupal\address_suggestion\Element;

use Drupal\Core\Locale\CountryManager;
use Drupal\address\Element\Address;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides an address_suggestion form element.
 *
 * Usage example:
 *
 * @code
 * $form['address_suggestion'] = [
 *   '#type' => 'address_suggestion',
 * ];
 * @endcode
 *
 * @FormElement("address_suggestion")
 */
class AddressSuggestion extends Address {

  /**
   * {@inheritDoc}
   */
  public function getInfo() {
    $info = parent::getInfo();

    $info['#process'][] = [
      get_class($this),
      'processAutocomplete',
    ];

    return $info;
  }

  /**
   * {@inheritDoc}
   */
  public static function processAutocomplete(&$element, FormStateInterface $form_state, &$complete_form) {
    $element["#attached"]["library"][] = 'address_suggestion/address_suggestion';
    $element["address_line1"]['#autocomplete_route_name'] = 'address_suggestion.addresses';
    $element["address_line1"]['#autocomplete_route_parameters'] = $parameters = [
      'entity_type' => $element["#entity_type"],
      'bundle' => $element["#bundle"],
      'field_name' => $element["#field_name"],
    ];
    $values = $form_state->getValue($element["#field_name"]);
    if (!empty($values[0]) && !empty($values[0]["address"]["country_code"])) {
      $listCountry = CountryManager::getStandardList();
      $country = $values[0]["address"]["country_code"];
      \Drupal::state()->set($stateField = implode('|', $parameters), $country);
      if (!empty($listCountry[$country])) {
        $country = (string) $listCountry[$country];
      }
      \Drupal::state()->set($stateField . '|Country', $country);
      $element["address_line1"]['#autocomplete_route_parameters']['country'] = $values[0]["address"]["country_code"];
    }
    $element["address_line1"]["#attributes"]['placeholder'] = t('Please start typing your address...');
    return $element;
  }

}
