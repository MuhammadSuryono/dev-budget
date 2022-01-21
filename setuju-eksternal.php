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
}

if ($_POST['no'] && $_POST['waktu'] && $_POST['term']) {
    $id = $_POST['no'];
    $waktu = $_POST['waktu'];
    $term = $_POST['term'];

    $getRekening = mysqli_query($koneksiDevelop, "SELECT * FROM kas WHERE stat = 'MRI'");

    $sql = "SELECT a.*, b.jenis FROM bpu a JOIN pengajuan b ON a.waktu = b.waktu WHERE a.no = '$id' AND a.waktu = '$waktu' AND a.term = '$term' GROUP BY a.noid";
    $dataBpuQuery = mysqli_query($koneksi, $sql);
    $dataBpu = mysqli_fetch_assoc($dataBpuQuery);
    $statusBpu = $dataBpu['statusbpu'];
    $namaVendor = $dataBpu['nama_vendor'];
    $typeVendor = $dataBpu['vendor_type'];
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
                $resultJenisPembayaran = mysqli_fetch_assoc($query);

                if ($baris['pengajuan_jumlah'] < $resultJenisPembayaran['max_transfer']) {
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
                    <?php if($statusBpu == 'Vendor/Supplier' && $typeVendor == 'perorangan') { ?>
                    <input type="checkbox" id="pph21" name="pajak" value="pph21">
                    <label for="pph21"> PPH 21 (2.5%)</label><br>
                    <input type="checkbox" id="pph213" name="pajak" value="pph213">
                    <label for="pph213"> PPH 21 (3%)</label><br>
                    <?php } ?>
                </div>
            </div>
            <div class="col-lg-3">
                <?php if($statusBpu == 'Vendor/Supplier' && $typeVendor == 'perseroan') { ?>
                <input type="checkbox" id="pph232" name="pajak" value="pph232">
                <label for="pph232"> PPH 23 (2%)</label><br>
                <input type="checkbox" id="pph234" name="pajak" value="pph234">
                <label for="pph234"> PPH 23 (4%)</label><br>
                <?php } ?>
            </div>
        </div>

    </div>

    <ul class="list-group">
    <?php if($statusBpu == 'Vendor/Supplier') { ?>
        <li class="list-group-item">Nama Vendor : <b><?= $namaVendor ?></b></li>
        <li class="list-group-item">Jenis Vendor : <b><?= strtoupper($typeVendor) ?></b></li>
    <?php } ?>
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
        <input type="date" class="form-control" id="tglbayar" name="tanggalbayar" value="<?= $dataBpu['tanggalbayar'] ?>" min="<?= $dataBpu['tanggalbayar'] == '' ? date('Y-m-d', strtotime($Date . ' + 2 days')) : $dataBpu['tanggalbayar'] ?>">
    </div>

    <div class="form-group">
        <label for="tglcair" class="control-label">Status Urgent :</label>
        <select class="form-control" name="urgent" onchange="onChangeStatusUrgent(this)">
            <option value="Not Urgent" selected>Not Urgent</option>
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
    var typeVendor = '<?= $typeVendor ?>';
    var maxTransfer = '<?= $resultJenisPembayaran["max_transfer"] ?>';
    var tanggalBayar = '<?= $dataBpu['tanggalbayar'] ?>'
    const inputDate = document.getElementById("tglbayar")

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
        let resPpn = typeVendor == 'perseroan' ? resDpp * ppn : 0

        let totalAfterPph = Math.round((resDpp - resPPh) + resPpn)

        return Math.round(totalData - totalAfterPph)
    }

    function onChangeStatusUrgent(e) {
        if (e.value === "Urgent") {
            inputDate.value = formatDate("yyyy-mm-dd")
            inputDate.setAttribute("min", formatDate("yyyy-mm-dd"))
        } else {
            inputDate.value = tanggalBayar
            inputDate.setAttribute("min", tanggalBayar)
        }
    }

    function formatDate(format) {
        const date = new Date();
        let month = date.getMonth() + 1;
        let day = date.getDate()
        let year = date.getFullYear()
        let singleMonth = [1,2,3,4,5,6,7,8,9]

        month = singleMonth.includes(month) ? "0" + month : month
        return `${year}-${month}-${day}`
    }

    $('input[name=pajak]').change(function() {
            let tdPengajuan = $('.td-pengajuan');
            let tdActual = $('.td-aktual');
            let tdMetodePembayaran = $('.td-metode-pembayaran');
            let inputPengajuan = $("input[name='pengajuan_jumlah[]']");
            let inputMetodePembayaran = $("input[name='metode_pembayaran[]']");
            // let inputMetodePembayaran = $('input[name=metode_pembayaran[]]');

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
            } else if ($(this).val() == 'pph213') {
                if ($(this).prop('checked')) {
                    for (let i = 0; i < tdPengajuan.length; i++) {
                        result = Math.round(parseInt(tdActual[i].textContent) - (0.03 * parseInt(tdPengajuan[i].textContent)));
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
                        result = Math.round(parseInt(tdActual[i].textContent) + (0.03 * parseInt(tdPengajuan[i].textContent)));
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
        })
</script>
<?php
}
$koneksi->close();
?>