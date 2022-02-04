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

$id_user = $_SESSION['id_user'];

$aplikasi = [];
$queryAplikasi = mysqli_query($koneksi, "SELECT * FROM daftar_aplikasi_pembayaran ORDER BY nama_aplikasi");
while ($a = mysqli_fetch_assoc($queryAplikasi)) {
  array_push($aplikasi, $a['nama_aplikasi']);
}

if ($_POST['no'] && $_POST['waktu']) {
  $id = $_POST['no'];
  $waktu = $_POST['waktu'];
  $code = $_POST['id'];

  $queryBpu = "SELECT max(term) as last_term, SUM(jumlah) as total FROM bpu WHERE no = '$no' AND waktu = '$waktu'";
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
      <div class="form-group">
        <label for="rincian" class="control-label">Total BPU (IDR)s :</label>
        <input class="form-control" name="jumlah" id="id_step2-number_2" type="text">
      </div>

      <!-- <div class="form-group">
              <label for="tglcair" class="control-label">Tanggal Permintaan Pencairan :</label>
                <input type="date" class="form-control" id="b" name="tglcair"
                                  min="<?php
                                        // date_default_timezone_set("Asia/Bangkok");
                                        // $currentTime = date("H:i:s");
                                        // if (date('H') >= 16) {
                                        // $date1 = date('Y-m-d', strtotime("+2 day")); echo $date1;
                                        // }else{
                                        // $date2 = date('Y-m-d', strtotime("+1 day")); echo $date2;
                                        // }
                                        ?>">
            </div> -->



      <?php
      $statusbpu = $baris['status'];

      if ($statusbpu == 'UM' || $statusbpu == 'UM Burek') {
      ?>
        <div class="form-group">
          <label for="id_rekening" class="control-label">Nama Penerima: <span data-toggle="tooltip" title="Pembuat BPU tidak bisa ditujukan sebagai penerima BPU"><i class="fa fa-question-circle"></i></span></label>
          <select class="form-control" id="id_rekening" name="id_rekening" onchange="ambil_rekening(this.value)">
            <option selected disabled>Pilih Nama Penerima</option>
            <?php
            $queryRekening = "SELECT a.*, b.namabank, c.id_user FROM rekening a JOIN bank b ON b.kodebank = a.bank JOIN tb_user c ON c.id_user = a.user_id WHERE status = 'internal' ORDER BY a.nama";
            $run_queryRekening = $koneksi->query($queryRekening);
            foreach ($run_queryRekening as $rq) {
            ?>
              <option value="<?php echo $rq['no']; ?>" <?= ($rq['id_user'] == $id_user) ? "disabled" : ""; ?>><?php echo $rq['nama'] . ' - ' . $rq['rekening'] . ' - ' . $rq['namabank'] ?></option>
            <?php
            }
            ?>
            <?php if ($_POST['page'] == 'views') : ?>
              <?php
              // $queryAplikasi = mysqli_query($koneksi, "SELECT * FROM daftar_aplikasi_pembayaran ORDER BY nama_aplikasi");
              // while ($a = mysqli_fetch_assoc($queryAplikasi)) :
              foreach ($aplikasi as $a) :
              ?>
                <!-- <option value="">a</option> -->
                <!-- <option value="<?= $a ?>"><?= $a ?></option> -->
              <?php endforeach; ?>
            <?php endif; ?>
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
          <input type="text" class="form-control" id="c" name="namabank[]" readonly>
        </div>

        <div class="form-group">
          <label for="norek" class="control-label">Nomor Rekening :</label>
          <input type="number" class="form-control" id="d" name="norek[]" readonly>
        </div>

        <div class="form-group">
          <label for="namabank" class="control-label">Tanggal Pembayaran :</label>
          <input type="date" class="form-control" name="tanggal_bayar" id="tanggal-bayar" min="<?= date('Y-m-d', strtotime($Date . ' + 1 days')) ?>">
        </div>

        <div class="form-group">
          <label for="namabank" class="control-label">Tanggal Jatuh Tempo :</label>
          <input type="date" class="form-control" name="tanggal_jatuh_tempo" id="tanggal-jatuh-tempo" min="<?= date('Y-m-d', strtotime($Date . ' + 2 days')) ?>" required>
        </div>

        <button class="btn btn-primary" type="submit" name="submit">SUBMIT</button>

      <?php
      } else if ($statusbpu == 'Biaya' || $statusbpu == 'Biaya Lumpsum') {
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
      } else {
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

      let alertError = document.getElementById("alert-more-than")


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

      var statusBpu = '<?= $statusbpu ?>';
      if (statusBpu == 'UM' || statusBpu == 'UM Burek') {
        var picker = document.getElementById('tanggal-bayar');
        // picker.addEventListener('input', function(e) {
        //   var day = new Date(this.value).getUTCDay();
        //   if ([6, 0].includes(day)) {
        //     e.preventDefault();
        //     this.value = '';
        //     alert('Weekends not allowed');
        //   }
        // });

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

      function ambil_rekening(id_user) {
        $("#d").val('');
        $("#c").val('');
        $("#emailBpu").val('');
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
              $("#emailBpu").val(data.email);
              $("#d").val(data.rekening);
              $("#c").val(data.bank);
            } else {
              $("#emailBpu").val('');
              $("#d").val('');
              $("#c").val('');
            }
          })
          .fail(function() {
            console.log('Gagal');
          });
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


        $('.btn-tambah-penerima').click(function() {
          var count = $('.sub-penerima').length;
          html = `
          
          <div class="sub-penerima">
          <div class="form-group">
          <span style="float: right; margin-bottom: 5px;"><button type="button" class="btn btn-sm btn-danger btn-hapus-penerima">Hapus Penerima</button></span>
            <label for="namapenerima" class="control-label">Nama Penerima:</label>
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
          `;

          $('.div-penerima').append(html);
        })
      });

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