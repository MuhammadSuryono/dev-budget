<?php
error_reporting(0);

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

session_start();
if (!isset($_SESSION['nama_user'])) {
  header("location:login.php");
  // die('location:login.php');//jika belum login jangan lanjut
}

if ($_POST['no'] && $_POST['waktu']) {
  $id = $_POST['no'];
  $waktu = $_POST['waktu'];
  $term = $_POST['term'];

  $cekterm = "SELECT min(term) FROM bpu WHERE no ='$id' AND waktu ='$waktu' AND status='Belum Di Bayar' AND persetujuan='Disetujui (Direksi)'";
  $run_cekterm = $koneksi->query($cekterm);
  $rc = mysqli_fetch_assoc($run_cekterm);
  $minterm = $rc['min(term)'];

  // mengambil data berdasarkan id
  // dan menampilkan data ke dalam form modal bootstrap
  // var_dump($term);
  // var_dump($waktu);
  $sql = "SELECT a.*, b.nama AS nama_rekening FROM bpu a LEFT JOIN rekening b ON b.no = a.rekening_id WHERE a.no ='$id' AND a.waktu ='$waktu' AND a.status='Belum Di Bayar' AND a.persetujuan <> 'Belum Disetujui' AND a.term='$term'";
  $result = $koneksi->query($sql);
  foreach ($result as $baris) {

?>

    <!-- MEMBUAT FORM -->
    <form action="bayarbudgetproses.php" method="post">

      <input type="hidden" name="no" value="<?php echo $baris['no']; ?>">
      <input type="hidden" name="waktu" value="<?php echo $baris['waktu']; ?>">
      <input type="hidden" name="term" value="<?php echo $term; ?>">
      <input type="hidden" name="pembayar" value="<?php echo $_SESSION['nama_user']; ?>">
      <input type="hidden" name="divisi" value="<?php echo $_SESSION['divisi']; ?>">

      <ul class="list-group">
        <li class="list-group-item">Request BPU : <b><?php echo number_format($baris['jumlah'], 0, '', ','); ?></b></li>
        <li class="list-group-item">Bank : <b><?php echo $baris['namabank']; ?></b></li>
        <li class="list-group-item">No Rekening : <b><?php echo $baris['norek']; ?></b></li>
        <li class="list-group-item">Nama Penerima : <b><?php echo ($baris['nama_rekening']) ? $baris['nama_rekening'] : $baris['namapenerima']; ?></b></li>
      </ul>

      <div class="form-group">
        <label for="jumlah">Jumlah Pembayaran :</label>
        <input type="number" class="form-control" id="jumlah" name="jumlahbayar" value="<?php echo $baris['jumlah']; ?>" readonly>
      </div>

      <div class="form-group">
        <label for="nomorvoucher">Nomor Voucher :</label>
        <input type="text" class="form-control" id="nomorvoucher" name="nomorvoucher" required>
      </div>

      <div class="form-group">
        <label for="tanggal">Tanggal Pembayaran:</label>
        <input type="date" class="form-control" id="tanggal" name="tanggalbayar" required>
      </div>

      <button class="btn btn-primary" type="submit" name="submit">Bayar</button>
    </form>

<?php }
}
$koneksi->close();
?>