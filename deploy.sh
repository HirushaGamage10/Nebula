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
	sudo -n find storage bootstrap/cache -type d -exec chmod 775 {} \; || true
	sudo -n find storage bootstrap/cache -type f -exec chmod 664 {} \; || true
fi

chmod -R ug+rwX storage bootstrap/cache || true
find storage bootstrap/cache -type d -exec chmod g+s {} \; || true

php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo "Nebula CI Deploy Finished"
