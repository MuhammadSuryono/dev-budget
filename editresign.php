<?php

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

?>

<!-- MEMBUAT FORM -->
<form action="editresignproses.php" method="post">

  <div class="form-group">
    <label for="status">Nama :</label>
    <select class="form-control" id="status" name="id_user">
      <option selected disabled>Pilih Nama</option>
      <?php
      $carinama = "SELECT id_user,nama_user FROM tb_user WHERE resign IS NULL ORDER BY nama_user";
      $run_carinama = $koneksi->query($carinama);
      foreach ($run_carinama as $rc) {
      ?>
        <option value="<?php echo $rc['id_user']; ?>"><?php echo $rc['nama_user']; ?></option>
      <?php
      }
      ?>
    </select>
  </div>

  <div class="form-group">
    <label for="status">Status :</label>
    <select class="form-control" id="status" name="resign">
      <option value="Wanprestasi">Wanprestasi</option>
      <option value="Resign">Resign</option>
    </select>
  </div>


  <button class="btn btn-primary" type="submit" name="submit">Submit</button>

</form>

<?php
$koneksi->close();
?>