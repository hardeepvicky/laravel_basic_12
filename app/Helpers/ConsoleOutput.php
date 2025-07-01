<?php

namespace App\Helpers;

use Exception;
use Symfony\Component\Console\Output\ConsoleOutput as OutputConsoleOutput;

class ConsoleOutput extends OutputConsoleOutput
{

    private array $log_messages = [];

    private array $log_types = ["info", "error"];

    public function addLog(string | array $data)
    {
        if (is_string($data)) {
            $this->addLogMessage($data);
        } else {
            $msg_list = [];
            foreach ($data as $k => $v) {
                if (is_scalar($v)) {

                    if (strlen($v) > 20) {
                        $v = substr($v, 0, 17) . "...";
                    }

                    $msg_list[] = $k . ":" . $v;
                } else {
                    $msg_list[] = $k . ":" . gettype($v);
                }
            }

            $this->addLogMessage(implode(", ", $msg_list));
        }
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

    public function writeLogToConsole()
    {
        foreach ($this->log_messages as $arr) {

            if (count($arr) < 2) {
                continue;
            }

            if ($arr[1] == "info") {
                $this->writeln('<info>' . ($arr[0]) . '</info>');
            } else if ($arr[1] == "error") {
                $this->writeln('<error>' . ($arr[0]) . '</error>');
            }
        }
    }
}
