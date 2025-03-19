<?php

namespace App\Actions\PhpActions;

use App\Services\ShellScriptService;

class InstallPhpAction
{
    protected ShellScriptService $shellService;

    public function __construct()
    {
        $this->shellService = new ShellScriptService();
    }

    public function execute(string $version): string
    {
        $shell_script = view('Scripts.phpScripts.install-php-version', ['php_version' => $version])->render();

        try {
            $output = $this->shellService->runScript($shell_script);
            return $version.'installed successfully.';
        } catch (\Exception $e) {
           return 'Failed to install '.$version;
        }

    }
}
