<?php

/**
 * @file
 * Drupal 8 configuration file.
 *
 * You should not edit this file, please use environment specific files!
 * They are loaded in this order:
 * - all.settings.php
 *   For settings that should be applied to all environments (dev, prod, staging, docker, etc).
 * - all.services.yml
 *   For services that should be applied to all environments (dev, prod, staging, docker, etc).
 * - prod.settings.php
 *   For settings only for the production environment.
 * - prod.services.yml
 *   For services only for the production environment.
 * - dev.settings.php
 *   For settings only for the development environment (devevlopment sites, docker).
 * - dev.services.yml
 *   For services only for the development environment (devevlopment sites, docker).
 * - settings.local.php
 *   For settings only for the local environment, this file will not be commited in GIT!
 * - services.local.yml
 *   For services only for the local environment, this file will not be commited in GIT!
 */

// Use druidfi/omen for env extractor.
extract((new Druidfi\Omen\DrupalEnvDetector(__DIR__))->getConfiguration());

// Private files path
$settings['file_private_path'] = realpath(__DIR__ . '/../../../files_private');

### Trusted Host Patterns, see https://www.drupal.org/node/2410395 for more information.
### If your site runs on multiple domains, you need to add these domains here
$settings['trusted_host_patterns'] = array(
  '^beta\.suomidigi\.cloud\.vrk\.fi$',
  '^prod\.suomidigi\.cloud\.vrk\.fi$',
  '^suomidigi\.fi$',
  '^www\.suomidigi\.fi$',
  '^' . str_replace('.', '\.', getenv('AMAZEEIO_SITE_URL')) . '$',
);

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
