<?php

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

?>

<form action="tambahuserproses.php" class="form-horizontal" method="POST" enctype="multipart/form-data">

  <div class="form-group">
    <label class="col-sm-3 control-label">Nama User <font color="red">*</font></label>
    <div class="col-sm-7">
      <input type="text" name="nama_user" class="form-control" placeholder="Nama" maxlength="64" required>
    </div>
  </div>

  <div class="form-group">
    <?php
    $getDivisi = mysqli_query($koneksi, "SELECT * FROM divisi ORDER BY nama_divisi");
    // var_dump($getDivisi);
    ?>
    <label class="col-sm-3 control-label">Divisi <font color="red">*</font></label>
    <div class="col-sm-7">
      <select name="divisi" class="form-control">
        <option value="">Pilih</option>
        <?php foreach ($getDivisi as $divisi) : ?>
          <option value="<?= $divisi['nama_divisi']; ?>"><?= $divisi['nama_divisi']; ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <div class="form-group">
    <?php
    $getJabatan = mysqli_query($koneksi, 'SELECT * FROM jabatan ORDER BY nama_jabatan');
    ?>
    <label class="col-sm-3 control-label">Jabatan </label>
    <div class="col-sm-7">
      <select class="form-control" name="jabatan">
        <option value="">Pilih</option>
        <?php foreach ($getJabatan as $jabatan) : ?>
          <option value="<?= $jabatan['nama_jabatan'] ?>"><?= $jabatan['nama_jabatan']; ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label">Hak Akses <font color="red">*</font></label>
    <div class="col-sm-7">
      <select name="hak_akses" class="form-control">
        <option value="">Pilih</option>
        <option value="Level 1">Level 1</option>
        <option value="Level 2">Level 2</option>
        <option value="Level 3">Level 3</option>
        <option value="Level 4">Level 4</option>
        <option value="Level 5">Level 5</option>
      </select>
    </div>
  </div>

  <div class="form-group">
    <div class="col-sm-offset-3 col-sm-7">
      <button type="submit" name="save" value="save" class="btn btn-danger">Save</button>
    </div>
  </div>


</form>