#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/.."
docker compose build backend
if [ ! -f backend/.env ]; then cp backend/.env.example backend/.env; fi
docker compose run --rm backend composer install --no-interaction
if ! grep -q '^APP_KEY=base64:' backend/.env; then docker compose run --rm backend php artisan key:generate; fi
touch backend/database/database.sqlite
docker compose run --rm backend php artisan migrate --force
docker compose run --rm backend php artisan db:seed --force
docker compose up -d
