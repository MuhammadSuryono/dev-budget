<?php

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

?>

<!-- Pembuka Table Project -->
<h3>
  <center>Project</center>
</h3>
<br>
<table class="table table-striped table-bordered">
  <thead>
    <tr class="warning">
      <th>#</th>
      <th>Jenis</th>
      <th>Project</th>
      <th>Item</th>
      <th>Kategori</th>
      <th>Request BPU</th>
      <th>Tanggal Pembayaran</th>
      <th>Penerima</th>
      <th>Pengaju(Divisi)</th>
      <th>Move</th>
    </tr>
  </thead>

  <tbody>

    <?php
    date_default_timezone_set("Asia/Bangkok");
    $i = 1;
    $sql = mysqli_query($koneksi, "SELECT * FROM bpu WHERE metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1') AND statusrtp='Moved'
                                                OR metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1') AND statusrtp='Moved'
                                                OR metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B2') AND statusrtp='Moved'
                                                OR metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B2') AND statusrtp='Moved'
                                                ORDER BY tanggalbayar");
    while ($d = mysqli_fetch_array($sql)) {
      $waktu = $d['waktu'];
      $no    = $d['no'];
      $term  = $d['term'];
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
          <!-- Item -->
          <?php
          $namarincian = mysqli_query($koneksi, "SELECT no,rincian FROM selesai WHERE waktu='$d[waktu]' AND no='$d[no]'");
          $namrin = mysqli_fetch_assoc($namarincian);
          echo "<b>" . $namrin['no'] . "</b>." . $namrin['rincian'];
          ?>
        </td>
        <td><?php echo $d['statusbpu']; ?></td>
        <td><?php echo 'Rp. ' . number_format($d['jumlah'], 0, '', ','); ?></td>
        <td>
          <?php
          if ($d['tanggalbayar'] == '0000-00-00') {
            echo $d['tglcair'];
          } else {
            echo $d['tanggalbayar'];
          }
          ?>
        </td>
        <td><?php echo $d['namapenerima']; ?></td>
        <td><?php echo $d['pengaju'] . "(" . $d['divisi'] . ")"; ?>
        <td><button type="button" class="btn btn-success btn-md" onclick="pengajuan('<?php echo $waktu; ?>','<?php echo $no; ?>','<?php echo $term; ?>')"><i class="fas fa-angle-double-right"></i> Move</button></td>
      </tr>
    <?php } ?>
  </tbody>
</table>

<?php
$wewew = mysqli_query($koneksi, "SELECT sum(jumlah) AS sumia FROM bpu WHERE metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1') AND statusrtp='Moved'
                                            OR metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1') AND statusrtp='Moved'
                                            OR metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B2') AND statusrtp='Moved'
                                            OR metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B2') AND statusrtp='Moved'");
$t = mysqli_fetch_array($wewew);

$totalhonor = mysqli_query($koneksi, "SELECT sum(jumlah) AS sumtot FROM bpu WHERE metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND statusbpu ='Honor Eksternal' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1') AND statusrtp='Moved'
                                                                      OR metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND statusbpu ='Honor Eksternal' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1') AND statusrtp='Moved'");
$h = mysqli_fetch_array($totalhonor);
?>
<h5>
  <div class="row">
    <div class="col-xs-3">Total Honor SHP dan PWT</div>
    <div class="col-xs-3">: <b><?php echo 'Rp. ' . number_format($h['sumtot'], 0, '', ','); ?></b></div>
  </div>


  <div class="row">
    <div class="col-xs-3">Total Pengajuan KAS</div>
    <div class="col-xs-3">: <b><?php echo 'Rp. ' . number_format($t['sumia'], 0, '', ','); ?></b></div>
  </div>
</h5>

<br />

<button type="button" class="btn btn-default btn-md" onclick="clear('<?php echo $waktu; ?>','<?php echo $no; ?>','<?php echo $term; ?>')"><i class="fas fa-trash-alt"></i> CLEAR</button>
<!-- //Penutup Table Project -->

</br></br>

<!-- Pembuka Table Non Project -->
<h3>
  <center>Non Project</center>
</h3>
<br>
<table class="table table-striped table-bordered">
  <thead>
    <tr class="warning">
      <th>#</th>
      <th>Jenis</th>
      <th>Project</th>
      <th>Item</th>
      <th>Kategori</th>
      <th>Request BPU</th>
      <th>Tanggal Pembayaran</th>
      <th>Penerima</th>
      <th>Pengaju(Divisi)</th>
      <th>Move</th>
    </tr>
  </thead>

  <tbody>

    <?php
    date_default_timezone_set("Asia/Bangkok");
    
    $i = 1;
    $sql = mysqli_query($koneksi, "SELECT * FROM bpu WHERE metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Non Rutin') AND statusrtp='Moved'
                                                  OR metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Non Rutin') AND statusrtp='Moved'
                                                  ORDER BY tanggalbayar");
    while ($d = mysqli_fetch_array($sql)) {
      $waktu = $d['waktu'];
      $no    = $d['no'];
      $term  = $d['term'];
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
          <!-- Item -->
          <?php
          $namarincian = mysqli_query($koneksi, "SELECT no,rincian FROM selesai WHERE waktu='$d[waktu]' AND no='$d[no]'");
          $namrin = mysqli_fetch_assoc($namarincian);
          echo "<b>" . $namrin['no'] . "</b>." . $namrin['rincian'];
          ?>
        </td>
        <td><?php echo $d['statusbpu']; ?></td>
        <td><?php echo 'Rp. ' . number_format($d['jumlah'], 0, '', ','); ?></td>
        <td>
          <?php
          if ($d['tanggalbayar'] == '0000-00-00') {
            echo $d['tglcair'];
          } else {
            echo $d['tanggalbayar'];
          }
          ?>
        </td>
        <td><?php echo $d['namapenerima']; ?></td>
        <td><?php echo $d['pengaju'] . "(" . $d['divisi'] . ")"; ?>
        <td><button type="button" class="btn btn-success btn-md" onclick="pengajuan('<?php echo $waktu; ?>','<?php echo $no; ?>','<?php echo $term; ?>')"><i class="fas fa-angle-double-right"></i> Move</button></td>
      </tr>
    <?php } ?>
  </tbody>
</table>

<?php
$wewew = mysqli_query($koneksi, "SELECT sum(jumlah) AS sumia FROM bpu WHERE metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Non Rutin') AND statusrtp='Moved'
                                              OR metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Non Rutin') AND statusrtp='Moved'");
$t = mysqli_fetch_array($wewew);

$totalhonor = mysqli_query($koneksi, "SELECT sum(jumlah) AS sumtot FROM bpu WHERE metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND statusbpu ='Non Rutin' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1') AND statusrtp='Moved'
                                                                        OR metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND statusbpu ='Non Rutin' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1') AND statusrtp='Moved'");
$h = mysqli_fetch_array($totalhonor);
?>
<h5>
  <div class="row">
    <div class="col-xs-3">Total Pengajuan KAS</div>
    <div class="col-xs-3">: <b><?php echo 'Rp. ' . number_format($t['sumia'], 0, '', ','); ?></b></div>
  </div>
</h5>
<!-- //Penutup Table Non Project -->

</br></br>

<!-- Pembuka Table Rutin -->
<h3>
  <center>Rutin</center>
</h3>
<br>
<table class="table table-striped table-bordered">
  <thead>
    <tr class="warning">
      <th>#</th>
      <th>Jenis</th>
      <th>Project</th>
      <th>Item</th>
      <th>Kategori</th>
      <th>Request BPU</th>
      <th>Tanggal Pembayaran</th>
      <th>Penerima</th>
      <th>Pengaju(Divisi)</th>
      <th>Move</th>
    </tr>
  </thead>

  <tbody>

    <?php
    date_default_timezone_set("Asia/Bangkok");
    
    $i = 1;
    $sql = mysqli_query($koneksi, "SELECT * FROM bpu WHERE metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Rutin') AND statusrtp='Moved'
                                                    OR metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Rutin') AND statusrtp='Moved'
                                                    ORDER BY tanggalbayar");
    while ($d = mysqli_fetch_array($sql)) {
      $waktu = $d['waktu'];
      $no    = $d['no'];
      $term  = $d['term'];
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
          <!-- Item -->
          <?php
          $namarincian = mysqli_query($koneksi, "SELECT no,rincian FROM selesai WHERE waktu='$d[waktu]' AND no='$d[no]'");
          $namrin = mysqli_fetch_assoc($namarincian);
          echo "<b>" . $namrin['no'] . "</b>." . $namrin['rincian'];
          ?>
        </td>
        <td><?php echo $d['statusbpu']; ?></td>
        <td><?php echo 'Rp. ' . number_format($d['jumlah'], 0, '', ','); ?></td>
        <td>
          <?php
          if ($d['tanggalbayar'] == '0000-00-00') {
            echo $d['tglcair'];
          } else {
            echo $d['tanggalbayar'];
          }
          ?>
        </td>
        <td><?php echo $d['namapenerima']; ?></td>
        <td><?php echo $d['pengaju'] . "(" . $d['divisi'] . ")"; ?>
        <td><button type="button" class="btn btn-success btn-md" onclick="pengajuan('<?php echo $waktu; ?>','<?php echo $no; ?>','<?php echo $term; ?>')"><i class="fas fa-angle-double-right"></i> Move</button></td>
      </tr>
    <?php } ?>
  </tbody>
</table>

<?php
$wewew = mysqli_query($koneksi, "SELECT sum(jumlah) AS sumia FROM bpu WHERE metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Rutin') AND statusrtp='Moved'
                                                OR metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Rutin') AND statusrtp='Moved'");
$t = mysqli_fetch_array($wewew);

$totalhonor = mysqli_query($koneksi, "SELECT sum(jumlah) AS sumtot FROM bpu WHERE metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND statusbpu ='Honor Eksternal' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1') AND statusrtp='Moved'
                                                                          OR metode_pembayaran = 'MRI Kas' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND statusbpu ='Honor Eksternal' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1') AND statusrtp='Moved'");
$h = mysqli_fetch_array($totalhonor);
?>
<h5>
  <div class="row">
    <div class="col-xs-3">Total Pengajuan KAS</div>
    <div class="col-xs-3">: <b><?php echo 'Rp. ' . number_format($t['sumia'], 0, '', ','); ?></b></div>
  </div>
</h5>
<!-- //Penutup Table Rutin -->