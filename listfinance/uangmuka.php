<?php
require_once "../application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
?>
<div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
          <div class="panel-body no-padding">
            <table class="table table-striped">
              <thead>
                <tr class="warning">
                  <th rowspan="2">No</th>
                  <th rowspan="2">Nama User</th>
                  <th rowspan="2">Limit</th>
                  <th colspan="3" class="text-center">Total Uang Muka</th>
                  <th rowspan="2">Sisa Limit</th>
                  <th rowspan="2">Action</th>
                </tr>
                <tr class="warning">
                  <th>Saldo Awal Outstanding</th>
                  <th>Pengajuan</th>
                  <th>Saldo Akhir Outstanding</th>
                </tr>
              </thead>

              <tbody>
                <?php
                $i = 1;
                $sql = mysqli_query($koneksi, "SELECT a.namapenerima, SUM(a.jumlah) AS total_pengajuan, c.saldo FROM bpu a JOIN selesai b ON a.waktu = b.waktu AND a.no = b.no JOIN tb_user c ON c.nama_user = a.namapenerima JOIN pengajuan d ON d.waktu = a.waktu WHERE b.status IN ('UM', 'UM Burek') AND a.status IN ('Telah Di Bayar', 'Belum Di Bayar') AND c.aktif = 'Y' GROUP BY a.namapenerima") or die(mysqli_error($koneksi));
                while ($d = mysqli_fetch_array($sql)) {
                  $sql2 = mysqli_query($koneksi, "SELECT SUM(jumlah) AS total_pengajuan FROM (SELECT DISTINCT a.* FROM bpu a JOIN selesai b ON a.waktu = b.waktu AND a.no = b.no WHERE b.status IN ('UM', 'UM Burek') AND a.status = 'Telah Di Bayar' AND a.namapenerima = '$d[namapenerima]') AS t") or die(mysqli_error($koneksi));
                  $terbayar = mysqli_fetch_assoc($sql2);
                  $sql3 = mysqli_query($koneksi, "SELECT SUM(jumlah) AS total_pengajuan FROM (SELECT DISTINCT a.* FROM bpu a JOIN selesai b ON a.waktu = b.waktu AND a.no = b.no WHERE b.status IN ('UM', 'UM Burek') AND a.status = 'Belum Di Bayar' AND a.namapenerima = '$d[namapenerima]') AS t") or die(mysqli_error($koneksi));
                  $belumTerbayar = mysqli_fetch_assoc($sql3);
                ?>
                  <tr>
                    <th bgcolor="#fcfaa4" scope="row"><?php echo $i++; ?></th>
                    <td bgcolor="#fcfaa4"><?php echo $d['namapenerima']; ?></td>
                    <td bgcolor="#fcfaa4">Rp. <?php echo number_format($d['saldo']); ?></td>
                    <td bgcolor="#fcfaa4">Rp. <?php echo number_format($terbayar['total_pengajuan']); ?></td>
                    <td bgcolor="#fcfaa4">Rp. <?php echo number_format($belumTerbayar['total_pengajuan']); ?></td>
                    <td bgcolor="#fcfaa4">Rp. <?php echo number_format($terbayar['total_pengajuan'] + $belumTerbayar['total_pengajuan']); ?></td>
                    <td bgcolor="#fcfaa4">Rp. <?php echo number_format($d['saldo'] - ($terbayar['total_pengajuan'] + $belumTerbayar['total_pengajuan'])); ?></td>
                    <td bgcolor="#fcfaa4"><a target="_blank" href="views-um.php?code=<?php echo $d['namapenerima']; ?>"><i class="fas fa-eye" title="View Detail Uang Muka"></i></a></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div><!-- /.table-responsive -->
        </div>