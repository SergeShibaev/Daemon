<?php
class Config {
    public $root = '..';    // path to the root directory from the Daemon location
    public $emailTo = 'serge@sergeshibaev.ru';
    public $db_host = 'localhost';
    public $db_name = 'your_db_name';
    public $db_user = 'your_db_user';
    public $db_pass = 'your_db_pass';
    public $logfile = 'sfc.log';
    public $exclude_dirs = array('../cache', '../daemon', '../plw', '../temp');

    private static $config;

    private function __construct() {
        return self::$config;
    }

    public static function GetInstance() {
        if (empty(self::$config)) {
            self::$config = new Config();
        }
        return self::$config;
    }
};
