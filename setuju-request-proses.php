<?php
session_start();
require "application/config/database.php";
require_once "application/config/whatsapp.php";
require_once "application/config/message.php";

$helper = new Message();
$con = new Database();
$koneksi = $con->connect();
require "dompdf/save-document.php";
require_once("dompdf/dompdf_config.inc.php");

$user = $_SESSION['nama_user'];
$time = date("Y-m-d h:i:sa");
$id = $_GET['id'];
$queryWaktu = mysqli_query($koneksi, "SELECT * FROM pengajuan_request WHERE id=$id") or die(mysqli_error($koneksi));
$dataPengajuan = mysqli_fetch_assoc($queryWaktu);
$waktu = $dataPengajuan['waktu'];
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

        $phoneNumbers = [];
        $namaUserSendNotifications = [];
        $idUsersNotification = [];
        $dataDivisi = [];
        $dataLevel = [];
        $queryGetEmail = mysqli_query($koneksi, "SELECT phone_number,divisi,nama_user,id_user,level from tb_user WHERE nama_user='$gPengaju' AND aktif='Y'");
        $data = mysqli_fetch_assoc($queryGetEmail);
        $divisi = $data['divisi'];
        array_push($phoneNumbers, $data['phone_number']);
        array_push($namaUserSendNotifications, $data['nama_user']);
        array_push($idUsersNotification, $data['id_user']);
        array_push($dataDivisi, $data['divisi']);
        array_push($dataLevel, $data['level']);

        $queryUserByDivisi = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi = '$data[divisi]' AND (level = 'Manager' OR level = 'Senior Manager') AND aktif='Y'") or die(mysqli_error($koneksi));
        $user = mysqli_fetch_assoc($queryUserByDivisi);
        array_push($phoneNumbers, $user['phone_number']);
        array_push($namaUserSendNotifications, $user['nama_user']);
        array_push($idUsersNotification, $data['id_user']);
        array_push($dataDivisi, $data['divisi']);
        array_push($dataLevel, $data['level']);

        $url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        $port = $_SERVER['SERVER_PORT'];
        $url = explode('/', $url);
        $hostProtocol = $url[0];
        if ($port != "") {
        $hostProtocol = $hostProtocol . ":" . $port;
        }
        $host = $hostProtocol. '/'. $url[1];

        if (count($phoneNumbers) > 0) {
            $whatsapp = new Whastapp();
            for($i = 0; $i < count($phoneNumbers); $i++) {
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
              $msg = $helper->messagePersetujuanBudget($namaUserSendNotifications[$i], $pengaju, $gNamaProject, $divisi, $gTotalBudget, $gPembuat, $url);
              if($phoneNumbers[$i] != "") $whatsapp->sendMessage($phoneNumbers[$i], $msg);
            }
          }

        $notification = 'Persetujuan Berhasil. Pemberitahuan via whatsapp sedang dikirimkan ke ';
        for ($i = 0; $i < count($phoneNumbers); $i++) {
            $notification .= ($namaUserSendNotifications[$i] . ' (' . $phoneNumbers[$i] . ')');
            if ($i < count($phoneNumbers) - 1) $notification .= ', ';
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
