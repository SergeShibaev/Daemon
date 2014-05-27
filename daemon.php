<?php
require_once 'config.php';
require_once 'DB.php';
require_once 'Logger.php';

class Daemon {
    private $db;
    private $logger;
    private $config;
    private $files = 0;
    private $dirs = 0;

    function __construct() {
        $this->config = Config::GetInstance();
        $this->db = new DB();
        $this->logger = new Logger($this->config->logdir . '/sfc.log');

        $this->EnumDirectory(getcwd() . Config::GetInstance()->root);
    }

    function __destruct() {
        $this->logger->Add("Checked: {$this->dirs} Directories, {$this->files} Files");
    }

    private function CutFileName($file) {
        $pos = strrpos($file, $this->config->root);
        return substr($file, $pos + strlen($this->config->root) + 1);
    }

    private function EnumDirectory($dir) {
        $list = scandir($dir . '/');
        for ($i = 0; $i < count($list); ++$i) {
            $item = $list[$i];

            if ($item == '.' || $item == '..')
                continue;

            $item = $dir . '/' . $item;
            if (in_array($item, $this->config->exclude_dirs)) {
                continue;
            }
            if (is_dir($item)) {
                $this->EnumDirectory($item);
                $this->dirs++;
            }
            else if (is_file($item)) {
                $this->files++;
                $short_filename = $this->CutFileName($item);
                $file['path'] = $short_filename;
                $file['size'] = filesize($item);
                $file['modify_date'] = filemtime($item);

                $res = $this->db->CheckFileModification($file);
                if ($res == DB::UNKNOWN_FILE) {
                    $this->db->AddFile($file);
                    $this->logger->Add("Unknown file founded: $short_filename");
                }
                else if ($res == DB::MODIFIED_FILE) {
                    $this->db->AddFile($file);
                    $this->logger->Add("File $item was modified at ". date('Y-m-d H:i:s', $file['modify_date']));
                }
            }
        }
    }
}

new Daemon();