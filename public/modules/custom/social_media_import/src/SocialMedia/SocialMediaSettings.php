<?php

declare(strict_types = 1);

namespace Drupal\social_media_import\SocialMedia;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Settings for social media post types.
 */
class SocialMediaSettings extends ControllerBase {

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
   * Build a table of all social media post types.
   *
   * @return array
   *   A renderable array.
   */
  public function buildSocialMediaTypesTable() {
    $socialMediaPostTypes = $this->socialMediaPostTypePluginManager->getDefinitions();
    $build['table'] = [
      '#type' => 'table',
      '#title' => $this->t('The social media post types'),
      '#header' => [
        $this->t('Name'),
        $this->t('Operations'),
      ],
      '#rows' => array_map(function ($socialMediaPostType) {
        $operations = [
          '#type' => 'operations',
          '#links' => [
            'edit' => [
              'title' => $this->t('Edit'),
              'url' => Url::fromRoute("entity.social_media_post.settings.{$socialMediaPostType['id']}"),
            ],
          ],
        ];
        return [
          $socialMediaPostType['label'],
          [
            'data' => $operations,
          ],
        ];
      }, $socialMediaPostTypes),
      '#cache' => [
        'contexts' => $this->socialMediaPostTypePluginManager->getCacheContexts(),
        'tags' => $this->socialMediaPostTypePluginManager->getCacheTags(),
      ],
    ];

    $build['link_import_social_media'] = [
      '#title' => $this->t('Clear all social media post entities and rerun the importer.'),
      '#type' => 'link',
      '#url' => Url::fromRoute('entity.social_media_post.import'),
      '#attributes' => [
        'class' => ['button'],
      ],
    ];

    return $build;
  }

}
