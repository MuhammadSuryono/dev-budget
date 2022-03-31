<?php

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

$p = $_POST;

$actions = ($p['actions']) ? $p['actions'] : "";

switch ($actions) {
  case 'ambil_rekening':
    $id_user = ($p['id_user']) ? $p['id_user'] : "";
    $q = mysqli_query($koneksi, "SELECT a.rekening, a.bank, b.email, c.namabank FROM rekening a JOIN tb_user b ON b.id_user = a.user_id JOIN bank c ON a.bank = c.kodebank WHERE a.no=$id_user LIMIT 1");
    $x = mysqli_fetch_assoc($q);

    echo json_encode($x);
    break;

  default:
    // Apabila actions kosong, maka masuk ke sini
    break;
}
