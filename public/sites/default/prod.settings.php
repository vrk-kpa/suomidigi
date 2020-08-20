<?php
/**
 * @file
 * Drupal 8 production environment configuration file.
 *
 * This file will only be included on production environments.
 */

// Disabling stage file proxy on production, with that the module can be enabled even on production
$config['stage_file_proxy.settings']['origin'] = false;

// Use PHP transport for mails unless SMTP configuration is present.
if (!isset($_ENV['SMTP_ADDRESS'])) {
  $config['swiftmailer.transport']['transport'] = 'native';
}
