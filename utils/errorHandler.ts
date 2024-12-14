export interface ErrorDetails {
    code: string;
    context?: Record<string, unknown>;
    stack?: string;
}

export class AppError extends Error {
    code: string;
    details: ErrorDetails;

    constructor(message: string, code: string = 'GENERAL_ERROR', details?: Partial<ErrorDetails>) {
        super(message);
        this.code = code;
        this.details = {
            code: code,
            ...details
        };
        this.name = 'AppError';
    }
}

export const errorCodes = {
    INVALID_ENV: 'Environment configuration error',
    FILE_SYSTEM: 'File system error',
    SECURITY: 'Security violation',
    VALIDATION: 'Validation error',
    DOCKER: 'Docker configuration error'
} as const;

export function handleError(error: Error | AppError, spinner?: any): never {
    if (spinner) spinner.fail();

    if (error instanceof AppError) {
        console.error(`Error (${error.code}): ${error.message}`);
        if (Object.keys(error.details).length > 0) {
            console.error('Details:', error.details);
        }
    } else {
        console.error('Unexpected error:', error.message);
    }

    process.exit(1);
} 