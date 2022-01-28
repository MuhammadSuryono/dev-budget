<?php
require "../application/config/database.php";

$con = new Database();
$koneksi = $con->connect();;

$tab = $_POST['tab'];
$tahun = $_POST['tahun'];
if (strpos($tab, 'B1') !== false) : ?>
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
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                    <?php
                    $i = 1;
                    $checkWaktu = [];
                    $sql = mysqli_query($koneksi, "SELECT
                                   *
                            FROM pengajuan
                            WHERE jenis='B1' AND status ='Disetujui' AND tahun ='$tahun'
                               OR jenis='B1' AND status ='Pending' AND tahun ='$tahun'
                               OR jenis='B1' AND status ='Disapprove' AND tahun ='$tahun'");
                    while ($d = mysqli_fetch_array($sql)) {

                        if (!in_array($d['waktu'], $checkWaktu)) :

                            $waktu = $d['waktu'];
                            $query2 = "SELECT sum(jumlah) AS sumasum FROM bpu WHERE waktu='$waktu'";
                            $result2 = mysqli_query($koneksi, $query2);
                            $row2 = mysqli_fetch_array($result2);

                            $query3 = "SELECT sum(jumlah) AS ready_to_pay FROM bpu WHERE waktu='$waktu' AND persetujuan='Disetujui (Direksi)' AND status='Belum Di Bayar'";
                            $result3 = mysqli_query($koneksi, $query3);
                            $row3 = mysqli_fetch_array($result3);

                            $query10 = "SELECT sum(uangkembali) AS sum FROM bpu WHERE waktu='$waktu'";
                            $result10 = mysqli_query($koneksi, $query10);
                            $row10 = mysqli_fetch_array($result10);
                            $tysb = $row2['sumasum'] - $row10['sum'];

                            $queryTotalBudget = mysqli_query($koneksi, "SELECT sum(total) as total_budget FROM selesai WHERE waktu = '$d[waktu]'");
                            $dataTotalBudget = mysqli_fetch_assoc($queryTotalBudget);

                            $aaaa = $dataTotalBudget['total_budget'];
                            $bbbb = $row2['sumasum'];
                            $belumbayar = $aaaa - ($bbbb - $row3['ready_to_pay']);

                            $query3 = "SELECT sum(jumlah) AS sumi FROM bpu WHERE waktu='$waktu' AND persetujuan='Disetujui (Direksi)' AND status='Belum Di Bayar'";
                            $result3 = mysqli_query($koneksi, $query3);
                            $row3 = mysqli_fetch_array($result3);

                            $query12 = "SELECT sum(jumlah) AS sumin FROM bpu WHERE waktu='$waktu' AND persetujuan='Belum Disetujui' AND status='Belum Di Bayar'";
                            $result12 = mysqli_query($koneksi, $query12);
                            $row12 = mysqli_fetch_array($result12);

                            $arrDocument = [];
                            $document = unserialize($d['document']);
                            if (!is_array($document)) {
                                array_push($arrDocument, $document);
                            } else {
                                $arrDocument = $document;
                            }

                            if ($d['status'] == "Disetujui") {
                    ?>
                                <tr>
                                    <th bgcolor="#fcfaa4" scope="row"><?php echo $i++; ?></th>
                                    <td bgcolor="#fcfaa4"> <?= $d['nama'] ?>
                                        <?php if ($arrDocument[0]) : ?>
                                            -
                                            <?php
                                            $j = 0;
                                            foreach ($arrDocument as $ad) :
                                            ?>
                                                <?php if ($d['on_revision_status'] == 1) : ?>
                                                    <?php if ($j == count($arrDocument) - 1) : ?>
                                                        <a target="_blank" href='document/<?= $ad ?>.pdf'><i class='fa fa-file' style="color: red;"></i></a>
                                                    <?php else : ?>
                                                        <a target="_blank" href='document/<?= $ad ?>.pdf'><i class='fa fa-file'></i></a>
                                                    <?php endif; ?>
                                                <?php else : ?>
                                                    <a target="_blank" href='document/<?= $ad ?>.pdf'><i class='fa fa-file'></i></a>
                                                <?php endif;
                                                $j++; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td bgcolor="#fcfaa4"><?php echo $d['tahun']; ?></td>
                                    <td bgcolor="#fcfaa4"><?php echo $d['pengaju']; ?></td>
                                    <td bgcolor="#fcfaa4"><?php echo $d['divisi']; ?></td>

                                    <td bgcolor="#fcfaa4"><?php echo 'Rp. ' . number_format($dataTotalBudget['total_budget'], 0, '', ','); ?></td>
                                    <td bgcolor="#fcfaa4">
                                        <font color="#1bd34f"><?php echo 'Rp. ' . number_format($bbbb - $row3['ready_to_pay'], 0, '', ','); ?></font>
                                    </td>
                                    <td bgcolor="#fcfaa4">
                                        <font color="#f23f2b"><?php echo 'Rp. ' . number_format($belumbayar, 0, '', ','); ?></font>
                                        </font>
                                    </td>
                                    <td bgcolor="#fcfaa4"><a href="views-direksi.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a></td>
                                    <td bgcolor="#fcfaa4">
                                        <center>--</center>
                                    </td>
                                    <td bgcolor="#fcfaa4"><?php echo $d['status']; ?></td>
                                    <?php
                                    echo "<td>";
                                    echo "<a href='#myModal3' id='custId' data-toggle='modal' data-id=" . $d['noid'] . "><button type='button' class='btn btn-success'>Job Closed</button></a>";
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
                                    <td bgcolor="#fea700"><?= $d['nama'] ?>
                                        <?php if ($arrDocument[0]) : ?>
                                            -
                                            <?php
                                            $j = 0;
                                            foreach ($arrDocument as $ad) :
                                            ?>
                                                <?php if ($d['on_revision_status'] == 1) : ?>
                                                    <?php if ($j == count($arrDocument) - 1) : ?>
                                                        <a target="_blank" href='document/<?= $ad ?>.pdf'><i class='fa fa-file' style="color: red;"></i></a>
                                                    <?php else : ?>
                                                        <a target="_blank" href='document/<?= $ad ?>.pdf'><i class='fa fa-file'></i></a>
                                                    <?php endif; ?>
                                                <?php else : ?>
                                                    <a target="_blank" href='document/<?= $ad ?>.pdf'><i class='fa fa-file'></i></a>
                                                <?php endif;
                                                $j++; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td bgcolor="#fea700"><?php echo $d['tahun']; ?></td>
                                    <td bgcolor="#fea700"><?php echo $d['pengaju']; ?></td>
                                    <td bgcolor="#fea700"><?php echo $d['divisi']; ?></td>
                                    <td bgcolor="#fea700"><?php echo 'Rp. ' . number_format($dataTotalBudget['total_budget'], 0, '', ','); ?></td>
                                    <td bgcolor="#fea700">
                                        <font color="#1bd34f"><?php echo 'Rp. ' . number_format($bbbb - $row3['ready_to_pay'], 0, '', ','); ?></font>
                                    </td>
                                    <td bgcolor="#fea700">
                                        <font color="#f23f2b"><?php echo 'Rp. ' . number_format($belumbayar, 0, '', ','); ?></font>
                                        </font>
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
                                    <td><?= $d['nama'] ?>
                                        <?php if ($arrDocument[0]) : ?>
                                            -
                                            <?php
                                            $j = 0;
                                            foreach ($arrDocument as $ad) :
                                            ?>
                                                <?php if ($d['on_revision_status'] == 1) : ?>
                                                    <?php if ($j == count($arrDocument) - 1) : ?>
                                                        <a target="_blank" href='document/<?= $ad ?>.pdf'><i class='fa fa-file' style="color: red;"></i></a>
                                                    <?php else : ?>
                                                        <a target="_blank" href='document/<?= $ad ?>.pdf'><i class='fa fa-file'></i></a>
                                                    <?php endif; ?>
                                                <?php else : ?>
                                                    <a target="_blank" href='document/<?= $ad ?>.pdf'><i class='fa fa-file'></i></a>
                                                <?php endif;
                                                $j++; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $d['tahun']; ?></td>
                                    <td><?php echo $d['pengaju']; ?></td>
                                    <td><?php echo $d['divisi']; ?></td>
                                    <td><?php echo 'Rp. ' . number_format($dataTotalBudget['total_budget'], 0, '', ','); ?></td>
                                    <td>
                                        <font color="#f23f2b"><?php echo 'Rp. ' . number_format($belumbayar, 0, '', ','); ?></font>
                                        </font>
                                    </td>
                                    <td>
                                        <font color="#1bd34f"><?php echo 'Rp. ' . number_format($bbbb - $row3['ready_to_pay'], 0, '', ','); ?></font>
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
                            array_push($checkWaktu, $d['waktu']);
                        endif;
                    } ?>
                </tbody>
            </table>
        </div><!-- /.table-responsive -->
    </div>
<?php elseif (strpos($tab, 'B2') !== false) : ?>
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
                        <th>Sisa Budget</th>
                        <th>Total Biaya dan Uang Muka</th>
                        <th>View</th>
                        <th>Persetujuan</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                    <?php
                    $i = 1;
                    $sql2 = mysqli_query($koneksi, "SELECT
                                     *
                             FROM pengajuan
                             WHERE jenis='B2' AND status ='Disetujui' AND tahun ='$tahun'
                                OR jenis='B2' AND status ='Pending' AND tahun ='$tahun'
                                OR jenis='B2' AND status ='Disapprove' AND tahun ='$tahun'");
                    while ($e = mysqli_fetch_array($sql2)) {

                        $waktu = $e['waktu'];
                        $query2 = "SELECT sum(jumlah) AS sumasum FROM bpu WHERE waktu='$waktu'";
                        $result2 = mysqli_query($koneksi, $query2);
                        $row2 = mysqli_fetch_array($result2);

                        $query3 = "SELECT sum(jumlah) AS ready_to_pay FROM bpu WHERE waktu='$waktu' AND persetujuan='Disetujui (Direksi)' AND status='Belum Di Bayar'";
                  $result3 = mysqli_query($koneksi, $query3);
                  $row3 = mysqli_fetch_array($result3);

                        $queryTotalBudget = mysqli_query($koneksi, "SELECT sum(total) as total_budget FROM selesai WHERE waktu = '$e[waktu]'");
                        $dataTotalBudget = mysqli_fetch_assoc($queryTotalBudget);

                        $aaaa = $dataTotalBudget['total_budget'];
                        $bbbb = $row2['sumasum'];
                        $belumbayar = $aaaa - ($bbbb - $row3['ready_to_pay']);

                        $arrDocument = [];
                        $document = unserialize($e['document']);
                        if (!is_array($document)) {
                            array_push($arrDocument, $document);
                        } else {
                            $arrDocument = $document;
                        }

                        if ($e['status'] == "Disetujui") {
                    ?>
                            <tr>
                                <th bgcolor="#fcfaa4" scope="row"><?php echo $i++; ?></th>
                                <td bgcolor="#fcfaa4"><?= $e['nama'] ?>
                                    <?php if ($arrDocument[0]) : ?>
                                        -
                                        <?php
                                        $j = 0;
                                        foreach ($arrDocument as $ad) :
                                        ?>
                                            <?php if ($e['on_revision_status'] == 1) : ?>
                                                <?php if ($j == count($arrDocument) - 1) : ?>
                                                    <a target="_blank" href='document/<?= $ad ?>.pdf'><i class='fa fa-file' style="color: red;"></i></a>
                                                <?php else : ?>
                                                    <a target="_blank" href='document/<?= $ad ?>.pdf'><i class='fa fa-file'></i></a>
                                                <?php endif; ?>
                                            <?php else : ?>
                                                <a target="_blank" href='document/<?= $ad ?>.pdf'><i class='fa fa-file'></i></a>
                                            <?php endif;
                                            $j++; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </td>
                                <td bgcolor="#fcfaa4"><?php echo $e['tahun']; ?></td>
                                <td bgcolor="#fcfaa4"><?php echo $e['pengaju']; ?></td>
                                <td bgcolor="#fcfaa4"><?php echo $e['divisi']; ?></td>
                                <td bgcolor="#fcfaa4"><?php echo 'Rp. ' . number_format($dataTotalBudget['total_budget'], 0, '', ','); ?></td>
                                
                                <td bgcolor="#fcfaa4">
                                    <font color="#1bd34f"><?php echo 'Rp. ' . number_format($bbbb - $row3['ready_to_pay'], 0, '', ','); ?></font>
                                </td>
                                <td bgcolor="#fcfaa4">
                                    <font color="#f23f2b"><?php echo 'Rp. ' . number_format($belumbayar, 0, '', ','); ?></font>
                                    </font>
                                </td>
                                <td bgcolor="#fcfaa4"><a href="views-direksi.php?code=<?php echo $e['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a></td>
                                <td bgcolor="#fcfaa4">
                                    <center>--</center>
                                </td>
                                <td bgcolor="#fcfaa4"><?php echo $e['status']; ?></td>
                                <?php
                                echo "<td>";
                                echo "<a href='#myModal3' id='custId' data-toggle='modal' data-id=" . $e['noid'] . "><button type='button' class='btn btn-success'>Finish</button></a>";
                                echo "</td>";
                                echo "<td>";
                                echo "<a href='#myModal2' id='custId' data-toggle='modal' data-id=" . $e['noid'] . "><button type='button' class='btn btn-danger'>Hapus</button></a>";
                                echo "</td>";
                                echo "<td>";
                                echo "<a href='#myModal6' id='custId' data-toggle='modal' data-id=" . $e['noid'] . "><button type='button' class='btn btn-warning'>Dissapprove</button></a>";
                                echo "</td>";
                                ?>
                            </tr>
                        <?php
                        } else if ($e['status'] == "Disapprove") {
                        ?>
                            <tr>
                                <th bgcolor="#ff99a1" scope="row"><?php echo $i++; ?></th>
                                <td bgcolor="#ff99a1"><?= $e['nama'] ?>
                                    <?php if ($arrDocument[0]) : ?>
                                        -
                                        <?php
                                        $j = 0;
                                        foreach ($arrDocument as $ad) :
                                        ?>
                                            <?php if ($e['on_revision_status'] == 1) : ?>
                                                <?php if ($j == count($arrDocument) - 1) : ?>
                                                    <a target="_blank" href='document/<?= $ad ?>.pdf'><i class='fa fa-file' style="color: red;"></i></a>
                                                <?php else : ?>
                                                    <a target="_blank" href='document/<?= $ad ?>.pdf'><i class='fa fa-file'></i></a>
                                                <?php endif; ?>
                                            <?php else : ?>
                                                <a target="_blank" href='document/<?= $ad ?>.pdf'><i class='fa fa-file'></i></a>
                                            <?php endif;
                                            $j++; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </td>
                                <td bgcolor="#ff99a1"><?php echo $e['tahun']; ?></td>
                                <td bgcolor="#ff99a1"><?php echo $e['pengaju']; ?></td>
                                <td bgcolor="#ff99a1"><?php echo $e['divisi']; ?></td>
                                <td bgcolor="#ff99a1"><?php echo 'Rp. ' . number_format($dataTotalBudget['total_budget'], 0, '', ','); ?></td>
                                <td bgcolor="#ff99a1">
                                    <font color="#1bd34f"><?php echo 'Rp. ' . number_format($bbbb - $row3['ready_to_pay'], 0, '', ','); ?></font>
                                </td>
                                <td bgcolor="#ff99a1">
                                    <font color="#f23f2b"><?php echo 'Rp. ' . number_format($belumbayar, 0, '', ','); ?></font>
                                    </font>
                                </td>
                                <td bgcolor="#ff99a1"><a href="view-direksi.php?code=<?php echo $e['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a></td>
                                <td bgcolor="#ff99a1">
                                    <center>--</center>
                                </td>
                                <td bgcolor="#ff99a1"><?php echo $e['status']; ?></td>
                                <?php
                                echo "<td>";
                                echo "<td><a href='#myModal' class='btn btn-default btn-small' id='custId' data-toggle='modal' data-id=" . $e['noid'] . ">Setujui</a></td>";
                                echo "</td>";
                                echo "<td>";
                                echo "<a href='#myModal2' id='custId' data-toggle='modal' data-id=" . $e['noid'] . "><button type='button' class='btn btn-danger'>Hapus</button></a>";
                                echo "</td>";
                                ?>
                            </tr>
                        <?php
                        } else {
                        ?>
                            <tr>
                                <th scope="row"><?php echo $i++; ?></th>
                                <td><?= $e['nama'] ?>
                                    <?php if ($arrDocument[0]) : ?>
                                        -
                                        <?php
                                        $j = 0;
                                        foreach ($arrDocument as $ad) :
                                        ?>
                                            <?php if ($e['on_revision_status'] == 1) : ?>
                                                <?php if ($j == count($arrDocument) - 1) : ?>
                                                    <a target="_blank" href='document/<?= $ad ?>.pdf'><i class='fa fa-file' style="color: red;"></i></a>
                                                <?php else : ?>
                                                    <a target="_blank" href='document/<?= $ad ?>.pdf'><i class='fa fa-file'></i></a>
                                                <?php endif; ?>
                                            <?php else : ?>
                                                <a target="_blank" href='document/<?= $ad ?>.pdf'><i class='fa fa-file'></i></a>
                                            <?php endif;
                                            $j++; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $e['tahun']; ?></td>
                                <td><?php echo $e['pengaju']; ?></td>
                                <td><?php echo $e['divisi']; ?></td>
                                <td><?php echo 'Rp. ' . number_format($dataTotalBudget['total_budget'], 0, '', ','); ?></td>
                                <td>
                                    <font color="#f23f2b"><?php echo 'Rp. ' . number_format($belumbayar, 0, '', ','); ?></font>
                                    </font>
                                </td>
                                <td>
                                    <font color="#1bd34f"><?php echo 'Rp. ' . number_format($bbbb - $row3['ready_to_pay'], 0, '', ','); ?></font>
                                </td>
                                <td><a href="view-direksi.php?code=<?php echo $e['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a></td>
                                <?php echo "<td><a href='#myModal' class='btn btn-default btn-small' id='custId' data-toggle='modal' data-id=" . $e['noid'] . ">Setujui</a></td>"; ?>
                                <td><?php echo $e['status']; ?></td>
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
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                    <?php
                    $i = 1;
                    $sql = mysqli_query($koneksi, "SELECT
                                   *
                            FROM pengajuan
                            WHERE jenis='Rutin' AND status ='Disetujui' AND tahun ='$tahun'
                               OR jenis='Rutin' AND status ='Pending' AND tahun ='$tahun'
                               OR jenis='Rutin' AND status ='Disapprove' AND tahun ='$tahun'");
                    while ($d = mysqli_fetch_array($sql)) {

                        $waktu = $d['waktu'];
                        $query2 = "SELECT sum(jumlah) AS sumasum FROM bpu WHERE waktu='$waktu'";
                        $result2 = mysqli_query($koneksi, $query2);
                        $row2 = mysqli_fetch_array($result2);

                        $query3 = "SELECT sum(jumlah) AS ready_to_pay FROM bpu WHERE waktu='$waktu' AND persetujuan='Disetujui (Direksi)' AND status='Belum Di Bayar'";
                  $result3 = mysqli_query($koneksi, $query3);
                  $row3 = mysqli_fetch_array($result3);

                        $queryTotalBudget = mysqli_query($koneksi, "SELECT sum(total) as total_budget FROM selesai WHERE waktu = '$d[waktu]'");
                            $dataTotalBudget = mysqli_fetch_assoc($queryTotalBudget);

                        $aaaa = $dataTotalBudget['total_budget'];
                        $bbbb = $row2['sumasum'];
                        $belumbayar = $aaaa - ($bbbb - $row3['ready_to_pay']);

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
                                <td bgcolor="#fcfaa4"><?php echo 'Rp. ' . number_format($dataTotalBudget['total_budget'], 0, '', ','); ?></td>
                                <td bgcolor="#fcfaa4">
                                    <font color="#1bd34f"><?php echo 'Rp. ' . number_format($bbbb - $row3['ready_to_pay'], 0, '', ','); ?></font>
                                </td>
                                <td bgcolor="#fcfaa4">
                                    <font color="#f23f2b"><?php echo 'Rp. ' . number_format($belumbayar, 0, '', ','); ?></font>
                                    </font>
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
                                <th bgcolor="#ff99a1" scope="row"><?php echo $i++; ?></th>
                                <td bgcolor="#ff99a1">
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
                                <td bgcolor="#ff99a1"><?php echo $d['tahun']; ?></td>
                                <td bgcolor="#ff99a1"><?php echo $d['pengaju']; ?></td>
                                <td bgcolor="#ff99a1"><?php echo $d['divisi']; ?></td>
                                <td bgcolor="#ff99a1"><?php echo 'Rp. ' . number_format($dataTotalBudget['total_budget'], 0, '', ','); ?></td>
                                <td bgcolor="#ff99a1">
                                    <font color="#1bd34f"><?php echo 'Rp. ' . number_format($bbbb - $row3['ready_to_pay'], 0, '', ','); ?></font>
                                </td>
                                <td bgcolor="#ff99a1">
                                    <font color="#f23f2b"><?php echo 'Rp. ' . number_format($belumbayar, 0, '', ','); ?></font>
                                    </font>
                                </td>
                                <td bgcolor="#ff99a1"><a href="view-direksi.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a></td>
                                <td bgcolor="#ff99a1">
                                    <center>--</center>
                                </td>
                                <td bgcolor="#ff99a1"><?php echo $d['status']; ?></td>
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
                                <td><?php echo 'Rp. ' . number_format($dataTotalBudget['total_budget'], 0, '', ','); ?></td>
                                <td><?php echo 'Rp. ' . number_format($bbbb - $row3['ready_to_pay'], 0, '', ','); ?></font>
                                </td>
                                <td><?php echo 'Rp. ' . number_format($belumbayar, 0, '', ','); ?></font>
                                    </font>
                                </td>
                                <td><a href="view-direksi.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a></td>
                                <td>
                                    <?php
                                    echo "<a href='#myModal' class='btn btn-default btn-small' id='custId' data-toggle='modal' data-id=" . $d['noid'] . ">Setujui</a>";
                                    ?>
                                </td>
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
<?php elseif (strpos($tab, 'Non') !== false) : ?>
    <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
        <div class="panel-body no-padding">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr class="warning">
                        <th>No</th>
                        <th>Nama Project</th>
                        <th>Klasifikasi</th>
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
                    $sql = mysqli_query($koneksi, "SELECT
                                   *
                            FROM pengajuan
                            WHERE jenis='Non Rutin' AND status ='Disetujui' AND tahun ='$tahun'
                               OR jenis='Non Rutin' AND status ='Pending' AND tahun ='$tahun'
                               OR jenis='Non Rutin' AND status ='Disapprove' AND tahun ='$tahun'");
                    while ($d = mysqli_fetch_array($sql)) {

                        $waktu = $d['waktu'];
                        $query2 = "SELECT sum(jumlah) AS sumasum FROM bpu WHERE waktu='$waktu'";
                        $result2 = mysqli_query($koneksi, $query2);
                        $row2 = mysqli_fetch_array($result2);

                        $query3 = "SELECT sum(jumlah) AS ready_to_pay FROM bpu WHERE waktu='$waktu' AND persetujuan='Disetujui (Direksi)' AND status='Belum Di Bayar'";
                  $result3 = mysqli_query($koneksi, $query3);
                  $row3 = mysqli_fetch_array($result3);

                        $queryTotalBudget = mysqli_query($koneksi, "SELECT sum(total) as total_budget FROM selesai WHERE waktu = '$d[waktu]'");
                            $dataTotalBudget = mysqli_fetch_assoc($queryTotalBudget);

                        $aaaa = $dataTotalBudget['total_budget'];
                        $bbbb = $row2['sumasum'];
                        $belumbayar = $aaaa - ($bbbb - $row3['ready_to_pay']);
                        
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
                                <td bgcolor="#fcfaa4"><?php echo $d['katnonrut']; ?></td>
                                <td bgcolor="#fcfaa4"><?php echo $d['tahun']; ?></td>
                                <td bgcolor="#fcfaa4"><?php echo $d['pengaju']; ?></td>
                                <td bgcolor="#fcfaa4"><?php echo $d['divisi']; ?></td>
                                <td bgcolor="#fcfaa4"><?php echo 'Rp. ' . number_format($dataTotalBudget['total_budget'], 0, '', ','); ?></td>
                                <td bgcolor="#fcfaa4">
                                    <font color="#1bd34f"><?php echo 'Rp. ' . number_format($bbbb - $row3['ready_to_pay'], 0, '', ','); ?></font>
                                </td>
                                <td bgcolor="#fcfaa4">
                                    <font color="#f23f2b"><?php echo 'Rp. ' . number_format($belumbayar, 0, '', ','); ?></font>
                                    </font>
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
                                <th bgcolor="#ff99a1" scope="row"><?php echo $i++; ?></th>
                                <td bgcolor="#ff99a1">
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
                                <td bgcolor="#fcfaa4"><?php echo $d['katnonrut']; ?></td>
                                <td bgcolor="#ff99a1"><?php echo $d['tahun']; ?></td>
                                <td bgcolor="#ff99a1"><?php echo $d['pengaju']; ?></td>
                                <td bgcolor="#ff99a1"><?php echo $d['divisi']; ?></td>
                                <td bgcolor="#ff99a1"><?php echo 'Rp. ' . number_format($dataTotalBudget['total_budget'], 0, '', ','); ?></td>
                                <td bgcolor="#ff99a1">
                                    <font color="#1bd34f"><?php echo 'Rp. ' . number_format($bbbb - $row3['ready_to_pay'], 0, '', ','); ?></font>
                                </td>
                                <td bgcolor="#ff99a1">
                                    <font color="#f23f2b"><?php echo 'Rp. ' . number_format($belumbayar, 0, '', ','); ?></font>
                                    </font>
                                </td>
                                <td bgcolor="#ff99a1"><a href="view-direksi.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a></td>
                                <td bgcolor="#ff99a1">
                                    <center>--</center>
                                </td>
                                <td bgcolor="#ff99a1"><?php echo $d['status']; ?></td>
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
                                <td bgcolor="#fcfaa4"><?php echo $d['katnonrut']; ?></td>
                                <td><?php echo $d['tahun']; ?></td>
                                <td><?php echo $d['pengaju']; ?></td>
                                <td><?php echo $d['divisi']; ?></td>
                                <td><?php echo 'Rp. ' . number_format($dataTotalBudget['total_budget'], 0, '', ','); ?></td>
                                <td><?php echo 'Rp. ' . number_format($bbbb - $row3['ready_to_pay'], 0, '', ','); ?></font>
                                </td>
                                <td><?php echo 'Rp. ' . number_format($belumbayar, 0, '', ','); ?></font>
                                    </font>
                                </td>
                                <td><a href="view-direksi.php?code=<?php echo $d['noid']; ?>"><i class="fas fa-eye" title="VIEW"></i></a></td>
                                <td>
                                    <?php
                                    echo "<a href='#myModal' class='btn btn-default btn-small' id='custId' data-toggle='modal' data-id=" . $d['noid'] . ">Setujui</a>";
                                    ?>
                                </td>
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
<?php endif; ?>