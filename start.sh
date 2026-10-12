#!/bin/sh
echo "=== START SCRIPT JALAN ==="
echo "=== CLEAR CONFIG ==="
php artisan config:clear
echo "=== MIGRATE ==="
php artisan migrate --force
echo "=== CEK PUBLIC FOLDER ==="
ls -la public/
echo "=== STARTING SERVER DI PORT 8080 ==="
php -S 0.0.0.0:8080 -t public