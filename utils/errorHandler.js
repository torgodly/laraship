const chalk = require('chalk');

class AppError extends Error {
    constructor(message, code = 'GENERAL_ERROR', details = {}) {
        super(message);
        this.code = code;
        this.details = details;
        this.name = 'AppError';
    }
}

const errorCodes = {
    INVALID_ENV: 'Environment configuration error',
    FILE_SYSTEM: 'File system error',
    SECURITY: 'Security violation',
    VALIDATION: 'Validation error',
    DOCKER: 'Docker configuration error'
};

function handleError(error, spinner = null) {
    if (spinner) spinner.fail();

    if (error instanceof AppError) {
        console.error(chalk.red(`Error (${error.code}): ${error.message}`));
        if (Object.keys(error.details).length > 0) {
            console.error(chalk.yellow('Details:'), error.details);
        }
    } else {
        console.error(chalk.red('Unexpected error:'), error.message);
    }

    // Log to file for debugging (mask sensitive data)
    logError(error);

    process.exit(1);
}

function logError(error) {
    const { maskSensitiveData } = require('./security');
    const fs = require('fs');
    const path = require('path');

    const logEntry = {
        timestamp: new Date().toISOString(),
        error: {
            name: error.name,
            message: error.message,
            code: error.code,
            details: maskSensitiveData(error.details || {})
        },
        stack: error.stack
    };

    const logDir = path.join(process.cwd(), 'logs');
    if (!fs.existsSync(logDir)) {
        fs.mkdirSync(logDir);
    }

    fs.appendFileSync(
        path.join(logDir, 'error.log'),
        JSON.stringify(logEntry, null, 2) + '\n'
    );
}

module.exports = {
    AppError,
    errorCodes,
    handleError
}; 