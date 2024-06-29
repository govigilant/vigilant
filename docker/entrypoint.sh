#!/bin/sh

php artisan migrate --force
php artisan notifications:create
php artisan notifications:rename-classes

cp -f -r /tmp/public/* /app/public

/usr/bin/supervisord -c /app/docker/supervisor/supervisor.conf
