<?php

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
  $tanggal = date("Y-m-d");


  // mengambil data berdasarkan id
  // dan menampilkan data ke dalam form modal bootstrap
  $sql = "SELECT * FROM selesai WHERE no = '$id' AND waktu = '$waktu'";
  $result = $koneksi->query($sql);
  foreach ($result as $baris) {

?>

    <!-- MEMBUAT FORM -->
    <form action="realisasidireksiproses.php" method="post" id="theForm" name="Form" onsubmit="return validateForm()">

      <input type="hidden" name="no" value="<?php echo $baris['no']; ?>">
      <input type="hidden" name="waktu" value="<?php echo $baris['waktu']; ?>">
      <input type="hidden" name="tanggalrealisasi" value="<?php echo $tanggal; ?>">
      <input type="hidden" name="status" value="Realisasi (Direksi)">

      <div class="form-group">
        <label for="sel1">Gabungan BPU:</label>
        <?php
        $sql2 = "SELECT term,jumlahbayar FROM bpu WHERE no = '$id' AND waktu = '$waktu' AND status='Telah Di Bayar' ORDER BY term";
        $result2 = $koneksi->query($sql2);
        foreach ($result2 as $baris2) {
        ?>
          <div class="checkbox">
            <label><input type="checkbox" class="charge" data-cash="<?php echo $baris2['jumlahbayar']; ?>" value="<?php echo $baris2['jumlahbayar']; ?>">Term <?php echo $baris2['term']; ?></label>
          </div>
        <?php
        }
        ?>
      </div>

      <div class="total"></div>

      <div class="form-group">
        <label for="rincian" class="control-label">Total Realisasi (IDR) :</label>
        <input type="text" class="form-control" id="total" name="uangkembali">
      </div>

      <div class="form-group">
        <label for="rincian" class="control-label">Uang Kembali (IDR) :</label>
        <input type="number" class="form-control" name="uangkembali">
      </div>

      <button class="btn btn-primary" type="submit" name="submit">OK</button>

    </form>


    <script type="text/javascript">
      var $cs = $('.charge').change(function() {
        // var total = +$('#total').val().trim() || 0;
        var v = $(this).data('cash');
        if (this.checked) {
          total += v
        } else {
          total -= v;
        }
        $('#total').val(total);
      });

      $('.charge:checked').change();
    </script>

<?php }
}
$koneksi->close();
?>