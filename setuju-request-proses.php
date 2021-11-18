<?php
session_start();
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
require "vendor/email/send-email.php";
require "dompdf/save-document.php";
require_once("dompdf/dompdf_config.inc.php");

$user = $_SESSION['nama_user'];
$time = date("Y-m-d h:i:sa");
$id = $_GET['id'];
$queryWaktu = mysqli_query($koneksi, "SELECT waktu FROM pengajuan_request WHERE id=$id") or die(mysqli_error($koneksi));
$waktu = mysqli_fetch_array($queryWaktu)[0];
$gPengaju = '';
$gNamaProject = '';
$gTotalBudget = '';
$gPembuat = $_SESSION['nama_user'];

$duplicateStatus = 0;

$updatePengajuanRequest = mysqli_query($koneksi, "UPDATE pengajuan_request SET status_request='Disetujui', waktu='$waktu', on_revision_status = '0' WHERE waktu='$waktu'") or die(mysqli_error($koneksi));

if ($updatePengajuanRequest) {
    $cariwaktunya = mysqli_query($koneksi, "SELECT waktu FROM pengajuan_request WHERE id='$id' ORDER BY id DESC LIMIT 1");
    $waktu = mysqli_fetch_assoc($cariwaktunya);
    $waktunya = $waktu['waktu'];

    $queryGetAllId = mysqli_query($koneksi, "SELECT id FROM pengajuan_request WHERE waktu='$waktunya'");
    while ($row = mysqli_fetch_array($queryGetAllId)) {

        $id = $row['id'];
        $queryPengajuanRequest = mysqli_query($koneksi, "SELECT * FROM pengajuan_request WHERE id='$id'") or die(mysqli_error($koneksi));
        $pengajuanRequest = mysqli_fetch_assoc($queryPengajuanRequest);
        $jenis = $pengajuanRequest['jenis'];
        $nama = $pengajuanRequest['nama'];
        $gNamaProject = $nama;
        $tahun = $pengajuanRequest['tahun'];
        $pengaju = $pengajuanRequest['pengaju'];
        $pembuat = $pengajuanRequest['pembuat'];
        $gPengaju = $pengaju;
        $divisi = $pengajuanRequest['divisi'];
        $kodepro = $pengajuanRequest['kode_project'];
        $totalbudget = $pengajuanRequest['totalbudget'];
        $gTotalBudget = $totalbudget;
        $document = $pengajuanRequest['document'];

        $queryCheckData = mysqli_query($koneksi, "SELECT COUNT(*) AS count_check FROM pengajuan WHERE kodeproject = '$kodepro' AND waktu = '$waktunya'");
        $checkData = mysqli_fetch_assoc($queryCheckData);
        if ($checkData['count_check'] == 0) {
            $insertkepengaju = mysqli_query($koneksi, "INSERT INTO pengajuan(jenis,nama,tahun,pengaju,divisi,status,kodeproject, totalbudget, pembuat, waktu, penyetuju, date_approved, document, on_revision_status)
                                                        VALUES ('$jenis','$nama','$tahun','$pengaju','$divisi','Disetujui','$kodepro', '$totalbudget', '$pembuat', '$waktunya', '$user', '$time', '$document', '0')") or die(mysqli_error($koneksi));
        } else {
            $duplicateStatus = 1;
        }
    }

    if (!$insertkepengaju && $duplicateStatus == 1) {
        $updatePengajuanRequestDouble = mysqli_query($koneksi, "UPDATE pengajuan_request SET ket='Data Double', waktu='$waktu[waktu]', on_revision_status = '0' WHERE waktu='$waktu[waktu]'") or die(mysqli_error($koneksi));
        // var_dump($waktu);
        // var_dump($updatePengajuanRequestDouble);
        // die;
        if ($_SESSION['divisi'] == 'FINANCE') {
            echo "<script language='javascript'>";
            echo "alert('Proses dihentikan, Terindikasi data double')";
            echo "</script>";
            echo "<script> document.location.href='home-finance.php'; </script>";
        } else {
            echo "<script language='javascript'>";
            echo "alert('Proses dihentikan, Terindikasi data double')";
            echo "</script>";
            echo "<script> document.location.href='home-direksi.php'; </script>";
        }
        die;
    }
    $querySelesaiRequest = mysqli_query($koneksi, "SELECT * FROM selesai_request WHERE waktu = '$waktunya' AND rincian != ''") or die(mysqli_error($koneksi));
    $selesaiRequest = [];
    $checkName = [];
    while ($row = mysqli_fetch_assoc($querySelesaiRequest)) {
        if (!in_array($row["rincian"], $checkName)) {
            array_push($selesaiRequest, $row);
            array_push($checkName, $row['rincian']);
        }
    }
    // var_dump($selesaiRequest);
    // die;
    $countTrue = 0;
    foreach ($selesaiRequest as $sr) {
        $urutan = $sr['urutan'];
        $rincian = $sr['rincian'];
        $kota = $sr['kota'];
        $status = $sr['status'];
        $penerima = $sr['penerima'];
        $harga = $sr['harga'];
        $quantity = $sr['quantity'];
        $total = $sr['total'];
        $pengaju = $sr['pengaju'];
        $divisi = $sr['divisi'];


        if ($rincian != "") {
            $insertSelesai = mysqli_query($koneksi, "INSERT INTO selesai (no,rincian,kota,status,penerima,harga,quantity,total,pembayaran,pengaju,divisi,waktu,komentar,uangkembaliused)
                                                    VALUES ('$urutan','$rincian','$kota','$status','$penerima','$harga','$quantity','$total','','$pengaju','$divisi','$waktunya','','')") or die(mysqli_error($koneksi));
        }
        if ($insertkepengaju) $countTrue++;
    }
    if ($countTrue >= count($selesaiRequest)) {

        $email = [];
        $nama = [];
        $queryGetEmail = mysqli_query($koneksi, "SELECT email,divisi,nama_user from tb_user WHERE nama_user='$gPengaju' AND aktif='Y'");
        $data = mysqli_fetch_assoc($queryGetEmail);
        $divisi = $data['divisi'];
        array_push($email, $data['email']);
        array_push($nama, $data['nama_user']);

        $queryUserByDivisi = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi = '$data[divisi]' AND (level = 'Manager' OR level = 'Senior Manager') AND aktif='Y'") or die(mysqli_error($koneksi));
        $user = mysqli_fetch_assoc($queryUserByDivisi);
        array_push($email, $user['email']);
        array_push($nama, $user['nama_user']);

        $url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        $url = explode('/', $url);
        $url = $url[0] . '/' . $url[1] . '/' . 'login.php';

        $msg = "Dear $gPengaju, <br><br>
        Budget dengan keterangan berikut:<br><br>
        Nama Project    : <strong>$gNamaProject</strong><br>
        Pengaju         : <strong>$gPengaju</strong><br>
        Divisi          : <strong>$divisi</strong><br>
        Total Budget    : <strong>Rp. " . number_format($gTotalBudget, 0, '', ',') . "</strong><br><br>
        
        Telah disetujui oleh <strong> $gPembuat </strong> pada <strong> " . date("d/m/Y H:i:s") . "</strong><br><br>
        ";

        $msg .= "Klik <a href='$url'>Disini</a> untuk membuka aplikasi budget.";
        $subject = "Notifikasi Untuk Penyetujuan Budget";
        if ($email) {
            $message = sendEmail($msg, $subject, $email, $name, $address = "multiple");
        }

        $notification = 'Persetujuan Berhasil. Pemberitahuan via email telah terkirim ke ';
        for ($i = 0; $i < count($email); $i++) {
            $notification .= ($nama[$i] . ' (' . $email[$i] . ')');
            if ($i < count($email) - 1) $notification .= ', ';
            else $notification .= '.';
        }

        $queryDocument = mysqli_query($koneksi, "SELECT noid, document FROM pengajuan ORDER BY noid DESC LIMIT 1");
        $document = mysqli_fetch_assoc($queryDocument);
        $doc = unserialize($document['document']);
        $noid = $document['noid'];

        if (is_array($doc)) {
            $name = $doc[count($doc) - 1];
        } else {
            $name = $doc;
        }

        saveDocApproved($koneksi, $noid, $name);

        // SELECT waktu FROM pengajuan_request WHERE id='$id' ORDER BY id DESC LIMIT 1
        if ($_SESSION['divisi'] == 'FINANCE') {
            echo "<script language='javascript'>";
            echo "alert('$notification')";
            echo "</script>";
            echo "<script> document.location.href='home-finance.php'; </script>";
        } else {
            echo "<script language='javascript'>";
            echo "alert('$notification')";
            echo "</script>";
            echo "<script> document.location.href='home-direksi.php'; </script>";
        }
    } else {
        if ($_SESSION['divisi'] == 'FINANCE') {
            echo "<script language='javascript'>";
            echo "alert('Persetujuan Gagal')";
            echo "</script>";
            echo "<script> document.location.href='home-finance.php'; </script>";
        } else {
            echo "<script language='javascript'>";
            echo "alert('Persetujuan Gagal')";
            echo "</script>";
            echo "<script> document.location.href='home-direksi.php'; </script>";
        }
    }
} else {
    if ($_SESSION['divisi'] == 'FINANCE') {
        echo "<script language='javascript'>";
        echo "alert('Persetujuan Gagal')";
        echo "</script>";
        echo "<script> document.location.href='home-finance.php'; </script>";
    } else {
        echo "<script language='javascript'>";
        echo "alert('Persetujuan Gagal')";
        echo "</script>";
        echo "<script> document.location.href='home-direksi.php'; </script>";
    }
}
