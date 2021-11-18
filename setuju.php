<?php
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

session_start();
if (!isset($_SESSION['nama_user'])) {
  header("location:login.php");
  // die('location:login.php');//jika belum login jangan lanjut
}

if ($_POST['no'] && $_POST['waktu'] && $_POST['term']) {
  $id = $_POST['no'];
  $waktu = $_POST['waktu'];
  $term = $_POST['term'];

  $querySelesai = mysqli_query($koneksi, "SELECT * FROM selesai WHERE no = '$id' AND waktu = '$waktu'");
  $selesai = mysqli_fetch_assoc($querySelesai);


  $queryPengajuan = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE waktu = '$waktu'");
  $pengajuan = mysqli_fetch_assoc($queryPengajuan);

  $getRekening = mysqli_query($koneksiDevelop, "SELECT * FROM kas WHERE stat = 'MRI'");

  // mengambil data berdasarkan id
  // dan menampilkan data ke dalam form modal bootstrap
  $sql = "SELECT * FROM selesai WHERE no = '$id' AND waktu = '$waktu'";
  $result = $koneksi->query($sql);
  foreach ($result as $baris) {

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

    <?php
    $sql2 = "SELECT t1.*, t2.berita_transfer, t4.nama AS nama_rekening, t3.label_kas, t3.bank AS kas_bank FROM bpu t1 LEFT JOIN bridgetransfer.data_transfer t2 ON t1.noid = t2.noid_bpu LEFT JOIN develop.kas t3 ON t1.rekening_sumber = t3.rekening LEFT JOIN rekening t4 ON t4.no = t1.rekening_id WHERE t1.no = '$id' AND t1.waktu = '$waktu' AND t1.term='$term' AND t1.persetujuan='Belum Disetujui'";


    $result2 = mysqli_query($koneksi, $sql2) or die(mysqli_error($koneksi));
    // $result2 = $koneksi->query($sql2);


    // var_dump($result2);
    foreach ($result2 as $baris2) {
      $tanggal_bayar = $baris2['tanggalbayar'];
      // var_dump($baris2);
      $queryKasBank = mysqli_query($koneksi, "SELECT * FROM bank WHERE kodebank = '$baris2[kas_bank]'");
      $kasBank = mysqli_fetch_assoc($queryKasBank);

      $queryBank = mysqli_query($koneksi, "SELECT * FROM bank WHERE kodebank = '$baris2[namabank]'");
      $bank = mysqli_fetch_assoc($queryBank);
    ?>
      <ul class="list-group">
        <li class="list-group-item">Bank : <b><?php echo $bank['namabank']; ?></b></li>
        <li class="list-group-item">No. Rekening : <b><?php echo $baris2['norek']; ?></b></li>
        <li class="list-group-item">Nama Penerima : <b><?php echo ($baris2['nama_rekening']) ? $baris2['nama_rekening'] : $baris2['namapenerima']; ?></b></li>
        <li class="list-group-item">Total BPU (IDR) : <b><?php echo 'Rp. ' . number_format($baris2['jumlah'], 0, '', ','); ?></b></li>
        <li class="list-group-item">Pengaju BPU : <b><?php echo $baris2['pengaju']; ?> (<?php echo $baris2['divisi']; ?>)</b></li>
        <li class="list-group-item">Berita Transfer : <b><?php echo $baris2['ket_pembayaran']; ?></b></li>
        <li class="list-group-item">Rekening Sumber : <b><?php echo $baris2['rekening_sumber'] . ' - ' . $kasBank['namabank'] ?></b></li>
        <li class="list-group-item">Nama Rekening Sumber : <b><?php echo $baris2['label_kas']; ?></b></li>
        <li class="list-group-item">Metode Pembayaran : <b><?php echo $baris2['metode_pembayaran']; ?></b></li>
      </ul>
      <div class="form-group">
        <p class="control-p"><b>Uploaded File</b></p>
        <img id="image" src="uploads/<?= $baris2['fileupload'] ?>" class="img-responsive" style="display: block; margin-left: auto;  margin-right: auto; background-repeat: no-repeat; background-size: cover; background-attachment: fixed;  " src="" alt="">
      </div>

    <?php break;
    } ?>

    <!-- MEMBUAT FORM -->
    <form action="setujuproses.php" method="post">
      <input type="hidden" name="noid" value="<?php echo $baris2['noid']; ?>">
      <input type="hidden" name="no" value="<?php echo $baris['no']; ?>">
      <input type="hidden" name="waktu" value="<?php echo $baris['waktu']; ?>">
      <input type="hidden" name="pengaju" value="<?php echo $baris['pengaju']; ?>">
      <input type="hidden" name="divisi" value="<?php echo $baris['divisi']; ?>">
      <input type="hidden" name="metode_pembayaran" value="<?php echo $baris2['metode_pembayaran']; ?>">
      <input type="hidden" name="term" value="<?php echo $term ?>">
      <input type="hidden" name="persetujuan" value="Sudah Disetujui">


      <p>Apakah anda ingin menyetujui <b>BPU</b> di Nomor <b><?= $baris['no']; ?></b>?</p>

      <!--<div class="form-group">
              <label for="tglcair" class="control-label">Tanggal Pembayaran :</label>
                <input type="date" class="form-control" id="tglbayar" name="tanggalbayar"
                                  min="<?php
                                        // date_default_timezone_set("Asia/Bangkok");
                                        // $currentTime = date("H:i:s");
                                        // $currentDate = date("Y-m-d");
                                        // echo $currentDate;
                                        // if (date('H') >= 16) {
                                        // $date1 = date('Y-m-d', strtotime("+2 day")); echo $date1;
                                        // }else{
                                        // $date2 = date('Y-m-d', strtotime("+1 day")); echo $date2;
                                        // }
                                        ?>" required>
            </div> -->


      <div class="form-group">
        <label for="tglcair" class="control-label">Status Urgent :</label>
        <select class="form-control" name="urgent">
          <option value="Not Urgent" selected>-</option>
          <option value="Urgent">Urgent</option>
        </select>
      </div>
      <div class="form-group">
        <label for="tglbayar" class="control-label">Tanggal Pembayaran :</label>
        <input type="date" class="form-control" id="tglbayar" name="tanggalbayar" value="<?= ($tanggal_bayar) ? $tanggal_bayar : '-' ?>" min="<?= date('Y-m-d', strtotime($Date . ' + 2 days')) ?>">
      </div>

      <div class="form-group">
        <label for="alasanTolakBpu" class="control-label">Alasan Penolakan (Jika ditolak):</label>
        <input type="text" class="form-control" name="alasanTolakBpu" id="alasanTolakBpu">
      </div>

      <button class="btn btn-primary" type="submit" name="submit" value="1">Setujui</button>
      <button class="btn btn-danger" type="submit" name="submit" value="0">Tolak</button>
    </form>

    <br />

    <form action="pendingproses.php" method="POST">
      <input type="hidden" name="no" value="<?php echo $baris['no']; ?>">
      <input type="hidden" name="waktu" value="<?php echo $baris['waktu']; ?>">
      <input type="hidden" name="pengaju" value="<?php echo $baris['pengaju']; ?>">
      <input type="hidden" name="divisi" value="<?php echo $baris['divisi']; ?>">
      <button class="btn-warning btn" name="pending">Pending</button>
    </form>

<?php
    break;
  }
}
$koneksi->close();
?>
<script>
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
</script>