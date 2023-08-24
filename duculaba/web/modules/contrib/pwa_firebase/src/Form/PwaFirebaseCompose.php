<?php

namespace Drupal\pwa_firebase\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Configuration form firebase.
 */
class PwaFirebaseCompose extends FormBase {

  /**
   * Name of the config.
   *
   * @var string
   */
  public static $configName = 'pwa_firebase.compose';

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [self::$configName];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'pwa_firebase_compose_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = \Drupal::config('pwa_firebase.settings');
    if (empty($config->get('firebase_apiKey_id'))) {
      $routeNameCurrent = Url::fromRoute(\Drupal::routeMatch()->getRouteName())
        ->toString();
      $url = Url::fromRoute('pwa_firebase.configuration', [], ['query' => ['destination' => $routeNameCurrent]])
        ->setAbsolute()->toString();
      return new RedirectResponse($url);
    }

    $form['#attached']['library'][] = 'pwa_firebase/pwa_firebase_composer';
    $form['title'] = [
      "#type" => 'textfield',
      '#title' => $this->t('Title'),
      '#required' => TRUE,
      "#maxlength" => 1024,
    ];
    $form['pwa_firebase_phonebook'] = [
      '#type' => 'details',
      '#title' => $this->t('User list'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    ];
    $searchText = $this->t('Search');
    $form['pwa_firebase_phonebook']['checkAll'] = [
      '#type' => 'markup',
      '#markup' => Markup::create('
          <div class="form-inline">
              <div class="form-group">
                  <input type="checkbox" id="checkAll" class="form-check-input checkAll"/> <label for="checkAll">' . $this->t('Check All') . '</label>
               </div>
               <div class="form-group">
                  <input type="search" id="phonebook_search" class="phonebook_search form-control" placeholder="' . $searchText . '"/>
               </div>
           </div>'),
    ];

    $form['pwa_firebase_phonebook']["user"] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Users'),
      '#options' => $this->getUserHasToken(),
    ];
    $form['#tree'] = TRUE;
    $form['message'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Message'),
      '#required' => TRUE,
      "#maxlength" => 254,
    ];
    $form['url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('url'),
      "#maxlength" => 555,
    ];

    // Add a submit button that handles the submission of the form.
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Send notification'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $users = array_filter($values["pwa_firebase_phonebook"]["user"]);
    if (empty($users)) {
      \Drupal::service('pwa_firebase.send')
        ->sendMessageToAllUsers($values['title'], $values['message'], $values['url']);
      \Drupal::messenger()
        ->addMessage($this->t("Notification sent to all users"));
    }
    else {
      foreach ($users as $user) {
        \Drupal::service('pwa_firebase.send')
          ->sendMessageToUser($user, $values['title'], $values['message'], $values['url']);
      }
      \Drupal::messenger()
        ->addMessage($this->t("Notification sent to selected users"));
    }
  }

  /**
   * Get the user available has a token.
   */
  private function getUserHasToken() {
    $database = \Drupal::database();
    $query = $database->select('pwa_firebase', 'f');
    $query->fields('f', ['uid']);
    $userHasToken = $query->distinct()->execute()->fetchCol();

    $query = $database->select('users_field_data', 'u')
      ->fields('u', ['uid', 'name'])
      ->condition('status', 1);
    if (!empty($userHasToken)) {
      $query->condition('u.uid', $userHasToken, 'IN');
    }
    return $query->execute()->fetchAllKeyed(0, 1);
  }

}
