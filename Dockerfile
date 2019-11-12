FROM druidfi/drupal:7.3-web

# Copy files needed for building codebase
COPY composer.* /app/
COPY conf /app/conf
COPY public /app/public
COPY patches /app/patches
