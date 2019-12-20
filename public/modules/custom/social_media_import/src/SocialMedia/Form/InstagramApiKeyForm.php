<?php

declare(strict_types = 1);

namespace Drupal\social_media_import\SocialMedia\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Component\Utility\UrlHelper;
use MetzWeb\Instagram\Instagram;

/**
 * Form handler for the Instagram API keys.
 */
class InstagramApiKeyForm extends ConfigFormBase {

  /**
   * Instagram Client ID.
   *
   * @var clientId
   */
  private $clientId;

  /**
   * Instagram Client Secret.
   *
   * @var string
   */
  private $clientSecret;

  /**
   * Instagram Client code.
   *
   * @var string
   */
  private $authToken;

  /**
   * Instagram Client code received.
   *
   * @var string
   */
  private $codeAvailable;

  /**
   * Redirect URI for Instagram.
   *
   * @var string
   */
  private $redirectUri;

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'social_media_import.instagram_api_settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'social_media_import.instagram_api_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $config = $this->config('social_media_import.instagram_api_settings');
    $this->redirectUri = Url::fromRoute('social_media_feed.api_settings.instagram.authorize_url')->setAbsolute()->toString();
    $this->clientId = $config->get('client_id');
    $this->clientSecret = $config->get('client_secret');
    $this->authToken = $config->get('auth_token');
    $this->codeAvailable = \Drupal::request()->query->get('code');

    $form['client_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Instagram Client ID'),
      '#default_value' => $config->get('client_id'),
      '#size' => 60,
      '#maxlength' => 100,
      '#required' => TRUE,
    ];

    $form['client_secret'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Instagram Client Secret'),
      '#default_value' => $config->get('client_secret'),
      '#size' => 60,
      '#maxlength' => 100,
      '#required' => TRUE,
    ];

    $form['redirect_uri'] = [
      '#type' => 'hidden',
      '#value' => $this->redirectUri,
    ];

    if (
      empty($form_state->getUserInput()['auth_token']) &&
      empty($this->authToken) &&
      empty($this->codeAvailable)
    ) {
      $query = [
        'client_id' => $this->clientId,
        'redirect_uri' => $this->redirectUri,
        'response_type' => 'code',
      ];
      $authorize_url = 'https://api.instagram.com/oauth/authorize/?' . UrlHelper::buildQuery($query);

      if (!empty($this->clientId)) {
        $form['authorize_key'] = [
          '#markup' => 'Please visit this link after you have added and saved Client ID and Client Secret.<br /><a href="' . $authorize_url . '">' . $authorize_url . '</a>',
        ];
      }
    }
    else {
      if (
        empty($form_state->getUserInput()['auth_token']) &&
        empty($this->authToken)
      ) {
        $instagram = new Instagram([
          'apiKey' => $config->get('client_id'),
          'apiSecret' => $config->get('client_secret'),
          'apiCallback' => $this->redirectUri,
        ]);
        $clientAccessToken = $instagram->getOAuthToken($this->codeAvailable);
        if (isset($clientAccessToken->access_token)) {
          $this->authToken = $clientAccessToken->access_token;
        };
      }

      if (!empty($this->codeAvailable)) {
        $messenger = \Drupal::messenger();
        $messenger->addMessage($this->t('Remember to save the Instagram authentication token.'), $messenger::TYPE_WARNING);
      }
      $form['auth_token'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Instagram Authentication Token'),
        '#default_value' => (empty($config->get('auth_token'))) ? $this->authToken : $config->get('auth_token'),
        '#size' => 60,
        '#maxlength' => 100,
        '#description' => $this->t('Only empty this field if instagram is not operational.'),
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('social_media_import.instagram_api_settings');
    foreach ($form_state->getValues() as $key => $value) {
      $config->set($key, $value);

      if ($key == 'auth_token' && !empty($value)) {
        $messenger = \Drupal::messenger();
        $messenger->addMessage($this->t('Instagram authentication token has been saved.'));
      }
    }
    $config->save();
    parent::submitForm($form, $form_state);
  }

}
