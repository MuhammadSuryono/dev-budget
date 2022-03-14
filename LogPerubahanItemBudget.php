<?php

require_once "application/config/database.php";

class LogPerubahanItemBudget extends Database
{
    protected $koneksi;
    public function __construct($fromInit = true)
    {
        parent::__construct($fromInit);
        $this->koneksi = $this->connect();
        $this->load_database($this->koneksi);
    }

    public function get_list_log()
    {
        return $this->select()->from("log_item_request")->where("id_item_request", "=", $_GET["idItemRequest"])->get();
    }
}

$log = new LogPerubahanItemBudget();
$action = $_GET["action"];

if ($action == "getLog") {
    echo json_encode($log->get_list_log());
}