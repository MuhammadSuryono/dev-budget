<?php

require_once "database.php";

class Role extends Database
{
    public function __construct($fromInit, mysqli $koneksi)
    {
        parent::__construct($fromInit);
        $this->load_database($koneksi);
    }

    public function get_role_budget($id, $condition = "", $valueCondition = "")
    {
        $role = $this->select("*")->from("tb_role_budget")->where("budget", "=", $id)->first();
        return $role != null;
    }

}