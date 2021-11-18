<div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
  <div class="panel-body no-padding">
    <table id="example1" class="table table-striped table-bordered">
      <thead>
        <tr class="warning">
          <th>No</th>
          <th>Nama Project</th>
          <th>Tahun</th>
          <th>Nama Yang Mengajukan</th>
          <th>Divisi</th>
          <th>Total</th>
          <th>Sisa Budget</th>
          <th>Total DiBayar</th>
          <th>View</th>
          <th>Persetujuan</th>
          <th>Status</th>
          <th>Finish</th>
          <th>Action</th>
          <th>Disapprove</th>
        </tr>
      </thead>

      <tbody>

        <?php
        $i = 1;
        $sql = mysqli_query($koneksi, "SELECT
                                *
                              FROM
                                pengajuan
                              WHERE
                                 jenis = 'B1' AND STATUS = 'Disetujui' AND tahun = '2018'
                              OR jenis = 'B1' AND STATUS = 'Pending' AND tahun = '2018'
                              OR jenis = 'B1' AND STATUS = 'Disapprove' AND tahun = '2018'");
        while ($d = mysqli_fetch_array($sql)) {

          $waktu = $d['waktu'];
          $query2 = "SELECT sum(jumlahbayar) AS sumasum FROM bpu WHERE waktu='$waktu'";
          $result2 = mysqli_query($koneksi, $query2);
          $row2 = mysqli_fetch_array($result2);

          $query10 = "SELECT sum(uangkembali) AS sum FROM bpu WHERE waktu='$waktu'";
          $result10 = mysqli_query($koneksi, $query10);
          $row10 = mysqli_fetch_array($result10);
          $tysb = $row2['sumasum'] - $row10['sum'];

          $aaaa = $d['totalbudget'];
          $bbbb = $row2['sumasum'];
          $belumbayar = $aaaa - $bbbb;

          $query3 = "SELECT sum(jumlah) AS sumi FROM bpu WHERE waktu='$waktu' AND persetujuan='Disetujui (Direksi)' AND status='Belum Di Bayar'";
          $result3 = mysqli_query($koneksi, $query3);
          $row3 = mysqli_fetch_array($result3);

          $query12 = "SELECT sum(jumlah) AS sumin FROM bpu WHERE waktu='$waktu' AND persetujuan='Belum Disetujui' AND status='Belum Di Bayar'";
          $result12 = mysqli_query($koneksi, $query12);
          $row12 = mysqli_fetch_array($result12);

          if ($d['status'] == "Disetujui") {
        ?>
            <tr>
              <th bgcolor="#fcfaa4" scope="row"><?php echo $i++; ?></th>
              <td bgcolor="#fcfaa4">
                <?php
                echo $d['nama'];
                $carifile = mysqli_query($koneksi, "SELECT * FROM upload WHERE waktu='$waktu'");
                if (mysqli_num_rows($carifile) < 1) {
                  echo "";
                } else {
                  while ($cf = mysqli_fetch_array($carifile)) {
                    $gambar = $cf['gambar'];
                    echo " - ";
                    echo "<a href='uploads/$gambar'><i class='fa fa-file'></i></a>";
                  }
                }
                ?>
              </td>
              <td bgcolor="#fcfaa4"><?php echo $d['tahun']; ?></td>
              <td bgcolor="#fcfaa4"><?php echo $d['pengaju']; ?></td>
              <td bgcolor="#fcfaa4"><?php echo $d['divisi']; ?></td>
              <td bgcolor="#fcfaa4"><?php echo 'Rp. ' . number_format($d['totalbudget'], 0, '', ','); ?></td>
              <td bgcolor="#fcfaa4">
                <font color="#f23f2b"><?php echo 'Rp. ' . number_format($belumbayar, 0, '', ','); ?></font>
                </font>
              </td>
              <td bgcolor="#fcfaa4">
                <font color="#1bd34f"><?php echo 'Rp. ' . number_format($row2['sumasum'], 0, '', ','); ?></font>
              </td>
              <td bgcolor="#fcfaa4"><a href="views-direksi.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a></td>
              <td bgcolor="#fcfaa4">
                <center>--</center>
              </td>
              <td bgcolor="#fcfaa4"><?php echo $d['status']; ?></td>
              <?php
              echo "<td>";
              echo "<a href='#myModal3' id='custId' data-toggle='modal' data-id=" . $d['noid'] . "><button type='button' class='btn btn-success'>Finish</button></a>";
              echo "</td>";
              echo "<td>";
              echo "<a href='#myModal2' id='custId' data-toggle='modal' data-id=" . $d['noid'] . "><button type='button' class='btn btn-danger'>Hapus</button></a>";
              echo "</td>";
              echo "<td>";
              echo "<a href='#myModal6' id='custId' data-toggle='modal' data-id=" . $d['noid'] . "><button type='button' class='btn btn-warning'>Dissapprove</button></a>";
              echo "</td>";
              ?>
            </tr>
          <?php
          } else if ($d['status'] == "Disapprove") {
          ?>
            <tr>
              <th bgcolor="#fea700" scope="row"><?php echo $i++; ?></th>
              <td bgcolor="#fea700">
                <?php
                echo $d['nama'];
                $carifile = mysqli_query($koneksi, "SELECT * FROM upload WHERE waktu='$waktu'");
                if (mysqli_num_rows($carifile) < 1) {
                  echo "";
                } else {
                  while ($cf = mysqli_fetch_array($carifile)) {
                    $gambar = $cf['gambar'];
                    echo " - ";
                    echo "<a href='uploads/$gambar'><i class='fa fa-file'></i></a>";
                  }
                }
                ?>
              </td>
              <td bgcolor="#fea700"><?php echo $d['tahun']; ?></td>
              <td bgcolor="#fea700"><?php echo $d['pengaju']; ?></td>
              <td bgcolor="#fea700"><?php echo $d['divisi']; ?></td>
              <td bgcolor="#fea700"><?php echo 'Rp. ' . number_format($d['totalbudget'], 0, '', ','); ?></td>
              <td bgcolor="#fea700">
                <font color="#f23f2b"><?php echo 'Rp. ' . number_format($belumbayar, 0, '', ','); ?></font>
                </font>
              </td>
              <td bgcolor="#fea700">
                <font color="#1bd34f"><?php echo 'Rp. ' . number_format($row2['sumasum'], 0, '', ','); ?></font>
              </td>
              <td bgcolor="#fea700"><a href="view-direksi.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a></td>
              <td bgcolor="#fea700">
                <center>--</center>
              </td>
              <td bgcolor="#fea700"><?php echo $d['status']; ?></td>
              <?php
              echo "<td>";
              echo "<td><a href='#myModal' class='btn btn-default btn-small' id='custId' data-toggle='modal' data-id=" . $d['noid'] . ">Setujui</a></td>";
              echo "</td>";
              echo "<td>";
              echo "<a href='#myModal2' id='custId' data-toggle='modal' data-id=" . $d['noid'] . "><button type='button' class='btn btn-danger'>Hapus</button></a>";
              echo "</td>";
              ?>
            </tr>
          <?php
          } else {
          ?>
            <tr>
              <th scope="row"><?php echo $i++; ?></th>
              <td>
                <?php
                echo $d['nama'];
                $carifile = mysqli_query($koneksi, "SELECT * FROM upload WHERE waktu='$waktu'");
                if (mysqli_num_rows($carifile) < 1) {
                  echo "";
                } else {
                  while ($cf = mysqli_fetch_array($carifile)) {
                    $gambar = $cf['gambar'];
                    echo " - ";
                    echo "<a href='uploads/$gambar'><i class='fa fa-file'></i></a>";
                  }
                }
                ?>
              </td>
              <td><?php echo $d['tahun']; ?></td>
              <td><?php echo $d['pengaju']; ?></td>
              <td><?php echo $d['divisi']; ?></td>
              <td><?php echo 'Rp. ' . number_format($d['totalbudget'], 0, '', ','); ?></td>
              <td>
                <font color="#f23f2b"><?php echo 'Rp. ' . number_format($belumbayar, 0, '', ','); ?></font>
                </font>
              </td>
              <td>
                <font color="#1bd34f"><?php echo 'Rp. ' . number_format($row2['sumasum'], 0, '', ','); ?></font>
              </td>
              <td><a href="view-direksi.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a></td>
              <?php echo "<td><a href='#myModal' class='btn btn-default btn-small' id='custId' data-toggle='modal' data-id=" . $d['noid'] . ">Setujui</a></td>"; ?>
              <td><?php echo $d['status']; ?></td>
              <?php
              echo "<td>";
              echo "<div class='btn-group-vertical'>";
              echo "<a href='#myModal2' id='custId' data-toggle='modal' data-id=" . $d['noid'] . "><button type='button' class='btn btn-danger'>Hapus</button></a>";
              echo "</div>";
              echo "</td>";
              ?>
            </tr>
        <?php }
        } ?>
      </tbody>
    </table>
  </div><!-- /.table-responsive -->
</div>