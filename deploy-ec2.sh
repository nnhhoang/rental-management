#!/bin/bash

# Update system packages
sudo yum update -y

# Install Docker
sudo amazon-linux-extras install docker -y
sudo service docker start
sudo systemctl enable docker
sudo usermod -a -G docker ec2-user

# Install Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Create necessary directories
mkdir -p nginx/conf.d
mkdir -p nginx/ssl
mkdir -p php
mkdir -p mysql

# Copy configuration files
cp nginx.conf nginx/conf.d/
cp local.ini php/
cp my.cnf mysql/

# Create .env file from example if not exists
if [ ! -f .env ]; then
    cp .env.example .env
    # Update .env file with production settings
    sed -i 's/APP_ENV=local/APP_ENV=production/' .env
    sed -i 's/APP_DEBUG=true/APP_DEBUG=false/' .env
    
    # Generate application key
    docker-compose exec app php artisan key:generate
    
    # Update database connection (if using RDS, update these values)
    # This is just a placeholder, you should manually update these values
    # sed -i 's/DB_HOST=127.0.0.1/DB_HOST=your-rds-endpoint.amazonaws.com/' .env
    # sed -i 's/DB_DATABASE=laravel/DB_DATABASE=your_db_name/' .env
    # sed -i 's/DB_USERNAME=root/DB_USERNAME=your_db_user/' .env
    # sed -i 's/DB_PASSWORD=/DB_PASSWORD=your_db_password/' .env
fi

# Build and start containers
docker-compose up -d

# Run migrations and seed database
docker-compose exec app php artisan migrate --force

# Install application dependencies and build assets
docker-compose exec app composer install --no-interaction --no-dev --optimize-autoloader
docker-compose exec app npm install
docker-compose exec app npm run build

# Optimize Laravel
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# Set proper permissions
docker-compose exec app chmod -R 775 storage bootstrap/cache

echo "Deployment completed successfully!"