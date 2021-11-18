<?php
session_start();

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

if (!isset($_SESSION['nama_user'])) {
    header("location:login.php");
    // die('location:login.php');//jika belum login jangan lanjut
}


if ($_POST['no'] && $_POST['waktu']) {
    $id = $_POST['no'];
    $waktu = $_POST['waktu'];
    $tanggal = date("Y-m-d");

    // mengambil data berdasarkan id
    // dan menampilkan data ke dalam form modal bootstrap
    $sql = "SELECT * FROM selesai WHERE no = '$id' AND waktu = '$waktu'";
    $result = $koneksi->query($sql);
    foreach ($result as $baris) {
?>

        <!-- MEMBUAT FORM -->
        <form action="eksternalproses.php" method="post" name="Form" onsubmit="return validateForm()" enctype="multipart/form-data">

            <input type="hidden" name="no" value="<?php echo $baris['no']; ?>">
            <input type="hidden" name="waktu" value="<?php echo $baris['waktu']; ?>">
            <input type="hidden" name="pengaju" value="<?php echo $_SESSION['nama_user']; ?>">
            <input type="hidden" name="divisi" value="<?php echo $_SESSION['divisi']; ?>">
            <input type="hidden" name="statusbpu" value="<?php echo $baris['status']; ?>">

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

            <div class="div-penerima">
                <div class="sub-penerima">
                    <div class="form-group">
                        <!-- <span style="float: right; margin-bottom: 5px;"><button type="button" class="btn btn-sm btn-primary btn-tambah-penerima">Tambah Penerima</button></span> -->
                        <label for="namapenerima" class="control-label">Nama Penerima: </label>
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
            <?php else : ?>
                <div class="form-group">
                    <label for="berita-transfer" class="control-label">Keterangan Pembayaran/Berita Transfer :</label>
                    <input type="text" class="form-control" id="keterangan_pembayaran" name="keterangan_pembayaran">
                </div>
            <?php endif; ?>


            <?php
            if ($_SESSION['divisi'] == 'FINANCE') {
            ?>
                <!-- <div class="form-group">
          <label class="control-label">Upload File <a href="#" data-toggle="tooltip" title="Upload File Rincian BPU"><i class="fa fa-question-circle"></i></a></label>
          <input type="file" class="form-control" accept="image/*" name="gambar" id="fileInput">
        </div> -->
                </div>
            <?php
            } else {
                echo "";
            }
            ?>
            <img src="" alt="">

            <button class="btn btn-primary" type="submit" name="submit">OK</button>

        </form>
        <script>
            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        $('img').remove();
                        // $('#imageBpuEksternal').attr('src', e.target.result);
                        $html = `
              <img src="${e.target.result}" class="img-responsive" style="display: block; margin-left: auto;  margin-right: auto; background-repeat: no-repeat; background-size: cover; background-attachment: fixed;"">
            `
                        $('.form-group-file').append($html);
                    }

                    reader.readAsDataURL(input.files[0]); // convert to base64 string
                }
            }
            $(document.body).delegate('#fileInputEksternal', 'change', function() {
                // console.log(this);
                readURL(this);
                // alert('The option with value ' + $(this).val());
            });
        </script>
        <?php break; ?>


<?php }
}
?>
<script>
    $(document).on('click', '.btn-hapus-penerima', function() {
        $(this).parent().parent().parent().remove();
    })

    $(document).ready(function() {
        $('.btn-tambah-penerima').click(function() {
            var count = $('.sub-penerima').length;
            console.log(count);

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

<?php
$koneksi->close();
?>