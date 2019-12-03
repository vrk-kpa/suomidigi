<?php

namespace Drupal\suopa_blog_conversion\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Apply the automatic paragraph filter to content
 *
 * @MigrateProcessPlugin(
 *   id = "wp_content"
 * )
 */
class WpContent extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   *
   * Split the 'administer nodes' permission from 'access content overview'.
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    return _filter_autop($value);
  }

}
