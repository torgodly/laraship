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
    public function runScript(string $script): string
    {
        //create a bash script file called provision.sh inside the /home/laraship/.laraship directory and put the script content in it
        $scriptPath = '/home/laraship/.laraship/provision.sh';
        // also add #!/bin/bash at the top of the script
        file_put_contents($scriptPath, "#!/bin/bash\n" . $script);
        //make the script executable
        chmod($scriptPath, 0755);
        //run the script as laraship user using sudo
        $command = "sudo bash {$scriptPath}";

        // Prepare the process
        $process = Process::fromShellCommandline($command);
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
    public function runCommand(string $command): string
    {
        // Prepare the arguments in the correct format

        // Prepend sudo -u laraship
        $commandWithUser = "sudo -u laraship " . $command;

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
