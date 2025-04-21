#!/bin/bash

mkdir -p /var/www/data
mkdir -p /var/www/data/vendor
mkdir -p /var/www/data/database

composer install --no-scripts

php application migrate

exec "$@"
