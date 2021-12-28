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

$code = $_GET['code'];

$query = mysqli_query($koneksi, "SELECT a.nama, a.waktu, a.noid, a.jenis FROM pengajuan a JOIN bpu b ON a.waktu = b.waktu JOIN selesai c ON b.waktu = c.waktu where b.namapenerima = '$code' GROUP BY nama");
$queryUser = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE nama_user = '$code'");
$tb_user = mysqli_fetch_assoc($queryUser);

$queryTotalOut = mysqli_query($koneksi, "SELECT SUM(jumlah) AS total_pengajuan FROM (SELECT DISTINCT a.* FROM bpu a JOIN selesai b ON a.waktu = b.waktu AND a.no = b.no LEFT JOIN tb_user c ON c.nama_user = a.namapenerima WHERE b.status IN ('UM', 'UM Burek') AND a.status IN ('Telah Di Bayar', 'Belum Di Bayar') AND a.namapenerima = '$code') AS t") or die(mysqli_error($koneksi));
$totalOut = mysqli_fetch_assoc($queryTotalOut);
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
                <?php if ($_SESSION['hak_akses'] == 'HRD') { ?>
                    <a class="navbar-brand" href="home-direksi.php">Budget-Ing</a>
                <?php } else { ?>
                    <a class="navbar-brand" href="home-finance.php">Budget-Ing</a>
                <?php } ?>
            </div>
            <div class="collapse navbar-collapse" id="myNavbar">
                <ul class="nav navbar-nav">
                    <?php if ($_SESSION['hak_akses'] == 'HRD') { ?>
                        <li class="active"><a href="home-direksi.php">Home</a></li>
                        <li><a href="list-direksi.php">List</a></li>
                        <li><a href="saldobpu.php">Saldo BPU</a></li>
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
                        <li><a href="saldobpu.php">Saldo BPU</a></li>
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
                   <ul class="nav navbar-nav navbar-right">
                        <li><a href="notif-page.php"><i class="fa fa-envelope"></i></a></li>

                        <li><a href="ubahpassword.php"><span class="glyphicon glyphicon-user"></span><?php echo $_SESSION['nama_user']; ?> (<?php echo $_SESSION['divisi']; ?>)</a></li>
                        <li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> Logout</a></li>
                    </ul>
            </div>
        </div>
    </nav>


    <center>
        <h2>Status Uang Muka <?php echo $code; ?></h2>
    </center>

    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-1">Limit </div>
            <div class="col-xs-3">: Rp. <?= number_format($tb_user['saldo']) ?></b></div>
        </div>
        <div class="row">
            <div class="col-xs-1">Sisa Limit </div>
            <div class="col-xs-3">: Rp. <?= number_format($tb_user['saldo'] - $totalOut['total_pengajuan']) ?></b></div>
        </div>
        <br>
        <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
            <div class="panel-body no-padding">

                <ul class="list-inline row ml-3">
                    <li class="col-lg-3" style="padding-left: 30px;"><strong> Nama Project</strong></li>
                    <li class="col-lg-2"><strong>Total Saldo Awal Outstanding</strong></li>
                    <li class="col-lg-2"><strong>Total Pengajuan</strong></li>
		            <li class="col-lg-2"><strong>Total Realisasi</strong></li>
                    <li class="col-lg-2"><strong>Total Saldo Akhir Outstanding</strong></li>
                    <li class="col-lg-1"></li>
                </ul>
                <?php
                $i = 0;
                $total = 0;
                $totalTerbayar = 0;
                $totalBelumTerbayar = 0;
                $totalRealisasi = 0;
                while ($item = mysqli_fetch_assoc($query)) :
                    $queryBpu = mysqli_query($koneksi, "SELECT SUM(a.jumlah) AS total_pengajuan FROM bpu a JOIN selesai b ON a.waktu = b.waktu AND a.no = b.no WHERE b.status IN ('UM', 'UM Burek') AND a.namapenerima = '$code' AND a.waktu = '$item[waktu]' AND a.uangkembali = '0' AND a.status IN ('Telah Di Bayar', 'Belum Di Bayar', 'Realisasi (Direksi)')") or die(mysqli_error($koneksi));
                    $pengajuan = mysqli_fetch_assoc($queryBpu);
                    
                    $queryBpuTerbayar = mysqli_query($koneksi, "SELECT SUM(a.jumlah) AS total_pengajuan FROM bpu a JOIN selesai b ON a.waktu = b.waktu AND a.no = b.no WHERE b.status IN ('UM', 'UM Burek') AND a.namapenerima = '$code' AND a.waktu = '$item[waktu]' AND a.status IN ('Telah Di Bayar','Realisasi (Direksi)')") or die(mysqli_error($koneksi));
                    $pengajuanTerbayar = mysqli_fetch_assoc($queryBpuTerbayar);
                    
                    $queryBpuBelumTerbayar = mysqli_query($koneksi, "SELECT SUM(a.jumlah) AS total_pengajuan FROM bpu a JOIN selesai b ON a.waktu = b.waktu AND a.no = b.no WHERE b.status IN ('UM', 'UM Burek') AND a.namapenerima = '$code' AND a.waktu = '$item[waktu]' AND a.status = 'Belum Di Bayar'") or die(mysqli_error($koneksi));
                    $pengajuanBelumTerbayar = mysqli_fetch_assoc($queryBpuBelumTerbayar);
                    
			        $queryBpuRealisasi = mysqli_query($koneksi, "SELECT SUM(a.realisasi) AS total_realisasi FROM bpu a JOIN selesai b ON a.waktu = b.waktu AND a.no = b.no WHERE b.status IN ('UM', 'UM Burek') AND a.namapenerima = '$code' AND a.waktu = '$item[waktu]' AND a.realisasi + a.uangkembali != a.jumlah AND a.status IN ('Telah Di Bayar','Realisasi (Direksi)')") or die(mysqli_error($koneksi));
                    $pengajuanRealisasi = mysqli_fetch_assoc($queryBpuRealisasi);

                    if ($pengajuan['total_pengajuan'] != null) :
                        $i++;
                        $total += $pengajuan['total_pengajuan'];
                        $totalRealisasi += $pengajuanRealisasi['total_realisasi'];
                        $totalTerbayar += $pengajuanTerbayar['total_pengajuan'];
                        $totalSaldoOutstanding = ($pengajuanTerbayar['total_pengajuan'] + $pengajuanBelumTerbayar['total_pengajuan']) -  $pengajuanRealisasi['total_realisasi'];
                ?>
                        <div class="list-group-item" id="grandparent<?= $i ?>" style="border: 1px solid black !important;">
                            <div id="expander" data-target="#grandparentContent<?= $i ?>" data-toggle="collapse" data-group-id="grandparent<?= $i ?>" data-role="expander">

                                <ul class="list-inline row border">
                                    <li class="col-lg-3"><?= $i . '. ' .  $item['nama'] ?></li>
                                    <li class="col-lg-2">Rp. <?= number_format($pengajuanTerbayar['total_pengajuan']) ?></li>
                                    <li class="col-lg-2">Rp. <?= number_format($pengajuanBelumTerbayar['total_pengajuan']) ?></li>
                		            <li class="col-lg-2">Rp. <?= number_format($pengajuanRealisasi['total_realisasi']) ?> </li>
		                            <li class="col-lg-2">Rp. <?= number_format($totalSaldoOutstanding) ?></li>
                                    <li class="col-lg-1">
                                        <?php
                                        $aksesSes = $_SESSION['hak_akses'];
                                        $divisiSes = $_SESSION['divisi'];
                                        if ($divisiSes == 'FINANCE') :
                                            if ($aksesSes == 'Manager') :
                                                if ($item['jenis'] == 'B1') : ?>
                                                    <a href="view-finance-manager-b1.php?code=<?= $item['noid'] ?>" target="_blank"><i class="fas fa-external-link-alt" title="View Budget"></i></a>
                                                <?php elseif ($item['jenis'] == 'B2' || $item['jenis'] == 'Rutin') : ?>
                                                    <a href="view-finance-manager.php?code=<?= $item['noid'] ?>" target="_blank"><i class="fas fa-external-link-alt" title="View Budget"></i></a>
                                                <?php elseif ($item['jenis'] == 'Non Rutin') : ?>
                                                    <a href="view-finance-nonrutin-manager.php?code=<?= $item['noid'] ?>" target="_blank"><i class="fas fa-external-link-alt" title="View Budget"></i></a>
                                                <?php endif; ?>
                                            <?php else : ?>
                                                <?php if ($item['jenis'] == 'B1' || $item['jenis'] == 'B2' || $item['jenis'] == 'Rutin') : ?>
                                                    <a href="view-finance.php?code=<?= $item['noid'] ?>" target="_blank"><i class="fas fa-external-link-alt" title="View Budget"></i></a>
                                                <?php elseif ($item['jenis'] == 'Non Rutin') : ?>
                                                    <a href="view-finance-nonrutin.php?code=<?= $item['noid'] ?>" target="_blank"><i class="fas fa-external-link-alt" title="View Budget"></i></a>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        <?php elseif ($divisiSes == 'Direksi') : ?>
                                            <a href="views-direksi.php?code=<?= $item['noid'] ?>" target="_blank"><i class="fas fa-external-link-alt" title="View Budget"></i></a>
                                        <?php endif; ?>
                                        <span id="grandparentIcon<?= $i ?>" style="cursor: pointer; margin: 0 10px;" class="col-lg-1"><a><i class="fas fa-eye" title="View Rincian"></i></a></span>
                                    </li>
                                </ul>
                            </div>
                            <div class="collapse" id="grandparentContent<?= $i ?>" aria-expanded="true">
                                <h3 class="text-center">Outstanding</h3>
                                <table class="table table-striped">
                                    <thead>
                                        <tr class="warning">
                                            <th>No.</th>
                                            <th>Nomor Item Budget</th>
                                            <th>Rincian Item Budget</th>
                                            <th>Term Bpu</th>
                                            <th>Terbayarkan</th>
                                            <th>Belum Terbayar</th>
                                            <th>Realisasi</th>
                                            <th>Sisa Realisasi</th>
                                            <th>Total Pengajuan</th>
                                            <th>Tanggal Bayar</th>
                                            <th>Tanggal Realisasi</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php
                                        $j = 1;
                                        $queryDetailBpu = mysqli_query($koneksi, "SELECT a.*, b.rincian FROM bpu a JOIN selesai b ON a.waktu = b.waktu AND a.no = b.no WHERE b.status IN ('UM', 'UM Burek') AND a.namapenerima = '$code' AND a.waktu = '$item[waktu]'  AND a.status IN ('Telah Di Bayar', 'Realisasi (Direksi)') AND a.realisasi + a.uangkembali != a.jumlah") or die(mysqli_error($koneksi));

                                        if (mysqli_num_rows($queryDetailBpu)) {
                                            while ($item2 = mysqli_fetch_assoc($queryDetailBpu)) :
                                                $queryBpuTerbayar = mysqli_query($koneksi, "SELECT SUM(a.jumlah) AS total_pengajuan FROM bpu a where a.noid = '$item2[noid]' AND a.status = 'Telah Di Bayar'") or die(mysqli_error($koneksi));
                                                $pengajuanTerbayar = mysqli_fetch_assoc($queryBpuTerbayar);
                                                
                                                $queryBpuBelumTerbayar = mysqli_query($koneksi, "SELECT SUM(a.jumlah) AS total_pengajuan FROM bpu a where a.noid = '$item2[noid]' AND a.status = 'Belum Di Bayar'") or die(mysqli_error($koneksi));
                                                $pengajuanBelumTerbayar = mysqli_fetch_assoc($queryBpuBelumTerbayar);
                                                
                                                $queryBpuRealisasi = mysqli_query($koneksi, "SELECT SUM(a.realisasi) AS total_realisasi FROM bpu a where a.noid = '$item2[noid]' AND a.realisasi + a.uangkembali != a.jumlah AND a.status IN ('Telah Di Bayar','Realisasi (Direksi)')") or die(mysqli_error($koneksi));
                                                $pengajuanRealisasi = mysqli_fetch_assoc($queryBpuRealisasi);

                                        ?>
                                                <tr data-toggle="collapse" data-target=".child1">
                                                    <td><?= $j++ ?></td>
                                                    <td><?= $item2['no'] ?></td>
                                                    <td><?= $item2['rincian'] ?></td>
                                                    <td><?= $item2['term'] ?></td>
                                                    <td>Rp. <?= number_format($pengajuanTerbayar['total_pengajuan']) ?></td>
                                                    <td>Rp. <?= number_format($pengajuanBelumTerbayar['total_pengajuan']) ?></td>
                                                    <td>Rp. <?= number_format($pengajuanRealisasi['total_realisasi']) ?></td>
                                                    <td>Rp. <?= number_format($item2['realisasi'] == 0 ? 0 : $item2['jumlah'] - $item2['realisasi'])  ?></td>
                                                    <td>Rp. <?= number_format($item2['jumlah'])  ?></td>
                                                    <td><?= $item2['tanggalbayar'] ?></td>
                                                    <td><?= $item2['tanggalrealisasi'] ?></td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php } else { ?>
                                            <tr data-toggle="collapse" data-target=".child1">
                                                <!-- <td></td> -->
                                                <td>Tidak ada outstanding</td>
                                                <!-- <td></td> -->
                                                <!-- <td></td> -->
                                            </tr>
                                        <?php } ?>

                                    </tbody>
                                </table>
                                <br>
                                <h3 class="text-center">Pengajuan</h3>
                                <table class="table table-striped">
                                    <thead>
                                        <tr class="warning">
                                            <th>No.</th>
                                            <th>Nomor Item Budget</th>
                                            <th>Rincian Item Budget</th>
                                            <th>Term Bpu</th>
                                            <th>Jumlah</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php
                                        $queryDetailBpu = mysqli_query($koneksi, "SELECT a.*, b.rincian FROM bpu a JOIN selesai b ON a.waktu = b.waktu AND a.no = b.no WHERE b.status IN ('UM', 'UM Burek') AND a.namapenerima = '$code' AND a.waktu = '$item[waktu]' AND a.status = 'Belum Di Bayar'") or die(mysqli_error($koneksi));
                                        if (mysqli_num_rows($queryDetailBpu)) {
                                            while ($item2 = mysqli_fetch_assoc($queryDetailBpu)) :
                                                $totalBelumTerbayar += $item2['jumlah'];
                                        ?>
                                                <tr data-toggle="collapse" data-target=".child1">
                                                    <td><?= $j++ ?></td>
                                                    <td><?= $item2['no'] ?></td>
                                                    <td><?= $item2['rincian'] ?></td>
                                                    <td><?= $item2['term'] ?></td>
                                                    <td>Rp.<?= number_format($item2['jumlah']) ?></td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php } else { ?>
                                            <td>Tidak ada pengajuan Uang Muka</td>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endwhile; ?>
                <br>
                <ul class="list-inline row ml-3">
                    <li class="col-lg-3" style="padding-left: 30px;"><strong>Total </strong></li>
                    <li class="col-lg-2"><strong>Rp. <?= number_format($totalTerbayar) ?></strong></li>
                    <li class="col-lg-2"><strong>Rp. <?= number_format($totalBelumTerbayar) ?></strong></li>
                    <li class="col-lg-2"><strong>Rp. <?= number_format($totalRealisasi) ?></strong></li>
                    <li class="col-lg-2"><strong>Rp. <?= number_format(($totalTerbayar + $totalBelumTerbayar) - $totalRealisasi) ?></strong></li>
                    <li class="col-lg-1">
                    </li>
                </ul>
                <!-- <p>Total Outstanding Uang Muka:</p>
                <p>Total Pengajuan Uang Muka: </p>
                <p>Total Uang Muka Keseluruhan: </p> -->
            </div><!-- /.table-responsive -->
        </div>
    </div>

    <script type="text/javascript">

    </script>

</body>

</html>
