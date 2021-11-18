<?php
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

$email = $_POST['email'];
$id = $_POST['id'];
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $emailErr = "Format email salah";
} else {
    $queryUpdateEmail = mysqli_query($koneksi, "UPDATE tb_user SET email='$email' WHERE id_user='$id'");
}
if ($queryUpdateEmail) {
    echo true;
} else {
    echo $emailErr;
}
