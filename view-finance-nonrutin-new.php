<?php
error_reporting(0);
session_start();
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
if (!isset($_SESSION['nama_user'])) {
    header("location:login.php");
    // die('location:login.php');//jika belum login jangan lanjut
}

$querySetting = mysqli_query($koneksi, "SELECT * FROM setting_budget WHERE keterangan = 'approval_bpu'") or die(mysqli_error($koneksi));
$setting = mysqli_fetch_assoc($querySetting);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Form Pengajuan Budget</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" defer></script>
</head>

<body>

    <nav class="navbar navbar-inverse">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="home-direksi.php">Budget-Ing</a>
            </div>
            <div class="collapse navbar-collapse" id="myNavbar">
                <ul class="nav navbar-nav">
                    <?php if ($_SESSION['hak_akses'] == 'HRD') { ?>
                        <li><a href="home-direksi.php">Home</a></li>
                        <li><a href="list-direksi.php">List</a></li>
                        <li><a href="saldobpu.php">Data User</a></li>
                        <!--<li><a href="summary.php">Summary</a></li>-->
                        <li><a href="listfinish-direksi.php">Budget Finish</a></li>
                    <?php } else { ?>
                        <li><a href="home-finance.php">Home</a></li>
                        <?php
                        $aksesSes = $_SESSION['hak_akses'];
                        if ($aksesSes == 'Fani') {
                        ?>
                            <li><a href="list-finance-fani.php">List</a></li>
                        <?php } else if ($aksesSes == 'Manager') {
                        ?>
                            <li><a href="list-finance-budewi.php">List</a></li>
                        <?php
                        } else {
                        ?>
                            <li><a href="list-finance.php">List</a></li>
                        <?php } ?>
                        <li><a href="saldobpu.php">Data User</a></li>
                        <li><a href="history-finance.php">History</a></li>
                        <li><a href="list.php">Personal</a></li>
                        <li><a href="summary-finance.php">Summary</a></li>
                    <?php } ?>
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Rekap
                            <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="rekap-finance.php">Ready To Paid (MRI Kas)</a></li>
                            <li><a href="rekap-finance-mripal.php">Ready To Paid (MRI PAL)</a></li>
                            <li><a href="belumrealisasi.php">Belum Realisasi</a></li>
                            <li><a href="cashflow.php">Cash Flow</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Transfer
                            <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="laporan-transfer.php">Laporan Transfer</a></li>
                            <li><a href="antrian-transfer.php">Antrian Transfer</a></li>
                        </ul>
                    </li>
                </ul>


                <?php if ($_SESSION['hak_akses'] != 'HRD') { ?>
                    <?php
                    
                    $cari = mysqli_query($koneksi, "SELECT * FROM bpu WHERE status ='Belum Di Bayar' AND persetujuan !='Belum Disetujui' AND waktu != 0");
                    $belbyr = mysqli_num_rows($cari);
                    ?>
                   <ul class="nav navbar-nav navbar-right">
                        <li><a href="notif-page.php"><i class="fa fa-envelope"></i></a></li>
                        <li class="dropdown messages-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-inbox"></i><span class="label label-warning"><?= $belbyr ?></span></a>
                            <ul class="dropdown-menu">
                                <?php
                                while ($wkt = mysqli_fetch_array($cari)) {
                                    $wktulang = $wkt['waktu'];
                                    $selectnoid = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE waktu='$wktulang'");
                                    $noid = mysqli_fetch_assoc($selectnoid);
                                    $kode = $noid['noid'];
                                    $project = $noid['nama'];
                                ?>
                                    <li class="header"><a href="view-finance.php?code=<?= $kode ?>">Project <b><?= $project ?></b> BPU Belum Dibayar</a></li>
                                <?php
                                }
                                ?>
                            </ul>
                        </li>
                        <li><a href="ubahpassword.php"><span class="glyphicon glyphicon-user"></span><?php echo $_SESSION['nama_user']; ?> (<?php echo $_SESSION['divisi']; ?>)</a></li>
                        <li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> Logout</a></li>
                    </ul>
                <?php } else {
                    
                    $cari = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE status='Pending'");
                    $belbyr = mysqli_num_rows($cari);
                    $caribpu = mysqli_query($koneksi, "SELECT * FROM bpu WHERE persetujuan='Belum Disetujui'");
                    $bpuyahud = mysqli_num_rows($caribpu);
                    $queryPengajuanReq = mysqli_query($koneksi, "SELECT * FROM pengajuan_request WHERE status_request = 'Di Ajukan' AND waktu != 0");
                    $countPengajuanReq = mysqli_num_rows($queryPengajuanReq);
                    $notif = $belbyr + $bpuyahud + $countPengajuanReq;
                ?>
                   <ul class="nav navbar-nav navbar-right">
                        <li><a href="notif-page.php"><i class="fa fa-envelope"></i></a></li>
                        <li class="dropdown messages-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-inbox"></i><span class="label label-warning"><?= $notif ?></span></a>
                            <ul class="dropdown-menu">
                                <?php
                                while ($wkt = mysqli_fetch_array($cari)) {
                                    $wktulang = $wkt['waktu'];
                                    $selectnoid = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE waktu='$wktulang'");
                                    $noid = mysqli_fetch_assoc($selectnoid);
                                    $kode = $noid['noid'];
                                    $project = $noid['nama'];
                                ?>
                                    <li class="header"><a href="view-direksi.php?code=<?= $kode ?>">Project <b><?= $project ?></b> status masih Pending</a></li>
                                    <?php
                                    while ($wktbpu = mysqli_fetch_array($caribpu)) {
                                        $bpulagi = $wktbpu['waktu'];
                                        $selectnoid2 = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE waktu='$bpulagi'");
                                        $noid2 = mysqli_fetch_assoc($selectnoid2);
                                        $kode2 = $noid2['noid'];
                                        $project2 = $noid2['nama'];
                                    ?>
                                        <li class="header"><a href="views-direksi.php?code=<?= $kode2 ?>">Project <b><?= $project2 ?></b> ada BPU yang belum di setujui</a></li>
                                <?php
                                    }
                                }
                                ?>
                                <?php
                                while ($qpr = mysqli_fetch_array($queryPengajuanReq)) {
                                    $time = $qpr['waktu'];
                                    $selectnoid3 = mysqli_query($koneksi, "SELECT * FROM pengajuan_request WHERE waktu='$time'");
                                    $noid3 = mysqli_fetch_assoc($selectnoid3);
                                    $kode3 = $noid3['id'];
                                    $project3 = $noid3['nama'];
                                ?>
                                    <li class="header"><a href="view-request.php?id=<?= $kode3 ?>">Pengajuan Budget <b><?= $project3 ?></b> telah diajukan </a></li>
                                <?php
                                }
                                ?>
                            </ul>
                        </li>

                        <li><a href="ubahpassword.php"><span class="glyphicon glyphicon-user"></span><?php echo $_SESSION['nama_user']; ?> (<?php echo $_SESSION['divisi']; ?>)</a></li>
                        <li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> Logout</a></li>
                    </ul>
                <?php } ?>
            </div>
        </div>
    </nav>

    <div class="container">

        <?php
        
        $code = $_GET['code'];
        $select = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE noid='$code'");
        $d = mysqli_fetch_assoc($select);


        $queryUser = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE id_user = '$_SESSION[id_user]'");
        $user = mysqli_fetch_assoc($queryUser);
        $buttonAkses = unserialize($user['hak_button']);
        ?>

        <center>
            <h2><?php echo $d['nama']; ?></h2>
        </center>

        <br /><br />
        <div class="row">
            <div class="col-sm-2">Nama Yang Mengajukan</div>
            <div class="col-sm-6">: <b><?php echo $d['pengaju']; ?></b></div>
            <div class="col-sm-4"><b>C&nbsp;&nbsp; = <img src="images/coklat.jpg" width="20px" height="15px"> BPU perlu diketahui atasan</b></div>
        </div>

        <div class="row">
            <div class="col-sm-2">Divisi</div>
            <div class="col-sm-6">: <b><?php echo $d['divisi']; ?></b></div>
            <div class="col-sm-4"><b>O&nbsp;&nbsp; = <img src="images/orange.png" width="20px" height="15px"> BPU perlu diverifikasi/ditindaklanjuti Finance</b></div>
        </div>

        <div class="row">
            <div class="col-sm-2">Tahun</div>
            <div class="col-sm-6">: <b><?php echo $d['tahun']; ?></b></div>
            <div class="col-sm-4"><b>MM = <img src="images/pink.png" width="20px" height="15px"> Pengajuan Approval BPU</b></div>
        </div>

        <div class="row">
            <div class="col-sm-2"></div>
            <div class="col-sm-6"></div>
            <div class="col-sm-4"><b>K&nbsp;&nbsp; = <img src="images/kuning.png" width="20px" height="15px"> BPU Disetujui</b></div>
        </div>

        <div class="row">
            <div class="col-sm-2"></div>
            <div class="col-sm-6"></div>
            <div class="col-sm-4"><b>HT = <img src="images/hijautua.png" width="20px" height="15px"> BPU Sudah Dibayar & <u>Belum Realisasi</u></b></div>
        </div>

        <div class="row">
            <div class="col-sm-2"></div>
            <div class="col-sm-6"></div>
            <div class="col-sm-4"><b>H&nbsp;&nbsp; = <img src="images/hijau.png" width="20px" height="15px"> BPU Sudah Dibayar & <u>Sudah Realisasi</u></b></div>
        </div>

        <div class="row">
            <div class="col-sm-2"></div>
            <div class="col-sm-6"></div>
            <div class="col-sm-4"><b>M&nbsp;&nbsp; = <img src="images/merah.jpg" width="20px" height="15px"> BPU Ditolak</b></div>
        </div>

        <br><br>

        <div class="but_list">
            <div class="bs-example bs-example-tabs" role="tabpanel" data-example-id="togglable-tabs">

                <ul id="myTab" class="nav nav-tabs" role="tablist">

                    <li role="presentation" class="active">
                        <a href="#budget" id="budget-tab" role="tab" data-toggle="tab" aria-controls="budget" aria-expanded="true">Budget</a>
                    </li>

                    <li role="presentation">
                        <a href="#history" role="tab" id="history-tab" data-toggle="tab" aria-controls="history">History</a>
                    </li>

                    <li role="presentation">
                        <a href="#rincian" role="tab" id="rincian-tab" data-toggle="tab" aria-controls="rincian">Rincian BPU</a>
                    </li>

                </ul>

                <div id="myTabContent" class="tab-content">
                    <!-- Tab -->

                    <div role="tabpanel" class="tab-pane fade in active" id="budget" aria-labelledby="home-tab">

                        <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
                            <div class="panel-body no-padding">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr class="warning">
                                            <th>No</th>
                                            <th>Rincian & Keterangan</th>
                                            <th>Kota</th>
                                            <th>Status</th>
                                            <th>Penerima Pembayaran</th>
                                            <th>Harga Satuan (IDR)</th>
                                            <th>Quantity</th>
                                            <th>Total Harga (IDR)</th>
                                            <th>Sisa Pembayaran</th>
                                            <th>Pembayaran</th>

                                            <?php
                                            $waktu = $d['waktu'];
                                            $selno = mysqli_query($koneksi, "SELECT no FROM selesai WHERE waktu ='$waktu'");
                                            $wkwk = mysqli_fetch_assoc($selno);
                                            $no = $wkwk['no'];
                                            $liatbayarth = mysqli_query($koneksi, "SELECT * FROM bpu WHERE waktu='$waktu' AND no='$no'");
                                            if (mysqli_num_rows($liatbayarth) == 0) {
                                                echo "";
                                            } else {
                                                $n = 1;
                                                while ($bayar = mysqli_fetch_array($liatbayarth)) {
                                                    echo "<th>Term Pembayaran " . $n++ . "</th>";
                                                }
                                            } ?>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php
                                        $i = 1;
                                        $waktu = $d['waktu'];
                                        $checkName = [];
                                        $sql = mysqli_query($koneksi, "SELECT * FROM selesai WHERE waktu='$waktu' ORDER BY no");
                                        while ($a = mysqli_fetch_array($sql)) {
                                            if (!in_array($a["rincian"], $checkName)) :
                                                $no = $a['no'];
                                                $waktu = $a['waktu'];
                                                // $a['status'] = 'UM Burek';
                                        ?>
                                                <tr>
                                                    <th scope="row"><?php echo $i++; ?></th>
                                                    <td><?php echo $a['rincian']; ?></td>
                                                    <td><?php echo $a['kota']; ?></td>
                                                    <td><?php echo $a['status']; ?></td>
                                                    <td><?php echo $a['penerima']; ?></td>
                                                    <td><?php echo 'Rp. ' . number_format($a['harga'], 0, '', ','); ?></td>
                                                    <td><?php echo $a['quantity']; ?></td>
                                                    <td><?php echo 'Rp. ' . number_format($a['total'], 0, '', ','); ?></td>

                                                    <!-- Sisa Pembayaran -->
                                                    <?php
                                                    $no = $a['no'];
                                                    $waktu = $a['waktu'];
                                                    $pilihtotal = mysqli_query($koneksi, "SELECT total FROM selesai WHERE no='$no' AND waktu='$waktu'");
                                                    $aw = mysqli_fetch_assoc($pilihtotal);
                                                    $hargaah = $aw['total'];
                                                    $query = "SELECT sum(jumlahbayar) AS sum FROM bpu WHERE no='$no' AND waktu='$waktu'";
                                                    $result = mysqli_query($koneksi, $query);
                                                    $row = mysqli_fetch_array($result);
                                                    $total = $row[0];
                                                    $query16 = "SELECT sum(uangkembali) AS sum FROM bpu WHERE no='$no' AND waktu='$waktu'";
                                                    $result16 = mysqli_query($koneksi, $query16);
                                                    $row16 = mysqli_fetch_array($result16);
                                                    $total16 = $row16[0];
                                                    $jadinya = ($hargaah - $total) + $total16
                                                    ?>
                                                    <td><?php echo 'Rp. ' . number_format($jadinya, 0, '', ','); ?></td>
                                                    <!-- //Sisa Pembayaran -->
                                                    <td>

                                                        <?php
                                                        if ($a['status'] == 'UM' || $a['status'] == 'UM Burek' || $a['status'] == 'Finance' || $a['status'] == 'Pulsa' || $a['status'] == 'Biaya' || $a['status'] == 'Biaya Lumpsum') {
                                                        ?>
                                                            <!-- <button type="button" class="btn btn-default btn-small" onclick="edit_budget('<?php echo $no; ?>','<?php echo $waktu; ?>')">Bayar</button> -->
                                                            <?php
                                                        } else {
                                                            if (in_array("eksternal_bpu", $buttonAkses)) :
                                                            ?>
                                                                <br>
                                                                <button type="button" class="btn btn-success btn-small" onclick="eksternal('<?php echo $no; ?>','<?php echo $waktu; ?>')">Eksternal</button>
                                                        <?php
                                                            endif;
                                                        } ?>

                                                        <?php
                                                        if ($aksesSes != "Level 1") {
                                                        ?>
                                                            <?php if ($a['status'] == 'UM' || $a['status'] == 'Pulsa' || $a['status'] == 'Biaya External' || $a['status'] == 'Biaya' || $a['status'] == 'Biaya Lumpsum' || $a['status'] == 'UM Burek') { ?>
                                                                <button type="button" style="margin-top: 5px;" class="btn btn-default btn-small" onclick="bpu_budget('<?php echo $no; ?>','<?php echo $waktu; ?>')">BPU</button>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </td>
                                                    <?php
                                                    $arrCheck = [];

                                                    $liatbayar = mysqli_query($koneksi, "SELECT * FROM bpu WHERE waktu='$waktu' AND no='$no'");
                                                    if (mysqli_num_rows($liatbayar) == 0) {
                                                        echo "";
                                                    } else {
                                                        while ($bayar = mysqli_fetch_array($liatbayar)) {
                                                            $queryTotal = mysqli_query($koneksi, "SELECT SUM(jumlah) AS jumlah_total, SUM(pengajuan_jumlah) AS jumlah_pengajuan FROM bpu WHERE waktu='$waktu' AND no='$no' AND term = '$bayar[term]'");
                                                            $total = mysqli_fetch_assoc($queryTotal);

                                                            if (!in_array($waktu . $no . $bayar['term'], $arrCheck)) :

                                                                $checkMetodePembayaran = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS count FROM bpu WHERE waktu='$waktu' AND no='$no' AND term = '$bayar[term]' AND metode_pembayaran = 'MRI Kas'"));

                                                                if ($checkMetodePembayaran['count']) {
                                                                    $metodePembayaran = 'MRI Kas';
                                                                } else {
                                                                    $metodePembayaran = 'MRI PAL';
                                                                }

                                                                $checkPembayaran = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS count FROM bpu WHERE waktu='$waktu' AND no='$no' AND term = '$bayar[term]'  AND status = 'Belum Di Bayar'"));
                                                                if ($checkPembayaran['count']) {
                                                                    $statusbayar = 'Belum Di Bayar';
                                                                } else {
                                                                    $statusbayar = 'Telah Di Bayar';
                                                                }

                                                                $showButtonBayar =  mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS count FROM bpu WHERE waktu='$waktu' AND no='$no' AND term = '$bayar[term]'  AND status = 'Belum Di Bayar' AND metode_pembayaran = 'MRI Kas'"))['count'];
                                                                // var_dump($showButtonBayar);

                                                                $jumlbayar          = $bayar['jumlah'];
                                                                $pengajuanJumlah = $bayar['pengajuan_jumlah'];
                                                                $tglbyr             = $bayar['tglcair'];
                                                                // $statusbayar        = $bayar['status'];
                                                                $persetujuan        = $bayar['persetujuan'];
                                                                $novoucher          = $bayar['novoucher'];
                                                                $tanggalbayar       = $bayar['tanggalbayar'];
                                                                $nobay              = $bayar['no'];
                                                                $termm              = $bayar['term'];
                                                                $wakbay             = $bayar['waktu'];
                                                                $alasan             = $bayar['alasan'];
                                                                $realisasi          = $bayar['realisasi'];
                                                                $uangkembali        = $bayar['uangkembali'];
                                                                $tanggalrealisasi   = $bayar['tanggalrealisasi'];
                                                                $termreal           = $bayar['term'];
                                                                $namabank           = $bayar['namabank'];
                                                                $namapenerima       = $bayar['namapenerima'];
                                                                $norek              = $bayar['norek'];
                                                                $tglcair            = $bayar['tglcair'];
                                                                $waktustempel       = $bayar['waktustempel'];
                                                                $jumlahjadi         = $jumlbayar - $uangkembali;
                                                                $pengaju            = $bayar['pengaju'];
                                                                $divisi2            = $bayar['divisi'];
                                                                $pembayar           = $bayar['pembayar'];
                                                                $statusbpu        = $bayar['statusbpu'];
                                                                $fileupload       = $bayar['fileupload'];
                                                                $kembreal         = $realisasi + $uangkembali;
                                                                $sisarealisasi    = $jumlbayar - $kembreal;
                                                                $statusPengajuanBpu = $bayar['status_pengajuan_bpu'];
                                                                $pengajuan_realisasi = $bayar['pengajuan_realisasi'];
                                                                $pengajuan_uangkembali = $bayar['pengajuan_uangkembali'];
                                                                $pengajuan_tanggalrealisasi  = $bayar['pengajuan_tanggalrealisasi'];
                                                                $statusPengajuanRealisasi = $bayar['status_pengajuan_realisasi'];
                                                                $pengajuan_realiasi = $bayar['pengajuan_realiasi'];
                                                                $fileuploadRealisasi       = $bayar['fileupload_realisasi'];
                                                                $batasTanggalBayar = $bayar['batas_tanggal_bayar'];
                                                                $ketPembayaran = $bayar['ket_pembayaran'];

                                                                if ($uangkembali == 0) {
                                                                    $jumlahjadi = $jumlbayar;
                                                                } else if ($kembreal < $jumlbayar) {
                                                                    $jumlahjadi = $jumlbayar;
                                                                } else {
                                                                    $jumlahjadi = $realisasi;
                                                                }

                                                                $selstat = mysqli_query($koneksi, "SELECT status FROM selesai WHERE waktu='$waktu' AND no='$no'");
                                                                $ss = mysqli_fetch_assoc($selstat);
                                                                $exin = $ss['status'];

                                                                if ($persetujuan == 'Belum Disetujui' && $statusbayar == 'Belum Di Bayar' && $statusPengajuanBpu == '1') {
                                                                    $color = 'orange';
                                                                } else if ($persetujuan == 'Belum Disetujui' && $statusbayar == 'Belum Di Bayar') {
                                                                    $color = '#ffd3d3';
                                                                } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)' || $persetujuan == 'Disetujui oleh sistem') && $statusbayar == 'Belum Di Bayar') {
                                                                    $color = '#fff5c6';
                                                                } else if ($persetujuan == 'Pending' && $statusbayar == 'Belum Di Bayar') {
                                                                    $color = 'orange';
                                                                } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)' || $persetujuan == 'Disetujui oleh sistem') && $statusbayar == 'Telah Di Bayar' &&
                                                                    ($exin == 'Honor Eksternal' || $exin == 'Vendor/Supplier' || $exin == 'Lumpsum' || $exin == 'Honor SHP Jabodetabek' ||
                                                                        $exin == 'Honor SHI/PWT Jabodetabek' || $exin == 'Honor SHP Luar Kota' || $exin == 'Honor SHI/PWT Luar Kota' ||
                                                                        $exin == 'Honor Jakarta' || $exin == 'Honor Luar Kota' || $exin == 'STKB TRK Jakarta' || $exin == 'STKB TRK Luar Kota' || $exin == 'STKB OPS' || $exin == 'Honor Area Head')
                                                                ) {
                                                                    $color = '#d5f9bd';
                                                                } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)' || $persetujuan == 'Disetujui oleh sistem') && $statusbayar == 'Telah Di Bayar' && ($exin == 'Pulsa' || $exin == 'Biaya External' || $exin == 'Biaya' || $exin == 'Biaya Lumpsum')) {
                                                                    $color = '#d5f9bd';
                                                                } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)' || $persetujuan == 'Disetujui oleh sistem') && $statusbayar == 'Telah Di Bayar' && ($exin == 'UM' || $exin == 'UM Burek')) {
                                                                    $color = '#8aad70';
                                                                } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)' || $persetujuan == 'Disetujui oleh sistem') && $statusbayar == 'Realisasi (Direksi)' && ($exin == 'UM' || $exin == 'UM Burek')) {
                                                                    $color = '#d5f9bd';
                                                                } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)' || $persetujuan == 'Disetujui oleh sistem') && $statusbayar == 'Realisasi (Finance)' && ($exin == 'UM' || $exin == 'UM Burek')) {
                                                                    $color = '#d5f9bd';
                                                                }

                                                                if ($statusPengajuanBpu == 0 && ($persetujuan == 'Belum Disetujui' && $statusbayar == 'Belum Di Bayar')) {
                                                                    $color = '#ffd3d3';
                                                                } else if ($statusPengajuanBpu == 2) {
                                                                    $color = '#ff3b3b';
                                                                } else if ($statusPengajuanBpu == 3) {
                                                                    $color = '#DEB887';
                                                                }

                                                                echo "<td bgcolor=' $color '>";
                                                                echo "No :<b> $termm";
                                                                echo "</b><br>";
                                                                echo ($statusPengajuanBpu != 0) ? "Request BPU : <br><b>Rp. " . number_format($total['jumlah_pengajuan'], 0, '', ',') : "BPU : <br><b>Rp. " . number_format($total['jumlah_total'], 0, '', ',');
                                                                echo "</b><br>";
                                                                if ($realisasi != 0 && $statusbayar == 'Telah Di Bayar' && $statusbpu == 'UM') {
                                                                    echo "Realisasi Biaya : <br><b>Rp. " . number_format($kembreal, 0, '', ',');
                                                                    echo "</b><br>";
                                                                    echo "Sisa Realisasi: <br><b>Rp. " . number_format($sisarealisasi, 0, '', ',');
                                                                    echo "</b><br>";
                                                                } else if ($statusbayar == 'Realisasi (Direksi)') {
                                                                    echo "Realisasi Biaya: <br><b>Rp. " . number_format($realisasi, 0, '', ',');
                                                                    echo "</b><br>";
                                                                } else {
                                                                    echo "";
                                                                }
                                                                echo "Tanggal Buat BPU: <br><b> " . date('Y-m-d', strtotime($waktustempel));
                                                                echo "</b><br>";
                                                                echo "Jam : <b>" . date('H:i:s', strtotime($waktustempel));
                                                                echo "</b></br>";
                                                                echo "Tanggal Terima Uang : <b>$tglcair ";
                                                                echo "</b></br>";
                                                                echo "Diajukan Oleh : <br><b> $pengaju($divisi2)";
                                                                echo "</b><br>";
                                                                echo "No Voucher : <br><b> $novoucher ";
                                                                echo "</b><br/>";
                                                                echo "Tgl Bayar : <br><b> $tanggalbayar";
                                                                echo "</b><br/>";
                                                                echo "Kasir : <br><b> $pembayar ";
                                                                echo "</b><br/>";
                                                                echo "File Rincian BPU : <br>";
                                                                echo "<a href='view-print-bpu.php?no=$no&waktu=$waktu&term=$termm' target='_blank'><i class='fa fa-file'></i></a>";
                                                                echo "<br/><br/>";
                                                                if ($fileupload != NULL) {
                                                                    echo "File Upload : <br>";
                                                                    echo "<a href='uploads/$fileupload' target='_blank'><i class='fa fa-file'></i></a>";
                                                                    echo "<br/><br/>";
                                                                } else {
                                                                    echo "";
                                                                }
                                                                if ($fileuploadRealisasi != NULL) {
                                                                    echo "File Upload Realisasi: <br>";
                                                                    echo "<a href='uploads/$fileuploadRealisasi' target='_blank'><i class='fa fa-file'></i></a>";
                                                                    echo "<br/><br/>";
                                                                } else {
                                                                    echo "";
                                                                }

                                                                if ($persetujuan == 'Belum Disetujui' && $statusbayar == 'Belum Di Bayar') {
                                                                    echo "<i class='far fa-check-square'></i> Pengajuan ";
                                                                    echo "</b><br/>";
                                                                    echo "<i class='far fa-square'></i> Approval ";
                                                                    echo "</b><br/>";
                                                                    echo "<i class='far fa-square'></i> Paid ";
                                                                    echo "</b><br/>";
                                                                } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)' || $persetujuan == 'Disetujui oleh sistem') && $statusbayar == 'Belum Di Bayar') {
                                                                    echo "<i class='far fa-check-square'></i> Pengajuan";
                                                                    echo "</b><br/>";
                                                                    echo "<i class='far fa-check-square'></i> Approval";
                                                                    echo "</b><br/>";
                                                                    echo "<i class='far fa-square'></i> Paid ";
                                                                    echo "</b><br/>";
                                                                } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)' || $persetujuan == 'Disetujui oleh sistem') && ($statusbayar == 'Telah Di Bayar' || $statusbayar == 'Realisasi (Finance)' || $statusbayar == 'Realisasi (Direksi)')) {
                                                                    echo "<i class='far fa-check-square'></i> Pengajuan";
                                                                    echo "</b><br/>";
                                                                    echo "<i class='far fa-check-square'></i> Approval";
                                                                    echo "</b><br/>";
                                                                    echo "<i class='far fa-check-square'></i> Paid ";
                                                                    echo "</b><br/>";
                                                                    echo "Uang Kembali :<br><b> Rp. " . number_format($uangkembali, 0, '', ',');
                                                                    echo "</b><br/>";
                                                                }

                                                                if ($statusbayar == 'Realisasi (Direksi)' || $statusbayar == 'Realisasi (Finance)') {
                                                                    echo "<button><a href='forprint.php?page=2&code=" . $nobay . "&waktu=" . $wakbay . "&term=" . $termm . "'>Memorial</a></button>";
                                                                } else {
                                                                    echo "";
                                                                }

                                                                if ($statusPengajuanBpu == 2 && $pengaju == $_SESSION['nama_user']) { ?>
                                                                    <button type="button" style="margin-bottom: 5px; margin-top: 10px;" class="btn btn-info" onclick="ajukanBpu('<?php echo $no; ?>','<?php echo $waktu; ?>', '<?= $termm ?>', '<?= $pengajuanJumlah ?>', '<?= $namapenerima ?>', '<?= $norek ?>', '<?= $namabank ?>', '<?= $fileupload ?>', '<?= $alasanTolakBpu ?>', '<?= $statusbpu ?>')">Ajukan Kembali</button>
                                                                    <?php  }


                                                                if ($persetujuan == 'Pending' || $persetujuan == 'Belum Disetujui' && ($statusPengajuanBpu == 0 || !$statusPengajuanBpu)) :
                                                                    if ($jumlbayar <= $setting['plafon'] && $_SESSION['hak_akses'] == 'Manager') : ?>
                                                                        <button type="button" class="btn btn-success btn-small" onclick="setujuiBpu('<?php echo $no; ?>','<?php echo $waktu; ?>', '<?= $termm ?>')">Setujui</button>
                                                                        </br>
                                                                    <?php
                                                                    endif;
                                                                endif;


                                                                if (($a['status'] == 'UM' || $a['status'] == 'UM Burek' || $a['status'] == 'Finance' || $a['status'] == 'Pulsa' || $a['status'] == 'Biaya' || $a['status'] == 'Biaya Lumpsum' || $a['status'] == 'Vendor/Supplier' || $a['status'] == 'Honor Eksternal') && $showButtonBayar && $metodePembayaran != "MRI PAL") {
                                                                    ?>
                                                                    <?php if (is_null($batasTanggalBayar)) { ?>
                                                                        <button style="margin:3px 0" type="button" class="btn btn-info btn-small" onclick="bayarBpu('<?php echo $no; ?>','<?php echo $waktu; ?>','<?= $termm ?>')">Bayar</button>
                                                                        <?php } else {
                                                                        if ((int) date('H') < 15)
                                                                            $thisDay = date('Y-m-d');
                                                                        else
                                                                            $thisDay = date('Y-m-d', strtotime('+ 1 days'));

                                                                        if ($thisDay < $batasTanggalBayar) {
                                                                        ?>
                                                                            <button style="margin:3px 0" type="button" class="btn btn-info btn-small" onclick="bayarBpu('<?php echo $no; ?>','<?php echo $waktu; ?>','<?= $termm ?>')">Bayar</button>
                                                                        <?php } else { ?>
                                                                            <button style="margin:3px 0" type="button" title="Telah melebih waktu pembayaran" class="btn btn-info btn-small" onclick="bayarBpu('<?php echo $no; ?>','<?php echo $waktu; ?>','<?= $termm ?>')" disabled>Bayar</button>

                                                                    <?php }
                                                                    } ?>
                                                                <?php
                                                                }

                                                                if ($statusPengajuanBpu == 1  && in_array("verifikasi_bpu", $buttonAkses)) : ?>
                                                                    <br>
                                                                    <button type="button" style="margin-bottom: 5px; margin-top: 10px;" class="btn btn-info" onclick="verifikasiBpu('<?php echo $no; ?>','<?php echo $waktu; ?>', '<?= $termm ?>')">Verifikasi BPU</button>
                                                                    <?php
                                                                endif;

                                                                if (in_array("ubah_file_bpu", $buttonAkses)) :
                                                                    if (($a['status'] == 'UM' || $a['status'] == 'UM Burek') && ($statusbayar != 'Realisasi (Direksi)' && $statusbayar != 'Realisasi (Finance)')) :
                                                                    ?>
                                                                        <br>
                                                                        <button type="button" style="margin-bottom: 5px; margin-top: 10px;" class="btn btn-info" onclick="uploadFileBpu('<?php echo $no; ?>','<?php echo $waktu; ?>', '<?= $termm ?>', '<?= $code ?>',  '<?= $fileupload ?>')">Ubah File BPU</button>
                                                                    <?php
                                                                    elseif (($a['status'] != 'UM' && $a['status'] != 'UM Burek') && ($statusbayar != 'Telah Di Bayar')) : ?>
                                                                        <br>
                                                                        <button type="button" style="margin-bottom: 5px; margin-top: 10px;" class="btn btn-info" onclick="uploadFileBpu('<?php echo $no; ?>','<?php echo $waktu; ?>', '<?= $termm ?>', '<?= $code ?>',  '<?= $fileupload ?>')">Ubah File BPU</button>
                                                    <?php
                                                                    endif;
                                                                endif;

                                                                echo "<button><a href='forprint.php?page=1&code=" . $nobay . "&waktu=" . $wakbay . "&term=" . $termm . "'>Print</a></button>";
                                                                echo "</td>";
                                                                array_push($arrCheck, $waktu . $no . $bayar['term']);
                                                            endif;
                                                        }
                                                    } ?>
                                                </tr>
                                                <?php array_push($checkName, $a['rincian']); ?>
                                            <?php endif; ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div><!-- /.table-responsive -->
                        </div>

                        <div class="row">
                            <div class="col-xs-3">Total Budget Keseluruhan</div>
                            <div class="col-xs-3">: <b><?php echo 'Rp. ' . number_format($d['totalbudget'], 0, '', ','); ?></b></div>
                        </div>

                        <div class="row">
                            <div class="col-xs-3">
                                <font color="#1bd34f">Total Biaya dan Uang Muka
                            </div>

                            <?php
                            $query2 = "SELECT sum(jumlahbayar) AS sum FROM bpu WHERE waktu='$waktu'";
                            $result2 = mysqli_query($koneksi, $query2);
                            $row2 = mysqli_fetch_array($result2);

                            $q_real = "SELECT sum(realisasi) AS sum FROM bpu WHERE waktu='$waktu'";
                            $r_real = mysqli_query($koneksi, $q_real);
                            $g_real = mysqli_fetch_array($r_real);

                            $query10 = "SELECT sum(uangkembali) AS sum FROM bpu WHERE waktu='$waktu'";
                            $result10 = mysqli_query($koneksi, $query10);
                            $row10 = mysqli_fetch_array($result10);
                            $totlah = $row2['sum'];
                            $reallah = $row10['sum'];
                            $tysb = $totlah - $reallah;
                            ?>

                            <div class="col-xs-3">: <b><?php echo 'Rp. ' . number_format($tysb, 0, '', ','); ?></font></b></div>
                        </div>


                        <!-- Yang belum Bayar -->
                        <div class="row">
                            <div class="col-xs-3">
                                <font color='#f23f2b'>Sisa Budget
                            </div>
                            <?php
                            $aaaa = $d['totalbudget'];
                            $bbbb = $row2['sum'];
                            $belumbayar = $aaaa - $bbbb;
                            ?>
                            <div class="col-xs-3">: <b><?php echo 'Rp. ' . number_format($belumbayar, 0, '', ','); ?></font></b></div>
                        </div>
                        <!-- // Yang belum bayar -->

                        <!-- Ready To Pay -->
                        <div class="row">
                            <div class="col-xs-3">
                                <font color='#fcce00'>Ready To Pay :
                            </div>
                            <?php
                            $query3 = "SELECT sum(jumlah) AS sumi FROM bpu WHERE waktu='$waktu' AND persetujuan='Disetujui (Direksi)' AND status='Belum Di Bayar'";
                            $result3 = mysqli_query($koneksi, $query3);
                            $row3 = mysqli_fetch_array($result3);
                            ?>
                            <div class="col-xs-3">: <b><?php echo 'Rp. ' . number_format($row3['sumi'], 0, '', ','); ?></font></b></div>
                        </div>
                        <!-- // Ready To Pay -->

                        Note :
                        <div class="row">
                            <div class="col-xs-3">
                                <font color="#cbf442">Total Uang Kembali Realisasi
                            </div>
                            <div class="col-xs-3">: <b><?php echo 'Rp. ' . number_format($row10['sum'], 0, '', ','); ?></font></b></div>
                        </div>

                        <br><br>

                        <div class="row">
                            <div class="col-xs-12">
                                <p><strong>Alur Pengajuan BPU:</strong></p>
                                <p>1. User atau Finance <strong>membuat</strong> BPU</p>
                                <p style="margin-left: 15px;">1.a. <strong>User</strong>: Apabila BPU dibuat oleh User, BPU akan masuk ke proses persetujuan oleh Kepala Divisi (2) terlebih dahulu</p>
                                <p style="margin-left: 15px;">1.b. <strong>Finance</strong>: Apabila BPU dibuat oleh Finance, BPU akan masuk ke proses verifikasi oleh Finance (3)</p>
                                <p>2. Kepala Divisi melakukan <strong>persetujuan</strong> terhadap BPU</p>
                                <p>3. Finance melakukan <strong>verifikasi </strong>(menyetujui atau menolak) terhadap BPU yang telah di ajukan</p>
                                <p style="margin-left: 15px;">3.a. <strong>Setuju</strong>: Apabila BPU disetujui, BPU akan masuk ke proses selanjutnya</p>
                                <p style="margin-left: 15px;">3.b. <strong>Tolak</strong>: Apabila BPU ditolak, Pengaju BPU harus melakukan pengajuan kembali</p>
                                <p>4. Manajemen melakukan <strong>validasi </strong>(menyetujui atau menolak) terhadap BPU yang telah di verifikasi</p>
                                <p style="margin-left: 15px;">4.a. <strong>Setuju</strong>: Apabila BPU disetujui, BPU akan masuk ke proses selanjutnya</p>
                                <p style="margin-left: 15px;">4.b. <strong>Tolak</strong>: Apabila BPU ditolak, Pengaju BPU harus melakukan pengajuan kembali</p>
                                <p>5. Finance melakukan <strong>pembayaran</strong> terhadap BPU yang telah di validasi</p>
                                <p>6. Manajemen melakukan <strong>realisasi</strong> terhadap BPU</p>
                            </div>
                        </div>

                        <br><br>

                        <h3>Memo Upload</h3>
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr class="warning">
                                    <th>No</th>
                                    <th>Gambar</th>
                                    <th>Status</th>
                                    <th>Pembayaran</th>
                            </thead>

                            <tbody>
                                <?php
                                $selupload = mysqli_query($koneksi, "SELECT * FROM upload WHERE waktu='$waktu' AND status='Belum Dibayar'");
                                $i = 1;
                                while ($su = mysqli_fetch_array($selupload)) {
                                ?>
                                    <tr>
                                        <td><?php echo $i++ ?></td>
                                        <td><a href="uploads/<?php echo $su['gambar']; ?>"><img src="uploads/<?php echo $su['gambar']; ?>" width="75" height="75"></a></td>
                                        <td><?php echo $su['status']; ?></td>
                                        <td><button type="button" class="btn btn-default btn-small" onclick="bayarmemo('<?php echo $no; ?>','<?php echo $waktu; ?>')">Bayar Memo</button></td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>

                    </div>

                    <div role="tabpanel" class="tab-pane fade" id="history" aria-labelledby="history-tab">
                        <h3>History Pembayaran Memo</h3>
                        <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
                            <div class="panel-body no-padding">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr class="warning">
                                            <th>No</th>
                                            <th>Gambar</th>
                                            <th>Tanggal Upload</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        
                                        $i = 1;
                                        $divisi = $_SESSION['divisi'];
                                        $sql = mysqli_query($koneksi, "SELECT * FROM upload WHERE waktu='$waktu' AND status='Telah Dibayar'");
                                        while ($a = mysqli_fetch_array($sql)) {
                                        ?>
                                            <tr>
                                                <th scope="row"><?php echo $i++; ?></th>
                                                <td><a href="uploads/<?php echo $a['gambar']; ?>"><img src="uploads/<?php echo $a['gambar']; ?>" width="75" height="75"></a></td>
                                                <td><?php echo $a['timestam']; ?>
                                                <td><?php echo $a['status']; ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div><!-- /.table-responsive -->
                        </div>
                    </div>

                    <div role="tabpanel" class="tab-pane fade" id="rincian" aria-labelledby="rincian-tab">
                        <h3>Rincian BPU "Belum Di Bayar"</h3>
                        <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
                            <div class="panel-body no-padding">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr class="warning">
                                            <th>Nomor</th>
                                            <th>Nama Bank</th>
                                            <th>Nomor Rekening</th>
                                            <th>Nama Penerima</th>
                                            <th>Jenis</th>
                                            <th>Total BPU</th>
                                            <th>Req Tgl Pencairan</th>
                                            <th>Status</th>
                                            <th>Persetujuan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql = mysqli_query($koneksi, "SELECT * FROM bpu WHERE waktu='$waktu' AND status='Belum Di Bayar' ORDER BY no");
                                        while ($a = mysqli_fetch_array($sql)) {
                                        ?>
                                            <tr>
                                                <th scope="row"><?php echo $i++; ?></th>
                                                <td><?php echo $a['namabank']; ?></td>
                                                <td><?php echo $a['norek']; ?></td>
                                                <td><?php echo $a['namapenerima']; ?></td>
                                                <?php
                                                $nono = $a['no'];
                                                $eaaa = mysqli_query($koneksi, "SELECT status FROM selesai WHERE waktu='$waktu' AND no='$nono'");
                                                $eano = mysqli_fetch_assoc($eaaa);
                                                ?>
                                                <td><?php echo $eano['status']; ?></td>
                                                <td><?php echo 'Rp. ' . number_format($a['jumlah'], 0, '', ','); ?></td>
                                                <td><?php echo $a['tglcair']; ?></td>
                                                <td><?php echo $a['status']; ?></td>
                                                <td><?php echo $a['persetujuan']; ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div><!-- /.table-responsive -->
                        </div>


                        <!-- Yang belum Bayar BPU-->
                        <?php
                        $query3 = "SELECT sum(jumlah) AS sumjum FROM bpu WHERE waktu='$waktu' AND status='Belum Di Bayar'";
                        $result3 = mysqli_query($koneksi, $query3);
                        $row3 = mysqli_fetch_array($result3);
                        ?>
                        <p>
                        <h4><b>Total BPU : <?php echo 'Rp. ' . number_format($row3['sumjum'], 0, '', ','); ?></b></h4>
                        </p>
                        <!-- // Yang belum bayar BPU-->

                    </div>

                </div><!-- //Tab -->

            </div>

            <div class="modal fade" id="myModal" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h3 class="modal-title text-center">Bayar Budget</h3>
                        </div>
                        <div class="modal-body">
                            <div class="fetched-data"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="myModal2" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h3 class="modal-title text-center">Pembayaran Memo</h3>
                        </div>
                        <div class="modal-body">
                            <div class="fetched-data"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="myModal3" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h3 class="modal-title text-center">Pembayaran BPU Eksternal</h3>
                        </div>
                        <div class="modal-body">
                            <div class="fetched-data"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="myModal4" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h3 class="modal-title text-center">Realisasi</h3>
                        </div>
                        <div class="modal-body">
                            <div class="fetched-data"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="myModal5" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h3 class="modal-title text-center">BPU Eksternal</h3>
                        </div>
                        <div class="modal-body">
                            <div class="fetched-data"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="verifikasiBpuModal" role="dialog" aria-labelledby="verifikasiBpuModalLabel">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h3 class="modal-title text-center" id="verifikasiBpuModalLabel">Verifikasi BPU</h3>
                        </div>
                        <form action="proses-bpu-finance-new.php" method="post" name="Form" enctype="multipart/form-data">
                            <div class="modal-body">

                                <div class="fetched-data"></div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-danger" value="0" name="submit">Tolak</button>
                                <button type="submit" class="btn btn-primary" value="1" name="submit">Setuju</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            <div class="modal fade" id="setujuiBpuModal" role="dialog" aria-labelledby="setujuiBpuModalLabel">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h3 class="modal-title text-center" id="setujuiBpuModalLabel">Persetujuan BPU</h3>
                        </div>
                        <form action="setujuproses-new.php" method="post" name="Form" enctype="multipart/form-data">
                            <div class="modal-body">

                                <div class="fetched-data"></div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-danger" value="0" name="submit">Tolak</button>
                                <button type="submit" class="btn btn-primary" value="1" name="submit">Setuju</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            <div class="modal fade" id="bayarBpuModal" role="dialog" aria-labelledby="bayarBpuModalLabel">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h3 class="modal-title text-center" id="bayarBpuModalLabel">Bayar BPU</h3>
                        </div>
                        <form action="bayarbudgetproses-new.php" method="post" name="Form" enctype="multipart/form-data">
                            <div class="modal-body">

                                <div class="fetched-data"></div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" value="1" name="submit">Bayar</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            <div class="modal fade" id="ajukanBpuModal" role="dialog" aria-labelledby="ajukanBpuModalLabel">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h3 class="modal-title text-center" id="ajukanBpuModalLabel">Pengajuan Kembali BPU</h3>
                        </div>
                        <form action="ajukan-kembali-bpu-proses-new.php" method="post" name="Form" enctype="multipart/form-data">
                            <div class="modal-body">

                                <div class="fetched-data"></div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" value="1" name="submit">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="ubahFileBpuModal" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h3 class="modal-title text-center">Ubah File BPU</h3>
                        </div>
                        <div class="modal-body">
                            <form action="ubah-file-bpu.php" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="term" id="termUbahFile">
                                <input type="hidden" name="no" id="noUbahFile">
                                <input type="hidden" name="waktu" id="waktuUbahFile">
                                <input type="hidden" name="code" id="codeUbahFile">
                                <div class="form-group">
                                    <p class="control-p"><b>Uploaded File</b></p>
                                    <img id="imageUploadedFile" class="img-responsive" style="display: block; margin-left: auto;  margin-right: auto; background-repeat: no-repeat; background-size: cover; background-attachment: fixed;  " src="" alt="">
                                </div>
                                <div class="form-group">
                                    <p class="control-p"><b>Upload New File</b></p>
                                    <input type="file" class="form-control" accept="image/*,application/pdf" name="gambar" id="fileInputNewFileBpu">
                                    <img id="imageUploadNewFile" class="img-responsive" style="display: block; margin-left: auto;  margin-right: auto; background-repeat: no-repeat; background-size: cover; background-attachment: fixed;  " src="" alt="">
                                </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" type="submit" name="submit"> Submit</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>


            <?php
            $noid = isset($_GET['no']) && $_GET['no'] ? $_GET['no'] : NULL;
            $waktu = isset($_GET['waktu']) && $_GET['waktu'] ? $_GET['waktu'] : NULL;
            ?>

            <script type="text/javascript">
                $(document).ready(function() {
                    $('.umo_biaya_kode_id').select2();

                    $('#fileInputNewFileBpu').change(function() {
                        readURLNewFileBpu(this);
                    })
                })

                $('input[type=text][name=berita_transfer]').tooltip({
                    placement: "top",
                    trigger: "focus"
                });

                function readURLNewFileBpu(input) {
                    if (input.files && input.files[0]) {
                        var reader = new FileReader();

                        reader.onload = function(e) {
                            $('#imageUploadNewFile').attr('src', e.target.result);
                        }

                        reader.readAsDataURL(input.files[0]); // convert to base64 string
                    }
                }

                function uploadFileBpu(no, waktu, term, code, file) {
                    $('#noUbahFile').val(no);
                    $('#waktuUbahFile').val(waktu);
                    $('#termUbahFile').val(term);
                    $('#codeUbahFile').val(code);
                    $('#imageUploadedFile').attr('src', `uploads/${file}`)
                    $('#ubahFileBpuModal').modal('show');
                }

                function ajukanBpu(no, waktu, term, totalBpu, namaPenerima, noRek, namaBank, file, alasan, statusBpu) {
                    $.ajax({
                        type: 'post',
                        url: 'ajukan-kembali-bpu-new.php',
                        data: {
                            no: no,
                            waktu: waktu,
                            term: term
                        },
                        success: function(data) {
                            $('#ajukanBpuModal .fetched-data').html(data); //menampilkan data ke dalam modal
                            $('#ajukanBpuModal').modal();
                        }
                    });
                }

                function setujuiBpu(no, waktu, term) {
                    $.ajax({
                        type: 'post',
                        url: 'setuju-new.php',
                        data: {
                            no: no,
                            waktu: waktu,
                            term: term
                        },
                        success: function(data) {
                            $('#setujuiBpuModal .fetched-data').html(data); //menampilkan data ke dalam modal
                            $('#setujuiBpuModal').modal();
                        }
                    });
                }


                function verifikasiBpu(no, waktu, term) {
                    $.ajax({
                        type: 'post',
                        url: 'verifikasi-bpu.php',
                        data: {
                            no: no,
                            waktu: waktu,
                            term: term
                        },
                        success: function(data) {
                            $('#verifikasiBpuModal .fetched-data').html(data); //menampilkan data ke dalam modal
                            $('#verifikasiBpuModal').modal();
                        }
                    });
                }

                function numberWithCommas(x) {
                    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                }

                function bayarBpu(no, waktu, term) {
                    // alert(noid+' - '+waktu);
                    $.ajax({
                        type: 'post',
                        url: 'bayarbudget-new.php',
                        data: {
                            no: no,
                            waktu: waktu,
                            term: term
                        },
                        success: function(data) {
                            $('#bayarBpuModal .fetched-data').html(data); //menampilkan data ke dalam modal
                            $('#bayarBpuModal').modal();
                        }
                    });
                }

                function eksternal(no, waktu) {
                    // alert(noid+' - '+waktu);
                    $.ajax({
                        type: 'post',
                        url: 'eksternal-new.php',
                        data: {
                            no: no,
                            waktu: waktu
                        },
                        success: function(data) {
                            $('.fetched-data').html(data); //menampilkan data ke dalam modal
                            $('#myModal3').modal();
                        }
                    });
                }
            </script>

</body>

</html>