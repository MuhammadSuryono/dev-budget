<?php
    session_start();
    require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

    if(!isset($_SESSION['nama_user'])){
      header("location:login.php");
        // die('location:login.php');//jika belum login jangan lanjut

    if($_POST['no'] && $_POST['waktu']) {
        $noidbpu = $_POST['noidbpu'];
        $id = $_POST['no'];
        $waktu = $_POST['waktu'];
        $term = $_POST['term'];


        // mengambil data berdasarkan id
        // dan menampilkan data ke dalam form modal bootstrap


        $sql = "SELECT * FROM bpu WHERE noid='$noidbpu' AND no = '$id' AND waktu = '$waktu' AND term = '$term'";
        $result = $koneksi->query($sql);
        foreach ($result as $baris) {

        ?>

        <!-- MEMBUAT FORM -->
        <form action="editdireksiproses.php" method="post">
            <input type="hidden" name="noidbpu" value="<?php echo $baris['noid']; ?>">
            <input type="hidden" name="no" value="<?php echo $baris['no']; ?>">
            <input type="hidden" name="waktu" value="<?php echo $baris['waktu']; ?>">
            <input type="hidden" name="term" value="<?php echo $baris['term']; ?>">
            <input type="hidden" name="lastedit" value="<?php echo $_SESSION['nama_user']; ?>">
            <input type="hidden" name="jumlahbayar" value="<?php echo $baris['jumlahbayar']; ?>">

            <div class="form-group">
              <label for="jumlah" class="control-label">Request BPU (IDR) :</label>
                <input type="text" class="form-control" id="jumlah" value="<?php echo $baris['jumlah']; ?>" name="jumlah">
            </div>

            <div class="form-group">
              <label for="jumlah" class="control-label">Realisasi Biaya (IDR) :</label>
                <input type="text" class="form-control" id="jumlah" value="<?php echo $baris['realisasi']; ?>" name="realisasi">
            </div>

            <div class="form-group">
              <label for="jumlah" class="control-label">Uang Kembali (IDR) :</label>
                <input type="text" class="form-control" id="jumlah" value="<?php echo $baris['uangkembali']; ?>" name="uangkembali">
            </div>

            <div class="form-group">
              <label for="status">Status :</label>
              <select class="form-control" id="status" name="statusbpu">
                <option selected value="<?php echo $baris['statusbpu']; ?>"><?php echo $baris['statusbpu']; ?></option>
                <option value="UM">UM</option>
                <option value="Vendor/Supplier">Vendor / Supplier</option>
                <option value="Honor Eksternal">Honor Eksternal</option>
                <option value="Biaya Lumpsum">Biaya Lumpsum Operational</option>
              </select>
            </div>

            <div class="form-group">
              <label  for="namabank" class="control-label">Nama Bank :</label>
                <input type="text" class="form-control" id="namabank" value="<?php echo $baris['namabank']; ?>" name="namabank">
            </div>

            <div class="form-group">
              <label for="namapenerima" class="control-label">Nama Penerima :</label>
                <input type="text" class="form-control" id="namapenerima" value="<?php echo $baris['namapenerima']; ?>" name="namapenerima">
            </div>

            <div class="form-group">
              <label for="norek" class="control-label">Nomor Rekening :</label>
                <input type="text" class="form-control" id="norek" value="<?php echo $baris['norek']; ?>" name="norek">
            </div>

            <div class="form-group">
              <label for="tglcair" class="control-label">Tanggal Pencairan :</label>
                <input type="date" class="form-control" id="tglcair" value="<?php echo $baris['tglcair']; ?>" name="tglcair">
            </div>

            <div class="form-group">
              <label for="alasan" class="control-label">Comment :</label>
                <input type="text" class="form-control" id="alasan" name="alasan">
            </div>

              <button class="btn btn-primary" type="submit" name="submit">Update</button>

        </form>

      <?php } }
    $koneksi->close();
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
