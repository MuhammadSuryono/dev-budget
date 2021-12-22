<div style="display: flex; justify-content: end; margin-bottom: 10px;">
        <button type="button" class="btn btn-sm btn-primary btn-tambah-row">Tambah Penerima</button>
    </div>

    <div class="row row-penerima-honor">
        <div class="col-lg-2">
            <div class="form-group">
                <label for="rincian" class="control-label">Total BPU (IDR) :</label>
                <input class="form-control" name="jumlah[]" type="number">
            </div>
        </div>

        <div class="col-lg-3">
            <div class="form-group">
                <!-- <span style="float: right; margin-bottom: 5px;"><button type="button" class="btn btn-sm btn-primary btn-tambah-penerima">Tambah Penerima</button></span> -->
                <label for="namapenerima" class="control-label">Nama Penerima: </label>
                <input type="text" class="form-control" name="namapenerima[]" required>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="form-group">
                <label for="email" class="control-label">Email :</label>
                <input type="email" class="form-control" name="email[]" required>
            </div>
        </div>

        <div class="col-lg-2">
            <div class="form-group">
                <label for="namabank" class="control-label">Nama Bank :</label>
                <select class="form-control" name="namabank[]" required>
                    <option value="" selected disabled>Pilih Kategori</option>
                    <?php
                    $queryDaftarBank = mysqli_query($koneksi, 'SELECT * FROM bank');
                    while ($db = mysqli_fetch_assoc($queryDaftarBank)) :
                    ?>
                        <option value="<?= $db['kodebank'] ?>"><?= $db['namabank'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

        <div class="col-lg-2">
            <div class="form-group">
                <label for="norek" class="control-label">No. Rekening :</label>
                <input type="number" class="form-control" name="norek[]" required>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="tglcair" class="control-label">Tanggal Permintaan Pencairan :</label>
        <input type="date" class="form-control" name="tglcair">
    </div>
    
    <div class="form-group form-group-file">
        <label class="control-label">Upload File <a href="#" data-toggle="tooltip" title="Upload File Rincian BPU"><i class="fa fa-question-circle"></i></a></label>
        <input type="file" class="form-control" accept="image/*" name="gambar" id="fileInputEksternal" required>
        <img class="img-responsive" style="display: block; margin-left: auto;  margin-right: auto; background-repeat: no-repeat; background-size: cover; background-attachment: fixed;" src="" id="imageBpuEksternal">
    </div>

    <div class="form-group">
        <label for="berita-transfer" class="control-label">Keterangan Pembayaran/Berita Transfer :</label>
        <input type="text" class="form-control" id="keterangan_pembayaran" name="keterangan_pembayaran">
    </div>