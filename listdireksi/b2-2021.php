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
                    <th>Total DiBayar</th>
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
                             WHERE jenis='B2' AND status ='Disetujui' AND tahun ='2021'
                                OR jenis='B2' AND status ='Pending' AND tahun ='2021'
                                OR jenis='B2' AND status ='Disapprove' AND tahun ='2021'");
                while ($e = mysqli_fetch_array($sql2)) {

                    $waktu = $e['waktu'];
                    $query2 = "SELECT sum(jumlahbayar) AS sumasum FROM bpu WHERE waktu='$waktu'";
                    $result2 = mysqli_query($koneksi, $query2);
                    $row2 = mysqli_fetch_array($result2);

                    $aaaa = $e['totalbudget'];
                    $bbbb = $row2['sumasum'];
                    $belumbayar = $aaaa - $bbbb;

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
                            <td bgcolor="#fcfaa4"><?php echo 'Rp. ' . number_format($e['totalbudget'], 0, '', ','); ?></td>
                            <td bgcolor="#fcfaa4">
                                <font color="#f23f2b"><?php echo 'Rp. ' . number_format($belumbayar, 0, '', ','); ?></font>
                                </font>
                            </td>
                            <td bgcolor="#fcfaa4">
                                <font color="#1bd34f"><?php echo 'Rp. ' . number_format($row2['sumasum'], 0, '', ','); ?></font>
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
                            <td bgcolor="#ff99a1"><?php echo 'Rp. ' . number_format($e['totalbudget'], 0, '', ','); ?></td>
                            <td bgcolor="#ff99a1">
                                <font color="#f23f2b"><?php echo 'Rp. ' . number_format($belumbayar, 0, '', ','); ?></font>
                                </font>
                            </td>
                            <td bgcolor="#ff99a1">
                                <font color="#1bd34f"><?php echo 'Rp. ' . number_format($row2['sumasum'], 0, '', ','); ?></font>
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
                            <td><?php echo 'Rp. ' . number_format($e['totalbudget'], 0, '', ','); ?></td>
                            <td>
                                <font color="#f23f2b"><?php echo 'Rp. ' . number_format($belumbayar, 0, '', ','); ?></font>
                                </font>
                            </td>
                            <td>
                                <font color="#1bd34f"><?php echo 'Rp. ' . number_format($row2['sumasum'], 0, '', ','); ?></font>
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