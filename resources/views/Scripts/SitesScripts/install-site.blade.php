set -e


# Remove The Current Site Directory

rm -rf /home/laraship/{{$site->domain}}

# Clone The Repository Into The Site
    git clone --depth 1 --single-branch -b 'main' git@github.com:torgodly/maqrah.git {{$site->domain}}

    cd /home/laraship/{{$site->domain}}

git submodule update --init --recursive

# Install Composer Dependencies If Requested

    cd /home/laraship/{{$site->domain}}
    php8.3 /usr/local/bin/composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Create Environment File If Necessary

if [ -f /home/laraship/{{$site->domain}}/artisan ]
then
    LARAVEL_VERSION=$(cat /home/laraship/{{$site->domain}}/composer.json | sed -n -e 's/.*"laravel\/framework": "[^0-9]*\([0-9.]\+\)".*/\1/p1' | cut -d "." -f 1)

    if [ -f /home/laraship/{{$site->domain}}/.env.example ]
    then
        cp /home/laraship/{{$site->domain}}/.env.example /home/laraship/{{$site->domain}}/.env
    else
        if [ $LARAVEL_VERSION -gt 10 ]
        then
            # Laravel >= 11
            cat > /home/laraship/{{$site->domain}}/.env << EOF
APP_NAME=Laravel
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_TIMEZONE=UTC
APP_URL=http://localhost

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
APP_MAINTENANCE_STORE=database

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=sqlite
# DB_HOST=145.223.81.191
# DB_PORT=3306
# DB_DATABASE=maqrah
# DB_USERNAME=laraship
# DB_PASSWORD="taKUPwwIEJcEFP2S"

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
MAIL_FROM_NAME="${APP_NAME}"

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

VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
EOF
        else
            # Laravel <= 10
            cat > /home/laraship/{{$site->domain}}/.env << EOF
APP_NAME=Laravel
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://localhost

LOG_CHANNEL=stack

DB_CONNECTION=
DB_HOST=145.223.81.191
DB_PORT=
DB_DATABASE=maqrah
DB_USERNAME=laraship
DB_PASSWORD="taKUPwwIEJcEFP2S"

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
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
EOF
        fi
    fi

    sed -i -r "s/APP_ENV=.*/APP_ENV=production/" /home/laraship/{{$site->domain}}/.env
    sed -i -r "s/APP_URL=.*/APP_URL=\"http:\/\/{{$site->domain}}\"/" /home/laraship/{{$site->domain}}/.env
    sed -i -r "s/APP_DEBUG=.*/APP_DEBUG=false/" /home/laraship/{{$site->domain}}/.env

        sed -i -r "s/DB_CONNECTION=.*/DB_CONNECTION=mysql/" /home/laraship/{{$site->domain}}/.env
    sed -i "s/^\(# DB_HOST=\|DB_HOST=\).*/DB_HOST=145.223.81.191/" /home/laraship/{{$site->domain}}/.env
    sed -i "s/^\(# DB_PORT=\|DB_PORT=\).*/DB_PORT=3306/" /home/laraship/{{$site->domain}}/.env
    sed -i "s/^\(# DB_DATABASE=\|DB_DATABASE=\).*/DB_DATABASE=maqrah/" /home/laraship/{{$site->domain}}/.env
    sed -i "s/^\(# DB_USERNAME=\|DB_USERNAME=\).*/DB_USERNAME=laraship/" /home/laraship/{{$site->domain}}/.env
    sed -i "s/^\(# DB_PASSWORD=\|DB_PASSWORD=\).*/DB_PASSWORD=\"taKUPwwIEJcEFP2S\"/" /home/laraship/{{$site->domain}}/.env

    sed -i -r "s/MEMCACHED_HOST=.*/MEMCACHED_HOST=127.0.0.1/" /home/laraship/{{$site->domain}}/.env
    sed -i -r "s/REDIS_HOST=.*/REDIS_HOST=127.0.0.1/" /home/laraship/{{$site->domain}}/.env
    sed -i -r "s/REDIS_PASSWORD=.*/REDIS_PASSWORD=\"\"/" /home/laraship/{{$site->domain}}/.env

        php8.3 /home/laraship/{{$site->domain}}/artisan key:generate --force || true
    fi

# Run Artisan Migrations If Requested

    php8.3 /home/laraship/{{$site->domain}}/artisan migrate --force || true
