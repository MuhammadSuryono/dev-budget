<?php
session_start();
include_once '../application/config/database.php';
require_once '../application/config/email.php';
require_once '../application/config/whatsapp.php';
require_once '../application/config/message.php';

$emailHelper = new Email();

$db = new Database();
$koneksi = $db->connect();

$message = new Message();
$wa = new Whastapp();

$db->set_name_db(DB_MRI_TRANSFER);
$db->init_connection();
$koneksiMriTransfer = $db->connect();

$namaInternal = [];
$emailInternal = [];
$idUserInternal = [];
$dataDivisi = [];
$dataLevel = [];

$action = $_GET['action'];

$url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$port = $_SERVER['SERVER_PORT'];
$url = explode('/', $url);
$hostProtocol = $url[0];
if ($port != "") {
$hostProtocol = $hostProtocol . ":" . $port;
}
$host = $hostProtocol. '/'. $url[1];

if ($action == 'get-data') {

    $where = '';
    if ($_SESSION['hak_akses'] == 'Level 2' && $_SESSION['jabatan'] == 'Koordinator') {
        $where = " AND c.jenis = 'Rutin'";
    }

    $query = mysqli_query($koneksi, "SELECT a.id, a.id_bpu, b.no as no_urut, c.nama, c.jenis, b.term FROM bpu_verify a LEFT JOIN bpu b ON a.id_bpu = b.noid LEFT JOIN pengajuan c ON c.waktu = b.waktu where a.is_need_approved = '0' && a.is_approved = '0' $where ORDER BY a.id asc");
    $data = [];
    while ($row = $query->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode(["data" => $data]);
}

if ($action == 'get-data-validasi') {
    $where = '';
    if ($_SESSION['hak_akses'] == 'Pegawai2' && $_SESSION['level'] == 'Koordinator') {
        $where = " AND c.jenis = 'Rutin'";
    }
    $query = mysqli_query($koneksi, "SELECT a.id, a.id_bpu, b.no as no_urut, c.nama, c.jenis, b.term FROM bpu_verify a LEFT JOIN bpu b ON a.id_bpu = b.noid LEFT JOIN pengajuan c ON c.waktu = b.waktu where a.is_verified = '1' AND a.is_need_approved = '1' AND a.is_approved = '0' $where ORDER BY a.id asc");
    $data = [];
    while ($row = $query->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode(["data" => $data]);
}

if ($action == 'get-data-single') {
    $id = $_GET['id'];
    $bpu = $_GET['id-bpu'];

    $query = mysqli_query($koneksi, "SELECT a.is_verified, a.is_need_approved, b.tglcheck, b.checkby, a.id, a.id_bpu, b.no as no_urut, c.nama, c.jenis, b.term, b.pengajuan_jumlah FROM bpu_verify a LEFT JOIN bpu b ON a.id_bpu = b.noid LEFT JOIN pengajuan c ON c.waktu = b.waktu where a.id = '$id' AND a.id_bpu = '$bpu' ORDER BY a.id asc");
    $data = [];
    while ($row = $query->fetch_assoc()) {
        $data[] = $row;
    }
    
    echo json_encode(["data" => $data, "request" => ["id" => $id, "bpu" => $bpu]]);
}

// simpan-verifikasi&id=${id}&id-bpu=${idBpu}

if ($action == 'simpan-verifikasi') {
    $nominal = $_GET['nominal'];
    $id = $_GET['id'];
    $bpu = $_GET['id-bpu'];
    $today = date("Y-m-d H:i:s");

    $queryBpu = mysqli_query($koneksi, "SELECT * FROM bpu where noid = '$bpu'");
    $dataBpu = mysqli_fetch_assoc($queryBpu);
    
    $upload = uploadFile($_FILES);

    $query = mysqli_query($koneksiMriTransfer, "SELECT * FROM jenis_pembayaran WHERE jenispembayaran = '$dataBpu[statusbpu]'") or die(mysqli_error($koneksiMriTransfer));
    $result = mysqli_fetch_assoc($query);

    $metode_pembayaran = "MRI Kas";
    if ($nominal < $result['max_transfer']) {
        $metode_pembayaran = "MRI PAL";
    }

    $time = date("Y-m-d H:i:s");

    $update = mysqli_query($koneksi, "UPDATE bpu_verify SET created_by = '$_SESSION[id_user]', is_verified = '1', total_verify = '$nominal', document = '$upload[filename]' WHERE id = '$id'");
    $update = mysqli_query($koneksi, "UPDATE bpu SET pengaju = '$_SESSION[nama_user]', divisi = '$_SESSION[divisi]', metode_pembayaran = '$metode_pembayaran', status_pengajuan_bpu = '0', fileupload = '$upload[filename]' WHERE noid = '$bpu'");

    if ($upload['error'] == null) {
        $upload['is_success'] = true;
        $upload['result'] = json_encode($result);
        $upload['bpu'] = $dataBpu;
        $upload['metode'] = $metode_pembayaran;
        $upload['max'] = $result['max_transfer'];
        $upload['nominal'] = $nominal < $result['max_transfer'];
        echo json_encode($upload);
    } else {
        $upload['is_success'] = false;
        echo json_encode($upload);
    }
}

if ($action == 'approval') {
    $id = $_GET['id'];
    $bpuId = $_GET['bpu'];
    $verify = $_GET['approval'];
    $dateNow = date("Y-m-d H:m:s");

    $update = mysqli_query($koneksi, "UPDATE bpu_verify SET is_approved = '1', is_need_approved = '0', status_approved = '$verify' WHERE id = '$id'");
    $update = mysqli_query($koneksi, "UPDATE bpu SET tglapprove='$dateNow', approveby='$_SESSION[nama_user]' where noid = '$bpuId'");

    // $queryBpu = mysqli_query($koneksi, "SELECT * from bpu where noid = '$bpuId'");
    // $dataBpu = [];
    // while($row = mysqli_fetch_assoc($queryBpu))
    // {
    //     $dataBpu = $row;
    // }

    // $queryPengajuan = mysqli_query($koneksi, "SELECT nama from bpu where noid = '$bpuId'");
    // $dataPengajuan = [];
    // while($row = mysqli_fetch_assoc($queryPengajuan))
    // {
    //     $dataPengajuan = $row;
    // }
    
    // $queryUser = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi = 'FINANCE' AND status_penerima_email_id = '3'");
    // while ($e = mysqli_fetch_assoc($queryUser)) {
    //     if ($e['phone_number']) {
    //         array_push($emailInternal, $e['phone_number']);
    //         array_push($namaInternal, $e['nama_user']);
    //         array_push($idUserInternal, $e['id_user']);
    //         array_push($dataDivisi, $e['divisi']);
    //         array_push($dataLevel, $e['level']);
    //     }
    // }

    // for ($i=0; $i < count($emailInternal); $i++) { 
    //     $url =  $host. '/view-bpu-verify.php?id='.$id.'&bpu='.$bpuId.'&session='.base64_encode(json_encode(["id_user" => $idUsersNotification[$i], "timeout" => time()]));
    //     $msg = $message->messagerequestProcessBPUFinance($namaInternal[$i], $dataBpu['no'], $dataBpu['pengaju'], $queryPengajuan['nama'], [$dataBpu['namapenerima']], [$dataBpu['jumlah']], "", $url);
    //     if ($emailInternal[$i] != "") {
    //         $wa->sendMessage($emailInternal[$i], $msg);
    //     }
    // }


    $upload['is_success'] = true;
    echo json_encode($upload);
}


function uploadFile($files)
{
    $ekstensi_diperbolehkan	= array('png','jpg','jpeg','gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx');
    $nama = $files['file']['name'];
    $x = explode('.', $nama);
    $ekstensi = strtolower(end($x));
    $ukuran	= $files['file']['size'];
    $file_tmp = $files['file']['tmp_name'];	
    if(in_array($ekstensi, $ekstensi_diperbolehkan) === true){
        if($ukuran < 1044070){			
            move_uploaded_file($file_tmp, '../document/'.$nama);
            return ["error" => null, "filename" => $nama];
        }else{
            return ["error" => "Ukuran File terlalu besar", "filename" => $nama];
        }
    }else{
        return ["error" => "Ekstensi file tidak didukung", "filename" => $nama];
    }
}