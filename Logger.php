<?php

class Logger {
    private $log;
    private static $logger;

    private function __construct($filename) {
        $this->log = fopen($filename, 'w+');
        return self::$logger;
    }

    public function Add($message) {
        fputs($this->log, '[' . date('Y-m-d H:i:s') . "] $message\r");
    }

    function __destruct() {
        fclose($this->log);
    }

    public static function GetInstance($filename) {
        if (empty(self::$logger)) {
            self::$logger = new Logger($filename);
        }
        return self::$logger;
    }
};
