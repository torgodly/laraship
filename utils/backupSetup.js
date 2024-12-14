const fs = require('fs');
const path = require('path');

async function setupBackupPackage(spinner) {
    try {
        // Check if composer.json exists
        const composerPath = path.join(process.cwd(), 'composer.json');
        const composerContent = JSON.parse(fs.readFileSync(composerPath, 'utf8'));
        
        // Check if backup package is already installed
        const hasBackupPackage = composerContent.require && 
            composerContent.require['spatie/laravel-backup'];
            
        if (!hasBackupPackage) {
            spinner.info('Installing Laravel Backup Package...');
            
            // Check if spatie/laravel-backup is compatible
            const composerJson = require(composerPath);
            const phpVersion = composerJson.require?.php || '^8.0';
            
            // Determine correct package version based on PHP version
            const packageVersion = phpVersion.startsWith('^7') ? '^6.0' : '^8.0';
            
            return {
                installCommand: `composer require "spatie/laravel-backup:${packageVersion}"`,
                needsInstall: true
            };
        }
        
        return { needsInstall: false };
    } catch (error) {
        spinner.fail('Error checking backup package: ' + error.message);
        return { needsInstall: false };
    }
}

module.exports = { setupBackupPackage }; 