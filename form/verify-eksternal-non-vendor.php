<?php 
$path = "eksternalproses-new.php?action=update&id-bpu=".$idBpu."&id-verify=".$idVerify;
$tanggalCair = $dataBpu['tglcair'];

$queryDataPenerima = mysqli_query($koneksi, "SELECT namapenerima, emailpenerima, norek, namabank, bank_account_name, vendor_type FROM bpu WHERE namapenerima NOT IN ('TLF', '') GROUP BY namapenerima");
$dataPenerima = [];
while ($row = $queryDataPenerima->fetch_assoc()) {
    $dataPenerima[] = $row;
}

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
                    <input type="text" list="brow" class="form-control name" onchange="onChangeName(this)" name="namapenerima[]" required>
                    <datalist id="brow" class="brow">
                    <?php
                        foreach ($dataPenerima as $value) {
                            echo '<option email="'.$value['emailpenerima'].'" norek="'.$value['norek'].'" bank="'.$value['namabank'].'" bank_account="'.$value['bank_account_name'].'" value="'.$value['namapenerima'].'" vendor_type="'.$value['vendor_type'].'">';
                        }
                    ?>
                    </datalist>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="form-group">
                    <label for="email" class="control-label">Email :</label>
                    <input type="email" class="form-control email" name="email[]" required>
                </div>
            </div>
        </div>
        <div class="row">

        <div class="col-lg-4">
            <div class="form-group">
                <label for="norek" class="control-label">No. Rekening :</label>
                <input type="number" class="form-control norek" onchange="onChangeNorek(this)" name="norek[]" required>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="form-group">
                <label for="namabank" class="control-label">Nama Bank :</label>
                <select class="form-control bank" name="namabank[]" required readonly>
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
                    <input type="text" class="form-control nama-norek" name="bank_account_name[]" required>
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
    <?php if ($dataPengajuan['jenis'] != 'Rutin' && $_SESSION['hak_akses'] == 'Pegawai2' && $_SESSION['level'] == 'Koordinator') { ?>
        <button class="btn btn-primary" type="submit" name="submit">Simpan</button>
    <?php } ?>
</form>

<script>
    var optionBank = document.getElementsByClassName("bank");
    var emailPenerima = document.getElementsByClassName("email");
    var namaRekeningPenerima = document.getElementsByClassName("nama-norek");
    var dataListSelected = document.getElementsByClassName("brow");
    var norek = document.getElementsByClassName("norek");
    function onChangeName(elem) {
        let indexElement = undefined;
        $('.name').each(function(index, elm) {
            if (elem.value === elm.value) {
                indexElement = index
            }
        });

        let listSelected = dataListSelected[indexElement]
        let optionBanks = optionBank[indexElement]
        for (let i = 0; i < listSelected.childElementCount; i++) {
            if (listSelected.children[i].attributes.value.value == elem.value) {
                emailPenerima[indexElement].value = listSelected.children[i].attributes.email.value
                norek[indexElement].value = listSelected.children[i].attributes.norek.value
                namaRekeningPenerima[indexElement].value = listSelected.children[i].attributes.bank_account.value

                if (listSelected.children[i].attributes.bank.value !== "") {
                    for (let j = 0; j < optionBanks.childElementCount; j++) {
                        const element = optionBanks[j];
                        
                        if (element.value == listSelected.children[i].attributes.bank.value) {
                            element.selected = true
                        }
                    }
                }

                if (listSelected.children[i].attributes.vendor_type.value !== "") {
                    for (let k = 0; k < optionVendorType.childElementCount; k++) {
                        const element = optionVendorType[k];

                        if (element.value == listSelected.children[i].attributes.vendor_type.value) {
                            element.selected = true
                        }
                    }
                }
            }
        }
    }

    function onChangeNorek(e) {
        let indexElement = undefined;
        $('.norek').each(function(index, elm) {
            if (e.value === elm.value) {
                indexElement = index
            }
        });

        let fifthCharacter = ""
        let fourthCharacter = ""
        let secondCharacter = ""
        let thirdCharacter = ""

        if (e.value.length > 4) {
            fifthCharacter = e.value.substring(0, 5)
        }

        if (e.value.length > 3) {
            fourthCharacter = e.value.substring(0, 4)
        }

        if (e.value.length > 1) {
            secondCharacter = e.value.substring(0, 1)
        }

        if (e.value.length > 2) {
            thirdCharacter = e.value.substring(0, 3)
        }

        for (let index = 0; index < optionBank[indexElement].childElementCount; index++) {
            const element = optionBank[indexElement].children[index];
            
            if (e.value.length === 13 && element.value === "BMRIIDJA") {
                element.selected = true
            }

            if (e.value.length === 16 && fifthCharacter === "88708" && element.value === "BMRIIDJA") {
                element.selected = true
            }

            if (e.value.length === 10 && (fourthCharacter === "0427" || secondCharacter === "002") && element.value === "BNINIDJA") {
                element.selected = true
            }

            // IBBKIDJA
            if (e.valu.length === 10 && (thirdCharacter === "223" || thirdCharacter === "221") && element.value === "IBBKIDJA") {
                element.selected = true
            }

            if (e.value.length === 10 && element.value === "CENAIDJA") {
                element.selected = true
            }

            if (e.value.length === 15 && fifthCharacter === "88708" && element.value === "BMRIIDJA") {
                element.selected = true
            }

            if (e.value.length === 18 && element.value === "BMRIIDJA") {
                element.selected = true
            }

            if (e.value.length === 15 && (thirdCharacter === "025" || thirdCharacter === "225" || thirdCharacter === "018") && element.value === "BMRIIDJA") {
                element.selected = true
            }

            if (e.value.length === 15 && element.value === "BRINIDJA") {
                element.selected = true
            }

            if ((e.value.length !== 13 || e.value.length !== 16 || e.value.length !== 10 || e.value.length !== 15) && element.value === "") {
                element.selected = true
            }
            
        }
    }
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
                    <input type="text" list="brow" class="form-control name" onchange="onChangeName(this)" name="namapenerima[]" required>
                    <datalist id="brow" class="brow">
                    <?php
                        foreach ($dataPenerima as $value) {
                            echo '<option email="'.$value['emailpenerima'].'" norek="'.$value['norek'].'" bank="'.$value['namabank'].'" bank_account="'.$value['bank_account_name'].'" value="'.$value['namapenerima'].'" vendor_type="'.$value['vendor_type'].'">';
                        }
                    ?>
                    </datalist>
                </div>
            </div>


            <div class="col-lg-4">
                <div class="form-group">
                    <label for="email" class="control-label">Email :</label>
                    <input type="email" class="form-control email" name="email[]" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4">
                <div class="form-group">
                    <label for="norek" class="control-label">No. Rekening :</label>
                    <input type="number" class="form-control norek" onchange="onChangeNorek(this)" name="norek[]" required>
                </div>
            </div>
        <div class="col-lg-4">
            <div class="form-group">
                <label for="namabank" class="control-label">Nama Bank :</label>
                <select class="form-control bank" name="namabank[]" required readonly>
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
                    <input type="text" class="form-control nama-norek" name="bank_account_name[]" required>
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