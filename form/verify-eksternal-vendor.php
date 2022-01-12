<?php 
$path = "eksternalproses-new.php?action=update&id-bpu=".$idBpu."&id-verify=".$idVerify;

$queryDataPenerima = mysqli_query($koneksi, "SELECT namapenerima, emailpenerima, norek, namabank, bank_account_name, vendor_type FROM bpu WHERE namapenerima NOT IN ('TLF', '') GROUP BY namapenerima");
$dataPenerima = [];
while ($row = $queryDataPenerima->fetch_assoc()) {
    $dataPenerima[] = $row;
}
$nomorInvoce = "";
$term = 1;
$endTerm = 1;
$ket = $dataBpu['ket_pembayaran'];
$dateInvoice = "";
$explodeKetPembayaran = explode(".", $dataBpu['ket_pembayaran']);
if (count($explodeKetPembayaran) > 0) {
    // INV.87990.301221.T1/1.Keterangan Pembayarn
    // INV.[NOMOR_INVOICE].[DATE(dd-mm-y)].T[START_TERM]/[END_TERM].[KETERANGAN]
    if ($explodeKetPembayaran[0] == "INV") {
        $nomorInvoce = $explodeKetPembayaran[1]; // NUMBER INVOICE
        $term = $explodeKetPembayaran[3][1]; // [START TERM PEMBAYARAN]
        
        $dateFormat = $explodeKetPembayaran[2]; // [DATE]
        $day = $dateFormat[0].$dateFormat[1];
        $month = $dateFormat[2].$dateFormat[3];
        $year = "20".$dateFormat[4].$dateFormat[5];
        $dateInvoice = $year . "-" . $month . "-" . $day;

        $explodeTerm = explode("/", $explodeKetPembayaran[3]);
        $endTerm = $explodeTerm[1]; // [END TERM PEMBAYARAN]
        $ket = $explodeKetPembayaran[count($explodeKetPembayaran) - 1]; // [KETERANGAN]
    }
}

$penerima = $dataBpu['namapenerima'];
if ($penerima == "") {
    $penerima = $dataBpu['nama_vendor'];
}
?>
<form action="<?= $path ?>" method="post">
    <input type="hidden" name="no" value="<?= $dataBpu['no'] ?>" />
    <input type="hidden" name="divisi" value="<?= $dataBpu['divisi'] ?>" />
    <input type="hidden" name="pengaju" value="<?= $dataBpu['pengaju'] ?>" />
    <input type="hidden" name="waktu" value="<?= $dataBpu['waktu'] ?>" />
    <input type="hidden" name="statusbpu" value="<?= $dataBpu['statusbpu'] ?>" />
    <div class="form-group">
        <label for="namapenerima" class="control-label">Nama Penerima: </label>
        <input type="text" list="brow" class="form-control" name="namapenerima" id="nama-penerima" value="<?= $penerima ?>" onchange="onChangePenerima(this)" required readonly>
        <datalist id="brow">
        <?php
            foreach ($dataPenerima as $value) {
                echo '<option email="'.$value['emailpenerima'].'" norek="'.$value['norek'].'" bank="'.$value['namabank'].'" bank_account="'.$value['bank_account_name'].'" value="'.$value['namapenerima'].'" vendor_type="'.$value['vendor_type'].'">';
            }
        ?>
        </datalist>
        </div>
        <div class="form-group">
            <label for="vendor_type" class="control-label">Jenis Perusahaan :</label>
            <select class="form-control" name="vendor_type" id="vendor_type" required>
                <option value="" selected disabled>Pilih Jenis Perusahaan</option>
                <option value="perseroan" <?= $dataBpu['vendor_type'] == 'perseroan' ? 'selected' : '' ?>>Perseroan</option>
                <option value="perorangan" <?= $dataBpu['vendor_type'] == 'perorangan' ? 'selected' : '' ?>>Perorangan</option>
            </select>
        </div>

        <div class="form-group">
            <label for="email" class="control-label">Email :</label>
            <input type="email" class="form-control" name="email" value="<?= $dataBpu['emailpenerima'] ?>"  id="email" required>
        </div>
        <div class="form-group">
            <label for="namabank" class="control-label">Nama Bank :</label>
            <select class="form-control" name="namabank" id="bank" onchange="" required>
                <option value="" selected disabled>Pilih Kategori</option>
                <?php
                $queryDaftarBank = mysqli_query($koneksi, 'SELECT * FROM bank');
                while ($db = mysqli_fetch_assoc($queryDaftarBank)) :
                    $selected = $db['kodebank'] == $dataBpu['namabank'] ? "selected" : "";
                ?>
                    <option value="<?= $db['kodebank'] ?>" <?= $selected ?> ><?= $db['namabank'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="bank_account_name" class="control-label">Nama Rekening Bank:</label>
            <input type="text" class="form-control" name="bank_account_name" id="bank_account_name" value="<?= $dataBpu['bank_account_name'] ?>" required>
            <small>Nama harus sesuai dengan yang terdaftar pada bank Penerima. <b  class="text-danger">KESALAHAN PADA NAMA DAPAT MENYEBABKAN DANA TIDAK DAPAT DI TRANSFER UNTUK METODE PEMBAYARAN MRI PAL</b></small>
        </div>
        <div class="form-group">
            <label for="norek" class="control-label">Nomor Rekening :</label>
            <input type="number" class="form-control" name="norek" id="norek" value="<?= $dataBpu['norek'] ?>" required>
        </div>
        <div class="row">
        <div class="col-lg-4">
            <div class="form-group">
                <label for="invoice" class="control-label">Nomor Invoice:</label>
                <input type="text" class="form-control" id="invoice" value="<?= $nomorInvoce ?>" name="invoice" maxlength="15" placeholder="00000">
            </div>
        </div>
        <div class="col-lg-4">
            <div class="form-group">
                <label for="tgl" class="control-label">Tanggal:</label>
                <input type="date" class="form-control" value="<?= $dateInvoice ?>" id="tgl" name="tgl">
            </div>
        </div>
        <div class="col-lg-2">
            <div class="form-group">
                <label for="term" class="control-label">Term:</label>
                <input type="text" class="form-control" id="term1" name="term1" value="<?= $term ?>" maxlength="1" placeholder="1" readonly>
            </div>
        </div>
        <div class="col-lg-2">
            <div class="form-group">
                <label for="term" class="control-label">of Term:</label>
                <input type="text" class="form-control" id="term2" name="term2" value="<?= $endTerm ?>" maxlength="1" placeholder="1" readonly>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="jenis_pembayaran" class="control-label">Jenis Pembayaran(barang/jasa) :</label>
        <input type="text" class="form-control" id="jenis_pembayaran" value="<?= $ket ?>" name="jenis_pembayaran">
    </div>
    <?php if ($dataBpu['pengajuan_jumlah'] < 1000000 && $dataPengajuan['jenis'] != 'Rutin') { 
        if ($_SESSION['hak_akses'] != 'Pegawai2') {
            echo '<button class="btn btn-primary" type="submit" name="submit">Simpan</button>';
        }

        if ($_SESSION['hak_akses'] == 'Pegawai2') {
            if ($_SESSION['level'] != 'Koordinator') {
                echo '<button class="btn btn-primary" type="submit" name="submit">Simpan</button>';
            }
        }
        
        ?>
    <?php } ?>
    <?php if ($dataPengajuan['jenis'] == 'Rutin' && $_SESSION['hak_akses'] == 'Level 2' && $_SESSION['level'] == 'Koordinator') { ?>
        <button class="btn btn-primary" type="submit" name="submit">Simpan</button>
    <?php } ?>
    <?php if ($dataBpu['pengajuan_jumlah'] >= 1000000 && $dataPengajuan['jenis'] != 'Rutin' && $_SESSION['hak_akses'] == 'Pegawai2' && $_SESSION['level'] == 'Koordinator') { ?>
        <button class="btn btn-primary" type="submit" name="submit">Simpan</button>
    <?php } ?>
</form>
<script>
    let optionBank = document.getElementById('bank')
    let optionVendorType = document.getElementById('vendor_type')
    let dataListSelected = document.getElementById('brow')
    let emailInput = document.getElementById('email')
    let norekInput = document.getElementById('norek')
    let inputPenerima = document.getElementById('nama-penerima')
    let inputNamarekening = document.getElementById('bank_account_name')

    onChangePenerima({value: inputPenerima.value})

    function onChangePenerima(elem) {
        for (let i = 0; i < dataListSelected.childElementCount; i++) {
            if (dataListSelected.children[i].attributes.value.value == elem.value) {
                emailInput.value = dataListSelected.children[i].attributes.email.value
                norekInput.value = dataListSelected.children[i].attributes.norek.value
                inputNamarekening.value = dataListSelected.children[i].attributes.bank_account.value

                if (dataListSelected.children[i].attributes.bank.value !== "") {
                    for (let j = 0; j < optionBank.childElementCount; j++) {
                        const element = optionBank[j];
                        
                        if (element.value == dataListSelected.children[i].attributes.bank.value) {
                            element.selected = true
                        }
                    }
                }

                if (dataListSelected.children[i].attributes.vendor_type.value !== "") {
                    for (let k = 0; k < optionVendorType.childElementCount; k++) {
                        const element = optionVendorType[k];

                        if (element.value == dataListSelected.children[i].attributes.vendor_type.value) {
                            element.selected = true
                        }
                    }
                }
            }
        }
    }
</script>