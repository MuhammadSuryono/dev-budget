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
        <a class="navbar-brand" href="home-hrd.php">Budget-Ing</a>
      </div>
      <div class="collapse navbar-collapse" id="myNavbar">
        <ul class="nav navbar-nav">
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
        $cari = mysqli_query($koneksi, "SELECT * FROM bpu WHERE status ='Belum Di Bayar' AND persetujuan !='Belum Disetujui' AND waktu != 0");
        $belbyr = mysqli_num_rows($cari);
        ?>
       <ul class="nav navbar-nav navbar-right">
                        <li><a href="/log-notifikasi-aplikasi/index.html" target="_blank"><i class="fa fa-envelope"></i></a></li>
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

    <ul id="myTab" class="nav nav-tabs" role="tablist">
      <li role="presentation" class="active">
        <a href="#B1" id="B1-tab" role="tab" data-toggle="tab" aria-controls="B1" aria-expanded="true">Folder B1</a>
      </li>
      <li role="presentation">
        <a href="#B2" role="tab" id="B2-tab" data-toggle="tab" aria-controls="B2">Folder B2</a>
      </li>
      <li role="presentation">
        <a href="#umum" role="tab" id="umum-tab" data-toggle="tab" aria-controls="umum">Folder Biaya Umum</a>
      </li>
      <!-- <li role="presentation">
        <a href="#rutin" role="tab" id="rutin-tab" data-toggle="tab" aria-controls="rutin">Rutin</a>
      </li>
      <li role="presentation">
        <a href="#nonrutin" role="tab" id="nonrutin-tab" data-toggle="tab" aria-controls="nonrutin">Non Rutin</a>
      </li> -->
      <li role="presentation">
        <a href="#uangmuka" role="tab" id="uangmuka-tab" data-toggle="tab" aria-controls="uangmuka">Rekap Monitoring Uang Muka</a>
      </li>
      <!-- <li role="presentation">
        <a href="#umburek" role="tab" id="umburek-tab" data-toggle="tab" aria-controls="umburek">Rekap Monitoring UM Burek</a>
      </li> -->
      <li role="presentation">
        <a href="#honor" role="tab" id="honor-tab" data-toggle="tab" aria-controls="honor">Rekap Monitoring Honor SHP dan PWT</a>
      </li>
    </ul>

    <div id="myTabContent" class="tab-content">
      <!-- Tab -->

      <div role="tabpanel" class="tab-pane fade in active" id="B1" aria-labelledby="home-tab">
        <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
          <div class="panel-body no-padding">
            <table class="table table-striped">
              <thead>
                <tr class="warning">
                  <th>No</th>
                  <th>Nama Project</th>
                  <th>Tahun</th>
                  <th>Nama Yang Mengajukan</th>
                  <th>Divisi</th>
                  <th>Action</th>
                  <th>Status</th>
                </tr>
              </thead>

              <tbody>

                <?php
                $i = 1;
                $checkWaktu = [];
                $sql = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE jenis ='B1' AND status !='Belum Di Ajukan'");
                while ($d = mysqli_fetch_array($sql)) {
                  $arrDocument = [];
                  $document = unserialize($d['document']);
                  if (!is_array($document)) {
                    array_push($arrDocument, $document);
                  } else {
                    $arrDocument = $document;
                  }
                  if (!in_array($d['waktu'], $checkWaktu)) :
                    if ($d['status'] == "Disetujui") {
                ?>
                      <tr>
                        <th bgcolor="#fcfaa4" scope="row"><?php echo $i++; ?></th>
                        <td bgcolor="#fcfaa4"> <?= $d['nama'] ?>
                          <?php if ($arrDocument[0]) : ?>
                            -
                            <?php
                            $j = 0;
                            foreach ($arrDocument as $ad) :
                            ?>
                              <?php if ($d['on_revision_status'] == 1) : ?>
                                <?php if ($j == count($arrDocument) - 1) : ?>
                                  <a target="_blank" href='document/<?= $ad ?>.pdf'><i class='fa fa-file' style="color: red;"></i></a>
                                <?php else : ?>
                                  <a target="_blank" href='document/<?= $ad ?>.pdf'><i class='fa fa-file'></i></a>
                                <?php endif; ?>
                              <?php else : ?>
                                <a target="_blank" href='document/<?= $ad ?>.pdf'><i class='fa fa-file'></i></a>
                              <?php endif;
                              $j++; ?>
                            <?php endforeach; ?>
                          <?php endif; ?>
                        </td>
                        <td bgcolor="#fcfaa4"><?php echo $d['tahun']; ?></td>
                        <td bgcolor="#fcfaa4"><?php echo $d['pengaju']; ?></td>
                        <td bgcolor="#fcfaa4"><?php echo $d['divisi']; ?></td>
                        <td bgcolor="#fcfaa4">
                          <?php
                          if ($aksesSes == 'Manager') {
                          ?>
                            <a href="view-finance-manager-b1.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a>
                          <?php
                          } else if ($aksesSes == 'Melinda') {
                          ?>
                            <a href="view-finance-melinda-b1.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a>
                          <?php
                          } else {
                          ?>
                            <a href="view-finance.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a>
                          <?php } ?>
                        </td>
                        <td bgcolor="#fcfaa4"><?php echo $d['status']; ?></td>
                      </tr>
                    <?php
                    } else { ?>
                      <tr>
                        <th scope="row"><?php echo $i++; ?></th>
                        <td><?php echo $d['nama']; ?></td>
                        <td><?php echo $d['tahun']; ?></td>
                        <td><?php echo $d['pengaju']; ?></td>
                        <td><?php echo $d['divisi']; ?></td>
                        <td>--</td>
                        <td><?php echo $d['status']; ?></td>
                      </tr>
                <?php }
                    array_push($checkWaktu, $d['waktu']);
                  endif;
                } ?>
              </tbody>
            </table>
          </div><!-- /.table-responsive -->
        </div>
      </div>

      <div role="tabpanel" class="tab-pane fade" id="B2" aria-labelledby="home-tab">
        <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
          <div class="panel-body no-padding">
            <table class="table table-striped">
              <thead>
                <tr class="warning">
                  <th>No</th>
                  <th>Nama Project</th>
                  <th>Tahun</th>
                  <th>Nama Yang Mengajukan</th>
                  <th>Divisi</th>
                  <th>Action</th>
                  <th>Status</th>
                </tr>
              </thead>

              <tbody>

                <?php
                $i = 1;
                $sql = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE jenis ='B2' AND status !='Belum Di Ajukan'");
                while ($d = mysqli_fetch_array($sql)) {
                  $arrDocument = [];
                  $document = unserialize($d['document']);
                  if (!is_array($document)) {
                    array_push($arrDocument, $document);
                  } else {
                    $arrDocument = $document;
                  }

                  if ($d['status'] == "Disetujui") {
                ?>
                    <tr>
                      <th bgcolor="#fcfaa4" scope="row"><?php echo $i++; ?></th>
                      <td bgcolor="#fcfaa4">
                        <?= $d['nama'] ?>
                        <?php if ($arrDocument[0]) : ?>
                          -
                          <?php
                          $j = 0;
                          foreach ($arrDocument as $ad) :
                          ?>
                            <?php if ($d['on_revision_status'] == 1) : ?>
                              <?php if ($j == count($arrDocument) - 1) : ?>
                                <a target="_blank" href='document/<?= $ad ?>.pdf'><i class='fa fa-file' style="color: red;"></i></a>
                              <?php else : ?>
                                <a target="_blank" href='document/<?= $ad ?>.pdf'><i class='fa fa-file'></i></a>
                              <?php endif; ?>
                            <?php else : ?>
                              <a target="_blank" href='document/<?= $ad ?>.pdf'><i class='fa fa-file'></i></a>
                            <?php endif;
                            $j++; ?>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </td>
                      <td bgcolor="#fcfaa4"><?php echo $d['tahun']; ?></td>
                      <td bgcolor="#fcfaa4"><?php echo $d['pengaju']; ?></td>
                      <td bgcolor="#fcfaa4"><?php echo $d['divisi']; ?></td>
                      <!--<td bgcolor="#fcfaa4"><a href="view-finance.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a></td>-->
                      <td bgcolor="#fcfaa4">
                        <?php
                        if ($aksesSes == 'Manager') {
                        ?>
                          <a href="view-finance-manager.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a>
                        <?php
                        } else if ($aksesSes == 'Melinda') {
                        ?>
                          <a href="view-finance-melinda-b1.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a>
                        <?php
                        } else {
                        ?>
                          <a href="view-finance.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a>
                        <?php } ?>
                      </td>
                      <td bgcolor="#fcfaa4"><?php echo $d['status']; ?></td>
                    </tr>
                  <?php
                  } else { ?>
                    <tr>
                      <th scope="row"><?php echo $i++; ?></th>
                      <td> <?= $d['nama'] ?>
                        <?php if ($arrDocument[0]) : ?>
                          -
                          <?php
                          $j = 0;
                          foreach ($arrDocument as $ad) :
                          ?>
                            <?php if ($d['on_revision_status'] == 1) : ?>
                              <?php if ($j == count($arrDocument) - 1) : ?>
                                <a target="_blank" href='document/<?= $ad ?>.pdf'><i class='fa fa-file' style="color: red;"></i></a>
                              <?php else : ?>
                                <a target="_blank" href='document/<?= $ad ?>.pdf'><i class='fa fa-file'></i></a>
                              <?php endif; ?>
                            <?php else : ?>
                              <a target="_blank" href='document/<?= $ad ?>.pdf'><i class='fa fa-file'></i></a>
                            <?php endif;
                            $j++; ?>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </td>
                      <td><?php echo $d['tahun']; ?></td>
                      <td><?php echo $d['pengaju']; ?></td>
                      <td><?php echo $d['divisi']; ?></td>
                      <td>--</td>
                      <td><?php echo $d['status']; ?></td>
                    </tr>
                <?php }
                } ?>
              </tbody>
            </table>
          </div><!-- /.table-responsive -->
        </div>
      </div>

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
                <ul class="nav nav-pills">
                  <li class="active"><a data-toggle="pill" href="#2021">2021</a></li>
                  <li><a data-toggle="pill" href="#2020">2020</a></li>
                  <li><a data-toggle="pill" href="#2019">2019</a></li>
                  <li><a data-toggle="pill" href="#2018">2018</a></li>
                </ul>

                <div class="tab-content">
                  <!-- Content Nav -->
                  <div id="2021" class="tab-pane fade in active">
                    <?php
                    include "listbudewi/rutin-2021.php";
                    ?>
                  </div>

                  <div id="2020" class="tab-pane fade">
                    <?php
                    include "listbudewi/rutin-2020.php";
                    ?>
                  </div>

                  <div id="2019" class="tab-pane fade">
                    <?php
                    include "listbudewi/rutin-2019.php";
                    ?>
                  </div>

                  <div id="2018" class="tab-pane fade">
                    <?php
                    include "listbudewi/rutin-2018.php";
                    ?>
                  </div>

                </div>

              </div>
              <div class="tab-pane fade" id="nonrutin">
                <table class="table table-striped">
                  <thead>
                    <tr class="warning">
                      <th>No</th>
                      <th>Nama Project</th>
                      <th>Tahun</th>
                      <th>Nama Yang Mengajukan</th>
                      <th>Divisi</th>
                      <th>Action</th>
                      <th>Status</th>
                      <!-- <th>Persetujuan</th> -->
                    </tr>
                  </thead>

                  <tbody>
                    <?php
                    $i = 1;
                    $sql = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE jenis ='Non Rutin' AND status !='Belum Di Ajukan'");
                    while ($d = mysqli_fetch_array($sql)) {
                      $arrDocument = [];
                      $document = unserialize($d['document']);
                      if (!is_array($document)) {
                        array_push($arrDocument, $document);
                      } else {
                        $arrDocument = $document;
                      }

                      if ($d['status'] == "Disetujui") {
                    ?>
                        <tr>
                          <th bgcolor="#fcfaa4" scope="row"><?php echo $i++; ?></th>
                          <td bgcolor="#fcfaa4">
                            <?= $d['nama'] ?>
                            <?php if ($arrDocument[0]) : ?>
                              -
                              <?php
                              $j = 0;
                              foreach ($arrDocument as $ad) :
                              ?>
                                <?php if ($d['on_revision_status'] == 1) : ?>
                                  <?php if ($j == count($arrDocument) - 1) : ?>
                                    <a target="_blank" href='document/<?= $ad ?>.pdf'><i class='fa fa-file' style="color: red;"></i></a>
                                  <?php else : ?>
                                    <a target="_blank" href='document/<?= $ad ?>.pdf'><i class='fa fa-file'></i></a>
                                  <?php endif; ?>
                                <?php else : ?>
                                  <a target="_blank" href='document/<?= $ad ?>.pdf'><i class='fa fa-file'></i></a>
                                <?php endif;
                                $j++; ?>
                              <?php endforeach; ?>
                            <?php endif; ?>
                          </td>
                          <td bgcolor="#fcfaa4"><?php echo $d['tahun']; ?></td>
                          <td bgcolor="#fcfaa4"><?php echo $d['pengaju']; ?></td>
                          <td bgcolor="#fcfaa4"><?php echo $d['divisi']; ?></td>
                          <td bgcolor="#fcfaa4"><a href="view-finance-nonrutin-manager.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a></td>
                          <td bgcolor="#fcfaa4"><?php echo $d['status']; ?></td>
                          <!-- <td bgcolor="#fcfaa4">-</td> -->
                        </tr>
                      <?php
                      } else if ($d['status'] == 'Pending') {
                      ?>
                        <tr>
                          <th bgcolor="#fcfaa4" scope="row"><?php echo $i++; ?></th>
                          <td bgcolor="#fcfaa4">
                            <?= $d['nama'] ?>
                            <?php if ($arrDocument[0]) : ?>
                              -
                              <?php
                              $j = 0;
                              foreach ($arrDocument as $ad) :
                              ?>
                                <?php if ($d['on_revision_status'] == 1) : ?>
                                  <?php if ($j == count($arrDocument) - 1) : ?>
                                    <a target="_blank" href='document/<?= $ad ?>.pdf'><i class='fa fa-file' style="color: red;"></i></a>
                                  <?php else : ?>
                                    <a target="_blank" href='document/<?= $ad ?>.pdf'><i class='fa fa-file'></i></a>
                                  <?php endif; ?>
                                <?php else : ?>
                                  <a target="_blank" href='document/<?= $ad ?>.pdf'><i class='fa fa-file'></i></a>
                                <?php endif;
                                $j++; ?>
                              <?php endforeach; ?>
                            <?php endif; ?>
                          </td>
                          <td bgcolor="#fcfaa4"><?php echo $d['tahun']; ?></td>
                          <td bgcolor="#fcfaa4"><?php echo $d['pengaju']; ?></td>
                          <td bgcolor="#fcfaa4"><?php echo $d['divisi']; ?></td>
                          <td bgcolor="#fcfaa4"><a href="view-finance.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a></td>
                          <td bgcolor="#fcfaa4"><?php echo $d['status']; ?></td>
                          <?php
                          echo "<td bgcolor='#fcfaa4'><a href='#myModal2' class='btn btn-default btn-small' id='custId' data-toggle='modal' data-id=" . $d['noid'] . ">Setujui</a></td>";
                          ?>
                        </tr>

                      <?php
                      } else { ?>
                        <tr>
                          <th scope="row"><?php echo $i++; ?></th>
                          <td>
                            <?= $d['nama'] ?>
                            <?php if ($arrDocument[0]) : ?>
                              -
                              <?php
                              $j = 0;
                              foreach ($arrDocument as $ad) :
                              ?>
                                <?php if ($d['on_revision_status'] == 1) : ?>
                                  <?php if ($j == count($arrDocument) - 1) : ?>
                                    <a target="_blank" href='document/<?= $ad ?>.pdf'><i class='fa fa-file' style="color: red;"></i></a>
                                  <?php else : ?>
                                    <a target="_blank" href='document/<?= $ad ?>.pdf'><i class='fa fa-file'></i></a>
                                  <?php endif; ?>
                                <?php else : ?>
                                  <a target="_blank" href='document/<?= $ad ?>.pdf'><i class='fa fa-file'></i></a>
                                <?php endif;
                                $j++; ?>
                              <?php endforeach; ?>
                            <?php endif; ?>
                          </td>
                          <td><?php echo $d['tahun']; ?></td>
                          <td><?php echo $d['pengaju']; ?></td>
                          <td><?php echo $d['divisi']; ?></td>
                          <td><a href="view-finance.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a></td>
                          <td><?php echo $d['status']; ?></td>
                          <td>-</td>
                        </tr>
                    <?php }
                    } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div role="tabpanel" class="tab-pane fade" id="uangmuka" aria-labelledby="uangmuka-tab">
        <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
          <div class="panel-body no-padding">
            <table class="table table-striped">
              <thead>
                <tr class="warning">
                  <th rowspan="2">No</th>
                  <th rowspan="2">Nama User</th>
                  <th rowspan="2">Limit</th>
                  <th colspan="3" class="text-center">Total Uang Muka</th>
                  <th rowspan="2">Sisa Limit</th>
                  <th rowspan="2">Action</th>
                </tr>
                <tr class="warning">
                  <th>Saldo Awal Outstanding</th>
                  <th>Pengajuan</th>
                  <th>Saldo Akhir Outstanding</th>
                </tr>
              </thead>

              <tbody>
                <?php
                $i = 1;
                $sql = mysqli_query($koneksi, "SELECT a.namapenerima, SUM(a.jumlah) AS total_pengajuan, c.saldo FROM bpu a JOIN selesai b ON a.waktu = b.waktu AND a.no = b.no JOIN tb_user c ON c.nama_user = a.namapenerima JOIN pengajuan d ON d.waktu = a.waktu WHERE b.status IN ('UM', 'UM Burek') AND a.status IN ('Telah Di Bayar', 'Belum Di Bayar') AND c.aktif = 'Y' GROUP BY a.namapenerima") or die(mysqli_error($koneksi));
                while ($d = mysqli_fetch_array($sql)) {
                  $sql2 = mysqli_query($koneksi, "SELECT SUM(jumlah) AS total_pengajuan FROM (SELECT DISTINCT a.* FROM bpu a JOIN selesai b ON a.waktu = b.waktu AND a.no = b.no WHERE b.status IN ('UM', 'UM Burek') AND a.status = 'Telah Di Bayar' AND a.namapenerima = '$d[namapenerima]') AS t") or die(mysqli_error($koneksi));
                  $terbayar = mysqli_fetch_assoc($sql2);
                  $sql3 = mysqli_query($koneksi, "SELECT SUM(jumlah) AS total_pengajuan FROM (SELECT DISTINCT a.* FROM bpu a JOIN selesai b ON a.waktu = b.waktu AND a.no = b.no WHERE b.status IN ('UM', 'UM Burek') AND a.status = 'Belum Di Bayar' AND a.namapenerima = '$d[namapenerima]') AS t") or die(mysqli_error($koneksi));
                  $belumTerbayar = mysqli_fetch_assoc($sql3);
                ?>
                  <tr>
                    <th bgcolor="#fcfaa4" scope="row"><?php echo $i++; ?></th>
                    <td bgcolor="#fcfaa4"><?php echo $d['namapenerima']; ?></td>
                    <td bgcolor="#fcfaa4">Rp. <?php echo number_format($d['saldo']); ?></td>
                    <td bgcolor="#fcfaa4">Rp. <?php echo number_format($terbayar['total_pengajuan']); ?></td>
                    <td bgcolor="#fcfaa4">Rp. <?php echo number_format($belumTerbayar['total_pengajuan']); ?></td>
                    <td bgcolor="#fcfaa4">Rp. <?php echo number_format($terbayar['total_pengajuan'] + $belumTerbayar['total_pengajuan']); ?></td>
                    <td bgcolor="#fcfaa4">Rp. <?php echo number_format($d['saldo'] - ($terbayar['total_pengajuan'] + $belumTerbayar['total_pengajuan'])); ?></td>
                    <td bgcolor="#fcfaa4"><a target="_blank" href="views-um.php?code=<?php echo $d['namapenerima']; ?>"><i class="fas fa-eye" title="View Detail Uang Muka"></i></a></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div><!-- /.table-responsive -->
        </div>
      </div>

      <div role="tabpanel" class="tab-pane fade" id="umburek" aria-labelledby="umburek-tab">
        <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
          <div class="panel-body no-padding">
            <table class="table table-striped table-bordered">
              <thead>
                <tr class="warning">
                  <th>#</th>
                  <th>Nama (Divisi)</th>
                  <th>Level</th>
                  <th>Limit UM</th>
                  <th>UM On Process</th>
                  <th>Saldo UM</th>
                  <th>BPU</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $i = 1;
                $carinama = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE saldo IS NOT NULL ORDER BY nama_user");
                while ($cn = mysqli_fetch_array($carinama)) {
                ?>
                  <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo $cn['nama_user'] ?> (<?php echo $cn['divisi'] ?>)</td>
                    <td><?php echo $cn['level']; ?></td>
                    <td><?php echo 'Rp. ' . number_format($cn['saldo'], 0, '', ','); ?></td>
                    <td>
                      <?php
                      $namauser = $cn['nama_user'];
                      $iduser   = $cn['id_user'];
                      $carisaldo = mysqli_query($koneksi, "SELECT sum(jumlah) AS sumjum FROM bpu WHERE namapenerima='$namauser' AND status !='Realisasi (Direksi)' AND statusbpu='UM'");
                      $cs = mysqli_fetch_array($carisaldo);
                      echo 'Rp. ' . number_format($cs['sumjum'], 0, '', ',');
                      ?>
                    </td>
                    <td>
                      <?php
                      $umproses = $cs['sumjum'];
                      $limit    = $cn['saldo'];
                      $umsisa   = $limit - $umproses;
                      echo 'Rp. ' . number_format($umsisa, 0, '', ',');
                      ?>
                    </td>
                    <td><button type="button" class="btn btn-success btn-small" onclick="bpu_um('<?php echo $iduser; ?>')">BPU</button></td>
                    <?php
                    $bpusamping = mysqli_query($koneksi, "SELECT * FROM bpu WHERE waktu='0000-00-00 00:00:00' AND namapenerima='$namauser' ORDER BY term");

                    if (mysqli_num_rows($bpusamping) == 0) {
                      echo "";
                    } else {
                      while ($bayar = mysqli_fetch_array($bpusamping)) {
                        $noidbpu          = $bayar['noid'];
                        $pengajuanJumlah = $bayar['pengajuan_jumlah'];
                        $jumlbayar        = $bayar['jumlah'];
                        $tglbyr           = $bayar['tglcair'];
                        $statusbayar      = $bayar['status'];
                        $persetujuan      = $bayar['persetujuan'];
                        $bayarfinance     = $bayar['jumlahbayar'];
                        $novoucher        = $bayar['novoucher'];
                        $tanggalbayar     = $bayar['tanggalbayar'];
                        $pengaju          = $bayar['pengaju'];
                        $divisi2          = $bayar['divisi'];
                        $namabank         = $bayar['namabank'];
                        $norek            = $bayar['norek'];
                        $namapenerima     = $bayar['namapenerima'];
                        $alasan           = $bayar['alasan'];
                        $realisasi        = $bayar['realisasi'];
                        $uangkembali      = $bayar['uangkembali'];
                        $tanggalrealisasi = $bayar['tanggalrealisasi'];
                        $waktustempel     = $bayar['waktustempel'];
                        $pembayar         = $bayar['pembayar'];
                        $tglcair          = $bayar['tglcair'];
                        $term             = $bayar['term'];
                        $statusbpu        = $bayar['statusbpu'];
                        $fileupload       = $bayar['fileupload'];
                        $pengajuan_realisasi = $bayar['pengajuan_realisasi'];
                        $pengajuan_uangkembali = $bayar['pengajuan_uangkembali'];
                        $pengajuan_tanggalrealisasi  = $bayar['pengajuan_tanggalrealisasi'];
                        $statusPengajuanRealisasi = $bayar['status_pengajuan_realisasi'];
                        $noStkb       = ($bayar['nomorstkb']) ? $bayar['nomorstkb'] : '-';
                        $kembreal         = $realisasi + $uangkembali;
                        $sisarealisasi    = $jumlbayar - $kembreal;
                        $nampro           = $bayar['project'];
                        $jatuhtempo       = $bayar['jatuhtempo'];
                        $statusPengajuanBpu = $bayar['status_pengajuan_bpu'];


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

                        if ($persetujuan == 'Belum Disetujui' && $statusbayar == 'Belum Di Bayar') {
                          $color = '#ffd3d3';
                        } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)') && $statusbayar == 'Belum Di Bayar') {
                          $color = 'orange';
                        } else if ($persetujuan == 'Pending' && $statusbayar == 'Belum Di Bayar') {
                          $color = 'orange';
                        } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)') && $statusbayar == 'Telah Di Bayar' && ($exin == 'Honor Eksternal' || $exin == 'Vendor/Supplier' || $exin == 'Lumpsum')) {
                          $color = '#d5f9bd';
                        } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)') && $statusbayar == 'Telah Di Bayar' && ($exin == 'Pulsa' || $exin == 'Biaya External' || $exin == 'Biaya' || $exin == 'Biaya Lumpsum')) {
                          $color = '#d5f9bd';
                        } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)') && $statusbayar == 'Telah Di Bayar' && $exin == 'UM') {
                          $color = '#8aad70';
                        } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)') && $statusbayar == 'Realisasi (Direksi)' && $exin == 'UM') {
                          $color = '#d5f9bd';
                        } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)') && $statusbayar == 'Realisasi (Finance)' && $exin == 'UM') {
                          $color = '#d5f9bd';
                        }

                        if ($statusPengajuanBpu == 0 && ($persetujuan == 'Belum Disetujui' && $statusbayar == 'Belum Di Bayar')) {
                          $color = 'orange';
                        } else if ($statusPengajuanBpu == 2) {
                          $color = 'red';
                        }

                        if ($statusPengajuanRealisasi == 1) {
                          $color = '#8aad70';
                        } else if ($statusPengajuanRealisasi == 2) {
                          $color = 'red';
                        } else if ($statusPengajuanBpu == 3) {
                          $color = 'orange';
                        }

                        echo "<td bgcolor=' $color '>";
                        echo "No :<b> $term";
                        echo "</b><br>";
                        echo "No. STKB :<b> $noStkb";
                        echo "</b><br>";
                        echo ($statusPengajuanBpu != 0) ? "Request BPU : <br><b>Rp. " . number_format($pengajuanJumlah, 0, '', ',') : "BPU : <br><b>Rp. " . number_format($jumlbayar, 0, '', ',');
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
                        echo "Dibuat Oleh : <br><b> $pengaju($divisi2)";
                        echo "</b><br>";
                        echo "Project : <br><b> $nampro";
                        echo "</b><br>";
                        echo "Jatuh Tempo : <br><b> $jatuhtempo";
                        echo "</b><br>";
                        echo "Dibayarkan Kepada : <br><b> $namapenerima ";
                        echo "</b><br>";
                        echo "No Rekening :<b> $norek";
                        echo "</b><br>";
                        echo "Bank :<b> $namabank";
                        echo "</b><br>";
                        echo "No Voucher : <br><b> $novoucher ";
                        echo "</b><br/>";
                        echo "Tgl Bayar : <br><b> $tanggalbayar";
                        echo "</b><br/>";
                        echo "Kasir : <br><b> $pembayar ";
                        echo "</b><br/>";
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

                        if ($persetujuan == 'Belum Disetujui' && $statusbayar == 'Belum Di Bayar') {
                          echo "<i class='far fa-check-square'></i> Pengajuan ";
                          echo "</b><br/>";
                          echo "<i class='far fa-square'></i> Approval ";
                          echo "</b><br/>";
                          echo "<i class='far fa-square'></i> Paid ";
                          echo "</b><br/>";
                        } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)') && $statusbayar == 'Belum Di Bayar') {
                          echo "<i class='far fa-check-square'></i> Pengajuan";
                          echo "</b><br/>";
                          echo "<i class='far fa-check-square'></i> Approval";
                          echo "</b><br/>";
                          echo "<i class='far fa-square'></i> Paid ";
                          echo "</b><br/>";
                        } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)') && ($statusbayar == 'Telah Di Bayar' || $statusbayar == 'Realisasi (Finance)' || $statusbayar == 'Realisasi (Direksi)')) {
                          echo "<i class='far fa-check-square'></i> Pengajuan";
                          echo "</b><br/>";
                          echo "<i class='far fa-check-square'></i> Approval";
                          echo "</b><br/>";
                          echo "<i class='far fa-check-square'></i> Paid ";
                          echo "</b><br/>";
                        }
                        if ($statusPengajuanRealisasi != 4 && !($exin == 'Honor Eksternal' || $exin == 'Vendor/Supplier' || $exin == 'Lumpsum' || $exin == 'Honor SHP Jabodetabek' ||
                          $exin == 'Honor SHI/PWT Jabodetabek' || $exin == 'Honor SHP Luar Kota' || $exin == 'Honor SHI/PWT Luar Kota' ||
                          $exin == 'Honor Jakarta' || $exin == 'Honor Luar Kota' || $exin == 'STKB TRK Jakarta' || $exin == 'STKB TRK Luar Kota' || $exin == 'STKB OPS')) {
                          echo "<i class='far fa-square'></i> Realisasi ";
                          echo "</b><br/>";
                        } else {
                          echo "<i class='far fa-check-square'></i> Realisasi ";
                          echo "</b><br/>";
                        }


                        if ($persetujuan == 'Pending' || $persetujuan == 'Belum Disetujui') {
                          echo "Komentar : <br><b> $alasan ";
                          echo "</b><br/>";
                    ?>
                          <button type="button" class="btn btn-success btn-small" onclick="edit_budget('<?php echo $term; ?>','<?php echo $namapenerima; ?>')">Setujui</button>
                          </br>
                          <button type="button" class="btn btn-warning btn-small" onclick="editharga('<?php echo $noidbpu; ?>','<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Edit</button>
                          </br>
                          <button type="button" class="btn btn-danger btn-small" onclick="hapus_bpu('<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Hapus</button>
                        <?php
                        } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)') and $statusbayar == 'Belum Di Bayar') {
                        ?>
                          <button type="button" class="btn btn-warning btn-small" onclick="editharga('<?php echo $noidbpu; ?>','<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Edit</button>
                          </br>
                          <button type="button" class="btn btn-danger btn-small" onclick="hapus_bpu('<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Hapus</button>

                        <?php
                        } else if ($statusbayar == 'Realisasi (Finance)' || $statusbayar == 'Realisasi (Direksi)' || $uangkembali != 0) {
                          echo "Uang Kembali :<br><b> Rp. " . number_format($uangkembali, 0, '', ',');
                          echo "</b><br/>";
                        ?>
                          <button type="button" class="btn btn-warning btn-small" onclick="editharga('<?php echo $noidbpu; ?>','<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Edit</button>
                          </br>
                          <button type="button" class="btn btn-danger btn-small" onclick="hapus_bpu('<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Hapus</button>
                        <?php
                        } else {
                        ?>
                          <button type="button" class="btn btn-warning btn-small" onclick="editharga('<?php echo $noidbpu; ?>','<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Edit</button>
                          </br>
                          <button type="button" class="btn btn-danger btn-small" onclick="hapus_bpu('<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Hapus</button>
                    <?php
                        }
                        echo "</td>";
                      }
                    } ?>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div><!-- /.table-responsive -->
        </div>
      </div>


      <div role="tabpanel" class="tab-pane fade" id="honor" aria-labelledby="honor-tab">
        <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
          <div class="panel-body no-padding">
            <table class="table table-striped table-bordered">
              <thead>
                <tr class="warning">
                  <th>#</th>
                  <th>No Item</th>
                  <th>Project</th>
                  <th>Rincian</th>
                  <th>Jenis</th>
                  <th>Harga Satuan (IDR)</th>
                  <th>Quantity</th>
                  <th>Total (IDR)</th>
                  <th>Sisa Pembayaran</th>
                  <th>Action</th>
                </tr>
              </thead>

              <tbody>
                <?php
                $i = 1;
                $carihonor = mysqli_query($koneksi, "SELECT * FROM selesai WHERE rincian LIKE '%honor%' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1' AND status='Disetujui')");
                while ($ch = mysqli_fetch_array($carihonor)) {
                ?>
                  <tr>
                    <td><?php echo $i++ ?></td>
                    <td><?php echo $ch['no']; ?></td>
                    <td>
                      <?php
                      $waks = $ch['waktu'];
                      $carinama = mysqli_query($koneksi, "SELECT nama FROM pengajuan WHERE waktu='$waks'");
                      $cn = mysqli_fetch_assoc($carinama);
                      echo $cn['nama'];
                      ?>
                    </td>
                    <td><?php echo $ch['rincian']; ?></td>
                    <td><?php echo $ch['status']; ?></td>
                    <td><?php echo 'Rp. ' . number_format($ch['harga'], 0, '', ','); ?></td>
                    <td><?php echo $ch['quantity']; ?></td>
                    <td><?php echo 'Rp. ' . number_format($ch['total'], 0, '', ','); ?></td>
                    <!-- Sisa Pembayaran -->
                    <?php
                    $no = $ch['no'];
                    $waktu = $ch['waktu'];
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

                    $jadinya = $hargaah - $total + $total16;
                    ?>
                    <td><?php echo 'Rp. ' . number_format($jadinya, 0, '', ','); ?></td>
                    <!-- //Sisa Pembayaran -->

                    <!-- TOMBOL -->
                    <td>

                    </td>
                    <!-- //TOMBOL -->

                    <!-- BPU -->
                    <?php
                    $liatbayar = mysqli_query($koneksi, "SELECT * FROM bpu WHERE waktu='$waktu' AND no='$no' ORDER BY term");
                    if (mysqli_num_rows($liatbayar) == 0) {
                      echo "";
                    } else {
                      while ($bayar = mysqli_fetch_array($liatbayar)) {
                        $noidbpu          = $bayar['noid'];
                        $jumlbayar        = $bayar['jumlah'];
                        $pengajuanJumlah = $bayar['pengajuan_jumlah'];
                        $tglbyr           = $bayar['tglcair'];
                        $statusbayar      = $bayar['status'];
                        $persetujuan      = $bayar['persetujuan'];
                        $bayarfinance     = $bayar['jumlahbayar'];
                        $novoucher        = $bayar['novoucher'];
                        $tanggalbayar     = $bayar['tanggalbayar'];
                        $pengaju          = $bayar['pengaju'];
                        $divisi2          = $bayar['divisi'];
                        $namabank         = $bayar['namabank'];
                        $norek            = $bayar['norek'];
                        $namapenerima     = $bayar['namapenerima'];
                        $alasan           = $bayar['alasan'];
                        $realisasi        = $bayar['realisasi'];
                        $uangkembali      = $bayar['uangkembali'];
                        $tanggalrealisasi = $bayar['tanggalrealisasi'];
                        $waktustempel     = $bayar['waktustempel'];
                        $pembayar         = $bayar['pembayar'];
                        $tglcair          = $bayar['tglcair'];
                        $term             = $bayar['term'];
                        $statusbpu        = $bayar['statusbpu'];
                        $fileupload       = $bayar['fileupload'];
                        $pengajuan_realisasi = $bayar['pengajuan_realisasi'];
                        $pengajuan_uangkembali = $bayar['pengajuan_uangkembali'];
                        $pengajuan_tanggalrealisasi  = $bayar['pengajuan_tanggalrealisasi'];
                        $statusPengajuanRealisasi = $bayar['status_pengajuan_realisasi'];
                        $noStkb       = ($bayar['nomorstkb']) ? $bayar['nomorstkb'] : '-';
                        $kembreal         = $realisasi + $uangkembali;
                        $sisarealisasi    = $jumlbayar - $kembreal;
                        $statusPengajuanBpu = $bayar['status_pengajuan_bpu'];


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

                        if ($persetujuan == 'Belum Disetujui' && $statusbayar == 'Belum Di Bayar') {
                          $color = '#ffd3d3';
                        } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)') && $statusbayar == 'Belum Di Bayar') {
                          $color = 'orange';
                        } else if ($persetujuan == 'Pending' && $statusbayar == 'Belum Di Bayar') {
                          $color = 'orange';
                        } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)') && $statusbayar == 'Telah Di Bayar' && ($exin == 'Honor Eksternal' || $exin == 'Vendor/Supplier' || $exin == 'Lumpsum')) {
                          $color = '#d5f9bd';
                        } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)') && $statusbayar == 'Telah Di Bayar' && ($exin == 'Pulsa' || $exin == 'Biaya External' || $exin == 'Biaya' || $exin == 'Biaya Lumpsum')) {
                          $color = '#d5f9bd';
                        } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)') && $statusbayar == 'Telah Di Bayar' && $exin == 'UM') {
                          $color = '#8aad70';
                        } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)') && $statusbayar == 'Realisasi (Direksi)' && $exin == 'UM') {
                          $color = '#d5f9bd';
                        } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)') && $statusbayar == 'Realisasi (Finance)' && $exin == 'UM') {
                          $color = '#d5f9bd';
                        }
                        if ($statusPengajuanRealisasi == 11) {
                          $color = 'orange';
                        }

                        if ($statusPengajuanBpu == 1) {
                          $color = '#ffd3d3';
                        } else if ($statusPengajuanBpu == 2) {
                          $color = 'red';
                        }
                        echo "<td bgcolor=' $color '>";
                        echo "No :<b> $term";
                        echo "</b><br>";
                        echo "No. STKB :<b> $noStkb";
                        echo "</b><br>";
                        echo ($statusPengajuanBpu != 0) ? "Request BPU : <br><b>Rp. " . number_format($pengajuanJumlah, 0, '', ',') : "BPU : <br><b>Rp. " . number_format($jumlbayar, 0, '', ',');
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
                        echo "Dibuat Oleh : <br><b> $pengaju($divisi2)";
                        echo "</b><br>";
                        echo "Dibayarkan Kepada : <br><b> $namapenerima ";
                        echo "</b><br>";
                        echo "No Rekening :<b> $norek";
                        echo "</b><br>";
                        echo "Bank :<b> $namabank";
                        echo "</b><br>";
                        echo "No Voucher : <br><b> $novoucher ";
                        echo "</b><br/>";
                        echo "Tgl Bayar : <br><b> $tanggalbayar";
                        echo "</b><br/>";
                        echo "Kasir : <br><b> $pembayar ";
                        echo "</b><br/>";
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

                        if ($persetujuan == 'Belum Disetujui' && $statusbayar == 'Belum Di Bayar') {
                          echo "<i class='far fa-check-square'></i> Pengajuan ";
                          echo "</b><br/>";
                          echo "<i class='far fa-square'></i> Approval ";
                          echo "</b><br/>";
                          echo "<i class='far fa-square'></i> Paid ";
                          echo "</b><br/>";
                        } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)') && $statusbayar == 'Belum Di Bayar') {
                          echo "<i class='far fa-check-square'></i> Pengajuan";
                          echo "</b><br/>";
                          echo "<i class='far fa-check-square'></i> Approval";
                          echo "</b><br/>";
                          echo "<i class='far fa-square'></i> Paid ";
                          echo "</b><br/>";
                        } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)') && ($statusbayar == 'Telah Di Bayar' || $statusbayar == 'Realisasi (Finance)' || $statusbayar == 'Realisasi (Direksi)')) {
                          echo "<i class='far fa-check-square'></i> Pengajuan";
                          echo "</b><br/>";
                          echo "<i class='far fa-check-square'></i> Approval";
                          echo "</b><br/>";
                          echo "<i class='far fa-check-square'></i> Paid ";
                          echo "</b><br/>";
                        }
                        if ($statusPengajuanRealisasi != 4 && !($exin == 'Honor Eksternal' || $exin == 'Vendor/Supplier' || $exin == 'Lumpsum' || $exin == 'Honor SHP Jabodetabek' ||
                          $exin == 'Honor SHI/PWT Jabodetabek' || $exin == 'Honor SHP Luar Kota' || $exin == 'Honor SHI/PWT Luar Kota' ||
                          $exin == 'Honor Jakarta' || $exin == 'Honor Luar Kota' || $exin == 'STKB TRK Jakarta' || $exin == 'STKB TRK Luar Kota' || $exin == 'STKB OPS')) {
                          echo "<i class='far fa-square'></i> Realisasi ";
                          echo "</b><br/>";
                        } else {
                          echo "<i class='far fa-check-square'></i> Realisasi ";
                          echo "</b><br/>";
                        }


                        if ($persetujuan == 'Pending' || $persetujuan == 'Belum Disetujui') {
                          echo "Komentar : <br><b> $alasan ";
                          echo "</b><br/>";
                    ?>
                          <button type="button" class="btn btn-success btn-small" onclick="edit_budget('<?php echo $term; ?>','<?php echo $namapenerima ?>')">Setujui</button>
                          </br>
                          <button type="button" class="btn btn-warning btn-small" onclick="editharga('<?php echo $noidbpu; ?>','<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Edit</button>
                          </br>
                          <button type="button" class="btn btn-danger btn-small" onclick="hapus_bpu('<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Hapus</button>
                        <?php
                        } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)') and $statusbayar == 'Belum Di Bayar') {
                        ?>
                          <button type="button" class="btn btn-warning btn-small" onclick="editharga('<?php echo $noidbpu; ?>','<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Edit</button>
                          </br>
                          <button type="button" class="btn btn-danger btn-small" onclick="hapus_bpu('<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Hapus</button>

                        <?php
                        } else if ($statusbayar == 'Realisasi (Finance)' || $statusbayar == 'Realisasi (Direksi)' || $uangkembali != 0) {
                          echo "Uang Kembali :<br><b> Rp. " . number_format($uangkembali, 0, '', ',');
                          echo "</b><br/>";
                        ?>
                          <button type="button" class="btn btn-warning btn-small" onclick="editharga('<?php echo $noidbpu; ?>','<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Edit</button>
                          </br>
                          <button type="button" class="btn btn-danger btn-small" onclick="hapus_bpu('<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Hapus</button>
                        <?php
                        } else {
                        ?>
                          <button type="button" class="btn btn-warning btn-small" onclick="editharga('<?php echo $noidbpu; ?>','<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Edit</button>
                          </br>
                          <button type="button" class="btn btn-danger btn-small" onclick="hapus_bpu('<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Hapus</button>
                    <?php
                        }
                        echo "</td>";
                      }
                    }
                    ?>
                    <!-- //BPU -->
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div><!-- /.table-responsive -->
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

    <div class="modal fade" id="myModal" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Persetujuan BPU</h4>
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
      $("ul.nav-tabs a").click(function(e) {
        e.preventDefault();
        $(this).tab('show');
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
            $('#myModal').modal();
          }
        });
      }

      $(document).ready(function() {
        $('#myModal2').on('show.bs.modal', function(e) {
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
    </script>

</body>

</html>