<div class="">
    <div class="form-group row">
        <div class="col-sm-6">
            <label>Pilih Term</label>
            <select class="form-control" id="termPengajuan" required>
                <option value="">Pilih Term</option>
                <option value="1">Term 1</option>
                <option value="2">Term 2</option>
            </select>
        </div>
        <div class="col-sm-6">
            <label>Jenis Kas</label>
            <select class="form-control" id="typeKas" required>
                <option value="">Pilih Jenis Kas</option>
                <?php
                while ($q = mysqli_fetch_array($itemKasQuery)) {
                    $bank = "MANDIRI";
                    if ($q['bank'] == 'CENAIDJA') {
                        $bank = 'BCA';
                    }
                    $typeKas = "KAS";
                    if ($q['type_kas'] == 'mri-pall') $typeKas = 'PALL';
                    echo "<option value='$q[id_kas]'>BANK $bank ($typeKas) - $q[rekening]</option>";
                }
                ?>
            </select>
        </div>

        <div class="col-sm-6">
            <label>Tanggal Jatuh Tempo</label>
            <input type="date" id="JatuhTempo" class="form-control" required>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th></th>
                    <th>No</th>
                    <th>Rincian</th>
                    <th>Kota</th>
                    <th>Status</th>
                    <th>Penerimaan</th>
                    <th>Sisa Total Item</th>
                    <th>Total Diajukan</th>
                </tr>
            </thead>
            <tbody id="allRincianItem">
            <?php foreach ($items as $key => $item) {
                $dataPengajuan = $this->select('SUM(total_pengajuan) as total_pengajuan')->from('pengajuan_kas_item')->where('item_id', '=', $item['id'])->first();
                $selisih = $item['total'];
                if (isset($dataPengajuan)) {
                    $selisih = $item['total'] - $dataPengajuan['total_pengajuan'];
                }
                if ($selisih != 0) {
                ?>
                <tr>
                    <td>
                        <input class="form-check-input" type="checkbox" name="idSelectItem" value="1">
                    </td>
                    <td><?= $item['no'] ?> <input class="form-check-input" type="hidden" value="<?= $item['id'] ?>"></td>
                    <td><?= $item['rincian'] ?></td>
                    <td><?= $item['kota'] ?></td>
                    <td><?= $item['status'] ?></td>
                    <td><?= $item['penerima'] ?></td>
                    <td>Rp. <?= number_format($selisih) ?> <input class="form-check-input" type="hidden" value="<?= $selisih ?>"></td>
                    <td><input type="number" class="form-control input-value txtCal" name="nominalPengajuan" onkeyup="setnum()" value="0"></td>
                </tr>
            <?php } } ?>
            </tbody>
        </table>
        <div class="text-right">
            <h4><strong>Total Diajukan: Rp. <span id="total_sum_value">0</span></strong></h4>
        </div>
    </div>
    <button class="btn btn-success btn-sm" type="submit">Buat Pengajuan</button>
</div>

<script>

</script>