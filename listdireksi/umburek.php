<?php
require_once "../application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
?>
<div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
  <div class="panel-body no-padding">

    <br><br>

    <ul class="nav nav-tabs">
      <li class="active"><a href="#uber">List</a></li>
      <li><a href="#overdue">Overdue</a></li>
    </ul>


    <div class="tab-content">

      <div id="uber" class="tab-pane fade in active">
        <table class="table table-striped table-bordered">
          <thead>
            <tr class="warning">
              <th>#</th>
              <th>Nama (Divisi)</th>
              <th>Level</th>
              <th>Limit UM</th>
              <th>UM On Process</th>
              <th>Saldo UM</th>
              <th>BPU</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $i = 1;
            $carinama = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE saldo IS NOT NULL ORDER BY nama_user");
            while ($cn = mysqli_fetch_array($carinama)) {
            ?>
              <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo $cn['nama_user'] ?> (<?php echo $cn['divisi'] ?>)</td>
                <td><?php echo $cn['level']; ?></td>
                <td><?php echo 'Rp. ' . number_format($cn['saldo'], 0, '', ','); ?></td>
                <td>
                  <?php
                  $namauser = $cn['nama_user'];
                  $iduser   = $cn['id_user'];
                  $carisaldo = mysqli_query($koneksi, "SELECT sum(jumlah) AS sumjum FROM bpu WHERE namapenerima='$namauser' AND status !='Realisasi (Direksi)' AND statusbpu IN ('UM', 'UM Burek')");
                  $cs = mysqli_fetch_array($carisaldo);
                  echo 'Rp. ' . number_format($cs['sumjum'], 0, '', ',');
                  ?>
                </td>
                <td>
                  <?php
                  $umproses = $cs['sumjum'];
                  $limit    = $cn['saldo'];
                  $umsisa   = $limit - $umproses;
                  echo 'Rp. ' . number_format($umsisa, 0, '', ',');
                  ?>
                </td>
                <td><button type="button" class="btn btn-success btn-small" onclick="bpu_um('<?php echo $iduser; ?>')">BPU</button></td>
                <?php
                $bpusamping = mysqli_query($koneksi, "SELECT * FROM bpu WHERE namapenerima='$namauser' ORDER BY term");
                // foreach ($bpusamping as $b) {
                //   // $bpusamping =  mysqli_fetch_assoc($bpusamping);
                //   var_dump($b);
                // }
                // var_dump($bpusamping);
                if (mysqli_num_rows($bpusamping) == 0) {
                  echo "";
                } else {
                  while ($bayar = mysqli_fetch_array($bpusamping)) {
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
                    $nampro           = $bayar['project'];
                    $jatuhtempo       = $bayar['jatuhtempo'];


                    if ($uangkembali == 0) {
                      $jumlahjadi = $jumlbayar;
                    } else if ($kembreal < $jumlbayar) {
                      $jumlahjadi = $jumlbayar;
                    } else {
                      $jumlahjadi = $realisasi;
                    }

                    $selstat = mysqli_query($koneksi, "SELECT status FROM selesai WHERE waktu='$waktu' AND no='$noidbpu'");
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
                    echo "No. Term:<b> $term";
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
                    echo "Project : <br><b> $nampro";
                    echo "</b><br>";
                    echo "Jatuh Tempo : <br><b> $jatuhtempo";
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
                      <button type="button" class="btn btn-success btn-small" onclick="edit_budget('<?php echo $term; ?>','<?php echo $namapenerima; ?>')">Setujui</button>
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
                } ?>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>

      <div id="overdue" class="tab-pane fade">
        <h3>LIST UM Overdue</h3>
        <p>Belum Ada UM Overdue</p>
      </div>

    </div>



  </div><!-- /.table-responsive -->
</div>