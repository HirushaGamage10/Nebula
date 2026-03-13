#!/bin/bash
set -e

echo "Nebula CI Deploy Started"

cd /var/www/html/nebula

ENV_BACKUP="/tmp/nebula_env_$(date +%s)"

if [ -f .env ]; then
	cp .env "$ENV_BACKUP"
fi

git fetch origin main
git reset --hard origin/main

if [ -f "$ENV_BACKUP" ]; then
	mv "$ENV_BACKUP" .env
fi

if command -v sudo >/dev/null 2>&1 && sudo -n true >/dev/null 2>&1; then
	sudo -n chown -R cpo_admin:www-data storage bootstrap/cache || true
	sudo -n find storage bootstrap/cache -type d -exec chmod 775 {} \; || true
	sudo -n find storage bootstrap/cache -type f -exec chmod 664 {} \; || true
fi

chmod -R ug+rwX storage bootstrap/cache || true
find storage bootstrap/cache -type d -exec chmod g+s {} \; || true

php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo "Nebula CI Deploy Finished"
