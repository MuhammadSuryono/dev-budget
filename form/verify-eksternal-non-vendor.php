<?php 
$path = "eksternalproses-new.php?action=update&id-bpu=".$idBpu."&id-verify=".$idVerify;
$tanggalCair = $dataBpu['tglcair'];

if ($tanggalCair == "0000-00-00") {
    $tanggalCair = $dataBpu['tanggalbayar'];
}

?>
<form action="<?= $path ?>" method="post">
    <input type="hidden" name="no" value="<?= $dataBpu['no'] ?>" />
    <input type="hidden" name="divisi" value="<?= $dataBpu['divisi'] ?>" />
    <input type="hidden" name="pengaju" value="<?= $dataBpu['pengaju'] ?>" />
    <input type="hidden" name="waktu" value="<?= $dataBpu['waktu'] ?>" />
    <input type="hidden" name="term" value="<?= $dataBpu['term'] ?>" />
<div style="display: flex; justify-content: end; margin-bottom: 10px;">
        <button type="button" class="btn btn-sm btn-primary btn-tambah-row">Tambah Penerima</button>
    </div>

    <div class="row-penerima-honor">
        <div class="row">
            <div class="col-lg-4">
                <div class="form-group">
                    <label for="rincian" class="control-label">Total BPU (IDR) :</label>
                    <input class="form-control" name="jumlah[]" type="number">
                </div>
            </div>

            <div class="col-lg-4">
                <div class="form-group">
                    <!-- <span style="float: right; margin-bottom: 5px;"><button type="button" class="btn btn-sm btn-primary btn-tambah-penerima">Tambah Penerima</button></span> -->
                    <label for="namapenerima" class="control-label">Nama Penerima: </label>
                    <input type="text" class="form-control" name="namapenerima[]" required>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="form-group">
                    <label for="email" class="control-label">Email :</label>
                    <input type="email" class="form-control" name="email[]" required>
                </div>
            </div>
        </div>
        <div class="row">
        <div class="col-lg-4">
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

            <div class="col-lg-4">
                <div class="form-group">
                    <label for="namapenerima" class="control-label">Nama Rekening: </label>
                    <input type="text" class="form-control" name="bank_account_name[]" required>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="form-group">
                    <label for="norek" class="control-label">No. Rekening :</label>
                    <input type="number" class="form-control" name="norek[]" required>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="tglcair" class="control-label">Tanggal Permintaan Pencairan :</label>
        <input type="date" class="form-control" value="<?= $tanggalCair ?>" name="tglcair">
    </div>

    <div class="form-group">
        <label for="berita-transfer" class="control-label">Keterangan Pembayaran/Berita Transfer :</label>
        <input type="text" class="form-control" id="keterangan_pembayaran" value="<?= $dataBpu['ket_pembayaran'] ?>" name="keterangan_pembayaran">
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
    $(document).ready(function() {
        $('.btn-tambah-row').click(function() {
            var count = $('.row-penerima-honor').length;

            html = `
            <div class="row-penerima">
<div class="row"><button type="button" style="float: right" class="btn btn-sm btn-danger btn-hapus-row">Hapus Penerima</button></div>
            <div class="row">
            <div class="col-lg-4">
                <div class="form-group">
                    <label for="rincian" class="control-label">Total BPU (IDR) :</label>
                    <input class="form-control" name="jumlah[]" type="number">
                </div>
            </div>

            <div class="col-lg-4">
                <div class="form-group">
                    <!-- <span style="float: right; margin-bottom: 5px;"><button type="button" class="btn btn-sm btn-primary btn-tambah-penerima">Tambah Penerima</button></span> -->
                    <label for="namapenerima" class="control-label">Nama Penerima: </label>
                    <input type="text" class="form-control" name="namapenerima[]" required>
                </div>
            </div>


            <div class="col-lg-4">
                <div class="form-group">
                    <label for="email" class="control-label">Email :</label>
                    <input type="email" class="form-control" name="email[]" required>
                </div>
            </div>
        </div>
        <div class="row">
        <div class="col-lg-4">
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

            <div class="col-lg-4">
                <div class="form-group">
                    <label for="namapenerima" class="control-label">Nama Rekening: </label>
                    <input type="text" class="form-control" name="bank_account_name[]" required>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="form-group">
                    <label for="norek" class="control-label">No. Rekening :</label>
                    <input type="number" class="form-control" name="norek[]" required>
                </div>
            </div>
        </div>
</div>
          `;

            $('.row-penerima-honor').after(html);
        })
    });

    $(document).on('click', '.btn-hapus-penerima', function() {
        $(this).parent().parent().parent().remove();
    })
    $(document).on('click', '.btn-hapus-row', function() {
        console.log($(this).parent())
        $(this).closest('.row-penerima').remove();
    })

    $(document).ready(function() {
        $('.btn-tambah-penerima').click(function() {
            var count = $('.sub-penerima').length;
            console.log("JALAN")
            html = `
          
          <div class="sub-penerima">
          <div class="form-group">
          <span style="float: right; margin-bottom: 5px;"><button type="button" class="btn btn-sm btn-danger btn-hapus-penerima">Hapus Penerima</button></span>
            <label for="namapenerima" class="control-label">Nama Penerima:</label>
            <input type="text" class="form-control" name="namapenerima[]" required>
          </div>
          
          <div class="form-group">
              <label for="email" class="control-label">Email :</label>
              <input type="email" class="form-control" name="email[]" required>
            </div>  

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

          <div class="form-group">
            <label for="norek" class="control-label">Nomor Rekening :</label>
            <input type="number" class="form-control" name="norek[]" required>
          </div>
        </div>
        </div>
          `;

            $('.div-penerima').append(html);
        })
    });
</script>