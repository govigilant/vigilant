#!/bin/sh

php artisan migrate --force
php artisan notifications:create
php artisan notifications:rename-classes

cp -f -r /tmp/public/* /app/public

if ! grep -q "^APP_KEY=" ".env" || [ -z "$(grep "^APP_KEY=" ".env" | cut -d '=' -f2)" ]; then
    php artisan key:generate
fi

/usr/bin/supervisord -c /app/docker/supervisor/supervisor.conf
