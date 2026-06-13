#!/usr/bin/env sh
set -eu

cd /var/www/html

if [ ! -f .env ]; then
    cp .env.example .env
fi

composer install --no-interaction --prefer-dist

if ! grep -q '^APP_KEY=base64:.' .env; then
    php artisan key:generate --force --no-interaction
fi

if ! grep -q '^JWT_SECRET=.' .env; then
    php artisan jwt:secret --force --no-interaction
fi

php artisan config:clear
php artisan migrate --force
php artisan db:seed --force

php artisan serve --host=0.0.0.0 --port=8000
