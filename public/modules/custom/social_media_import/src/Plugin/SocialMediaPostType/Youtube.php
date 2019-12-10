<?php

declare(strict_types = 1);

namespace Drupal\social_media_import\Plugin\SocialMediaPostType;

use Drupal\social_media_import\SocialMedia\SocialMediaPostTypeInterface;
use Drupal\entity\BundleFieldDefinition;
use Drupal\Core\Plugin\PluginBase;

/**
 * Provides a bundle for Youtube tweets.
 *
 * @SocialMediaPostType(
 *   id = "youtube",
 *   label = @Translation("Youtube"),
 *   description = @Translation("Social media post type for Youtube tweets."),
 *   social_media_feed = "social_media_feed_youtube",
 * )
 */
class Youtube extends PluginBase implements SocialMediaPostTypeInterface {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {
    $fields = [];

    $fields['video_id'] = BundleFieldDefinition::create('string')
      ->setLabel(t('Video ID'))
      ->setDescription(t('Unique identifier of the post in the 3rd party platform.'))
      ->setRequired(TRUE)
      ->setReadOnly(TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['text'] = BundleFieldDefinition::create('text_long')
      ->setLabel(t('Text'))
      ->setRequired(TRUE)
      ->setReadOnly(TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['channel_name'] = BundleFieldDefinition::create('string')
      ->setLabel(t('Channel name'))
      ->setRequired(TRUE)
      ->setReadOnly(TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['title'] = BundleFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setRequired(TRUE)
      ->setReadOnly(TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['full_name'] = BundleFieldDefinition::create('string')
      ->setLabel(t('Full name'))
      ->setRequired(TRUE)
      ->setReadOnly(TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['link'] = BundleFieldDefinition::create('link')
      ->setLabel(t('Link'))
      ->setRequired(TRUE)
      ->setReadOnly(TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['image'] = BundleFieldDefinition::create('image')
      ->setLabel(t('Image'))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'type' => 'image',
        'weight' => 5,
        'label' => 'hidden',
        'settings' => [
          'image_style' => 'full_width_wide',
        ],
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setReadOnly(TRUE);

    return $fields;
  }

}
