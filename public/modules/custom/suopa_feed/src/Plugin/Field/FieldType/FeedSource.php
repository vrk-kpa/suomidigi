<?php

namespace Drupal\suopa_feed\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'feed_source' field type.
 *
 * @FieldType(
 *   id = "feed_source",
 *   label = @Translation("Feed source"),
 *   description = @Translation("Feed source used in a feed configuration."),
 *   default_widget = "feed_source_widget",
 *   default_formatter = "feed_source_field_formatter"
 * )
 */
class FeedSource extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = [];

    $properties['name'] = DataDefinition::create('string')
      ->setLabel(t('Name'))
      ->setRequired(TRUE);

    $properties['url'] = DataDefinition::create('string')
      ->setLabel(t('URL template'))
      ->setDescription(t('Use %s in the template to place the keyword(s).'))
      ->setRequired(TRUE);

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'name' => [
          'description' => 'Name',
          'type' => 'varchar',
          'length' => 255,
        ],
        'url' => [
          'description' => 'Url',
          'type' => 'varchar',
          'length' => 2048,
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('name')->getValue();
    return $value === NULL || $value === '';
  }

}
