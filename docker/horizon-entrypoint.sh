#!/bin/sh

mkdir -p /app/storage/framework/cache
mkdir -p /app/storage/framework/sessions
mkdir -p /app/storage/framework/views
mkdir -p /app/storage/logs
chown -R www-data:www-data /app/storage

exec su-exec www-data php artisan horizon
