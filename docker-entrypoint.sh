#!/usr/bin/env bash

if [ ! -d /.composer ]; then
    mkdir /.composer
fi

# Install packages
chmod -R ugo+rw /.composer
cd /var/www/html
composer install

# Serve
cd /var/www/html/public
php -S 0.0.0.0:8000