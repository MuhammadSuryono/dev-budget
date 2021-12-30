<?php
session_start();

$url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$port = $_SERVER['SERVER_PORT'];
$url = explode('/', $url);
$hostProtocol = $url[0];

echo $hostProtocol;

if ($hostProtocol == "180.211.92.131")
{
   $host = "http://mkp-operation.com:7793/".$url[1];
   header("Location: ".$host, true, 301);
} else {


if ( isset($_SESSION['user_login']) && $_SESSION['user_login'] != '' ) {
    $halaman = $_SESSION['user_login'];

    header('location:on-' . $halaman);
    exit();
} else {
    header('location:login.php');
    exit();
}
}
