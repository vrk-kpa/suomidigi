<?php

declare(strict_types = 1);

namespace Drupal\social_media_import\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Provides an entity type for the social media posts.
 *
 * @ContentEntityType(
 *   id = "social_media_post",
 *   label = @Translation("Social media post"),
 *   base_table = "social_media_post",
 *   bundle_plugin_type = "social_media_post_type",
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "views_data" = "Drupal\social_media_import\Entity\SocialMediaPostViewsData",
 *     "list_builder" = "Drupal\social_media_import\Entity\SocialMediaPostListBuilder",
 *     "access" = "Drupal\social_media_import\Entity\SocialMediaPostAccessControlHandler",
 *     "form" = {
 *       "delete" = "Drupal\social_media_import\SocialMedia\Form\SocialMediaPostDeleteForm",
 *     },
 *   },
 *   entity_keys = {
 *    "id" = "id",
 *    "bundle" = "type",
 *   },
 *   links = {
 *     "canonical" = "/social_media_post/{social_media_post}",
 *     "delete-form" = "/social_media_post/{social_media_post}/delete",
 *     "collection" = "/admin/structure/social_media",
 *   },
 * )
 */
class SocialMediaPost extends ContentEntityBase {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entityType) {
    $fields = parent::baseFieldDefinitions($entityType);

    $fields['created'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('Created on'))
      ->setDescription(t('The time that the post was created.'))
      ->setRequired(TRUE)
      ->setReadOnly(TRUE)
      ->setDisplayOptions('view', [
        'type' => 'datetime_timestamp',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['feed_id'] = BaseFieldDefinition::create('string')
      ->setLabel(t('The ID of the feed where the post has been imported from.'))
      ->setRequired(TRUE)
      ->setReadOnly(TRUE);

    return $fields;
  }

  /**
   * Gets the post feed id.
   *
   * @return string
   *   The identifier of the feed the post has been imported from.
   */
  public function getFeedId() :string {
    return $this->get('feed_id')->value;
  }

  /**
   * Sets the feed id for the post.
   *
   * @param string $feedId
   *   The identifier of the feed the post has been imported from.
   *
   * @return \Drupal\social_media_import\Entity\SocialMediaPost
   *   The called entity.
   */
  public function setFeedId(string $feedId) :SocialMediaPost {
    $this->set('feed_id', $feedId);
    return $this;
  }

  /**
   * Gets the post creation timestamp.
   *
   * @return int
   *   Creation timestamp of the post.
   */
  public function getCreatedTime() :int {
    return (int) $this->get('created')->value;
  }

  /**
   * Sets the post creation timestamp.
   *
   * @param int $timestamp
   *   The post creation timestamp.
   *
   * @return \Drupal\social_media_import\Entity\SocialMediaPost
   *   The called entity.
   */
  public function setCreatedTime(int $timestamp) :SocialMediaPost {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * Gets the type of social media post.
   *
   * @return string
   *   Type of the post.
   */
  public function getType() {
    return $this->get('type')->value;
  }

  /**
   * Gets the post text.
   *
   * @return string|null
   *   The text for the post.
   */
  public function getText() {
    return $this->get('text')->value;
  }

}
