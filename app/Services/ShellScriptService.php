<?php

namespace App\Services;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ShellScriptService
{
    /**
     * Run a bash script with named arguments as user 'laraship'.
     *
     * @param string $scriptPath Path to the bash script.
     * @param array $arguments Arguments to pass to the script as key-value pairs.
     * @return string Output of the script.
     *
     * @throws ProcessFailedException If the script fails.
     */
    public function runScript(string $scriptPath, array $arguments = []): string
    {
        // Ensure the script is executable
        if (!is_executable($scriptPath)) {
            throw new \RuntimeException("Script {$scriptPath} is not executable.");
        }

        // Prepare the arguments in the correct format
        $formattedArguments = [];
        foreach ($arguments as $key => $value) {
            $formattedArguments[] = "--{$key}={$value}";
        }

        // Prepend sudo -u laraship
        $command = array_merge(['sudo', '-u', 'laraship', $scriptPath], $formattedArguments);

        // Prepare the process
        $process = new Process($command);
        $process->run();

        // Check if the process was successful
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }

    /**
     * Run a bash command with named arguments as user 'laraship'.
     *
     * @param string $command Command to run.
     * @param array $arguments Arguments to pass to the command as key-value pairs.
     * @return string Output of the command.
     *
     * @throws ProcessFailedException If the command fails.
     */
    public function runCommand(string $command, array $arguments = []): string
    {
        // Prepare the arguments in the correct format
        $formattedArguments = [];
        foreach ($arguments as $key => $value) {
            $formattedArguments[] = "--{$key}={$value}";
        }

        // Prepend sudo -u laraship
        $commandWithUser = "sudo -u laraship " . $command . ' ' . implode(' ', $formattedArguments);

        // Prepare the process
        $process = Process::fromShellCommandline($commandWithUser);
        $process->run();

        // Check if the process was successful
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }
}
