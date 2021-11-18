<?php

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

session_start();

if ($_SESSION['divisi'] == 'Direksi') {
    $url = "home-direksi.php";
} else {
    $url = "home.php";
}
$date = date('Y-m-d H:i:s');

$id = $_POST['id'];
$namaUser = $_POST['namaUser'];
$divisiUser = $_POST['divisiUser'];
$namaProject = $_POST['namaProject'];
$tahun = $_POST['tahun'];
$kategori = $_POST['kategori'];
$totalKategori = $_POST['tKeseluruhan'];
$kodeProject = $_POST['kodeProject'];
$tanggalPembayaran = $_POST['tanggal_pembayaran'];

$arrNama = $_POST['nama'];
$arrKota = $_POST['kota'];
$arrStatus = $_POST['status'];
$arrPUang = $_POST['pUang'];
$arrHarga = $_POST['harga'];
$arrQuantity = $_POST['quantity'];
$arrTHarga = $_POST['tHarga'];
$arrIdData = $_POST['idData'];

$arrNamaB1B1 = ['Honor Jakarta', 'Honor Luar Kota', 'STKB Transaksi Jakarta', 'STKB Transaksi Luar Kota', 'STKB OPS'];
$arrKotaB1 = ['Jabodetabek', 'Luar kota', 'Jabodetabek', 'Luar Kota', 'Jabodetabek dan Luar Kota'];
$arrStatusB1 = ['Honor Jakarta', 'Honor Luar Kota', 'STKB TRK Jakarta', 'STKB TRK Luar Kota', 'STKB OPS'];
$arrPenerimaB1 = ['Shopper/PWT', 'Shopper/PWT', 'TLF', 'TLF', 'TLF'];
$kodepro = $_POST['kodepro'];

$queryWaktu = mysqli_query($koneksi, "SELECT * FROM pengajuan_request WHERE id=$id") or die(mysqli_error($koneksi));
// $waktu = mysqli_fetch_array($queryWaktu)[0];
$data =  mysqli_fetch_assoc($queryWaktu);
$waktuG = $data['waktu'];
$jenis = $data['jenis'];

if ($jenis == 'B1' && $_POST['kodeProject'] != 'undefined') {
    if (count($kodepro) == 1) {
        $kode = $kodepro[0];
        $updatePengajuanRequest = mysqli_query($koneksi, "UPDATE pengajuan_request SET kode_project='$kode', waktu='$waktuG' WHERE id=$id");
    } else {
        $queryPengajuanRequest = mysqli_query($koneksi, "SELECT * FROM pengajuan_request WHERE id=$id");
        $pengajuanRequest = mysqli_fetch_assoc($queryPengajuanRequest);
        $jenis = $pengajuanRequest['jenis'];
        $nama = $pengajuanRequest['nama'];
        $tahun = $pengajuanRequest['tahun'];
        $pembuat = $pengajuanRequest['pembuat'];
        $pengaju = $pengajuanRequest['pengaju'];
        $divisi = $pengajuanRequest['divisi'];
        $totalbudget = $pengajuanRequest['totalbudget'];
        $status_request = $pengajuanRequest['status_request'];
        $on_revision_status = $pengajuanRequest['on_revision_status'];
        $waktu = $pengajuanRequest['waktu'];
        for ($i = 0; $i < count($kodepro); $i++) {
            $kode = $kodepro[$i];
            if ($i == 0) {
                $updatePengajuanRequest = mysqli_query($koneksi, "UPDATE pengajuan_request SET kode_project='$kode', waktu='$waktuG' WHERE id=$id") or die(mysqli_error($koneksi));
            } else {
                $insertPengajuanRequest = mysqli_query($koneksi, "INSERT INTO pengajuan_request(jenis, nama, tahun, pembuat, pengaju, divisi, totalbudget, status_request, kode_project, on_revision_status, waktu) VALUES (
                                                '$jenis', 
                                                '$nama', 
                                                '$tahun',
                                                '$pembuat',
                                                '$pengaju',
                                                '$divisi',
                                                '$totalbudget',
                                                '$status_request',
                                                '$kode',
                                                '$on_revision_status',
                                                '$waktuG')") or die(mysqli_error($koneksi));
            }
        }


        for ($j = 0; $j < count($kodepro) - 1; $j++) {
            $queryCheckId = mysqli_query($koneksi, "SELECT id FROM pengajuan_request ORDER BY ID DESC LIMIT 1");
            $checkId = mysqli_fetch_assoc($queryCheckId)["id"];
            $checkId -= $j;
            for ($i = 0; $i  < count($arrNama); $i++) {
                $nama = $arrNama[$i];
                $kota = $arrKota[$i];
                $status = $arrStatus[$i];
                $pUang = $arrPUang[$i];
                $harga = $arrHarga[$i];
                $quantity = $arrQuantity[$i];
                $totalHarga = $arrTHarga[$i];
                $checkId = (int)$checkId;
                $urutan = $i + 1;
                if ($nama != "") {
                    $insertSelesaiRequest = mysqli_query($koneksi, "INSERT INTO selesai_request(urutan, id_pengajuan_request, rincian, kota, status, penerima, harga, quantity, total, pengaju, divisi, waktu) VALUES(                             
                                                        '$urutan',
                                                        '$checkId',
                                                        '$nama',
                                                        '$kota',
                                                        '$status',
                                                        '$pUang',
                                                        '$harga',
                                                        '$quantity',
                                                        '$totalHarga',
                                                        '$namaUser',
                                                        '$divisiUser',
                                                        '$waktuG')                           
                                                        ") or die(mysqli_error($koneksi));
                }
            }
        }
    }
}

if ($id) {
    $queryWaktu = mysqli_query($koneksi, "SELECT waktu FROM pengajuan_request WHERE id=$id") or die(mysqli_error($koneksi));
    $waktu = mysqli_fetch_array($queryWaktu)[0];
    $updatePengajuaRequest = mysqli_query($koneksi, "UPDATE pengajuan_request SET totalbudget='$totalKategori', waktu='$waktuG' WHERE waktu='$waktuG'") or die(mysqli_error($koneksi));

    mysqli_query($koneksi, "DELETE FROM reminder_tanggal_bayar WHERE selesai_waktu = '$waktuG'");
    // var_dump($arrIdData);
    // die;
    if ($updatePengajuaRequest) {
        for ($i = 0; $i < count($arrNama); $i++) {
            $nama = $arrNama[$i];
            $kota = $arrKota[$i];
            $status = $arrStatus[$i];
            $pUang = $arrPUang[$i];
            $harga = $arrHarga[$i];
            $quantity = $arrQuantity[$i];
            $tHarga = $arrTHarga[$i];

            if ($arrIdData[$i]) {
                $idData = $arrIdData[$i];
                $querySelesaiReq = mysqli_query($koneksi, "SELECT urutan, waktu FROM selesai_request WHERE id=$idData") or die(mysqli_error($koneksi));
                $data = mysqli_fetch_assoc($querySelesaiReq);
                $urutan = $data['urutan'];

                $insertSelesaiRequest = mysqli_query($koneksi, "UPDATE selesai_request SET rincian = '$nama', kota = '$kota', status = '$status', penerima = '$pUang', harga = '$harga', quantity = '$quantity', total = '$tHarga', waktu='$waktuG' WHERE waktu='$waktuG' AND urutan='$urutan'") or die(mysqli_error($koneksi));

                $arrTanggal = explode(',', $tanggalPembayaran[$i]);
                foreach ($arrTanggal as $at) {
                    if ($at && $at != '-') {
                        $newDate = date("Y-m-d", strtotime($at));

                        mysqli_query($koneksi, "INSERT INTO `reminder_tanggal_bayar`( `selesai_no`, `selesai_waktu`, `tanggal`, `created_at`) VALUES ('$urutan','$waktuG','$newDate','$date')");
                    }
                }
            } else {
                $countData = mysqli_query($koneksi, "SELECT COUNT(id) as count FROM pengajuan_request WHERE waktu='$waktuG'") or die(mysqli_error($koneksi));
                $countData = mysqli_fetch_array($countData)[0];
                $urutan = mysqli_query($koneksi, "SELECT COUNT(id) as urutan FROM selesai_request WHERE id_pengajuan_request='$id'") or die(mysqli_error($koneksi));
                $urutan = mysqli_fetch_array($urutan)[0];
                $urutan += 1;

                for ($j = 0; $j < $countData; $j++) {
                    $newId = $id + $j;
                    if ($nama != "") {
                        $insertSelesaiRequest = mysqli_query($koneksi, "INSERT INTO selesai_request(urutan, id_pengajuan_request, rincian, kota, status, penerima, harga, quantity, total, pengaju, divisi, waktu) VALUES(                             
                '$urutan',
                '$newId',
                '$nama',
                '$kota',
                '$status',
                '$pUang',
                '$harga',
                '$quantity',
                '$tHarga',
                '$namaUser',
                '$divisiUser',
                '$waktuG')                           
                ") or die(mysqli_error($koneksi));
                    }
                }

                $arrTanggal = explode(',', $tanggalPembayaran[$i]);
                foreach ($arrTanggal as $at) {
                    if ($at && $at != '-') {
                        $newDate = date("Y-m-d", strtotime($at));

                        mysqli_query($koneksi, "INSERT INTO `reminder_tanggal_bayar`( `selesai_no`, `selesai_waktu`, `tanggal`, `created_at`) VALUES ('$urutan','$waktuG','$newDate','$date')");
                    }
                }
            }
        }
        // die;
        echo "<script language='javascript'>";
        echo "alert('Data Berhasil Disimpan')";
        echo "</script>";
        echo "<script> document.location.href='view-request.php?id=" . $id . "'; </script>";
    } else {
        echo "<script language='javascript'>";
        echo "alert('Data Gagal Disimpan')";
        echo "</script>";
        echo "<script> document.location.href='view-request.php?id=" . $id . "'; </script>";
    }
    // } else if ($jenis == "Non Rutin") {
    //     $urutan = mysqli_query($koneksi, "SELECT COUNT(id) as urutan FROM selesai_request WHERE id_pengajuan_request='$id'") or die(mysqli_error($koneksi));
    //     $urutan = mysqli_fetch_array($urutan)[0];
    //     $urutan += 1;
    //     $insertSelesaiRequest = mysqli_query($koneksi, "INSERT INTO selesai_request(urutan, id_pengajuan_request, rincian, kota, status, penerima, harga, quantity, total, pengaju, divisi, waktu) VALUES(                             
    //         '$urutan',
    //         '$id',
    //         '$nama',
    //         '$kota',
    //         '$status',
    //         '$pUang',
    //         '$harga',
    //         '$quantity',
    //         '$tHarga',
    //         '$namaUser',
    //         '$divisiUser',
    //         '$waktu')                           
    //         ") or die(mysqli_error($koneksi));
    // }
} else {
    if (!$namaProject || !$tahun) {
        echo "<script language='javascript'>";
        echo "alert('Data Gagal Disimpan, Harap isi semua data')";
        echo "</script>";
        echo "<script> document.location.href='request-budget.php'; </script>";
    }
    if ($kategori == 'B1') {
        if (!$kodeProject) {
            echo "<script language='javascript'>";
            echo "alert('Data Gagal Disimpan, Harap isi semua data')";
            echo "</script>";
            echo "<script> document.location.href='request-budget.php'; </script>";
        }
    }
    $countInsert = 0;
    for ($i = 0; $i < count($kodeProject); $i++) {
        $kode = $kodeProject[$i];
        $insertPengajuanRequest = mysqli_query($koneksi, "INSERT INTO pengajuan_request(jenis, nama, tahun, pengaju, divisi, totalbudget, status_request, kode_project) VALUES (
                                                '$kategori', 
                                                '$namaProject', 
                                                '$tahun',
                                                '$namaUser',
                                                '$divisiUser',
                                                '$totalKategori',
                                                'Belum Di Ajukan',
                                                '$kode')");
        if ($insertPengajuanRequest) $countInsert++;
    }


    if (count($kodeProject) == $countInsert) {
        $countSelesai = 0;
        for ($j = 0; $j < count($kodeProject); $j++) {
            $queryCheckId = mysqli_query($koneksi, "SELECT id FROM pengajuan_request ORDER BY ID DESC LIMIT 1");
            $checkId = mysqli_fetch_assoc($queryCheckId)["id"];
            $checkId -= $j;
            for ($i = 0; $i < count($arrNama); $i++) {
                $nama = $arrNama[$i];
                $kota = $arrKota[$i];
                $status = $arrStatus[$i];
                $pUang = $arrPUang[$i];
                $harga = $arrHarga[$i];
                $quantity = $arrQuantity[$i];
                $tHarga = $arrTHarga[$i];
                $checkId = (int)$checkId;
                $urutan = $i + 1;

                if ($nama != "") {
                    $insertSelesaiRequest = mysqli_query($koneksi, "INSERT INTO selesai_request(urutan, id_pengajuan_request, rincian, kota, status, penerima, harga, quantity, total, pengaju, divisi) VALUES(                             
                                                        '$urutan',
                                                        '$checkId',
                                                        '$nama',
                                                        '$kota',
                                                        '$status',
                                                        '$pUang',
                                                        '$harga',
                                                        '$quantity',
                                                        '$tHarga',
                                                        '$namaUser',
                                                        '$divisiUser')                           
                                                        ") or die(mysqli_error($koneksi));
                }
            }
        }
        echo "<script language='javascript'>";
        echo "alert('Data Berhasil Disimpan')";
        echo "</script>";
        echo "<script> document.location.href='home.php'; </script>";
    } else {
        echo "<script language='javascript'>";
        echo "alert('Data Gagal Disimpan')";
        echo "</script>";
        echo "<script> document.location.href='request-budget.php'; </script>";
    }
}
