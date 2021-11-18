<?php

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

if ($_POST['no'] && $_POST['waktu']) {
  $id = $_POST['no'];
  $waktu = $_POST['waktu'];


  // mengambil data berdasarkan id
  // dan menampilkan data ke dalam form modal bootstrap
  $sql = "SELECT * FROM selesai WHERE no = '$id' AND waktu = '$waktu'";
  $result = $koneksi->query($sql);
  foreach ($result as $baris) {

?>

    <!-- MEMBUAT FORM -->
    <form action="editbudgetproses.php" method="post">
      <input type="hidden" name="no" value="<?php echo $baris['no']; ?>">
      <input type="hidden" name="waktu" value="<?php echo $baris['waktu']; ?>">

      Rincian & Keterangan : <b><?php echo $baris['rincian']; ?></b><br>
      Kota : <b><?php echo $baris['kota']; ?></b><br>
      Status : <b><?php echo $baris['status']; ?></b><br>
      Penerima Pembayaran : <b><?php echo $baris['penerima']; ?></b><br>
      Harga (IDR) : <b><?php echo 'Rp. ' . number_format($baris['harga'], 0, '', ','); ?></b><br>
      Quantity : <b><?php echo $baris['quantity']; ?></b><br>
      Total Harga (IDR) : <b><?php echo 'Rp. ' . number_format($baris['total'], 0, '', ','); ?></b><br><br>

      <div class="form-group">
        <label for="penerima" class="col-sm-3 control-label">Penerima Pembayaran:</label>
        <input type="text" class="form-control" id="penerima" name="penerima" value="<?php echo $baris['penerima']; ?>">
      </div>

      <div class="form-group">
        <label for="harga" class="col-sm-3 control-label">Harga :</label>
        <input type="number" class="form-control" id="harga" placeholder="" name="harga" onkeyup="sum();">
      </div>

      <div class="form-group">
        <label for="quantity" class="col-sm-3 control-label">Quantity :</label>
        <input type="number" class="form-control" id="quantity" placeholder="" name="quantity" onkeyup="sum();">
      </div>

      <div class="form-group">
        <label for="total">Total Harga (IDR) :</label>
        <input type="number" class="form-control" id="total" name="total" onkeyup="sum();" readonly>
      </div>

      <div class="form-group">
        <label for="total">Komentar :</label>
        <input type="text" class="form-control" id="komentar" name="komentar" value="<?php echo $baris['komentar']; ?>">
      </div>

      <button class="btn btn-primary" type="submit" name="submit">Update</button>
    </form>
    <?php break; ?>
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