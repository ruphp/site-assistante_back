FROM php:8.4-fpm-alpine3.21

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY ./ /app/

RUN mkdir -p /app/runtime/cache
RUN mkdir -p /app/runtime/debug
RUN mkdir -p /app/runtime/HTML
RUN mkdir -p /app/runtime/URI
RUN mkdir -p /app/web/assets
RUN mkdir -p /app/web/static
RUN mkdir -p /app/web/custom

RUN chmod 777 -R /app/web/assets  \
    && chmod -R 777 /app/runtime  \
    && chmod -R 777 /app/web/static \
    && chmod -R 777 /app/web/custom

WORKDIR /app

RUN apk update && apk add zlib-dev libpng-dev freetype-dev libjpeg-turbo-dev libxml2-dev libxslt-dev libzip-dev unzip git
RUN set -ex && apk add postgresql-dev
RUN echo "max_input_vars=5000" >> /usr/local/etc/php/conf.d/help-layer.ini
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql pgsql intl xsl zip fileinfo gd
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

CMD ["php-fpm"]
