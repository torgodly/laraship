import { readFileSync } from 'fs-extra';
import { join } from 'path';
import { ComposerInfo } from '../types';

export function generateDockerfileContent(composerInfo: ComposerInfo): string {
    const templatePath = join(__dirname, 'Dockerfile.template');
    let template = readFileSync(templatePath, 'utf8');
    
    // Replace template variables
    return template
        .replace(/{{PHP_VERSION}}/g, composerInfo.phpVersion || '8.3')
        .replace(/{{SYSTEM_PACKAGES}}/g, composerInfo.systemPackages.join(' \\\n    '))
        .replace(/{{PHP_EXTENSIONS}}/g, composerInfo.extensions.join(' '));
} 