<?php 
$path = "eksternalproses-new.php?action=update&id-bpu=".$idBpu."&id-verify=".$idVerify;
?>
<form action="<?= $path ?>" method="post">
    <input type="hidden" name="no" value="<?= $dataBpu['no'] ?>" />
    <input type="hidden" name="divisi" value="<?= $dataBpu['divisi'] ?>" />
    <input type="hidden" name="pengaju" value="<?= $dataBpu['pengaju'] ?>" />
    <input type="hidden" name="waktu" value="<?= $dataBpu['waktu'] ?>" />
    <div class="form-group">
        <label for="namapenerima" class="control-label">Nama Penerima: </label>
        <input type="text" class="form-control" name="namapenerima" required>
        </div>

        <div class="form-group">
            <label for="email" class="control-label">Email :</label>
            <input type="email" class="form-control" name="email" required>
        </div>

        <div class="form-group">
            <label for="namabank" class="control-label">Nama Bank :</label>
            <select class="form-control" name="namabank" required>
                <option value="" selected disabled>Pilih Kategori</option>
                <?php
                $queryDaftarBank = mysqli_query($koneksi, 'SELECT * FROM bank');
                while ($db = mysqli_fetch_assoc($queryDaftarBank)) :
                ?>
                    <option value="<?= $db['kodebank'] ?>"><?= $db['namabank'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="norek" class="control-label">Nomor Rekening :</label>
            <input type="number" class="form-control" name="norek" required>
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
        <input type="text" class="form-control" id="jenis_pembayaran" name="jenis_pembayaran">
    </div>
    <button class="btn btn-primary" type="submit" name="submit">Simpan</button>
</form>