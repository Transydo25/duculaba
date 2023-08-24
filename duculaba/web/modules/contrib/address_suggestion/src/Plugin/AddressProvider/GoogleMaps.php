<?php

namespace Drupal\address_suggestion\Plugin\AddressProvider;

use Drupal\address_suggestion\AddressProviderBase;
use Drupal\Component\Serialization\Json;

/**
 * Defines a GoogleMaps plugin for address_autocomplete.
 *
 * @package Drupal\address_suggestion\Plugin\AddressProvider
 *
 * @AddressProvider(
 *   id = "google_maps",
 *   label = @Translation("Google Maps"),
 * )
 */
class GoogleMaps extends AddressProviderBase {

  /**
   * {@inheritDoc}
   */
  public function processQuery($string, $settings) {
    $results = [];

    $url = !empty($settings['endpoint']) ? $settings['endpoint'] : 'https://maps.googleapis.com/maps/api/geocode/json';
    if(empty($string) && empty($settings['api_key'])){
      return $results;
    }
    $query = [
      'key' => $settings['api_key'],
      'address' => $string,
      'language' => \Drupal::languageManager()->getCurrentLanguage()->getId(),
    ];
    if (!empty($settings['countryName'])) {
      $query['components'] = 'country:' . $settings['countryName'];
    }
    if (!empty($settings['country'])) {
      $country = $settings['country'];
    }

    $response = $this->client->request('GET', $url, [
      'query' => $query,
    ]);

    $content = Json::decode($response->getBody());

    if (!empty($content["error_message"])) {
      return $results;
    }

    foreach ($content["results"] as $key => $result) {
      foreach ($result["address_components"] as $component) {
        switch ($component["types"][0]) {
          case "street_number":
            $streetNumber = $component["long_name"];
            break;

          case "route":
            $results[$key]["street_name"] = $component["long_name"];
            break;

          case "locality":
            $results[$key]["town_name"] = $component["long_name"];
            break;

          case "administrative_area_level_1":
            $results[$key]["administrative_area"] = $component["short_name"];
            break;

          case "postal_code":
            $results[$key]["zip_code"] = $component["long_name"];
            break;
        }
      }

      // Some country have format Street number street name.
      $countryFormatSpecial = ['FR', 'CA', 'IE', 'IN', 'IL', 'HK', 'MY', 'OM',
        'NZ', 'PH', 'SA', 'SE', 'SG', 'LK', 'TH', 'UK', 'US', 'VN',
      ];
      if (!empty($country) && in_array($country, $countryFormatSpecial)) {
        $results[$key]['street_name'] = $streetNumber ?? '' . ' ' . $results[$key]['street_name'];
      }
      elseif (!empty($streetNumber)) {
        $results[$key]['street_name'] .= ', ' . $streetNumber;
      }

      $results[$key]["label"] = $result["formatted_address"];
      if (!empty($result["geometry"])) {
        $results[$key]['location'] = [
          'longitude' => $result['geometry']['location']['lng'],
          'latitude' => $result['geometry']['location']['lat'],
        ];
      }
    }

    return $results;
  }

}
