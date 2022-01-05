<?php
error_reporting(0);
session_start();
require "application/config/database.php";
require_once "application/config/whatsapp.php";
require_once "application/config/message.php";
require_once "application/config/email.php";


$emailHelper = new Email();

$con = new Database();
$koneksi = $con->connect();

$messageHelpper = new Message();
$wa = new Whastapp();

require "vendor/email/send-email.php";

$url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$url = explode('/', $url);
$url = $url[0] . '/' . $url[1] . '/' . 'login.php';

$user = $_SESSION['nama_user'];
$divisi = $_SESSION['divisi'];

$no           = $_POST['no'];
$term         = $_POST['term'];
$jumlahbayar  = $_POST['jumlahbayar'];
$nomorvoucher = $_POST['nomorvoucher'];
$tanggalbayar = $_POST['tanggalbayar'];
$waktu        = $_POST['waktu'];

if ($_FILES["gambar"]["name"]) {
    $extension = pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION);
    $nama_gambar = random_bytes(20) . "." . $extension;
    $target_file = "uploads/" . $nama_gambar;
    move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file);
}


$queryItemBudget = mysqli_query($koneksi, "SELECT * FROM selesai WHERE waktu = '$waktu' AND no = '$no'");
$itemBudget = mysqli_fetch_assoc($queryItemBudget);

$queryBudget = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE waktu = '$waktu'");
$budget = mysqli_fetch_assoc($queryBudget);
$idBudget = $budget['noid'];

if ($budget['jenis'] == 'Non Rutin') {
    $isNonRutin = '-nonrutin';
} else {
    $isNonRutin = '';
}

if ($budget['jenis'] == 'B1') {
    $isB1 = '-b1';
} else {
    $isB1 = '';
}

//periksa apakah udah submit
if (isset($_POST['submit'])) {
    $update = mysqli_query($koneksi, "UPDATE bpu SET status = 'Telah Di Bayar',
                                        jumlahbayar = '$jumlahbayar',
                                        novoucher = '$nomorvoucher',
                                        tanggalbayar = '$tanggalbayar',
                                        pembayar = '$user',
                                        dokumen_bukti_pembayaran= '$nama_gambar',
                                        divpemb = '$divisi' WHERE no='$no' AND waktu='$waktu' AND term='$term'");
}


$email = [];
$nama = [];
$arrPenerima = [];
$arrJumlah = [];
$arrPembayaran = [];
$pengaju = '';
$acknowledged = '';

$idUsersNotification = [];
$dataDivisi = [];
$dataLevel = [];
$arremailpenerima = [];


$url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$port = $_SERVER['SERVER_PORT'];
$url = explode('/', $url);
$hostProtocol = $url[0];
if ($port != "") {
$hostProtocol = $hostProtocol . ":" . $port;
}
$host = $hostProtocol. '/'. $url[1];

if ($update) {

    $queryBpu = mysqli_query($koneksi, "SELECT * FROM bpu WHERE no='$no' AND waktu='$waktu' AND term='$term'");
    while ($bpu = mysqli_fetch_assoc($queryBpu)) {
        array_push($arrPembayaran, $bpu['metode_pembayaran']);
        array_push($arrPenerima, $bpu['namapenerima']);
        array_push($arremailpenerima, $bpu['emailpenerima']);
        array_push($arrJumlah, "Rp. " . number_format($bpu['jumlah'], 0, ",", "."));
        $pengaju = $bpu['pengaju'];
        $acknowledged = $bpu['acknowledged_by'];

        $queryBank = mysqli_query($koneksi, "SELECT * FROM bank WHERE kodebank = '$bpu[namabank]'");
        $bank = mysqli_fetch_assoc($queryBank);
        if ($itemBudget['status'] == 'Vendor/Supplier' || $itemBudget['status'] == 'Honor Eksternal') {
            $explodeString = explode('.', $bpu['ket_pembayaran']);

            $dt = new DateTime($tanggalbayar);
            if ($itemBudget['status'] == 'Vendor/Supplier') {
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
                $message = $emailHelper->sendEmail($msg, $subject, $bpu['emailpenerima'], $name = '', $address = "single");
            }
        }
    }

    $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE nama_user = '$pengaju' AND aktif='Y'");
    $emailUser = mysqli_fetch_assoc($queryEmail);
    if ($emailUser) {
        array_push($email, $emailUser['phone_number']);
        array_push($nama, $emailUser['nama_user']);
        array_push($idUsersNotification, $emailUser['id_user']);
        array_push($dataDivisi, $emailUser['divisi']);
        array_push($dataLevel, $emailUser['level']);
    }

    $queryEmail = mysqli_query($koneksi, "SELECT *,divisi FROM tb_user WHERE nama_user = '$budget[pengaju]' AND aktif='Y'");
    $emailUser = mysqli_fetch_assoc($queryEmail);
    if ($emailUser) {
        array_push($email, $emailUser['phone_number']);
        array_push($nama, $emailUser['nama_user']);
        array_push($idUsersNotification, $emailUser['id_user']);
        array_push($dataDivisi, $emailUser['divisi']);
        array_push($dataLevel, $emailUser['level']);
    }

    $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE nama_user = '$acknowledged' AND aktif='Y'");
    $emailUser = mysqli_fetch_assoc($queryEmail);
    if ($emailUser) {
        array_push($email, $emailUser['phone_number']);
        array_push($nama, $emailUser['nama_user']);
        array_push($idUsersNotification, $emailUser['id_user']);
        array_push($dataDivisi, $emailUser['divisi']);
        array_push($dataLevel, $emailUser['level']);
    }

    if ($budget['jenis'] == 'B1' || $budget['jenis'] == 'B2') {
        $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('1', '3')");
        while ($e = mysqli_fetch_assoc($queryEmail)) {
            if ($e['phone_number']) {
                array_push($email, $e['phone_number']);
                array_push($nama, $e['nama_user']);
                array_push($idUsersNotification, $emailUser['id_user']);
                array_push($dataDivisi, $emailUser['divisi']);
                array_push($dataLevel, $emailUser['level']);
            }
        }
    } else {
        $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('2', '3')");
        while ($e = mysqli_fetch_assoc($queryEmail)) {
            if ($e['phone_number']) {
                array_push($email, $e['phone_number']);
                array_push($nama, $e['nama_user']);
                array_push($idUsersNotification, $emailUser['id_user']);
                array_push($dataDivisi, $emailUser['divisi']);
                array_push($dataLevel, $emailUser['level']);
            }
        }
    }

    $querUser = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE nama_user='$namapenerima' AND aktif='Y'");
    $user = mysqli_fetch_assoc($querUser);
    if ($user) {
        array_push($email, $user['phone_number']);
        array_push($nama, $user['nama_user']);
        array_push($idUsersNotification, $emailUser['id_user']);
        array_push($dataDivisi, $emailUser['divisi']);
        array_push($dataLevel, $emailUser['level']);
    }

    $notification = 'Bayar Budget Berhasil. Pemberitahuan via whatsapp telah terkirim ke ';
    $i = 0;
    for($i = 0; $i < count($email); $i++) {
        $path = '/views.php';
        if ($dataDivisi[$i] == 'FINANCE') {
            $pathManager = ($dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager") && $dataPengajuan['jenis'] == 'B1' ? '/view-finance-manager-b1.php' : '/view-finance-manager.php';
            $pathManager = ($dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager") && $dataPengajuan['jenis'] == 'Non Rutin' ? '/view-finance-nonrutin-manager.php' : '/view-finance-manager.php';
            $pathKaryawan = ($dataLevel[$i] != "Manager" || $dataLevel[$i] != "Senior Manager") && $dataPengajuan['jenis'] == 'Non Rutin' ? '/view-finance-nonrutin.php' : '/view-finance.php';
            $path =  $dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager" ? $pathManager : $pathKaryawan;
        } else if ($dataDivisi[$i] == 'Direksi') {
            $path = '/views-direksi.php';
        }
      $url =  $host. $path.'?code='.$id.'&session='.base64_encode(json_encode(["id_user" => $idUsersNotification[$i], "timeout" => time()]));
      $msg = $messageHelpper->messageStatusPembayaranBPUVendorSuplier($budget['nama'],$no,$term,$arrPenerima[$i], $user, $tanggalbayar, $nomorvoucher, $arrJumlah, $keterangan, $url);
      if($email[$i] != "") $wa->sendMessage($email[$i], $msg);
      $notification .= ($nama[$i] . ' (' . $email[$i] . ')');
        if ($i < count($email) - 1) $notification .= ', ';
        else $notification .= '.';
    }
    $notification = 'Bayar Budget Berhasil. Pemberitahuan via email telah terkirim ke ' . implode(",", $arremailpenerima);

    if ($_SESSION['hak_akses'] == 'Manager') {
        echo "<script language='javascript'>";
        echo "alert('$notification')";
        echo "</script>";
        echo "<script> document.location.href='view-finance" . $isNonRutin  . "-manager" . $isB1 . ".php?code=" . $idBudget . "'; </script>";
    } else {
        echo "<script language='javascript'>";
        echo "alert('$notification')";
        echo "</script>";
        echo "<script> document.location.href='view-finance" . $isNonRutin  . ".php?code=" . $idBudget . "'; </script>";
    }
} else {
    if ($_SESSION['hak_akses'] == 'Manager') {
        echo "<script language='javascript'>";
        echo "alert('Bayar Budget Gagal!')";
        echo "</script>";
        echo "<script> document.location.href='view-finance" . $isNonRutin  . "-manager" . $isB1 . ".php?code=" . $idBudget . "'; </script>";
    } else {
        echo "<script language='javascript'>";
        echo "alert('Bayar Budget Gagal!')";
        echo "</script>";
        echo "<script> document.location.href='view-finance" . $isNonRutin  . ".php?code=" . $idBudget . "'; </script>";
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