<?php

namespace Drupal\sms_message\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Http\RequestStack;
use Drupal\views\Views;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * This controller will get and publish from app on mobile to server.
 *
 * @package Drupal\sms_message\Controller
 */
class SmsMessageController extends ControllerBase {

  /**
   * Request stack.
   *
   * @var Drupal\Core\Http\RequestStack
   */
  protected $requestStack;

  /**
   * The entity repository.
   *
   * @var \Drupal\Core\Entity\EntityRepositoryInterface
   */
  protected $entityRepository;

  /**
   * Constructs a Controller.
   *
   * @param Drupal\Core\Http\RequestStack $requestStack
   *   A request stack symfony instance.
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entity_repository
   *   The entity repository.
   */
  public function __construct(RequestStack $requestStack, EntityRepositoryInterface $entity_repository) {
    $this->requestStack = $requestStack;
    $this->entityRepository = $entity_repository;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('request_stack'),
      $container->get('entity.repository')
    );
  }

  /**
   * {@inheritDoc}
   */
  public function apiGet($token) {
    $config = $this->config('sms_message.settings');
    if ($token == $config->get('token')) {
      $datas = [];
      $view = Views::getView('sms_all_messages');
      $view->setDisplay('api_get');
      $view->execute();
      if (!empty($view->result)) {
        foreach ($view->result as $row) {
          $data = [];
          foreach ($view->field as $fid => $field) {
            $data[$fid] = $field->getValue($row);
          }
          $datas[] = $data;
        }
      }
      return new JsonResponse($datas);
    }
    return new JsonResponse([
      'message' => $this->t("Your token is not valid"),
      'error' => Response::HTTP_FORBIDDEN,
    ], Response::HTTP_FORBIDDEN);
  }

  /**
   * {@inheritDoc}
   */
  public function apiPost($token) {
    $config = $this->config('sms_message.settings');
    if ($token == $config->get('token')) {
      $uuid = $this->requestStack->getCurrentRequest()->get('uuid');
      $entity = $this->entityRepository->loadEntityByUuid('sms_message', $uuid);
      $entity->set('status', FALSE);
      $entity->save();
      return new JsonResponse(['message' => $this->t('update status')]);
    }
    return new JsonResponse([
      'message' => $this->t("Your token is not valid"),
      'error' => Response::HTTP_FORBIDDEN,
    ], Response::HTTP_FORBIDDEN);
  }

}
