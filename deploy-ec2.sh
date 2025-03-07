#!/bin/bash
set -e

# Update and install dependencies
sudo apt update && sudo apt install -y apt-transport-https ca-certificates curl software-properties-common

# Install Docker if needed
if ! command -v docker &> /dev/null; then
    curl -fsSL https://get.docker.com -o get-docker.sh
    sudo sh get-docker.sh
    sudo usermod -aG docker $USER
fi

# Install Docker Compose if needed
if ! command -v docker-compose &> /dev/null; then
    sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
    sudo chmod +x /usr/local/bin/docker-compose
fi

# Create required directories
mkdir -p nginx/conf.d nginx/ssl php

# Generate SSL certificates if needed
if [ ! -f nginx/ssl/rental.crt ]; then
    sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
        -keyout nginx/ssl/rental.key \
        -out nginx/ssl/rental.crt \
        -subj "/CN=localhost"
    sudo chmod 644 nginx/ssl/rental.crt nginx/ssl/rental.key
fi

# Create/copy config files
if [ ! -f php/local.ini ]; then
    echo "upload_max_filesize=40M
post_max_size=40M
memory_limit=512M
max_execution_time=600" > php/local.ini
fi

# Setup environment
if [ ! -f .env ]; then
    cp .env.production.example .env
fi

# Start containers
docker-compose up -d

# Setup application
docker-compose exec -T app composer install --no-dev --optimize-autoloader
docker-compose exec -T app php artisan key:generate --force
docker-compose exec -T app php artisan migrate --force
docker-compose exec -T app npm install
docker-compose exec -T app npm run build
docker-compose exec -T app php artisan storage:link
docker-compose exec -T app php artisan config:cache
docker-compose exec -T app php artisan route:cache
docker-compose exec -T app php artisan view:cache
docker-compose exec -T app chmod -R 775 storage bootstrap/cache

echo "Deployment complete!"