function generateDockerCompose(envVars, migrationPreferences = {}, featurePreferences = {}) {
    const services = [];
    const volumes = [];
    const commands = [];

    // Always run composer install first
    commands.push('composer install --no-interaction');

    // Handle post-build migrations and seeding
    if (migrationPreferences.runMigrations) {
        commands.push('php artisan migrate --force');
    }
    if (migrationPreferences.runSeeder) {
        commands.push('php artisan db:seed --force');
    }

    // Add backup package installation if needed
    if (featurePreferences.backupSetup?.needsInstall) {
        commands.push(featurePreferences.backupSetup.installCommand);
    }

    // Add migrations after package installations
    if (migrationPreferences.runMigrations) {
        commands.push(migrationPreferences.forceMigrations ? 
            'php artisan migrate --force' : 
            'php artisan migrate'
        );
    }

    // Add any other commands (migrations, etc)
    if (commands.length) {
        commands.push('php-fpm'); // Add the main service command at the end
    }

    // Main app service
    services.push(`
    app:
        build:
            context: .
            dockerfile: Dockerfile
        image: ${envVars.parsed.APP_NAME?.toLowerCase() || 'laravel'}-image
        container_name: ${envVars.parsed.APP_NAME?.toLowerCase() || 'laravel'}-container
        restart: unless-stopped
        working_dir: /var/www/html
        depends_on: ${generateDependsOn(featurePreferences)}
        volumes:
            - .:/var/www/html
            - ./storage/logs:/var/www/html/storage/logs
            - ./storage/app:/var/www/html/storage/app
            - ./storage/framework:/var/www/html/storage/framework
            ${featurePreferences.enableBackups ? '- backups:/var/www/html/storage/app/backups' : ''}
        environment:
            ${envVars.orderedEnv.map(line => 
                line === '' ? '' : `- ${line}`
            ).join('\n            ')}
        ports:
            - "80:80"
            ${featurePreferences.enableSSL ? '- "443:443"' : ''}
        networks:
            - app-network
        command: sh -c "php artisan config:cache && php artisan route:cache && php-fpm"
        healthcheck:
            test: ["CMD-SHELL", "php artisan --version || exit 1"]
            interval: 30s
            timeout: 10s
            retries: 3
            start_period: 30s`);

    // Add Redis if selected
    if (featurePreferences.redisSetup === 'redis') {
        services.push(`
    redis:
        image: redis:alpine
        container_name: ${envVars.parsed.APP_NAME?.toLowerCase() || 'laravel'}_redis
        restart: unless-stopped
        networks:
            - app-network`);

        // Update app service environment variables
        envVars.parsed.REDIS_HOST = 'redis';
        envVars.parsed.REDIS_PASSWORD = null;
        envVars.parsed.CACHE_DRIVER = 'redis';
        envVars.parsed.QUEUE_CONNECTION = 'redis';
    }

    // Add Queue Worker if selected
    if (featurePreferences.additionalServices?.includes('queue')) {
        services.push(`
    queue:
        build: .
        container_name: ${envVars.parsed.APP_NAME?.toLowerCase() || 'laravel'}_queue
        restart: unless-stopped
        command: php artisan queue:work
        networks:
            - app-network`);
    }

    // Add Scheduler if selected
    if (featurePreferences.additionalServices?.includes('scheduler')) {
        services.push(`
    scheduler:
        build: .
        container_name: ${envVars.parsed.APP_NAME?.toLowerCase() || 'laravel'}_scheduler
        restart: unless-stopped
        command: php artisan schedule:work
        networks:
            - app-network`);
    }

    // Add PHPMyAdmin if selected
    if (featurePreferences.additionalServices?.includes('phpmyadmin')) {
        services.push(`
    phpmyadmin:
        image: phpmyadmin/phpmyadmin
        container_name: ${envVars.parsed.APP_NAME?.toLowerCase() || 'laravel'}_phpmyadmin
        environment:
            PMA_HOST: ${envVars.parsed.DB_HOST}
        ports:
            - "8080:80"
        networks:
            - app-network`);
    }

    // Add Mailhog if selected
    if (featurePreferences.additionalServices?.includes('mailhog')) {
        services.push(`
    mailhog:
        image: mailhog/mailhog
        container_name: ${envVars.parsed.APP_NAME?.toLowerCase() || 'laravel'}_mailhog
        ports:
            - "1025:1025"
            - "8025:8025"
        networks:
            - app-network`);
    }

    // Add MySQL service if user wants it
    if (featurePreferences.databaseSetup === 'mysql') {
        services.push(`
    db:
        image: mysql:8.0
        container_name: ${envVars.parsed.APP_NAME?.toLowerCase() || 'laravel'}_db
        restart: unless-stopped
        command: --default-authentication-plugin=mysql_native_password
        environment:
            MYSQL_DATABASE: ${featurePreferences.dbName || 'laravel'}
            MYSQL_ROOT_PASSWORD: '${featurePreferences.dbRootPassword}'
            MYSQL_PASSWORD: '${featurePreferences.dbRootPassword}'
            MYSQL_USER: ${featurePreferences.dbUsername || 'laravel_user'}
        volumes:
            - mysql-data:/var/lib/mysql
            - ./docker/mysql/init:/docker-entrypoint-initdb.d
        networks:
            - app-network
        healthcheck:
            test: ["CMD", "mysqladmin", "ping", "-p${featurePreferences.dbRootPassword}"]
            interval: 10s
            timeout: 5s
            retries: 5`);

        // Update app service environment variables for MySQL
        envVars.parsed.DB_CONNECTION = 'mysql';
        envVars.parsed.DB_HOST = 'db';
        envVars.parsed.DB_DATABASE = featurePreferences.dbName;
        envVars.parsed.DB_USERNAME = featurePreferences.dbUsername;
        envVars.parsed.DB_PASSWORD = featurePreferences.dbRootPassword;

        volumes.push(`
    mysql-data:
        driver: local`);
    }

    return `version: '3.8'

services:
${services.join('\n')}

networks:
    app-network:
        driver: bridge

volumes:
    backups:
        driver: local
    composer-cache:
        driver: local
    ${volumes.join('\n    ')}`;
}

function generateDependsOn(featurePreferences) {
    const dependencies = [];
    
    if (featurePreferences.databaseSetup === 'mysql') {
        dependencies.push('db');
    }
    if (featurePreferences.redisSetup === 'redis') {
        dependencies.push('redis');
    }

    if (dependencies.length === 0) {
        return '';
    }

    return `\n        depends_on:\n            ${dependencies.map(dep => `- ${dep}`).join('\n            ')}`;
}

module.exports = { generateDockerCompose }; 