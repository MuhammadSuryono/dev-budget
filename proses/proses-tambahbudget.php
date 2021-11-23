<?php
require_once "../application/config/database.php";
require_once "../application/config/whatsapp.php";
require_once "../application/config/message.php";

$con = new Database();
$koneksi = $con->connect();

$helper = new Message();
// require "../vendor/email/send-email.php";

session_start();
if (!isset($_SESSION['nama_user'])) {
  header("location:../login.php");
}

if (isset($_POST['submit'])) {
  $jenis             = $_POST['jenis'];
  $namaProject             = $_POST['nama'];
  $tahun             = $_POST['tahun'];
  $status            = $_POST['status'];
  $idUserPICBudget         = $_POST['idUser'];
  $katnon            = $_POST['katnon'];
  $namaCreatorProject           = $_SESSION['nama_user'];
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

  if (!$idUserPICBudget) {
    if ($_SESSION['divisi'] == 'Direksi') {
      echo $helper->alertMessage("Pembuatan Budget Gagal, Data PIC Tidak ada","../home-direksi.php");
    } else if ($_SESSION['divisi'] == 'FINANCE' && $_SESSION['hak_akses']) {
      echo $helper->alertMessage("Pembuatan Budget Gagal, Data PIC Tidak ada","../home-finance.php");
    } else {
      echo $helper->alertMessage("Pembuatan Budget Gagal, Data PIC Tidak ada","../home.php");
    }
    die();
  }

  if ($jenis == 'B1') {
    if (!$namaProject || !$tahun || !$status || !$idUserPICBudget) {
      if ($_SESSION['divisi'] == 'Direksi') {
        echo $helper->alertMessage("Pembuatan Budget Gagal, Harap mengisi semua data.","../home-direksi.php");
      } else if ($_SESSION['divisi'] == 'FINANCE' && $_SESSION['hak_akses']) {
        echo $helper->alertMessage("Pembuatan Budget Gagal, Harap mengisi semua data.","../home-finance.php");
      } else {
        echo $helper->alertMessage("Pembuatan Budget Gagal, Harap mengisi semua data.","../home.php");
      }
    }
  }

  $phoneNumbers = [];
  $namaUserSendNotifications = [];
  $idUsersNotification = [];

  // Get data user Yang Mengajukan
  $queryCreator = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE id_user='$idUserPICBudget' AND aktif='Y'");
  $dataCreatorBudget = mysqli_fetch_assoc($queryCreator);
  $namaCreatorBudget           = $dataCreatorBudget['nama_user'];
  $divisiCreatorBudget            = $dataCreatorBudget['divisi'];

  // $phoneNumbers = $dataCreatorBudget['email'];
  array_push($phoneNumbers, $dataCreatorBudget['phone_number']);
  array_push($namaUserSendNotifications, $dataCreatorBudget['nama_user']);
  array_push($idUsersNotification, $idUserPICBudget);

  $queryUserByDivisi = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi = '$divisiCreatorBudget' AND (level = 'Manager' OR level = 'Senior Manager') AND aktif='Y'") or die(mysqli_error($koneksi));
  $user = mysqli_fetch_assoc($queryUserByDivisi);
  
  array_push($phoneNumbers, $user['phone_number']);
  array_push($namaUserSendNotifications, $user['nama_user']);
  array_push($idUsersNotification, $user['id_user']);


  $countInsert = 0;
  $insertPengajuanRequest = mysqli_query($koneksi, "INSERT INTO pengajuan_request(jenis, nama, tahun, pembuat, pengaju, divisi, totalbudget, status_request, kode_project, on_revision_status) VALUES (
                                            '$jenis', 
                                            '$namaProject', 
                                            '$tahun',
                                            '$namaCreatorProject',
                                            '$namaCreatorBudget',
                                            '$divisiCreatorBudget',
                                            '0',
                                            'Belum Di Ajukan',
                                            '$kode',
                                            '1')") or die(mysqli_error($koneksi));
              
  $idUserPICBudgetanRequest = mysqli_insert_id($koneksi);

  if ($insertPengajuanRequest) {
    $cariwaktunya = mysqli_query($koneksi, "SELECT waktu FROM pengajuan_request ORDER BY id DESC LIMIT 1");
    $waktu = mysqli_fetch_assoc($cariwaktunya);
    $waktunya = $waktu['waktu'];

    if ($jenis == 'B1') {
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
                                                      '$namaCreatorBudget',
                                                      '$divisiCreatorBudget',
                                                      '$waktunya')                           
                                                      ") or die(mysqli_error($koneksi));
      }
      $inserkeselesai5 = TRUE;
    } else if ($jenis == "B2") {
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
                                                      '$namaCreatorBudget',
                                                      '$divisiCreatorBudget',
                                                      '$waktunya')                           
                                                      ") or die(mysqli_error($koneksi));
      }
      $inserkeselesai5 = TRUE;
    } else if ($jenis == 'Rutin') {
      $sqlPengajuanRutin = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE jenis='Rutin' ORDER BY noid DESC LIMIT 1");
      $rutin = mysqli_fetch_assoc($sqlPengajuanRutin);
      $wakturutin = $rutin['waktu'];

      $queryCheckId = mysqli_query($koneksi, "SELECT id FROM pengajuan_request ORDER BY ID DESC LIMIT 1");
      $checkId = mysqli_fetch_assoc($queryCheckId)["id"];

      $rutinselesai = mysqli_query($koneksi, "SELECT * FROM selesai WHERE waktu='$wakturutin'");
      while ($rsnya = mysqli_fetch_array($rutinselesai)) {

        $rutininsert = mysqli_query($koneksi, "INSERT INTO selesai_request (urutan,id_pengajuan_request,rincian,kota,status,penerima,harga,quantity,total,pengaju,divisi)
                                                     VALUES ('$rsnya[no]', $checkId, '$rsnya[rincian]','$rsnya[kota]','$rsnya[status]','$rsnya[penerima]','$rsnya[harga]','$rsnya[quantity]',
                                                             '$rsnya[total]','$rsnya[pengaju]','$rsnya[divisi]')") or die(mysqli_error($koneksi));


      }

      $inserkeselesai5 = TRUE;
    } else {

      $inserkeselesai5 = TRUE;
    }
  }

  if ($inserkeselesai5) {

    $con->set_host_db(DB_HOST_DIGITALISASI_MARKETING);
    $con->set_name_db(DB_DIGITAL_MARKET);
    $con->init_connection();

    $koneksiDigitalMarket = $con->connect();

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
    $host = $url[0]. '/'. $url[1];

    if (count($phoneNumbers) > 0) {
      $whatsapp = new Whastapp();
      for($i = 0; $i < count($phoneNumbers); $i++) {
        $url =  $host. '/view-request.php?id='.$idUserPICBudgetanRequest.'&session='.base64_encode(json_encode(["id_user" => $idUsersNotification[$i], "timeout" => time()]));
        $msg = $helper->messageCreateProject($namaUserSendNotifications[$i], $namaUserSendNotifications[0], $namaCreatorProject, $namaProject, $divisiCreatorBudget, $url, "Notifikasi Pembukaan Akses Untuk Pengajuan Budget");
        if($phoneNumbers[$i] != "") $whatsapp->sendMessage($phoneNumbers[$i], $msg);
      }
    }


    $notification = "Pembuatan Permohonan Budget Berhasil. Pemberitahuan via whatsapp sedang dikirimkan ke $namaCreatorBudget ($phoneNumbers[0])";
    for ($i = 0; $i < count($phoneNumbers); $i++) {
      if ($phoneNumbers[$i] != "" && $i != 0) {
        $notification .= ($namaUserSendNotifications[$i] . ' (' . $phoneNumbers[$i] . ')');
        if ($i < count($phone) - 1) $notification .= ', ';
        else $notification .= '.';
      }
    }

    if ($_SESSION['divisi'] == 'Direksi') {
      echo $helper->alertMessage($notification, "../home-direksi.php");
    } else if ($_SESSION['divisi'] == 'FINANCE' && $_SESSION['hak_akses']) {
      echo $helper->alertMessage($notification, "../home-finance.php");
    } else {
      echo $helper->alertMessage($notification, "../home.php");
    }
  }
}
