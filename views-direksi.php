<?php
error_reporting(0);
session_start();

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

$con->set_name_db(DB_TRANSFER);
$con->init_connection();
$koneksiBridge = $con->connect();

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



</head>

<body>
<style>
    .tableFixHead          { overflow: auto; height: 100px; }
    .tableFixHead thead th { position: sticky; top: 0; z-index: 1; }
  </style>
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
          <li><a href="home-direksi.php">Home</a></li>
          <li class="active"><a href="list-direksi.php">List</a></li>
          <li><a href="saldobpu.php">Saldo BPU</a></li>
          <!--<li><a href="summary.php">Summary</a></li>-->
          <li><a href="listfinish-direksi.php">Budget Finish</a></li>
          <!-- <li><a href="history-finance.php">History</a></li> -->
        </ul>
        
       <ul class="nav navbar-nav navbar-right">
                        <li><a href="/log-notifikasi-aplikasi/index.html" target="_blank"><i class="fa fa-envelope"></i></a></li>
          
         <ul class="nav navbar-nav navbar-right">
                        <li><a href="/log-notifikasi-aplikasi/index.html" target="_blank"><i class="fa fa-envelope"></i></a></li>
            <li><a href="ubahpassword.php"><span class="glyphicon glyphicon-user"></span><?php echo $_SESSION['nama_user']; ?> (<?php echo $_SESSION['divisi']; ?>)</a></li>
            <li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> Logout</a></li>
          </ul>
      </div>
    </div>
  </nav>

  <!-- <div class="container"> -->

  <?php
  $code = $_GET['code'];
  $select = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE noid='$code'");
  $d = mysqli_fetch_assoc($select);
  ?>

  <center>
    <h2><?php echo $d['nama']; ?></h2>
  </center>

  <br /><br />

  <div class="row">
    <div class="col-sm-2">Nama Projek</div>
    <div class="col-sm-6">: <b><?php echo $d['nama']; ?></b></div>
  </div>

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
          <a href="#history" role="tab" id="history-tab" data-toggle="tab" aria-controls="history">File Upload</a>
        </li>

        <li role="presentation">
          <a href="#rincian" role="tab" id="rincian-tab" data-toggle="tab" aria-controls="rincian">Rincian BPU</a>
        </li>

        <li role="presentation">
          <a href="#semuabpu" role="tab" id="semuabpu-tab" data-toggle="tab" aria-controls="semuabpu">Semua BPU</a>
        </li>

      </ul>

      <div id="myTabContent" class="tab-content">
        <!-- Tab -->

        <div role="tabpanel" class="tab-pane fade in active" id="budget" aria-labelledby="home-tab">

          <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
            <div class="panel-body no-padding">
              <table class="table table-striped table-bordered table-hover tableFixHead">
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
                    <th>Total Dibayarkan</th>
                    <th>Sisa Pembayaran</th>
                    <th>Action</th>

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
                      $querySumTotalBayar = mysqli_query($koneksi, "SELECT SUM(jumlahbayar) as total_bayar FROM bpu where no = '$a[no]' AND waktu = '$waktu'");
                      $totalBayar = mysqli_fetch_assoc($querySumTotalBayar);
                  ?>
                      <tr>
                        <th scope="row"> <?php echo $i++; ?></th>
                        <td><?php echo $a['rincian']; ?></td>
                        <td><?php echo $a['kota']; ?></td>
                        <td><?php echo $a['status']; ?></td>
                        <td><?php echo $a['penerima']; ?></td>
                        <td><?php echo 'Rp. ' . number_format($a['harga'], 0, '', ','); ?></td>
                        <td><?php echo $a['quantity']; ?></td>
                        <td><?php echo 'Rp. ' . number_format($a['total'], 0, '', ','); ?></td>
                        <td>Rp. <?= number_format($totalBayar['total_bayar']) ?></td>

                        <!-- Sisa Pembayaran -->
                        <?php
                        $no = $a['no'];
                        $waktu = $a['waktu'];
                        $pilihtotal = mysqli_query($koneksi, "SELECT total FROM selesai WHERE no='$no' AND waktu='$waktu'");
                        $aw = mysqli_fetch_assoc($pilihtotal);
                        $hargaah = $aw['total'];
                        $query = "SELECT sum(jumlah) AS sum FROM bpu WHERE no='$no' AND waktu='$waktu'";
                        $result = mysqli_query($koneksi, $query);
                        $row = mysqli_fetch_array($result);
                        $total = $row[0];
                        $query16 = "SELECT sum(uangkembali) AS sum FROM bpu WHERE no='$no' AND waktu='$waktu'";
                        $result16 = mysqli_query($koneksi, $query16);
                        $row16 = mysqli_fetch_array($result16);
                        $total16 = $row16[0];

                        $jadinya = $hargaah - $total;
                        ?>
                        <td><?php echo 'Rp. ' . number_format($jadinya, 0, '', ','); ?></td>
                        <!-- //Sisa Pembayaran -->

                        <div class="modal fade" id="myModal5" role="dialog">
                          <div class="modal-dialog" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h3 class="modal-title text-center">Edit</h3>
                              </div>
                              <div class="modal-body">
                                <div id="gobloklah"></div>
                                <div class="fetched-data">
                                </div>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
                              </div>
                            </div>
                          </div>
                        </div>

                        <!-- Tombol Eksternal -->
                        <?php

                        $crbpu = mysqli_query($koneksi, "SELECT * FROM bpu WHERE no ='$no' AND waktu = '$waktu'");

                        if ($a['status'] == 'UM' || $a['status'] == 'UM Burek') {
                        ?>
                          <td>
                            <button type="button" class="btn btn-info btn-small" onclick="realisasi('<?php echo $no; ?>','<?php echo $waktu; ?>')">Realisasi</button>
                            <br /><br />
                            <button type="button" class="btn btn-default btn-small" onclick="edit_row('<?php echo $no; ?>','<?php echo $waktu; ?>')">Edit</button>
                            <br /><br />
                            <button type="button" class="btn btn-danger btn-small" onclick="hapus_row('<?php echo $no; ?>','<?php echo $waktu; ?>')">Hapus</button>
                            <br /><br />
                            <button type="button" class="btn btn-warning btn-small" onclick="detail_bpu('<?php echo $no; ?>','<?php echo $waktu; ?>')">Detail</button>
                            <!-- <br/><br/> -->
                            <!-- <button type="button" class="btn btn-success btn-small" onclick="move_budget('<?php echo $no; ?>','<?php echo $waktu; ?>')">Move</button> -->
                          </td>
                        <?php
                        } else if ($a['status'] == 'Biaya External' || $a['status'] == 'Biaya' || $a['status'] == 'Pulsa' || $a['status'] == 'Biaya Lumpsum') {
                        ?>
                          <td>
                            <button type="button" class="btn btn-default btn-small" onclick="edit_row('<?php echo $no; ?>','<?php echo $waktu; ?>')">Edit</button>
                            <br /><br />
                            <button type="button" class="btn btn-danger btn-small" onclick="hapus_row('<?php echo $no; ?>','<?php echo $waktu; ?>')">Hapus</button>
                            <br /><br />
                            <button type="button" class="btn btn-warning btn-small" onclick="detail_bpu('<?php echo $no; ?>','<?php echo $waktu; ?>')">Detail</button>
                            <!-- <br/><br/> -->
                            <!-- <button type="button" class="btn btn-success btn-small" onclick="move_budget('<?php echo $no; ?>','<?php echo $waktu; ?>')">Move</button> -->
                          </td>
                        <?php
                        } else {
                        ?>
                          <td>
                            <?php if($a['total'] > 1000000) { ?>
                            <button type="button" class="btn btn-success btn-small" onclick="eksternal('<?php echo $no; ?>','<?php echo $waktu; ?>')">Eksternal</button>
                            <br /><br />
                            <?php } ?>
                            <button type="button" class="btn btn-default btn-small" onclick="edit_row('<?php echo $no; ?>','<?php echo $waktu; ?>')">Edit</button>
                            <br /><br />
                            <button type="button" class="btn btn-danger btn-small" onclick="hapus_row('<?php echo $no; ?>','<?php echo $waktu; ?>')">Hapus</button>
                            <br /><br />
                            <button type="button" class="btn btn-warning btn-small" onclick="detail_bpu('<?php echo $no; ?>','<?php echo $waktu; ?>')">Detail</button>
                            <!-- <br/><br/> -->
                            <!-- <button type="button" class="btn btn-success btn-small" onclick="move_budget('<?php echo $no; ?>','<?php echo $waktu; ?>')">Move</button> -->
                          </td>
                          <?php
                        }

                        $arrCheck = [];
                        $liatbayar = mysqli_query($koneksi, "SELECT * FROM bpu WHERE waktu='$waktu' AND no='$no' ORDER BY term");
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

                              $noidbpu          = $bayar['noid'];
                              $jumlbayar        = $bayar['jumlah'];
                              $pengajuanJumlah = $bayar['pengajuan_jumlah'];
                              $tglbyr           = $bayar['tglcair'];
                              $statusbayar      = $bayar['status'];
                              $persetujuan      = $bayar['persetujuan'];
                              $bayarfinance     = $bayar['jumlahbayar'];
                              $novoucher        = $bayar['novoucher'];
                              $tanggalbayar     = $bayar['tanggalbayar'];
                              $pengaju          = $bayar['pengaju'];
                              $userMengetahui      = $bayar['acknowledged_by'];
                              $userCheck          = $bayar['checkby'];
                              $userApprove         = $bayar['approveby'];
                              $userPembayar         = $bayar['pembayar'];
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
                              $fileupload_realisasi     = $bayar['fileupload_realisasi'];
                              $pengajuan_realisasi = $bayar['pengajuan_realisasi'];
                              $pengajuan_uangkembali = $bayar['pengajuan_uangkembali'];
                              $pengajuan_tanggalrealisasi  = $bayar['pengajuan_tanggalrealisasi'];
                              $statusPengajuanRealisasi = $bayar['status_pengajuan_realisasi'];
                              $noStkb       = ($bayar['nomorstkb']) ? $bayar['nomorstkb'] : '-';
                              $kembreal         = $realisasi + $uangkembali;
                              $sisarealisasi    = $jumlbayar - $kembreal;
                              $statusPengajuanBpu = $bayar['status_pengajuan_bpu'];
                              $fileuploadRealisasi       = $bayar['fileupload_realisasi'];
                              $bankAccountName       = $bayar['bank_account_name'];

                              $tglcair = $tglcair == "0000-00-00" ? "-" : $tglcair;

                              $queryBank = mysqli_query($koneksi, "SELECT namabank FROM bank WHERE kodebank = '$namabank'");
                              $dataBank = mysqli_fetch_assoc($queryBank);
                              $bank = $dataBank['namabank'];

                              $queryTransfer = mysqli_query($koneksiBridge, "SELECT bank, jadwal_transfer FROM data_transfer WHERE noid_bpu = '$noidbpu'");
                              $dataTransfer = mysqli_fetch_assoc($queryTransfer);

                              $jadwalTransfer = $dataTransfer['jadwal_transfer'];

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

                              $isEksternalProcess = $statusbpu == 'Vendor/Supplier' || $statusbpu == 'Honor Eksternal' || $statusbpu == 'Honor Area Head' || $statusbpu == 'STKB OPS' || $statusbpu == 'STKB TRK Luar Kota' || $statusbpu == 'Honor Luar Kota' || $statusbpu == 'Honor Jakarta' || $statusbpu == 'STKB TRK Jakarta' ? true : false;

                              if ($statusPengajuanBpu == 1 && $isEksternalProcess) {
                                $color = 'orange';
                              }

                              // if ($statusPengajuanRealisasi == 1) {
                              //   $color = '#8aad70';
                              // } else if ($statusPengajuanRealisasi == 2) {
                              //   $color = '#ff3b3b';
                              // } else if ($statusPengajuanRealisasi == 3) {
                              //   $color = '#9932CC';
                              // }

                              echo "<td bgcolor=' $color '>";
                              // echo "<table>";
                              // echo "<tr><td>Tanggal Pembuatan</td><td> : <b>". date('Y-m-d', strtotime($waktustempel)) . "</b></td></tr>";
                              // echo "<tr><td>No. Term</td><td> : <b>$term</b></td></tr>";
                              // echo "<tr><td>No. STKB</td><td> : <b>$noStkb</b></td></tr>";
                              // echo "<tr><td>No Term</td><td> : <b>$term</b></td></tr>";
                              // echo "<tr><td>No Term</td><td> : <b>$term</b></td></tr>";
                              // echo "</table>";
                              echo "No. BPU :<b> $noidbpu";
                              echo "</b><br>";
                              echo "No Term:<b> $term";
                              echo "</b><br>";
                              echo "No. STKB :<b> $noStkb";
                              echo "</b><br>";
                              echo ($statusPengajuanBpu != 0) ? "Request BPU : <br><b>Rp. " . number_format($total['jumlah_pengajuan'], 0, '', ',') : "Nominal BPU : <br><b>Rp. " . number_format($total['jumlah_total'], 0, '', ',');
                              echo "</b><br>";
                              if ($realisasi != 0 && $statusbayar == 'Telah Di Bayar' && $statusbpu == 'UM') {
                                echo "Realisasi Biaya : <br><b>Rp. " . number_format($realisasi, 0, '', ',');
                                echo "</b><br>";
                                echo "Sisa Realisasi: <br><b>Rp. " . number_format($sisarealisasi, 0, '', ',');
                                echo "</b><br>";
                              } else if ($statusbayar == 'Realisasi (Direksi)') {
                                echo "Realisasi Biaya: <br><b>Rp. " . number_format($realisasi, 0, '', ',');
                                echo "</b><br>";
                              } else if ($statusbayar == 'Telah Di Bayar' && $statusbpu == 'Biaya') {
                                echo "";
                              } else {
                                echo "";
                              }
                              echo "Tanggal Buat BPU: <br><b> " . date('Y-m-d', strtotime($waktustempel));
                              echo "</b><br>";
                              echo "Jam : <b>" . date('H:i:s', strtotime($waktustempel));
                              echo "</b></br>";
                              echo "Request Pembayaran : <br><b>$tglcair ";
                              echo "</b></br>";
                              echo "Metode Pembayaran : <br><b>$metodePembayaran ";
                              echo "</b><br>";
                              echo "<hr />";
                              echo "Tanggal Pembayaran : <br><b> $tanggalbayar";
                              echo "</b><br/>";
                              echo "Nama Penerima : <br><b> $namapenerima";
                              echo "</b><br/>";
                              echo "Bank : <br><b> $bank";
                              echo "</b><br/>";
                              echo "Nomor Rekening : <br><b> $norek";
                              echo "</b><br/>";
                              echo "Nama Penerima Sesuai Rekening : <br><b> $bankAccountName";
                              echo "</b><br/>";
                              echo "Nominal Pembayaran : <br><b> Rp. " . number_format($jumlbayar);
                              echo "</b><br/>";
                              echo "No Voucher : <br><b> $novoucher ";
                              echo "</b><br/>";
                              echo "<hr />";
                              echo "Kasir : <br><b> $pembayar ";
                              echo "</b><br/>";
                              echo "File Rincian BPU : <br>";
                              echo "<a href='view-print-bpu.php?no=$no&waktu=$waktu&term=$term' target='_blank'><i class='fa fa-file'></i></a>";
                              echo "<br/><br/>";
                              if ($fileupload != NULL) {
                                echo "File Upload : <br>";
                                echo "<a href='uploads/$fileupload' target='_blank'><i class='fa fa-file'></i></a>";
                                echo "<br/><br/>";
                              } else {
                                echo "";
                              }
                              if ($fileupload_realisasi != NULL) {
                                echo "File Upload Realisasi: <br>";
                                echo "<a href='uploads/$fileuploadRealisasi' target='_blank'><i class='fa fa-file'></i></a>";
                                echo "<br/><br/>";
                              } else {
                                echo "";
                              }

                              if ($statusPengajuanBpu == 3 || $statusPengajuanBpu == 2) {
                                echo "<i class='far fa-check-square'></i> Diajukan Oleh $pengaju";
                                echo "</b><br/>";
                                echo "<i class='far fa-square'></i> Mengetahui ";
                                echo "</b><br/>";
                                echo "<i class='far fa-square'></i> Verifikasi ";
                                echo "</b><br/>";
                                echo "<i class='far fa-square'></i> Approval ";
                                echo "</b><br/>";
                                echo "<i class='far fa-square'></i> Paid ";
                                echo "</b><br/>";
                              } else if ($statusPengajuanBpu == 1) {
                                $statusCheckApproval = $persetujuan == "Disetujui (Direksi)" && $isEksternalProcess ? 'fa-check-square' : 'fa-square';
                                echo "<i class='far fa-check-square'></i> Diajukan Oleh $pengaju";
                                echo "</b><br/>";
                                echo "<i class='far fa-check-square'></i> Mengetahui (" . (!is_null($userMengetahui) ? $userMengetahui : '-') . ")";
                                echo "</b><br/>";
                                echo "<i class='far fa-square'></i> Verifikasi ";
                                echo "</b><br/>";
                                echo "<i class='far ".$statusCheckApproval  ."'></i> Approval ";
                                echo "</b><br/>";
                                echo "<i class='far fa-square'></i> Paid ";
                                echo "</b><br/>";

                                echo $persetujuan;
                              } else if ($statusPengajuanBpu == 0) {
                                echo "<i class='far fa-check-square'></i> Diajukan Oleh $pengaju";
                                echo "</b><br/>";
                                echo "<i class='far fa-check-square'></i> Mengetahui (" . (!is_null($userMengetahui) ? $userMengetahui : '-') . ")";
                                echo "</b><br/>";
                                echo "<i class='far fa-check-square'></i> Verifikasi (" . (!is_null($userCheck) ? $userCheck : '-') . ")";
                                echo "</b><br/>";
                                if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)' || $persetujuan == 'Disetujui oleh sistem')) {
                                  echo "<i class='far fa-check-square'></i> Approval (" . (!is_null($userApprove) ? $userApprove : '-') . ")";
                                  echo "</b><br/>";
                                } else {
                                  echo "<i class='far fa-square'></i> Approval ";
                                  echo "</b><br/>";
                                }
                                if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)' || $persetujuan == 'Disetujui oleh sistem') && ($statusbayar == 'Telah Di Bayar' || $statusbayar == 'Realisasi (Finance)' || $statusbayar == 'Realisasi (Direksi)')) {
                                  echo "<i class='far fa-check-square'></i> Paid (" . (!is_null($userPembayar) ? $userPembayar : '-') . ")";
                                  echo "</b><br/>";
                                } else {
                                  echo "<i class='far fa-square'></i> Paid ";
                                  echo "</b><br/>";
                                }
                              }
                              if (!($exin == 'Honor Eksternal' || $exin == 'Vendor/Supplier' || $exin == 'Lumpsum' || $exin == 'Honor SHP Jabodetabek' ||
                                $exin == 'Honor SHI/PWT Jabodetabek' || $exin == 'Honor SHP Luar Kota' || $exin == 'Honor SHI/PWT Luar Kota' ||
                                $exin == 'Honor Jakarta' || $exin == 'Honor Luar Kota' || $exin == 'STKB TRK Jakarta' || $exin == 'STKB TRK Luar Kota' || $exin == 'STKB OPS' || $exin == 'Honor Area Head')) {
                                if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)' || $persetujuan == 'Disetujui oleh sistem') && ($statusbayar == 'Realisasi (Finance)' || $statusbayar == 'Realisasi (Direksi)') && ($exin == 'UM' || $exin == 'UM Burek')) {
                                  echo "<i class='far fa-check-square'></i> Realisasi ";
                                  echo "</b><br/>";
                                } else {
                                  echo "<i class='far fa-square'></i> Realisasi ";
                                  echo "</b><br/>";
                                }
                              } else {
                                echo "<i class='far fa-check-square'></i> Realisasi ";
                                echo "</b><br/>";
                              }


                              if ($persetujuan == 'Pending' || $persetujuan == 'Belum Disetujui' && ($statusPengajuanBpu == 0 || !$statusPengajuanBpu)) {
                                echo "Komentar : <br><b> $alasan ";
                                echo "</b><br/>";
                          ?>

                                <?php if ($total['jumlah_total'] > $setting['plafon']) : ?>
                                  <button type="button" class="btn btn-success btn-small" onclick="setujuiBpu('<?php echo $no; ?>','<?php echo $waktu; ?>', '<?= $term ?>')">Setujui</button>
                                  </br>
                                <?php endif; ?>
                                <button type="button" class="btn btn-warning btn-small" style="margin-bottom: 3px;" onclick="editharga('<?php echo $noidbpu; ?>','<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Edit</button>
                                </br>
                                <button type="button" class="btn btn-danger btn-small" style="margin-bottom: 3px;" onclick="hapus_bpu('<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Hapus</button>
                              <?php
                              } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)' || $persetujuan == 'Disetujui oleh sistem') and $statusbayar == 'Belum Di Bayar') {
                              ?>
                                <button type="button" class="btn btn-warning btn-small" style="margin-bottom: 3px;" onclick="editharga('<?php echo $noidbpu; ?>','<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Edit</button>
                                </br>
                                <button type="button" class="btn btn-danger btn-small" style="margin-bottom: 3px;" onclick="hapus_bpu('<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Hapus</button>

                              <?php
                              } else if ($statusbayar == 'Realisasi (Finance)' || $statusbayar == 'Realisasi (Direksi)' || $uangkembali != 0) {
                                echo "Uang Kembali :<br><b> Rp. " . number_format($uangkembali, 0, '', ',');
                                echo "</b><br/>";
                              ?>
                                <button type="button" class="btn btn-warning btn-small" style="margin-bottom: 3px;" onclick="editharga('<?php echo $noidbpu; ?>','<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Edit</button>
                                </br>
                                <button type="button" class="btn btn-danger btn-small" style="margin-bottom: 3px;" onclick="hapus_bpu('<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Hapus</button>
                              <?php
                              } else {
                              ?>
                                <button type="button" class="btn btn-warning btn-small" style="margin-bottom: 3px;" onclick="editharga('<?php echo $noidbpu; ?>','<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Edit</button>
                                </br>
                                <button type="button" class="btn btn-danger btn-small" style="margin-bottom: 3px;" onclick="hapus_bpu('<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Hapus</button>
                              <?php
                              }
                              ?>
                              <?php if ($statusPengajuanRealisasi == 3) : ?>
                                <!-- <button type="button" class="btn btn-primary btn-small" onclick="proses_realisasi_bpu('<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>', '<?= $jumlbayar ?>', '<?= $pengajuan_realisasi ?>', '<?= $pengajuan_uangkembali ?>', '<?= $pengajuan_tanggalrealisasi ?>', '<?= $fileupload_realisasi ?>' , '<?= $sisarealisasi ?>')">Validasi Realisasi</button> -->
                        <?php endif;
                              echo "</td>";

                              array_push($arrCheck, $waktu . $no . $bayar['term']);
                            endif;
                          }
                        }
                        ?>
                      </tr>

                      <?php array_push($checkName, $a['rincian']); ?>
                    <?php endif; ?>
                  <?php } ?>
                </tbody>
              </table>
            </div><!-- /.table-responsive -->
          </div>

          <!-- <button type="button" class="btn btn-info btn-small" onclick="tambah_budget('<?php echo $waktu; ?>')">Tambah Item</button> -->

          <br /><br>

          <div class="row">
            <div class="col-xs-3">Total Budget Keseluruhan</div>
            <?php
              $queryTotalBudget = mysqli_query($koneksi, "SELECT sum(total) as total_budget FROM selesai WHERE waktu = '$d[waktu]'");
              $dataTotalBudget = mysqli_fetch_assoc($queryTotalBudget);
            ?>
            <div class="col-xs-3">: <b><?php echo 'Rp. ' . number_format($dataTotalBudget['total_budget'], 0, '', ','); ?></b></div>
          </div>

          <div class="row">
            <div class="col-xs-3">
              <font color="#1bd34f">Total Biaya dan Uang Muka 
                <hr />
            </div>

            <?php
            // echo $waktu . "\n";
            $query2 = "SELECT sum(jumlah) AS sum FROM bpu WHERE waktu='$waktu'";
            $result2 = mysqli_query($koneksi, $query2);
            $row2 = mysqli_fetch_array($result2);

            $q_real = "SELECT sum(realisasi) AS sum FROM bpu WHERE waktu='$waktu'";
            $r_real = mysqli_query($koneksi, $q_real);
            $g_real = mysqli_fetch_array($r_real);

            $query10 = "SELECT sum(uangkembali) AS sum FROM bpu WHERE waktu='$waktu'";
            $result10 = mysqli_query($koneksi, $query10);
            $row10 = mysqli_fetch_array($result10);

            $useduangkemb = mysqli_query($koneksi, "SELECT SUM(total) AS sumused FROM selesai WHERE waktu='$waktu' AND uangkembaliused='Y'");
            $uak = mysqli_fetch_array($useduangkemb);
            $uangkembaliused = $uak['sumused'];


            $totlah = $row2['sum']; // total bayar
            $reallah = $row10['sum'];
            $tysb = $totlah - $reallah;

            $totuangkembali = $reallah - $uangkembaliused;
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

          <!-- Note : -->
          <div class="row">
            <div class="col-xs-3">Total Uang Kembali Realisasi</div>
            <div class="col-xs-3">: <button type="button" class="btn" onclick="uang_kembali('<?php echo $waktu; ?>','<?php echo $no ?>')"><?php echo 'Rp. ' . number_format($totuangkembali, 0, '', ','); ?></button></div>
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

        </div>

        <div role="tabpanel" class="tab-pane fade" id="history" aria-labelledby="history-tab">
          <h3>File Upload</h3>
          <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
            <div class="panel-body no-padding">
              <table class="table table-striped table-bordered">
                <thead>
                  <tr class="warning">
                    <th>No</th>
                    <th>File</th>
                    <th>Tanggal Upload</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  
                  $i = 1;
                  $divisi = $_SESSION['divisi'];
                  $sql = mysqli_query($koneksi, "SELECT * FROM upload WHERE waktu='$waktu'");
                  while ($a = mysqli_fetch_array($sql)) {
                  ?>
                    <tr>
                      <th scope="row"><?php echo $i++; ?></th>
                      <td><a href="uploads/<?php echo $a['gambar']; ?>"><?php echo $a['gambar']; ?></a></td>
                      <td><?php echo $a['timestam']; ?></td>
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
                      <th scope="row"><?php echo $a['no']; ?></th>
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

      <!-- </div> -->

      <!-- Modal -->
      <div class="modal fade" id="prosesRealisasiModal" tabindex="-1" role="dialog" aria-labelledby="prosesRealisasiModalLabel">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h3 class="modal-title text-center" id="prosesRealisasiModalLabel">Verifikasi Realisasi</h3>
            </div>
            <div class="modal-body">

              <form action="setuju-realisasi-proses-direksi.php" method="post" id="theForm" name="Form" onsubmit="return validateForm()" enctype="multipart/form-data">
                <input type="hidden" id="noRealisasi" name="no">
                <input type="hidden" id="waktuRealisasi" name="waktu">
                <input type="hidden" id="termRealisasi" name="term">
                <input type="hidden" id="sisaRealisasi" name="sisa">
                <input type="hidden" name="kode" value="<?= $code ?>">
                <div class="form-group">
                  <label for="rincian" class="control-label">Total BPU:</label>
                  <input type="text" class="form-control" id="hasilText" readonly>
                  <input type="hidden" class="form-control" id="hasilProsesRealisasi" name="totalbpu">
                </div>

                <div class="form-group">
                  <label for="rincian" class="control-label">Total Realisasi:</label>
                  <input type="number" class="form-control" name="realisasi" id="id_step2-number_2ProsesRealisasi">
                  <a type="button" id="id_step2-number_2_btn" class="pull-right" style="text-decoration: none; cursor: pointer; margin-top: 2px;">Edit <i class="far fa-edit"></i></a>
                </div>

                <div class="form-group">
                  <label for="rincian" class="control-label">Uang Kembali (IDR) :</label>
                  <input type="text" class="form-control" name="uangkembali" id="id_step3-number_3ProsesRealisasi">
                  <a type="button" id="id_step3-number_3_btn" class="pull-right" style="text-decoration: none; cursor: pointer; margin-top: 2px;">Edit <i class="far fa-edit"></i></a>
                </div>

                <div class="form-group">
                  <label for="rincian" class="control-label">Tanggal Realisasi :</label>
                  <input type="date" class="form-control" name="tanggalrealisasi" id="id_step3-number_4">
                  <a type="button" id="id_step3-number_4_btn" class="pull-right" style="text-decoration: none; cursor: pointer; margin-top: 2px;">Edit <i class="far fa-edit"></i></a>
                </div>

                <div class="form-group">
                  <label for="alasanTolakRealisasi" class="control-label">Alasan Penolakan (Jika ditolak):</label>
                  <input type="text" class="form-control" name="alasanTolakRealisasi" id="alasanTolakRealisasi">
                </div>

                <div class="form-group">
                  <p class="control-p"><b>Uploaded File</b></p>
                  <img id="image" class="img-responsive" style="display: block; margin-left: auto;  margin-right: auto; background-repeat: no-repeat; background-size: cover; background-attachment: fixed;  " src="" alt="">
                </div>

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

      <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h3 class="modal-title text-center">Verifikasi dan Persetujuan BPU</h3>
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
              <h3 class="modal-title text-center">Edit BPU</h3>
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

      <div class="modal fade" id="myModal4" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h3 class="modal-title text-center">Realisasi</h3>
            </div>
            <div class="modal-body">
              <form action="realisasidireksiproses2.php" method="post" id="theForm" name="Form" onsubmit="return validateForm()">
                <div id="isi_form"></div>

                <div class="fetched-data">
                  <div class="form-group">
                    <label for="rincian" class="control-label">Total BPU:</label>
                    <input type="text" class="form-control" id="hasil" name="totalbpu" readonly>
                  </div>

                  <div class="form-group">
                    <label for="rincian" class="control-label">Total Realisasi:</label>
                    <input type="text" class="form-control" name="totalrealisasi" id="id_step2-number_2">
                  </div>

                  <div class="form-group">
                    <label for="rincian" class="control-label">Uang Kembali (IDR) :</label>
                    <input type="text" class="form-control" name="uangkembali" id="id_step3-number_3">
                  </div>

                  <button class="btn btn-primary" type="submit" name="submit">OK</button>
                </div>
              </form>
              <script>
                // $(document).ready(function() {
                //   $("#id_step2-number_2").keyup(function(event) {
                //     // skip for arrow keys
                //     if (event.which >= 37 && event.which <= 40) {
                //       event.preventDefault();
                //     }
                //     var $this = $(this);
                //     var num = $this.val().replace(/,/gi, "").split("").reverse().join("");

                //     var num2 = RemoveRougeChar(num.replace(/(.{3})/g, "$1,").split("").reverse().join(""));

                //     console.log(num2);


                //     // the following line has been simplified. Revision history contains original.
                //     $this.val(num2);
                //   });
                // });

                // function RemoveRougeChar(convertString) {


                //   if (convertString.substring(0, 1) == ",") {

                //     return convertString.substring(1, convertString.length)

                //   }
                //   return convertString;

                // }

                // $(document).ready(function() {
                //   $("#id_step3-number_3").keyup(function(event) {
                //     // skip for arrow keys
                //     if (event.which >= 37 && event.which <= 40) {
                //       event.preventDefault();
                //     }
                //     var $this = $(this);
                //     var num = $this.val().replace(/,/gi, "").split("").reverse().join("");

                //     var num2 = RemoveRougeChar(num.replace(/(.{3})/g, "$1,").split("").reverse().join(""));

                //     console.log(num2);


                //     // the following line has been simplified. Revision history contains original.
                //     $this.val(num2);
                //   });
                // });

                // function RemoveRougeChar(convertString) {


                //   if (convertString.substring(0, 1) == ",") {

                //     return convertString.substring(1, convertString.length)

                //   }
                //   return convertString;

                // }
              </script>

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
              <h3 class="modal-title text-center">Hapus</h3>
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

      <div class="modal fade" id="myModal7" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h3 class="modal-title text-center">Hapus BPU</h3>
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

      <div class="modal fade" id="myModal8" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h3 class="modal-title text-center">Detail BPU</h3>
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

      <div class="modal fade" id="myModal9" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h3 class="modal-title text-center">Tambah Budget (Uang Kembali)</h3>
            </div>
            <div class="modal-body">
              <form action="tambahuangkembaliproses.php" method="post">
                <div id="isi_form2"></div>
                <div class="fetched-data">
                  <div class="form-group">
                    <label for="harga" class="control-label">Harga (IDR) :</label>
                    <input type="text" class="form-control" id="harga" value="" name="harga" onkeyup="sum();" max="3000000">
                  </div>

                  <div class="form-group">
                    <label for="quantity" class="control-label">Quantity :</label>
                    <input type="text" class="form-control" id="quantity" value="" name="quantity" onkeyup="sum();">
                  </div>

                  <div class="form-group">
                    <label for="total">Total Harga (IDR) :</label>
                    <input type="number" class="form-control" id="total" name="total" onkeyup="sum();" value="" readonly>
                  </div>

                  <button class="btn btn-primary" type="submit" name="submit">Update</button>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" id="myModal10" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h3 class="modal-title text-center">Tambah Item Budget</h3>
            </div>
            <div class="modal-body">

              <?php
              $cariselisih = mysqli_query($koneksi, "SELECT totalbudget,totalbudgetnow FROM pengajuan WHERE waktu='$waktu'");
              $cs = mysqli_fetch_array($cariselisih);
              $totalbudget      = $cs['totalbudget'];
              $totalbudgetnow   = $cs['totalbudgetnow'];

              if ($totalbudget < $totalbudgetnow) {
              ?>
                <form action="tambahitemdireksiproses.php" method="post">
                  <div id="isi_form3"></div>
                  <div class="fetched-data">
                    <div class="form-group">
                      <label for="harga" class="control-label">Harga (IDR) :</label>
                      <input type="text" class="form-control" id="harga2" value="" name="harga" onkeyup="sum();" max="3000000">
                    </div>

                    <div class="form-group">
                      <label for="quantity" class="control-label">Quantity :</label>
                      <input type="text" class="form-control" id="quantity2" value="" name="quantity" onkeyup="sum();">
                    </div>

                    <div class="form-group">
                      <label for="total">Total Harga (IDR) :</label>
                      <input type="number" class="form-control" id="total2" name="total" onkeyup="sum();" value="" readonly>
                    </div>

                    <button class="btn btn-primary" type="submit" name="submit">Update</button>
                  </div>
                </form>
              <?php
              } else {
                echo "Total budget yang disetujui adalah ";
                echo "<b>";
                echo 'Rp. ' . number_format($totalbudget, 0, '', ',');
                echo "</b>";
                echo "<br/>";
                echo "Kurangi total budget terlebih dahulu untuk membuat item baru";
              }
              ?>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" id="movebudget" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h3 class="modal-title text-center">Move Item Budget</h3>
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

      <script type="text/javascript">

        $("table").on("scroll", function () {
          console.log($("table").width())
            $("table > *").width($("table").width() + $("table").scrollLeft());
        });

        $('#id_step2-number_2_btn').click(function() {
          // console.log($(this));
          $('#id_step2-number_2ProsesRealisasi').prop('readonly', false);
        })
        $('#id_step3-number_3_btn').click(function() {
          $('#id_step3-number_3number_3ProsesRealisasi').prop('readonly', false);

        })
        $('#id_step3-number_4_btn').click(function() {
          $('#id_step3-number_4').prop('readonly', false);
        })

        function proses_realisasi_bpu(no, waktu, term, jumlah, tRealisasi, uangKembali, tanggal, file, sisa) {
          $.ajax({
            url: "ajax/ajax-views-direksi.php",
            type: 'post',
            data: {
              'no': no,
              'waktu': waktu,
              'term': term
            },
            success: function(r) {
              r = JSON.parse(r);
              $('#noRealisasi').val(no);
              $('#waktuRealisasi').val(waktu);
              $('#termRealisasi').val(term);
              $('#hasilProsesRealisasi').val(jumlah);
              $('#hasilText').val(numberWithCommas(jumlah));
              $('#id_step2-number_2ProsesRealisasi').val(tRealisasi);
              $('#id_step3-number_3ProsesRealisasi').val(uangKembali);
              $('#id_step3-number_4').val(tanggal);
              $('#sisaRealisasi').val(sisa);
              $('#image').attr('src', `uploads/${file}`)

              $('#id_step2-number_2ProsesRealisasi').prop('max', sisa);

              $('#id_step2-number_2ProsesRealisasi').prop('readonly', true);
              $('#id_step3-number_3ProsesRealisasi').prop('readonly', true);
              $('#id_step3-number_4').prop('readonly', true);
              $('#prosesRealisasiModal').modal('toggle');
            }
          })
        }

        function numberWithCommas(x) {
          return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        function edit_budget(no, waktu, term) {

          // alert(noid+' - '+waktu);
          $.ajax({
            type: 'post',
            url: 'setuju.php',
            data: {
              no: no,
              waktu: waktu,
              term: term
            },
            success: function(data) {
              $('.fetched-data').html(data); //menampilkan data ke dalam modal
              $('#myModal').modal();
            }
          });
        }

        function editharga(noidbpu, no, waktu, term) {
          // alert(noid+' - '+waktu);
          $.ajax({
            type: 'post',
            url: 'editdireksi.php',
            data: {
              noidbpu: noidbpu,
              no: no,
              waktu: waktu,
              term: term
            },
            success: function(data) {
              $('.fetched-data').html(data); //menampilkan data ke dalam modal
              $('#myModal2').modal();
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
              $('#myModal3 .fetched-data').html(data); //menampilkan data ke dalam modal
              $('#myModal3').modal();
            }
          });
        }

        function realisasi(no, waktu) {
          // alert(noid+' - '+waktu);
          $.ajax({
            type: 'post',
            url: 'realisasidireksi.php',
            data: {
              no: no,
              waktu: waktu
            },
            success: function(data) {
              $("#isi_form").html(data);
              // $('.fetched-data').html(data);//menampilkan data ke dalam modal
              $('#myModal4').modal();
            }
          });
        }

        function edit_row(no, waktu) {
          // alert(noeditid+' - '+waktu);
          $.ajax({
            type: 'post',
            url: 'editrow.php',
            data: {
              no: no,
              waktu: waktu
            },
            success: function(data) {
              $("#gobloklah").html(data);
              //$('.fetched-data').html(data);//menampilkan data ke dalam modal
              $('#myModal5').modal();
            }
          });
        }

        function hapus_row(no, waktu) {
          // alert(noid+' - '+waktu);
          $.ajax({
            type: 'post',
            url: 'hapusrow.php',
            data: {
              no: no,
              waktu: waktu
            },
            success: function(data) {
              $('.fetched-data').html(data); //menampilkan data ke dalam modal
              $('#myModal6').modal();
            }
          });
        }

        function hapus_bpu(no, waktu, term) {
          // alert(noid+' - '+waktu);
          $.ajax({
            type: 'post',
            url: 'hapusbpu.php',
            data: {
              no: no,
              waktu: waktu,
              term: term
            },
            success: function(data) {
              $('.fetched-data').html(data); //menampilkan data ke dalam modal
              $('#myModal7').modal();
            }
          });
        }

        function detail_bpu(no, waktu) {
          // alert(noid+' - '+waktu);
          $.ajax({
            type: 'post',
            url: 'detailbpu.php',
            data: {
              no: no,
              waktu: waktu
            },
            success: function(data) {
              $('.fetched-data').html(data); //menampilkan data ke dalam modal
              $('#myModal8').modal();
            }
          });
        }

        function uang_kembali(waktu, no) {
          // alert(noid+' - '+waktu);
          $.ajax({
            type: 'post',
            url: 'tambahuangkembali.php',
            data: {
              waktu: waktu,
              no: no
            },
            success: function(data) {
              $("#isi_form2").html(data);
              //$('.fetched-data').html(data);//menampilkan data ke dalam modal
              $('#myModal9').modal();
            }
          });
        }

        function tambah_budget(waktu) {
          // alert(noid+' - '+waktu);
          $.ajax({
            type: 'post',
            url: 'tambahitemdireksi.php',
            data: {
              waktu: waktu
            },
            success: function(data) {
              $("#isi_form3").html(data);
              //$('.fetched-data').html(data);//menampilkan data ke dalam modal
              $('#myModal10').modal();
            }
          });
        }

        function move_budget(no, waktu) {
          // alert(noid+' - '+waktu);
          $.ajax({
            type: 'post',
            url: 'movebudget.php',
            data: {
              no: no,
              waktu: waktu
            },
            success: function(data) {
              $('.fetched-data').html(data); //menampilkan data ke dalam modal
              $('#movebudget').modal();
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
      </script>




</body>

</html>