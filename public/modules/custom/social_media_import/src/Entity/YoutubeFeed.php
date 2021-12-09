<?php

declare(strict_types = 1);

namespace Drupal\social_media_import\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Youtube feed config entity.
 *
 * @ConfigEntityType(
 *   id = "social_media_feed_youtube",
 *   label = @Translation("Youtube feed"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\social_media_import\SocialMedia\SocialMediaFeedListBuilder",
 *     "form" ={
 *       "add" = "Drupal\social_media_import\SocialMedia\Form\YoutubeFeedForm",
 *       "edit" = "Drupal\social_media_import\SocialMedia\Form\YoutubeFeedForm",
 *     }
 *   },
 *   config_prefix = "youtube_feed",
 *   admin_permission = "administer social media settings",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *   },
  *   config_export = {
 *     "id",
 *     "label",
 *     "channel_name",
 *   }
 *   links = {
 *     "edit-form" = "/admin/structure/social_media_post/youtube/{social_media_feed_youtube}",
 *     "create" = "/admin/structure/social_media_post/youtube/add",
 *   }
 * )
 *
 * @todo allow deleting Youtube feeds.
 */
class YoutubeFeed extends ConfigEntityBase {

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
   * The screen name of the account which tweets are being imported.
   *
   * @var string
   */
  protected $channel_name;

  /**
   * Gets the screen name.
   *
   * @return string
   *   The screen name to return.
   */
  public function getChannelName() :string {
    return $this->channel_name;
  }

  /**
   * Gets the ID of the feed.
   *
   * @return string
   *   The screen name to return.
   */
  public function getId() :string {
    return $this->id;
  }

}
