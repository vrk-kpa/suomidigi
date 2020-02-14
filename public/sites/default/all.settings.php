<?php
/**
 * @file
 * Drupal 8 all environment configuration file.
 *
 * This file should contain all settings.php configurations that are needed by all environments.
 */
// Defines where the sync folder of your configuration lives. In this case it's inside
// the Drupal root, which is protected by amazee.io nginx configs, so it cannot be read
// via the browser. If your Drupal root is inside a subfolder (like 'web') you can put the config
// folder outside this subfolder for an advanced security measure: '../config/sync'.
$config_directories[CONFIG_SYNC_DIRECTORY] = '../conf/cmi';

// Public files path
$settings['file_public_path'] = 'sites/default/files';

// Private files path
$settings['file_private_path'] = realpath(__DIR__ . '../../../files_private');

// Disable CND by default
$config['cdn.settings'] = [
  'status' => FALSE,
];

// Set environment indicator.
switch (getenv('HTTP_HOST')) {
  case 'suomidigi.docker.sh':
    $settings['simple_environment_indicator'] = '#0F0F0F';
    break;
  case 'dev.suomidigi.fi':
  case 'testi.suomidigi.fi':
    $settings['simple_environment_indicator'] = '#2a6ebb';
    break;
  default:
    $settings['simple_environment_indicator'] = '#bb2a2a';
    break;
}
