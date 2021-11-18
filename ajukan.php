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
  foreach ($result as $baris) {
?>

    <!-- MEMBUAT FORM -->

    <form action="ajukanproses.php" method="POST">

      <p>Apakah kamu ingin mengajukan budget ini?</p>

      <input type="hidden" name="status" value="<?php


                                                if ($baris['status'] == 'Belum Di Ajukan') {
                                                  $statusbudget = "Pending";
                                                } else {
                                                  $statusbudget = "Pending(Penambahan)";
                                                }
                                                echo $statusbudget

                                                ?>">
      <input type="hidden" name="noid" value="<?php echo $baris['noid']; ?>">

      <button class="btn btn-primary" type="submit" name="submit">Ajukan</button>

    </form>

<?php }
}
$koneksi->close();
?>