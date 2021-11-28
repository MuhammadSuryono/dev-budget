<?php
error_reporting(0);
session_start();

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

$con->set_name_db(DB_MRI_TRANSFER);
$con->init_connection();
$koneksiTransfer = $con->connect();

if (!isset($_SESSION['nama_user'])) {
  header("location:login.php");
  // die('location:login.php');//jika belum login jangan lanjut
}

$idUser = $_SESSION['id_user'];
$queryUser = mysqli_query($koneksi, "SELECT email, e_sign, phone_number FROM tb_user WHERE id_user = '$idUser'");
$user = mysqli_fetch_assoc($queryUser);
$emailUser = $user['email'];
$signUser = $user['e_sign'];
$phoneNumber = $user['phone_number'];

$date = date('my');
$countQuery = mysqli_query($koneksiTransfer, "SELECT count(transfer_id) FROM data_transfer WHERE transfer_req_id LIKE '2001%'");
$count = mysqli_fetch_array($countQuery)[0];

$formatId = $date . $count;

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
          <li class="active"><a href="home.php">Home</a></li>
          <li><a href="list.php">List</a></li>
          <!-- <li><a href="request-budget.php">Request Budget</a></li> -->
        </ul>

        <ul class="nav navbar-nav navbar-right">
          
          <ul class="nav navbar-nav navbar-right">
            <li><a href="ubahpassword.php"><span class="glyphicon glyphicon-user"></span><?php echo $_SESSION['nama_user']; ?> (<?php echo $_SESSION['divisi']; ?>)</a></li>
            <li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> Logout</a></li>
          </ul>
        </ul>
      </div>
    </div>
  </nav>

  <br />
  <div class="container">

    <p>
    <h4> Sisa Plafon :
      <?php
      $suser = $_SESSION['id_user'];
      $nuser = $_SESSION['nama_user'];
      $ceksaldo = mysqli_query($koneksi, "SELECT saldo FROM tb_user WHERE id_user='$suser'");
      $rcs = mysqli_fetch_assoc($ceksaldo);
      $pertama = $rcs['saldo'];

      $query2 = "SELECT sum(jumlah) AS sumi FROM bpu WHERE namapenerima='$nuser' AND statusbpu IN ('UM', 'UM Burek') AND status IN ('Telah Di Bayar', 'Belum Di Bayar')";
      $result2 = mysqli_query($koneksi, $query2);
      $row2 = mysqli_fetch_array($result2);
      $totalUm = $row2['sumi'];

      $saldosisa = $pertama - $totalUm;

      echo 'Rp. ' . number_format($saldosisa, 0, '', ',');

      ?>
    </h4>
    </p>

    <br />

    <!-- OUTSTANDING BPU UM -->
    <!-- <h5>Outstanding BPU UM </h5>
    <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
      <div class="panel-body no-padding">
        <table class="table table-striped">
          <thead>
            <tr class="warning">
              <th>No</th>
              <th>Nama</th>
              <th>Divisi</th>
              <th>Total Outstanding</th>
            </tr>
          </thead>

          <tbody>
            <?php
            // 
            $i = 1;
            $divisi   = strtolower(mb_substr($_SESSION['divisi'], 0, 5));
            $username = $_SESSION['nama_user'];
            $divisiSes = $_SESSION['divisi'];

            if ($_SESSION['hak_akses'] == 'Manager') {
              $sql2 = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi='$divisi' AND aktif = 'Y' ORDER BY nama_user ASC");
            } else if ($divisiSes == 'field') {
              $sql2 = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi LIKE '%FIELD%' ORDER BY nama_user ASC");
            } else {
              $sql2 = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE nama_user='$nuser'");
            }

            while ($e = mysqli_fetch_array($sql2)) {
            ?>
              <tr>
                <td scope="row"><?php echo $i++; ?></td>
                <td><?php echo $e['nama_user']; ?></td>
                <td><?php echo $e['divisi']; ?></td>
                <td>
                  <?php
                  $usernya = $e['nama_user'];
                  // var_dump($usernya);
                  $getUm = mysqli_query($koneksi, "SELECT sum(jumlah) AS sumjum FROM bpu WHERE namapenerima='$usernya' AND status !='Realisasi (Direksi)' AND statusbpu IN ('UM', 'UM Burek')");
                  $um = mysqli_fetch_array($getUm);
                  echo 'Rp. ' . number_format($um[0], 0, '', ',');
                  ?>
                </td>
                <?php
                $caribpunya2 = mysqli_query($koneksi, "SELECT
                                              pengajuan.noid AS kode,
                                              pengajuan.nama AS nama,
                                              bpu.status AS status_bpu,
                                              bpu.status_pengajuan_realisasi AS status_pengajuan_realisasi,
                                              bpu.no AS no,
                                              bpu.waktu AS waktu,
                                              bpu.term AS term,
                                              bpu.jumlah AS jumlah,
                                              bpu.realisasi AS realisasi,
                                              bpu.uangkembali AS uangkembali,
                                              bpu.fileupload_realisasi AS fileupload_realisasi,
                                              bpu.pengajuan_realisasi,
                                              bpu.pengajuan_uangkembali AS pengajuan_uangkembali,
                                              bpu.pengajuan_tanggalrealisasi AS pengajuan_tanggalrealisasi,
                                              bpu.alasan_tolak_realisasi,
                                              selesai.status AS status_selesai,
                                              pengajuan.jenis AS jenis
                                              FROM
                                                bpu
                                              LEFT JOIN pengajuan ON bpu.waktu = pengajuan.waktu
                                              LEFT JOIN selesai ON bpu.waktu = selesai.waktu
                                              AND bpu.no = selesai.no
                                              WHERE
                                                bpu.namapenerima = '$usernya'
                                              AND bpu.status = 'Telah Di Bayar'
                                              AND bpu.statusbpu = 'UM'");
                $checkName = [];
                while ($cb = mysqli_fetch_array($caribpunya2)) {
                  $key = $cb['jenis'] . '-' . $cb['nama'] . '-' . $cb['no'] . '-' . $cb['term'];

                  if (!in_array($key, $checkName)) :
                ?>
                    <td bgcolor="#8aad70">
                      Jenis : <b><?php echo $cb['jenis']; ?></b>
                      <br />
                      Project :
                      <b><a href="views.php?code=<?php echo $cb['kode']; ?>"><?php echo $cb['nama']; ?></a></b>
                      <br />
                      Item No : <b><?php echo $cb['no']; ?></b>
                      <br />
                      Term : <b><?php echo $cb['term']; ?></b>
                      <br />
                      Jumlah :
                      <br />
                      <b>
                        <?php
                        $jumlahnya     = $cb['jumlah'];
                        $realisasinya  = $cb['realisasi'];
                        $jadinya = $jumlahnya - $realisasinya;
                        echo 'Rp. ' . number_format($jadinya, 0, '', ',');
                        ?>
                      </b><br>
                      <?php

                      $statusbayar         = $cb['status_bpu'];
                      $statusPengajuanRealisasi = $cb['status_pengajuan_realisasi'];
                      $no = $cb['no'];
                      $term = $cb['term'];
                      $waktu = $cb['waktu'];
                      $jumlbayar = $cb['jumlah'];
                      $realisasi           = $cb['realisasi'];
                      $uangkembali         = $cb['uangkembali'];
                      $kembreal         = $realisasi + $uangkembali;
                      $sisarealisasi    = $jumlbayar - $kembreal;
                      $fileuploadRealisasi       = $cb['fileupload_realisasi'];
                      $pengajuan_realisasi = $cb['pengajuan_realisasi'];
                      $pengajuan_uangkembali = $cb['pengajuan_uangkembali'];
                      $pengajuan_tanggalrealisasi  = $cb['pengajuan_tanggalrealisasi'];
                      $alasan_tolak_realisasi  = $cb['alasan_tolak_realisasi'];

                      ?>
                    </td>
                    <?php array_push($checkName, $key); ?>
                  <?php endif; ?>
                <?php
                }
                ?>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div> -->
    <!-- //OUTSTANDING BPU UM -->

    <!-- BPU UM -->
    <?php if ($_SESSION['jabatan'] != 'Manager' && $_SESSION['jabatan'] != 'Senior Manager') : ?>
      <h5>Daftar BPU UM</h5>
      <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
        <div class="panel-body no-padding">
          <div class="list-group-item border" id="grandparent1" style="border: 1px solid black !important;">
            <div id="expander" data-target="#grandparentContent1" data-toggle="collapse" data-group-id="grandparent<?= $i ?>" data-role="expander">

              <ul class="list-inline row border">
                <li class="col-lg-11">1. Outstanding</li>
                <li class="col-lg-1">
                  <span id="grandparentIcon1" style="cursor: pointer; margin: 0 10px;" class="col-lg-1"><a><i class="fas fa-eye" title="View Rincian"></i></a></span>
                </li>
              </ul>
            </div>
            <div class="collapse" id="grandparentContent1" aria-expanded="true">
              <table class="table table-striped">
                <table class="table table-striped">
                  <thead>
                    <tr class="warning">
                      <th>No</th>
                      <th>Nama Project</th>
                      <th>Nomor Item Budget</th>
                      <th>Rincian Item Budget</th>
                      <th>Term BPU</th>
                      <th>Action</th>
                      <!-- <th>Pengajuan Request</th> -->
                    </tr>
                  </thead>

                  <tbody>
                    <?php
                    $i = 1;
                    $checkUnique = [];
                    $sql = mysqli_query($koneksi, "SELECT a.*, b.nama, b.noid AS budget_noid, b.jenis, c.rincian FROM bpu a JOIN pengajuan b ON b.waktu = a.waktu JOIN selesai c ON c.waktu = a.waktu AND c.no = a.no WHERE c.status IN ('UM', 'UM Burek') AND a.status = 'Telah Di Bayar' AND a.namapenerima = '$_SESSION[nama_user]'");
                    while ($d = mysqli_fetch_array($sql)) :
                      $unique = $d['waktu'] . $d['nama'] . $d['no'] . $d['rincian'] . $d['term'];
                      if (!in_array($unique, $checkUnique)) :
                    ?>
                        <tr>
                          <td><?= $i++ ?></td>
                          <td><?= $d['nama'] ?></td>
                          <td><?= $d['no'] ?></td>
                          <td><?= $d['rincian'] ?></td>
                          <td><?= $d['term'] ?></td>
                          <td>
                            <a href="views.php?code=<?= $d['budget_noid'] ?>" target="_blank"><i class="fas fa-external-link-alt" title="View Budget"></i></a>
                          </td>
                        </tr>
                        <?php array_push($checkUnique, $unique); ?>
                      <?php endif; ?>
                    <?php endwhile; ?>

                  </tbody>
                </table>
            </div>
          </div>
          <div class="list-group-item border" id="grandparent2" style="border: 1px solid black !important;">
            <div id="expander" data-target="#grandparentContent2" data-toggle="collapse" data-group-id="grandparent<?= $i ?>" data-role="expander">

              <ul class="list-inline row border">
                <li class="col-lg-11">2. Pengajuan</li>
                <li class="col-lg-1">
                  <span id="grandparentIcon1" style="cursor: pointer; margin: 0 10px;" class="col-lg-1"><a><i class="fas fa-eye" title="View Rincian"></i></a></span>
                </li>
              </ul>
            </div>
            <div class="collapse" id="grandparentContent2" aria-expanded="true">
              <table class="table table-striped">
                <table class="table table-striped">
                  <thead>
                    <tr class="warning">
                      <th>No</th>
                      <th>Nama Project</th>
                      <th>Nomor Item Budget</th>
                      <th>Rincian Item Budget</th>
                      <th>Term BPU</th>
                      <th>Action</th>
                      <!-- <th>Pengajuan Request</th> -->
                    </tr>
                  </thead>

                  <tbody>
                    <?php
                    $i = 1;
                    $checkWaktu = [];
                    $sql = mysqli_query($koneksi, "SELECT a.*, b.nama, b.noid AS budget_noid, b.jenis, c.rincian FROM bpu a JOIN pengajuan b ON b.waktu = a.waktu JOIN selesai c ON c.waktu = a.waktu AND c.no = a.no WHERE c.status IN ('UM', 'UM Burek') AND a.status = 'Belum Di Bayar' AND a.namapenerima = '$_SESSION[nama_user]'");
                    while ($d = mysqli_fetch_array($sql)) :

                      $unique = $d['waktu'] . $d['nama'] . $d['no'] . $d['rincian'] . $d['term'];
                      if (!in_array($unique, $checkUnique)) :
                    ?>
                        <tr>
                          <td><?= $i++ ?></td>
                          <td><?= $d['nama'] ?></td>
                          <td><?= $d['no'] ?></td>
                          <td><?= $d['rincian'] ?></td>
                          <td><?= $d['term'] ?></td>
                          <td>
                            <a href="views.php?code=<?= $d['budget_noid'] ?>" target="_blank"><i class="fas fa-external-link-alt" title="View Budget"></i></a>
                          </td>
                        </tr>
                        <?php array_push($checkUnique, $unique); ?>
                      <?php endif; ?>
                    <?php endwhile; ?>

                  </tbody>
                </table>
            </div>
          </div>
        </div>
      </div>
    <?php else : ?>
      <h5>Daftar BPU UM User divisi <?= $_SESSION['divisi'] ?></h5>
      <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
        <div class="panel-body no-padding">
          <ul class="list-inline row border">
            <li class="col-lg-1">Nama User</li>
            <li class="col-lg-2">Limit</li>
            <li class="col-lg-2">Saldo Awal Outstanding</li>
            <li class="col-lg-2">Pengajuan</li>
            <li class="col-lg-2">Saldo Akhir Outstanding</li>
            <li class="col-lg-2">Sisa Limit</li>
            <li class="col-lg-1">
              Action
            </li>
          </ul>
          <?php
          $i = 1;
          $sql = mysqli_query($koneksi, "SELECT a.namapenerima, SUM(a.jumlah) AS total_pengajuan, c.saldo FROM bpu a JOIN selesai b ON a.waktu = b.waktu AND a.no = b.no JOIN tb_user c ON c.nama_user = a.namapenerima JOIN pengajuan d ON d.waktu = a.waktu WHERE b.status IN ('UM', 'UM Burek') AND a.status IN ('Telah Di Bayar', 'Belum Di Bayar') AND c.aktif = 'Y' AND c.divisi = '$_SESSION[divisi]' GROUP BY a.namapenerima") or die(mysqli_error($koneksi));
          while ($d = mysqli_fetch_assoc($sql)) :
            $sql2 = mysqli_query($koneksi, "SELECT SUM(jumlah) AS total_pengajuan FROM (SELECT DISTINCT a.* FROM bpu a JOIN selesai b ON a.waktu = b.waktu AND a.no = b.no WHERE b.status IN ('UM', 'UM Burek') AND a.status = 'Telah Di Bayar' AND a.namapenerima = '$d[namapenerima]') AS t") or die(mysqli_error($koneksi));
            $terbayar = mysqli_fetch_assoc($sql2);
            $sql3 = mysqli_query($koneksi, "SELECT SUM(jumlah) AS total_pengajuan FROM (SELECT DISTINCT a.* FROM bpu a JOIN selesai b ON a.waktu = b.waktu AND a.no = b.no WHERE b.status IN ('UM', 'UM Burek') AND a.status = 'Belum Di Bayar' AND a.namapenerima = '$d[namapenerima]') AS t") or die(mysqli_error($koneksi));
            $belumTerbayar = mysqli_fetch_assoc($sql3);
          ?>

            <div class="list-group-item border" id="grandparent<?= $d['namapenerima'] ?>" style="border: 1px solid black !important;">
              <div id="expander" data-target="#grandparentContent<?= $d['namapenerima'] ?>" data-toggle="collapse" data-group-id="grandparent<?= $d['namapenerima'] ?>" data-role="expander">
                <ul class="list-inline row border">
                  <li class="col-lg-1"><?= $i++ ?>. <?= $d['namapenerima'] ?></li>
                  <li class="col-lg-2">Rp. <?php echo number_format($d['saldo']); ?></li>
                  <li class="col-lg-2">Rp. <?php echo number_format($terbayar['total_pengajuan']); ?></li>
                  <li class="col-lg-2">Rp. <?php echo number_format($belumTerbayar['total_pengajuan']); ?></li>
                  <li class="col-lg-2">Rp. <?php echo number_format($terbayar['total_pengajuan'] + $belumTerbayar['total_pengajuan']); ?></li>
                  <li class="col-lg-2">Rp. <?php echo number_format($d['saldo'] - ($terbayar['total_pengajuan'] + $belumTerbayar['total_pengajuan'])); ?></li>
                  <li class="col-lg-1">
                    <span id="grandparentIcon<?= $d['namapenerima'] ?>" style="cursor: pointer; margin: 0 10px;" class="col-lg-1"><a><i class="fas fa-eye" title="View Rincian"></i></a></span>
                  </li>
                </ul>
              </div>
              <div class="collapse" id="grandparentContent<?= $d['namapenerima'] ?>" aria-expanded="true">
                <h3 class="text-center">Outstanding</h3>
                <table class="table table-striped">
                  <thead>
                    <tr class="warning">
                      <th>No.</th>
                      <th>Nama Project</th>
                      <th>Nomor Item Budget</th>
                      <th>Rincian Item Budget</th>
                      <th>Term Bpu</th>
                      <th>Jumlah</th>
                      <th>Tanggal Bayar</th>
                    </tr>
                  </thead>

                  <tbody>
                    <?php
                    $j = 1;
                    $checkUnique = [];
                    $queryDetailBpu = mysqli_query($koneksi, "SELECT a.*, b.rincian FROM bpu a JOIN selesai b ON a.waktu = b.waktu AND a.no = b.no JOIN pengajuan c ON c.waktu = a.waktu WHERE b.status IN ('UM', 'UM Burek') AND a.namapenerima = '$d[namapenerima]' AND a.status = 'Telah Di Bayar'") or die(mysqli_error($koneksi));
                    if (mysqli_num_rows($queryDetailBpu)) {
                      while ($item2 = mysqli_fetch_assoc($queryDetailBpu)) :
                        $unique = $item2['waktu'] . $item2['nama'] . $item2['no'] . $item2['rincian'] . $item2['term'];
                        if (!in_array($unique, $checkUnique)) :

                          $totalTerbayar += $item2['jumlah'];
                    ?>
                          <tr data-toggle="collapse" data-target=".child1">
                            <td><?= $j++ ?></td>
                            <td><?= $item2['namaproject'] ?></td>
                            <td><?= $item2['no'] ?></td>
                            <td><?= $item2['rincian'] ?></td>
                            <td><?= $item2['term'] ?></td>
                            <td>Rp.<?= number_format($item2['jumlah']) ?></td>
                            <td><?= $item2['tanggalbayar'] ?></td>
                          </tr>
                          <?php array_push($checkUnique, $unique); ?>
                        <?php endif; ?>
                      <?php endwhile; ?>
                    <?php } else { ?>
                      <tr data-toggle="collapse" data-target=".child1">
                        <!-- <td></td> -->
                        <td>Tidak ada outstanding</td>
                        <!-- <td></td> -->
                        <!-- <td></td> -->
                      </tr>
                    <?php } ?>

                  </tbody>
                </table>
                <br>
                <h3 class="text-center">Pengajuan</h3>
                <table class="table table-striped">
                  <thead>
                    <tr class="warning">
                      <th>No.</th>
                      <th>Nama Project</th>
                      <th>Nomor Item Budget</th>
                      <th>Rincian Item Budget</th>
                      <th>Term Bpu</th>
                      <th>Jumlah</th>
                    </tr>
                  </thead>

                  <tbody>
                    <?php
                    $checkUnique = [];
                    $queryDetailBpu = mysqli_query($koneksi, "SELECT a.*, b.rincian, c.nama AS namaproject FROM bpu a JOIN selesai b ON a.waktu = b.waktu AND a.no = b.no JOIN pengajuan c ON c.waktu = a.waktu WHERE b.status IN ('UM', 'UM Burek') AND a.namapenerima = '$d[namapenerima]' AND a.status = 'Belum Di Bayar'") or die(mysqli_error($koneksi));
                    if (mysqli_num_rows($queryDetailBpu)) {
                      while ($item2 = mysqli_fetch_assoc($queryDetailBpu)) :

                        $unique = $item2['waktu'] . $item2['nama'] . $item2['no'] . $item2['rincian'] . $item2['term'];
                        if (!in_array($unique, $checkUnique)) :
                          $totalBelumTerbayar += $item2['jumlah'];
                    ?>
                          <tr data-toggle="collapse" data-target=".child1">
                            <td><?= $j++ ?></td>
                            <td><?= $item2['namaproject'] ?></td>
                            <td><?= $item2['no'] ?></td>
                            <td><?= $item2['rincian'] ?></td>
                            <td><?= $item2['term'] ?></td>
                            <td>Rp.<?= number_format($item2['jumlah']) ?></td>
                          </tr>
                          <?php array_push($checkUnique, $unique); ?>
                        <?php endif; ?>
                      <?php endwhile; ?>
                    <?php } else { ?>
                      <td>Tidak ada pengajuan Uang Muka</td>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          <?php endwhile; ?>
        </div>
      </div>
    <?php endif; ?>
    <br /><br />
    <!-- //BPU UM -->

    <?php if ($_SESSION['jabatan'] == 'Manager' || $_SESSION['jabatan'] == 'Senior Manager') : ?>
      <h5>Daftar BPU yang perlu follow up</h5>
      <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
        <div class="panel-body no-padding">
          <div class="list-group-item border" id="grandparentmengetahui" style="border: 1px solid black !important;">
            <div id="expander" data-target="#grandparentContentMengetahui" data-toggle="collapse" data-group-id="grandparentmengetahui" data-role="expander">

              <ul class="list-inline row border">
                <li class="col-lg-11">1. BPU yang perlu di setujui</li>
                <li class="col-lg-1">
                  <span id="grandparentIconMengetahui" style="cursor: pointer; margin: 0 10px;" class="col-lg-1"><a><i class="fas fa-eye" title="View Rincian"></i></a></span>
                </li>
              </ul>
            </div>
            <div class="collapse" id="grandparentContentMengetahui" aria-expanded="true">
              <!-- <table class="table table-striped"> -->
              <table class="table table-striped">
                <thead>
                  <tr class="warning">
                    <th>No</th>
                    <th>Nama Project</th>
                    <th>Nomor Item Budget</th>
                    <th>Rincian Item Budget</th>
                    <th>Term BPU</th>
                    <th>Action</th>
                    <!-- <th>Pengajuan Request</th> -->
                  </tr>
                </thead>

                <tbody>
                  <?php
                  $i = 1;
                  $checkUnique = [];
                  $sql = mysqli_query($koneksi, "SELECT DISTINCT a.term, a.no, a.waktu, b.nama, b.noid AS budget_noid, c.rincian FROM bpu a JOIN pengajuan b ON b.waktu = a.waktu JOIN selesai c ON c.waktu = a.waktu AND c.no = a.no WHERE status_pengajuan_bpu = 3");
                  while ($d = mysqli_fetch_array($sql)) :
                    $unique = $d['waktu'] . $d['nama'] . $d['no'] . $d['rincian'] . $d['term'];
                  ?>
                    <?php
                    if (!in_array($unique, $checkUnique)) : ?>
                      <!-- <br> -->
                      <tr>
                        <td><?= $i++ ?></td>
                        <td><?= $d['nama'] ?></td>
                        <td><?= $d['no'] ?></td>
                        <td><?= $d['rincian'] ?></td>
                        <td><?= $d['term'] ?></td>
                        <td>
                          <a href="views.php?code=<?= $d['budget_noid'] ?>" target="_blank"><i class="fas fa-external-link-alt" title="View Budget"></i></a>
                        </td>
                      </tr>
                      <?php array_push($checkUnique, $unique); ?>
                    <?php endif; ?>
                  <?php endwhile; ?>

                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <br>
    <?php endif; ?>

    <h5>Daftar Budget</h5>
    <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
      <div class="panel-body no-padding">
        <table class="table table-striped">
          <thead>
            <tr class="warning">
              <th>No</th>
              <th>Nama Project</th>
              <th>Doc</th>
              <th>Jenis</th>
              <th>Tahun</th>
              <th>Nama Yang Mengajukan</th>
              <th>Divisi</th>
              <th>Action</th>
              <th>Status</th>
              <!-- <th>Pengajuan</th> -->
            </tr>
          </thead>

          <tbody>

            <?php
            // 
            $i = 1;
            $divisi = $_SESSION['divisi'];
            $username = $_SESSION['nama_user'];
            $checkWaktu = [];
            $sql = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE pengaju='$username' AND (status='Belum Di Ajukan' OR status='Belum Di Ajukan(Penambahan)' OR status='Disapprove')");
            while ($d = mysqli_fetch_array($sql)) {
              if (!in_array($d['waktu'], $checkWaktu)) :
                $arrDocument = [];
                $document = unserialize($d['document']);
                if (!is_array($document)) {
                  array_push($arrDocument, $document);
                } else {
                  $arrDocument = $document;
                }
            ?>
                <tr>
                  <th scope="row"><?php echo $i++; ?></th>
                  <td><?php echo $d['nama']; ?></td>
                  <td>
                    <?php if ($arrDocument[0]) : ?>
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
                  <td><?php echo $d['jenis']; ?></td>
                  <td><?php echo $d['tahun']; ?></td>
                  <td><?php echo $d['pengaju']; ?></td>
                  <td><?php echo $d['divisi']; ?></td>
                  <?php if ($d['status'] == 'Disapprove') : ?>
                    <td><a href="view-disapprove.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a></td>
                  <?php else : ?>
                    <td><a href="view.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a></td>
                  <?php endif; ?>
                  <td><?php echo $d['status']; ?></td>
                  <?php
                  // if ($_SESSION['divisi'] == 'GA' || $_SESSION['divisi'] == 'RE B1') {
                  //   echo "<td><a href='#myModal' class='btn btn-default btn-small' id='custId' data-toggle='modal' data-id=" . $d['noid'] . ">Ajukan</a></td>";
                  // } else if ($_SESSION['hak_page'] == 'Create') {
                  //   echo "<td><a href='#myModal' class='btn btn-default btn-small' id='custId' data-toggle='modal' data-id=" . $d['noid'] . ">Ajukan</a></td>";
                  // } else {
                  //   echo "<td><a href='#myModal' class='btn btn-default btn-small' id='custId' data-toggle='modal' data-id=" . $d['noid'] . ">Ajukan</a></td>";
                  // }
                  ?>
                </tr>
                <?php array_push($checkWaktu, $d['waktu']); ?>
              <?php endif; ?>
            <?php } ?>
          </tbody>
        </table>
      </div><!-- /.table-responsive -->
    </div>

    <br /><br />

    <h5>Daftar Permohonan Budget</h5>
    <div class="panel panel-warning">
      <div class="panel-body no-padding">
        <table class="table table-striped">
          <thead>
            <tr class="warning">
              <th>No</th>
              <th>Nama Project</th>
              <th>Doc</th>
              <th>Jenis</th>
              <th>Tahun</th>
              <th>Nama Yang Mengajukan</th>
              <th>Divisi</th>
              <th>Action</th>
              <th>Status</th>
              <th>Keterangan</th>
              <!-- <th>Pengajuan Request</th> -->
            </tr>
          </thead>

          <tbody>
            <?php
            $i = 1;
            $divisi = $_SESSION['divisi'];
            $username = $_SESSION['nama_user'];
            $checkWaktu = [];
            $sql = mysqli_query($koneksi, "SELECT * FROM pengajuan_request WHERE pengaju='$username' AND (status_request='Belum Di Ajukan' OR status_request='Ditolak')");
            while ($d = mysqli_fetch_array($sql)) {
              if (!in_array($d['waktu'], $checkWaktu)) :
                $arrDocument = [];
                $document = unserialize($d['document']);
                if (!is_array($document)) {
                  array_push($arrDocument, $document);
                } else {
                  $arrDocument = $document;
                }
            ?>
                <tr>
                  <th scope="row"><?php echo $i++; ?></th>
                  <td><?php echo $d['nama']; ?></td>
                  <td>
                    <?php if ($arrDocument[0]) : ?>
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
                  <td><?php echo $d['jenis']; ?></td>
                  <td><?php echo $d['tahun']; ?></td>
                  <td><?php echo $d['pengaju']; ?></td>
                  <td><?php echo $d['divisi']; ?></td>
                  <td><a href="view-request.php?id=<?php echo $d['id']; ?>"><i class="fas fa-eye" title="View"></i></a></td>
                  <td><?php echo $d['status_request']; ?></td>
                  <?php if ($d['status_request'] == 'Ditolak') : ?>
                    <td><?php echo $d['declined_note']; ?></td>
                  <?php else : ?>
                    <td>-</td>
                  <?php endif; ?>

                  <!-- <td><a href='#requestModal' class='btn btn-default btn-small buttonAjukan' id="buttonRequestAjukan" data-toggle='modal' data-id="<?= $d['id'] ?>">Ajukan</a></td> -->
                </tr>
                <?php array_push($checkWaktu, $d['waktu']); ?>
              <?php endif; ?>
            <?php } ?>
          </tbody>
        </table>
      </div><!-- /.table-responsive -->
    </div>

    <p>
      <b>KETENTUAN DALAM PEMBUATAN BUDGET ONLINE UNTUK PROJECT :</b><br>
      1. Melengkapi berkas yang diperlukan pada aplikasi Digital Marketing<br>
      2. Request pembukaan akses pengajuan budget online kepada bu Ina <br>
      3. Apabila akses pengajuan budget telah dibuka, silahkan mengisi detail budget<br>
      4. Klik Ajukan setelah item budget sudah diinput semua, budget yang telah diajukan tidak bisa diubah kembali.<br>
      5. Request approval budget online yang sudah dibuat ke Bu Ina.<br>
      <!-- 4. Budget tidak akan bisa dibuat apabila belum upload berkas pengajuan yang sudah di approve.<br> -->
      <!-- <b>KETENTUAN DALAM PEMBUATAN BUDGET ONLINE UNTUK PROJECT :</b><br>
      1. Membawa berkas pengajuan budget ke Bu Ina untuk di approval.<br>
      2. Upload budget yang sudah di approve tersebut untuk dapat membuat/ menambah item budget yang termasuk didalam budget project tersebut.<br>
      3. Budget tidak akan bisa dibuat apabila belum upload berkas pengajuan yang sudah di approve.<br>
      4. Klik Ajukan setelah item budget sudah diinput semua.<br>
      5. Request approval budget online yang sudah dibuat ke Bu Ina.<br> -->
    </p>

    <p>
      <b>KETENTUAN DALAM PEMBUATAN BPU BUDGET ONLINE :</b><br>
      1. Klik BPU di item budget yang akan diajukan<br>
      2. Isi BPU sesuai kebutuhan.<br>
      3. Upload file rinci ke BPU online yang akan dibuat.<br>
      4. BPU tidak akan bisa di submit bila belum upload file rincian di pengajuan BPU online.<br>
    </p>

    </br></br>
    <?php

    if(strtolower($_SESSION['level']) == 'manager' || strtolower($_SESSION['level']) == 'senior manager' || $_SESSION['hak_page'] == 'Create') {
      echo '<a href="home.php?page=1"><button type="button" class="btn btn-primary">Create Folder Project</button></a>';
    }
    ?>
    

    <?php
    // if ($_SESSION['hak_page'] == 'Create') {
    //   echo "<a href='home.php?page=1'><button type='button' class='btn btn-primary'>Tambah Project</button></a>";
    // } else {
    //   echo "";
    // }
    ?>

    </br></br>

    <?php
    if(strtolower($_SESSION['level']) == 'manager' || strtolower($_SESSION['level']) == 'senior manager') {
      include "isi.php";
    }

    ?>

    <div class="modal fade" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            Pendaftaran Email
          </div>
          <div class="modal-body">
            <p>Silahkan masukkan email anda untuk melengkapi data diri anda</p>
            <input type="email" class="form-control" id="email" name="email" value="" autocomplete="off" required>
          </div>
          <div class="modal-footer">
            <button type="submit" id="buttonSubmitEmail" class="btn btn-success success">Submit</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="phoneNumberModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
          Pendaftaran Nomor Handphone
          </div>
          <div class="modal-body">
          <p>Silahkan masukkan Nomor Handphone anda yang terhubung dengan layanan Whatsapp untuk melengkapi data diri anda</p>
            <input type="text" class="form-control" id="phone_number" name="phone_number" value="" autocomplete="off" required>
          </div>
          <div class="modal-footer">
            <button type="submit" id="buttonSubmitPhonneNumber" class="btn btn-success success">Submit</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="signModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            Tanda tangan
          </div>
          <form action="tambah-tanda-tangan.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_user" value="<?= $_SESSION['id_user'] ?>">
            <div class="modal-body">
              <p>Silahkan masukkan foto tanda tangan Anda untuk digunakan sebagai e-sign proses pengajuan/verifikasi/persetujuan bpu</p>
              <p>Note:</p>
              <p>1. Usahakan file memiliki dimensi lebar dan tinggi yang sama (ex: 100x100)</p>
              <p>2. Ukuran maksimal file 200kb</p>
              <input type="file" class="form-control" accept="image/*" name="gambar" id="inputImageSign" required>
              <img id="imageSign" class="img-responsive" style="display: block; margin-left: auto;  margin-right: auto; background-repeat: no-repeat; background-size: cover; background-attachment: fixed;  " src="" alt="">
            </div>
            <div class="modal-footer">
              <button type="submit" id="buttonSubmitEsign" class="btn btn-success success">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class=" modal fade" id="requestModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            Konfirmasi Pengajuan Request
          </div>
          <div class="modal-body">
            Klik submit untuk untuk melakukan pengajuan request data
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <a href="" id="buttonSubmitAjukan" class="btn btn-success success">Submit</a>
          </div>
        </div>
      </div>
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

    <div class="modal fade" id="submitModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            Confirm Submit
          </div>
          <div class="modal-body">
            Klik submit untuk menyimpan data
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <a href="#" id="submitButton" class="btn btn-success success">Submit</a>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="realiasiModal" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Realisasi</h4>
          </div>
          <div class="modal-body">
            <form action="realisasi-proses-user.php" method="post" id="theForm" name="Form" enctype="multipart/form-data">
              <input type="hidden" id="noRealisasi" name="no">
              <input type="hidden" id="waktuRealisasi" name="waktu">
              <input type="hidden" id="termRealisasi" name="term">
              <input type="hidden" id="sisaRealisasi" name="sisa">
              <div class="form-group">
                <label for="rincian" class="control-label">Total BPU:</label>
                <input type="text" class="form-control" id="hasilRealisasiText" readonly>
                <input type="hidden" class="form-control" id="hasilRealisasi" name="totalbpu">
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
                <label class="control-label">Upload File <i class="fa fa-question-circle" title="Upload bukti penggunaan dana dan pengembalian dana (jika ada)"></i></label>
                <input type="file" class="form-control" accept="image/*,application/pdf" name="gambar" id="fileInput" required>
                <img class="img-responsive" style="display: block; margin-left: auto;  margin-right: auto; background-repeat: no-repeat; background-size: cover; background-attachment: fixed;" src="" alt="" id="imageRealisasi">
              </div>

              <div class="form-group" id="divTolak" style="display: none;">
                <label for="rincian" class="control-label">Alasan Penolakan:</label>
                <input type="text" class="form-control" name="alasanTolakRealisasi" id="alasanTolakRealisasi" disabled>
              </div>

              <button class="btn btn-primary" type="submit" name="submit" value="1">OK</button>
            </form>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
          </div>
        </div>
      </div>
    </div>

    <script type="text/javascript">
      const emailUser = <?= json_encode($emailUser); ?>;
      const idUser = <?= json_encode($idUser); ?>;
      const signUser = <?= json_encode($signUser); ?>;
      const phoneNumber = <?= json_encode($phoneNumber); ?>;

      console.log(phoneNumber);

      $(document).ready(function() {
        $('#inputImageSign').change(function() {
          readURLSign(this);
        })

        if (signUser == null) {
          $('#signModal').modal({
            backdrop: 'static',
            keyboard: false
          });
        }

        if (emailUser == null) {
          console.log('here');
          $('#emailModal').modal({
            backdrop: 'static',
            keyboard: false
          });
        }

        if (phoneNumber == null || phoneNumber == "") {
          $('#phoneNumberModal').modal({
            backdrop: 'static',
            keyboard: false
          });
        }

        $('#buttonSubmitPhonneNumber').click(function() {
          let phoneNumber = $('#phone_number').val();
          if (phoneNumber === "") {
            alert('Masukkan Phone Number Anda');
          } else {
            if (phoneNumber[0] == "0") {
              phoneNumber = replaceAtIndex(phoneNumber, 0, "62")
            }
            $.ajax({
              url: "register-phone-number.php",
              type: "post",
              data: {
                phoneNumber: phoneNumber,
                id: idUser
              },
              success: function(result) {
                if (result == true) {
                  alert('Pendaftaran Nomor Handphone Berhasil');
                  $('#phoneNumberModal').modal('hide');
                } else {
                  alert('Pendaftaran Nomor Handphone Gagal, ' + result);
                }
              }
            })
          }

        $('#buttonSubmitEmail').click(function() {
          const email = $('#email').val();
          if (!email) {
            alert('Masukkan Email Anda');
          } else {
            $.ajax({
              url: "pendaftaran-email.php",
              type: "post",
              data: {
                email: email,
                id: idUser
              },
              success: function(result) {
                if (result == true) {
                  alert('Pendaftaran Email Berhasil');
                  $('#emailModal').modal('hide');
                } else {
                  alert('Pendaftaran Email Gagal, ' + result);
                }
              }
            })
          }
        })

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

        const buttonRequestAjukan = document.querySelectorAll(".buttonAjukan");
        buttonRequestAjukan.forEach(function(e, i) {
          e.addEventListener("click", function() {
            let id = e.getAttribute("data-id");
            let findLink = document.getElementById("buttonSubmitAjukan");
            findLink.href = "ajukan-request-proses.php?id=" + id;
          })
        })

        $('#fileInput').change(function() {
          readURL(this);
        })
      });

      function readURLSign(input) {
        if (input.files && input.files[0]) {
          var reader = new FileReader();

          reader.onload = function(e) {
            $('#imageSign').attr('src', e.target.result);
          }

          reader.readAsDataURL(input.files[0]); // convert to base64 string
        }
      }

      function realisasi(no, waktu, term, jumlah, sisa, realisasi, uangkembali, tanggalrealisasi, file, alasan) {
        console.log(sisa);
        $('#noRealisasi').val(no);
        $('#waktuRealisasi').val(waktu);
        $('#termRealisasi').val(term);
        $('#hasilRealisasi').val(jumlah);
        $('#hasilRealisasiText').val(numberWithCommas(jumlah));
        $('#sisaRealisasi').val(sisa);
        $('#realisasi').val(realisasi);
        $('#uangkembali').val(uangkembali);
        $('#tanggalrealisasi').val(tanggalrealisasi);

        if (realisasi) {
          $('#divTolak').show();
          $('#alasanTolakRealisasi').val(alasan);
        }

        $('#realisasi').prop('max', sisa);

        // $('#imageRealisasi').attr('src', `uploads/${file}`)

        $('#realiasiModal').modal();
      }

      function readURL(input) {
        if (input.files && input.files[0]) {
          var reader = new FileReader();

          reader.onload = function(e) {
            $('#imageRealisasi').attr('src', e.target.result)
          }

          reader.readAsDataURL(input.files[0]); // convert to base64 string
        }
      }

      function numberWithCommas(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
      }
    </script>

</body>

</html>