const dotenv = require('dotenv');
const fs = require('fs').promises;

async function parseEnvFile(envPath) {
    try {
        // Read the original file to maintain order and spacing
        const envFile = await fs.readFile(envPath, 'utf8');
        const envLines = envFile.split('\n');
        
        // Parse for values
        const parsed = dotenv.parse(envFile);
        
        // Create ordered environment variables maintaining original structure
        const orderedEnv = [];
        let lastGroupWasEmpty = false;

        envLines.forEach(line => {
            // Skip comments and empty lines but track empty lines for spacing
            if (line.startsWith('#') || line.trim() === '') {
                if (!lastGroupWasEmpty && line.trim() === '') {
                    orderedEnv.push(''); // Add single empty line between groups
                    lastGroupWasEmpty = true;
                }
                return;
            }
            
            lastGroupWasEmpty = false;
            const match = line.match(/^([^=]+)=/);
            if (match) {
                const key = match[1];
                const value = parsed[key] || '';
                orderedEnv.push(`${key}=${value}`);
            }
        });

        return {
            parsed,
            orderedEnv
        };
    } catch (error) {
        throw new Error(`Failed to parse .env file: ${error.message}`);
    }
}

module.exports = { parseEnvFile }; 