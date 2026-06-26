#!/bin/sh
set -eu

: "${PORT:=10000}"
: "${DB_DATABASE:=/var/www/html/storage/database.sqlite}"

cd /var/www/html

# 1) Apache debe escuchar en el puerto que asigna Render.
sed -ri "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# 2) Directorios escribibles en runtime (algunos se excluyen del build vía .dockerignore).
mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    storage/app/public \
    bootstrap/cache \
    "$(dirname "$DB_DATABASE")"

# 3) Archivo SQLite.
touch "$DB_DATABASE"

chown -R www-data:www-data storage bootstrap/cache "$(dirname "$DB_DATABASE")"
chmod -R 775 storage bootstrap/cache

# 4) URL pública real de Render (para enlaces absolutos y back_urls de Mercado Pago).
if [ -n "${RENDER_EXTERNAL_URL:-}" ]; then
    export APP_URL="$RENDER_EXTERNAL_URL"
fi

# 5) Garantiza una APP_KEY (genera una efímera si no se definió en Render).
if [ -z "${APP_KEY:-}" ]; then
    APP_KEY="$(php artisan key:generate --show)"
    export APP_KEY
    echo "==> APP_KEY no definido: se generó uno efímero (defínelo en Render para que sea estable)."
fi

# 6) Base de datos: migra y siembra el catálogo (BookSeeder es idempotente).
php artisan migrate --force --no-interaction
php artisan db:seed --force --no-interaction || true

# 7) Cachea la configuración (las variables de entorno ya están disponibles).
php artisan config:clear
php artisan config:cache

exec docker-php-entrypoint "$@"
