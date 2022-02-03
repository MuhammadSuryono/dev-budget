<?php
error_reporting(0);
require "application/config/database.php";
require_once "application/config/whatsapp.php";
require_once "application/config/message.php";
require_once "application/config/email.php";
require_once "application/controllers/Cuti.php";
require_once "application/config/messageEmail.php";

$emailHelper = new Email();
$cuti = new Cuti();

$con = new Database();
$koneksi = $con->connect();
require "vendor/email/send-email.php";

$wa = new Whastapp();
$messageHelper = new Message();
$messageEmail = new MessageEmail();

$con->set_name_db(DB_TRANSFER);
$con->init_connection();
$koneksiTransfer = $con->connect();

session_start();
if (!isset($_SESSION['nama_user'])) {
    header("location:login.php");
}

$time = date("Y-m-d H:i:s");

if (is_array($_POST['jumlah'])) {
    $arrjumlah       = $_POST['jumlah'];
    $arrnamabank     = $_POST['namabank'];
    $arrnorek        = $_POST['norek'];
    $arrnamapenerima = $_POST['namapenerima'];
    $arremailpenerima = $_POST['email'];
    $jumlah = array_sum($arrjumlah);
} else {
    $jumlah       = $_POST['jumlah'];
    $namabank     = $_POST['namabank'];
    $norek        = $_POST['norek'];
    $namapenerima = $_POST['namapenerima'];
    $emailpenerima = $_POST['email'];
}

$actionProcess = $_GET['action'];
$no           = $_POST['no'];
$waktu        = $_POST['waktu'];

$tglcair      = ($_POST['tglcair']) ? $_POST['tglcair'] : null;
$pengaju      = $_POST['pengaju'];
$divisi       = $_POST['divisi'];
$statusbpu    = $_POST['statusbpu'];
$bankAccountName = $_POST['bank_account_name'];

$vendorName = $_POST['nama_vendor'];

$namaInternal = [];
$emailInternal = [];
$idUserInternal = [];
$dataDivisi = [];
$dataLevel = [];
$idPengajuan = [];
$duplicatePhoneNumber = [];
$hakAkses = [];
$emails = [];

$queryItemBpu = mysqli_query($koneksi, "SELECT rincian FROM selesai where no = '$no' AND waktu = '$waktu'");
$dataItemBpu = mysqli_fetch_assoc($queryItemBpu);

$queryPengajuan = mysqli_query($koneksi, "SELECT jenis FROM pengajuan where waktu = '$waktu'");
$dataPengajuan = mysqli_fetch_assoc($queryPengajuan);

if ($actionProcess == "update" && (strpos(strtolower($dataItemBpu['rincian']), 'kas negara') !== false || strpos(strtolower($dataItemBpu['rincian']), 'penerimaan negara') !== false)) {
    $queryUser = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi = 'FINANCE' AND hak_akses = 'Level 2' AND level = 'Manager'");
    while ($e = mysqli_fetch_assoc($queryUser)) {
        if ($e['phone_number'] && !in_array($e['phone_number'], $duplicatePhoneNumber)) {
            array_push($emailInternal, $e['phone_number']);
            array_push($namaInternal, $e['nama_user']);
            array_push($idUserInternal, $e['id_user']);
            array_push($dataDivisi, $e['divisi']);
            array_push($dataLevel, $e['level']);
            array_push($hakAkses, $e['hak_akses']);
            array_push($duplicatePhoneNumber, $e['phone_number']);
        }
    }
}


$queryUser = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE nama_user = '$pengaju'");
while ($e = mysqli_fetch_assoc($queryUser)) {
    if ($e['phone_number'] && !in_array($e['phone_number'], $duplicatePhoneNumber)) {
        array_push($emailInternal, $e['phone_number']);
        array_push($namaInternal, $e['nama_user']);
        array_push($idUserInternal, $e['id_user']);
        array_push($dataDivisi, $e['divisi']);
        array_push($dataLevel, $e['level']);
        array_push($hakAkses, $e['hak_akses']);
        array_push($duplicatePhoneNumber, $e['phone_number']);
        array_push($emails, $e['email']);
    }
}

$queryUser = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi = '$divisi' AND hak_akses = 'Manager'");
while ($e = mysqli_fetch_assoc($queryUser)) {
    if ($e['phone_number']&& !in_array($e['phone_number'], $duplicatePhoneNumber)) {
        array_push($emailInternal, $e['phone_number']);
        array_push($namaInternal, $e['nama_user']);
        array_push($idUserInternal, $e['id_user']);
        array_push($dataDivisi, $e['divisi']);
        array_push($dataLevel, $e['level']);
        array_push($hakAkses, $e['hak_akses']);
        array_push($duplicatePhoneNumber, $e['phone_number']);
        array_push($emails, $e['email']);
    }
}

$queryUser = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi = 'FINANCE' AND status_penerima_email_id = '3'");
while ($e = mysqli_fetch_assoc($queryUser)) {
    if ($e['phone_number']&& !in_array($e['phone_number'], $duplicatePhoneNumber)) {
        array_push($emailInternal, $e['phone_number']);
        array_push($namaInternal, $e['nama_user']);
        array_push($idUserInternal, $e['id_user']);
        array_push($dataDivisi, $e['divisi']);
        array_push($dataLevel, $e['level']);
        array_push($hakAkses, $e['hak_akses']);
        array_push($duplicatePhoneNumber, $e['phone_number']);
        array_push($emails, $e['email']);
    }
}

if ($actionProcess == "update") {
    if ((strpos(strtolower($dataItemBpu['rincian']), 'kas negara') == false || strpos(strtolower($dataItemBpu['rincian']), 'penerimaan negara') == false) && $dataPengajuan['jenis'] != "Rutin") {
        for ($i=0; $i < count($emailInternal); $i++) { 
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
                    $emails[$i] = $e['email'];
                }
            }
        }
    }
}

if ($statusbpu == 'Vendor/Supplier') {
    $invoice = str_replace('.', '', $_POST['invoice']);
    $tgl = date_create($_POST['tgl']);
    $term1 = $_POST['term1'];
    $term2 = $_POST['term2'];
    $jenis_pembayaran = str_replace('.', '', $_POST['jenis_pembayaran']);
    $invoice = $invoice == "" ? "000" : $invoice;

    if ($jenis_pembayaran == '') {
        $jenis_pembayaran = $_POST['keterangan_pembayaran'];
    }

    if ($_POST['tgl'] == "") {
        $tgl = date("Y-m-d");
    }

    if ($_POST['total-term'] != "") {
        $term2 = $_POST['total-term'];
    }

    $keterangan_pembayaran = "INV." . $invoice . "." . date_format($tgl, 'dmy') . ".T" . $term1 . "/" . $term2 . "." . $jenis_pembayaran;
} else {
    $keterangan_pembayaran    = $_POST['keterangan_pembayaran'];
}

$url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$port = $_SERVER['SERVER_PORT'];
$url = explode('/', $url);
$hostProtocol = $url[0];
if ($port != "") {
$hostProtocol = $hostProtocol . ":" . $port;
}
$host = $hostProtocol. '/'. $url[1];


$idBpu = $_GET['id-bpu'];
$idVerify = $_GET['id-verify'];

$queryBpuVerify = mysqli_query($koneksi, "SELECT * FROM bpu_verify WHERE id = '$idVerify'");
$dataVerify = [];
while($row = mysqli_fetch_assoc($queryBpuVerify)) {
    $dataVerify = $row;
}

$termterm = $_POST['term'];
$queryBpu = mysqli_query($koneksi, "SELECT * FROM bpu WHERE no='$no' AND waktu='$waktu' AND term='$termterm'");
// $queryBpuStatus = mysqli_query($koneksi, "SELECT * FROM bpu WHERE no='$no' AND waktu='$waktu' AND term='$termterm'");
$bpu = mysqli_fetch_assoc($queryBpu);

if ($jumlah == 0 || $jumlah == "") {
    $jumlah = $dataVerify['total_verify'];
}


//periksa apakah udah submit
if ($actionProcess == "update") {
    $queryBpuNew = mysqli_query($koneksi, "SELECT * FROM bpu WHERE noid='$idBpu'");
    $bpu = mysqli_fetch_assoc($queryBpuNew);
}

if ($statusbpu == "") {
    $statusbpu = $bpu["statusbpu"];
}

$eksternal = ['Honor Eksternal','Honor Area Head','STKB OPS', 'STKB TRK Luar Kota', 'Honor Luar Kota', 'Honor Jakarta', 'STKB TRK Jakarta', 'Vendor/Supplier'];
$isEksternalProcess = in_array($statusbpu, $eksternal);
$path = '/view-bpu-verify.php?id='.$bpuVerify["id"].'&bpu='.$bpuVerify["id_bpu"];

if (isset($_POST['submit'])) {

    if ($_SESSION['divisi'] == 'FINANCE') {
        $nama_gambar  = $_FILES['gambar']['name'];
        $lokasi       = $_FILES['gambar']['tmp_name']; // Menyiapkan tempat nemapung gambar yang diupload
        $lokasitujuan = "uploads"; // Menguplaod gambar kedalam folder ./image
        $upload       = move_uploaded_file($lokasi, $lokasitujuan . "/" . $nama_gambar);
    } else {
        echo "";
    }

    $sel1 = mysqli_query($koneksi, "SELECT nama,noid,jenis FROM pengajuan WHERE waktu='$waktu'");
    $uc = mysqli_fetch_assoc($sel1);
    $numb = $uc['noid'];
    $jenis = $uc['jenis'];
    $idPengajuan = $uc['noid'];
    $namaProject = $uc["nama"];

    if ($uc['jenis'] == 'Non Rutin') {
        $isNonRutin = '-nonrutin';
    } else {
        $isNonRutin = '';
    }

    $pilihtotal = mysqli_query($koneksi, "SELECT * FROM selesai WHERE no='$no' AND waktu='$waktu'");
    $aw = mysqli_fetch_assoc($pilihtotal);
    $hargaah = $aw['total'];

    $query = "SELECT sum(jumlah) AS sum FROM bpu WHERE no='$no' AND waktu='$waktu'";
    $result = mysqli_query($koneksi, $query);
    $row = mysqli_fetch_array($result);
    $total = $row[0];

    $query2 = "SELECT sum(uangkembali) AS sum FROM bpu WHERE no='$no' AND waktu='$waktu'";
    $result2 = mysqli_query($koneksi, $query2);
    $row2 = mysqli_fetch_array($result2);
    $total2 = $row2[0];
    
    $jadinya = $hargaah - $total + $total2;

    if ($jumlah > $jadinya) {
        if ($_SESSION['divisi'] == 'FINANCE') {
            if ($_SESSION['hak_akses'] == 'Manager') {
                echo "<script language='javascript'>";
                echo "alert('GAGAL!!, Kamu tidak bisa mengajukan lebih dari sisa Pembayaran')";
                echo "</script>";
                echo "<script> document.location.href='view-finance" . $isNonRutin  . "-manager.php?code=" . $numb . "'; </script>";
            } else {
                echo "<script language='javascript'>";
                echo "alert('GAGAL!!, Kamu tidak bisa mengajukan lebih dari sisa Pembayaran')";
                echo "</script>";
                echo "<script> document.location.href='view-finance" . $isNonRutin  . ".php?code=" . $numb . "'; </script>";
            }
        } else {
            echo "<script language='javascript'>";
            echo "alert('GAGAL!!, Kamu tidak bisa mengajukan lebih dari sisa Pembayaran')";
            echo "</script>";
            echo "<script> document.location.href='views-direksi.php?code=" . $numb . "'; </script>";
        }
    } else {
        $selterm = mysqli_query($koneksi, "SELECT MAX(term) FROM bpu WHERE no='$no' AND waktu='$waktu'");
        $m = mysqli_fetch_assoc($selterm);
        $termterm = $m['MAX(term)'];
        $termfinal = $termterm + 1;

        $bridge = mysqli_query($koneksiTransfer, "SELECT MAX(transfer_req_id) AS maxtrans FROM data_transfer");
        $br = mysqli_fetch_assoc($bridge);

        $maxtrans = $br['maxtrans'];

        $bulannya = substr($maxtrans, 2, 2);
        $bulansekarang = date('m');

        if ($bulansekarang != $bulannya) {
            $transidthn = date('y');
            $transidbln = date('m');
            $transferid = $transidthn . $transidbln . "0001";
        } else {
            $transferid = $maxtrans + 1;
        }
        $datetime = date('Y-m-d H:i:s');
        $jam = "14:00:00";
        if ($tglcair) {
            $tglcairnya = $tglcair . " " . $jam;
        } else {
            $tglcairnya = null;
        }
        
        if (is_array($_POST['jumlah'])) {
            $jumlahDiterima = "0";

            for ($i = 0; $i < count($arrjumlah); $i++) {
                $jumlahDiterima += $arrjumlah[$i];
                if ($aw['status'] == 'Vendor/Supplier' || $aw['status'] == 'Honor Eksternal' || $aw['status'] == 'Honor Area Head') {
                    $queryBank = mysqli_query($koneksi, "SELECT * FROM bank WHERE kodebank = '$arrnamabank[$i]'");
                    $bank = mysqli_fetch_assoc($queryBank);
                    if ($aw['status'] == 'Vendor/Supplier' && $_SESSION['divisi'] != 'Direksi') {
                        $msg = "Kepada $arrnamapenerima[$i], <br><br>
                        Berikut informasi status pembayaran Anda:<br><br>
                        No.Invoice       : <strong>$invoice</strong><br>
                        Tgl. Invoice     : <strong>" . date_format($tgl, 'd/m/Y') . "</strong><br>
                        Term             : <strong>$term1 of $term2</strong><br>
                        Jenis Pembayaran : <strong>$jenis_pembayaran</strong><br>
                        No. Rekening Anda : <strong>$arrnorek[$i]</strong><br>
                        Bank             : <strong>" . $bank['namabank'] . "</strong><br>
                        Nama Penerima    : <strong>$arrnamapenerima[$i]</strong><br>
                        Jumlah Dibayarkan : <strong>Rp. " . number_format($arrjumlah[$i], 0, '', '.') . "</strong><br>
                        Status           : <strong>Sedang Diproses</strong><br><br>
                        Informasi update status pembayaran akan kami kirimkan kembali melalui email. Jika ada pertanyaan lebih lanjut, silahkan email Divisi Finance ke finance@mri-research-ind.com.<br><br>
                        Hormat kami,<br>
                        Finance Marketing Research Indonesia";
                    } else if ($_SESSION['divisi'] != 'Direksi') {
                        $msg = "Kepada $arrnamapenerima[$i], <br><br>
                        Berikut informasi status pembayaran Anda:<br><br>
                        Nama Pembayaran  : <strong>$keterangan_pembayaran</strong><br>
                        No. Rekening Anda : <strong>$arrnorek[$i]</strong><br>
                        Bank             : <strong>" . $bank['namabank'] . "</strong><br>
                        Nama Penerima    : <strong>$arrnamapenerima[$i]</strong><br>
                        Jumlah Dibayarkan : <strong>Rp. " . number_format($arrjumlah[$i], 0, '', '.') . "</strong><br>
                        Status           : <strong>Diproses</strong><br><br>
                        Informasi update status pembayaran akan kami kirimkan kembali melalui email. Jika ada pertanyaan lebih lanjut, silahkan email Divisi Finance ke finance@mri-research-ind.com.<br><br>
                        Hormat kami,<br>
                        Finance Marketing Research Indonesia
                        ";
                    }

                    if ($arremailpenerima[$i] && $actionProcess == "update") {
                        $message = $emailHelper->sendEmail($msg, $subject, $arremailpenerima[$i], $name = '', $address = "single");
                    }
                }

                if ($_SESSION['divisi'] == 'Direksi' || ($isEksternalProcess && $_SESSION['divisi'] == 'FINANCE' && $_SESSION['level'] == 'Manager')) {
                    if ($actionProcess != "update") {
                        
                        // echo "INSERT NOT UPDATE IF";
                        $insert = mysqli_query($koneksi, "INSERT INTO bpu (no,nama_vendor,tanggalbayar,pengajuan_jumlah,tglcair,namabank,norek,namapenerima,pengaju,divisi,waktu,status,persetujuan,term,statusbpu,transfer_req_id,status_pengajuan_bpu,emailpenerima,ket_pembayaran,created_at) VALUES
                                                        ('$no','$vendorName','$_POST[tgl]','$arrjumlah[$i]','$tglcairnya','$arrnamabank[$i]','$arrnorek[$i]','$arrnamapenerima[$i]','$pengaju','$divisi','$waktu','Belum Di Bayar','Disetujui (Direksi)','$termfinal','$statusbpu','$transferid', 1, '$arremailpenerima[$i]', '$keterangan_pembayaran', '$time')");
                        
                        $idBpu = mysqli_insert_id($koneksi);
                        $insertDataNeedVerifikasi = mysqli_query($koneksi, "INSERT INTO bpu_verify (id_bpu) VALUES ('$idBpu')");
                        $idVerify = mysqli_insert_id($koneksi);

                        $queryBpuVerify = mysqli_query($koneksi, "SELECT * FROM bpu_verify WHERE id_bpu = '$idBpu'");
                        $dataVerify = [];
                        while($row = mysqli_fetch_assoc($queryBpuVerify)) {
                            $dataVerify = $row;
                        }
                    }

                    if ($actionProcess == "update" && $i == 0) {
                        
                        // echo "INSERT UPDATE IF";
                        $insert = mysqli_query($koneksi, "UPDATE bpu SET tanggalbayar = '$_POST[tgl]', pengajuan_jumlah='$arrjumlah[$i]', tglcair = '$tglcair', namabank= '$arrnamabank[$i]', norek = '$arrnorek[$i]', namapenerima = '$arrnamapenerima[$i]', ket_pembayaran = '$keterangan_pembayaran', emailpenerima = '$arremailpenerima[$i]' WHERE noid = '$idBpu';
                        ");
                        
                        $insert = mysqli_query($koneksi, "UPDATE bpu_verify SET is_need_approved = '1' WHERE id = '$idVerify'");
                    } else if ($actionProcess == "update" && $i != 0) {
                        // echo "INSERT UPDATE ELSE IF";
                        $insert = mysqli_query($koneksi, "INSERT INTO bpu (no, status_pengajuan_bpu, metode_pembayaran,pengajuan_jumlah,tglcair,namabank,norek,namapenerima,pengaju,divisi,waktu,status,persetujuan,term,statusbpu,fileupload,transfer_req_id, status_pengajuan_bpu,emailpenerima,ket_pembayaran,created_at) VALUES
                        ('$no','$bpu[status_pengajuan_bpu]','$bpu[metode_pembayaran]','$arrjumlah[$i]','$tglcairnya','$arrnamabank[$i]','$arrnorek[$i]','$arrnamapenerima[$i]','$pengaju','$divisi','$waktu','Belum Di Bayar','Belum Disetujui','$bpu[term]','$bpu[statusbpu]','$bpu[fileupload]','$transferid', 1, '$arremailpenerima[$i]', '$keterangan_pembayaran', '$time')");
                                                        // $idBpu = mysqli_insert_id($koneksi);
                    }
                    
                } else {
                    if ($actionProcess == "update" && $i == 0) {
                        $accountBankName = $_POST['bank_account_name'][$i];
                        $insert = mysqli_query($koneksi, "UPDATE bpu SET tanggalbayar = '$tglcairnya', status_pengajuan_bpu = '0', bank_account_name = '$accountBankName', pengajuan_jumlah='$arrjumlah[$i]', tglcair = '$tglcairnya', namabank= '$arrnamabank[$i]', norek = '$arrnorek[$i]', namapenerima = '$arrnamapenerima[$i]', ket_pembayaran = '$keterangan_pembayaran', emailpenerima = '$arremailpenerima[$i]' WHERE noid = '$idBpu'");
                        
                        $insert = mysqli_query($koneksi, "UPDATE bpu_verify SET is_need_approved = '1' WHERE id = '$idVerify'");
                    } 
                    
                    if ($actionProcess == "update" && $i != 0) {
                        $accountBankName = $_POST['bank_account_name'][$i];
                        $insert = mysqli_query($koneksi, "INSERT INTO bpu (no, tanggalbayar, status_pengajuan_bpu, metode_pembayaran, pengajuan_jumlah,tglcair,namabank,norek,namapenerima,pengaju,divisi,waktu,status,persetujuan,term,statusbpu,fileupload,transfer_req_id, emailpenerima,ket_pembayaran,created_at,bank_account_name, nama_vendor) VALUES
                        ('$no', '$tglcairnya', '0','$bpu[metode_pembayaran]','$arrjumlah[$i]','$tglcairnya','$arrnamabank[$i]','$arrnorek[$i]','$arrnamapenerima[$i]','$pengaju','$divisi','$waktu','Belum Di Bayar','$bpu[persetujuan]','$bpu[term]','$bpu[statusbpu]','$bpu[fileupload]','$transferid', '$arremailpenerima[$i]', '$keterangan_pembayaran', '$time', '$accountBankName', '$bpu[nama_vendor]')");

                        $insert = mysqli_query($koneksi, "UPDATE bpu_verify SET is_need_approved = '1' WHERE id = '$idVerify'");

                    } else if ($actionProcess != "update") {
                        $insert = mysqli_query($koneksi, "INSERT INTO bpu (no,nama_vendor,pengajuan_jumlah,tglcair,namabank,norek,namapenerima,pengaju,divisi,waktu,status,persetujuan,term,statusbpu,fileupload,transfer_req_id, status_pengajuan_bpu,emailpenerima,ket_pembayaran,created_at) VALUES
                        ('$no','$vendorName','$arrjumlah[$i]','$tglcair','$arrnamabank[$i]','$arrnorek[$i]','$arrnamapenerima[$i]','$pengaju','$divisi','$waktu','Belum Di Bayar','Belum Disetujui','$termfinal','$statusbpu','$nama_gambar','$transferid', 1, '$arremailpenerima[$i]', '$keterangan_pembayaran', '$time')");

                        $idBpu = mysqli_insert_id($koneksi);
                        $insertDataNeedVerifikasi = mysqli_query($koneksi, "INSERT INTO bpu_verify (id_bpu) VALUES ('$idBpu')");
                        $queryBpuVerify = mysqli_query($koneksi, "SELECT * FROM bpu_verify WHERE id_bpu = '$idBpu'");
                        $dataVerify = [];
                        while($row = mysqli_fetch_assoc($queryBpuVerify)) {
                            $dataVerify = $row;
                        }
                    }
                }
            }

            if ($_SESSION['divisi'] != 'Direksi' || ($isEksternalProcess && $_SESSION['divisi'] != 'FINANCE' && $_SESSION['level'] != 'Manager')) {
                $notification = 'Pembuatan BPU Eksternal Berhasil. Pemberitahuan via email telah terkirim ke ';
            } else {
                $notification = '';
            }

            $i = 0;

            $namaPenerima = "";
            for ($i = 0; $i < count($arremailpenerima); $i++) {
                $notification .= ($arrnamapenerima[$i] . ' (' . $arremailpenerima[$i] . ')');
                $namaPenerima .= $arrnamapenerima[$i];
                if ($i < count($arremailpenerima) - 1) $notification .= ', ';
                else $notification .= '.';
            }
            if ($_SESSION['divisi'] != 'Direksi' || ($isEksternalProcess && $_SESSION['divisi'] != 'FINANCE' && $_SESSION['level'] != 'Manager')) {
                $notification = ' Dan Pemberitahuan via whatsapp telah terkirim ke ';
            } else {
                $notification = 'Pembuatan BPU Eksternal Berhasil. Pemberitahuan via whatsapp telah terkirim ke';
            }

            array_unique($emailInternal);
            array_unique($namaInternal);

            for($i = 0; $i < count($emailInternal); $i++) {
                $path = '/views.php';

                if (!$cuti->checkStatusCutiUser($namaInternal[$i]) || $_SESSION['nama_user'] != $namaInternal[$i]) {
                    if ($isEksternalProcess && $dataDivisi[$i] != "Direksi") {
                        $path = '/view-bpu-verify.php?id='.$dataVerify["id"].'&bpu='.$dataVerify["id_bpu"];
                    } else {
                        if ($dataDivisi[$i] == 'FINANCE') {
                            $pathManager = ($dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager") && $jenis == 'B1' ? '/view-finance-manager-b1.php?code='.$numb.'' : '/view-finance-manager.php?code='.$numb.'';
                            $pathManager = ($dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager") && $jenis == 'Non Rutin' ? '/view-finance-nonrutin-manager.php?code='.$numb.'' : '/view-finance-manager.php?code='.$numb.'';
                            $pathKaryawan = ($dataLevel[$i] != "Manager" || $dataLevel[$i] != "Senior Manager") && $jenis == 'Non Rutin' ? '/view-finance-nonrutin.php?code='.$numb.'' : '/view-finance.php?code='.$numb.'';
                            $path =  $dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager" ? $pathManager : $pathKaryawan;
                            $path =  $dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager" ? $pathManager : $pathKaryawan;
                        } else if ($dataDivisi[$i] == 'Direksi') {
                            $path = '/views-direksi.php?code='.$numb.'';
                        }
                    }
    
                  $url =  $host. $path.'&session='.base64_encode(json_encode(["id_user" => $idUserInternal[$i], "timeout" => time()]));
                  $msg = $messageHelper->messagePengajuanBPU($namaInternal[$i], $pengaju, $namaProject, $namaPenerima, $jumlahDiterima, "", $url);
                  $msgEmail = $messageEmail->applyBPU($namaInternal[$i], $pengaju, $namaProject, $namaPenerima, $jumlahDiterima, "", $url);
                  if($emailInternal[$i] != "") $wa->sendMessage($emailInternal[$i], $msg);
                  if ($emails[$i] != "") $emailHelper->sendEmail($msgEmail, "Informais Pengajuan BPU", $emails[$i]);
    
                  $notification .= ($namaInternal[$i] . ' (' . $emailInternal[$i] . ')');
                    if ($i < count($emailInternal) - 1) $notification .= ', ';
                    else $notification .= '.';
                }
            }
        } else {
            if ($_SESSION['divisi'] == 'Direksi' || ($isEksternalProcess && $_SESSION['divisi'] == 'FINANCE' && $_SESSION['level'] == 'Manager')) {
                if ($actionProcess == "update") {
                    $insert = mysqli_query($koneksi, "UPDATE bpu SET bank_account_name='$bankAccountName', pengajuan_jumlah='$dataVerify[total_verify]', tglcair = '$tglcair', namabank= '$namabank', norek = '$norek', namapenerima = '$namapenerima', ket_pembayaran = '$keterangan_pembayaran', emailpenerima = '$emailpenerima',vendor_type = '$_POST[vendor_type]' WHERE noid = '$idBpu';
                    ");
                    $insert = mysqli_query($koneksi, "UPDATE bpu_verify SET is_need_approved = '1' WHERE id = '$idVerify'");
                } else {
                    $insert = mysqli_query($koneksi, "INSERT INTO bpu (no,bank_account_name, nama_vendor,tanggalbayar,pengajuan_jumlah,tglcair,namabank,norek,namapenerima,divisi,waktu,status,persetujuan,term,statusbpu,transfer_req_id,status_pengajuan_bpu,emailpenerima,ket_pembayaran,created_at, approveby, tglapprove) VALUES
                                                    ('$no','$bankAccountName','$vendorName','$_POST[tgl]','$jumlah','$tglcair','$namabank','$norek','$namapenerima','$divisi','$waktu','Belum Di Bayar','Disetujui (Direksi)','$termfinal','$statusbpu','$transferid', 1, '$emailpenerima', '$keterangan_pembayaran', '$time', '$_SESSION[nama_user]', '$time')");
                    $idBpu = mysqli_insert_id($koneksi);
                    $insertDataNeedVerifikasi = mysqli_query($koneksi, "INSERT INTO bpu_verify (id_bpu) VALUES ('$idBpu')");
                    $idVerify = mysqli_insert_id($koneksi);
                }

                $queryBpuVerify = mysqli_query($koneksi, "SELECT * FROM bpu_verify WHERE id_bpu = '$idBpu'");
                $dataVerify = [];
                while($row = mysqli_fetch_assoc($queryBpuVerify)) {
                    $dataVerify = $row;
                }
                
            } else {
                if ($actionProcess == "update") {
                    $insert = mysqli_query($koneksi, "UPDATE bpu SET bank_account_name='$bankAccountName', pengajuan_jumlah='$dataVerify[total_verify]', tglcair = '$tglcair', namabank= '$namabank', norek = '$norek', namapenerima = '$namapenerima', ket_pembayaran = '$keterangan_pembayaran', emailpenerima = '$emailpenerima', vendor_type = '$_POST[vendor_type]' WHERE noid = '$idBpu';
                    ");
                    $insert = mysqli_query($koneksi, "UPDATE bpu_verify SET is_need_approved = '1' WHERE id = '$idVerify'");
                } else {
                    $insert = mysqli_query($koneksi, "INSERT INTO bpu (no,bank_account_name,nama_vendor,pengajuan_jumlah,tglcair,namabank,norek,namapenerima,pengaju,divisi,waktu,status,persetujuan,term,statusbpu,fileupload,transfer_req_id, status_pengajuan_bpu,emailpenerima,ket_pembayaran,created_at, vendor_type) VALUES
                    ('$no','$bankAccountName','$vendorName','$jumlah','$tglcair','$namabank','$norek','$namapenerima','$pengaju','$divisi','$waktu','Belum Di Bayar','Belum Disetujui','$termfinal','$statusbpu','$nama_gambar','$transferid', 1, '$emailpenerima', '$keterangan_pembayaran', '$time', '$_POST[vendor_type]')");

                    $idBpu = mysqli_insert_id($koneksi);
                    $insertDataNeedVerifikasi = mysqli_query($koneksi, "INSERT INTO bpu_verify (id_bpu) VALUES ('$idBpu')");
                    $queryBpuVerify = mysqli_query($koneksi, "SELECT * FROM bpu_verify WHERE id_bpu = '$idBpu'");
                    $dataVerify = [];
                    while($row = mysqli_fetch_assoc($queryBpuVerify)) {
                        $dataVerify = $row;
                    }
                }
                
            }
            if ($aw['status'] == 'Vendor/Supplier' || $aw['status'] == 'Honor Eksternal') {
                $queryBank = mysqli_query($koneksi, "SELECT * FROM bank WHERE kodebank = '$namabank'");
                $bank = mysqli_fetch_assoc($queryBank);
                if ($aw['status'] == 'Vendor/Supplier' && $_SESSION['divisi'] != 'Direksi') {
                    $msg = "Kepada $namapenerima, <br><br>
                        Berikut informasi status pembayaran Anda:<br><br>
                        No.Invoice       : <strong>$invoice</strong><br>
                        Tgl. Invoice     : <strong>" . date_format($tgl, 'd/m/Y') . "</strong><br>
                        Term             : <strong>$term1 of $term2</strong><br>
                        Jenis Pembayaran : <strong>$jenis_pembayaran</strong><br>
                        No. Rekening Anda : <strong>$norek</strong><br>
                        Bank             : <strong>" . $bank['namabank'] . "</strong><br>
                        Nama Penerima    : <strong>$namapenerima</strong><br>
                        Jumlah Dibayarkan : <strong>Rp. " . number_format($jumlah, 0, '', '.') . "</strong><br>
                        Status           : <strong>Sedang Diproses</strong><br><br>
                        Informasi update status pembayaran akan kami kirimkan kembali melalui email. Jika ada pertanyaan lebih lanjut, silahkan email Divisi Finance ke finance@mri-research-ind.com.<br><br>
                        Hormat kami,<br>
                        Finance Marketing Research Indonesia ";
                } else if ($_SESSION['divisi'] != 'Direksi') {
                    $msg = "Kepada $namapenerima, <br><br>
                    Berikut informasi status pembayaran Anda:<br><br>
                    Nama Pembayaran  : <strong>$keterangan_pembayaran</strong><br>
                    No. Rekening Anda : <strong>$norek</strong><br>
                    Bank             : <strong>" . $bank['namabank'] . "</strong><br>
                    Nama Penerima    : <strong>$namapenerima</strong><br>
                    Jumlah Dibayarkan : <strong>Rp. " . number_format($jumlah, 0, '', '.') . "</strong><br>
                    Status           : <strong>Diproses</strong><br><br>
                    Informasi update status pembayaran akan kami kirimkan kembali melalui email. Jika ada pertanyaan lebih lanjut, silahkan email Divisi Finance ke finance@mri-research-ind.com.<br><br>
                    Hormat kami,<br>
                    Finance Marketing Research Indonesia
                    ";
                }
                $subject = "Informasi Pembayaran";

                if ($emailpenerima && $_SESSION['divisi'] != 'Direksi'|| ($isEksternalProcess && $_SESSION['divisi'] != 'FINANCE' && $_SESSION['level'] != 'Manager')) {
                    $message = $emailHelper->sendEmail($msg, $subject, $emailpenerima, $name = '', $address = "single");
                }
                $notification = 'Pembuatan BPU Eksternal Berhasil. Pemberitahuan via whatsapp telah terkirim ke ';
                for($i = 0; $i < count($emailInternal); $i++) {
                    $path = '/views.php';

                    if (!$cuti->checkStatusCutiUser($namaInternal[$i]) || $_SESSION['nama_user'] != $namaInternal[$i]) {
                        if ($isEksternalProcess && $dataDivisi[$i] != "Direksi") {
                            $path = '/view-bpu-verify.php?id='.$dataVerify["id"].'&bpu='.$dataVerify["id_bpu"];
                        } else {
                            if ($dataDivisi[$i] == 'FINANCE') {
                                $pathManager = ($dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager") && $jenis == 'B1' ? '/view-finance-manager-b1.php?code='.$numb.'' : '/view-finance-manager.php?code='.$numb.'';
                                $pathManager = ($dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager") && $jenis == 'Non Rutin' ? '/view-finance-nonrutin-manager.php?code='.$numb.'' : '/view-finance-manager.php?code='.$numb.'';
                                $pathKaryawan = ($dataLevel[$i] != "Manager" || $dataLevel[$i] != "Senior Manager") && $jenis == 'Non Rutin' ? '/view-finance-nonrutin.php?code='.$numb : '/view-finance.php?code='.$numb.'';
                                $path =  $dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager" ? $pathManager : $pathKaryawan;
                                $path =  $dataLevel[$i] == "Manager" || $dataLevel[$i] == "Senior Manager" ? $pathManager : $pathKaryawan;
                            } else if ($dataDivisi[$i] == 'Direksi') {
                                $path = '/views-direksi.php?code='.$numb.'';
                            }
                        }
                        $url =  $host. $path.'&session='.base64_encode(json_encode(["id_user" => $idUserInternal[$i], "timeout" => time()]));
                        if ($actionProcess == "update" && $isEksternalProcess) {
                            $msg = $messageHelper->messagePengajuanBPU($namaInternal[$i], $pengaju, $namaProject, $namapenerima, $jumlah, "", $url);
                            $msgEmail = $messageEmail->applyBPU($namaInternal[$i], $pengaju, $namaProject, $namapenerima, $jumlah, "", $url);
                        } else {
                            $penerima = $vendorName;
                            if ($penerima == "") {
                                $penerima = "-";
                            }
                            $msg = $messageHelper->messagePembuatanBPUEksternal($namaInternal[$i], $_SESSION['nama_user'], $namaProject, $penerima, $jumlah, "", $url);
                            $msgEmail = $messageEmail->applyBPUEksternal($namaInternal[$i], $_SESSION['nama_user'], $namaProject, $penerima, $jumlah, "", $url);
                        }
                        if($emailInternal[$i] != "") $wa->sendMessage($emailInternal[$i], $msg);
                        if ($emails[$i] != "") $emailHelper->sendEmail($msgEmail, "Informasi Pengajuan BPU", $emails[$i]);
    
                        $notification .= ($namaInternal[$i] . ' (' . $emailInternal[$i] . ')');
                        if ($i < count($emailInternal) - 1) $notification .= ', ';
                        else $notification .= '.';
                    }
                }
            }

            
            if (($_SESSION['divisi'] != 'Direksi' && $namapenerima != "") || ($isEksternalProcess && $_SESSION['divisi'] != 'FINANCE' && $_SESSION['level'] != 'Manager')) {
                $notification .= " dan Pemberitahuan via email telah terkirim ke - $namapenerima ($emailpenerima)";
            }
        }
    }

    if ($insert) {

        $q = explode('/', $path);
        if ($_SESSION["divisi"] == "Direksi") {
            echo "<script language='javascript'>";
            echo "alert('$notification!!')";
            echo "</script>";
            echo "<script> document.location.href='views-direksi.php?code=" . $numb . "'; </script>";
        }

        if($isEksternalProcess && $_SESSION['divisi'] == 'FINANCE' && $_SESSION['level'] == 'Manager') {
            
            echo "<script language='javascript'>";
            echo "alert('$notification!!')";
            echo "</script>";
            echo "<script> document.location.href='".$_SERVER['HTTP_REFERER']."'; </script>";
        }
        
        
        if (!$isEksternalProcess && $_SESSION["divisi"] != "Direksi") {
            if ($_SESSION['divisi'] == 'FINANCE') {
                if ($actionProcess = "update") {
                    echo "<script language='javascript'>";
                    echo "alert('$notification')";
                    echo "</script>";
                    echo "<script> document.location.href='view-bpu-verify.php?id=".$idVerify."&bpu=".$idBpu."&status=success'; </script>";
                } else {
                    if ($_SESSION['hak_akses'] == 'Manager') {
                        echo "<script language='javascript'>";
                        echo "alert('$notification')";
                        echo "</script>";
                        echo "<script> document.location.href='view-finance" . $isNonRutin  . "-manager.php?code=" . $numb . "'; </script>";
                    } else {
                        echo "<script language='javascript'>";
                        echo "alert('$notification')";
                        echo "</script>";
                        echo "<script> document.location.href='view-finance" . $isNonRutin  . ".php?code=" . $numb . "'; </script>";
                    }
                }
                
            } else {
                echo "<script language='javascript'>";
                echo "alert('$notification!!')";
                echo "</script>";
                echo "<script> document.location.href='views-direksi.php?code=" . $numb . "'; </script>";
            }
        } else {
            $path = '/view-bpu-verify.php?id='.$dataVerify["id"].'&bpu='.$dataVerify["id_bpu"];
            echo "<script language='javascript'>";
            echo "alert('$notification')";
            echo "</script>";
            echo "<script> document.location.href='".$q[1]."'; </script>";
        }
    } else {
        echo "Pembuatan Budget External Gagal";
    }
}
