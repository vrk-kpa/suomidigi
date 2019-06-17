<?php

namespace Drupal\suopa_media_embed\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\suopa_media_embed\Plugin\media\Source\MediaEmbed;
use Drupal\image\Entity\ImageStyle;

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
      $style = ImageStyle::load('thumbnail');
      $uri = $style->buildUri('public://media-icons/generic/mediaembed.png');

      foreach ($items as $delta => $item) {
        $element[$delta] = [
          '#theme' => 'image',
          '#uri' => $uri,
          '#title' => $media->getName(),
        ];
      }
    }
    return $element;
  }

}
