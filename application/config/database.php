<?php
defined("MODE_APP") OR define("MODE_APP", "dev");
defined("DB_APP") OR define("DB_APP", "budget");
defined("DB_HOST") OR define("DB_HOST", "192.168.8.2");
defined("DB_USER") OR define("DB_USER", "adam");
defined("DB_PASS") OR define("DB_PASS", "Ad@mMR!db213");
defined("DB_PORT") OR define("DB_PORT", "3306");

// Define other const value here
defined("DB_JAY") OR define("DB_JAY", "jay2");
defined("DB_DIGITAL_MARKET") OR define("DB_DIGITAL_MARKET", "digitalisasimarketing");
defined("DB_TRANSFER") OR define("DB_TRANSFER", "bridgetransfer");
defined("DB_MRI_TRANSFER") OR define("DB_MRI_TRANSFER", "mritransferapi");
defined("DB_CUTI") OR define("DB_CUTI", "db_cuti");
defined("DB_DEVELOP") OR define("DB_DEVELOP", "develop");
defined("DB_HOST_DIGITALISASI_MARKETING") OR define("DB_HOST_DIGITALISASI_MARKETING", MODE_APP == "dev" ? "192.168.10.240" : "192.168.10.240");
defined("DB_USER_DIGITAL_MARKET") OR define("DB_USER_DIGITAL_MARKET", "adam");
defined("DB_PASS_DIGITAL_MARKET") OR define("DB_PASS_DIGITAL_MARKET", "Ad@mMR!db213");


require_once 'ModelBudget.php';

class Database extends ModelBudget {
    private $conn;
    private $dbUser = DB_USER;
    private $dbPass = DB_PASS;
    private $dbName = DB_APP;
    private $dbPort = DB_PORT;
    private $dbHost = DB_HOST;

    public function __construct($fromInit = true)  {
        if ($fromInit == true) $this->init_connection();
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

    public function get_name_db() {
        return $this->dbName;
    }

    private function get_port_db() {
        return $this->dbPort;
    }
}