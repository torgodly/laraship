<?php

namespace App\Actions\PhpActions;

class ListPhpVersionsAction
{
    public function execute(): array
    {
        $versions = [];
        $output = shell_exec('ls /usr/bin/php*');  // List PHP binaries
        $binaries = explode("\n", trim($output));

        foreach ($binaries as $binary) {
            if (preg_match('/php(\d+\.\d+)(?:\.\d+)?$/', basename($binary), $matches)) {
                // Check if binary is valid PHP executable
                $version = $matches[1];
                if (shell_exec("php$version -v")) {
                    $versions[] = "php$version";
                }
            }
        }

        return array_unique($versions);
    }

}
