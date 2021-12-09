<?php

declare(strict_types = 1);

namespace Drupal\social_media_import\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Instagram feed config entity.
 *
 * @ConfigEntityType(
 *   id = "social_media_feed_instagram",
 *   label = @Translation("Instagram feed"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\social_media_import\SocialMedia\SocialMediaFeedListBuilder",
 *     "form" ={
 *       "add" = "Drupal\social_media_import\SocialMedia\Form\InstagramFeedForm",
 *       "edit" = "Drupal\social_media_import\SocialMedia\Form\InstagramFeedForm",
 *     }
 *   },
 *   config_prefix = "instagram_feed",
 *   admin_permission = "administer social media settings",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "user_id",
 *   }
 *   links = {
 *     "edit-form" = "/admin/structure/social_media_post/instagram/{social_media_feed_instagram}",
 *     "create" = "/admin/structure/social_media_post/instagram/add",
 *   },
 * )
 *
 * @todo allow deleting Instagram feeds.
 */
class InstagramFeed extends ConfigEntityBase {

  /**
   * The unique identifier of the feed.
   *
   * @var string
   */
  protected $id;

  /**
   * The human readable name of the feed.
   *
   * @var string
   */
  protected $label;

  /**
   * The user ID of the account which posts are being imported.
   *
   * @var string
   */
  protected $user_id;

  /**
   * Gets the user ID.
   *
   * @return string
   *   The user id to return.
   */
  public function getUserId() :string {
    return $this->user_id;
  }

}
