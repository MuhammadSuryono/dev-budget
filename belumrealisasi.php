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
            <li class="dropdown">
              <a class="dropdown-toggle" data-toggle="dropdown" href="#">Rekap
                <span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="rekap-finance.php">Ready To Paid</a></li>
                <li><a href="belumrealisasi.php">Belum Realisasi</a></li>
                <li><a href="cashflow.php">Cash Flow</a></li>
              </ul>
            </li>
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
          <?php } ?>
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


  <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
    <!-- PROJECT -->
    <div class="panel-body no-padding">
      <h3>
        <center>Project<center>
      </h3>
      <table class="table table-striped table-bordered">
        <thead>
          <tr class="warning">
            <th>#</th>
            <th>Jenis</th>
            <th>Project</th>
            <th>Item</th>
            <th>Request BPU</th>
            <th>Tanggal</th>
            <th>Penerima</th>
            <th>Pengaju(Divisi)</th>
          </tr>
        </thead>

        <tbody>

          <?php
          
          $i = 1;
          $sql = mysqli_query($koneksi, "SELECT * FROM bpu WHERE status='Telah Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND statusbpu='UM' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1')
                                                        OR status='Telah Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND statusbpu='UM' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1')
                                                        OR status='Telah Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND statusbpu='UM' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B2')
                                                        OR status='Telah Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND statusbpu='UM' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B2')
                                                        ORDER BY tglcair");
          while ($d = mysqli_fetch_array($sql)) {
          ?>
            <tr>
              <th scope="row" bgcolor="#8aad70"><?php echo $i++; ?></th>

              <td bgcolor="#8aad70">
                <!-- Nama Jenis -->
                <?php
                $namajenis = mysqli_query($koneksi, "SELECT jenis FROM pengajuan WHERE waktu='$d[waktu]'");
                $namjen = mysqli_fetch_assoc($namajenis);
                echo $namjen['jenis'];
                ?>
              </td>

              <td bgcolor="#8aad70">
                <!-- Nama Project -->
                <?php
                $namaproject = mysqli_query($koneksi, "SELECT nama FROM pengajuan WHERE waktu='$d[waktu]'");
                $nampro = mysqli_fetch_assoc($namaproject);
                echo $nampro['nama'];
                ?>
              </td>

              <td bgcolor="#8aad70">
                <!-- Nama Project -->
                <?php
                $namarincian = mysqli_query($koneksi, "SELECT no,rincian FROM selesai WHERE waktu='$d[waktu]' AND no='$d[no]'");
                $namrin = mysqli_fetch_assoc($namarincian);
                echo "<b>" . $namrin['no'] . "</b>." . $namrin['rincian'];
                ?>
              </td>

              <td bgcolor="#8aad70"><?php echo 'Rp. ' . number_format($d['jumlah'], 0, '', ','); ?></td>
              <td bgcolor="#8aad70"><?php echo $d['tanggalbayar']; ?></td>
              <td bgcolor="#8aad70"><?php echo $d['namapenerima']; ?></td>
              <td bgcolor="#8aad70"><?php echo $d['pengaju'] . "(" . $d['divisi'] . ")"; ?>
            </tr>
          <?php } ?>
        </tbody>
      </table>

      <?php
      $wewew = mysqli_query($koneksi, "SELECT sum(jumlah) AS sumia FROM bpu WHERE status='Telah Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND statusbpu='UM' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1')
                                                    OR status='Telah Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND statusbpu='UM' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1')
                                                    OR status='Telah Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND statusbpu='UM' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B2')
                                                    OR status='Telah Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND statusbpu='UM' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B2')");
      $t = mysqli_fetch_array($wewew);
      ?>
      <h4>Total UM Belum Realisasi : <?php echo 'Rp. ' . number_format($t['sumia'], 0, '', ','); ?></h4>
    </div><!-- /.table-responsive -->

    <!-- NON PROJECT -->
    <div class="panel-body no-padding">
      <h3>
        <center>NON Project<center>
      </h3>
      <table class="table table-striped table-bordered">
        <thead>
          <tr class="warning">
            <th>#</th>
            <th>Jenis</th>
            <th>Project</th>
            <th>Item</th>
            <th>Request BPU</th>
            <th>Tanggal</th>
            <th>Penerima</th>
            <th>Pengaju(Divisi)</th>
          </tr>
        </thead>

        <tbody>

          <?php
          
          $i = 1;
          $sql = mysqli_query($koneksi, "SELECT * FROM bpu WHERE status='Telah Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND statusbpu='UM' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Rutin')
                                                        OR status='Telah Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND statusbpu='UM' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Rutin')
                                                        OR status='Telah Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND statusbpu='UM' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Non Rutin')
                                                        OR status='Telah Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND statusbpu='UM' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Non Rutin')
                                                        ORDER BY tanggalbayar");
          while ($d = mysqli_fetch_array($sql)) {
          ?>
            <tr>
              <th scope="row" bgcolor="#8aad70"><?php echo $i++; ?></th>

              <td bgcolor="#8aad70">
                <!-- Nama Jenis -->
                <?php
                $namajenis = mysqli_query($koneksi, "SELECT jenis FROM pengajuan WHERE waktu='$d[waktu]'");
                $namjen = mysqli_fetch_assoc($namajenis);
                echo $namjen['jenis'];
                ?>
              </td>

              <td bgcolor="#8aad70">
                <!-- Nama Project -->
                <?php
                $namaproject = mysqli_query($koneksi, "SELECT nama FROM pengajuan WHERE waktu='$d[waktu]'");
                $nampro = mysqli_fetch_assoc($namaproject);
                echo $nampro['nama'];
                ?>
              </td>

              <td bgcolor="#8aad70">
                <!-- Nama Project -->
                <?php
                $namarincian = mysqli_query($koneksi, "SELECT no,rincian FROM selesai WHERE waktu='$d[waktu]' AND no='$d[no]'");
                $namrin = mysqli_fetch_assoc($namarincian);
                echo "<b>" . $namrin['no'] . "</b>." . $namrin['rincian'];
                ?>
              </td>

              <td bgcolor="#8aad70"><?php echo 'Rp. ' . number_format($d['jumlah'], 0, '', ','); ?></td>
              <td bgcolor="#8aad70"><?php echo $d['tanggalbayar']; ?></td>
              <td bgcolor="#8aad70"><?php echo $d['namapenerima']; ?></td>
              <td bgcolor="#8aad70"><?php echo $d['pengaju'] . "(" . $d['divisi'] . ")"; ?>
            </tr>
          <?php } ?>
        </tbody>
      </table>

      <?php
      $wewew = mysqli_query($koneksi, "SELECT sum(jumlah) AS sumia FROM bpu WHERE status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND tanggalbayar BETWEEN '$daritgl' AND '$sampaitgl' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Rutin')
                                                    OR status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND tglcair BETWEEN '$daritgl' AND '$sampaitgl' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Rutin')
                                                    OR status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND tglcair BETWEEN '$daritgl' AND '$sampaitgl' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Non Rutin')
                                                    OR status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND tglcair BETWEEN '$daritgl' AND '$sampaitgl' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Non Rutin')");
      $t = mysqli_fetch_array($wewew);
      ?>
      <h4>Total UM Belum Realisasi : <?php echo 'Rp. ' . number_format($t['sumia'], 0, '', ','); ?></h4>
    </div><!-- /.table-responsive -->

  </div>
  </div>

  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Bayar Budget</h4>
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
          <h4 class="modal-title">Pembayaran BPU Eksternal</h4>
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

</body>

</html>