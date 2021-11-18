<?php

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

error_reporting(0);
session_start();
if (!isset($_SESSION['nama_user'])) {
  header("location:login.php");
  // die('location:login.php');//jika belum login jangan lanjut
}


if ($_POST['no'] && $_POST['waktu']) {
  $id = $_POST['no'];
  $waktu = $_POST['waktu'];

?>

  <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th>No</th>
        <th>Rincian</th>
        <th>Status</th>
        <th>Harga (IDR)</th>
        <th>Quantity</th>
        <th>Total (IDR)</th>
      </tr>
    </thead>

    <tbody>
      <?php
      $sql = "SELECT * FROM selesai WHERE waktu='$waktu' AND no='$id'";
      $result = $koneksi->query($sql);
      foreach ($result as $baris) {
      ?>
        <tr>
          <td><?php echo $baris['no']; ?></td>
          <td><?php echo $baris['rincian']; ?></td>
          <td><?php echo $baris['status']; ?></td>
          <td><?php echo 'Rp. ' . number_format($baris['harga'], 0, '', ','); ?></td>
          <td><?php echo $baris['quantity']; ?></td>
          <td><?php echo 'Rp. ' . number_format($baris['total'], 0, '', ','); ?></td>
        </tr>
      <?php
      }
      ?>
    </tbody>
  </table>

  <!-- MEMBUAT FORM -->
  <form action="movebudget_proses.php" method="post" name="Form" onsubmit="return validateForm()" enctype="multipart/form-data">

    <input type="hidden" name="waktu" value="<?php echo $baris['waktu']; ?>">
    <input type="hidden" name="nomorawal" value="<?php echo $baris['no']; ?>">
    <input type="hidden" name="harga" value="<?php echo $_SESSION['harga']; ?>">
    <input type="hidden" name="quantity" value="<?php echo $baris['quantity']; ?>">
    <input type="hidden" name="total" value="<?php echo $baris['total']; ?>">

    <div class="form-group">
      <label for="harga" class="control-label">Harga (IDR) :</label>
      <input class="form-control" name="hargaakhir" type="number">
    </div>

    <div class="form-group">
      <label for="harga" class="control-label">Quantity :</label>
      <input class="form-control" name="quantityakhir" type="number">
    </div>

    <div class="form-group">
      <label for="harga" class="control-label">Total (IDR) :</label>
      <input class="form-control" name="totalakhir" type="number">
    </div>

    <div class="form-group">
      <label for="sel1">Pindah Ke Nomor Item :</label>
      <select class="form-control" id="sel1" name="nomorakhir">
        <?php
        $carinomor = "SELECT no FROM selesai WHERE waktu='$waktu' AND no !='$id'";
        $run_carinomor = $koneksi->query($carinomor);
        while ($rc = mysqli_fetch_array($run_carinomor)) {
          $notujuan = $rc['no'];
          echo "<option value='$notujuan'>$notujuan</option>";
        }
        ?>
      </select>
    </div>


  <?php }
$koneksi->close();
  ?>