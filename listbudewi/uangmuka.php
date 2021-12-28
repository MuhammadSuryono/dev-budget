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
          <th colspan="4" class="text-center">Total Uang Muka</th>
          <th rowspan="2">Sisa Limit</th>
          <th rowspan="2">Action</th>
        </tr>
        <tr class="warning">
          <th>Saldo Awal Outstanding</th>
          <th>Pengajuan</th>
          <th>Realisasi</th>
          <th>Saldo Akhir Outstanding</th>
        </tr>
      </thead>

      <tbody>
        <?php
        $i = 1;
        $sql = mysqli_query($koneksi, "SELECT a.namapenerima, SUM(a.jumlah) AS total_pengajuan FROM bpu a JOIN selesai b ON a.waktu = b.waktu AND a.no = b.no JOIN pengajuan d ON d.waktu = a.waktu WHERE b.status IN ('UM', 'UM Burek') AND a.status IN ('Telah Di Bayar', 'Belum Di Bayar') GROUP BY a.namapenerima") or die(mysqli_error($koneksi));
        while ($d = mysqli_fetch_array($sql)) {
            $queryUser = mysqli_query($koneksi, "SELECT saldo FROM tb_user WHERE nama_user = '$d[namapenerima]' AND aktif = 'Y'");
            if (mysqli_num_rows($queryUser) > 0) {
                $user = mysqli_fetch_assoc($queryUser);
                $sql2 = mysqli_query($koneksi, "SELECT SUM(jumlah) AS total_pengajuan FROM (SELECT DISTINCT a.* FROM bpu a JOIN selesai b ON a.waktu = b.waktu AND a.no = b.no WHERE b.status IN ('UM', 'UM Burek') AND a.status = 'Telah Di Bayar' AND a.namapenerima = '$d[namapenerima]') AS t") or die(mysqli_error($koneksi));
                $terbayar = mysqli_fetch_assoc($sql2);
                $sql3 = mysqli_query($koneksi, "SELECT SUM(jumlah) AS total_pengajuan FROM (SELECT DISTINCT a.* FROM bpu a JOIN selesai b ON a.waktu = b.waktu AND a.no = b.no WHERE b.status IN ('UM', 'UM Burek') AND a.status = 'Belum Di Bayar' AND a.namapenerima = '$d[namapenerima]') AS t") or die(mysqli_error($koneksi));
                $belumTerbayar = mysqli_fetch_assoc($sql3);

                // $qSisaRealisasi = mysqli_query($koneksi, "SELECT SUM(jumlah - realisasi) as sisa FROM budget.bpu where namapenerima = '$d[namapenerima]' AND statusbpu IN ('UM', 'UM Burek') AND status = 'Telah Di Bayar'");
                // $sisaRealisasi = mysqli_fetch_assoc($qSisaRealisasi);


                $queryBpuRealisasi = mysqli_query($koneksi, "SELECT SUM(a.realisasi) AS total_realisasi FROM bpu a JOIN selesai b ON a.waktu = b.waktu AND a.no = b.no WHERE b.status IN ('UM', 'UM Burek') AND a.namapenerima = '$d[namapenerima]' AND a.realisasi + a.uangkembali != a.jumlah AND a.status IN ('Telah Di Bayar','Realisasi (Direksi)')") or die(mysqli_error($koneksi));
                $pengajuanRealisasi = mysqli_fetch_assoc($queryBpuRealisasi);
                
                $totalSaldoOutstanding = ($terbayar['total_pengajuan'] + $belumTerbayar['total_pengajuan']) - $pengajuanRealisasi['total_realisasi'];
        ?>
          <tr>
            <th bgcolor="#fcfaa4" scope="row"><?php echo $i++; ?></th>
            <td bgcolor="#fcfaa4"><?php echo $d['namapenerima']; ?></td>
            <td bgcolor="#fcfaa4">Rp. <?php echo number_format($user['saldo']); ?></td>
            <td bgcolor="#fcfaa4">Rp. <?php echo number_format($terbayar['total_pengajuan']); ?></td>
            <td bgcolor="#fcfaa4">Rp. <?php echo number_format($belumTerbayar['total_pengajuan']); ?></td>
          <td bgcolor="#fcfaa4">Rp. <?php echo number_format($pengajuanRealisasi['total_realisasi']); ?></td>
            <td bgcolor="#fcfaa4">Rp. <?php echo number_format($totalSaldoOutstanding) ?></td>
            <td bgcolor="#fcfaa4">Rp. <?php echo number_format($user['saldo'] - $totalSaldoOutstanding) ?></td>
            <td bgcolor="#fcfaa4"><a target="_blank" href="views-um.php?code=<?php echo $d['namapenerima']; ?>"><i class="fas fa-eye" title="View Detail Uang Muka"></i></a></td>
          </tr>
        <?php } } ?>
      </tbody>
    </table>
  </div><!-- /.table-responsive -->
</div>