ARG LARAVEL_PATH=/var/www/html

# Stage 1: Composer for PHP Dependencies
FROM composer:1.9.3 AS composer
ARG LARAVEL_PATH

COPY src/composer.json $LARAVEL_PATH
COPY src/composer.lock $LARAVEL_PATH
RUN composer install --working-dir $LARAVEL_PATH --ignore-platform-reqs --no-progress --no-autoloader --no-scripts

COPY src $LARAVEL_PATH
RUN composer install --working-dir $LARAVEL_PATH --ignore-platform-reqs --no-progress --optimize-autoloader

# Stage 2: Node.js for React
FROM node:16 AS node
ARG LARAVEL_PATH

WORKDIR $LARAVEL_PATH
COPY src/package.json src/package-lock.json ./
RUN npm install
RUN npm i -g webpack webpack-cli
COPY src/resources/js ./resources/js
COPY src/resources/sass ./resources/sass
COPY src/webpack.mix.js ./webpack.mix.js
COPY src/tsconfig.json ./tsconfig.json

RUN npm run prod # Build React assets

RUN echo "PWD:" && pwd
RUN echo "Listing contents of ./public/js and ./public/css" && ls -al ./public/js && ls -al ./public/css

# Stage 3: PHP and Apache for Laravel
FROM php:7.4-apache
ARG LARAVEL_PATH

# Install required PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev \
    && docker-php-ext-install \
        pdo_mysql \
        zip

# Configure Apache
ENV APACHE_DOCUMENT_ROOT $LARAVEL_PATH/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN a2enmod rewrite

# Copy Composer from the first stage
COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN mkdir -p /root/.composer
COPY --from=composer /tmp/cache /root/.composer/cache
COPY --from=composer $LARAVEL_PATH $LARAVEL_PATH

# Copy React build from the Node stage
COPY --from=node $LARAVEL_PATH/public/js $LARAVEL_PATH/public/js
COPY --from=node $LARAVEL_PATH/public/css $LARAVEL_PATH/public/css

# Set permissions
RUN chown -R www-data $LARAVEL_PATH/storage

# Set working directory
WORKDIR $LARAVEL_PATH
