<?php

namespace Drupal\suopa_legislations;

use Drupal\Core\Cache\Cache;
use Drupal\node\Entity\Node;

/**
 * LegislationService provides methods for retrieving legislation cards.
 */
class LegislationService {

  /**
   * The current legislation taxonomy term id.
   *
   * @var int
   */
  private $legislationId;

  /**
   * The current legislation card id.
   *
   * @var int
   */
  private $currentLegislationCardId;

  /**
   * Get next and previous legislation cards.
   */
  public function getNextAndPreviousCards($nid) {
    $nids = [];
    $this->currentLegislationCardId = $nid;

    if ($cached = $this->getCached(FALSE)) {
      return $cached->data;
    }
    else {
      $node = Node::load($this->currentLegislationCardId);

      // Get all nodes based on legislation section field.
      if ($node->hasField('field_legislation_section')) {
        $section_id = $node->get('field_legislation_section')->getString();
        $taxonomy_term = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
        $terms = $taxonomy_term->loadAllParents($section_id);

        // Root term is first in the taxonomy tree.
        if (!empty($terms)) {
          // Find the correct parent term.
          // It should be on second level in the vocabulary.
          $parent_term = end($terms);
          $parent_term = prev($terms);

          $child_terms = $taxonomy_term->loadChildren($parent_term->id());

          if (is_array($child_terms) && !empty($child_terms)) {
            foreach ($child_terms as $term) {
              $this->legislationId = $term->id();
              $nids = array_unique(array_merge($nids, $this->getLegislationNids()));
            }
          }
        }
      }
    }

    if (!empty($nids)) {
      $this->setCache($nids, FALSE);
    }

    return $nids;
  }

  /**
   * Get related legislation cards based on legislation taxonomy term.
   *
   * @param int $id
   *   Term to search the legislation cards with.
   *
   * @return array
   *   Returns an array of node ids.
   */
  public function getRelatedLegislationCards($id) {
    $this->legislationId = $id;

    if ($cached = $this->getCached()) {
      $ids = $cached->data;
    }
    else {
      $ids = $this->getLegislationNids();

      if (!empty($ids)) {
        $this->setCache($ids);
      }
    }

    return $ids;
  }

  /**
   * Retrieve the summary page based on current node id.
   *
   * @param int $current_nid
   *   Node id for the current page.
   *
   * @return int|bool
   *   Returns node id or False.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   *   Thrown if the entity type doesn't exist.
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   *   Thrown if the storage handler couldn't be loaded.
   */
  public function getLegislationCollectionTerm($current_nid) {
    // Return the legislation collection page nid from cache if it is available.
    $cid = 'cache_tag_' . $current_nid . '__legislation_collection_page';
    if ($cached = \Drupal::cache()->get($cid)) {
      return $cached->data;
    }

    // Get the taxonomy term id associated with current nid.
    $query = \Drupal::database()->select('taxonomy_index', 'ti');
    $tid = $query
      ->fields('ti', ['tid'])
      ->condition('ti.nid', $current_nid)
      ->execute()
      ->fetchField();

    // Load all parents for the current taxonomy term. Conduct a search
    // with all the parent terms to find a correct legislation collection page
    // for the current taxonomy tree.
    if (!empty($tid)) {
      $parents = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->loadAllParents($tid);

      $query = \Drupal::database()->select('paragraph__field_legislation_section', 'section');
      $query->join('node__field_legis_cpage_paragraphs', 'node', 'section.entity_id = node.field_legis_cpage_paragraphs_target_id');
      $nid = $query
        ->fields('node', ['entity_id'])
        ->condition('section.field_legislation_section_target_id', array_keys($parents), 'IN')
        ->distinct(TRUE)
        ->execute()
        ->fetchField();

      // Cache node id before returning it.
      if (!empty($nid)) {
        \Drupal::cache()->set(
          $cid,
          $nid,
          Cache::PERMANENT,
          ['node:' . $current_nid, 'node:' . $nid]
        );

        return $nid;
      }
    }
    return FALSE;
  }

  /**
   * Retrieve cached data if available.
   *
   * @param bool $cache_term
   *   Determines if cache retrieved is based on taxonomy term or on node.
   *
   * @return false|object
   *   Returns the cache item or FALSE on failure.
   */
  private function getCached($cache_term = TRUE) {
    $cid = ($cache_term)
      ? 'cache_tag_term_id__' . $this->legislationId
      : 'cache_tag_node_id__' . $this->currentLegislationCardId;
    return \Drupal::cache()->get($cid);
  }

  /**
   * Retrieve legislation cards based on legislation taxonomy term id.
   *
   * @return mixed
   *   Returns an array of node ids.
   */
  protected function getLegislationNids() {
    $query = \Drupal::database()->select('node', 'n');
    $query->join('node_field_data', 'nfd', 'n.nid = nfd.nid');
    $query->join('taxonomy_index', 'ti', 'n.nid = ti.nid');
    $query->join('node__field_legislation_section', 'nfls', 'n.nid = nfls.entity_id');

    return $query
      ->fields('n', ['nid'])
      ->fields('ti', ['weight'])
      ->condition('n.type', 'legislation_card')
      ->condition('nfd.status', 1)
      ->condition('nfls.field_legislation_section_target_id', $this->legislationId)
      ->condition('ti.tid', $this->legislationId, 'IN')
      ->distinct(TRUE)
      ->orderBy('ti.weight', 'ASC')
      ->execute()
      ->fetchCol();
  }

  /**
   * Cache data based on either taxonomy term or node.
   *
   * @param array $ids
   *   An array of node ids to cache.
   * @param bool $cache_term
   *   Determines if cache retrieved is based on taxonomy term or on node.
   */
  protected function setCache(array $ids, $cache_term = TRUE) {
    if (!empty($ids)) {
      $cache_tags = ($cache_term) ? ['term:' . $this->legislationId] : [];
      foreach ($ids as $id) {
        $cache_tags[] = 'node:' . $id;
      }

      $cid = ($cache_term)
        ? 'cache_tag_term_id__' . $this->legislationId
        : 'cache_tag_node_id__' . $this->currentLegislationCardId;

      \Drupal::cache()->set(
        $cid,
        $ids,
        Cache::PERMANENT,
        $cache_tags
      );
    }
  }

}
