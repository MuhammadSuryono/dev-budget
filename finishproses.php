<?php
//error_reporting(0);
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
require "vendor/email/send-email.php";

$noid = $_POST['noid'];

//periksa apakah udah submit
if (isset($_POST['submit'])) {

  $queryGetPengajuan = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE noid='$noid'");
  $time = mysqli_fetch_assoc($queryGetPengajuan)['waktu'];

  $update = mysqli_query($koneksi, "UPDATE pengajuan SET status = 'Finish' WHERE waktu ='$time'");

  $queryGetData = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE noid=$noid");
  $data = mysqli_fetch_assoc($queryGetData);
  $pembuat = $data['pembuat'];
  $pengaju = $data['pengaju'];
  $namaProject = $data['nama'];
  $divisi = $data['divisi'];
  $totalbudget = $data['totalbudget'];

  $email = [];
  $nama = [];
  $queryGetEmail = mysqli_query($koneksi, "SELECT email,divisi,nama_user from tb_user WHERE nama_user='$pengaju' AND aktif='Y'");
  $data = mysqli_fetch_assoc($queryGetEmail);
  $divisi = $data['divisi'];
  array_push($email, $data['email']);
  array_push($nama, $data['nama_user']);

  $queryUserByDivisi = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi = '$data[divisi]' AND (level = 'Manager' OR level = 'Senior Manager') AND aktif='Y'") or die(mysqli_error($koneksi));
  $user = mysqli_fetch_assoc($queryUserByDivisi);
  array_push($email, $user['email']);
  array_push($nama, $user['nama_user']);

  //jika sudah berhasil
  if ($update) {

    $url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    $url = explode('/', $url);
    $url = $url[0] . '/' . $url[1] . '/' . 'login.php';

    $msg = "Dear $pengaju, <br><br>
        Budget dengan keterangan berikut:<br><br>
        Nama Project    : <strong>$namaProject</strong><br>
        Pengaju         : <strong>$pengaju</strong><br>
        Divisi          : <strong>$divisi</strong><br>
        Total Budget    : <strong>Rp. " . number_format($totalbudget, 0, '', ',') . "</strong><br><br>
        
        Telah di Close oleh <strong> $pembuat </strong> pada <strong> " . date("d/m/Y H:i:s") . "</strong><br><br>
        ";

    $msg .= "Klik <a href='$url'>Disini</a> untuk membuka aplikasi budget.";
    $subject = "Notifikasi Untuk Budget";
    if ($email) {
      $message = sendEmail($msg, $subject, $email, $name, $address = "multiple");
    }

    $notifikasi = 'Berhasil!! , Status Budget Telah Di Ubah Menjadi Finish. Pemberitahuan via email telah terkirim ke ';
    for ($i = 0; $i < count($email); $i++) {
      $notifikasi .= ($nama[$i] . ' (' . $email[$i] . ')');
      if ($i < count($email) - 1) $notifikasi .= ', ';
      else $notifikasi .= '.';
    }

    // $notifikasi = "Berhasil!! , Status Budget Telah Di Ubah Menjadi Finish. Pemberitahuan via email telah terkirim ke $pengaju ($email)";

    echo "<script language='javascript'>";
    echo "alert('$notifikasi')";
    echo "</script>";
    echo "<script> document.location.href='list-direksi.php'; </script>";
  } else {
    echo "<script language='javascript'>";
    echo "alert('Edit Budget Gagal')";
    echo "</script>";
    echo "<script> document.location.href='list-direksi.php'; </script>";
  }
}
