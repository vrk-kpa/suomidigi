<?php

namespace Drupal\suopa_content\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'ContentPrintButtonBlock' block.
 *
 * @Block(
 *  id = "content_print_button_block",
 *  admin_label = @Translation("Content print button block"),
 * )
 */
class ContentPrintButtonBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];

    $build['#button_title'] = t('Print this page');
    $build['#theme'] = 'suopa_content_print_button';

    return $build;
  }

}
