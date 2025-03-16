set -e

SITE_DIR="/home/laraship/{{$site->domain}}"
REPO_URL="{{$site->deployments->first()->repository_url}}"
REPO_BRANCH="{{$site->deployments->first()->branch}}"
DB_HOST="127.0.0.1"
DB_PORT="3306"
DB_DATABASE="{{$site->database_name}}"
DB_USERNAME="laraship"
DB_PASSWORD="{{env('DB_PASSWORD')}}"
APP_ENV="local"
APP_DEBUG="true"
PHP_VERSION="{{$site->php_version}}"

# Function to generate .env file content
generate_env_content() {
local laravel_version=$1
local db_connection="mysql"
local db_vars=""

@if (empty($site->database_name))
    @php
        $db_vars = "
        DB_CONNECTION=sqlite
        #DB_HOST=127.0.0.1
        #DB_PORT=3306
        #DB_DATABASE=laraship
        #DB_USERNAME=root
        #DB_PASSWORD=
        ";
    @endphp
@else
    @php
        $db_vars = '
        DB_CONNECTION=mysql
        DB_HOST="$DB_HOST"
        DB_PORT="$DB_PORT"
        DB_DATABASE="$DB_DATABASE"
        DB_USERNAME="$DB_USERNAME"
        DB_PASSWORD="$DB_PASSWORD"
        ';
    @endphp
@endif

if [ "$laravel_version" -gt 10 ]; then
cat << EOF
APP_NAME=Laravel
APP_ENV=$APP_ENV
APP_KEY=
APP_DEBUG=$APP_DEBUG
APP_TIMEZONE=UTC
APP_URL=http://{{$site->domain}}

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
APP_MAINTENANCE_STORE=database

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

{{$db_vars}}

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
VITE_PUSHER_SCHEME=\${PUSHER_SCHEME}
VITE_PUSHER_APP_CLUSTER=\${PUSHER_APP_CLUSTER}
EOF
else
cat << EOF
APP_NAME=Laravel
APP_ENV=$APP_ENV
APP_KEY=
APP_DEBUG=$APP_DEBUG
APP_URL=http://{{$site->domain}}

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
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY=\${PUSHER_APP_KEY}
VITE_PUSHER_HOST=\${PUSHER_HOST}
VITE_PUSHER_PORT=\${PUSHER_PORT}
VITE_PUSHER_SCHEME=\${PUSHER_SCHEME}
VITE_PUSHER_APP_CLUSTER=\${PUSHER_APP_CLUSTER}
EOF
fi
}

# Remove The Current Site Directory
rm -rf "$SITE_DIR"

# Execute commands as the 'laraship' user
su - laraship -c "
# Clone The Repository Into The Site
git clone --depth 1 --single-branch -b '$REPO_BRANCH' \"$REPO_URL\" \"$SITE_DIR\"

cd \"$SITE_DIR\"

git submodule update --init --recursive

# Set permissions for storage and cache directories
chmod -R 775 \"$SITE_DIR/storage\" \"$SITE_DIR/bootstrap/cache\"

# Set the correct owner for the files
chown -R laraship:laraship \"$SITE_DIR/storage\" \"$SITE_DIR/bootstrap/cache\"

# Install Composer Dependencies
${PHP_VERSION} /usr/local/bin/composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Create Environment File
if [ -f \"$SITE_DIR/artisan\" ]; then
# Determine Laravel Version
LARAVEL_VERSION=\$(cat \"$SITE_DIR/composer.json\" | sed -n -e 's/.*\"laravel\/framework\": \"[^0-9]*\\([0-9.]\\+\\)\".*/\\1/p' | cut -d \".\" -f 1)

if [ -f \"$SITE_DIR/.env.example\" ]; then
cp \"$SITE_DIR/.env.example\" \"$SITE_DIR/.env\"
else
# Create .env file based on Laravel version and DB_DATABASE
generate_env_content \$LARAVEL_VERSION > \"$SITE_DIR/.env\"
fi

# Generate app key
${PHP_VERSION} \"$SITE_DIR/artisan\" key:generate --force || true
fi

# Run Artisan Migrations
${PHP_VERSION} \"$SITE_DIR/artisan\" migrate --force || true
"
