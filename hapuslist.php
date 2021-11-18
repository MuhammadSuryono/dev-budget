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

    <p>Apakah anda yakin ingin menghapus budget ini?</p>

    <form action="hapuslistproses.php" method="post">
      <input type="hidden" name="noid" value="<?php echo $baris['noid']; ?>">
      <button class="btn btn-primary" type="submit" name="submit">HAPUS</button>
    </form>

<?php }
}
$koneksi->close();
?>