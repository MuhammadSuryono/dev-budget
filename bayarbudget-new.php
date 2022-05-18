<?php
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

error_reporting(0);
session_start();
if (!isset($_SESSION['nama_user'])) {
    header("location:login.php");
    // die('location:login.php');//jika belum login jangan lanjut
}
$total = 0;

if ($_POST['no'] && $_POST['waktu'] && $_POST['term']) {
    $id = $_POST['no'];
    $waktu = $_POST['waktu'];
    $term = $_POST['term'];

    // mengambil data berdasarkan id
    // dan menampilkan data ke dalam form modal bootstrap
    $result = $koneksi->query("SELECT * from bpu where no = '$id' AND waktu = '$waktu' AND term = '$term' AND (metode_pembayaran = 'MRI Kas' OR metode_pembayaran = '') AND status = 'Belum Di Bayar'");
    ?>

    <table class="table table-bordered">
        <thead>
            <th>Nama Penerima</th>
            <th>Total</th>
            <th>Bank</th>
            <th>No. Rekening</th>
        </thead>

        <?php foreach ($result as $baris) :
            $total += $baris['jumlah'];
        ?>
            <tbody>
                <tr>
                    <td><?= $baris['namapenerima'] ?></td>
                    <td>Rp. <?= number_format($baris['jumlah']) ?></td>
                    <td><?= $baris['namabank'] ?></td>
                    <td><?= $baris['norek'] ?></td>
                </tr>
            </tbody>
        <?php
        endforeach;
        ?>
    </table>
    <input type="hidden" name="waktu" value="<?= $waktu ?>">
    <input type="hidden" name="no" value="<?= $id ?>">
    <input type="hidden" name="term" value="<?= $term ?>">

    <div class="form-group">
        <label for="jumlah">Jumlah Pembayaran :</label>
        <input type="number" class="form-control" id="jumlah" name="jumlahbayar" value="<?php echo $total ?>" readonly>
    </div>

    <div class="form-group">
        <label for="nomorvoucher">Nomor Voucher :</label>
        <input type="text" class="form-control" id="nomorvoucher" name="nomorvoucher" required>
    </div>

    <div class="form-group">
        <label for="tanggal">Upload Bukti Bayar:</label>
        <input type="file" class="form-control" accept="image/*,application/pdf" name="gambar" id="fileInput" required>
        <img class="img-responsive" style="display: block; margin-left: auto;  margin-right: auto; background-repeat: no-repeat; background-size: cover; background-attachment: fixed;" src="" alt="" id="imageRealisasi">
    </div>

    <div class="form-group">
        <label for="tanggal">Tanggal Pembayaran:</label>
        <input type="date" class="form-control" id="tanggal" name="tanggalbayar" required>
    </div>

<?php
}
$koneksi->close();
?>

<script>
    $(document).ready(function() {
        $('#fileInput').change(function() {
            console.log(this)
            readURL(this);
          })
        
          function readURL(input) {
              console.log(input.files)
          if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
              $('#imageRealisasi').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]); // convert to base64 string
          }
        }
    })
</script>