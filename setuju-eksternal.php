<?php
error_reporting(0);
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

$con->set_name_db(DB_DEVELOP);
$con->init_connection();
$koneksiDevelop = $con->connect();

$con->set_name_db(DB_MRI_TRANSFER);
$con->init_connection();
$koneksiMriTransfer = $con->connect();


session_start();
if (!isset($_SESSION['nama_user'])) {
    header("location:login.php");
    // die('location:login.php');//jika belum login jangan lanjut
}

if ($_POST['no'] && $_POST['waktu'] && $_POST['term']) {
    $id = $_POST['no'];
    $waktu = $_POST['waktu'];
    $term = $_POST['term'];

    $getRekening = mysqli_query($koneksiDevelop, "SELECT * FROM kas WHERE stat = 'MRI'");

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
                $pengaju = $baris['pengaju'];
                $ket_pembayaran = $baris['ket_pembayaran'];

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

    <ul class="list-group">
        <li class="list-group-item">Pengaju : <b><?= $pengaju ?></b></li>
        <li class="list-group-item">Berita Transfer : <b><?= $ket_pembayaran ?></b></li>
    </ul>

    <!-- MEMBUAT FORM -->
    <input type="hidden" name="no" value="<?php echo $id ?>">
    <input type="hidden" name="waktu" value="<?php echo $waktu ?>">
    <input type="hidden" name="term" value="<?php echo $term ?>">
    <input type="hidden" name="persetujuan" value="Sudah Disetujui">

    <p>Apakah anda ingin menyetujui <b>BPU</b> di Nomor <b><?= $baris['no']; ?></b>?</p>

    <div class="form-group">
        <label for="tglbayar" class="control-label">Tanggal Pembayaran :</label>
        <input type="date" class="form-control" id="tglbayar" name="tanggalbayar" min="<?= date('Y-m-d', strtotime($Date . ' + 2 days')) ?>" required>
    </div>

    <div class="form-group">
        <label for="tglcair" class="control-label">Status Urgent :</label>
        <select class="form-control" name="urgent">
            <option value="Not Urgent" selected>-</option>
            <option value="Urgent">Urgent</option>
        </select>
    </div>

    <div class="form-group">
        <label for="alasanTolakBpu" class="control-label">Alasan Penolakan (Jika ditolak):</label>
        <input type="text" class="form-control" name="alasanTolakBpu" id="alasanTolakBpu">
    </div>


<script>
    var ketPembayaran = '<?= $ketPembayaran ?>';
    var jenis = '<?= $jenis ?>';
    var maxTransfer = '<?= $result["max_transfer"] ?>';
    const picker = document.getElementById('tglbayar');
    picker.addEventListener('input', function(e) {
        var day = new Date(this.value).getUTCDay();
        if ([6, 0].includes(day)) {
            e.preventDefault();
            this.value = '';
            alert('Weekends not allowed');
        }
    });
    $(document).on('change', '#metode_pembayaran', function() {
        if ($(this).val() == 'MRI PAL') {
            $('.form-rekening-sumber').show();
        } else {
            $('.form-rekening-sumber').hide();

        }
        console.log('here');
    })

    function convertToRupiah(number) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IND' }).format(number)
    }

    function countPph(valPph, totalData) {
        let dpp = 1.1
        let pph = valPph
        let ppn = 0.1

        let resDpp = totalData / dpp
        let resPPh = resDpp * pph
        let resPpn = resDpp * ppn

        let totalAfterPph = Math.round((resDpp - resPPh) + resPpn)

        return Math.round(totalData - totalAfterPph)
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
                    
                    for (let i = 0; i < tdPengajuan.length; i++) {
                        let totalData = parseInt(tdPengajuan[i].textContent)
                        let selisih = result = countPph(0.02, totalData)
                        result = Math.round(parseInt(tdActual[i].textContent) - selisih);
                        
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
                    for (let i = 0; i < tdPengajuan.length; i++) {
                        
                        let totalData = parseInt(tdPengajuan[i].textContent)
                        let selisih = result = countPph(0.02, totalData)
                        result = Math.round(parseInt(tdActual[i].textContent) + selisih);
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
                        
                        let totalData = parseInt(tdPengajuan[i].textContent)
                        let selisih = result = countPph(0.04, totalData)
                        result = Math.round(parseInt(tdActual[i].textContent) - selisih);
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
                        
                        let totalData = parseInt(tdPengajuan[i].textContent)
                        let selisih = result = countPph(0.04, totalData)
                        result = Math.round(parseInt(tdActual[i].textContent) + selisih);
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
</script>
<?php
}
$koneksi->close();
?>