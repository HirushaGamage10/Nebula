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

php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo "Nebula CI Deploy Finished"
