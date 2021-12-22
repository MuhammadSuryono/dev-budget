<div class="form-group">
        <label for="rincian" class="control-label">Total BPU (IDR) :</label>
        <input class="form-control" name="jumlah" type="number">
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

    <!-- <div class="form-group form-group-file">
        <label class="control-label">Upload File <a href="#" data-toggle="tooltip" title="Upload File Rincian BPU"><i class="fa fa-question-circle"></i></a></label>
        <input type="file" class="form-control" accept="image/*" name="gambar" id="fileInputEksternal" required>
        <img class="img-responsive" style="display: block; margin-left: auto;  margin-right: auto; background-repeat: no-repeat; background-size: cover; background-attachment: fixed;" src="" id="imageBpuEksternal">
    </div> -->

    <div class="div-penerima">
        <div class="sub-penerima">
            <div class="form-group">
                <!-- <span style="float: right; margin-bottom: 5px;"><button type="button" class="btn btn-sm btn-primary btn-tambah-penerima">Tambah Penerima</button></span> -->
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
        </div>
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

    <img src="" alt="">