<?php

declare(strict_types = 1);

namespace Drupal\social_media_import\SocialMedia\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form handler for the Twitter API keys.
 */
class TwitterApiKeyForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'social_media_import.twitter_api_settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'social_media_import.twitter_api_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $config = $this->config('social_media_import.twitter_api_settings');

    $form['consumer_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Twitter Consumer Key'),
      '#default_value' => $config->get('consumer_key'),
      '#size' => 60,
      '#maxlength' => 100,
      '#required' => TRUE,
    ];
    $form['consumer_secret'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Twitter Consumer Secret'),
      '#default_value' => $config->get('consumer_secret'),
      '#size' => 60,
      '#maxlength' => 100,
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('social_media_import.twitter_api_settings');
    foreach ($form_state->getValues() as $key => $value) {
      $config->set($key, $value);
    }
    $config->save();
    parent::submitForm($form, $form_state);
  }

}
