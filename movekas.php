<?php
error_reporting(0);

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

if ($_POST['waktu'] && $_POST['no'] && $_POST['term'] && $_POST['namauser']) {
  $waktu    = $_POST['waktu'];
  $no       = $_POST['no'];
  $term     = $_POST['term'];
  $namauser = $_POST['namauser'];
  // mengambil data berdasarkan id
  // dan menampilkan data ke dalam form modal bootstrap
  $sql = "SELECT * FROM bpu WHERE waktu='$waktu' AND no='$no' AND term='$term' AND nama_user='$namauser'";
  $result = $koneksi->query($sql);
  foreach ($result as $baris) { ?>

    <p>Apakah anda ingin memindahkan bpu ke pengajuan kas ?</p>

    <form action="movekas-proses.php" method="post">
      <input type="hidden" name="waktu" value="<?php echo $baris['waktu']; ?>">
      <input type="hidden" name="no" value="<?php echo $baris['no']; ?>">
      <input type="hidden" name="term" value="<?php echo $baris['term']; ?>">
      <input type="hidden" name="nama_user" value="<?php echo $namauser ?>">
      <button class="btn btn-primary" type="submit" name="submit">Move >></button>
    </form>

<?php }
}
$koneksi->close();
?>