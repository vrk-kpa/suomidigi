<?php

declare(strict_types = 1);

namespace Drupal\social_media_import\SocialMedia;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Link;

/**
 * Provides a listing of social media feeds.
 */
class SocialMediaFeedListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Feed name');
    $header['id'] = $this->t('Machine name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $entity_type_id = explode('social_media_feed_', $entity->getEntityTypeId());
    $entity_type_id = array_pop($entity_type_id);
    $row['label'] = Link::createFromRoute(
      $entity->label(),
      "entity.{$entity->getEntityTypeId()}.{$entity->id()}.collection",
      ['feed_id' => $entity->id(), 'service' => $entity_type_id]
    );
    $row['id'] = $entity->id();

    return $row + parent::buildRow($entity);
  }

}
