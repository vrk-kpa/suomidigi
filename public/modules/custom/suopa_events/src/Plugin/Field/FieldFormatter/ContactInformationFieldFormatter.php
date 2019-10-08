<?php

namespace Drupal\suopa_events\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'contact_information_field_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "contact_information_field_formatter",
 *   label = @Translation("Contact information field formatter"),
 *   field_types = {
 *     "contact_information"
 *   }
 * )
 */
class ContactInformationFieldFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      // Implement default settings.
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    return [
      // Implement settings form.
    ] + parent::settingsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = $this->viewValue($item);
    }

    return $elements;
  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return array
   *   The textual output generated.
   */
  protected function viewValue(FieldItemInterface $item) {
    $name = $item->get('name')->getValue();
    $email = $item->get('email')->getValue();
    $phone_number = $item->get('phone_number')->getValue();

    return [
      '#theme' => 'contact_information',
      '#name' => $name,
      '#email' => $email,
      '#phone_number' => $phone_number,
    ];
  }

}
