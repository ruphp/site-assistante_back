FROM php:8.4-cli-alpine3.21

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY ./ /app/

WORKDIR /app

RUN apk update && apk add zlib-dev libpng-dev freetype-dev libjpeg-turbo-dev libxml2-dev libxslt-dev libzip-dev unzip git
RUN set -ex && apk add postgresql-dev
RUN echo "max_input_vars=5000" >> /usr/local/etc/php/conf.d/help-layer.ini
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql pgsql intl zip gd
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

RUN crontab cronjobs.txt

CMD php yii migrate --interactive=0 && crond && tail -f /dev/null
