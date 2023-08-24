<?php

namespace Drupal\Tests\contact_emails\Functional;

/**
 * Base class for contact emails tests.
 *
 * @group contact_emails
 */
trait ContactEmailsTestBaseTrait {

  /**
   * The admin user.
   *
   * @var bool|\Drupal\user\UserInterface
   */
  protected $adminUser = FALSE;

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();
    $this->createUserAndLogin();
    $this->createBaseContactForm();
  }

  /**
   * Creates the admin user and logs in.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function createUserAndLogin(): void {
    // Create the user.
    $this->adminUser = $this->createUser([], NULL, TRUE);
    $this->drupalLogin($this->adminUser);
  }

  /**
   * Creates a base contact form for use in all tests.
   */
  protected function createBaseContactForm(): void {
    // Create a contact form.
    $params = [
      'label' => 'Contact Emails Test Form',
      'id' => 'contact_emails_test_form',
      'message' => 'Your message has been sent.',
      'recipients' => 'test@example.com',
      'contact_storage_submit_text' => 'Send message',
    ];
    $this->drupalGet('admin/structure/contact/add');
    $this->submitForm($params, 'Save');
  }

  /**
   * Set the site email.
   */
  protected function setSiteMail(): void {
    $settings['config']['system.site']['mail'] = (object) [
      'value' => 'site-default-mail@test.com',
      'required' => TRUE,
    ];
    $this->writeSettings($settings);
  }

  /**
   * Helper function to add an email field to the contact form.
   */
  protected function addEmailFieldToContactForm(): void {
    // Add the field.
    $params = [
      'new_storage_type' => 'email',
      'label' => 'Email address',
      'field_name' => 'email_address',
    ];
    $this->drupalGet('admin/structure/contact/manage/contact_emails_test_form/fields/add-field');
    $this->submitForm($params, 'Save and continue');

    // Save the default base field settings.
    $this->submitForm([], 'Save field settings');

    // Save the field settings.
    $this->submitForm([], 'Save settings');

    // Assert that the field exists.
    $this->assertSession()->pageTextContains('field_email_address');
  }

  /**
   * Helper function to create additional contact form to test referencing.
   */
  protected function addContactFormWithEmailFieldForReferencing(): void {
    // Create a contact form.
    $params = [
      'label' => 'Contact Reference Test Form',
      'id' => 'contact_reference_test_form',
      'message' => 'Your message has been sent.',
      'recipients' => 'test@example.com',
      'contact_storage_submit_text' => 'Send message',
    ];
    $this->drupalGet('admin/structure/contact/add');
    $this->submitForm($params, 'Save');

    // Add an email field to be referenced.
    $params = [
      'new_storage_type' => 'email',
      'label' => 'Email reference',
      'field_name' => 'email_reference',
    ];
    $this->drupalGet('admin/structure/contact/manage/contact_reference_test_form/fields/add-field');
    $this->submitForm($params, 'Save and continue');

    // Save the default base field settings.
    $this->submitForm([], 'Save field settings');

    // Save the field settings.
    $this->submitForm([], 'Save settings');

    // Assert that the field exists.
    $this->assertSession()->pageTextContains('field_email_reference');

    // Add an email field to reference the new form's field.
    $params = [
      'new_storage_type' => 'entity_reference',
      'label' => 'Reference',
      'field_name' => 'reference',
    ];
    $this->drupalGet('admin/structure/contact/manage/contact_emails_test_form/fields/add-field');
    $this->submitForm($params, 'Save and continue');

    // Save the default base field settings.
    $params = [
      'settings[target_type]' => 'contact_message',
    ];
    $this->submitForm($params, 'Save field settings');

    // Save the field settings.
    $params = [
      'settings[handler_settings][target_bundles][contact_reference_test_form]' => 'contact_reference_test_form',
    ];
    $this->submitForm($params, 'Save settings');

    // Assert that the field exists.
    $this->assertSession()->pageTextContains('field_reference');

    // Save the display settings to make the reference a simple select.
    $params = [
      'fields[field_reference][type]' => 'options_select',
    ];
    $this->drupalGet('admin/structure/contact/manage/contact_emails_test_form/form-display');
    $this->submitForm($params, 'Save');

    // Submit the refernce contact form on the front-end of the website.
    $params = [
      'subject[0][value]' => 'Submission Test Form Subject',
      'message[0][value]' => 'Submission Test Form Body',
      'field_email_reference[0][value]' => 'email-via-reference@test.com',
    ];
    $this->drupalGet('contact/contact_reference_test_form');
    $this->submitForm($params, 'Send message');

    // Assert that it says message has been sent.
    $this->assertSession()->pageTextContains('Your message has been sent.');
  }

}
