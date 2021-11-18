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
        <a class="navbar-brand" href="home-direksi.php">Budget-Ing</a>
      </div>
      <div class="collapse navbar-collapse" id="myNavbar">
        <ul class="nav navbar-nav">
          <li><a href="home-direksi.php">Home</a></li>
          <li><a href="list-direksi.php">List</a></li>
          <li><a href="saldobpu.php">Data User</a></li>
          <!--<li><a href="summary.php">Summary</a></li>-->
          <li class="active"><a href="listfinish-direksi.php">Budget Finish</a></li>
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
        $cari = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE status='Pending'");
        $belbyr = mysqli_num_rows($cari);
        $caribpu = mysqli_query($koneksi, "SELECT * FROM bpu WHERE persetujuan='Belum Disetujui'");
        $bpuyahud = mysqli_num_rows($caribpu);
        $queryPengajuanReq = mysqli_query($koneksi, "SELECT * FROM pengajuan_request WHERE status_request = 'Di Ajukan' AND waktu != 0");
        $countPengajuanReq = mysqli_num_rows($queryPengajuanReq);
        $notif = $belbyr + $bpuyahud + $countPengajuanReq;
        ?>
        <ul class="nav navbar-nav navbar-right">
          <li class="dropdown messages-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-inbox"></i><span class="label label-warning"><?php echo $notif; ?></span></a>
            <ul class="dropdown-menu">
              <?php
              if (mysqli_num_rows($cari) == 0) {
                echo "";
              } else {
                while ($wkt = mysqli_fetch_array($cari)) {
                  $wktulang = $wkt['waktu'];
                  $selectnoid = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE waktu='$wktulang'");
                  $noid = mysqli_fetch_assoc($selectnoid);
                  $kode = $noid['noid'];
                  $project = $noid['nama'];
              ?>
                  <li class="header"><a href="view-direksi.php?code=<?= $kode ?>">Project <b><?= $project ?></b> status masih Pending</a></li>
                <?php
                }
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

          <li><a href="#"><span class="glyphicon glyphicon-user"></span><?php echo $_SESSION['nama_user']; ?> (<?php echo $_SESSION['divisi']; ?>)</a></li>
          <li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container">

    <ul id="myTab" class="nav nav-tabs" role="tablist">
      <li role="presentation" class="active">
        <a href="#B1" id="B1-tab" role="tab" data-toggle="tab" aria-controls="B1" aria-expanded="true">B1</a>
      </li>
      <li role="presentation">
        <a href="#B2" role="tab" id="B2-tab" data-toggle="tab" aria-controls="B2">B2</a>
      </li>
      <li role="presentation">
        <a href="#rutin" role="tab" id="rutin-tab" data-toggle="tab" aria-controls="rutin">Rutin</a>
      </li>
      <li role="presentation">
        <a href="#nonrutin" role="tab" id="nonrutin-tab" data-toggle="tab" aria-controls="nonrutin">Non Rutin</a>
      </li>

      <div id="myTabContent" class="tab-content">
        <!-- Tab -->

        <div role="tabpanel" class="tab-pane fade in active" id="B1" aria-labelledby="home-tab">
          <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
            <div class="panel-body no-padding">
              <table class="table table-striped table-bordered">
                <thead>
                  <tr class="warning">
                    <th>No</th>
                    <th>Nama Project</th>
                    <th>Tahun</th>
                    <th>Nama Yang Mengajukan</th>
                    <th>Divisi</th>
                    <th>Total</th>
                    <th>View</th>
                    <th>Status</th>
                  </tr>
                </thead>

                <tbody>

                  <?php
                  
                  $i = 1;
                  $sql = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE jenis='B1' AND status ='Finish'");
                  while ($d = mysqli_fetch_array($sql)) {
                  ?>
                    <tr>
                      <th bgcolor="#fcfaa4" scope="row"><?php echo $i++; ?></th>
                      <td bgcolor="#fcfaa4"><?php echo $d['nama']; ?></td>
                      <td bgcolor="#fcfaa4"><?php echo $d['tahun']; ?></td>
                      <td bgcolor="#fcfaa4"><?php echo $d['pengaju']; ?></td>
                      <td bgcolor="#fcfaa4"><?php echo $d['divisi']; ?></td>
                      <td bgcolor="#fcfaa4"><?php echo 'Rp. ' . number_format($d['totalbudget'], 0, '', ','); ?></td>
                      <td bgcolor="#fcfaa4"><a href="view-finish-direksi.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a></td>
                      <td bgcolor="#fcfaa4"><?php echo $d['status']; ?></td>
                    </tr>
                  <?php
                  }
                  ?>
                </tbody>
              </table>
            </div><!-- /.table-responsive -->
          </div>
        </div>

        <div role="tabpanel" class="tab-pane fade" id="B2" aria-labelledby="B2-tab">
          <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
            <div class="panel-body no-padding">
              <table class="table table-striped table-bordered">
                <thead>
                  <tr class="warning">
                    <th>No</th>
                    <th>Nama Project</th>
                    <th>Tahun</th>
                    <th>Nama Yang Mengajukan</th>
                    <th>Divisi</th>
                    <th>Total</th>
                    <th>View</th>
                    <th>Status</th>
                  </tr>
                </thead>

                <tbody>

                  <?php
                  
                  $i = 1;
                  $sql2 = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE jenis='B2' AND status ='Finish'");
                  while ($e = mysqli_fetch_array($sql2)) {
                  ?>
                    <tr>
                      <th bgcolor="#fcfaa4" scope="row"><?php echo $i++; ?></th>
                      <td bgcolor="#fcfaa4"><?php echo $e['nama']; ?></td>
                      <td bgcolor="#fcfaa4"><?php echo $e['tahun']; ?></td>
                      <td bgcolor="#fcfaa4"><?php echo $e['pengaju']; ?></td>
                      <td bgcolor="#fcfaa4"><?php echo $e['divisi']; ?></td>
                      <td bgcolor="#fcfaa4"><?php echo 'Rp. ' . number_format($e['totalbudget'], 0, '', ','); ?></td>
                      <td bgcolor="#fcfaa4"><a href="view-finish-direksi.php?code=<?php echo $e['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a></td>
                      <td bgcolor="#fcfaa4"><?php echo $e['status']; ?></td>
                    </tr>
                  <?php
                  }
                  ?>
                </tbody>
              </table>
            </div><!-- /.table-responsive -->
          </div>
        </div>

        <div role="tabpanel" class="tab-pane fade" id="rutin" aria-labelledby="rutin-tab">
          <br /><br />
          <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
            <div class="panel-body no-padding">
              <table class="table table-striped table-bordered">
                <thead>
                  <tr class="warning">
                    <th>No</th>
                    <th>Nama Project</th>
                    <th>Tahun</th>
                    <th>Nama Yang Mengajukan</th>
                    <th>Divisi</th>
                    <th>Total</th>
                    <th>View</th>
                    <th>Status</th>
                  </tr>
                </thead>

                <tbody>
                  <?php
                  
                  $i = 1;
                  $sql = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE jenis='Rutin' AND status ='Finish'");
                  while ($d = mysqli_fetch_array($sql)) {
                  ?>
                    <tr>
                      <th bgcolor="#fcfaa4" scope="row"><?php echo $i++; ?></th>
                      <td bgcolor="#fcfaa4"><?php echo $d['nama']; ?></td>
                      <td bgcolor="#fcfaa4"><?php echo $d['tahun']; ?></td>
                      <td bgcolor="#fcfaa4"><?php echo $d['pengaju']; ?></td>
                      <td bgcolor="#fcfaa4"><?php echo $d['divisi']; ?></td>
                      <td bgcolor="#fcfaa4"><?php echo 'Rp. ' . number_format($d['totalbudget'], 0, '', ','); ?></td>
                      <td bgcolor="#fcfaa4"><a href="view-finish-direksi.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a></td>
                      <td bgcolor="#fcfaa4"><?php echo $d['status']; ?></td>
                    </tr>
                  <?php
                  }
                  ?>
                </tbody>
              </table>
            </div><!-- /.table-responsive -->
          </div>
        </div>

        <div role="tabpanel" class="tab-pane fade" id="nonrutin" aria-labelledby="nonrutin-tab">
          <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
            <div class="panel-body no-padding">
              <table class="table table-striped table-bordered">
                <thead>
                  <tr class="warning">
                    <th>No</th>
                    <th>Nama Project</th>
                    <th>Tahun</th>
                    <th>Nama Yang Mengajukan</th>
                    <th>Divisi</th>
                    <th>Total</th>
                    <th>View</th>
                    <th>Status</th>
                  </tr>
                </thead>

                <tbody>

                  <?php
                  
                  $i = 1;
                  $sql = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE jenis='Non Rutin' AND status ='Finish'");
                  while ($d = mysqli_fetch_array($sql)) {
                  ?>
                    <tr>
                      <th bgcolor="#fcfaa4" scope="row"><?php echo $i++; ?></th>
                      <td bgcolor="#fcfaa4"><?php echo $d['nama']; ?></td>
                      <td bgcolor="#fcfaa4"><?php echo $d['tahun']; ?></td>
                      <td bgcolor="#fcfaa4"><?php echo $d['pengaju']; ?></td>
                      <td bgcolor="#fcfaa4"><?php echo $d['divisi']; ?></td>
                      <td bgcolor="#fcfaa4"><?php echo 'Rp. ' . number_format($d['totalbudget'], 0, '', ','); ?></td>
                      <td bgcolor="#fcfaa4"><a href="view-finish-direksi.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a></td>
                      <td bgcolor="#fcfaa4"><?php echo $d['status']; ?></td>
                    </tr>
                  <?php
                  }
                  ?>
                </tbody>
              </table>
            </div><!-- /.table-responsive -->
          </div>
        </div>


      </div>
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


      <script type="text/javascript">
        $(document).ready(function() {
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
      </script>

</body>

</html>