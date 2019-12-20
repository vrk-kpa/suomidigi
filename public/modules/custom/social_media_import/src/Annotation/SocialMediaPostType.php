<?php

namespace Drupal\social_media_import\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines the SocialMediaPostType annotation object.
 *
 * Plugin namespace: Plugin\SocialMediaPostType.
 *
 * @Annotation
 */
class SocialMediaPostType extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The plugin label.
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $label;

}
