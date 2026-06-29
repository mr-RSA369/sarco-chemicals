#!/bin/sh
set -e

echo "Checking public directory..."

if [ ! -f /var/www/public/index.php ]; then
    echo "Public directory is empty. Copying files..."

    cp -a /var/www-public-template/. /var/www/public/
fi

echo "Starting PHP-FPM..."

exec php-fpm
