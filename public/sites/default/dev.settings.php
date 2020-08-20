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

### amazee.io Varnish & Reverse proxy settings
# Use only in Amazee.io hosting, but not on local Docker env.
if (getenv('AMAZEEIO_HOSTINGSTACK') == 'fi1.compact' &&
  getenv('AMAZEEIO_VARNISH_HOSTS') &&
  getenv('AMAZEEIO_VARNISH_SECRET')) {
  $varnish_hosts = explode(',', getenv('AMAZEEIO_VARNISH_HOSTS'));
  array_walk($varnish_hosts, function(&$value, $key) { $value .= ':6082'; });

  $settings['reverse_proxy'] = TRUE;
  $settings['reverse_proxy_addresses'] = array_merge(explode(',', getenv('AMAZEEIO_VARNISH_HOSTS')), array('127.0.0.1'));

  $config['varnish.settings']['varnish_control_terminal'] = implode($varnish_hosts, " ");
  $config['varnish.settings']['varnish_control_key'] = getenv('AMAZEEIO_VARNISH_SECRET');
  $config['varnish.settings']['varnish_version'] = 4;
}

// Use PHP transport for mails unless SMTP configuration is present.
if (!isset($_ENV['SMTP_ADDRESS'])) {
  $config['swiftmailer.transport']['transport'] = 'native';
}
