<?php

namespace Drupal\address_suggestion\Plugin\AddressProvider;

use Drupal\address_suggestion\AddressProviderBase;
use Drupal\Component\Serialization\Json;

/**
 * Defines a Mapbox Geocoding plugin for address_autocomplete.
 *
 * @package Drupal\address_suggestion\Plugin\AddressProvider
 * @AddressProvider(
 *   id = "mapbox_geocoding",
 *   label = @Translation("Mapbox Geocoding"),
 * )
 */
class MapboxGeocoding extends AddressProviderBase {

  /**
   * {@inheritDoc}
   */
  public function processQuery($string, $settings) {
    $results = [];

    if (!empty($settings['countryName'])) {
      $string .= ', ' . $settings['countryName'];
    }
    if (!empty($settings['country'])) {
      $country = $settings['country'];
    }
    $token = $settings['api_key'];
    $url = !empty($settings['endpoint']) ? $settings['endpoint'] : 'https://api.mapbox.com/geocoding/v5/mapbox.places/';
    $url .= $string . '.json';
    $size = !empty($settings['limit']) ? $settings['limit'] : 10;
    $query = [
      'access_token' => $token,
      'autocomplete' => 'true',
      'types' => 'address',
      'limit' => $size,
    ];

    $url .= '?' . http_build_query($query);

    $response = $this->client->request('GET', $url);
    $content = Json::decode($response->getBody());
    // Some country have format Street number street name.
    $countryFormatSpecial = ['FR', 'CA', 'IE', 'IN', 'IL', 'HK', 'MY', 'OM',
      'NZ', 'PH', 'SA', 'SE', 'SG', 'LK', 'TH', 'UK', 'US', 'VN',
    ];

    foreach ($content["features"] as $key => $feature) {
      $results[$key]['street_name'] = $feature["text"];
      if (!empty($feature["address"])) {
        if (!empty($country) && in_array($country, $countryFormatSpecial)) {
          $results[$key]['street_name'] = $feature["address"] . ' ' . $results[$key]['street_name'];
        }
        else {
          $results[$key]['street_name'] .= isset($feature["address"]) ? ', ' . $feature["address"] : '';
        }
      }

      $results[$key]['town_name'] = $feature["context"][1]["text"];
      $results[$key]['zip_code'] = $feature["context"][0]["text"];
      $results[$key]['label'] = $feature["place_name"];
      $results[$key]['location'] = [
        'longitude' => $feature["geometry"]["coordinates"][0],
        'latitude' => $feature["geometry"]["coordinates"][1],
      ];
    }

    return $results;
  }

}
