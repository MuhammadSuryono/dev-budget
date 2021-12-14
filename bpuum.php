<?php
error_reporting(0);
session_start();

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

if (!isset($_SESSION['nama_user'])) {
  header("location:login.php");
  // die('location:login.php');//jika belum login jangan lanjut
}

if ($_POST['iduser']) {
  $iduser = $_POST['iduser'];


  // mengambil data berdasarkan id
  // dan menampilkan data ke dalam form modal bootstrap
  $sql = "SELECT * FROM tb_user WHERE id_user='$iduser'";
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

    <!-- MEMBUAT FORM -->
    <form action="bpuumproses.php" method="post" name="Form" onsubmit="return validateForm()" enctype="multipart/form-data">

      <input type="hidden" name="no" value="0">
      <input type="hidden" name="waktu" value="0000-00-00 00:00:00">
      <input type="hidden" name="pengaju" value="<?php echo $_SESSION['nama_user']; ?>">
      <input type="hidden" name="divisi" value="<?php echo $_SESSION['divisi']; ?>">
      <input type="hidden" name="statusbpu" value="UM">


      <div class="form-group">
        <label for="rincian" class="control-label">Total BPU (IDR) :</label>
        <input type="text" class="form-control" id="a" name="jumlah">
      </div>

      <div class="form-group">
        <label for="tglcair" class="control-label">Tanggal Permintaan Pencairan :</label>
        <input type="date" class="form-control" id="b" name="tglcair" required>
      </div>

      <div class="form-group">
        <label for="tglcair" class="control-label">Pilih Nama Project :</label>
        <select name="project" class="form-control" required>
          <?php
          $carinama = "SELECT nama FROM pengajuan WHERE jenis='B1' AND status='Disetujui' ORDER BY nama";
          $result2 = $koneksi->query($carinama);
          while ($cn = mysqli_fetch_array($result2)) {
          ?>
            <option value="<?php echo $cn['nama']; ?>"><?php echo $cn['nama']; ?></option>
          <?php
          }
          ?>
        </select>
      </div>

      <div class="form-group">
        <label for="tglcair" class="control-label">Tanggal Jatuh Tempo :</label>
        <input type="date" class="form-control" name="jatuhtempo" required>
      </div>

      <!-- <div class="form-group">
        <label class="control-label">Upload File <a href="#" data-toggle="tooltip" title="Upload File Rincian BPU"><i class="fa fa-question-circle"></i></a></label>
        <input type="file" class="form-control" accept="image/*" name="gambar" id="fileInput">
      </div> -->
      </div>

      <div class="form-group">
        <label for="tglcair" class="control-label">Nama Penerima :</label>
        <input type="text" class="form-control" id="b" name="namapenerima" value="<?php echo $baris['nama_user']; ?>" readonly>
      </div>

      <div class="form-group">
        <label for="tglcair" class="control-label">Nomor Rekening :</label>
        <input type="text" class="form-control" id="b" name="norek" value="<?php echo $baris['norek']; ?>" readonly>
      </div>

      <div class="form-group">
        <label for="tglcair" class="control-label">Bank :</label>
        <input type="text" class="form-control" id="b" name="bank" value="<?php echo $baris['bank']; ?>" readonly>
      </div>



      <button class="btn btn-primary" type="submit" name="submit">SUBMIT</button>
    </form>

<?php }
}
$koneksi->close();
?>