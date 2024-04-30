#!/bin/sh


php artisan migrate --force

/usr/bin/supervisord -c /app/docker/supervisor/supervisor.conf
