<?php
error_reporting(0);
session_start();

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

$con->set_name_db(DB_TRANSFER);
$con->init_connection();
$koneksiTransfer = $con->connect();

if (!isset($_SESSION['nama_user'])) {
    header("location:login.php");
    // die('location:login.php');//jika belum login jangan lanjut
}

$idUser = $_SESSION['id_user'];
$queryUser = mysqli_query($koneksi, "SELECT email FROM tb_user WHERE id_user = '$idUser'");
$emailUser = mysqli_fetch_row($queryUser)[0];


$start_date = $_GET['start_date'];
$end_date = $_GET['end_date'];
$kettrf = $_GET['kettrf'];
$trftype = $_GET['trftype'];

$conditions = array();

if (!empty($start_date)) {
    $conditions[] = "jadwal_transfer>='$start_date'";
}
if (!empty($end_date)) {
    $conditions[] = "jadwal_transfer<='$end_date'";
}
if (!empty($kettrf)) {
    $conditions[] = "ket_transfer='$kettrf'";
}
if (!empty($trftype)) {
    $conditions[] = "transfer_type='$trftype'";
}

$sql = '';
if (count($conditions) > 0) {
    $sql .= implode(' AND ', $conditions);
}

$bagianWhere = $sql;

$getTrfFlap = mysqli_query($koneksiTransfer, "SELECT * FROM data_transfer JOIN mritransfer.jenis_pembayaran AS t2 ON data_transfer.jenis_pembayaran_id = t2.jenispembayaranid " . (($bagianWhere) ? "WHERE " . $bagianWhere : "") . " ORDER BY data_transfer.transfer_id ASC") or die(mysqli_error($koneksiTransfer));

$url = explode('/', $_SERVER["REQUEST_URI"]);
$url = $url[count($url) - 1];

$queryUser = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE id_user = '$_SESSION[id_user]'");
$user = mysqli_fetch_assoc($queryUser);
$buttonAkses = unserialize($user['hak_button']);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title></title>
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

        .no-background {
            background-color: transparent;
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
                        <a class="nav-link" aria-current="page" href="saldobpu.php">Data User</a>
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
                        <a class="nav-link" href="saldobpu.php">Data User</a>
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
                <?php
                if ($_SESSION['hak_akses'] != 'HRD') {
                    $cari = mysqli_query($koneksi, "SELECT * FROM bpu WHERE status ='Belum Di Bayar' AND persetujuan !='Belum Disetujui' AND waktu != 0");
                    $belbyr = mysqli_num_rows($cari);
                ?>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                            <i class="fas fa-bell"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?= $belbyr ?>
                            </span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown"><?php
                                                                                    while ($wkt = mysqli_fetch_array($cari)) {
                                                                                        $wktulang = $wkt['waktu'];

                                                                                        $selectnoid = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE waktu='$wktulang'");
                                                                                        $noid = mysqli_fetch_assoc($selectnoid);
                                                                                        $kode = $noid['noid'];
                                                                                        $project = $noid['nama'];
                                                                                    ?>

                                <a class="dropdown-item" href="view-finance.php?code=<?= $kode ?>">Project <b><?= $project ?></b> BPU Belum Dibayar</a>

                            <?php
                                                                                    }
                            ?>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="ubahpassword.php"><span class="glyphicon glyphicon-user"></span><?php echo $_SESSION['nama_user']; ?> (<?php echo $_SESSION['divisi']; ?>)</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                <?php } else {
                    $cari = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE status='Pending'");
                    $belbyr = mysqli_num_rows($cari);
                    $caribpu = mysqli_query($koneksi, "SELECT * FROM bpu WHERE persetujuan='Belum Disetujui'");
                    $bpuyahud = mysqli_num_rows($caribpu);
                    $queryPengajuanReq = mysqli_query($koneksi, "SELECT * FROM pengajuan_request WHERE status_request = 'Di Ajukan' AND waktu != 0");
                    $countPengajuanReq = mysqli_num_rows($queryPengajuanReq);
                    $notif = $belbyr + $bpuyahud + $countPengajuanReq; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                            <i class="fas fa-bell"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?= $notif ?>
                            </span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown"><?php
                                                                                    while ($wkt = mysqli_fetch_array($cari)) {
                                                                                        $wktulang = $wkt['waktu'];
                                                                                        $selectnoid = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE waktu='$wktulang'");
                                                                                        $noid = mysqli_fetch_assoc($selectnoid);
                                                                                        $kode = $noid['noid'];
                                                                                        $project = $noid['nama'];
                                                                                    ?>
                                <a class="dropdown-item" href="view-direksi.php?code=<?= $kode ?>">Project <b><?= $project ?></b> status masih Pending</a>
                                <?php
                                                                                        while ($wktbpu = mysqli_fetch_array($caribpu)) {
                                                                                            $bpulagi = $wktbpu['waktu'];
                                                                                            $selectnoid2 = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE waktu='$bpulagi'");
                                                                                            $noid2 = mysqli_fetch_assoc($selectnoid2);
                                                                                            $kode2 = $noid2['noid'];
                                                                                            $project2 = $noid2['nama'];
                                ?>
                                    <a class="dropdown-item" href="views-direksi.php?code=<?= $kode2 ?>">Project <b><?= $project2 ?></b> ada BPU yang belum di setujui</a>
                                <?php
                                                                                        }
                                                                                    }
                                                                                    while ($qpr = mysqli_fetch_array($queryPengajuanReq)) {
                                                                                        $time = $qpr['waktu'];
                                                                                        $selectnoid3 = mysqli_query($koneksi, "SELECT * FROM pengajuan_request WHERE waktu='$time'");
                                                                                        $noid3 = mysqli_fetch_assoc($selectnoid3);
                                                                                        $kode3 = $noid3['id'];
                                                                                        $project3 = $noid3['nama'];
                                ?>

                                <a class="dropdown-item" href="view-request.php?id=<?= $kode3 ?>">Pengajuan Budget <b><?= $project3 ?></b> telah diajukan</a>
                            <?php } ?>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="ubahpassword.php"><span class="glyphicon glyphicon-user"></span><?php echo $_SESSION['nama_user']; ?> (<?php echo $_SESSION['divisi']; ?>)</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </nav>



    <br /><br />


    <div class="container">
        <h2>Laporan Transfer MRI PAL</h2>
        <div class="content-wrapper">
            <section class="content">
                <div class="row">
                    <div class="col-12">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link <?= ($_GET['tab'] == 'project' || $_GET['tab'] == null) ? 'active' : '' ?>" id="project-tab" data-toggle="tab" href="#project" role="tab" aria-controls="project" aria-selected="true">Kas Project</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link <?= ($_GET['tab'] == 'umum') ? 'active' : '' ?>" id="umum-tab" data-toggle="tab" href="#umum" role="tab" aria-controls="umum" aria-selected="false">Kas Umum</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link <?= ($_GET['tab'] == 'uangmuka') ? 'active' : '' ?>" id="uangmuka-tab" data-toggle="tab" href="#uangmuka" role="tab" aria-controls="uangmuka" aria-selected="false">Kas Uang Muka</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade <?= ($_GET['tab'] == 'project' || $_GET['tab'] == null) ? 'show active' : '' ?>" id="project" role="tabpanel" aria-labelledby="project-tab">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="form-group">
                                            <label><b>Search : </b></label>
                                            <form action="" method="get">
                                                <input type="hidden" name="tab" value="project">
                                                <input type="date" class="startdate" placeholder="Start Date" id="start_date" name="start_date" value="<?= ($start_date) ? $start_date : '' ?>">
                                                <span> S/D </span>
                                                <input type="date" class="enddate" placeholder="End Date" id="end_date" name="end_date" value="<?= ($end_date) ? $end_date : '' ?>">
                                                <span> | </span>
                                                <select name="kettrf">
                                                    <?php $getKetTransfer = mysqli_query($koneksiTransfer, "SELECT ket_transfer FROM data_transfer WHERE ket_transfer != 'Success' GROUP BY ket_transfer ORDER BY ket_transfer ASC"); ?>
                                                    <option value="">Pilih</option>
                                                    <?php
                                                    while ($item = mysqli_fetch_assoc($getKetTransfer)) :
                                                    ?>
                                                        <option value="<?php echo $item['ket_transfer'] ?>" <?= ($kettrf == $item['ket_transfer']) ? "selected" : "" ?>><?php echo $item['ket_transfer'] ?></option>
                                                    <?php endwhile; ?>
                                                </select>
                                                <span> | </span>
                                                <select name="trftype">
                                                    <option value="">Pilih</option>
                                                    <option value="1" <?= ($trftype == 1) ? "selected" : "" ?>>Transfer Schedule</option>
                                                    <option value="2" <?= ($trftype == 2) ? "selected" : "" ?>>Transfer Manual</option>
                                                    <option value="3" <?= ($trftype == 3) ? "selected" : "" ?>>Transfer Auto</option>
                                                </select>
                                                <button class="btn-primary" type="submit">Search</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="callout callout-info ml-3">
                                        <?php
                                        $i = 1;
                                        $total = 0;
                                        $getTrfFlap = mysqli_query($koneksiTransfer, "SELECT * FROM data_transfer JOIN mritransfer.jenis_pembayaran AS t2 ON data_transfer.jenis_pembayaran_id = t2.jenispembayaranid WHERE hasil_transfer NOT IN ('1','4') AND jenis_project IN ('B1', 'B2') AND keterangan NOT IN ('UM', 'UM Burek')" . (($bagianWhere) ? " AND " . $bagianWhere : "") . "  ORDER BY data_transfer.transfer_id ASC") or die(mysqli_error($koneksiTransfer));
                                        while ($item = mysqli_fetch_assoc($getTrfFlap)) {
                                            $total += $item['jumlah'];
                                        }


                                        $getTrfFlapSuccess = mysqli_query($koneksiTransfer, "SELECT SUM(jumlah) AS jumlah FROM data_transfer JOIN mritransfer.jenis_pembayaran AS t2 ON data_transfer.jenis_pembayaran_id = t2.jenispembayaranid WHERE hasil_transfer = 2 AND jenis_project IN ('B1', 'B2') AND keterangan NOT IN ('UM', 'UM Burek')" . (($bagianWhere) ? " AND " . $bagianWhere : "")) or die(mysqli_error($koneksiTransfer));
                                        // var_dump(mysqli_fetch_assoc($getTrfFlapSuccess));
                                        $totalSuccess = mysqli_fetch_assoc($getTrfFlapSuccess)['jumlah'];
                                        // var_dump('here');

                                        $getTrfFlapNotSuccess = mysqli_query($koneksiTransfer, "SELECT SUM(jumlah) AS jumlah FROM data_transfer JOIN mritransfer.jenis_pembayaran AS t2 ON data_transfer.jenis_pembayaran_id = t2.jenispembayaranid WHERE hasil_transfer = 3 AND jenis_project IN ('B1', 'B2') AND keterangan NOT IN ('UM', 'UM Burek')" . (($bagianWhere) ? " AND " . $bagianWhere : "")) or die(mysqli_error($koneksiTransfer));
                                        $totalNotSuccess = mysqli_fetch_assoc($getTrfFlapNotSuccess)['jumlah'];
                                        ?>
                                        <h5>Total Pembayaran Keseluruhan: <?php echo 'Rp. ' . number_format($total, 0, '', ','); ?>
                                        </h5>
                                        <h5>Total Pembayaran Gagal: <?php echo 'Rp. ' . number_format($totalNotSuccess, 0, '', ','); ?></h5>
                                        <!-- <h5>Total Pembayaran Cancel: <?php echo 'Rp. ' . number_format($totalCancel, 0, '', ','); ?></h5> -->
                                        <h5>Total Pembayaran Sukses: <?php echo 'Rp. ' . number_format($totalSuccess, 0, '', ','); ?></h5>
                                    </div>

                                    <div class="card-body">
                                        <!-- <form action="" method="get"> -->
                                        <div class="table-responsive">
                                            <table id="example1" class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>No.</th>
                                                        <th>Request ID</th>
                                                        <th>Jenis Pembayaran</th>
                                                        <th>Jadwal Transfer</th>
                                                        <th>No. Rekening</th>
                                                        <th>Jumlah</th>
                                                        <th>Ket. Transfer</th>
                                                        <th>Ket. Tambahan</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $getTrfFlap = mysqli_query($koneksiTransfer, "SELECT * FROM data_transfer JOIN mritransfer.jenis_pembayaran AS t2 ON data_transfer.jenis_pembayaran_id = t2.jenispembayaranid " . (($bagianWhere) ? "WHERE " . $bagianWhere : "") .  " AND hasil_transfer NOT IN ('1','4') AND jenis_project IN ('B1', 'B2') AND keterangan NOT IN ('UM', 'UM Burek') ORDER BY data_transfer.transfer_id ASC") or die(mysqli_error($koneksiTransfer));

                                                    if (!empty($getTrfFlap)) {
                                                        error_reporting(0);
                                                        $i = 1;
                                                        $total = 0;
                                                        while ($data = mysqli_fetch_assoc($getTrfFlap)) {
                                                            $transfer_req_id = $data['transfer_req_id'];
                                                            $jenispembayaran = $data['jenispembayaran'];
                                                            $jadwal_transfer = ($data['jadwal_transfer']) ? $data['jadwal_transfer'] : '-';
                                                            $norek = $data['norek'];
                                                            $jumlah = $data['jumlah'];
                                                            $ket_transfer = $data['ket_transfer'];
                                                            $keterangan = $data['keterangan'];
                                                            $waktu_request = $data['waktu_request'];
                                                            $pemilik_rekening = $data['pemilik_rekening'];
                                                            $bank = $data['bank'];
                                                            $kode_bank = $data['kode_bank'];
                                                            $nm_pembuat = $data['nm_pembuat'];
                                                            $nm_validasi = $data['nm_validasi'];
                                                            $nm_otorisasi = $data['nm_otorisasi'];
                                                            $nm_manual = $data['nm_manual'];
                                                            $jenis_project = $data['jenis_project'];
                                                            $nm_project = $data['nm_project'];
                                                            $rekening_sumber = $data['rekening_sumber'];
                                                            $ket_tambahan = $data['ket_tambahan'];

                                                            if ($data['transfer_type'] == 1) {
                                                                $transfer_type = 'Transfer Schedule';
                                                            } else if ($data['transfer_type'] == 2) {
                                                                $transfer_type = 'Transfer Manual';
                                                            } else {
                                                                $transfer_type = 'Transfer Auto';
                                                            }

                                                            echo "<tr>";
                                                            echo "<td>" . $i++ . "</td>";
                                                            echo "<td>" . $transfer_req_id . "</td>";
                                                            echo "<td>" . $keterangan . "</td>";
                                                            echo "<td>" . $jadwal_transfer . "</td>";
                                                            echo "<td>" . $norek . "</td>";
                                                            echo "<td>" . number_format($jumlah, 0, '', ',') . "</td>";
                                                            echo "<td>" . $ket_transfer . "</td>";
                                                            echo "<td>" . $ket_tambahan . "</td>";
                                                            echo "<td><button type='button' class='Update btn btn-sm text-primary no-background' 

                        data-transferreqid='$transfer_req_id'
                        data-transfertype='$transfer_type'
                        data-jenispembayaran='$jenispembayaran'
                        data-keterangan='$keterangan'
                        data-wakturequest='$waktu_request'
                        data-jadwaltransfer='$jadwal_transfer'
                        data-norek='$norek'
                        data-pemilikrekening='$pemilik_rekening'
                        data-bank='$bank'
                        data-kodebank='$kode_bank'
                        data-jumlah='" . number_format($jumlah, 0, '', ',') . "'
                        data-kettransfer='$ket_transfer'
                        data-nmpembuat='$nm_pembuat'
                        data-nmvalidasi='$nm_validasi'
                        data-nmotorisasi='$nm_otorisasi'
                        data-nmmanual='$nm_manual'
                        data-jenisproject='$jenis_project'
                        data-nmproject='$nm_project'
                        data-rekeningsumber='$rekening_sumber'

                    ><i class='fa fa-eye'></i> view</button>
                    ";
                                                            if ($data['hasil_transfer'] == 2) {
                                                                echo "<br>
                        <a class='Print btn btn-sm text-primary no-background' target='_blank' href='bukti-transfer-print.php?id=" . $data['transfer_req_id'] . "'><i class='fa fa-print'></i> print</a>";
                                                            }
                                                            if (($data['hasil_transfer'] == 3) && in_array("ulang_transfer", $buttonAkses)) {
                                                                echo "<br><button type='button' class='Edit btn btn-sm text-primary no-background' data-id='" . $data['transfer_id'] . "' data-button='antri' data-from='" . $url . "'><i class='fa fa-sync'></i> ulang</button>
                                                        ";
                                                            }
                                                            echo "</td>";
                                                            echo "</tr>";
                                                        }
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <br>
                                        <?php error_reporting(0); ?>
                                        <input type="hidden" value="<?php echo $_REQUEST['kettrf']; ?>" name="kettrf">
                                        <input type="hidden" value="<?php echo $_REQUEST['start_date']; ?>" name="start_date">
                                        <input type="hidden" value="<?php echo $_REQUEST['end_date']; ?>" name="end_date">
                                        <input type="hidden" value="<?php echo $_REQUEST['trftype']; ?>" name="trftype">
                                        <!-- <button type="submit" class="btn btn-success btn-small">Export To excel</button> -->
                                        <!-- </form> -->


                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade <?= ($_GET['tab'] == 'umum') ? 'show active' : '' ?>" id="umum" role="tabpanel" aria-labelledby="umum-tab">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="form-group">
                                            <label><b>Search : </b></label>
                                            <form action="" method="get">
                                                <input type="hidden" name="tab" value="umum">
                                                <input type="date" class="startdate" placeholder="Start Date" id="start_date" name="start_date" value="<?= ($start_date) ? $start_date : '' ?>">
                                                <span> S/D </span>
                                                <input type="date" class="enddate" placeholder="End Date" id="end_date" name="end_date" value="<?= ($end_date) ? $end_date : '' ?>">
                                                <span> | </span>
                                                <select name="kettrf">
                                                    <?php $getKetTransfer = mysqli_query($koneksiTransfer, "SELECT ket_transfer FROM data_transfer WHERE ket_transfer != 'Success' GROUP BY ket_transfer ORDER BY ket_transfer ASC"); ?>
                                                    <option value="">Pilih</option>
                                                    <?php
                                                    while ($item = mysqli_fetch_assoc($getKetTransfer)) :
                                                    ?>
                                                        <option value="<?php echo $item['ket_transfer'] ?>" <?= ($kettrf == $item['ket_transfer']) ? "selected" : "" ?>><?php echo $item['ket_transfer'] ?></option>
                                                    <?php endwhile; ?>
                                                </select>
                                                <span> | </span>
                                                <select name="trftype">
                                                    <option value="">Pilih</option>
                                                    <option value="1" <?= ($trftype == 1) ? "selected" : "" ?>>Transfer Schedule</option>
                                                    <option value="2" <?= ($trftype == 2) ? "selected" : "" ?>>Transfer Manual</option>
                                                    <option value="3" <?= ($trftype == 3) ? "selected" : "" ?>>Transfer Auto</option>
                                                </select>
                                                <button class="btn-primary" type="submit">Search</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="callout callout-info ml-3">
                                        <?php
                                        $i = 1;
                                        $total = 0;
                                        $getTrfFlap = mysqli_query($koneksiTransfer, "SELECT * FROM data_transfer JOIN mritransfer.jenis_pembayaran AS t2 ON data_transfer.jenis_pembayaran_id = t2.jenispembayaranid WHERE hasil_transfer NOT IN ('1','4') AND jenis_project IN ('Rutin', 'Non Rutin') AND keterangan NOT IN ('UM', 'UM Burek')" . (($bagianWhere) ? " AND " . $bagianWhere : "") . "  ORDER BY data_transfer.transfer_id ASC") or die(mysqli_error($koneksiTransfer));
                                        while ($item = mysqli_fetch_assoc($getTrfFlap)) {
                                            $total += $item['jumlah'];
                                        }


                                        $getTrfFlapSuccess = mysqli_query($koneksiTransfer, "SELECT SUM(jumlah) AS jumlah FROM data_transfer JOIN mritransfer.jenis_pembayaran AS t2 ON data_transfer.jenis_pembayaran_id = t2.jenispembayaranid WHERE hasil_transfer = 2 AND jenis_project IN ('Rutin', 'Non Rutin') AND keterangan NOT IN ('UM', 'UM Burek')" . (($bagianWhere) ? " AND " . $bagianWhere : "")) or die(mysqli_error($koneksiTransfer));
                                        // var_dump(mysqli_fetch_assoc($getTrfFlapSuccess));
                                        $totalSuccess = mysqli_fetch_assoc($getTrfFlapSuccess)['jumlah'];
                                        // var_dump('here');

                                        $getTrfFlapNotSuccess = mysqli_query($koneksiTransfer, "SELECT SUM(jumlah) AS jumlah FROM data_transfer JOIN mritransfer.jenis_pembayaran AS t2 ON data_transfer.jenis_pembayaran_id = t2.jenispembayaranid WHERE hasil_transfer = 3 AND jenis_project IN ('Rutin', 'Non Rutin') AND keterangan NOT IN ('UM', 'UM Burek')" . (($bagianWhere) ? " AND " . $bagianWhere : "")) or die(mysqli_error($koneksiTransfer));
                                        $totalNotSuccess = mysqli_fetch_assoc($getTrfFlapNotSuccess)['jumlah'];

                                        $getTrfFlapNotSuccess = mysqli_query($koneksiTransfer, "SELECT SUM(jumlah) AS jumlah FROM data_transfer JOIN mritransfer.jenis_pembayaran AS t2 ON data_transfer.jenis_pembayaran_id = t2.jenispembayaranid WHERE hasil_transfer = 1 AND jenis_project IN ('Rutin', 'Non Rutin') AND keterangan NOT IN ('UM', 'UM Burek')" . (($bagianWhere) ? " AND " . $bagianWhere : "")) or die(mysqli_error($koneksiTransfer));
                                        $totalAntri = mysqli_fetch_assoc($getTrfFlapNotSuccess)['jumlah'];

                                        $getTrfFlapCancel = mysqli_query($koneksiTransfer, "SELECT SUM(jumlah) AS jumlah FROM data_transfer JOIN mritransfer.jenis_pembayaran AS t2 ON data_transfer.jenis_pembayaran_id = t2.jenispembayaranid WHERE hasil_transfer = 4 AND jenis_project IN ('Rutin', 'Non Rutin') AND keterangan NOT IN ('UM', 'UM Burek')" . (($bagianWhere) ? " AND " . $bagianWhere : "")) or die(mysqli_error($koneksiTransfer));
                                        $totalCancel = mysqli_fetch_assoc($getTrfFlapCancel)['jumlah'];
                                        ?>
                                        <h5>Total Pembayaran Keseluruhan: <?php echo 'Rp. ' . number_format($total, 0, '', ','); ?>
                                        </h5>
                                        <h5>Total Pembayaran Gagal: <?php echo 'Rp. ' . number_format($totalNotSuccess, 0, '', ','); ?></h5>
                                        <!-- <h5>Total Pembayaran Cancel: <?php echo 'Rp. ' . number_format($totalCancel, 0, '', ','); ?></h5> -->
                                        <h5>Total Pembayaran Sukses: <?php echo 'Rp. ' . number_format($totalSuccess, 0, '', ','); ?></h5>
                                    </div>

                                    <div class="card-body">
                                        <!-- <form action="" method="get"> -->
                                        <div class="table-responsive">
                                            <table id="example2" class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>No.</th>
                                                        <th>Request ID</th>
                                                        <th>Jenis Pembayaran</th>
                                                        <th>Jadwal Transfer</th>
                                                        <th>No. Rekening</th>
                                                        <th>Jumlah</th>
                                                        <th>Ket. Transfer</th>
                                                        <th>Ket. Tambahan</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $getTrfFlap = mysqli_query($koneksiTransfer, "SELECT * FROM data_transfer JOIN mritransfer.jenis_pembayaran AS t2 ON data_transfer.jenis_pembayaran_id = t2.jenispembayaranid " . (($bagianWhere) ? "WHERE " . $bagianWhere : "") . " AND hasil_transfer NOT IN ('1','4') AND jenis_project IN ('Rutin', 'Non Rutin') AND keterangan NOT IN ('UM', 'UM Burek') ORDER BY data_transfer.transfer_id ASC") or die(mysqli_error($koneksiTransfer));

                                                    if (!empty($getTrfFlap)) {
                                                        error_reporting(0);
                                                        $i = 1;
                                                        $total = 0;
                                                        while ($data = mysqli_fetch_assoc($getTrfFlap)) {
                                                            $transfer_req_id = $data['transfer_req_id'];
                                                            $jenispembayaran = $data['jenispembayaran'];
                                                            $jadwal_transfer = ($data['jadwal_transfer']) ? $data['jadwal_transfer'] : '-';
                                                            $norek = $data['norek'];
                                                            $jumlah = $data['jumlah'];
                                                            $ket_transfer = $data['ket_transfer'];
                                                            $keterangan = $data['keterangan'];
                                                            $waktu_request = $data['waktu_request'];
                                                            $pemilik_rekening = $data['pemilik_rekening'];
                                                            $bank = $data['bank'];
                                                            $kode_bank = $data['kode_bank'];
                                                            $nm_pembuat = $data['nm_pembuat'];
                                                            $nm_validasi = $data['nm_validasi'];
                                                            $nm_otorisasi = $data['nm_otorisasi'];
                                                            $nm_manual = $data['nm_manual'];
                                                            $jenis_project = $data['jenis_project'];
                                                            $nm_project = $data['nm_project'];
                                                            $rekening_sumber = $data['rekening_sumber'];
                                                            $ket_tambahan = $data['ket_tambahan'];

                                                            if ($data['transfer_type'] == 1) {
                                                                $transfer_type = 'Transfer Schedule';
                                                            } else if ($data['transfer_type'] == 2) {
                                                                $transfer_type = 'Transfer Manual';
                                                            } else {
                                                                $transfer_type = 'Transfer Auto';
                                                            }

                                                            echo "<tr>";
                                                            echo "<td>" . $i++ . "</td>";
                                                            echo "<td>" . $transfer_req_id . "</td>";
                                                            echo "<td>" . $keterangan . "</td>";
                                                            echo "<td>" . $jadwal_transfer . "</td>";
                                                            echo "<td>" . $norek . "</td>";
                                                            echo "<td>" . number_format($jumlah, 0, '', ',') . "</td>";
                                                            echo "<td>" . $ket_transfer . "</td>";
                                                            echo "<td>" . $ket_tambahan . "</td>";
                                                            echo "<td><button type='button' class='Update btn btn-sm text-primary no-background' 

                        data-transferreqid='$transfer_req_id'
                        data-transfertype='$transfer_type'
                        data-jenispembayaran='$jenispembayaran'
                        data-keterangan='$keterangan'
                        data-wakturequest='$waktu_request'
                        data-jadwaltransfer='$jadwal_transfer'
                        data-norek='$norek'
                        data-pemilikrekening='$pemilik_rekening'
                        data-bank='$bank'
                        data-kodebank='$kode_bank'
                        data-jumlah='" . number_format($jumlah, 0, '', ',') . "'
                        data-kettransfer='$ket_transfer'
                        data-nmpembuat='$nm_pembuat'
                        data-nmvalidasi='$nm_validasi'
                        data-nmotorisasi='$nm_otorisasi'
                        data-nmmanual='$nm_manual'
                        data-jenisproject='$jenis_project'
                        data-nmproject='$nm_project'
                        data-rekeningsumber='$rekening_sumber'

                    ><i class='fa fa-eye'></i> view</button>
                    ";
                                                            if ($data['hasil_transfer'] == 2) {
                                                                echo "<br>
                        <a class='Print btn btn-sm text-primary no-background' target='_blank' href='bukti-transfer-print.php?id=" . $data['transfer_req_id'] . "'><i class='fa fa-print'></i> print</a>";
                                                            }
                                                            if (($data['hasil_transfer'] == 3) && in_array("ulang_transfer", $buttonAkses)) {
                                                                echo "<br><button type='button' class='Edit btn btn-sm text-primary no-background' data-id='" . $data['transfer_id'] . "' data-button='antri' data-from='" . $url . "'><i class='fa fa-sync'></i> ulang</button>
                                                        ";
                                                            }
                                                            echo "</td>";
                                                            echo "</tr>";
                                                        }
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <br>
                                        <?php error_reporting(0); ?>
                                        <input type="hidden" value="<?php echo $_REQUEST['kettrf']; ?>" name="kettrf">
                                        <input type="hidden" value="<?php echo $_REQUEST['start_date']; ?>" name="start_date">
                                        <input type="hidden" value="<?php echo $_REQUEST['end_date']; ?>" name="end_date">
                                        <input type="hidden" value="<?php echo $_REQUEST['trftype']; ?>" name="trftype">
                                        <!-- <button type="submit" class="btn btn-success btn-small">Export To excel</button> -->
                                        <!-- </form> -->


                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade <?= ($_GET['tab'] == 'uangmuka') ? 'show active' : '' ?>" id="uangmuka" role="tabpanel" aria-labelledby="uangmuka-tab">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="form-group">
                                            <label><b>Search : </b></label>
                                            <form action="" method="get">
                                                <input type="hidden" name="tab" value="uangmuka">
                                                <input type="date" class="startdate" placeholder="Start Date" id="start_date" name="start_date" value="<?= ($start_date) ? $start_date : '' ?>">
                                                <span> S/D </span>
                                                <input type="date" class="enddate" placeholder="End Date" id="end_date" name="end_date" value="<?= ($end_date) ? $end_date : '' ?>">
                                                <span> | </span>
                                                <select name="kettrf">
                                                    <?php $getKetTransfer = mysqli_query($koneksiTransfer, "SELECT ket_transfer FROM data_transfer WHERE ket_transfer != 'Success' GROUP BY ket_transfer ORDER BY ket_transfer ASC"); ?>
                                                    <option value="">Pilih</option>
                                                    <?php
                                                    while ($item = mysqli_fetch_assoc($getKetTransfer)) :
                                                    ?>
                                                        <option value="<?php echo $item['ket_transfer'] ?>" <?= ($kettrf == $item['ket_transfer']) ? "selected" : "" ?>><?php echo $item['ket_transfer'] ?></option>
                                                    <?php endwhile; ?>
                                                </select>
                                                <span> | </span>
                                                <select name="trftype">
                                                    <option value="">Pilih</option>
                                                    <option value="1" <?= ($trftype == 1) ? "selected" : "" ?>>Transfer Schedule</option>
                                                    <option value="2" <?= ($trftype == 2) ? "selected" : "" ?>>Transfer Manual</option>
                                                    <option value="3" <?= ($trftype == 3) ? "selected" : "" ?>>Transfer Auto</option>
                                                </select>
                                                <button class="btn-primary" type="submit">Search</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="callout callout-info ml-3">
                                        <?php
                                        $i = 1;
                                        $total = 0;
                                        $getTrfFlap = mysqli_query($koneksiTransfer, "SELECT * FROM data_transfer JOIN mritransfer.jenis_pembayaran AS t2 ON data_transfer.jenis_pembayaran_id = t2.jenispembayaranid WHERE hasil_transfer NOT IN ('1','4') AND keterangan IN ('UM', 'UM Burek')" . (($bagianWhere) ? " AND " . $bagianWhere : "") . "  ORDER BY data_transfer.transfer_id ASC") or die(mysqli_error($koneksiTransfer));
                                        while ($item = mysqli_fetch_assoc($getTrfFlap)) {
                                            $total += $item['jumlah'];
                                        }


                                        $getTrfFlapSuccess = mysqli_query($koneksiTransfer, "SELECT SUM(jumlah) AS jumlah FROM data_transfer JOIN mritransfer.jenis_pembayaran AS t2 ON data_transfer.jenis_pembayaran_id = t2.jenispembayaranid WHERE hasil_transfer = 2 AND keterangan IN ('UM', 'UM Burek')" . (($bagianWhere) ? " AND " . $bagianWhere : "")) or die(mysqli_error($koneksiTransfer));
                                        // var_dump(mysqli_fetch_assoc($getTrfFlapSuccess));
                                        $totalSuccess = mysqli_fetch_assoc($getTrfFlapSuccess)['jumlah'];
                                        // var_dump('here');

                                        $getTrfFlapNotSuccess = mysqli_query($koneksiTransfer, "SELECT SUM(jumlah) AS jumlah FROM data_transfer JOIN mritransfer.jenis_pembayaran AS t2 ON data_transfer.jenis_pembayaran_id = t2.jenispembayaranid WHERE hasil_transfer = 3 AND keterangan IN ('UM', 'UM Burek')" . (($bagianWhere) ? " AND " . $bagianWhere : "")) or die(mysqli_error($koneksiTransfer));
                                        $totalNotSuccess = mysqli_fetch_assoc($getTrfFlapNotSuccess)['jumlah'];

                                        $getTrfFlapNotSuccess = mysqli_query($koneksiTransfer, "SELECT SUM(jumlah) AS jumlah FROM data_transfer JOIN mritransfer.jenis_pembayaran AS t2 ON data_transfer.jenis_pembayaran_id = t2.jenispembayaranid WHERE hasil_transfer = 1 AND keterangan IN ('UM', 'UM Burek')" . (($bagianWhere) ? " AND " . $bagianWhere : "")) or die(mysqli_error($koneksiTransfer));
                                        $totalAntri = mysqli_fetch_assoc($getTrfFlapNotSuccess)['jumlah'];

                                        $getTrfFlapCancel = mysqli_query($koneksiTransfer, "SELECT SUM(jumlah) AS jumlah FROM data_transfer JOIN mritransfer.jenis_pembayaran AS t2 ON data_transfer.jenis_pembayaran_id = t2.jenispembayaranid WHERE hasil_transfer = 4 AND keterangan IN ('UM', 'UM Burek')" . (($bagianWhere) ? " AND " . $bagianWhere : "")) or die(mysqli_error($koneksiTransfer));
                                        $totalCancel = mysqli_fetch_assoc($getTrfFlapCancel)['jumlah'];
                                        ?>
                                        <h5>Total Pembayaran Keseluruhan: <?php echo 'Rp. ' . number_format($total, 0, '', ','); ?>
                                        </h5>
                                        <h5>Total Pembayaran Gagal: <?php echo 'Rp. ' . number_format($totalNotSuccess, 0, '', ','); ?></h5>
                                        <!-- <h5>Total Pembayaran Cancel: <?php echo 'Rp. ' . number_format($totalCancel, 0, '', ','); ?></h5> -->
                                        <h5>Total Pembayaran Sukses: <?php echo 'Rp. ' . number_format($totalSuccess, 0, '', ','); ?></h5>
                                    </div>

                                    <div class="card-body">
                                        <!-- <form action="" method="get"> -->
                                        <div class="table-responsive">
                                            <table id="example3" class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>No.</th>
                                                        <th>Request ID</th>
                                                        <th>Jenis Pembayaran</th>
                                                        <th>Jadwal Transfer</th>
                                                        <th>No. Rekening</th>
                                                        <th>Jumlah</th>
                                                        <th>Ket. Transfer</th>
                                                        <th>Ket. Tambahan</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $getTrfFlap = mysqli_query($koneksiTransfer, "SELECT * FROM data_transfer JOIN mritransfer.jenis_pembayaran AS t2 ON data_transfer.jenis_pembayaran_id = t2.jenispembayaranid " . (($bagianWhere) ? "WHERE " . $bagianWhere : "") . " AND hasil_transfer NOT IN ('1','4') AND keterangan IN ('UM', 'UM Burek') ORDER BY data_transfer.transfer_id ASC") or die(mysqli_error($koneksiTransfer));

                                                    if (!empty($getTrfFlap)) {
                                                        error_reporting(0);
                                                        $i = 1;
                                                        $total = 0;
                                                        while ($data = mysqli_fetch_assoc($getTrfFlap)) {
                                                            $transfer_req_id = $data['transfer_req_id'];
                                                            $jenispembayaran = $data['jenispembayaran'];
                                                            $jadwal_transfer = ($data['jadwal_transfer']) ? $data['jadwal_transfer'] : '-';
                                                            $norek = $data['norek'];
                                                            $jumlah = $data['jumlah'];
                                                            $ket_transfer = $data['ket_transfer'];
                                                            $keterangan = $data['keterangan'];
                                                            $waktu_request = $data['waktu_request'];
                                                            $pemilik_rekening = $data['pemilik_rekening'];
                                                            $bank = $data['bank'];
                                                            $kode_bank = $data['kode_bank'];
                                                            $nm_pembuat = $data['nm_pembuat'];
                                                            $nm_validasi = $data['nm_validasi'];
                                                            $nm_otorisasi = $data['nm_otorisasi'];
                                                            $nm_manual = $data['nm_manual'];
                                                            $jenis_project = $data['jenis_project'];
                                                            $nm_project = $data['nm_project'];
                                                            $rekening_sumber = $data['rekening_sumber'];
                                                            $ket_tambahan = $data['ket_tambahan'];

                                                            if ($data['transfer_type'] == 1) {
                                                                $transfer_type = 'Transfer Schedule';
                                                            } else if ($data['transfer_type'] == 2) {
                                                                $transfer_type = 'Transfer Manual';
                                                            } else {
                                                                $transfer_type = 'Transfer Auto';
                                                            }

                                                            echo "<tr>";
                                                            echo "<td>" . $i++ . "</td>";
                                                            echo "<td>" . $transfer_req_id . "</td>";
                                                            echo "<td>" . $keterangan . "</td>";
                                                            echo "<td>" . $jadwal_transfer . "</td>";
                                                            echo "<td>" . $norek . "</td>";
                                                            echo "<td>" . number_format($jumlah, 0, '', ',') . "</td>";
                                                            echo "<td>" . $ket_transfer . "</td>";
                                                            echo "<td>" . $ket_tambahan . "</td>";
                                                            echo "<td><button type='button' class='Update btn btn-sm text-primary no-background' 

                        data-transferreqid='$transfer_req_id'
                        data-transfertype='$transfer_type'
                        data-jenispembayaran='$jenispembayaran'
                        data-keterangan='$keterangan'
                        data-wakturequest='$waktu_request'
                        data-jadwaltransfer='$jadwal_transfer'
                        data-norek='$norek'
                        data-pemilikrekening='$pemilik_rekening'
                        data-bank='$bank'
                        data-kodebank='$kode_bank'
                        data-jumlah='" . number_format($jumlah, 0, '', ',') . "'
                        data-kettransfer='$ket_transfer'
                        data-nmpembuat='$nm_pembuat'
                        data-nmvalidasi='$nm_validasi'
                        data-nmotorisasi='$nm_otorisasi'
                        data-nmmanual='$nm_manual'
                        data-jenisproject='$jenis_project'
                        data-nmproject='$nm_project'
                        data-rekeningsumber='$rekening_sumber'

                    ><i class='fa fa-eye'></i> view</button>
                    ";
                                                            if ($data['hasil_transfer'] == 2) {
                                                                echo "<br>
                        <a class='Print btn btn-sm text-primary no-background' target='_blank' href='bukti-transfer-print.php?id=" . $data['transfer_req_id'] . "'><i class='fa fa-print'></i> print</a>";
                                                            }
                                                            if (($data['hasil_transfer'] == 3) && in_array("ulang_transfer", $buttonAkses)) {
                                                                echo "<br><button type='button' class='Edit btn btn-sm text-primary no-background' data-id='" . $data['transfer_id'] . "' data-button='antri' data-from='" . $url . "'><i class='fa fa-sync'></i> ulang</button>
                                                        ";
                                                            }
                                                            echo "</td>";
                                                            echo "</tr>";
                                                        }
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <br>
                                        <?php error_reporting(0); ?>
                                        <input type="hidden" value="<?php echo $_REQUEST['kettrf']; ?>" name="kettrf">
                                        <input type="hidden" value="<?php echo $_REQUEST['start_date']; ?>" name="start_date">
                                        <input type="hidden" value="<?php echo $_REQUEST['end_date']; ?>" name="end_date">
                                        <input type="hidden" value="<?php echo $_REQUEST['trftype']; ?>" name="trftype">
                                        <!-- <button type="submit" class="btn btn-success btn-small">Export To excel</button> -->
                                        <!-- </form> -->


                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </section>
        </div>
    </div>



    <!-- Modal -->
    <div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                            <input type="text" class="form-control" id="transferreqid" readonly>

                        </div>
                        <div class="form-group">
                            <label for="transfertype">TRF. Type</label>
                            <input type="text" class="form-control" id="transfertype" readonly>

                        </div>
                        <!-- <div class="form-group">
                            <label for="jenispembayaran">Jenis Pembayaran</label>
                            <input type="text" class="form-control" id="jenispembayaran" readonly>

                        </div> -->
                        <div class="form-group">
                            <label for="keterangan">Keterangan</label>
                            <input type="text" class="form-control" id="keterangan" readonly>

                        </div>
                        <div class="form-group">
                            <label for="wakturequest">Waktu Request</label>
                            <input type="text" class="form-control" id="wakturequest" readonly>

                        </div>
                        <div class="form-group">
                            <label for="jadwaltransfer">Jadwal Transfer</label>
                            <input type="text" class="form-control" id="jadwaltransfer" readonly>

                        </div>
                        <div class="form-group">
                            <label for="norek">No. Rekening</label>
                            <input type="text" class="form-control" id="norek" readonly>

                        </div>
                        <div class="form-group">
                            <label for="pemilikrekening">Nama Rekening</label>
                            <input type="text" class="form-control" id="pemilikrekening" readonly>

                        </div>
                        <div class="form-group">
                            <label for="bank">Bank</label>
                            <input type="text" class="form-control" id="bank" readonly>

                        </div>
                        <div class="form-group">
                            <label for="kodebank">Kode Bank</label>
                            <input type="text" class="form-control" id="kodebank" readonly>

                        </div>
                        <div class="form-group">
                            <label for="jumlah">Jumlah</label>
                            <input type="text" class="form-control" id="jumlah" readonly>

                        </div>
                        <div class="form-group">
                            <label for="kettransfer">Ket. Transfer</label>
                            <input type="text" class="form-control" id="kettransfer" readonly>

                        </div>
                        <div class="form-group">
                            <label for="nmpembuat">Nama Pembuat</label>
                            <input type="text" class="form-control" id="nmpembuat" readonly>

                        </div>
                        <div class="form-group">
                            <label for="nmvalidasi">Nama Validasi</label>
                            <input type="text" class="form-control" id="nmvalidasi" readonly>

                        </div>
                        <div class="form-group">
                            <label for="nmotorisasi">Nama Otorisasi</label>
                            <input type="text" class="form-control" id="nmotorisasi" readonly>

                        </div>
                        <div class="form-group">
                            <label for="nmmanual">Nama Manual</label>
                            <input type="text" class="form-control" id="nmmanual" readonly>

                        </div>
                        <div class="form-group">
                            <label for="jenisproject">Jenis Project</label>
                            <input type="text" class="form-control" id="jenisproject" readonly>

                        </div>
                        <div class="form-group">
                            <label for="nmproject">Nama Project</label>
                            <input type="text" class="form-control" id="nmproject" readonly>
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


    <div id="myModal2" class="modal fade">
        <div class="modal-dialog modal-lg">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Ulang Transfer</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                </div>
                <form action="antrian-transfer-proses.php" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="id">
                        <input type="hidden" name="button">
                        <input type="hidden" name="from">

                        <div class="form-group">
                            <label for="jadwaltransfer">Jadwal Transfer</label>
                            <input type="datetime-local" class="form-control" name="jadwaltransfer" required>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Modal -->

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
        $(document).ready(function() {
            $('#example1').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'print'
                ]
            });
            $('#example2').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'print'
                ]
            });
            $('#example3').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'print'
                ]
            });

            let title = $('a.nav-link.active').text();
            $('title').text('Laporan Transfer ' + title);

            $('.nav-link').click(function() {
                $('title').text('Laporan Transfer ' + $(this).text());
            })
        });

        $(function() {
            $(document).on('click', '.Update', function() {
                console.log('here');
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
                // return fa    lse
            })


            $(document).on('click', '.Edit', function() {
                $('#myModal2').modal("show");
                var id = $(this).attr('data-id');
                var button = $(this).attr('data-button');
                var from = $(this).attr('data-from');

                $('input[name=id]').val(id);
                $('input[name=button]').val(button);
                $('input[name=from]').val(from);
                return false;
            })
        })
    </script>

</body>

</html>