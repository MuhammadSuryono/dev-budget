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
    $sql = "SELECT a.namapenerima, a.jumlah, a.namabank, a.norek, b.jenis FROM bpu a LEFT JOIN pengajuan b ON a.waktu = b.qrcode WHERE a.no = '$id' AND a.waktu = '$waktu' AND a.term = '$term' AND a. metode_pembayaran = 'MRI Kas' AND a.status = 'Belum Di Bayar'";
    $result = $koneksi->query($sql); ?>

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
        <label for="tanggal">Tanggal Pembayaran:</label>
        <input type="date" class="form-control" id="tanggal" name="tanggalbayar" required>
    </div>

<?php
}
$koneksi->close();
?>