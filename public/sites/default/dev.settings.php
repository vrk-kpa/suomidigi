<?php

$settings['cache']['bins']['render'] = 'cache.backend.null';
$settings['cache']['bins']['page'] = 'cache.backend.null';
$settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';

// Use PHP transport for mails unless SMTP configuration is present.
if (!isset($_SERVER['SMTP_ADDRESS'])) {
  $config['swiftmailer.transport']['transport'] = 'native';
}

$config['stage_file_proxy.settings']['origin'] = 'https://www.suomidigi.fi';
$config['stage_file_proxy.settings']['hotlink'] = TRUE;
