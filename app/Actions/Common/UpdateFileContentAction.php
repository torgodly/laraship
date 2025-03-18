<?php

namespace App\Actions\Common;

class UpdateFileContentAction
{
    public function execute(string $config_path, string $content): string
    {
        // Escape the content to avoid issues with special characters
        $escaped_content = escapeshellarg($content);

        // Build the command to write to the file using sudo
        $command = "echo $escaped_content | sudo tee $config_path > /dev/null 2>&1";

        // Execute the command and capture the return code
        exec($command, $output, $return_var);

        // Check if the command executed successfully (return code 0 means success)
        if ($return_var !== 0) {
            return "Failed to update the" . basename($config_path) . "file.";
        }

        return basename($config_path) . " file updated successfully.";
    }
}
