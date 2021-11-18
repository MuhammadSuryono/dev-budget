<?php
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
require "vendor/email/send-email.php";
session_start();

$url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$url = explode('/', $url);
$url = $url[0] . '/' . $url[1] . '/' . 'login.php';

$divisi = $_SESSION['divisi'];

$waktu = $_POST['waktu'];
$noid = $_POST['noid'];
$jumlah = $_POST['jumlah'];
$namapenerima = $_POST['namapenerima'];
$email = $_POST['email'];
$bank = $_POST['namabank'];
$norek = $_POST['norek'];
$tglcair = $_POST['tglcair'];
$keterangan_pembayaran = $_POST['keterangan_pembayaran'];

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

if ($_FILES["gambar"]["name"]) {
    $extension = pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION);
    $nama_gambar = random_bytes(20) . "." . $extension;
    $target_file = "uploads/" . $nama_gambar;
    move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file);
}

$email = [];
$nama = [];
$arrPenerima = [];
$arrJumlah = [];
if ($_POST['submit'] == 1) {
    if (is_array($noid)) {
        for ($i = 0; $i < count($noid); $i++) {
            $queryBpu = mysqli_query($koneksi, "SELECT * FROM bpu WHERE noid = '$noid[$i]'");
            $bpu = mysqli_fetch_assoc($queryBpu);
            array_push($arrPenerima, $namapenerima[$i]);
            array_push($arrJumlah, "Rp. " . number_format($jumlah[$i], 0, ",", "."));
            $pengaju = $bpu['pengaju'];

            if ($divisi == 'FINANCE') {
                if ($nama_gambar) {
                    $update = mysqli_query($koneksi, "UPDATE bpu SET status_pengajuan_bpu=1, pengajuan_jumlah=$jumlah[$i], fileupload='$nama_gambar', namapenerima='$namapenerima[$i]', namabank='$bank[$i]', norek='$norek[$i]' WHERE noid = '$noid[$i]'") or die(mysqli_error($koneksi));
                } else {
                    $update = mysqli_query($koneksi, "UPDATE bpu SET status_pengajuan_bpu=1, pengajuan_jumlah=$jumlah[$i], namapenerima='$namapenerima[$i]', namabank='$bank[$i]', norek='$norek[$i]' WHERE noid = '$noid[$i]'") or die(mysqli_error($koneksi));
                }
            } else {
                if ($nama_gambar) {
                    $update = mysqli_query($koneksi, "UPDATE bpu SET status_pengajuan_bpu=3, pengajuan_jumlah=$jumlah[$i], fileupload='$nama_gambar', namapenerima='$namapenerima[$i]', namabank='$bank[$i]', norek='$norek[$i]' WHERE noid = '$noid[$i]'") or die(mysqli_error($koneksi));
                } else {
                    $update = mysqli_query($koneksi, "UPDATE bpu SET status_pengajuan_bpu=3, pengajuan_jumlah=$jumlah[$i], namapenerima='$namapenerima[$i]', namabank='$bank[$i]', norek='$norek[$i]' WHERE noid = '$noid[$i]'") or die(mysqli_error($koneksi));
                }
            }
        }
    } else {
        $queryBpu = mysqli_query($koneksi, "SELECT * FROM bpu WHERE noid = '$noid'");
        $bpu = mysqli_fetch_assoc($queryBpu);
        $pengaju = $bpu['pengaju'];
        array_push($arrPenerima, $namapenerima);
        array_push($arrJumlah, "Rp. " . number_format($jumlah, 0, ",", "."));

        if ($divisi == 'FINANCE') {
            if ($nama_gambar) {
                $update = mysqli_query($koneksi, "UPDATE bpu SET status_pengajuan_bpu=1, pengajuan_jumlah=$jumlah  , fileupload='$nama_gambar', namapenerima='$namapenerima', namabank='$bank', norek='$norek' WHERE noid = '$noid'") or die(mysqli_error($koneksi));
            } else {
                $update = mysqli_query($koneksi, "UPDATE bpu SET status_pengajuan_bpu=1, pengajuan_jumlah=$jumlah, namapenerima='$namapenerima', namabank='$bank', norek='$norek' WHERE noid = '$noid'") or die(mysqli_error($koneksi));
            }
        } else {
            if ($nama_gambar) {
                $update = mysqli_query($koneksi, "UPDATE bpu SET status_pengajuan_bpu=3, pengajuan_jumlah=$jumlah, fileupload='$nama_gambar', namapenerima='$namapenerima', namabank='$bank', norek='$norek' WHERE noid = '$noid'") or die(mysqli_error($koneksi));
            } else {
                $update = mysqli_query($koneksi, "UPDATE bpu SET status_pengajuan_bpu=3, pengajuan_jumlah=$jumlah, namapenerima='$namapenerima', namabank='$bank', norek='$norek' WHERE noid = '$noid'") or die(mysqli_error($koneksi));
            }
        }
    }
}

if ($divisi == 'FINANCE') {
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
} else {
    $queryUserByDivisi = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi = '$emailUser[divisi]' AND (level = 'Manager' OR level = 'Senior Manager') AND aktif='Y'");
    $user = mysqli_fetch_assoc($queryUserByDivisi);
    if ($user) {
        array_push($email, $user['email']);
        array_push($nama, $user['nama_user']);
    }
}

$queryEmail = mysqli_query($koneksi, "SELECT email,nama_user,divisi FROM tb_user WHERE nama_user = '$pengajuan[pembuat]' AND aktif='Y'");
$emailUser = mysqli_fetch_assoc($queryEmail);
if ($emailUser) {
    array_push($email, $emailUser['email']);
    array_push($nama, $emailUser['nama_user']);
}

$nama = array_unique($nama);
$email = array_unique($email);

$msg = "Notifikasi BPU, <br><br>
BPU telah diajukan dengan keterangan sebagai berikut:<br><br>
Nama Project      : <strong>" . $pengajuan['nama'] . "</strong><br>
Nama Pengaju      : <strong>$pengaju</strong><br>
Nama Penerima     : <strong>" . implode(', ', $arrPenerima) . "</strong><br>
Jumlah Diajukan   : <strong>" . implode(', ', $arrJumlah) . "</strong><br>
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

$notification = 'Pengajuan Kembali BPU Sukses. Pemberitahuan via email telah terkirim ke ';
$i = 0;
for ($i = 0; $i < count($email); $i++) {
    $notification .= ($nama[$i] . ' (' . $email[$i] . ')');
    if ($i < count($email) - 1) $notification .= ', ';
    else $notification .= '.';
}

if ($update) {
    if ($divisi == 'FINANCE') {
        if ($_SESSION['hak_akses'] == 'Manager') {
            echo "<script language='javascript'>";
            echo "alert('$notification')";
            echo "</script>";
            echo "<script> document.location.href='view-finance" . $isNonRutin  . "-manager.php?code=" . $kode . "'; </script>";
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
        echo "<script> document.location.href='views.php?code=" . $kode . "'; </script>";
    }
} else {
    if ($divisi == 'FINANCE') {
        if ($_SESSION['hak_akses'] == 'Manager') {
            echo "<script language='javascript'>";
            echo "alert('Gagal')";
            echo "</script>";
            echo "<script> document.location.href='view-finance" . $isNonRutin  . "-manager.php?code=" . $kode . "'; </script>";
        } else {
            echo "<script language='javascript'>";
            echo "alert('Gagal')";
            echo "</script>";
            echo "<script> document.location.href='view-finance" . $isNonRutin  . ".php?code=" . $kode . "'; </script>";
        }
    } else {
        echo "<script language='javascript'>";
        echo "alert('Gagal')";
        echo "</script>";
        echo "<script> document.location.href='views.php?code=" . $kode . "'; </script>";
    }
}
