<?php
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

session_start();
if (!isset($_SESSION['nama_user'])) {
  header("location:login.php");
  // die('location:login.php');//jika belum login jangan lanjut
}


if ($_POST['waktu'] && $_POST['no']) {
  $waktu = $_POST['waktu'];
  $no    = $_POST['no'];


  // mengambil data berdasarkan id
  // dan menampilkan data ke dalam form modal bootstrap
  $sql = "SELECT * FROM pengajuan WHERE waktu = '$waktu'";
  $result = $koneksi->query($sql);
  foreach ($result as $baris) {

?>

    <!-- MEMBUAT FORM -->

    <input type="hidden" name="no" value="<?php echo $baris['noid']; ?>">
    <input type="hidden" name="waktu" value="<?php echo $baris['waktu']; ?>">
    <input type="hidden" name="pengaju" value="<?php echo $_SESSION['nama_user']; ?>">
    <input type="hidden" name="divisi" value="<?php echo $_SESSION['divisi']; ?>">

    <div class="form-group">
      <label for="rincian" class="control-label">Rincian & Keterangan :</label>
      <input type="text" class="form-control" id="rincian" name="rincian">
    </div>

    <div class="form-group">
      <label for="kota" class="control-label">Kota :</label>
      <input type="text" class="form-control" id="kota" name="kota">
    </div>

    <div class="form-group">
      <label for="status">Status :</label>
      <select class="form-control" id="status" name="status">
        <?php
        $carijenis = "SELECT jenis FROM pengajuan WHERE waktu='$waktu'";
        $run_carijenis = $koneksi->query($carijenis);
        $rc = mysqli_fetch_assoc($run_carijenis);

        if ($rc['jenis'] == 'UM Burek') {
        ?>
          <option selected value="UM">UM</option>
        <?php
        } else {
        ?>
          <option selected disabled>Pilih Status</option>
          <option value="UM">UM</option>
          <option value="Vendor/Supplier">Vendor / Supplier</option>
          <option value="Honor Eksternal">Honor Eksternal</option>
          <option value="Biaya Lumpsum">Biaya Lumpsum Operational</option>
        <?php } ?>
      </select>
    </div>

    <div class="form-group">
      <label for="penerima" class="control-label">Penerima :</label>
      <input type="text" class="form-control" id="penerima" name="penerima">
    </div>

    </form>

<?php }
}
$koneksi->close();
?>


<script>
  function sum() {
    var txtSecondNumberValue = document.getElementById('harga').value;
    var txtTigaNumberValue = document.getElementById('quantity').value;
    var result = parseFloat(txtSecondNumberValue) * parseFloat(txtTigaNumberValue);
    if (!isNaN(result)) {
      document.getElementById('total').value = result;
    }
  }
</script>