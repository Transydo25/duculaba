<?php

namespace Drupal\pwa_firebase\Controller;

use Drupal\Core\Cache\CacheableResponse;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controller routines for help routes.
 */
class PWAController extends ControllerBase {

  /**
   * Function to generate manifest.json.
   */
  public function manifest() {
    $config = \Drupal::config('pwa_firebase.settings');
    $logo = theme_get_setting('logo.url');
    $typeId = exif_imagetype(DRUPAL_ROOT . $logo);
    $type = image_type_to_mime_type($typeId);
    $lang = \Drupal::languageManager()->getDefaultLanguage()->getId();
    $systemSite = \Drupal::config('system.site');
    $manifest = [
      "name" => $systemSite->get('name'),
      "icons" => [
        [
          "src" => $logo,
          "sizes" => "48x48 72x72 96x96 128x128 256x256 512x512",
          "type" => $type ? $type : 'image/png',
          "purpose" => "any",
        ],
      ],
      "start_url" => "/",
      "display" => "standalone",
      "lang" => $lang,
      "gcm_sender_id" => $config->get('firebase_sender_id'),
      // "theme_color" => "#e91e63",
      // "background_color" => '#ffffff',
    ];
    if ($slogan = $systemSite->get('slogan')) {
      $manifest["short_name"] = $slogan;
    }

    $response = new CacheableResponse(
      json_encode($manifest, JSON_UNESCAPED_SLASHES),
      200,
      ['Content-Type' => 'application/json']
    );
    $meta_data = $response->getCacheableMetadata();
    $meta_data->addCacheTags(['manifestjson']);
    $meta_data->addCacheContexts(['languages:language_interface']);
    return $response;

  }

  /**
   * Function to generate service worker.
   *
   * @return \Drupal\Core\Cache\CacheableResponse
   *   A notification for a user.
   */
  public function serviceworker() {

    $response = new CacheableResponse('', 200);
    $config = \Drupal::config('pwa_firebase.settings');

    $firebase_version = $config->get('firebase_version', '9.8.3');

    $importScripts = [
      'importScripts("https://www.gstatic.com/firebasejs/' . $firebase_version . '/firebase-app-compat.js");',
      'importScripts("https://www.gstatic.com/firebasejs/' . $firebase_version . '/firebase-messaging-compat.js");',
    ];
    $firebase_messaging_sw = implode("\n", $importScripts);
    $icon = \Drupal::service('file_url_generator')
      ->generateAbsoluteString(theme_get_setting('logo.url'));

    // Initial Firebase app in the service worker by passing messagingSenderId.
    $firebase_config = [
      'apiKey' => $config->get('firebase_apiKey_id'),
      'authDomain' => $config->get('firebase_project_id') . '.firebaseapp.com',
      'projectId' => $config->get('firebase_project_id'),
      'storageBucket' => $config->get('firebase_project_id') . '.appspot.com',
      'databaseURL' => 'https://' . $config->get('firebase_project_id') . '.firebaseio.com',
      'messagingSenderId' => $config->get('firebase_sender_id'),
      'appId' => $config->get('firebase_app_id'),
    ];
    if (!empty($config->get('firebase_measurement_id'))) {
      $firebase_config['measurementId'] = $config->get('firebase_measurement_id');
    }
    $firebase_messaging_sw .= 'firebase.initializeApp(' . json_encode($firebase_config, JSON_UNESCAPED_SLASHES) . ');';
    // Retrieve an instance of Firebase Messaging so that
    // it can handle background messages.
    $notificationTitle = "Background Message Title";
    $notificationBody = "Background Message Body";
    $firebase_messaging_sw .= '
  const messaging = firebase.messaging();
  messaging.onBackgroundMessage(function(payload) {
    console.log("[firebase-messaging-sw.js] Received background message ",payload);
    const notificationTitle = "' . $notificationTitle . '";
    const notificationOptions = {
        body: "' . $notificationBody . '",
        icon: "' . $icon . '",
    };
    return self.registration.showNotification(
        notificationTitle,
        notificationOptions,
    );
  });
  ';

    $response->setContent($firebase_messaging_sw);
    // $cache_metadata = CacheableMetadata::createFromRenderArray($build);
    // $response->addCacheableDependency($cache_metadata);
    $response->headers->set('Content-type', 'application/javascript');
    $response->headers->set('Service-Worker-Allowed', '/');

    return $response;
  }

  /**
   * Function receives the user token and save's it in the database.
   *
   * @param string $token
   *   Token for each user.
   *
   * @return bool
   *   Sent or not.
   *
   * @throws \Exception
   */
  public function tokenReceived($token = '') {
    if (empty($token)) {
      $token = $_POST['token'];
    }
    if (empty($token)) {
      return new JsonResponse(FALSE);
    }
    $user = \Drupal::currentUser();
    $database = \Drupal::database();
    $hasToken = $database->select('pwa_firebase', 'f')
      ->fields('f')
      ->condition('token', $token, '=')
      ->execute()
      ->fetchAssoc();

    if (!$hasToken) {
      $device = 'pc';
      $iPod = strpos($_SERVER['HTTP_USER_AGENT'], "iPod");
      $iPhone = strpos($_SERVER['HTTP_USER_AGENT'], "iPhone");
      $iPad = strpos($_SERVER['HTTP_USER_AGENT'], "iPad");
      $android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");
      if ($iPad || $iPhone || $iPod) {
        $device = 'ios';
      }
      else {
        if ($android) {
          $device = 'android';
        }
      }

      $database->insert('pwa_firebase')
        ->fields([
          'token' => $token,
          'uid' => \Drupal::currentUser()->id(),
          'device' => $device,
          'created' => date("Y-m-d H:i:s"),
        ])
        ->execute();
      return new JsonResponse(['device' => $device, 'uid' => $user->id()]);
    }
    elseif (!empty($_POST['action']) && $_POST['action'] == 'delete') {
      \Drupal::database()->delete('pwa_firebase')
        ->condition('token', $token)
        ->execute();
      return new JsonResponse(FALSE);
    }
    elseif (empty($hasToken['uid'])) {
      // Update uid with token.
      $database->update('pwa_firebase')
        ->fields(['uid' => $user->id()])
        ->condition('token', $token)
        ->execute();
    }
    return new JsonResponse(FALSE);
  }

}
