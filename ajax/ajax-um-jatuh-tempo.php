<?php
session_start();
include_once '../application/config/database.php';
// include_once "../vendor/email/send-email.php";

$db = new Database();
$koneksi = $db->connect();

$action = $_GET['action'];

if ($action == 'get-list') {
    $query = mysqli_query($koneksi, 'SELECT a.id, a.id_bpu, a.tanggal_jatuh_tempo, b.no as no_urut, b.term, c.nama, c.jenis FROM tb_jatuh_tempo a LEFT JOIN bpu b ON a.id_bpu = b.noid LEFT JOIN pengajuan c ON b.waktu = c.waktu
    WHERE a.tanggal_jatuh_tempo BETWEEN DATE_SUB(NOW(), INTERVAL 4 DAY) AND NOW() AND b.statusbpu LIKE "UM%" AND a.is_long_term = "0" AND b.status = "Telah Di Bayar" ORDER BY a.tanggal_jatuh_tempo desc');

    echo json_encode(["data" => $query->fetch_all(MYSQLI_ASSOC)]);
}

if ($action == 'direksi-get-list') {
    $query = mysqli_query($koneksi, 'SELECT a.id, a.id_bpu, a.tanggal_jatuh_tempo, a.tanggal_perpanjangan, b.no as no_urut, b.term, c.nama, c.jenis FROM tb_jatuh_tempo a LEFT JOIN bpu b ON a.id_bpu = b.noid LEFT JOIN pengajuan c ON b.waktu = c.waktu
    WHERE a.is_approval_long_term = "0" AND a.tanggal_perpanjangan <> "null" AND a.is_long_term = "1" AND a.is_disapprove_long_term = "0" ORDER BY a.tanggal_jatuh_tempo desc;');

    echo json_encode(["data" => $query->fetch_all(MYSQLI_ASSOC)]);
}

if ($action == 'update-jatuh-tempo') {
    $tanggalDiperpanjang = $_GET['tanggal-perpanjang'];
    $alasan = $_GET['reason'];
    $id = $_GET['id'];
    $bpu = $_GET['id-bpu'];

    $filename = '';
    $queryString = "UPDATE tb_jatuh_tempo SET is_long_term = '1', tanggal_perpanjangan = '$tanggalDiperpanjang'";
    if ($_FILES) {
        $upload = uploadFile($_FILES);
        $filename = $upload["filename"];
        $queryString .= "document = '$filename',";
    }

    $queryString .= "reason = '$alasan' WHERE id = '$id'";

    $update = mysqli_query($koneksi, $queryString);

    if ($update) {
        echo json_encode(["is_success" => true]);
    } else {
        echo json_encode(["is_success" => false, "data" => $queryString]);
    }
}

if ($action == 'approve-jatuh-tempo') {
    $tanggalDiperpanjang = $_GET['tanggal-perpanjang'];
    $approve = $_GET['approve'];
    $id = $_GET['id'];
    $bpu = $_GET['id-bpu'];

    $filename = '';
    $disApprove = '0';
    if ($approve == "0") {
        $disApprove = "1";
    }


    $queryString = "UPDATE tb_jatuh_tempo SET is_approval_long_term = '$approve', is_disapprove_long_term = '$disApprove' WHERE id = '$id'";
    $update = mysqli_query($koneksi, $queryString);

    if ($update && $approve == '1') {
        $insert = mysqli_query($koneksi, "INSERT INTO tb_jatuh_tempo (tanggal_jatuh_tempo, id_bpu) VALUES ('$tanggalDiperpanjang','$bpu')");
    }

    if ($update) {
        echo json_encode(["is_success" => true, "data" => "UPDATE tb_jatuh_tempo SET is_approval_long_term = '$approve', is_disapprove_long_term = '$disApprove' WHERE id = '$id'"]);
    } else {
        echo json_encode(["is_success" => false]);
    }
}

function uploadFile($files)
{
    $ekstensi_diperbolehkan	= array('png','jpg','jpeg','gif');
    $nama = $files['file']['name'];
    $x = explode('.', $nama);
    $ekstensi = strtolower(end($x));
    $ukuran	= $files['file']['size'];
    $file_tmp = $files['file']['tmp_name'];	
    if(in_array($ekstensi, $ekstensi_diperbolehkan) === true){
        if($ukuran < 1044070){			
            move_uploaded_file($file_tmp, '../fileupload/'.$nama);
            return ["error" => null, "filename" => $nama];
        }else{
            return ["error" => "Ukuran File terlalu besar", "filename" => $nama];
        }
    }else{
        return ["error" => "Ekstensi file tidak didukung", "filename" => $nama];
    }
}