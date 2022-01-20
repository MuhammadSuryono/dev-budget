<?php
// error_reporting(0);
session_start();

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

if (!isset($_SESSION['nama_user'])) {
    header("location:login.php");
    // die('location:login.php');//jika belum login jangan lanjut
}

$year = (int)date('Y');
$subTab = ['B1', 'B2', 'Umum'];
$subTabUmum = ['Rutin', 'Non-Rutin']

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
    <!-- DataTables -->
    <link rel="stylesheet" href="datatables/dataTables.bootstrap.css">

    <style>
        iframe {
            width: 1px;
            min-width: 100%;
            *width: 100%;
        }

        /*Hidden class for adding and removing*/
        .lds-dual-ring.hidden {
            display: none;
        }

        /*Add an overlay to the entire page blocking any further presses to buttons or other elements.*/
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, .8);
            z-index: 999;
            opacity: 1;
            transition: all 0.5s;
        }

        /*Spinner Styles*/
        .lds-dual-ring {
            display: inline-block;
            width: 100%;
            height: 100%;
        }

        .lds-dual-ring:after {
            content: " ";
            display: block;
            width: 64px;
            height: 64px;
            margin: 5% auto;
            border-radius: 50%;
            border: 6px solid #fff;
            border-color: #fff transparent #fff transparent;
            animation: lds-dual-ring 1.2s linear infinite;
        }

        @keyframes lds-dual-ring {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>

    <!-- </head> -->

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
                <a class="navbar-brand" href="home-finance.php">Budget-Ing</a>
            </div>
            <div class="collapse navbar-collapse" id="myNavbar">
                <ul class="nav navbar-nav">
                    <li><a href="home-finance.php">Home</a></li>
                    <li class="active"><a href="list-finance.php">List</a></li>
                    <li><a href="saldobpu.php">Saldo BPU</a></li>
                    <!--<li><a href="summary.php">Summary</a></li>-->
                    <!-- <li><a href="hak-akses.php">Hak Akses</a></li> -->
                    <li><a href="listfinish-finance.php">Budget Finish</a></li>
                    <!-- <li><a href="history-direksi.php">History</a></li> -->
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
                <?php
                
                ?>
               <ul class="nav navbar-nav navbar-right">
                        <li><a href="/log-notifikasi-aplikasi/index.html" target="_blank"><i class="fa fa-envelope"></i></a></li>

                    <li><a href="ubahpassword.php"><span class="glyphicon glyphicon-user"></span><?php echo $_SESSION['nama_user']; ?> (<?php echo $_SESSION['divisi']; ?>)</a></li>
                    <li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>


    <div class="container-fluid">

        <!-- Nav List budget 2018 - 2019 -->
        <!-- <ul class="nav nav-pills">
            <li class="active"><a data-toggle="pill" href="#2021">List Budget 2021</a></li>
            <li><a data-toggle="pill" href="#2020">List Budget 2020</a></li>
            <li><a data-toggle="pill" href="#menu1">List Budget 2019</a></li>
            <li><a data-toggle="pill" href="#list2018">List Budget 2018</a></li>
            <li><a data-toggle="pill" href="#menu2">UM Burek, Honor SHP PWT, STKB</a></li>
        </ul> -->
        <div id="loader" class="lds-dual-ring hidden overlay"></div>

        <ul class="nav nav-pills">
            <ul class="nav nav-tabs">
                <?php for ($i = $year; $i > $year - 4; $i--) :
                    if ($i == $year) : ?>
                        <li class="active"><a data-toggle="pill" href="#<?= $i ?>" class="tab-year-button">List Budget <?= $i ?></a></li>
                    <?php else : ?>
                        <li><a data-toggle="pill" href="#<?= $i ?>" class="tab-year-button">List Budget <?= $i ?></a></li>
                    <?php endif; ?>
                <?php endfor; ?>
                <li><a data-toggle="pill" href="#menu2">UM Burek, Honor SHP PWT, STKB</a></li>
                <li class="uangMuka"> <a data-toggle="pill" href="#uangmuka2021">Rekap Monitoring Uang Muka</a></li=>
            </ul>
        </ul>

        <div class="tab-content">

            <?php for ($i = $year; $i > $year - 4; $i--) : ?>

                <div id="<?= $i ?>" class="tab-pane fade <?= ($i == $year) ? "active in" : "" ?>">
                    <ul id="myTab" class="nav nav-tabs" role="tablist">
                        <?php for ($j = 0; $j < count($subTab); $j++) : ?>
                            <li class="<?= ($j == 0) ? "active" : "" ?>" role="presentation">
                                <a href="#<?= $subTab[$j] ?>-<?= $i ?>" id="<?= $subTab[$j] ?>-tab" role="tab" data-toggle="tab" aria-controls="<?= $subTab[$j] ?>" aria-expanded="true" class="<?= ($subTab[$j] == 'Umum') ? "umum-button" : "end-button" ?>">Folder <?= $subTab[$j] ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>

                    <div id="myTabContent" class="tab-content">
                        <?php for ($j = 0; $j < count($subTab); $j++) : ?>
                            <div role="tabpanel" class="tab-pane fade <?= ($j == 0) ? "active in" : "" ?>" id="<?= $subTab[$j] . '-' . $i ?>" aria-labelledby="home-tab">

                                <?php if ($subTab[$j] == 'Umum') : ?>
                                    <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
                                        <div class="panel-body no-padding">
                                            <ul class="nav nav-tabs">
                                                <?php for ($k = 0; $k < count($subTabUmum); $k++) : ?>
                                                    <li class="<?= ($k == 0) ? "active" : "" ?>"><a href="#<?= $subTabUmum[$k] . '-' . $i ?>" class="end-button"><?= $subTabUmum[$k] ?></a>
                                                    </li>
                                                <?php endfor; ?>
                                            </ul>

                                            <div class="tab-content">
                                                <?php for ($k = 0; $k < count($subTabUmum); $k++) : ?>
                                                    <div class="tab-pane fade <?= ($k == 0) ? "active in" : "" ?>" id="<?= $subTabUmum[$k] . '-' . $i ?>">
                                                        <div class="tab-content tab-fetched-data"></div>
                                                    </div>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php else : ?>
                                    <div class="tab-content tab-fetched-data"></div>
                                <?php endif; ?>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            <?php endfor; ?>


            <div id="menu2" class="tab-pane fade">
                <ul id="myTab" class="nav nav-tabs" role="tablist">
                    <div id="myTabContent" class="tab-content">
                        <!-- UM BUREK -->
                        <div role="tabpanel" class="tab-pane fade in active" id="umburek" aria-labelledby="umburek-tab">
                            <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
                                <div class="panel-body no-padding">
                                    <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr class="warning">
                                        <th>#</th>
                                        <th>Nama (Divisi)</th>
                                        <th>Level</th>
                                        <th>Limit UM</th>
                                        <th>UM On Process</th>
                                        <th>Saldo UM</th>
                                        <th>BPU</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;
                                        $carinama = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE saldo IS NOT NULL ORDER BY nama_user");
                                        while ($cn = mysqli_fetch_array($carinama)) {
                                        ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td><?php echo $cn['nama_user'] ?> (<?php echo $cn['divisi'] ?>)</td>
                                            <td><?php echo $cn['level']; ?></td>
                                            <td><?php echo 'Rp. ' . number_format($cn['saldo'], 0, '', ','); ?></td>
                                            <td>
                                            <?php
                                            $namauser = $cn['nama_user'];
                                            $iduser   = $cn['id_user'];
                                            $carisaldo = mysqli_query($koneksi, "SELECT sum(jumlah) AS sumjum FROM bpu WHERE namapenerima='$namauser' AND status !='Realisasi (Direksi)' AND statusbpu='UM'");
                                            $cs = mysqli_fetch_array($carisaldo);
                                            echo 'Rp. ' . number_format($cs['sumjum'], 0, '', ',');
                                            ?>
                                            </td>
                                            <td>
                                            <?php
                                            $umproses = $cs['sumjum'];
                                            $limit    = $cn['saldo'];
                                            $umsisa   = $limit - $umproses;
                                            echo 'Rp. ' . number_format($umsisa, 0, '', ',');
                                            ?>
                                            </td>
                                            <td><button type="button" class="btn btn-success btn-small" onclick="bpu_um('<?php echo $iduser; ?>')">BPU</button></td>
                                            <?php
                                            $bpusamping = mysqli_query($koneksi, "SELECT * FROM bpu WHERE waktu='0000-00-00 00:00:00' AND namapenerima='$namauser' ORDER BY term");

                                            if (mysqli_num_rows($bpusamping) == 0) {
                                            echo "";
                                            } else {
                                            while ($bayar = mysqli_fetch_array($bpusamping)) {
                                                $noidbpu          = $bayar['noid'];
                                                $pengajuanJumlah = $bayar['pengajuan_jumlah'];
                                                $jumlbayar        = $bayar['jumlah'];
                                                $tglbyr           = $bayar['tglcair'];
                                                $statusbayar      = $bayar['status'];
                                                $persetujuan      = $bayar['persetujuan'];
                                                $bayarfinance     = $bayar['jumlahbayar'];
                                                $novoucher        = $bayar['novoucher'];
                                                $tanggalbayar     = $bayar['tanggalbayar'];
                                                $pengaju          = $bayar['pengaju'];
                                                $divisi2          = $bayar['divisi'];
                                                $namabank         = $bayar['namabank'];
                                                $norek            = $bayar['norek'];
                                                $namapenerima     = $bayar['namapenerima'];
                                                $alasan           = $bayar['alasan'];
                                                $realisasi        = $bayar['realisasi'];
                                                $uangkembali      = $bayar['uangkembali'];
                                                $tanggalrealisasi = $bayar['tanggalrealisasi'];
                                                $waktustempel     = $bayar['waktustempel'];
                                                $pembayar         = $bayar['pembayar'];
                                                $tglcair          = $bayar['tglcair'];
                                                $term             = $bayar['term'];
                                                $statusbpu        = $bayar['statusbpu'];
                                                $fileupload       = $bayar['fileupload'];
                                                $pengajuan_realisasi = $bayar['pengajuan_realisasi'];
                                                $pengajuan_uangkembali = $bayar['pengajuan_uangkembali'];
                                                $pengajuan_tanggalrealisasi  = $bayar['pengajuan_tanggalrealisasi'];
                                                $statusPengajuanRealisasi = $bayar['status_pengajuan_realisasi'];
                                                $noStkb       = ($bayar['nomorstkb']) ? $bayar['nomorstkb'] : '-';
                                                $kembreal         = $realisasi + $uangkembali;
                                                $sisarealisasi    = $jumlbayar - $kembreal;
                                                $nampro           = $bayar['project'];
                                                $jatuhtempo       = $bayar['jatuhtempo'];
                                                $statusPengajuanBpu = $bayar['status_pengajuan_bpu'];


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

                                                if ($persetujuan == 'Belum Disetujui' && $statusbayar == 'Belum Di Bayar') {
                                                $color = '#ffd3d3';
                                                } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)') && $statusbayar == 'Belum Di Bayar') {
                                                $color = 'orange';
                                                } else if ($persetujuan == 'Pending' && $statusbayar == 'Belum Di Bayar') {
                                                $color = 'orange';
                                                } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)') && $statusbayar == 'Telah Di Bayar' && ($exin == 'Honor Eksternal' || $exin == 'Vendor/Supplier' || $exin == 'Lumpsum')) {
                                                $color = '#d5f9bd';
                                                } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)') && $statusbayar == 'Telah Di Bayar' && ($exin == 'Pulsa' || $exin == 'Biaya External' || $exin == 'Biaya' || $exin == 'Biaya Lumpsum')) {
                                                $color = '#d5f9bd';
                                                } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)') && $statusbayar == 'Telah Di Bayar' && $exin == 'UM') {
                                                $color = '#8aad70';
                                                } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)') && $statusbayar == 'Realisasi (Direksi)' && $exin == 'UM') {
                                                $color = '#d5f9bd';
                                                } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)') && $statusbayar == 'Realisasi (Finance)' && $exin == 'UM') {
                                                $color = '#d5f9bd';
                                                }

                                                if ($statusPengajuanBpu == 0 && ($persetujuan == 'Belum Disetujui' && $statusbayar == 'Belum Di Bayar')) {
                                                $color = 'orange';
                                                } else if ($statusPengajuanBpu == 2) {
                                                $color = 'red';
                                                }

                                                if ($statusPengajuanRealisasi == 1) {
                                                $color = '#8aad70';
                                                } else if ($statusPengajuanRealisasi == 2) {
                                                $color = 'red';
                                                } else if ($statusPengajuanBpu == 3) {
                                                $color = 'orange';
                                                }

                                                echo "<td bgcolor=' $color '>";
                                                echo "No :<b> $term";
                                                echo "</b><br>";
                                                echo "No. STKB :<b> $noStkb";
                                                echo "</b><br>";
                                                echo ($statusPengajuanBpu != 0) ? "Request BPU : <br><b>Rp. " . number_format($pengajuanJumlah, 0, '', ',') : "BPU : <br><b>Rp. " . number_format($jumlbayar, 0, '', ',');
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
                                                echo "Tanggal : <br><b> " . date('Y-m-d', strtotime($waktustempel));
                                                echo "</b><br>";
                                                echo "Jam : <b>" . date('H:i:s', strtotime($waktustempel));
                                                echo "</b></br>";
                                                echo "Tanggal Terima Uang : <b>$tglcair ";
                                                echo "</b></br>";
                                                echo "Dibuat Oleh : <br><b> $pengaju($divisi2)";
                                                echo "</b><br>";
                                                echo "Project : <br><b> $nampro";
                                                echo "</b><br>";
                                                echo "Jatuh Tempo : <br><b> $jatuhtempo";
                                                echo "</b><br>";
                                                echo "Dibayarkan Kepada : <br><b> $namapenerima ";
                                                echo "</b><br>";
                                                echo "No Rekening :<b> $norek";
                                                echo "</b><br>";
                                                echo "Bank :<b> $namabank";
                                                echo "</b><br>";
                                                echo "No Voucher : <br><b> $novoucher ";
                                                echo "</b><br/>";
                                                echo "Tgl Bayar : <br><b> $tanggalbayar";
                                                echo "</b><br/>";
                                                echo "Kasir : <br><b> $pembayar ";
                                                echo "</b><br/>";
                                                if ($fileupload != NULL) {
                                                echo "File Upload : <br>";
                                                echo "<a href='uploads/$fileupload' target='_blank'><i class='fa fa-file'></i></a>";
                                                echo "<br/><br/>";
                                                } else {
                                                echo "";
                                                }
                                                if ($fileuploadRealisasi != NULL) {
                                                echo "File Upload : <br>";
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
                                                } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)') && $statusbayar == 'Belum Di Bayar') {
                                                echo "<i class='far fa-check-square'></i> Pengajuan";
                                                echo "</b><br/>";
                                                echo "<i class='far fa-check-square'></i> Approval";
                                                echo "</b><br/>";
                                                echo "<i class='far fa-square'></i> Paid ";
                                                echo "</b><br/>";
                                                } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)') && ($statusbayar == 'Telah Di Bayar' || $statusbayar == 'Realisasi (Finance)' || $statusbayar == 'Realisasi (Direksi)')) {
                                                echo "<i class='far fa-check-square'></i> Pengajuan";
                                                echo "</b><br/>";
                                                echo "<i class='far fa-check-square'></i> Approval";
                                                echo "</b><br/>";
                                                echo "<i class='far fa-check-square'></i> Paid ";
                                                echo "</b><br/>";
                                                }
                                                if ($statusPengajuanRealisasi != 4 && !($exin == 'Honor Eksternal' || $exin == 'Vendor/Supplier' || $exin == 'Lumpsum' || $exin == 'Honor SHP Jabodetabek' ||
                                                $exin == 'Honor SHI/PWT Jabodetabek' || $exin == 'Honor SHP Luar Kota' || $exin == 'Honor SHI/PWT Luar Kota' ||
                                                $exin == 'Honor Jakarta' || $exin == 'Honor Luar Kota' || $exin == 'STKB TRK Jakarta' || $exin == 'STKB TRK Luar Kota' || $exin == 'STKB OPS')) {
                                                echo "<i class='far fa-square'></i> Realisasi ";
                                                echo "</b><br/>";
                                                } else {
                                                echo "<i class='far fa-check-square'></i> Realisasi ";
                                                echo "</b><br/>";
                                                }


                                                if ($persetujuan == 'Pending' || $persetujuan == 'Belum Disetujui') {
                                                echo "Komentar : <br><b> $alasan ";
                                                echo "</b><br/>";
                                            ?>
                                                <button type="button" class="btn btn-success btn-small" onclick="edit_budget('<?php echo $term; ?>','<?php echo $namapenerima; ?>')">Setujui</button>
                                                </br>
                                                <button type="button" class="btn btn-warning btn-small" onclick="editharga('<?php echo $noidbpu; ?>','<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Edit</button>
                                                </br>
                                                <button type="button" class="btn btn-danger btn-small" onclick="hapus_bpu('<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Hapus</button>
                                                <?php
                                                } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)') and $statusbayar == 'Belum Di Bayar') {
                                                ?>
                                                <button type="button" class="btn btn-warning btn-small" onclick="editharga('<?php echo $noidbpu; ?>','<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Edit</button>
                                                </br>
                                                <button type="button" class="btn btn-danger btn-small" onclick="hapus_bpu('<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Hapus</button>

                                                <?php
                                                } else if ($statusbayar == 'Realisasi (Finance)' || $statusbayar == 'Realisasi (Direksi)' || $uangkembali != 0) {
                                                echo "Uang Kembali :<br><b> Rp. " . number_format($uangkembali, 0, '', ',');
                                                echo "</b><br/>";
                                                ?>
                                                <button type="button" class="btn btn-warning btn-small" onclick="editharga('<?php echo $noidbpu; ?>','<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Edit</button>
                                                </br>
                                                <button type="button" class="btn btn-danger btn-small" onclick="hapus_bpu('<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Hapus</button>
                                                <?php
                                                } else {
                                                ?>
                                                <button type="button" class="btn btn-warning btn-small" onclick="editharga('<?php echo $noidbpu; ?>','<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Edit</button>
                                                </br>
                                                <button type="button" class="btn btn-danger btn-small" onclick="hapus_bpu('<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Hapus</button>
                                            <?php
                                                }
                                                echo "</td>";
                                            }
                                            } ?>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                    </table>
                                </div><!-- /.table-responsive -->
                                </div>
                        </div>

                    </div>
            </div>

            <div role="tabpanel" class="tab-pane fade" id="uangmuka2021" aria-labelledby="uangmuka-tab">
                <div class="tab-content content-uang-muka">Sedang Mengambil data...</div>
            </div>
        </div>
    </div><!-- Content Nav -->
    <!--Container -->

    <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Persetujuan Budget</h4>
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
                    <h4 class="modal-title">Hapus Budget</h4>
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
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Finish Budget</h4>
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
                    <h4 class="modal-title">BPU UM Burek</h4>
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
                    <h4 class="modal-title">Persetujuan BPU UM Burek</h4>
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

    <div class="modal fade" id="myModal6" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Dissapprove</h4>
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


    <script type="text/javascript">
        $(document).ajaxStart(function() {
            // $('#loader').removeClass('hidden');
            $('.tab-fetched-data').html('<p>Sedang mengambil...</p>');
        }).ajaxSuccess(function() {
            // $('#loader').addClass('hidden');
        });

        const element = $('li.active .end-button').first();
        // console.log(element.attr('href'));

        if (!element.attr('href').includes('Umum')) {
            const href = element.attr('href').split('-');
            const tahun = href[href.length - 1];
            const tab = href[0];
            $.ajax({
                type: 'post',
                url: 'ajax/ajax-tab-listfinance.php',
                data: {
                    tahun: tahun,
                    tab: tab
                },
                success: function(data) {
                    console.log("UMUM");
                    $('.tab-fetched-data').html(data);
                }
            });
        }

        $('.uangMuka').click(function() {
            $.ajax({
                type: 'get',
                url: 'listbudewi/uangmuka.php',
                success: function(data) {
                    // console.log(data);
                    $('.content-uang-muka').html(data);
                }
            });
            })

            $('.umbrek').click(function() {
            $.ajax({
                type: 'get',
                url: 'listdireksi/umburek.php',
                success: function(data) {
                    // console.log(data);
                    $('.content-umbrek').html(data);
                }
            });
            })

        $(document).ready(function() {
            $('.end-button').click(function() {
                if (!$(this).attr('href').includes('Umum')) {
                    const href = $(this).attr('href').split('-');
                    const tahun = href[href.length - 1];
                    const tab = href[0];
                    $.ajax({
                        type: 'post',
                        url: 'ajax/ajax-tab-listfinance.php',
                        data: {
                            tahun: tahun,
                            tab: tab,
                        },
                        success: function(data) {
                            console.log("END");
                            $('.tab-fetched-data').html(data);
                        }
                    });
                }
            })

            $('.tab-year-button').click(function() {
                // console.log($('.tab-pane.fade.active.in li.active a.end-button'))
                tahun = $(this).attr('href').substring(1);
                tab = 'B1';
                $.ajax({
                    type: 'post',
                    url: 'ajax/ajax-tab-listfinance.php',
                    data: {
                        tahun: tahun,
                        tab: tab
                    },
                    success: function(data) {
                        console.log("YEAR");
                        $('.tab-fetched-data').html(data);
                    }
                });
            })

            $('.umum-button').click(function() {
                // console.log($('.tab-pane.fade.active.in li.active a.end-button'))
                const href = $(this).attr('href').split('-');
                const tahun = href[href.length - 1];
                const tab = 'Rutin';
                $.ajax({
                    type: 'post',
                    url: 'ajax/ajax-tab-listfinance.php',
                    data: {
                        tahun: tahun,
                        tab: tab
                    },
                    success: function(data) {
                        // console.log(data);
                        $('.tab-fetched-data').html(data);
                    }
                });
            })
        })

        $(document).ready(function() {
            $('.btn-honor').click(function() {
                // console.log($(this).text());
                const year = $(this).text();
                $.ajax({
                    type: 'post',
                    url: 'ajax/ajax-honor.php',
                    data: {
                        year: year
                    },
                    success: function(data) {
                        $('.honor-fetched-data').html(data);
                    }
                });
            })

            $('.header-btn-honor').click(function() {
                const year = <?= $year ?>;
                $.ajax({
                    type: 'post',
                    url: 'ajax/ajax-honor.php',
                    data: {
                        year: year
                    },
                    success: function(data) {
                        $('.honor-fetched-data').html(data);
                    }
                });
            })

            $('.header-btn-stkb').click(function() {
                // console.log($(this).text());
                const year = '<?= $year ?>';
                $.ajax({
                    type: 'post',
                    url: 'ajax/ajax-stkb.php',
                    data: {
                        year: year
                    },
                    success: function(data) {
                        $('.stkb-fetched-data').html(data);
                    }
                });
            })

            $('.btn-stkb').click(function() {
                // console.log($(this).text());
                const year = $(this).text();
                $.ajax({
                    type: 'post',
                    url: 'ajax/ajax-stkb.php',
                    data: {
                        year: year
                    },
                    success: function(data) {
                        $('.stkb-fetched-data').html(data);
                    }
                });
            })

            $('#myModal').on('show.bs.modal', function(e) {
                var rowid = $(e.relatedTarget).data('id');
                //menggunakan fungsi ajax untuk pengambilan data
                $.ajax({
                    type: 'post',
                    url: 'approve.php',
                    data: 'rowid=' + rowid,
                    success: function(data) {
                        $('.fetched-data').html(data); //menampilkan data ke dalam modal
                    }
                });
            });
        });

        $(document).ready(function() {
            $('#myModal2').on('show.bs.modal', function(e) {
                var rowid = $(e.relatedTarget).data('id');
                //menggunakan fungsi ajax untuk pengambilan data
                $.ajax({
                    type: 'post',
                    url: 'hapuslist.php',
                    data: 'rowid=' + rowid,
                    success: function(data) {
                        $('.fetched-data').html(data); //menampilkan data ke dalam modal
                    }
                });
            });
        });

        $(document).ready(function() {
            $('#myModal3').on('show.bs.modal', function(e) {
                var rowid = $(e.relatedTarget).data('id');
                //menggunakan fungsi ajax untuk pengambilan data
                $.ajax({
                    type: 'post',
                    url: 'finish.php',
                    data: 'rowid=' + rowid,
                    success: function(data) {
                        $('.fetched-data').html(data); //menampilkan data ke dalam modal
                    }
                });
            });
        });

        function bpu_um(iduser) {
            // alert(noid+' - '+waktu);
            $.ajax({
                type: 'post',
                url: 'bpuum.php',
                data: {
                    iduser: iduser
                },
                success: function(data) {
                    $('.fetched-data').html(data); //menampilkan data ke dalam modal
                    $('#myModal4').modal();
                }
            });
        }

        function edit_budget(term, namapenerima) {
            // alert(noid+' - '+waktu);
            $.ajax({
                type: 'post',
                url: 'setuju_um.php',
                data: {
                    term: term,
                    namapenerima: namapenerima
                },
                success: function(data) {
                    $('.fetched-data').html(data); //menampilkan data ke dalam modal
                    $('#myModal5').modal();
                }
            });
        }

        $(document).ready(function() {
            $('#myModal6').on('show.bs.modal', function(e) {
                var rowid = $(e.relatedTarget).data('id');
                //menggunakan fungsi ajax untuk pengambilan data
                $.ajax({
                    type: 'post',
                    url: 'disapprove.php',
                    data: 'rowid=' + rowid,
                    success: function(data) {
                        $('.fetched-data').html(data); //menampilkan data ke dalam modal
                    }
                });
            });
        });

        $(document).ready(function() {
            $(".nav-tabs a").click(function() {
                $(this).tab('show');
            });
        });

        // $('#B2').load('listdireksi/b2-2018.php');
        // $('#rutin').load('listdireksi/rutin-2018.php');
        // $('#nonrutin').load('listdireksi/nonrutin-2018.php');
        // $('#B22019').load('listdireksi/b2-2019.php');
        // $('#rutin2019').load('listdireksi/rutin-2019.php');
        // $('#nonrutin2019').load('listdireksi/nonrutin-2019.php');
        // $('#honor').load('listdireksi/honor.php');
        // $('#stkb').load('listdireksi/stkb.php');
    </script>


    <!-- </body></html> -->

</body>

</html>