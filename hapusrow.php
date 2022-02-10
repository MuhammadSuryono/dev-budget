<?php
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

$con->set_name_db(DB_TRANSFER);
$con->init_connection();
$konTf = $con->connect();

if ($_POST['no'] && $_POST['waktu']) {
  $id = $_POST['no'];
  $waktu = $_POST['waktu'];

  $queryBpu = "SELECT * FROM bpu a WHERE a.no = '$id' AND a.waktu = '$waktu'";
  $res = $koneksi->query($queryBpu);

  $alreadyDataTransfer = false;
  foreach ($res as $item) {
    $quueryTf = "SELECT * FROM data_transfer a WHERE a.noid_bpu = '$id'";
    $resTf = $konTf->query($quueryTf);
    if (count($resTf) > 0) {
      $alreadyDataTransfer = true;
      break;
    }
  }
  $otherWarning = "";
  if ($alreadyDataTransfer) {
    $otherWarning = "Terdeteksi beberapa data BPU dalam proses antrian Transfer untuk pembayaran MRI PALL, jika anda menghapus item ini maka akan menyebabkan transaksi yang dalam antrian dan yang akan diproses ikut terhapus.";
  }
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
      <p>Apakah anda yakin ingin menghapus <b><?= $baris['rincian']; ?></b>? <?= $otherWarning ?></p>
      <button class="btn btn-primary" type="submit" name="submit">Hapus</button>
    </form>
    <?php break; ?>
<?php }
}
$koneksi->close();
?>