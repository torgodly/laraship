const inquirer = require('inquirer');
const chalk = require('chalk');

async function getMigrationPreferences(envVars) {
    // Migration preferences are now handled in featurePrompts.js
    return {};
}

module.exports = { getMigrationPreferences }; 