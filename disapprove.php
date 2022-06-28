<?php
error_reporting(0);
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

if ($_POST['rowid']) {
  $id = $_POST['rowid'];
  // mengambil data berdasarkan id
  // dan menampilkan data ke dalam form modal bootstrap
  $sql = "SELECT * FROM pengajuan WHERE noid = $id";
  $result = $koneksi->query($sql);
  foreach ($result as $baris) { ?>

    <p>Apakah anda ingin membuat status budget menjadi <b>Disapprove</b>?</p>

    <form action="disapprove_proses.php" method="post">

      <p style="margin-top: 2px;">Masukkan keterangan tambahan (jika ada)</p>
      <div class="form-group" style="margin-top: 5px;">
        <input type="text" id="keteranganTambahan" class="form-control" name="alasanTolak" placeholder="Keterangan" autocomplete="off">
      </div>
      <input type="hidden" name="noid" value="<?php echo $baris['noid']; ?>">
      <input type="hidden" name="status" value="Disapprove">
      <button class="btn btn-primary" type="submit" name="submit">Disapprove</button>
    </form>

<?php }
}
$koneksi->close();
?>