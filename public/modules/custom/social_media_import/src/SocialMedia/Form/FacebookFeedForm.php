<?php

declare(strict_types = 1);

namespace Drupal\social_media_import\SocialMedia\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form handler for the Facebook feed add and edit forms.
 */
class FacebookFeedForm extends EntityForm {

  /**
   * Constructs an ExampleForm object.
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
      '#description' => $this->t("Label for the Facebook feed."),
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
    $form['page_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Page ID'),
      '#description' => $this->t('The page id of the page which timeline will be imported. If you need to convert name to id, use: <a href="https://lookup-id.com/">https://lookup-id.com/</a>'),
      '#default_value' => $config->get('page_name'),
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
    $example = $this->entity;
    $status = $example->save();

    if ($status) {
      drupal_set_message($this->t('Saved the %label.', [
        '%label' => $example->label(),
      ]));
    }
    else {
      drupal_set_message($this->t('The %label was not saved.', [
        '%label' => $example->label(),
      ]));
    }

    $form_state->setRedirect('entity.social_media_post.settings.facebook');
  }

  /**
   * Helper function to check whether an Example configuration entity exists.
   */
  public function exist($id) {
    $entity = $this->entityTypeManager->getStorage('social_media_feed_facebook')->getQuery()
      ->condition('id', $id)
      ->execute();
    return (bool) $entity;
  }

}
