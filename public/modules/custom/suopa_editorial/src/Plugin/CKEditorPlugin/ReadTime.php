<?php

namespace Drupal\suopa_editorial\Plugin\CKEditorPlugin;

use Drupal\ckwordcount\Plugin\CKEditorPlugin\Wordcount;

/**
 * Defines the "readtime" plugin.
 *
 * @CKEditorPlugin(
 *   id = "readtime",
 *   label = @Translation("Read time calculator")
 * )
 */
class ReadTime extends Wordcount {

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return drupal_get_path('module', 'suopa_editorial') . "/assets/js/plugin.js";
  }

}
