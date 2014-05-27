<?php

class Logger {
    private $log;

    public function __construct($filename) {
        $this->log = fopen($filename, 'a');
    }

    public function Add($message) {
        fputs($this->log, '[' . date('Y-m-d H:i:s') . "] $message\r");
    }

    function __destruct() {
        fclose($this->log);
    }
};
