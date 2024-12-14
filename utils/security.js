const crypto = require('crypto');

// Sanitize input to prevent command injection
function sanitizeInput(input) {
    if (typeof input !== 'string') return '';
    return input.replace(/[;&|`$]/g, '');
}

// Mask sensitive data in logs and outputs
function maskSensitiveData(data, keysToMask = ['password', 'secret', 'key', 'token']) {
    if (typeof data !== 'object') return data;
    
    const masked = { ...data };
    for (const key of Object.keys(masked)) {
        if (keysToMask.some(k => key.toLowerCase().includes(k))) {
            masked[key] = '********';
        }
    }
    return masked;
}

// Validate environment variables
function validateEnvVars(envVars) {
    const required = ['APP_NAME'];
    
    // Check database configuration
    if (envVars.DB_CONNECTION === 'sqlite') {
        // SQLite only needs DB_CONNECTION
        return;
    } else {
        // Other databases need full configuration
        const dbRequired = ['DB_HOST', 'DB_DATABASE'];
        const missing = dbRequired.filter(key => !envVars[key]);
        
        if (missing.length > 0) {
            throw new Error(`Missing required environment variables: ${missing.join(', ')}`);
        }
    }
}

// Generate secure random string
function generateSecureString(length = 32) {
    return crypto.randomBytes(length).toString('hex');
}

// Validate file paths to prevent directory traversal
function validatePath(path) {
    if (!path) return false;
    // Prevent directory traversal
    const normalizedPath = path.replace(/\\/g, '/');
    return !normalizedPath.includes('../') && !normalizedPath.includes('..\\');
}

module.exports = {
    sanitizeInput,
    maskSensitiveData,
    validateEnvVars,
    generateSecureString,
    validatePath
}; 