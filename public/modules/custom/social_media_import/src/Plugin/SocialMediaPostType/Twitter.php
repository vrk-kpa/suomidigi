<?php

declare(strict_types = 1);

namespace Drupal\social_media_import\Plugin\SocialMediaPostType;

use Drupal\social_media_import\SocialMedia\SocialMediaPostTypeInterface;
use Drupal\entity\BundleFieldDefinition;
use Drupal\Core\Plugin\PluginBase;

/**
 * Provides a bundle for Twitter tweets.
 *
 * @SocialMediaPostType(
 *   id = "twitter",
 *   label = @Translation("Twitter"),
 *   description = @Translation("Social media post type for Twitter tweets."),
 *   social_media_feed = "social_media_feed_twitter",
 * )
 */
class Twitter extends PluginBase implements SocialMediaPostTypeInterface {

  /**
   * {@inheritdoc}
   */
  public function buildFieldDefinitions() {
    $fields = [];

    $fields['tweet_id'] = BundleFieldDefinition::create('string')
      ->setLabel(t('External ID'))
      ->setDescription(t('Unique identifier of the post in the 3rd party platform.'))
      ->setRequired(TRUE)
      ->setReadOnly(TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['text'] = BundleFieldDefinition::create('text_long')
      ->setLabel(t('Text'))
      ->setRequired(TRUE)
      ->setReadOnly(TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['author_screen_name'] = BundleFieldDefinition::create('string')
      ->setLabel(t('Author screen name'))
      ->setRequired(TRUE)
      ->setReadOnly(TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['full_name'] = BundleFieldDefinition::create('string')
      ->setLabel(t('Full name'))
      ->setRequired(TRUE)
      ->setReadOnly(TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['profile_image'] = BundleFieldDefinition::create('string')
      ->setLabel(t('Profile image'))
      ->setRequired(FALSE)
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
