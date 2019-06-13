<?php

namespace Drupal\suopa_media_embed\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\suopa_media_embed\Plugin\media\Source\MediaEmbed;

/**
 * Plugin implementation of the 'Media embed' formatter.
 *
 * @FieldFormatter(
 *   id = "media_embed",
 *   label = @Translation("Media embed"),
 *   field_types = {
 *     "string_long"
 *   }
 * )
 */
class MediaEmbedFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    /** @var \Drupal\media\MediaInterface $media */
    $media = $items->getEntity();

    $element = [];
    if (($source = $media->getSource()) && $source instanceof MediaEmbed) {
      /** @var \Drupal\media\MediaTypeInterface $item */
      foreach ($items as $delta => $item) {
        $element[$delta] = [
          '#theme' => 'media_embed',
          '#thumbnail_uri' => drupal_get_path('module', 'suopa_media_embed') . '/images/icons/mediaembed.png'
        ];
      }
    }
    return $element;
  }

}
