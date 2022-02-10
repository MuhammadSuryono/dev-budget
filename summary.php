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
        <a class="navbar-brand" href="home-direksi.php">Budget-Ing</a>
      </div>
      <div class="collapse navbar-collapse" id="myNavbar">
        <ul class="nav navbar-nav">
          <li><a href="home-direksi.php">Home</a></li>
          <li><a href="list-direksi.php">List</a></li>
          <li><a href="saldobpu.php">Saldo BPU</a></li>
          <li class="active"><a href="summary.php">Summary</a></li>
          <li><a href="listfinish-direksi.php">Budget Finish</a></li>
          <!-- <li><a href="history-direksi.php">History</a></li> -->
        </ul>

        <?php
        $cari = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE status='Pending'");
        $belbyr = mysqli_num_rows($cari);
        $caribpu = mysqli_query($koneksi, "SELECT * FROM bpu WHERE persetujuan='Belum Disetujui'");
        $bpuyahud = mysqli_num_rows($caribpu);
        $notif = $belbyr + $bpuyahud;
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
            </ul>
          </li>

          <li><a href="ubahpassword.php"><span class="glyphicon glyphicon-user"></span><?php echo $_SESSION['nama_user']; ?> (<?php echo $_SESSION['divisi']; ?>)</a></li>
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
    </ul>

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
                  <th>Total Jumlah</th>
                  <th>Total Yang Belum Dibayar</th>
                  <th>Total Yang Sudah Dibayar</th>
                  <th>Ready To Pay</th>
                  <th>BPU Belum Di Setujui</th>
                  <th>Action</th>
                </tr>
              </thead>

              <tbody>

                <?php
                
                $i = 1;
                $sql = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE jenis='B1' AND status ='Disetujui'");
                while ($d = mysqli_fetch_array($sql)) {
                ?>
                  <tr>
                    <th scope="row"><?php echo $i++; ?></th>
                    <td><?php echo $d['nama']; ?></td>
                    <td><?php echo 'Rp. ' . number_format($d['totalbudget'], 0, '', ','); ?></td>

                    <?php
                    $waktu = $d['waktu'];
                    $query2 = "SELECT sum(jumlahbayar) AS sumasum FROM bpu WHERE waktu='$waktu'";
                    $result2 = mysqli_query($koneksi, $query2);
                    $row2 = mysqli_fetch_array($result2);

                    $query10 = "SELECT sum(uangkembali) AS sum FROM bpu WHERE waktu='$waktu'";
                    $result10 = mysqli_query($koneksi, $query10);
                    $row10 = mysqli_fetch_array($result10);
                    $tysb = $row2['sum'] - $row10['sum'];

                    $aaaa = $d['totalbudget'];
                    $bbbb = $row2['sumasum'];
                    $belumbayar = $aaaa - $bbbb;

                    $query3 = "SELECT sum(jumlah) AS sumi FROM bpu WHERE waktu='$waktu' AND persetujuan='Disetujui (Direksi)' AND status='Belum Di Bayar'";
                    $result3 = mysqli_query($koneksi, $query3);
                    $row3 = mysqli_fetch_array($result3);

                    $query12 = "SELECT sum(jumlah) AS sumin FROM bpu WHERE waktu='$waktu' AND persetujuan='Belum Disetujui' AND status='Belum Di Bayar'";
                    $result12 = mysqli_query($koneksi, $query12);
                    $row12 = mysqli_fetch_array($result12);

                    ?>

                    <td>
                      <font color="#f23f2b"><?php echo 'Rp. ' . number_format($belumbayar, 0, '', ','); ?></font>
                      </font>
                    </td>
                    <td>
                      <font color="#1bd34f"><?php echo 'Rp. ' . number_format($row2['sumasum'], 0, '', ','); ?></font>
                    </td>
                    <td>
                      <font color="#fcce00"><?php echo 'Rp. ' . number_format($row3['sumi'], 0, '', ','); ?></font>
                    </td>
                    <td>
                      <font color="#e87935"><?php echo 'Rp. ' . number_format($row12['sumin'], 0, '', ','); ?></font>
                    </td>
                    <?php echo "<td><a href='#myModal' class='btn btn-danger btn-small' id='custId' data-toggle='modal' data-id=" . $d['noid'] . ">Finish Budget</a></td>"; ?>
                  </tr>
                <?php } ?>
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
                  <th>Total Jumlah</th>
                  <th>Total Yang Belum Dibayar</th>
                  <th>Total Yang Sudah Dibayar</th>
                  <th>Ready To Pay</th>
                  <th>BPU Belum Di Setujui</th>
                  <th>Action</th>
                </tr>
              </thead>

              <tbody>

                <?php
                
                $i = 1;
                $sql = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE jenis='B2' AND status ='Disetujui'");
                while ($d = mysqli_fetch_array($sql)) {
                ?>
                  <tr>
                    <th scope="row"><?php echo $i++; ?></th>
                    <td><?php echo $d['nama']; ?></td>
                    <td><?php echo 'Rp. ' . number_format($d['totalbudget'], 0, '', ','); ?></td>

                    <?php
                    $waktu = $d['waktu'];
                    $query2 = "SELECT sum(jumlahbayar) AS sumasum FROM bpu WHERE waktu='$waktu'";
                    $result2 = mysqli_query($query2);
                    $row2 = mysqli_fetch_array($result2);

                    $query10 = "SELECT sum(uangkembali) AS sum FROM bpu WHERE waktu='$waktu'";
                    $result10 = mysqli_query($query10);
                    $row10 = mysqli_fetch_array($result10);
                    $tysb = $row2['sum'] - $row10['sum'];

                    $aaaa = $d['totalbudget'];
                    $bbbb = $row2['sumasum'];
                    $belumbayar = $aaaa - $bbbb;

                    $query3 = "SELECT sum(jumlah) AS sumi FROM bpu WHERE waktu='$waktu' AND persetujuan='Disetujui (Direksi)' AND status='Belum Di Bayar'";
                    $result3 = mysqli_query($query3);
                    $row3 = mysqli_fetch_array($result3);

                    $query12 = "SELECT sum(jumlah) AS sumin FROM bpu WHERE waktu='$waktu' AND persetujuan='Belum Disetujui' AND status='Belum Di Bayar'";
                    $result12 = mysqli_query($query12);
                    $row12 = mysqli_fetch_array($result12);

                    ?>

                    <td>
                      <font color="#f23f2b"><?php echo 'Rp. ' . number_format($belumbayar, 0, '', ','); ?></font>
                      </font>
                    </td>
                    <td>
                      <font color="#1bd34f"><?php echo 'Rp. ' . number_format($row2['sumasum'], 0, '', ','); ?></font>
                    </td>
                    <td>
                      <font color="#fcce00"><?php echo 'Rp. ' . number_format($row3['sumi'], 0, '', ','); ?></font>
                    </td>
                    <td>
                      <font color="#e87935"><?php echo 'Rp. ' . number_format($row12['sumin'], 0, '', ','); ?></font>
                    </td>
                    <?php echo "<td><a href='#myModal' class='btn btn-danger btn-small' id='custId' data-toggle='modal' data-id=" . $d['noid'] . ">Finish Budget</a></td>"; ?>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div><!-- /.table-responsive -->
        </div>
      </div>

      <div role="tabpanel" class="tab-pane fade" id="rutin" aria-labelledby="rutin-tab">
        <br /><br />
        <a href="rekaprutin.php">Rekap Budget Rutin</a>
        <br /><br />
        <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
          <div class="panel-body no-padding">
            <table class="table table-striped table-bordered">
              <thead>
                <tr class="warning">
                  <th>No</th>
                  <th>Nama Project</th>
                  <th>Total Jumlah</th>
                  <th>Total Yang Belum Dibayar</th>
                  <th>Total Yang Sudah Dibayar</th>
                  <th>Ready To Pay</th>
                  <th>BPU Belum Di Setujui</th>
                  <th>Action</th>
                </tr>
              </thead>

              <tbody>

                <?php
                
                $i = 1;
                $sql = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE jenis='Rutin' AND status ='Disetujui'");
                while ($d = mysqli_fetch_array($sql)) {
                ?>
                  <tr>
                    <th scope="row"><?php echo $i++; ?></th>
                    <td><?php echo $d['nama']; ?></td>
                    <td><?php echo 'Rp. ' . number_format($d['totalbudget'], 0, '', ','); ?></td>

                    <?php
                    $waktu = $d['waktu'];
                    $query2 = "SELECT sum(jumlahbayar) AS sumasum FROM bpu WHERE waktu='$waktu'";
                    $result2 = mysqli_query($query2);
                    $row2 = mysqli_fetch_array($result2);

                    $query10 = "SELECT sum(uangkembali) AS sum FROM bpu WHERE waktu='$waktu'";
                    $result10 = mysqli_query($query10);
                    $row10 = mysqli_fetch_array($result10);
                    $tysb = $row2['sum'] - $row10['sum'];

                    $aaaa = $d['totalbudget'];
                    $bbbb = $row2['sumasum'];
                    $belumbayar = $aaaa - $bbbb;

                    $query3 = "SELECT sum(jumlah) AS sumi FROM bpu WHERE waktu='$waktu' AND persetujuan='Disetujui (Direksi)' AND status='Belum Di Bayar'";
                    $result3 = mysqli_query($query3);
                    $row3 = mysqli_fetch_array($result3);

                    $query12 = "SELECT sum(jumlah) AS sumin FROM bpu WHERE waktu='$waktu' AND persetujuan='Belum Disetujui' AND status='Belum Di Bayar'";
                    $result12 = mysqli_query($query12);
                    $row12 = mysqli_fetch_array($result12);

                    ?>

                    <td>
                      <font color="#f23f2b"><?php echo 'Rp. ' . number_format($belumbayar, 0, '', ','); ?></font>
                      </font>
                    </td>
                    <td>
                      <font color="#1bd34f"><?php echo 'Rp. ' . number_format($row2['sumasum'], 0, '', ','); ?></font>
                    </td>
                    <td>
                      <font color="#fcce00"><?php echo 'Rp. ' . number_format($row3['sumi'], 0, '', ','); ?></font>
                    </td>
                    <td>
                      <font color="#e87935"><?php echo 'Rp. ' . number_format($row12['sumin'], 0, '', ','); ?></font>
                    </td>
                    <?php echo "<td><a href='#myModal' class='btn btn-danger btn-small' id='custId' data-toggle='modal' data-id=" . $d['noid'] . ">Finish Budget</a></td>"; ?>
                  </tr>
                <?php } ?>
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
                  <th>Total Jumlah</th>
                  <th>Total Yang Belum Dibayar</th>
                  <th>Total Yang Sudah Dibayar</th>
                  <th>Ready To Pay</th>
                  <th>BPU Belum Di Setujui</th>
                  <th>Action</th>
                </tr>
              </thead>

              <tbody>

                <?php
                
                $i = 1;
                $sql = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE jenis='Non Rutin' AND status ='Disetujui'");
                while ($d = mysqli_fetch_array($sql)) {
                ?>
                  <tr>
                    <th scope="row"><?php echo $i++; ?></th>
                    <td><?php echo $d['nama']; ?></td>
                    <td><?php echo 'Rp. ' . number_format($d['totalbudget'], 0, '', ','); ?></td>

                    <?php
                    $waktu = $d['waktu'];
                    $query2 = "SELECT sum(jumlahbayar) AS sumasum FROM bpu WHERE waktu='$waktu'";
                    $result2 = mysqli_query($query2);
                    $row2 = mysqli_fetch_array($result2);

                    $query10 = "SELECT sum(uangkembali) AS sum FROM bpu WHERE waktu='$waktu'";
                    $result10 = mysqli_query($query10);
                    $row10 = mysqli_fetch_array($result10);
                    $tysb = $row2['sum'] - $row10['sum'];

                    $aaaa = $d['totalbudget'];
                    $bbbb = $row2['sumasum'];
                    $belumbayar = $aaaa - $bbbb;

                    $query3 = "SELECT sum(jumlah) AS sumi FROM bpu WHERE waktu='$waktu' AND persetujuan='Disetujui (Direksi)' AND status='Belum Di Bayar'";
                    $result3 = mysqli_query($query3);
                    $row3 = mysqli_fetch_array($result3);

                    $query12 = "SELECT sum(jumlah) AS sumin FROM bpu WHERE waktu='$waktu' AND persetujuan='Belum Disetujui' AND status='Belum Di Bayar'";
                    $result12 = mysqli_query($query12);
                    $row12 = mysqli_fetch_array($result12);

                    ?>

                    <td>
                      <font color="#f23f2b"><?php echo 'Rp. ' . number_format($belumbayar, 0, '', ','); ?></font>
                      </font>
                    </td>
                    <td>
                      <font color="#1bd34f"><?php echo 'Rp. ' . number_format($row2['sumasum'], 0, '', ','); ?></font>
                    </td>
                    <td>
                      <font color="#fcce00"><?php echo 'Rp. ' . number_format($row3['sumi'], 0, '', ','); ?></font>
                    </td>
                    <td>
                      <font color="#e87935"><?php echo 'Rp. ' . number_format($row12['sumin'], 0, '', ','); ?></font>
                    </td>
                    <?php echo "<td><a href='#myModal' class='btn btn-danger btn-small' id='custId' data-toggle='modal' data-id=" . $d['noid'] . ">Finish Budget</a></td>"; ?>
                  </tr>
                <?php } ?>
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


    <script type="text/javascript">
      $(document).ready(function() {
        $('#myModal').on('show.bs.modal', function(e) {
          var rowid = $(e.relatedTarget).data('id');
          //menggunakan fungsi ajax untuk pengambilan data
          $.ajax({
            type: 'post',
            url: 'finish.php',
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