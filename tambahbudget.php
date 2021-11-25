<?php
require_once "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();


$con->set_name_db(DB_JAY);
$con->init_connection();
$koneksiJay = $con->connect();


?>

<form class="form-horizontal" action="proses/proses-tambahbudget.php" id="form-create-project" method="POST">

  <div class="form-group">
    <label for="jenis">Pilih Jenis Project :</label>
    <select class="form-control" id="jenis" name="jenis" required>
      <option disabled selected>Pilih Jenis Project</option>
      <?php if ($_SESSION['divisi'] == 'Direksi') : ?>
        <option value="B1">B1</option>
        <option value="B2">B2</option>
        <option value="Rutin">Rutin</option>
        <option value="Non Rutin">Non Rutin</option>
        <option value="Lainnya">Lainnya</option>
      <?php elseif (strtolower($_SESSION['divisi']) == 'finance') : ?>
        <option value="Rutin">Rutin</option>
        <option value="Non Rutin">Non Rutin</option>
      <?php elseif (strtolower($_SESSION['hak_akses']) == 'manager') : ?>

        <?php if(strpos($_SESSION['divisi'],'B1')) {
          echo '<option value="B1">B1</option>';
        } ?>
        <?php if(strpos($_SESSION['divisi'],'B2')) {
          echo '<option value="B2">B2</option>';
        } ?>
        <option value="Rutin">Rutin</option>
        <option value="Non Rutin">Non Rutin</option>
        <option value="Lainnya">Lainnya</option>
      <?php endif; ?>

    </select>
  </div>

  <div class="form-group" style="display: none;" id="projectDiv">
    <label for="project">Pilih Project :</label>
    <select class="form-control" id="project" name="project">

    </select>
  </div>

  <input type="hidden" name="nama" id="nama" class="nama">
  <input type="hidden" name="idUser" id="idUser">
  <input type="hidden" name="table" id="table">

  <div id="tahunDiv" class="form-group" style="display: none;">
    <label for="tahun">Tahun</label>
    <input type="text" class="form-control" id="tahun" name="tahun" style="cursor: no-drop;" readonly>
  </div>

  <div id="picDiv" class="form-group" style="display: none;">
    <label for="pic">PIC Budget</label>
    <input type="text" class="form-control" id="pic" name="pic" style="cursor: no-drop;" readonly>
  </div>


  <!-- <div class="form-group">
    <label for="jenis">Jenis Project :</label>
    <select class="form-control" id="jenis" name="jenis" required>
      <option disabled selected>Pilih Jenis Project</option>
      <?php
      if ($_SESSION['divisi'] == 'GA' || $_SESSION['hak_page'] == 'Create') {
        echo "<option id='nonrut' value='Non Rutin'>Non Rutin</option>";
      } else if ($_SESSION['divisi'] == 'FINANCE') {
        echo "<option id='nonrut' value='Non Rutin'>Non Rutin</option>";
        echo "<option value='Rutin'>Rutin</option>";
      } else if ($_SESSION['divisi'] == 'Direksi') {
        echo "<option id='jenb1' value='B1'>B1</option>";
        echo "<option id='jenb2' value='B2'>B2</option>";
        echo "<option id='nonrut' value='Non Rutin'>Non Rutin</option>";
      } else {
        echo "<option value='Non Rutin'>Non Rutin</option>";
      }
      ?>
    </select>
  </div> -->

  <div id="katnon" class="form-group" style="display:none;">
    <label for="katnon">Kategori</label>
    <select class="form-control" id="kate" name="katnon">
      <option value="" selected>Pilih Kategori</option>
      <?php
      $carikatnon = $koneksi->query("SELECT * FROM kategori_nonrutin WHERE kode !='012' ORDER BY kategori");
      while ($ckn = mysqli_fetch_array($carikatnon)) {
        $kodekat = $ckn['kode'];
        $namakat = $ckn['kategori'];
        echo "<option value='$kodekat'>$namakat</option>";
      }
      ?>
      <option value="012">Lain - lain</option>
    </select>
  </div>

  <div id="namanon" class="form-group" style="display: none;">
    <label for="nama">Nama Project :</label>
    <input type="text" class="form-control nama" name="nama">
  </div>

  <div id="namab1" class="form-group" style="display:none;">
    <label for="kodeproject">Kode Project</label>
    <select class="custom-select form-control" id="kodeproject" name="kodepro[]" multiple>
      <option selected disabled>Pilih Project</option>
      <?php
      $kode = mysqli_query($koneksiJay, "SELECT * FROM project WHERE visible='y' ORDER BY nama");
      foreach ($kode as $rc) {
        $kodepro = $rc['kode'];
        $nampro  = $rc['nama'];
        echo "<option value='$kodepro'>$kodepro - $nampro</option>";
      }
      ?>
    </select>
  </div>

  <div id="tahunNonrutDiv" class="form-group" style="display: none;">
    <label for="tahun">Tahun :</label>
    <select class="form-control" id="tahunNonRut" name="tahun">
      <option disabled selected>Pilih Tahun</option>
      <option>Pilih Tahun</option>
      <?php
      for ($i = 2017; $i <= 2030; $i++) {
        echo "<option>".$i."</option>";
      }
      ?>
    </select>
  </div>

  <div id="picNonRutDiv" class="form-group" style="display: none;">
    <label for="pic">PIC Budget :</label>
    <select class="form-control" id="picNonRut" name="idUser">
      <option disabled selected>Pilih PIC Budget</option>
      <?php
      $pic = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE resign is NULL ORDER BY nama_user");
      foreach ($pic as $p) {
      ?>
        <option value="<?php echo $p['id_user']; ?>"><?php echo $p['nama_user']; ?> - (<?php echo $p['divisi']; ?>)</option>
      <?php } ?>
    </select>
  </div>
  <input type="hidden" class="form-control" id="pwd" name="status" value="Belum Di Ajukan">

  <div class="form-group">
    <button type="submit" class="btn btn-primary btn-submit-project" name="submit">Submit</button>
  </div>
</form>



<script>
  $(document).ready(function() {
    $('#project').change(function() {
      $('.nama').val($("#project option:selected").text())
    })

    $('#jenis').change(function() {
      $('#project option').remove();

      $('#projectDiv').hide();
      $('#tahunNonrutDiv').hide();
      $('#katnon').hide();
      $('#picNonRutDiv').hide();
      $('#namanon').hide();
      $('#tahunDiv').hide();
      $('#picDiv').hide();

      const jenis = $(this).val();
      if (jenis == 'B1' || jenis == 'B2') {
        $.ajax({
          url: "ajax/ajax-home-direksi.php",
          type: 'post',
          data: {
            'jenis': jenis
          },
          success: function(r) {
            r = JSON.parse(r);
            console.log(r);
            let html = `<option disabled selected>Pilih Project</option>`;
            for (let i = 0; i < r.name.length; i++) {
              html += `<option value="${r.id[i]}" data-table="${r.table[i]}">${r.name[i]}</option>`
            }
            $('#project').append(html);
            $('#projectDiv').show();
          }
        })
      } else {
        if (jenis == 'Non Rutin')
          $('#katnon').show();
        $('#tahunNonrutDiv').show();
        $('#picNonRutDiv').show();
        $('#namanon').show();
      }
    })

    $('#project').change(function() {
      const id = $(this).val();
      const jenis = $('#jenis').val();
      const table = $(this).find(':selected').data('table')

      if ($(this).val() == 'Non Rutin') {
        $('#katnon').show();
        $('#tahunNonrutDiv').show();
        $('#picNonRutDiv').show();
        $('#namanon').show();
        $('#jenis').val('Non Rutin');
      } else {
        $.ajax({
          url: "ajax/ajax-home-direksi.php",
          type: "post",
          data: {
            id: id,
            table: table
          },
          success: function(r) {
            const result = JSON.parse(r);
            if (jenis == 'B1' || jenis == 'B2') {
              $('#tahunDiv').show();
              $('#picDiv').show();
              $('#tahun').val(result.tahun);
              $('#pic').val(result.pic);
              // $('.nama').val(result.nama);
              $('#idUser').val(result.id_user);
              $('#table').val(table);
            }
          }
        })
      }
    })
    // $("#jenis").change(function() {
    //   if ($("#jenb1").is(":selected")) {
    //     $("#namab1").show();
    //     $("#katnon").hide();
    //   } else if ($("#nonrut").is(":selected")) {
    //     $("#katnon").show();
    //     $("#namab1").hide();
    //   } else {
    //     $("#namab1").hide();
    //     $("$katnon").hide();
    //   }
    // }).trigger('change');
  });
</script>