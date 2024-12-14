const fs = require('fs');
const path = require('path');

async function updateEnvFile(envVars, featurePreferences) {
    try {
        const envPath = path.join(process.cwd(), '.env');
        let envContent = await fs.promises.readFile(envPath, 'utf8');

        // If user wants MySQL container
        if (featurePreferences.databaseSetup === 'mysql') {
            envContent = envContent.replace(/DB_HOST=.*/g, 'DB_HOST=db');
            envContent = envContent.replace(/DB_PORT=.*/g, 'DB_PORT=3306');
            envContent = envContent.replace(/DB_DATABASE=.*/g, `DB_DATABASE=${featurePreferences.dbName}`);
            envContent = envContent.replace(/DB_USERNAME=.*/g, `DB_USERNAME=${featurePreferences.dbUsername}`);
            envContent = envContent.replace(/DB_PASSWORD=.*/g, `DB_PASSWORD=${featurePreferences.dbRootPassword}`);
        }

        // If user wants Redis container
        if (featurePreferences.redisSetup === 'redis') {
            envContent = envContent.replace(/REDIS_HOST=.*/g, 'REDIS_HOST=redis');
            envContent = envContent.replace(/REDIS_PASSWORD=.*/g, 'REDIS_PASSWORD=null');
            envContent = envContent.replace(/REDIS_PORT=.*/g, 'REDIS_PORT=6379');
            envContent = envContent.replace(/CACHE_DRIVER=.*/g, 'CACHE_DRIVER=redis');
        }

        // Update backup configuration
        if (featurePreferences.backupStorage === 'local') {
            addOrUpdateEnv(envContent, {
                'BACKUP_DRIVER': 'local',
                'BACKUP_PATH': featurePreferences.localBackupPath
            });
        } 
        else if (featurePreferences.backupStorage === 's3') {
            const prefix = featurePreferences.s3Provider === 'custom' ? 
                featurePreferences.customS3Name : 
                featurePreferences.s3Provider.toUpperCase();

            const s3Config = {
                'BACKUP_DRIVER': 's3',
                [`${prefix}_ACCESS_KEY_ID`]: featurePreferences.s3AccessKey,
                [`${prefix}_SECRET_ACCESS_KEY`]: featurePreferences.s3SecretKey,
                [`${prefix}_DEFAULT_REGION`]: featurePreferences.s3Region || 'us-east-1',
                [`${prefix}_BUCKET`]: featurePreferences.s3Bucket
            };

            if (featurePreferences.s3Provider !== 'aws') {
                s3Config[`${prefix}_ENDPOINT`] = featurePreferences.s3Endpoint;
                s3Config[`${prefix}_URL`] = featurePreferences.s3Endpoint;
                s3Config[`${prefix}_USE_PATH_STYLE_ENDPOINT`] = 'true';
            }

            addOrUpdateEnv(envContent, s3Config);
        } 
        else if (featurePreferences.backupStorage === 'ftp') {
            addOrUpdateEnv(envContent, {
                'BACKUP_DRIVER': 'ftp',
                'FTP_TYPE': featurePreferences.ftpType,
                'FTP_HOST': featurePreferences.ftpHost,
                'FTP_PORT': featurePreferences.ftpPort,
                'FTP_USERNAME': featurePreferences.ftpUsername,
                'FTP_PASSWORD': featurePreferences.ftpPassword,
                'FTP_PATH': featurePreferences.ftpPath,
                'FTP_SSL': featurePreferences.ftpType === 'ftps' ? 'true' : 'false',
                'FTP_PASSIVE': featurePreferences.ftpType === 'ftp' ? 'true' : 'false'
            });
        }

        await fs.promises.writeFile(envPath, envContent);
    } catch (error) {
        throw new Error(`Failed to update .env file: ${error.message}`);
    }
}

function addOrUpdateEnv(content, variables) {
    let lines = content.split('\n');
    const updated = new Set();

    // Update existing variables
    lines = lines.map(line => {
        const match = line.match(/^([^=]+)=/);
        if (match) {
            const key = match[1];
            if (variables[key] !== undefined) {
                updated.add(key);
                return `${key}=${variables[key]}`;
            }
        }
        return line;
    });

    // Add new variables
    Object.entries(variables).forEach(([key, value]) => {
        if (!updated.has(key)) {
            lines.push(`${key}=${value}`);
        }
    });

    return lines.join('\n');
}

module.exports = { updateEnvFile }; 