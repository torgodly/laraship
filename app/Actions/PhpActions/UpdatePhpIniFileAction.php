<?php

namespace App\Actions\PhpActions;

class UpdatePhpIniFileAction
{
    public function execute(string $config_path, string $content): void
    {
        $command = "echo '$content' | sudo tee -a $config_path > /dev/null";
        shell_exec($command);
    }
}
