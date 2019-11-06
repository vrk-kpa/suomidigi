<?php

namespace Drupal\suopa_feed\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'feed_source_widget' widget.
 *
 * @FieldWidget(
 *   id = "feed_source_widget",
 *   module = "suopa_feed",
 *   label = @Translation("Feed source widget"),
 *   field_types = {
 *     "feed_source"
 *   }
 * )
 */
class FeedSourceWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'size' => 60,
      'placeholder' => '',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  /*public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = [];

    $elements['size'] = [
      '#type' => 'number',
      '#title' => t('Size of textfield'),
      '#default_value' => $this->getSetting('size'),
      '#required' => TRUE,
      '#min' => 1,
    ];
    $elements['placeholder'] = [
      '#type' => 'textfield',
      '#title' => t('Placeholder'),
      '#default_value' => $this->getSetting('placeholder'),
      '#description' => t('Text that will be shown inside the field until a value is entered. This hint is usually a sample value or a brief description of the expected format.'),
    ];

    return $elements;
  }*/

  /**
   * {@inheritdoc}
   */
  /*public function settingsSummary() {
    $summary = [];

    $summary[] = t('Textfield size: @size', ['@size' => $this->getSetting('size')]);
    if (!empty($this->getSetting('placeholder'))) {
      $summary[] = t('Placeholder: @placeholder', ['@placeholder' => $this->getSetting('placeholder')]);
    }

    return $summary;
  }*/

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#default_value' => isset($items[$delta]->name) ? $items[$delta]->name : NULL,
      '#maxlength' => 255,
      '#required' => $this->getFieldSetting('name') === DRUPAL_REQUIRED && $element['#required'],
    ];

    $element['url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('URL pattern'),
      '#description' => $this->t('Use %s in the URL template to place the keyword(s).'),
      '#default_value' => isset($items[$delta]->url) ? $items[$delta]->url : NULL,
      '#maxlength' => 2048,
      '#required' => $this->getFieldSetting('name') === DRUPAL_REQUIRED && $element['#required'],
    ];

    return $element;
  }

}
