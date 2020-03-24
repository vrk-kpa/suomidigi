<?php

/**
 * @file
 * Don't change anything here, it's magic.
 */

global $_aliases_stub;
if (empty($_aliases_stub)) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_URL, 'https://drush-alias.amazeeio.cloud/aliases.drushrc.php.stub');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
  $_aliases_stub = curl_exec($ch);
  curl_close($ch);
}
eval($_aliases_stub);
