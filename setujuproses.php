<?php
//error_reporting(0);
include('koneksi.php');
require "vendor/email/send-email.php";

session_start();
if (!isset($_SESSION['nama_user'])) {
  header("location:login.php");
  // die('location:login.php');//jika belum login jangan lanjut
}

$userSetuju = $_SESSION['nama_user'];
date_default_timezone_set("Asia/Bangkok");

$time = date('Y-m-d H:i:s');

$noid_bpu           = $_POST['noid'];
$no           = $_POST['no'];
$waktu        = $_POST['waktu'];
$divisi       = $_POST['divisi'];
$persetujuan  = $_POST['persetujuan'];
$finance      = $_SESSION['divisi'];
$urgent       = $_POST['urgent'];
$tanggalbayar = $_POST['tanggalbayar'];
$term       = $_POST['term'];
$metodePembayaran = $_POST['metode_pembayaran'];
// $beritaTransfer = $_POST['berita-transfer'];
$alasanTolakBpu = $_POST['alasanTolakBpu'];
// $rekeningSumber = $_POST['rekening_sumber'];

$aksesSes = $_SESSION['hak_akses'];

if ($urgent == 'Urgent') {
  $tanggalbayar = $time;
} else {
  $d = mktime(8, 15, 0);
  $hour = date("H:i:s", $d);
  $tanggalbayar = $_POST['tanggalbayar'] . ' ' .  $hour;
}

$dt = new DateTime($tanggalbayar);

$email = [];
$nama = [];

//periksa apakah udah submit
$selbay = mysqli_query($koneksi, "SELECT noid,jenis,pengaju,nama FROM pengajuan WHERE waktu='$waktu'");
$s = mysqli_fetch_assoc($selbay);
$noid = $s['noid'];
$jenis = $s['jenis'];

$selfirst = mysqli_query($koneksi, "SELECT MIN(noid) FROM bpu WHERE no='$no' AND waktu='$waktu' AND persetujuan='Belum Disetujui'");
$selsec = mysqli_fetch_assoc($selfirst);
$numb = $selsec['MIN(noid)'];

$sel1 = mysqli_query($koneksi, "SELECT noid FROM pengajuan WHERE waktu='$waktu'");
$uc = mysqli_fetch_assoc($sel1);
$idBpu = $uc['noid'];

$queryBpu = mysqli_query($koneksi, "SELECT * FROM bpu WHERE no='$no' AND waktu='$waktu' AND persetujuan='Belum Disetujui' AND term=$term");
$bpu = mysqli_fetch_assoc($queryBpu);


$querySelesai = mysqli_query($koneksi, "SELECT * FROM selesai WHERE waktu = '$waktu' AND no = '$no'");
$selesai = mysqli_fetch_assoc($querySelesai);

// $nm_project = '"' . $s['nama'] . '", "item ke ' . $bpu['no'] . '", "BPU ke ' . $bpu['term'] . '"';

if ($_POST['submit'] == 1) {
  if ($metodePembayaran == 'MRI PAL') {

    // var_dump($noid_bpu);
    // die;
    $update = mysqli_query($koneksiTransfer, "UPDATE data_transfer SET jadwal_transfer ='$tanggalbayar', nm_otorisasi = '$userSetuju' WHERE noid_bpu = '$noid_bpu'") or die(mysqli_error($koneksiTransfer));

    // $update = mysqli_query($koneksiTransfer, "INSERT INTO data_transfer (transfer_req_id, transfer_type, jenis_pembayaran_id, keterangan, waktu_request, jadwal_transfer, norek, pemilik_rekening, bank, kode_bank, berita_transfer, jumlah, terotorisasi, hasil_transfer, ket_transfer, nm_pembuat, nm_validasi, nm_otorisasi, nm_manual, jenis_project, nm_project, noid_bpu, biaya_trf, rekening_sumber, email_pemilik_rekening) 
    //       VALUES ('$formatId', '3', '1', '$bpu[statusbpu]', '$waktu', '$tanggalbayar','$temp_norek' , '$temp_namapenerima','$bank[namabank]', '$bank[kodebank]', '$beritaTransfer','$bpu[jumlah]', '2', '1', 'Antri', '$bpu[pengaju]', '$bpu[checkby]', '$userSetuju', '', '$s[jenis]', '$nm_project', '$bpu[noid]', $biayaTrf, '$rekeningSumber', '$temp_email')") or die(mysqli_error($koneksiTransfer));
    // var_dump($noid_bpu);
    // var_dump('here');
    // die;

    if (!$update) {
      echo "<script language='javascript'>";
      echo "alert('BPU Gagal Disetujui!')";
      echo "</script>";
      if ($aksesSes == 'HRD') echo "<script> document.location.href='views-direksi.php?code=" . $idBpu . "'; </script>";
      if ($aksesSes == 'Manager') echo "<script> document.location.href='views-finance-manager.php?code=" . $idBpu . "'; </script>";
      else echo "<script> document.location.href='views.php?code=" . $idBpu . "'; </script>";
    }
  }


  if ($finance == 'FINANCE') {
    $update = mysqli_query($koneksi, "UPDATE bpu SET status_pengajuan_bpu =0, persetujuan = 'Disetujui (Sri Dewi Marpaung)', tanggalbayar = '$tanggalbayar', urgent = '$urgent', approveby = '$userSetuju', tglapprove = '$time'
                            WHERE no='$no' AND waktu='$waktu' AND persetujuan='Belum Disetujui' AND term=$term");

    $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user FROM tb_user WHERE nama_user = '$bpu[pengaju]' AND aktif='Y'");
    $emailUser = mysqli_fetch_assoc($queryEmail);
    if ($emailUser) {
      array_push($email, $emailUser['email']);
      array_push($nama, $emailUser['nama_user']);
    }

    $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user,divisi FROM tb_user WHERE nama_user = '$s[pengaju]' AND aktif='Y'");
    $emailUser = mysqli_fetch_assoc($queryEmail);
    if ($emailUser) {
      array_push($email, $emailUser['email']);
      array_push($nama, $emailUser['nama_user']);
    }

    $queryUserByDivisi = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi = '$emailUser[divisi]' AND (level = 'Manager' OR level = 'Senior Manager' AND aktif='Y')");
    $user = mysqli_fetch_assoc($queryUserByDivisi);
    if ($user) {
      array_push($email, $user['email']);
      array_push($nama, $user['nama_user']);
    }

    if ($s['jenis'] == 'B1' || $s['jenis'] == 'B2') {
      $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('1', '3')");
      while ($e = mysqli_fetch_assoc($queryEmail)) {
        if ($e['email']) {
          array_push($email, $e['email']);
          array_push($nama, $e['nama_user']);
        }
      }
    } else {
      $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('2', '3')");
      while ($e = mysqli_fetch_assoc($queryEmail)) {
        if ($e['email']) {
          array_push($email, $e['email']);
          array_push($nama, $e['nama_user']);
        }
      }
    }
  } else {
    $update = mysqli_query($koneksi, "UPDATE bpu SET status_pengajuan_bpu =0, persetujuan = 'Disetujui (Direksi)', tanggalbayar = '$tanggalbayar', urgent = '$urgent', approveby = '$userSetuju', tglapprove = '$time'
                               WHERE no='$no' AND waktu='$waktu' AND persetujuan='Belum Disetujui'  AND term=$term");

    $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user FROM tb_user WHERE nama_user = '$bpu[pengaju]' AND aktif='Y'");
    $emailUser = mysqli_fetch_assoc($queryEmail);
    if ($emailUser) {
      array_push($email, $emailUser['email']);
      array_push($nama, $emailUser['nama_user']);
    }

    $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user,divisi FROM tb_user WHERE nama_user = '$s[pengaju]' AND aktif='Y'");
    $emailUser = mysqli_fetch_assoc($queryEmail);
    if ($emailUser) {
      array_push($email, $emailUser['email']);
      array_push($nama, $emailUser['nama_user']);
    }

    $queryUserByDivisi = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi = '$emailUser[divisi]' AND (level = 'Manager' OR level = 'Senior Manager') AND aktif='Y'");
    $user = mysqli_fetch_assoc($queryUserByDivisi);
    if ($user) {
      array_push($email, $user['email']);
      array_push($nama, $user['nama_user']);
    }

    if ($s['jenis'] == 'B1' || $s['jenis'] == 'B2') {
      $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('1', '3')");
      while ($e = mysqli_fetch_assoc($queryEmail)) {
        if ($e['email']) {
          array_push($email, $e['email']);
          array_push($nama, $e['nama_user']);
        }
      }
    } else {
      $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('2', '3')");
      while ($e = mysqli_fetch_assoc($queryEmail)) {
        if ($e['email']) {
          array_push($email, $e['email']);
          array_push($nama, $e['nama_user']);
        }
      }
    }
  }

  array_unique($email);
  array_unique($nama);

  $queryBpu = mysqli_query($koneksi, "SELECT * FROM bpu WHERE no='$no' AND waktu='$waktu' AND term='$term'");
  $bpu = mysqli_fetch_assoc($queryBpu);
  $namapenerima = $bpu['namapenerima'];
  $jumlah = $bpu['jumlah'];


  $queryProject = mysqli_query($koneksi, "SELECT nama FROM pengajuan WHERE waktu='$waktu'");
  $namaProject = mysqli_fetch_array($queryProject)[0];

  $url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
  $url = explode('/', $url);
  $url = $url[0] . '/' . $url[1] . '/' . 'login.php';

  $msg = "Notifikasi BPU, <br><br>
      BPU telah di setujui oleh $userSetuju dengan keterangan sebagai berikut:<br><br>
      Nama Project       : <strong>$namaProject</strong><br>
      Item No.           : <strong>$no</strong><br>
      Term               : <strong>$term</strong><br>
      Nama Penerima      : <strong>$namapenerima</strong><br>
      Tanggal Pembayaran : <strong>$tanggalbayar</strong><br>
      Metode Pembayaran  : <strong>$metodePembayaran</strong><br>
      Total Diajukan     : <strong>Rp. " . number_format($jumlah, 0, '', ',') . "</strong><br>
      ";
  if ($keterangan) {
    $msg .= "Keterangan:<strong> $keterangan </strong><br><br>";
  } else {
    $msg .= "<br>";
  }
  $msg .= "Klik <a href='$url'>Disini</a> untuk membuka aplikasi budget.";
  $subject = "Notifikasi Aplikasi Budget";

  if ($email) {
    $message = sendEmail($msg, $subject, $email, $name, $address = "multiple");
  }

  $notification = 'BPU Telah Disetujui. Pemberitahuan via email telah terkirim ke ';
  $i = 0;
  for ($i = 0; $i < count($email); $i++) {
    $notification .= ($nama[$i] . ' (' . $email[$i] . ')');
    if ($i < count($email) - 1) $notification .= ', ';
    else $notification .= '.';
  }

  $queryBank = mysqli_query($koneksi, "SELECT * FROM bank WHERE kodebank = '$bpu[namabank]'");
  $bank = mysqli_fetch_assoc($queryBank);
  if ($selesai['status'] == 'Vendor/Supplier' || $selesai['status'] == 'Honor Eksternal') {
    $explodeString = explode('.', $bpu['ket_pembayaran']);
    // var_dump($explodeString[2][0]);
    // die;
    if ($selesai['status'] == 'Vendor/Supplier') {
      $msg = "Kepada " . $bpu['namapenerima'] . ", <br><br>
      Berikut informasi status pembayaran Anda:<br><br>
      No.Invoice       : <strong>" . $explodeString[1] . "</strong><br>
      Tgl. Invoice     : <strong>" . $explodeString[2][0] . $explodeString[2][1] . "/" . $explodeString[2][2] .  $explodeString[2][3] . "/20" . $explodeString[2][4] . $explodeString[2][5] . "</strong><br>
      Term             : <strong>" . $explodeString[3][1] . " of " . $explodeString[3][3] . "</strong><br>
      Jenis Pembayaran : <strong>" . $explodeString[4] . "</strong><br>
      No. Rekening Anda : <strong>" . $bpu['norek'] . "</strong><br>
      Bank             : <strong>" . $bank['namabank'] . "</strong><br>
      Nama Penerima    : <strong>" . $bpu['namapenerima'] . "</strong><br>
      Jumlah Dibayarkan : <strong>Rp. " . number_format($jumlah, 0, '', '.') . "</strong><br>
      Status           : <strong>Dijadwalkan</strong>, Tanggal : <strong>" . $dt->format('d/m/Y')  . "</strong><br><br>
      Informasi update status pembayaran akan kami kirimkan kembali melalui email. Jika ada pertanyaan lebih lanjut, silahkan email Divisi Finance ke finance@mri-research-ind.com.<br><br>
      Hormat kami,<br>
      Finance Marketing Research Indonesia
      ";
    } else {
      $msg = "Kepada " . $bpu['namapenerima'] . ", <br><br>
      Berikut informasi status pembayaran yang akan Anda terima:<br><br>
      Nama Pembayaran  : <strong>" . $bpu['ket_pembayaran'] . "</strong><br>
      No. Rekening Anda : <strong>" . $bpu['norek'] . "</strong><br>
      Bank             : <strong>" . $bank['namabank'] . "</strong><br>
      Nama Penerima    : <strong>" . $bpu['namapenerima'] . "</strong><br>
      Jumlah Dibayarkan : <strong>Rp. " . number_format($jumlah, 0, '', '.') . "</strong><br>
      Status           : <strong>Dijadwalkan</strong>, Tanggal : <strong>" . $dt->format('d/m/Y')  . "</strong><br><br>
      Informasi update status pembayaran akan kami kirimkan kembali melalui email. Jika ada pertanyaan lebih lanjut, silahkan email Divisi Finance ke finance@mri-research-ind.com.<br><br>
      Hormat kami,<br>
      Finance Marketing Research Indonesia
      ";
    }
    $subject = "Informasi Pembayaran";

    if (!is_null($bpu['emailpenerima'])) {
      $message = sendEmail($msg, $subject, $bpu['emailpenerima'], $name = '', $address = "single");
    }
  }
} else if ($_POST['submit'] == 0) {
  $update = mysqli_query($koneksi, "UPDATE bpu SET status_pengajuan_bpu = 2, alasan_tolak_bpu = '$alasanTolakBpu', jumlah = '0' WHERE no='$no' AND waktu='$waktu' AND term='$term'");

  $queryBpu = mysqli_query($koneksi, "SELECT pengaju,namapenerima,jumlah FROM bpu WHERE no='$no' AND waktu='$waktu' AND term='$term'");
  $bpu = mysqli_fetch_assoc($queryBpu);
  $namapenerima = $bpu['namapenerima'];
  $jumlah = $bpu['jumlah'];

  $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user,divisi FROM tb_user WHERE nama_user = '$bpu[pengaju]' AND aktif='Y'");
  $emailUser = mysqli_fetch_assoc($queryEmail);
  if ($emailUser) {
    array_push($email, $emailUser['email']);
    array_push($nama, $emailUser['nama_user']);
  }

  $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user,divisi FROM tb_user WHERE nama_user = '$s[pengaju]' AND aktif='Y'");
  $emailUser = mysqli_fetch_assoc($queryEmail);
  if ($emailUser) {
    array_push($email, $emailUser['email']);
    array_push($nama, $emailUser['nama_user']);
  }

  $queryUserByDivisi = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi = '$emailUser[divisi]' AND (level = 'Manager' OR level = 'Senior Manager') AND aktif='Y'");
  $user = mysqli_fetch_assoc($queryUserByDivisi);
  if ($user) {
    array_push($email, $user['email']);
    array_push($nama, $user['nama_user']);
  }

  if ($s['jenis'] == 'B1' || $s['jenis'] == 'B2') {
    $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('1', '3')");
    while ($e = mysqli_fetch_assoc($queryEmail)) {
      if ($e['email']) {
        array_push($email, $e['email']);
        array_push($nama, $e['nama_user']);
      }
    }
  } else {
    $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('2', '3')");
    while ($e = mysqli_fetch_assoc($queryEmail)) {
      if ($e['email']) {
        array_push($email, $e['email']);
        array_push($nama, $e['nama_user']);
      }
    }
  }

  array_unique($email);
  array_unique($nama);

  $queryProject = mysqli_query($koneksi, "SELECT nama FROM pengajuan WHERE waktu='$waktu'");
  $namaProject = mysqli_fetch_array($queryProject)[0];

  $url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
  $url = explode('/', $url);
  $url = $url[0] . '/' . $url[1] . '/' . 'login.php';

  $msg = "Notifikasi BPU, <br><br>
        BPU telah di tolak oleh $userSetuju dengan keterangan sebagai berikut:<br><br>
        Nama Project      : <strong>$namaProject</strong><br>
        Item No.       : <strong>$no</strong><br>
        Term           : <strong>$term</strong><br>
        Nama Pengaju      : <strong>$bpu[pengaju]</strong><br>
        Nama Penerima     : <strong>$namapenerima</strong><br>
        Total Diajukan    : <strong>Rp. " . number_format($jumlah, 0, '', ',') . "</strong><br><br>
        ";
  if ($alasanTolakBpu) {
    $msg .= "Ditolak dengan alasan <strong> $alasanTolakBpu </strong>.<br><br>";
  } else {
    $msg .= "Ditolak tanpa alasan.<br><br>";
  }
  $msg .= "Klik <a href='$url'>Disini</a> untuk membuka aplikasi budget.";
  $subject = "Notifikasi Aplikasi Budget";

  if ($email) {
    $message = sendEmail($msg, $subject, $email, $name, $address = "multiple");
  }

  $notification = "Verifikasi BPU Sukses. Pemberitahuan via email telah terkirim ke ";

  for ($i = 0; $i < count($email); $i++) {
    $notification .= ($nama[$i] . ' (' . $email[$i] . ')');
    if ($i < count($email) - 1) $notification .= ', ';
    else $notification .= '.';
  }
  // var_dump($notification);
}

// die;
if ($update) {

  if ($finance == 'FINANCE') {

    if ($jenis == 'Non Rutin') {
      echo "<script language='javascript'>";
      echo "alert('$notification')";
      echo "</script>";
      if ($aksesSes == 'Manager') echo "<script> document.location.href='view-finance-nonrutin-manager.php?code=" . $idBpu . "'; </script>";
      else echo "<script> document.location.href='view-finance-nonrutin.php?code=" . $idBpu . "'; </script>";
    } else if ($jenis == 'B1') {
      echo "<script language='javascript'>";
      echo "alert('$notification')";
      echo "</script>";
      if ($aksesSes == 'Manager') echo "<script> document.location.href='view-finance-manager-b1.php?code=" . $idBpu . "'; </script>";
      else echo "<script> document.location.href='view-finance.php?code=" . $idBpu . "'; </script>";
    } else {
      echo "<script language='javascript'>";
      echo "alert('$notification')";
      echo "</script>";
      if ($aksesSes == 'Manager') echo "<script> document.location.href='view-finance-manager.php?code=" . $idBpu . "'; </script>";
      else echo "<script> document.location.href='view-finance.php?code=" . $idBpu . "'; </script>";
    }
  } else {
    echo "<script language='javascript'>";
    echo "alert('$notification')";
    echo "</script>";
    if ($aksesSes == 'HRD') echo "<script> document.location.href='views-direksi.php?code=" . $idBpu . "'; </script>";
    else echo "<script> document.location.href='views.php?code=" . $idBpu . "'; </script>";
  }
} else {
  if ($finance == 'FINANCE') {

    if ($jenis == 'Non Rutin') {
      echo "<script language='javascript'>";
      echo "alert('Gagal menyetujui BPU')";
      echo "</script>";
      if ($aksesSes == 'Manager') echo "<script> document.location.href='view-finance-nonrutin-manager.php?code=" . $idBpu . "'; </script>";
      else echo "<script> document.location.href='view-finance-nonrutin.php?code=" . $idBpu . "'; </script>";
    } else {
      echo "<script language='javascript'>";
      echo "alert('Gagal menyetujui BPU')";
      echo "</script>";
      if ($aksesSes == 'Manager') echo "<script> document.location.href='view-finance-manager.php?code=" . $idBpu . "'; </script>";
      else echo "<script> document.location.href='view-finance.php?code=" . $idBpu . "'; </script>";
    }
  } else {
    echo "<script language='javascript'>";
    echo "alert('Gagal menyetujui BPU')";
    echo "</script>";
    if ($aksesSes == 'HRD') echo "<script> document.location.href='views-direksi.php?code=" . $idBpu . "'; </script>";
    else echo "<script> document.location.href='views.php?code=" . $idBpu . "'; </script>";
  }
}
