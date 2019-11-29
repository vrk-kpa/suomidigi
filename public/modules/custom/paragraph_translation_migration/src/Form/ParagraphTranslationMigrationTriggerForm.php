<?php

namespace Drupal\paragraph_translation_migration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\field\Entity\FieldStorageConfig;

/**
 * Class ParagraphTranslationMigrationTriggerForm.
 *
 * @package Drupal\paragraph_translation_migration\Form
 */
class ParagraphTranslationMigrationTriggerForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'paragraph_translation_migration';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Migrate entities'),
    ];

    return $form;
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Get all the entities which contain entity reference revisions paragraphs.
    $entities = \Drupal::getContainer()
      ->get('entity_field.manager')
      ->getFieldMapByFieldType('entity_reference_revisions');

    foreach ($entities as $entity_type => $fields) {
      $entity_type = \Drupal::entityTypeManager()->getDefinition($entity_type);
      $this->handleMigration($fields, $entity_type);
    }
    drupal_set_message('Migration completed.');
  }

  /**
   * Handle migration.
   *
   * @param array $fields
   *   An array of fields.
   * @param object $entity_type
   *   Entity type.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  private function handleMigration(array $fields, $entity_type) {
    foreach ($fields as $field_name => $field) {
      $field_storage_config = FieldStorageConfig::loadByName($entity_type->id(), $field_name);

      if ($field_storage_config->getSetting('target_type') == 'paragraph') {
        foreach ($field['bundles'] as $bundle) {
          $entities = \Drupal::entityTypeManager()->getStorage($entity_type->id())->loadMultiple(\Drupal::entityQuery($entity_type->id())->condition($entity_type->getKey('bundle'), $bundle)->execute());
          foreach ($entities as $entity) {
            $translated_languages = $entity->getTranslationLanguages(FALSE);
            if (!empty($translated_languages)) {
              foreach ($translated_languages as $language) {
                $translated_paragraph_entities = [];
                $paragraph_values = $entity->get($field_name)->getValue();
                $paragraph_entities = [];

                foreach ($paragraph_values as $item) {
                  $paragraph_entities[$item['target_revision_id']] = \Drupal::entityTypeManager()
                    ->getStorage('paragraph')
                    ->loadRevision($item['target_revision_id']);
                }

                foreach ($paragraph_entities as $paragraph_entity) {
                  $translated_paragraph_entity = $paragraph_entity->createDuplicate();
                  if ($translated_paragraph_entity->hasTranslation($language->getId())) {
                    $translated_paragraph_entity->removeTranslation($language->getId());

                    foreach ($paragraph_entity->getFieldDefinitions() as $paragraph_field) {
                      if (!in_array(
                        $paragraph_field->getName(),
                        ['langcode', 'uuid', 'id', 'default_langcode']
                      )) {
                        $translated_paragraph_entity->set($paragraph_field->getName(), $paragraph_entity->getTranslation($language->getId())->get($paragraph_field->getName())->getValue());
                      }
                    }

                    $translated_paragraph_entity->save();
                    $translated_paragraph_entities[] = [
                      'target_id' => $translated_paragraph_entity->id(),
                      'target_revision_id' => $translated_paragraph_entity->getRevisionId(),
                    ];
                  }
                }

                $translated_entity = $entity->getTranslation($language->getId());
                $translated_entity->set($field_name, $translated_paragraph_entities);
                $translated_entity->save();
                drupal_set_message('Saved ' . $entity->getEntityType()->id() . ', id ' . $entity->id() . '. Language: ' . $language->getId() . '. Translated entity id: ' . $translated_entity->id());
              }
            }
          }
        }
      }
    }
  }

}
