<?php

namespace App\Actions\PhpActions;

class UpdatePhpIniFileAction
{
    public function execute(string $config_path, string $content): string
    {
        // Escape the content to avoid issues with special characters
        $escaped_content = escapeshellarg($content);

        // Build the command to write to the file using sudo
        $command = "echo $escaped_content | sudo tee $config_path > /dev/null";

        // Execute the command
        $output = shell_exec($command);

        // Check for errors
        if ($output === null) {
            return "Failed to update the php.ini file.";
        }

        return "php.ini file updated successfully.";
    }
}
