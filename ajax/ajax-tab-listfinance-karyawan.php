<?php
require "../application/config/database.php";

$con = new Database();
$koneksi = $con->connect();;

$tab = $_POST['tab'];
$tahun = $_POST['tahun'];
$aksesSes = $_POST['hak_akses'];
if (strpos($tab, 'B1') !== false) : ?>
    <div role="tabpanel" class="tab-pane fade in active" id="B1" aria-labelledby="home-tab">
        <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
          <div class="panel-body no-padding">
            <table class="table table-striped">
              <thead>
                <tr class="warning">
                  <th>No</th>
                  <th>Nama Project</th>
                  <th>Tahun</th>
                  <th>Nama Yang Mengajukan</th>
                  <th>Divisi</th>
                  <th>Total</th>
                  <th>Total Biaya dan Uang Muka</th>
                  <th>Sisa Budget</th>
                  <th>View</th>
                  <th>Persetujuan</th>
                  <th>Status</th>
                </tr>
              </thead>

              <tbody>

                <?php
                $i = 1;
                $checkWaktu = [];
                $sql = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE jenis ='B1' AND tahun = '$tahun' AND status !='Belum Di Ajukan' AND pengaju !='SRI DEWI MARPAUNG'");
                while ($d = mysqli_fetch_array($sql)) {
                  if (!in_array($d['waktu'], $checkWaktu)) :
                    $waktu = $d['waktu'];
                    $query2 = "SELECT sum(jumlah) AS sumasum FROM bpu WHERE waktu='$waktu'";
                    $result2 = mysqli_query($koneksi, $query2);
                    $row2 = mysqli_fetch_array($result2);
  
                    $query10 = "SELECT sum(uangkembali) AS sum FROM bpu WHERE waktu='$waktu'";
                    $result10 = mysqli_query($koneksi, $query10);
                    $row10 = mysqli_fetch_array($result10);
                    $tysb = $row2['sumasum'] - $row10['sum'];

                    $queryTotalBudget = mysqli_query($koneksi, "SELECT sum(total) as total_budget FROM selesai WHERE waktu = '$d[waktu]'");
                            $dataTotalBudget = mysqli_fetch_assoc($queryTotalBudget);
  
                    $aaaa = $dataTotalBudget['total_budget'];
                    $bbbb = $row2['sumasum'];
                    $belumbayar = $aaaa - $bbbb;

                    if ($d['status'] == "Disetujui") {
                ?>
                      <tr>
                        <th bgcolor="#fcfaa4" scope="row"><?php echo $i++; ?></th>
                        <td bgcolor="#fcfaa4"><?php echo $d['nama']; ?></td>
                        <td bgcolor="#fcfaa4"><?php echo $d['tahun']; ?></td>
                        <td bgcolor="#fcfaa4"><?php echo $d['pengaju']; ?></td>
                        <td bgcolor="#fcfaa4"><?php echo $d['divisi']; ?></td>

                          <td bgcolor="#fcfaa4"><?php echo 'Rp. ' . number_format($dataTotalBudget['total_budget'], 0, '', ','); ?></td>
                          <td bgcolor="#fcfaa4">
                              <font color="#1bd34f"><?php echo 'Rp. ' . number_format($row2['sumasum'], 0, '', ','); ?></font>
                          </td>
                          <td bgcolor="#fcfaa4">
                              <font color="#f23f2b"><?php echo 'Rp. ' . number_format($belumbayar, 0, '', ','); ?></font>
                              </font>
                          </td>
                        <td bgcolor="#fcfaa4">
                          <?php
                          if ($_SESSION['hak_akses'] == 'Suci Indah Sari') {
                          ?>
                            <a href="view-finance.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-dollar-sign" title="VIEW-finance"></i></a>
                            <a href="views.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a>
                          <?php
                          } else if ($aksesSes == 'Manager') {
                          ?>
                            <a href="view-finance-manager.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a>
                          <?php } else { ?>
                            <a href="view-finance.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a>
                          <?php } ?>
                        </td>
                        <td bgcolor="#fcfaa4">
                            <center>--</center>
                        </td>
                        <td bgcolor="#fcfaa4"><?php echo $d['status']; ?></td>
                      </tr>
                    <?php
                    } else { ?>
                      <tr>
                        <th scope="row"><?php echo $i++; ?></th>
                        <td><?php echo $d['nama']; ?></td>
                        <td><?php echo $d['tahun']; ?></td>
                        <td><?php echo $d['pengaju']; ?></td>
                        <td><?php echo $d['divisi']; ?></td>
                        <td><?php echo 'Rp. ' . number_format($dataTotalBudget['total_budget'], 0, '', ','); ?></td>
                          <td>
                              <font color="#1bd34f"><?php echo 'Rp. ' . number_format($row2['sumasum'], 0, '', ','); ?></font>
                          </td>
                          <td>
                              <font color="#f23f2b"><?php echo 'Rp. ' . number_format($belumbayar, 0, '', ','); ?></font>
                              </font>
                          </td>
                        <td>--</td>
                        <td><?php echo $d['status']; ?></td>
                      </tr>
                <?php }
                    array_push($checkWaktu, $d['waktu']);
                  endif;
                } ?>
              </tbody>
            </table>
          </div><!-- /.table-responsive -->
        </div>
      </div>
<?php elseif (strpos($tab, 'B2') !== false) : ?>
  <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
          <div class="panel-body no-padding">
            <table class="table table-striped">
              <thead>
                <tr class="warning">
                  <th>No</th>
                  <th>Nama Project</th>
                  <th>Tahun</th>
                  <th>Nama Yang Mengajukan</th>
                  <th>Divisi</th>
                  <th>Total</th>
                  <th>Total Biaya dan Uang Muka</th>
                  <th>Sisa Budget</th>
                  <th>View</th>
                  <th>Persetujuan</th>
                  <th>Status</th>
                </tr>
              </thead>

              <tbody>

                <?php
                $i = 1;
                $sql = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE jenis ='B2' AND tahun = '$tahun' AND status !='Belum Di Ajukan' AND pengaju !='SRI DEWI MARPAUNG'");
                while ($d = mysqli_fetch_array($sql)) {
                  $waktu = $d['waktu'];
                  $query2 = "SELECT sum(jumlah) AS sumasum FROM bpu WHERE waktu='$waktu'";
                  $result2 = mysqli_query($koneksi, $query2);
                  $row2 = mysqli_fetch_array($result2);

                  $query10 = "SELECT sum(uangkembali) AS sum FROM bpu WHERE waktu='$waktu'";
                  $result10 = mysqli_query($koneksi, $query10);
                  $row10 = mysqli_fetch_array($result10);
                  $tysb = $row2['sumasum'] - $row10['sum'];

                  $queryTotalBudget = mysqli_query($koneksi, "SELECT sum(total) as total_budget FROM selesai WHERE waktu = '$d[waktu]'");
                            $dataTotalBudget = mysqli_fetch_assoc($queryTotalBudget);

                  $aaaa = $dataTotalBudget['total_budget'];
                  $bbbb = $row2['sumasum'];
                  $belumbayar = $aaaa - $bbbb;

                  if ($d['status'] == "Disetujui") {
                ?>
                    <tr>
                      <th bgcolor="#fcfaa4" scope="row"><?php echo $i++; ?></th>
                      <td bgcolor="#fcfaa4"><?php echo $d['nama']; ?></td>
                      <td bgcolor="#fcfaa4"><?php echo $d['tahun']; ?></td>
                      <td bgcolor="#fcfaa4"><?php echo $d['pengaju']; ?></td>
                      <td bgcolor="#fcfaa4"><?php echo $d['divisi']; ?></td>
                      <td bgcolor="#fcfaa4"><?php echo 'Rp. ' . number_format($dataTotalBudget['total_budget'], 0, '', ','); ?></td>
                          <td bgcolor="#fcfaa4">
                              <font color="#1bd34f"><?php echo 'Rp. ' . number_format($row2['sumasum'], 0, '', ','); ?></font>
                          </td>
                          <td bgcolor="#fcfaa4">
                              <font color="#f23f2b"><?php echo 'Rp. ' . number_format($belumbayar, 0, '', ','); ?></font>
                              </font>
                          </td>
                      <td bgcolor="#fcfaa4">
                        <?php
                        if ($_SESSION['nama_user'] == 'Suci Indah Sari') {
                        ?>
                          <a href="view-finance.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-dollar-sign" title="VIEW-finance"></i></a>
                          <a href="views.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a>
                        <?php
                        } else if ($aksesSes == 'Manager') {
                        ?>
                          <a href="view-finance-manager.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a>
                        <?php } else { ?>
                          <a href="view-finance.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a>
                        <?php } ?>
                      </td>
                        <td>--</td>
                      <td bgcolor="#fcfaa4"><?php echo $d['status']; ?></td>
                    </tr>
                  <?php
                  } else { ?>
                    <tr>
                      <th scope="row"><?php echo $i++; ?></th>
                      <td><?php echo $d['nama']; ?></td>
                      <td><?php echo $d['tahun']; ?></td>
                      <td><?php echo $d['pengaju']; ?></td>
                      <td><?php echo $d['divisi']; ?></td>
                      <td><?php echo 'Rp. ' . number_format($dataTotalBudget['total_budget'], 0, '', ','); ?></td>
                          <td>
                              <font color="#1bd34f"><?php echo 'Rp. ' . number_format($row2['sumasum'], 0, '', ','); ?></font>
                          </td>
                          <td>
                              <font color="#f23f2b"><?php echo 'Rp. ' . number_format($belumbayar, 0, '', ','); ?></font>
                              </font>
                          </td>
                      <td>--</td>
                      <td><?php echo $d['status']; ?></td>
                    </tr>
                <?php }
                } ?>
              </tbody>
            </table>
          </div><!-- /.table-responsive -->
        </div>
<?php elseif (strpos($tab, 'Rutin') !== false) : ?>
    <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
        <div class="panel-body no-padding">
            <table class="table table-striped table-bordered">
            <thead>
                    <tr class="warning">
                      <th>No</th>
                      <th>Nama Project</th>
                      <th>Tahun</th>
                      <th>Nama Yang Mengajukan</th>
                      <th>Divisi</th>
                      <th>Total</th>
                      <th>Total Biaya dan Uang Muka</th>
                      <th>Sisa Budget</th>
                      <th>View</th>
                      <th>Persetujuan</th>
                    </tr>
                  </thead>

                  <tbody>

                    <?php
                    $i = 1;
                    $sql = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE jenis ='Rutin' AND tahun = '$tahun' AND status !='Belum Di Ajukan'");
                    while ($d = mysqli_fetch_array($sql)) {
                      $waktu = $d['waktu'];
                      $query2 = "SELECT sum(jumlah) AS sumasum FROM bpu WHERE waktu='$waktu'";
                      $result2 = mysqli_query($koneksi, $query2);
                      $row2 = mysqli_fetch_array($result2);
    
                      $query10 = "SELECT sum(uangkembali) AS sum FROM bpu WHERE waktu='$waktu'";
                      $result10 = mysqli_query($koneksi, $query10);
                      $row10 = mysqli_fetch_array($result10);
                      $tysb = $row2['sumasum'] - $row10['sum'];

                      $queryTotalBudget = mysqli_query($koneksi, "SELECT sum(total) as total_budget FROM selesai WHERE waktu = '$d[waktu]'");
                            $dataTotalBudget = mysqli_fetch_assoc($queryTotalBudget);
    
                      $aaaa = $dataTotalBudget['total_budget'];
                      $bbbb = $row2['sumasum'];
                      $belumbayar = $aaaa - $bbbb;

                      if ($d['status'] == "Disetujui") {
                    ?>
                        <tr>
                          <th bgcolor="#fcfaa4" scope="row"><?php echo $i++; ?></th>
                          <td bgcolor="#fcfaa4"><?php echo $d['nama']; ?></td>
                          <td bgcolor="#fcfaa4"><?php echo $d['tahun']; ?></td>
                          <td bgcolor="#fcfaa4"><?php echo $d['pengaju']; ?></td>
                          <td bgcolor="#fcfaa4"><?php echo $d['divisi']; ?></td>
                        <td bgcolor="#fcfaa4"><?php echo 'Rp. ' . number_format($dataTotalBudget['total_budget'], 0, '', ','); ?></td>
                            <td bgcolor="#fcfaa4">
                                <font color="#1bd34f"><?php echo 'Rp. ' . number_format($row2['sumasum'], 0, '', ','); ?></font>
                            </td>
                            <td bgcolor="#fcfaa4">
                                <font color="#f23f2b"><?php echo 'Rp. ' . number_format($belumbayar, 0, '', ','); ?></font>
                                </font>
                            </td>
                          <td bgcolor="#fcfaa4">
                            <?php
                            if ($aksesSes == 'Manager') {
                            ?>
                              <a href="view-finance-manager.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a>
                            <?php
                            } else {
                            ?>
                              <a href="view-finance.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a>
                            <?php } ?>
                          </td>
                          <td bgcolor="#fcfaa4"><?php echo $d['status']; ?></td>
                        </tr>
                      <?php
                      } else { ?>
                        <tr>
                          <th scope="row"><?php echo $i++; ?></th>
                          <td><?php echo $d['nama']; ?></td>
                          <td><?php echo $d['tahun']; ?></td>
                          <td><?php echo $d['pengaju']; ?></td>
                          <td><?php echo $d['divisi']; ?></td>
                        <td bgcolor="#fcfaa4"><?php echo 'Rp. ' . number_format($dataTotalBudget['total_budget'], 0, '', ','); ?></td>
                            <td bgcolor="#fcfaa4">
                                <font color="#1bd34f"><?php echo 'Rp. ' . number_format($row2['sumasum'], 0, '', ','); ?></font>
                            </td>
                            <td bgcolor="#fcfaa4">
                                <font color="#f23f2b"><?php echo 'Rp. ' . number_format($belumbayar, 0, '', ','); ?></font>
                                </font>
                            </td>
                          <td>--</td>
                          <td><?php echo $d['status']; ?></td>
                        </tr>
                    <?php }
                    } ?>
                  </tbody>
            </table>
        </div><!-- /.table-responsive -->
    </div>
<?php elseif (strpos($tab, 'Non') !== false) : ?>
    <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
  <div class="panel-body no-padding">
    <table class="table table-striped table-bordered">
    <thead>
                    <tr class="warning">
                      <th>No</th>
                      <th>Nama Project</th>
                      <th>Tahun</th>
                      <th>Nama Yang Mengajukan</th>
                      <th>Divisi</th>
                      <th>Total</th>
                      <th>Total Biaya dan Uang Muka</th>
                      <th>Sisa Budget</th>
                      <th>View</th>
                      <th>Persetujuan</th>
                    </tr>
                  </thead>

                  <tbody>
                    <?php
                    $i = 1;
                    $sql = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE jenis ='Non Rutin' AND tahun = '$tahun' AND status !='Belum Di Ajukan'");
                    while ($d = mysqli_fetch_array($sql)) {
                      $waktu = $d['waktu'];
                      $query2 = "SELECT sum(jumlah) AS sumasum FROM bpu WHERE waktu='$waktu'";
                      $result2 = mysqli_query($koneksi, $query2);
                      $row2 = mysqli_fetch_array($result2);
    
                      $query10 = "SELECT sum(uangkembali) AS sum FROM bpu WHERE waktu='$waktu'";
                      $result10 = mysqli_query($koneksi, $query10);
                      $row10 = mysqli_fetch_array($result10);
                      $tysb = $row2['sumasum'] - $row10['sum'];

                      $queryTotalBudget = mysqli_query($koneksi, "SELECT sum(total) as total_budget FROM selesai WHERE waktu = '$d[waktu]'");
                            $dataTotalBudget = mysqli_fetch_assoc($queryTotalBudget);
    
                      $aaaa = $dataTotalBudget['total_budget'];
                      $bbbb = $row2['sumasum'];
                      $belumbayar = $aaaa - $bbbb;

                      if ($d['status'] == "Disetujui") {
                    ?>
                        <tr>
                          <th bgcolor="#fcfaa4" scope="row"><?php echo $i++; ?></th>
                          <td bgcolor="#fcfaa4"><?php echo $d['nama']; ?></td>
                          <td bgcolor="#fcfaa4"><?php echo $d['tahun']; ?></td>
                          <td bgcolor="#fcfaa4"><?php echo $d['pengaju']; ?></td>
                          <td bgcolor="#fcfaa4"><?php echo $d['divisi']; ?></td>
                        <td bgcolor="#fcfaa4"><?php echo 'Rp. ' . number_format($dataTotalBudget['total_budget'], 0, '', ','); ?></td>
                            <td bgcolor="#fcfaa4">
                                <font color="#1bd34f"><?php echo 'Rp. ' . number_format($row2['sumasum'], 0, '', ','); ?></font>
                            </td>
                            <td bgcolor="#fcfaa4">
                                <font color="#f23f2b"><?php echo 'Rp. ' . number_format($belumbayar, 0, '', ','); ?></font>
                                </font>
                            </td>
                          <td bgcolor="#fcfaa4"><a href="view-finance-nonrutin.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a></td>
                          <td bgcolor="#fcfaa4"><?php echo $d['status']; ?></td>
                        </tr>
                      <?php
                      } else { ?>
                        <tr>
                          <th scope="row"><?php echo $i++; ?></th>
                          <td><?php echo $d['nama']; ?></td>
                          <td><?php echo $d['tahun']; ?></td>
                          <td><?php echo $d['pengaju']; ?></td>
                          <td><?php echo $d['divisi']; ?></td>
                        <td bgcolor="#fcfaa4"><?php echo 'Rp. ' . number_format($dataTotalBudget['total_budget'], 0, '', ','); ?></td>
                            <td bgcolor="#fcfaa4">
                                <font color="#1bd34f"><?php echo 'Rp. ' . number_format($row2['sumasum'], 0, '', ','); ?></font>
                            </td>
                            <td bgcolor="#fcfaa4">
                                <font color="#f23f2b"><?php echo 'Rp. ' . number_format($belumbayar, 0, '', ','); ?></font>
                                </font>
                            </td>
                          <td>--</td>
                          <td><?php echo $d['status']; ?></td>
                        </tr>
                    <?php }
                    } ?>
                  </tbody>
    </table>
  </div><!-- /.table-responsive -->
</div>
<?php endif; ?>