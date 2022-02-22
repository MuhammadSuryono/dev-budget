<?php
session_start();
require_once "application/config/database.php";
$con = new Database();
$con->set_host_db(DB_HOST_DIGITALISASI_MARKETING);
$con->set_name_db(DB_DIGITAL_MARKET);
$con->set_user_db(DB_USER_DIGITAL_MARKET);
$con->set_password_db(DB_PASS_DIGITAL_MARKET);
$con->init_connection();
$koneksiDigitalMarket = $con->connect();

var_dump($koneksiDigitalMarket);
//$url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
//$port = $_SERVER['SERVER_PORT'];
//$url = explode('/', $url);
//$hostProtocol = $url[0];
//
//
//if ($hostProtocol == "180.211.92.131")
//{
//   $host = "http://mkp-operation.com:7793/".$url[1];
//   header("Location: ".$host, true, 301);
//} else {
//
//
//if ( isset($_SESSION['user_login']) && $_SESSION['user_login'] != '' ) {
//    $halaman = $_SESSION['user_login'];
//
//    header('location:on-' . $halaman);
//    exit();
//} else {
//    header('location:login.php');
//    exit();
//}
//}
