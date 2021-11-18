<?php

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

?>
<div class="container">

  <form class="form-horizontal" action="selesai.php" method="POST">


    <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
      <div class="panel-body no-padding">
        <table class="table table-striped">
          <thead>
            <tr class="warning">
              <th>No</th>
              <th>Rincian & Keterangan</th>
              <th>Kota Tugas</th>
              <th>Status</th>
              <th>Penerima</th>
              <th>Harga Satuan (IDR)</th>
              <th>Quantity</th>
              <th>Total Harga (IDR)</th>
            </tr>
          </thead>

          <tbody>

            <?php
            $i = 1;
            $pengaju = $_SESSION['nama_user'];
            $divisi  = $_SESSION['divisi'];
            $sql = mysqli_query($koneksi, "SELECT * FROM tampungan WHERE pengaju='$pengaju' AND divisi='$divisi'");
            while ($d = mysqli_fetch_array($sql)) {

            ?>

              <tr>
                <th scope="row"><?php echo $i++; ?></th>
                <td><?php echo $d['rincian']; ?></td>
                <td><?php echo $d['kota']; ?></td>
                <td><?php echo $d['status']; ?></td>
                <td><?php echo $d['penerima']; ?></td>
                <td><?php echo 'Rp. ' . number_format($d['harga'], 0, '', ','); ?></td>
                <td><?php echo $d['quantity']; ?></td>
                <td><?php echo 'Rp. ' . number_format($d['total'], 0, '', ','); ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div><!-- /.table-responsive -->
    </div>




    <!-- Total harga -->
    <div class="form-group">
      <label for="focusedinput" class="control-label">Total Keseluruhan :</label>
      <?php
      $pengaju = $_SESSION['nama_user'];
      $divisi  = $_SESSION['divisi'];
      $query = "SELECT sum(total) AS sum FROM tampungan WHERE pengaju='$pengaju' AND divisi='$divisi'";
      $result = mysqli_query($koneksi, $query);
      $row = mysqli_fetch_array($result);
      ?>
      <input type="text" value="<?php echo $total = $row[0]; ?>" readonly class="form-control1" id="focusedinput1" placeholder="<?php echo 'Rp. ' . number_format($total = $row[0], 0, '', ','); ?>" name="totalbudget" onkeyup="summ();">
    </div>
    <!-- //Total harga -->


    <div class="form-group">
      <label for="email">Nama Project :</label>
      <input type="text" class="form-control" id="email" name="nama">
    </div>

    <div class="form-group">
      <label for="sel1">Jenis Budget:</label>
      <select class="form-control" id="sel1" name="jenis">
        <option selected disabled>Pilih Jenis Budget</option>
        <option value="B1">B1</option>
        <option value="B2">B2</option>
        <option value="Umum">Umum</option>
      </select>
    </div>

    <div class="form-group">
      <label for="email">Tahun :</label>
      <select class="form-control" id="email" name="tahun">
        <option disabled selected>Pilih Tahun</option>
        <?php
        for ($i = 2017; $i <= 2030; $i++) {
          echo "<option>$i</option>";
        }
        ?>
      </select>
    </div>


    <input type="hidden" class="form-control" id="pwd" name="namapengaju" value="<?php echo $_SESSION['nama_user']; ?>">

    <input type="hidden" class="form-control" id="pwd" name="divisi" value="<?php echo $_SESSION['divisi']; ?>">

    <input type="hidden" class="form-control" id="pwd" name="statusbudget" value="Belum Di Ajukan">



    <div class="form-group">
      <button type="submit" class="btn btn-primary" name="submit">Submit</button>
  </form>

  <br /><br />

  <form action="kosongkan.php" method="POST">
    <button class="btn-inverse btn" name="kosong">Reset</button>
  </form>

</div>