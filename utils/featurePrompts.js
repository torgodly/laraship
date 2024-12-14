const inquirer = require('inquirer');
const chalk = require('chalk');
const simpleGit = require('simple-git');
const envVars = require('dotenv').config();
const { generateStrongPassword } = require('./passwordGenerator');

async function getBranchList() {
    try {
        const git = simpleGit();
        // Get remote branches
        const { all: branches } = await git.branch(['-r']);
        
        // Clean up branch names (remove origin/ prefix)
        const cleanBranches = branches
            .map(branch => branch.trim())
            .filter(branch => branch.startsWith('origin/'))
            .map(branch => ({
                name: branch.replace('origin/', ''),
                value: branch.replace('origin/', '')
            }));

        return cleanBranches.length ? cleanBranches : [
            { name: 'main', value: 'main' },
            { name: 'master', value: 'master' },
            { name: 'develop', value: 'develop' }
        ];
    } catch (error) {
        // Fallback to default branches if git command fails
        return [
            { name: 'main', value: 'main' },
            { name: 'master', value: 'master' },
            { name: 'develop', value: 'develop' }
        ];
    }
}

async function getFeaturePreferences(envVars) {
    const isProduction = envVars.parsed.APP_ENV === 'production';

    // First get migration preferences
    const migrationAnswers = await inquirer.prompt([
        {
            type: 'confirm',
            name: 'runMigrations',
            message: '📦 Run database migrations after container build?',
            default: !isProduction
        },
        {
            type: 'confirm',
            name: 'runSeeder',
            message: '🌱 Run database seeders after migrations?',
            default: false,
        }
    ]);

    console.log(chalk.blue('\n🔧 Additional Features Configuration'));

    // Get branches before other prompts
    const branches = await getBranchList();

    const answers = await inquirer.prompt([
        {
            type: 'confirm',
            name: 'enableAutoPull',
            message: '📥 Enable automatic deployment on GitHub push?',
            default: !isProduction
        },
        {
            type: 'list',
            name: 'branch',
            message: 'Which branch to watch?',
            choices: branches,
            when: (answers) => answers.enableAutoPull
        },
        {
            type: 'list',
            name: 'dbActions',
            message: 'What database actions to perform after deployment?',
            choices: [
                { name: 'Run migrations after deploy', value: 'migrate' },
                { name: 'Run seeders after deploy', value: 'seed' },
                { name: 'Run both migrations and seeders after deploy', value: 'both' },
                { name: 'No database actions after deploy', value: 'none' }
            ],
            when: (answers) => answers.enableAutoPull
        },
        {
            type: 'confirm',
            name: 'enableBackups',
            message: '💾 Enable automated backups?',
            default: isProduction
        },
        {
            type: 'list',
            name: 'backupFrequency',
            message: 'Backup frequency:',
            choices: [
                { name: 'Every hour', value: 'hourly' },
                { name: 'Every 6 hours', value: '6hours' },
                { name: 'Every 12 hours', value: '12hours' },
                { name: 'Daily', value: 'daily' },
                { name: 'Every 3 days', value: '3days' },
                { name: 'Weekly', value: 'weekly' },
                { name: 'Bi-weekly', value: 'biweekly' },
                { name: 'Monthly', value: 'monthly' },
                { name: 'Custom (cron expression)', value: 'custom' }
            ],
            when: (answers) => answers.enableBackups
        },
        {
            type: 'input',
            name: 'customBackupSchedule',
            message: 'Enter cron expression (e.g., "0 3 * * *" for daily at 3 AM):',
            when: (answers) => answers.enableBackups && answers.backupFrequency === 'custom',
            validate: (input) => {
                const cronRegex = /^(\*|([0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])|\*\/([0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])) (\*|([0-9]|1[0-9]|2[0-3])|\*\/([0-9]|1[0-9]|2[0-3])) (\*|([1-9]|1[0-9]|2[0-9]|3[0-1])|\*\/([1-9]|1[0-9]|2[0-9]|3[0-1])) (\*|([1-9]|1[0-2])|\*\/([1-9]|1[0-2])) (\*|([0-6])|\*\/([0-6]))$/;
                return cronRegex.test(input) ? true : 'Please enter a valid cron expression';
            }
        },
        {
            type: 'checkbox',
            name: 'backupItems',
            message: 'What to backup:',
            choices: [
                { name: 'Database (MySQL dump)', value: 'db', checked: true },
                { name: 'Storage files (uploads, media)', value: 'files' }
            ],
            when: (answers) => answers.enableBackups
        },
        {
            type: 'list',
            name: 'backupStorage',
            message: 'Where to store backups?',
            choices: [
                { name: 'Local Storage (in project directory)', value: 'local' },
                { name: 'S3 Compatible Storage', value: 's3' },
                { name: 'FTP Server', value: 'ftp' }
            ],
            when: (answers) => answers.enableBackups
        },
        {
            type: 'input',
            name: 'localBackupPath',
            message: 'Local backup directory path:',
            default: './storage/app/backups',
            when: (answers) => answers.backupStorage === 'local',
            validate: (input) => {
                if (!input.startsWith('./')) {
                    return 'Path must be relative to project root (start with ./)';
                }
                return true;
            }
        },
        {
            type: 'list',
            name: 's3Provider',
            message: 'Select S3 Provider:',
            choices: [
                { name: 'AWS S3', value: 'aws' },
                { name: 'DigitalOcean Spaces', value: 'digitalocean' },
                { name: 'MinIO', value: 'minio' },
                { name: 'Custom S3 Service', value: 'custom' }
            ],
            when: (answers) => answers.backupStorage === 's3'
        },
        {
            type: 'input',
            name: 'customS3Name',
            message: 'Custom S3 service name (for env variables):',
            when: (answers) => answers.backupStorage === 's3' && answers.s3Provider === 'custom',
            validate: (input) => /^[A-Z0-9_]+$/.test(input) ? true : 'Use uppercase letters, numbers and underscores only'
        },
        {
            type: 'input',
            name: 's3Endpoint',
            message: (answers) => `${answers.s3Provider === 'custom' ? 'Custom' : answers.s3Provider} endpoint URL:`,
            when: (answers) => answers.backupStorage === 's3' && answers.s3Provider !== 'aws',
            validate: (input) => input.startsWith('http') ? true : 'URL must start with http:// or https://'
        },
        {
            type: 'input',
            name: 's3AccessKey',
            message: (answers) => `${answers.s3Provider} Access Key:`,
            when: (answers) => answers.backupStorage === 's3'
        },
        {
            type: 'input',
            name: 's3SecretKey',
            message: (answers) => `${answers.s3Provider} Secret Key:`,
            when: (answers) => answers.backupStorage === 's3'
        },
        {
            type: 'input',
            name: 's3Bucket',
            message: 'Bucket name:',
            when: (answers) => answers.backupStorage === 's3',
            default: (answers) => `${envVars.parsed.APP_NAME?.toLowerCase().replace(/[^a-z0-9-]/g, '-')}-backups`
        },
        {
            type: 'input',
            name: 's3Region',
            message: 'Region:',
            when: (answers) => answers.backupStorage === 's3' && answers.s3Provider === 'aws',
            default: 'us-east-1'
        },
        {
            type: 'list',
            name: 'ftpType',
            message: 'Select FTP type:',
            choices: [
                { name: 'FTP (standard)', value: 'ftp' },
                { name: 'SFTP (SSH File Transfer)', value: 'sftp' },
                { name: 'FTPS (FTP over SSL)', value: 'ftps' }
            ],
            when: (answers) => answers.backupStorage === 'ftp'
        },
        {
            type: 'input',
            name: 'ftpHost',
            message: 'FTP server hostname:',
            when: (answers) => answers.backupStorage === 'ftp'
        },
        {
            type: 'input',
            name: 'ftpPort',
            message: (answers) => `${answers.ftpType.toUpperCase()} port:`,
            default: (answers) => {
                switch(answers.ftpType) {
                    case 'sftp': return '22';
                    case 'ftps': return '990';
                    default: return '21';
                }
            },
            when: (answers) => answers.backupStorage === 'ftp'
        },
        {
            type: 'input',
            name: 'ftpUsername',
            message: 'FTP username:',
            when: (answers) => answers.backupStorage === 'ftp'
        },
        {
            type: 'password',
            name: 'ftpPassword',
            message: 'FTP password:',
            when: (answers) => answers.backupStorage === 'ftp'
        },
        {
            type: 'input',
            name: 'ftpPath',
            message: 'Backup directory on FTP server:',
            default: (answers) => `/backups/${envVars.parsed.APP_NAME?.toLowerCase().replace(/[^a-z0-9-]/g, '-')}`,
            when: (answers) => answers.backupStorage === 'ftp'
        },
        {
            type: 'confirm',
            name: 'enableSlack',
            message: '📢 Enable Slack notifications?',
            default: isProduction
        },
        {
            type: 'input',
            name: 'slackWebhook',
            message: 'Slack webhook URL:',
            when: (answers) => answers.enableSlack,
            validate: (input) => input.startsWith('https://hooks.slack.com/') ? true : 'Please enter a valid Slack webhook URL'
        },
        {
            type: 'confirm',
            name: 'enableSSL',
            message: '🔒 Configure SSL with Let\'s Encrypt?',
            default: isProduction
        },
        {
            type: 'input',
            name: 'domain',
            message: 'Domain name:',
            when: (answers) => answers.enableSSL,
            validate: (input) => /^[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]\.[a-zA-Z]{2,}$/.test(input) ? true : 'Please enter a valid domain'
        },
        {
            type: 'input',
            name: 'sslEmail',
            message: 'Email for SSL certificates:',
            when: (answers) => answers.enableSSL,
            validate: (input) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(input) ? true : 'Please enter a valid email'
        },
        {
            type: 'checkbox',
            name: 'additionalServices',
            message: '🔧 Select additional services:',
            choices: [
                { name: 'Redis Cache', value: 'redis' },
                { name: 'Queue Worker', value: 'queue' },
                { name: 'Scheduler', value: 'scheduler' },
                { name: 'PHPMyAdmin', value: 'phpmyadmin' },
                { name: 'Mailhog', value: 'mailhog' }
            ]
        },
        {
            type: 'list',
            name: 'repoAuthMethod',
            message: 'How should the server authenticate with GitHub?',
            choices: [
                { 
                    name: 'Deploy Key (Recommended)', 
                    value: 'deploy_key' 
                },
                { 
                    name: 'Personal Access Token', 
                    value: 'pat' 
                },
                { 
                    name: 'SSH Key Already Set Up', 
                    value: 'existing' 
                }
            ],
            when: (answers) => answers.enableAutoPull
        },
        {
            type: 'list',
            name: 'databaseSetup',
            message: '🗄️  Database configuration:',
            choices: [
                { name: 'Use existing database server (from .env)', value: 'existing' },
                { name: 'Create new MySQL container', value: 'mysql' }
            ],
            default: 'mysql' // Most users want a contained environment
        },
        {
            type: 'input',
            name: 'dbName',
            message: 'Database name:',
            when: (answers) => answers.databaseSetup === 'mysql',
            default: (answers) => envVars.parsed.APP_NAME?.toLowerCase().replace(/\s+/g, '_') || 'laravel'
        },
        {
            type: 'input',
            name: 'dbUsername',
            message: 'Database username:',
            when: (answers) => answers.databaseSetup === 'mysql',
            default: (answers) => `${envVars.parsed.APP_NAME?.toLowerCase().replace(/\s+/g, '_')}_user` || 'laravel_user'
        },
        {
            type: 'input',
            name: 'dbRootPassword',
            message: 'Set MySQL root password:',
            when: (answers) => answers.databaseSetup === 'mysql',
            default: () => generateStrongPassword()
        },
        {
            type: 'list',
            name: 'redisSetup',
            message: '📦 Redis configuration:',
            choices: [
                { name: 'Create new Redis container', value: 'redis' },
                { name: 'Use existing Redis server (from .env)', value: 'existing' },
                { name: 'Don\'t use Redis', value: 'none' }
            ],
            default: 'none' // Default to not using Redis
        }
    ]);

    return {
        ...migrationAnswers,
        ...answers
    };
}

module.exports = { getFeaturePreferences }; 