<?php

namespace Drupal\suopa_editorial\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Link;

/**
 * Provides a 'ContentCreationLinksBlock' block.
 *
 * @Block(
 *  id = "content_creation_links_block",
 *  admin_label = @Translation("Content creation links block"),
 * )
 */
class ContentCreationLinksBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $links = [];
    $links[] = Link::createFromRoute($this->t('New event'), 'node.add', ['node_type' => 'event'])->toRenderable();

    $build['#label'] = $this->t('Create new content');
    $build['#content'] = [
      '#theme' => 'item_list',
      '#items' => $links,
      '#attributes' => ['class' => ['links']],
    ];

    $build['#theme'] = 'content_creation_links_block';
    $build['#attached']['library'][] = 'suopa_editorial/content-creation-links';

    return $build;
  }

}
