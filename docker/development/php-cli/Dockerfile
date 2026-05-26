FROM php:8.4-cli-alpine3.21

COPY ./ /app/

RUN apk add unzip
RUN cd /app && unzip /app/vendor.zip && rm -Rf /app/vendor.zip

WORKDIR /app

RUN apk update && apk add zlib-dev libpng-dev libxml2-dev libxslt-dev libzip-dev
RUN set -ex && apk add postgresql-dev
RUN echo "extension=zip.so" >> /usr/local/etc/php/conf.d/docker-php-ext-sodium.ini  \
    && echo "max_input_vars=5000" >> /usr/local/etc/php/conf.d/docker-php-ext-exif.ini  \
    && echo "extension=pdo_pgsql" >> /usr/local/etc/php/php.ini
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql && docker-php-ext-install pdo pdo_pgsql pgsql intl zip

RUN crontab cronjobs.txt

CMD php yii migrate --interactive=0 && crond && tail -f /dev/null