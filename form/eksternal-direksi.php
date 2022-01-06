<?php
$query = "SELECT DISTINCT nama_vendor FROM bpu WHERE nama_vendor IS NOT NULL";

$mysqlQuery = mysqli_query($koneksi, $query);
$data = [];
while ($row = $mysqlQuery->fetch_assoc()) {
    $data[] = $row;
}
$no = $_POST['no'];
$waktu = $_POST['waktu'];

$queryBpu = "SELECT max(term) as last_term, SUM(jumlah) as total FROM bpu WHERE no = '$no' AND waktu = '$waktu'";
$mysqlQuery = mysqli_query($koneksi, $queryBpu);

$lastTerm = 0;
$total = 0;
while($row = mysqli_fetch_assoc($mysqlQuery)) {
    $lastTerm = $row['last_term'];
    $total = $row['total'];
}

if ($lastTerm == NULL) {
    $lastTerm = 0;
}

if ($total == NULL) {
    $total = 0;
}

$totalTerm = 1;
if ($lastTerm > 0) {
    $queryLastBpu = mysqli_query($koneksi, "SELECT ket_pembayaran FROM bpu WHERE no = '$no' AND waktu = '$waktu' AND term = '$lastTerm'");
    $dataLastBpu = mysqli_fetch_assoc($queryLastBpu);
    $explodeLastInvoice = explode(".", $dataLastBpu['ket_pembayaran']);
    $explodeLastTerm = explode("/", $explodeLastInvoice[3]);
    
    $totalTerm = $explodeLastTerm[1];
}

$totalPengajuan = $baris['total'];
$sisaPembayaran = $totalPengajuan - $total;
?>

<div id="alert-more-than"></div>
<div class="form-group">
    <label for="rincian" class="control-label">Total BPU (IDR) :</label>
    <input class="form-control" name="jumlah" id="jumlah" value="<?= $sisaPembayaran ?>" type="number">
</div>
<div class="form-group">
    <label for="berita-transfer" class="control-label">Keterangan Pembayaran/Berita Transfer :</label>
    <input type="text" class="form-control" id="keterangan_pembayaran" name="keterangan_pembayaran">
</div>
<div class="form-group">
    <label class="control-label">Term</label>
    <select class="form-control" name="term1" id="term">
        <?php
            for ($i=0; $i < $lastTerm + 1; $i++) { 
                $option = $i + 1;
                $disabled =  $i + 1 <= $lastTerm ? "disabled" : "";
                echo '<option value="'.$option.'" '.$disabled.'>'.$option.'</option>';
            }
        ?>
    </select>
</div>
<div class="form-group">
    <label class="control-label">Total Term</label>
    <input type="text" class="form-control" id="total-term" name="total-term">
</div>
<div class="form-group">
    <label for="tgl" class="control-label">Tanggal Pembayaran:</label>
    <input type="date" class="form-control" id="tgl" name="tgl">
</div>
<div class="form-group">
    <label for="tgl" class="control-label">Nama Vendor:</label>
    <input type="text" list="brow" class="form-control" id="nama_vendor" name="nama_vendor">
    <datalist id="brow">
        <?php
            foreach ($data as $value) {
                echo '<option value="'.$value['nama_vendor'].'">'.$value['nama_vendor'].'</option>';
            }
        ?>
    </datalist>
</div>

<script>
    let sisaPembayaran = '<?= $sisaPembayaran ?>';
    let lastTerm = '<?= $lastTerm ?>';
    let totalTerm = '<?= $totalTerm ?>';
    
    sisaPembayaran = parseInt(sisaPembayaran)
    lastTerm = parseInt(lastTerm)

    let inputJumlah = document.getElementById("jumlah")
    let totalTermInput = document.getElementById("total-term")
    let alertError = document.getElementById("alert-more-than")

    if (lastTerm == 0) {
        totalTermInput.value = 1
    }
    totalTermInput.value = totalTerm
    inputJumlah.addEventListener('change', (e) => {
        let value = e.target.value
        value = parseInt(value)

        if (lastTerm == 0 && value < sisaPembayaran) {
            totalTermInput.value = 2
        }

        if (lastTerm == 0 && value == sisaPembayaran) {
            totalTerm.value = 1
        }

        if (value > sisaPembayaran) {
            inputJumlah.value = sisaPembayaran
            alertError.innerHTML = `<div class="alert alert-warning" role="alert">
                Total melebihi sisa pembayaran, total otomatis di atur sama dengan sisa pembayaran 
            </div>`;
        } else {
            alertError.innerHTML = ''
        }
    })
</script>