<?php
error_reporting(0);

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

if ($_POST['no'] && $_POST['waktu']) {
  $no     = $_POST['no'];
  $waktu  = $_POST['waktu'];
  // mengambil data berdasarkan id
  // dan menampilkan data ke dalam form modal bootstrap
?>

  <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th>No BPU</th>
        <th>Jumlah BPU</th>
        <th>Jenis BPU</th>
        <th>Pembayaran</th>
        <th>Realisasi</th>
        <th>Uang Kembali</th>
      </tr>
    </thead>

    <tbody>
      <?php
      $sql = "SELECT * FROM bpu WHERE waktu='$waktu' AND no='$no' ORDER BY term";
      $result = $koneksi->query($sql);
      foreach ($result as $baris) {
        $persetujuan = $baris['persetujuan'];
        $statusbayar = $baris['status'];
      ?>
        <tr>
          <td><?php echo $baris['term']; ?></td>
          <td><?php echo 'Rp. ' . number_format($baris['jumlah'], 0, '', ','); ?></td>
          <td><?php echo $baris['statusbpu']; ?></td>
          <td>
            <?php
            if ($persetujuan == 'Belum Disetujui' && $statusbayar == 'Belum Di Bayar') {
              echo "<i class='far fa-check-square'></i> Pengajuan ";
            } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)') && $statusbayar == 'Belum Di Bayar') {
              echo "<i class='far fa-check-square'></i> Approval";
            } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)') && ($statusbayar == 'Telah Di Bayar' || $statusbayar == 'Realisasi (Finance)' || $statusbayar == 'Realisasi (Direksi)')) {
              echo "<i class='far fa-check-square'></i> Paid ";
            }
            ?>
          </td>
          <td><?php echo 'Rp. ' . number_format($baris['realisasi'], 0, '', ','); ?></td>
          <td><?php echo 'Rp. ' . number_format($baris['uangkembali'], 0, '', ','); ?></td>
        </tr>
      <?php
      }
      ?>
    </tbody>
  </table>

  <h5>

    <?php
    $query2 = "SELECT sum(jumlah) AS sum, sum(uangkembali) AS sumkem, sum(realisasi) AS sumreal FROM bpu WHERE waktu='$waktu' AND no='$no'";
    $result2 = $koneksi->query($query2);
    $row2 = mysqli_fetch_array($result2);

    $query3 = "SELECT sum(jumlah) AS sumbel FROM bpu WHERE waktu='$waktu' AND no='$no' AND status='Belum Di Bayar'";
    $result3 = $koneksi->query($query3);
    $row3 = mysqli_fetch_array($result3);

    echo "Total BPU : ";
    echo 'Rp. ' . number_format($row2['sum'], 0, '', ',');
    echo "<br/>";
    echo "Total Realisasi : ";
    echo 'Rp. ' . number_format($row2['sumreal'], 0, '', ',');
    echo "<br/>";
    echo "Total Uang Kembali : ";
    echo 'Rp. ' . number_format($row2['sumkem'], 0, '', ',');
    echo "<br/>";
    echo "Total Outstanding : ";
    echo 'Rp. ' . number_format($row3['sumbel'], 0, '', ',');
    ?>
  </h5>

<?php }
$koneksi->close();
?>