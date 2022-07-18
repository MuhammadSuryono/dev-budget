<?php
error_reporting(0);
session_start();

require "application/config/database.php";
require_once "application/config/helper.php";
$helper = new Helper();

$con = new Database();
$koneksi = $con->connect();
$con->load_database($koneksi);

$con->set_name_db(DB_TRANSFER);
$con->init_connection();
$koneksiBridge = $con->connect();

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
            <!--<li><a href="summary.php">Summary</a></li>
              <li><a href="bank.php">Bank</a></li>-->
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

  <!-- <div class="container"> -->

  <?php
  
  $code = $_GET['code'];
  $select = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE noid='$code'");
  $d = mysqli_fetch_assoc($select);
  $totalbudget      = $d['totalbudget'];
  $totalbudgetnow   = $d['totalbudgetnow'];


  $queryUser = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE id_user = '$_SESSION[id_user]'");
  $user = mysqli_fetch_assoc($queryUser);
  if (@unserialize($user['hak_button'])) {
    $buttonAkses = unserialize($user['hak_button']);
  } else {
    $buttonAkses = [];
  }

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

        <li role="presentation"   class="active">
          <a href="#budget" id="budget-tab" role="tab" data-toggle="tab" aria-controls="budget" aria-expanded="true">Budget</a>
        </li>

        <li role="presentation">
          <a href="#history" role="tab" id="history-tab" data-toggle="tab" aria-controls="history">History</a>
        </li>

        <li role="presentation">
          <a href="#rincian" role="tab" id="rincian-tab" data-toggle="tab" aria-controls="rincian">Rincian BPU</a>
        </li>

          <?php if ($d['jenis'] == 'Rutin') { ?>
              <li role="presentation" >
                  <a href="#pengajuanKas" role="tab" id="pengajuanKas-tab" data-toggle="tab" aria-controls="rincian">Pengajuan Kas</a>
              </li>
          <?php } ?>

      </ul>

      <div id="myTabContent" class="tab-content">
        <!-- Tab -->
          <?php if ($d['jenis'] == 'Rutin') {
              $dataPengajuanKas = $con->select('a.*, b.flow_name')
                  ->from('pengajuan_kas a')
                  ->join('flow_pengajuan_kas b', 'a.status = b.status_code')
                  ->where('a.id_pengajuan_budget', '=', $d['noid'])->get();

              if ($dataPengajuanKas == null) {
                  $con->insert('pengajuan_kas')->set_value_insert('id_pengajuan_budget', $d['noid'])->set_value_insert('term', 1)->save_insert();
              }

              $dataPengajuanKas = $con->select('a.*, b.flow_name')
                  ->from('pengajuan_kas a')
                  ->join('flow_pengajuan_kas b', 'a.status = b.status_code')
                  ->where('a.id_pengajuan_budget', '=', $d['noid'])
                  ->order_by('term', 'asc')->get();
              ?>
              <div role="tabpanel" class="tab-pane fade in" id="pengajuanKas" aria-labelledby="pengajuanKas-tab">
                  <div class="container-fluid" style="margin: 20px">
                      <ul id="pengajuanKasItemTab" class="nav nav-tabs" role="tablist">
                          <?php
                          $alreadyActive = false;
                          foreach($dataPengajuanKas as $kas) {
                              $active = $kas['status'] != 1 && $alreadyActive == false ? 'active' : '';
                              if ($active == 'active' && $alreadyActive == false) $alreadyActive = true;
                              ?>
                              <li role="presentation" class="<?= $active ?>">
                                  <a href="#tab<?= $kas['id'] ?>" role="tab" id="<?= $kas['id'] ?>-tab" data-toggle="tab" aria-controls="rincian">Term <?= $kas['term'] ?></a>
                              </li>
                          <?php } ?>
                      </ul>
                      <div id="pengajuanKasItemTabContent" class="tab-content">
                          <?php
                          $alreadyActive = false;
                          foreach($dataPengajuanKas as $kas) {
                              $active = $kas['status'] != 1 && $alreadyActive == false ? 'active' : '';
                              if ($active == 'active' && $alreadyActive == false) $alreadyActive = true;

                              $rekening = $con->select("b.id_rekening, a.rekening, a.bank, a.type_kas, a.id_kas")
                                  ->from('pengajuan_kas_item b')
                                  ->join('develop.kas a', 'a.id_kas = b.id_rekening')
                                  ->where('b.id_pengajuan_budget', '=', $d['noid'])
                                  ->where('b.term', '=', $kas['term'])
                                  ->group_by('b.id_rekening')->get();

                              $dataPengajuan = $con->select("p.*, s.rincian as rincianItem, s.total as totalBudget, s.no as noItem")
                                  ->from('pengajuan_kas_item p')
                                  ->join('selesai s', 's.id = p.item_id')
                                  ->where('p.term', '=', $kas['term'])
                                  ->where('p.id_pengajuan_budget', '=', $d['noid'])->get();
                              ?>
                              <div role="tabpanel" class="tab-pane fade in <?= $active ?>" id="tab<?= $kas['id'] ?>" aria-labelledby="<?= $kas['id'] ?>-tab">
                                  <div class="container-fluid">
                                      <?php
                                      if ($kas['status'] == 0) {
                                          echo '<div class="alert alert-warning" role="alert" style="">
                              Anda belum membuat pengajuan KAS, Silahkan buat pengajuan dan Ajukan
                            </div>';
                                      }

                                      if ($kas['status'] != 0 && !in_array($kas['status'], [110,220,330]) && $kas['status'] != 1) {
                                          echo '<div class="alert alert-warning" role="alert" style="">
                                  Status Pengajuan anda '.$kas['flow_name'].'
                                </div>';
                                      }

                                      if (in_array($kas['status'], [110,220,330])) {
                                          echo '<div class="alert alert-danger" role="alert" style="">
                                  Pengajuan anda telah di tolak oleh <b>'.$kas["reject_by"].'</b>, dengan keterangan <b>'.$kas["description"].'</b>  Silahkan perbaiki data pengajuan anda
                                </div>';
                                      }

                                      if ($kas['status'] == 1) {
                                          echo '<div class="alert alert-success" role="alert" style="">
                                      Pengajuan anda telah di Setujui oleh <b>'.$kas["approval_by"].'</b>
                                </div>';
                                      }
                                      ?>
                                      <?php if ($dataPengajuan != null) { ?>
                                          <a href="print-pengajuan-kas.php?code=<?= $d['noid'] ?>&term=<?= $kas['term']?>" target="_blank" class="btn btn-warning btn-sm"><i class="fa fa-print"></i> Cetak Pengajuan</a>
                                      <?php } ?>
                                      
                                      <?php
                                      if(in_array($kas['status'], [22])) { ?>
                                          <button class="btn btn-danger btn-sm" onclick="tolakPengajuanKas('<?= $kas['id'] ?>')" ><i class="fa fa-plus"></i> Penolakan Check 2</button>
                                          <button class="btn btn-success btn-sm" onclick="validasiPengajuanKas('<?= $kas['id'] ?>')" ><i class="fa fa-plus"></i> Validasi Check 2</button>
                                      <?php } ?>

                                      <div class="table-responsive" style="margin-top: 10px">
                                          <table class="table table-hover table-striped table-bordered">
                                              <thead class="warning">
                                              <tr class="warning">
                                                  <th style="width:5%">No Item</th>
                                                  <th >Tanggal Jatuh Tempo</th>
                                                  <th >Keterangan</th>
                                                  <th >All Budget</th>
                                                  <?php
                                                  foreach($rekening as $rek) {
                                                      $type_kas = 'KAS';
                                                      if ($rek['type_kas'] == 'mri-pall') { $type_kas = 'PALL'; }

                                                      $bank = 'Bank Mandiri';
                                                      if ($rek['bank'] == 'CENAIDJA') { $bank = 'Bank BCA'; }
                                                      ?>
                                                      <th><?= $bank ?> (<?= $type_kas ?>)<br><?= $rek['rekening'] ?></th>
                                                  <?php } ?>
                                              </tr>
                                              </thead>
                                              <tbody>
                                              <?php

                                              foreach($dataPengajuan as $key => $value) {
                                                  ?>
                                                  <tr>
                                                      <td><?= $value['noItem'] ?></td>
                                                      <td><?= $value['jatuh_tempo'] ?></td>
                                                      <td><?= $value['rincianItem'] ?></td>
                                                      <td>Rp. <?= number_format($value['totalBudget']) ?></td>

                                                      <?php
                                                      foreach($rekening as $rek) {
                                                          ${"term1" . $rek['rekening']} = 0;

                                                          if ($value['id_rekening'] == $rek['id_kas']) {
                                                              ${"term1" . $rek['rekening']} = $value['total_pengajuan'];
                                                          }
                                                          ?>
                                                          <td>Rp. <?= number_format(${"term1" . $rek['rekening']}, 0, ',', '.') ?></td>
                                                          <?php

                                                          ${"totalterm1" . $rek['rekening']} += ${"term1" . $rek['rekening']};
                                                      } ?>
                                                  </tr>
                                              <?php }
                                              if ($rekening != NULL) {
                                                  ?>
                                                  <tr class="warning">
                                                      <td colspan="4">Total</td>
                                                      <?php foreach ($rekening as $rek) { ?>
                                                          <td>Rp. <?= number_format(${"totalterm1" . $rek['rekening']}, 0, ',', '.') ?></td>
                                                      <?php } ?>
                                                  </tr>
                                              <?php } ?>

                                              </tbody>
                                          </table>
                                      </div>
                                  </div>
                              </div>
                          <?php } ?>
                      </div>

                      <div class="modal fade" id="pengajuanKasModal" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="pengajuanKasModalLabel" aria-hidden="true">
                          <div class="modal-dialog modal-lg" role="document">
                              <div class="modal-content">
                                  <div class="modal-header">
                                      <h5 class="modal-title" id="pengajuanKasModalLabel">Pengajuan Modal</h5>
                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                          <span aria-hidden="true">&times;</span>
                                      </button>
                                  </div>
                                  <form id="formPengajuanKas">
                                      <div class="modal-body" id="bodyPengajuanKas">
                                      </div>
                                  </form>
                              </div>
                          </div>
                      </div>

                      <div class="modal fade" id="konfirmasiPengajuan" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="konfirmasiPengajuanLabel" aria-hidden="true">
                          <div class="modal-dialog" role="document">
                              <div class="modal-content">
                                  <div class="modal-header">
                                      <h5 class="modal-title" id="konfirmasiPengajuanLabel">Konfirmasi Pengajuan</h5>
                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                          <span aria-hidden="true">&times;</span>
                                      </button>
                                  </div>
                                  <div class="modal-body" id="bodyPengajuanKas">
                                      <input type="hidden" value="" id="idPengajuanKas">
                                      Apakah anda yakin ingin mengajukan kas ini ?
                                  </div>
                                  <div class="modal-footer">
                                      <button type="button" class="btn btn-primary" id="btnAjukanKas">Iya, Ajukan</button>
                                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                  </div>
                              </div>
                          </div>
                      </div>

                      <div class="modal fade" id="konfirmasiPenolakan" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="konfirmasiPenolakanLabel" aria-hidden="true">
                          <div class="modal-dialog" role="document">
                              <div class="modal-content">
                                  <div class="modal-header">
                                      <h5 class="modal-title" id="konfirmasiPenolakanLabel">Konfirmasi Penolakan</h5>
                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                          <span aria-hidden="true">&times;</span>
                                      </button>
                                  </div>
                                  <div class="modal-body" id="bodyPengajuanKas">
                                      Apakah anda yakin ingin menolak pengajuan kas ini ?
                                      <input id="descriptionReject" class="form-control" placeholder="Keterangan Penolakan">
                                  </div>
                                  <div class="modal-footer">
                                      <button type="button" class="btn btn-danger" id="btnTolakPengajuanKas">Iya, Tolak</button>
                                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                  </div>
                              </div>
                          </div>
                      </div>

                      <div class="modal fade" id="konfirmasiApprove" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="konfirmasiApproveLabel" aria-hidden="true">
                          <div class="modal-dialog" role="document">
                              <div class="modal-content">
                                  <div class="modal-header">
                                      <h5 class="modal-title" id="konfirmasiApproveLabel">Konfirmasi Approve Pengajuan</h5>
                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                          <span aria-hidden="true">&times;</span>
                                      </button>
                                  </div>
                                  <div class="modal-body" id="bodyPengajuanKas">
                                      Apakah anda yakin ingin menyetujui pengajuan kas ini ?
                                  </div>
                                  <div class="modal-footer">
                                      <button type="button" class="btn btn-primary" id="btnApproveKas">Iya, Setujui</button>
                                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          <?php } ?>
        <div role="tabpanel" class="tab-pane fade in active" id="budget" aria-labelledby="home-tab">

          <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
            <div class="panel-body no-padding">
              <table class="table table-striped table-bordered tableFixHead">
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
                  $checkName = [];
                  $waktu = $d['waktu'];
                  $sql = mysqli_query($koneksi, "SELECT * FROM selesai WHERE waktu='$waktu' ORDER BY no");
                  $totalPembayaran = 0;
                  if ($sql->num_rows == 0) {
                      echo "<tr><td colspan='12' align='center'><b>Tidak ada data</b><br/>Jika dipengajuan data item sudah terisi namun setelah budget disetujui item menjadi hilang. <br/>Silahkan klik button dibawah untuk mensinkronkan ulang item budget anda <br/><button class='btn btn-primary mb-5' data-waktu='$waktu' id='btn-pull-item'>Tarik Data Item Budget</button></td></tr>";
                  }
                  while ($a = mysqli_fetch_array($sql)) {
                    if (!in_array($a["rincian"], $checkName)) :
                        $querySumTotalBayar = mysqli_query($koneksi, "SELECT SUM(CASE WHEN jumlah > 0 THEN jumlah ELSE pengajuan_jumlah END) as total_bayar FROM bpu where no = '$a[no]' AND waktu = '$waktu' AND is_locked = 0 AND (status_pengajuan_bpu != 2 OR status_pengajuan_bpu IS NULL)");
                        $totalBayar = mysqli_fetch_assoc($querySumTotalBayar);
                        $totalPembayaran = $totalBayar['total_bayar'];
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
                                      echo "<li>$value[nama_penerima] ($value[jabatan]) - <span class='text-center' title='$title'>$iconValidate</span> <a href='$value[path]' target='_blank'><i class='fa fa-file'></i></a></li>";
                                  }
                                  echo "</ul>";
                              }
                              ?>
                          </td>
                        <td><?php echo 'Rp. ' . number_format($a['harga'], 0, '', ','); ?></td>
                        <td><?php echo $a['quantity']; ?></td>
                        <td><?php echo 'Rp. ' . number_format($a['total'], 0, '', ','); ?></td>
                          <td>Rp. <?= number_format($totalPembayaran) ?></td>

                        <!-- Sisa Pembayaran -->
                        <?php
                        $no = $a['no'];
                        $waktu = $a['waktu'];
                        $pilihtotal = mysqli_query($koneksi, "SELECT total FROM selesai WHERE no='$no' AND waktu='$waktu'");
                        $aw = mysqli_fetch_assoc($pilihtotal);
                        $hargaah = $aw['total'];
                        $query = "SELECT sum(jumlah) AS sum FROM bpu WHERE no='$no' AND waktu='$waktu' and is_locked = 0";
                        $result = mysqli_query($koneksi, $query);
                        $row = mysqli_fetch_array($result);
                        $total = $row[0];
                        $query16 = "SELECT sum(uangkembali) AS sum FROM bpu WHERE no='$no' AND waktu='$waktu'";
                        $result16 = mysqli_query($koneksi, $query16);
                        $row16 = mysqli_fetch_array($result16);
                        $total16 = $row16[0];

                        $jadinya = $hargaah - $totalPembayaran;
                        ?>
                        <td><?php echo 'Rp. ' . number_format($jadinya, 0, '', ','); ?></td>
                        <!-- //Sisa Pembayaran -->

                        <td>
                          <!-- Tombol Eksternal -->
                          <?php 
                          if  ($d['jenis'] != 'B1') :
                            if ($a['status'] == 'UM' || $a['status'] == 'Pulsa' || $a['status'] == 'Biaya External' || $a['status'] == 'Biaya' || $a['status'] == 'UM Burek') {
                          ?>
                              <button type="button" class="btn btn-default btn-small" onclick="ajukan_bpu('<?php echo $no; ?>','<?php echo $waktu; ?>')">BPU</button>
                              <br /><br />
                              <?php
                            } else if ($a['status'] == 'Vendor/Supplier' || $a['status'] == 'Honor Eksternal') {
                              if (in_array("eksternal_bpu", $buttonAkses) && $jadinya > 0) :
                              ?>
                                <button type="button" class="btn btn-success btn-small" onclick="eksternal('<?php echo $no; ?>','<?php echo $waktu; ?>')">Eksternal</button>
                                <br /><br />
                            <?php
                              endif;
                            }
                          endif;

                          if ($d['jenis'] == 'Rutin' || $d['jenis'] == 'Non Rutin') {
                            ?>
                            <button type="button" class="btn btn-default btn-small" onclick="edit_row('<?php echo $no; ?>','<?php echo $waktu; ?>')">Edit</button>
                            <br /><br />
                            <button type="button" class="btn btn-danger btn-small" onclick="hapus_row('<?php echo $no; ?>','<?php echo $waktu; ?>')">Hapus</button>
                          <?php
                          } ?>

                        </td>
                        <?php
                        $arrCheck = [];
                        $liatbayar = mysqli_query($koneksi, "SELECT * FROM bpu WHERE waktu='$waktu' AND no='$no' AND (status_pengajuan_bpu != 2 or status_pengajuan_bpu is NULL)");
                        if (mysqli_num_rows($liatbayar) == 0) {
                          echo "";
                        } else {
                          while ($bayar = mysqli_fetch_array($liatbayar)) {
                            $queryTotal = mysqli_query($koneksi, "SELECT SUM(jumlah) AS jumlah_total, SUM(pengajuan_jumlah) AS jumlah_pengajuan FROM bpu WHERE waktu='$waktu' AND no='$no' AND term = '$bayar[term]'");
                            $total = mysqli_fetch_assoc($queryTotal);

                            if (!in_array($waktu . $no . $bayar['term'], $arrCheck)) :

                              $checkMetodePembayaran = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS count FROM bpu WHERE waktu='$waktu' AND no='$no' AND term = '$bayar[term]' AND (metode_pembayaran = 'MRI Kas' OR metode_pembayaran = '')"));

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
                              $noidbpu        = $bayar['noid'];
                              $jumlbayar      = $bayar['jumlah'];
                              $pengajuanJumlah      = $bayar['pengajuan_jumlah'];
                              $tglbyr         = $bayar['tglcair'];
                              $statusbayar    = $bayar['status'];
                              $persetujuan = $bayar['persetujuan'];
                              $novoucher = $bayar['novoucher'];
                              $termm              = $bayar['term'];
                              $tanggalbayar = $bayar['tanggalbayar'];
                              $pengaju = $bayar['pengaju'];
                              $userMengetahui      = $bayar['acknowledged_by'];
                              $userCheck          = $bayar['checkby'];
                              $userApprove         = $bayar['approveby'];
                              $userPembayar         = $bayar['pembayar'];
                              $divisi2 = $bayar['divisi'];
                              $namabank = $bayar['namabank'];
                              $norek = $bayar['norek'];
                              $namapenerima = $bayar['namapenerima'];
                              $alasan = $bayar['alasan'];
                              $realisasi    = $bayar['realisasi'];
                              $uangkembali  = $bayar['uangkembali'];
                              $tanggalrealisasi = $bayar['tanggalrealisasi'];
                              $waktustempel = $bayar['waktustempel'];
                              $pembayar = $bayar['pembayar'];
                              $tglcair = $bayar['tglcair'];
                              $statusbpu        = $bayar['statusbpu'];
                              $pengajuan_realisasi = $bayar['pengajuan_realisasi'];
                              $pengajuan_uangkembali = $bayar['pengajuan_uangkembali'];
                              $pengajuan_tanggalrealisasi  = $bayar['pengajuan_tanggalrealisasi'];
                              $statusPengajuanRealisasi = $bayar['status_pengajuan_realisasi'];
                              // $pengajuan_realiasi = $bayar['pengajuan_realiasi'];
                              $jumlahjadi = $jumlbayar - $uangkembali;
                              $term = $bayar['term'];
                              $fileupload       = $bayar['fileupload'];
                              $fileuploadRealisasi       = $bayar['fileupload_realisasi'];
                              $statusPengajuanBpu = $bayar['status_pengajuan_bpu'];
                              $batasTanggalBayar = $bayar['batas_tanggal_bayar'];
                              // $metodePembayaran = $bayar['metode_pembayaran'];
                              $ketPembayaran = $bayar['ket_pembayaran'];
                                $termStkb     = $bayar['termstkb'];

                              $bankAccountName       = $bayar['bank_account_name'];

                              $tglcair = $tglcair == "0000-00-00" ? "-" : $tglcair;

                              $queryBank = mysqli_query($koneksi, "SELECT namabank FROM bank WHERE kodebank = '$namabank'");
                              $dataBank = mysqli_fetch_assoc($queryBank);
                              $bank = $dataBank['namabank'];

                              $queryTransfer = mysqli_query($koneksiBridge, "SELECT bank, jadwal_transfer FROM data_transfer WHERE noid_bpu = '$noidbpu'");
                              $dataTransfer = mysqli_fetch_assoc($queryTransfer);

                              $jadwalTransfer = $dataTransfer['jadwal_transfer'];


                              $noStkb       = ($bayar['nomorstkb']) ? $bayar['nomorstkb'] : '-';
                              $kembreal         = $realisasi + $uangkembali;
                              $sisarealisasi    = $jumlbayar - $kembreal;


                              $selstat = mysqli_query($koneksi, "SELECT status FROM selesai WHERE waktu='$waktu' AND no='$no'");
                              $ss = mysqli_fetch_assoc($selstat);
                              $exin = $ss['status'];
                              $color = '#fff';

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

                              $nominalPajak = $bayar['nominal_pajak'] == null ? 0 : $bayar['nominal_pajak'];

                              // if ($statusPengajuanRealisasi == 1) {
                              //   $color = '#8aad70';
                              // } else if ($statusPengajuanRealisasi == 2) {
                              //   $color = '#ff3b3b';
                              // } else if ($statusPengajuanRealisasi == 3) {
                              //   $color = '#9932CC';
                              // }
                                $isLockedStyle = $bayar['is_locked'] == true ? 'filter: blur(1px); cursor: not-allowed; background: url("https://www.freeiconspng.com/thumbs/lock-icon/lock-icon-11.png") no-repeat; background-size: contain; background-position-y: center;':'';

                              echo "<td bgcolor=' $color ' style='border: 1px black solid;$isLockedStyle'>";
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
echo "Nominal Pajak :<b>Rp. " .number_format($nominalPajak) . " (".$bayar['jenis_pajak'].")";
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
                                echo "File Upload : <br>";
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
                                echo "<i class='far ".$statusCheckApproval  ."'></i> Approval (" . (!is_null($userApprove) ? $userApprove : '-') . ")";
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
                              if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)' || $persetujuan == 'Disetujui oleh sistem') && ($statusbayar == 'Telah Di Bayar' || $statusbayar == 'Realisasi (Finance)' || $statusbayar == 'Realisasi (Direksi)')) {
                                echo "Uang Kembali :<br><b> Rp. " . number_format($uangkembali, 0, '', ',');
                                echo "</b><br/>";
                              }


                              if ($persetujuan == 'Pending' || $persetujuan == 'Belum Disetujui' && $d['jenis'] == 'Rutin' && ($statusPengajuanBpu == 0 || !$statusPengajuanBpu)) {
                                echo "Komentar : <br><b> $alasan ";
                                echo "</b><br/>";

                        ?>

                                <button type="button" class="btn btn-success btn-small" onclick="setujuiBpu('<?php echo $no; ?>','<?php echo $waktu; ?>', '<?= $term ?>')">Setujui</button>
                                </br>
                                <button type="button" class="btn btn-warning btn-small" onclick="editharga('<?php echo $noidbpu; ?>','<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Edit</button>
                                </br>
                                <button type="button" class="btn btn-danger btn-small" onclick="hapus_bpu('<?php echo $no; ?>','<?php echo $waktu; ?>')">Hapus</button>
                              <?php
                              } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)' || $persetujuan == 'Disetujui oleh sistem') and $statusbayar == 'Belum Di Bayar' && $d['jenis'] == 'Rutin') {
                              ?>
                                <button type="button" class="btn btn-warning btn-small" onclick="editharga('<?php echo $noidbpu; ?>','<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Edit</button>
                                </br>
                                <button type="button" class="btn btn-danger btn-small" onclick="hapus_bpu('<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Hapus</button>
                              <?php
                              } else if ($persetujuan == 'Pending' || $persetujuan == 'Belum Disetujui' && $d['jenis'] == 'B2' && ($statusPengajuanBpu == 0 || !$statusPengajuanBpu)) { ?>
                                <?php if ($total['jumlah_total'] <= $setting['plafon'] && $bayar['is_locked'] == 0) : ?>
                                  <button type="button" class="btn btn-success btn-small" onclick="setujuiBpu('<?php echo $no; ?>','<?php echo $waktu; ?>', '<?= $term ?>')">Setujui</button>
                                  </br>
                                <?php endif; ?>
                              <?php  } else {
                              ?>
                                <!-- <button type="button" class="btn btn-warning btn-small" onclick="editharga('<?php echo $noidbpu; ?>','<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Edit</button>
                              </br>
                              <button type="button" class="btn btn-danger btn-small" onclick="hapus_bpu('<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Hapus</button> -->
                              <?php
                              }

                              if ($d['jenis'] == 'Rutin') { ?>
                                <!-- <button type="button" class="btn btn-warning btn-small" onclick="editharga('<?php echo $noidbpu; ?>','<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Edit</button> -->
                              <?php
                              }

                              if ($statusPengajuanRealisasi == 1) {
                              ?>
                                <!-- <button type="button" style="margin-bottom: 5px; margin-top: 10px;" class="btn btn-info" onclick="realisasi('<?php echo $no; ?>','<?php echo $waktu; ?>', '<?= $termm ?>', '<?= $jumlbayar ?>', '<?= $pengajuan_realisasi ?>', '<?= $pengajuan_uangkembali ?>', '<?= $pengajuan_tanggalrealisasi ?>', '<?= $sisarealisasi ?>' , '<?= $fileuploadRealisasi ?>')">Verifikasi Realisasi</button> -->
                              <?php
                              }
                              if ($statusPengajuanBpu == 1  && in_array("verifikasi_bpu", $buttonAkses) && !$isEksternalProcess && $bayar['is_locked'] == 0) : ?>
                                <br>
                                <button type="button" style="margin-bottom: 5px; margin-top: 10px;" class="btn btn-info" onclick="verifikasiBpu('<?php echo $no; ?>','<?php echo $waktu; ?>', '<?= $term ?>')">Verifikasi BPU</button>
                                <?php
                              endif;

                              if (($a['status'] == 'UM' || $a['status'] == 'UM Burek' || $a['status'] == 'Finance' || $a['status'] == 'Pulsa' || $a['status'] == 'Biaya' || $a['status'] == 'Biaya Lumpsum' || $a['status'] == 'Vendor/Supplier' || $a['status'] == 'Honor Eksternal') && ($statusbayar == 'Belum Di Bayar' && ($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)' || $persetujuan == 'Disetujui oleh sistem')) && $showButtonBayar && $metodePembayaran != "MRI PAL") {
                                if (!is_null($batasTanggalBayar)) {
                                  if ((int) date('H') < 15) $thisDay = date('Y-m-d');
                                  else $thisDay = date('Y-m-d', strtotime('+ 1 days'));
                                  if ($thisDay >= $batasTanggalBayar) {
                                ?>
                                    <button style="margin:3px 0" type="button" class="btn btn-info btn-small" data-toggle="modal" data-target="#aksesBayarModal" id="btn-akses-bpu" onclick="aksesBpu('<?php echo $no; ?>','<?php echo $waktu; ?>', '<?= $termm ?>', '<?= $code ?>')">Buka Akses Bayar</button>
                                  <?php }
                                }
                              }

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

            <?php
            $totalBudgetBerubah = 0;
            $totalBudgetKeseluruhan = $totalbudget;
            if($totalbudgetnow - $totalbudget != 0){
                ?>
                <div class="row">
                    <div class="col-xs-3">Total Budget Yang Disetujui</div>
                    <?php
                    ?>
                    <div class="col-xs-3">: <b><?php echo 'Rp. ' . number_format($totalBudgetKeseluruhan, 0, '', ','); ?></b></div>
                </div>
                <div class="row">
                    <div class="col-xs-3">Total Perubahan Budget</div>
                    <?php
                    $totalBudgetBerubah = max($totalbudgetnow - $totalbudget, 0);
                    ?>
                    <div class="col-xs-3">: <b><?php echo 'Rp. ' . number_format($totalBudgetBerubah, 0, '', ','); ?></b></div>
                </div>
            <?php } ?>
            <div class="row">
                <div class="col-xs-3">Total Budget Keseluruhan</div>
                <?php
                $totalBudgetKeseluruhan = $totalBudgetKeseluruhan + $totalBudgetBerubah;
                ?>
                <div class="col-xs-3">: <b><?php echo 'Rp. ' . number_format($totalBudgetKeseluruhan, 0, '', ','); ?></b></div>
            </div>

            <div class="row">
                <div class="col-xs-3">
                    <font color="#1bd34f">Total Biaya dan Uang Muka
                        <hr />
                </div>

                <?php
                $useduangkemb = mysqli_query($koneksi, "SELECT SUM(total) AS sumused FROM selesai WHERE waktu='$waktu' AND uangkembaliused='Y'");
                $uak = mysqli_fetch_array($useduangkemb);
                $uangkembaliused = $uak['sumused'];

                $query3 = "SELECT SUM(CASE WHEN jumlah > 0 THEN jumlah ELSE pengajuan_jumlah END) as penggunaan, SUM(uangkembali) as uangkembali FROM bpu WHERE waktu = '$waktu' AND is_locked = 0  AND (status_pengajuan_bpu != 2 OR status_pengajuan_bpu IS NULL)";
                $result3 = mysqli_query($koneksi, $query3);
                $penggunaan = mysqli_fetch_array($result3);

                $query4 = "SELECT sum(jumlah) AS sumi FROM bpu WHERE waktu='$waktu' AND persetujuan='Disetujui (Direksi)' AND status='Belum Di Bayar'";
                $result4 = mysqli_query($koneksi, $query4);
                $row3 = mysqli_fetch_array($result4);

                $penggunaanBudget = (($penggunaan['penggunaan'] - $penggunaan['uangkembali']) + $uangkembaliused) - $row3['sumi'];

                $queryTotalUangKembali = "SELECT SUM(uangkembali) as uangkembali FROM bpu WHERE waktu = '$waktu'";
                $resUangKembali = mysqli_query($koneksi, $queryTotalUangKembali);
                $dataUangKembali = mysqli_fetch_array($resUangKembali);
                ?>

                <div class="col-xs-3">: <b><?php echo 'Rp. ' . number_format($penggunaanBudget, 0, '', ','); ?></font></b></div>
            </div>


            <!-- Yang belum Bayar -->
            <div class="row">
                <div class="col-xs-3">
                    <font color='#f23f2b'>Sisa Budget
                </div>
                <?php
                $belumbayar = $totalBudgetKeseluruhan - $penggunaanBudget - $row3['sumi'] ;
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
              <label for="alasanTolakRealisasi" class="control-label">Alasan Penolakan (Jika ditolak):</label>
              <input type="text" class="form-control" name="alasanTolakRealisasi" id="alasanTolakRealisasi">
            </div>

            <div class="form-group">
              <label class="control-label">Uploaded File</label>
              <img class="img-responsive" style="display: block; margin-left: auto;  margin-right: auto; background-repeat: no-repeat; background-size: cover; background-attachment: fixed;" src="" alt="" id="imageRealisasi">
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

  <div class="modal fade" id="myModal" role="dialog">
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

  <div class="modal fade" id="myModal2" role="dialog">
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

  <div class="modal fade" id="myModal5" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h3 class="modal-title text-center">Edit</h3>
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

  <div class="modal fade" id="aksesBayarModal" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h3 class="modal-title text-center">Buka Akses Pembayaran</h3>
        </div>
        <div class="modal-body">
          <form action="akses-bayar-bpu.php" method="post">
            <input type="hidden" name="term" id="termAkses">
            <input type="hidden" name="no" id="noAkses">
            <input type="hidden" name="waktu" id="waktuAkses">
            <input type="hidden" name="code" id="codeAkses">

            <h5>Apa anda yakin ingin membuka akses pembayaran terhadap BPU?</h5>
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary" type="submit" name="submit">Buka Akses</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
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

  <?php
  $noid = isset($_GET['no']) && $_GET['no'] ? $_GET['no'] : NULL;
  $waktu = isset($_GET['waktu']) && $_GET['waktu'] ? $_GET['waktu'] : NULL;
  $term = isset($_GET['term']) && $_GET['term'] ? $_GET['term'] : NULL;
  ?>

  <script type="text/javascript">
      const btnPullItemBudget = document.getElementById('btn-pull-item')
      $('#btn-pull-item').click(function(e) {
          e.target.disabled = true;
          e.target.innerHTML = "Sedang menarik data..."
          $.ajax({
              type: 'post',
              url: 'PullItemBudget.php',
              data: {
                  waktu: e.target.dataset.waktu,
              },
              success: function(data) {
                  setInterval(function() {
                      e.target.disabled = false;
                      e.target.innerHTML = "Tarik Data Item Budget"
                      if (data === "OK") window.location.reload()
                  }, 3000)

              }
          });
      })
    $(document).ready(function() {
      $('.umo_biaya_kode_id').select2();

      $('#fileInputVerifikasiBpu').change(function() {
        readURLVerifikasiBpu(this);
      })

      $('#fileInput').change(function() {
        readURL(this);
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

    function readURLNewFileBpu(input) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
          $('#imageUploadNewFile').attr('src', e.target.result);
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

    function aksesBpu(no, waktu, term, code) {
      $('#noAkses').val(no);
      $('#waktuAkses').val(waktu);
      $('#termAkses').val(term);
      $('#codeAkses').val(code);
    }

    function verifikasiBpu(no, waktu, term, totalBpu, namaPenerima, noRek, namaBank, file, jenis, ketPembayaran) {
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
    //       $('.fetched-data').html(data); //menampilkan data ke dalam modal
    //       $('#myModal4').modal();
    //     }
    //   });
    // }
    function realisasi(no, waktu, term, jumlah, realisasi, uangkembali, tanggalrealisasi, sisa, file) {
      console.log(sisa);
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

    function edit_row(no, waktu) {
      // alert(noid+' - '+waktu);
      $.ajax({
        type: 'post',
        url: 'editrow.php',
        data: {
          no: no,
          waktu: waktu
        },
        success: function(data) {
          $('.fetched-data').html(data); //menampilkan data ke dalam modal
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

    function ajukan_bpu(no, waktu) {
      // alert(noid+' - '+waktu);
      $.ajax({
        type: 'post',
        url: 'bpu.php',
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

  <script>
      const params = new Proxy(new URLSearchParams(window.location.search), {
          get: (searchParams, prop) => searchParams.get(prop),
      });
      $(document).ready(function(){
          $('#btnTolakPengajuanKas').on('click', function () {
              $('#btnTolakPengajuanKas').prop('disabled', true);
              $.ajax({
                  type: 'post',
                  url: `PengajuanUangKas.php?code=${params.code}&idPengajuanKas=${$('#idPengajuanKas').val()}&action=rejectRequest`,
                  data: {
                      description: $('#descriptionReject').val()
                  },
                  success: function(data) {
                      try {
                          let resp = JSON.parse(data)
                          if (resp.status == true) {
                              alert("Berhasil menyimpan penolakan pengajuan")
                              $('#konfirmasiPenolakan').modal('hide')
                              window.location.reload()
                          } else {
                              $('#btnTolakPengajuanKas').prop('disabled', false);
                              alert("Tidak bisa menyimpan penolakan pengajuan. Terjadi kesalahan ketika menyimpan")
                          }
                      } catch (e) {
                          $('#btnTolakPengajuanKas').prop('disabled', false);
                          alert("Tidak bisa menyimpan penolakan pengajuan. Terjadi kesalahan ketika menyimpan")
                      }
                  }
              });
          })

          $('#btnApproveKas').on('click', function () {
              $('#btnApproveKas').prop('disabled', true);
              $.ajax({
                  type: 'post',
                  url: `PengajuanUangKas.php?code=${params.code}&idPengajuanKas=${$('#idPengajuanKas').val()}&action=acceptRequest`,
                  data: {},
                  success: function(data) {
                      try {
                          let resp = JSON.parse(data)
                          if (resp.status == true) {
                              alert("Berhasil menyetujui pengajuan")
                              $('#konfirmasiApprove').modal('hide')
                              window.location.reload()
                          } else {
                              $('#btnApproveKas').prop('disabled', false);
                              alert("Tidak bisa menyimpan pengajuan. Terjadi kesalahan ketika menyimpan")
                          }
                      } catch (e) {
                          $('#btnApproveKas').prop('disabled', false);
                          alert("Tidak bisa menyimpan pengajuan. Terjadi kesalahan ketika menyimpan")
                      }
                  }
              });
          })
      })


      function createPengajuanKas(id) {
          $('#idPengajuanKas').val(id)
          $('#konfirmasiPengajuan').modal('show')
      }

      function validasiPengajuanKas(id) {
          $('#idPengajuanKas').val(id)
          $('#konfirmasiApprove').modal('show')

      }

      function tolakPengajuanKas(id) {
          $('#idPengajuanKas').val(id)
          $('#konfirmasiPenolakan').modal('show')

      }


  </script>

</body>

</html>