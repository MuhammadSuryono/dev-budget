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

  $sql1 = "SELECT MIN(term) FROM bpu WHERE no ='$id' AND waktu ='$waktu' AND status ='Belum Di Bayar'";
  $result1 = $koneksi->query($sql1);
  foreach ($result1 as $baris1) {
    $minterm = $baris1['MIN(term)'];

    // mengambil data berdasarkan id
    // dan menampilkan data ke dalam form modal bootstrap
    $sql = "SELECT * FROM bpu WHERE no ='$id' AND waktu ='$waktu' AND status='Belum Di Bayar' AND term ='$minterm'";
    $result = $koneksi->query($sql);
    foreach ($result as $baris) {

?>

      <!-- MEMBUAT FORM -->
      <form action="financeeksternalproses.php" method="post">

        <input type="hidden" name="no" value="<?php echo $baris['no']; ?>">
        <input type="hidden" name="waktu" value="<?php echo $baris['waktu']; ?>">
        <input type="hidden" name="pembayar" value="<?php echo $_SESSION['nama_user']; ?>">
        <input type="hidden" name="divisi" value="<?php echo $_SESSION['divisi']; ?>">
        <input type="hidden" name="minterm" value="<?php echo $minterm; ?>">

        <?php
        $jumlah = $baris['jumlah'];
        ?>

        <div class="form-group">
          <label for="jumlah">Nama Bank :</label>
          <input type="text" class="form-control" id="namabank" name="namabank" value="<?php echo $baris['namabank']; ?>">
        </div>

        <div class="form-group">
          <label for="jumlah">Nomor Rekening :</label>
          <input type="number" class="form-control" id="norek" name="norek" value="<?php echo $baris['norek']; ?>">
        </div>

        <div class="form-group">
          <label for="jumlah">Nama Penerima :</label>
          <input type="text" class="form-control" id="namapenerima" name="namapenerima" value="<?php echo $baris['namapenerima']; ?>">
        </div>

        <div class="form-group">
          <label for="jumlah">Total BPU (IDR) :</label>
          <input type="text" class="form-control" value="<?php echo 'Rp. ' . number_format($jumlah, 0, '', ',') ?>" readonly>
          <input type="hidden" class="form-control" id="jumlah" name="jumlahbayar" value="<?php echo $jumlah ?>">
        </div>

        <div class="form-group">
          <label for="nomorvoucher">Nomor Voucher :</label>
          <input type="text" class="form-control" id="nomorvoucher" name="nomorvoucher">
        </div>

        <div class="form-group">
          <label for="tanggal">Tanggal Pembayaran:</label>
          <input type="date" class="form-control" id="tanggal" name="tanggalbayar">
        </div>

        <div class="form-group">
          <p class="control-p"><b>Uploaded File</b></p>
          <img id="image" class="img-responsive" style="display: block; margin-left: auto;  margin-right: auto; background-repeat: no-repeat; background-size: cover; background-attachment: fixed;  " src="uploads/<?= $baris['fileupload'] ?>" alt="">
        </div>

        <button class="btn btn-primary" type="submit" name="submit">Bayar</button>
      </form>

<?php }
  }
}
$koneksi->close();
?>