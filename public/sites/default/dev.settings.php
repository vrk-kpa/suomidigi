<?php
/**
 * @file
 * Drupal 8 development environment configuration file.
 *
 * This file will only be included on development environments.
 */

// Disable Google Analytics from sending dev GA data.
if (getenv('HTTP_HOST') === 'suomidigi.docker.sh') {
  $config['google_analytics.settings']['account'] = 'UA-XXXXXXXX-YY';
}

// Stage file proxy URL from production URL
if (getenv('AMAZEEIO_PRODUCTION_URL')){
  $config['stage_file_proxy.settings']['origin'] = getenv('AMAZEEIO_PRODUCTION_URL');
}

$settings['cache']['bins']['render'] = 'cache.backend.null';
$settings['cache']['bins']['page'] = 'cache.backend.null';
$settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';
