<?php
//error_reporting(0);
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
require "vendor/email/send-email.php";

session_start();
if (!isset($_SESSION['nama_user'])) {
  header("location:login.php");
  // die('location:login.php');//jika belum login jangan lanjut
}

$time = date("Y-m-d H:i:s");

$no           = $_POST['no'];
$jumlah       = $_POST['jumlah'];
$waktu        = $_POST['waktu'];
// $tglcair      = $_POST['tglcair'];

$tglcair      = ($_POST['tglcair']) ? $_POST['tglcair'] : null;
$pengaju      = $_POST['pengaju'];
$divisi       = $_POST['divisi'];
$arrnamabank     = $_POST['namabank'];
$arrnorek        = $_POST['norek'];
$arrnamapenerima = $_POST['namapenerima'];
$arremailpenerima = $_POST['email'];
$statusbpu    = $_POST['statusbpu'];
if ($statusbpu == 'Vendor/Supplier') {
  $invoice = str_replace('.', '', $_POST['invoice']);
  $tgl = date_create($_POST['tgl']);
  $term1 = $_POST['term1'];
  $term2 = $_POST['term2'];
  $jenis_pembayaran = str_replace('.', '', $_POST['jenis_pembayaran']);
  $keterangan_pembayaran = "INV." . $invoice . "." . date_format($tgl, 'dmy') . ".T" . $term1 . "/" . $term2 . "." . $jenis_pembayaran;
} else {
  $keterangan_pembayaran    = $_POST['keterangan_pembayaran'];
}

// var_dump($keterangan_pembayaran);
// die;

for ($i = 0; $i < count($arrnamapenerima); $i++) {
  $namapenerima .= $arrnamapenerima[$i];
  if ($i < count($arrnamapenerima) - 1)
    $namapenerima .= ', ';
}
for ($i = 0; $i < count($arrnorek); $i++) {
  $norek .= $arrnorek[$i];
  if ($i < count($arrnorek) - 1)
    $norek .= ', ';
}
for ($i = 0; $i < count($arrnamabank); $i++) {
  $namabank .= $arrnamabank[$i];
  if ($i < count($arrnamabank) - 1)
    $namabank .= ', ';
}
for ($i = 0; $i < count($arremailpenerima); $i++) {
  $emailpenerima .= $arremailpenerima[$i];
  if ($i < count($arremailpenerima) - 1)
    $emailpenerima .= ', ';
}

//periksa apakah udah submit
if (isset($_POST['submit'])) {

  if ($_SESSION['divisi'] == 'FINANCE') {
    $nama_gambar  = $_FILES['gambar']['name'];
    $lokasi       = $_FILES['gambar']['tmp_name']; // Menyiapkan tempat nemapung gambar yang diupload
    $lokasitujuan = "uploads/"; // Menguplaod gambar kedalam folder ./image
    $upload       = move_uploaded_file($lokasi, $lokasitujuan . "/" . $nama_gambar);
  } else {
    echo "";
  }

  $sel1 = mysqli_query($koneksi, "SELECT noid,jenis FROM pengajuan WHERE waktu='$waktu'");
  $uc = mysqli_fetch_assoc($sel1);
  $numb = $uc['noid'];
  $jenis = $uc['jenis'];

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

  $caribayar = mysqli_query($koneksi, "SELECT status FROM bpu WHERE waktu='$waktu' AND no='$no' AND status='Belum Di Bayar'");

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

    if ($_SESSION['divisi'] == 'Direksi') {
      $insert = mysqli_query($koneksi, "INSERT INTO bpu (no,pengajuan_jumlah,tglcair,namabank,norek,namapenerima,pengaju,divisi,waktu,status,persetujuan,term,statusbpu,transfer_req_id,status_pengajuan_bpu,emailpenerima,ket_pembayaran,created_at) VALUES
                                                ('$no','$jumlah','$tglcair','$namabank','$norek','$namapenerima','$pengaju','$divisi','$waktu','Belum Di Bayar','Disetujui (Direksi)','$termfinal','$statusbpu','$transferid', 1, '$emailpenerima', '$keterangan_pembayaran', '$time')");
    } else {
      $insert = mysqli_query($koneksi, "INSERT INTO bpu (no,pengajuan_jumlah,tglcair,namabank,norek,namapenerima,pengaju,divisi,waktu,status,persetujuan,term,statusbpu,fileupload,transfer_req_id, status_pengajuan_bpu,emailpenerima,ket_pembayaran,created_at) VALUES
                                                ('$no','$jumlah','$tglcair','$namabank','$norek','$namapenerima','$pengaju','$divisi','$waktu','Belum Di Bayar','Belum Disetujui','$termfinal','$statusbpu','$nama_gambar','$transferid', 1, '$emailpenerima', '$keterangan_pembayaran', '$time')");
    }

    if ($insert) {
      if ($aw['status'] == 'Vendor/Supplier' || $aw['status'] == 'Honor Eksternal') {
        $queryBank = mysqli_query($koneksi, "SELECT * FROM bank WHERE kodebank = '$namabank'");
        $bank = mysqli_fetch_assoc($queryBank);
        if ($aw['status'] == 'Vendor/Supplier') {
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
        Finance Marketing Research Indonesia
        ";
        } else {
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

        if ($arremailpenerima) {
          $message = sendEmail($msg, $subject, $arremailpenerima, $name = '', $address = "multiple");
        }
      }
    }
  }

  if ($insert) {
    if ($_SESSION['divisi'] == 'FINANCE') {
      if ($_SESSION['hak_akses'] == 'Manager') {
        echo "<script language='javascript'>";
        echo "alert('Pembuatan BPU Eksternal Berhasil')";
        echo "</script>";
        echo "<script> document.location.href='view-finance" . $isNonRutin  . "-manager.php?code=" . $numb . "'; </script>";
      } else {
        echo "<script language='javascript'>";
        echo "alert('Pembuatan BPU Eksternal Berhasil')";
        echo "</script>";
        echo "<script> document.location.href='view-finance" . $isNonRutin  . ".php?code=" . $numb . "'; </script>";
      }
    } else {
      echo "<script language='javascript'>";
      echo "alert('Pembuatan BPU Eksternal Berhasil!!')";
      echo "</script>";
      echo "<script> document.location.href='views-direksi.php?code=" . $numb . "'; </script>";
    }
  } else {
    $error =  preg_replace('/[^\p{L}\p{N}\s]/u', '',  $koneksi->error);
    if ($_SESSION['divisi'] == 'FINANCE') {
      if ($_SESSION['hak_akses'] == 'Manager') {
        echo "<script language='javascript'>";
        echo "alert('Pembuatan Budget External Gagal. Error : ($koneksi->errno) $error')";
        echo "</script>";
        echo "<script> document.location.href='view-finance" . $isNonRutin  . "-manager.php?code=" . $numb . "'; </script>";
      } else {
        echo "<script language='javascript'>";
        echo "alert('Pembuatan Budget External Gagal. Error : ($koneksi->errno) $error')";
        echo "</script>";
        echo "<script> document.location.href='view-finance" . $isNonRutin  . ".php?code=" . $numb . "'; </script>";
      }
    } else {
      echo "<script language='javascript'>";
      echo "alert('Pembuatan Budget External Gagal. Error : ($koneksi->errno) $error')";
      echo "</script>";
      echo "<script> document.location.href='views-direksi.php?code=" . $numb . "'; </script>";
    }
  }
}
