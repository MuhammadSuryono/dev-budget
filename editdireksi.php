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
  $noidbpu = $_POST['noidbpu'];
  $id = $_POST['no'];
  $waktu = $_POST['waktu'];
  $term = $_POST['term'];


  // mengambil data berdasarkan id
  // dan menampilkan data ke dalam form modal bootstrap
  $queryItem = mysqli_query($koneksi, "SELECT * FROM selesai WHERE waktu = '$waktu' AND no = '$id'");
  $item = mysqli_fetch_assoc($queryItem);

    $queryBpu = "SELECT SUM(CASE WHEN jumlah > 0 THEN jumlah ELSE pengajuan_jumlah END) as total FROM bpu WHERE no = '$id' AND waktu = '$waktu'";
    $mysqlQuery = mysqli_query($koneksi, $queryBpu);

    $total = 0;
    while($row = mysqli_fetch_assoc($mysqlQuery)) {
        $total = $row['total'];
    }

  $sql = "SELECT * FROM bpu WHERE noid='$noidbpu' AND no = '$id' AND waktu = '$waktu' AND term = '$term'";
  $result = $koneksi->query($sql);

  $sisaPembayaran = $item['total'] - $total;
  foreach ($result as $baris) {

?>

    <!-- MEMBUAT FORM -->
    <form action="editdireksiproses.php" method="post">
      <input type="hidden" name="noidbpu" value="<?php echo $baris['noid']; ?>">
      <input type="hidden" name="no" value="<?php echo $baris['no']; ?>">
      <input type="hidden" name="waktu" value="<?php echo $baris['waktu']; ?>">
      <input type="hidden" name="term" value="<?php echo $baris['term']; ?>">
      <input type="hidden" name="lastedit" value="<?php echo $_SESSION['nama_user']; ?>">
      <input type="hidden" name="persetujuan" value="<?php echo $baris['persetujuan']; ?>">
      <input type="hidden" name="status" value="<?php echo $baris['status']; ?>">
        <div id="alert-error-than"></div>

      <?php if ($baris['status_pengajuan_bpu'] != 0) : ?>
        <div class="form-group">
          <label for="jumlah" class="control-label">Request BPU (IDR) :</label>
          <input type="text" class="form-control" id="jumlahBpu" onchange="onChangeInput(this)" value="<?php echo $baris['pengajuan_jumlah']; ?>" disabled>
        </div>
      <?php else : ?>
        <div class="form-group">
          <label for="jumlah" class="control-label">BPU (IDR) :</label>
          <input type="text" class="form-control" id="jumlahBpu" onchange="onChangeInput(this)" value="<?php echo $baris['jumlah']; ?>" name="jumlah">
        </div>
      <?php endif; ?>

      <?php
      $jumreal = $baris['realisasi'];

      if ($jumreal > 0) {
      ?>
        <div class="form-group">
          <label for="jumlah" class="control-label">Realisasi Biaya (IDR) :</label>
          <input type="text" class="form-control" id="realisasi" value="<?php echo $baris['realisasi']; ?>" name="realisasi">
        </div>

        <div class="form-group">
          <label for="jumlah" class="control-label">Uang Kembali (IDR) :</label>
          <input type="text" class="form-control" id="uangkembali" value="<?php echo $baris['uangkembali']; ?>" name="uangkembali">
        </div>

        <div class="form-group">
          <label for="jumlah" class="control-label">Tanggal Realisasi :</label>
          <input type="date" class="form-control" id="tanggalrealisasi" value="<?php echo $baris['tanggalrealisasi']; ?>" name="tanggalrealisasi">
        </div>
      <?php
      } else {
      ?>
        <div class="form-group">
          <label for="jumlah" class="control-label">Realisasi Biaya (IDR) :</label>
          <input type="text" class="form-control" id="jumlahRealisasi" value="<?php echo $baris['realisasi']; ?>" name="realisasi" readonly>
        </div>

        <div class="form-group">
          <label for="jumlah" class="control-label">Uang Kembali (IDR) :</label>
          <input type="text" class="form-control" id="jumlahUangKembali" value="<?php echo $baris['uangkembali']; ?>" name="uangkembali" readonly>
        </div>

        <div class="form-group">
          <label for="jumlah" class="control-label">Tanggal Realisasi :</label>
          <input type="date" class="form-control" id="tanggalrealisasi" value="<?php echo $baris['tanggalrealisasi']; ?>" name="tanggalrealisasi" readonly>
        </div>
      <?php
      }
      ?>
      <!-- <div class="form-group">
        <label for="namabank" class="control-label">Nama Bank :</label>
        <input type="text" class="form-control" id="namabank" value="<?php echo $baris['namabank']; ?>" name="namabank">
      </div> -->

      <?php if (in_array($item['status'], ['UM', 'UM Burek'])) : ?>
        <div class="form-group">
          <label for="namapenerima" class="control-label">Nama Penerima :</label>
          <select class="form-control" id="namapenerimaBpuListEdit" name="namapenerima" onchange="ambil_rekening2(this.value)">
            <option selected disabled>Pilih Nama Penerima</option>
            <?php
            $querycok = "SELECT * FROM tb_user ORDER BY nama_user";
            $run_querycok = $koneksi->query($querycok);
            foreach ($run_querycok as $rq) {
            ?>
              <option value="<?php echo $rq['nama_user']; ?>" <?= ($rq['nama_user'] == $baris['namapenerima']) ? 'selected' : '' ?>><?php echo $rq['nama_user']; ?></option>
            <?php
            }
            ?>
            <?php

            $aplikasi = [];
            $queryAplikasi = mysqli_query($koneksi, "SELECT * FROM daftar_aplikasi_pembayaran ORDER BY nama_aplikasi");
            while ($a = mysqli_fetch_assoc($queryAplikasi)) {
              array_push($aplikasi, $a['nama_aplikasi']);
            }
            // $queryAplikasi = mysqli_query($koneksi, "SELECT * FROM daftar_aplikasi_pembayaran ORDER BY nama_aplikasi");
            // while ($a = mysqli_fetch_assoc($queryAplikasi)) :
            foreach ($aplikasi as $a) :
            ?>
              <!-- <option value="">a</option> -->
              <option value="<?= $a ?>"><?= $a ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="free-text">
          <div class="form-group">
            <label for="namabank" class="control-label">Nama Bank :</label>
            <input type="text" class="form-control bankBpuTextEdit" name="namabank" value="<?= ($baris['namabank']) ? $baris['namabank'] : '' ?>" readonly>
          </div>

          <div class="form-group">
            <label for="norek" class="control-label">Nomor Rekening :</label>
            <input type="number" class="form-control noRekBpuTextEdit" name="norek" value="<?= ($baris['norek']) ? $baris['norek'] : '' ?>" readonly>
          </div>
        </div>
      <?php else : ?>
        <!-- <div class="form-group">
          <label for="namabank" class="control-label">Nama Bank :</label>
          <input type="text" class="form-control" id="namabank" value="<?php echo $baris['namabank']; ?>" name="namabank">
        </div> -->
        <?php

        $arrNamaPenerima = explode(',', $baris['namapenerima']);
        $arrBank = explode(',', $baris['namabank']);
        $arrNorek = explode(',', $baris['norek']);
        $arrEmail = explode(',', $baris['emailpenerima']);

        for ($i = 0; $i < count($arrNamaPenerima); $i++) :
          $temp_namapenerima = trim($arrNamaPenerima[$i]);
          $temp_bank = trim($arrBank[$i]);
          $temp_norek = trim($arrNorek[$i]);
          $temp_email = trim($arrEmail[$i]);

        ?>

          <div class="div-penerima">
            <div class="sub-penerima">
              <div class="form-group">
                <?php if ($i == 0) : ?>
                  <!-- <span style="float: right; margin-bottom: 5px;"><button type="button" class="btn btn-sm btn-primary btn-tambah-penerima">Tambah Penerima</button></span> -->
                <?php else : ?>
                  <!-- <span style="float: right; margin-bottom: 5px;"><button type="button" class="btn btn-sm btn-danger btn-hapus-penerima">Hapus Penerima</button></span> -->
                <?php endif; ?>
                <label for="namapenerima" class="control-label">Nama Penerima :</label>
                <input type="text" class="form-control" id="namapenerima" value="<?php echo $temp_namapenerima; ?>" name="namapenerima[]" required>
              </div>

              <div class="form-group">
                <label for="email" class="control-label">Email :</label>
                <input type="email" class="form-control" name="email[]" value="<?= $temp_email ?>" required>
              </div>

              <div class="option-list">
                <div class="form-group">
                  <label for="namabank" class="control-label">Nama Bank :</label>
                  <select class="form-control" name="namabank[]" required>
                    <option value="" selected disabled>Pilih Kategori</option>
                    <?php
                    $queryDaftarBank = mysqli_query($koneksi, 'SELECT * FROM bank');
                    while ($db = mysqli_fetch_assoc($queryDaftarBank)) :
                    ?>
                      <option value="<?= $db['kodebank'] ?>" <?= ($db['kodebank'] == $temp_bank) ? 'selected' : '' ?>><?= $db['namabank'] ?></option>
                    <?php endwhile; ?>
                  </select>
                  <!-- <input type="text" class="form-control" name="namabank"> -->
                </div>

                <div class="form-group">
                  <label for="norek" class="control-label">Nomor Rekening :</label>
                  <input type="text" class="form-control" id="norek" value="<?= ($temp_norek) ? $temp_norek : '' ?>" name="norek[]" required>
                </div>
              </div>
            </div>
          </div>
        <?php endfor; ?>

      <?php endif; ?>

      <div class="form-group">
        <label for="tglcair" class="control-label">Tanggal Pencairan :</label>
        <input type="date" class="form-control" id="tglcair" value="<?php echo $baris['tanggalbayar']; ?>" name="tanggalbayar">
      </div>

      <div class="form-group">
        <label for="alasan" class="control-label">Comment :</label>
        <input type="text" class="form-control" id="alasan" name="alasan">
      </div>

      <button class="btn btn-primary" id="updateBpu" type="submit" name="submit">Update</button>

    </form>

<?php }
}
$koneksi->close();
?>

<!-- <script src="http://code.jquery.com/jquery-latest.min.js"></script> -->
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> -->
<script>
    var inputJumlah = document.getElementById("jumlahBpu")
    var sisaPembayaran = '<?= $sisaPembayaran ?>';
    var jabatan = '<?= $_SESSION["jabatan"]?>';
    var divisi = '<?= $_SESSION["divisi"]?>';

    var alertError = document.getElementById("alert-error-than")
    var submitBtn = document.getElementById("updateBpu")

    sisaPembayaran = parseInt(sisaPembayaran)

    function onChangeInput(e) {
        var val = parseInt(e.value)
        if (val > sisaPembayaran) {
            inputJumlah.value = sisaPembayaran
            alertError.innerHTML = `<div class="alert alert-warning" role="alert">
                Total melebihi sisa pembayaran, total otomatis di atur sama dengan sisa pembayaran
            </div>`;
            submitBtn.disabled = true
        } else {
            submitBtn.disabled = false
            alertError.innerHTML = ''
        }

        if (divisi === 'FINANCE' && jabatan === 'Manager' && value > 1000000) {
            submitBtn.disabled = true
        } else {
            submitBtn.disabled = false
        }
    }

    // inputJumlah.addEventListener('change', (e) => {
    //     console.log("CHANGE", e.target.value)
    //     // var value = e.target.value
    //     // value = parseInt(value)
    //     //
    //     // if (value > sisaPembayaran) {
    //     //     inputJumlah.value = sisaPembayaran
    //     //     alertError.innerHTML = `<div class="alert alert-warning" role="alert">
    //     //         Total melebihi sisa pembayaran, total otomatis di atur sama dengan sisa pembayaran
    //     //     </div>`;
    //     //     submitBtn.disabled = true
    //     // } else {
    //     //     submitBtn.disabled = false
    //     //     alertError.innerHTML = ''
    //     // }
    //     //
    //     // if (divisi === 'FINANCE' && jabatan === 'Manager' && value > 1000000) {
    //     //     submitBtn.disabled = true
    //     // } else {
    //     //     submitBtn.disabled = false
    //     // }
    // })

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
          // <span style="float: right; margin-bottom: 5px;"><button type="button" class="btn btn-sm btn-danger btn-hapus-penerima">Hapus Penerima</button></span>
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
  })

  function sum() {
    var txtSecondNumberValue = document.getElementById('harga').value;
    var txtTigaNumberValue = document.getElementById('quantity').value;
    var result = parseFloat(txtSecondNumberValue) * parseFloat(txtTigaNumberValue);
    if (!isNaN(result)) {
      document.getElementById('total').value = result;
    }
  }


  function ambil_rekening2(id_user) {
    $('.bankBpuTextEdit').val('');
    $('.noRekBpuTextEdit').val('');
    $.ajax({
        url: 'bpuajax.php',
        type: 'post',
        dataType: 'json',
        data: {
          actions: 'ambil_rekening',
          id_user: id_user
        }
      })
      .done(function(data) {

        console.log(data);
        if (data != '') {
          $('.bankBpuTextEdit').val(data.bank);
          $('.noRekBpuTextEdit').val(data.norek);
        } else {
          $('.bankBpuTextEdit').val('');
          $('.noRekBpuTextEdit').val('');
        }
      })
      .fail(function() {
        console.log('Gagal');
      });
  }
</script>