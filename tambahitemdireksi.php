<?php

session_start();
if (!isset($_SESSION['nama_user'])) {
  header("location:login.php");
  // die('location:login.php');//jika belum login jangan lanjut
}

if ($_POST['waktu']) {
  $waktu = $_POST['waktu'];

  $cariselisih = mysqli_query($koneksi, "SELECT totalbudget,totalbudgetnow FROM pengajuan WHERE waktu='$waktu'");
  $cs = mysqli_fetch_array($cariselisih);
  $totalbudget      = $cs['totalbudget'];
  $totalbudgetnow   = $cs['totalbudgetnow'];

  if ($totalbudget < $totalbudgetnow) {

    $carimax = mysqli_query($koneksi, "SELECT MAX(no) AS nomax FROM selesai WHERE waktu='$waktu'");
    $cm = mysqli_fetch_assoc($carimax);
    $nomax = $cm['nomax'];
    $jadinomax = $nomax + 1;

    $queryin = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE waktu='$waktu'");
    while ($qi = mysqli_fetch_array($queryin)) {

?>

      <!-- MEMBUAT FORM -->

      <input type="hidden" name="nomax" value="<?php echo $jadinomax; ?>">
      <input type="hidden" name="waktu" value="<?php echo $qi['waktu']; ?>">

      <input type="hidden" name="totalbudget" value="<?php echo $totalbudget; ?>">
      <input type="hidden" name="totalbudgetnow" value="<?php echo $totalbudgetnow; ?>">


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
          <option selected disabled>Pilih Status</option>
          <option value="UM">UM</option>
          <option value="Vendor/Supplier">Vendor / Supplier</option>
          <option value="Honor Eksternal">Honor Eksternal</option>
          <option value="Biaya Lumpsum">Biaya Lumpsum Operational</option>
        </select>
      </div>

      <div class="form-group">
        <label for="penerima" class="control-label">Penerima :</label>
        <input type="text" class="form-control" id="penerima2" name="penerima">
      </div>

      </form>

<?php
    }
  } else {
  }
}
?>


<script>
  function sum() {
    var txtSecondNumberValue = document.getElementById('harga2').value;
    var txtTigaNumberValue = document.getElementById('quantity2').value;
    var result = parseFloat(txtSecondNumberValue) * parseFloat(txtTigaNumberValue);
    if (!isNaN(result)) {
      document.getElementById('total2').value = result;
    }
  }
</script>