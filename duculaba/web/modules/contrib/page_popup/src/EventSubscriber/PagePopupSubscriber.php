<?php

namespace Drupal\page_popup\EventSubscriber;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Path\PathMatcherInterface;
use Drupal\Core\Render\AttachmentsInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\Core\Condition\ConditionAccessResolverTrait;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Routing\AdminContext;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\path_alias\AliasManagerInterface;
use Drupal\Core\Path\CurrentPathStack;
use Drupal\Core\State\StateInterface;

/**
 * Class PagePopupSubscriber.
 *
 * @package Drupal\page_popup\EventSubscriber
 */
class PagePopupSubscriber implements EventSubscriberInterface {

  use ConditionAccessResolverTrait;

  /**
   * The route admin context to determine whether a route is an admin one.
   *
   * @var \Drupal\Core\Routing\AdminContext
   */
  protected $adminContext;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The path matcher.
   *
   * @var \Drupal\Core\Path\PathMatcherInterface
   */
  protected $pathMatcher;

  /**
   * The route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * The path alias manager.
   *
   * @var \Drupal\Core\Path\AliasManagerInterface
   */
  protected $aliasManager;

  /**
   * The current path.
   *
   * @var \Drupal\Core\Path\CurrentPathStack
   */
  protected $currentPath;

  /**
   * The state keyvalue collection.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * {@inheritdoc}
   */
  public function __construct(AdminContext $admin_context, EntityTypeManagerInterface $entity_type_manager, PathMatcherInterface $pathMatcher, RouteMatchInterface $route_match, AliasManagerInterface $alias_manager, CurrentPathStack $current_path, StateInterface $state) {
    $this->adminContext = $admin_context;
    $this->entityTypeManager = $entity_type_manager;
    $this->path_matcher = $pathMatcher;
    $this->routeMatch = $route_match;
    $this->aliasManager = $alias_manager;
    $this->currentPath = $current_path;
    $this->state = $state;
  }

  /**
   * Init PopupMessage.
   *
   * @param \Symfony\Component\HttpKernel\Event\ResponseEvent $event
   *   PopupMessage event.
   */
  public function showPopupMessage(ResponseEvent $event) {
    // Check permissions to display message.
    $response = $event->getResponse();

    if (!$response instanceof AttachmentsInterface) {
      return;
    }
    $storage = $this->entityTypeManager->getStorage('page_popup_entity');
    $configEntities = $storage->getQuery()->sort('weight', 'ASC')->execute();
    $rules = $storage->loadMultiple($configEntities);
    foreach ($rules as $rule) {
      if ($rule->status()) {
        $route = $this->routeMatch->getRouteObject();
        $is_admin_route = $this->adminContext->isAdminRoute($route);
        if (!$is_admin_route) {
          if ($this->state->get('page_popup.mode')) {
            $message_title = '';
          }
          else {
            $message_title = Xss::filter($rule->getMessageTitle());
          }
          $message_body = $rule->getMessageBody();
          $text_color = !empty($rule->getTextColor()) ? $rule->getTextColor() : '#000000';
          $bg_color = !empty($rule->getBgColor()) ? $rule->getBgColor() : '#FFFFFF';
          $layout = !empty($rule->getLayout()) ? $rule->getLayout() : 0;
          $delay = !empty($rule->getDelay()) ? $rule->getDelay() : 0;
          $width = !empty($rule->getWidth()) ? $rule->getWidth() : 400;
          $height = !empty($rule->getHeight()) ? $rule->getHeight() : 200;
          $fontsize = !empty($rule->getFontsize()) ? $rule->getFontsize() : 20;
          $popup_message_parameters = [
            'title' => $message_title,
            'body' => $message_body,
            'text_color' => $text_color,
            'bg_color' => $bg_color,
            'layout' => $layout,
            'delay' => $delay,
            'width' => $width,
            'height' => $height,
            'font_size' => $fontsize,
          ];
          $visibility_page = $rule->getVisibility()['request_path']['pages'];
          $current_path = $this->currentPath->getPath();
          $alias_path = $this->aliasManager->getAliasByPath($current_path);
          if (substr($alias_path, 0, 1) == '/') {
            $path = substr($alias_path, 1, strlen($alias_path) - 1);
          }
          else {
            $path = $alias_path;
          }
          if ($this->path_matcher->matchPath($path, $visibility_page) || $this->path_matcher->matchPath($alias_path, $visibility_page)) {
            if ($message_title || $message_body) {
              $attachments = $response->getAttachments();
              $attachments['library'][] = 'page_popup/popup_message_style';
              $attachments['drupalSettings']['pagePopup'] = $popup_message_parameters;
              $response->setAttachments($attachments);
            }
          }
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::RESPONSE][] = ['showPopupMessage', 20];

    return $events;
  }

}
