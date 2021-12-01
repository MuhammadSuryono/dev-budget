<?php
    require "../application/config/database.php";
    session_start();
    $con = new Database();
    $koneksi = $con->connect();
    $i = 1;
    $sql = mysqli_query($koneksi, "SELECT a.namapenerima, SUM(a.jumlah) AS total_pengajuan, c.saldo FROM bpu a JOIN selesai b ON a.waktu = b.waktu AND a.no = b.no JOIN tb_user c ON c.nama_user = a.namapenerima JOIN pengajuan d ON d.waktu = a.waktu WHERE b.status IN ('UM', 'UM Burek') AND a.status IN ('Telah Di Bayar', 'Belum Di Bayar') AND c.aktif = 'Y' AND c.divisi = '$_SESSION[divisi]' GROUP BY a.namapenerima") or die(mysqli_error($koneksi));

    if (mysqli_num_rows($sql)) {
        while ($d = mysqli_fetch_assoc($sql)) :
            $sql2 = mysqli_query($koneksi, "SELECT SUM(jumlah) AS total_pengajuan FROM (SELECT DISTINCT a.* FROM bpu a JOIN selesai b ON a.waktu = b.waktu AND a.no = b.no WHERE b.status IN ('UM', 'UM Burek') AND a.status = 'Telah Di Bayar' AND a.namapenerima = '$d[namapenerima]') AS t") or die(mysqli_error($koneksi));
            $terbayar = mysqli_fetch_assoc($sql2);
            $sql3 = mysqli_query($koneksi, "SELECT SUM(jumlah) AS total_pengajuan FROM (SELECT DISTINCT a.* FROM bpu a JOIN selesai b ON a.waktu = b.waktu AND a.no = b.no WHERE b.status IN ('UM', 'UM Burek') AND a.status = 'Belum Di Bayar' AND a.namapenerima = '$d[namapenerima]') AS t") or die(mysqli_error($koneksi));
            $belumTerbayar = mysqli_fetch_assoc($sql3);
            ?>
        
            <div class="list-group-item border" id="grandparent<?= $d['namapenerima'] ?>" style="border: 1px solid black !important;">
                <div id="expander" data-target="#grandparentContent<?= $d['namapenerima'] ?>" data-toggle="collapse" data-group-id="grandparent<?= $d['namapenerima'] ?>" data-role="expander">
                <ul class="list-inline row border">
                    <li class="col-lg-1"><?= $i++ ?>. <?= $d['namapenerima'] ?></li>
                    <li class="col-lg-2">Rp. <?php echo number_format($d['saldo']); ?></li>
                    <li class="col-lg-2">Rp. <?php echo number_format($terbayar['total_pengajuan']); ?></li>
                    <li class="col-lg-2">Rp. <?php echo number_format($belumTerbayar['total_pengajuan']); ?></li>
                    <li class="col-lg-2">Rp. <?php echo number_format($terbayar['total_pengajuan'] + $belumTerbayar['total_pengajuan']); ?></li>
                    <li class="col-lg-2">Rp. <?php echo number_format($d['saldo'] - ($terbayar['total_pengajuan'] + $belumTerbayar['total_pengajuan'])); ?></li>
                    <li class="col-lg-1">
                    <span id="grandparentIcon<?= $d['namapenerima'] ?>" style="cursor: pointer; margin: 0 10px;" class="col-lg-1"><a><i class="fas fa-eye" title="View Rincian"></i></a></span>
                    </li>
                </ul>
                </div>
                <div class="collapse" id="grandparentContent<?= $d['namapenerima'] ?>" aria-expanded="true">
                <h3 class="text-center">Outstanding</h3>
                <table class="table table-striped">
                    <thead>
                    <tr class="warning">
                        <th>No.</th>
                        <th>Nama Project</th>
                        <th>Nomor Item Budget</th>
                        <th>Rincian Item Budget</th>
                        <th>Term Bpu</th>
                        <th>Jumlah</th>
                        <th>Tanggal Bayar</th>
                    </tr>
                    </thead>
        
                    <tbody>
                    <?php
                    $j = 1;
                    $checkUnique = [];
                    $queryDetailBpu = mysqli_query($koneksi, "SELECT a.*, b.rincian FROM bpu a JOIN selesai b ON a.waktu = b.waktu AND a.no = b.no JOIN pengajuan c ON c.waktu = a.waktu WHERE b.status IN ('UM', 'UM Burek') AND a.namapenerima = '$d[namapenerima]' AND a.status = 'Telah Di Bayar'") or die(mysqli_error($koneksi));
                    if (mysqli_num_rows($queryDetailBpu)) {
                        while ($item2 = mysqli_fetch_assoc($queryDetailBpu)) :
                        $unique = $item2['waktu'] . $item2['nama'] . $item2['no'] . $item2['rincian'] . $item2['term'];
                        if (!in_array($unique, $checkUnique)) :
        
                            $totalTerbayar += $item2['jumlah'];
                    ?>
                            <tr data-toggle="collapse" data-target=".child1">
                            <td><?= $j++ ?></td>
                            <td><?= $item2['namaproject'] ?></td>
                            <td><?= $item2['no'] ?></td>
                            <td><?= $item2['rincian'] ?></td>
                            <td><?= $item2['term'] ?></td>
                            <td>Rp.<?= number_format($item2['jumlah']) ?></td>
                            <td><?= $item2['tanggalbayar'] ?></td>
                            </tr>
                            <?php array_push($checkUnique, $unique); ?>
                        <?php endif; ?>
                        <?php endwhile; ?>
                    <?php } else { ?>
                        <tr data-toggle="collapse" data-target=".child1">
                        <!-- <td></td> -->
                        <td>Tidak ada outstanding</td>
                        <!-- <td></td> -->
                        <!-- <td></td> -->
                        </tr>
                    <?php } ?>
        
                    </tbody>
                </table>
                <br>
                <h3 class="text-center">Pengajuan</h3>
                <table class="table table-striped">
                    <thead>
                    <tr class="warning">
                        <th>No.</th>
                        <th>Nama Project</th>
                        <th>Nomor Item Budget</th>
                        <th>Rincian Item Budget</th>
                        <th>Term Bpu</th>
                        <th>Jumlah</th>
                    </tr>
                    </thead>
        
                    <tbody>
                    <?php
                    $checkUnique = [];
                    $queryDetailBpu = mysqli_query($koneksi, "SELECT a.*, b.rincian, c.nama AS namaproject FROM bpu a JOIN selesai b ON a.waktu = b.waktu AND a.no = b.no JOIN pengajuan c ON c.waktu = a.waktu WHERE b.status IN ('UM', 'UM Burek') AND a.namapenerima = '$d[namapenerima]' AND a.status = 'Belum Di Bayar'") or die(mysqli_error($koneksi));
                    if (mysqli_num_rows($queryDetailBpu)) {
                        while ($item2 = mysqli_fetch_assoc($queryDetailBpu)) :
        
                        $unique = $item2['waktu'] . $item2['nama'] . $item2['no'] . $item2['rincian'] . $item2['term'];
                        if (!in_array($unique, $checkUnique)) :
                            $totalBelumTerbayar += $item2['jumlah'];
                    ?>
                            <tr data-toggle="collapse" data-target=".child1">
                            <td><?= $j++ ?></td>
                            <td><?= $item2['namaproject'] ?></td>
                            <td><?= $item2['no'] ?></td>
                            <td><?= $item2['rincian'] ?></td>
                            <td><?= $item2['term'] ?></td>
                            <td>Rp.<?= number_format($item2['jumlah']) ?></td>
                            </tr>
                            <?php array_push($checkUnique, $unique); ?>
                        <?php endif; ?>
                        <?php endwhile; ?>
                    <?php } else { ?>
                        <td>Tidak ada pengajuan Uang Muka</td>
                    <?php } ?>
                    </tbody>
                </table>
                </div>
            </div>
        <?php endwhile; } else { echo '<div class="text-center">Tidak Ditemukan Data</div>'; } ?>                    
    