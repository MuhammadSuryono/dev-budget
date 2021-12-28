<?php
    require "../application/config/database.php";
    session_start();
    $con = new Database();
    $koneksi = $con->connect();
    $i = 1;
    // $sql = mysqli_query($koneksi, "SELECT a.namapenerima, SUM(a.jumlah) AS total_pengajuan, c.saldo FROM bpu a JOIN selesai b ON a.waktu = b.waktu AND a.no = b.no JOIN tb_user c ON c.nama_user = a.namapenerima JOIN pengajuan d ON d.waktu = a.waktu WHERE b.status IN ('UM', 'UM Burek') AND a.status IN ('Telah Di Bayar', 'Belum Di Bayar') AND c.aktif = 'Y' AND c.divisi = '$_SESSION[divisi]' GROUP BY a.namapenerima") or die(mysqli_error($koneksi));
    $sql = mysqli_query($koneksi, "SELECT * FROM tb_user where divisi = '$_SESSION[divisi]' AND aktif = 'Y'");

    if (mysqli_num_rows($sql)) {
        while ($d = mysqli_fetch_assoc($sql)) :
            // $sql2 = mysqli_query($koneksi, "SELECT SUM(jumlah) AS total_pengajuan FROM (SELECT DISTINCT a.* FROM bpu a JOIN selesai b ON a.waktu = b.waktu AND a.no = b.no WHERE b.status IN ('UM', 'UM Burek') AND a.status = 'Telah Di Bayar' AND a.namapenerima = '$d[namapenerima]') AS t") or die(mysqli_error($koneksi));
            // $terbayar = mysqli_fetch_assoc($sql2);
            // $sql3 = mysqli_query($koneksi, "SELECT SUM(jumlah) AS total_pengajuan FROM (SELECT DISTINCT a.* FROM bpu a JOIN selesai b ON a.waktu = b.waktu AND a.no = b.no WHERE b.status IN ('UM', 'UM Burek') AND a.status = 'Belum Di Bayar' AND a.namapenerima = '$d[namapenerima]') AS t") or die(mysqli_error($koneksi));
            // $belumTerbayar = mysqli_fetch_assoc($sql3);

            $sql2 = mysqli_query($koneksi, "SELECT SUM(jumlah) AS total_pengajuan FROM (SELECT DISTINCT a.* FROM bpu a JOIN selesai b ON a.waktu = b.waktu AND a.no = b.no WHERE b.status IN ('UM', 'UM Burek') AND a.status = 'Telah Di Bayar' AND a.namapenerima = '$d[nama_user]') AS t") or die(mysqli_error($koneksi));
            $terbayar = mysqli_fetch_assoc($sql2);
            $sql3 = mysqli_query($koneksi, "SELECT SUM(jumlah) AS total_pengajuan FROM (SELECT DISTINCT a.* FROM bpu a JOIN selesai b ON a.waktu = b.waktu AND a.no = b.no WHERE b.status IN ('UM', 'UM Burek') AND a.status = 'Belum Di Bayar' AND a.namapenerima = '$d[nama_user]') AS t") or die(mysqli_error($koneksi));
            $belumTerbayar = mysqli_fetch_assoc($sql3);

            // $qSisaRealisasi = mysqli_query($koneksi, "SELECT SUM(jumlah - realisasi) as sisa FROM budget.bpu where namapenerima = '$d[nama_user]' AND statusbpu IN ('UM', 'UM Burek') AND status = 'Telah Di Bayar'");
            // $sisaRealisasi = mysqli_fetch_assoc($qSisaRealisasi);


            $queryBpuRealisasi = mysqli_query($koneksi, "SELECT SUM(a.realisasi) AS total_realisasi FROM bpu a JOIN selesai b ON a.waktu = b.waktu AND a.no = b.no WHERE b.status IN ('UM', 'UM Burek') AND a.namapenerima = '$d[nama_user]' AND a.realisasi + a.uangkembali != a.jumlah AND a.status IN ('Telah Di Bayar','Realisasi (Direksi)')") or die(mysqli_error($koneksi));
            $pengajuanRealisasi = mysqli_fetch_assoc($queryBpuRealisasi);
            
            $totalSaldoOutstanding = ($terbayar['total_pengajuan'] + $belumTerbayar['total_pengajuan']) - $pengajuanRealisasi['total_realisasi'];
            ?>
        
            <div class="list-group-item border" id="grandparent<?= $d['nama_user'] ?>" style="border: 1px solid black !important;">
                <div id="expander" data-target="#grandparentContent<?= $d['nama_user'] ?>" data-toggle="collapse" data-group-id="grandparent<?= $d['nama_user'] ?>" data-role="expander">
                <ul class="list-inline row border">
                    <li class="col-lg-1"><?= $i++ ?>. <?= $d['nama_user'] ?></li>
                    <li class="col-lg-1">Rp. <?php echo number_format($d['saldo']); ?></li>
                    <li class="col-lg-2">Rp. <?php echo number_format($terbayar['total_pengajuan']); ?></li>
                    <li class="col-lg-2">Rp. <?php echo number_format($belumTerbayar['total_pengajuan']); ?></li>
                    <li class="col-lg-2">Rp. <?php echo number_format($pengajuanRealisasi['total_realisasi']); ?></li>
                    <li class="col-lg-2">Rp. <?php echo number_format($totalSaldoOutstanding) ?></li>
                    <li class="col-lg-1">Rp. <?php echo number_format($d['saldo'] - $totalSaldoOutstanding) ?></li>
                    <li class="col-lg-1">
                    <a href="views-um.php?code=<?=$d['nama_user']?>"><i class="fas fa-eye" title="View Rincian"></i></a>
                    </li>
                </ul>
                </div>
            </div>
        <?php endwhile; } else { echo '<div class="text-center">Tidak Ditemukan Data</div>'; } ?>                    
    