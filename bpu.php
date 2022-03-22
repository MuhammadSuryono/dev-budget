<?php
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
$con->load_database($koneksi);

error_reporting(0);
session_start();
if (!isset($_SESSION['nama_user'])) {
  header("location:login.php");
  // die('location:login.php');//jika belum login jangan lanjut
}

$id_user = $_SESSION['id_user'];

$aplikasi = [];
$queryAplikasi = mysqli_query($koneksi, "SELECT * FROM daftar_aplikasi_pembayaran ORDER BY nama_aplikasi");
while ($a = mysqli_fetch_assoc($queryAplikasi)) {
  array_push($aplikasi, $a['nama_aplikasi']);
}

if ($_POST['no'] && $_POST['waktu']) {
  $id = $_POST['no'];
  $waktu = $_POST['waktu'];
  $code = isset($_POST['id']) ? $_POST['id'] : null;

  $queryBpu = "SELECT max(term) as last_term, SUM(jumlah) as total FROM bpu WHERE no = '$id' AND waktu = '$waktu'";
  $mysqlQuery = mysqli_query($koneksi, $queryBpu);

  $lastTerm = 0;
  $total = 0;
  while($row = mysqli_fetch_assoc($mysqlQuery)) {
      $lastTerm = $row['last_term'];
      $total = $row['total'];
  }

  if ($lastTerm == NULL) {
    $lastTerm = 0;
  }

  if ($total == NULL) {
    $total = 0;
  }

  $totalTerm = 1;



  // mengambil data berdasarkan id
  // dan menampilkan data ke dalam form modal bootstrap
  $sql = "SELECT * FROM selesai WHERE no = '$id' AND waktu = '$waktu'";
  $result = $koneksi->query($sql);
  foreach ($result as $baris) {

    $itemId = $baris["id"];
  $totalPengajuan = $baris['total'];
  $sisaPembayaran = $totalPengajuan - $total;

?>

    <script type="text/javascript">
      function validateForm() {
        var a = document.forms["Form"]["jumlah"].value;
        var b = document.forms["Form"]["tglcair"].value;
        var c = document.forms["Form"]["namabank"].value;
        var d = document.forms["Form"]["norek"].value;
        var e = document.forms["Form"]["namapenerima"].value;
        if (a == null || a == "", b == null || b == "", c == null || c == "", d == null || d == "", e == null || e == "") {
          alert("Harap Isi Yang Kosong");
          return false;
        }
      }
    </script>

    <!-- MEMBUAT FORM -->
    <form action="bpuproses.php" method="post" name="Form" onsubmit="return validateForm()" enctype="multipart/form-data" runat="server">

      <input type="hidden" name="id" value="<?php echo $code ?>">
      <input type="hidden" name="no" value="<?php echo $baris['no']; ?>">
      <input type="hidden" name="waktu" value="<?php echo $baris['waktu']; ?>">
      <input type="hidden" name="pengaju" value="<?php echo $_SESSION['nama_user']; ?>">
      <input type="hidden" name="divisi" value="<?php echo $_SESSION['divisi']; ?>">
      <input type="hidden" name="statusbpu" value="<?php echo $baris['status']; ?>">

      <div id="alert-more-than"></div>

      <?php
      $statusbpu = $baris['status'];

        if ($statusbpu != "Biaya Lumpsum") { ?>
        <div class="form-group">
            <label for="rincian" class="control-label">Total BPU (IDR)s :</label>
            <input class="form-control" name="jumlah" id="id_step2-number_2" type="text" required>
        </div>
        <?php
        }

      if ($statusbpu == 'UM' || $statusbpu == 'UM Burek') {
      ?>
        <div class="form-group">
          <label for="id_rekening" class="control-label">Nama Penerima: <span data-toggle="tooltip" title="Pembuat BPU tidak bisa ditujukan sebagai penerima BPU"><i class="fa fa-question-circle"></i></span></label>
          <select class="form-control" id="namapenerima" name="namapenerima">
            <option selected disabled>Pilih Nama Penerima</option>
            <?php
            $penerima = $con->select("a.*, b.namabank")->from("rekening a")
                ->join("bank b", "a.kode_bank = b.kodebank", "LEFT")
                ->where("a.is_validate", "=", true)
                ->where("a.item_id", "=", $itemId)->get();
            foreach ($penerima as $rq) {
            ?>
              <option value="<?php echo $rq['nama_penerima']; ?>" data-penerima="<?= htmlspecialchars(json_encode($rq)) ?>" onclick="ambil_rekening(this)"><?php echo $rq['nama_penerima'] . ' - ' . $rq['nomor_rekening'] . ' - ' . $rq['namabank'] ?></option>
            <?php
            }
            ?>
          </select>
        </div>

        <div class="form-group">
          <label for="email" class="control-label">Email :</label>
          <input type="email" id="emailBpu" class="form-control" name="email[]" readonly>
        </div>

        <div class="form-group">
          <label class="control-label">Upload File <a href="#" data-toggle="tooltip" title="Upload File Rincian BPU"><i class="fa fa-question-circle"></i></a></label>
          <input type="file" class="form-control" accept="image/*,application/pdf" name="gambar" id="fileInputBpu" required>
          <img class="img-responsive" style="display: block; margin-left: auto;  margin-right: auto; background-repeat: no-repeat; background-size: cover; background-attachment: fixed;" src="" alt="" id="imageBpu">
        </div>

        <div class="form-group">
          <label for="namabank" class="control-label">Nama Bank :</label>
            <input type="hidden" class="form-control" id="c" name="namabank[]" readonly>
          <input type="text" class="form-control" id="namabank" name="namabankshow[]" readonly>
        </div>

        <div class="form-group">
          <label for="norek" class="control-label">Nomor Rekening :</label>
          <input type="number" class="form-control" id="d" name="norek[]" readonly>
        </div>

          <?php
          if ($statusbpu != 'Biaya Lumpsum') {
          ?>
        <div class="form-group">
          <label for="namabank" class="control-label">Tanggal Pembayaran :</label>
          <input type="date" class="form-control" name="tanggal_bayar" id="tanggal-bayar" min="<?= date('Y-m-d', strtotime($Date . ' + 1 days')) ?>">
        </div>

        <div class="form-group">
          <label for="namabank" class="control-label">Tanggal Jatuh Tempo :</label>
          <input type="date" class="form-control" name="tanggal_jatuh_tempo" id="tanggal-jatuh-tempo" min="<?= date('Y-m-d', strtotime($Date . ' + 2 days')) ?>" required>
        </div>
              <?php } ?>

        <button class="btn btn-primary" type="submit" name="submit">SUBMIT</button>

      <?php
      } else if ($statusbpu == 'Biaya') {
      ?>
        <div class="form-group">
          <label class="control-label">Upload File <a href="#" data-toggle="tooltip" title="Upload File Rincian BPU"><i class="fa fa-question-circle"></i></a></label>
          <input type="file" class="form-control" accept="image/*,application/pdf" name="gambar" id="fileInputBpu" required>
          <img class="img-responsive" style="display: block; margin-left: auto;  margin-right: auto; background-repeat: no-repeat; background-size: cover; background-attachment: fixed;" src="" alt="" id="imageBpu">
        </div>

        <div class="div-penerima">
          <div class="sub-penerima">
            <div class="form-group">
              <!-- <span style="float: right; margin-bottom: 5px;"><button type="button" class="btn btn-sm btn-primary btn-tambah-penerima">Tambah Penerima</button></span> -->
              <label for="namapenerima" class="control-label">Nama Penerima: </label>
              <input type="text" class="form-control" name="namapenerima" required>
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

        <button class="btn btn-primary" type="submit" name="submit">SUBMIT</button>
      <?php
      }

      elseif ($statusbpu == 'Biaya Lumpsum') {
          $queryDataPenerima = mysqli_query($koneksi, "SELECT namapenerima, emailpenerima, norek, namabank, bank_account_name, vendor_type FROM bpu WHERE namapenerima NOT IN ('TLF', '') GROUP BY namapenerima");
          $dataPenerima = [];
          while ($row = $queryDataPenerima->fetch_assoc()) {
              $dataPenerima[] = $row;
          }
          ?>
          <h4 style="float: left">Sisa Pembayaran: Rp. <span id="totalBpuNominal"><?= number_format($sisaPembayaran) ?></span></h4>
          <div style="display: flex; justify-content: end; margin-bottom: 10px;">
              <button type="button" class="btn btn-sm btn-primary btn-tambah-row">Tambah Penerima</button>
          </div>
          <div class="row-penerima-honor">
              <div class="row">
                  <div class="col-lg-4">
                      <div class="form-group">
                          <label for="rincian" class="control-label">Total BPU (IDR) :</label>
                          <input class="form-control" name="jumlah[]" type="number" onchange="countTotalNominal(this)">
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
          <button class="btn btn-primary" type="submit" name="submit">SUBMIT</button>
      <?php } else {
      ?>
        <div class="div-penerima">
          <div class="sub-penerima">
            <div class="form-group">
              <!-- <span style="float: right; margin-bottom: 5px;"><button type="button" class="btn btn-sm btn-primary btn-tambah-penerima">Tambah Penerima</button></span> -->
              <label for="namapenerima" class="control-label">Nama Penerima :</label>
              <input type="text" class="form-control" name="namapenerima" required>
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

        <button class="btn btn-primary" type="submit" name="submit">SUBMIT</button>
      <?php
      }
      ?>
    </form>

    <script>
      let sisaPembayaran = '<?= $sisaPembayaran ?>';
      sisaPembayaran = parseInt(sisaPembayaran)
      let inputJumlah = document.getElementById("id_step2-number_2")

      var optionBank = document.getElementsByClassName("bank");
      var emailPenerima = document.getElementsByClassName("email");
      var namaRekeningPenerima = document.getElementsByClassName("nama-norek");
      var dataListSelected = document.getElementsByClassName("brow");
      var norek = document.getElementsByClassName("norek");
      let totalBpu = 0;
      let totalBpuNominal = document.getElementById("totalBpuNominal");

      let alertError = document.getElementById("alert-more-than")

      function formatNumber(num) {
          return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
      }

        function countTotalNominal(e) {
          totalBpu += parseInt(e.value)
          // totalBpuNominal.innerHTML = formatNumber(totalBpu)
        }
      if (inputJumlah !== null) {
          inputJumlah.addEventListener('change', (e) => {
              let value = e.target.value
              value = parseInt(value)

              if (value > sisaPembayaran) {
                  inputJumlah.value = sisaPembayaran
                  alertError.innerHTML = `<div class="alert alert-warning" role="alert">
                Total melebihi sisa pembayaran, total otomatis di atur sama dengan sisa pembayaran
            </div>`;
                  submitBtn.disabled = false
              } else {
                  alertError.innerHTML = ''
              }
          })
      }

      var statusBpu = '<?= $statusbpu ?>';
      if (statusBpu == 'UM' || statusBpu == 'UM Burek') {
        var picker = document.getElementById('tanggal-bayar');
        picker.addEventListener('input', function(e) {
          var day = new Date(this.value).getUTCDay();
          if ([6, 0].includes(day)) {
            e.preventDefault();
            this.value = '';
            alert('Weekends not allowed');
          }
        });

      }

      function readURL(input) {
        if (input.files && input.files[0]) {
          var reader = new FileReader();

          reader.onload = function(e) {
            $('#imageBpu').attr('src', e.target.result);
          }

          reader.readAsDataURL(input.files[0]); // convert to base64 string
        }
      }

      function ambil_rekening(e) {
          let json = JSON.parse(e.dataset.penerima)
          $("#emailBpu").val(json.email);
          $("#d").val(json.nomor_rekening);
          $("#c").val(json.kode_bank);
          $("#namabank").val(json.namabank);
      }

      $(document).on('click', '.btn-hapus-penerima', function() {
        $(this).parent().parent().parent().remove();
      })

      $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();

        var fruits = ["Banana", "Orange", "Apple", "Mango"];
        const aplikasi = <?= json_encode($aplikasi) ?>;
        $('#namapenerima').change(function() {
          const namaPenerima = $(this).val();
          // console.log(namaPenerima);
          if (aplikasi.includes(namaPenerima)) {
            $('#d').attr('readonly', false);
          } else {
            $('#d').attr('readonly', true);
          }
        })
      });

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
                    <input class="form-control" name="jumlah[]" onchange="countTotalNominal(this)" type="number">
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

      // console.log($('#fileInput'));
      $(document).ready(function() {
        $('#fileInputBpu').change(function() {
          readURL(this);
        })

        $("#id_step2-number_2").keyup(function(event) {
          // skip for arrow keys
          // if (event.which >= 37 && event.which <= 40) {
          //   event.preventDefault();
          // }
          // var $this = $(this);
          // var num = $this.val().replace(/,/gi, "").split("").reverse().join("");

          // var num2 = RemoveRougeChar(num.replace(/(.{3})/g, "$1,").split("").reverse().join(""));


          // // the following line has been simplified. Revision history contains original.
          // $this.val(num2);
        });
      });

      function RemoveRougeChar(convertString) {


        if (convertString.substring(0, 1) == ",") {

          return convertString.substring(1, convertString.length)

        }
        return convertString;

      }
    </script>

<?php
    break;
  }
}
$koneksi->close();
?>