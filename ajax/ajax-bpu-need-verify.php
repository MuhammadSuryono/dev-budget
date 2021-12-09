<?php
session_start();
include_once '../application/config/database.php';
// include_once "../vendor/email/send-email.php";

$db = new Database();
$koneksi = $db->connect();


$db->set_name_db(DB_MRI_TRANSFER);
$db->init_connection();
$koneksiMriTransfer = $db->connect();


$action = $_GET['action'];

if ($action == 'get-data') {
    $query = mysqli_query($koneksi, "SELECT a.id, a.id_bpu, b.no as no_urut, c.nama, c.jenis, b.term FROM bpu_verify a LEFT JOIN bpu b ON a.id_bpu = b.noid LEFT JOIN pengajuan c ON c.waktu = b.waktu where a.is_verified = '0' ORDER BY a.id asc");
    echo json_encode(["data" => mysqli_fetch_array($query)]);
}

if ($action == 'get-data-single') {
    $id = $_GET['id'];
    $bpu = $_GET['id-bpu'];

    $query = mysqli_query($koneksi, "SELECT a.is_verified, b.tglcheck, b.checkby, a.id, a.id_bpu, b.no as no_urut, c.nama, c.jenis, b.term, b.pengajuan_jumlah FROM bpu_verify a LEFT JOIN bpu b ON a.id_bpu = b.noid LEFT JOIN pengajuan c ON c.waktu = b.waktu where a.id = '$id' AND a.id_bpu = '$bpu' ORDER BY a.id asc");
    echo json_encode(["data" => mysqli_fetch_array($query), "request" => ["id" => $id, "bpu" => $bpu]]);
}

// simpan-verifikasi&id=${id}&id-bpu=${idBpu}

if ($action == 'simpan-verifikasi') {
    $nominal = $_GET['nominal'];
    $id = $_GET['id'];
    $bpu = $_GET['id-bpu'];
    $today = date("Y-m-d H:i:s");

    
    $upload = uploadFile($_FILES);

    $query = mysqli_query($koneksiMriTransfer, "SELECT * FROM jenis_pembayaran WHERE jenispembayaran = '$statusBpu'") or die(mysqli_error($koneksiMriTransfer));
    $result = mysqli_fetch_assoc($query);

    $metode_pembayaran = "MRI Kas";
    if ($nominal < $result['max_transfer']) {
        $metode_pembayaran = "MRI PAL";
    }

    $update = mysqli_query($koneksi, "UPDATE bpu_verify SET created_by = '$_SESSION[id_user]', is_verified = '1', total_verify = '$nominal', document = '$upload[filename]' WHERE id = '$id'");
    $update = mysqli_query($koneksi, "UPDATE bpu SET metode_pembayaran = '$metode_pembayaran', jumlah = '$nominal', status_pengajuan_bpu = '0', fileupload = '$upload[filename]', checkby='$_SESSION[nama_user]', tglcheck = '$today' WHERE noid = '$bpu'");
    
    if ($upload['error'] == null) {
        $upload['is_success'] = true;
        // sendEmailPemeritahuan($koneksi, $bpu);
        echo json_encode($upload);
    } else {
        $upload['is_success'] = false;
        echo json_encode($upload);
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

function sendEmailPemeritahuan($koneksi, $idBpu) {
    $query = mysqli_query($koneksi, "SELECT a.*, b.* FROM bpu a LEFT JOIN pengajuan b ON a.waktu = b.waktu where a.noid = '$idBpu'");
    $dataBpu = $query->fetch_all(MYSQLI_ASSOC);
    
    if (count($dataBpu)) {
        $dataBpu = $dataBpu[0];
        $msg = "Notifikasi BPU, <br><br>
        BPU telah di verifikasi oleh Finance dengan keterangan sebagai berikut:<br><br>
        Nama Project   : <strong>" . $dataBpu['nama'] . "</strong><br>
        Item No.       : <strong>".$dataBpu['no']."</strong><br>
        Term           : <strong>".$dataBpu['term']."</strong><br>
        Nama Pengaju   : <strong>".$dataBpu['pengaju']."</strong><br>
        Nama Penerima  : <strong>".$dataBpu['namapenerima']."</strong><br>
        Total Diajukan : <strong>".$dataBpu['pengaju']."</strong><br>
        ";

        // $msg .= "Klik <a href='$url'>Disini</a> untuk membuka aplikasi budget.";
        $subject = "Notifikasi Aplikasi Budget";

        sendEmail($msg, $subject, $dataBpu['emailpenerima']);
    }

}