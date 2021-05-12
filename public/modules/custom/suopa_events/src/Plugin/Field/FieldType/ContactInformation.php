<?php

namespace Drupal\suopa_events\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Render\Element\Email;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'contact_information' field type.
 *
 * @FieldType(
 *   id = "contact_information",
 *   label = @Translation("Contact information"),
 *   description = @Translation("Contact information generated from name, email and phone number."),
 *   default_widget = "contact_information_widget",
 *   default_formatter = "contact_information_field_formatter"
 * )
 */
class ContactInformation extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = [];
    $properties['name'] = DataDefinition::create('string')
      ->setLabel(t('Name'));
    $properties['email'] = DataDefinition::create('email')
      ->setLabel(t('Email'));
    $properties['phone_number'] = DataDefinition::create('string')
      ->setLabel(t('Phone number'));

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
        'email' => [
          'description' => 'Email',
          'type' => 'varchar',
          'length' => Email::EMAIL_MAX_LENGTH,
        ],
        'phone_number' => [
          'description' => 'Phone number',
          'type' => 'varchar',
          'length' => 255,
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getConstraints() {
    $constraint_manager = \Drupal::typedDataManager()->getValidationConstraintManager();
    $constraints = parent::getConstraints();

    $constraints[] = $constraint_manager->create('ComplexData', [
      'email' => [
        'Length' => [
          'max' => Email::EMAIL_MAX_LENGTH,
          'maxMessage' => t('%name: the email address can not be longer than @max characters.', [
            '%name' => $this->getFieldDefinition()->getLabel(),
            '@max' => Email::EMAIL_MAX_LENGTH
          ]),
        ],
      ],
    ]);

    return $constraints;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('name')->getValue();
    return $value === NULL || $value === '';
  }

}
