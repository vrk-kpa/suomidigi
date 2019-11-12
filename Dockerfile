FROM node:8.16.0-alpine AS theme-builder

COPY public/themes/custom/suomidigi /usr/src/app
RUN npm install --production --engine-strict true
RUN npm run gulp production

FROM druidfi/drupal:7.3-web

# Copy files needed for building codebase
COPY composer.* /app/
COPY conf /app/conf
COPY public /app/public
COPY patches /app/patches

# Copy built theme files from the theme-builder container.
COPY --from=theme-builder /usr/src/app/dist /app/public/themes/custom/suomidigi/dist

RUN whoami && \
    composer global require hirak/prestissimo && \
    composer install --no-dev --optimize-autoloader --prefer-dist --no-suggest
