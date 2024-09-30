<?php

namespace ViewStatsWrapper;

class Logger {
    public function debug(string $message): void {
        $this->log('DEBUG', $message);
    }

    public function info(string $message): void {
        $this->log('INFO', $message);
    }

    public function error(string $message): void {
        $this->log('ERROR', $message);
    }

    private function log(string $level, string $message): void {
        $logMessage = date('Y-m-d H:i:s') . " - [$level] - $message\n";
        file_put_contents('debug.log', $logMessage, FILE_APPEND);
    }
}