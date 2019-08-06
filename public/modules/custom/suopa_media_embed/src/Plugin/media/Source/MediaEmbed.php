<?php

namespace Drupal\suopa_media_embed\Plugin\media\Source;

use Drupal\media\MediaInterface;
use Drupal\media\MediaSourceBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Media embed entity media source.
 *
 * @MediaSource(
 *   id = "mediaembed",
 *   label = @Translation("Media embed"),
 *   allowed_field_types = {"string_long"},
 *   default_thumbnail_filename = "mediaembed.png",
 *   description = @Translation("Provides business logic and metadata for Media embed.")
 * )
 */
class MediaEmbed extends MediaSourceBase {

  /**
   * Config factory interface.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('entity_field.manager'),
      $container->get('plugin.manager.field.field_type'),
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getMetadataAttributes() {
    $attributes = [
      'name' => $this->t('Name'),
      'thumbnail_uri' => t('URI of the thumbnail'),
    ];
    return $attributes;
  }

  /**
   * {@inheritdoc}
   */
  public function getMetadata(MediaInterface $media, $attribute_name) {
    switch ($attribute_name) {
      case 'thumbnail_uri':
        return 'public://media-icons/generic/mediaembed.png';

      default:
        return parent::getMetadata($media, $attribute_name);
    }
  }

}
