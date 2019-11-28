<?php

namespace Drupal\suopa_blog_conversion\Commands;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Database;
use Drupal\field\Entity\FieldConfig;
use Drupal\node\NodeStorageInterface;
use Drush\Commands\DrushCommands;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Output\Output;

/**
 * A Drush commandfile for changing blog type of content to blog content type.
 */
class ConvertBlogPosts extends DrushCommands {

  /**
   * The configuration object factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a new convertBlogPosts object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The configuration object factory.
   */
  public function __construct(ConfigFactoryInterface $configFactory) {
    $this->configFactory = $configFactory;
  }

  /**
   * Convert blog posts.
   *
   * @command suopa_blog_conversion:convertBlogPosts
   * @aliases convert-blogs
   * @usage convert-blogs
   *   Convert all Article nodes of Blog sub-content type to Blog content type.
   *
   * @return false
   *   Returns false or nothing.
   */
  public function convertBlogPosts($action) {

    // Get all the entities which contain entity reference revisions paragraphs.
    $term = taxonomy_term_load_multiple_by_name('blogi', 'article_type');
    $term = reset($term);

    $nids = \Drupal::entityQuery('node')
      ->condition('type', 'article')
      ->condition('field_article_type', $term->tid->value)
      ->execute();

    if (empty($nids)) {
      $this->output()->writeln('All blog articles have been converted to Blog posts.');
      return FALSE;
    }

    $this->handleMigration($nids);
  }

  /**
   * Handle conversion.
   */
  private function handleMigration($nids) {

    // Get the names of the base tables.
    $storage = \Drupal::service('entity_type.manager')->getStorage('node');
    $base_table_names = [];
    $base_table_names[] = $storage->getBaseTable();
    $base_table_names[] = $storage->getDataTable();

    // Base tables have 'nid' and 'type' columns.
    foreach ($base_table_names as $table_name) {
      $query = Database::getConnection('default')
        ->update($table_name)
        ->fields(['type' => 'blog_post'])
        ->condition('nid', $nids, 'IN')
        ->execute();
    }
    // Field tables have 'entity_id' and 'bundle' columns.
    foreach ($this->getFieldTableNames($storage) as $table_name) {
      $query = Database::getConnection('default')
        ->update($table_name)
        ->fields(['bundle' => 'blog_post'])
        ->condition('entity_id', $nids, 'IN')
        ->execute();
    }
    $this->logger()->success(dt('All blog posts were successfully converted.'));
  }

  /**
   * Get the names of the field tables for fields on the article node type.
   *
   * @param \Drupal\node\NodeStorageInterface $storage
   *   Node storage.
   *
   * @return array
   *   Returns an array of field table names.
   */
  private function getFieldTableNames(NodeStorageInterface $storage) {
    $table_mapping = $storage->getTableMapping();
    $bundle_fields = \Drupal::entityManager()->getFieldDefinitions('node', 'article');
    $field_table_names = [];
    foreach ($bundle_fields as $field) {
      if (!$field instanceof FieldConfig) {
        continue;
      }

      $field_table = $table_mapping->getFieldTableName($field->getName());
      $field_table_names[] = $field_table;

      $field_storage_definition = $field->getFieldStorageDefinition();
      $field_revision_table = $table_mapping
        ->getDedicatedRevisionTableName($field_storage_definition);
      $field_table_names[] = $field_revision_table;
    }
    return $field_table_names;
  }

  /**
   * Interact with user.
   *
   * @hook interact suopa_blog_conversion:convertBlogPosts
   *
   * @throws \Drush\Exceptions\UserAbortException
   *   Thrown when the user cancels the operation during CLI interaction.
   */
  public function interactConvertBlogPosts(Input $input, Output $output) {
    if (!$input->getArgument('action')) {
      $term = taxonomy_term_load_multiple_by_name('blogi', 'article_type');
      $term = reset($term);

      $nids = \Drupal::entityQuery('node')
        ->condition('type', 'article')
        ->condition('field_article_type', $term->tid->value)
        ->execute();

      $actions['continue'] = dt('Continue');
      $action = $this->io()->choice(dt(count($nids) . ' article "blog" nodes were found. Continue with the conversion?'), $actions);
      $input->setArgument('action', $action);
    }
  }

}
