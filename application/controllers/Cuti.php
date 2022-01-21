<?php

require_once "application/config/database.php";

class Cuti {
    protected $conn;

    public function __construct()
    {
        
    }

    public function checkStatusCutiUser($namaUser)
    {
        $db = new Database();
        $db->set_name_db(DB_CUTI);
        $db->init_connection();
        $this->conn = $db->connect();

        
        $sqlUser = mysqli_query($this->conn, "SELECT id_user FROM tb_user WHERE nama_user = '$namaUser'");
        $user = mysqli_fetch_assoc($sqlUser);

        if (mysqli_num_rows($sqlUser) == 0) {
            return true;
        }

        $dataNow = date('Y-m-d');
        $idUser = $user["id_user"];
        $sqlCuti = mysqli_query($this->conn, "SELECT * FROM tb_mohoncuti WHERE dari <= '$dataNow' AND sampai >= '$dataNow' AND nip = '$idUser'");
        
        if (mysqli_num_rows($sqlCuti) > 0) {
            return true;
        }

        $sqlIzin = mysqli_query($this->conn, "SELECT * FROM tb_izin WHERE dari <= '$dataNow' AND sampai >= '$dataNow' AND nip = '$idUser'");
        
        if (mysqli_num_rows($sqlIzin) > 0) {
            return true;
        }

        return false;
        
    }
}