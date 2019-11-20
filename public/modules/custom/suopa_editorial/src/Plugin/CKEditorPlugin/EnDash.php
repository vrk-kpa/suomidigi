<?php

namespace Drupal\suopa_editorial\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\editor\Entity\Editor;

/**
 * Defines the "En dash helper" plugin.
 *
 * @CKEditorPlugin(
 *   id = "endash",
 *   label = @Translation("En dash"),
 * )
 */
class EnDash extends CKEditorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function isInternal() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return drupal_get_path('module', 'suopa_editorial') . '/assets/js/en-dash-helper.js';
  }

  /**
   * {@inheritdoc}
   */
  public function isEnabled(Editor $editor) {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    return [
      'endash' => [
        'label' => t('en dash'),
        'image_alternative' => [
          '#type' => 'inline_template',
          '#template' => '<a href="#" role="button" aria-label=""><span>–</span></a>',
          '#context' => [
            'endash' => t('en dash'),
          ],
        ],
      ],
    ];
  }

}
