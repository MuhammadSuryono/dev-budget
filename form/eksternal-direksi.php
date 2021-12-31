<?php
$query = "SELECT DISTINCT nama_vendor FROM bpu WHERE nama_vendor IS NOT NULL";

$mysqlQuery = mysqli_query($koneksi, $query);
$data = [];
while ($row = $mysqlQuery->fetch_assoc()) {
    $data[] = $row;
}

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

$totalPengajuan = $baris['total'];
$sisaPembayaran = $totalPengajuan - $total;

?>

<div class="form-group">
    <label for="rincian" class="control-label">Total BPU (IDR) :</label>
    <input class="form-control" name="jumlah" id="jumlah" type="number">
</div>
<div class="form-group">
    <label for="berita-transfer" class="control-label">Keterangan Pembayaran/Berita Transfer :</label>
    <input type="text" class="form-control" id="keterangan_pembayaran" name="keterangan_pembayaran">
</div>
<div class="form-group">
    <label class="control-label">Term</label>
    <select class="form-control" name="term" id="term">
        <?php
            for ($i=0; $i < $lastTerm + 1; $i++) { 
                $option = $i + 1;
                $disabled = $i + 1 != 1 && $lastTerm == $i + 1 ? "disabled" : "";
                echo '<option value="'.$option.'" '.$disabled.'>'.$option.'</option>';
            }
        ?>
    </select>
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

<!-- <script>
    // let sisaPembayaran = '<?= $sisaPembayaran ?>';
    // let totalBpu = '<?= $total ?>';
    // let lastTerm = '<?= $lastTerm ?>';

    // let inputJumlah = document.getElementById("jumlah")
    // let optionTerms = document.getElementById("term")
    // inputJumlah.addEventListener('change', (e) => {
    //     let value = e.target.value
    //     let options = ""
    //     value = parseInt(value)
    //     sisaPembayaran = parseInt(sisaPembayaran)
    //     lastTerm = parseInt(lastTerm)

    //     if (lastTerm == 0 && value == sisaPembayaran) {
    //         return
    //     }
    //     // for (i=0; i < lastTerm + 1; i++) { 
    //     //     let option = i + 1;
    //     //     let disabled = i + 1 != 1 && lastTerm == i + 1 ? "disabled" : "";
    //     //     // echo '<option value="'.option.'" '.disabled.'>'.option.'</option>';
    //     // }
    // })
</script> -->