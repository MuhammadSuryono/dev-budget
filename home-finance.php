<?php
error_reporting(0);
session_start();

require "application/config/database.php";
require_once "application/config/whatsapp.php";
require_once "application/config/message.php";

$con = new Database();
$koneksi = $con->connect();

$helperMessage = new Message();
$whatsapp = new Whastapp();
require "vendor/email/send-email.php";

if (!isset($_SESSION['nama_user'])) {
  header("location:login.php");
}

$url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$url = explode('/', $url);
$url = $url[0] . '/' . $url[1] . '/' . 'login.php';

$idUser = $_SESSION['id_user'];
$queryUser = mysqli_query($koneksi, "SELECT email, e_sign, phone_number FROM tb_user WHERE id_user = '$idUser'");
$user = mysqli_fetch_assoc($queryUser);
$emailUser = $user['email'];
$signUser = $user['e_sign'];
$phoneNumber = $user['phone_number'];

$queryRekening = mysqli_query($koneksiDevelop, "SELECT * FROM kas WHERE stat = 'MRI'");

$querySetting = mysqli_query($koneksi, "SELECT * FROM setting_budget WHERE keterangan = 'approval_bpu'") or die(mysqli_error($koneksi));
$setting = mysqli_fetch_assoc($querySetting);

$oneWeek = date('Y-m-d', strtotime('+7 days'));

$email = [];
$queryEmailFinance = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('2', '3')");
while ($item = mysqli_fetch_assoc($queryEmailFinance)) {
  array_push($email, $item['phone_number']);
}

$queryReminderPembayaran = mysqli_query($koneksi, "SELECT a.*, b.nama AS nama_project, c.rincian FROM reminder_tanggal_bayar a JOIN pengajuan b ON b.waktu = a.selesai_waktu JOIN selesai c ON c.waktu = a.selesai_waktu AND c.no = a.selesai_no WHERE a.tanggal <= '$oneWeek' AND (has_send_email = 0 OR has_send_email IS NULL)");
while ($item = mysqli_fetch_assoc($queryReminderPembayaran)) {

  // $msg = "Reminder Pembayaran, <br><br>
  //               Nama Project      : <strong>" . $item['nama_project'] . "</strong><br>
  //               Nama Item Budget  : <strong>" . $item['rincian'] . "</strong><br>
  //               Tanggal Bayar     : <strong>" . date('d-m-Y', strtotime($item['tanggal']))  .  "</strong><br><br>
  //               Klik <a href='$url'>Disini</a> untuk membuka aplikasi budget.
  //               ";

  // $subject = "Reminder Pembayaran";

  if (count($email) > 0) {
    foreach($email  as $phone) {
      $whatsapp->sendMessage($phone, $helperMessage->messageReminderPembayaran($item['nama_project'], $item['rincian'], date('d-m-Y', strtotime($item['tanggal'])), $url));
    }
  }


  // $message = sendEmail($msg, $subject, $email, $name, $address = "multiple");

  mysqli_query($koneksi, "UPDATE reminder_tanggal_bayar SET has_send_email = 1 WHERE id = $item[id]");
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
<style>
  .text-blink {
  animation: blinker 1s linear infinite;
  }

  @keyframes blinker {
    50% {
      opacity: 0;
    }
  }

  .alert-blink {
  animation: blinker-alert 5s linear infinite;
  }

  @keyframes blinker-alert {
    50% {
      opacity: 0;
    }
  }
</style>

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
          <li class="active"><a href="home-finance.php">Home</a></li>

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
          <?php
          if ($_SESSION['hak_page'] == 'Suci') {
            echo "<li><a href='rekap-project.php'>Rekap Project</a></li>";
          } else {
            echo "";
          }
          ?>
        </ul>

      
       <ul class="nav navbar-nav navbar-right">
                        <li><a href="notif-page.php"><i class="fa fa-envelope"></i></a></li>
          <li><a href="ubahpassword.php"><span class="glyphicon glyphicon-user"></span><?php echo $_SESSION['nama_user']; ?> (<?php echo $_SESSION['divisi']; ?>)</a></li>
          <li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <br /><br />

  <div class="container">
    <div id="reminder-um-jatuh-tempo"></div>
    <p>
    <h4> Saldo BPU :
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

    <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
      <div class="panel-body no-padding">
        <table class="table table-striped">
          <thead>
            <tr class="warning">
              <th>No</th>
              <th>Nama Project</th>
              <th>Jenis</th>
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
            $sql = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE pengaju='$username' AND status='Belum Di Ajukan' ORDER BY noid desc");
            while ($d = mysqli_fetch_array($sql)) {
            ?>
              <tr>
                <th scope="row"><?php echo $i++; ?></th>
                <td><?php echo $d['nama']; ?></td>
                <td><?php echo $d['jenis']; ?></td>
                <td><?php echo $d['tahun']; ?></td>
                <td><?php echo $d['pengaju']; ?></td>
                <td><?php echo $d['divisi']; ?></td>
                <td><a href="view.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a></td>
                <td><?php echo $d['status']; ?></td>
                <?php
                if ($d['jenis'] == 'B1' || $d['jenis'] == 'B2' || $d['jenis'] == 'UM Burek') {
                  echo "---";
                } else {
                  echo "<td><a href='#myModal' class='btn btn-default btn-small' id='custId' data-toggle='modal' data-id=" . $d['noid'] . ">Ajukan</a></td>";
                }
                ?>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div><!-- /.table-responsive -->
    </div>
                
    <?php $j = 1;?>
    <h5>Daftar BPU yang perlu follow up</h5>
    <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
      <div class="panel-body no-padding">
        <div class="list-group-item border" id="grandparent1" style="border: 1px solid black !important;">
          <div id="expander" data-target="#grandparentContent1" data-toggle="collapse" data-group-id="grandparent<?= $i ?>" data-role="expander">

            <ul class="list-inline row border">
              <li class="col-lg-11"><?= $j++ ?>. BPU yang perlu di verifikasi</li>
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
                  $sql = mysqli_query($koneksi, "SELECT a.*, b.nama, b.noid AS budget_noid, b.jenis, c.rincian FROM bpu a JOIN pengajuan b ON b.waktu = a.waktu JOIN selesai c ON c.waktu = a.waktu AND c.no = a.no WHERE status_pengajuan_bpu = 1 ORDER BY a.noid desc");
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
                          <?php
                          $aksesSes = $_SESSION['hak_akses'];
                          if ($aksesSes == 'Manager') :
                            if ($d['jenis'] == 'B1') : ?>
                              <a href="view-finance-manager-b1.php?code=<?= $d['budget_noid'] ?>" target="_blank"><i class="fas fa-external-link-alt" title="View Budget"></i></a>
                            <?php elseif ($d['jenis'] == 'B2' || $d['jenis'] == 'Rutin') : ?>
                              <a href="view-finance-manager.php?code=<?= $d['budget_noid'] ?>" target="_blank"><i class="fas fa-external-link-alt" title="View Budget"></i></a>
                            <?php elseif ($d['jenis'] == 'Non Rutin') : ?>
                              <a href="view-finance-nonrutin-manager.php?code=<?= $d['budget_noid'] ?>" target="_blank"><i class="fas fa-external-link-alt" title="View Budget"></i></a>
                            <?php endif; ?>
                          <?php else : ?>
                            <?php if ($d['jenis'] == 'B1' || $d['jenis'] == 'B2' || $d['jenis'] == 'Rutin') : ?>
                              <a href="view-finance.php?code=<?= $d['budget_noid'] ?>" target="_blank"><i class="fas fa-external-link-alt" title="View Budget"></i></a>
                            <?php elseif ($d['jenis'] == 'Non Rutin') : ?>
                              <a href="view-finance-nonrutin.php?code=<?= $d['budget_noid'] ?>" target="_blank"><i class="fas fa-external-link-alt" title="View Budget"></i></a>
                            <?php endif; ?>
                          <?php endif; ?>
                        </td>
                      </tr>
                      <?php array_push($checkUnique, $unique); ?>
                    <?php endif; ?>
                  <?php endwhile; ?>

                </tbody>
              </table>
          </div>
        </div>
        <?php if ($_SESSION['hak_akses'] == 'Manager') : ?>
          <div class="list-group-item border" id="grandparent2" style="border: 1px solid black !important;">
            <div id="expander" data-target="#grandparentContent2" data-toggle="collapse" data-group-id="grandparent<?= $i ?>" data-role="expander">

              <ul class="list-inline row border">
                <li class="col-lg-11"><?= $j++ ?>. BPU yang perlu di setujui</li>
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
                    $checkUnique = [];
                    $sql = mysqli_query($koneksi, "SELECT a.*, b.nama, b.noid AS budget_noid, b.jenis, c.rincian FROM bpu a JOIN pengajuan b ON b.waktu = a.waktu JOIN selesai c ON c.waktu = a.waktu AND c.no = a.no WHERE (a.persetujuan = 'Pending' OR a.persetujuan = 'Belum Disetujui') AND (a.status_pengajuan_bpu = 0 OR a.status_pengajuan_bpu IS NULL) order by a.noid desc");
                    while ($d = mysqli_fetch_array($sql)) :
                      $unique = $d['waktu'] . $d['nama'] . $d['no'] . $d['rincian'] . $d['term'];
                      if (!in_array($unique, $checkUnique)) :
                        if ($d['jenis'] == 'Rutin') :
                    ?>
                          <tr>
                            <td><?= $i++ ?></td>
                            <td><?= $d['nama'] ?></td>
                            <td><?= $d['no'] ?></td>
                            <td><?= $d['rincian'] ?></td>
                            <td><?= $d['term'] ?></td>
                            <td>
                              <?php
                              $aksesSes = $_SESSION['hak_akses'];
                              if ($aksesSes == 'Manager') :
                                if ($d['jenis'] == 'B1') : ?>
                                  <a href="view-finance-manager-b1.php?code=<?= $d['budget_noid'] ?>" target="_blank"><i class="fas fa-external-link-alt" title="View Budget"></i></a>
                                <?php elseif ($d['jenis'] == 'B2' || $d['jenis'] == 'Rutin') : ?>
                                  <a href="view-finance-manager.php?code=<?= $d['budget_noid'] ?>" target="_blank"><i class="fas fa-external-link-alt" title="View Budget"></i></a>
                                <?php elseif ($d['jenis'] == 'Non Rutin') : ?>
                                  <a href="view-finance-nonrutin-manager.php?code=<?= $d['budget_noid'] ?>" target="_blank"><i class="fas fa-external-link-alt" title="View Budget"></i></a>
                                <?php endif; ?>
                              <?php else : ?>
                                <?php if ($d['jenis'] == 'B1' || $d['jenis'] == 'B2' || $d['jenis'] == 'Rutin') : ?>
                                  <a href="view-finance.php?code=<?= $d['budget_noid'] ?>" target="_blank"><i class="fas fa-external-link-alt" title="View Budget"></i></a>
                                <?php elseif ($d['jenis'] == 'Non Rutin') : ?>
                                  <a href="view-finance-nonrutin.php?code=<?= $d['budget_noid'] ?>" target="_blank"><i class="fas fa-external-link-alt" title="View Budget"></i></a>
                                <?php endif; ?>
                              <?php endif; ?>
                            </td>
                          </tr>
                        <?php else : ?>
                          <?php if ($d['jumlah'] <= $setting['plafon']) : ?>
                            <tr>
                              <td><?= $i++ ?></td>
                              <td><?= $d['nama'] ?></td>
                              <td><?= $d['no'] ?></td>
                              <td><?= $d['rincian'] ?></td>
                              <td><?= $d['term'] ?></td>
                              <td>
                                <?php
                                $aksesSes = $_SESSION['hak_akses'];
                                if ($aksesSes == 'Manager') :
                                  if ($d['jenis'] == 'B1') : ?>
                                    <a href="view-finance-manager-b1.php?code=<?= $d['budget_noid'] ?>" target="_blank"><i class="fas fa-external-link-alt" title="View Budget"></i></a>
                                  <?php elseif ($d['jenis'] == 'B2' || $d['jenis'] == 'Rutin') : ?>
                                    <a href="view-finance-manager.php?code=<?= $d['budget_noid'] ?>" target="_blank"><i class="fas fa-external-link-alt" title="View Budget"></i></a>
                                  <?php elseif ($d['jenis'] == 'Non Rutin') : ?>
                                    <a href="view-finance-nonrutin-manager.php?code=<?= $d['budget_noid'] ?>" target="_blank"><i class="fas fa-external-link-alt" title="View Budget"></i></a>
                                  <?php endif; ?>
                                <?php else : ?>
                                  <?php if ($d['jenis'] == 'B1' || $d['jenis'] == 'B2' || $d['jenis'] == 'Rutin') : ?>
                                    <a href="view-finance.php?code=<?= $d['budget_noid'] ?>" target="_blank"><i class="fas fa-external-link-alt" title="View Budget"></i></a>
                                  <?php elseif ($d['jenis'] == 'Non Rutin') : ?>
                                    <a href="view-finance-nonrutin.php?code=<?= $d['budget_noid'] ?>" target="_blank"><i class="fas fa-external-link-alt" title="View Budget"></i></a>
                                  <?php endif; ?>
                                <?php endif; ?>
                              </td>
                            </tr>
                          <?php endif; ?>
                        <?php endif; ?>

                        <?php
                        array_push($checkUnique, $unique);
                        ?>
                      <?php endif; ?>
                    <?php endwhile; ?>

                  </tbody>
                </table>
            </div>
          </div>
          <div class="list-group-item border" id="bpu-eksternal-need-verifikasi" style="border: 1px solid black !important;">
          <div id="expander" data-target="#bpu-content-eksternal-need-verifikasi" data-toggle="collapse" data-group-id="grandparent<?= $i ?>" data-role="expander">

            <ul class="list-inline row border">
              <li class="col-lg-11" id="title-text-bpu-eksternal"><?= $j++ ?>. BPU Eksternal Perlu di Validasi <span class="text-danger">(*)</span></li>
              <li class="col-lg-1">
                <span id="grandparentIcon1" style="cursor: pointer; margin: 0 10px;" class="col-lg-1"><a><i class="fas fa-eye" title="View Rincian"></i></a></span>
              </li>
            </ul>
          </div>
          <div class="collapse" id="bpu-content-eksternal-need-verifikasi" aria-expanded="true">
            <table class="table table-striped">
              <table class="table table-striped">
                <thead>
                  <tr class="warning">
                    <th>No</th>
                    <th>Nama Project</th>
                    <th>Nomor Item Budget</th>
                    <th>Jenis Item Budget</th>
                    <th>Term BPU</th>
                    <th>Action</th>
                    <!-- <th>Pengajuan Request</th> -->
                  </tr>
                </thead>

                <tbody id="data-bpu-need-validasi">
                  
                </tbody>
              </table>
          </div>
        </div>
        <?php endif; ?>
        <div class="list-group-item border" id="grandparent3" style="border: 1px solid black !important;">
          <div id="expander" data-target="#grandparentContent3" data-toggle="collapse" data-group-id="grandparent<?= $i ?>" data-role="expander">

            <ul class="list-inline row border">
              <li class="col-lg-11"><?= $j++ ?>. Reminder Pembayaran Vendor Non Rutin</li>
              <li class="col-lg-1">
                <span id="grandparentIcon1" style="cursor: pointer; margin: 0 10px;" class="col-lg-1"><a><i class="fas fa-eye" title="View Rincian"></i></a></span>
              </li>
            </ul>
          </div>
          <div class="collapse" id="grandparentContent3" aria-expanded="true">
            <table class="table table-striped">
              <table class="table table-striped">
                <thead>
                  <tr class="warning">
                    <th>No</th>
                    <th>Nama Project</th>
                    <th>Nomor Item Budget</th>
                    <th>Rincian Item Budget</th>
                    <th>Tanggal Pembayaran</th>
                    <th>Action</th>
                    <!-- <th>Pengajuan Request</th> -->
                  </tr>
                </thead>

                <tbody>
                  <?php
                  $i = 1;
                  $queryReminderPembayaran = mysqli_query($koneksi, "SELECT a.*, b.nama AS nama_project, c.rincian FROM reminder_tanggal_bayar a JOIN pengajuan b ON b.waktu = a.selesai_waktu JOIN selesai c ON c.waktu = a.selesai_waktu AND c.no = a.selesai_no WHERE a.tanggal <= '$oneWeek'");
                  while ($d = mysqli_fetch_array($queryReminderPembayaran)) :
                  ?>
                    <tr>
                      <td><?= $i++ ?></td>
                      <td><?= $d['nama_project'] ?></td>
                      <td><?= $d['selesai_no'] ?></td>
                      <td><?= $d['rincian'] ?></td>
                      <td><?= $d['tanggal'] ?></td>
                      <td>
                        <?php
                        $aksesSes = $_SESSION['hak_akses'];
                        if ($aksesSes == 'Manager') :
                          if ($d['jenis'] == 'B1') : ?>
                            <a href="view-finance-manager-b1.php?code=<?= $d['budget_noid'] ?>" target="_blank"><i class="fas fa-external-link-alt" title="View Budget"></i></a>
                          <?php elseif ($d['jenis'] == 'B2' || $d['jenis'] == 'Rutin') : ?>
                            <a href="view-finance-manager.php?code=<?= $d['budget_noid'] ?>" target="_blank"><i class="fas fa-external-link-alt" title="View Budget"></i></a>
                          <?php elseif ($d['jenis'] == 'Non Rutin') : ?>
                            <a href="view-finance-nonrutin-manager.php?code=<?= $d['budget_noid'] ?>" target="_blank"><i class="fas fa-external-link-alt" title="View Budget"></i></a>
                          <?php endif; ?>
                        <?php else : ?>
                          <?php if ($d['jenis'] == 'B1' || $d['jenis'] == 'B2' || $d['jenis'] == 'Rutin') : ?>
                            <a href="view-finance.php?code=<?= $d['budget_noid'] ?>" target="_blank"><i class="fas fa-external-link-alt" title="View Budget"></i></a>
                          <?php elseif ($d['jenis'] == 'Non Rutin') : ?>
                            <a href="view-finance-nonrutin.php?code=<?= $d['budget_noid'] ?>" target="_blank"><i class="fas fa-external-link-alt" title="View Budget"></i></a>
                          <?php endif; ?>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endwhile; ?>

                </tbody>
              </table>
          </div>
        </div>
        <?php if ($_SESSION['hak_akses'] != 'Manager') { ?>
        <div class="list-group-item border" id="bpu-eksternal-need-verifikasi" style="border: 1px solid black !important;">
          <div id="expander" data-target="#bpu-content-eksternal-need-verifikasi" data-toggle="collapse" data-group-id="grandparent<?= $i ?>" data-role="expander">

            <ul class="list-inline row border">
              <li class="col-lg-11" id="title-text-bpu-eksternal"><?= $j++ ?>. BPU Eksternal Perlu di Verifikasi <span class="text-danger">(*)</span></li>
              <li class="col-lg-1">
                <span id="grandparentIcon1" style="cursor: pointer; margin: 0 10px;" class="col-lg-1"><a><i class="fas fa-eye" title="View Rincian"></i></a></span>
              </li>
            </ul>
          </div>
          <div class="collapse" id="bpu-content-eksternal-need-verifikasi" aria-expanded="true">
            <table class="table table-striped">
              <table class="table table-striped">
                <thead>
                  <tr class="warning">
                    <th>No</th>
                    <th>Nama Project</th>
                    <th>Nomor Item Budget</th>
                    <th>Jenis Item Budget</th>
                    <th>Term BPU</th>
                    <th>Action</th>
                    <!-- <th>Pengajuan Request</th> -->
                  </tr>
                </thead>

                <tbody id="data-bpu-need-verifikasi">
                  
                </tbody>
              </table>
          </div>
        </div>
        <?php } ?>
        <div class="list-group-item border" id="reminder-um-jatuh-tempo" style="border: 1px solid black !important;">
          <div id="expander" data-target="#content-reminder-um-jatuh-tempo" data-toggle="collapse" data-group-id="grandparent<?= $i ?>" data-role="expander">

            <ul class="list-inline row border">
              <li class="col-lg-11" id="title-reminder-um-jatuh-tempo"><?= $j++ ?>. Reminder UM Jatuh Tempo <span class="text-danger">(*)</span></li>
              <li class="col-lg-1">
                <span id="grandparentIcon1" style="cursor: pointer; margin: 0 10px;" class="col-lg-1"><a><i class="fas fa-eye" title="View Rincian"></i></a></span>
              </li>
            </ul>
          </div>
          <div class="collapse" id="content-reminder-um-jatuh-tempo" aria-expanded="true">
            <table class="table table-striped">
              <table class="table table-striped">
                <thead>
                  <tr class="warning">
                    <th>No</th>
                    <th>Nama Project</th>
                    <th>Nomor Item Budget</th>
                    <th>Jenis Item Budget</th>
                    <th>Term BPU</th>
                    <th>Tanggal Jatuh Tempo</th>
                    <th>Keterangan</th>
                    <th>Action</th>
                    <!-- <th>Pengajuan Request</th> -->
                  </tr>
                </thead>

                <tbody id="data-bpu-jatuh-tempo">
                  
                </tbody>
              </table>
          </div>
        </div>
      </div>
    </div>
    <br>

    <?php if ($_SESSION['hak_akses'] == 'Manager') : ?>
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
              $checkWaktu = [];
              $sql = mysqli_query($koneksi, "SELECT * FROM pengajuan_request WHERE totalbudget <= 1000000 AND (status_request!='Dihapus' AND status_request!='Disetujui')");
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
                    <?php if ($d['status_request'] == 'Di Ajukan') : ?>
                      <td><?= ($d['submission_note']) ? $d['submission_note'] : '-' ?></td>
                    <?php else : ?>
                      <td>-</td>
                    <?php endif; ?>
                    <?php $code = strtoupper(md5($d['nama'])); ?>
                    <!-- <td>
                    <a href='#requestModal' class='btn btn-default btn-small buttonAjukan' id="buttonRequestAjukan" data-toggle='modal' data-id="<?= $d['id'] ?>" data-code="<?= $code ?>">Setujui</a>
                    <a href='#cancelModal' class='btn btn-danger btn-small buttonCancel' id="buttonTolakAjukan" data-toggle='modal' data-id="<?= $d['id'] ?>" data-code="<?= $code ?>">Tolak</a>
                     <a href='#sendCodeModal' class='btn btn-primary btn-small buttonAjukan' id="buttonRequestAjukan" data-toggle='modal' data-id="<?= $d['id'] ?>" data-code="<?= $code ?>">Send Code</a>
                  </td> -->

                  </tr>
                  <?php array_push($checkWaktu, $d['waktu']); ?>
                <?php endif; ?>
              <?php } ?>
            </tbody>
          </table>
        </div><!-- /.table-responsive -->
      </div>
    <?php endif; ?>

    <a href="home-finance.php?page=1"><button type="button" class="btn btn-primary">Create Folder Project</button></a>

    <br /><br />

    <?php
    include "isi.php";
    ?>

  </div>

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

  <div class="modal fade" id="rekeningModal" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Notifikasi Top Up</h4>
        </div>
        <div class="modal-body">
          <?php
          $statusTopUp = 0;
          while ($item = mysqli_fetch_assoc($queryRekening)) :
            $querySaldo = mysqli_query($koneksiMriTransfer, "SELECT * FROM saldo WHERE rekening = '$item[rekening]' ORDER BY saldo_id DESC LIMIT 1");
            $getSaldo = mysqli_fetch_assoc($querySaldo);

            $countTotal = mysqli_query($koneksiTransfer, "SELECT SUM(jumlah) AS total FROM data_transfer WHERE rekening_sumber = '$item[rekening]' AND hasil_transfer = 1 AND ket_transfer = 'Antri'");
            $total = mysqli_fetch_assoc($countTotal);

            if ($getSaldo['saldo'] < $total['total']) :
              $statusTopUp = 1;
          ?>
              <p>Rekening <?= $item['rekening'] ?> kurang Rp. <?= number_format($total['total'] - $getSaldo['saldo']) ?> dari total transfer yang dibutuhkan, Harap segera menambah saldo anda.</p><br>
            <?php endif ?>
          <?php endwhile; ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
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

  <script type="text/javascript">
    const emailUser = <?= json_encode($emailUser); ?>;
    const idUser = <?= json_encode($idUser); ?>;
    const statusTopUp = '<?= $statusTopUp ?>';
    const signUser = <?= json_encode($signUser); ?>;
    const phoneNumber = <?= json_encode($phoneNumber); ?>;

    const titleBpuEksternalVerifikasi = document.getElementById('title-text-bpu-eksternal');
    const titleReminderUmJatuhTempo = document.getElementById('title-reminder-um-jatuh-tempo')
    const reminderReminderUmJatuhTempo = document.getElementById('reminder-um-jatuh-tempo')

    $(document).ready(function() {
      $('#inputImageSign').change(function() {
        readURLSign(this);
      })

      setTimeout(() => {
        bpuEksternalNeedVerify()
        bpuEksternalNeedValidation()
      }, 1000)

      setTimeout(() => {
        bpuUMJatuhTempo()
      }, 1000)


      if (signUser == null) {
        $('#signModal').modal({
          backdrop: 'static',
          keyboard: false
        });
      }
      if (statusTopUp == 1) {
        $('#rekeningModal').modal();
      }
      
      if (emailUser == null || emailUser == "") {
        $('#emailModal').modal({
          backdrop: 'static',
          keyboard: false
        });
      }
      
      // if (phoneNumber == null || phoneNumber == "") {
        //     $('#phoneNumberModal').modal({
          //       backdrop: 'static',
          //       keyboard: false
          //     });
          //   }
          
          $('#buttonSubmitPhonneNumber').click(function() {
            let phoneNumber = $('#phone_number').val();
            if (phoneNumber === "") {
              alert('Masukkan Phone Number Anda');
            } else {
              // if (phoneNumber[0] == "0") {
                //   phoneNumber = replaceAtIndex(phoneNumber, 0, "62")
                // }
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
            })
            
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
    
    function replaceAtIndex(_string,_index,_newValue) {
      if(_index > _string.length-1) 
      {
        return string
      }
      else{
        return _string.substring(0,_index) + _newValue + _string.substring(_index+1)
      }
    }
    
    function httpRequestGet(url) {
      return fetch(url)
      .then((response) => response.json())
      .then(data => data);
    }
    
    function bpuEksternalNeedVerify() {
      let bodyTable = document.getElementById('data-bpu-need-verifikasi')
      httpRequestGet('ajax/ajax-bpu-need-verify.php?action=get-data').then((res) => {
        if (res.data !== null && res.data.length > 0) {
          titleBpuEksternalVerifikasi.classList.add('text-blink')

          let htmlBody = '';
          let data = res.data

          data.forEach((element, i) => {
            htmlBody += `<tr><td>${i + 1}</td><td>${element.nama}</td><td>${element.no_urut}</td><td>${element.jenis}</td><td>${element.term}</td><td><a href="view-bpu-verify.php?id=${element.id}&bpu=${element.id_bpu}"><i class="fas fa-external-link-alt" title="View Verify"></i></a></td></tr>`
          });

          bodyTable.innerHTML = htmlBody
          
        }
      })
    }

    function bpuEksternalNeedValidation() {
      let bodyTable = document.getElementById('data-bpu-need-validasi')
      httpRequestGet('ajax/ajax-bpu-need-verify.php?action=get-data-validasi').then((res) => {
        if (res.data !== null && res.data.length > 0) {
          titleBpuEksternalVerifikasi.classList.add('text-blink')

          let htmlBody = '';
          let data = res.data

          data.forEach((element, i) => {
            htmlBody += `<tr><td>${i + 1}</td><td>${element.nama}</td><td>${element.no_urut}</td><td>${element.jenis}</td><td>${element.term}</td><td><a href="/view-bpu-verify.php?id=${element.id}&bpu=${element.id_bpu}"><i class="fas fa-external-link-alt" title="View Verify"></i></a></td></tr>`
          });

          bodyTable.innerHTML = htmlBody
          
        }
      })
    }

    function bpuUMJatuhTempo() {
      let bodyTable = document.getElementById('data-bpu-jatuh-tempo')
      httpRequestGet('ajax/ajax-um-jatuh-tempo.php?action=get-list').then((res) => {
        if (res.data !== null && res.data.length > 0) {
          titleReminderUmJatuhTempo.classList.add('text-blink')
          reminderReminderUmJatuhTempo.innerHTML = `<div class="alert alert-danger" role="alert"><i class='fa fa-bell text-blink'></i>
  Anda memiliki Penagihan BPU yang telah Jatuh Tempo
</div>`

          let htmlBody = '';
          let data = res.data

          data.forEach((element, i) => {
            htmlBody += `<tr><td>${i + 1}</td><td>${element.nama}</td><td>${element.no_urut}</td><td>${element.jenis}</td><td>${element.term}</td><td>${element.tanggal_jatuh_tempo}</td><td></td><td><a href="bpu-perpanjangan.php?id=${element.id}&bpu=${element.id_bpu}"><i class="fas fa-calendar-alt" title="View Jatuh Tempo"></i></a></td></tr>`
          });

          bodyTable.innerHTML = htmlBody
          
        }
      })
    }
  </script>

</body>

</html>