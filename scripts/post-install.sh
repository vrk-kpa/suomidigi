#!/bin/sh

cd /app/public

drush cr
drush updb -y
drush cim -y
drush cr
drush status
