#!/bin/bash
set -e

echo "Deployment started ..."

eval "$(ssh-agent -s)"
ssh-add ~/.ssh/money_tracker

# Pull the latest version of the app
git pull origin master

# Install composer dependencies
/opt/php83/bin/php composer.phar install

# Clear the old cache
/opt/php83/bin/php artisan clear-compiled

# Recreate cache
/opt/php83/bin/php artisan optimize

# Run database migrations
/opt/php83/bin/php artisan migrate

# Run database migrations
/opt/php83/bin/php artisan migrate

# Run docs generation
/opt/php83/bin/php artisan scribe:generate

echo "Deployment finished!"
