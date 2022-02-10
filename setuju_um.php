<?php
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

session_start();
if (!isset($_SESSION['nama_user'])) {
  header("location:login.php");
  // die('location:login.php');//jika belum login jangan lanjut
}

if ($_POST['term'] && $_POST['namapenerima']) {
  $term         = $_POST['term'];
  $namapenerima = $_POST['namapenerima'];



  // mengambil data berdasarkan id
  // dan menampilkan data ke dalam form modal bootstrap
  $sql = "SELECT * FROM bpu WHERE namapenerima = '$namapenerima' AND term = '$term' AND waktu='0000-00-00 00:00:00' AND no='0'";
  $result = $koneksi->query($sql);
  foreach ($result as $baris) {
?>

    <form action="setuju_um_proses.php" method="POST">

      <p>Apakah kamu ingin menyetujui BPU UM atas nama <b><?php echo $namapenerima; ?></b> pada <b>Term <?php echo $term; ?></b></p>

      <input type="hidden" name="term" value="<?php echo $term; ?>">
      <input type="hidden" name="namapenerima" value="<?php echo $namapenerima; ?>">
      <input type="hidden" name="waktu" value="0000-00-00 00:00:00">
      <input type="hidden" name="no" value="0">

      <div class="form-group">
        <label for="tglcair" class="control-label">Tanggal Pembayaran :</label>
        <input type="date" class="form-control" id="tglbayar" name="tanggalbayar">
      </div>

      <button class="btn btn-primary" type="submit" name="submit">Setujui</button>

    </form>


<?php
  }
}
$koneksi->close();
?>