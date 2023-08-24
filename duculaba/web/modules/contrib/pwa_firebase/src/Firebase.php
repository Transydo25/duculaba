<?php

namespace Drupal\pwa_firebase;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;
use GuzzleHttp\ClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Service to send notification.
 */
class Firebase {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * HTTP client.
   *
   * @var \GuzzleHttp\Client
   */
  protected $client;

  /**
   * Database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  private $connection;

  /**
   * Constructs a FirebaseServiceBase object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The factory for configuration objects.
   * @param \GuzzleHttp\ClientInterface $client
   *   An HTTP client.
   * @param \Drupal\Core\Database\Connection $connection
   *   Database connection.
   */
  public function __construct(ConfigFactoryInterface $configFactory, ClientInterface $client, Connection $connection) {
    $this->configFactory = $configFactory;
    $this->client = $client;
    $this->connection = $connection;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('http_client'),
      $container->get('database')
    );
  }

  /**
   * Function to send a notification to all the users.
   */
  public function sendMessageToAllUsers($title = '', $message = '', $url = NULL, $option = []) {

    $rows = $this->connection->select('pwa_firebase', 'f')
      ->fields('f', ['token', 'device'])
      ->execute()
      ->fetchAll();
    $this->prepareSend($rows, $title, $message, $url, $option);
  }

  /**
   * Function to send a notification to 1 user.
   */
  public function sendMessageToUser($uid, $title, $message, $url = NULL, $option = []) {

    $rows = $this->connection->select('pwa_firebase', 'f')
      ->fields('f', ['token', 'device'])
      ->condition('uid', $uid)
      ->execute()
      ->fetchAll();
    $this->prepareSend($rows, $title, $message, $url, $option);
  }

  /**
   * {@inheritDoc}
   */
  protected function prepareSend($rows, $title, $message, $url = NULL, $option = []) {
    $tokens = [];
    foreach ($rows as $row) {
      $token = $row->token;
      $device_type = 'pc';
      if (!empty($row->device)) {
        $device_type = $row->device;
      }
      if (!isset($tokens[$device_type])) {
        $tokens[$device_type] = [];
      }
      $tokens[$device_type][] = $token;
    }
    if (!empty($tokens)) {
      foreach ($tokens as $device => $token) {
        $option['device_type'] = $device;
        $this->sendNotification($token, $title, $message, $url, $option);
      }
    }
  }

  /**
   * Function send message to firebase.
   */
  public function sendNotification($token, $title, $message, $url = NULL, $option = []) {
    global $base_url;
    global $base_path;

    $config = $this->configFactory->get('pwa_firebase.settings');
    if (strlen($title) > 255) {
      $title = substr($title, 0, 254) . '…';
    }
    if (strlen($message) > 1024) {
      $message = substr($message, 0, 1023) . '…';
    }
    if (empty($url)) {
      $url = $base_url . $base_path;
    }

    $headers = [
      'Content-Type' => 'application/json',
      'Accept' => 'application/json',
      'Authorization' => 'key=' . $config->get('firebase_server_key'),
    ];

    $logo = theme_get_setting('logo.url');
    // Note: this is the image that is used for the manifest file.
    $icon = !empty($option['image']) ? $base_url . $base_path . $option['image'] : $logo;

    \Drupal::moduleHandler()->alter('pwa_firebase_send', $option);
    $notification = [
      'body' => $message,
      'title' => $title,
      'icon' => $icon,
      'click_action' => $url,
    ];
    foreach ($notification as $key => $value) {
      if (!empty($option[$key])) {
        $notification[$key] = $option[$key];
        unset($option[$key]);
      }
    }
    $data = [
      "content_available" => !empty($option['content_available']) == 1 ? FALSE : TRUE,
      "priority" => !empty($option['priority']) ? $option['priority'] : 'high',
      "notification" => $notification,
      "data" => $option,
    ];
    if (is_array($token)) {
      if (count($token) == 1) {
        $data["to"] = current($token);
      }
      else {
        $data["registration_ids"] = $token;
      }
    }
    else {
      $data["to"] = $token;
    }

    $response = $this->client->post($config->get('firebase_endpoint'), [
      'headers' => $headers,
      'body' => Json::encode($data),
    ]);

    $result = Json::decode($response->getBody());

    if (!empty($result["failure"])) {
      foreach ($result["results"] as $index => $value) {
        if (!empty($value['error'])) {
          \Drupal::messenger()->addError($value['error']);
          // @todo remove token invalidate.
          /* $this->connection->delete('pwa_firebase')
          ->condition('token', $token[$index])
          ->execute();*/
        }
      }
    }
    return $result;
  }

}
