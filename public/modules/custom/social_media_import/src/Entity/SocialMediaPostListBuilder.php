<?php

namespace Drupal\social_media_import\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of SocialMediaPost entities.
 *
 * @ingroup social_media_import
 */
class SocialMediaPostListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   *
   * We override ::render() so that we can add our own content above the table.
   * parent::render() is where EntityListBuilder creates the table using our
   * buildHeader() and buildRow() implementations.
   */
  public function render() {
    $build['description'] = [
      '#markup' => '<p>' . $this->t('Social media posts are imported from predefined sources. Posts cannot be added manually, but can be deleted manually.') . '</p>',
    ];
    $build['table'] = parent::render();
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Entity ID');
    $header['type'] = $this->t('Type');
    $header['text'] = $this->t('Text');
    $header['original_link'] = $this->t('Original link');
    $header['created'] = $this->t('Created');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\social_media_import\Entity\SocialMediaPost */
    $feed_id = \Drupal::request()->query->get('feed_id');
    $service = \Drupal::request()->query->get('service');

    if (
      $entity->getType() == $service &&
      $entity->getFeedId() == $feed_id
    ) {
      $row['id'] = Link::createFromRoute(
        'Post ID: ' . $entity->id(),
        $entity->toUrl()->getRouteName(),
        ['social_media_post' => $entity->id()]
      );
      $row['type'] = $entity->getType();
      $row['text'] = $entity->getText();
      $original_link = Url::fromUri($entity->get('link')->uri);
      $row['original_link'] = Link::fromTextAndUrl($this->t('Original post'), $original_link);
      $row['created'] = $entity->getCreatedTime();

      return $row + parent::buildRow($entity);
    }

  }

}
