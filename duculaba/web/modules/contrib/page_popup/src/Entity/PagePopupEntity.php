<?php

namespace Drupal\page_popup\Entity;

use Drupal\Core\Condition\ConditionPluginCollection;
use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\EntityWithPluginCollectionInterface;
use Drupal\page_popup\PagePopupEntityInterface;

/**
 * Defines the Page Popup Entity.
 *
 * @ConfigEntityType(
 *   id = "page_popup_entity",
 *   label = @Translation("Page Popup"),
 *   module = "page_popup",
 *   handlers = {
 *     "access" = "Drupal\page_popup\Access\PagePopupAccessControlHandler",
 *     "list_builder" = "Drupal\page_popup\Controller\PagePopupEntityListBuilder",
 *     "form" = {
 *       "add" = "Drupal\page_popup\Form\PagePopupEntityForm",
 *       "edit" = "Drupal\page_popup\Form\PagePopupEntityForm",
 *       "delete" = "Drupal\page_popup\Form\PagePopupEntityDeleteForm",
 *       "settings" = "Drupal\page_popup\Form\PagePopupEntitySettingsForm"
 *     }
 *   },
 *   config_prefix = "rule",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "label" = "label",
 *     "status" = "status",
 *     "weight" = "weight",
 *     "message_title" = "message_title",
 *     "message_body" = "message_body",
 *     "popup_text_color" = "popup_text_color",
 *     "popup_bg_color" = "popup_bg_color",
 *     "popup_layout" = "popup_layout",
 *     "popup_delay" = "popup_delay",
 *     "popup_width" = "popup_width",
 *     "popup_height" = "popup_height",
 *     "popup_fontsize" = "popup_fontsize"
 *   },
 *   config_export = {
 *     "uuid",
 *     "id",
 *     "label",
 *     "weight",
 *     "status",
 *     "message_title" = "message_title",
 *     "message_body" = "message_body",
 *     "visibility",
 *     "popup_text_color" = "popup_text_color",
 *     "popup_bg_color" = "popup_bg_color",
 *     "popup_layout" = "popup_layout",
 *     "popup_delay" = "popup_delay",
 *     "popup_width" = "popup_width",
 *     "popup_height" = "popup_height",
 *     "popup_fontsize" = "popup_fontsize"
 *   },
 *   links = {
 *     "edit-form" = "/admin/config/page_popup/{rule}",
 *     "delete-form" = "/admin/config/page_popup/{rule}/delete",
 *     "settings-form" = "/admin/config/page_popup/{rule}/settings",
 *   }
 * )
 */
class PagePopupEntity extends ConfigEntityBase implements PagePopupEntityInterface, EntityWithPluginCollectionInterface {

  /**
   * The ID of the page popup message entity.
   *
   * @var string
   */
  protected $id;

  /**
   * The page popup message entity label.
   *
   * @var string
   */
  protected $label;

  /**
   * The page popup message entity order.
   *
   * @var int
   */
  protected $weight;

  /**
   * The title to apply.
   *
   * @var string
   */
  protected $message_title;

  /**
   * The body to apply.
   *
   * @var string
   */
  protected $message_body;

  /**
   * Switchers instance IDs.
   *
   * @var array
   */
  protected $visibility = [];

  /**
   * The plugin collection that holds the block plugin for this entity.
   *
   * @var \Drupal\block\BlockPluginCollection
   */
  protected $pluginCollection;

  /**
   * The visibility collection.
   *
   * @var \Drupal\Core\Condition\ConditionPluginCollection
   */
  protected $visibilityCollection;

  /**
   * The condition plugin manager.
   *
   * @var \Drupal\Core\Executable\ExecutableManagerInterface
   */
  protected $conditionPluginManager;

  /**
   * The page popup entity popup_text_color.
   *
   * @var string
   */
  protected $popup_text_color;

  /**
   * The page popup entity popup_bg_color.
   *
   * @var string
   */
  protected $popup_bg_color;

  /**
   * The popup_layout to apply.
   *
   * @var string
   */
  protected $popup_layout;

  /**
   * The popup_delay to apply.
   *
   * @var int
   */
  protected $popup_delay;

  /**
   * The popup_width to apply.
   *
   * @var int
   */
  protected $popup_width;

  /**
   * The popup_height to apply.
   *
   * @var int
   */
  protected $popup_height;

  /**
   * The popup_fontsize to apply.
   *
   * @var int
   */
  protected $popup_fontsize;

  /**
   * {@inheritdoc}
   */
  public function getWeight() {
    return $this->weight;
  }

  /**
   * {@inheritdoc}
   */
  public function getMessageTitle() {
    return $this->message_title;
  }

  /**
   * {@inheritdoc}
   */
  public function getMessageBody() {
    return $this->message_body;
  }

  /**
   * {@inheritdoc}
   */
  public function getVisibility() {
    return $this->getVisibilityConditions()->getConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function getPluginCollections() {
    return [
      'visibility' => $this->getVisibilityConditions(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getTextColor() {
    return $this->popup_text_color;
  }

  /**
   * {@inheritdoc}
   */
  public function getBgColor() {
    return $this->popup_bg_color;
  }

  /**
   * {@inheritdoc}
   */
  public function getLayout() {
    return $this->popup_layout;
  }

  /**
   * {@inheritdoc}
   */
  public function getDelay() {
    return $this->popup_delay;
  }

  /**
   * {@inheritdoc}
   */
  public function getWidth() {
    return $this->popup_width;
  }

  /**
   * {@inheritdoc}
   */
  public function getHeight() {
    return $this->popup_height;
  }

  /**
   * {@inheritdoc}
   */
  public function getFontSize() {
    return $this->popup_fontsize;
  }

  /**
   * {@inheritdoc}
   */
  public function getVisibilityConditions() {
    if (!isset($this->visibilityCollection)) {
      $this->visibilityCollection = new ConditionPluginCollection(
        $this->conditionPluginManager(),
        $this->get('visibility')
      );
    }
    return $this->visibilityCollection;
  }

  /**
   * Gets the condition plugin manager.
   *
   * @return \Drupal\Core\Executable\ExecutableManagerInterface
   *   The condition plugin manager.
   */
  protected function conditionPluginManager() {
    if (!isset($this->conditionPluginManager)) {
      $this->conditionPluginManager = \Drupal::service('plugin.manager.condition');
    }
    return $this->conditionPluginManager;
  }

}
