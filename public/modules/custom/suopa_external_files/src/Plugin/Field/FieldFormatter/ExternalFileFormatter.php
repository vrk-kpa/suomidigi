<?php

namespace Drupal\suopa_external_files\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\suopa_external_files\Plugin\media\Source\ExternalFile;
use Drupal\image\Entity\ImageStyle;

/**
 * Plugin implementation of the 'External files' formatter.
 *
 * @FieldFormatter(
 *   id = "external_file",
 *   label = @Translation("External file"),
 *   field_types = {
 *     "string"
 *   }
 * )
 */
class ExternalFileFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    /** @var \Drupal\media\MediaInterface $media */
    $media = $items->getEntity();

    $element = [];
    if (($source = $media->getSource()) && $source instanceof ExternalFile) {
      /** @var \Drupal\media\MediaTypeInterface $item */
      $style = ImageStyle::load('thumbnail');
      $uri = $style->buildUri('public://media-icons/generic/externalfile.png');

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
