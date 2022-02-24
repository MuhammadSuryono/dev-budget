<?php
error_reporting(0);
session_start();
date_default_timezone_set("Asia/Jakarta");

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

$con->set_name_db(DB_DEVELOP);
$con->init_connection();
$koneksiDevelop = $con->connect();


$con->set_name_db(DB_TRANSFER);
$con->init_connection();
$koneksiTransfer = $con->connect();

$con->set_name_db(DB_MRI_TRANSFER);
$con->init_connection();
$koneksiMriTransfer = $con->connect();


if (!isset($_SESSION['nama_user'])) {
    header("location:login.php");
    // die('location:login.php');//jika belum login jangan lanjut
}
$idUser = $_SESSION['id_user'];
$queryUser = mysqli_query($koneksi, "SELECT email FROM tb_user WHERE id_user = '$idUser'");
$emailUser = mysqli_fetch_row($queryUser)[0];

$url = explode('/', $_SERVER["REQUEST_URI"]);
$url = $url[count($url) - 1];

$queryUser = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE id_user = '$_SESSION[id_user]'");
$user = mysqli_fetch_assoc($queryUser);
$buttonAkses = unserialize($user['hak_button']);

$queryRekening = mysqli_query($koneksiDevelop, "SELECT * FROM kas WHERE stat = 'MRI'");

$batasWaktu = date('Y-m-d H:i:s', strtotime('-2hours'));
$update = mysqli_query($koneksiTransfer, "UPDATE data_transfer SET hasil_transfer = 3, ket_transfer = 'Jadwal Terlewat' WHERE jadwal_transfer < '$batasWaktu' AND hasil_transfer = 1 AND ket_transfer = 'Antri'") or die(mysqli_error($koneksiTransfer));
// var_dump($update);

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Form Pengajuan Budget</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />

    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css" />
    <style>
        .dropdown-menu>a {
            word-wrap: break-word;
            white-space: normal;
        }

        .dropdown-menu {
            width: 300px;
            max-height: 380px;
            overflow-y: auto;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <?php if ($_SESSION['hak_akses'] == 'HRD') { ?>
            <a class="navbar-brand" href="home-direksi.php">Budget-Ing</a>
        <?php } else { ?>
            <a class="navbar-brand" href="home-finance.php">Budget-Ing</a>
        <?php } ?>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <?php if ($_SESSION['hak_akses'] == 'HRD') { ?>
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="home-direksi.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="list-direksi.php">List</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="saldobpu.php">Saldo BPU</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="listfinish-direksi.php">Budget Finish</a>
                    </li>
                <?php } else { ?>
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="home-finance.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <?php
                        $aksesSes = $_SESSION['hak_akses'];
                        if ($aksesSes == 'Fani') {
                        ?>
                            <a class="nav-link" href="list-finance-fani.php">List</a>
                        <?php } else if ($aksesSes == 'Manager') {
                        ?>
                            <a class="nav-link" href="list-finance-budewi.php">List</a>
                        <?php
                        } else {
                        ?>
                            <a class="nav-link" href="list-finance.php">List</a>
                        <?php } ?>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="saldobpu.php">Saldo BPU</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="history-finance.php">History</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="list.php">Personal</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="summary-finance.php">Summary</a>
                    </li>
                <?php } ?>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Rekap
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="rekap-finance.php">Ready To Paid (MRI Kas)</a>
                        <a class="dropdown-item" href="rekap-finance-mripal.php">Ready To Paid (MRI PAL)</a>
                        <a class="dropdown-item" href="belumrealisasi.php">Belum Realisasi</a>
                        <a class="dropdown-item" href="cashflow.php">Cash Flow</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Transfer
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="laporan-transfer.php">Laporan Transfer</a>
                        <a class="dropdown-item" href="antrian-transfer.php">Antrian Transfer</a>
                    </div>
                </li>
            </ul>
            <ul class="navbar-nav my-2 my-lg-0" style="margin-right: 3em;">
                    <li class="nav-item">
                        <a class="nav-link" href="ubahpassword.php"><span class="glyphicon glyphicon-user"></span><?php echo $_SESSION['nama_user']; ?> (<?php echo $_SESSION['divisi']; ?>)</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
            </ul>
        </div>
    </nav>


    <br /><br />

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <!-- <div class="callout callout-info">
                            <?php $getRekening = mysqli_query($koneksiDevelop, "SELECT * FROM kas WHERE stat = 'MRI'"); ?>
                            <?php while ($item = mysqli_fetch_assoc($getRekening)) : ?>
                                <?php
                                $getSaldo = mysqli_query($koneksiMriTransfer, "SELECT saldo FROM saldo WHERE rekening = '$item[rekening]' ORDER BY datetime DESC LIMIT 1") or die(mysqli_error($koneksiMriTransfer));
                                $saldo = mysqli_fetch_assoc($getSaldo);
                                ?>
                                <h6>Saldo Rek. <?= $item['rekening'] ?>: <?= (($saldo['saldo']) ?  'Rp. ' . number_format($saldo['saldo'], 0, '', ',') : 'Data tidak ada') ?>
                                    &nbsp;
                                </h6>
                            <?php endwhile; ?>
                        </div> -->

                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="project-tab" data-toggle="tab" href="#project" role="tab" aria-controls="project" aria-selected="true">Kas Project</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="umum-tab" data-toggle="tab" href="#umum" role="tab" aria-controls="umum" aria-selected="false">Kas Umum</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="uangmuka-tab" data-toggle="tab" href="#uangmuka" role="tab" aria-controls="uangmuka" aria-selected="false">Kas Uang Muka</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="project" role="tabpanel" aria-labelledby="project-tab">

                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">
                                            <?php
                                            $getSumTrf = mysqli_query($koneksiTransfer, "SELECT COUNT(transfer_req_id) AS trx, SUM(jumlah) AS total  FROM data_transfer WHERE hasil_transfer = 1 AND jumlah != '0' AND ket_transfer = 'Antri' AND jenis_project IN ('B1', 'B2') AND keterangan NOT IN ('UM', 'UM Burek') AND jadwal_transfer IS NOT NULL");
                                            $sumTrf = mysqli_fetch_assoc($getSumTrf);

                                            ?>
                                            Antrian : <b><?php echo $sumTrf['trx'] ?></b> Transaksi
                                        </h5>
                                    </div>
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title">
                                                <?php
                                                $getRekening = mysqli_query($koneksiDevelop, "SELECT a.*, b.saldo, b.saldo_id FROM kas a LEFT JOIN ".DB_MRI_TRANSFER.".saldo b ON b.rekening = a.rekening WHERE a.label_kas = 'Kas Project' order by saldo_id desc LIMIT 1") or die(mysqli_error($koneksi));
                                                $rekening = mysqli_fetch_assoc($getRekening);

                                                $queryTotalProject = mysqli_query($koneksiTransfer, "SELECT SUM(jumlah) AS total  FROM data_transfer WHERE hasil_transfer = 1 AND ket_transfer = 'Antri' AND jenis_project IN ('B1', 'B2', 'STKB OPS', 'STKB TRK Jakarta','STKB TRK Luar Kota') AND keterangan NOT IN ('UM', 'UM Burek') AND jadwal_transfer IS NOT NULL ORDER BY transfer_id desc");
                                                $totalProject = mysqli_fetch_assoc($queryTotalProject);
                                                ?>
                                                <p><?= $rekening['label_kas'] ?>, Nomor Rekening: <?= $rekening['rekening'] ?></p>

                                                Saldo Akhir :
                                                <b><?php echo 'Rp. ' . number_format($rekening['saldo'], 0, '', ','); ?></b><br>
                                                Total Biaya :
                                                <b><?php echo 'Rp. ' . number_format($totalProject['total'], 0, '', ','); ?></b><br>
                                            </h5>
                                        </div>
                                        <!-- /.card-header -->
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table id="table-project" class="table table-bordered table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>No.</th>
                                                            <th>Request ID</th>
                                                            <th>Keterangan Transfer</th>
                                                            <th>Jenis Pembayaran</th>
                                                            <th>Jadwal Transfer</th>
                                                            <th>Nomor STKB</th>
                                                            <th>Term</th>
                                                            <th>Nama Penerima</th>
                                                            <th>No. Rekening</th>
                                                            <th>Nama Bank</th>
                                                            <th>Jumlah</th>
                                                            <th>Nama Project</th>
                                                            <!-- <th>Ket. Transfer</th> -->
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $i = 1;
                                                        $getAntri = mysqli_query($koneksiTransfer, "SELECT *
                                                FROM data_transfer
                                                JOIN ".DB_MRI_TRANSFER.".jenis_pembayaran AS t2 ON data_transfer.jenis_pembayaran_id = t2.jenispembayaranid
                                                WHERE ket_transfer = 'Antri' AND jumlah != '0'
                                                AND hasil_transfer =1  AND jenis_project IN ('B1', 'B2') AND keterangan NOT IN ('UM', 'UM Burek') AND jadwal_transfer IS NOT NULL ORDER BY transfer_id desc") or die(mysqli_error($koneksiTransfer));;

                                                        while ($data = mysqli_fetch_assoc($getAntri)) :
                                                            if ($data['transfer_type'] == 1) {
                                                                $transfer_type = 'Transfer Schedule';
                                                            } else if ($data['transfer_type'] == 2) {
                                                                $transfer_type = 'Transfer Manual';
                                                            } else {
                                                                $transfer_type = 'Transfer Auto';
                                                            }
                                                            $bpuQuery = mysqli_query($koneksi, "SELECT term,termstkb FROM bpu where nomorstkb = '$data[nomor_stkb]' AND noid='$data[noid_bpu]' LIMIT 1");
                                                            $bpu = mysqli_fetch_assoc($bpuQuery);
                                                        ?>
                                                            <tr>
                                                                <td><?php echo $i++ ?></td>
                                                                <td><?php echo $data['transfer_req_id'] ?></td>
                                                                <td><?php echo $data['ket_transfer'] ?></td>
                                                                <td><?php echo $data['keterangan'] ?></td>
                                                                <td><?php echo ($data['jadwal_transfer']) ? $data['jadwal_transfer'] : '-' ?></td>
                                                                <td><?php echo $data['nomor_stkb'] ?></td>
                                                                <td><?php echo $data['keterangan'] == 'STKB' ? $bpu['termstkb']:$bpu['term'] ?></td>
                                                                <td><?php echo $data['pemilik_rekening'] ?></td>
                                                                <td><?php echo $data['norek'] ?></td>
                                                                <td><?php echo $data['bank'] ?></td>
                                                                <td><?php echo number_format($data['jumlah'], 0, '', ','); ?></td>
                                                                <td><?php echo $data['nm_project'] ?></td>
                                                                <!-- <td><?php echo $data['ket_transfer'] ?></td> -->
                                                                <td>
                                                                    <a class="Update" href="" data-transferreqid="<?php echo $data['transfer_req_id'] ?>" data-transfertype="<?php echo $transfer_type ?>" data-jenispembayaran="<?php echo $data['jenispembayaran'] ?>" data-keterangan="<?php echo $data['keterangan'] ?>" data-wakturequest="<?php echo $data['waktu_request'] ?>" data-jadwaltransfer="<?php echo $data['jadwal_transfer'] ?>" data-norek="<?php echo $data['norek'] ?>" data-pemilikrekening="<?php echo $data['pemilik_rekening'] ?>" data-bank="<?php echo $data['bank'] ?>" data-kodebank="<?php echo $data['kode_bank'] ?>" data-jumlah="<?php echo number_format($data['jumlah'], 0, '', ','); ?>" data-kettransfer="<?php echo $data['ket_transfer'] ?>" data-nmpembuat="<?php echo $data['nm_pembuat'] ?>" data-nmvalidasi="<?php echo $data['nm_validasi'] ?>" data-nmotorisasi="<?php echo $data['nm_otorisasi'] ?>" data-nmmanual="<?php echo $data['nm_manual'] ?>" data-jenisproject="<?php echo $data['jenis_project'] ?>" data-nmproject='<?php echo $data['nm_project'] ?>' data-rekeningsumber="<?= $data['rekening_sumber'] ?>"><i class="fa fa-eye"></i> view</a>
                                                                    <?php if (in_array("cancel_transfer", $buttonAkses)) : ?>
                                                                        <br>
                                                                        <a class="Cancel" href="" data-id="<?= $data['transfer_id'] ?>" data-button="cancel" data-from="<?= $url ?>"><i class="fa fa-times"></i> cancel</a>
                                                                    <?php endif; ?>
                                                                    <br>
                                                                    <a class="Edit" href="" data-id="<?= $data['transfer_id'] ?>" data-button="edit" data-from="<?= $url ?>"><i class="fa fa-pen"></i> edit</a>
                                                                </td>
                                                            </tr>
                                                        <?php endwhile; ?>
                                                    </tbody>

                                                </table>
                                            </div>
                                        </div>
                                        <!-- /.card-body -->
                                    </div>
                                    <!-- /.card -->
                                </div>
                            </div>
                            <div class="tab-pane fade" id="umum" role="tabpanel" aria-labelledby="umum-tab">

                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">
                                            <?php
                                            $getSumTrf = mysqli_query($koneksiTransfer, "SELECT COUNT(transfer_req_id) AS trx, SUM(jumlah) AS total  FROM data_transfer WHERE hasil_transfer = 1 AND jumlah != '0' AND ket_transfer = 'Antri' AND jenis_project IN ('Rutin', 'Non Rutin') AND keterangan NOT IN ('UM', 'UM Burek') AND jadwal_transfer IS NOT NULL ORDER BY transfer_id desc");
                                            $sumTrf = mysqli_fetch_assoc($getSumTrf);

                                            ?>
                                            Antrian : <b><?php echo $sumTrf['trx'] ?></b> Transaksi
                                        </h5>
                                    </div>
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title">
                                                <?php
                                                $getRekening = mysqli_query($koneksiDevelop, "SELECT a.*, b.saldo, b.saldo_id FROM kas a LEFT JOIN ".DB_MRI_TRANSFER.".saldo b ON b.rekening = a.rekening WHERE a.label_kas = 'Kas Umum' order by saldo_id desc LIMIT 1") or die(mysqli_error($koneksi));
                                                $rekening = mysqli_fetch_assoc($getRekening);

                                                $queryTotalProject = mysqli_query($koneksiTransfer, "SELECT SUM(jumlah) AS total  FROM data_transfer WHERE hasil_transfer = 1 AND ket_transfer = 'Antri' AND jenis_project IN ('B1', 'B2', 'STKB OPS', 'STKB TRK Jakarta','STKB TRK Luar Kota') AND keterangan NOT IN ('UM', 'UM Burek') AND jadwal_transfer IS NOT NULL ORDER BY transfer_id desc");
                                                $totalProject = mysqli_fetch_assoc($queryTotalProject);
                                                ?>

                                                <p><?= $rekening['label_kas'] ?>, Nomor Rekening: <?= $rekening['rekening'] ?></p>

                                                Saldo Akhir :
                                                <b><?php echo 'Rp. ' . number_format($rekening['saldo'], 0, '', ','); ?></b><br>
                                                Total Biaya :
                                                <b><?php echo 'Rp. ' . number_format($totalProject['total'], 0, '', ','); ?></b><br>
                                            </h5>
                                        </div>
                                        <!-- /.card-header -->
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table id="table-umum" class="table table-bordered table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>No.</th>
                                                            <th>Request ID</th>
                                                            <th>Keterangan Transfer</th>
                                                            <th>Jenis Pembayaran</th>
                                                            <th>Jadwal Transfer</th>
                                                            <th>Nama Penerima</th>
                                                            <th>No. Rekening</th>
                                                            <th>Nama Bank</th>
                                                            <th>Jumlah</th>
                                                            <th>Nama Project</th>
                                                            <!-- <th>Ket. Transfer</th> -->
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $i = 1;
                                                        $getAntri = mysqli_query($koneksiTransfer, "SELECT *
                                                FROM data_transfer
                                                JOIN ".DB_MRI_TRANSFER.".jenis_pembayaran AS t2 ON data_transfer.jenis_pembayaran_id = t2.jenispembayaranid
                                                WHERE ket_transfer = 'Antri'
                                                AND hasil_transfer =1  AND jenis_project IN ('Rutin', 'Non Rutin') AND keterangan NOT IN ('UM', 'UM Burek') AND jadwal_transfer IS NOT NULL ORDER BY transfer_id desc") or die(mysqli_error($koneksiTransfer));;

                                                        while ($data = mysqli_fetch_assoc($getAntri)) :
                                                            if ($data['transfer_type'] == 1) {
                                                                $transfer_type = 'Transfer Schedule';
                                                            } else if ($data['transfer_type'] == 2) {
                                                                $transfer_type = 'Transfer Manual';
                                                            } else {
                                                                $transfer_type = 'Transfer Auto';
                                                            }
                                                        ?>
                                                            <tr>
                                                                <td><?php echo $i++ ?></td>
                                                                <td><?php echo $data['transfer_req_id'] ?></td>
                                                                <td><?php echo $data['ket_transfer'] ?></td>
                                                                <td><?php echo $data['keterangan'] ?></td>
                                                                <td><?php echo ($data['jadwal_transfer']) ? $data['jadwal_transfer'] : '-' ?></td>
                                                                <td><?php echo $data['pemilik_rekening'] ?></td>
                                                                <td><?php echo $data['norek'] ?></td>
                                                                <td><?php echo $data['bank'] ?></td>
                                                                <td><?php echo number_format($data['jumlah'], 0, '', ','); ?></td>
                                                                <td><?php echo $data['nm_project'] ?></td>
                                                                <!-- <td><?php echo $data['ket_transfer'] ?></td> -->
                                                                <td>
                                                                    <a class="Update" href="" data-transferreqid="<?php echo $data['transfer_req_id'] ?>" data-transfertype="<?php echo $transfer_type ?>" data-jenispembayaran="<?php echo $data['jenispembayaran'] ?>" data-keterangan="<?php echo $data['keterangan'] ?>" data-wakturequest="<?php echo $data['waktu_request'] ?>" data-jadwaltransfer="<?php echo $data['jadwal_transfer'] ?>" data-norek="<?php echo $data['norek'] ?>" data-pemilikrekening="<?php echo $data['pemilik_rekening'] ?>" data-bank="<?php echo $data['bank'] ?>" data-kodebank="<?php echo $data['kode_bank'] ?>" data-jumlah="<?php echo number_format($data['jumlah'], 0, '', ','); ?>" data-kettransfer="<?php echo $data['ket_transfer'] ?>" data-nmpembuat="<?php echo $data['nm_pembuat'] ?>" data-nmvalidasi="<?php echo $data['nm_validasi'] ?>" data-nmotorisasi="<?php echo $data['nm_otorisasi'] ?>" data-nmmanual="<?php echo $data['nm_manual'] ?>" data-jenisproject="<?php echo $data['jenis_project'] ?>" data-nmproject='<?php echo $data['nm_project'] ?>' data-rekeningsumber="<?= $data['rekening_sumber'] ?>"><i class="fa fa-eye"></i> view</a>
                                                                    <?php if (in_array("cancel_transfer", $buttonAkses)) : ?>
                                                                        <br>
                                                                        <a class="Cancel" href="" data-id="<?= $data['transfer_id'] ?>" data-button="cancel" data-from="<?= $url ?>"><i class="fa fa-times"></i> cancel</a>
                                                                    <?php endif; ?>
                                                                    <br>
                                                                    <a class="Edit" href="" data-id="<?= $data['transfer_id'] ?>" data-button="edit" data-from="<?= $url ?>"><i class="fa fa-pen"></i> edit</a>
                                                                </td>
                                                            </tr>
                                                        <?php endwhile; ?>
                                                    </tbody>

                                                </table>
                                            </div>
                                        </div>
                                        <!-- /.card-body -->
                                    </div>
                                    <!-- /.card -->
                                </div>
                            </div>
                            <div class="tab-pane fade" id="uangmuka" role="tabpanel" aria-labelledby="uangmuka-tab">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">
                                            <?php
                                            $getSumTrf = mysqli_query($koneksiTransfer, "SELECT COUNT(transfer_req_id) AS trx, SUM(jumlah) AS total  FROM data_transfer WHERE hasil_transfer = 1 AND jumlah != '0' AND ket_transfer = 'Antri' AND keterangan IN ('UM', 'UM Burek') AND jadwal_transfer IS NOT NULL");
                                            $sumTrf = mysqli_fetch_assoc($getSumTrf);

                                            ?>
                                            Antrian : <b><?php echo $sumTrf['trx'] ?></b> Transaksi
                                        </h5>
                                    </div>
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title">
                                                <?php
                                                $getRekening = mysqli_query($koneksiDevelop, "SELECT a.*, b.saldo, b.saldo_id FROM kas a LEFT JOIN ".DB_MRI_TRANSFER.".saldo b ON b.rekening = a.rekening WHERE a.label_kas = 'Kas Uang Muka' order by saldo_id desc LIMIT 1") or die(mysqli_error($koneksi));
                                                $rekening = mysqli_fetch_assoc($getRekening);

                                                $queryTotalProject = mysqli_query($koneksiTransfer, "SELECT SUM(jumlah) AS total  FROM data_transfer WHERE hasil_transfer = 1 AND ket_transfer = 'Antri' AND keterangan IN ('UM', 'UM Burek') AND jadwal_transfer IS NOT NULL ORDER BY transfer_id desc");
                                                $totalProject = mysqli_fetch_assoc($queryTotalProject);
                                                ?>

                                                <p><?= $rekening['label_kas'] ?>, Nomor Rekening: <?= $rekening['rekening'] ?></p>

                                                Saldo Akhir :
                                                <b><?php echo 'Rp. ' . number_format($rekening['saldo'], 0, '', ','); ?></b><br>
                                                Total Biaya :
                                                <b><?php echo 'Rp. ' . number_format($totalProject['total'], 0, '', ','); ?></b><br>
                                            </h5>
                                        </div>
                                        <!-- /.card-header -->
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table id="table-uangmuka" class="table table-bordered table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>No.</th>
                                                            <th>Request ID</th>
                                                            <th>Keterangan Transfer</th>
                                                            <th>Jenis Pembayaran</th>
                                                            <th>Jadwal Transfer</th>
                                                            <th>Nama Penerima</th>
                                                            <th>No. Rekening</th>
                                                            <th>Nama Bank</th>
                                                            <th>Jumlah</th>
                                                            <th>Nama Project</th>
                                                            <!-- <th>Ket. Transfer</th> -->
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $i = 1;
                                                        $getAntri = mysqli_query($koneksiTransfer, "SELECT *
                                                FROM data_transfer
                                                JOIN ".DB_MRI_TRANSFER.".jenis_pembayaran AS t2 ON data_transfer.jenis_pembayaran_id = t2.jenispembayaranid
                                                WHERE ket_transfer = 'Antri' AND jumlah != '0'
                                                AND hasil_transfer =1  AND keterangan IN ('UM', 'UM Burek') AND jadwal_transfer IS NOT NULL ORDER BY transfer_id desc") or die(mysqli_error($koneksiTransfer));;

                                                        while ($data = mysqli_fetch_assoc($getAntri)) :
                                                            if ($data['transfer_type'] == 1) {
                                                                $transfer_type = 'Transfer Schedule';
                                                            } else if ($data['transfer_type'] == 2) {
                                                                $transfer_type = 'Transfer Manual';
                                                            } else {
                                                                $transfer_type = 'Transfer Auto';
                                                            }
                                                        ?>
                                                            <tr>
                                                                <td><?php echo $i++ ?></td>
                                                                <td><?php echo $data['transfer_req_id'] ?></td>
                                                                <td><?php echo $data['ket_transfer'] ?></td>
                                                                <td><?php echo $data['keterangan'] ?></td>
                                                                <td><?php echo ($data['jadwal_transfer']) ? $data['jadwal_transfer'] : '-' ?></td>
                                                                <td><?php echo $data['pemilik_rekening'] ?></td>
                                                                <td><?php echo $data['norek'] ?></td>
                                                                <td><?php echo $data['bank'] ?></td>
                                                                <td><?php echo number_format($data['jumlah'], 0, '', ','); ?></td>
                                                                <td><?php echo $data['nm_project'] ?></td>
                                                                <!-- <td><?php echo $data['ket_transfer'] ?></td> -->
                                                                <td>
                                                                    <a class="Update" href="" data-transferreqid="<?php echo $data['transfer_req_id'] ?>" data-transfertype="<?php echo $transfer_type ?>" data-jenispembayaran="<?php echo $data['jenispembayaran'] ?>" data-keterangan="<?php echo $data['keterangan'] ?>" data-wakturequest="<?php echo $data['waktu_request'] ?>" data-jadwaltransfer="<?php echo $data['jadwal_transfer'] ?>" data-norek="<?php echo $data['norek'] ?>" data-pemilikrekening="<?php echo $data['pemilik_rekening'] ?>" data-bank="<?php echo $data['bank'] ?>" data-kodebank="<?php echo $data['kode_bank'] ?>" data-jumlah="<?php echo number_format($data['jumlah'], 0, '', ','); ?>" data-kettransfer="<?php echo $data['ket_transfer'] ?>" data-nmpembuat="<?php echo $data['nm_pembuat'] ?>" data-nmvalidasi="<?php echo $data['nm_validasi'] ?>" data-nmotorisasi="<?php echo $data['nm_otorisasi'] ?>" data-nmmanual="<?php echo $data['nm_manual'] ?>" data-jenisproject="<?php echo $data['jenis_project'] ?>" data-nmproject='<?php echo $data['nm_project'] ?>' data-rekeningsumber="<?= $data['rekening_sumber'] ?>"><i class="fa fa-eye"></i> view</a>
                                                                    <?php if (in_array("cancel_transfer", $buttonAkses)) : ?>
                                                                        <br>
                                                                        <a class="Cancel" href="" data-id="<?= $data['transfer_id'] ?>" data-button="cancel" data-from="<?= $url ?>"><i class="fa fa-times"></i> cancel</a>
                                                                    <?php endif; ?>
                                                                    <br>
                                                                    <a class="Edit" href="" data-id="<?= $data['transfer_id'] ?>" data-button="edit" data-from="<?= $url ?>"><i class="fa fa-pen"></i> edit</a>
                                                                </td>
                                                            </tr>
                                                        <?php endwhile; ?>
                                                    </tbody>

                                                </table>
                                            </div>
                                        </div>
                                        <!-- /.card-body -->
                                    </div>
                                    <!-- /.card -->
                                </div>
                            </div>
                        </div>

                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>
    <!-- End Modal -->



    <!-- Modal -->
    <div id="myModal" class="modal fade">
        <div class="modal-dialog modal-lg">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Detail</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                </div>
                <div class="modal-body">

                    <form>
                        <div class="form-group">
                            <label for="transferreqid">Request ID</label>
                            <input type="text" class="form-control" readonly id="transferreqid">

                        </div>
                        <div class="form-group">
                            <label for="transfertype">TRF. Type</label>
                            <input type="text" class="form-control" readonly id="transfertype">

                        </div>
                        <!-- <div class="form-group">
                            <label for="jenispembayaran">Jenis Pembayaran</label>
                            <input type="text" class="form-control" readonly id="jenispembayaran">
                        </div> -->
                        <div class="form-group">
                            <label for="keterangan">Keterangan</label>
                            <input type="text" class="form-control" readonly id="keterangan">
                        </div>
                        <div class="form-group">
                            <label for="wakturequest">Waktu Request</label>
                            <input type="text" class="form-control" readonly id="wakturequest">

                        </div>
                        <div class="form-group">
                            <label for="jadwaltransfer">Jadwal Transfer</label>
                            <input type="text" class="form-control" readonly id="jadwaltransfer">

                        </div>
                        <div class="form-group">
                            <label for="norek">No. Rekening</label>
                            <input type="text" class="form-control" readonly id="norek">

                        </div>
                        <div class="form-group">
                            <label for="pemilikrekening">Nama Rekening</label>
                            <input type="text" class="form-control" readonly id="pemilikrekening">

                        </div>
                        <div class="form-group">
                            <label for="bank">Bank</label>
                            <input type="text" class="form-control" readonly id="bank">

                        </div>
                        <div class="form-group">
                            <label for="kodebank">Kode Bank</label>
                            <input type="text" class="form-control" readonly id="kodebank">

                        </div>
                        <div class="form-group">
                            <label for="jumlah">Jumlah</label>
                            <input type="text" class="form-control" readonly id="jumlah">

                        </div>
                        <div class="form-group">
                            <label for="kettransfer">Ket. Transfer</label>
                            <input type="text" class="form-control" readonly id="kettransfer">

                        </div>
                        <div class="form-group">
                            <label for="nmpembuat">Nama Pembuat</label>
                            <input type="text" class="form-control" readonly id="nmpembuat">

                        </div>
                        <div class="form-group">
                            <label for="nmvalidasi">Nama Validasi</label>
                            <input type="text" class="form-control" readonly id="nmvalidasi">

                        </div>
                        <div class="form-group">
                            <label for="nmotorisasi">Nama Otorisasi</label>
                            <input type="text" class="form-control" readonly id="nmotorisasi">

                        </div>
                        <div class="form-group">
                            <label for="nmmanual">Nama Manual</label>
                            <input type="text" class="form-control" readonly id="nmmanual">

                        </div>
                        <div class="form-group">
                            <label for="jenisproject">Jenis Project</label>
                            <input type="text" class="form-control" readonly id="jenisproject">

                        </div>
                        <div class="form-group">
                            <label for="nmproject">Nama Project</label>
                            <input type="text" class="form-control" readonly id="nmproject">
                        </div>
                        <div class="form-group">
                            <label for="rekening_sumber">Rekening Sumber</label>
                            <input type="text" class="form-control" readonly id="rekening_sumber">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal -->

    <div id="myModal2" class="modal fade">
        <div class="modal-dialog modal-lg">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Pembatalan Transfer</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                </div>
                <form action="antrian-transfer-proses.php" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="id">
                        <input type="hidden" name="button">
                        <input type="hidden" name="from">
                        <div class="form-group">
                            <label for="ket_tambahan">Alasan Cancel</label>
                            <input type="text" class="form-control" id="ket_tambahan" name="ket_tambahan">
                        </div>
                        <p>Klik "Submit" untuk membatalkan transfer</p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="myModalEdit" class="modal fade">
        <div class="modal-dialog modal-lg">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Perubahan Jadwal Transfer</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                </div>
                <form action="antrian-transfer-proses.php" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="id">
                        <input type="hidden" name="button">
                        <input type="hidden" name="from">
                        <div class="form-group">
                            <label for="jadwal_transfer">Jadwal Transfer</label>
                            <input type="datetime-local" class="form-control" id="jadwal_transfer" name="jadwal_transfer" min="<?= date("Y-m-d") . "T" . date("H:i") ?>">
                        </div>
                        <div class="form-group">
                            <label for="">Alasan Perubahan</label>
                            <input type="text" class="form-control" name="ket_tambahan">
                        </div>
                        <p>Klik "Submit" untuk Melakukan perubahan jadwal transfer</p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="rekeningModal" tabindex="-1" role="dialog" aria-labelledby="rekeningModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rekeningModalLabel">Notifikasi Top Up</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php
                    $statusTopUp = 0;
                    while ($item = mysqli_fetch_assoc($queryRekening)) :
                        $querySaldo = mysqli_query($koneksiMriTransfer, "SELECT * FROM saldo WHERE rekening = '$item[rekening]' ORDER BY saldo_id DESC LIMIT 1");
                        $getSaldo = mysqli_fetch_assoc($querySaldo);

                        $countTotal = mysqli_query($koneksiTransfer, "SELECT SUM(jumlah) AS total FROM data_transfer WHERE rekening_sumber = '$item[rekening]' AND hasil_transfer = 1 AND ket_transfer = 'Antri'");
                        $total = mysqli_fetch_assoc($countTotal);

                        if ($getSaldo['saldo'] < $total['total']) :
                            $statusTopUp = 1;
                    ?>
                            <p>Rekening <?= $item['rekening'] ?> kurang Rp. <?= number_format($total['total'] - $getSaldo['saldo']) ?> dari total transfer yang dibutuhkan, Harap segera menambah saldo anda.</p><br>
                        <?php endif ?>
                    <?php endwhile; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script> -->
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>

    <script type="text/javascript">
        const statusTopUp = '<?= $statusTopUp ?>';

        $(document).ready(function() {
            $('#table-project').DataTable({});
            $('#table-umum').DataTable({});
            $('#table-uangmuka').DataTable({});

            if (statusTopUp == 1) {
                $('#rekeningModal').modal('show');
            }
        });

        $(function() {
            $("a[class='Update']").click(function() {
                $('#myModal').modal("show");

                var transferreqid = $(this).attr('data-transferreqid')
                var transfertype = $(this).attr('data-transfertype')
                var jenispembayaran = $(this).attr('data-jenispembayaran')
                var keterangan = $(this).attr('data-keterangan')
                var wakturequest = $(this).attr('data-wakturequest')
                var jadwaltransfer = $(this).attr('data-jadwaltransfer')
                var norek = $(this).attr('data-norek')
                var pemilikrekening = $(this).attr('data-pemilikrekening')
                var bank = $(this).attr('data-bank')
                var kodebank = $(this).attr('data-kodebank')
                var jumlah = $(this).attr('data-jumlah')
                var kettransfer = $(this).attr('data-kettransfer')
                var nmpembuat = $(this).attr('data-nmpembuat')
                var nmvalidasi = $(this).attr('data-nmvalidasi')
                var nmotorisasi = $(this).attr('data-nmotorisasi')
                var nmmanual = $(this).attr('data-nmmanual')
                var jenisproject = $(this).attr('data-jenisproject')
                var nmproject = $(this).attr('data-nmproject')
                var rekeningsumber = $(this).attr('data-rekeningsumber')

                $('#transferreqid').val(transferreqid)
                $('#transfertype').val(transfertype)
                $('#jenispembayaran').val(jenispembayaran)
                $('#keterangan').val(keterangan)
                $('#wakturequest').val(wakturequest)
                $('#jadwaltransfer').val(jadwaltransfer)
                $('#norek').val(norek)
                $('#pemilikrekening').val(pemilikrekening)
                $('#bank').val(bank)
                $('#kodebank').val(kodebank)
                $('#jumlah').val(jumlah)
                $('#kettransfer').val(kettransfer)
                $('#nmpembuat').val(nmpembuat)
                $('#nmvalidasi').val(nmvalidasi)
                $('#nmotorisasi').val(nmotorisasi)
                $('#nmmanual').val(nmmanual)
                $('#jenisproject').val(jenisproject)
                $('#nmproject').val(nmproject)
                $('#rekening_sumber').val(rekeningsumber)
                return false
            })

            $('.Cancel').click(function() {
                $('#myModal2').modal("show");
                var id = $(this).attr('data-id');
                var button = $(this).attr('data-button');
                var from = $(this).attr('data-from');

                $('input[name=id]').val(id);
                $('input[name=button]').val(button);
                $('input[name=from]').val(from);
                return false;
            })

            $('.Edit').click(function() {
                $('#myModalEdit').modal("show");
                var id = $(this).attr('data-id');
                var button = $(this).attr('data-button');
                var from = $(this).attr('data-from');

                $('input[name=id]').val(id);
                $('input[name=button]').val(button);
                $('input[name=from]').val(from);
                return false;
            })

            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>

</body>

</html>