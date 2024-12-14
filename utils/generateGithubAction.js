function generateGithubAction(featurePreferences) {
    return `name: Deploy Application

on:
  push:
    branches: [ ${featurePreferences.branch || 'main'} ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - name: Deploy to production server
        uses: appleboy/ssh-action@master
        with:
          host: \${{ secrets.SERVER_HOST }}
          username: \${{ secrets.SERVER_USERNAME }}
          key: \${{ secrets.SSH_PRIVATE_KEY }}
          script: |
            cd /var/www/html
            git pull origin ${featurePreferences.branch || 'main'}
            docker-compose down
            docker-compose up -d --build
            ${generateDbCommands(featurePreferences)}
            ${generateBackupCommands(featurePreferences)}
            ${generateSlackNotification(featurePreferences)}`;
}

function generateDbCommands(featurePreferences) {
    const commands = [];
    if (featurePreferences.dbActions === 'migrate' || featurePreferences.dbActions === 'both') {
        commands.push('# Run migrations after deployment');
        commands.push('docker-compose exec -T app php artisan migrate --force');
    }
    if (featurePreferences.dbActions === 'seed' || featurePreferences.dbActions === 'both') {
        commands.push('# Run seeders after deployment');
        commands.push('docker-compose exec -T app php artisan db:seed --force');
    }
    return commands.join('\n            ');
}

function generateBackupCommands(featurePreferences) {
    if (!featurePreferences.enableBackups) return '';
    return `
            # Backup database
            docker-compose exec -T app php artisan backup:run`;
}

function generateSlackNotification(featurePreferences) {
    if (!featurePreferences.enableSlack) return '';
    return `
            # Send Slack notification
            curl -X POST -H 'Content-type: application/json' --data '{"text":"🚀 Deployment completed successfully!"}' ${featurePreferences.slackWebhook}`;
}

module.exports = { generateGithubAction }; 