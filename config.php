<?php
class Config {
    public $root = '/shs78.bget.ru/public_html';
    public $emailTo = 'serge@sergeshibaev.ru';
    public $notify = 0;
    public $db_host = 'localhost';
    public $db_name = 'your_db_name';
    public $db_user = 'your_db_user';
    public $db_pass = 'your_db_pass';
    public $logdir = '/daemon';
    public $exclude_dirs = array('/cache', '/daemon', '/plw', '/tmp');

    private static $config;

    private function __construct() {
        for ($i = 0; $i < count($this->exclude_dirs); ++$i)
            $this->exclude_dirs[$i] = getcwd() . $this->root . $this->exclude_dirs[$i];
        $this->logdir = getcwd() . $this->root . $this->logdir;
        return self::$config;
    }

    public static function GetInstance() {
        if (empty(self::$config)) {
            self::$config = new Config();
        }
        return self::$config;
    }
};
