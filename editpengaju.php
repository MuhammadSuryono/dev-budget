<?php
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

if ($_POST['no'] && $_POST['waktu']) {
  $id = $_POST['no'];
  $waktu = $_POST['waktu'];


  $select = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE waktu='$waktu'");
  $d = mysqli_fetch_assoc($select);
  // mengambil data berdasarkan id
  // dan menampilkan data ke dalam form modal bootstrap
  $sql = "SELECT * FROM selesai WHERE no = '$id' AND waktu = '$waktu'";
  $result = $koneksi->query($sql);
  foreach ($result as $baris) {

?>

    <!-- MEMBUAT FORM -->
    <form action="editpengajuproses.php" method="post">
      <input type="hidden" name="no" value="<?php echo $baris['no']; ?>">
      <input type="hidden" name="waktu" value="<?php echo $baris['waktu']; ?>">

      <div class="form-group">
        <label for="rincian" class="control-label">Rincian & Keterangan :</label>
        <input type="text" class="form-control" id="rincian" value="<?php echo $baris['rincian']; ?>" name="rincian">
      </div>

      <div class="form-group">
        <label for="kota" class="control-label">Kota :</label>
        <input type="text" class="form-control" id="kota" value="<?php echo $baris['kota']; ?>" name="kota">
      </div>

      <div class="form-group">
        <label for="status">Status :</label>
        <select class="form-control" id="status" name="status">
          <!-- <option selected value="<?php echo $baris['status']; ?>"><?php echo $baris['status']; ?></option> -->
          <?php if ($d['jenis'] == 'Uang Muka') : ?>
            <option value="UM" <?= ($baris['status'] == "UM") ? "selected" : ''; ?>>UM</option>
            <option value="UM Burek" <?= ($baris['status'] == "UM Burek") ? "selected" : ''; ?>>UM Burek</option>
          <?php else : ?>
            <option value="UM" <?= ($baris['status'] == "UM") ? "selected" : ''; ?>>UM</option>
            <option value="UM Burek" <?= ($baris['status'] == "UM Burek") ? "selected" : ''; ?>>UM Burek</option>
            <option value="Vendor/Supplier" <?= ($baris['status'] == "Vendor/Supplier") ? "selected" : ''; ?>>Vendor / Supplier</option>
            <option value="Honor Eksternal" <?= ($baris['status'] == "Honor Eksternal") ? "selected" : ''; ?>>Honor Eksternal</option>
            <option value="Biaya Lumpsum" <?= ($baris['status'] == "Biaya Lumpsum") ? "selected" : ''; ?>>Biaya Lumpsum Operational</option>
          <?php endif; ?>
        </select>
      </div>

      <div class="form-group">
        <label for="penerima" class="control-label">Penerima :</label>
        <input type="text" class="form-control" id="penerima" value="<?php echo $baris['penerima']; ?>" name="penerima">
      </div>

      <div class="form-group">
        <label for="harga" class="control-label">Harga (IDR) :</label>
        <input type="text" class="form-control" id="harga" value="<?php echo $baris['harga']; ?>" name="harga" onkeyup="sum();">
      </div>

      <div class="form-group">
        <label for="quantity" class="control-label">Quantity :</label>
        <input type="text" class="form-control" id="quantity" value="<?php echo $baris['quantity']; ?>" name="quantity" onkeyup="sum();">
      </div>

      <div class="form-group">
        <label for="total">Total Harga (IDR) :</label>
        <input type="number" class="form-control" id="total" name="total" onkeyup="sum();" value="<?php echo $baris['total']; ?>" readonly>
      </div>

      <button class="btn btn-primary" type="submit" name="submit">Update</button>

    </form>
    <?php break; ?>
<?php }
}
$koneksi->close();
?>


<script>
  // const arrStatus = ['Honor Jakarta', 'Honor Luar Kota', 'STKB TRK Jakarta', 'STKB TRK Luar Kota', 'STKB OPS'];
  // console.log($('#rincian'));
  // reloadButtonEdit(arrStatus);

  function sum() {
    var txtSecondNumberValue = document.getElementById('harga').value;
    var txtTigaNumberValue = document.getElementById('quantity').value;
    var result = parseFloat(txtSecondNumberValue) * parseFloat(txtTigaNumberValue);
    if (!isNaN(result)) {
      document.getElementById('total').value = result;
    }
  }

  function reloadButtonEdit(arrStatus) {
    buttonAddQuestion = document.querySelectorAll(".buttonEdit");
    buttonAddQuestion.forEach(function(e, i) {
      e.addEventListener("click", function() {
        buttonClicked = e;
        numberClicked = i + 1;
        // if (arrStatus.includes($(`#status${numberClicked}`).text())) {
        //   $('#rincian').prop('disabled', true);
        //   $('#kota').prop('disabled', true);
        //   $('#penerima').prop('disabled', true);
        //   $(`#status option[value=""]`).text($(`#status${numberClicked}`).text());
        //   $(`#status option[value=""]`).val($(`#status${numberClicked}`).text());
        //   $(`#status`).prop('disabled', true);
        // } else {
        //   $(`#status`).prop('disabled', false);
        //   $('#rincian').prop('disabled', false);
        //   $('#kota').prop('disabled', false);
        //   $('#penerima').prop('disabled', false);
        // }
        // fillingEditModal(numberClicked);
        // $('#myModal').modal();
      });
    });
  }
</script>