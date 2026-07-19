#!/usr/bin/env sh
set -eu

PHP_CONTAINER="${SITEWIDGET_PHP_CONTAINER:-webserver_php_fpm}"
NGINX_CONTAINER="${SITEWIDGET_NGINX_CONTAINER:-webserver_nginx}"
APP_DIR="${SITEWIDGET_APP_DIR:-/var/www/sitewidget}"

echo "[sitewidget] after git pull"
echo "[sitewidget] php container: ${PHP_CONTAINER}"
echo "[sitewidget] nginx container: ${NGINX_CONTAINER}"
echo "[sitewidget] app dir: ${APP_DIR}"

if ! command -v docker >/dev/null 2>&1; then
  echo "[sitewidget] docker is not available, skip container steps"
  exit 0
fi

if ! docker inspect "${PHP_CONTAINER}" >/dev/null 2>&1; then
  echo "[sitewidget] container ${PHP_CONTAINER} not found, skip container steps"
  exit 0
fi

docker exec "${PHP_CONTAINER}" sh -lc "
  set -eu
  cd '${APP_DIR}'
  mkdir -p web/assets runtime/cache runtime/logs
  chown -R www-data:www-data web/assets runtime 2>/dev/null || true
  chmod -R 775 web/assets runtime 2>/dev/null || true
  if command -v composer >/dev/null 2>&1; then
    composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader
  else
    echo '[sitewidget] composer is not available in container, skip composer install'
  fi
"

docker exec "${PHP_CONTAINER}" sh -lc "
  set -eu
  cd '${APP_DIR}'
  if [ -f yii ]; then
    php yii migrate --interactive=0
  elif [ -f yii.php ]; then
    php yii.php migrate --interactive=0
  else
    echo '[sitewidget] yii entrypoint not found, skip migrations'
  fi
"

docker restart "${PHP_CONTAINER}" >/dev/null
if docker inspect "${NGINX_CONTAINER}" >/dev/null 2>&1; then
  docker restart "${NGINX_CONTAINER}" >/dev/null
fi

docker exec "${PHP_CONTAINER}" sh -lc "kill -USR2 1 2>/dev/null || true"

echo "[sitewidget] done"
