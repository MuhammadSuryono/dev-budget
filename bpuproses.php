<?php
error_reporting(0);
require "application/config/database.php";
require_once "application/config/message.php";
require_once "application/config/whatsapp.php";
require_once "application/config/email.php";
require_once "application/controllers/Cuti.php";
require_once "application/config/messageEmail.php";

$cuti = new Cuti();

$helper = new Message();
$emailHelper = new Email();
$mssageEmail = new MessageEmail();

$con = new Database();
$koneksi = $con->connect();
// require "vendor/email/send-email.php";

//error_reporting(0);
session_start();
if (!isset($_SESSION['nama_user'])) {
  header("location:login.php");
  // die('location:login.php');//jika belum login jangan lanjut
}

$querySetting = mysqli_query($koneksi, "SELECT * FROM setting_budget WHERE keterangan = 'approval_bpu'") or die(mysqli_error($koneksi));
$setting = mysqli_fetch_assoc($querySetting);

$time = date("Y-m-d H:i:s");
$url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$port = $_SERVER['SERVER_PORT'];
$url = explode('/', $url);
$hostProtocol = $url[0];
if ($port != "") {
$hostProtocol = $hostProtocol . ":" . $port;
}
$host = $hostProtocol. '/'. $url[1];
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
  $tanggalJatuhTempo = $_POST['tanggal_jatuh_tempo'];
  $namapenerima = $_POST['namapenerima'];

//  if ($statusbpu == 'UM' || $statusbpu == 'UM Burek') {
//    $queryRekening = mysqli_query($koneksi, "SELECT * FROM rekening WHERE no=$id_rekening");
//    $rekening = mysqli_fetch_assoc($queryRekening);
//
//    $queryTbUser = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE id_user = '$rekening[user_id]'");
//    $tbUser = mysqli_fetch_assoc($queryTbUser);
//    $namapenerima = $tbUser['nama_user'];
//  } else {
//    $namapenerima = $_POST['namapenerima'];
//  }

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


  $duplicates = [];

  $caribayar = mysqli_query($koneksi, "SELECT status FROM bpu WHERE waktu='$waktu' AND no='$no' AND status='Belum Di Bayar'");

  $saldo = mysqli_query($koneksi, "SELECT saldo FROM tb_user WHERE nama_user='$namapenerima'");
  $sld = mysqli_fetch_assoc($saldo);
  $saldobpu = $sld['saldo'];

  $query2 = "SELECT sum(jumlah) AS sumi FROM bpu WHERE namapenerima='$namapenerima' AND statusbpu IN ('UM', 'UM Burek') AND status IN ('Telah Di Bayar', 'Belum Di Bayar')";
  $result2 = mysqli_query($koneksi, $query2);
  $row2 = mysqli_fetch_array($result2);
  $totalUm = $row2['sumi'];

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
      $insert = mysqli_query($koneksi, "INSERT INTO bpu (no,pengajuan_jumlah,namabank,norek,namapenerima,bank_account_name,pengaju,divisi,waktu,status,persetujuan,term,statusbpu,fileupload, status_pengajuan_bpu,batas_tanggal_bayar,emailpenerima, rekening_id,tanggalbayar,created_at) VALUES
                                                ('$no','$jumlah','$namabank','$norek','$namapenerima', '$namapenerima','$pengaju','$divisi','$waktu','Belum Di Bayar','Belum Disetujui','$termfinal','$statusbpu','$nama_gambar', '1', '$tanggalBatasBayar', '$emailpenerima', '$id_rekening', '$tanggal_bayar' ,'$time')") or die(mysqli_error($koneksi));
      $idBpu = mysqli_insert_id($koneksi);
      
      $insert = mysqli_query($koneksi, "INSERT INTO tb_jatuh_tempo (id_bpu, tanggal_jatuh_tempo) VALUES ('$idBpu', '$tanggalJatuhTempo')") or die(mysqli_error($koneksi));
    } else {
      $insert = mysqli_query($koneksi, "INSERT INTO bpu (no,pengajuan_jumlah,namabank,norek,namapenerima,bank_account_name,pengaju,divisi,waktu,status,persetujuan,term,statusbpu,fileupload, status_pengajuan_bpu,batas_tanggal_bayar,emailpenerima, rekening_id,tanggalbayar,created_at) VALUES
                                                ('$no','$jumlah','$namabank','$norek','$namapenerima', '$namapenerima','$pengaju','$divisi','$waktu','Belum Di Bayar','Belum Disetujui','$termfinal','$statusbpu','$nama_gambar', '3', '$tanggalBatasBayar', '$emailpenerima', '$id_rekening','$tanggal_bayar' ,'$time')") or die(mysqli_error($koneksi));
      $idBpu = mysqli_insert_id($koneksi);
      $insert = mysqli_query($koneksi, "INSERT INTO tb_jatuh_tempo (id_bpu, tanggal_jatuh_tempo) VALUES ('$idBpu', '$tanggalJatuhTempo')") or die(mysqli_error($koneksi));
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
      $phoneNumbers = [];
      $nama = [];
      $idUsersNotification = [];
      $dataLevel = [];
      $dataDivisi = [];
      $emails = [];

      if ($divisi == 'FINANCE') {
        $insert = mysqli_query($koneksi, "INSERT INTO bpu (no,pengajuan_jumlah,namabank,norek,namapenerima,bank_account_name,pengaju,divisi,waktu,status,persetujuan,term,statusbpu,fileupload, status_pengajuan_bpu,batas_tanggal_bayar,emailpenerima, rekening_id,tanggalbayar,created_at) VALUES
                                                  ('$no','$jumlah','$namabank','$norek','$namapenerima', '$namapenerima','$pengaju','$divisi','$waktu','Belum Di Bayar','Belum Disetujui','$termfinal','$statusbpu','$nama_gambar', '1', '$tanggalBatasBayar', '$emailpenerima', '$id_rekening', '$tanggal_bayar' ,'$time')") or die(mysqli_error($koneksi));
        $idBpu = mysqli_insert_id($koneksi);
        
        $insert = mysqli_query($koneksi, "INSERT INTO tb_jatuh_tempo (id_bpu, tanggal_jatuh_tempo) VALUES ('$idBpu', '$tanggalJatuhTempo')") or die(mysqli_error($koneksi));
        $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('1','2', '3')");
        while ($e = mysqli_fetch_assoc($queryEmail)) {
          if (@unserialize($e['hak_button'])) {
            $buttonAkses = unserialize($e['hak_button']);
            if (in_array("verifikasi_bpu", $buttonAkses)) {
              if ($e['phone_number'] && !in_array($e["phone_number"], $duplicates)) {
                array_push($phoneNumbers, $e['phone_number']);
                array_push($nama, $e['nama_user']);
                array_push($idUsersNotification, $e['id_user']);
                array_push($dataLevel, $e['level']);
                array_push($dataDivisi, $e['divisi']);
                array_push($emails, $e['email']);
                array_push($duplicates, $e['phone_number']);
              }
            }
          }
        }

        $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE nama_user = '$uc[pembuat]' AND aktif='Y'");
        $emailUser = mysqli_fetch_assoc($queryEmail);
        if ($emailUser && !in_array($e["phone_number"], $duplicates)) {
          array_push($phoneNumbers, $e['phone_number']);
          array_push($nama, $e['nama_user']);
          array_push($idUsersNotification, $e['id_user']);
          array_push($dataLevel, $e['level']);
          array_push($dataDivisi, $e['divisi']);
          array_push($emails, $e['email']);
          array_push($duplicates, $e['phone_number']);
        }
      } else {
        $insert = mysqli_query($koneksi, "INSERT INTO bpu (no,pengajuan_jumlah,namabank,norek,namapenerima,bank_account_name, pengaju,divisi,waktu,status,persetujuan,term,statusbpu,fileupload, status_pengajuan_bpu,batas_tanggal_bayar,emailpenerima, rekening_id,tanggalbayar,created_at) VALUES
                                                ('$no','$jumlah','$namabank','$norek','$namapenerima', '$namapenerima', '$pengaju','$divisi','$waktu','Belum Di Bayar','Belum Disetujui','$termfinal','$statusbpu','$nama_gambar', '3', '$tanggalBatasBayar', '$emailpenerima', '$id_rekening', '$tanggal_bayar' ,'$time')") or die(mysqli_error($koneksi));
        $idBpu = mysqli_insert_id($koneksi);
        $insert = mysqli_query($koneksi, "INSERT INTO tb_jatuh_tempo (id_bpu, tanggal_jatuh_tempo) VALUES ('$idBpu', '$tanggalJatuhTempo')") or die(mysqli_error($koneksi));

        $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('1','2', '3')");
        while ($e = mysqli_fetch_assoc($queryEmail)) {
          if (@unserialize($e['hak_button'])) {
            $buttonAkses = unserialize($e['hak_button']);
            if (in_array("verifikasi_bpu", $buttonAkses)) {
              if ($e['phone_number'] && !in_array($e["phone_number"], $duplicates)) {
                array_push($phoneNumbers, $e['phone_number']);
                array_push($nama, $e['nama_user']);
                array_push($idUsersNotification, $e['id_user']);
                array_push($dataLevel, $e['level']);
                array_push($dataDivisi, $e['divisi']);
                  array_push($emails, $e['email']);
                  array_push($duplicates, $e['phone_number']);
              }
            }
          }
        }

        // $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE nama_user = '$pengaju' AND aktif='Y'");
        // $emailUser = mysqli_fetch_assoc($queryEmail);
        // if ($emailUser) {
        //   array_push($phoneNumbers, $emailUser['phone_number']);
        //   array_push($nama, $emailUser['nama_user']);
        //   array_push($idUsersNotification, $emailUser['id_user']);
        //   array_push($dataLevel, $emailUser['level']);
        //   array_push($dataDivisi, $emailUser['divisi']);
        //     array_push($emails, $e['email']);
        // }

        $queryUserByDivisi = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi = '$emailUser[divisi]' AND (level = 'Manager' OR level = 'Senior Manager') AND aktif='Y'");
        // $user = mysqli_fetch_array($queryUserByDivisi);
        while ($usr = mysqli_fetch_assoc($queryUserByDivisi)) {
          // foreach($user as $usr) {
            if (!in_array($e["phone_number"], $duplicates)) {
                array_push($phoneNumbers, $usr['phone_number']);
                array_push($nama, $usr['nama_user']);
                array_push($dataLevel, $usr['level']);
                array_push($idUsersNotification, $usr['id_user']);
                array_push($dataDivisi, $usr['divisi']);
                array_push($emails, $e['email']);
                array_push($duplicates, $e['phone_number']);
          // }
            }
        }
      }
      $queryProject = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE waktu='$waktu'");
      $dataProject = mysqli_fetch_assoc($queryProject);
      $namaProject = $dataProject['nama'];


      $queryUserPenerima = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE nama_user='$namapenerima'");
      $user = mysqli_fetch_assoc($queryUserPenerima);
      if ($user) {
          array_push($phoneNumbers, $user['phone_number']);
          array_push($nama, $user['nama_user']);
          array_push($dataLevel, $e['level']);
          array_push($idUsersNotification, $e['id_user']);
          array_push($dataDivisi, $e['divisi']);
          array_push($emails, $e['email']);
      }

      $notification .= "BPU telah berahasil dibuat, pemberitahuan dikirim via whatsapp ke " ;

      if (count($phoneNumbers) > 0) {
        $whatsapp = new Whastapp();
        for($i = 0; $i < count($phoneNumbers); $i++) {
          $path = '/views.php';
          if (!$cuti->checkStatusCutiUser($nama[$i])) {
            if ($nama[$i] != $_SESSION['nama_user']) {
              if ($dataDivisi[$i] == 'FINANCE') {
                $pathManager = ($dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager") && $dataProject['jenis'] == 'B1' ? '/view-finance-manager-b1.php' : '/view-finance-manager.php';
                $pathManager = ($dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager") && $dataProject['jenis'] == 'Non Rutin' ? '/view-finance-nonrutin-manager.php' : '/view-finance-manager.php';
                $pathKaryawan = ($dataLevel[$i] != "Manager" || $dataLevel[$i] != "Senior Manager") && $dataProject['jenis'] == 'Non Rutin' ? '/view-finance-nonrutin.php' : '/view-finance.php';
                $path =  $dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager" ? $pathManager : $pathKaryawan;
              } else if ($dataDivisi[$i] == 'Direksi') {
                  $path = '/views-direksi.php';
              }
              $notification .= ($nama[$i] . ' (' . $phoneNumbers[$i] . ')');
              if ($i < count($phoneNumbers) - 1) $notification .= ', ';
              else $notification .= '.';
              $url =  $host. $path.'?code='.$numb.'&session='.base64_encode(json_encode(["id_user" => $idUsersNotification[$i], "timeout" => time()]));
              $msg = $helper->messagePengajuanBPU($nama[$i], $pengaju, $namaProject, $namapenerima, $jumlah, $keterangan, $url);
              $msgEmail = $mssageEmail->applyBPU($nama[$i], $pengaju, $namaProject, $namapenerima, $jumlah, $keterangan, $url);
              if($phoneNumbers[$i] != "") $whatsapp->sendMessage($phoneNumbers[$i], $msg);
              if ($emails[$i] != "") $emailHelper->sendEmail($mssageEmail, "Informasi Pengajuan BPU", $emails[$i]);
            }
            
          }
        }
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
      $phoneNumbers = [];
      $nama = [];
      $idUsersNotification = [];
      $dataLevel = [];
      $dataDivisi = [];
      $emails = [];

      $selterm = mysqli_query($koneksi, "SELECT MAX(term) FROM bpu WHERE no='$no' AND waktu='$waktu'");
      $m = mysqli_fetch_assoc($selterm);
      $termterm = $m['MAX(term)'];
      $termfinal = $termterm + 1;

      if ($divisi == 'FINANCE') {
        $insert = mysqli_query($koneksi, "INSERT INTO bpu (no,pengajuan_jumlah,namabank,norek,namapenerima,bank_account_name,pengaju,divisi,waktu,status,persetujuan,term,statusbpu,fileupload, status_pengajuan_bpu, batas_tanggal_bayar,emailpenerima, rekening_id, created_at) VALUES
      ('$no','$jumlah','$namabank','$norek','$namapenerima', '$namapenerima', '$pengaju','$divisi','$waktu','Belum Di Bayar','Belum Disetujui','$termfinal','$statusbpu','$nama_gambar', '1', '$tanggalBatasBayar', '$emailpenerima', '$id_rekening' ,'$time')") or die(mysqli_error($koneksi));
        $idBpu = mysqli_insert_id($koneksi);
      
        $insert = mysqli_query($koneksi, "INSERT INTO tb_jatuh_tempo (id_bpu, tanggal_jatuh_tempo) VALUES ('$idBpu', '$tanggalJatuhTempo')") or die(mysqli_error($koneksi));
        $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('1', '3')");
        while ($e = mysqli_fetch_assoc($queryEmail)) {
          if (@unserialize($e['hak_button'])) {
            $buttonAkses = unserialize($e['hak_button']);
            if (in_array("verifikasi_bpu", $buttonAkses)) {
              if ($e['phone_number'] && !in_array($e["phone_number"], $duplicates)) {
                  array_push($phoneNumbers, $e['phone_number']);
                  array_push($nama, $e['nama_user']);
                  array_push($dataLevel, $e['level']);
                  array_push($idUsersNotification, $e['id_user']);
                  array_push($dataDivisi, $e['divisi']);
                  array_push($emails, $e['email']);
                  array_push($duplicates, $e['phone_number']);
                  
              }
            }
          }
        }

        $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE nama_user = '$uc[pembuat]' AND aktif='Y'");
        $emailUser = mysqli_fetch_assoc($queryEmail);
        if ($emailUser && !in_array($emailUser["phone_number"], $duplicates)) {
          array_push($phoneNumbers, $emailUser['phone_number']);
          array_push($nama, $emailUser['nama_user']);
          array_push($dataLevel, $emailUser['level']);
          array_push($idUsersNotification, $emailUser['id_user']);
          array_push($dataDivisi, $emailUser['divisi']);
            array_push($emails, $emailUser['email']);
            array_push($duplicates, $emailUser['phone_number']);
        }
      } else {
        $insert = mysqli_query($koneksi, "INSERT INTO bpu (no,pengajuan_jumlah,namabank,norek,namapenerima,bank_account_name,pengaju,divisi,waktu,status,persetujuan,term,statusbpu,fileupload, status_pengajuan_bpu, batas_tanggal_bayar,emailpenerima, rekening_id,created_at) VALUES
                                                ('$no','$jumlah','$namabank','$norek','$namapenerima', '$namapenerima', '$pengaju','$divisi','$waktu','Belum Di Bayar','Belum Disetujui','$termfinal','$statusbpu','$nama_gambar', '3', '$tanggalBatasBayar', '$emailpenerima', '$id_rekening' ,'$time')") or die(mysqli_error($koneksi));
        $idBpu = mysqli_insert_id($koneksi);

        $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('1','2', '3')");
        while ($e = mysqli_fetch_assoc($queryEmail)) {
          if (@unserialize($e['hak_button'])) {
            $buttonAkses = unserialize($e['hak_button']);
            if (in_array("verifikasi_bpu", $buttonAkses)) {
              if ($e['phone_number'] && !in_array($e["phone_number"], $duplicates)) {
                array_push($phoneNumbers, $e['phone_number']);
                array_push($nama, $e['nama_user']);
                array_push($dataLevel, $e['level']);
                array_push($idUsersNotification, $e['id_user']);
                array_push($dataDivisi, $e['divisi']);
                  array_push($emails, $e['email']);
                  array_push($duplicates, $e['phone_number']);
              }
            }
          }
        }
        
        $insert = mysqli_query($koneksi, "INSERT INTO tb_jatuh_tempo (id_bpu, tanggal_jatuh_tempo) VALUES ('$idBpu', '$tanggalJatuhTempo')") or die(mysqli_error($koneksi));
        // $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE nama_user = '$pengaju' AND aktif='Y'");
        // $emailUser = mysqli_fetch_assoc($queryEmail);
        // if ($emailUser) {
        //   array_push($phoneNumbers, $emailUser['phone_number']);
        //   array_push($nama, $emailUser['nama_user']);
        //   array_push($dataLevel, $e['level']);
        //   array_push($idUsersNotification, $e['id_user']);
        //   array_push($dataDivisi, $e['divisi']);
        //     array_push($emails, $e['email']);
        // }

        $queryUserByDivisi = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi = '$emailUser[divisi]' AND (level = 'Manager' OR level = 'Senior Manager') AND aktif='Y'");
        while ($user = mysqli_fetch_assoc($queryUserByDivisi)) {
          if ($e['phone_number'] && !in_array($e["phone_number"], $duplicates)) {
            array_push($phoneNumbers, $user['phone_number']);
            array_push($nama, $user['nama_user']);
            array_push($dataLevel, $e['level']);
            array_push($idUsersNotification, $e['id_user']);
            array_push($dataDivisi, $e['divisi']);
              array_push($emails, $e['email']);
            array_push($duplicates, $e['phone_number']);
          }
          
        }
      }

      $queryProject = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE waktu='$waktu'");
      $dataProject = mysqli_fetch_assoc($queryProject);
      $namaProject = $dataProject['nama'];

      $notification = 'Pembuatan BPU Berhasil. Pemberitahuan via whatsapp sedang dikirimkan ke ';
      $i = 0;
      if (count($phoneNumbers) > 0) {
        $whatsapp = new Whastapp();
        for($i = 0; $i < count($phoneNumbers); $i++) {
          $path = '/views.php';
          if (!$cuti->checkStatusCutiUser($nama[$i])) {
            if ($nama[$i] != $_SESSION['nama_user']) {
              if ($dataDivisi[$i] == 'FINANCE') {
                $pathManager = ($dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager") && $dataProject['jenis'] == 'B1' ? '/view-finance-manager-b1.php' : '/view-finance-manager.php';
                $pathManager = ($dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager") && $dataProject['jenis'] == 'Non Rutin' ? '/view-finance-nonrutin-manager.php' : '/view-finance-manager.php';
                $pathKaryawan = ($dataLevel[$i] != "Manager" || $dataLevel[$i] != "Senior Manager") && $dataProject['jenis'] == 'Non Rutin' ? '/view-finance-nonrutin.php' : '/view-finance.php';
                $path =  $dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager" ? $pathManager : $pathKaryawan;
            } else if ($dataDivisi[$i] == 'Direksi') {
                $path = '/views-direksi.php';
            }
            $notification .= ($nama[$i] . ' (' . $phoneNumbers[$i] . ')');
            if ($i < count($phoneNumbers) - 1) $notification .= ', ';
            else $notification .= '.';
            $url =  $host. $path.'?code='.$numb.'&session='.base64_encode(json_encode(["id_user" => $idUsersNotification[$i], "timeout" => time()]));
            $msg = $helper->messagePengajuanBPU($nama[$i], $pengaju, $namaProject, $namapenerima, $jumlah, $keterangan, $url);
            $msgEmail = $mssageEmail->applyBPU($nama[$i], $pengaju, $namaProject, $namapenerima, $jumlah, $keterangan, $url);
            if($phoneNumbers[$i] != "") $whatsapp->sendMessage($phoneNumbers[$i], $msg);
            if ($emails[$i] != "") $emailHelper->sendEmail($msgEmail, "Informasi Pengajuan BPU", $emails[$i]);
            }
          }
        }
      }

      
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
      $msg .= "Klik <a href='$host'>Disini</a> untuk membuka aplikasi budget.";
      $subject = "Notifikasi Aplikasi Budget";
      
      $emailHelper->sendEmail($msg, $subject, $arremailpenerima, '', 'multiple');

      $notification .= " Dan telah dikirim pemberitahuan ke penerima via email ke " . implode(",", $arremailpenerima);
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
