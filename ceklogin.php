<?php
error_reporting(0);
session_start();
require_once("application/config/session.php");

$op = $_GET['op'];
$session = new Session(true);

if ($op == "in") {
  $username = $_POST['username'];
  $password = $_POST['password'];

  $isLogin = $session->setAuthLogin($username, $password);
  if ($isLogin) {
      $bfrSessionNextPath = isset($_SESSION['before_session_next_path']) ? $_SESSION['before_session_next_path'] : NULL;
      nextPage($_SESSION['divisi']);
  } else {
    header("location: login.php?error=true", true, 301);
    exit();
  }
} else if ($op == "out") {
  session_destroy();
  header("location:login.php", true, 301);
  exit();
}

function nextPage($divisi) {
  if ($divisi == "Direksi" || $divisi == "Direksi") {
    header("location:home-direksi.php");
  } else if ($divisi == "FINANCE" || $divisi == "FINANCE") {
    header("location:home-finance.php");
  } else if ($divisi == "Admin" || $divisi == "Admin") {
    header("location:home-admin.php");
  } else {
    header("location:home.php");
  }
}

?>