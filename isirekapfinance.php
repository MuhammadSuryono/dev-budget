<?php

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

if (isset($_POST['submit'])) {

  $daritgl    = $_POST['daritgl'];
  $sampaitgl  = $_POST['sampaitgl'];
?>

  <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
    <!-- PROJECT -->
    <div class="panel-body no-padding">
      <h3>
        <center>Project<center>
      </h3>
      <h4>Periode : <?php echo $daritgl . " <b>s/d</b>  " . $sampaitgl; ?></h4>
      <table class="table table-striped table-bordered">
        <thead>
          <tr class="warning">
            <th>#</th>
            <th>Jenis</th>
            <th>Project</th>
            <th>Item</th>
            <th>Kategori</th>
            <th>Request BPU</th>
            <th>Tanggal</th>
            <th>Penerima</th>
            <th>Pengaju(Divisi)</th>
            <th>Action</th>
          </tr>
        </thead>

        <tbody>

          <?php
          $i = 1;
          $sql = mysqli_query($koneksi, "SELECT * FROM bpu WHERE status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND tglcair BETWEEN '$daritgl' AND '$sampaitgl' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1')
                                                    OR status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND tglcair BETWEEN '$daritgl' AND '$sampaitgl' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1')
                                                    OR status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND tglcair BETWEEN '$daritgl' AND '$sampaitgl' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B2')
                                                    OR status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND tglcair BETWEEN '$daritgl' AND '$sampaitgl' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B2')
                                                    ORDER BY tglcair");
          while ($d = mysqli_fetch_array($sql)) {
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
                <!-- Nama Project -->
                <?php
                $namarincian = mysqli_query($koneksi, "SELECT no,rincian FROM selesai WHERE waktu='$d[waktu]' AND no='$d[no]'");
                $namrin = mysqli_fetch_assoc($namarincian);
                echo "<b>" . $namrin['no'] . "</b>." . $namrin['rincian'];
                ?>
              </td>

              <td><?php echo 'Rp. ' . number_format($d['jumlah'], 0, '', ','); ?></td>
              <td><?php echo $d['tglcair']; ?></td>
              <td><?php echo $d['namapenerima']; ?></td>
              <td><?php echo $d['pengaju'] . "(" . $d['divisi'] . ")"; ?>
              <td><?php echo $d['statusbpu']; ?></td>
              <?php
              if ($d['statusbpu'] == 'UM' || $d['statusbpu'] == 'Finance' || $d['statusbpu'] == 'Pulsa' || $d['statusbpu'] == 'Biaya' || $d['statusbpu'] == 'Biaya Lumpsum') {
              ?>
                <td><button type="button" class="btn btn-default btn-small" onclick="edit_budget('<?php echo $d['no']; ?>','<?php echo $d['waktu']; ?>')">Bayar</button></td>
              <?php
              } else {
              ?>
                <td><button type="button" class="btn btn-default btn-small" onclick="eksternal_finance('<?php echo $d['no']; ?>','<?php echo $d['waktu']; ?>')">Eksternal</button></td>
              <?php
              }
              ?>
            </tr>
          <?php } ?>
        </tbody>
      </table>

      <?php
      $wewew = mysqli_query($koneksi, "SELECT sum(jumlah) AS sumia FROM bpu WHERE status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND tglcair BETWEEN '$daritgl' AND '$sampaitgl' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1')
                                                OR status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND tglcair BETWEEN '$daritgl' AND '$sampaitgl' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1')
                                                OR status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND tglcair BETWEEN '$daritgl' AND '$sampaitgl' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B2')
                                                OR status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND tglcair BETWEEN '$daritgl' AND '$sampaitgl' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B2')");
      $t = mysqli_fetch_array($wewew);
      ?>
      <h4>Total Pengajuan KAS : <?php echo 'Rp. ' . number_format($t['sumia'], 0, '', ','); ?></h4>

      <form>
        <div class="form-group row">
          <div class="col-xs-3">
            <label for="ex1">Total Pengajan Kas :</label>
            <input class="form-control" id="ex1" type="text" name="pengajuankas" value="<?php echo $t['sumia']; ?>" placeholder="<?php echo 'Rp. ' . number_format($t['sumia'], 0, '', ','); ?>" readonly>
          </div>
          <div class="col-xs-3">
            <label for="ex2">Total Kas Disetujui :</label>
            <input class="form-control" id="ex2" type="text" name="setujuaju">
          </div>
        </div>
      </form>

    </div><!-- /.table-responsive -->

    <!-- NON PROJECT -->
    <div class="panel-body no-padding">
      <h3>
        <center>NON Project<center>
      </h3>
      <h4>Periode : <?php echo $daritgl . " <b>s/d</b>  " . $sampaitgl; ?></h4>
      <table class="table table-striped table-bordered">
        <thead>
          <tr class="warning">
            <th>#</th>
            <th>Jenis</th>
            <th>Project</th>
            <th>Item</th>
            <th>Request BPU</th>
            <th>Tanggal</th>
            <th>Penerima</th>
            <th>Pengaju(Divisi)</th>
            <th>Action</th>
          </tr>
        </thead>

        <tbody>

          <?php
          
          $i = 1;
          $sql = mysqli_query($koneksi, "SELECT * FROM bpu WHERE status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND tglcair BETWEEN '$daritgl' AND '$sampaitgl' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Rutin')
                                                    OR status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND tglcair BETWEEN '$daritgl' AND '$sampaitgl' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Rutin')
                                                    OR status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND tglcair BETWEEN '$daritgl' AND '$sampaitgl' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Non Rutin')
                                                    OR status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND tglcair BETWEEN '$daritgl' AND '$sampaitgl' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Non Rutin')
                                                    ORDER BY tglcair");
          while ($d = mysqli_fetch_array($sql)) {
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
                <!-- Nama Project -->
                <?php
                $namarincian = mysqli_query($koneksi, "SELECT no,rincian FROM selesai WHERE waktu='$d[waktu]' AND no='$d[no]'");
                $namrin = mysqli_fetch_assoc($namarincian);
                echo "<b>" . $namrin['no'] . "</b>." . $namrin['rincian'];
                ?>
              </td>

              <td><?php echo 'Rp. ' . number_format($d['jumlah'], 0, '', ','); ?></td>
              <td><?php echo $d['tglcair']; ?></td>
              <td><?php echo $d['namapenerima']; ?></td>
              <td><?php echo $d['pengaju'] . "(" . $d['divisi'] . ")"; ?>
                <?php
                if ($d['statusbpu'] == 'UM' || $d['statusbpu'] == 'Finance' || $d['statusbpu'] == 'Pulsa' || $d['statusbpu'] == 'Biaya' || $d['statusbpu'] == 'Biaya Lumpsum') {
                ?>
              <td><button type="button" class="btn btn-default btn-small" onclick="edit_budget('<?php echo $d['no']; ?>','<?php echo $d['waktu']; ?>')">Bayar</button></td>
            <?php
                } else {
            ?>
              <td><button type="button" class="btn btn-default btn-small" onclick="eksternal_finance('<?php echo $d['no']; ?>','<?php echo $d['waktu']; ?>')">Eksternal</button></td>
            <?php
                }
            ?>
            </tr>
          <?php } ?>
        </tbody>
      </table>

      <?php
      $wewew = mysqli_query($koneksi, "SELECT sum(jumlah) AS sumia FROM bpu WHERE status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND tglcair BETWEEN '$daritgl' AND '$sampaitgl' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Rutin')
                                                OR status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND tglcair BETWEEN '$daritgl' AND '$sampaitgl' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Rutin')
                                                OR status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND tglcair BETWEEN '$daritgl' AND '$sampaitgl' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Non Rutin')
                                                OR status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND tglcair BETWEEN '$daritgl' AND '$sampaitgl' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Non Rutin')");
      $t = mysqli_fetch_array($wewew);
      ?>
      <h4>Total Pengajuan KAS : <?php echo 'Rp. ' . number_format($t['sumia'], 0, '', ','); ?></h4>
    </div><!-- /.table-responsive -->
  </div>

  </div>
  </div>

<?php } ?>

<div class="modal fade" id="myModal" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Bayar Budget</h4>
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
        <h4 class="modal-title">Pembayaran BPU Eksternal</h4>
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