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

if ($_POST['no'] && $_POST['waktu']) {
  $id = $_POST['no'];
  $waktu = $_POST['waktu'];


  // mengambil data berdasarkan id
  // dan menampilkan data ke dalam form modal bootstrap
  $sql = "SELECT * FROM bpu WHERE waktu ='$waktu' AND status='Belum Di Bayar'";
  $result = $koneksi->query($sql);
  foreach ($result as $baris) {

?>

    <!-- MEMBUAT FORM -->
    <ul class="list-group">
      <li class="list-group-item">Bank : <?php echo $baris['namabank']; ?></li>
      <li class="list-group-item">NO Rekening : <?php echo $baris['norek']; ?></li>
      <li class="list-group-item">Nama Penerima : <?php echo $baris['namapenerima']; ?></li>
    </ul>

<?php }
}
$koneksi->close();
?>