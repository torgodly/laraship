const fs = require('fs').promises;
const path = require('path');

async function parseComposerJson() {
    try {
        const composerPath = path.join(process.cwd(), 'composer.json');
        const composerContent = await fs.readFile(composerPath, 'utf8');
        const composer = JSON.parse(composerContent);
        
        // Get PHP version
        const phpVersion = composer.require?.php || "8.3"; // default to 8.3 if not specified
        // Clean version number (e.g., "^8.1" -> "8.1")
        const cleanPhpVersion = phpVersion.replace(/[\^~]/, '').split('.').slice(0, 2).join('.');
        
        // Detect required extensions
        const extensions = new Set();
        
        // Check PHP extensions from require section
        Object.keys(composer.require || {}).forEach(pkg => {
            if (pkg.startsWith('ext-')) {
                extensions.add(pkg.replace('ext-', ''));
            }
        });
        
        // Common extension to package mapping
        const extensionPackages = {
            'intl': 'libicu-dev',
            'exif': 'libexif-dev',
            'gd': 'libpng-dev libjpeg-dev',
            'zip': 'libzip-dev',
            'xml': 'libxml2-dev',
            'curl': 'libcurl4-openssl-dev',
        };
        
        // Get required system packages
        const systemPackages = Array.from(extensions)
            .filter(ext => extensionPackages[ext])
            .map(ext => extensionPackages[ext]);
        
        return {
            phpVersion: cleanPhpVersion,
            extensions: Array.from(extensions),
            systemPackages: Array.from(new Set(systemPackages.join(' ').split(' ')))
        };
    } catch (error) {
        console.error('Error parsing composer.json:', error);
        // Return defaults if composer.json can't be parsed
        return {
            phpVersion: '8.3',
            extensions: ['intl', 'exif'],
            systemPackages: ['libicu-dev', 'libexif-dev']
        };
    }
}

module.exports = { parseComposerJson }; 