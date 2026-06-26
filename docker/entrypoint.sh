#!/bin/sh
set -eu

: "${PORT:=10000}"

sed -ri "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf
sed -ri "s!DocumentRoot /var/www/html!DocumentRoot /var/www/html/public!" /etc/apache2/sites-available/000-default.conf

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

exec docker-php-entrypoint "$@"
