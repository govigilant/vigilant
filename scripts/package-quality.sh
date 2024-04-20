#!/bin/sh

for dir in ./packages/*
do
    echo 'Checking ' $dir

    [ -f "$dir/composer.lock" ] && rm "$dir/composer.lock"

    composer install --working-dir=$dir --quiet || exit 1
    composer quality --working-dir=$dir || exit 1

    rm -rf "$dir/vendor"
    rm "$dir/composer.lock"
done
