<?php

namespace Drupal\n1ed\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Provides route responses for Flmngr file manager.
 */
class N1EDController extends ControllerBase {

  /**
   * Drupal\Core\Config\ConfigFactoryInterface definition.
   *
   * @var Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Symfony\Component\HttpFoundation\RequestStack definition.
   *
   * @var Symfony\Component\HttpFoundation\RequestStack
   */
  private $requestStack;

  /**
   * {@inheritdoc}
   */
  public function __construct(ConfigFactoryInterface $config_factory,
      RequestStack $request_stack) {
    $this->configFactory = $config_factory;
    $this->requestStack = $request_stack;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('request_stack')
    );
  }

  /**
   * Sets API key into Drupal config.
   */
  public function setApiKey() {
    $apiKey = $this->requestStack->getCurrentRequest()->request->get("n1edApiKey");
    $token = $this->requestStack->getCurrentRequest()->request->get("n1edToken");

    if ($apiKey == NULL || $token == NULL) {
      throw new AccessDeniedHttpException();
    }

    $config = $this->configFactory->getEditable('n1ed.settings');
    $config->set('apikey', $apiKey);
    $config->set('token', $token);
    $config->set('integrationType', 'n1ed');
    $config->save(TRUE);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,"https://cloud.n1ed.com/api/v1/conf/get-integration-type");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('apiKey' => $apiKey)));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT,3000);
    $json = curl_exec($ch);
    $isError = curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200;
    curl_close($ch);

    if (!$isError) {
      $json = json_decode($json);
      if (!(isset($json->data)) || (isset($json->error) && $json->error !== NULL)) {
        error_log("Unable to parse a response with integrationType");
        $isError = TRUE;
      }
    } else {
      error_log("Unable get a response with integrationType");
    }
    if (!$isError) {
      $config = $this->configFactory->getEditable('n1ed.settings');
      $config->set('integrationType', $json->data);
      $config->save(TRUE);
    }

    return new Response();
  }

  /**
   * Toggles on the option defining attaching file manager to Drupa file fields.
   */
  public function toggleUseFlmngrOnFileFields() {
    $useFlmngrOnFileFields = json_decode(file_get_contents('php://input'))->useFlmngrOnFileFields;
    $config = $this->configFactory->getEditable('n1ed.settings');
    $config->set('useFlmngrOnFileFields', $useFlmngrOnFileFields);
    $config->save(TRUE);
    return new Response();
  }

  /**
   * Toggles on the option defining attaching file manager to Drupa file fields.
   */
  public function toggleUseLegacyFlmngrBackend() {
    $useLegacyFlmngrBackend = json_decode(file_get_contents('php://input'))->useLegacyFlmngrBackend;
    $config = $this->configFactory->getEditable('n1ed.settings');
    $config->set('useLegacyFlmngrBackend', $useLegacyFlmngrBackend);
    $config->save(TRUE);
    return new Response();
  }

  public function getPluginJs() {
    $name = __DIR__ . '/../../js/ckeditor4_plugins/N1ED-editor/plugin.js';
    $fp = fopen($name, 'rb');
    header("Content-Type: text/javascript");
    header("Content-Length: " . filesize($name));
    fpassthru($fp);
    exit;
  }

}
