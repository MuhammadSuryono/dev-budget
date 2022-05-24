<?php
error_reporting(0);
require "application/config/database.php";
require_once "application/config/message.php";
require_once "application/config/whatsapp.php";
require_once "application/config/email.php";
require "vendor/email/send-email.php";
require_once "application/controllers/Cuti.php";

session_start();

$emailHelper = new Email();
$cuti = new Cuti();

$con = new Database();
$koneksi = $con->connect();

$con->set_name_db(DB_TRANSFER);
$con->init_connection();
$koneksiTransfer = $con->connect();

$con->set_name_db(DB_MRI_TRANSFER);
$con->init_connection();
$koneksiMriTransfer = $con->connect();

$messageHelper = new Message();
$whatsapp = new Whastapp();

$url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$url = explode('/', $url);
$url = $url[0] . '/' . $url[1] . '/' . 'login.php';

$querySetting = mysqli_query($koneksi, "SELECT * FROM setting_budget WHERE keterangan = 'approval_bpu'") or die(mysqli_error($koneksi));
$setting = mysqli_fetch_assoc($querySetting);

$now = date_create('now')->format('Y-m-d H:i:s');
$user = $_SESSION['nama_user'];
$divisi = $_SESSION['divisi'];

$no = $_POST['no'];
$waktu = $_POST['waktu'];
$term = $_POST['term'];
$noid = $_POST['noid'];
$pengajuan_jumlah = $_POST['pengajuan_jumlah'];
$metode_pembayaran = $_POST['metode_pembayaran'];
$rekening_sumber_mri_pal = $_POST['rekening_sumber_mri_pal'];
$rekening_sumber_mri_kas = $_POST['rekening_sumber_mri_kas'];
$berita_transfer = $_POST['berita_transfer'];
$umo_biaya_kode_id = $_POST['umo_biaya_kode_id'];
$alasanTolakBpu = $_POST['alasanTolakBpu'];
$submit = $_POST['submit'];


$jenisPajak = $_POST['jenispajak'];
$nominalPajak = $_POST['nominalpajak'];

$urlCallback = getHostUrl() . "/api/callback.php";

$querypengajuan = mysqli_query($koneksi, "SELECT noid,jenis,pengaju,nama FROM pengajuan WHERE waktu='$waktu'");
$pengajuan = mysqli_fetch_assoc($querypengajuan);
$kode = $pengajuan['noid'];

if ($pengajuan['jenis'] == 'Non Rutin') {
    $isNonRutin = '-nonrutin';
} else {
    $isNonRutin = '';
}

if ($pengajuan['jenis'] == 'B1') {
    $isB1 = '-b1';
} else {
    $isB1 = '';
}

$email = [];
$nama = [];
$idUsersNotification = [];
$dataDivisi = [];
$dataLevel = [];
$arrPenerima = [];
$arrJumlah = [];
$arremailpenerima = [];
$hakAkses = [];

$url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$port = $_SERVER['SERVER_PORT'];
$url = explode('/', $url);
$hostProtocol = $url[0];
if ($port != "") {
$hostProtocol = $hostProtocol . ":" . $port;
}
$host = $hostProtocol. '/'. $url[1];

$duplicates = [];

if ($submit == 1) {
    for ($i = 0; $i < count($noid); $i++) {
        $queryBpu = mysqli_query($koneksi, "SELECT * FROM bpu WHERE noid='$noid[$i]'");
        $bpu = mysqli_fetch_assoc($queryBpu);

        
        $queryBpuVerify = mysqli_query($koneksi, "SELECT * FROM bpu_verify WHERE id_bpu = '$bpu[noid]'");
        $bpuVerify = mysqli_fetch_assoc($queryBpuVerify);

        $nm_project = '"' . $pengajuan['nama'] . '", "item ke ' . $bpu['no'] . '", "BPU ke ' . $bpu['term'] . '"';
        if ($metode_pembayaran[$i] == 'MRI PAL') {
            $rekening_sumber = $rekening_sumber_mri_pal;

            $date = date('my');
            $countQuery = mysqli_query($koneksiTransfer, "SELECT transfer_req_id FROM data_transfer WHERE transfer_req_id LIKE '$date%' ORDER BY transfer_req_id DESC LIMIT 1");
            $count = mysqli_fetch_assoc($countQuery);
            $count = (int)substr($count['transfer_req_id'], -4);

            $queryBank = mysqli_query($koneksi, "SELECT * FROM bank WHERE kodebank = '$bpu[namabank]'");
            $bank = mysqli_fetch_assoc($queryBank);

            $formatId = $date . sprintf('%04d', $count + 1);

            if ($bank['kodebank'] == "CENAIDJA") {
                $biayaTrf = 0;
            } else {
                $biayaTrf = 2900;
            }
            
            $queryJenisPembayaran = mysqli_query($koneksiMriTransfer, "SELECT * FROM jenis_pembayaran WHERE jenispembayaran = '$bpu[statusbpu]'");
            $jenisPembayaran = mysqli_fetch_assoc($queryJenisPembayaran);
            $insert = mysqli_query($koneksiTransfer, "INSERT INTO data_transfer (url_callback,transfer_req_id, transfer_type, jenis_pembayaran_id, keterangan, waktu_request, norek, pemilik_rekening, bank, kode_bank, berita_transfer, jumlah, terotorisasi, hasil_transfer, ket_transfer, nm_pembuat, nm_validasi, nm_manual, jenis_project, nm_project, noid_bpu, biaya_trf, rekening_sumber, email_pemilik_rekening) 
                    VALUES ('$urlCallback', '$formatId', '3', '$jenisPembayaran[jenispembayaranid]', '$bpu[statusbpu]', '$waktu', '$bpu[norek]', '$bpu[bank_account_name]','$bank[namabank]', '$bank[kodebank]', '$berita_transfer','$pengajuan_jumlah[$i]', '2', '1', 'Antri', '$bpu[pengaju]', '$user', '', '$pengajuan[jenis]', '$nm_project', '$bpu[noid]', $biayaTrf, '$rekening_sumber', '$bpu[emailpenerima]')") or die(mysqli_error($koneksiTransfer));

            if (!$insert) {
                if ($_SESSION['hak_akses'] == 'Manager') {
                    echo "<script language='javascript'>";
                    echo "alert('Verifikasi BPU Gagal!')";
                    echo "</script>";
                    echo "<script> document.location.href='view-finance" . $isNonRutin  . "-manager" . $isB1 . ".php?code=" . $kode . "'; </script>";
                } else {
                    echo "<script language='javascript'>";
                    echo "alert('Verifikasi BPU Gagal!')";
                    echo "</script>";
                    echo "<script> document.location.href='view-finance" . $isNonRutin  . ".php?code=" . $kode . "'; </script>";
                }
            }
        } else {
            $rekening_sumber = $rekening_sumber_mri_kas;
        }


        if ($_FILES["gambar"]["name"]) {
            $extension = pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION);
            $nama_gambar = random_bytes(20) . "." . $extension;
            $target_file = "uploads/" . $nama_gambar;
            move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file);
            $update = mysqli_query($koneksi, "UPDATE bpu SET jumlah=$pengajuan_jumlah[$i], status_pengajuan_bpu = 0, jenis_pajak = '$jenisPajak', nominal_pajak = '$nominalPajak', checkby='$user', tglcheck='$now', metode_pembayaran = '$metode_pembayaran[$i]', umo_biaya_kode_id = '$umo_biaya_kode_id', fileupload='$nama_gambar', ket_pembayaran='$berita_transfer', rekening_sumber = '$rekening_sumber' WHERE noid = '$noid[$i]'") or die(mysqli_error($koneksi));
            // $update = mysqli_query($koneksi, "UPDATE bpu_verify SET is_need_approved = '0', status_approved = '1', is_approved = '1' WHERE id = '$bpuVerify[id]'");
        } else {
            $update = mysqli_query($koneksi, "UPDATE bpu SET jumlah=$pengajuan_jumlah[$i], status_pengajuan_bpu = 0, jenis_pajak = '$jenisPajak', nominal_pajak = '$nominalPajak', checkby='$user', tglcheck='$now', metode_pembayaran = '$metode_pembayaran[$i]', umo_biaya_kode_id = '$umo_biaya_kode_id', ket_pembayaran='$berita_transfer', rekening_sumber = '$rekening_sumber' WHERE noid = '$noid[$i]'") or die(mysqli_error($koneksi));
            // $update = mysqli_query($koneksi, "UPDATE bpu_verify SET is_need_approved = '0', status_approved = '1', is_approved = '1' WHERE id = '$bpuVerify[id]'");
        }

        if (!$update) {
            if ($_SESSION['hak_akses'] == 'Manager') {
                echo "<script language='javascript'>";
                echo "alert('Verifikasi BPU Gagal!')";
                echo "</script>";
                echo "<script> document.location.href='view-finance" . $isNonRutin  . "-manager" . $isB1 . ".php?code=" . $kode . "'; </script>";
            } else {
                echo "<script language='javascript'>";
                echo "alert('Verifikasi BPU Gagal!')";
                echo "</script>";
                echo "<script> document.location.href='view-finance" . $isNonRutin  . ".php?code=" . $kode . "'; </script>";
            }
        } else {
            array_push($arremailpenerima, $bpu['emailpenerima']);
            array_push($arrPenerima, $bpu['namapenerima']);
            array_push($arrJumlah, "Rp. " . number_format($pengajuan_jumlah[$i], 0, ",", "."));

            if ($pengajuan_jumlah[$i] <= $setting['plafon']) {
                if ($pengajuan['jenis'] == 'B1' || $pengajuan['jenis'] == 'B2') {
                    if ($bpu['pengajuan_jumlah'] > 1000000) {
                        $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi='Direksi' AND aktif='Y'");
                        while ($e = mysqli_fetch_assoc($queryEmail)) {
                            if ($e['phone_number'] && !in_array($e['phone_number'], $duplicates)) {
                                array_push($email, $e['phone_number']);
                                array_push($nama, $e['nama_user']);
                                array_push($idUsersNotification, $e['id_user']);
                                array_push($dataDivisi, $e['divisi']);
                                array_push($dataLevel, $e['level']);
                                array_push($hakAkses, $e['hak_akses']);
                                array_push($duplicates, $e['phone_number']);
                            }
                        }
                    } else {
                        $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND hak_akses='Manager'");
                        while ($e = mysqli_fetch_assoc($queryEmail)) {
                            if ($e['phone_number'] && !in_array($e['phone_number'], $duplicates)) {
                                array_push($email, $e['phone_number']);
                                array_push($nama, $e['nama_user']);
                                array_push($idUsersNotification, $e['id_user']);
                                array_push($dataDivisi, $e['divisi']);
                                array_push($dataLevel, $e['level']);
                                array_push($hakAkses, $e['hak_akses']);
                                array_push($duplicates, $e['phone_number']);
                            }
                        }
                    }
                    
                } else {
                    if ($bpu['pengajuan_jumlah'] > 1000000) {
                        $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi='Direksi' AND aktif='Y'");
                        while ($e = mysqli_fetch_assoc($queryEmail)) {
                            if ($e['phone_number'] && !in_array($e['phone_number'], $duplicates)) {
                                array_push($email, $e['phone_number']);
                                array_push($nama, $e['nama_user']);
                                array_push($idUsersNotification, $e['id_user']);
                                array_push($dataDivisi, $e['divisi']);
                                array_push($dataLevel, $e['level']);
                                array_push($hakAkses, $e['hak_akses']);
                                array_push($duplicates, $e['phone_number']);
                            }
                        }
                    } else {
                        $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND hak_akses='Manager'");
                        while ($e = mysqli_fetch_assoc($queryEmail)) {
                            if ($e['phone_number'] && !in_array($e['phone_number'], $duplicates)) {
                                $email[] = $e['phone_number'];
                                $nama[] = $e['nama_user'];
                                $idUsersNotification[] = $e['id_user'];
                                $dataDivisi[] = $e['divisi'];
                                $dataLevel[] = $e['level'];
                                $hakAkses[] = $e['hak_akses'];
                                $duplicates[] = $e['phone_number'];
                            }
                        }
                    }
                    
                }
            } else {
                if ($pengajuan['jenis'] != 'Rutin') {
                    $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi='Direksi' AND aktif='Y'");
                    while ($e = mysqli_fetch_assoc($queryEmail)) {
                        if ($e['phone_number'] && !in_array($e['phone_number'], $duplicates)) {
                            $email[] = $e['phone_number'];
                            $nama[] = $e['nama_user'];
                            $idUsersNotification[] = $e['id_user'];
                            $dataDivisi[] = $e['divisi'];
                            $hakAkses[] = $e['hak_akses'];
                            $dataLevel[] = $e['level'];
                            $duplicates[] = $e['phone_number'];
                        }
                    }
                }
            }
        }
    }

    if ($email) {
        for($i = 0; $i < count($email); $i++) {
            $path = '/views.php';
            $statusBpu = $bpu["statusbpu"];
            $isEksternalProcess = $statusbpu == 'Vendor/Supplier' || $statusbpu == 'Honor Eksternal' || $statusbpu == 'Honor Area Head' || $statusbpu == 'STKB OPS' || $statusbpu == 'STKB TRK Luar Kota' || $statusbpu == 'Honor Luar Kota' || $statusbpu == 'Honor Jakarta' || $statusbpu == 'STKB TRK Jakarta' ? true : false;
            if ($_SESSION['nama_user'] != $namaInternal[$i]) {
                if ($dataDivisi[$i] == "FINANCE" && $hakAkses[$i] == 'Manager') {
                    $isCuti = $cuti->checkStatusCutiUser($namaInternal[$i]);
                    if ($isCuti) {
                        $queryUser = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi = 'Direksi'");
                        $e = mysqli_fetch_assoc($queryUser);
                        $emailInternal[$i] = $e['phone_number'];
                        $namaInternal[$i] = $e['nama_user'];
                        $idUserInternal[$i] = $e['id_user'];
                        $dataDivisi[$i] = $e['divisi'];
                        $dataLevel[$i] = $e['level'];
                        $hakAkses[$i] = $e['hak_akses'];
                        $duplicatePhoneNumber[$i] = $e['phone_number'];
                    }
                }
                if ($isEksternalProcess) {
                    $path = '/view-bpu-verify.php?id='.$bpuVerify["id"].'&bpu='.$bpu["noid"];
                } else {
                    if ($dataDivisi[$i] == 'FINANCE') {
                        $pathManager = ($dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager") && $pengajuan['jenis'] == 'B1' ? '/view-finance-manager-b1.php' : '/view-finance-manager.php';
                        $pathManager = ($dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager") && $pengajuan['jenis'] == 'Non Rutin' ? '/view-finance-nonrutin-manager.php' : '/view-finance-manager.php';
                        $pathKaryawan = ($dataLevel[$i] != "Manager" || $dataLevel[$i] != "Senior Manager") && $pengajuan['jenis'] == 'Non Rutin' ? '/view-finance-nonrutin.php' : '/view-finance.php';
                        $path =  $dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager" ? $pathManager : $pathKaryawan;
                    } else if ($dataDivisi[$i] == 'Direksi') {
                        $path = '/views-direksi.php';
                    }
                }
    
              $url =  $host. $path.'?code='.$kode.'&session='.base64_encode(json_encode(["id_user" => $idUsersNotification[$i], "timeout" => time()]));
              $msg = $messageHelper->messageProcessBPUFinance($pengajuan["nama"], $user, $no, $term, $bpu["pengaju"], $arrPenerima, $arrJumlah, $keterangan, $url);
              if($email[$i] != "") $whatsapp->sendMessage($email[$i], $msg);
            }
            
        }
    }

    $notification = 'Verifikasi BPU Sukses. Pemberitahuan via whatsapp sedang dikirimkan ke ';
    $i = 0;
    for ($i = 0; $i < count($email); $i++) {
        $notification .= ($nama[$i] . ' (' . $email[$i] . ')');
        if ($i < count($email) - 1) $notification .= ', ';
        else $notification .= '.';
    }


} else if ($submit == 0) {
    $update = mysqli_query($koneksi, "UPDATE bpu SET status_pengajuan_bpu = 2, alasan_tolak_bpu = '$alasanTolakBpu' WHERE no='$no' AND waktu='$waktu' AND term='$term'");
    $queryBpu = mysqli_query($koneksi, "SELECT * FROM bpu WHERE no='$no' AND waktu='$waktu' AND term='$term'");

    $bpuItem = mysqli_fetch_assoc($queryBpu);
    $queryBpuVerify = mysqli_query($koneksi, "SELECT * FROM bpu_verify WHERE id_bpu = '$bpuItem[noid]'");
    $bpuVerify = mysqli_fetch_assoc($queryBpuVerify);

    // $update = mysqli_query($koneksi, "UPDATE bpu_verify SET is_need_approved = '0', status_approved = '0', is_approved = '1' WHERE id = '$bpuVerify[id]'");
    
    
    while ($bpu = mysqli_fetch_assoc($queryBpu)) {


        array_push($arrPenerima, $bpu['namapenerima']);
        array_push($arrJumlah, "Rp. " . number_format($bpu['pengajuan_jumlah'], 0, ",", "."));

        $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE nama_user = '$bpu[pengaju]' AND aktif='Y'");
        $emailUser = mysqli_fetch_assoc($queryEmail);
        if ($emailUser) {
            array_push($email, $emailUser['phone_number']);
            array_push($nama, $emailUser['nama_user']);
            array_push($idUsersNotification, $emailUser['id_user']);
            array_push($dataDivisi, $emailUser['divisi']);
            array_push($dataLevel, $emailUser['level']);
        }

        $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE nama_user = '$bpu[acknowledged_by]' AND aktif='Y'");
        $emailUser = mysqli_fetch_assoc($queryEmail);
        if ($emailUser) {
            array_push($email, $emailUser['phone_number']);
            array_push($nama, $emailUser['nama_user']);
            array_push($idUsersNotification, $emailUser['id_user']);
            array_push($dataDivisi, $emailUser['divisi']);
            array_push($dataLevel, $emailUser['level']);
        }

        if ($pengajuan['jenis'] == 'B1' || $pengajuan['jenis'] == 'B2') {
            $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('1', '3')");
            while ($e = mysqli_fetch_assoc($queryEmail)) {
                if ($e['phone_number']) {
                    array_push($email, $e['email']);
                    array_push($nama, $e['nama_user']);
                    array_push($idUsersNotification, $e['id_user']);
                    array_push($dataDivisi, $e['divisi']);
                    array_push($dataLevel, $e['level']);
                }
            }
        } else {
            $queryEmail = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('2', '3')");
            while ($e = mysqli_fetch_assoc($queryEmail)) {
                if ($e['phone_number']) {
                    array_push($email, $e['phone_number']);
                    array_push($nama, $e['nama_user']);
                    array_push($idUsersNotification, $e['id_user']);
                    array_push($dataDivisi, $e['divisi']);
                    array_push($dataLevel, $e['level']);
                }
            }
        }
    }

    $email = array_unique($email);
    $nama = array_unique($nama);
    $notification = 'BPU telah ditolak. Pemberitahuan via whatsapp sedang dikirimkan ke ';
    if ($email) {
        for($i = 0; $i < count($email); $i++) {
            $path = '/views.php';

            $statusBpu = $bpuItem["statusbpu"];
            $isEksternalProcess = $statusbpu == 'Vendor/Supplier' || $statusbpu == 'Honor Eksternal' || $statusbpu == 'Honor Area Head' || $statusbpu == 'STKB OPS' || $statusbpu == 'STKB TRK Luar Kota' || $statusbpu == 'Honor Luar Kota' || $statusbpu == 'Honor Jakarta' || $statusbpu == 'STKB TRK Jakarta' ? true : false;
            
            if ($cuti->checkStatusCutiUser($nama[$i])) {
                if ($nama[$i] != $_SESSION['nama_user']) {
                    if ($isEksternalProcess) {
                        $path = '/view-bpu-verify.php?id='.$bpuVerify["id"].'&bpu='.$bpu["noid"];
                    } else {
                        if ($dataDivisi[$i] == 'FINANCE') {
                            $pathManager = ($dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager") && $pengajuan['jenis'] == 'B1' ? '/view-finance-manager-b1.php' : '/view-finance-manager.php';
                            $pathManager = ($dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager") && $pengajuan['jenis'] == 'Non Rutin' ? '/view-finance-nonrutin-manager.php' : '/view-finance-manager.php';
                            $pathKaryawan = ($dataLevel[$i] != "Manager" || $dataLevel[$i] != "Senior Manager") && $pengajuan['jenis'] == 'Non Rutin' ? '/view-finance-nonrutin.php' : '/view-finance.php';
                            $path =  $dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager" ? $pathManager : $pathKaryawan;
                        } else if ($dataDivisi[$i] == 'Direksi') {
                            $path = '/views-direksi.php';
                        }
                    }
                    $notification .= ($nama[$i] . ' (' . $email[$i] . ')');
                    if ($i < count($email) - 1) $notification .= ', ';
                    else $notification .= '.';
                    $url =  $host. $path.'?code='.$kode.'&session='.base64_encode(json_encode(["id_user" => $idUsersNotification[$i], "timeout" => time()]));
                    $msg = $messageHelper->messageProcessTolakBPUFinance($pengajuan["nama"], $user, $no, $term, $bpu["pengaju"], $arrPenerima, $arrJumlah, $keterangan, $url);
                    if($email[$i] != "") $whatsapp->sendMessage($email[$i], $msg);
                }
                
            }
        }
    }
    
}
$isEksternalProcess = $statusbpu == 'Vendor/Supplier' || $statusbpu == 'Honor Eksternal' || $statusbpu == 'Honor Area Head' || $statusbpu == 'STKB OPS' || $statusbpu == 'STKB TRK Luar Kota' || $statusbpu == 'Honor Luar Kota' || $statusbpu == 'Honor Jakarta' || $statusbpu == 'STKB TRK Jakarta' ? true : false;

$path = '/view-bpu-verify.php?id='.$dataVerify["id"].'&bpu='.$dataVerify["id_bpu"];
if ($update) {
    if (!$isEksternalProcess) {
        if ($_SESSION['hak_akses'] == 'Manager') {
            echo "<script language='javascript'>";
            echo "alert('$notification')";
            echo "</script>";
            echo "<script> document.location.href='view-finance" . $isNonRutin  . "-manager" . $isB1 . ".php?code=" . $kode . "'; </script>";
        } else {
            echo "<script language='javascript'>";
            echo "alert('$notification')";
            echo "</script>";
            echo "<script> document.location.href='view-finance" . $isNonRutin  . ".php?code=" . $kode . "'; </script>";
        }
    } else {
        echo "<script language='javascript'>";
        echo "alert('$notification')";
        echo "</script>";
        echo "<script> document.location.href='".$path."'; </script>";
    }
} else {
    if (!$isEksternalProcess) {
        if ($_SESSION['hak_akses'] == 'Manager') {
            echo "<script language='javascript'>";
            echo "alert('Verifikasi BPU Gagal!')";
            echo "</script>";
            echo "<script> document.location.href='view-finance" . $isNonRutin  . "-manager" . $isB1 . ".php?code=" . $kode . "'; </script>";
        } else {
            echo "<script language='javascript'>";
            echo "alert('Verifikasi BPU Gagal!')";
            echo "</script>";
            echo "<script> document.location.href='view-finance" . $isNonRutin  . ".php?code=" . $kode . "'; </script>";
        }
    } else {
        echo "<script language='javascript'>";
        echo "alert('Verifikasi BPU Gagal!')";
        echo "</script>";
        echo "<script> document.location.href='".$path."'; </script>";
    }
    
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

function random_bytes($length = 6)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $characters_length = strlen($characters);
    $output = '';
    for ($i = 0; $i < $length; $i++)
        $output .= $characters[rand(0, $characters_length - 1)];
    return $output;
}