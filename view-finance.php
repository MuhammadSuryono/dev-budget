<?php
error_reporting(0);
session_start();
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
$con->load_database($koneksi);

$con->set_name_db(DB_TRANSFER);
$con->init_connection();
$koneksiBridge = $con->connect();

require_once "application/config/helper.php";
$helper = new Helper();
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
          <?php if ($_SESSION['hak_akses'] == 'HRD') { ?>
            <li><a href="home-direksi.php">Home</a></li>
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


        <?php if ($_SESSION['hak_akses'] != 'HRD') { ?>
          
         <ul class="nav navbar-nav navbar-right">
                        <li><a href="/log-notifikasi-aplikasi/index.html" target="_blank"><i class="fa fa-envelope"></i></a></li>
            
            <li><a href="ubahpassword.php"><span class="glyphicon glyphicon-user"></span><?php echo $_SESSION['nama_user']; ?> (<?php echo $_SESSION['divisi']; ?>)</a></li>
            <li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> Logout</a></li>
          </ul>
        <?php } else {
          
        ?>
         <ul class="nav navbar-nav navbar-right">
                        <li><a href="/log-notifikasi-aplikasi/index.html" target="_blank"><i class="fa fa-envelope"></i></a></li>
            

            <li><a href="ubahpassword.php"><span class="glyphicon glyphicon-user"></span><?php echo $_SESSION['nama_user']; ?> (<?php echo $_SESSION['divisi']; ?>)</a></li>
            <li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> Logout</a></li>
          </ul>
        <?php } ?>
      </div>
    </div>
  </nav>

  <div class="container-fluid">

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
                      $liatbayarth = mysqli_query($koneksi, "SELECT * FROM bpu WHERE waktu='$waktu' AND no='$no' AND status_pengajuan_bpu != 2");
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
                    ?>
                        <tr>
                          <th scope="row"><?php echo $i++; ?></th>
                          <td><?php echo $a['rincian']; ?></td>
                          <td><?php echo $a['kota']; ?></td>
                          <td><?php echo $a['status']; ?></td>
                            <td>
                                <?php echo $a['penerima']; ?><br/>
                                <?php
                                if (in_array($a['status'], ["UM", "UM Burek", "Biaya Lumpsum"])) {
                                    $listReceiver = $con->select("*")->from("tb_penerima")
                                        ->where("item_id", "=", $a["id"])->get();
                                    echo "<ul>";
                                    foreach ($listReceiver as $key => $value) {
                                        $iconValidate = $value["is_validate"] == 1 ? "<i class='fa fa-check text-success'></i>": "<i class='fa fa-exclamation text-danger'></i>";
                                        $title = $value["is_validate"] == 1 ? "Terverifikasi oleh $value[validator]": "Belum Terverifikasi";
                                        echo "<li>$value[nama_penerima] ($value[jabatan]) - <span class='text-center' title='$title'>$iconValidate</span></li>";
                                    }
                                    echo "</ul>";
                                }
                                ?>
                            </td>
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
                          $query = "SELECT sum(jumlah) AS sum FROM bpu WHERE no='$no' AND waktu='$waktu'";
                          $result = mysqli_query($koneksi, $query);
                          $row = mysqli_fetch_array($result);
                          $total = $row[0];
                          $query16 = "SELECT sum(uangkembali) AS sum FROM bpu WHERE no='$no' AND waktu='$waktu'";
                          $result16 = mysqli_query($koneksi, $query16);
                          $row16 = mysqli_fetch_array($result16);
                          $total16 = $row16[0];

                          $queryTerm = mysqli_query($koneksi, "SELECT term FROM bpu WHERE no='$no' AND waktu='$waktu'");
                          $resultTerm = mysqli_fetch_array($queryTerm);
                          $term = $resultTerm[0];

                          $jadinya = $hargaah - $total;
                          ?>
                          <td><?php echo 'Rp. ' . number_format($jadinya, 0, '', ','); ?></td>
                          <!-- //Sisa Pembayaran -->
                          <td>
                            <?php


                            if ($a['status'] == 'Honor Eksternal' || $a['status'] == 'Vendor/Supplier' || $a['status'] == 'Honor Area Head') {

                              if (in_array("eksternal_bpu", $buttonAkses)) :
                            ?>
                                <button type="button" class="btn btn-default btn-small" onclick="eksternal_finance('<?php echo $no; ?>','<?php echo $waktu; ?>')">Eksternal</button>
                              <?php
                              endif;
                            }
                            
                              // echo $a['status'];
                              if ($aksesSes != "Level 1") {
                              ?>
                                <!-- <?php if ($a['status'] == 'UM' || $a['status'] == 'Pulsa' || $a['status'] == 'Biaya External' || $a['status'] == 'Biaya' || $a['status'] == 'UM Burek' || $a['status'] == 'Biaya Lumpsum') {
                                      if ($a['status'] == 'Biaya Lumpsum' || $a['status'] == 'UM' || $a['status'] == 'UM Burek') {
                                          $id = $a["id"];
                                          echo '<button type="button" class="btn btn-primary btn-small" onclick="showModalAddReceiverBpu('.$id.')">Tambah Penerima</button><br/>';
                                      }
                                    ?> -->

                                  <button type="button" style="margin-top: 5px;" class="btn btn-default btn-small" onclick="bpu_budget('<?php echo $no; ?>','<?php echo $waktu; ?>')">BPU</button>
                                <!-- <?php } ?> -->
                              <?php } ?>
                          </td>

                          <?php
                          $arrCheck = [];
                          $liatbayar = mysqli_query($koneksi, "SELECT * FROM bpu WHERE waktu='$waktu' AND no='$no' AND status_pengajuan_bpu != 2 ORDER BY term ASC");
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
                                $jumlbayar          = $bayar['jumlah'];
                                $pengajuanJumlah = $bayar['pengajuan_ju$uangkembalimlah'];
                                $tglbyr             = $bayar['tglcair'];
                                $statusbayar        = $bayar['status'];
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
                                $userMengetahui      = $bayar['acknowledged_by'];
                                $userCheck          = $bayar['checkby'];
                                $userApprove         = $bayar['approveby'];
                                $userPembayar         = $bayar['pembayar'];
                                $divisi2            = $bayar['divisi'];
                                $pembayar           = $bayar['pembayar'];
                                $statusbpu        = $bayar['statusbpu'];
                                $fileupload       = $bayar['fileupload'];
                                $fileuploadRealisasi       = $bayar['fileupload_realisasi'];
                                $pengajuan_realisasi = $bayar['pengajuan_realisasi'];
                                $pengajuan_uangkembali = $bayar['pengajuan_uangkembali'];
                                $pengajuan_tanggalrealisasi  = $bayar['pengajuan_tanggalrealisasi'];
                                $statusPengajuanRealisasi = $bayar['status_pengajuan_realisasi'];
                                $pengajuan_realiasi = $bayar['pengajuan_realiasi'];
                                $noStkb       = ($bayar['nomorstkb']) ? $bayar['nomorstkb'] : '-';
                                $kembreal         = $realisasi + $uangkembali;
                                $sisarealisasi    = $jumlbayar - $kembreal;
                                $statusPengajuanBpu = $bayar['status_pengajuan_bpu'];
                                $batasTanggalBayar = $bayar['batas_tanggal_bayar'];
                                $alasanTolakBpu = $bayar['alasan_tolak_bpu'];
                                $alasanTolakRealisasi = $bayar['alasan_tolak_realisasi'];
                                // $metodePembayaran = $bayar['metode_pembayaran'];
                                $ketPembayaran = $bayar['ket_pembayaran'];
                                $termStkb = $bayar['termstkb'];

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

                                // if ($statusPengajuanRealisasi == 1) {
                                //   $color = '#8aad70';
                                // } else if ($statusPengajuanRealisasi == 2) {
                                //   $color = '#ff3b3b';
                                // } else if ($statusPengajuanRealisasi == 3) {
                                //   $color = '#9932CC';
                                // }

                                echo "<td bgcolor=' $color '>";
                                echo "</b><br>";
                                echo "No. BPU :<b> $noidbpu";
                                echo "</b><br>";
                                echo "No. STKB :<b> $noStkb";
                                echo "</b><br>";
                                  echo "Term STKB :<b> $termStkb";
                                  echo "</b><br>";
                                echo "No. Term:<b> $termm";
                                echo "</b><br>";
                                echo "Tanggal Buat BPU: <br><b> " . date('Y-m-d', strtotime($waktustempel));
                                echo "</b><br>";
                                echo "Jam : <b>" . date('H:i:s', strtotime($waktustempel));
                                echo "</b></br>";
                                echo "Tanggal Terima Uang : <b>$tglcair ";
                                echo "</b></br>";
                                echo "<hr/>";
                                echo "Nominal Pajak : <br><b>Rp. " .number_format($bayar['nominal_pajak'] == null ? 0 : $bayar['nominal_pajak']) . " (".$bayar['jenis_pajak'].")";
                                echo "</b><br>";
                                echo ($statusPengajuanBpu != 0) ? "Request BPU : <br><b>Rp. " . number_format($total['jumlah_pengajuan'], 0, '', ',') : "Nominal Pembayaran : <br><b>Rp. " . number_format($total['jumlah_total'], 0, '', ',');
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
                              echo "Keterangan Pembayaran : <br><b> " . $ketPembayaran;
                              echo "</b><br/>";
                              echo "No Voucher : <br><b> $novoucher ";
                              echo "</b><br/>";
                              echo "<hr />";
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
                                if ($statusbayar == 'Telah Di Bayar' && !$statusPengajuanRealisasi && ($a['status'] == 'UM' || $a['status'] == 'UM Burek') && ($aksesSes == 'Manager')) {
                          ?>
                                  <!-- <button type="button" style="margin-bottom: 5px; margin-top: 10px;" class="btn btn-info btn-xs" onclick="realisasi('<?php echo $no; ?>','<?php echo $waktu; ?>', '<?= $termm ?>', '<?= $jumlbayar ?>', '<?= $sisarealisasi ?>')">Realisasi</button> -->
                                <?php
                                }

                                if (($a['status'] == 'UM' || $a['status'] == 'UM Burek' || $a['status'] == 'Finance' || $a['status'] == 'Pulsa' || $a['status'] == 'Biaya' || $a['status'] == 'Biaya Lumpsum' || $a['status'] == 'Vendor/Supplier' || $a['status'] == 'Honor Eksternal' || $a['status'] == 'Honor Area Head') && ($statusbayar == 'Belum Di Bayar' && ($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)' || $persetujuan == 'Disetujui oleh sistem')) && $showButtonBayar && $metodePembayaran != "MRI PAL") {

                                  
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
                                if ($statusbayar == 'Realisasi (Direksi)' || $statusbayar == 'Realisasi (Finance)') {
                                  echo "<button><a href='forprint.php?page=2&code=" . $nobay . "&waktu=" . $wakbay . "&term=" . $termm . "'>Memorial</a></button>";
                                } else {
                                  echo "";
                                }
                                echo "<button><a href='forprint.php?page=1&code=" . $nobay . "&waktu=" . $wakbay . "&term=" . $termm . "'>Print</a></button>";

                                if ($statusPengajuanBpu == 2 && $pengaju == $_SESSION['nama_user']) { ?>
                                  <button type="button" style="margin-bottom: 5px; margin-top: 10px;" class="btn btn-info" onclick="ajukanBpu('<?php echo $no; ?>','<?php echo $waktu; ?>', '<?= $termm ?>', '<?= $pengajuanJumlah ?>', '<?= $namapenerima ?>', '<?= $norek ?>', '<?= $namabank ?>', '<?= $fileupload ?>', '<?= $alasanTolakBpu ?>', '<?= $statusbpu ?>')">Ajukan Kembali</button>
                                <?php  }

                                if ($statusPengajuanRealisasi == 1) {
                                ?>
                                  <!-- <button type="button" style="margin-bottom: 5px; margin-top: 10px;" class="btn btn-info" onclick="realisasi('<?php echo $no; ?>','<?php echo $waktu; ?>', '<?= $termm ?>', '<?= $jumlbayar ?>', '<?= $pengajuan_realisasi ?>', '<?= $pengajuan_uangkembali ?>', '<?= $pengajuan_tanggalrealisasi ?>', '<?= $sisarealisasi ?>' , '<?= $fileuploadRealisasi ?>')">Verifikasi Realisasi</button> -->
                                <?php
                                }
                                if ($statusPengajuanBpu == 1 && in_array("verifikasi_bpu", $buttonAkses)) : ?>
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
              <?php
              $queryTotalBudget = mysqli_query($koneksi, "SELECT sum(total) as total_budget FROM selesai WHERE waktu = '$d[waktu]'");
              $dataTotalBudget = mysqli_fetch_assoc($queryTotalBudget);
            ?>
            <div class="col-xs-3">: <b><?php echo 'Rp. ' . number_format($dataTotalBudget['total_budget'], 0, '', ','); ?></b></div>
            </div>

            <div class="row">
              <div class="col-xs-3">
                <font color="#1bd34f">Total Biaya dan Uang Muka 
                  <hr/>
              </div>

              <?php
              $query2 = "SELECT sum(jumlah) AS total_pembayaran FROM bpu WHERE waktu='$waktu'";
              $result2 = mysqli_query($koneksi, $query2);
              $row2 = mysqli_fetch_array($result2);
  
              $q_real = "SELECT sum(realisasi) AS total_realisasi FROM bpu WHERE waktu='$waktu'";
              $r_real = mysqli_query($koneksi, $q_real);
              $g_real = mysqli_fetch_array($r_real);
  
              $query10 = "SELECT sum(uangkembali) AS total_kembalian FROM bpu WHERE waktu='$waktu'";
              $result10 = mysqli_query($koneksi, $query10);
              $row10 = mysqli_fetch_array($result10);

              $query3 = "SELECT sum(jumlah) AS sumi FROM bpu WHERE waktu='$waktu' AND persetujuan='Disetujui (Direksi)' AND status='Belum Di Bayar'";
              $result3 = mysqli_query($koneksi, $query3);
              $row3 = mysqli_fetch_array($result3);

              
              $totlah = $row2['total_pembayaran'];
              $reallah = $row10['total_kembalian'];
              $tysb = $totlah - $reallah;
              ?>

              <div class="col-xs-3">: <b><?php echo 'Rp. ' . number_format($tysb - $row3['sumi'], 0, '', ','); ?></font></b></div>
            </div>


            <!-- Yang belum Bayar -->
            <div class="row">
              <div class="col-xs-3">
                <font color='#f23f2b'>Sisa Budget
              </div>
              <?php
              $aaaa = $dataTotalBudget['total_budget'];
              $bbbb = $row2['total_pembayaran'];
              $belumbayar = $aaaa - ($tysb - $row3['sumi']);
              ?>
              <div class="col-xs-3">: <b><?php echo 'Rp. ' . number_format($belumbayar, 0, '', ','); ?></font></b></div>
            </div>
            <!-- // Yang belum bayar -->

            <!-- Ready To Pay -->
            <div class="row">
              <div class="col-xs-3">
                <font color='#fcce00'>Ready To Pay :
              </div>
              <div class="col-xs-3">: <b><?php echo 'Rp. ' . number_format($row3['sumi'], 0, '', ','); ?></font></b></div>
            </div>
            <!-- // Ready To Pay -->

            <div class="row">
              <div class="col-xs-3">
                <font color="#cbf442">Total Uang Kembali Realisasi
              </div>
              <div class="col-xs-3">: <b><?php echo 'Rp. ' . number_format($row10['total_kembalian'], 0, '', ','); ?></font></b></div>
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
            <table class="table table-striped table-bordered tableFixHead">
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

          <!-- <div class="modal fade" id="verifikasiBpuModal" role="dialog" aria-labelledby="verifikasiBpuModalLabel">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h3 class="modal-title text-center" id="verifikasiBpuModalLabel">Verifikasi BPU</h3>
                </div>
                <div class="modal-body">

                  <form action="proses-bpu-finance.php" method="post" id="theForm" name="Form" onsubmit="return validateForm()" enctype="multipart/form-data">
                    <input type="hidden" id="noBpu" name="no">
                    <input type="hidden" id="waktuBpu" name="waktu">
                    <input type="hidden" id="termBpu" name="term">
                    <input type="hidden" name="kode" value="<?= $code ?>">
                    <div class="form-group">
                      <label for="rincian" class="control-label">BPU Diajukan:</label>
                      <input type="text" class="form-control" id="bpuDiajukan" readonly>
                      <a type="button" id="editVerifikasi1" class="pull-right" style="text-decoration: none; cursor: pointer; margin-top: 2px;">Edit <i class="far fa-edit"></i></a>
                    </div>

                    <label for="">Pajak</label>
                    <div class="row">
                      <div class="form-group">
                        <div class="col-lg-3">
                          <div class="input-group">

                            <input type="checkbox" id="pph4" name="pajak" value="pph4">
                            <label for="pph4"> PPH 4 ayat 2</label><br>
                            <input type="checkbox" id="pph21" name="pajak" value="pph21">
                            <label for="pph21"> PPH 21</label><br>
                          </div>
                        </div>
                        <div class="col-lg-3">

                          <input type="checkbox" id="pph23" name="pajak" value="pph23">
                          <label for="pph23"> PPH 23</label><br>
                          <select name="pph23value" id="pph23value" style="display: none;">
                            <option value="0.02">2%</option>
                            <option value="0.04">4%</option>
                          </select>
                        </div>
                      </div>

                    </div>
                    <div class="form-group">
                      <label for="rincian" class="control-label">Total BPU:</label>
                      <input type="hidden" class="form-control" id="hasilBpu" name="totalbpu">
                      <input type="text" class="form-control" id="hasilBpuText" readonly>
                    </div>


                    <div class="form-group">
                      <label for="rincian" class="control-label">Nama Penerima:</label>
                      <input type="text" class="form-control" name="penerima" id="penerimaBpu" readonly>
                    </div>

                    <div class="form-group">
                      <label for="rincian" class="control-label">Nama Bank:</label>
                      <input type="text" class="form-control" name="bank" id="bankBpu" readonly>
                    </div>

                    <div class="form-group">
                      <label for="rincian" class="control-label">Nomor Rekening:</label>
                      <input type="text" class="form-control" name="norek" id="noRekBpu" readonly>
                    </div>

                    <div class="form-group">
                      <label for="tglcair" class="control-label">Pembayaran :</label>
                      <select class="form-control" name="metode_pembayaran" id="metode_pembayaran" required>
                      </select>
                    </div>

                    <input type="hidden" name="status_sumber_rekening">

                    <div class="form-group form-rekening-sumber-pal-um" style="display: none;">
                      <label for="rekening_sumber" class="control-label">Rekening Sumber:</label>
                      <select class="form-control" name="rekening_sumber_mri_pal_um">
                        <?php $getRekening = mysqli_query($koneksiDevelop, "SELECT * FROM kas WHERE label_kas = 'Kas Uang Muka'"); ?>
                        <?php while ($item = mysqli_fetch_assoc($getRekening)) : ?>
                          <option value="<?= $item['rekening'] ?>"><?= $item['rekening'] ?> - <?= $item['label_kas'] ?></option>
                        <?php endwhile; ?>
                      </select>
                    </div>

                    <div class="form-group form-rekening-sumber-pal" style="display: none;">
                      <label for="rekening_sumber" class="control-label">Rekening Sumber :</label>
                      <select class="form-control" name="rekening_sumber_mri_pal">
                        <?php if ($d['jenis'] == 'B1' || $d['jenis'] == 'B2') : ?>
                          <?php $getRekening = mysqli_query($koneksiDevelop, "SELECT * FROM kas WHERE label_kas = 'Kas Project'"); ?>
                        <?php else : ?>
                          <?php $getRekening = mysqli_query($koneksiDevelop, "SELECT * FROM kas WHERE label_kas = 'Kas Umum'"); ?>
                        <?php endif; ?>
                        <?php while ($item = mysqli_fetch_assoc($getRekening)) : ?>
                          <option value="<?= $item['rekening'] ?>"><?= $item['rekening'] ?> - <?= $item['label_kas'] ?></option>
                        <?php endwhile; ?>
                      </select>
                    </div>

                    <div class="form-group form-rekening-sumber-kas" style="display: none;">
                      <label for="rekening_sumber" class="control-label">Rekening Sumber :</label>
                      <select class="form-control" name="rekening_sumber_mri_kas">
                        <?php $getRekening = mysqli_query($koneksiDevelop, "SELECT * FROM kas"); ?>
                        <?php while ($item = mysqli_fetch_assoc($getRekening)) : ?>
                          <option value="<?= $item['rekening'] ?>"><?= $item['rekening'] ?> - <?= $item['label_kas'] ?></option>
                        <?php endwhile; ?>
                      </select>
                    </div>

                    <div class="form-group" id="div-berita-transfer">
                      <label for="berita-transfer" class="control-label">Berita Transfer : <span data-toggle="tooltip" title="Maks. 36 Karakter"><i class="fa fa-question-circle"></i></span></label>
                      <input type="text" class="form-control" id="berita-transfer" name="berita_transfer" maxlength="36">
                    </div>


                    <div class="form-group form-rekening-sumber">
                      <label for="umo_biaya_kode_id" class="control-label">Kode Biaya :</label>
                      <br>
                      <select class="umo_biaya_kode_id" name="umo_biaya_kode_id" id="umo_biaya_kode_id" style="width: 100%;">
                        <?php $getBiayaKode = mysqli_query($koneksiDevelop, "SELECT * FROM umo_biaya_kode"); ?>
                        <?php while ($item = mysqli_fetch_assoc($getBiayaKode)) : ?>
                          <option value="<?= $item['biaya_kode_id'] ?>"><?= $item['biaya_kode_nama'] ?> - <?= $item['biaya_kode_kode'] ?></option>
                        <?php endwhile; ?>
                      </select>
                    </div>

                    <div class="form-group">
                      <label for="alasanTolakBpu" class="control-label">Alasan Penolakan (Jika ditolak):</label>
                      <input type="text" class="form-control" name="alasanTolakBpu" id="alasanTolakBpu">
                    </div>

                    <div class="form-group">
                      <label class="control-label">Upload File(Jika ada perubahan file) <a href="#" data-toggle="tooltip" title="Upload File Rincian BPU"><i class="fa fa-question-circle"></i></a></label>
                      <input type="file" class="form-control" accept="image/*,application/pdf" name="gambar" id="fileInputVerifikasiBpu">
                      <img class="img-responsive" style="display: block; margin-left: auto;  margin-right: auto; background-repeat: no-repeat; background-size: cover; background-attachment: fixed;" src="" alt="" id="imageVerifikasiBpu">
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
          </div> -->

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

          <div class="modal fade" id="realiasiModal" role="dialog">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h3 class="modal-title text-center">Realisasi</h3>
                </div>
                <div class="modal-body">
                  <form action="realisasi-proses-finance.php" method="post" id="theForm" name="Form" onsubmit="return validateForm()" enctype="multipart/form-data">

                    <input type="hidden" id="noRealisasi" name="no">
                    <input type="hidden" id="waktuRealisasi" name="waktu">
                    <input type="hidden" id="termRealisasi" name="term">
                    <input type="hidden" id="sisaRealisasi" name="sisa">
                    <div class="form-group">
                      <label for="rincian" class="control-label">Total BPU:</label>
                      <input type="text" class="form-control" id="hasilText" readonly>
                      <input type="hidden" class="form-control" id="hasil" name="totalbpu">
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
                      <label class="control-label">Uploaded File</label>
                      <img class="img-responsive" style="display: block; margin-left: auto;  margin-right: auto; background-repeat: no-repeat; background-size: cover; background-attachment: fixed;" src="" alt="" id="imageRealisasi">
                    </div>

                    <div class="form-group">
                      <label for="alasanTolakRealisasi" class="control-label">Alasan Penolakan (Jika ditolak):</label>
                      <input type="text" class="form-control" name="alasanTolakRealisasi" id="alasanTolakRealisasi">
                    </div>

                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
                  <button type="submit" class="btn btn-danger" value="0" name="submit">Tolak</button>
                  <button type="submit" class="btn btn-primary" value="1" name="submit">Setuju</button>
                </div>
                </form>
              </div>
            </div>
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
            <h3><b>Total BPU : <?php echo 'Rp. ' . number_format($row3['sumjum'], 0, '', ','); ?></b></h3>
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
              <div class="fetched-data"></div>
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
        $queryBank = mysqli_query($koneksi, "SELECT * FROM bank");
        ?>
        <div class="modal fade" id="tambahPenerima" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Tambah Data Penerima</h4>
                    </div>
                    <div class="modal-body">
                        <form id="form_add_receiver_bpu" method="post">
                            <input type="hidden" id="item_id" name="item_id">
                            <div class="form-group">
                                <label for="id_tb_user">Nama Penerima:</label>
                                <input type="text" class="form-control" name="nama_penerima" required>
                            </div>
                            <div class="form-group">
                                <label for="id_tb_user">Email Penerima:</label>
                                <input type="text" class="form-control" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="id_tb_user">Jabatan:</label>
                                <select class="form-control" name="jabatan" required>
                                    <option value="">Pilih Jabatan</option>
                                    <option value="Kepala Divisi">Kepala Divisi</option>
                                    <option value="Staff">Staff</option>
                                    <option value="Interviewer">Interviewer</option>
                                    <option value="Responder">Responder</option>
                                    <option value="Translater">Translater</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="id_tb_user">Nama Penerima Sesuai Rekening:</label>
                                <input type="text" class="form-control" name="nama_pemilik_rekening" required>
                            </div>
                            <div class="form-group">
                                <label for="status">Bank :</label>
                                <select class="form-control" name="kode_bank" required>
                                    <?php while ($item = mysqli_fetch_assoc($queryBank)) : ?>
                                        <option value="<?= $item['kodebank'] ?>"><?= $item['namabank'] ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <label for="status">Nomor Rekening :</label>
                            <div class="form-group">
                                <input type="text" name="nomor_rekening" class="form-control" required>
                            </div>

                            <button class="btn btn-primary" type="submit" name="submit" value="submit">Submit</button>

                        </form>
                    </div>
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


      <?php
      $noid = isset($_GET['no']) && $_GET['no'] ? $_GET['no'] : NULL;
      $waktu = isset($_GET['waktu']) && $_GET['waktu'] ? $_GET['waktu'] : NULL;
      ?>

      <script type="text/javascript">
        $(document).ready(function() {
          $('.umo_biaya_kode_id').select2();

          $('#fileInputVerifikasiBpu').change(function() {
            readURLVerifikasiBpu(this);
          })
          $('#fileInput').change(function() {
            readURL(this);
          })
          $('#fileInputPengajuanBpu').change(function() {
            readURLAjukaKembali(this);
          })

          $('#fileInputNewFileBpu').change(function() {
            readURLNewFileBpu(this);
          })
        })

        $('input[type=text][name=berita_transfer]').tooltip({
          placement: "top",
          trigger: "focus"
        });

        function readURLVerifikasiBpu(input) {
          if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
              $('#imageVerifikasiBpu').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]); // convert to base64 string
          }
        }

        function readURL(input) {
          if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
              $('#imageRealisasi').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]); // convert to base64 string
          }
        }

        function readURLAjukaKembali(input) {
          if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
              $('#imagePengajuanBpu').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]); // convert to base64 string
          }
        }

        function showModalAddReceiverBpu(idItem)
        {
            $('#tambahPenerima').modal('show')
            $('#form_add_receiver_bpu')[0].reset()
            let form = document.getElementById("form_add_receiver_bpu")
            let itemId = document.getElementById("item_id")
            form.action = "ReceiverBpu.php?action=save"
            itemId.value = idItem
        }

        $('#form_add_receiver_bpu').on('submit', function (e) {
            e.preventDefault();
            let form = $(this);
            const btnSubmit = $(this).find('button[type="submit"]');
            btnSubmit.prop('disabled', true);

            $.ajax({
                type: 'post',
                url: form[0].action,
                data: form.serialize(),
                success: function(data) {
                    let json = JSON.parse(data)
                    alert(json.message)
                    window.location.reload()
                }
            });
        })

        function readURLNewFileBpu(input) {
          if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
              $('#imageUploadNewFile').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]); // convert to base64 string
          }
        }

        $('#bpuDiajukan').keyup(function() {
          $('#hasilBpu').val($(this).val());
          $('#hasilBpuText').val(numberWithCommas($(this).val()));
        })

        $('input[name=pajak]').change(function() {
          const bpu = $('#bpuDiajukan').val();
          const actual = $('#hasilBpu').val();
          let result = 0;
          if ($(this).val() == 'pph21') {
            if ($(this).prop('checked')) {
              result = Math.round(parseInt(actual) - (0.05 * 0.5 * bpu));
            } else {
              result = Math.round(parseInt(actual) + (0.05 * 0.5 * bpu));
            }
          } else if ($(this).val() == 'pph4') {
            if ($(this).prop('checked')) {
              result = Math.round(parseInt(actual) - (0.1 * bpu));
            } else {
              result = Math.round(parseInt(actual) + (0.1 * bpu));
            }
          } else if ($(this).val() == 'pph23') {
            if ($(this).prop('checked')) {
              $('#pph23value').show();
              result = Math.round(parseInt(actual) - ($('#pph23value').val() * bpu));
              $('#pph23value').change(function() {
                result = Math.round(parseInt(actual) - ($(this).val() * bpu));

                $('#hasilBpu').val(result);
                $('#hasilBpuText').val(numberWithCommas(result));
              })
            } else {
              $('#pph23value').hide();
              result = Math.round(parseInt(actual) + ($('#pph23value').val() * bpu));
            }
          }
          $('#hasilBpu').val(result);
          $('#hasilBpuText').val(numberWithCommas(result));
        })

        $('#editVerifikasi1').click(function() {
          $('#bpuDiajukan').prop('readonly', false);
        });
        $('#editVerifikasi2').click(function() {
          $('#penerimaBpu').prop('readonly', false);
        });
        $('#editVerifikasi3').click(function() {
          $('#bankBpu').prop('readonly', false);
        });
        $('#editVerifikasi4').click(function() {
          $('#noRekBpu').prop('readonly', false);
        });


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

        //         function verifikasiBpu(no, waktu, term, totalBpu, namaPenerima, noRek, namaBank, file, jenis, ketPembayaran) {

        //           $('.form-rekening-sumber-pal-um').hide();
        //           $('.form-rekening-sumber-pal').hide();
        //           $('.form-rekening-sumber-kas').hide();

        //           $('#hasilBpu').prop('readonly', true);
        //           $('#penerimaBpu').prop('readonly', true);
        //           $('#bankBpu').prop('readonly', true);
        //           $('#noRekBpu').prop('readonly', true);

        //           $('#noBpu').val(no);
        //           $('#waktuBpu').val(waktu);
        //           $('#termBpu').val(term);
        //           $('#bpuDiajukan').val(totalBpu);
        //           $('#hasilBpu').val(totalBpu);
        //           $('#hasilBpuText').val(numberWithCommas(totalBpu));
        //           $('#penerimaBpu').val(namaPenerima);
        //           $('#bankBpu').val(namaBank);
        //           $('#noRekBpu').val(noRek);
        //           if (ketPembayaran) {
        //             $('#berita-transfer').val(ketPembayaran);
        //             $('#berita-transfer').attr('readonly', true);
        //           } else {
        //             $('#berita-transfer').attr('readonly', false);
        //           }

        //           $.ajax({
        //             type: 'post',
        //             url: 'ajax/ajax-jenis-pembayaran.php',
        //             data: {
        //               jenis: jenis
        //             },
        //             success: function(data) {
        //               $('#verifikasiBpuModal #metode_pembayaran').empty();

        //               const result = JSON.parse(data);
        //               if (parseInt(totalBpu) <= parseInt(result['max_transfer'])) {
        //                 html = `
        //       <option value="MRI PAL" selected>MRI PAL</option>`;
        //                 if (jenis == 'UM' || jenis == 'UM Burek') {
        //                   // console.log('here');
        //                   $('.form-rekening-sumber-pal-um').show();
        //                   $('input[name=status_sumber_rekening]').val('pal-um');
        //                 } else {
        //                   // console.log('here2');
        //                   $('.form-rekening-sumber-pal').show();
        //                   $('input[name=status_sumber_rekening]').val('pal');
        //                 }
        //               } else {
        //                 $('input[name=status_sumber_rekening]').val('kas');
        //                 html = `
        // <option selected value="MRI Kas">MRI Kas</option>`;
        //                 $('.form-rekening-sumber-kas').show();
        //               }

        //               $('#verifikasiBpuModal #metode_pembayaran').append(html);
        //             }
        //           });

        //           $('#image').attr('src', `uploads/${file}`)

        //           $('#verifikasiBpuModal').modal();
        //         }

        function numberWithCommas(x) {
          return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        function edit_budget(no, waktu, term) {
          // alert(noid+' - '+waktu);
          $.ajax({
            type: 'post',
            url: 'bayarbudget.php',
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

        function bpu_budget(no, waktu) {
          // alert(noid+' - '+waktu);
          $.ajax({
            type: 'post',
            url: 'bpu.php',
            data: {
              no: no,
              waktu: waktu
            },
            success: function(data) {
              $('#myModal .fetched-data').html(data); //menampilkan data ke dalam modal
              $('#myModal').modal();
            }
          });
        }

        function eksternal_finance(no, waktu) {
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

        // function realisasi(no, waktu, term) {
        //   // alert(noid+' - '+waktu);
        //   $.ajax({
        //     type: 'post',
        //     url: 'realisasi.php',
        //     data: {
        //       no: no,
        //       waktu: waktu,
        //       term: term
        //     },
        //     success: function(data) {
        //       $('.fetched-data').html(data); //menampilkan data ke dalam modal
        //       $('#myModal4').modal();
        //     }
        //   });
        // }
        function realisasi(no, waktu, term, jumlah, realisasi, uangkembali, tanggalrealisasi, sisa, file) {
          $('#noRealisasi').val(no);
          $('#waktuRealisasi').val(waktu);
          $('#termRealisasi').val(term);
          $('#hasil').val(jumlah);
          $('#hasilText').val(numberWithCommas(jumlah));
          $('#sisaRealisasi').val(sisa);
          $('#realisasi').val(realisasi);
          $('#uangkembali').val(uangkembali);
          $('#tanggalrealisasi').val(tanggalrealisasi);

          $('#realisasi').prop('max', sisa);

          $('#imageRealisasi').attr('src', `uploads/${file}`)

          $('#realiasiModal').modal();
        }

        function bayarmemo(no, waktu) {
          // alert(noid+' - '+waktu);
          $.ajax({
            type: 'post',
            url: 'bayarmemo.php',
            data: {
              no: no,
              waktu: waktu
            },
            success: function(data) {
              $('.fetched-data').html(data); //menampilkan data ke dalam modal
              $('#myModal2').modal();
            }
          });
        }

        function bayarBpu(no, waktu, term) {
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
      </script>

</body>

</html>