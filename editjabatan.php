<?php

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

if ($_POST['nama']) {

  $nama = $_POST['nama'];

  $carinama = "SELECT * FROM tb_user WHERE nama_user='$nama'";
  $result = $koneksi->query($carinama);
  foreach ($result as $baris) {
?>

    <!-- MEMBUAT FORM -->
    <form action="editjabatanproses.php" method="post">

      <label for="status">Username :</label>
      <div class="form-group">
        <input type="text" name="nama_user" class="form-control" value="<?php echo $baris['nama_user']; ?>" readonly>
      </div>

      <div class="form-group">
        <?php $arrJabatan = [
          'Koordinator', 'Superviser', 'Senior Supervisor', 'RA', 'Associate Manager', 'Manager', 'Senior Manager',
          'Associate Director'
        ];
        ?>
        <label for="status">Jabatan :</label>
        <select class="form-control" id="status" name="jabatan">
          <option selected value="<?= $baris['level']; ?>"><?php echo $baris['level']; ?></option>
          <?php

          ?>
          <option value="Koordinator">Koordinator</option>
          <option value="Supervisor">Supervisor</option>
          <option value="Senior Supervisor">Senior Supervisor</option>
          <option value="RA">RA</option>
          <option value="Senior RA">Senior RA</option>
          <option value="Associate Manager">Associate Manager</option>
          <option value="Manager">Manager</option>
          <option value="Senior Manager">Senior Manager</option>
          <option value="Associate Director">Associate Director</option>
        </select>
      </div>

      <label for="limit">Limit :</label>
      <div class="form-group">
        <input type="number" name="limit" class="form-control" value="<?php echo $baris['saldo']; ?>" min="0">
      </div>

      <button class="btn btn-primary" type="submit" name="submit">Update</button>

    </form>

<?php }
}
$koneksi->close();
?>