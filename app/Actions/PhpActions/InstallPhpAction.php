<?php

namespace App\Actions\PhpActions;

use App\Services\ShellScriptService;
use Illuminate\Support\Facades\Cache;

class InstallPhpAction
{
    protected ShellScriptService $shellService;

    public function __construct()
    {
        $this->shellService = new ShellScriptService();
    }

    public function execute(string $version): string
    {
        $shell_script = view('Scripts.phpScripts.install-php-version', ['php_version' => $version, 'php_version_number' => substr($version, 3)])->render();
        try {
            $output = $this->shellService->runScript($shell_script);
            //clear cache
            Cache::forget('php_versions_installed');
            return $version.' installed successfully.';
        } catch (\Exception $e) {
           return 'Failed to install '.$version;
        }

    }
}
