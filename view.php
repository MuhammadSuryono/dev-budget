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
        <a class="navbar-brand" href="home.php">Budget-Ing</a>
      </div>
      <div class="collapse navbar-collapse" id="myNavbar">
        <ul class="nav navbar-nav">
          <li><a href="home.php">Home</a></li>
          <li class="active"><a href="list.php">List</a></li>
          <!-- <li><a href="request-budget.php">Request Budget</a></li> -->
        </ul>
        <ul class="nav navbar-nav navbar-right">
          <li><a href="ubahpassword.php"><span class="glyphicon glyphicon-user"></span><?php echo $_SESSION['nama_user']; ?> (<?php echo $_SESSION['divisi']; ?>)</a></li>
          <li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container">

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

    <br /><br />

    <a href="view-print.php?id=<?= $code ?>" target="_blank" class="btn btn-warning pull-right">Print <i class="fas fa-print"></i></a>
    <div class="but_list">
      <div class="bs-example bs-example-tabs" role="tabpanel" data-example-id="togglable-tabs">

        <ul id="myTab" class="nav nav-tabs" role="tablist">

          <li role="presentation" class="active">
            <a href="#budget" id="budget-tab" role="tab" data-toggle="tab" aria-controls="budget" aria-expanded="true">Budget</a>
          </li>

          <li role="presentation">
            <a href="#history" role="tab" id="history-tab" data-toggle="tab" aria-controls="history">File Upload</a>
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
                      <th>Nama</th>
                      <th>Kota</th>
                      <th>Status</th>
                      <th>Penerima Uang</th>
                      <th>Harga (IDR)</th>
                      <th>Total Quantity</th>
                      <th>Total Harga (IDR)</th>
                      <th>Sisa Pembayaran</th>
                      <!-- <th>Edit</th> -->
                      <?php
                      $waktu = $d['waktu'];
                      $selno = mysqli_query($koneksi, "SELECT no FROM selesai WHERE waktu ='$waktu'");
                      $wkwk = mysqli_fetch_assoc($selno);
                      $no = $wkwk['no'];
                      $liatbayarth = mysqli_query($koneksi, "SELECT * FROM pembayaran WHERE waktu='$waktu' AND no='$no'");
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
                    ?>

                        <tr>
                          <th scope="row"><?php echo $i++; ?></th>
                          <td><?php echo $a['rincian']; ?></td>
                          <td><?php echo $a['kota']; ?></td>
                          <td><?php echo $a['status']; ?></td>
                          <td><?php echo $a['penerima']; ?></td>
                          <td><?php echo 'Rp. ' . number_format($a['harga'], 0, '', ','); ?></td>
                          <td>
                            <center><?php echo $a['quantity']; ?></center>
                          </td>
                          <td><?php echo 'Rp. ' . number_format($a['total'], 0, '', ','); ?></td>

                          <!-- Sisa Pembayaran -->
                          <?php
                          $no = $a['no'];
                          $waktu = $a['waktu'];
                          $pilihtotal = mysqli_query($koneksi, "SELECT total FROM selesai WHERE no='$no' AND waktu='$waktu'");
                          $aw = mysqli_fetch_assoc($pilihtotal);
                          $hargaah = $aw['total'];
                          $query = "SELECT sum(jumlahbayar) AS sum FROM pembayaran WHERE no='$no' AND waktu='$waktu'";
                          $result = mysqli_query($koneksi, $query);
                          $row = mysqli_fetch_array($result);
                          $total = $row[0];
                          $jadinya = $hargaah - $total
                          ?>
                          <td><?php echo 'Rp. ' . number_format($jadinya, 0, '', ','); ?></td>
                          <!-- //Sisa Pembayaran -->

                          <!-- <td><button type="button" class="btn btn-default btn-small" onclick="edit_budget('<?php echo $no; ?>','<?php echo $waktu; ?>')">Edit</button></td> -->

                          <?php
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

                                $jumlbayar           = $bayar['jumlah'];
                                $pengajuanJumlah = $bayar['pengajuan_jumlah'];
                                $tglbyr              = $bayar['tglcair'];
                                // $statusbayar         = $bayar['status'];
                                $persetujuan         = $bayar['persetujuan'];
                                $novoucher           = $bayar['novoucher'];
                                $tanggalbayar        = $bayar['tanggalbayar'];
                                $nobay               = $bayar['no'];
                                $termm               = $bayar['term'];
                                $wakbay              = $bayar['waktu'];
                                $alasan              = $bayar['alasan'];
                                $namapenerima        = $bayar['namapenerima'];
                                $namabank            = $bayar['namabank'];
                                $norek               = $bayar['norek'];
                                $termreal            = $bayar['term'];
                                $realisasi           = $bayar['realisasi'];
                                $uangkembali         = $bayar['uangkembali'];
                                $tanggalrealisasi    = $bayar['tanggalrealisasi'];
                                $tglcair             = $bayar['tglcair'];
                                $jumlahjadi          = $jumlbayar - $uangkembali;
                                $waktustempel        = $bayar['waktustempel'];
                                $pengaju             = $bayar['pengaju'];
                                $userMengetahui      = $bayar['acknowledged_by'];
                                $userCheck          = $bayar['checkby'];
                                $userApprove         = $bayar['approveby'];
                                $userPembayar         = $bayar['pembayar'];
                                $divisi2             = $bayar['divisi'];
                                $pembayar            = $bayar['pembayar'];
                                $term                = $bayar['term'];
                                $statusbpu        = $bayar['statusbpu'];
                                $fileupload       = $bayar['fileupload'];
                                $noStkb       = ($bayar['nomorstkb']) ? $bayar['nomorstkb'] : '-';
                                $kembreal         = $realisasi + $uangkembali;
                                $sisarealisasi    = $jumlbayar - $kembreal;
                                $statusPengajuanBpu = $bayar['status_pengajuan_bpu'];
                                $statusPengajuanRealisasi = $bayar['status_pengajuan_realisasi'];

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
                                  $color = '#ff8787';
                                } else if ($statusPengajuanBpu == 3) {
                                  $color = '#DEB887';
                                }

                                // if ($statusPengajuanRealisasi == 1) {
                                //   $color = '#8aad70';
                                // } else if ($statusPengajuanRealisasi == 2) {
                                //   $color = '#ff8787';
                                // } else if ($statusPengajuanRealisasi == 3) {
                                //   $color = '#9932CC';
                                // }

                                echo "<td bgcolor=' $color '>";
                                echo "No :<b> $term";
                                echo "</b><br>";
                                echo "No. STKB :<b> $noStkb";
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
                                echo "Tanggal : <br><b> " . date('Y-m-d', strtotime($waktustempel));
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
                                echo "<a href='view-print-bpu.php?no=$no&waktu=$waktu&term=$term' target='_blank'><i class='fa fa-file'></i></a>";
                                echo "<br/><br/>";
                                if ($statusbpu == 'Biaya' || $statusbpu == 'Biaya Lumpsum') {
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
                                }
                                // if ($statusbpu == 'Biaya' && $fileupload != NULL || $statusbpu == 'Biaya Lumpsum' && $fileupload != NULL) {
                                //   echo "File Upload : <br>";
                                //   // echo "<a href='fileupload/$fileupload'><i class='fa fa-file'></i></a>";
                                //   echo "<a target='_blank' href='view-print.php?id=$kode'><i class='fa fa-file'></i></a>";
                                //   echo "<br/><br/>";\
                                // } else {
                                //   echo "";
                                // }

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
                                  echo "<i class='far fa-check-square'></i> Diajukan Oleh $pengaju";
                                  echo "</b><br/>";
                                  echo "<i class='far fa-check-square'></i> Mengetahui (" . (!is_null($userMengetahui) ? $userMengetahui : '-') . ")";
                                  echo "</b><br/>";
                                  echo "<i class='far fa-square'></i> Verifikasi ";
                                  echo "</b><br/>";
                                  echo "<i class='far fa-square'></i> Approval ";
                                  echo "</b><br/>";
                                  echo "<i class='far fa-square'></i> Paid ";
                                  echo "</b><br/>";
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
                                // if ($statusPengajuanRealisasi != 4 && !($exin == 'Honor Eksternal' || $exin == 'Vendor/Supplier' || $exin == 'Lumpsum' || $exin == 'Honor SHP Jabodetabek' ||
                                //   $exin == 'Honor SHI/PWT Jabodetabek' || $exin == 'Honor SHP Luar Kota' || $exin == 'Honor SHI/PWT Luar Kota' ||
                                //   $exin == 'Honor Jakarta' || $exin == 'Honor Luar Kota' || $exin == 'STKB TRK Jakarta' || $exin == 'STKB TRK Luar Kota' || $exin == 'STKB OPS')) {
                                //   echo "<i class='far fa-square'></i> Realisasi ";
                                //   echo "</b><br/>";
                                // } else {
                                //   echo "<i class='far fa-check-square'></i> Realisasi ";
                                //   echo "</b><br/>";
                                // }
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
                                  // echo "<i class='far fa-square'></i> Realisasi ";
                                  // echo "</b><br/>";
                                } else {
                                  echo "<i class='far fa-check-square'></i> Realisasi ";
                                  echo "</b><br/>";
                                }

                                if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)' || $persetujuan == 'Disetujui oleh sistem') && ($statusbayar == 'Telah Di Bayar' || $statusbayar == 'Realisasi (Finance)' || $statusbayar == 'Realisasi (Direksi)')) {
                                  echo "Uang Kembali :<br><b> Rp. " . number_format($uangkembali, 0, '', ',');
                                  echo "</b><br/>";
                                }

                                if ($persetujuan == 'Pending' || $persetujuan == 'Belum Disetujui') {
                                  echo "Komentar : <br><b> $alasan ";
                                  echo "</b><br/>";
                                } else {
                                  echo "";
                                }

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

            <br /><br />

            <div class="row">
              <div class="col-xs-3">Total Budget Keseluruhan</div>
              <div class="col-xs-3">: <b><?php echo 'Rp. ' . number_format($d['totalbudget'], 0, '', ','); ?></b></div>
            </div>

            <div class="row">
              <div class="col-xs-3">Total Yang Sudah Di bayarkan</div>

              <?php

              $query2 = "SELECT sum(jumlahbayar) AS sum FROM pembayaran WHERE waktu='$waktu'";
              $result2 = mysqli_query($koneksi, $query2);
              $row2 = mysqli_fetch_array($result2);
              ?>

              <div class="col-xs-3">: <b><?php echo 'Rp. ' . number_format($row2['sum'], 0, '', ','); ?></b></div>
            </div>


            <!-- Yang belum Bayar -->
            <div class="row">
              <div class="col-xs-3">Total Yang Belum Di bayarkan</div>
              <?php
              $aaaa = $d['totalbudget'];
              $bbbb = $row2['sum'];
              $belumbayar = $aaaa - $bbbb;

              ?>
              <div class="col-xs-3">: <b><?php echo 'Rp. ' . number_format($belumbayar, 0, '', ','); ?></b></div>
            </div>
            <!-- // Yang belum bayar -->

            <br /><br />

            <!-- <?php
                  $hakAkses = $_SESSION['hak_akses'];
                  if ($hakAkses != 'Level 1' && $hakAkses != 'Level 2') {
                    $cekfile = mysqli_query($koneksi, "SELECT * FROM upload WHERE waktu='$waktu' AND disapprove='Validasi'");
                    if (mysqli_num_rows($cekfile) > 0) {
                  ?>
                <button type="button" class="btn btn-default btn-small" onclick="tambah_budget('<?php echo $waktu; ?>','<?php echo $code; ?>')">Tambah</button>
            <?php
                    } else {
                      echo "<button type='button' class='btn btn-default btn-small' data-toggle='modal' data-target='#myModal4'>Tambah</button>";
                    }
                  } ?> -->

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

            <!-- <button type="button" class="btn btn-danger btn-small" onclick="upload('<?php echo $waktu; ?>')">Upload</button> -->

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
                        <td><a href="uploads/<?php echo $a['gambar']; ?>" download><?php echo $a['gambar']; ?></a></td>
                        <td><?php echo $a['timestam']; ?></td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div><!-- /.table-responsive -->
            </div>
          </div>


        </div><!-- //Tab -->

      </div><!-- //Container -->

      <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h3 class="modal-title text-center">Edit Budget</h3>
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
              <h3 class="modal-title text-center">UPLOAD File</h3>
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
              <h3 class="modal-title text-center">Tambah Budget</h3>
            </div>
            <div class="modal-body">
              <form action="tambahpengajuproses.php" method="post">
                <div id="isi_form"></div>
                <div class="fetched-data">
                  <div class="form-group">
                    <label for="harga" class="control-label">Harga (IDR) :</label>
                    <input type="text" class="form-control" id="harga" value="" name="harga" onkeyup="sum();">
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

      <!-- Modal -->
      <div class="modal fade" id="myModal4" role="dialog">
        <div class="modal-dialog">

          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h3 class="modal-title text-center">Edit dan Tambah Budget</h3>
            </div>
            <div class="modal-body">
              <p>Untuk <b>penambahan atau edit Budget</b>, harap upload File terkait terlebih dahulu.</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
          </div>

        </div>
      </div>

      <script type="text/javascript">
        // $(document).ready(function(){
        //     $('#myModal').on('show.bs.modal', function (e) {
        //         var rowid = $(e.relatedTarget).data('id');
        //         //menggunakan fungsi ajax untuk pengambilan data
        //         $.ajax({
        //             type : 'post',
        //             url : 'editbudget.php',
        //             data :  'rowid='+ rowid,
        //             success : function(data){
        //             $('.fetched-data').html(data);//menampilkan data ke dalam modal
        //             }
        //         });
        //      });
        //
        //      $('.edit_budget').on('click', function (e) {
        //          var noid = '<?php //echo $noid; 
                                ?>';
        //          var waktu = '<?php //echo $waktu; 
                                  ?>';
        //          //menggunakan fungsi ajax untuk pengambilan data
        //          $.ajax({
        //              type : 'post',
        //              url : 'editbudget.php',
        //              data :  {noid:noid, waktu:waktu},
        //              success : function(data){
        //                $('.fetched-data').html(data);//menampilkan data ke dalam modal
        //                $('#myModal').modal();
        //              }
        //          });
        //       });
        //
        // });

        function edit_budget(no, waktu) {
          // alert(noid+' - '+waktu);
          $.ajax({
            type: 'post',
            url: 'editpengaju.php',
            data: {
              no: no,
              waktu: waktu
            },
            success: function(data) {
              $('.fetched-data').html(data); //menampilkan data ke dalam modal
              $('#myModal').modal();
            }
          });
        }

        function tambah_budget(waktu, noid) {
          // alert(noid+' - '+waktu);
          $.ajax({
            type: 'post',
            url: 'tambahpengaju.php',
            data: {
              waktu: waktu,
              noid: noid
            },
            success: function(data) {
              $("#isi_form").html(data);
              //$('.fetched-data').html(data);//menampilkan data ke dalam modal
              $('#myModal2').modal();
            }
          });
        }

        function upload(waktu) {
          // alert(noid+' - '+waktu);
          $.ajax({
            type: 'post',
            url: 'upload.php',
            data: {
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