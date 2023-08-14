FROM druidfi/drupal-web:php-8.1.19

# Copy files needed for building codebase
COPY --chown=druid:www-data composer.* /app/
COPY --chown=druid:www-data conf /app/conf
COPY --chown=druid:www-data public /app/public
COPY --chown=druid:www-data patches /app/patches
COPY --chown=druid:www-data scripts /app/scripts

# Create symlink for drupal public files
RUN ln -s /app/files/public /app/public/sites/default/files

# Create symlink for private files (TEMP solution so you don't need different settings.php for Amazee and AWS)
RUN ln -s /app/files/private /app/files_private

# Install Drupal, contrib modules and dependencies with composer.
RUN composer install --no-dev --optimize-autoloader --prefer-dist --no-progress
