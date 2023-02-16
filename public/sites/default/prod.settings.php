<?php

// Disabling stage file proxy on production, with that the module can be enabled even on production
$config['stage_file_proxy.settings']['origin'] = FALSE;

// Use PHP transport for mails unless SMTP configuration is present.
if (!isset($_SERVER['SMTP_ADDRESS'])) {
  $config['swiftmailer.transport']['transport'] = 'native';
}
