###
# Build theme using a separate theme builder container.
###
FROM node:8.16.0-alpine AS theme-builder

WORKDIR /usr/src/app
COPY public/themes/custom/suomidigi /usr/src/app
RUN npm install --production --engine-strict true
RUN npm run gulp production


###
# Build the actual container.
###
FROM druidfi/drupal:7.3-web

# Copy files needed for building codebase
COPY composer.* /app/
COPY conf /app/conf
COPY public /app/public
COPY patches /app/patches

# Create symlink for drupal public files
RUN ln -s /app/files/public /app/public/sites/default/files
# Create symlink for private files (TEMP solution so you don't need different settings.php for Amazee and AWS)
RUN ln -s /app/files/private /app/files_private

# Copy built theme files from the theme-builder container.
COPY --from=theme-builder /usr/src/app/dist /app/public/themes/custom/suomidigi/dist
COPY --from=theme-builder /usr/src/app/icons/svg /app/public/themes/custom/suomidigi/icons/svg
COPY --from=theme-builder /usr/src/app/icons/icons.svg /app/public/themes/custom/suomidigi/icons/icons.svg

# Install Drupal, contrib modules and dependencies with composer.
RUN whoami && \
    composer global require hirak/prestissimo && \
    composer install --no-dev --optimize-autoloader --prefer-dist --no-suggest
