<?php
require "../application/config/database.php";

$con = new Database();
$koneksi = $con->connect();;

$id = $_POST['id'];
$akses = $_POST['akses'];
$isChecked = $_POST['isChecked'];

$queryUser = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE id_user = '$id'");
$user = mysqli_fetch_assoc($queryUser);
$hakAkses = unserialize($user['hak_button']);

if (!$hakAkses) {
    $hakAkses  = [];
}

if ($isChecked !== "false") {
    array_push($hakAkses, $akses);
} else {
    if (($key = array_search($akses, $hakAkses)) !== false) {
        unset($hakAkses[$key]);
    }
}

$hakAkses = serialize($hakAkses);

echo json_encode($hakAkses);
// die;
$update = mysqli_query($koneksi, "UPDATE tb_user SET hak_button = '$hakAkses' WHERE id_user = '$id'");
// echo json_encode($hakAkses);
// echo ($update);
die;
