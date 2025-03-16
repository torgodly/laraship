<?php

namespace App\Actions\PhpActions;

class ListPhpVersionsAction
{
    public function execute(): array
    {
        $output = trim(shell_exec('ls /etc/php'));
        $versions = explode("\n", $output);
        $versions = array_map(fn($version) => 'php' . $version, $versions);
        return array_filter($versions);
    }
}
