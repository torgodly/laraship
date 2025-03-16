#!/bin/bash
set -e

SITE_DIR="/home/laraship/{{$site->domain}}"
REPO_URL="{{$site->deployments->first()->repository_url}}"
REPO_BRANCH="{{$site->deployments->first()->branch}}"


PHP_VERSION="{{$site->php_version}}"

# Function to generate .env file content
generate_env_content() {
  local DB_DATABASE="{{$site->database_name}}"  #This might be empty
  local DB_HOST="127.0.0.1"
  local DB_PORT="3306"
  local DB_USERNAME="laraship"
  # DO NOT HARDCODE PASSWORD IN SCRIPT!
  local DB_PASSWORD="FTr80vpftYO37LRu"
  local APP_ENV="local"
  local APP_DEBUG="false"  # CHANGE TO FALSE IN PRODUCTION
  local laravel_version=$1

  local db_connection="mysql"
  local db_vars=""
  local app_vars="
APP_NAME=Laravel
APP_ENV=$APP_ENV
APP_KEY=
APP_DEBUG=$APP_DEBUG
APP_TIMEZONE=UTC
APP_URL=http://{{$site->domain}}
  "

  if [ -z "$DB_DATABASE" ]; then
    db_connection="sqlite"
    db_vars="
DB_CONNECTION=sqlite
"
  else
    db_vars="
DB_CONNECTION=mysql
DB_HOST=$DB_HOST
DB_PORT=$DB_PORT
DB_DATABASE=$DB_DATABASE
DB_USERNAME=$DB_USERNAME
DB_PASSWORD="${DB_PASSWORD}"
"
  fi

  local app_debug_value="$APP_DEBUG" # Store APP_DEBUG value
  local app_env_value="$APP_ENV"     # Store APP_ENV value


  if [ "$laravel_version" -gt 10 ]; then
    cat << EOF
$app_vars

APP_LOCALE=en
APP_FALLBACK_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
APP_MAINTENANCE_STORE=database

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

$db_vars

BROADCAST_CONNECTION=log
CACHE_STORE=database
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=database
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=""
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME=\${APP_NAME}

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME=\${APP_NAME}
VITE_PUSHER_APP_KEY=\${PUSHER_APP_KEY}
VITE_PUSHER_HOST=\${PUSHER_HOST}
VITE_PUSHER_PORT=\${PUSHER_PORT}
VITE_PUSHER_SCHEME=\${PUSHER_APP_CLUSTER}
EOF
  else
    cat << EOF
$app_vars

LOG_CHANNEL=stack

$db_vars

BROADCAST_DRIVER=log
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=""
REDIS_PORT=6379

MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=null
MAIL_FROM_NAME=\${APP_NAME}

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY=\${PUSHER_APP_KEY}
VITE_PUSHER_HOST=\${PUSHER_HOST}
VITE_PUSHER_PORT=\${PUSHER_PORT}
VITE_PUSHER_SCHEME=\${PUSHER_APP_CLUSTER}
EOF
  fi
}

# Store the function definition in a variable
GENERATE_ENV_CONTENT_DEF="$(declare -f generate_env_content)"

# Remove The Current Site Directory
sudo -u laraship bash -c "$GENERATE_ENV_CONTENT_DEF; rm -rf '$SITE_DIR'" || { echo "Error: Failed to remove site directory"; exit 1; }

# Clone The Repository Into The Site
sudo -u laraship bash -c "$GENERATE_ENV_CONTENT_DEF; git clone --depth 1 --single-branch -b '$REPO_BRANCH' '$REPO_URL' '$SITE_DIR'" || { echo "Error: Git clone failed"; exit 1; }

# Switch to the site directory
cd "$SITE_DIR"

sudo -u laraship bash -c "$GENERATE_ENV_CONTENT_DEF; git submodule update --init --recursive" || { echo "Error: Git submodule update failed"; exit 1; }

# Set permissions for storage and cache directories
sudo -u laraship bash -c "$GENERATE_ENV_CONTENT_DEF; chmod -R 775 '$SITE_DIR/storage' '$SITE_DIR/bootstrap/cache'"

# Set the correct owner for the files
sudo -u laraship bash -c "$GENERATE_ENV_CONTENT_DEF; chown -R laraship:laraship '$SITE_DIR/storage' '$SITE_DIR/bootstrap/cache'"

# Install Composer Dependencies
sudo -u laraship bash -c "$GENERATE_ENV_CONTENT_DEF; $PHP_VERSION /usr/local/bin/composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader" || { echo "Error: Composer install failed"; exit 1; }

# Create Environment File
if [ -f "$SITE_DIR/artisan" ]; then
  # Determine Laravel Version
  #LARAVEL_VERSION=$(cat "$SITE_DIR/composer.json" | sed -n -e 's/.*"laravel\/framework": "[^0-9]*\([0-9.]\+\\)".*/\1/p' | cut -d "." -f 1)
  LARAVEL_VERSION=$($PHP_VERSION artisan --version | awk '{print $2}' | cut -d '.' -f 1) #More robust Laravel version detection

#  if [ -f "$SITE_DIR/.env.example" ]; then
#      sudo -u laraship bash -c "$GENERATE_ENV_CONTENT_DEF; cp '$SITE_DIR/.env.example' '$SITE_DIR/.env'"
#  else
      # Create .env file based on Laravel version and DB_DATABASE
      sudo -u laraship bash -c "$GENERATE_ENV_CONTENT_DEF; generate_env_content $LARAVEL_VERSION > '$SITE_DIR/.env'"
#  fi

  # Generate app key
  sudo -u laraship bash -c "$GENERATE_ENV_CONTENT_DEF; $PHP_VERSION artisan key:generate --force" || { echo "Error: Key generation failed"; exit 1; }
fi

# Run Artisan Migrations
sudo -u laraship bash -c "$GENERATE_ENV_CONTENT_DEF; $PHP_VERSION artisan migrate --force" || { echo "Error: Migration failed"; exit 1; }
