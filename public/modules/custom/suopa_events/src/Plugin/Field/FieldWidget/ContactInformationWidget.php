<?php

namespace Drupal\suopa_events\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\Email;

/**
 * Plugin implementation of the 'contact_information_widget' widget.
 *
 * @FieldWidget(
 *   id = "contact_information_widget",
 *   module = "suopa_events",
 *   label = @Translation("Contact information widget"),
 *   field_types = {
 *     "contact_information"
 *   }
 * )
 */
class ContactInformationWidget extends WidgetBase {

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
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);

    $elements['placeholder_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Placeholder for Name'),
      '#default_value' => $this->getSetting('placeholder_name'),
      '#description' => $this->t('Text that will be shown inside the field until a value is entered. This hint is usually a sample value or a brief description of the expected format.'),
    ];

    $elements['placeholder_email'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Placeholder for Email'),
      '#default_value' => $this->getSetting('placeholder_email'),
      '#description' => $this->t('Text that will be shown inside the field until a value is entered. This hint is usually a sample value or a brief description of the expected format.'),
    ];

    $elements['placeholder_phone_number'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Placeholder for Phone number'),
      '#default_value' => $this->getSetting('placeholder_phone_number'),
      '#description' => $this->t('Text that will be shown inside the field until a value is entered. This hint is usually a sample value or a brief description of the expected format.'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    $placeholder_name = $this->getSetting('placeholder_name');
    $placeholder_email = $this->getSetting('placeholder_email');
    $placeholder_phone_number = $this->getSetting('placeholder_phone_number');

    if (
      empty($placeholder_name) &&
      empty($placeholder_email) &&
      empty($placeholder_phone_number)
    ) {
      $summary[] = $this->t('No placeholders');
    }
    else {
      if (!empty($placeholder_name)) {
        $summary[] = $this->t('Title placeholder: @placeholder_name', ['@placeholder_name' => $placeholder_name]);
      }
      if (!empty($placeholder_email)) {
        $summary[] = $this->t('URL placeholder: @placeholder_email', ['@placeholder_email' => $placeholder_email]);
      }
      if (!empty($placeholder_phone_number)) {
        $summary[] = $this->t('URL placeholder: @placeholder_phone_number', ['@placeholder_phone_number' => $placeholder_phone_number]);
      }
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#placeholder' => $this->getSetting('placeholder_name'),
      '#default_value' => isset($items[$delta]->name) ? $items[$delta]->name : NULL,
      '#maxlength' => 255,
      '#required' => $this->getFieldSetting('name') === DRUPAL_REQUIRED && $element['#required'],
    ];

    $element['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
      '#placeholder' => $this->getSetting('placeholder_email'),
      '#default_value' => isset($items[$delta]->email) ? $items[$delta]->email : NULL,
      '#maxlength' => Email::EMAIL_MAX_LENGTH,
      '#required' => $this->getFieldSetting('email') === DRUPAL_REQUIRED && $element['#required'],
    ];

    $element['phone_number'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Phone number'),
      '#placeholder' => $this->getSetting('placeholder_phone_number'),
      '#default_value' => isset($items[$delta]->phone_number) ? $items[$delta]->phone_number : NULL,
      '#maxlength' => 255,
      '#required' => $this->getFieldSetting('phone_number') === DRUPAL_REQUIRED && $element['#required'],
    ];


    return $element;
  }

}
