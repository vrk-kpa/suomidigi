<?php

declare(strict_types = 1);

namespace Drupal\social_media_import\SocialMedia\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form handler for the Youtube feed add and edit forms.
 */
class YoutubeFeedForm extends EntityForm {

  /**
   * Constructs an YoutubeForm object.
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
      '#description' => $this->t("Label for the Youtube feed."),
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
    $form['channel_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Channel name'),
      '#description' => $this->t("The channel name which timeline will be imported."),
      '#default_value' => $config->get('channel_name'),
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
    $youtube = $this->entity;
    $status = $youtube->save();

    if ($status) {
      $this->messenger()->addStatus($this->t('Saved the %label.', [
        '%label' => $youtube->label(),
      ]));
    }
    else {
      $this->messenger()->addStatus($this->t('The %label was not saved.', [
        '%label' => $youtube->label(),
      ]));
    }

    $form_state->setRedirect('entity.social_media_post.settings.youtube');
  }

  /**
   * Helper function to check whether an Youtube configuration entity exists.
   */
  public function exist($id) {
    $entity = $this->entityTypeManager->getStorage('social_media_feed_youtube')->getQuery()
      ->condition('id', $id)
      ->execute();
    return (bool) $entity;
  }

}
