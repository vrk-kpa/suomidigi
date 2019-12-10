<?php

namespace Drupal\social_media_import\SocialMedia;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Controller for importing Social Media posts.
 *
 * @ingroup social_media_import
 */
class SocialMediaImporter extends ControllerBase {

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
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.social_media_post_type'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * Get post types.
   */
  private function getPostTypes() {
    return [
      'TwitterFeed' => 'twitter',
      'YoutubeFeed' => 'youtube',
      'InstagramFeed' => 'instagram',
    ];
  }

  /**
   * Cleanup.
   */
  public function doCleanup() {
    $query = \Drupal::entityQuery('social_media_post');
    $posts = $query->execute();

    if (!empty($posts)) {
      $storage_handler = \Drupal::entityTypeManager()->getStorage('social_media_post');
      $entities = $storage_handler->loadMultiple($posts);
      $storage_handler->delete($entities);

      $image_directories = [
        'public://facebook/',
        'public://instagram/',
        'public://youtube/',
        'public://twitter/',
      ];

      foreach ($image_directories as $dir) {
        file_unmanaged_delete_recursive($dir);
      }
    }
  }

  /**
   * Clear social media post entities and import new ones.
   *
   * @return object
   *   Return redirect to social media posts -page.
   */
  public function importSocialMediaPosts() {
    $this->doCleanup();

    foreach ($this->getPostTypes() as $class => $type) {
      $service = \Drupal::service("social_media_import.social_media.{$type}_importer");
      $type_feeds = \Drupal::entityTypeManager()
        ->getStorage("social_media_feed_{$type}")
        ->getQuery()
        ->execute();

      $class = "\\Drupal\\social_media_import\\Entity\\" . $class;

      if (!empty($type_feeds)) {
        foreach ($type_feeds as $feed_name) {
          $config = \Drupal::config("social_media_import.{$type}_feed." . $feed_name)
            ->getRawData();
          $service->setClient();
          $service->refresh(new $class($config, "social_media_feed_{$type}"));
        }
      }
    }

    drupal_set_message($this->t('The importer has been run and social media posts has been updated.'));

    return new RedirectResponse(\Drupal::url('entity.social_media_post'));

  }

}
