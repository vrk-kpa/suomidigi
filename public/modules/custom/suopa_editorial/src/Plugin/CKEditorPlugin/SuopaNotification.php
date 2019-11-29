<?php

namespace Drupal\suopa_editorial\Plugin\CKEditorPlugin;

use Drupal\editor\Entity\Editor;
use Drupal\notification\Plugin\CKEditorPlugin\Notification;

/**
 * Defines the "notification" plugin.
 *
 * @CKEditorPlugin(
 *   id = "notification",
 *   label = @Translation("Notification"),
 * )
 */
class SuopaNotification extends Notification {

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return drupal_get_path('module', 'suopa_editorial') . "/assets/js/suopa-notification.js";
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
    return [];
  }

}
