<?php

declare(strict_types = 1);

namespace Drupal\social_media_import\SocialMedia;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Manages discovery and instantiation of SocialMediaPostType plugins.
 */
class SocialMediaPostTypePluginManager extends DefaultPluginManager {

  /**
   * Constructs a new object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   The cache backend.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/SocialMediaPostType', $namespaces, $module_handler, 'Drupal\social_media_import\SocialMedia\SocialMediaPostTypeInterface', 'Drupal\social_media_import\Annotation\SocialMediaPostType');

    $this->alterInfo('social_media_post_type_info');
    $this->setCacheBackend($cache_backend, 'social_media_post_type_plugins');
  }

}
