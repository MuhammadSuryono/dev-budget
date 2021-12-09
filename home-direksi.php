<?php
// error_reporting(0);
session_start();

require "application/config/database.php";
$con = new Database();
$koneksi = $con->connect();


require "vendor/email/send-email.php";
require "dompdf/save-document.php";
require_once("dompdf/dompdf_config.inc.php");

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

$querySetting = mysqli_query($koneksi, "SELECT * FROM setting_budget WHERE keterangan = 'approval_bpu'") or die(mysqli_error($koneksi));
$setting = mysqli_fetch_assoc($querySetting);
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>Form Pengajuan Budget 1</title>
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
        <a class="navbar-brand" href="home-direksi.php">Budget-Ing</a>
      </div>
      <div class="collapse navbar-collapse" id="myNavbar">
        <ul class="nav navbar-nav">
          <li class="active"><a href="home-direksi.php">Home</a></li>
          <li><a href="list-direksi.php">List</a></li>
          <li><a href="saldobpu.php">Data User</a></li>
          <!--<li><a href="summary.php">Summary</a></li>-->
          <!-- <li><a href="hak-akses.php">Hak Akses</a></li> -->
          <li><a href="listfinish-direksi.php">Budget Finish</a></li>
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

          <!-- <li><a href="history-direksi.php">History</a></li> -->
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

    <h5>Daftar BPU yang perlu follow up</h5>
    <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
      <div class="panel-body no-padding">
        <div class="list-group-item border" id="grandparent2" style="border: 1px solid black !important;">
          <div id="expander" data-target="#grandparentContent2" data-toggle="collapse" data-group-id="grandparent<?= $i ?>" data-role="expander">

            <ul class="list-inline row border">
              <li class="col-lg-11">1. BPU yang perlu di setujui</li>
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
                  $sql = mysqli_query($koneksi, "SELECT a.*, b.nama, b.noid AS budget_noid, b.jenis, c.rincian FROM bpu a JOIN pengajuan b ON b.waktu = a.waktu JOIN selesai c ON c.waktu = a.waktu AND c.no = a.no WHERE (a.persetujuan = 'Pending' OR a.persetujuan = 'Belum Disetujui') AND (a.status_pengajuan_bpu = 0 OR a.status_pengajuan_bpu IS NULL)");
                  while ($d = mysqli_fetch_array($sql)) :
                    $unique = $d['waktu'] . $d['nama'] . $d['no'] . $d['rincian'] . $d['term'];
                    if (!in_array($unique, $checkUnique)) :
                      if ($d['jenis'] != 'Rutin') : ?>
                        <?php if ($d['jumlah'] > $setting['plafon']) : ?>
                          <tr>
                            <td><?= $i++ ?></td>
                            <td><?= $d['nama'] ?></td>
                            <td><?= $d['no'] ?></td>
                            <td><?= $d['rincian'] ?></td>
                            <td><?= $d['term'] ?></td>
                            <td>
                              <a href="views-direksi.php?code=<?= $d['budget_noid'] ?>" target="_blank"><i class="fas fa-external-link-alt" title="View Budget"></i></a>
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
        <div class="list-group-item border" id="reminder-um-jatuh-tempo" style="border: 1px solid black !important;">
          <div id="expander" data-target="#content-reminder-um-jatuh-tempo" data-toggle="collapse" data-group-id="grandparent<?= $i ?>" data-role="expander">

            <ul class="list-inline row border">
              <li class="col-lg-11" id="title-reminder-um-jatuh-tempo">2. Reminder Approval UM Perpanjangan Jatuh Tempo <span class="text-danger">(*)</span></li>
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
                    <th>Tanggal Perpanjangan</th>
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

    <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
      <div class="panel-body no-padding">
        <table class="table table-striped">
          <thead>
            <tr class="warning">
              <th>No</th>
              <th>Nama Project</th>
              <th>Doc</th>
              <th>Tahun</th>
              <th>PIC Budget</th>
              <th>Divisi</th>
              <th>Action</th>
              <th>Status</th>
            </tr>
          </thead>

          <tbody>

            <?php
            $i = 1;
            $divisi = $_SESSION['divisi'];
            $username = $_SESSION['nama_user'];
            $checkWaktu = [];
            // $sql = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE status='Belum Di Ajukan' AND pembuat='Dummy' OR status='Belum Di Ajukan' AND pembuat='Ina Puspito'");
            $sql = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE status='Belum Di Ajukan' AND pembuat='Ina Puspito'");
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
                  <td><?php echo $d['tahun']; ?></td>
                  <td><?php echo $d['pengaju']; ?></td>
                  <td><?php echo $d['divisi']; ?></td>
                  <td><a href="view-direksi.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a></td>
                  <td><?php echo $d['status']; ?></td>
                </tr>
                <?php array_push($checkWaktu, $d['waktu']); ?>
              <?php endif; ?>
            <?php } ?>
          </tbody>
        </table>
      </div><!-- /.table-responsive -->
    </div>

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
            $sql = mysqli_query($koneksi, "SELECT * FROM pengajuan_request WHERE (status_request!='Dihapus' AND status_request!='Disetujui') ORDER BY id desc");
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
                  <td>
                    <a href="view-request.php?id=<?php echo $d['id']; ?>"><i class="fas fa-eye" title="View"></i></a>
                    <a href="hapus-view-request.php?id=<?php echo $d['id']; ?>" onclick="return confirm('Anda yakin ingin menghapus data pengajuan?')"><i class="fas fa-trash" title="Delete"></i></a>
                  </td>
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


    <p>
      <b>KETENTUAN DALAM PEMBUATAN BUDGET ONLINE UNTUK PROJECT :</b><br>
      1. Melengkapi berkas yang diperlukan pada aplikasi Digital Marketing<br>
      2. Request pembukaan akses pengajuan budget online kepada bu Ina <br>
      3. Apabila akses pengajuan budget telah dibuka, silahkan mengisi detail budget<br>
      4. Klik Ajukan setelah item budget sudah diinput semua, budget yang telah diajukan tidak bisa diubah kembali.<br>
      5. Request approval budget online yang sudah dibuat ke Bu Ina.<br>
      <!-- <b>KETENTUAN DALAM PEMBUATAN BUDGET ONLINE UNTUK PROJECT & NON RUTIN :</b><br>
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

    <a href="home-direksi.php?page=1"><button type="button" class="btn btn-primary">Create Folder Project</button></a>

    <br /><br />
    <?php
    include "isi.php";
    ?>

  </div>

  <div class="modal fade" id="requestModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          Konfirmasi Pengajuan Request
        </div>
        <div class="modal-body">
          Masukan Kode Berikut untuk menyetujui:
          <h2 style="text-align: center;" id="kodeApprove"></h2>
          <input type="text" class="form-control" id="inputKode" name="namaProject" value="<?= $namaProject ?>" autocomplete="off" required>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <a href="" id="buttonSubmitAjukan" class="btn btn-success success">Submit</a>
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

  <div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          Konfirmasi Pengajuan Request
        </div>
        <div class="modal-body">
          Klik Submit untuk menolak pengajuan dana.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <a href="" id="buttonSubmitCancelAjukan" class="btn btn-success success">Submit</a>
        </div>
      </div>
    </div>
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

  <!-- <div class="modal fade" id="sendCodeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          Konfirmasi Pengajuan Request
        </div>
        <div class="modal-body">
          Masukan email untuk mengirim kode
          <input type="text" class="form-control" id="email" name="email" value="" autocomplete="off" required>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button href="" id="buttonSubmitEmail" class="btn btn-success success">Submit</button>
        </div>
      </div>
    </div>
  </div> -->

</body>

<script>
  const emailUser = <?= json_encode($emailUser); ?>;
  const idUser = <?= json_encode($idUser); ?>;
  const signUser = <?= json_encode($signUser); ?>;
  const phoneNumber = <?= json_encode($phoneNumber); ?>;


  const titleReminderUmJatuhTempo = document.getElementById('title-reminder-um-jatuh-tempo')
  const reminderReminderUmJatuhTempo = document.getElementById('reminder-um-jatuh-tempo')

  $(document).ready(function() {
  //   console.log(phoneNumber);

    $('#inputImageSign').change(function() {
      readURLSign(this);
    })

    setTimeout(() => {
        bpuUMJatuhTempo()
      }, 1000)

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
    // if (phoneNumber == null || phoneNumber == "") {
    //       $('#phoneNumberModal').modal({
    //         backdrop: 'static',
    //         keyboard: false
    //       });
    //     }
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

    function readURLSign(input) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
          $('#imageSign').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]); // convert to base64 string
      }
    }

    const buttonRequestAjukan = document.querySelectorAll(".buttonAjukan");
    let fullcode = '';
    buttonRequestAjukan.forEach(function(e, i) {
      e.addEventListener("click", function() {
        let nama = e.getAttribute("data-code");
        let code1 = nama.substring(0, 4);
        let code2 = nama.substring(nama.length - 4, nama.length);
        fullcode = `${code1}APPROVE${code2}`;

        $('#kodeApprove').text(fullcode);

        let findLink = document.getElementById("buttonSubmitAjukan");
        findLink.addEventListener("click", function() {
          if ($('#inputKode').val() == fullcode) {
            let id = e.getAttribute("data-id");
            findLink.href = "setuju-request-proses.php?id=" + id;
          } else {
            alert("Kode Approval Salah");
            findLink.href = "home-direksi.php";
          }
        })
      })
    })

    const buttonCancel = document.querySelectorAll(".buttonCancel");
    buttonCancel.forEach(function(e, i) {
      e.addEventListener("click", function() {
        let findLink = document.getElementById("buttonSubmitCancelAjukan");
        findLink.addEventListener("click", function() {
          let id = e.getAttribute("data-id");
          findLink.href = "request-budget-tolak.php?id=" + id;
        })
      })
    })

    const buttonSubmitEmail = document.getElementById("buttonSubmitEmail");
    buttonSubmitEmail.addEventListener("click", function() {
      const email = $('#email').val();
      $.ajax({
        url: 'kirim-email.php',
        type: 'post',
        data: {
          code: fullcode,
          email: email
        },
        success: function(data) {
          $('#sendCodeModal').modal('toggle')
        }
      })
    })

    function replaceAtIndex(_string,_index,_newValue) {
        if(_index > _string.length-1) 
        {
            return string
        }
        else{
        return _string.substring(0,_index) + _newValue + _string.substring(_index+1)
        }
    }

    function bpuUMJatuhTempo() {
      let bodyTable = document.getElementById('data-bpu-jatuh-tempo')
      httpRequestGet('/dev-budget/ajax/ajax-um-jatuh-tempo.php?action=direksi-get-list').then((res) => {
        if (res.data !== null && res.data.length > 0) {
          titleReminderUmJatuhTempo.classList.add('text-blink')
          reminderReminderUmJatuhTempo.innerHTML = `<div class="alert alert-danger" role="alert"><i class='fa fa-bell text-blink'></i>
  Anda memiliki <b>Pengajuan Perpanjangan</b> BPU yang telah Jatuh Tempo
</div>`

          let htmlBody = '';
          let data = res.data

          data.forEach((element, i) => {
            htmlBody += `<tr><td>${i + 1}</td><td>${element.nama}</td><td>${element.no_urut}</td><td>${element.jenis}</td><td>${element.term}</td><td>${element.tanggal_jatuh_tempo}</td><td>${element.tanggal_perpanjangan}</td><td><a href="bpu-perpanjangan.php?id=${element.id}&bpu=${element.id_bpu}"><i class="fas fa-calendar-alt" title="View Jatuh Tempo"></i></a></td></tr>`
          });

          bodyTable.innerHTML = htmlBody
          
        }
      })
    }

    function httpRequestGet(url) {
      return fetch(url)
      .then((response) => response.json())
      .then(data => data);
    }
  })
</script>

</html>