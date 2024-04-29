#!/bin/sh


php artisan migrate

/usr/bin/supervisord -c /app/docker/supervisor/supervisor.conf
