#!/bin/bash
# Set up script for the project

# Get setup dir
SETUPDIR=$(dirname $0)
PROJECTDIR=$(dirname $SETUPDIR)

# Step into projectdir
cd ${PROJECTDIR}

# Initialize SQLite DB
touch "database/database.sqlite"

# Add migrations table
php artisan migrate:install

# Run migrations
php artisan migrate

echo 'Done'
