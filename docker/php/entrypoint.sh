#!/usr/bin/env sh
set -e

mkdir -p \
  /var/www/html/storage/app/public \
  /var/www/html/storage/framework/cache \
  /var/www/html/storage/framework/sessions \
  /var/www/html/storage/framework/views \
  /var/www/html/bootstrap/cache

if [ ! -L /var/www/html/public/storage ]; then
  rm -rf /var/www/html/public/storage
  ln -s /var/www/html/storage/app/public /var/www/html/public/storage
fi

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true

exec "$@"
