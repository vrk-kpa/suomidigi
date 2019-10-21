<?php

namespace Drupal\suopa_events\Plugin\WebformHandler;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\webformSubmissionInterface;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;

/**
 * Create a new Event entity from a webform submission.
 *
 * @WebformHandler(
 *   id = "event_from_webform",
 *   label = @Translation("Create a event entity on submit"),
 *   category = @Translation("Form Handler"),
 *   description = @Translation("Creates a new event entity from Webform Submissions."),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_SINGLE,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_REQUIRED,
 * )
 */
class EventWebformHandler extends WebformHandlerBase {

  /**
   * The Webform submission array.
   *
   * @var array
   */
  protected $submissionArray = [];

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {
    // Get an array of form field values.
    if (in_array('::submitForm', $form_state->getSubmitHandlers())) {
      $this->submissionArray = $webform_submission->getData();

      // Create the entity.
      $event = Node::create([
        'status' => FALSE,
        'title' => $this->validateField('name'),
        'type' => 'event',
      ]);

      // Webform formats date and time with a DATETIME_STORAGE_FORMAT but with
      // timezone added. Because of a core bug all datetimes are added to
      // database without timezone.
      // See https://www.drupal.org/project/drupal/issues/2716891.
      // Reformat the dates to a more suitable format for the database.
      if ($this->validateField('start_date_and_time')) {
        $start_date = new DrupalDateTime($this->validateField('start_date_and_time'));
        $start = $start_date->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);

        if ($this->validateField('end_date_and_time')) {
          $end_date = new DrupalDateTime($this->validateField('end_date_and_time'));
          $end = $end_date->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);
        }

        $event->set('field_event_date_and_time', [
          'value' => $start,
          'end_value' => (isset($end)) ? $end : NULL,
        ]);
      }

      $event->set('field_event_description', [
        'value' => nl2br($this->validateField('description')),
        'format' => 'basic_html',
      ]);

      $event->set('field_event_image', [
        'target_id' => $this->validateField('image'),
        'alt' => $this->validateField('name'),
      ]);

      $event->set('field_event_schedule', [
        'value' => nl2br($this->validateField('schedule')),
        'format' => 'basic_html',
      ]);

      $event->set('field_event_link', [
        'uri' => $this->validateField('link_to_official_event_page'),
      ]);

      $event->set('field_event_registration', [
        'uri' => $this->validateField('link_to_event_registration_page'),
      ]);

      $event->set('field_venue_name', [
        'value' => $this->validateField('venue_name'),
      ]);

      $event->set('field_venue_description', [
        'value' => nl2br($this->validateField('venue_description')),
        'format' => 'basic_html',
      ]);

      $event->set('field_venue_address', [
        'address_line1' => $this->validateField('venue_address')['address'],
        'postal_code' => $this->validateField('venue_address')['postal_code'],
        'locality' => $this->validateField('venue_address')['city'],
        'country_code' => $this->validateField('venue_address')['country'],
      ]);

      $event->set('field_venue_travel_information', [
        'value' => nl2br($this->validateField('travel_information')),
        'format' => 'basic_html',
      ]);

      $event->set('field_event_organiser', [
        'value' => $this->validateField('organiser'),
      ]);

      if ($this->validateField('contact_information')) {
        foreach ($this->validateField('contact_information') as $delta => $value) {
          $event->field_event_org_contact_info[$delta] = $value;
        }
      }

      $event->set('field_event_organiser', [
        'value' => $this->validateField('organiser'),
      ]);

      $event->save();
    }
  }

  /**
   * Check if submission array has a value for the selected field.
   *
   * @param string $key
   *   The field name.
   *
   * @return bool|mixed
   *   Returns submission value or NULL.
   */
  private function validateField($key) {
    return (
      array_key_exists($key, $this->submissionArray) &&
      !empty($this->submissionArray[$key])
    ) ? $this->submissionArray[$key] : NULL;
  }

}
