<?php 
$path = "eksternalproses-new.php?action=update&id-bpu=".$idBpu."&id-verify=".$idVerify;

$queryDataPenerima = mysqli_query($koneksi, "SELECT namapenerima, emailpenerima, norek, namabank FROM bpu WHERE namapenerima NOT IN ('TLF', '') GROUP BY namapenerima");
$dataPenerima = [];
while ($row = $queryDataPenerima->fetch_assoc()) {
    $dataPenerima[] = $row;
}

$explodeKetPembayaran = explode(".", $dataBpu['ket_pembayaran']);
?>
<form action="<?= $path ?>" method="post">
    <input type="hidden" name="no" value="<?= $dataBpu['no'] ?>" />
    <input type="hidden" name="divisi" value="<?= $dataBpu['divisi'] ?>" />
    <input type="hidden" name="pengaju" value="<?= $dataBpu['pengaju'] ?>" />
    <input type="hidden" name="waktu" value="<?= $dataBpu['waktu'] ?>" />
    <input type="hidden" name="statusbpu" value="<?= $dataBpu['statusbpu'] ?>" />
    <div class="form-group">
        <label for="namapenerima" class="control-label">Nama Penerima: </label>
        <input type="text" list="brow" class="form-control" name="namapenerima" value="<?= $dataBpu['namapenerima'] ?>" onchange="onChangePenerima(this)" required>
        <datalist id="brow">
        <?php
            foreach ($dataPenerima as $value) {
                echo '<option email="'.$value['emailpenerima'].'" norek="'.$value['norek'].'" bank="'.$value['namabank'].'" value="'.$value['namapenerima'].'">';
            }
        ?>
        </datalist>
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
            <label for="norek" class="control-label">Nomor Rekening :</label>
            <input type="number" class="form-control" name="norek" id="norek" value="<?= $dataBpu['norek'] ?>" required>
        </div>
        <div class="row">
        <div class="col-lg-4">
            <div class="form-group">
                <label for="invoice" class="control-label">Nomor Invoice:</label>
                <input type="text" class="form-control" id="invoice" name="invoice" maxlength="15" placeholder="00000">
            </div>
        </div>
        <div class="col-lg-4">
            <div class="form-group">
                <label for="tgl" class="control-label">Tanggal:</label>
                <input type="date" class="form-control" id="tgl" name="tgl">
            </div>
        </div>
        <div class="col-lg-2">
            <div class="form-group">
                <label for="term" class="control-label">Term:</label>
                <input type="text" class="form-control" id="term1" name="term1" maxlength="1" placeholder="1">
            </div>
        </div>
        <div class="col-lg-2">
            <div class="form-group">
                <label for="term" class="control-label">of Term:</label>
                <input type="text" class="form-control" id="term2" name="term2" maxlength="1" placeholder="1">
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="jenis_pembayaran" class="control-label">Jenis Pembayaran(barang/jasa) :</label>
        <input type="text" class="form-control" id="jenis_pembayaran" value="<?= $dataBpu['ket_pembayaran'] ?>" name="jenis_pembayaran">
    </div>
    <button class="btn btn-primary" type="submit" name="submit">Simpan</button>
</form>
<script>
    let optionBank = document.getElementById('bank')
    let dataListSelected = document.getElementById('brow')
    let emailInput = document.getElementById('email')
    let norekInput = document.getElementById('norek')

    function onChangePenerima(elem) {
        for (let i = 0; i < dataListSelected.childElementCount; i++) {
            console.log(dataListSelected.children[i].attributes.value.value == elem.value, dataListSelected.children[i].attributes.value.value, elem.value)
            if (dataListSelected.children[i].attributes.value.value == elem.value) {
                emailInput.value = dataListSelected.children[i].attributes.email.value
                norekInput.value = dataListSelected.children[i].attributes.norek.value

                if (dataListSelected.children[i].attributes.bank.value !== "") {
                    for (let j = 0; j < optionBank.childElementCount; j++) {
                        const element = optionBank[j];
                        
                        if (element.value == dataListSelected.children[i].attributes.bank.value) {
                            element.selected = true
                            options += `<option value="${element.value}">${element.value}</option>`
                        }
                    }
                }
            }
        }
    }
</script>