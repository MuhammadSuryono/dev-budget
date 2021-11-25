<?php
error_reporting(0);
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
        <a class="navbar-brand" href="home-finance.php">Budget-Ing</a>
      </div>
      <div class="collapse navbar-collapse" id="myNavbar">
        <ul class="nav navbar-nav">
          <?php
          if ($_SESSION['divisi'] == 'Direksi') {
          ?>
            <li class="active"><a href="home-direksi.php">Home</a></li>
            <li><a href="list-direksi.php">List</a></li>
            <li><a href="saldobpu.php">Saldo BPU</a></li>
            <!--<li><a href="summary.php">Summary</a></li>-->
            <li><a href="listfinish-direksi.php">Budget Finish</a></li>
          <?php
          } else {
          ?>
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
            <li><a href="history-finance.php">History</a></li>
            <li><a href="list.php">Personal</a></li>
            <li><a href="summary-finance.php">Summary</a></li>
          <?php
          }
          ?>
          <li class="dropdown active">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">Rekap
              <span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li class="active"><a href="rekap-finance.php">Ready To Paid</a></li>
              <li><a href="belumrealisasi.php">Belum Realisasi</a></li>
              <li><a href="cashflow.php">Cash Flow</a></li>
            </ul>
          </li>
        </ul>

        <?php
        $cari = mysqli_query($koneksi, "SELECT * FROM bpu WHERE status ='Belum Di Bayar' AND persetujuan !='Belum Disetujui'");
        $belbyr = mysqli_num_rows($cari);
        ?>
        <ul class="nav navbar-nav navbar-right">
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
        <table class="table table-striped table-bordered">
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
            $sql = mysqli_query($koneksi, "SELECT * FROM bpu WHERE status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1') AND statusbpu !='UM'
                                                      OR status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1') AND statusbpu !='UM'
                                                      OR status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B2') AND statusbpu !='UM'
                                                      OR status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B2') AND statusbpu !='UM'
                                                      ORDER BY tanggalbayar");
            while ($d = mysqli_fetch_array($sql)) {
              $waktu = $d['waktu'];
              $no    = $d['no'];
              $term  = $d['term'];
            ?>
              <tr>
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
                <td><button type="button" class="btn btn-success btn-md" onclick="pengajuan('<?php echo $waktu; ?>','<?php echo $no; ?>','<?php echo $term; ?>')"><i class="fas fa-angle-double-right"></i> Move</button></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>

        <?php
        $wewew = mysqli_query($koneksi, "SELECT sum(jumlah) AS sumia FROM bpu WHERE status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1') AND statusbpu !='UM'
                                                  OR status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1') AND statusbpu !='UM'
                                                  OR status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B2') AND statusbpu !='UM'
                                                  OR status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B2')AND statusbpu !='UM'");
        $t = mysqli_fetch_array($wewew);

        $totalhonor = mysqli_query($koneksi, "SELECT sum(jumlah) AS sumtot FROM bpu WHERE status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND statusbpu ='Honor Eksternal' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1')
                                                                            OR status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND statusbpu ='Honor Eksternal' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1')");
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

        <!-- Pembuka Table Non Project -->
        <h3>
          <center>Non Project</center>
        </h3>
        <br>
        <table class="table table-striped table-bordered">
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
            $sql = mysqli_query($koneksi, "SELECT * FROM bpu WHERE status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Non Rutin') AND statusbpu !='UM'
                                                        OR status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Non Rutin') AND statusbpu !='UM'
                                                        ORDER BY tanggalbayar");
            while ($d = mysqli_fetch_array($sql)) {
              $waktu = $d['waktu'];
              $no    = $d['no'];
              $term  = $d['term'];
            ?>
              <tr>
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
                <td><button type="button" class="btn btn-success btn-md" onclick="pengajuan('<?php echo $waktu; ?>','<?php echo $no; ?>','<?php echo $term; ?>')"><i class="fas fa-angle-double-right"></i> Move</button></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>

        <?php
        $wewew = mysqli_query($koneksi, "SELECT sum(jumlah) AS sumia FROM bpu WHERE status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Non Rutin') AND statusbpu !='UM'
                                                    OR status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Non Rutin') AND statusbpu !='UM'");
        $t = mysqli_fetch_array($wewew);

        $totalhonor = mysqli_query($koneksi, "SELECT sum(jumlah) AS sumtot FROM bpu WHERE status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND statusbpu ='Non Rutin' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1') AND statusbpu !='UM'
                                                                              OR status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND statusbpu ='Non Rutin' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1') AND statusbpu !='UM'");
        $h = mysqli_fetch_array($totalhonor);
        ?>
        <h5>
          <div class="row">
            <div class="col-xs-3">Total Pengajuan KAS</div>
            <div class="col-xs-3">: <b><?php echo 'Rp. ' . number_format($t['sumia'], 0, '', ','); ?></b></div>
          </div>
        </h5>
        <!-- //Penutup Table Non Project -->

        </br></br>

        <!-- Pembuka Table Rutin -->
        <h3>
          <center>Rutin</center>
        </h3>
        <br>
        <table class="table table-striped table-bordered">
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
            $sql = mysqli_query($koneksi, "SELECT * FROM bpu WHERE status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Rutin') AND statusbpu !='UM'
                                                          OR status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Rutin') AND statusbpu !='UM'
                                                          ORDER BY tanggalbayar");
            while ($d = mysqli_fetch_array($sql)) {
              $waktu = $d['waktu'];
              $no    = $d['no'];
              $term  = $d['term'];
            ?>
              <tr>
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
                <td><button type="button" class="btn btn-success btn-md" onclick="pengajuan('<?php echo $waktu; ?>','<?php echo $no; ?>','<?php echo $term; ?>')"><i class="fas fa-angle-double-right"></i> Move</button></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>

        <?php
        $wewew = mysqli_query($koneksi, "SELECT sum(jumlah) AS sumia FROM bpu WHERE status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Rutin') AND statusbpu !='UM'
                                                      OR status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Rutin') AND statusbpu !='UM'");
        $t = mysqli_fetch_array($wewew);

        $totalhonor = mysqli_query($koneksi, "SELECT sum(jumlah) AS sumtot FROM bpu WHERE status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND statusbpu ='Honor Eksternal' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1') AND statusbpu !='UM'
                                                                                OR status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND statusbpu ='Honor Eksternal' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1') AND statusbpu !='UM'");
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

    function pengajuan(waktu, no, term) {
      // alert(noid+' - '+waktu);
      $.ajax({
        type: 'post',
        url: 'pengajuankas.php',
        data: {
          waktu: waktu,
          no: no,
          term: term
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