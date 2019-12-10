<?php

declare(strict_types = 1);

namespace Drupal\social_media_import\SocialMedia\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form handler for the Facebook API keys.
 */
class FacebookApiKeyForm extends ConfigFormBase {

  /**
   * Facebook App ID.
   *
   * @var string
   */
  private $appId;

  /**
   * Facebook App Secret.
   *
   * @var string
   */
  private $appSecret;

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'social_media_import.facebook_api_settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'social_media_import.facebook_api_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $config = $this->config('social_media_import.facebook_api_settings');
    $this->appId = $config->get('app_id');
    $this->appSecret = $config->get('app_secret');

    $form['app_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Facebook APP ID'),
      '#default_value' => $config->get('app_id'),
      '#size' => 60,
      '#maxlength' => 100,
      '#required' => TRUE,
    ];
    $form['app_secret'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Facebook Secret Key'),
      '#default_value' => $config->get('app_secret'),
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
    $config = $this->config('social_media_import.facebook_api_settings');
    foreach ($form_state->getValues() as $key => $value) {
      $config->set($key, $value);
    }
    $config->save();
    parent::submitForm($form, $form_state);
  }

}
