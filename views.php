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
          <?php
          if ($_SESSION['divisi'] == 'FINANCE') {
            echo "<li class='active'><a href='list-finance.php'>List</a></li>";
            echo "<li><a href='scanner.php'>Scan QR</a></li>";
          } else {
            echo "<li class='active'><a href='list.php'>List</a></li>";
            echo "<li><a href='scanner.php'>Scan QR</a></li>";
          }
          ?>
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

    <!-- <div class="row">
      <div class="col-sm-2"></div>
      <div class="col-sm-6"></div>
      <div class="col-sm-4"><b>U&nbsp;&nbsp; = <img src="images/purple.jpg" width="20px" height="15px"> Pengajuan Realisasi</b></div>
    </div> -->

    <br /><br />

    <div class="but_list">
      <div class="bs-example bs-example-tabs" role="tabpanel" data-example-id="togglable-tabs">

        <ul id="myTab" class="nav nav-tabs" role="tablist">

          <li role="presentation" class="active">
            <a href="#budget" id="budget-tab" role="tab" data-toggle="tab" aria-controls="budget" aria-expanded="true">Budget</a>
          </li>

          <li role="presentation">
            <a href="#history" role="tab" id="history-tab" data-toggle="tab" aria-controls="history">History</a>
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
                      <th>No </th>
                      <th>Nama</th>
                      <th>Kota</th>
                      <th>Status</th>
                      <th>Penerima Uang</th>
                      <th>Harga (IDR)</th>
                      <th>Total Quantity</th>
                      <th>Total Harga (IDR)</th>
                      <th>Sisa Pembayaran</th>

                      <?php
                      $hakAkses = $_SESSION['hak_akses'];
                      if ($hakAkses != "Level 1") {
                      ?>
                        <th>Pengajuan Pencairan</th>
                      <?php } ?>
                      <th>Action</th>
                      <?php
                      $waktu = $d['waktu'];
                      $code = $_GET["code"];
                      $selno = mysqli_query($koneksi, "SELECT no FROM selesai WHERE waktu ='$waktu'");
                      $wkwk = mysqli_fetch_assoc($selno);
                      $no = $wkwk['no'];
                      $liatbayarth = mysqli_query($koneksi, "SELECT * FROM bpu WHERE waktu='$waktu' AND no='$no'");
                      if (mysqli_num_rows($liatbayarth) == 0) {
                        echo "";
                      } else {
                        $n = 1;
                        while ($bayar = mysqli_fetch_array($liatbayarth)) {
                          echo "<th style='width:200px'>Term Pembayaran " . $n++ . "</th>";
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
                    $totalBiaya = 0;
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
                          $pilihtotal = mysqli_query($koneksi, "SELECT total, status FROM selesai WHERE no='$no' AND waktu='$waktu'");
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

                          if ($aw['status'] != 'UM Burek') {
                            $totalBiaya += $jadinya;
                          }
                          ?>
                          <td><?php echo 'Rp. ' . number_format($jadinya, 0, '', ','); ?></td>
                          <!-- //Sisa Pembayaran -->

                          <?php
                          if ($hakAkses != "Level 1") {
                          ?>
                            <td>
                              <?php
                              if ($a['status'] == 'UM' || $a['status'] == 'Pulsa' || $a['status'] == 'Biaya External' || $a['status'] == 'Biaya' || $a['status'] == 'Biaya Lumpsum' || $a['status'] == 'UM Burek') {

                                $tespengajuan = mysqli_query($koneksi, "SELECT qrcode FROM pengajuan WHERE waktu='$waktu'");
                                $tsp = mysqli_fetch_array($tespengajuan);

                                // if ($tsp['qrcode'] == NULL){
                                //   echo "<a href='#' data-toggle='tooltip' title='Harap scan QR Code terlebih dahulu sebelum membuat bpu'>QR Required</a>";
                                // }else{
                              ?>
                                <button type="button" class="btn btn-default btn-small" onclick="edit_budget('<?php echo $no; ?>','<?php echo $waktu; ?>', '<?php echo $code ?>')">BPU</button>
                              <?php
                              } else {
                                echo "<a href='#' data-toggle='tooltip' title='Untuk pembayaran External, Harap lampirkan dokumen terkait seperti Invoice, Berita Acara DSB. Untuk di ajukan ke Ibu Ina Puspito untuk mendapatkan persetujuan'>External</a>";
                              }
                              ?>
                            </td>
                          <?php } ?>
                          <?php if (($a['status'] == 'UM' || $a['status'] == 'UM Burek') && ($_SESSION['divisi'] == 'Direksi') && $_SESSION['divisi'] == 'FINANCE') { ?>
                            <td>
                              <!-- <button type="button" class="btn btn-info btn-small" onclick="realisasi('<?php echo $no; ?>','<?php echo $waktu; ?>')">Realisasi</button> -->
                            </td>
                          <?php } else { ?>
                            <td></td>
                          <?php } ?>

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

                                $noidbpu          = $bayar['noid'];
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
                                $fileuploadRealisasi       = $bayar['fileupload_realisasi'];
                                $pengajuan_realisasi = $bayar['pengajuan_realisasi'];
                                $pengajuan_uangkembali = $bayar['pengajuan_uangkembali'];
                                $pengajuan_tanggalrealisasi  = $bayar['pengajuan_tanggalrealisasi'];
                                $statusPengajuanRealisasi = $bayar['status_pengajuan_realisasi'];
                                $noStkb       = ($bayar['nomorstkb']) ? $bayar['nomorstkb'] : '-';
                                $kembreal         = $realisasi + $uangkembali;
                                $sisarealisasi    = $jumlbayar - $kembreal;
                                $statusPengajuanBpu = $bayar['status_pengajuan_bpu'];
                                $alasanTolakBpu = $bayar['alasan_tolak_bpu'];
                                $alasanTolakRealisasi = $bayar['alasan_tolak_realisasi'];

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

                                // if ($statusPengajuanRealisasi == 1) {
                                //   $color = '#8aad70';
                                // } else if ($statusPengajuanRealisasi == 2) {
                                //   $color = '#ff3b3b';
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
                                echo "Metode Pembayaran : <br><b> ".$bayar['metode_pembayaran'];
                                echo "</b><br/>";
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
                                if ($fileuploadRealisasi != NULL) {
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

                                if ($persetujuan == 'Pending' || $persetujuan == 'Belum Disetujui') {
                                  echo "Komentar : <br><b> $alasan ";
                                  echo "</b><br/>";
                                  if ($hakAkses == "Level 4" || $hakAkses == "Level 5") {
                                    if ($hakAkses == "Level 5") {
                          ?>
                                      <button type="button" class="btn btn-success btn-small" onclick="edit_budget2('<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Setujui</button>
                                      </br>
                                    <?php } ?>

                                    <button type="button" class="btn btn-warning btn-small" onclick="editharga('<?php echo $noidbpu; ?>','<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Edit</button>
                                    </br>
                                    <button type="button" class="btn btn-danger btn-small" onclick="hapus_bpu('<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Hapus</button>
                                  <?php
                                  }
                                } else if ((($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)' || $persetujuan == 'Disetujui oleh sistem') and $statusbayar == 'Belum Di Bayar') && ($hakAkses == "Level 4" || $hakAkses == "Level 5")) {
                                  ?>
                                  <button type="button" class="btn btn-warning btn-small" onclick="editharga('<?php echo $noidbpu; ?>','<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Edit</button>
                                  </br>
                                  <button type="button" class="btn btn-danger btn-small" onclick="hapus_bpu('<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Hapus</button>

                                <?php
                                } else if (($statusbayar == 'Realisasi (Finance)' || $statusbayar == 'Realisasi (Direksi)' || $uangkembali != 0) && ($hakAkses == "Level 4" || $hakAkses == "Level 5")) {
                                  echo "Uang Kembali :<br><b> Rp. " . number_format($uangkembali, 0, '', ',');
                                  echo "</b><br/>";
                                ?>
                                  <button type="button" class="btn btn-warning btn-small" onclick="editharga('<?php echo $noidbpu; ?>','<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Edit</button>
                                  </br>
                                  <button type="button" class="btn btn-danger btn-small" onclick="hapus_bpu('<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Hapus</button>
                                <?php
                                } else if ($hakAkses == "Level 4" || $hakAkses == "Level 5") {
                                ?>
                                  <button type="button" class="btn btn-warning btn-small" onclick="editharga('<?php echo $noidbpu; ?>','<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Edit</button>
                                  </br>
                                  <button type="button" class="btn btn-danger btn-small" onclick="hapus_bpu('<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Hapus</button>
                                <?php
                                }

                                if (($_SESSION['jabatan'] == 'Manager' || $_SESSION['jabatan'] == 'Senior Manager') && $statusPengajuanBpu == 3) { ?>
                                  <button type="button" class="btn btn-primary btn-small" onclick="mengetahui('<?php echo $pengajuanJumlah; ?>','<?php echo $namapenerima; ?>','<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Mengetahui</button>
                                  </br>
                                <?php
                                }

                                if ($statusbayar == 'Telah Di Bayar' && !$statusPengajuanRealisasi && ($a['status'] == 'UM' || $a['status'] == 'UM Burek')) {
                                ?>
                                  <!-- <button type="button" style="margin-bottom: 5px; margin-top: 10px;" class="btn btn-info" onclick="realisasi('<?php echo $no; ?>','<?php echo $waktu; ?>', '<?= $termm ?>', '<?= $jumlbayar ?>', '<?= $sisarealisasi ?>')">Realisasi</button> -->
                                <?php
                                }

                                if ($statusPengajuanRealisasi == 2) { ?>
                                  <!-- <button type="button" style="margin-bottom: 5px; margin-top: 10px;" class="btn btn-info" onclick="realisasi('<?php echo $no; ?>','<?php echo $waktu; ?>', '<?= $termm ?>', '<?= $jumlbayar ?>',  '<?= $sisarealisasi ?>', '<?= $pengajuan_realisasi ?>', '<?= $pengajuan_uangkembali ?>', '<?= $pengajuan_tanggalrealisasi ?>' , '<?= $fileuploadRealisasi ?>', '<?= $alasanTolakRealisasi ?>')">Ajukan Kembali Realisasi</button> -->
                                <?php
                                }

                                if ($statusPengajuanBpu == 2 && $pengaju == $_SESSION['nama_user']) { ?>
                                  <button type="button" style="margin-bottom: 5px; margin-top: 10px;" class="btn btn-info" onclick="ajukanBpu('<?php echo $no; ?>','<?php echo $waktu; ?>', '<?= $termm ?>', '<?= $pengajuanJumlah ?>', '<?= $namapenerima ?>', '<?= $norek ?>', '<?= $namabank ?>', '<?= $fileupload ?>', '<?= $alasanTolakBpu ?>', '<?= $statusbpu ?>')">Ajukan Kembali</button>
                                <?php  }

                                if ($_SESSION['nama_user'] == $pengaju && $statusPengajuanBpu == 0 && $persetujuan == 'Belum Disetujui') {
                                ?>
                                  <!-- <button type="button" style="margin-bottom: 5px; margin-top: 10px;" class="btn btn-info" onclick="editBpu('<?php echo $no; ?>','<?php echo $waktu; ?>', '<?= $termm ?>', '<?= $jumlbayar ?>', '<?= $namapenerima ?>', '<?= $norek ?>', '<?= $namabank ?>', '<?= $fileupload ?>', '<?= $alasanTolakBpu ?>', '<?= $statusbpu ?>')">Edit</button> -->
                          <?php }
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

              <div class="col-xs-3">: <b><?php echo 'Rp. ' . number_format($totalBiaya, 0, '', ','); ?></font></b></div>
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
              <div class="col-xs-3">: <b><?php echo 'Rp. ' . number_format($d['totalbudget'] - $totalBiaya, 0, '', ','); ?></font></b></div>
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

            <br /><br />

            <h3>Memo Upload</h3>
            <table class="table table-striped table-bordered">
              <thead>
                <tr class="warning">
                  <th>No</th>
                  <th>Gambar</th>
                  <th>Status</th>
                  <th>Tanggal Dan Jam</th>
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
                    <td><?php echo $su['timestam']; ?></td>
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
                        <td><?php echo $a['timestam']; ?></td>
                        <td><?php echo $a['status']; ?></td>
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
              <h3 class="modal-title text-center">Permintaan Pencairan</h3>
            </div>
            <div class="modal-body">
              <div class="fetched-data"></div>
              <img id="image" class="img-responsive" style="display: block; margin-left: auto;  margin-right: auto; background-repeat: no-repeat; background-size: cover; background-attachment: fixed;  " src="" alt="">
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
            </div>
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

      <div class="modal fade" id="editBpuModal" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h3 class="modal-title text-center">Edit BPU</h3>
            </div>
            <div class="modal-body">
              <form action="edit-bpu.php" method="post" id="theForm" name="Form" enctype="multipart/form-data">

                <input type="hidden" id="noBpuEdit" name="no">
                <input type="hidden" id="waktuBpuEdit" name="waktu">
                <input type="hidden" id="termBpuEdit" name="term">
                <input type="hidden" id="kodeBpuEdit" name="kode" value="<?= $code ?>">

                <div class="form-group">
                  <label for="rincian" class="control-label">Total BPU (IDR) :</label>
                  <input class="form-control" name="jumlah" id="jumlahBpuEdit" type="number">
                </div>
                <div class="form-group" id="nama-penerima-listEdit">
                  <label for="namapenerima" class="control-label">Nama Penerima :</label>
                  <select class="form-control" id="namapenerimaBpuListEdit" name="namapenerimaAjukanKembali" onchange="ambil_rekening2(this.value)">
                    <option selected disabled>Pilih Nama Penerima</option>
                    <?php
                    $querycok = "SELECT * FROM tb_user ORDER BY nama_user";
                    $run_querycok = $koneksi->query($querycok);
                    foreach ($run_querycok as $rq) {
                    ?>
                      <option value="<?php echo $rq['nama_user']; ?>"><?php echo $rq['nama_user']; ?></option>
                    <?php
                    }
                    ?>
                    <?php

                    $aplikasi = [];
                    $queryAplikasi = mysqli_query($koneksi, "SELECT * FROM daftar_aplikasi_pembayaran ORDER BY nama_aplikasi");
                    while ($a = mysqli_fetch_assoc($queryAplikasi)) {
                      array_push($aplikasi, $a['nama_aplikasi']);
                    }
                    // $queryAplikasi = mysqli_query($koneksi, "SELECT * FROM daftar_aplikasi_pembayaran ORDER BY nama_aplikasi");
                    // while ($a = mysqli_fetch_assoc($queryAplikasi)) :
                    foreach ($aplikasi as $a) :
                    ?>
                      <!-- <option value="">a</option> -->
                      <option value="<?= $a ?>"><?= $a ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="form-group" id="nama-penerima-textEdit">
                  <label for="namapenerima" class="control-label">Nama Penerima :</label>
                  <input type="text" class="form-control" id="namapenerimaBpuTextEdit" name="namapenerima">
                </div>

                <div class="form-group">
                  <label class="control-label">Upload File <a href="#" data-toggle="tooltip" title="Upload File Rincian BPU"><i class="fa fa-question-circle"></i></a></label>
                  <input type="file" class="form-control" accept="image/*,application/pdf" name="gambar" id="fileInputPengajuanBpuEdit">
                  <img class="img-responsive" style="display: block; margin-left: auto;  margin-right: auto; background-repeat: no-repeat; background-size: cover; background-attachment: fixed;" src="" alt="" id="imagePengajuanBpuEdit">
                </div>

                <div class="free-text">
                  <div class="form-group">
                    <label for="namabank" class="control-label">Nama Bank :</label>
                    <input type="text" class="form-control" id="bankBpuTextEdit" name="namabankAjukanKembali" readonly>
                  </div>

                  <div class="form-group">
                    <label for="norek" class="control-label">Nomor Rekening :</label>
                    <input type="number" class="form-control" id="noRekBpuTextEdit" name="norekAjukanKembali" readonly>
                  </div>
                </div>

                <div class="option-list">
                  <div class="form-group">
                    <label for="namabank" class="control-label">Nama Bank :</label>
                    <select class="form-control" id="bankBpuListEdit" name="namabank">
                      <option value="" selected disabled>Pilih Kategori</option>
                      <?php
                      $queryDaftarBank = mysqli_query($koneksi, 'SELECT * FROM bank');
                      while ($db = mysqli_fetch_assoc($queryDaftarBank)) :
                      ?>
                        <option value="<?= $db['kodebank'] ?>"><?= $db['namabank'] ?></option>
                      <?php endwhile; ?>
                    </select>
                    <!-- <input type="text" class="form-control" name="namabank"> -->
                  </div>

                  <div class="form-group">
                    <label for="norek" class="control-label">Nomor Rekening :</label>
                    <input type="number" class="form-control" id="noRekBpuListEdit" name="norek">
                  </div>
                </div>

            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
              <button type="submit" class="btn btn-primary">Save</button>
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
              <h3 class="modal-title text-center">Tambah Budget</h3>
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
              <h3 class="modal-title text-center">Persetujuan BPU</h3>
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

      <div class="modal fade" id="myModalEditHarga" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h3 class="modal-title text-center">Edit PBU</h3>
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

      <div class="modal fade" id="myModalHapusBpu" role="dialog">
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

      <div class="modal fade" id="mengetahuiModal" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h3 class="modal-title text-center">Data BPU</h3>
            </div>
            <form action="proses-bpu-kadiv.php" method="POST">
              <input type="hidden" name="no">
              <input type="hidden" name="waktu">
              <input type="hidden" name="term">
              <input type="hidden" name="kode" value="<?= $code ?>">
              <div class="modal-body">
                <div class="form-group">
                  <label for="rincian" class="control-label">Total diajukan:</label>
                  <input type="text" class="form-control" name="jumlah" disabled>
                </div>
                <div class="form-group">
                  <label for="rincian" class="control-label">Nama Penerima:</label>
                  <input type="text" class="form-control" name="namapenerima" disabled>
                </div>
              </div>
              <div class="modal-footer">
                <button class="btn btn-primary" type="submit" name="submit">Submit</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
              </div>
            </form>
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
                $(document).ready(function() {
                  $('#fileInputPengajuanBpu').change(function() {
                    readURL(this);
                  })
                  $("#id_step2-number_2").keyup(function(event) {
                    // skip for arrow keys
                    if (event.which >= 37 && event.which <= 40) {
                      event.preventDefault();
                    }
                    var $this = $(this);
                    var num = $this.val().replace(/,/gi, "").split("").reverse().join("");

                    var num2 = RemoveRougeChar(num.replace(/(.{3})/g, "$1,").split("").reverse().join(""));

                    console.log(num2);


                    // the following line has been simplified. Revision history contains original.
                    $this.val(num2);
                  });
                });

                function readURL(input) {
                  if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                      $('#imagePengajuanBpu').attr('src', e.target.result);
                    }

                    reader.readAsDataURL(input.files[0]); // convert to base64 string
                  }
                }


                function RemoveRougeChar(convertString) {


                  if (convertString.substring(0, 1) == ",") {

                    return convertString.substring(1, convertString.length)

                  }
                  return convertString;

                }

                $(document).ready(function() {
                  $("#id_step3-number_3").keyup(function(event) {
                    // skip for arrow keys
                    if (event.which >= 37 && event.which <= 40) {
                      event.preventDefault();
                    }
                    var $this = $(this);
                    var num = $this.val().replace(/,/gi, "").split("").reverse().join("");

                    var num2 = RemoveRougeChar(num.replace(/(.{3})/g, "$1,").split("").reverse().join(""));

                    console.log(num2);


                    // the following line has been simplified. Revision history contains original.
                    $this.val(num2);
                  });
                });

                function RemoveRougeChar(convertString) {


                  if (convertString.substring(0, 1) == ",") {

                    return convertString.substring(1, convertString.length)

                  }
                  return convertString;

                }
              </script>

            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" id="realiasiModal" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h3 class="modal-title text-center">Realisasi</h3>
            </div>
            <div class="modal-body">
              <form action="realisasi-proses-user.php" method="post" id="theForm" name="Form" onsubmit="return validateForm()" enctype="multipart/form-data">


                <input type="hidden" id="noRealisasi" name="no">
                <input type="hidden" id="waktuRealisasi" name="waktu">
                <input type="hidden" id="termRealisasi" name="term">
                <input type="hidden" id="sisaRealisasi" name="sisa">
                <div class="form-group">
                  <label for="rincian" class="control-label">Total BPU:</label>
                  <input type="text" class="form-control" id="hasilRealisasiText" readonly>
                  <input type="hidden" class="form-control" id="hasilRealisasi" name="totalbpu">
                </div>

                <div class="form-group">
                  <label for="rincian" class="control-label">Realisasi (IDR) :</label>
                  <input type="text" class="form-control" name="realisasi" id="realisasi">
                </div>
                <div class="form-group">
                  <label for="rincian" class="control-label">Uang Kembali (IDR) :</label>
                  <input type="text" class="form-control" name="uangkembali" id="uangkembali">
                </div>

                <div class="form-group">
                  <label for="rincian" class="control-label">Tanggal Realisasi :</label>
                  <input type="date" class="form-control" name="tanggalrealisasi" id="tanggalrealisasi">
                </div>

                <div class="form-group">
                  <label class="control-label">Upload File <i class="fa fa-question-circle" title="Upload bukti penggunaan dana dan pengembalian dana (jika ada)"></i></label>
                  <input type="file" class="form-control" accept="image/*,application/pdf" name="gambar" id="fileInput" required>
                  <img class="img-responsive" style="display: block; margin-left: auto;  margin-right: auto; background-repeat: no-repeat; background-size: cover; background-attachment: fixed;" src="" alt="" id="imageRealisasi">
                </div>

                <div class="form-group" id="form-alasan-penolakan">
                  <label for="rincian" class="control-label">Alasan Penolakan:</label>
                  <input type="text" class="form-control" name="alasanTolakRealisasi" id="alasanTolakRealisasi" disabled>
                </div>

                <button class="btn btn-primary" type="submit" name="submit">OK</button>
              </form>

            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
            </div>
          </div>
        </div>
      </div>


      <?php
      $no = isset($_GET['no']) && $_GET['no'] ? $_GET['no'] : NULL;
      $waktu = isset($_GET['waktu']) && $_GET['waktu'] ? $_GET['waktu'] : NULL;
      $termreal = isset($_GET['term']) && $_GET['term'] ? $_GET['term'] : NULL;
      ?>

      <script type="text/javascript">
        $(document).ready(function() {
          $('#fileInput').change(function() {
            readURL(this);
          })

          $('#fileInputPengajuanBpu').change(function() {
            readURL2(this);
          })

          $('#fileInputPengajuanBpuEdit').change(function() {
            readURL3(this);
          })
        })

        function readURL(input) {
          if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
              $('#imageRealisasi').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]); // convert to base64 string
          }
        }

        function readURL3(input) {
          if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
              $('#imagePengajuanBpuEdit').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]); // convert to base64 string
          }
        }

        function readURL2(input) {
          if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
              $('#imagePengajuanBpu').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]); // convert to base64 string
          }
        }

        function numberWithCommas(x) {
          return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        function mengetahui(jumlah, namapenerima, no, waktu, term) {
          $('#mengetahuiModal input[name=jumlah]').val(numberWithCommas(jumlah));
          $('#mengetahuiModal input[name=namapenerima]').val(namapenerima);
          $('#mengetahuiModal input[name=no]').val(no);
          $('#mengetahuiModal input[name=waktu]').val(waktu);
          $('#mengetahuiModal input[name=term]').val(term);
          $('#mengetahuiModal').modal();
        }

        function realisasi(no, waktu, term, jumlah, sisa, realisasi, uangkembali, tanggalrealisasi, file, alasanTolak) {
          $('#noRealisasi').val(no);
          $('#waktuRealisasi').val(waktu);
          $('#termRealisasi').val(term);
          $('#hasilRealisasi').val(jumlah);
          $('#hasilRealisasiText').val(numberWithCommas(jumlah));
          $('#sisaRealisasi').val(sisa);
          $('#realisasi').val(realisasi);
          $('#uangkembali').val(uangkembali);
          $('#tanggalrealisasi').val(tanggalrealisasi);
          if (alasanTolak) {
            $('#alasanTolakRealisasi').val(alasanTolak);
            $('#form-alasan-penolakan').show();
          } else {
            $('#alasanTolakRealisasi').val('');
            $('#form-alasan-penolakan').hide();
          }
          console.log(alasanTolak);

          $('#realisasi').prop('max', sisa);

          // $('#imageRealisasi').attr('src', `uploads/${file}`)

          $('#realiasiModal').modal();
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

        function editBpu(no, waktu, term, totalBpu, namaPenerima, noRek, namaBank, file, alasan, statusBpu) {
          console.log(file);

          $('#noBpuEdit').val(no);
          $('#waktuBpuEdit').val(waktu);
          $('#termBpuEdit').val(term);
          $('#imagePengajuanBpuEdit').attr('src', 'uploads/' + file);
          // readURL3(file);

          $('#jumlahBpuEdit').val(totalBpu);
          if (statusBpu == "UM" || statusBpu == "UM Burek") {
            console.log($('#nama-penerima-listEdit'));
            $('#nama-penerima-listEdit').show();
            $('.option-list').hide();
            $('.free-text').show();
            $('#nama-penerima-textEdit').hide();

            $('#bankBpuTextEdit').val(namaBank);
            $('#noRekBpuTextEdit').val(noRek);
            $('#namapenerimaBpuListEdit').val(namaPenerima);
          } else {
            $('#nama-penerima-textEdit').show();
            $('#nama-penerima-listEdit').hide();
            $('.option-list').show();
            $('.free-text').hide();


            $('#bankBpuListEdit').val(namaBank);
            $('#noRekBpuListEdit').val(noRek);
            $('#namapenerimaBpuTextEdit').val(namaPenerima);
          }
          // $('#alasanTolakBpu').val(alasan);

          $('#editBpuModal').modal();
        }

        function ambil_rekening(id_user) {
          // $("#bankBpuList").val('');
          // $("#noRekBpuList").val('');
          $('#bankBpuText').val('');
          $('#noRekBpuText').val('');
          $.ajax({
              url: 'bpuajax.php',
              type: 'post',
              dataType: 'json',
              data: {
                actions: 'ambil_rekening',
                id_user: id_user
              }
            })
            .done(function(data) {

              console.log(data);
              if (data != '') {
                // $("#bankBpuList").val(data.bank);
                // $("#noRekBpuList").val(data.norek);
                $('#bankBpuText').val(data.bank);
                $('#noRekBpuText').val(data.norek);
              } else {
                // $("#bankBpuList").val('');
                // $("#noRekBpuList").val('');
                $('#bankBpuText').val('');
                $('#noRekBpuText').val('');
              }
            })
            .fail(function() {
              console.log('Gagal');
            });
        }

        function ambil_rekening2(id_user) {
          // $("#bankBpuList").val('');
          // $("#noRekBpuList").val('');
          $('#bankBpuTextEdit').val('');
          $('#noRekBpuTextEdit').val('');
          $.ajax({
              url: 'bpuajax.php',
              type: 'post',
              dataType: 'json',
              data: {
                actions: 'ambil_rekening',
                id_user: id_user
              }
            })
            .done(function(data) {

              console.log(data);
              if (data != '') {
                // $("#bankBpuList").val(data.bank);
                // $("#noRekBpuList").val(data.norek);
                $('#bankBpuTextEdit').val(data.bank);
                $('#noRekBpuTextEdit').val(data.norek);
              } else {
                // $("#bankBpuList").val('');
                // $("#noRekBpuList").val('');
                $('#bankBpuTextEdit').val('');
                $('#noRekBpuTextEdit').val('');
              }
            })
            .fail(function() {
              console.log('Gagal');
            });
        }

        function edit_budget2(no, waktu, term) {

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
              $('#myModal2').modal();
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
              $('#myModalEditHarga').modal();
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
              $('#myModalHapusBpu').modal();
            }
          });
        }

        // function realisasi(no, waktu) {
        //   // alert(noid+' - '+waktu);
        //   $.ajax({
        //     type: 'post',
        //     url: 'realisasidireksi.php',
        //     data: {
        //       no: no,
        //       waktu: waktu
        //     },
        //     success: function(data) {
        //       $("#isi_form").html(data);
        //       // $('.fetched-data').html(data);//menampilkan data ke dalam modal
        //       $('#myModal4').modal();
        //     }
        //   });
        // }
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

        function edit_budget(no, waktu, id) {
          // alert(noid+' - '+waktu);
          $.ajax({
            type: 'post',
            url: 'bpu.php',
            data: {
              no: no,
              waktu: waktu,
              page: 'views',
              id: id,
            },
            success: function(data) {
              $('.fetched-data').html(data); //menampilkan data ke dalam modal
              $('#myModal').modal();
            }
          });
        }

        function tambah_budget(no, waktu) {
          // alert(noid+' - '+waktu);
          $.ajax({
            type: 'post',
            url: 'tambahpengaju.php',
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
      </script>

</body>

</html>