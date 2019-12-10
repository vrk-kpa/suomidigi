<?php

declare(strict_types = 1);

namespace Drupal\social_media_import\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Facebook feed config entity.
 *
 * @ConfigEntityType(
 *   id = "social_media_feed_facebook",
 *   label = @Translation("Facebook feed"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\social_media_import\SocialMedia\SocialMediaFeedListBuilder",
 *     "form" ={
 *       "add" = "Drupal\social_media_import\SocialMedia\Form\FacebookFeedForm",
 *       "edit" = "Drupal\social_media_import\SocialMedia\Form\FacebookFeedForm",
 *     }
 *   },
 *   config_prefix = "facebook_feed",
 *   admin_permission = "administer social media settings",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *   },
 *   links = {
 *     "edit-form" = "/admin/structure/social_media_post/facebook/{social_media_feed_facebook}",
 *     "create" = "/admin/structure/social_media_post/facebook/add",
 *   },
 * )
 *
 * @todo allow deleting Facebook feeds.
 */
class FacebookFeed extends ConfigEntityBase {

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
   * The page name of the account which posts are being imported.
   *
   * @var string
   */
  protected $page_name;

  /**
   * Gets the page name.
   *
   * @return string
   *   The page name to return.
   */
  public function getPageName() :string {
    return $this->page_name;
  }

}
