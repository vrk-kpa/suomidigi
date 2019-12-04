<?php

namespace Drupal\suopa_blog_conversion\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * SubProcess (sub_process) needs and array of values. Convert value to array.
 *
 * @MigrateProcessPlugin(
 *   id = "key_wrapper"
 * )
 */
class MigrateProcessKeyWrapper extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (!is_array($value)) {
      $value = [$value];
    }
    return [$value];
  }

}
