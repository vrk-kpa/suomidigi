<?php

// Use druidfi/omen to auto-configure Drupal
//
// You can setup project specific configuration in this directory:
//
// ENV.settings.php and ENV.services.yml
// and
// local.settings.php and local.service.yml
//
// These files are loaded automatically if found.
//
extract((new Druidfi\Omen\Reader(__DIR__))->get());
