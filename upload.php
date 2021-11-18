<?php
error_reporting(0);
session_start();

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

if (!isset($_SESSION['nama_user'])) {
  header("location:login.php");
  // die('location:login.php');//jika belum login jangan lanjut
}


if ($_POST['waktu']) {
  $waktu = $_POST['waktu'];


  // mengambil data berdasarkan id
  // dan menampilkan data ke dalam form modal bootstrap
  $sql = "SELECT * FROM pengajuan WHERE waktu ='$waktu'";
  $result = $koneksi->query($sql);
  foreach ($result as $baris) {

?>

    <!-- MEMBUAT FORM -->
    <form action="uploadproses.php" method="post" enctype="multipart/form-data">

      <input type="hidden" name="waktu" value="<?php echo $baris['waktu']; ?>">
      <input type="hidden" name="pengaju" value="<?php echo $_SESSION['nama_user']; ?>">
      <input type="hidden" name="divisi" value="<?php echo $_SESSION['divisi']; ?>">

      <div class="form-group">
        <label for="jumlah">Pilih File :</label>
        <input type="file" class="form-control" id="jumlah" name="gambar">
      </div>

      <button class="btn btn-primary" type="submit" name="submit">SUBMIT</button>
    </form>
    <?php break; ?>

<?php }
}
$koneksi->close();
?>