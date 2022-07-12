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
                <option value="pall">MRI Pall</option>
                <option value="kas">MRI Kas</option>
            </select>
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
            <?php foreach ($items as $key => $item) { ?>
                <tr>
                    <td>
                        <input class="form-check-input" type="checkbox" name="idSelectItem" value="1">
                    </td>
                    <td><?= $key + 1 ?> <input class="form-check-input" type="hidden" value="<?= $item['id'] ?>"></td>
                    <td><?= $item['rincian'] ?></td>
                    <td><?= $item['kota'] ?></td>
                    <td><?= $item['status'] ?></td>
                    <td><?= $item['penerima'] ?></td>
                    <td>Rp. <?= number_format($item['total']) ?> <input class="form-check-input" type="hidden" value="<?= $item['total'] ?>"></td>
                    <td><input type="text" class="form-control" name="nominalPengajuan" value="0"></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <div class="text-right">
            <h4><strong>Total Diajukan: Rp. 0</strong></h4>
        </div>
    </div>
    <button class="btn btn-success btn-sm" type="submit">Buat Pengajuan</button>
</div>

<script>

</script>