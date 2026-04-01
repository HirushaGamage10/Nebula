#!/bin/bash
set -euo pipefail

echo "Nebula CI Deploy Started"

cd /var/www/html/nebula

php artisan down --retry=60 || true
trap 'php artisan up || true' EXIT

ENV_BACKUP="/tmp/nebula_env_$(date +%s)"

if [ -f .env ]; then
cp .env "$ENV_BACKUP"
fi

git fetch origin main
git reset --hard origin/main

if [ -f "$ENV_BACKUP" ]; then
cp "$ENV_BACKUP" .env
elif [ -f .env.backup ]; then
cp .env.backup .env
else
echo "ERROR: .env restoration source not found"
exit 1
fi

if ! grep -qE '^APP_KEY=base64:.+' .env; then
echo "ERROR: APP_KEY missing after .env restoration"
exit 1
fi

if command -v sudo >/dev/null 2>&1 && sudo -n true >/dev/null 2>&1; then
sudo -n chown cpo_admin:www-data .env || true
sudo -n chmod 640 .env || true
else
chmod 644 .env || true
fi

if command -v sudo >/dev/null 2>&1 && sudo -n true >/dev/null 2>&1; then
sudo -n chown -R cpo_admin:www-data storage bootstrap/cache || true
sudo -n find storage bootstrap/cache -type d -exec chmod 2775 {} + || true
sudo -n find storage bootstrap/cache -type f -exec chmod 664 {} + || true
else
find storage bootstrap/cache -type d -writable -exec chmod 2775 {} + 2>/dev/null || true
find storage bootstrap/cache -type f -writable -exec chmod 664 {} + 2>/dev/null || true
fi

php artisan config:clear
php artisan migrate --force

if ! php artisan cache:clear; then
echo "WARN: cache:clear failed due to file ownership/permissions; continuing deploy"
fi

if ! php artisan view:clear; then
echo "WARN: view:clear failed due to file ownership/permissions; continuing deploy"
fi

echo "Nebula CI Deploy Finished"
