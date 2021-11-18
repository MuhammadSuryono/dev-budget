<form action="budgetproses.php" method="POST">

    <div class="form-group">
      <label for="email">Keterangan Dan Rincian :</label>
      <input type="text" class="form-control" id="email" name="rincian">
    </div>

    <div class="form-group">
      <label for="kota">Kota Tugas :</label>
      <input type="text" class="form-control" id="kota" name="kota">
    </div>

    <div class="form-group">
      <label for="status">Status :</label>
      <select class="form-control" id="status" name="status">
        <option value="Honor Eksternal">Eksternal</option>
        <option value="Biaya">Biaya Lumpsum Operational</option>
	<option value="Vendor/Supplier">Vendor Supplier</option>
        <option value="UM">Uang Muka (Internal)</option>
      </select>
    </div>

    <div class="form-group">
      <label for="penerima">Penerima Uang :</label>
      <input type="text" class="form-control" id="penerima" name="penerima">
    </div>

    <div class="form-group">
      <label for="harga">Harga Satuan (IDR) :</label>
      <input type="number" class="form-control" id="harga" name="harga" onkeyup="sum();">
    </div>

    <div class="form-group">
      <label for="quantity">Total Quantity :</label>
      <input type="number" class="form-control" id="quantity" name="quantity" onkeyup="sum();">
    </div>

    <div class="form-group">
      <label for="total">Total Harga (IDR) :</label>
      <input type="number" class="form-control" id="total" name="total" onkeyup="sum();" readonly>
    </div>

    <input type="hidden" name="pembayaran" value="Belum Dibayar">
    <input type="hidden" name="pengaju" value="<?php echo $_SESSION['nama_user']; ?>">
    <input type="hidden" name="divisi" value="<?php echo $_SESSION['divisi']; ?>">
    <input type="hidden" name="waktu" value="<?php echo $sp['waktu']; ?>">

    <button type="submit" class="btn btn-success" name="submit">Submit</button>

</form>

<br/><br/>

<?php
include "isi2.php";
?>

 <script src="http://code.jquery.com/jquery-latest.min.js"></script>
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
