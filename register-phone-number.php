<?php
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

$email = $_POST['phoneNumber'];
$id = $_POST['id'];
$queryUpdateEmail = mysqli_query($koneksi, "UPDATE tb_user SET phone_number='$email' WHERE id_user='$id'");
if ($queryUpdateEmail) {
    echo true;
} else {
    echo $emailErr;
}
