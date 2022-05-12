<?php
error_reporting(0);

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
session_start();
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
          
          $cari = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE status='Pending'");
          $belbyr = mysqli_num_rows($cari);
          $caribpu = mysqli_query($koneksi, "SELECT * FROM bpu WHERE persetujuan='Belum Disetujui'");
          $bpuyahud = mysqli_num_rows($caribpu);
          $queryPengajuanReq = mysqli_query($koneksi, "SELECT * FROM pengajuan_request WHERE status_request = 'Di Ajukan' AND waktu != 0");
          $countPengajuanReq = mysqli_num_rows($queryPengajuanReq);
          $notif = $belbyr + $bpuyahud + $countPengajuanReq;
        ?>
         <ul class="nav navbar-nav navbar-right">
                        <li><a href="/log-notifikasi-aplikasi/index.html" target="_blank"><i class="fa fa-envelope"></i></a></li>
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
    <!-- Kepala Tab -->
    <ul class="nav nav-tabs">
      <li class="active"><a data-toggle="tab" href="#rtp">Ready To Paid</a></li>
      <li><a data-toggle="tab" href="#pengajuan">Pengajuan Kas</a></li>
      <li><a data-toggle="tab" href="#overdue">Overdue</a></li>
    </ul>
    <!-- //Kepala Tab -->

    </br></br>

    <div class="tab-content">
      <!-- Konten Tab -->

      <div id="rtp" class="tab-pane fade in active">
        <!-- Pembuka RTP -->

        <!-- Pembuka Table Project -->
        <h3>
          <center>Project</center>
        </h3>
        <br>
        <table class="table table-bordered">
          <thead>
            <tr class="warning">
              <th>#</th>
              <th>Jenis</th>
              <th>Project</th>
              <th>Item</th>
              <th>Kategori</th>
              <th>Request BPU</th>
              <th>Tanggal Pembayaran</th>
              <th>Penerima</th>
              <th>Pengaju(Divisi)</th>
              <th>Move</th>
            </tr>
          </thead>

          <tbody>

            <?php
            $namauser = $_SESSION['nama_user'];
            date_default_timezone_set("Asia/Bangkok");
            
            $i = 1;
            $sql = mysqli_query($koneksi, "SELECT * FROM bpu WHERE metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1') AND statusrtp IS NULL AND (statusbpu !='UM' AND statusbpu != 'UM Burek')
                                                      OR metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1') AND statusrtp IS NULL AND (statusbpu !='UM' AND statusbpu != 'UM Burek')
                                                      OR metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B2') AND statusrtp IS NULL AND (statusbpu !='UM' AND statusbpu != 'UM Burek')
                                                      OR metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B2') AND statusrtp IS NULL AND (statusbpu !='UM' AND statusbpu != 'UM Burek')
                                                      ORDER BY tanggalbayar");
            while ($d = mysqli_fetch_array($sql)) {
              $waktu = $d['waktu'];
              $no    = $d['no'];
              $term  = $d['term'];

              if ($d['urgent'] == 'Urgent') {
                $red = "red";
              } else {
                $red = "";
              }
            ?>
              <tr bgcolor="<?php echo $red ?>">
                <th scope="row"><?php echo $i++; ?></th>

                <td>
                  <!-- Nama Jenis -->
                  <?php
                  $namajenis = mysqli_query($koneksi, "SELECT jenis FROM pengajuan WHERE waktu='$d[waktu]'");
                  $namjen = mysqli_fetch_assoc($namajenis);
                  echo $namjen['jenis'];
                  ?>
                </td>

                <td>
                  <!-- Nama Project -->
                  <?php
                  $namaproject = mysqli_query($koneksi, "SELECT nama FROM pengajuan WHERE waktu='$d[waktu]'");
                  $nampro = mysqli_fetch_assoc($namaproject);
                  echo $nampro['nama'];
                  ?>
                </td>

                <td>
                  <!-- Item -->
                  <?php
                  $namarincian = mysqli_query($koneksi, "SELECT no,rincian FROM selesai WHERE waktu='$d[waktu]' AND no='$d[no]'");
                  $namrin = mysqli_fetch_assoc($namarincian);
                  echo "<b>" . $namrin['no'] . "</b>." . $namrin['rincian'];
                  ?>
                </td>
                <td><?php echo $d['statusbpu']; ?></td>
                <td><?php echo 'Rp. ' . number_format($d['jumlah'], 0, '', ','); ?></td>
                <td>
                  <?php
                  if ($d['tanggalbayar'] == '0000-00-00') {
                    echo $d['tglcair'];
                  } else {
                    echo $d['tanggalbayar'];
                  }
                  ?>
                </td>
                <td><?php echo $d['namapenerima']; ?></td>
                <td><?php echo $d['pengaju'] . "(" . $d['divisi'] . ")"; ?>
                <td><button type="button" class="btn btn-success btn-md" onclick="pengajuan('<?php echo $waktu; ?>','<?php echo $no; ?>','<?php echo $term; ?>','<?php echo $namauser; ?>')"><i class="fas fa-angle-double-right"></i> Move</button></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>

        <?php
        $wewew = mysqli_query($koneksi, "SELECT sum(jumlah) AS sumia FROM bpu WHERE metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1') AND statusrtp IS NULL AND (statusbpu !='UM' AND statusbpu != 'UM Burek')
                                                  OR metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1') AND statusrtp IS NULL AND (statusbpu !='UM' AND statusbpu != 'UM Burek')
                                                  OR metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B2') AND statusrtp IS NULL AND (statusbpu !='UM' AND statusbpu != 'UM Burek')
                                                  OR metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B2') AND statusrtp IS NULL AND(statusbpu !='UM' AND statusbpu != 'UM Burek')");
        $t = mysqli_fetch_array($wewew);

        $totalhonor = mysqli_query($koneksi, "SELECT sum(jumlah) AS sumtot FROM bpu WHERE metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND statusbpu ='Honor Eksternal' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1') AND statusrtp IS NULL
                                                                            OR metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND statusbpu ='Honor Eksternal' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1') AND statusrtp IS NULL");
        $h = mysqli_fetch_array($totalhonor);
        ?>
        <h5>
          <div class="row">
            <div class="col-xs-3">Total Honor SHP dan PWT</div>
            <div class="col-xs-3">: <b><?php echo 'Rp. ' . number_format($h['sumtot'], 0, '', ','); ?></b></div>
          </div>


          <div class="row">
            <div class="col-xs-3">Total Pengajuan KAS</div>
            <div class="col-xs-3">: <b><?php echo 'Rp. ' . number_format($t['sumia'], 0, '', ','); ?></b></div>
          </div>
        </h5>
        <!-- //Penutup Table Project -->

        </br></br>

        <!-- Pembuka Table Uang Muka -->
        <h3>
          <center>Uang Muka</center>
        </h3>
        <br>
        <table class="table table-bordered">
          <thead>
            <tr class="warning">
              <th>#</th>
              <th>Jenis</th>
              <th>Project</th>
              <th>Item</th>
              <th>Kategori</th>
              <th>Request BPU</th>
              <th>Tanggal Pembayaran</th>
              <th>Penerima</th>
              <th>Pengaju(Divisi)</th>
              <th>Move</th>
            </tr>
          </thead>

          <tbody>

            <?php
            $namauser = $_SESSION['nama_user'];
            date_default_timezone_set("Asia/Bangkok");
            
            $i = 1;
            $sql = mysqli_query($koneksi, "SELECT * FROM bpu WHERE metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND (persetujuan ='Disetujui (Direksi)' OR persetujuan ='Disetujui (Sri Dewi Marpaung)') AND (statusbpu ='UM' OR statusbpu = 'UM Burek')
                                                      ORDER BY tanggalbayar");
            while ($d = mysqli_fetch_array($sql)) {
              $waktu = $d['waktu'];
              $no    = $d['no'];
              $term  = $d['term'];
              if ($d['urgent'] == 'Urgent') {
                $red = "red";
              } else {
                $red = "";
              }
            ?>
              <tr bgcolor="<?php echo $red; ?>">
                <th scope="row"><?php echo $i++; ?></th>

                <td>
                  <!-- Nama Jenis -->
                  <?php
                  $namajenis = mysqli_query($koneksi, "SELECT jenis FROM pengajuan WHERE waktu='$d[waktu]'");
                  $namjen = mysqli_fetch_assoc($namajenis);
                  echo $namjen['jenis'];
                  ?>
                </td>

                <td>
                  <!-- Nama Project -->
                  <?php
                  $namaproject = mysqli_query($koneksi, "SELECT nama FROM pengajuan WHERE waktu='$d[waktu]'");
                  $nampro = mysqli_fetch_assoc($namaproject);
                  echo $nampro['nama'];
                  ?>
                </td>

                <td>
                  <!-- Item -->
                  <?php
                  $namarincian = mysqli_query($koneksi, "SELECT no,rincian FROM selesai WHERE waktu='$d[waktu]' AND no='$d[no]'");
                  $namrin = mysqli_fetch_assoc($namarincian);
                  echo "<b>" . $namrin['no'] . "</b>." . $namrin['rincian'];
                  ?>
                </td>
                <td><?php echo $d['statusbpu']; ?></td>
                <td><?php echo 'Rp. ' . number_format($d['jumlah'], 0, '', ','); ?></td>
                <td>
                  <?php
                  if ($d['tanggalbayar'] == '0000-00-00') {
                    echo $d['tglcair'];
                  } else {
                    echo $d['tanggalbayar'];
                  }
                  ?>
                </td>
                <td><?php echo $d['namapenerima']; ?></td>
                <td><?php echo $d['pengaju'] . "(" . $d['divisi'] . ")"; ?>
                <td><button type="button" class="btn btn-success btn-md" onclick="pengajuan('<?php echo $waktu; ?>','<?php echo $no; ?>','<?php echo $term; ?>','<?php echo $namauser; ?>')"><i class="fas fa-angle-double-right"></i> Move</button></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>

        <?php
        $wewew = mysqli_query($koneksi, "SELECT sum(jumlah) AS sumia FROM bpu WHERE metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND (statusbpu ='UM' OR statusbpu = 'UM Burek')
                                                                    OR metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND (statusbpu ='UM' OR statusbpu = 'UM Burek')");
        $t = mysqli_fetch_array($wewew);
        ?>
        <h5>
          <div class="row">
            <div class="col-xs-3">Total Pengajuan KAS</div>
            <div class="col-xs-3">: <b><?php echo 'Rp. ' . number_format($t['sumia'], 0, '', ','); ?></b></div>
          </div>
        </h5>
        <!-- //Penutup Table Uang Muka -->

        <br /><br />

        <!-- Pembuka Table Non Rutin -->
        <h3>
          <center>Non Rutin</center>
        </h3>
        <br>
        <table class="table table-bordered">
          <thead>
            <tr class="warning">
              <th>#</th>
              <th>Jenis</th>
              <th>Project</th>
              <th>Item</th>
              <th>Kategori</th>
              <th>Request BPU</th>
              <th>Tanggal Pembayaran</th>
              <th>Penerima</th>
              <th>Pengaju(Divisi)</th>
              <th>Move</th>
            </tr>
          </thead>

          <tbody>

            <?php
            date_default_timezone_set("Asia/Bangkok");
            
            $i = 1;
            $sql = mysqli_query($koneksi, "SELECT * FROM bpu WHERE metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Non Rutin') AND statusrtp IS NULL AND (statusbpu !='UM' AND statusbpu != 'UM Burek')
                                                        OR metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Non Rutin') AND statusrtp IS NULL AND (statusbpu !='UM' AND statusbpu != 'UM Burek')
                                                        ORDER BY tanggalbayar");
            while ($d = mysqli_fetch_array($sql)) {
              $waktu = $d['waktu'];
              $no    = $d['no'];
              $term  = $d['term'];
              if ($d['urgent'] == 'Urgent') {
                $red = "red";
              } else {
                $red = "";
              }
            ?>
              <tr bgcolor="<?php echo $red; ?>">
                <th scope="row"><?php echo $i++; ?></th>

                <td>
                  <!-- Nama Jenis -->
                  <?php
                  $namajenis = mysqli_query($koneksi, "SELECT jenis FROM pengajuan WHERE waktu='$d[waktu]'");
                  $namjen = mysqli_fetch_assoc($namajenis);
                  echo $namjen['jenis'];
                  ?>
                </td>

                <td>
                  <!-- Nama Project -->
                  <?php
                  $namaproject = mysqli_query($koneksi, "SELECT nama FROM pengajuan WHERE waktu='$d[waktu]'");
                  $nampro = mysqli_fetch_assoc($namaproject);
                  echo $nampro['nama'];
                  ?>
                </td>

                <td>
                  <!-- Item -->
                  <?php
                  $namarincian = mysqli_query($koneksi, "SELECT no,rincian FROM selesai WHERE waktu='$d[waktu]' AND no='$d[no]'");
                  $namrin = mysqli_fetch_assoc($namarincian);
                  echo "<b>" . $namrin['no'] . "</b>." . $namrin['rincian'];
                  ?>
                </td>
                <td><?php echo $d['statusbpu']; ?></td>
                <td><?php echo 'Rp. ' . number_format($d['jumlah'], 0, '', ','); ?></td>
                <td>
                  <?php
                  if ($d['tanggalbayar'] == '0000-00-00') {
                    echo $d['tglcair'];
                  } else {
                    echo $d['tanggalbayar'];
                  }
                  ?>
                </td>
                <td><?php echo $d['namapenerima']; ?></td>
                <td><?php echo $d['pengaju'] . "(" . $d['divisi'] . ")"; ?>
                <td><button type="button" class="btn btn-success btn-md" onclick="pengajuan('<?php echo $waktu; ?>','<?php echo $no; ?>','<?php echo $term; ?>','<?php echo $namauser; ?>')"><i class="fas fa-angle-double-right"></i> Move</button></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>

        <?php
        $wewew = mysqli_query($koneksi, "SELECT sum(jumlah) AS sumia FROM bpu WHERE metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Non Rutin') AND statusrtp IS NULL AND (statusbpu !='UM' AND statusbpu != 'UM Burek')
                                                    OR metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Non Rutin') AND statusrtp IS NULL AND (statusbpu !='UM' AND statusbpu != 'UM Burek')");
        $t = mysqli_fetch_array($wewew);

        $totalhonor = mysqli_query($koneksi, "SELECT sum(jumlah) AS sumtot FROM bpu WHERE metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND statusbpu ='Non Rutin' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1') AND statusrtp IS NULL
                                                                              OR metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND statusbpu ='Non Rutin' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1') AND statusrtp IS NULL");
        $h = mysqli_fetch_array($totalhonor);
        ?>
        <h5>
          <div class="row">
            <div class="col-xs-3">Total Pengajuan KAS</div>
            <div class="col-xs-3">: <b><?php echo 'Rp. ' . number_format($t['sumia'], 0, '', ','); ?></b></div>
          </div>
        </h5>
        <!-- //Penutup Table Non Rutin -->

        </br></br>

        <!-- Pembuka Table Rutin -->
        <h3>
          <center>Rutin</center>
        </h3>
        <br>
        <table class="table table-bordered">
          <thead>
            <tr class="warning">
              <th>#</th>
              <th>Jenis</th>
              <th>Project</th>
              <th>Item</th>
              <th>Kategori</th>
              <th>Request BPU</th>
              <th>Tanggal Pembayaran</th>
              <th>Penerima</th>
              <th>Pengaju(Divisi)</th>
              <th>Move</th>
            </tr>
          </thead>

          <tbody>

            <?php
            date_default_timezone_set("Asia/Bangkok");
            
            $i = 1;
            $sql = mysqli_query($koneksi, "SELECT * FROM bpu WHERE metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Rutin') AND statusrtp IS NULL AND (statusbpu !='UM' AND statusbpu != 'UM Burek')
                                                          OR metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Rutin') AND statusrtp IS NULL AND (statusbpu !='UM' AND statusbpu != 'UM Burek')
                                                          ORDER BY tanggalbayar");
            while ($d = mysqli_fetch_array($sql)) {
              $waktu = $d['waktu'];
              $no    = $d['no'];
              $term  = $d['term'];
              if ($d['urgent'] == 'Urgent') {
                $red = "red";
              } else {
                $red = "";
              }
            ?>
              <tr bgcolor="<?php echo $red; ?>">
                <th scope="row"><?php echo $i++; ?></th>

                <td>
                  <!-- Nama Jenis -->
                  <?php
                  $namajenis = mysqli_query($koneksi, "SELECT jenis FROM pengajuan WHERE waktu='$d[waktu]'");
                  $namjen = mysqli_fetch_assoc($namajenis);
                  echo $namjen['jenis'];
                  ?>
                </td>

                <td>
                  <!-- Nama Project -->
                  <?php
                  $namaproject = mysqli_query($koneksi, "SELECT nama FROM pengajuan WHERE waktu='$d[waktu]'");
                  $nampro = mysqli_fetch_assoc($namaproject);
                  echo $nampro['nama'];
                  ?>
                </td>

                <td>
                  <!-- Item -->
                  <?php
                  $namarincian = mysqli_query($koneksi, "SELECT no,rincian FROM selesai WHERE waktu='$d[waktu]' AND no='$d[no]'");
                  $namrin = mysqli_fetch_assoc($namarincian);
                  echo "<b>" . $namrin['no'] . "</b>." . $namrin['rincian'];
                  ?>
                </td>
                <td><?php echo $d['statusbpu']; ?></td>
                <td><?php echo 'Rp. ' . number_format($d['jumlah'], 0, '', ','); ?></td>
                <td>
                  <?php
                  if ($d['tanggalbayar'] == '0000-00-00') {
                    echo $d['tglcair'];
                  } else {
                    echo $d['tanggalbayar'];
                  }
                  ?>
                </td>
                <td><?php echo $d['namapenerima']; ?></td>
                <td><?php echo $d['pengaju'] . "(" . $d['divisi'] . ")"; ?>
                <td><button type="button" class="btn btn-success btn-md" onclick="pengajuan('<?php echo $waktu; ?>','<?php echo $no; ?>','<?php echo $term; ?>','<?php echo $namauser; ?>')"><i class="fas fa-angle-double-right"></i> Move</button></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>

        <?php
        $wewew = mysqli_query($koneksi, "SELECT sum(jumlah) AS sumia FROM bpu WHERE metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Rutin') AND statusrtp IS NULL AND (statusbpu !='UM' AND statusbpu != 'UM Burek')
                                                      OR metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Rutin') AND statusrtp IS NULL AND (statusbpu !='UM' AND statusbpu != 'UM Burek')");
        $t = mysqli_fetch_array($wewew);

        $totalhonor = mysqli_query($koneksi, "SELECT sum(jumlah) AS sumtot FROM bpu WHERE metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND statusbpu ='Honor Eksternal' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1') AND statusrtp IS NULL AND (statusbpu !='UM' AND statusbpu != 'UM Burek')
                                                                                OR metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND statusbpu ='Honor Eksternal' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1') AND statusrtp IS NULL AND (statusbpu !='UM' AND statusbpu != 'UM Burek')");
        $h = mysqli_fetch_array($totalhonor);
        ?>
        <h5>
          <div class="row">
            <div class="col-xs-3">Total Pengajuan KAS</div>
            <div class="col-xs-3">: <b><?php echo 'Rp. ' . number_format($t['sumia'], 0, '', ','); ?></b></div>
          </div>
        </h5>
        <!-- //Penutup Table Rutin -->

      </div><!-- Penutup RTP -->

      <!-- Pengajuan Kas -->
      <div id="pengajuan" class="tab-pane fade">
        <?php
        include "pengajuankas.php";
        ?>
      </div>
      <!-- OVerdue -->


      <!-- OVerdue -->
      <div id="overdue" class="tab-pane fade">
        <?php
        include "overdue.php";
        ?>
      </div>
      <!-- OVerdue -->

    </div>
  </div>

  <div class="modal fade" id="myModal4" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Pengajuan Kas</h4>
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
    function edit_budget(no, waktu) {
      // alert(noid+' - '+waktu);
      $.ajax({
        type: 'post',
        url: 'bayarbudget.php',
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

    function eksternal_finance(no, waktu) {
      // alert(noid+' - '+waktu);
      $.ajax({
        type: 'post',
        url: 'financeeksternal.php',
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

    function pengajuan(waktu, no, term, namauser) {
      // alert(noid+' - '+waktu);
      $.ajax({
        type: 'post',
        url: 'movekas.php',
        data: {
          waktu: waktu,
          no: no,
          term: term,
          namauser: namauser
        },
        success: function(data) {
          $('.fetched-data').html(data); //menampilkan data ke dalam modal
          $('#myModal4').modal();
        }
      });
    }
  </script>

</body>
</html>