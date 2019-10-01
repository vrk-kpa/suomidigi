<?php

namespace Drupal\suopa_content\Plugin\views\filter;

use Drupal\views\Plugin\views\filter\InOperator;

/**
 * Filter exclude previous viewed node's.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("views_exclude_previous")
 */
class SuopaContentExcludePreviousEntities extends InOperator {

  /**
   * {@inheritdoc}
   */
  public function getValueOptions() {
    $this->valueOptions = [
      'views' => 'Exclude nodes that where loaded in any node based view.',
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['operator']['default'] = 'not in';
    $options['value']['default'] = [];

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function operatorOptions($which = 'title') {
    return [
      'not in' => t('Is not in'),
    ];
  }

  /**
   * {@inheritdoc}
   *
   * Exclude all node's yet displayed.
   */
  public function query() {
    if (!$this->value) {
      return;
    }
    $excludes = [];
    $term_id = 0;

    if (\Drupal::routeMatch()->getRouteName() == 'entity.taxonomy_term.canonical') {
      $term_id = \Drupal::routeMatch()->getRawParameter('taxonomy_term');
    }
    elseif (!empty($this->view->args)) {
      $term_id = reset($this->view->args);
    }

    foreach ($this->value as $category) {
      $excludes += _suopa_content_exclude_previous_entities($category, $term_id);
    }

    if (!empty($excludes)) {
      $this->query->addWhere($this->options['group'], 'node_field_data.nid', $excludes, 'NOT IN');
    }
  }

}
