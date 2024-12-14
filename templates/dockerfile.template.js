const { parseComposerJson } = require('../utils/composerParser');

// Disable eslint for Dockerfile template
/* eslint-disable */
function generateDockerfile(envVars) {
    const composerInfo = parseComposerJson();
    const phpVersion = composerInfo.phpVersion || '8.2';
    const systemPackages = composerInfo.systemPackages || [];
    const extensions = composerInfo.extensions || [];
    
    return `FROM serversideup/php:${phpVersion}-fpm-nginx

ENV PHP_OPCACHE_ENABLE=1
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_HOME=/composer

USER root

RUN apt-get update && apt-get install -y \\
    ${systemPackages.join(' \\\n    ')} \\
    && docker-php-ext-install ${extensions.join(' ')} \\
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN curl -fsSL https://deb.nodesource.com/setup_lts.x | bash - \\
    && apt-get install -y nodejs

RUN mkdir -p /var/www/html/storage/framework/{sessions,views,cache} \\
    && mkdir -p /var/www/html/storage/logs \\
    && mkdir -p /var/www/html/bootstrap/cache \\
    && chown -R www-data:www-data /var/www/html

COPY --chown=www-data:www-data . /var/www/html
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-interaction --optimize-autoloader --no-dev

USER www-data

RUN if [ -f "package.json" ]; then \\
        npm ci && \\
        npm run build; \\
    fi`;
}
/* eslint-enable */

module.exports = { generateDockerfile }; 