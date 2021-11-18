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

    <p>Apakah anda ingin menyetujui budget ini?</p>

    <form action="approveproses.php" method="post">
      <input type="hidden" name="noid" value="<?php echo $baris['noid']; ?>">
      <input type="hidden" name="totalbudget" value="<?php echo $baris['totalbudget']; ?>">
      <button class="btn btn-primary" type="submit" name="submit">Setujui</button>
    </form>

<?php }
}
$koneksi->close();
?>