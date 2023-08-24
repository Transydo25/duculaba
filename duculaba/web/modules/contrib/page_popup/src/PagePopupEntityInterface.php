<?php

namespace Drupal\page_popup;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining a page_popup_entity entity.
 */
interface PagePopupEntityInterface extends ConfigEntityInterface {

  /**
   * Gets the sort weight of the page popup rule.
   *
   * @return int
   *   The pagePopupRule record sort weight.
   */
  public function getWeight();

  /**
   * Gets the message title.
   *
   * @return string
   *   The message title.
   */
  public function getMessageTitle();

  /**
   * Gets the message body.
   *
   * @return string
   *   The message body.
   */
  public function getMessageBody();

  /**
   * Return the pages.
   *
   * @return array
   *   The pages.
   */
  public function getVisibility();

  /**
   * Gets the text color.
   *
   * @return string
   *   The text color.
   */
  public function getTextColor();

  /**
   * Gets the background color.
   *
   * @return string
   *   The background color.
   */
  public function getBgColor();

  /**
   * Gets the layout.
   *
   * @return string
   *   The layout.
   */
  public function getLayout();

  /**
   * Gets the delay.
   *
   * @return int
   *   The delay.
   */
  public function getDelay();

  /**
   * Gets the width.
   *
   * @return int
   *   The width.
   */
  public function getWidth();

  /**
   * Gets the height.
   *
   * @return int
   *   The height.
   */
  public function getHeight();

  /**
   * Gets the font size.
   *
   * @return int
   *   The font size.
   */
  public function getFontSize();

}
