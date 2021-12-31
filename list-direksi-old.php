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

$year = (int)date('Y');
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
  <!-- DataTables -->
  <link rel="stylesheet" href="datatables/dataTables.bootstrap.css">

  <style>
    iframe {
      width: 1px;
      min-width: 100%;
      *width: 100%;
    }
  </style>

  <!-- </head> -->

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
          <li class="active"><a href="list-direksi.php">List</a></li>
          <li><a href="saldobpu.php">Saldo BPU</a></li>
          <!--<li><a href="summary.php">Summary</a></li>-->
          <!-- <li><a href="hak-akses.php">Hak Akses</a></li> -->
          <li><a href="listfinish-direksi.php">Budget Finish</a></li>
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
        $cari = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE status='Pending' AND waktu != 0");
        $belbyr = mysqli_num_rows($cari);
        $caribpu = mysqli_query($koneksi, "SELECT * FROM bpu WHERE persetujuan='Belum Disetujui' AND waktu != 0");
        $bpuyahud = mysqli_num_rows($caribpu);
        $queryPengajuanReq = mysqli_query($koneksi, "SELECT * FROM pengajuan_request WHERE status_request = 'Di Ajukan' AND waktu != 0");
        $countPengajuanReq = mysqli_num_rows($queryPengajuanReq);
        $notif = $belbyr + $bpuyahud + $countPengajuanReq;
        ?>
       <ul class="nav navbar-nav navbar-right">
                        <li><a href="notif-page.php"><i class="fa fa-envelope"></i></a></li>
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
      </div>
    </div>
  </nav>


  <div class="container">

    <!-- Nav List budget 2018 - 2019 -->
    <ul class="nav nav-pills">
      <li class="active"><a data-toggle="pill" href="#2021">List Budget 2021</a></li>
      <li><a data-toggle="pill" href="#2020">List Budget 2020</a></li>
      <li><a data-toggle="pill" href="#menu1">List Budget 2019</a></li>
      <li><a data-toggle="pill" href="#list2018">List Budget 2018</a></li>
      <li><a data-toggle="pill" href="#menu2">UM Burek, Honor SHP PWT, STKB</a></li>
    </ul>

    <div class="tab-content">
      <!-- Content Nav -->

      <div id="list2018" class="tab-pane fade">
        <ul id="myTab" class="nav nav-tabs" role="tablist">
          <li class="active" role="presentation">
            <a href="#B1" id="B1-tab" role="tab" data-toggle="tab" aria-controls="B1" aria-expanded="true">Folder B1</a>
          </li>
          <li role="presentation">
            <a href="#B2" role="tab" id="B2-tab" data-toggle="tab" aria-controls="B2">Folder B2</a>
          </li>

          <li role="presentation">
            <a href="#umum" role="tab" id="umum-tab" data-toggle="tab" aria-controls="umum">Folder Biaya Umum</a>
          </li>
        </ul>

        <div id="myTabContent" class="tab-content">
          <!-- Tab -->

          <!-- Budget B1 2018 -->
          <div role="tabpanel" class="tab-pane fade in active" id="B1" aria-labelledby="home-tab">
            <?php
            require "listdireksi/b1-2018.php";
            ?>
          </div>
          <!-- //Budget B1 2018 -->


          <!-- Budget B2 2018 -->
          <div role="tabpanel" class="tab-pane fade" id="B2" aria-labelledby="B2-tab">

          </div>
          <!-- //Budget B2 2018 -->


          <!-- Budget Umum 2018 -->
          <div role="tabpanel" class="tab-pane fade" id="umum" aria-labelledby="umum-tab">
            <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">

              <div class="panel-body no-padding">

                <ul class="nav nav-tabs">
                  <li class="active"><a href="#rutin">Rutin</a>
                  </li>
                  <li><a href="#nonrutin">Non Rutin</a>
                  </li>
                </ul>

                <div class="tab-content">
                  <div class="tab-pane fade active in" id="rutin">

                  </div>
                  <div class="tab-pane fade" id="nonrutin">

                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- //Budget Umum 2018 -->

        </div>
      </div>

      <!-- 2021 -->
      <div id="2021" class="tab-pane fade active in">
        <ul id="myTab" class="nav nav-tabs" role="tablist">
          <li class="active" role="presentation">
            <a href="#B12021" id="B1-tab" role="tab" data-toggle="tab" aria-controls="B1" aria-expanded="true">Folder B1</a>
          </li>
          <li role="presentation">
            <a href="#B22021" role="tab" id="B2-tab" data-toggle="tab" aria-controls="B2">Folder B2</a>
          </li>
          <li role="presentation">
            <a href="#umum2021" role="tab" id="umum2021-tab" data-toggle="tab" aria-controls="umum2021">Folder Biaya Umum</a>
          </li>
          <li role="presentation">
            <a href="#uangmuka2021" role="tab" id="uangmuka-tab" data-toggle="tab" aria-controls="uangmuka">Rekap Monitoring Uang Muka</a>
          </li>
        </ul>

        <div id="myTabContent" class="tab-content">
          <!-- Tab -->

          <!-- Budget B1 2019 -->
          <div role="tabpanel" class="tab-pane fade in active" id="B12021" aria-labelledby="home-tab">
            <?php
            include "listdireksi/b1-2021.php";
            ?>
          </div>
          <!-- //Budget B1 2021 -->

          <!-- Budget B2 2021 -->
          <div role="tabpanel" class="tab-pane fade" id="B22021" aria-labelledby="B2-tab">
            <?php
            include "listdireksi/b2-2021.php";
            ?>
          </div>
          <!-- //Budget B2 2021 -->

          <!-- Budget Umum 2021 -->
          <div role="tabpanel" class="tab-pane fade" id="umum2021" aria-labelledby="umum-tab">
            <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">

              <div class="panel-body no-padding">

                <ul class="nav nav-tabs">
                  <li class="active"><a href="#rutin2021">Rutin</a>
                  </li>
                  <li><a href="#nonrutin2021">Non Rutin</a>
                  </li>
                </ul>

                <div class="tab-content">
                  <div class="tab-pane fade active in" id="rutin2021">
                    <?php
                    include "listdireksi/rutin-2021.php";
                    ?>
                  </div>
                  <div class="tab-pane fade" id="nonrutin2021">
                    <?php
                    include "listdireksi/nonrutin-2021.php";
                    ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- //Budget Umum 2021 -->

          <div role="tabpanel" class="tab-pane fade" id="uangmuka2021" aria-labelledby="uangmuka-tab">
            <?php
            include "listdireksi/uangmuka-2021.php";
            ?>
          </div>
          <!-- //Budget Non Rutin 2021 -->

        </div>
      </div>

      <!-- 2020 -->
      <div id="2020" class="tab-pane fade">


        <ul id="myTab" class="nav nav-tabs" role="tablist">
          <li class="active" role="presentation">
            <a href="#B12020" id="B1-tab" role="tab" data-toggle="tab" aria-controls="B1" aria-expanded="true">Folder B1</a>
          </li>
          <li role="presentation">
            <a href="#B22020" role="tab" id="B2-tab" data-toggle="tab" aria-controls="B2">Folder B2</a>
          </li>
          <li role="presentation">
            <a href="#umum2020" role="tab" id="umum-tab" data-toggle="tab" aria-controls="umum">Folder Biaya Umum</a>
          </li>
        </ul>

        <div id="myTabContent" class="tab-content">
          <!-- Tab -->

          <!-- Budget B1 2019 -->
          <div role="tabpanel" class="tab-pane fade in active" id="B12020" aria-labelledby="home-tab">
            <?php
            include "listdireksi/b1-2020.php";
            ?>
          </div>
          <!-- //Budget B1 2020 -->

          <!-- Budget B2 2020 -->
          <div role="tabpanel" class="tab-pane fade" id="B22020" aria-labelledby="B2-tab">
            <?php
            include "listdireksi/b2-2020.php";
            ?>
          </div>
          <!-- //Budget B2 2020 -->


          <!-- Budget Umum 2020 -->
          <div role="tabpanel" class="tab-pane fade" id="umum2020" aria-labelledby="umum-tab">
            <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">

              <div class="panel-body no-padding">

                <ul class="nav nav-tabs">
                  <li class="active"><a href="#rutin2020">Rutin</a>
                  </li>
                  <li><a href="#nonrutin2020">Non Rutin</a>
                  </li>
                </ul>

                <div class="tab-content">
                  <div class="tab-pane fade active in" id="rutin2020">
                    <?php
                    include "listdireksi/rutin-2020.php";
                    ?>
                  </div>
                  <div class="tab-pane fade" id="nonrutin2020">
                    <?php
                    include "listdireksi/nonrutin-2020.php";
                    ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- //Budget Umum 2020 -->

        </div>
      </div>



      <div id="menu1" class="tab-pane fade">

        <ul id="myTab" class="nav nav-tabs" role="tablist">
          <li class="active" role="presentation">
            <a href="#B12019" id="B1-tab" role="tab" data-toggle="tab" aria-controls="B1" aria-expanded="true">Folder B1</a>
          </li>
          <li role="presentation">
            <a href="#B22019" role="tab" id="B2-tab" data-toggle="tab" aria-controls="B2">Folder B2</a>
          </li>
          <li role="presentation">
            <a href="#umum2019" role="tab" id="umum-tab" data-toggle="tab" aria-controls="umum">Folder Biaya Umum</a>
          </li>
        </ul>

        <div id="myTabContent" class="tab-content">
          <!-- Tab -->

          <!-- Budget B1 2019 -->
          <div role="tabpanel" class="tab-pane fade in active" id="B12019" aria-labelledby="home-tab">
            <?php
            include "listdireksi/b1-2019.php";
            ?>
          </div>
          <!-- //Budget B1 2019 -->

          <!-- Budget B2 2019 -->
          <div role="tabpanel" class="tab-pane fade" id="B22019" aria-labelledby="B2-tab">

          </div>
          <!-- //Budget B2 2019 -->

          <!-- Budget Umum 2019 -->
          <div role="tabpanel" class="tab-pane fade" id="umum2019" aria-labelledby="umum-tab">
            <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">

              <div class="panel-body no-padding">

                <ul class="nav nav-tabs">
                  <li class="active"><a href="#rutin2019">Rutin</a>
                  </li>
                  <li><a href="#nonrutin2019">Non Rutin</a>
                  </li>
                </ul>

                <div class="tab-content">
                  <div class="tab-pane fade active in" id="rutin2019">
                  </div>
                  <div class="tab-pane fade" id="nonrutin2019">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- //Budget Umum 2019 -->

        </div>
      </div>


      <div id="menu2" class="tab-pane fade">

        <ul id="myTab" class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active">
            <a href="#umburek" role="tab" id="umburek-tab" data-toggle="tab" aria-controls="umburek">UM Burek</a>
          </li>
          <li role="presentation">
            <a href="#honor" role="tab" id="honor-tab" data-toggle="tab" aria-controls="honor">Honor SHP dan PWT</a>
          </li>
          <li role="presentation">
            <a href="#stkb" role="tab" id="stkb-tab" data-toggle="tab" aria-controls="stkb">STKB</a>
          </li>

          <div id="myTabContent" class="tab-content">
            <!-- Tab -->

            <!-- UM BUREK -->
            <div role="tabpanel" class="tab-pane fade in active" id="umburek" aria-labelledby="umburek-tab">
              <?php
              include "listdireksi/umburek.php";
              ?>
            </div>
            <!-- //UM BUREK -->

            <!-- Honor SHP PWT -->
            <div role="tabpanel" class="tab-pane fade" id="honor" aria-labelledby="honor-tab">
              <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
                <div class="panel-body no-padding">

                  <br><br>

                  <ul class="nav nav-tabs">
                    <?php for ($i = $year; $i > $year - 2; $i--) :
                      if ($i == $year) :
                    ?><li><a href="#honor<?= $i ?>" class="btn-honor"><?= $i ?></a></li>
                      <?php else : ?>
                        <li><a href="#honor<?= $i ?>" class="btn-honor"><?= $i ?></a></li>
                      <?php endif; ?>
                    <?php endfor; ?>
                  </ul>

                  <div class="tab-content honor-fetched-data">
                  </div>

                </div><!-- /.table-responsive -->
              </div>
            </div>
            <!-- //Honor SHP PWT -->

            <!-- STKB -->
            <div role="tabpanel" class="tab-pane fade" id="stkb" aria-labelledby="stkb-tab">
              <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
                <div class="panel-body no-padding">

                  <br><br>

                  <ul class="nav nav-tabs">
                    <?php for ($i = $year; $i > $year - 2; $i--) :
                      if ($i == $year) :
                    ?>
                        <li><a href="#stkb<?= $i ?>" class="btn-stkb"><?= $i ?></a></li>
                      <?php else : ?>
                        <li><a href="#stkb<?= $i ?>" class="btn-stkb"><?= $i ?></a></li>
                      <?php endif; ?>
                    <?php endfor; ?>
                  </ul>

                  <div class="tab-content stkb-fetched-data">
                  </div>
                </div><!-- /.table-responsive -->
              </div>
            </div>
            <!-- //STKB -->

          </div>
      </div>


    </div>

  </div><!-- Content Nav -->
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

  <div class="modal fade" id="myModal3" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Finish Budget</h4>
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
          <h4 class="modal-title">BPU UM Burek</h4>
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
          <h4 class="modal-title">Persetujuan BPU UM Burek</h4>
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
          <h4 class="modal-title">Dissapprove</h4>
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
      $('.btn-honor').click(function() {
        // console.log($(this).text());
        const year = $(this).text();
        $.ajax({
          type: 'post',
          url: 'ajax/ajax-honor.php',
          data: {
            year: year
          },
          success: function(data) {
            $('.honor-fetched-data').html(data);
          }
        });
      })

      $('.btn-stkb').click(function() {
        // console.log($(this).text());
        const year = $(this).text();
        $.ajax({
          type: 'post',
          url: 'ajax/ajax-stkb.php',
          data: {
            year: year
          },
          success: function(data) {
            $('.stkb-fetched-data').html(data);
          }
        });
      })

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

    $(document).ready(function() {
      $('#myModal3').on('show.bs.modal', function(e) {
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

    function bpu_um(iduser) {
      // alert(noid+' - '+waktu);
      $.ajax({
        type: 'post',
        url: 'bpuum.php',
        data: {
          iduser: iduser
        },
        success: function(data) {
          $('.fetched-data').html(data); //menampilkan data ke dalam modal
          $('#myModal4').modal();
        }
      });
    }

    function edit_budget(term, namapenerima) {
      // alert(noid+' - '+waktu);
      $.ajax({
        type: 'post',
        url: 'setuju_um.php',
        data: {
          term: term,
          namapenerima: namapenerima
        },
        success: function(data) {
          $('.fetched-data').html(data); //menampilkan data ke dalam modal
          $('#myModal5').modal();
        }
      });
    }

    $(document).ready(function() {
      $('#myModal6').on('show.bs.modal', function(e) {
        var rowid = $(e.relatedTarget).data('id');
        //menggunakan fungsi ajax untuk pengambilan data
        $.ajax({
          type: 'post',
          url: 'disapprove.php',
          data: 'rowid=' + rowid,
          success: function(data) {
            $('.fetched-data').html(data); //menampilkan data ke dalam modal
          }
        });
      });
    });

    $(document).ready(function() {
      $(".nav-tabs a").click(function() {
        $(this).tab('show');
      });
    });

    $('#B2').load('listdireksi/b2-2018.php');
    $('#rutin').load('listdireksi/rutin-2018.php');
    $('#nonrutin').load('listdireksi/nonrutin-2018.php');
    $('#B22019').load('listdireksi/b2-2019.php');
    $('#rutin2019').load('listdireksi/rutin-2019.php');
    $('#nonrutin2019').load('listdireksi/nonrutin-2019.php');
    // $('#honor').load('listdireksi/honor.php');
    // $('#stkb').load('listdireksi/stkb.php');
  </script>


  <!-- </body></html> -->

</body>

</html>