<?php

declare(strict_types = 1);

namespace Drupal\social_media_import\SocialMedia\Routing;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\social_media_import\SocialMedia\SocialMediaPostTypePluginManager;
use Symfony\Component\Routing\Route;

/**
 * Manages the routes for social media entities.
 */
class SocialMediaRoutes {

  /**
   * The social media post type plugin manager.
   *
   * @var \Drupal\social_media_import\SocialMedia\SocialMediaPostTypePluginManager
   */
  protected $socialMediaPostTypePluginManager;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new object.
   *
   * @param \Drupal\social_media_import\SocialMedia\SocialMediaPostTypePluginManager $socialMediaPostTypePluginManager
   *   The social media post type plugin manager.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(SocialMediaPostTypePluginManager $socialMediaPostTypePluginManager, EntityTypeManagerInterface $entityTypeManager) {
    $this->socialMediaPostTypePluginManager = $socialMediaPostTypePluginManager;
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * Gets the social media routes.
   *
   * @return array
   *   The routes.
   */
  public function getRoutes() :array {
    $routes = [];
    foreach ($this->socialMediaPostTypePluginManager->getDefinitions() as $plugin_id => $definition) {
      $base_path = '/admin/structure/social_media_post/';
      $path = $base_path . $plugin_id;
      $entity_type_id = 'social_media_post';
      $entity_id = 'social_media_feed_' . $plugin_id;
      $social_media_type_feed_types = \Drupal::service('entity_type.manager')->getStorage("social_media_feed_{$plugin_id}")->loadMultiple();

      // Entity list route.
      $routes['entity.social_media_post.settings.' . $plugin_id] = new Route(
        $path,
        [
          '_entity_list' => $definition['social_media_feed'],
          '_title' => (string) $definition['label'],
        ],
        [
          '_permission' => 'administer social media settings',
        ]
      );

      // Add social media feed form.
      $routes["entity.{$entity_id}.add_form"] = new Route(
        $path . '/add',
        [
          '_entity_form' => $definition['social_media_feed'] . '.add',
          '_title' => 'Add ' . $definition['label'],
        ],
        [
          '_permission' => 'administer social media settings',
        ]
      );

      // Edit social media feed form.
      $routes["entity.{$entity_id}.edit_form"] = new Route(
        $path . '/{' . $entity_id . '}/edit',
        [
          '_entity_form' => $definition['social_media_feed'] . '.edit',
          '_title' => 'Edit ' . $definition['label'],
        ],
        [
          '_permission' => 'administer social media settings',
        ]
      );

      // View social media posts.
      $routes["entity.{$entity_id}.collection"] = new Route(
        $path . '/{' . $entity_id . '}/list',
        [
          '_controller' => '\Drupal\social_media_import\SocialMedia\SocialMediaSettings::buildSocialMediaTypesTable',
          '_title' => 'View ' . $definition['label'],
        ],
        [
          '_permission' => 'administer social media settings',
        ]
      );

      $defaults = [
        'entity_type_id' => $entity_type_id,
        'bundle' => $plugin_id,
      ];

      // Default display mode.
      $routes["entity.entity_view_display.{$entity_type_id}.{$plugin_id}.default"] = new Route(
        "$path/display",
        [
          '_entity_form' => 'entity_view_display.edit',
          '_title' => 'Manage display',
          'view_mode_name' => 'default',
        ] + $defaults,
        ['_field_ui_view_mode_access' => 'administer ' . $entity_type_id . ' display']
      );

      // Custom display modes. @todo Needs work!
      $routes["entity.entity_view_display.{$entity_type_id}.{$plugin_id}.view_mode"] = new Route(
        "$path/display/{view_mode_name}",
        [
          '_entity_form' => 'entity_view_display.edit',
          '_title' => 'Manage display',
        ] + $defaults,
        ['_field_ui_view_mode_access' => 'administer ' . $entity_type_id . ' display']
      );

      // List social media post entities.
      foreach ($social_media_type_feed_types as $feed_type => $feed) {
        $routes["entity.{$entity_id}.{$feed_type}.collection"] = new Route(
          "$path/{$feed_type}/list",
          [
            '_entity_list' => $entity_type_id,
            '_title' => 'List of ' . $feed_type . ' entities',
          ] + $defaults,
          [
            '_permission' => 'administer social media settings',
          ]
        );
      }
    }

    return $routes;
  }

}
