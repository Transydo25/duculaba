<?php

namespace Drupal\sms_message\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the sms message entity edit forms.
 */
class SmsMessageForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $result = parent::save($form, $form_state);

    $entity = $this->getEntity();

    $message_arguments = ['%label' => $entity->get('number')->value];
    $logger_arguments = [
      '%label' => $entity->get('number')->value,
      'link' => $entity->toLink($this->t('View'))->toString(),
    ];

    switch ($result) {
      case SAVED_NEW:
        $this->messenger()->addStatus($this->t('New sms message %label has been created.', $message_arguments));
        $this->logger('sms_message')->notice('Created new sms message %label', $logger_arguments);
        break;

      case SAVED_UPDATED:
        $this->messenger()->addStatus($this->t('The sms message %label has been updated.', $message_arguments));
        $this->logger('sms_message')->notice('Updated sms message %label.', $logger_arguments);
        break;
    }

    $form_state->setRedirect('entity.sms_message.canonical', ['sms_message' => $entity->id()]);

    return $result;
  }

}
