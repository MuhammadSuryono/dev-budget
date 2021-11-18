<?php

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

?>

<!-- MEMBUAT FORM -->
<form action="inputjabatanproses.php" method="post">

  <div class="form-group">
    <label for="status">Nama :</label>
    <select class="form-control" id="status" name="id_user">
      <option selected disabled>Pilih Nama</option>
      <?php
      $carinama = "SELECT id_user,nama_user FROM tb_user WHERE saldo IS NULL AND resign IS NULL
                                                                        OR saldo IS NULL AND resign='' ORDER BY nama_user";
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
    <label for="status">Jabatan :</label>
    <select class="form-control" id="status" name="jabatan">
      <option selected disabled>Pilih Jabatan</option>
      <option value="Koordinator">Koordinator (Limit Rp.5.000.000)</option>
      <option value="Supervisor">Supervisor (Limit Rp.10.000.000)</option>
      <option value="Senior Supervisor">Senior Supervisor (Limit Rp.15.000.000)</option>
      <option value="ARA">ARA (Limit Rp.10.000.000)</option>
      <option value="RA">RA (Limit Rp.15.000.000)</option>
      <option value="Senior RA">Senior RA (Limit Rp.20.000.000)</option>
      <option value="Associate Manager">Associate Manager (Limit Rp.25.000.000)</option>
      <option value="Manager">Manager (Limit Rp.35.000.000)</option>
      <option value="Senior Manager">Senior Manager (Limit Rp.50.000.000)</option>
      <option value="Associate Director">Associate Director (Limit Rp.60.000.000)</option>
    </select>
  </div>


  <button class="btn btn-primary" type="submit" name="submit">Submit</button>

</form>

<?php
$koneksi->close();
?>