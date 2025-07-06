<?php

namespace App\Helpers;

use Exception;
use Symfony\Component\Console\Output\ConsoleOutput as OutputConsoleOutput;

class ConsoleOutput extends OutputConsoleOutput
{

    private array $log_messages = [];

    private array $log_types = ["info", "error"];

    public function addLog(string | array $data, $depth = 0)
    {
        if ($depth > 3)
        {
            return [];
        }

        if (is_string($data)) {
            $this->addLogMessage($data);
        } else if(is_array($data)) {
            $msg_list = [];
            foreach ($data as $k => $v) {
                if (is_scalar($v)) {

                    if (strlen($v) > 20) {
                        $v = substr($v, 0, 17) . "...";
                    }

                    $msg_list[] = $k . ":" . $v;
                } else {
                    $this->addLog($v, $depth + 1);
                }
            }

            if ($msg_list)
            {
                $this->addLogMessage(implode(", ", $msg_list));
            }
        }

        return [];
    }

    public function addLogMessage(string $msg, string $type = "info")
    {
        if (strlen($msg) > 80) {
            $msg = substr($msg, 0, 75) . "...";
        }

        if (!in_array($type, $this->log_types)) {
            throw new Exception("invalid type $type");
        }

        $this->log_messages[] = [$msg, $type];
    }

    public function clearLogs()
    {
        $this->log_messages = [];
    }

    public function writeLogToConsole($message = "")
    {
        if ($message && is_scalar($message))
        {
            $this->writeln('<info>' . $message. '</info>');
        }
        else
        {
            foreach ($this->log_messages as $arr) {

                if (count($arr) < 2) {
                    continue;
                }

                if  (isset($arr[0]))
                {
                    if ($arr[1] == "info") {
                        $this->writeln('<info>' . ($arr[0]) . '</info>');
                    } else if ($arr[1] == "error") {
                        $this->writeln('<error>' . ($arr[0]) . '</error>');
                    }
                }
            }
        }
    }
}
