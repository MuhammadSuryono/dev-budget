<div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
    <div class="panel-body no-padding">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr class="warning">
                        <th>No</th>
                        <th>Nama Project</th>
                        <th>Tahun</th>
                        <th>Nama Yang Mengajukan</th>
                        <th>Divisi</th>
                        <th>Action</th>
                        <th>Status</th>
                        <?php
                        if ($_SESSION['divisi'] == 'FINANCE' && $_SESSION['hak_akses'] == 'Manager') {
                            echo "<th>Dissapprove</th>";
                        } else {
                            echo "";
                        }
                        ?>
                    </tr>
                </thead>

                <tbody>

                    <?php
                    require "../application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
                    $i = 1;
                    $sql = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE jenis ='Rutin' AND status !='Belum Di Ajukan' AND tahun ='2021'");
                    while ($d = mysqli_fetch_array($sql)) {
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
                                <td bgcolor="#fcfaa4">
                                    <?= $d['nama'] ?>
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
                                <?php
                                if ($_SESSION['divisi'] == 'FINANCE' && $_SESSION['hak_akses'] == 'Manager') {
                                    echo "<td><a href='#myModal6' id='custId' data-toggle='modal' data-id=" . $d['noid'] . "><button type='button' class='btn btn-warning'>Dissapprove</button></a></td>";
                                } else {
                                    echo "";
                                }
                                ?>
                            </tr>
                        <?php
                        } else { ?>
                            <tr>
                                <th scope="row"><?php echo $i++; ?></th>
                                <td><?php echo $d['nama']; ?></td>
                                <td><?php echo $d['tahun']; ?></td>
                                <td><?php echo $d['pengaju']; ?></td>
                                <td><?php echo $d['divisi']; ?></td>
                                <td>--</td>
                                <td><?php echo $d['status']; ?></td>
                            </tr>
                    <?php }
                    } ?>
                </tbody>
            </table>
        </div><!-- /.table-responsive -->
    </div>
</div>