<?php

namespace App\Actions\PhpActions;

class UpdatePhpIniFileAction
{
    public function execute(string $config_path, string $content): string
    {
        dd($config_path, $content);
        $command = "echo '$content' | sudo tee -a $config_path > /dev/null";
        return shell_exec($command);
    }
}
