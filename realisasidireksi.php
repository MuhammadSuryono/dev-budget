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

if ($_POST['no'] && $_POST['waktu']) {
  $id = $_POST['no'];
  $waktu = $_POST['waktu'];
  $tanggal = date("Y-m-d");


  // mengambil data berdasarkan id
  // dan menampilkan data ke dalam form modal bootstrap
  $sql = "SELECT * FROM selesai WHERE no = '$id' AND waktu = '$waktu'";
  $result = $koneksi->query($sql);
  foreach ($result as $baris) {
    // var_dump($baris);

?>

    <script>
      // $(document).ready(function(){
      //   $("#total_realisasi").val("aeryhsaetyh");
      // })
      //
      // var $cs   = $('.charge').change(function () {
      //   var total = +$('.total').html().trim() || 0;
      //   var v = $(this).data('cash');
      //   if (this.checked) {
      //     total += v;
      //   } else {
      //       total -= v;
      //   }
      //   $('.total').html(total);
      // });
      //
      // $('.charge:checked').change();

      // window.onload=function(){
      // var inputs = document.getElementsByClassName('sum'),
      //     total  = document.getElementById('payment-total');
      //
      //  for (var i=0; i < inputs.length; i++) {
      //     inputs[i].onchange = function() {
      //         var add = this.value * (this.checked ? 1 : -1);
      //         total.innerHTML = parseFloat(total.innerHTML) + add
      //         var new_total = parseFloat(document.getElementById('input').value);
      //       console.log(new_total);
      //         document.getElementById('input').value=new_total + add
      //     }
      //   }
      // }

      $(document).ready(function() {
        $(".percobaan").change(function() {
          var hasil = +$("#hasil").val().trim() || 0;
          console.log($("#hasil").val().trim());
          var angka = parseInt($(this).data("cash"));
          if (this.checked) {
            hasil += angka;
          } else {
            hasil -= angka;
          }

          $("#hasil").val(hasil);
        })
      })
    </script>

    <!-- MEMBUAT FORM -->

    <table class="table table-striped table-bordered">
      <thead>
        <tr>
          <th>#</th>
          <th>Gabungan Term</th>
          <th>BPU Uang Muka</th>
          <th>Total Realisasi</th>
          <th>Uang Kembali</th>
          <th>Sisa BPU</th>
        </tr>
      </thead>

      <tbody>
        <?php
        $i = 1;
        $rno = $baris['no'];
        $rwaktu = $baris['waktu'];
        $selreal = "SELECT * FROM realisasi WHERE no='$rno' AND waktu='$rwaktu'";
        $run_selreal = $koneksi->query($selreal);

        if (mysqli_num_rows($run_selreal) == 0) {
        ?>
          <tr>
            <td colspan="5">
              <center><b>Belum Ada Realisasi</b></center>
            </td>
          </tr>
          <?php
        } else {
          while ($rs = mysqli_fetch_array($run_selreal)) {
          ?>
            <tr>
              <td><?php echo $i++; ?></td>
              <td><?php echo $rs['term']; ?></td>
              <td>
                <?php
                $totalBpu = 0;
                $perterm = explode(',', $rs['term']);
                for ($i = 0; $i < count($perterm); $i++) {
                  $cariasli = "SELECT jumlah FROM bpu WHERE waktu='$rwaktu' AND no='$rno' AND term='$perterm[$i]'";
                  $run_cariasli = $koneksi->query($cariasli);
                  $rc = mysqli_fetch_assoc($run_cariasli);
                  $totalBpu += $rc['jumlah'];
                }
                echo 'Rp. ' . number_format($totalBpu, 0, '', ',');
                ?></td>
              <td><?php echo 'Rp. ' . number_format($rs['totalrealisasi'], 0, '', ','); ?></td>
              <td><?php echo 'Rp. ' . number_format($rs['uangkembali'], 0, '', ','); ?></td>
              <td>
                <?php
                $jumlah   = $totalBpu;
                $totreal  = $rs['totalrealisasi'];
                $uangkemb = $rs['uangkembali'];
                $trial    = $totreal + $uangkemb;
                $jadinya = $jumlah - $trial;
                echo 'Rp. ' . number_format($jadinya, 0, '', ',');
                ?>
              </td>
            </tr>
        <?php
          }
        }
        ?>
      </tbody>
    </table>

    <input type="hidden" name="no" value="<?php echo $baris['no']; ?>">
    <input type="hidden" name="waktu" value="<?php echo $baris['waktu']; ?>">
    <input type="hidden" name="tanggalrealisasi" value="<?php echo $tanggal; ?>">

    <div class="form-group">
      <label for="sel1">Gabungan BPU:</label>
      <?php
      $sql2 = "SELECT * FROM bpu WHERE no = '$id' AND waktu = '$waktu' AND status='Telah Di Bayar' ORDER BY term";
      $result2 = $koneksi->query($sql2);

      if (mysqli_num_rows($result2) == 0) {
        echo "<p>Tidak Ada BPU yang harus di Realisasi</p>";
      } else {
        foreach ($result2 as $baris2) {

          // var_dump($baris2);

          $jumlahbayar = $baris2['jumlahbayar'];
          $realisasi   = $baris2['realisasi'];
          $uangkembali = $baris2['uangkembali'];
          $realkem     = $realisasi + $uangkembali;
          $jaditotal   = $jumlahbayar - $realkem;

      ?>
          <div class="checkbox">
            <label><input type="checkbox" class="charge percobaan" name="term[]" data-cash="<?php echo $jaditotal; ?>" value="<?php echo $baris2['term']; ?>">Term <?php echo $baris2['term']; ?></label>
          </div>
      <?php
        }
      }
      ?>
    </div>
    <!-- <p id="test"></p> -->
    </form>

    <?php break; ?>

<?php }
}

$koneksi->close();
?>