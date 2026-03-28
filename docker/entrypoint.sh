#!/bin/sh

cp -f -r /tmp/public/* /app/public

mkdir -p /app/storage/framework/cache
mkdir -p /app/storage/framework/sessions
mkdir -p /app/storage/framework/views
mkdir -p /app/storage/logs

if ! grep -q "^APP_KEY=" ".env" || [ -z "$(grep "^APP_KEY=" ".env" | cut -d '=' -f2)" ]; then
    php artisan key:generate
fi

php artisan optimize:clear
php artisan migrate --force
php artisan storage:link
php artisan notifications:create
php artisan notifications:rename-classes

/usr/bin/supervisord -c /app/docker/supervisor/supervisor.conf
