<?php
require "../application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
require "../vendor/email/send-email.php";

session_start();
if (!isset($_SESSION['nama_user'])) {
  header("location:../lsaogin.php");
}

if (isset($_POST['submit'])) {

  $jenis             = $_POST['jenis'];
  $namaG             = $_POST['nama'];
  $tahun             = $_POST['tahun'];
  $status            = $_POST['status'];
  $idpengaju         = $_POST['idUser'];
  $katnon            = $_POST['katnon'];
  $pembuat           = $_SESSION['nama_user'];
  $idProject = $_POST['project'];
  $table = $_POST['table'];

  $arrNamaB1 = ['Honor Jakarta', 'Honor Luar Kota', 'STKB Transaksi Jakarta', 'STKB Transaksi Luar Kota', 'STKB OPS', 'Honor Area Head Jakarta', 'Honor Area Head Luar Kota'];
  $arrKotaB1 = ['Jabodetabek', 'Luar kota', 'Jabodetabek', 'Luar Kota', 'Jabodetabek dan Luar Kota', 'Jabodetabek', 'Luar Kota'];
  $arrStatusB1 = ['Honor Jakarta', 'Honor Luar Kota', 'STKB TRK Jakarta', 'STKB TRK Luar Kota', 'STKB OPS', 'Honor Area Head', 'Honor Area Head'];
  $arrPenerimaB1 = ['Shopper/PWT', 'Shopper/PWT', 'TLF', 'TLF', 'TLF', 'Area Head', 'Area Head'];

  $arrNamaB2 = ['Respondent Gift', 'Honor Interviewer'];
  $arrKotaB2 = ['Semua Kota', 'Semua Kota'];
  $arrStatusB2 = ['UM', 'Honor Eksternal'];
  $arrPenerimaB2 = ['Responden', 'Interviewer'];

  if (!$idpengaju) {
    if ($_SESSION['divisi'] == 'Direksi') {
      echo "<script language='javascript'>";
      echo "alert('Pembuatan Budget Gagal, Data PIC tidak ada.')";
      echo "</script>";
      echo "<script> document.location.href='../home-direksi.php'; </script>";
    } else if ($_SESSION['divisi'] == 'FINANCE' && $_SESSION['hak_akses']) {
      echo "<script language='javascript'>";
      echo "alert('Pembuatan Budget Gagal, Data PIC tidak ada.')";
      echo "</script>";
      echo "<script> document.location.href='../home-finance.php'; </script>";
    } else {
      echo "<script language='javascript'>";
      echo "alert('Pembuatan Budget Gagal, Data PIC tidak ada.')";
      echo "</script>";
      echo "<script> document.location.href='../home.php'; </script>";
    }
    die();
  }

  if ($jenis == 'B1') {
    if (!$namaG || !$tahun || !$status || !$idpengaju) {
      if ($_SESSION['divisi'] == 'Direksi') {
        echo "<script language='javascript'>";
        echo "alert('Pembuatan Budget Gagal, Harap mengisi semua data.')";
        echo "</script>";
        echo "<script> document.location.href='../home-direksi.php'; </script>";
      } else if ($_SESSION['divisi'] == 'FINANCE' && $_SESSION['hak_akses']) {
        echo "<script language='javascript'>";
        echo "alert('Pembuatan Budget Gagal, Harap mengisi semua data.')";
        echo "</script>";
        echo "<script> document.location.href='../home-finance.php'; </script>";
      } else {
        echo "<script language='javascript'>";
        echo "alert('Pembuatan Budget Gagal, Harap mengisi semua data.')";
        echo "</script>";
        echo "<script> document.location.href='../home.php'; </script>";
      }
    }
  }

  $email = [];
  $namaUserEmail = [];
  $namaDiv = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE id_user='$idpengaju' AND aktif='Y'");
  $cn = mysqli_fetch_assoc($namaDiv);
  $pengaju           = $cn['nama_user'];
  $divisi            = $cn['divisi'];
  // $email = $cn['email'];
  array_push($email, $cn['email']);
  array_push($namaUserEmail, $cn['nama_user']);

  $queryUserByDivisi = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi = '$divisi' AND (level = 'Manager' OR level = 'Senior Manager') AND aktif='Y'") or die(mysqli_error($koneksi));
  $user = mysqli_fetch_assoc($queryUserByDivisi);
  array_push($email, $user['email']);
  array_push($namaUserEmail, $user['nama_user']);

  $carirutinnya = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE jenis='Rutin' ORDER BY noid DESC LIMIT 1");
  $crnya = mysqli_fetch_assoc($carirutinnya);
  $wakturutin = $crnya['waktu'];
  $countInsert = 0;
  $insertPengajuanRequest = mysqli_query($koneksi, "INSERT INTO pengajuan_request(jenis, nama, tahun, pembuat, pengaju, divisi, totalbudget, status_request, kode_project, on_revision_status) VALUES (
                                            '$jenis', 
                                            '$namaG', 
                                            '$tahun',
                                            '$pembuat',
                                            '$pengaju',
                                            '$divisi',
                                            '0',
                                            'Belum Di Ajukan',
                                            '$kode',
                                            '1')") or die(mysqli_error($koneksi));


  if ($insertPengajuanRequest) {

    $cariwaktunya = mysqli_query($koneksi, "SELECT waktu FROM pengajuan_request ORDER BY id DESC LIMIT 1");
    $waktu = mysqli_fetch_assoc($cariwaktunya);
    $waktunya = $waktu['waktu'];

    if ($jenis == 'B1') {
      $countSelesai = 0;

      $queryCheckId = mysqli_query($koneksi, "SELECT id FROM pengajuan_request ORDER BY ID DESC LIMIT 1");
      $checkId = mysqli_fetch_assoc($queryCheckId)["id"];
      $checkId -= $j;
      for ($i = 0; $i  < count($arrNamaB1); $i++) {
        $nama = $arrNamaB1[$i];
        $kota = $arrKotaB1[$i];
        $status = $arrStatusB1[$i];
        $pUang = $arrPenerimaB1[$i];
        $checkId = (int)$checkId;
        $urutan = $i + 1;
        $insertSelesaiRequest = mysqli_query($koneksi, "INSERT INTO selesai_request(urutan, id_pengajuan_request, rincian, kota, status, penerima, harga, quantity, total, pengaju, divisi, waktu) VALUES(                             
                                                      '$urutan',
                                                      '$checkId',
                                                      '$nama',
                                                      '$kota',
                                                      '$status',
                                                      '$pUang',
                                                      '0',
                                                      '0',
                                                      '0',
                                                      '$pengaju',
                                                      '$divisi',
                                                      '$waktunya')                           
                                                      ") or die(mysqli_error($koneksi));
      }
      $inserkeselesai5 = TRUE;
    } else if ($jenis == "B2") {
      $countSelesai = 0;

      $queryCheckId = mysqli_query($koneksi, "SELECT id FROM pengajuan_request ORDER BY ID DESC LIMIT 1");
      $checkId = mysqli_fetch_assoc($queryCheckId)["id"];
      $checkId -= $j;
      for ($i = 0; $i  < count($arrNamaB2); $i++) {
        $nama = $arrNamaB2[$i];
        $kota = $arrKotaB2[$i];
        $status = $arrStatusB2[$i];
        $pUang = $arrPenerimaB2[$i];
        $checkId = (int)$checkId;
        $urutan = $i + 1;
        $insertSelesaiRequest = mysqli_query($koneksi, "INSERT INTO selesai_request(urutan, id_pengajuan_request, rincian, kota, status, penerima, harga, quantity, total, pengaju, divisi, waktu) VALUES(                             
                                                      '$urutan',
                                                      '$checkId',
                                                      '$nama',
                                                      '$kota',
                                                      '$status',
                                                      '$pUang',
                                                      '0',
                                                      '0',
                                                      '0',
                                                      '$pengaju',
                                                      '$divisi',
                                                      '$waktunya')                           
                                                      ") or die(mysqli_error($koneksi));
      }
      $inserkeselesai5 = TRUE;
    } else if ($jenis == 'Rutin') {
      $queryCheckId = mysqli_query($koneksi, "SELECT id FROM pengajuan_request ORDER BY ID DESC LIMIT 1");
      $checkId = mysqli_fetch_assoc($queryCheckId)["id"];

      // var_dump($wakturutin);
      $rutinselesai = mysqli_query($koneksi, "SELECT * FROM selesai WHERE waktu='$wakturutin'");
      while ($rsnya = mysqli_fetch_array($rutinselesai)) {

        $rutininsert = mysqli_query($koneksi, "INSERT INTO selesai_request (urutan,id_pengajuan_request,rincian,kota,status,penerima,harga,quantity,total,pengaju,divisi)
                                                     VALUES ('$rsnya[no]', $checkId, '$rsnya[rincian]','$rsnya[kota]','$rsnya[status]','$rsnya[penerima]','$rsnya[harga]','$rsnya[quantity]',
                                                             '$rsnya[total]','$rsnya[pengaju]','$rsnya[divisi]')") or die(mysqli_error($koneksi));



        // var_dump($rutininsert);
        // die;
      }
      // $queryTotalBudget = mysqli_query($koneksi, "SELECT totalbudget FROM pengajuan WHERE waktu = '$wakturutin'");
      // $totalBudget = mysqli_fetch_assoc($queryTotalBudget)['totalbudget'];
      // $updateTotalBudget = mysqli_query($koneksi, "UPDATE pengajuan_request SET totalbudget='$totalBudget' WHERE id='$checkId'") or die(mysqli_error($koneksiDigitalMarket));
      // var_dump($totalBudget);
      // var_dump($updateTotalBudget);
      // var_dump($checkId);
      // die;

      $inserkeselesai5 = TRUE;
    } else {

      $inserkeselesai5 = TRUE;
    }
  }

  if ($inserkeselesai5) {

    if ($table == "data_sindikasi") {
      $strArr = explode('-', $_POST['nama']);
      $nameProject = trim($strArr[0]);
      $method = trim($strArr[count($strArr) - 1]);

      $queryMethodology = mysqli_query($koneksiDigitalMarket, "SELECT * FROM data_methodology WHERE methodology = '$method'");
      $methodology = mysqli_fetch_assoc($queryMethodology);
      $idMethodology = $methodology['id_methodology'];

      $querySindikasi = mysqli_query($koneksiDigitalMarket, "SELECT * FROM data_sindikasi WHERE nama_project = '$nameProject'");
      $sindikasi = mysqli_fetch_assoc($querySindikasi);
      if ($sindikasi['on_budget']) {
        $arrOnBudget = unserialize($sindikasi['on_budget']);
      } else {
        $arrOnBudget = [];
      }
      array_push($arrOnBudget, $idMethodology);
      $result = serialize($arrOnBudget);
      mysqli_query($koneksiDigitalMarket, "UPDATE data_sindikasi SET on_budget='$result' WHERE nama_project='$nameProject'") or die(mysqli_error($koneksiDigitalMarket));
    } else {
      mysqli_query($koneksiDigitalMarket, "UPDATE comm_voucher SET on_budget='1' WHERE id_comm_voucher='$idProject'") or die(mysqli_error($koneksiDigitalMarket));
    }

    $url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    $url = explode('/', $url);
    $url = $url[0] . '/' . $url[1] . '/' . 'login.php';

    $msg = "Dear $pengaju, <br><br>
        Akses untuk pengajuan budget telah dibuka oleh <strong> $pembuat </strong> pada <strong> " . date("d/m/Y H:i:s") . "</strong> dengan keterangan sebagai berikut: <br><br>
        Nama Project    : <strong>$namaG</strong><br>
        PIC Budget      : <strong>$pengaju</strong><br>
        Divisi          : <strong>$divisi</strong><br><br>
        Silahkan ajukan budget secepatnya.<br><br>
        ";

    $msg .= "Klik <a href='$url'>Disini</a> untuk membuka aplikasi budget.";
    $subject = "Notifikasi Pembukaan Akses Untuk Pengajuan Budget";
    if ($email) {
      $message = sendEmail($msg, $subject, $email, $name, $address = "multiple");
    }

    $notification = 'Pembuatan Permohonan Budget Berhasil. Pemberitahuan via email telah terkirim ke ';
    for ($i = 0; $i < count($email); $i++) {
      $notification .= ($namaUserEmail[$i] . ' (' . $email[$i] . ')');
      if ($i < count($email) - 1) $notification .= ', ';
      else $notification .= '.';
    }
    // $notification = "Pembuatan Permohonan Budget Berhasil. Pemberitahuan via email telah terkirim ke $pengaju ($email)";

    if ($_SESSION['divisi'] == 'Direksi') {
      echo "<script language='javascript'>";
      echo "alert('$notification')";
      echo "</script>";
      echo "<script> document.location.href='../home-direksi.php'; </script>";
    } else if ($_SESSION['divisi'] == 'FINANCE' && $_SESSION['hak_akses']) {
      echo "<script language='javascript'>";
      echo "alert('$notification')";
      echo "</script>";
      echo "<script> document.location.href='../home-finance.php'; </script>";
    } else {
      echo "<script language='javascript'>";
      echo "alert('$notification')";
      echo "</script>";
      echo "<script> document.location.href='../home.php'; </script>";
    }
  }
}
