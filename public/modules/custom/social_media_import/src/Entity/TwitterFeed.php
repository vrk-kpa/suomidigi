<?php

declare(strict_types = 1);

namespace Drupal\social_media_import\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Twitter feed config entity.
 *
 * @ConfigEntityType(
 *   id = "social_media_feed_twitter",
 *   label = @Translation("Twitter feed"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\social_media_import\SocialMedia\SocialMediaFeedListBuilder",
 *     "form" ={
 *       "add" = "Drupal\social_media_import\SocialMedia\Form\TwitterFeedForm",
 *       "edit" = "Drupal\social_media_import\SocialMedia\Form\TwitterFeedForm",
 *     }
 *   },
 *   config_prefix = "twitter_feed",
 *   admin_permission = "administer social media settings",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *   },
*   config_export = {
 *     "id",
 *     "label",
 *     "screen_name",
 *   },
 *   links = {
 *     "edit-form" = "/admin/structure/social_media_post/twitter/{social_media_feed_twitter}",
 *     "create" = "/admin/structure/social_media_post/twitter/add",
 *   }
 * )
 *
 * @todo allow deleting Twitter feeds.
 */
class TwitterFeed extends ConfigEntityBase {

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
  protected $screen_name;

  /**
   * Gets the screen name.
   *
   * @return string
   *   The screen name to return.
   */
  public function getScreenName() :string {
    return $this->screen_name;
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
