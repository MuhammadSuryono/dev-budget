<?php
//error_reporting(0);
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
require "vendor/email/send-email.php";

//error_reporting(0);
session_start();
if (!isset($_SESSION['nama_user'])) {
  header("location:login.php");
  // die('location:login.php');//jika belum login jangan lanjut
}

$querySetting = mysqli_query($koneksi, "SELECT * FROM setting_budget WHERE keterangan = 'approval_bpu'") or die(mysqli_error($koneksi));
$setting = mysqli_fetch_assoc($querySetting);

$time = date("Y-m-d H:i:s");

//periksa apakah udah submit
if (isset($_POST['submit'])) {

  $no           = $_POST['no'];
  $jumlah       = $_POST['jumlah'];
  $jumlah = (int) filter_var($jumlah, FILTER_SANITIZE_NUMBER_INT);
  $tglcair      = ($_POST['tglcair']) ? $_POST['tglcair'] : null;
  $arrnamabank     = $_POST['namabank'];
  $arrnorek        = $_POST['norek'];
  $id_rekening = $_POST['id_rekening'];
  $arremailpenerima = $_POST['email'];
  $pengaju      = $_POST['pengaju'];
  $divisi       = $_POST['divisi'];
  $waktu        = $_POST['waktu'];
  $metodePembayaran = $_POST['metodePembayaran'];
  $statusbpu    = $_POST['statusbpu'];
  $tanggal_bayar = $_POST['tanggal_bayar'];

  if ($statusbpu == 'UM' || $statusbpu == 'UM Burek') {
    $queryRekening = mysqli_query($koneksi, "SELECT * FROM rekening WHERE no=$id_rekening");
    $rekening = mysqli_fetch_assoc($queryRekening);

    $queryTbUser = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE id_user = '$rekening[user_id]'");
    $tbUser = mysqli_fetch_assoc($queryTbUser);
    $namapenerima = $tbUser['nama_user'];
  } else {
    $namapenerima = $_POST['namapenerima'];
  }
  // var_dump($namapenerima);
  // die;
  // for ($i = 0; $i < count($arrnamapenerima); $i++) {
  //   $namapenerima .= $arrnamapenerima[$i];
  //   if ($i < count($arrnamapenerima) - 1)
  //     $namapenerima .= ', ';
  // }
  for ($i = 0; $i < count($arrnorek); $i++) {
    $norek .= $arrnorek[$i];
    if ($i < count($arrnorek) - 1)
      $norek .= ', ';
  }
  for ($i = 0; $i < count($arrnamabank); $i++) {
    $namabank .= $arrnamabank[$i];
    if ($i < count($arrnamabank) - 1)
      $namabank .= ', ';
  }
  for ($i = 0; $i < count($arremailpenerima); $i++) {
    $emailpenerima .= $arremailpenerima[$i];
    if ($i < count($arremailpenerima) - 1)
      $emailpenerima .= ', ';
  }

  $extension = pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION);
  $nama_gambar = random_bytes(20) . "." . $extension;
  $target_file = "uploads/" . $nama_gambar;
  move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file);


  $sel1 = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE waktu='$waktu'");
  $uc = mysqli_fetch_assoc($sel1);
  $numb = $uc['noid'];

  if ($uc['jenis'] == 'Non Rutin') {
    $isNonRutin = '-nonrutin';
  } else {
    $isNonRutin = '';
  }

  // if($statusbpu == 'UM' ){

  $pilihtotal = mysqli_query($koneksi, "SELECT total FROM selesai WHERE no='$no' AND waktu='$waktu'");
  $aw = mysqli_fetch_assoc($pilihtotal);
  $hargaah = $aw['total'];
  $query = "SELECT sum(jumlah) AS sum FROM bpu WHERE no='$no' AND waktu='$waktu'";
  $result = mysqli_query($koneksi, $query);
  $row = mysqli_fetch_array($result);
  $total = $row[0];
  $query2 = "SELECT sum(realisasi) AS sum FROM bpu WHERE no='$no' AND waktu='$waktu'";
  $result2 = mysqli_query($koneksi, $query2);
  $row2 = mysqli_fetch_array($result2);
  $total2 = $row2[0];
  $query3 = "SELECT sum(uangkembali) AS sum FROM bpu WHERE no='$no' AND waktu='$waktu'";
  $result3 = mysqli_query($koneksi, $query3);
  $row3 = mysqli_fetch_array($result3);
  $total3 = $row3[0];
  $cobadulutot = $total - $total2;
  $jadinya = $hargaah - $total;

  $caribayar = mysqli_query($koneksi, "SELECT status FROM bpu WHERE waktu='$waktu' AND no='$no' AND status='Belum Di Bayar'");

  $saldo = mysqli_query($koneksi, "SELECT saldo FROM tb_user WHERE nama_user='$namapenerima'");
  $sld = mysqli_fetch_assoc($saldo);
  $saldobpu = $sld['saldo'];

  $query2 = "SELECT sum(jumlah) AS sumi FROM bpu WHERE namapenerima='$namapenerima' AND statusbpu IN ('UM', 'UM Burek') AND status IN ('Telah Di Bayar', 'Belum Di Bayar')";
  $result2 = mysqli_query($koneksi, $query2);
  $row2 = mysqli_fetch_array($result2);
  $totalUm = $row2['sumi'];

  // $query3 = "SELECT sum(realisasi) AS sumreal FROM bpu WHERE namapenerima='$namapenerima' AND statusbpu='UM Burek'";
  // $result3 = mysqli_query($koneksi, $query3);
  // $row3 = mysqli_fetch_array($result3);
  // $totalBurek = $row3['sumreal'];

  $saldosisa = $saldobpu - $totalUm;

  $carium = "SELECT * FROM bpu WHERE namapenerima='$namapenerima' AND status='Telah Di Bayar' AND statusbpu='UM'";
  $run_carium = mysqli_query($koneksi, $carium);

  if ((int) date('H') < 15)
    $tanggalBatasBayar = date('Y-m-d', strtotime('+ 2 days'));
  else
    $tanggalBatasBayar = date('Y-m-d', strtotime('+ 3 days'));

  $queryAplikasi = mysqli_query($koneksi, "SELECT * FROM daftar_aplikasi_pembayaran WHERE nama_aplikasi='$namapenerima'");
  if (mysqli_num_rows($queryAplikasi)) {
    $selterm = mysqli_query($koneksi, "SELECT MAX(term) FROM bpu WHERE no='$no' AND waktu='$waktu'");
    $m = mysqli_fetch_assoc($selterm);
    $termterm = $m['MAX(term)'];
    $termfinal = $termterm + 1;

    if ($divisi == 'FINANCE') {
      $insert = mysqli_query($koneksi, "INSERT INTO bpu (no,pengajuan_jumlah,namabank,norek,namapenerima,pengaju,divisi,waktu,status,persetujuan,term,statusbpu,fileupload, status_pengajuan_bpu,batas_tanggal_bayar,emailpenerima, rekening_id,tanggalbayar,created_at) VALUES
                                                ('$no','$jumlah','$namabank','$norek','$namapenerima','$pengaju','$divisi','$waktu','Belum Di Bayar','Belum Disetujui','$termfinal','$statusbpu','$nama_gambar', '1', '$tanggalBatasBayar', '$emailpenerima', '$id_rekening', '$tanggal_bayar' ,'$time')") or die(mysqli_error($koneksi));
    } else {
      $insert = mysqli_query($koneksi, "INSERT INTO bpu (no,pengajuan_jumlah,namabank,norek,namapenerima,pengaju,divisi,waktu,status,persetujuan,term,statusbpu,fileupload, status_pengajuan_bpu,batas_tanggal_bayar,emailpenerima, rekening_id,tanggalbayar,created_at) VALUES
                                                ('$no','$jumlah','$namabank','$norek','$namapenerima','$pengaju','$divisi','$waktu','Belum Di Bayar','Belum Disetujui','$termfinal','$statusbpu','$nama_gambar', '3', '$tanggalBatasBayar', '$emailpenerima', '$id_rekening','$tanggal_bayar' ,'$time')") or die(mysqli_error($koneksi));
    }
    echo "<script language='javascript'>";
    echo "alert('Pembuatan BPU Berhasil')";
    echo "</script>";

    if ($divisi == 'FINANCE') {
      if ($_SESSION['hak_akses'] == 'Manager') {
        echo "<script language='javascript'>";
        echo "alert('GAGAL!!, Kamu tidak bisa mengajukan lebih dari sisa Pembayaran')";
        echo "</script>";
        echo "<script> document.location.href='view-finance" . $isNonRutin  . "-manager.php?code=" . $numb . "'; </script>";
      } else {
        echo "<script language='javascript'>";
        echo "alert('GAGAL!!, Kamu tidak bisa mengajukan lebih dari sisa Pembayaran')";
        echo "</script>";
        echo "<script> document.location.href='view-finance" . $isNonRutin  . ".php?code=" . $numb . "'; </script>";
      }
    } else {
      echo "<script> document.location.href='views.php?code=" . $numb . "'; </script>";
    }
  }

  if ($statusbpu == 'UM' || $statusbpu == 'UM Burek') {
    if ($jumlah > $jadinya) {
      if ($divisi == 'FINANCE') {
        if ($_SESSION['hak_akses'] == 'Manager') {
          echo "<script language='javascript'>";
          echo "alert('GAGAL!!, Kamu tidak bisa mengajukan lebih dari sisa Pembayaran')";
          echo "</script>";
          echo "<script> document.location.href='view-finance" . $isNonRutin  . "-manager.php?code=" . $numb . "'; </script>";
        } else {
          echo "<script language='javascript'>";
          echo "alert('GAGAL!!, Kamu tidak bisa mengajukan lebih dari sisa Pembayaran')";
          echo "</script>";
          echo "<script> document.location.href='view-finance" . $isNonRutin  . ".php?code=" . $numb . "'; </script>";
        }
      } else {
        echo "<script language='javascript'>";
        echo "alert('GAGAL!!, Kamu tidak bisa mengajukan lebih dari sisa Pembayaran')";
        echo "</script>";
        echo "<script> document.location.href='views.php?code=" . $numb . "'; </script>";
      }
    } else if ($jumlah > $saldosisa) {
      if ($divisi == 'FINANCE') {
        if ($_SESSION['hak_akses'] == 'Manager') {
          echo "<script language='javascript'>";
          echo "alert('GAGAL!!, Saldo BPU $namapenerima Sisa Rp. " . number_format($saldosisa, 0, ",", ".") . ", Harap Segera Realisasikan untuk mengajukan BPU lebih besar')";
          echo "</script>";
          echo "<script> document.location.href='view-finance" . $isNonRutin  . "-manager.php?code=" . $numb . "'; </script>";
        } else {
          echo "<script language='javascript'>";
          echo "alert('GAGAL!!, Saldo BPU $namapenerima Sisa Rp. " . number_format($saldosisa, 0, ",", ".") . ", Harap Segera Realisasikan untuk mengajukan BPU lebih besar')";
          echo "</script>";
          echo "<script> document.location.href='view-finance" . $isNonRutin  . ".php?code=" . $numb . "'; </script>";
        }
      } else {
        echo "<script language='javascript'>";
        echo "alert('GAGAL!!, Saldo BPU $namapenerima Sisa Rp. " . number_format($saldosisa, 0, ",", ".") . ", Harap Segera Realisasikan untuk mengajukan BPU lebih besar')";
        echo "</script>";
        echo "<script> document.location.href='views.php?code=" . $numb . "'; </script>";
      }
    } else {
      $selterm = mysqli_query($koneksi, "SELECT MAX(term) FROM bpu WHERE no='$no' AND waktu='$waktu'");
      $m = mysqli_fetch_assoc($selterm);
      $termterm = $m['MAX(term)'];
      $termfinal = $termterm + 1;

      // var_dump($isNonRutin );
      // die;
      $email = [];
      $nama = [];

      if ($divisi == 'FINANCE') {
        $insert = mysqli_query($koneksi, "INSERT INTO bpu (no,pengajuan_jumlah,namabank,norek,namapenerima,pengaju,divisi,waktu,status,persetujuan,term,statusbpu,fileupload, status_pengajuan_bpu,batas_tanggal_bayar,emailpenerima, rekening_id,tanggalbayar,created_at) VALUES
                                                  ('$no','$jumlah','$namabank','$norek','$namapenerima','$pengaju','$divisi','$waktu','Belum Di Bayar','Belum Disetujui','$termfinal','$statusbpu','$nama_gambar', '1', '$tanggalBatasBayar', '$emailpenerima', '$id_rekening', '$tanggal_bayar' ,'$time')") or die(mysqli_error($koneksi));

        if ($uc['jenis'] == 'B1' || $uc['jenis'] == 'B2') {
          $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('1', '3')");
          while ($e = mysqli_fetch_assoc($queryEmail)) {
            if (@unserialize($e['hak_button'])) {
              $buttonAkses = unserialize($e['hak_button']);
              if (in_array("verifikasi_bpu", $buttonAkses)) {
                if ($e['email']) {
                  array_push($email, $e['email']);
                  array_push($nama, $e['nama_user']);
                }
              }
            }
          }
        } else {
          $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('1', '3')");
          while ($e = mysqli_fetch_assoc($queryEmail)) {
            if (@unserialize($e['hak_button'])) {
              $buttonAkses = unserialize($e['hak_button']);
              if (in_array("verifikasi_bpu", $buttonAkses)) {
                if ($e['email']) {
                  array_push($email, $e['email']);
                  array_push($nama, $e['nama_user']);
                }
              }
            }
          }
        }

        $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user,divisi FROM tb_user WHERE nama_user = '$uc[pembuat]' AND aktif='Y'");
        $emailUser = mysqli_fetch_assoc($queryEmail);
        if ($emailUser) {
          array_push($email, $emailUser['email']);
          array_push($nama, $emailUser['nama_user']);
        }
      } else {
        $insert = mysqli_query($koneksi, "INSERT INTO bpu (no,pengajuan_jumlah,namabank,norek,namapenerima,pengaju,divisi,waktu,status,persetujuan,term,statusbpu,fileupload, status_pengajuan_bpu,batas_tanggal_bayar,emailpenerima, rekening_id,tanggalbayar,created_at) VALUES
                                                ('$no','$jumlah','$namabank','$norek','$namapenerima','$pengaju','$divisi','$waktu','Belum Di Bayar','Belum Disetujui','$termfinal','$statusbpu','$nama_gambar', '3', '$tanggalBatasBayar', '$emailpenerima', '$id_rekening', '$tanggal_bayar' ,'$time')") or die(mysqli_error($koneksi));

        $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user,divisi FROM tb_user WHERE nama_user = '$pengaju' AND aktif='Y'");
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
      }
      $queryProject = mysqli_query($koneksi, "SELECT nama FROM pengajuan WHERE waktu='$waktu'");
      $namaProject = mysqli_fetch_array($queryProject)[0];

      $url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
      $url = explode('/', $url);
      $url = $url[0] . '/' . $url[1] . '/' . 'login.php';

      $msg = "Notifikasi BPU, <br><br>
        BPU telah diajukan dengan keterangan sebagai berikut:<br><br>
        Nama Project      : <strong>$namaProject</strong><br>
        Nama Pengaju      : <strong>$pengaju</strong><br>
        Nama Penerima     : <strong>$namapenerima</strong><br>
        Jumlah Diajukan   : <strong>Rp. " . number_format($jumlah, 0, '', ',') . "</strong><br>
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

      $notification = 'Pembuatan BPU Berhasil. Pemberitahuan via email telah terkirim ke ';
      $i = 0;
      for ($i = 0; $i < count($email); $i++) {
        $notification .= ($nama[$i] . ' (' . $email[$i] . ')');
        if ($i < count($email) - 1) $notification .= ', ';
        else $notification .= '.';
      }
    }
  } else {
    if ($jumlah > $jadinya) {
      if ($divisi == 'FINANCE') {
        if ($_SESSION['hak_akses'] == 'Manager') {
          echo "<script language='javascript'>";
          echo "alert('GAGAL!!, Kamu tidak bisa mengajukan lebih dari sisa Pembayaran')";
          echo "</script>";
          echo "<script> document.location.href='view-finance" . $isNonRutin  . "-manager.php?code=" . $numb . "'; </script>";
        } else {
          echo "<script language='javascript'>";
          echo "alert('GAGAL!!, Kamu tidak bisa mengajukan lebih dari sisa Pembayaran')";
          echo "</script>";
          echo "<script> document.location.href='view-finance" . $isNonRutin  . ".php?code=" . $numb . "'; </script>";
        }
      } else {

        echo "<script language='javascript'>";
        echo "alert('GAGAL!!, Kamu tidak bisa mengajukan lebih dari sisa Pembayaran')";
        echo "</script>";
        echo "<script> document.location.href='views.php?code=" . $numb . "'; </script>";
      }
    } else {
      $email = [];
      $nama = [];

      $selterm = mysqli_query($koneksi, "SELECT MAX(term) FROM bpu WHERE no='$no' AND waktu='$waktu'");
      $m = mysqli_fetch_assoc($selterm);
      $termterm = $m['MAX(term)'];
      $termfinal = $termterm + 1;

      if ($divisi == 'FINANCE') {
        $insert = mysqli_query($koneksi, "INSERT INTO bpu (no,pengajuan_jumlah,namabank,norek,namapenerima,pengaju,divisi,waktu,status,persetujuan,term,statusbpu,fileupload, status_pengajuan_bpu, batas_tanggal_bayar,emailpenerima, rekening_id, created_at) VALUES
      ('$no','$jumlah','$namabank','$norek','$namapenerima','$pengaju','$divisi','$waktu','Belum Di Bayar','Belum Disetujui','$termfinal','$statusbpu','$nama_gambar', '1', '$tanggalBatasBayar', '$emailpenerima', '$id_rekening' ,'$time')") or die(mysqli_error($koneksi));

        if ($uc['jenis'] == 'B1' || $uc['jenis'] == 'B2') {
          $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('1', '3')");
          while ($e = mysqli_fetch_assoc($queryEmail)) {
            if (@unserialize($e['hak_button'])) {
              $buttonAkses = unserialize($e['hak_button']);
              if (in_array("verifikasi_bpu", $buttonAkses)) {
                if ($e['email']) {
                  array_push($email, $e['email']);
                  array_push($nama, $e['nama_user']);
                }
              }
            }
          }
        } else {
          $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('2', '3')");
          while ($e = mysqli_fetch_assoc($queryEmail)) {
            if (@unserialize($e['hak_button'])) {
              $buttonAkses = unserialize($e['hak_button']);
              if (in_array("verifikasi_bpu", $buttonAkses)) {
                if ($e['email']) {
                  array_push($email, $e['email']);
                  array_push($nama, $e['nama_user']);
                }
              }
            }
          }
        }

        $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user,divisi FROM tb_user WHERE nama_user = '$uc[pembuat]' AND aktif='Y'");
        $emailUser = mysqli_fetch_assoc($queryEmail);
        if ($emailUser) {
          array_push($email, $emailUser['email']);
          array_push($nama, $emailUser['nama_user']);
        }
      } else {
        $insert = mysqli_query($koneksi, "INSERT INTO bpu (no,pengajuan_jumlah,namabank,norek,namapenerima,pengaju,divisi,waktu,status,persetujuan,term,statusbpu,fileupload, status_pengajuan_bpu, batas_tanggal_bayar,emailpenerima, rekening_id,created_at) VALUES
                                                ('$no','$jumlah','$namabank','$norek','$namapenerima','$pengaju','$divisi','$waktu','Belum Di Bayar','Belum Disetujui','$termfinal','$statusbpu','$nama_gambar', '3', '$tanggalBatasBayar', '$emailpenerima', '$id_rekening' ,'$time')") or die(mysqli_error($koneksi));

        $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user,divisi FROM tb_user WHERE nama_user = '$pengaju' AND aktif='Y'");
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
      }

      $queryProject = mysqli_query($koneksi, "SELECT nama FROM pengajuan WHERE waktu='$waktu'");
      $namaProject = mysqli_fetch_array($queryProject)[0];

      $url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
      $url = explode('/', $url);
      $url = $url[0] . '/' . $url[1] . '/' . 'login.php';

      $msg = "Notifikasi BPU, <br><br>
              BPU telah diajukan dengan keterangan sebagai berikut:<br><br>
              Nama Project      : <strong>$namaProject</strong><br>
              Nama Pengaju      : <strong>$pengaju</strong><br>
              Nama Penerima     : <strong>$namapenerima</strong><br>
              Jumlah Diajukan   : <strong>Rp. " . number_format($jumlah, 0, '', ',') . "</strong><br>
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

      $notification = 'Pembuatan BPU Berhasil. Pemberitahuan via email telah terkirim ke ';
      $i = 0;
      for ($i = 0; $i < count($email); $i++) {
        $notification .= ($nama[$i] . ' (' . $email[$i] . ')');
        if ($i < count($email) - 1) $notification .= ', ';
        else $notification .= '.';
      }
    }
  }

  if ($insert) {
    if ($divisi == 'FINANCE') {
      if ($_SESSION['hak_akses'] == 'Manager') {
        echo "<script language='javascript'>";
        echo "alert('$notification')";
        echo "</script>";
        echo "<script> document.location.href='view-finance" . $isNonRutin  . "-manager.php?code=" . $numb . "'; </script>";
      } else {
        echo "<script language='javascript'>";
        echo "alert('$notification')";
        echo "</script>";
        echo "<script> document.location.href='view-finance" . $isNonRutin  . ".php?code=" . $numb . "'; </script>";
      }
    } else {
      echo "<script language='javascript'>";
      echo "alert('$notification')";
      echo "</script>";
      echo "<script> document.location.href='views.php?code=" . $numb . "'; </script>";
    }
  } else {
    if ($divisi == 'FINANCE') {
      if ($_SESSION['hak_akses'] == 'Manager') {
        echo "<script language='javascript'>";
        echo "alert('Pembuatan BPU Gagal')";
        echo "</script>";
        echo "<script> document.location.href='view-finance" . $isNonRutin  . "-manager.php?code=" . $numb . "'; </script>";
      } else {
        echo "<script language='javascript'>";
        echo "alert('Pembuatan BPU Gagal')";
        echo "</script>";
        echo "<script> document.location.href='view-finance" . $isNonRutin  . ".php?code=" . $numb . "'; </script>";
      }
    } else {
      echo "<script language='javascript'>";
      echo "Pembuatan BPU Gagal";
      echo "</script>";
      echo "<script> document.location.href='views.php?code=" . $numb . "'; </script>";
    }
  }
}


function random_bytes($length = 6)
{
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $characters_length = strlen($characters);
  $output = '';
  for ($i = 0; $i < $length; $i++)
    $output .= $characters[rand(0, $characters_length - 1)];
  return $output;
}
