#!/bin/sh

cd /app/public

drush cr
drush updb
drush cim
drush cr
drush status
