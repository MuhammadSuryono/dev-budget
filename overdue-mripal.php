<?php

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

?>
<div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
    <!-- PROJECT -->
    <div class="panel-body no-padding">
        <h3>
            <center>Project<center>
        </h3>
        <h4>List Overdue</h4>
        <table class="table table-striped table-bordered">
            <thead>
                <tr class="warning">
                    <th>#</th>
                    <th>Jenis</th>
                    <th>Project</th>
                    <th>Item</th>
                    <th>Kategori</th>
                    <th>Request BPU</th>
                    <th>Tanggal</th>
                    <th>Penerima</th>
                    <th>Pengaju(Divisi)</th>
                    <th>Paid</th>
                </tr>
            </thead>

            <tbody>

                <?php
                $i = 1;
                $sql = mysqli_query($koneksi, "SELECT * FROM bpu WHERE metode_pembayaran = 'MRI PAL' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND statusbpu !='UM' AND tglcair < CURDATE() AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1')
                                                    OR metode_pembayaran = 'MRI PAL' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND statusbpu !='UM' AND tglcair < CURDATE() AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1')
                                                    OR metode_pembayaran = 'MRI PAL' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND statusbpu !='UM' AND tglcair < CURDATE() AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B2')
                                                    OR metode_pembayaran = 'MRI PAL' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND statusbpu !='UM' AND tglcair < CURDATE() AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B2')
                                                    ORDER BY tglcair");
                while ($d = mysqli_fetch_array($sql)) {
                    $waktu = $d['waktu'];
                    $no    = $d['no'];
                    $term  = $d['term'];
                ?>
                    <tr>
                        <th scope="row"><?php echo $i++; ?></th>

                        <td>
                            <!-- Nama Jenis -->
                            <?php
                            $namajenis = mysqli_query($koneksi, "SELECT jenis FROM pengajuan WHERE waktu='$d[waktu]'");
                            $namjen = mysqli_fetch_assoc($namajenis);
                            echo $namjen['jenis'];
                            ?>
                        </td>

                        <td>
                            <!-- Nama Project -->
                            <?php
                            $namaproject = mysqli_query($koneksi, "SELECT nama FROM pengajuan WHERE waktu='$d[waktu]'");
                            $nampro = mysqli_fetch_assoc($namaproject);
                            echo $nampro['nama'];
                            ?>
                        </td>

                        <td>
                            <!-- Item -->
                            <?php
                            $namarincian = mysqli_query($koneksi, "SELECT no,rincian FROM selesai WHERE waktu='$d[waktu]' AND no='$d[no]'");
                            $namrin = mysqli_fetch_assoc($namarincian);
                            echo "<b>" . $namrin['no'] . "</b>." . $namrin['rincian'];
                            ?>
                        </td>
                        <td><?php echo $d['statusbpu']; ?></td>
                        <td><?php echo 'Rp. ' . number_format($d['jumlah'], 0, '', ','); ?></td>
                        <td><?php echo $d['tglcair']; ?></td>
                        <td><?php echo $d['namapenerima']; ?></td>
                        <td><?php echo $d['pengaju'] . "(" . $d['divisi'] . ")"; ?>
                        <td><button type="button" class="btn btn-success btn-md" onclick="saveData('<?php echo $waktu; ?>','<?php echo $no; ?>','<?php echo $term; ?>')"><i class="fas fa-angle-double-right"></i> Paid</button></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <?php
        $wewew = mysqli_query($koneksi, "SELECT sum(jumlah) AS sumia FROM bpu WHERE metode_pembayaran = 'MRI PAL' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND statusbpu !='UM' AND tglcair < CURDATE() AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1')
                                                OR metode_pembayaran = 'MRI PAL' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND statusbpu !='UM' AND tglcair < CURDATE() AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1')
                                                OR metode_pembayaran = 'MRI PAL' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND statusbpu !='UM' AND tglcair < CURDATE() AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B2')
                                                OR metode_pembayaran = 'MRI PAL' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND statusbpu !='UM' AND tglcair < CURDATE() AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B2')");
        $t = mysqli_fetch_array($wewew);

        $totalhonor = mysqli_query($koneksi, "SELECT sum(jumlah) AS sumtot FROM bpu WHERE status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND statusbpu ='Honor Eksternal' AND tglcair < CURDATE() AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1')
                                                                          OR metode_pembayaran = 'MRI PAL' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND statusbpu ='Honor Eksternal' AND tglcair < CURDATE() AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1')");
        $h = mysqli_fetch_array($totalhonor);
        ?>
        <h5>
            <div class="row">
                <div class="col-xs-3">Total Honor SHP dan PWT</div>
                <div class="col-xs-3">: <b><?php echo 'Rp. ' . number_format($h['sumtot'], 0, '', ','); ?></b></div>
            </div>


            <div class="row">
                <div class="col-xs-3">Total Pengajuan KAS</div>
                <div class="col-xs-3">: <b><?php echo 'Rp. ' . number_format($t['sumia'], 0, '', ','); ?></b></div>
            </div>
        </h5>
    </div><!-- /.table-responsive -->

    <!-- NON PROJECT -->
    <div class="panel-body no-padding">
        <h3>
            <center>NON Project<center>
        </h3>
        <h4>List Overdue</h4>
        <table class="table table-striped table-bordered">
            <thead>
                <tr class="warning">
                    <th>#</th>
                    <th>Jenis</th>
                    <th>Project</th>
                    <th>Item</th>
                    <th>Kategori</th>
                    <th>Request BPU</th>
                    <th>Tanggal</th>
                    <th>Penerima</th>
                    <th>Pengaju(Divisi)</th>
                </tr>
            </thead>

            <tbody>

                <?php
                
                $i = 1;
                $sql = mysqli_query($koneksi, "SELECT * FROM bpu WHERE metode_pembayaran = 'MRI PAL' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND tglcair < CURDATE() AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Rutin')
                                                    OR metode_pembayaran = 'MRI PAL' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND tglcair < CURDATE() AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Rutin')
                                                    OR metode_pembayaran = 'MRI PAL' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND tglcair < CURDATE() AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Non Rutin')
                                                    OR metode_pembayaran = 'MRI PAL' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND tglcair < CURDATE() AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Non Rutin')
                                                    ORDER BY tglcair");
                while ($d = mysqli_fetch_array($sql)) {
                    $waktu = $d['waktu'];
                    $no    = $d['no'];
                    $term  = $d['term'];
                ?>
                    <tr>
                        <th scope="row"><?php echo $i++; ?></th>

                        <td>
                            <!-- Nama Jenis -->
                            <?php
                            $namajenis = mysqli_query($koneksi, "SELECT jenis FROM pengajuan WHERE waktu='$d[waktu]'");
                            $namjen = mysqli_fetch_assoc($namajenis);
                            echo $namjen['jenis'];
                            ?>
                        </td>

                        <td>
                            <!-- Nama Project -->
                            <?php
                            $namaproject = mysqli_query($koneksi, "SELECT nama FROM pengajuan WHERE waktu='$d[waktu]'");
                            $nampro = mysqli_fetch_assoc($namaproject);
                            echo $nampro['nama'];
                            ?>
                        </td>

                        <td>
                            <!-- Nama Project -->
                            <?php
                            $namarincian = mysqli_query($koneksi, "SELECT no,rincian FROM selesai WHERE waktu='$d[waktu]' AND no='$d[no]'");
                            $namrin = mysqli_fetch_assoc($namarincian);
                            echo "<b>" . $namrin['no'] . "</b>." . $namrin['rincian'];
                            ?>
                        </td>

                        <td><?php echo $d['statusbpu']; ?></td>
                        <td><?php echo 'Rp. ' . number_format($d['jumlah'], 0, '', ','); ?></td>
                        <td><?php echo $d['tglcair']; ?></td>
                        <td><?php echo $d['namapenerima']; ?></td>
                        <td><?php echo $d['pengaju'] . "(" . $d['divisi'] . ")"; ?>
                        <td><button type="button" class="btn btn-success btn-md" onclick="saveData('<?php echo $waktu; ?>','<?php echo $no; ?>','<?php echo $term; ?>')"><i class="fas fa-angle-double-right"></i> Paid</button></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <?php
        $wewew = mysqli_query($koneksi, "SELECT sum(jumlah) AS sumia FROM bpu WHERE metode_pembayaran = 'MRI PAL' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND tglcair < CURDATE() AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Rutin')
                                                OR metode_pembayaran = 'MRI PAL' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND tglcair < CURDATE() AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Rutin')
                                                OR metode_pembayaran = 'MRI PAL' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Direksi)' AND tglcair < CURDATE() AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Non Rutin')
                                                OR metode_pembayaran = 'MRI PAL' AND status='Belum Di Bayar' AND persetujuan ='Disetujui (Sri Dewi Marpaung)' AND tglcair < CURDATE() AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='Non Rutin')");
        $t = mysqli_fetch_array($wewew);
        ?>
        <h4>Total Pengajuan KAS : <?php echo 'Rp. ' . number_format($t['sumia'], 0, '', ','); ?></h4>
    </div><!-- /.table-responsive -->
</div>