import { readFile } from 'fs-extra';
import { parse } from 'dotenv';
import { EnvVars } from '../types';
import { AppError, errorCodes } from './errorHandler';

export async function parseEnvFile(envPath: string): Promise<EnvVars> {
    try {
        const envFile = await readFile(envPath, 'utf8');
        const parsed = parse(envFile);
        const envLines = envFile.split('\n');
        const orderedEnv: string[] = [];
        
        let lastGroupWasEmpty = false;

        for (const line of envLines) {
            if (line.startsWith('#') || line.trim() === '') {
                if (!lastGroupWasEmpty && line.trim() === '') {
                    orderedEnv.push('');
                    lastGroupWasEmpty = true;
                }
                continue;
            }
            
            lastGroupWasEmpty = false;
            const match = line.match(/^([^=]+)=/);
            if (match) {
                const key = match[1];
                orderedEnv.push(`${key}=${parsed[key] || ''}`);
            }
        }

        return { parsed, orderedEnv };
    } catch (error) {
        throw new AppError(
            'Failed to parse .env file',
            errorCodes.INVALID_ENV,
            {
                code: errorCodes.INVALID_ENV,
                context: {
                    error: error instanceof Error ? error.message : 'Unknown error'
                }
            }
        );
    }
} 