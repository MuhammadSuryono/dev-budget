<?php
session_start();

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

if (!isset($_SESSION['nama_user'])) {
    header("location:login.php");
    // die('location:login.php');//jika belum login jangan lanjut
}

if ($_POST['no'] && $_POST['waktu'] && $_POST['term']) {

    $id = $_POST['no'];
    $waktu = $_POST['waktu'];
    $term = $_POST['term'];

    $queryItemBudget = mysqli_query($koneksi, "SELECT * FROM selesai WHERE no = '$id' AND waktu = '$waktu'");
    $baris = mysqli_fetch_assoc($queryItemBudget);

    $querySingleBpu = mysqli_query($koneksi, "SELECT * FROM bpu WHERE no = '$id' AND waktu = '$waktu' AND term = '$term'");
    $bpu = mysqli_fetch_assoc($querySingleBpu); ?>

    <input type="hidden" name="waktu" value="<?= $bpu['waktu'] ?>">
    <?php
    if ($baris['status'] == 'Honor Eksternal') :
        $queryBpu = mysqli_query($koneksi, "SELECT * FROM bpu WHERE no = '$id' AND waktu = '$waktu' AND term = '$term'");
        $i = 0;
    ?>
        <?php while ($item = mysqli_fetch_assoc($queryBpu)) : ?>
            <input type="hidden" name="noid[]" value="<?= $item['noid'] ?>">
            <?php if ($i == 0) : ?>
                <!-- <div style="display: flex; justify-content: end; margin-bottom: 10px;">
                    <button type="button" class="btn btn-sm btn-primary btn-tambah-row">Tambah Penerima</button>
                </div> -->
            <?php else : ?>
                <!-- <div style="display: flex; justify-content: end; margin-bottom: 10px;">
                    <button type="button" class="btn btn-sm btn-danger btn-hapus-row">Hapus Penerima</button>
                </div> -->
            <?php endif; ?>
            <div class="row <?= ($i == 0) ? "row-penerima-honor" : "row-penerima" ?>">
                <div class="col-lg-2">
                    <div class="form-group">
                        <label for="rincian" class="control-label">Total BPU (IDR) :</label>
                        <input class="form-control" name="jumlah[]" type="number" value="<?= $item['pengajuan_jumlah'] ?>">
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="form-group">
                        <!-- <span style="float: right; margin-bottom: 5px;"><button type="button" class="btn btn-sm btn-primary btn-tambah-penerima">Tambah Penerima</button></span> -->
                        <label for="namapenerima" class="control-label">Nama Penerima: </label>
                        <input type="text" class="form-control" name="namapenerima[]" value="<?= $item['namapenerima'] ?>" required>
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="form-group">
                        <label for="email" class="control-label">Email :</label>
                        <input type="email" class="form-control" name="email[]" value="<?= $item['emailpenerima'] ?>" required>
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
                                <option value="<?= $db['kodebank'] ?>" <?= ($db['kodebank'] == $item['namabank']) ? 'selected' : '' ?>><?= $db['namabank'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div class="col-lg-2">
                    <div class="form-group">
                        <label for="norek" class="control-label">No. Rekening :</label>
                        <input type="number" class="form-control" name="norek[]" value="<?= $item['norek'] ?>" required>
                    </div>
                </div>
            </div>
        <?php
            $i++;
        endwhile; ?>

        <div class="form-group">
            <label for="tglcair" class="control-label">Tanggal Permintaan Pencairan :</label>
            <input type="date" class="form-control" name="tglcair" value="<?= $bpu['tglcair'] ?>">
        </div>

        <div class="form-group form-group-file">
            <label class="control-label">Upload File <a href="#" data-toggle="tooltip" title="Upload File Rincian BPU"><i class="fa fa-question-circle"></i></a></label>
            <input type="file" class="form-control" accept="image/*" name="gambar" id="fileInputEksternal">
            <img class="img-responsive" style="display: block; margin-left: auto;  margin-right: auto; background-repeat: no-repeat; background-size: cover; background-attachment: fixed;" src="uploads/<?= $bpu['fileupload'] ?>" id="imageBpuEksternal">
        </div>

        <div class="form-group">
            <label for="berita-transfer" class="control-label">Keterangan Pembayaran/Berita Transfer :</label>
            <input type="text" class="form-control" id="keterangan_pembayaran" name="keterangan_pembayaran" value="<?= $bpu['ket_pembayaran'] ?>">
        </div>

    <?php else : ?>

        <input type="hidden" name="noid" value="<?= $bpu['noid'] ?>">
        <div class="form-group">
            <label for="rincian" class="control-label">Total BPU (IDR) :</label>
            <input class="form-control" name="jumlah" type="number" value="<?= $bpu['pengajuan_jumlah'] ?>">
        </div>

        <div class="form-group">
            <label for="tglcair" class="control-label">Tanggal Permintaan Pencairan :</label>
            <input type="date" class="form-control" name="tglcair" value="<?= $bpu['tglcair'] ?>">
        </div>

        <div class="form-group form-group-file">
            <label class="control-label">Upload File <a href="#" data-toggle="tooltip" title="Upload File Rincian BPU"><i class="fa fa-question-circle"></i></a></label>
            <input type="file" class="form-control" accept="image/*" name="gambar" id="fileInputEksternal">
            <img class="img-responsive" style="display: block; margin-left: auto;  margin-right: auto; background-repeat: no-repeat; background-size: cover; background-attachment: fixed;" src="uploads/<?= $bpu['fileupload'] ?>" id="imageBpuEksternal">
        </div>

        <div class="div-penerima">
            <div class="sub-penerima">
                <div class="form-group">
                    <label for="namapenerima" class="control-label">Nama Penerima: </label>
                    <input type="text" class="form-control" name="namapenerima" value="<?= $bpu['namapenerima'] ?>" required>
                </div>

                <div class="form-group">
                    <label for="email" class="control-label">Email :</label>
                    <input type="email" class="form-control" name="email" value="<?= $bpu['emailpenerima'] ?>" required>
                </div>

                <div class="form-group">
                    <label for="namabank" class="control-label">Nama Bank :</label>
                    <select class="form-control" name="namabank" required>
                        <option value="" selected disabled>Pilih Kategori</option>
                        <?php
                        $queryDaftarBank = mysqli_query($koneksi, 'SELECT * FROM bank');
                        while ($db = mysqli_fetch_assoc($queryDaftarBank)) :
                        ?>
                            <option value="<?= $db['kodebank'] ?>" <?= ($db['kodebank'] == $bpu['namabank']) ? 'selected' : '' ?>><?= $db['namabank'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="norek" class="control-label">Nomor Rekening :</label>
                    <input type="number" class="form-control" name="norek" value="<?= $bpu['norek'] ?>" required>
                </div>
            </div>
        </div>

        <?php if ($baris['status'] == 'Vendor/Supplier') : ?>
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
    <?php endif;
    endif; ?>

    <div class="form-group">
        <label for="alasan_tolak_bpu" class="control-label">Alasan Penolakan :</label>
        <input type="text" class="form-control" id="alasan_tolak_bpu" name="alasan_tolak_bpu" value="<?= $bpu['alasan_tolak_bpu'] ?>" disabled>
    </div>
<?php
}
?>

<script>
    $(document).on('click', '.btn-hapus-row', function() {
        $(this).closest('.row-penerima').remove();
    });

    $(document).ready(function() {
        $('.btn-tambah-row').click(function() {
            var count = $('.row-penerima-honor').length;

            html = `
            <div class="row row-penerima">
                <div style="display: flex; justify-content: end; margin-bottom: 10px; margin-right:20px">
                <button type="button" class="btn btn-sm btn-danger btn-hapus-row">Hapus Penerima</button>
                </div>
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
          `;

            $('.row-penerima-honor').after(html);
        })
    });
</script>