FROM node:8.16.0-alpine AS theme-builder

WORKDIR /usr/src/app
COPY public/themes/custom/suomidigi /usr/src/app
RUN npm install --engine-strict true
RUN npm run gulp development

FROM druidfi/drupal:7.3-web

# Copy files needed for building codebase
COPY composer.* /app/
COPY conf /app/conf
COPY public /app/public
COPY patches /app/patches

# Copy built theme files from the theme-builder container.
COPY --from=theme-builder /usr/src/app/dist /app/public/themes/custom/suomidigi/dist
COPY --from=theme-builder /usr/src/app/icons/svg /app/public/themes/custom/suomidigi/icons/svg
COPY --from=theme-builder /usr/src/app/icons/icons.svg /app/public/themes/custom/suomidigi/icons/icons.svg

RUN whoami && \
    composer global require hirak/prestissimo && \
    composer install --no-dev --optimize-autoloader --prefer-dist --no-suggest
