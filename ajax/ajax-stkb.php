<?php
require "../application/config/database.php";

$con = new Database();
$koneksi = $con->connect();;

$year = $_POST['year'];
?>
<div id="stkb" class="tab-pane fade in active">

    <table class="table table-striped table-bordered">
        <thead>
            <tr class="warning">
                <th>#</th>
                <th>No Item</th>
                <th>Project</th>
                <th>Rincian</th>
                <th>Jenis</th>
                <th>Harga Satuan (IDR)</th>
                <th>Quantity</th>
                <th>Total (IDR)</th>
                <th>Sisa Pembayaran</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $i = 1;
            $carihonor = mysqli_query($koneksi, "SELECT * FROM selesai WHERE rincian LIKE '%STKB%' AND waktu IN (SELECT waktu FROM pengajuan WHERE jenis='B1' AND status='Disetujui' AND tahun = '$year')");
            while ($ch = mysqli_fetch_array($carihonor)) {
            ?>
                <tr>
                    <td><?php echo $i++ ?></td>
                    <td><?php echo $ch['no']; ?></td>
                    <td>
                        <?php
                        $waks = $ch['waktu'];
                        $carinama = mysqli_query($koneksi, "SELECT nama FROM pengajuan WHERE waktu='$waks'");
                        $cn = mysqli_fetch_assoc($carinama);
                        echo $cn['nama'];
                        ?>
                    </td>
                    <td><?php echo $ch['rincian']; ?></td>
                    <td><?php echo $ch['status']; ?></td>
                    <td><?php echo 'Rp. ' . number_format($ch['harga'], 0, '', ','); ?></td>
                    <td><?php echo $ch['quantity']; ?></td>
                    <td><?php echo 'Rp. ' . number_format($ch['total'], 0, '', ','); ?></td>
                    <!-- Sisa Pembayaran -->
                    <?php
                    $no = $ch['no'];
                    $waktu = $ch['waktu'];
                    $pilihtotal = mysqli_query($koneksi, "SELECT total FROM selesai WHERE no='$no' AND waktu='$waktu'");
                    $aw = mysqli_fetch_assoc($pilihtotal);
                    $hargaah = $aw['total'];
                    $query = "SELECT sum(jumlah) AS sum FROM bpu WHERE no='$no' AND waktu='$waktu'";
                    $result = mysqli_query($koneksi, $query);
                    $row = mysqli_fetch_array($result);
                    $total = $row[0];
                    $query16 = "SELECT sum(uangkembali) AS sum FROM bpu WHERE no='$no' AND waktu='$waktu'";
                    $result16 = mysqli_query($koneksi, $query16);
                    $row16 = mysqli_fetch_array($result16);
                    $total16 = $row16[0];

                    $jadinya = $hargaah - $total + $total16;
                    ?>
                    <td><?php echo 'Rp. ' . number_format($jadinya, 0, '', ','); ?></td>
                    <!-- //Sisa Pembayaran -->

                    <!-- Tombol Eksternal -->
                    <?php

                    // $crbpu = mysqli_query($koneksi, "SELECT * FROM bpu WHERE no ='$no' AND waktu = '$waktu'");

                    if ($ch['status'] == 'UM') {
                    ?>
                        <td>
                            <button type="button" class="btn btn-info btn-small" onclick="realisasi('<?php echo $no; ?>','<?php echo $waktu; ?>')">Realisasi</button>
                            <br /><br />
                            <button type="button" class="btn btn-default btn-small" onclick="edit_row('<?php echo $no; ?>','<?php echo $waktu; ?>')">Edit</button>
                            <br /><br />
                            <button type="button" class="btn btn-danger btn-small" onclick="hapus_row('<?php echo $no; ?>','<?php echo $waktu; ?>')">Hapus</button>
                        </td>
                    <?php
                    } else if ($ch['status'] == 'Biaya External' || $ch['status'] == 'Biaya' || $ch['status'] == 'Pulsa' || $ch['status'] == 'Biaya Lumpsum') {
                    ?>
                        <td>
                            <button type="button" class="btn btn-default btn-small" onclick="edit_row('<?php echo $no; ?>','<?php echo $waktu; ?>')">Edit</button>
                            <br /><br />
                            <button type="button" class="btn btn-danger btn-small" onclick="hapus_row('<?php echo $no; ?>','<?php echo $waktu; ?>')">Hapus</button>
                        </td>
                    <?php
                    } else {
                    ?>
                        <td>
                            <button type="button" class="btn btn-success btn-small" onclick="eksternal('<?php echo $no; ?>','<?php echo $waktu; ?>')">Eksternal</button>
                            <br /><br />
                            <button type="button" class="btn btn-default btn-small" onclick="edit_row('<?php echo $no; ?>','<?php echo $waktu; ?>')">Edit</button>
                            <br /><br />
                            <button type="button" class="btn btn-danger btn-small" onclick="hapus_row('<?php echo $no; ?>','<?php echo $waktu; ?>')">Hapus</button>
                        </td>
                        <!-- BPU -->
                        <?php
                    }

                    $liatbayar = mysqli_query($koneksi, "SELECT * FROM bpu WHERE waktu='$waktu' AND no='$no' ORDER BY term");
                    if (mysqli_num_rows($liatbayar) == 0) {
                        echo "";
                    } else {
                        while ($bayar = mysqli_fetch_array($liatbayar)) {
                            $noidbpu          = $bayar['noid'];
                            $jumlbayar        = $bayar['jumlah'];
                            $tglbyr           = $bayar['tglcair'];
                            $statusbayar      = $bayar['status'];
                            $persetujuan      = $bayar['persetujuan'];
                            $bayarfinance     = $bayar['jumlahbayar'];
                            $novoucher        = $bayar['novoucher'];
                            $tanggalbayar     = $bayar['tanggalbayar'];
                            $pengaju          = $bayar['pengaju'];
                            $divisi2          = $bayar['divisi'];
                            $namabank         = $bayar['namabank'];
                            $norek            = $bayar['norek'];
                            $namapenerima     = $bayar['namapenerima'];
                            $alasan           = $bayar['alasan'];
                            $realisasi        = $bayar['realisasi'];
                            $uangkembali      = $bayar['uangkembali'];
                            $tanggalrealisasi = $bayar['tanggalrealisasi'];
                            $waktustempel     = $bayar['waktustempel'];
                            $pembayar         = $bayar['pembayar'];
                            $tglcair          = $bayar['tglcair'];
                            $term             = $bayar['term'];
                            $statusbpu        = $bayar['statusbpu'];
                            $fileupload       = $bayar['fileupload'];
                            $noStkb       = ($bayar['nomorstkb']) ? $bayar['nomorstkb'] : '-';
                            $kembreal         = $realisasi + $uangkembali;
                            $sisarealisasi    = $jumlbayar - $kembreal;


                            if ($uangkembali == 0) {
                                $jumlahjadi = $jumlbayar;
                            } else if ($kembreal < $jumlbayar) {
                                $jumlahjadi = $jumlbayar;
                            } else {
                                $jumlahjadi = $realisasi;
                            }

                            $selstat = mysqli_query($koneksi, "SELECT status FROM selesai WHERE waktu='$waktu' AND no='$no'");
                            $ss = mysqli_fetch_assoc($selstat);
                            $exin = $ss['status'];

                            if ($persetujuan == 'Belum Disetujui' && $statusbayar == 'Belum Di Bayar') {
                                $color = '#ffd3d3';
                            } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)') && $statusbayar == 'Belum Di Bayar') {
                                // $color = 'orange';
                                $color = '#fff5c6';
                            } else if ($persetujuan == 'Pending' && $statusbayar == 'Belum Di Bayar') {
                                $color = 'orange';
                            } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)') && $statusbayar == 'Telah Di Bayar' && ($exin == 'Honor Eksternal' || $exin == 'Vendor/Supplier' || $exin == 'Lumpsum')) {
                                $color = '#d5f9bd';
                            } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)') && $statusbayar == 'Telah Di Bayar' && ($exin == 'Pulsa' || $exin == 'Biaya External' || $exin == 'Biaya' || $exin == 'Biaya Lumpsum')) {
                                $color = '#d5f9bd';
                            } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)') && $statusbayar == 'Telah Di Bayar' && $exin == 'UM') {
                                $color = '#8aad70';
                            } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)') && $statusbayar == 'Realisasi (Direksi)' && $exin == 'UM') {
                                $color = '#d5f9bd';
                            } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)') && $statusbayar == 'Realisasi (Finance)' && $exin == 'UM') {
                                $color = '#d5f9bd';
                            }


                            echo "<td bgcolor=' $color '>";
                            echo "No :<b> $term";
                            echo "</b><br>";
                            echo "No. STKB :<b> $noStkb";
                            echo "</b><br>";
                            echo "BPU : <br><b>Rp. " . number_format($jumlbayar, 0, '', ',');
                            echo "</b><br>";
                            if ($realisasi != 0 && $statusbayar == 'Telah Di Bayar' && $statusbpu == 'UM') {
                                echo "Realisasi Biaya : <br><b>Rp. " . number_format($kembreal, 0, '', ',');
                                echo "</b><br>";
                                echo "Sisa Realisasi: <br><b>Rp. " . number_format($sisarealisasi, 0, '', ',');
                                echo "</b><br>";
                            } else if ($statusbayar == 'Realisasi (Direksi)') {
                                echo "Realisasi Biaya: <br><b>Rp. " . number_format($realisasi, 0, '', ',');
                                echo "</b><br>";
                            } else {
                                echo "";
                            }
                            echo "Tanggal : <br><b> " . date('Y-m-d', strtotime($waktustempel));
                            echo "</b><br>";
                            echo "Jam : <b>" . date('H:i:s', strtotime($waktustempel));
                            echo "</b></br>";
                            echo "Tanggal Terima Uang : <b>$tglcair ";
                            echo "</b></br>";
                            echo "Dibuat Oleh : <br><b> $pengaju($divisi2)";
                            echo "</b><br>";
                            echo "Dibayarkan Kepada : <br><b> $namapenerima ";
                            echo "</b><br>";
                            echo "No Rekening :<b> $norek";
                            echo "</b><br>";
                            echo "Bank :<b> $namabank";
                            echo "</b><br>";
                            echo "No Voucher : <br><b> $novoucher ";
                            echo "</b><br/>";
                            echo "Tgl Bayar : <br><b> $tanggalbayar";
                            echo "</b><br/>";
                            echo "Kasir : <br><b> $pembayar ";
                            echo "</b><br/>";
                            if ($fileupload != NULL) {
                                echo "File Upload : <br>";
                                echo "<a href='uploads/$fileupload' target='_blank'><i class='fa fa-file'></i></a>";
                                echo "<br/><br/>";
                            } else {
                                echo "";
                            }
                            if ($persetujuan == 'Belum Disetujui' && $statusbayar == 'Belum Di Bayar') {
                                echo "<i class='far fa-check-square'></i> Pengajuan ";
                                echo "</b><br/>";
                                echo "<i class='far fa-square'></i> Approval ";
                                echo "</b><br/>";
                                echo "<i class='far fa-square'></i> Paid ";
                                echo "</b><br/>";
                            } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)') && $statusbayar == 'Belum Di Bayar') {
                                echo "<i class='far fa-check-square'></i> Pengajuan";
                                echo "</b><br/>";
                                echo "<i class='far fa-check-square'></i> Approval";
                                echo "</b><br/>";
                                echo "<i class='far fa-square'></i> Paid ";
                                echo "</b><br/>";
                            } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)') && ($statusbayar == 'Telah Di Bayar' || $statusbayar == 'Realisasi (Finance)' || $statusbayar == 'Realisasi (Direksi)')) {
                                echo "<i class='far fa-check-square'></i> Pengajuan";
                                echo "</b><br/>";
                                echo "<i class='far fa-check-square'></i> Approval";
                                echo "</b><br/>";
                                echo "<i class='far fa-check-square'></i> Paid ";
                                echo "</b><br/>";
                            }
                            if ($statusPengajuanRealisasi != 4 && !($exin == 'Honor Eksternal' || $exin == 'Vendor/Supplier' || $exin == 'Lumpsum' || $exin == 'Honor SHP Jabodetabek' ||
                                $exin == 'Honor SHI/PWT Jabodetabek' || $exin == 'Honor SHP Luar Kota' || $exin == 'Honor SHI/PWT Luar Kota' ||
                                $exin == 'Honor Jakarta' || $exin == 'Honor Luar Kota' || $exin == 'STKB TRK Jakarta' || $exin == 'STKB TRK Luar Kota' || $exin == 'STKB OPS')) {
                                echo "<i class='far fa-square'></i> Realisasi ";
                                echo "</b><br/>";
                            } else {
                                echo "<i class='far fa-check-square'></i> Realisasi ";
                                echo "</b><br/>";
                            }

                            if ($persetujuan == 'Pending' || $persetujuan == 'Belum Disetujui') {
                                echo "Komentar : <br><b> $alasan ";
                                echo "</b><br/>";
                        ?>
                                <button type="button" class="btn btn-success btn-small" onclick="edit_budget('<?php echo $no; ?>','<?php echo $waktu; ?>')">Setujui</button>
                                </br>
                                <button type="button" class="btn btn-warning btn-small" onclick="editharga('<?php echo $noidbpu; ?>','<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Edit</button>
                                </br>
                                <button type="button" class="btn btn-danger btn-small" onclick="hapus_bpu('<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Hapus</button>
                            <?php
                            } else if (($persetujuan == 'Disetujui (Direksi)' || $persetujuan == 'Disetujui (Sri Dewi Marpaung)') and $statusbayar == 'Belum Di Bayar') {
                            ?>
                                <button type="button" class="btn btn-warning btn-small" onclick="editharga('<?php echo $noidbpu; ?>','<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Edit</button>
                                </br>
                                <button type="button" class="btn btn-danger btn-small" onclick="hapus_bpu('<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Hapus</button>

                            <?php
                            } else if ($statusbayar == 'Realisasi (Finance)' || $statusbayar == 'Realisasi (Direksi)' || $uangkembali != 0) {
                                echo "Uang Kembali :<br><b> Rp. " . number_format($uangkembali, 0, '', ',');
                                echo "</b><br/>";
                            ?>
                                <button type="button" class="btn btn-warning btn-small" onclick="editharga('<?php echo $noidbpu; ?>','<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Edit</button>
                                </br>
                                <button type="button" class="btn btn-danger btn-small" onclick="hapus_bpu('<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Hapus</button>
                            <?php
                            } else {
                            ?>
                                <button type="button" class="btn btn-warning btn-small" onclick="editharga('<?php echo $noidbpu; ?>','<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Edit</button>
                                </br>
                                <button type="button" class="btn btn-danger btn-small" onclick="hapus_bpu('<?php echo $no; ?>','<?php echo $waktu; ?>','<?php echo $term; ?>')">Hapus</button>
                    <?php
                            }
                            echo "</td>";
                        }
                    }
                    ?>
                    <!-- //BPU -->
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>