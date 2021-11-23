<?php
// error_reporting(0);
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
          <?php if ($_SESSION['divisi'] == 'FINANCE') : ?>
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
            <li><a href="saldobpu.php">Data User</a></li>
            <li><a href="history-finance.php">History</a></li>
            <li class="active"><a href="list.php">Personal</a></li>
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
            <?php
            if ($_SESSION['hak_page'] == 'Suci') {
              echo "<li><a href='rekap-project.php'>Rekap Project</a></li>";
            } else {
              echo "";
            }
            ?>
          <?php else : ?>
            <li><a href="home.php">Home</a></li>
            <li class="active"><a href="list.php">List</a></li>
            <!-- <li><a href="request-budget.php">Request Budget</a></li> -->
            <!-- <li><a href="history.php">History</a></li> -->
          <?php endif; ?>
        </ul>

        <?php
        $pengaju = $_SESSION['nama_user'];
        $cari = mysqli_query($koneksi, "SELECT * FROM bpu WHERE pengaju ='$pengaju' AND persetujuan ='Belum Disetujui' OR pengaju ='$pengaju' AND persetujuan ='Pending'");
        $belbyr = mysqli_num_rows($cari);
        $queryPengajuanReq = mysqli_query($koneksi, "SELECT * FROM pengajuan_request WHERE (status_request = 'Ditolak' OR status_request = 'Belum Di Ajukan') AND pengaju='$pengaju' AND waktu != 0");
        $countPengajuanReq = mysqli_num_rows($queryPengajuanReq);
        $totalNotif = $belbyr + $countPengajuanReq;
        ?>
        <ul class="nav navbar-nav navbar-right">
          <li class="dropdown messages-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-inbox"></i><span class="label label-warning"><?= $totalNotif ?></span></a>
            <ul class="dropdown-menu">
              <?php
              while ($wkt = mysqli_fetch_array($cari)) {
                $wktulang = $wkt['waktu'];
                $selectnoid = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE waktu='$wktulang'");
                $noid = mysqli_fetch_assoc($selectnoid);
                $kode = $noid['noid'];
                $project = $noid['nama'];
              ?>
                <li class="header"><a href="view.php?code=<?= $kode ?>">Project <b><?= $project ?></b> BPU Belum Dibayar</a></li>
              <?php
              }
              ?>
              <?php
              while ($qpr = mysqli_fetch_array($queryPengajuanReq)) {
                $time = $qpr['waktu'];
                $selectnoid3 = mysqli_query($koneksi, "SELECT * FROM pengajuan_request WHERE waktu='$time'");
                $noid3 = mysqli_fetch_assoc($selectnoid3);
                $kode3 = $noid3['id'];
                $project3 = $noid3['nama'];
                if ($noid3['status_request'] == 'Belum Di Ajukan') :
              ?>
                  <li class="header"><a href="view-request.php?id=<?= $kode3 ?>">Akses Pengajuan Budget <b><?= $project3 ?></b> telah dibuka</a></li>
                <?php elseif ($noid3['status_request'] == 'Ditolak') : ?>
                  <li class="header"><a href="view-request.php?id=<?= $kode3 ?>">Pengajuan Budget <b><?= $project3 ?></b> telah ditolak</a></li>
                <?php endif; ?>
              <?php
              }
              ?>
            </ul>
          </li>
          <ul class="nav navbar-nav navbar-right">
            <li><a href="ubahpassword.php"><span class="glyphicon glyphicon-user"></span><?php echo $_SESSION['nama_user']; ?> (<?php echo $_SESSION['divisi']; ?>)</a></li>
            <li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> Logout</a></li>
          </ul>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container">


    <ul id="myTab" class="nav nav-tabs" role="tablist">
      <li role="presentation" class="active">
        <a href="#project" id="project-tab" role="tab" data-toggle="tab" aria-controls="project" aria-expanded="true">Project</a>
      </li>
      <li role="presentation">
        <a href="#pengajuan" id="project-tab" role="tab" data-toggle="tab" aria-controls="history" aria-expanded="true">Pengajuan Budget</a>
      </li>
      <?php
      $divisiSes = $_SESSION['divisi'];
      if ($divisiSes == 'Field') {
      ?>
        <li role="presentation">
          <a href="#umburek" id="umburek-tab" role="tab" data-toggle="tab" aria-controls="umburek" aria-expanded="true">UM Burek</a>
        </li>
      <?php
      } else if ($divisiSes == 'Desy') {
      ?>
        <li role="presentation">
          <a href="#b1" id="b1-tab" role="tab" data-toggle="tab" aria-controls="b1" aria-expanded="true">B1</a>
        </li>
        <li role="presentation">
          <a href="#b2" id="b2-tab" role="tab" data-toggle="tab" aria-controls="b2" aria-expanded="true">B2</a>
        </li>
      <?php
      } else {
        echo "";
      }
      ?>
    </ul>
    <div id="myTabContent" class="tab-content">
      <div role="tabpanel" class="tab-pane fade in active" id="project" aria-labelledby="project-tab">
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
                  <th>Pengajuan</th>
                </tr>
              </thead>

              <tbody>
                <?php
                $i = 1;
                $divisi = $_SESSION['divisi'];
                $username = $_SESSION['nama_user'];

                $checkWaktu = [];

                if ($divisiSes == 'FIELD B1' or $divisiSes == 'FIELD B2') {
                  $sql = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE ((jenis ='B1' AND status !='Belum Di Ajukan') OR (jenis ='B2' AND status !='Belum Di Ajukan') OR divisi ='$divisi') AND tahun > 2020 ORDER BY jenis");
                } else {
                  $sql = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE divisi ='$divisi'");
                }
                while ($d = mysqli_fetch_array($sql)) {

                  if (!in_array($d['waktu'], $checkWaktu)) :

                    $arrDocument = [];
                    $document = unserialize($d['document']);
                    if (!is_array($document)) {
                      array_push($arrDocument, $document);
                    } else {
                      $arrDocument = $document;
                    }

                    if ($d['status'] == "Belum Di Ajukan") {
                ?>
                      <tr>
                        <th scope="row"><?php echo $i++; ?></th>
                        <td> <?= $d['nama']; ?>
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
                        <td><a href="view.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a></td>
                        <td><?php echo $d['status']; ?></td>
                        <?php //echo "<td><a href='#myModal' class='btn btn-default btn-small' id='custId' data-toggle='modal' data-id=".$d['noid'].">Ajukan</a></td>"; 
                        ?>
                      </tr>
                    <?php
                    } else if ($d['status'] == "Dihapus") {
                    ?>
                      <tr>
                        <th scope="row"><?php echo $i++; ?></th>
                        <td><?php echo $d['nama']; ?></td>
                        <td><?php echo $d['tahun']; ?></td>
                        <td><?php echo $d['pengaju']; ?></td>
                        <td><?php echo $d['divisi']; ?></td>
                        <td><a href="view-hapus.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a></td>
                        <td><?php echo $d['status']; ?></td>
                      </tr>
                    <?php
                    } else if ($d['status'] == "Finish") {
                    ?>
                      <tr>
                        <th scope="row"><?php echo $i++; ?></th>
                        <td><?php echo $d['nama']; ?></td>
                        <td><?php echo $d['tahun']; ?></td>
                        <td><?php echo $d['pengaju']; ?></td>
                        <td><?php echo $d['divisi']; ?></td>
                        <td><a href="view-finish.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a></td>
                        <td><?php echo $d['status']; ?></td>
                      </tr>
                    <?php
                    } else if ($d['status'] == "Pending") {
                    ?>
                      <tr>
                        <th scope="row"><?php echo $i++; ?></th>
                        <td> <?= $d['nama']; ?>
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
                        <td><a href="view.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a></td>
                        <td><?php echo $d['status']; ?></td>
                        <?php echo "<td>--</td>"; ?>
                      </tr>
                    <?php
                    } else if ($d['status'] == "Disapprove") {
                    ?>
                      <tr>
                        <th bgcolor="#fea700" scope="row"><?php echo $i++; ?></th>
                        <td bgcolor="#fea700"><?= $d['nama']; ?>
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
                        <td bgcolor="#fea700"><?php echo $d['tahun']; ?></td>
                        <td bgcolor="#fea700"><?php echo $d['pengaju']; ?></td>
                        <td bgcolor="#fea700"><?php echo $d['divisi']; ?></td>
                        <td bgcolor="#fea700"><a href="view-disapprove.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a></td>
                        <td bgcolor="#fea700"><?php echo $d['status']; ?></td>
                        <?php echo "<td>--</td>"; ?>
                      </tr>
                    <?php
                    } else {
                    ?>
                      <tr>
                        <th bgcolor="#fcfaa4" scope="row"><?php echo $i++; ?></th>
                        <td bgcolor="#fcfaa4"> <?= $d['nama']; ?>
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
                        <td bgcolor="#fcfaa4"><a href="views.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a></td>
                        <td bgcolor="#fcfaa4"><?php echo $d['status']; ?></td>
                        <?php echo "<td>--</td>"; ?>
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

      <div role="tabpanel" class="tab-pane fade" id="pengajuan" aria-labelledby="pengajuan-tab">
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
                  <th>Action</th>
                  <th>Status</th>
                </tr>
              </thead>
<?php echo $divisi ?>
              <tbody>
                <?php
                $i = 1;
                $checkWaktu = [];
                if ($divisiSes == 'Field') {
                  $sql = mysqli_query($koneksi, "SELECT * FROM pengajuan_request WHERE (jenis ='B1' AND status_request !='Belum Di Ajukan') OR (jenis ='B2' AND status_request !='Belum Di Ajukan') AND divisi ='$divisi' ORDER BY jenis");
                } else {
                  $sql2 = mysqli_query($koneksi, "SELECT * FROM pengajuan_request WHERE (pengaju ='$username' AND status_request <> 'Disetujui') OR (status_request <> 'Disetujui' AND divisi = '$divisi') ");
                }
                while ($e = mysqli_fetch_array($sql2)) {
                  if (!in_array($e['waktu'], $checkWaktu)) :

                    $arrDocument = [];
                    $document = unserialize($e['document']);
                    if (!is_array($document)) {
                      array_push($arrDocument, $document);
                    } else {
                      $arrDocument = $document;
                    }

                ?>
                    <tr>
                      <th scope="row"><?php echo $i++; ?></th>
                      <td> <?= $e['nama']; ?>
                        <?php if ($arrDocument[0]) : ?>
                          -
                          <?php
                          $j = 0;
                          foreach ($arrDocument as $ad) :
                          ?>
                            <?php if ($e['on_revision_status'] == 1) : ?>
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
                      <td><?php echo $e['tahun']; ?></td>
                      <td><?php echo $e['pengaju']; ?></td>
                      <td><?php echo $e['divisi']; ?></td>
                      <td><a href="view-request.php?id=<?php echo $e['id']; ?>"><i class="fas fa-eye" title="View"></i></a></td>
                      <td><?php echo $e['status_request']; ?></td>
                    </tr>
                    <?php array_push($checkWaktu, $e['waktu']); ?>
                  <?php endif; ?>
                <?php } ?>
              </tbody>
            </table>
          </div><!-- /.table-responsive -->
        </div>
      </div>

      <div role="tabpanel" class="tab-pane fade" id="b1" aria-labelledby="b1-tab">
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
                  <th>Action</th>
                  <th>Status</th>
                </tr>
              </thead>

              <tbody>
                <?php
                $i = 1;
                $sql2 = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE jenis ='B1' AND status ='Disetujui'");
                while ($e = mysqli_fetch_array($sql2)) {

                  if ($e['status'] == "Disetujui") {
                ?>
                    <tr>
                      <th bgcolor="#fcfaa4" scope="row"><?php echo $i++; ?></th>
                      <td bgcolor="#fcfaa4"><?php echo $e['nama']; ?></td>
                      <td bgcolor="#fcfaa4"><?php echo $e['tahun']; ?></td>
                      <td bgcolor="#fcfaa4"><?php echo $e['pengaju']; ?></td>
                      <td bgcolor="#fcfaa4"><?php echo $e['divisi']; ?></td>
                      <td bgcolor="#fcfaa4"><a href="views.php?code=<?php echo $e['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a></td>
                      <td bgcolor="#fcfaa4"><?php echo $e['status']; ?></td>
                    </tr>
                  <?php
                  } else { ?>
                    <tr>
                      <th scope="row"><?php echo $i++; ?></th>
                      <td><?php echo $e['nama']; ?></td>
                      <td><?php echo $e['tahun']; ?></td>
                      <td><?php echo $e['pengaju']; ?></td>
                      <td><?php echo $e['divisi']; ?></td>
                      <td>--</td>
                      <td><?php echo $e['status']; ?></td>
                    </tr>
                <?php }
                } ?>
              </tbody>
            </table>
          </div><!-- /.table-responsive -->
        </div>
      </div>

      <div role="tabpanel" class="tab-pane fade" id="b2" aria-labelledby="b2-tab">
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
                  <th>Action</th>
                  <th>Status</th>
                </tr>
              </thead>

              <tbody>
                <?php
                $i = 1;
                $sql2 = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE jenis ='B2' AND status ='Disetujui'");
                while ($e = mysqli_fetch_array($sql2)) {

                  if ($e['status'] == "Disetujui") {
                ?>
                    <tr>
                      <th bgcolor="#fcfaa4" scope="row"><?php echo $i++; ?></th>
                      <td bgcolor="#fcfaa4"><?php echo $e['nama']; ?></td>
                      <td bgcolor="#fcfaa4"><?php echo $e['tahun']; ?></td>
                      <td bgcolor="#fcfaa4"><?php echo $e['pengaju']; ?></td>
                      <td bgcolor="#fcfaa4"><?php echo $e['divisi']; ?></td>
                      <td bgcolor="#fcfaa4"><a href="views.php?code=<?php echo $e['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a></td>
                      <td bgcolor="#fcfaa4"><?php echo $e['status']; ?></td>
                    </tr>
                  <?php
                  } else { ?>
                    <tr>
                      <th scope="row"><?php echo $i++; ?></th>
                      <td><?php echo $e['nama']; ?></td>
                      <td><?php echo $e['tahun']; ?></td>
                      <td><?php echo $e['pengaju']; ?></td>
                      <td><?php echo $e['divisi']; ?></td>
                      <td>--</td>
                      <td><?php echo $e['status']; ?></td>
                    </tr>
                <?php }
                } ?>
              </tbody>
            </table>
          </div><!-- /.table-responsive -->
        </div>
      </div>
    </div>


    <?php
    if ($divisiSes == 'Field') {
    ?>
      <div role="tabpanel" class="tab-pane fade in active" id="umburek" aria-labelledby="umburek-tab">
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
                  <th>Action</th>
                  <th>Status</th>
                </tr>
              </thead>

              <tbody>
                <?php
                $i = 1;
                $sql2 = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE jenis ='UM Burek' AND status !='Belum Di Ajukan'");
                while ($e = mysqli_fetch_array($sql2)) {

                  if ($e['status'] == "Disetujui") {
                ?>
                    <tr>
                      <th bgcolor="#fcfaa4" scope="row"><?php echo $i++; ?></th>
                      <td bgcolor="#fcfaa4"><?php echo $e['nama']; ?></td>
                      <td bgcolor="#fcfaa4"><?php echo $e['tahun']; ?></td>
                      <td bgcolor="#fcfaa4"><?php echo $e['pengaju']; ?></td>
                      <td bgcolor="#fcfaa4"><?php echo $e['divisi']; ?></td>
                      <td bgcolor="#fcfaa4"><a href="views-field.php?code=<?php echo $e['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a></td>
                      <td bgcolor="#fcfaa4"><?php echo $e['status']; ?></td>
                    </tr>
                  <?php
                  } else { ?>
                    <tr>
                      <th scope="row"><?php echo $i++; ?></th>
                      <td><?php echo $e['nama']; ?></td>
                      <td><?php echo $e['tahun']; ?></td>
                      <td><?php echo $e['pengaju']; ?></td>
                      <td><?php echo $e['divisi']; ?></td>
                      <td>--</td>
                      <td><?php echo $e['status']; ?></td>
                    </tr>
                <?php }
                } ?>
              </tbody>
            </table>
          </div><!-- /.table-responsive -->
        </div>
      </div>
  </div>

<?php
    } else {
      echo "";
    }
?>

</div>

<div class="modal fade" id="myModal" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Pengajuan</h4>
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
        url: 'ajukan.php',
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