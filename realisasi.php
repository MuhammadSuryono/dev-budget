<?php
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

if ($_POST['no'] && $_POST['waktu']) {
  $id = $_POST['no'];
  $waktu = $_POST['waktu'];
  $termreal = $_POST['term'];
  var_dump($termreal);


  // mengambil data berdasarkan id
  // dan menampilkan data ke dalam form modal bootstrap
  $sql = "SELECT * FROM bpu WHERE no = '$id' AND waktu = '$waktu' AND term = '$termreal'";
  $result = $koneksi->query($sql);
  foreach ($result as $baris) {


    if ($baris['status'] == 'Telah Di Bayar' && $realisasi == 0) {
      echo "BPU Belum Di Realisasi";
    } else {

?>

      <!-- MEMBUAT FORM -->
      <form action="realisasiproses.php" method="post" name="Form" onsubmit="return validateForm()">

        <input type="hidden" name="no" value="<?php echo $baris['no']; ?>">
        <input type="hidden" name="waktu" value="<?php echo $baris['waktu']; ?>">
        <input type="hidden" name="term" value="<?php echo $baris['term']; ?>">
        <input type="hidden" name="pengaju" value="<?php echo $baris['pengaju']; ?>">
        <input type="hidden" name="divisi" value="<?php echo $baris['divisi']; ?>">
        <input type="hidden" name="status" value="Realisasi (Finance)">


        <div class="form-group">
          <label for="realisasi" class="control-label">Total Realisasi (IDR) :</label>
          <input type="number" class="form-control" id="realisasi" name="realisasi" value="<?php echo $baris['realisasi'] ?>" readonly>
        </div>

        <div class="form-group">
          <label for="uangkembali" class="control-label">Uang Kembali (IDR) :</label>
          <input type="number" class="form-control" id="b" name="uangkembali">
        </div>

        <div class="form-group">
          <label for="tanggalrealisasi" class="control-label">Tanggal Transfer :</label>
          <input type="date" class="form-control" id="c" name="tanggalrealisasi">
        </div>


        <button class="btn btn-primary" type="submit" name="submit">SUBMIT</button>

      </form>

<?php }
  }
}
$koneksi->close();
?>