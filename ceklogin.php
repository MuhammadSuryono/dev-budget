<?php
error_reporting(0);
session_start();
require_once("application/config/database.php");

$id_user = $_POST['id_user'];
$password = md5($_POST['password']);
$op = $_GET['op'];

$con = new Database();
$koneksi = $con->connect();

if ($op == "in") {

  $sql = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE id_user='$id_user'");
  if (mysqli_num_rows($sql) == 1) { //jika berhasil akan bernilai 1
    $qry = mysqli_fetch_assoc($sql);
    if ($qry['aktif'] == 'Y') {
      if (($password == $qry['password'])) {
        $_SESSION['nama_user'] = $qry['nama_user'];
        $_SESSION['divisi'] = $qry['divisi'];
        $_SESSION['jabatan'] = $qry['level'];
        $_SESSION['hak_akses'] = $qry['hak_akses'];
        $_SESSION['id_user'] = $qry['id_user'];
        $_SESSION['hak_page'] = $qry['hak_page'];

        $bfrSessionNextPath = isset($_SESSION['before_session_next_path']) ? $_SESSION['before_session_next_path'] : NULL;

        if ($qry && $bfrSessionNextPath == NULL) {
          nextPage($qry['divisi']);
        } else {
          $_SESSION['before_session_next_path'] = NULL;
          $bfrSessionNextPath = json_decode($bfrSessionNextPath);
          
          if ($bfrSessionNextPath->id_user != $id_user) {
            nextPage($qry["divisi"]);
          } else {
            header("location:" . $bfrSessionNextPath->next_path);
          }
        }
      } else {
        echo "<script language='JavaScript'>
        alert('Username atau Password tidak sesuai. Silahkan diulang kembali!');
        document.location = 'index.php';
      </script>";
      }
    } else {
      echo "<script language='JavaScript'>
      alert('Akun sudah tidak aktif!');
      document.location = 'index.php';
    </script>";
    }
  } else {
      echo "<script language='JavaScript'>
      alert('Username atau Password tidak sesuai. Silahkan diulang kembali!');
      document.location = 'index.php';
    </script>";
  }
} else if ($op == "out") {

  unset($_SESSION['USERNAME']);
  unset($_SESSION['AKSES']);
  header("location:index.php");
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