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

  $sql1 = "SELECT MAX(noid) FROM upload WHERE waktu ='$waktu'";
  $result1 = $koneksi->query($sql1);
  foreach ($result1 as $baris1) {

    $max = $baris1['MAX(noid)'];

    $sql = "SELECT * FROM upload WHERE waktu ='$waktu' AND noid='$max'";
    $result = $koneksi->query($sql);
    foreach ($result as $baris) {

?>

      <!-- MEMBUAT FORM -->
      <form action="bayarmemoproses.php" method="post">

        <input type="hidden" name="no" value="<?php echo $baris['no']; ?>">
        <input type="hidden" name="waktu" value="<?php echo $baris['waktu']; ?>">
        <input type="hidden" name="pembayar" value="<?php echo $_SESSION['nama_user']; ?>">
        <input type="hidden" name="divisi" value="<?php echo $_SESSION['divisi']; ?>">

        <p>Apakah kamu ingin mengubah status memo ini menjadi <b>"Sudah Dibayar"</b> ?</p>

        <button class="btn btn-primary" type="submit" name="submit">IYA</button>
      </form>

<?php }
  }
}
$koneksi->close();
?>