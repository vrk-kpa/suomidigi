<?php

declare(strict_types = 1);

namespace Drush\Commands;

use Consolidation\AnnotatedCommand\CommandData;
use Symfony\Component\Process\Process;

/**
 * A drush command file.
 */
final class CacheClearCommands extends DrushCommands {
  /**
   * Re-generate drush aliases.
   *
   * @hook post-command cache:rebuild
   */
  public function rebuildAliases($result, CommandData $data) {
    $process = new Process(['drush', 'site:alias-convert', '../drush/sites']);
    $process->run();
  }
}
