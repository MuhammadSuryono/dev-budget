<?php
error_reporting(0);
require_once "application/config/database.php";
require_once "application/config/message.php";
require_once "application/config/whatsapp.php";
require_once "application/config/email.php";
require_once "application/config/messageEmail.php";

$emailHelper = new Email();

$con = new Database();
$koneksi = $con->connect();
$con->load_database($koneksi);

$messageHelper = new Message();
$whatsapp = new Whastapp();
$messageEmail = new MessageEmail();

$con->set_name_db(DB_TRANSFER);
$con->init_connection();
$koneksiTransfer = $con->connect();

$con->set_name_db(DB_MRI_TRANSFER);
$con->init_connection();
$koneksiMriTransfer = $con->connect();

$con->set_name_db(DB_DEVELOP);
$con->init_connection();
$koneksiDevelop = $con->connect();


require "vendor/email/send-email.php";

session_start();
if (!isset($_SESSION['nama_user'])) {
    header("location:login.php");
}

$userSetuju = $_SESSION['nama_user'];
$divisi = $_SESSION['divisi'];
$aksesSes = $_SESSION['hak_akses'];
date_default_timezone_set("Asia/Jakarta");

$time = date('Y-m-d H:i:s', time() + 7200);
$finance      = $_SESSION['divisi'];

$no           = $_POST['no'];
$waktu        = $_POST['waktu'];
$urgent       = $_POST['urgent'];
$term       = $_POST['term'];
$tanggalbayar = $_POST['tanggalbayar'];
$alasanTolakBpu = $_POST['alasanTolakBpu'];
$rekening_sumber_mri_pal = $_POST['rekening_sumber_mri_pal'];
$rekening_sumber_mri_kas = $_POST['rekening_sumber_mri_kas'];
$metodePembayaran = $_POST["metode_pembayaran"];

$arrPengajuanJumlah = $_POST['pengajuan_jumlah'];
$arrNoid = $_POST['noid'];

$jenisPajak = $_POST['jenispajak'];
$nominalPajak = $_POST['nominalpajak'];

$dt = new DateTime($tanggalbayar);

$itemBudget = $con->select()->from('selesai')->where('waktu', '=', $waktu)->where('no', '=', $no)->first();
$budget = $con->select()->from('pengajuan')->where('waktu', '=', $waktu)->first();
$idBudget = $budget['noid'];

if ($budget['jenis'] == 'Non Rutin') {
    $isNonRutin = '-nonrutin';
} else {
    $isNonRutin = '';
}

if ($urgent == 'Urgent') {
    $tanggalbayar = $time;
} else {
    $startHour = 8;
    $maxHour = 12;
    $startMinute = 10;
    $maxMinute = 59;
    $hourNow = date('H');
    $minuteNow = date('i');

    $dateNow = date("Y-m-d");

    if ($_POST['tanggalbayar'] == $dateNow) {
        if ($hourNow > $startHour) {
            $startHour = $startHour + ($hourNow - $startHour);
        }
    }

    $minute = str_pad(mt_rand($startMinute,$maxMinute), 2, "0", STR_PAD_LEFT);
    $dateTime = mt_rand((int)$startHour,(int)$maxHour).":".$minute.":00";
    $tanggalbayar = $_POST['tanggalbayar'] . ' ' .  $dateTime;

}

$dt = new DateTime($tanggalbayar);

$email = [];
$nama = [];
$arrPenerima = [];
$arrJumlah = [];
$pengaju = '';
$arrPembayaran = [];
$idUsersNotification = [];
$dataDivisi = [];
$dataLevel = [];
$emails = [];
$arremailpenerima = [];


$url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$port = $_SERVER['SERVER_PORT'];
$url = explode('/', $url);
$hostProtocol = $url[0];

if ($port != "") {
$hostProtocol = "http://".$hostProtocol . ":" . $port;
}

$host = $hostProtocol;
if ($port == "" || $port == "80" || $port == '7793') {
  $host = $hostProtocol. '/'. $url[1];
}

$dataBpuItem = $con->select()->from('bpu')->where('no', '=', $no)->where('waktu', '=', $waktu)->where('term', '=', $term)->get();
$bpuItem = $con->select()->from('bpu')->where('no', '=', $no)->where('waktu', '=', $waktu)->where('term', '=', $term)->first();
$bpuVerify = $con->select()->from('bpu_verify')->where('id_bpu', '=', $bpuItem['noid'])->first();

$statusBpu = $bpuItem["statusbpu"];
$eksternal = ['Honor Eksternal','Honor Area Head','STKB OPS', 'STKB TRK Luar Kota', 'Honor Luar Kota', 'Honor Jakarta', 'STKB TRK Jakarta', 'Vendor/Supplier'];
$isEksternalProcess = in_array($bpuItem["statusbpu"], $eksternal);
$path = '/view-bpu-verify.php?id='.$bpuVerify["id"].'&bpu='.$bpuItem["noid"];
$duplicateNumber = [];


$urlCallback = getHostUrl() . "/api/callback.php";

if ($_POST['submit'] == 1) {
    $index = 0;
    foreach ($dataBpuItem as $item) {
        if ($metodePembayaran != "") {
            $item['metode_pembayaran'] = $metodePembayaran;
        }

        array_push($arrPembayaran, $item['metode_pembayaran']);
        array_push($arrPenerima, $item['namapenerima']);
        if ($item['jumlah'] != 0) {
            array_push($arrJumlah, "Rp. " . number_format($item['jumlah'], 0, ",", "."));
        }

        if (isset($arrPengajuanJumlah[$index])) {
            if ($arrPengajuanJumlah[$index] != "") {
                array_push($arrJumlah, "Rp. " . number_format($arrPengajuanJumlah[$index], 0, ",", "."));
            }
        }

        $pengaju = $item['pengaju'];

        if (!$isEksternalProcess && $item['metode_pembayaran'] == 'MRI PAL') {
            $update = mysqli_query($koneksiTransfer, "UPDATE data_transfer SET jadwal_transfer ='$tanggalbayar', nm_otorisasi = '$userSetuju', url_callback = '$urlCallback' WHERE noid_bpu = '$item[noid]'") or die(mysqli_error($koneksiTransfer));

            if (!$update) {
                echo "<script language='javascript'>";
                echo "alert('BPU Gagal Disetujui!')";
                echo "</script>";
                if ($aksesSes == 'HRD') echo "<script> document.location.href='views-direksi.php?code=" . $idBudget . "'; </script>";
                if ($aksesSes == 'Manager') echo "<script> document.location.href='views-finance-manager.php?code=" . $idBudget . "'; </script>";
                else echo "<script> document.location.href='views.php?code=" . $idBudget . "'; </script>";
            }
        }

        if ($isEksternalProcess) {
            $update = mysqli_query($koneksi, "UPDATE bpu SET metode_pembayaran = '$item[metode_pembayaran]' ,jumlah = '$arrPengajuanJumlah[$index]', jenis_pajak = '$jenisPajak', nominal_pajak = '$nominalPajak', checkby = '$userSetuju', tglcheck='$time' WHERE noid = '$arrNoid[$index]'");
            $item['jumlah'] = $arrPengajuanJumlah[$index];
        }

        $queryBank = mysqli_query($koneksi, "SELECT * FROM bank WHERE kodebank = '$item[namabank]'");
        $bank = mysqli_fetch_assoc($queryBank);
        if ($itemBudget['status'] == 'Vendor/Supplier' || $itemBudget['status'] == 'Honor Eksternal') {
            $explodeString = explode('.', $item['ket_pembayaran']);
            array_push($arremailpenerima, $item['emailpenerima']);
            if ($itemBudget['status'] == 'Vendor/Supplier') {
                $msg = "Kepada " . $item['namapenerima'] . ", <br><br>
                Berikut informasi status pembayaran Anda:<br><br>
                No.Invoice       : <strong>" . $explodeString[1] . "</strong><br>
                Tgl. Invoice     : <strong>" . $explodeString[2][0] . $explodeString[2][1] . "/" . $explodeString[2][2] .  $explodeString[2][3] . "/20" . $explodeString[2][4] . $explodeString[2][5] . "</strong><br>
                Term             : <strong>" . $explodeString[3][1] . " of " . $explodeString[3][3] . "</strong><br>
                Jenis Pembayaran : <strong>" . $explodeString[4] . "</strong><br>
                No. Rekening Anda : <strong>" . $item['norek'] . "</strong><br>
                Bank             : <strong>" . $bank['namabank'] . "</strong><br>
                Nama Penerima    : <strong>" . $item['namapenerima'] . "</strong><br>
                Jumlah Dibayarkan : <strong>Rp. " . number_format($item['jumlah'], 0, '', '.') . "</strong><br>
                Status           : <strong>Dijadwalkan</strong>, Tanggal : <strong>" . $dt->format('d/m/Y')  . "</strong><br><br>
                Informasi update status pembayaran akan kami kirimkan kembali melalui email. Jika ada pertanyaan lebih lanjut, silahkan email Divisi Finance ke finance@mri-research-ind.com.<br><br>
                Hormat kami,<br>
                Finance Marketing Research Indonesia
                ";
            } else {
                $msg = "Kepada " . $item['namapenerima'] . ", <br><br>
                Berikut informasi status pembayaran yang akan Anda terima:<br><br>
                Nama Pembayaran  : <strong>" . $item['ket_pembayaran'] . "</strong><br>
                No. Rekening Anda : <strong>" . $item['norek'] . "</strong><br>
                Bank             : <strong>" . $bank['namabank'] . "</strong><br>
                Nama Penerima    : <strong>" . $item['namapenerima'] . "</strong><br>
                Jumlah Dibayarkan : <strong>Rp. " . number_format($item['jumlah'], 0, '', '.') . "</strong><br>
                Status           : <strong>Dijadwalkan</strong>, Tanggal : <strong>" . $dt->format('d/m/Y')  . "</strong><br><br>
                Informasi update status pembayaran akan kami kirimkan kembali melalui email. Jika ada pertanyaan lebih lanjut, silahkan email Divisi Finance ke finance@mri-research-ind.com.<br><br>
                Hormat kami,<br>
                Finance Marketing Research Indonesia
                ";
            }
            $subject = "Informasi Pembayaran";

            if ($item['emailpenerima']) {
                $message = $emailHelper->sendEmail($msg, $subject, $item['emailpenerima'], $name = '', $address = "single");
            }
        } else {
            $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE nama_user = '$item[namapenerima]' AND aktif='Y'");
            $emailUser = mysqli_fetch_assoc($queryEmail);
            if ($emailUser && !in_array($emailUser['phone_number'], $duplicateNumber)) {
                array_push($duplicateNumber, $emailUser['phone_number']);
                array_push($email, $emailUser['phone_number']);
                array_push($nama, $emailUser['nama_user']);
                array_push($idUsersNotification, $emailUser['id_user']);
                array_push($dataDivisi, $emailUser['divisi']);
                array_push($dataLevel, $emailUser['level']);
                array_push($emails, $emailUser['email']);
            }
        }

        $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE nama_user = '$item[acknowledged_by]' AND aktif='Y'");
        $emailUser = mysqli_fetch_assoc($queryEmail);
        if ($emailUser['phone_number'] != "" && !in_array($emailUser['phone_number'], $duplicateNumber)) {
            array_push($email, $emailUser['phone_number']);
            array_push($nama, $emailUser['nama_user']);
            array_push($idUsersNotification, $emailUser['id_user']);
            array_push($dataDivisi, $emailUser['divisi']);
            array_push($dataLevel, $emailUser['level']);
            array_push($duplicateNumber, $emailUser['phone_number']);
            array_push($emails, $emailUser['email']);
        }


        if ($isEksternalProcess && $item['metode_pembayaran'] == 'MRI PAL') {
            $rekening_sumber = $rekening_sumber_mri_pal;
            $date = date('my');
            $countQuery = mysqli_query($koneksiTransfer, "SELECT transfer_req_id FROM data_transfer WHERE transfer_req_id LIKE '$date%' ORDER BY transfer_req_id DESC LIMIT 1");

            $count = mysqli_fetch_assoc($countQuery);
            $count = (int)substr($count['transfer_req_id'], -4);

            $queryBank = mysqli_query($koneksi, "SELECT * FROM bank WHERE kodebank = '$item[namabank]'");
            $bank = mysqli_fetch_assoc($queryBank);

            $formatId = $date . sprintf('%04d', $count + 1);

            if ($bank['kodebank'] == "CENAIDJA") {
                $biayaTrf = 0;
            } else {
                $biayaTrf = 2900;
            }

            $nm_project = '"' . $budget['nama'] . '", "item ke ' . $item['no'] . '", "BPU ke ' . $item['term'] . '"';

            $queryJenisPembayaran = mysqli_query($koneksiMriTransfer, "SELECT * FROM jenis_pembayaran WHERE jenispembayaran = '$item[statusbpu]'");
            $jenisPembayaran = mysqli_fetch_assoc($queryJenisPembayaran);

            $typeKas = typeKas($budget['jenis'], $item['statusbpu']);
            $queryKas = mysqli_query($koneksiDevelop, "SELECT rekening FROM kas WHERE label_kas = '$typeKas'");
            $kas = mysqli_fetch_assoc($queryKas);
            if ($typeKas == U_UNDEFINED_VARIABLE) {
                $kas['rekening'] = U_UNDEFINED_VARIABLE;
            }

            $updateBpu = mysqli_query($koneksi, "UPDATE bpu SET rekening_sumber = '$kas[rekening]', jenis_pajak = '$jenisPajak', nominal_pajak = '$nominalPajak', rekening_id = '$formatId' WHERE noid = '$item[noid]'");

            $insert = mysqli_query($koneksiTransfer, "INSERT INTO data_transfer (transfer_req_id, transfer_type, jenis_pembayaran_id, keterangan, waktu_request, norek, pemilik_rekening, bank, kode_bank, berita_transfer, jumlah, terotorisasi, hasil_transfer, ket_transfer, nm_pembuat, nm_otorisasi, nm_validasi, nm_manual, jenis_project, nm_project, noid_bpu, biaya_trf, rekening_sumber, email_pemilik_rekening, jadwal_transfer, url_callback) 
                    VALUES ('$formatId', '3', '$jenisPembayaran[jenispembayaranid]', '$item[statusbpu]', '$waktu', '$item[norek]', '$item[bank_account_name]','$bank[namabank]', '$bank[kodebank]', '$berita_transfer','$arrPengajuanJumlah[$index]', '2', '1', 'Antri', '$item[pengaju]', '$_SESSION[nama_user]', '$_SESSION[nama_user]','', '$budget[jenis]', '$nm_project', '$item[noid]', $biayaTrf, '$kas[rekening]', '$item[emailpenerima]', '$tanggalbayar', '$urlCallback')") or die(mysqli_error($koneksiTransfer));
        }
        $index++;
    }

    if ($isEksternalProcess) {
        $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi = 'Direksi' AND aktif='Y'");
        $emailUser = mysqli_fetch_assoc($queryEmail);
        if ($emailUser['phone_number'] != "" && !in_array($emailUser['phone_number'], $duplicateNumber)) {
            array_push($email, $emailUser['phone_number']);
            array_push($nama, $emailUser['nama_user']);
            array_push($idUsersNotification, $emailUser['id_user']);
            array_push($dataDivisi, $emailUser['divisi']);
            array_push($dataLevel, $emailUser['level']);
            array_push($duplicateNumber, $emailUser['phone_number']);
            array_push($emails, $emailUser['email']);
        }
    }

    if ($bpuItem['statusbpu'] == 'UM' || $bpuItem['statusbpu'] == 'UM Burek') {
        $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE nama_user = '$bpuItem[namapenerima]' AND aktif='Y'");
        $emailUser = mysqli_fetch_assoc($queryEmail);
        if ($emailUser['phone_number'] != "" && !in_array($emailUser['phone_number'], $duplicateNumber)) {
            array_push($email, $emailUser['phone_number']);
            array_push($nama, $emailUser['nama_user']);
            array_push($idUsersNotification, $emailUser['id_user']);
            array_push($dataDivisi, $emailUser['divisi']);
            array_push($dataLevel, $emailUser['level']);
            array_push($duplicateNumber, $emailUser['phone_number']);
            array_push($emails, $emailUser['email']);
        }
    }

    $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE nama_user = '$budget[pengaju]' AND aktif='Y'");
    $emailUser = mysqli_fetch_assoc($queryEmail);
    if ($emailUser['phone_number'] && !in_array($emailUser['phone_number'], $duplicateNumber)) {
        array_push($email, $emailUser['phone_number']);
        array_push($nama, $emailUser['nama_user']);
        array_push($dataDivisi, $emailUser['divisi']);
        array_push($idUsersNotification, $emailUser['id_user']);
        array_push($dataLevel, $emailUser['level']);
        array_push($duplicateNumber, $emailUser['phone_number']);
        array_push($emails, $emailUser['email']);
    }

    if ($budget['jenis'] == 'B1' || $budget['jenis'] == 'B2') {
        $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('1', '3')");
        while ($e = mysqli_fetch_assoc($queryEmail)) {
            if ($e['phone_number'] != "" && !in_array($emailUser['phone_number'], $duplicateNumber)) {
                array_push($email, $e['phone_number']);
                array_push($nama, $e['nama_user']);
                array_push($idUsersNotification, $e['id_user']);
                array_push($dataDivisi, $e['divisi']);
                array_push($dataLevel, $e['level']);
                array_push($duplicateNumber, $emailUser['phone_number']);
                array_push($emails, $emailUser['email']);
            }
        }
    } else {
        $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('2', '3')");
        while ($e = mysqli_fetch_assoc($queryEmail)) {
            if ($e['phone_number'] != "" && !in_array($emailUser['phone_number'], $duplicateNumber)) {
                array_push($email, $e['phone_number']);
                array_push($nama, $e['nama_user']);
                array_push($idUsersNotification, $e['id_user']);
                array_push($dataDivisi, $e['divisi']);
                array_push($dataLevel, $e['level']);
                array_push($duplicateNumber, $emailUser['phone_number']);
                array_push($emails, $emailUser['email']);
            }
        }
    }

    $nama = array_unique($nama);
    $email = array_unique($email);

    if ($divisi == 'FINANCE') {
        $persetujuan = 'Disetujui (Sri Dewi Marpaung)';
    } else {
        $persetujuan = 'Disetujui (Direksi)';
    }


    if ($isEksternalProcess) {
        $update = mysqli_query($koneksi, "UPDATE bpu SET status_pengajuan_bpu =0, tanggalbayar = '$tanggalbayar', jenis_pajak = '$jenisPajak', nominal_pajak = '$nominalPajak', urgent = '$urgent', checkby = '$userSetuju', tglcheck = '$time'
                               WHERE no='$no' AND waktu='$waktu' AND persetujuan='Belum Disetujui' AND term=$term");
        $update = mysqli_query($koneksi, "UPDATE bpu_verify SET is_need_approved = '0', status_approved = '1', is_approved = '1' WHERE id = '$bpuVerify[id]'");

        

    } else {
        if ($metodePembayaran != "") {
            $dataBpuItem['metode_pembayaran'] = $metodePembayaran;
        }
        $update = mysqli_query($koneksi, "UPDATE bpu SET metode_pembayaran = '$dataBpuItem[metode_pembayaran]', status_pengajuan_bpu =0, persetujuan = '$persetujuan', jenis_pajak = '$jenisPajak', nominal_pajak = '$nominalPajak', tanggalbayar = '$tanggalbayar', urgent = '$urgent', approveby = '$userSetuju', tglapprove = '$time'
                               WHERE no='$no' AND waktu='$waktu' AND persetujuan='Belum Disetujui' AND term=$term");
        $update = mysqli_query($koneksi, "UPDATE bpu_verify SET is_need_approved = '0', status_approved = '1', is_approved = '1' WHERE id = '$bpuVerify[id]'");
    }

    array_unique($arrJumlah);
    array_unique($nama);
    array_unique($email);
    if ($update) {
        $notification = 'BPU Telah Disetujui. Pemberitahuan via whatsapp telah terkirim ke ';
        $i = 0;
        for($i = 0; $i < count($email); $i++) {
            $path = '/views.php';

            if ($_SESSION['nama_user'] != $nama[$i]) {
                if ($isEksternalProcess && $dataDivisi[$i] != 'Direksi') {
                    $path = '/view-bpu-verify.php?id='.$bpuVerify["id"].'&bpu='.$bpuItem["noid"];
                } else if ($isEksternalProcess && $dataDivisi[$i] == 'Direksi') {
                    $path = '/rekap-finance.php?see';
                } else {
                    if ($dataDivisi[$i] == 'FINANCE') {
                        $pathManager = ($dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager") && $budget['jenis'] == 'B1' ? '/view-finance-manager-b1.php?code='.$idBudget : '/view-finance-manager.php?code='.$idBudget ;
                        $pathManager = ($dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager") && $budget['jenis'] == 'Non Rutin' ? '/view-finance-nonrutin-manager.php?code='.$idBudget  : '/view-finance-manager.php?code='.$idBudget ;
                        $pathKaryawan = ($dataLevel[$i] != "Manager" || $dataLevel[$i] != "Senior Manager") && $budget['jenis'] == 'Non Rutin' ? '/view-finance-nonrutin.php?code='.$idBudget  : '/view-finance.php?code='.$idBudget ;
                        $path =  $dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager" ? $pathManager : $pathKaryawan;
                    } else if ($dataDivisi[$i] == 'Direksi') {
                        $path = '/rekap-finance.php?see';
                    }
                }
    
              $url =  $host. $path.'&session='.base64_encode(json_encode(["id_user" => $idUsersNotification[$i], "timeout" => time()]));
              $msg = $messageHelper->messageApprovePengajuanBPU($itemBudget['rincian'], $itemBudget['status'], $itemBudget['kota'], $userSetuju, $budget['nama'], $no, $term, $arrPenerima, $tanggalbayar, $arrPembayaran, $arrJumlah, $keterangan, $url);
              $msgEmail = $messageEmail->approvedApplyBPU($userSetuju, $budget['nama'], $no, $term, $arrPenerima, $tanggalbayar, $arrPembayaran, $arrJumlah, $keterangan, $url);
              if($email[$i] != "") $whatsapp->sendMessage($email[$i], $msg);
              if ($emails[$i] != "") $emailHelper->sendEmail($msgEmail, "Informasi Persetujuan BPU", $emails[$i]);
              $notification .= ($nama[$i] . ' (' . $email[$i] . ')');
              if ($i < count($email) - 1) $notification .= ', ';
              else $notification .= '.';
            }
        }

        if (count($arremailpenerima) > 0) {
            $notification .= " Dan telah dikirim pemberitahuan ke penerima via email ke " . implode(",", $arremailpenerima);
        }
    }
} 
else if ($submit == 0) {

    $update = mysqli_query($koneksi, "UPDATE bpu SET jumlah = null, status_pengajuan_bpu = 2, alasan_tolak_bpu = '$alasanTolakBpu' WHERE no='$no' AND waktu='$waktu' AND term='$term'");
    $update = mysqli_query($koneksi, "UPDATE bpu_verify SET is_need_approved = '0', status_approved = '0', is_approved = '0', is_verified = '0' WHERE id = '$bpuVerify[id]'");

    $queryBpu = mysqli_query($koneksi, "SELECT * FROM bpu WHERE no='$no' AND waktu='$waktu' AND term='$term'");
    while ($bpu = mysqli_fetch_assoc($queryBpu)) {
        array_push($arrPenerima, $bpu['namapenerima']);
        array_push($arremailpenerima, $item['emailpenerima']);
        array_push($arrJumlah, "Rp. " . number_format($bpu['pengajuan_jumlah'], 0, ",", "."));

        $queryEmail = mysqli_query($koneksi, "SELECT* FROM tb_user WHERE nama_user = '$bpu[pengaju]' AND aktif='Y'");
        $emailUser = mysqli_fetch_assoc($queryEmail);
        if ($emailUser && !in_array($emailUser['phone_number'], $duplicateNumber)) {
            array_push($email, $emailUser['phone_number']);
            array_push($nama, $emailUser['nama_user']);
            array_push($idUsersNotification, $emailUser['id_user']);
            array_push($dataDivisi, $emailUser['divisi']);
            array_push($dataLevel, $emailUser['level']);
            array_push($duplicateNumber, $emailUser['phone_number']);
        }

        $queryEmail = mysqli_query($koneksi, "SELECT* FROM tb_user WHERE nama_user = '$bpu[acknowledged_by]' AND aktif='Y'");
        $emailUser = mysqli_fetch_assoc($queryEmail);
        if ($emailUser && !in_array($emailUser['phone_number'], $duplicateNumber)) {
            array_push($email, $emailUser['phone_number']);
            array_push($nama, $emailUser['nama_user']);
            array_push($idUsersNotification, $emailUser['id_user']);
            array_push($dataDivisi, $emailUser['divisi']);
            array_push($dataLevel, $emailUser['level']);
            array_push($duplicateNumber, $emailUser['phone_number']);
        }

        if ($budget['jenis'] == 'B1' || $budget['jenis'] == 'B2') {
            $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('1', '3')");
            while ($e = mysqli_fetch_assoc($queryEmail)) {
                if ($e['phone_number'] && !in_array($e['phone_number'], $duplicateNumber)) {
                    array_push($email, $e['phone_number']);
                    array_push($nama, $e['nama_user']);
                    array_push($idUsersNotification, $e['id_user']);
                    array_push($dataDivisi, $e['divisi']);
                    array_push($dataLevel, $e['level']);
                    array_push($duplicateNumber, $emailUser['phone_number']);
                }
            }
        } else {
            $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('2', '3')");
            while ($e = mysqli_fetch_assoc($queryEmail)) {
                if ($e['phone_number'] && !in_array($e['phone_number'], $duplicateNumber)) {
                    array_push($email, $e['phone_number']);
                    array_push($nama, $e['nama_user']);
                    array_push($idUsersNotification, $e['id_user']);
                    array_push($dataDivisi, $e['divisi']);
                    array_push($dataLevel, $e['level']);
                    array_push($duplicateNumber, $emailUser['phone_number']);
                }
            }
        }
    }

    // $email = array_unique($email);
    // $nama = array_unique($nama);

    if ($bpuItem['status'] == "UM" || $bpuItem['status'] == "UM Burek") {
        if (count($arrPenerima) > 0) {
            for ($i=0; $i < count($arrPenerima); $i++) { 
                $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE nama_user = '$arrPenerima[$i]' AND aktif='Y'");
                $emailUser = mysqli_fetch_assoc($queryEmail);
                echo $emailUser['nama_user'];
                if ($emailUser && !in_array($e['phone_number'], $duplicateNumber)) {
                    array_push($email, $emailUser['phone_number']);
                    array_push($nama, $emailUser['nama_user']);
                    array_push($idUsersNotification, $emailUser['id_user']);
                    array_push($dataDivisi, $emailUser['divisi']);
                    array_push($dataLevel, $emailUser['level']);
                    array_push($duplicateNumber, $emailUser['phone_number']);
                }
            }
        }
    }
    

    $notification = 'BPU Telah Di Tolak. Pemberitahuan via whatsapp telah terkirim ke ';
    $i = 0;
    for($i = 0; $i < count($email); $i++) {
        $path = '/views.php';

        if ($_SESSION['nama_user'] != $nama[$i]) {
            if ($isEksternalProcess) {
                $path = '/view-bpu-verify.php?id='.$bpuVerify["id"].'&bpu='.$bpuItem["noid"];
            } else {
                if ($dataDivisi[$i] == 'FINANCE') {
                    $pathManager = ($dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager") && $budget['jenis'] == 'B1' ? '/view-finance-manager-b1.php?code='.$idBudget : '/view-finance-manager.php?code='.$idBudget;
                    $pathManager = ($dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager") && $budget['jenis'] == 'Non Rutin' ? '/view-finance-nonrutin-manager.php?code='.$idBudget : '/view-finance-manager.php?code='.$idBudget;
                    $pathKaryawan = ($dataLevel[$i] != "Manager" || $dataLevel[$i] != "Senior Manager") && $budget['jenis'] == 'Non Rutin' ? '/view-finance-nonrutin.php?code='.$idBudget : '/view-finance.php?code='.$idBudget;
                    $path =  $dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager" ? $pathManager : $pathKaryawan;
                } else if ($dataDivisi[$i] == 'Direksi') {
                    $path = '/views-direksi.php?code='.$idBudget;
                }
            }
            $url =  $host. $path.'&session='.base64_encode(json_encode(["id_user" => $idUsersNotification[$i], "timeout" => time()]));
            $msg = $messageHelper->messageTolakPengajuanBPU($userSetuju, $budget['nama'], $no, $term, $arrPenerima, $tanggalbayar, $arrPembayaran, $arrJumlah, $keterangan, $url);
            $msgEmail = $messageEmail->rejectedApplyBPU($userSetuju, $budget['nama'], $no, $term, $arrPenerima, $tanggalbayar, $arrPembayaran, $arrJumlah, $keterangan, $url, $alasanTolakBpu);
            if($email[$i] != "") $whatsapp->sendMessage($email[$i], $msg);
            if ($emails[$i] != "") $emailHelper->sendEmail($msgEmail, "Informais Penolakan BPU", $emails[$i]);
    
            $notification .= ($nama[$i] . ' (' . $email[$i] . ')');
            if ($i < count($email) - 1) $notification .= ', ';
            else $notification .= '.';
        }
    }

    if ($bpuItem['status'] != "UM" && $bpuItem['status'] != "UM Burek") {
        $msg = "Notifikasi BPU, <br><br>
        BPU telah di tolak oleh $userSetuju dengan keterangan sebagai berikut:<br><br>
        Nama Project   : <strong>" . $budget['nama'] . "</strong><br>
        Item No.       : <strong>$no</strong><br>
        Term           : <strong>$term</strong><br>
        Nama Pengaju   : <strong>" . $bpu['pengaju'] . "</strong><br>
        Nama Penerima  : <strong>" . implode(', ', $arrPenerima) . "</strong><br>
        Total Diajukan : <strong>" . implode(', ', $arrJumlah) . "</strong><br>
        ";
        if ($alasanTolakBpu) {
            $msg .= "Ditolak dengan alasan <strong> $alasanTolakBpu </strong>.<br><br>";
        } else {
            $msg .= "Ditolak tanpa alasan.<br><br>";
        }
        $msg .= "Klik <a href='$host'>Disini</a> untuk membuka aplikasi budget.";
        $subject = "Notifikasi Aplikasi Budget";

        if ($arremailpenerima) {
            $message = $emailHelper->sendEmail($msg, $subject, $arremailpenerima, $name, $address = "multiple");
        }

        if (count($arremailpenerima) > 0) {
            $notification .= " Dan telah dikirim pemberitahuan ke penerima via email ke " . implode(",", $arremailpenerima);
        }
    }
    
}

if ($update) {
    
    $q = explode('/', $path);
    if (!$isEksternalProcess) {
        if ($divisi == 'FINANCE') {
            if ($_SESSION['hak_akses'] == 'Manager') {
                echo "<script language='javascript'>";
                echo "alert('$notification')";
                echo "</script>";
                echo "<script> document.location.href='view-finance" . $isNonRutin  . "-manager.php?code=" . $idBudget . "'; </script>";
            } else {
                echo "<script language='javascript'>";
                echo "alert('$notification')";
                echo "</script>";
                echo "<script> document.location.href='view-finance" . $isNonRutin  . ".php?code=" . $idBudget . "'; </script>";
            }
        } else if ($aksesSes == 'HRD') {
            echo "<script language='javascript'>";
            echo "alert('$notification')";
            echo "</script>";
            echo "<script> document.location.href='views-direksi.php?code=" . $idBudget . "'; </script>";
        } else {
            echo "<script language='javascript'>";
            echo "alert('$notification')";
            echo "</script>";
            echo "<script> document.location.href='views.php?code=" . $idBudget . "'; </script>";
        }
    } else {
        echo "<script language='javascript'>";
        echo "alert('$notification')";
        echo "</script>";
        echo "<script> document.location.href='".$q[1]."'; </script>";
    }
} else {
    if (!$isEksternalProcess) {
        if ($divisi == 'FINANCE') {
            if ($_SESSION['hak_akses'] == 'Manager') {
                echo "<script language='javascript'>";
                echo "alert('Gagal')";
                echo "</script>";
                echo "<script> document.location.href='view-finance" . $isNonRutin  . "-manager.php?code=" . $idBudget . "'; </script>";
            } else {
                echo "<script language='javascript'>";
                echo "alert('Gagal')";
                echo "</script>";
                echo "<script> document.location.href='view-finance" . $isNonRutin  . ".php?code=" . $idBudget . "'; </script>";
            }
        } else if ($aksesSes == 'HRD') {
            echo "<script language='javascript'>";
            echo "alert('Gagal')";
            echo "</script>";
            echo "<script> document.location.href='views-direksi.php?code=" . $idBudget . "'; </script>";
        } else {
            echo "<script language='javascript'>";
            echo "alert('Gagal')";
            echo "</script>";
            echo "<script> document.location.href='views.php?code=" . $idBudget . "'; </script>";
        }
    } else {
        echo "<script language='javascript'>";
        echo "alert('Gagal')";
        echo "</script>";
        echo "<script> document.location.href='".$q[1]."'; </script>";
    }
}

function typeKas($typeBudget, $statusBpu = "") {
    $project = ["B1", "B2"];
    $uangMuka = ["UM", "UM Burek"];
    $umum = ['Rutin', 'Non Rutin'];

    if (in_array($typeBudget, $project) && !in_array($statusBpu, $uangMuka)) {
        return "Kas Project";
    } else if (in_array($statusBpu, $uangMuka)) {
        return "Kas Uang Muka";
    } else if (in_array($typeBudget, $umum) && !in_array($statusBpu, $uangMuka)) {
        return "Kas Umum";
    }

    return U_UNDEFINED_VARIABLE;

}

function getHostUrl()
{
    $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,strpos( $_SERVER["SERVER_PROTOCOL"],'/'))).'://';
    $url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    $port = $_SERVER['SERVER_PORT'];
    $url = explode('/', $url);
    $hostProtocol = $url[0];
    if ($port != "") {
        $hostProtocol = $hostProtocol . ":" . $port;
    }
    $host = $protocol.$hostProtocol. '/'. $url[1];
    return $host;
}