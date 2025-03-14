#!/bin/bash
set -e

# Update and install dependencies
sudo apt update && sudo apt upgrade -y

# Stop and remove existing containers if any
docker-compose -f docker-compose.yml down || true

# Build and start containers
docker-compose -f docker-compose.yml up -d --build

# Run database migrations
docker-compose -f docker-compose.yml exec -T app php artisan migrate --force

# Optimize the application
docker-compose -f docker-compose.yml exec -T app php artisan config:cache
docker-compose -f docker-compose.yml exec -T app php artisan route:cache
docker-compose -f docker-compose.yml exec -T app php artisan view:cache
docker-compose -f docker-compose.yml exec -T app php artisan storage:link
docker-compose -f docker-compose.yml exec -T app chmod -R 775 storage bootstrap/cache

echo "Deployment complete!"