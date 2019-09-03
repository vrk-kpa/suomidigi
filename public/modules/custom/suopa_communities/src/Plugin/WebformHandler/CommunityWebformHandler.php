<?php

namespace Drupal\suopa_communities\Plugin\WebformHandler;

use Drupal\Core\Form\FormStateInterface;
use Drupal\suopa_communities\Entity\Community;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\webformSubmissionInterface;

/**
 * Create a new Community entity from a webform submission.
 *
 * @WebformHandler(
 *   id = "community_from_webform",
 *   label = @Translation("Create a community entity on submit"),
 *   category = @Translation("Form Handler"),
 *   description = @Translation("Creates a new Community entity from Webform Submissions."),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_SINGLE,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_REQUIRED,
 * )
 */
class CommunityWebformHandler extends WebformHandlerBase {

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {
    // Get an array of form field values.
    $submissionArray = $webform_submission->getData();

    // Create the entity.
    $community = Community::create([
      'status' => FALSE,
      'name' => $submissionArray['name'],

      'field_community_description' => [
        'value' => $submissionArray['description'],
        'format' => 'basic_html',
      ],

      'field_community_link' => [
        'uri' => $submissionArray['url'],
      ],

      'field_community_domain' => [
        [
          'target_id' => $submissionArray['domain'],
        ],
      ],
    ]);

    $community->save();
  }

}
