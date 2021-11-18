<?php
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

if ($_POST['no'] && $_POST['waktu']) {
  $id = $_POST['no'];
  $waktu = $_POST['waktu'];



  // mengambil data berdasarkan id
  // dan menampilkan data ke dalam form modal bootstrap
  $sql = "SELECT * FROM selesai WHERE no = '$id' AND waktu = '$waktu'";
  $result = $koneksi->query($sql);
  foreach ($result as $baris) {
?>
    <!-- MEMBUAT FORM -->
    <form action="hapusrowproses.php" method="post">
      <input type="hidden" name="no" value="<?php echo $baris['no']; ?>">
      <input type="hidden" name="waktu" value="<?php echo $baris['waktu']; ?>">
      <p>Apakah anda yakin ingin menghapus <b><?= $baris['rincian']; ?></b>?</p>
      <button class="btn btn-primary" type="submit" name="submit">Hapus</button>
    </form>
    <?php break; ?>
<?php }
}
$koneksi->close();
?>