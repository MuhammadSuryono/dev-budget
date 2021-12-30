<?php
// error_reporting(0);
session_start();
require_once "application/config/database.php";

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
          <?php if ($_SESSION['hak_akses'] == 'HRD') { ?>
            <li><a href="home-direksi.php">Home</a></li>
            <li><a href="list-direksi.php">List</a></li>
            <li class="active"><a href="saldobpu.php">Saldo BPU</a></li>
            <!--<li><a href="summary.php">Summary</a></li>-->
            <li><a href="listfinish-direksi.php">Budget Finish</a></li>
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
            <li class="active"><a href="saldobpu.php">Saldo BPU</a></li>
            <li><a href="history-finance.php">History</a></li>
            <li><a href="list.php">Personal</a></li>
            <li><a href="summary-finance.php">Summary</a></li>
          <?php } ?>
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


        <?php if ($_SESSION['hak_akses'] != 'HRD') { ?>
          
         <ul class="nav navbar-nav navbar-right">
                        <li><a href="notif-page.php"><i class="fa fa-envelope"></i></a></li>
            
            <li><a href="ubahpassword.php"><span class="glyphicon glyphicon-user"></span><?php echo $_SESSION['nama_user']; ?> (<?php echo $_SESSION['divisi']; ?>)</a></li>]; ?>)</a></li>
            <li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> Logout</a></li>
          </ul>
        <?php } else {
        ?>
         <ul class="nav navbar-nav navbar-right">
                        <li><a href="notif-page.php"><i class="fa fa-envelope"></i></a></li>
            

            <li><a href="ubahpassword.php"><span class="glyphicon glyphicon-user"></span><?php echo $_SESSION['nama_user']; ?> (<?php echo $_SESSION['divisi']; ?>)</a></li>]; ?>)</a></li>
            <li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> Logout</a></li>
          </ul>
        <?php } ?>
      </div>
    </div>
  </nav>

  <br /><br />

  <div class="container">

    <ul id="myTab" class="nav nav-tabs" role="tablist">
      <?php if ($_SESSION['hak_akses'] == 'HRD') : ?>
        <li role="presentation" class="active">
          <a href="#B1" id="B1-tab" role="tab" data-toggle="tab" aria-controls="B1" aria-expanded="true">Data Karyawan Aktif</a>
        </li>
        <li role="presentation">
          <a href="#B2" role="tab" id="B2-tab" data-toggle="tab" aria-controls="B2">Data Karyawan Wanprestasi</a>
        </li>
        <li role="presentation">
          <a href="#B3" role="tab" id="B3-tab" data-toggle="tab" aria-controls="B3">Data Karyawan Resign</a>
        </li>
        <li role="presentation">
          <a href="#hak-akses" role="tab" id="hak-akses-tab" data-toggle="tab" aria-controls="hak-akses">Hak Akses Button</a>
        </li>
      <?php else : ?>
        <li role="presentation" class="active">
          <a href="#rekening" role="tab" id="rekening-tab" data-toggle="tab" aria-controls="rekening">Rekening</a>
        </li>
      <?php endif; ?>
    </ul>

    <div id="myTabContent" class="tab-content">
      <!-- Tab -->
      <?php if ($_SESSION['hak_akses'] == 'HRD') : ?>
        <div role="tabpanel" class="tab-pane fade in active" id="B1" aria-labelledby="home-tab">
          <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
            <div class="panel-body no-padding">
              <br>
              <!-- <button type="button" class="btn btn-info btn-small" onclick="input_jabatan()">Input Jabatan</button> -->

              <button type="button" class="btn btn-success btn-small" onclick="tambah_user()">Tambah User</button>

              <button type="button" class="btn btn-primary btn-small" onclick="tambah_divisi()">Tambah Divisi</button>

              <button type="button" class="btn btn-danger btn-small" onclick="edit_resign()">Input Data Resign</button>
              <br><br>

              <table class="table table-striped table-bordered">
                <thead>
                  <th>#</th>
                  <th>Nama</th>
                  <th>Divisi</th>
                  <th>Jabatan</th>
                  <th>Limit</th>
                  <th>Outstanding UM</th>
                  <th>Sisa Limit</th>
                  <th>Edit</th>
                </thead>

                <tbody>
                  <?php
                  $i = 1;
                  $saldo = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE saldo IS NOT NULL AND resign iS NULL ORDER BY nama_user");
                  while ($a = mysqli_fetch_array($saldo)) {
                  ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                      <td><?php echo $a['nama_user']; ?></td>
                      <td><?php echo $a['divisi']; ?></td>

                      <td><?php echo $a['level']; ?></td>
                      <td><?php echo 'Rp. ' . number_format($a['saldo'], 0, '', ','); ?></td>
                      <td>
                        <?php
                        // $nama = $a['nama_user'];
                        // $statusreal = "Realisasi (Direksi)";
                        // $query2 = "SELECT sum(jumlah) AS sumi FROM bpu WHERE namapenerima='$nama' AND status != 'Realisasi (Direksi)' AND statusbpu='UM'";
                        // $result2 = mysqli_query($koneksi, $query2);
                        // $row2 = mysqli_fetch_array($result2);
                        // echo 'Rp. ' . number_format($row2['sumi'], 0, '', ',');
                        $namauser = $a['nama_user'];
                        $carisaldo = mysqli_query($koneksi, "SELECT sum(jumlah) AS sumjum FROM bpu WHERE namapenerima='$namauser' AND status ='Telah Di Bayar' AND statusbpu IN ('UM', 'UM Burek')");
                        $cs = mysqli_fetch_array($carisaldo);
                        echo 'Rp. ' . number_format($cs['sumjum'], 0, '', ',');
                        ?>

                      </td>
                      <td>
                        <?php
                        $pertama = $cs['sumjum'];
                        $kedua   = $a['saldo'];
                        $ketiga  = $kedua - $pertama;
                        echo 'Rp. ' . number_format($ketiga, 0, '', ',');
                        ?>
                      </td>
                      <td>
                        <button type="button" class="btn btn-success btn-small" onclick="edit_jabatan('<?php echo $a['nama_user']; ?>')">EDIT</button>
                        <!-- <button type="button" class="btn btn-success btn-small" onclick="edit_rekening('<?php echo $a['id_user']; ?>')">EDIT</button> -->
                      </td>
                    </tr>
                  <?php
                  }
                  ?>
                </tbody>

              </table>
            </div>
          </div>
        </div>

        <div role="tabpanel" class="tab-pane fade" id="B2" aria-labelledby="B2-tab">
          <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
            <div class="panel-body no-padding">
              <table class="table table-bordered">
                <thead>
                  <th>#</th>
                  <th>Nama</th>
                  <th>Divisi</th>
                  <th>Jabatan</th>
                </thead>

                <tbody>
                  <?php
                  $i = 1;
                  $saldo = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE resign='Wanprestasi' ORDER BY nama_user");
                  while ($a = mysqli_fetch_array($saldo)) { ?>

                    <tr>
                      <td><?php echo $i++; ?></td>
                      <td><?php echo $a['nama_user']; ?></td>
                      <td><?php echo $a['divisi']; ?></td>
                      <td><?php echo $a['level']; ?></td>
                    </tr>
                  <?php
                  }
                  ?>
                </tbody>

              </table>
            </div>
          </div>

        </div>

        <div role="tabpanel" class="tab-pane fade" id="B3" aria-labelledby="B3-tab">
          <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
            <div class="panel-body no-padding">
              <table class="table table-bordered">
                <thead>
                  <th>#</th>
                  <th>Nama</th>
                  <th>Divisi</th>
                  <th>Jabatan</th>
                </thead>

                <tbody>
                  <?php
                  $i = 1;
                  $saldo = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE resign='Resign' ORDER BY nama_user");
                  while ($a = mysqli_fetch_array($saldo)) { ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                      <td><?php echo $a['nama_user']; ?></td>
                      <td><?php echo $a['divisi']; ?></td>
                      <td><?php echo $a['level']; ?></td>
                    </tr>
                  <?php
                  }
                  ?>
                </tbody>

              </table>
            </div>
          </div>

        </div>

        <div role="tabpanel" class="tab-pane fade" id="hak-akses" aria-labelledby="hak-akses-tab">
          <div role="tabpanel" class="tab-pane fade in active" id="hak-akses" aria-labelledby="home-tab">
            <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
              <div class="panel-body no-padding">
                <br>

                <table class="table table-striped table-bordered">
                  <thead>
                    <th>#</th>
                    <th>Nama</th>
                    <th style="text-align: center;">Verifikasi BPU</th>
                    <th style="text-align: center;">Eksternal BPU</th>
                    <th style="text-align: center;">Ubah File BPU</th>
                    <th style="text-align: center;">Cancel Transfer</th>
                    <th style="text-align: center;">Ulang Transfer</th>
                  </thead>

                  <tbody>
                    <?php
                    $i = 1;
                    $user = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi = 'FINANCE' ORDER BY nama_user");
                    while ($a = mysqli_fetch_array($user)) {
                      $buttonAkses = unserialize($a['hak_button']);
                      if (!is_array($buttonAkses)) {
                        $buttonAkses = [];
                      }
                    ?>
                      <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo $a['nama_user']; ?></td>
                        <td style="text-align: center;"><input type="checkbox" name="" id="" data-id="<?= $a['id_user'] ?>" data-akses="verifikasi_bpu" <?= in_array("verifikasi_bpu", $buttonAkses) ? "checked" : "" ?>></td>
                        <td style="text-align: center;"><input type="checkbox" name="" id="" data-id="<?= $a['id_user'] ?>" data-akses="eksternal_bpu" <?= in_array("eksternal_bpu", $buttonAkses) ? "checked" : "" ?>></td>
                        <td style="text-align: center;"><input type="checkbox" name="" id="" data-id="<?= $a['id_user'] ?>" data-akses="ubah_file_bpu" <?= in_array("ubah_file_bpu", $buttonAkses) ? "checked" : "" ?>></td>
                        <td style="text-align: center;"><input type="checkbox" name="" id="" data-id="<?= $a['id_user'] ?>" data-akses="cancel_transfer" <?= in_array("cancel_transfer", $buttonAkses) ? "checked" : "" ?>></td>
                        <td style="text-align: center;"><input type="checkbox" name="" id="" data-id="<?= $a['id_user'] ?>" data-akses="ulang_transfer" <?= in_array("ulang_transfer", $buttonAkses) ? "checked" : "" ?>></td>
                      </tr>
                    <?php
                    }
                    ?>
                  </tbody>

                </table>
              </div>
            </div>
          </div>

        </div>
      <?php else : ?>

        <div role="tabpanel" class="tab-pane fade in active" id="rekening" aria-labelledby="home-tab">
          <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
            <div class="panel-body no-padding">
              <br>

              <button type="button" class="btn btn-success btn-small" onclick="tambah_rekening()">Tambah Rekening</button>
              <br>
              <br>

              <table class="table table-striped table-bordered">
                <thead>
                  <th>#</th>
                  <th>Nama User</th>
                  <th>Divisi</th>
                  <th>Status</th>
                  <th>Nama Pemilik Rekening</th>
                  <th>Nomor Rekening</th>
                  <th>Bank</th>
                  <th>Edit</th>
                </thead>

                <tbody>
                  <?php
                  $i = 1;
                  $saldo = mysqli_query($koneksi, "SELECT a.*, b.divisi, b.nama_user FROM rekening a LEFT JOIN tb_user b ON b.id_user = a.user_id ORDER BY nama");
                  while ($a = mysqli_fetch_array($saldo)) {
                  ?>
                    <tr>
                      <td><?php echo $i++; ?></td>
                      <td><?php echo $a['nama_user']; ?></td>
                      <td><?php echo $a['divisi']; ?></td>
                      <td><?php echo ucfirst($a['status']); ?></td>
                      <td><?php echo $a['nama']; ?></td>
                      <td><?php echo $a['rekening']; ?></td>
                      <td><?php
                          $queryBank = mysqli_query($koneksi, "SELECT * FROM bank WHERE kodebank = '$a[bank]'");
                          $bank = mysqli_fetch_assoc($queryBank);
                          echo $bank['namabank'];
                          ?></td>
                      <td>
                        <button type="button" class="btn btn-success btn-small" onclick="edit_rekening('<?php echo $a['no']; ?>')">EDIT</button>
                        <button type="button" class="btn btn-danger btn-small" onclick="hapus_rekening('<?php echo $a['no']; ?>')">HAPUS</button>
                      </td>
                    </tr>
                  <?php
                  }
                  ?>
                </tbody>

              </table>
            </div>
          </div>
        </div>
      <?php endif; ?>


    </div><!-- // Container -->

    <div class="modal fade" id="myModal" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Input Matrix Jabatan</h4>
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
            <h4 class="modal-title">Edit Matrix Jabatan</h4>
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
            <h4 class="modal-title">Edit Data Resign Karyawan</h4>
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
            <h4 class="modal-title">Tambah Divisi</h4>
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
            <h4 class="modal-title">Ubah Data Rekening</h4>
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
            <h4 class="modal-title">Hapus Data Rekening</h4>
          </div>
          <form action="edit-rekening-proses.php" method="POST">
            <input type="hidden" name="id_user">
            <input type="hidden" name="action" value="hapus">
            <div class="modal-body">
              <p>Tekan Submit untuk menghapus data.</p>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary" name="submit" value="submit">Submit</button>
              <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="modal fade" id="myModal7" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Tambah Data Rekening</h4>
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
        // var hash = location.hash.replace(/^#/, '');
        // console.log(hash);
        $('input[type=checkbox]').change(function() {
          const id = $(this).data('id');
          const akses = $(this).data('akses');
          const isChecked = $(this).is(':checked');

          $.ajax({
            url: "ajax/ajax-hak-akses.php",
            type: 'post',
            data: {
              id: id,
              akses: akses,
              isChecked: isChecked
            },
            success: function() {
              // document.location.href = window.location.href;
              var hash = location.hash.replace(/^#/, ''); // ^ means starting, meaning only match the first hash
              if (hash) {
                $('.nav-tabs a[href="#' + hash + '"]').tab('show');
              }

              // Change hash for page-reload
              $('.nav-tabs a').on('shown.bs.tab', function(e) {
                window.location.hash = e.target.hash;
              })
            }
          })
        })
      })

      function input_jabatan(no, waktu) {
        // alert(noid+' - '+waktu);
        $.ajax({
          type: 'post',
          url: 'inputjabatan.php',
          data: {},
          success: function(data) {
            $('.fetched-data').html(data); //menampilkan data ke dalam modal
            $('#myModal').modal();
          }
        });
      }

      function edit_jabatan(nama) {
        // alert(noid+' - '+waktu);
        $.ajax({
          type: 'post',
          url: 'editjabatan.php',
          data: {
            nama: nama
          },
          success: function(data) {
            $('.fetched-data').html(data); //menampilkan data ke dalam modal
            $('#myModal2').modal();
          }
        });
      }

      function hapus_rekening(id) {
        $('#myModal6 input[name=id_user]').val(id);
        $('#myModal6').modal();
      }

      function edit_rekening(id) {
        // alert(noid+' - '+waktu);
        $.ajax({
          type: 'post',
          url: 'edit-rekening.php',
          data: {
            id: id
          },
          success: function(data) {
            $('.fetched-data').html(data); //menampilkan data ke dalam modal
            $('#myModal5').modal();
          }
        });
      }

      function tambah_rekening() {
        // alert(noid+' - '+waktu);
        $.ajax({
          url: 'tambah-rekening.php',
          success: function(data) {
            $('.fetched-data').html(data); //menampilkan data ke dalam modal
            $('#myModal7').modal();
          }
        });
      }

      function edit_resign(no, waktu) {
        // alert(noid+' - '+waktu);
        $.ajax({
          type: 'post',
          url: 'editresign.php',
          data: {},
          success: function(data) {
            $('.fetched-data').html(data); //menampilkan data ke dalam modal
            $('#myModal3').modal();
          }
        });
      }

      function tambah_user(no, waktu) {
        $.ajax({
          type: 'post',
          url: 'tambahuser.php',
          data: {},
          success: function(data) {
            $('.fetched-data').html(data);
            $('#myModal4').modal();
          }
        })
      }

      function tambah_divisi() {
        $.ajax({
          type: 'post',
          url: 'tambahdivisi.php',
          data: {},
          success: function(data) {
            $('.fetched-data').html(data);
            $('#myModal4').modal();
          }
        })
      }
    </script>

</body>

</html>