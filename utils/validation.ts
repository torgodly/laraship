import { z } from 'zod';
import type { FeaturePreferences } from '../types';

const backupStorageSchema = z.enum(['local', 's3', 'ftp']);
const dbActionsSchema = z.enum(['migrate', 'seed', 'both', 'none']);
const databaseSetupSchema = z.enum(['existing', 'mysql']);
const redisSetupSchema = z.enum(['existing', 'redis', 'none']);

export const featurePreferencesSchema = z.object({
    enableAutoPull: z.boolean().optional(),
    branch: z.string().min(1).optional(),
    dbActions: dbActionsSchema.optional(),
    enableBackups: z.boolean().optional(),
    backupStorage: backupStorageSchema.optional(),
    backupItems: z.array(z.enum(['db', 'files'])).optional(),
    databaseSetup: databaseSetupSchema.optional(),
    redisSetup: redisSetupSchema.optional(),
    additionalServices: z.array(z.string()).optional()
});

export function validateFeaturePreferences(data: unknown): FeaturePreferences {
    try {
        return featurePreferencesSchema.parse(data);
    } catch (error) {
        if (error instanceof z.ZodError) {
            throw new Error(`Invalid configuration: ${error.errors.map(e => e.message).join(', ')}`);
        }
        throw error;
    }
} 