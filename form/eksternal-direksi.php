<?php
session_start();
?>

<div class="form-group">
    <label for="rincian" class="control-label">Total BPU (IDR) :</label>
    <input class="form-control" name="jumlah" type="number">
</div>
<div class="form-group">
    <label for="berita-transfer" class="control-label">Keterangan Pembayaran/Berita Transfer :</label>
    <input type="text" class="form-control" id="keterangan_pembayaran" name="keterangan_pembayaran">
</div>
<div class="form-group">
    <label for="tgl" class="control-label">Tanggal Pembayaran:</label>
    <input type="date" class="form-control" id="tgl" name="tgl">
</div>
<div class="form-group">
    <label for="tgl" class="control-label">Nama Vendor:</label>
    <input type="text" class="form-control" id="nama_vendor" name="namaa_vendor">
</div>