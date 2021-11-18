<?php
require "../application/config/database.php";

$con = new Database();
$koneksi = $con->connect();;

$no = $_POST['no'];
$waktu = $_POST['waktu'];
$term = $_POST['term'];

$queryBpu = mysqli_query($koneksi, "SELECT * FROM bpu WHERE no='$no' AND waktu='$waktu' AND term='$term'");
$bpu = mysqli_fetch_assoc($queryBpu);

echo json_encode($bpu);
