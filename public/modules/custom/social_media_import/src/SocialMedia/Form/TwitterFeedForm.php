<?php

declare(strict_types = 1);

namespace Drupal\social_media_import\SocialMedia\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form handler for the Twitter feed add and edit forms.
 */
class TwitterFeedForm extends EntityForm {

  /**
   * Constructs an TwitterForm object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManager $entityTypeManager
   *   The entityTypeManager.
   */
  public function __construct(EntityTypeManager $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $config = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $config->label(),
      '#description' => $this->t("Label for the Twitter feed."),
      '#required' => TRUE,
    ];
    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $config->id(),
      '#machine_name' => [
        'exists' => [$this, 'exist'],
      ],
      '#disabled' => !$config->isNew(),
    ];
    $form['screen_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Screen name'),
      '#description' => $this->t("The screen name of the user which timeline will be imported. The user must be public."),
      '#default_value' => $config->get('screen_name'),
      '#size' => 60,
      '#maxlength' => 100,
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $twitter = $this->entity;
    $status = $twitter->save();

    if ($status) {
      drupal_set_message($this->t('Saved the %label.', [
        '%label' => $twitter->label(),
      ]));
    }
    else {
      drupal_set_message($this->t('The %label was not saved.', [
        '%label' => $twitter->label(),
      ]));
    }

    $form_state->setRedirect('entity.social_media_post.settings.twitter');
  }

  /**
   * Helper function to check whether an Twitter configuration entity exists.
   */
  public function exist($id) {
    $entity = $this->entityTypeManager->getStorage('social_media_feed_twitter')->getQuery()
      ->condition('id', $id)
      ->execute();
    return (bool) $entity;
  }

}
