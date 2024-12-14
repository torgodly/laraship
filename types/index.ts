export interface EnvVars {
    parsed: Record<string, string>;
    orderedEnv: string[];
}

export interface FeaturePreferences {
    enableAutoPull?: boolean;
    branch?: string;
    dbActions?: 'migrate' | 'seed' | 'both' | 'none';
    enableBackups?: boolean;
    backupStorage?: 'local' | 's3' | 'ftp';
    backupItems?: ('db' | 'files')[];
    databaseSetup?: 'existing' | 'mysql';
    redisSetup?: 'existing' | 'redis' | 'none';
    additionalServices?: string[];
    enableSSL?: boolean;
    enableSlack?: boolean;
}

export interface ComposerInfo {
    phpVersion: string;
    extensions: string[];
    systemPackages: string[];
}

export interface BackupConfig {
    needsInstall: boolean;
    installCommand?: string;
}

export interface ErrorDetails {
    code: string;
    context?: Record<string, unknown>;
    stack?: string;
} 