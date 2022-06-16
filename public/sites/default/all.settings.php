<?php

// Private files path
$settings['file_private_path'] = realpath(__DIR__ . '/../../../files_private');

### Trusted Host Patterns, see https://www.drupal.org/node/2410395 for more information.
### If your site runs on multiple domains, you need to add these domains here
$settings['trusted_host_patterns'] = [
  '^suomidigi\.docker\.so$',
  '^suomidigi\.docker\.sh$',
  '^beta\.suomidigi\.cloud\.dvv\.fi$',
  '^prod\.suomidigi\.cloud\.dvv\.fi$',
  '^suomidigi\.fi$',
  '^www\.suomidigi\.fi$',
  '^testi\.suomidigi\.fi$',
];

// Set environment indicator.
switch (getenv('HTTP_HOST')) {
  case 'suomidigi.docker.so':
    $settings['simple_environment_indicator'] = '#0F0F0F';
    break;
  case 'dev.suomidigi.fi':
  case 'testi.suomidigi.fi':
  case 'beta.suomidigi.fi':
  case 'beta.suomidigi.cloud.vrk.fi':
    $settings['simple_environment_indicator'] = '#2a6ebb';
    break;
  default:
    $settings['simple_environment_indicator'] = '#bb2a2a';
    break;
}

$settings['config_exclude_modules'][] = 'update';
