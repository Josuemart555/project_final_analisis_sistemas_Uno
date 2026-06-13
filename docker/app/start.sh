#!/usr/bin/env sh
set -eu

cd /var/www/html

if [ ! -f .env ]; then
    cp .env.example .env
fi

php -r '
$path = ".env";
$content = file_get_contents($path);
$keys = [
    "APP_ENV",
    "APP_DEBUG",
    "APP_URL",
    "FRONTEND_URL",
    "DB_CONNECTION",
    "DB_HOST",
    "DB_PORT",
    "DB_DATABASE",
    "DB_USERNAME",
    "DB_PASSWORD",
    "CACHE_STORE",
    "QUEUE_CONNECTION",
    "SESSION_DRIVER",
    "VITE_API_URL",
];

foreach ($keys as $key) {
    $value = getenv($key);

    if ($value === false) {
        continue;
    }

    $line = $key . "=" . $value;

    if (preg_match("/^" . preg_quote($key, "/") . "=.*/m", $content)) {
        $content = preg_replace("/^" . preg_quote($key, "/") . "=.*/m", $line, $content);
    } else {
        $content .= PHP_EOL . $line;
    }
}

file_put_contents($path, $content);
'

mkdir -p database
touch database/database.sqlite

git config --global --add safe.directory /var/www/html || true

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
