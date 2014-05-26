<?php
require_once 'config.php';
require_once 'Logger.php';

class DB {
    private $config;
    private $logger;
    const UNKNOWN_FILE = 0;
    const MODIFIED_FILE = 1;
    const UNCHANGED_FILE = 2;

    function __construct() {
        $this->config = Config::GetInstance();
        $this->logger = Logger::GetInstance($this->config->logfile);

        $this->Connect();
        $this->CreateTables();
    }

    private function Connect() {
        mysql_connect($this->config->db_host, $this->config->db_user, $this->config->db_pass) or $this->logger->Add('Unable to connect DB');
        mysql_select_db($this->config->db_name) or $this->logger->Add('Unable to select DB '.$this->config->db_name);
        mysql_set_charset('UTF-8');
    }

    private function CreateTables() {
        $query = "CREATE TABLE IF NOT EXISTS `sfc_files` (
            `id` INT AUTO_INCREMENT,
            `path` VARCHAR(255) NOT NULL,
            `size` INT NOT NULL,
            `modify_date` INT,
            PRIMARY KEY (`id`),
            INDEX (`path`),
            UNIQUE INDEX (`path`, `size`, `modify_date`)
        )";

        $this->Query($query);
    }

    private function Query($query) {
        $res = mysql_query($query) or $this->logger->Add(mysql_error());
        return $res;
    }

    function AddFile($file) {
        $query = "INSERT INTO sfc_files (`path`, `size`, `modify_date`)
                        VALUES('{$file['path']}', '{$file['size']}', '{$file['modify_date']}')
        ";

        $this->Query($query);
    }

    private function UpdateFileSize($id, $size) {
        $query = "SELECT * FROM sfc_files WHERE id = '$id'";
        $res = $this->Query($query);

        if (mysql_num_rows($res) > 0) {
            $query = "UPDATE sfc_files
                        SET size = '$size'
                        WHERE id = '$id'";
            $this->Query($query);
        }
    }

    private function UpdateFileDate($id, $date) {
        $query = "SELECT * FROM sfc_files WHERE id = '$id'";
        $res = $this->Query($query);

        if (mysql_num_rows($res) > 0) {
            $query = "UPDATE sfc_files
                        SET modify_date = '$date'
                        WHERE id = '$id'";
            $this->Query($query);
        }
    }

    function CheckFileModification($file) {
        $query = "SELECT * FROM sfc_files WHERE path = '{$file['path']}'";
        $res = $this->Query($query);

        if (mysql_num_rows($res) == 0)
            return self::UNKNOWN_FILE;

        $row = mysql_fetch_assoc($res);

        if ($row['modify_date'] != $file['modify_date']) {
            $this->UpdateFileDate($row['id'], $file['modify_date']);
            return self::MODIFIED_FILE;
        }

        if ($row['size'] != $file['size']) {
            $this->UpdateFileSize($row['id'], $file['size']);
            return self::MODIFIED_FILE;
        }

        return self::UNCHANGED_FILE;
    }
} 