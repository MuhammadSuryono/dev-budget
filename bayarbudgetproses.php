<?php
//error_reporting(0);
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
require "vendor/email/send-email.php";

$no           = $_POST['no'];
$term         = $_POST['term'];
$jumlahbayar  = $_POST['jumlahbayar'];
$nomorvoucher = $_POST['nomorvoucher'];
$tanggalbayar = $_POST['tanggalbayar'];
$waktu        = $_POST['waktu'];
$pembayar     = $_POST['pembayar'];
$divisi       = $_POST['divisi'];


//periksa apakah udah submit
if (isset($_POST['submit'])) {

  $selbay = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE waktu='$waktu'");
  $s = mysqli_fetch_assoc($selbay);
  $noid = $s['noid'];

  $selfirst = mysqli_query($koneksi, "SELECT max(noid) FROM bpu WHERE no='$no' AND waktu='$waktu' AND status='Belum Di Bayar' AND persetujuan !='Belum Disetujui'");
  $selsec = mysqli_fetch_assoc($selfirst);

  $queryPenerima = mysqli_query($koneksi, "SELECT namapenerima FROM bpu WHERE no='$no' AND waktu='$waktu' AND status='Belum Di Bayar' AND term = '$term'");
  $penerima =  mysqli_fetch_assoc($queryPenerima)['namapenerima'];

  $querySelesai = mysqli_query($koneksi, "SELECT * FROM selesai WHERE no='$no' AND waktu='$waktu'");
  $selesai = mysqli_fetch_assoc($querySelesai);

  if (mysqli_num_rows($selfirst) == 0) {
    echo "<script language='javascript'>";
    echo "alert('GAGAL, Tidak Ada BPU atau BPU belum disetujui Direksi')";
    echo "</script>";
    echo "<script> document.location.href='view-finance.php?code=" . $noid . "'; </script>";
  } else {
    $update = mysqli_query($koneksi, "UPDATE bpu SET status = 'Telah Di Bayar',
                                          jumlahbayar = '$jumlahbayar',
                                          novoucher = '$nomorvoucher',
                                          tanggalbayar = '$tanggalbayar',
                                          pembayar = '$pembayar',
                                          divpemb = '$divisi' WHERE no='$no' AND waktu='$waktu' AND term='$term'");
  }


  $queryBpu = mysqli_query($koneksi, "SELECT * FROM bpu WHERE no='$no' AND waktu='$waktu' AND term='$term'");
  $bpu = mysqli_fetch_assoc($queryBpu);
  $namapenerima = $bpu['namapenerima'];

  $email = [];
  $nama = [];
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

  $querUser = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE nama_user='$namapenerima' AND aktif='Y'");
  $user = mysqli_fetch_assoc($querUser);
  if ($user) {
    array_push($email, $user['email']);
    array_push($nama, $user['nama_user']);
  }


  // $queryUserByDivisi = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi = $user[divisi] AND (level = 'Manager' OR level = 'Senior Manager')");
  // if ($queryUserByDivisi) {
  //   $user = mysqli_fetch_assoc($queryUserByDivisi);
  //   array_push($email, $user['email']);
  //   array_push($nama, $user['nama_user']);
  // }

  $queryProject = mysqli_query($koneksi, "SELECT nama FROM pengajuan WHERE waktu='$waktu'");
  $namaProject = mysqli_fetch_array($queryProject)[0];

  $url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
  $url = explode('/', $url);
  $url = $url[0] . '/' . $url[1] . '/' . 'login.php';

  $msg = "Notifikasi BPU, <br><br>
      BPU telah dibayar oleh Finance dengan keterangan sebagai berikut:<br><br>
      Nama Project       : <strong>$namaProject</strong><br>
      Item No.           : <strong>$no</strong><br>
      Term               : <strong>$term</strong><br>
      Nama Penerima      : <strong>$namapenerima</strong><br>
      Pembayar           : <strong>$pembayar</strong><br>
      Tanggal Pembayaran : <strong>$tanggalbayar</strong><br>
      Nomer Voucher      : <strong>$nomorvoucher</strong><br>
      Jumlah Dibayar     : <strong>Rp. " . number_format($jumlahbayar, 0, '', ',') . "</strong><br>
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
    $notifikasi = 'Bayar Budget Berhasil. Pemberitahuan via email telah terkirim ke ';
    for ($i = 0; $i < count($email); $i++) {
      if ($nama[$i]) {
        $notifikasi .= ($nama[$i] . ' (' . $email[$i] . ')');
        if ($i < count($email) - 1) $notifikasi .= ', ';
        else $notifikasi .= '.';
      }
    }
  } else {
    $notifikasi = 'Bayar Budget Berhasil.';
  }

  $queryBank = mysqli_query($koneksi, "SELECT * FROM bank WHERE kodebank = '$bpu[namabank]'");
  $bank = mysqli_fetch_assoc($queryBank);
  if ($selesai['status'] == 'Vendor/Supplier' || $selesai['status'] == 'Honor Eksternal') {
    $explodeString = explode('.', $bpu['ket_pembayaran']);

    $dt = new DateTime($tanggalbayar);
    if ($selesai['status'] == 'Vendor/Supplier') {
      $msg = "Kepada " . $bpu['namapenerima'] . ", <br><br>
      Berikut informasi status pembayaran yang akan Anda terima:<br><br>
      No.Invoice       : <strong>" . $explodeString[1] . "</strong><br>
      Tgl. Invoice     : <strong>" . $explodeString[2][0] . $explodeString[2][1] . "/" . $explodeString[2][2] .  $explodeString[2][3] . "/20" . $explodeString[2][4] . $explodeString[2][5] . "</strong><br>
      Term             : <strong>" . $explodeString[3][1] . " of " . $explodeString[3][3]  . "</strong><br>
      Jenis Pembayaran : <strong>" . $explodeString[4] . "</strong><br>
      No. Rekening Anda : <strong>" . $bpu['norek'] . "</strong><br>
      Bank             : <strong>" . $bank['namabank'] . "</strong><br>
      Nama Penerima    : <strong>" . $bpu['namapenerima'] . "</strong><br>
      Jumlah Dibayarkan : <strong>Rp. " . number_format($jumlahbayar, 0, '', '.') . "</strong><br>
      Status           : <strong>Dibayar</strong>,  Tanggal : <strong>" . $dt->format('d/m/Y') . "</strong><br><br>
      Jika ada pertanyaan lebih lanjut, silahkan email Divisi Finance ke finance@mri-research-ind.com.<br><br>
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
      Jumlah Dibayarkan : <strong>Rp. " . number_format($jumlahbayar, 0, '', '.') . "</strong><br>
      Status           : <strong>Dibayar</strong>,  Tanggal : <strong>" . $dt->format('d/m/Y') . "</strong><br><br>
      Jika ada pertanyaan lebih lanjut, silahkan email Divisi Finance ke finance@mri-research-ind.com.<br><br>
      Hormat kami,<br>
      Finance Marketing Research Indonesia
      ";
    }
    $subject = "Informasi Pembayaran";

    if (!is_null($bpu['emailpenerima'])) {
      $message = sendEmail($msg, $subject, $bpu['emailpenerima'], $name = '', $address = "single");
    }
  }
}


if ($update) {
  echo "<script language='javascript'>";
  echo "alert('$notifikasi')";
  echo "</script>";
  echo "<script> document.location.href='view-finance.php?code=" . $noid . "'; </script>";
} else {
  echo "<script language='javascript'>";
  echo "alert('Bayar Budget Gagal')";
  echo "</script>";
  echo "<script> document.location.href='view-finance.php?code=" . $noid . "'; </script>";
}
