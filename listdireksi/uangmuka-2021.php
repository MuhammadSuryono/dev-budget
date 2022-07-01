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

                    $query = mysqli_query($koneksi, "SELECT a.nama, a.waktu, a.noid, a.jenis FROM pengajuan a JOIN bpu b ON a.waktu = b.waktu JOIN selesai c ON b.waktu = c.waktu where b.namapenerima = '$d[namapenerima]' GROUP BY nama");

                    $totalTerbayar = 0;
                    $totalRealisasi = 0;
                    $totalSaldoOutstanding = 0;
                    $totalBelumTerbayar = 0;
                    $code = $d['namapenerima'];
                    $total  = 0;
                    while ($item = mysqli_fetch_assoc($query)) {
                        $queryBpu = mysqli_query($koneksi, "SELECT SUM(a.jumlah) AS total_pengajuan FROM bpu a JOIN selesai b ON a.waktu = b.waktu AND a.no = b.no WHERE b.status IN ('UM', 'UM Burek') AND a.namapenerima = '$code' AND a.waktu = '$item[waktu]' AND a.realisasi + a.uangkembali != a.jumlah AND a.status IN ('Telah Di Bayar', 'Belum Di Bayar', 'Realisasi (Direksi)')") or die(mysqli_error($koneksi));
                        $pengajuan = mysqli_fetch_assoc($queryBpu);

                        // Pengajuan yang sudha dibayar dan sudah di realisasi
                        $queryBpuTerbayar = mysqli_query($koneksi, "SELECT SUM(a.jumlah) AS total_pengajuan FROM bpu a JOIN selesai b ON a.waktu = b.waktu AND a.no = b.no WHERE b.status IN ('UM', 'UM Burek') AND a.namapenerima = '$code' AND a.waktu = '$item[waktu]' AND a.status IN ('Telah Di Bayar','Realisasi (Direksi)')") or die(mysqli_error($koneksi));
                        $pengajuanTerbayar = mysqli_fetch_assoc($queryBpuTerbayar);

                        // Pengajuan yang belum dibayar
                        $queryBpuBelumTerbayar = mysqli_query($koneksi, "SELECT SUM(a.jumlah) AS total_pengajuan FROM bpu a JOIN selesai b ON a.waktu = b.waktu AND a.no = b.no WHERE b.status IN ('UM', 'UM Burek') AND a.namapenerima = '$code' AND a.waktu = '$item[waktu]' AND a.status = 'Belum Di Bayar'") or die(mysqli_error($koneksi));
                        $pengajuanBelumTerbayar = mysqli_fetch_assoc($queryBpuBelumTerbayar);

                        $queryBpuRealisasi = mysqli_query($koneksi, "SELECT SUM(a.realisasi) + SUM(a.uangkembali) AS total_realisasi FROM bpu a JOIN selesai b ON a.waktu = b.waktu AND a.no = b.no WHERE b.status IN ('UM', 'UM Burek') AND a.namapenerima = '$code' AND a.waktu = '$item[waktu]' AND a.realisasi + a.uangkembali = a.jumlah AND a.status IN ('Telah Di Bayar','Realisasi (Direksi)')") or die(mysqli_error($koneksi));
                        $pengajuanRealisasi = mysqli_fetch_assoc($queryBpuRealisasi);
                        if ($pengajuan['total_pengajuan'] != null) {
                            $total += $pengajuan['total_pengajuan'];
                            $totalRealisasi += $pengajuanRealisasi['total_realisasi'];

                            $totalTerbayar += $pengajuanTerbayar['total_pengajuan'];
                            $totalBelumTerbayar += $pengajuanBelumTerbayar['total_pengajuan'];
                            $totalSaldoOutstanding += ($pengajuanTerbayar['total_pengajuan'] + $pengajuanBelumTerbayar['total_pengajuan']) - $pengajuanRealisasi['total_realisasi'];
                        }
                    }
                    ?>
                    <tr>
                        <th bgcolor="#fcfaa4" scope="row"><?php echo $i++; ?></th>
                        <td bgcolor="#fcfaa4"><?php echo $d['namapenerima']; ?></td>
                        <td bgcolor="#fcfaa4">Rp. <?php echo number_format($user['saldo']); ?></td>
                        <td bgcolor="#fcfaa4">Rp. <?php echo number_format($totalTerbayar); ?></td>
                        <td bgcolor="#fcfaa4">Rp. <?php echo number_format($totalBelumTerbayar); ?></td>
                        <td bgcolor="#fcfaa4">Rp. <?php echo number_format($totalRealisasi); ?></td>
                        <td bgcolor="#fcfaa4">Rp. <?php echo number_format($totalSaldoOutstanding) ?></td>
                        <td bgcolor="#fcfaa4">Rp. <?php echo number_format($user['saldo'] - $totalSaldoOutstanding) ?></td>
                        <td bgcolor="#fcfaa4"><a target="_blank" href="views-um.php?code=<?php echo $d['namapenerima']; ?>"><i class="fas fa-eye" title="View Detail Uang Muka"></i></a></td>
                    </tr>
                <?php } } ?>
            </tbody>
        </table>
    </div><!-- /.table-responsive -->
</div>