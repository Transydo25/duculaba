<?php

namespace Drupal\sms_message\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\sms_message\SmsMessageInterface;
use Drupal\user\EntityOwnerTrait;

/**
 * Defines the sms message entity class.
 *
 * @ContentEntityType(
 *   id = "sms_message",
 *   label = @Translation("SMS Message"),
 *   label_collection = @Translation("SMS Messages"),
 *   label_singular = @Translation("sms message"),
 *   label_plural = @Translation("sms messages"),
 *   label_count = @PluralTranslation(
 *     singular = "@count sms messages",
 *     plural = "@count sms messages",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\sms_message\SmsMessageListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "access" = "Drupal\sms_message\SmsMessageAccessControlHandler",
 *     "form" = {
 *       "add" = "Drupal\sms_message\Form\SmsMessageForm",
 *       "edit" = "Drupal\sms_message\Form\SmsMessageForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     }
 *   },
 *   base_table = "sms_message",
 *   admin_permission = "administer sms message",
 *   entity_keys = {
 *     "id" = "id",
 *     "number" = "number",
 *     "uuid" = "uuid",
 *     "owner" = "uid",
 *     "published" = "status",
 *   },
 *   links = {
 *     "collection" = "/admin/content/sms-message",
 *     "add-form" = "/sms-message/add",
 *     "canonical" = "/sms-message/{sms_message}",
 *     "edit-form" = "/sms-message/{sms_message}/edit",
 *     "delete-form" = "/sms-message/{sms_message}/delete",
 *   },
 *   field_ui_base_route = "entity.sms_message.settings",
 * )
 */
class SmsMessage extends ContentEntityBase implements SmsMessageInterface {

  use EntityChangedTrait;
  use EntityOwnerTrait;

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);
    if (!$this->getOwnerId()) {
      // If no owner has been set explicitly, make the anonymous user the owner.
      $this->setOwnerId(0);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['number'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Number'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 15)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Status'))
      ->setDefaultValue(TRUE)
      ->setSetting('on_label', 'Enabled')
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => FALSE,
        ],
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'boolean',
        'label' => 'above',
        'weight' => 0,
        'settings' => [
          'format' => 'enabled-disabled',
        ],
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['message'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Message'))
      ->setDisplayOptions('form', [
        'type' => 'string_textarea',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'text_default',
        'label' => 'above',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Author'))
      ->setSetting('target_type', 'user')
      ->setDefaultValueCallback(static::class . '::getDefaultEntityOwner')
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ],
        'weight' => 15,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'author',
        'weight' => 15,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Authored on'))
      ->setDescription(t('The time that the sms message was created.'))
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the sms message was last edited.'));

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished() {
    $key = $this->getEntityType()->getKey('published');
    $this->set($key, TRUE);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setUnpublished() {
    $key = $this->getEntityType()->getKey('published');
    $this->set($key, FALSE);

    return $this;
  }

}
