#!/usr/bin/env node

const fs = require('fs');
const path = require('path');
const chalk = require('chalk');
const ora = require('ora');
const boxen = require('boxen');
const { getMigrationPreferences } = require('./utils/migrationPrompts');
const { parseEnvFile } = require('./utils/envParser');
const { generateDockerfile } = require('./templates/dockerfile.template.js');
const { generateDockerCompose } = require('./templates/docker-compose.template');
const { getFeaturePreferences } = require('./utils/featurePrompts');
const { generateGithubAction } = require('./utils/generateGithubAction');
const { setupBackupPackage } = require('./utils/backupSetup');
const { updateEnvFile } = require('./utils/envUpdater');
const { sanitizeInput, validateEnvVars, validatePath } = require('./utils/security');
const { handleError, AppError, errorCodes } = require('./utils/errorHandler');

console.log(boxen(
    chalk.blue.bold('LaraShip 🚢 - Docker Generator'),
    { 
        padding: 1, 
        margin: 1, 
        borderStyle: 'round',
        borderColor: 'blue'
    }
));

async function main() {
    let spinner = ora();
    try {
        // Start spinner with initial text
        spinner = ora('Checking project structure').start();
        
        // Validate working directory
        const currentDir = process.cwd();
        if (!validatePath(currentDir)) {
            spinner.fail('Invalid working directory path');
            throw new AppError('Invalid working directory path', errorCodes.SECURITY);
        }
        
        // Check if we're in a Laravel project
        if (!fs.existsSync(path.join(currentDir, 'artisan'))) {
            spinner.fail('Not a Laravel project');
            throw new AppError(
                'Must be run from Laravel project root',
                errorCodes.VALIDATION
            );
        }
        spinner.succeed('Project structure validated');
        
        // Parse and validate environment
        spinner.start('Reading environment configuration');
        const envPath = path.join(currentDir, '.env');
        if (!fs.existsSync(envPath)) {
            spinner.fail('Environment file not found');
            throw new Error('.env file not found. Please ensure you have a .env file in your project root');
        }

        const envVars = await parseEnvFile(envPath);
        validateEnvVars(envVars.parsed);
        spinner.succeed('Environment configuration loaded');
        
        // Get user preferences (with sanitized input)
        const preferences = await getFeaturePreferences(envVars);
        Object.keys(preferences).forEach(key => {
            if (typeof preferences[key] === 'string') {
                preferences[key] = sanitizeInput(preferences[key]);
            }
        });
        
        // Get migration preferences
        const migrationPreferences = await getMigrationPreferences(envVars.parsed);

        // Update .env file if needed
        spinner.start('Updating environment configuration');
        await updateEnvFile(envVars, preferences);
        spinner.succeed();

        // If backups are enabled, check and setup backup package
        if (preferences.enableBackups) {
            preferences.backupSetup = await setupBackupPackage(spinner);
        }

        spinner.start('Generating Dockerfile');
        // Generate Dockerfile
        const dockerfile = await generateDockerfile(envVars);
        fs.writeFileSync(path.join(process.cwd(), 'Dockerfile'), dockerfile);
        spinner.succeed();

        spinner.start('Generating docker-compose.yml');
        // Generate docker-compose.yml
        const dockerCompose = generateDockerCompose(envVars, migrationPreferences, preferences);
        fs.writeFileSync(path.join(process.cwd(), 'docker-compose.yml'), dockerCompose);
        spinner.succeed();

        // Generate GitHub Actions workflow if auto-pull is enabled
        if (preferences.enableAutoPull) {
            spinner.start('Generating GitHub Actions workflow');
            const githubAction = generateGithubAction(preferences);
            const workflowsDir = path.join(process.cwd(), '.github', 'workflows');
            fs.mkdirSync(workflowsDir, { recursive: true });
            fs.writeFileSync(path.join(workflowsDir, 'deploy.yml'), githubAction);
            spinner.succeed();
        }

        console.log(boxen(
            chalk.green.bold('Success! 🎉\n\n') +
            chalk.white('Generated files:\n') +
            chalk.cyan('- Dockerfile\n') +
            chalk.cyan('- docker-compose.yml\n\n') +
            chalk.yellow.bold('Next steps:\n\n') +
            chalk.white('Review the generated files and customize them as needed.'),
            { 
                padding: 1,
                margin: 1,
                borderStyle: 'round',
                borderColor: 'green'
            }
        ));

    } catch (error) {
        handleError(error, spinner);
    }
}

main(); 