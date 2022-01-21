<?php
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

$con->set_name_db(DB_DEVELOP);
$con->init_connection();
$koneksiDevelop = $con->connect();

session_start();
if (!isset($_SESSION['nama_user'])) {
    header("location:login.php");
    // die('location:login.php');//jika belum login jangan lanjut
}

if ($_POST['no'] && $_POST['waktu'] && $_POST['term']) {
    $id = $_POST['no'];
    $waktu = $_POST['waktu'];
    $term = $_POST['term'];

    $getRekening = mysqli_query($koneksiDevelop, "SELECT * FROM kas WHERE stat = 'MRI'");

    $sql = "SELECT t1.*, t2.berita_transfer, t4.nama AS nama_rekening, t3.label_kas, t3.bank AS kas_bank FROM bpu t1 LEFT JOIN bridgetransfer.data_transfer t2 ON t1.noid = t2.noid_bpu LEFT JOIN develop.kas t3 ON t1.rekening_sumber = t3.rekening LEFT JOIN rekening t4 ON t4.no = t1.rekening_id WHERE t1.no = '$id' AND t1.waktu = '$waktu' AND t1.term='$term' AND t1.persetujuan IN ('Belum Disetujui','Disetujui (Direksi)') GROUP BY t1.noid";
    $result = $koneksi->query($sql); ?>

    <table class="table table-bordered">
        <thead>
            <th>Nama Penerima</th>
            <th>Total</th>
            <th>Bank</th>
            <th>No. Rekening</th>
            <th>Metode Pembayaran</th>
            <th>Rekening Sumber</th>
            <th>Nama Rekening Sumber</th>
        </thead>

        <?php foreach ($result as $baris) :
            $pengaju = $baris['pengaju'];
            $ket_pembayaran = $baris['ket_pembayaran'];
        ?>
            <tbody>
                <tr>
                    <td><?= $baris['namapenerima'] ?></td>
                    <td>Rp. <?= number_format($baris['jumlah']) ?></td>
                    <td><?= $baris['namabank'] ?></td>
                    <td><?= $baris['norek'] ?></td>
                    <td><?= $baris['metode_pembayaran'] ?></td>
                    <td><?= $baris['rekening_sumber'] ?></td>
                    <td><?= $baris['label_kas'] ?></td>
                </tr>
            </tbody>
        <?php
        endforeach;
        ?>
    </table>

    <ul class="list-group">
        <li class="list-group-item">Pengaju : <b><?= $pengaju ?></b></li>
        <li class="list-group-item">Berita Transfer : <b><?= $ket_pembayaran ?></b></li>
    </ul>

    <!-- MEMBUAT FORM -->
    <input type="hidden" name="no" value="<?php echo $id ?>">
    <input type="hidden" name="waktu" value="<?php echo $waktu ?>">
    <input type="hidden" name="term" value="<?php echo $term ?>">
    <input type="hidden" name="persetujuan" value="Sudah Disetujui">

    <p>Apakah anda ingin menyetujui <b>BPU</b> di Nomor <b><?= $baris['no']; ?></b>?</p>

    <div class="form-group">
        <label for="tglbayar" class="control-label">Tanggal Pembayaran :</label>
        <input type="date" class="form-control" id="tglbayar" name="tanggalbayar" min="<?= date('Y-m-d', strtotime($Date . ' + 2 days')) ?>" required>
    </div>

    <div class="form-group">
        <label for="tglcair" class="control-label">Status Urgent :</label>
        <select class="form-control" name="urgent">
            <option value="Not Urgent" selected>-</option>
            <option value="Urgent">Urgent</option>
        </select>
    </div>

    <div class="form-group">
        <label for="alasanTolakBpu" class="control-label">Alasan Penolakan (Jika ditolak):</label>
        <input type="text" class="form-control" name="alasanTolakBpu" id="alasanTolakBpu">
    </div>

<?php
}
$koneksi->close();
?>
<script>
    const picker = document.getElementById('tglbayar');
    
    var tanggalBayar = '<?= $baris['tanggalbayar'] ?>'
    const inputDate = document.getElementById("tglbayar")


    picker.addEventListener('input', function(e) {
        var day = new Date(this.value).getUTCDay();
        if ([6, 0].includes(day)) {
            e.preventDefault();
            this.value = '';
            alert('Weekends not allowed');
        }
    });
    $(document).on('change', '#metode_pembayaran', function() {
        if ($(this).val() == 'MRI PAL') {
            $('.form-rekening-sumber').show();
        } else {
            $('.form-rekening-sumber').hide();

        }
        console.log('here');
    })

    function onChangeStatusUrgent(e) {
        
        if (e.value === "Urgent") {
            inputDate.value = formatDate("yyyy-mm-dd")
            inputDate.setAttribute("min", formatDate("yyyy-mm-dd"))
        } else {
            inputDate.value = tanggalBayar
            inputDate.setAttribute("min", tanggalBayar)
        }
    }

    function formatDate(format) {
        const date = new Date();
        let month = date.getMonth() + 1;
        let day = date.getDate()
        let year = date.getFullYear()
        let singleMonth = [1,2,3,4,5,6,7,8,9]

        month = singleMonth.includes(month) ? "0" + month : month
        return `${year}-${month}-${day}`
    }
</script>