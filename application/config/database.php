<?php

defined("DB_APP") OR define("DB_APP", "budget");
defined("DB_HOST") OR define("DB_HOST", "192.168.8.2");
defined("DB_USER") OR define("DB_USER", "adam");
defined("DB_PASS") OR define("DB_PASS", "Ad@mMR1db");
defined("DB_PORT") OR define("DB_PORT", "3306");

// Define other const value here
defined("DB_JAY") OR define("DB_JAY", "jay2");
defined("DB_DIGITAL_MARKET") OR define("DB_DIGITAL_MARKET", "digitalisasimarketing");
defined("DB_TRANSFER") OR define("DB_TRANSFER", "bridgetransfer");
defined("DB_MRI_TRANSFER") OR define("DB_MRI_TRANSFER", "mritransfer");
defined("DB_CUTI") OR define("DB_CUTI", "db_cuti");
defined("DB_DEVELOP") OR define("DB_DEVELOP", "develop");


class Database {
    private $conn;
    private $dbUser = DB_USER;
    private $dbPass = DB_PASS;
    private $dbName = DB_APP;
    private $dbPort = DB_PORT;
    private $dbHost = DB_HOST;

    public function __construct()  {
        $this->init_connection();
    }

    public function init_connection() {
        $dbHost = $this->get_host_db();
        $dbUser = $this->get_user_db();
        $dbPass = $this->get_password_db();
        $dbName = $this->get_name_db();
        $dbPort = $this->get_port_db();

        $this->conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
        if (!$this->conn) {
            die("Connection error: " . mysqli_connect_error());
        }
    }

    public function connect()
    {
        return $this->conn;
    }

    public function set_host_db($dbHost = "") {
        $this->dbHost = $dbHost;
    }

    public function set_user_db($dbUser = "") {
        $this->dbUser = $dbUser;
    }

    public function set_password_db($dbPass = "") {
        $this->dbPass = $dbPass;
    }

    public function set_name_db($dbName = "") {
        $this->dbName = $dbName;
    }

    public function set_port_db($dbPort = "") {
        $this->dbPort = $dbPort;
    }

    private function get_host_db() {
        return $this->dbHost;
    }

    private function get_user_db() {
        return $this->dbUser;
    }

    private function get_password_db() {
        return $this->dbPass;
    }

    private function get_name_db() {
        return $this->dbName;
    }

    private function get_port_db() {
        return $this->dbPort;
    }
}