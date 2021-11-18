<?php
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

error_reporting(0);
session_start();
if (!isset($_SESSION['nama_user'])) {
    header("location:login.php");
    // die('location:login.php');//jika belum login jangan lanjut
}
$arrCode = [];

if ($_POST['no'] && $_POST['waktu'] && $_POST['term']) {
    $id = $_POST['no'];
    $waktu = $_POST['waktu'];
    $term = $_POST['term'];


    // mengambil data berdasarkan id
    // dan menampilkan data ke dalam form modal bootstrap
    $sql = "SELECT a.*, b.jenis FROM bpu a JOIN pengajuan b ON a.waktu = b.waktu WHERE a.no = '$id' AND a.waktu = '$waktu' AND a.term = '$term' GROUP BY a.noid";
    $result = $koneksi->query($sql); ?>

    <table class="table table-bordered">
        <thead>
            <th>Nama Penerima</th>
            <th>Diajukan</th>
            <th>Total</th>
            <th>Bank</th>
            <th>No. Rekening</th>
            <th>Metode Pembayaran</th>
        </thead>

        <tbody>

            <?php foreach ($result as $baris) :
                $ketPembayaran = $baris['ket_pembayaran'];
                $statusBpu = $baris['statusbpu'];
                $jenis = $baris['jenis'];
                $fileupload = $baris['fileupload'];

                $query = mysqli_query($koneksiMriTransfer, "SELECT * FROM jenis_pembayaran WHERE jenispembayaran = '$statusBpu'") or die(mysqli_error($koneksiMriTransfer));
                $result = mysqli_fetch_assoc($query);

                if ($baris['pengajuan_jumlah'] < $result['max_transfer']) {
                    $metode_pembayaran = "MRI PAL";
                } else {
                    $metode_pembayaran = "MRI Kas";
                }

            ?>
                <tr>
                    <input type="hidden" name="noid[]" value="<?= $baris['noid'] ?>">
                    <input type="hidden" name="pengajuan_jumlah[]" value="<?= $baris['pengajuan_jumlah'] ?>">
                    <input type="hidden" name="metode_pembayaran[]" value="<?= $metode_pembayaran ?>">
                    <td><?= $baris['namapenerima'] ?></td>
                    <td class="td-pengajuan"><?= $baris['pengajuan_jumlah'] ?></td>
                    <td class="td-aktual"><?= $baris['pengajuan_jumlah'] ?></td>
                    <td><?= $baris['namabank'] ?></td>
                    <td><?= $baris['norek'] ?></td>
                    <td class="td-metode-pembayaran"><?= $metode_pembayaran ?></td>
                </tr>
            <?php
                /*
                1 -> MRI PAL (UM)
                2 -> MRI PAL (Project/Umum)
                3 -> MRI Kas (UM)
                4 -> MRI Kas (Project/Umum)
            */
                if ($baris['pengajuan_jumlah'] < $result['max_transfer']) {
                    if ($statusBpu == 'UM' || $statusBpu == 'UM Burek') {
                        array_push($arrCode, '1');
                        // $getRekening = mysqli_query($koneksiDevelop, "SELECT * FROM kas WHERE label_kas = 'Kas Uang Muka'");
                    } else {
                        array_push($arrCode, '2');
                        if ($jenis == 'B1' || $jenis == 'B2') {
                            // $getRekening = mysqli_query($koneksiDevelop, "SELECT * FROM kas WHERE label_kas = 'Kas Project'");
                        } else {
                            // $getRekening = mysqli_query($koneksiDevelop, "SELECT * FROM kas WHERE label_kas = 'Kas Umum'");
                        }
                    }
                } else {
                    if ($statusBpu == 'UM' || $statusBpu == 'UM Burek') {
                        array_push($arrCode, '3');
                        // $getRekening = mysqli_query($koneksiDevelop, "SELECT * FROM kas WHERE label_kas = 'Kas Uang Muka'");
                    } else {
                        array_push($arrCode, '4');
                        if ($jenis == 'B1' || $jenis == 'B2') {
                            // $getRekening = mysqli_query($koneksiDevelop, "SELECT * FROM kas WHERE label_kas = 'Kas Project'");
                        } else {
                            // $getRekening = mysqli_query($koneksiDevelop, "SELECT * FROM kas WHERE label_kas = 'Kas Umum'");
                        }
                    }
                    // $getRekening = mysqli_query($koneksiDevelop, "SELECT * FROM kas");
                }

            endforeach;
            ?>

        </tbody>
    </table>
    <input type="hidden" name="waktu" value="<?= $waktu ?>">
    <input type="hidden" name="no" value="<?= $id ?>">
    <input type="hidden" name="term" value="<?= $term ?>">

    <label for="">Pajak</label>
    <div class="row">
        <div class="form-group">
            <div class="col-lg-3">
                <div class="input-group">

                    <input type="checkbox" id="pph4" name="pajak" value="pph4">
                    <label for="pph4"> PPH 4 ayat 2</label><br>
                    <input type="checkbox" id="pph21" name="pajak" value="pph21">
                    <label for="pph21"> PPH 21</label><br>
                </div>
            </div>
            <div class="col-lg-3">
                <input type="checkbox" id="pph232" name="pajak" value="pph232">
                <label for="pph232"> PPH 23 (2%)</label><br>
                <input type="checkbox" id="pph234" name="pajak" value="pph234">
                <label for="pph234"> PPH 23 (4%)</label><br>

                <!-- <input type="checkbox" id="pph23" name="pajak" value="pph23">
                <label for="pph23"> PPH 23</label><br>
                <select name="pph23value" id="pph23value" style="display: none;">
                    <option value="0.02">2%</option>
                    <option value="0.04">4%</option>
                </select> -->
            </div>
        </div>

    </div>

    <input type="hidden" name="status_sumber_rekening">

    <?php if (in_array("1", $arrCode) || in_array("2", $arrCode)) : ?>
        <div class="form-group form-rekening-sumber-pal">
            <label for="rekening_sumber" class="control-label">Rekening Sumber MRI PAL:</label>
            <select class="form-control" name="rekening_sumber_mri_pal">
                <?php if (in_array("1", $arrCode)) : ?>
                    <?php $getRekening = mysqli_query($koneksiDevelop, "SELECT * FROM kas WHERE label_kas = 'Kas Uang Muka'"); ?>
                <?php else : ?>
                    <?php if ($jenis == 'B1' || $jenis == 'B2') : ?>
                        <?php $getRekening = mysqli_query($koneksiDevelop, "SELECT * FROM kas WHERE label_kas = 'Kas Project'"); ?>
                    <?php else : ?>
                        <?php $getRekening = mysqli_query($koneksiDevelop, "SELECT * FROM kas WHERE label_kas = 'Kas Umum'"); ?>
                    <?php endif; ?>
                <?php endif; ?>
                <?php while ($item = mysqli_fetch_assoc($getRekening)) : ?>
                    <option value="<?= $item['rekening'] ?>"><?= $item['rekening'] ?> - <?= $item['label_kas'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
    <?php endif; ?>

    <?php if (in_array("2", $arrCode)) : ?>
        <!-- <div class="form-group form-rekening-sumber-pal">
            <label for="rekening_sumber" class="control-label">Rekening Sumber MRI PAL: <span data-toggle="tooltip" title="Abaikan apabila tidak ada pembayaran menggunakan MRI PAL"><i class="fa fa-question-circle"></i></span></label>
            <select class="form-control" name="rekening_sumber_mri_pal">
                <?php if ($jenis == 'B1' || $jenis == 'B2') : ?>
                    <?php $getRekening = mysqli_query($koneksiDevelop, "SELECT * FROM kas WHERE label_kas = 'Kas Project'"); ?>
                <?php else : ?>
                    <?php $getRekening = mysqli_query($koneksiDevelop, "SELECT * FROM kas WHERE label_kas = 'Kas Umum'"); ?>
                <?php endif; ?>
                <?php while ($item = mysqli_fetch_assoc($getRekening)) : ?>
                    <option value="<?= $item['rekening'] ?>"><?= $item['rekening'] ?> - <?= $item['label_kas'] ?></option>
                <?php endwhile; ?>
            </select>
        </div> -->
    <?php endif; ?>

    <?php if (in_array("3", $arrCode) || in_array("4", $arrCode)) : ?>
        <div class="form-group form-rekening-sumber-kas">
            <label for="rekening_sumber" class="control-label">Rekening Sumber MRI Kas: <span data-toggle="tooltip" title="Abaikan apabila tidak ada pembayaran menggunakan MRI Kas"><i class="fa fa-question-circle"></i></span></label>
            <select class="form-control" name="rekening_sumber_mri_kas">
                <?php if (in_array("3", $arrCode)) : ?>
                    <?php $getRekening = mysqli_query($koneksiDevelop, "SELECT * FROM kas WHERE label_kas = 'KAS 3'"); ?>
                <?php else : ?>
                    <?php if ($jenis == 'B1' || $jenis == 'B2') : ?>
                        <?php $getRekening = mysqli_query($koneksiDevelop, "SELECT * FROM kas WHERE label_kas = 'KAS 1'"); ?>
                    <?php else : ?>
                        <?php $getRekening = mysqli_query($koneksiDevelop, "SELECT * FROM kas WHERE label_kas = 'KAS 2'"); ?>
                    <?php endif; ?>
                <?php endif; ?>
                <?php while ($item = mysqli_fetch_assoc($getRekening)) : ?>
                    <option value="<?= $item['rekening'] ?>"><?= $item['rekening'] ?> - <?= $item['label_kas']  . ' (' . $item['keterangan'] . ')' ?></option>
                <?php endwhile; ?>
            </select>
        </div>
    <?php endif; ?>

    <div class="form-group" id="div-berita-transfer">
        <label for="berita-transfer" class="control-label">Berita Transfer :</label>
        <input type="text" class="form-control" id="berita-transfer" name="berita_transfer" maxlength="36" title="Maks. 36 karakter">
    </div>


    <div class="form-group form-rekening-sumber">
        <label for="umo_biaya_kode_id" class="control-label">Kode Biaya :</label>
        <br>
        <select class="umo_biaya_kode_id" name="umo_biaya_kode_id" id="umo_biaya_kode_id" style="width: 100%;">
            <?php $getBiayaKode = mysqli_query($koneksiDevelop, "SELECT * FROM umo_biaya_kode"); ?>
            <?php while ($item = mysqli_fetch_assoc($getBiayaKode)) : ?>
                <option value="<?= $item['biaya_kode_id'] ?>"><?= $item['biaya_kode_nama'] ?> - <?= $item['biaya_kode_kode'] ?></option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="alasanTolakBpu" class="control-label">Alasan Penolakan (Jika ditolak):</label>
        <input type="text" class="form-control" name="alasanTolakBpu" id="alasanTolakBpu">
    </div>

    <div class="form-group">
        <label class="control-label">Upload File(Jika ada perubahan file) <span data-toggle="tooltip" title="Upload File Rincian BPU"><i class="fa fa-question-circle"></i></span></label>
        <input type="file" class="form-control" accept="image/*,application/pdf" name="gambar" id="fileInputVerifikasiBpu">
        <img class="img-responsive" style="display: block; margin-left: auto;  margin-right: auto; background-repeat: no-repeat; background-size: cover; background-attachment: fixed;" src="" alt="" id="imageVerifikasiBpu">
    </div>

    <div class="form-group">
        <p class="control-p"><b>Uploaded File</b></p>
        <img id="image" class="img-responsive" style="display: block; margin-left: auto;  margin-right: auto; background-repeat: no-repeat; background-size: cover; background-attachment: fixed;" src="uploads/<?= $fileupload ?>" alt="">
    </div>


    </form>

    <script>
        var ketPembayaran = '<?= $ketPembayaran ?>';
        var jenis = '<?= $jenis ?>';
        var maxTransfer = '<?= $result["max_transfer"] ?>';

        $('.umo_biaya_kode_id').select2();

        $('#hasilBpu').prop('readonly', true);
        $('#penerimaBpu').prop('readonly', true);
        $('#bankBpu').prop('readonly', true);
        $('#noRekBpu').prop('readonly', true);

        if (ketPembayaran) {
            $('#berita-transfer').val(ketPembayaran);
            $('#berita-transfer').attr('readonly', true);
        } else {
            $('#berita-transfer').attr('readonly', false);
        }

        $('input[name=pajak]').change(function() {
            let tdPengajuan = $('.td-pengajuan');
            let tdActual = $('.td-aktual');
            let tdMetodePembayaran = $('.td-metode-pembayaran');
            let inputPengajuan = $("input[name='pengajuan_jumlah[]']");
            let inputMetodePembayaran = $("input[name='metode_pembayaran[]']");
            // let inputMetodePembayaran = $('input[name=metode_pembayaran[]]');

            console.log(inputPengajuan)

            let result = 0;
            if ($(this).val() == 'pph21') {
                if ($(this).prop('checked')) {
                    for (let i = 0; i < tdPengajuan.length; i++) {
                        result = Math.round(parseInt(tdActual[i].textContent) - (0.05 * 0.5 * parseInt(tdPengajuan[i].textContent)));
                        tdActual[i].innerText = result

                        inputPengajuan[i].value = result

                        if (result < maxTransfer) {
                            inputMetodePembayaran[i].value = 'MRI PAL';
                            tdMetodePembayaran[i].innerText = 'MRI PAL';
                        } else {
                            inputMetodePembayaran[i].value = 'MRI Kas';
                            tdMetodePembayaran[i].innerText = 'MRI Kas';
                        }
                    }
                } else {
                    // result = Math.round(parseInt(actual) + (0.05 * 0.5 * bpu));
                    for (let i = 0; i < tdPengajuan.length; i++) {
                        result = Math.round(parseInt(tdActual[i].textContent) + (0.05 * 0.5 * parseInt(tdPengajuan[i].textContent)));
                        tdActual[i].innerText = result

                        inputPengajuan[i].value = result

                        if (result < maxTransfer) {
                            inputMetodePembayaran[i].value = 'MRI PAL';
                            tdMetodePembayaran[i].innerText = 'MRI PAL';
                        } else {
                            inputMetodePembayaran[i].value = 'MRI Kas';
                            tdMetodePembayaran[i].innerText = 'MRI Kas';
                        }
                    }
                }
            } else if ($(this).val() == 'pph4') {
                if ($(this).prop('checked')) {
                    // result = Math.round(parseInt(actual) - (0.1 * bpu));
                    for (let i = 0; i < tdPengajuan.length; i++) {
                        result = Math.round(parseInt(tdActual[i].textContent) - (0.1 * parseInt(tdPengajuan[i].textContent)));
                        tdActual[i].innerText = result
                        inputPengajuan[i].value = result

                        if (result < maxTransfer) {
                            inputMetodePembayaran[i].value = 'MRI PAL';
                            tdMetodePembayaran[i].innerText = 'MRI PAL';
                        } else {
                            inputMetodePembayaran[i].value = 'MRI Kas';
                            tdMetodePembayaran[i].innerText = 'MRI Kas';
                        }
                    }
                } else {
                    // result = Math.round(parseInt(actual) + (0.1 * bpu));
                    for (let i = 0; i < tdPengajuan.length; i++) {
                        result = Math.round(parseInt(tdActual[i].textContent) + (0.1 * parseInt(tdPengajuan[i].textContent)));
                        tdActual[i].innerText = result
                        inputPengajuan[i].value = result

                        if (result < maxTransfer) {
                            inputMetodePembayaran[i].value = 'MRI PAL';
                            tdMetodePembayaran[i].innerText = 'MRI PAL';
                        } else {
                            inputMetodePembayaran[i].value = 'MRI Kas';
                            tdMetodePembayaran[i].innerText = 'MRI Kas';
                        }
                    }
                }
            } else if ($(this).val() == 'pph232') {
                if ($(this).prop('checked')) {
                    // result = Math.round(parseInt(actual) - (0.1 * bpu));
                    for (let i = 0; i < tdPengajuan.length; i++) {
                        result = Math.round(parseInt(tdActual[i].textContent) - (0.02 * parseInt(tdPengajuan[i].textContent)));
                        tdActual[i].innerText = result
                        inputPengajuan[i].value = result

                        if (result < maxTransfer) {
                            inputMetodePembayaran[i].value = 'MRI PAL';
                            tdMetodePembayaran[i].innerText = 'MRI PAL';
                        } else {
                            inputMetodePembayaran[i].value = 'MRI Kas';
                            tdMetodePembayaran[i].innerText = 'MRI Kas';
                        }
                    }
                } else {
                    // result = Math.round(parseInt(actual) + (0.1 * bpu));
                    for (let i = 0; i < tdPengajuan.length; i++) {
                        result = Math.round(parseInt(tdActual[i].textContent) + (0.02 * parseInt(tdPengajuan[i].textContent)));
                        tdActual[i].innerText = result
                        inputPengajuan[i].value = result

                        if (result < maxTransfer) {
                            inputMetodePembayaran[i].value = 'MRI PAL';
                            tdMetodePembayaran[i].innerText = 'MRI PAL';
                        } else {
                            inputMetodePembayaran[i].value = 'MRI Kas';
                            tdMetodePembayaran[i].innerText = 'MRI Kas';
                        }
                    }
                }
            } else if ($(this).val() == 'pph234') {
                if ($(this).prop('checked')) {
                    // result = Math.round(parseInt(actual) - (0.1 * bpu));
                    for (let i = 0; i < tdPengajuan.length; i++) {
                        result = Math.round(parseInt(tdActual[i].textContent) - (0.04 * parseInt(tdPengajuan[i].textContent)));
                        tdActual[i].innerText = result
                        inputPengajuan[i].value = result

                        if (result < maxTransfer) {
                            inputMetodePembayaran[i].value = 'MRI PAL';
                            tdMetodePembayaran[i].innerText = 'MRI PAL';
                        } else {
                            inputMetodePembayaran[i].value = 'MRI Kas';
                            tdMetodePembayaran[i].innerText = 'MRI Kas';
                        }
                    }
                } else {
                    // result = Math.round(parseInt(actual) + (0.1 * bpu));
                    for (let i = 0; i < tdPengajuan.length; i++) {
                        result = Math.round(parseInt(tdActual[i].textContent) + (0.04 * parseInt(tdPengajuan[i].textContent)));
                        tdActual[i].innerText = result
                        inputPengajuan[i].value = result

                        if (result < maxTransfer) {
                            inputMetodePembayaran[i].value = 'MRI PAL';
                            tdMetodePembayaran[i].innerText = 'MRI PAL';
                        } else {
                            inputMetodePembayaran[i].value = 'MRI Kas';
                            tdMetodePembayaran[i].innerText = 'MRI Kas';
                        }
                    }
                }
            }
            // else if ($(this).val() == 'pph23') {
            //     if ($(this).prop('checked')) {
            //         $('#pph23value').show();
            //         // result = Math.round(parseInt(actual) - ($('#pph23value').val() * bpu));

            //         for (let i = 0; i < tdPengajuan.length; i++) {
            //             result = Math.round(parseInt(tdActual[i].textContent) + ($('#pph23value').val() * parseInt(tdPengajuan[i].textContent)));
            //             tdActual[i].innerText = result
            //         }
            //         $('#pph23value').change(function() {
            //             // result = Math.round(parseInt(actual) - ($(this).val() * bpu));
            //             for (let i = 0; i < tdPengajuan.length; i++) {
            //                 result = Math.round(parseInt(tdActual[i].textContent) + ($(this).val() * parseInt(tdPengajuan[i].textContent)));
            //                 tdActual[i].innerText = result
            //             }
            //         })
            //     } else {
            //         $('#pph23value').hide();
            //         // result = Math.round(parseInt(actual) + ($('#pph23value').val() * bpu));
            //         for (let i = 0; i < tdPengajuan.length; i++) {
            //             result = Math.round(parseInt(tdActual[i].textContent) + ($('#pph23value').val() * parseInt(tdPengajuan[i].textContent)));
            //             tdActual[i].innerText = result
            //         }
            //     }
            // }
        })

        // $('#image').attr('src', `uploads/${file}`)
    </script>
<?php
}
$koneksi->close();
?>