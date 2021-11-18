<?php
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
require "vendor/email/send-email.php";
session_start();

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
$arrPenerima = [];
$arrJumlah = [];
if ($submit == 1) {
    for ($i = 0; $i < count($noid); $i++) {
        $queryBpu = mysqli_query($koneksi, "SELECT * FROM bpu WHERE noid='$noid[$i]'");
        $bpu = mysqli_fetch_assoc($queryBpu);

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
            $insert = mysqli_query($koneksiTransfer, "INSERT INTO data_transfer (transfer_req_id, transfer_type, jenis_pembayaran_id, keterangan, waktu_request, norek, pemilik_rekening, bank, kode_bank, berita_transfer, jumlah, terotorisasi, hasil_transfer, ket_transfer, nm_pembuat, nm_validasi, nm_manual, jenis_project, nm_project, noid_bpu, biaya_trf, rekening_sumber, email_pemilik_rekening) 
                    VALUES ('$formatId', '3', '$jenisPembayaran[jenispembayaranid]', '$bpu[statusbpu]', '$waktu', '$bpu[norek]', '$bpu[namapenerima]','$bank[namabank]', '$bank[kodebank]', '$berita_transfer','$pengajuan_jumlah[$i]', '2', '1', 'Antri', '$bpu[pengaju]', '$user', '', '$pengajuan[jenis]', '$nm_project', '$bpu[noid]', $biayaTrf, '$rekening_sumber', '$bpu[emailpenerima]')") or die(mysqli_error($koneksiTransfer));

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
            $update = mysqli_query($koneksi, "UPDATE bpu SET jumlah=$pengajuan_jumlah[$i], status_pengajuan_bpu = 0, checkby='$user', tglcheck='$now', metode_pembayaran = '$metode_pembayaran[$i]', umo_biaya_kode_id = '$umo_biaya_kode_id' fileupload='$nama_gambar', ket_pembayaran='$berita_transfer', rekening_sumber = '$rekening_sumber' WHERE noid = '$noid[$i]'") or die(mysqli_error($koneksi));
        } else {
            $update = mysqli_query($koneksi, "UPDATE bpu SET jumlah=$pengajuan_jumlah[$i], status_pengajuan_bpu = 0, checkby='$user', tglcheck='$now', metode_pembayaran = '$metode_pembayaran[$i]', umo_biaya_kode_id = '$umo_biaya_kode_id', ket_pembayaran='$berita_transfer', rekening_sumber = '$rekening_sumber' WHERE noid = '$noid[$i]'") or die(mysqli_error($koneksi));
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
            array_push($arrPenerima, $bpu['namapenerima']);
            array_push($arrJumlah, "Rp. " . number_format($pengajuan_jumlah[$i], 0, ",", "."));

            if ($pengajuan_jumlah[$i] <= $setting['plafon']) {
                if ($pengajuan['jenis'] == 'B1' || $pengajuan['jenis'] == 'B2') {
                    $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('1', '3') AND hak_akses='Manager'");
                    while ($e = mysqli_fetch_assoc($queryEmail)) {
                        if ($e['email']) {
                            array_push($email, $e['email']);
                            array_push($nama, $e['nama_user']);
                        }
                    }
                } else {
                    $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('2', '3') AND hak_akses='Manager'");
                    while ($e = mysqli_fetch_assoc($queryEmail)) {
                        if ($e['email']) {
                            array_push($email, $e['email']);
                            array_push($nama, $e['nama_user']);
                        }
                    }
                }
            } else {
                if ($pengajuan['jenis'] != 'Rutin') {
                    $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user FROM tb_user WHERE divisi='Direksi' AND aktif='Y'");
                    while ($e = mysqli_fetch_assoc($queryEmail)) {
                        if ($e['email']) {
                            array_push($email, $e['email']);
                            array_push($nama, $e['nama_user']);
                        }
                    }
                }
            }

            $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user,divisi FROM tb_user WHERE nama_user = '$bpu[pengaju]' AND aktif='Y'");
            $emailUser = mysqli_fetch_assoc($queryEmail);
            if ($emailUser) {
                array_push($email, $emailUser['email']);
                array_push($nama, $emailUser['nama_user']);
            }

            $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user,divisi FROM tb_user WHERE nama_user = '$bpu[acknowledged_by]' AND aktif='Y'");
            $emailUser = mysqli_fetch_assoc($queryEmail);
            if ($emailUser) {
                array_push($email, $emailUser['email']);
                array_push($nama, $emailUser['nama_user']);
            }
        }
    }
    $email = array_unique($email);
    $nama = array_unique($nama);

    $msg = "Notifikasi BPU, <br><br>
        BPU telah di verifikasi oleh Finance dengan keterangan sebagai berikut:<br><br>
        Nama Project   : <strong>" . $pengajuan['nama'] . "</strong><br>
        Item No.       : <strong>$no</strong><br>
        Term           : <strong>$term</strong><br>
        Nama Pengaju   : <strong>" . $bpu['pengaju'] . "</strong><br>
        Nama Penerima  : <strong>" . implode(', ', $arrPenerima) . "</strong><br>
        Total Diajukan : <strong>" . implode(', ', $arrJumlah) . "</strong><br>
        ";
    if ($keterangan) {
        $msg .= "Keterangan:<strong> $keterangan </strong><br><br>";
    } else {
        $msg .= "<br>";
    }
    $msg .= "Klik <a href='$url'>Disini</a> untuk membuka aplikasi budget.";
    $subject = "Notifikasi Aplikasi Budget";

    if ($email) {
        $message = sendEmail($msg, $subject, $email, $name, $address = "multiple");
    }

    if ($pengajuan['jenis'] != 'Rutin') {
        $notification = 'Verifikasi BPU Sukses. Pemberitahuan via email telah terkirim ke ';
        $i = 0;
        for ($i = 0; $i < count($email); $i++) {
            $notification .= ($nama[$i] . ' (' . $email[$i] . ')');
            if ($i < count($email) - 1) $notification .= ', ';
            else $notification .= '.';
        }
    } else {
        $notification = 'Verifikasi BPU Sukses.';
    }
} else if ($submit == 0) {
    $update = mysqli_query($koneksi, "UPDATE bpu SET status_pengajuan_bpu = 2, alasan_tolak_bpu = '$alasanTolakBpu' WHERE no='$no' AND waktu='$waktu' AND term='$term'");

    $queryBpu = mysqli_query($koneksi, "SELECT * FROM bpu WHERE no='$no' AND waktu='$waktu' AND term='$term'");
    while ($bpu = mysqli_fetch_assoc($queryBpu)) {
        array_push($arrPenerima, $bpu['namapenerima']);
        array_push($arrJumlah, "Rp. " . number_format($bpu['pengajuan_jumlah'], 0, ",", "."));

        $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user,divisi FROM tb_user WHERE nama_user = '$bpu[pengaju]' AND aktif='Y'");
        $emailUser = mysqli_fetch_assoc($queryEmail);
        if ($emailUser) {
            array_push($email, $emailUser['email']);
            array_push($nama, $emailUser['nama_user']);
        }

        $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user,divisi FROM tb_user WHERE nama_user = '$bpu[acknowledged_by]' AND aktif='Y'");
        $emailUser = mysqli_fetch_assoc($queryEmail);
        if ($emailUser) {
            array_push($email, $emailUser['email']);
            array_push($nama, $emailUser['nama_user']);
        }

        if ($pengajuan['jenis'] == 'B1' || $pengajuan['jenis'] == 'B2') {
            $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('1', '3')");
            while ($e = mysqli_fetch_assoc($queryEmail)) {
                if ($e['email']) {
                    array_push($email, $e['email']);
                    array_push($nama, $e['nama_user']);
                }
            }
        } else {
            $queryEmail = mysqli_query($koneksi, "SELECT email,nama_user FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('2', '3')");
            while ($e = mysqli_fetch_assoc($queryEmail)) {
                if ($e['email']) {
                    array_push($email, $e['email']);
                    array_push($nama, $e['nama_user']);
                }
            }
        }
    }

    $email = array_unique($email);
    $nama = array_unique($nama);

    $msg = "Notifikasi BPU, <br><br>
        BPU telah di tolak oleh Finance dengan keterangan sebagai berikut:<br><br>
        Nama Project   : <strong>" . $pengajuan['nama'] . "</strong><br>
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
    $msg .= "Klik <a href='$url'>Disini</a> untuk membuka aplikasi budget.";
    $subject = "Notifikasi Aplikasi Budget";

    if ($email) {
        $message = sendEmail($msg, $subject, $email, $name, $address = "multiple");
    }
    $notification = 'BPU telah ditolak. Pemberitahuan via email telah terkirim ke ';
    $i = 0;
    for ($i = 0; $i < count($email); $i++) {
        $notification .= ($nama[$i] . ' (' . $email[$i] . ')');
        if ($i < count($email) - 1) $notification .= ', ';
        else $notification .= '.';
    }
}

if ($update) {
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
