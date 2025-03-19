<?php

namespace App\Actions\PhpActions;

use App\Services\ShellScriptService;

class UninstallPhpAction
{
    protected ShellScriptService $shellService;

    public function __construct()
    {
        $this->shellService = new ShellScriptService();
    }

    public function execute(string $version): string
    {
        // Build the command to uninstall the PHP version
        $shell_script = view('Scripts.phpScripts.uninstall-php-version', ['php_version' => $version])->render();

        try {
            $output = $this->shellService->runScript($shell_script);
            return $version . 'uninstalled successfully.';
        } catch (\Exception $e) {
            return 'Failed to uninstall ' . $version;
        }

    }
}
