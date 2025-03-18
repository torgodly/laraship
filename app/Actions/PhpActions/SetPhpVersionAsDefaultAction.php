<?php

namespace App\Actions\PhpActions;

class SetPhpVersionAsDefaultAction
{
    public function execute(string $version): string
    {
        // Build the command to set the PHP version as the default
        $command = "sudo update-alternatives --set php /usr/bin/$version > /dev/null 2>&1";

        // Execute the command and capture the return code
        exec($command, $output, $return_var);

        // Check if the command executed successfully (return code 0 means success)
        if ($return_var !== 0) {
            return "Failed to set $version as the default PHP version.";
        }

        return "$version set as the default PHP version.";
    }
}
