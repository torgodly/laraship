<?php

namespace App\Actions\PhpActions;

class ReloadPhpFpmAction
{

    public function execute(string $version): string
    {
        // Build the command to reload PHP-FPM
        $command = "sudo service " .$version."-fpm reload > /dev/null 2>&1";

        // Execute the command and capture the return code
        exec($command, $output, $return_var);

        // Check if the command executed successfully (return code 0 means success)
        if ($return_var !== 0) {
            return "Failed to reload PHP-FPM.";
        }

        return "PHP-FPM reloaded successfully.";
    }
}
