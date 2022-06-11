<?php
error_reporting(0);
session_start();
require "application/config/database.php";
require "application/config/helper.php";
require_once "application/config/Role.php";

$con = new Database();
$koneksi = $con->connect();
$con->load_database($koneksi);

$session = isset($_GET['session']) ? $_GET['session'] : null;
$isSetSession = false;
if ($session != null) $isSetSession = true;

$helper = new Helper($isSetSession);

$con->set_name_db(DB_JAY);
$con->init_connection();
$koneksiJay = $con->connect();

$con->set_host_db(DB_HOST_DIGITALISASI_MARKETING);
$con->set_name_db(DB_DIGITAL_MARKET);
$con->set_user_db(DB_USER_DIGITAL_MARKET);
$con->set_password_db(DB_PASS_DIGITAK_MARKET);
$con->init_connection();
$koneksiDigitalMarket = $con->connect();

$role = new Role(false, $koneksi);
$hasRoleBudget = $role->get_role_budget($_SESSION['id_user'], "", "");
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Form Pengajuan Budget</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"> -->

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">

    <style>
        .modal:nth-of-type(even) {
            z-index: 1050 !important;
        }

        .modal-backdrop.show:nth-of-type(even) {
            z-index: 1051 !important;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-inverse">
        <div class="container-fluid">
            <div class="navbar-header">
                <?php if ($_SESSION['divisi'] == 'Direksi') : ?>
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="home-direksi.php">Budget-Ing</a>
            </div>
            <div class="collapse navbar-collapse" id="myNavbar">
                <ul class="nav navbar-nav">
                    <li class="active"><a href="home-direksi.php">Home</a></li>
                    <li><a href="list-direksi.php">List</a></li>
                    <li><a href="saldobpu.php">Saldo BPU</a></li>
                    <!--<li><a href="summary.php">Summary</a></li>-->
                    <li><a href="listfinish-direksi.php">Budget Finish</a></li>
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Rekap
                            <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="rekap-finance.php">Ready To Paid</a></li>
                            <li><a href="belumrealisasi.php">Belum Realisasi</a></li>
                            <li><a href="cashflow.php">Cash Flow</a></li>
                        </ul>
                    </li>
                    <!-- <li><a href="history-direksi.php">History</a></li> -->
                </ul>
            <?php elseif ($_SESSION['divisi'] == 'FINANCE') : ?>
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="home-finance.php">Budget-Ing</a>
            </div>
            <div class="collapse navbar-collapse" id="myNavbar">
                <ul class="nav navbar-nav">
                    <li class="active"><a href="home-finance.php">Home</a></li>

                    <?php
                    $aksesSes = $_SESSION['hak_akses'];
                    if ($aksesSes == 'Fani') {
                    ?>
                        <li><a href="list-finance-fani.php">List</a></li>
                    <?php } else if ($aksesSes == 'Manager') {
                    ?>
                        <li><a href="list-finance-budewi.php">List</a></li>
                    <?php
                    } else {
                    ?>
                        <li><a href="list-finance.php">List</a></li>
                    <?php } ?>
                    <li><a href="history-finance.php">History</a></li>
                    <li><a href="list.php">Personal</a></li>
                    <li><a href="summary-finance.php">Summary</a></li>
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Rekap
                            <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="rekap-finance.php">Ready To Paid</a></li>
                            <li><a href="belumrealisasi.php">Belum Realisasi</a></li>
                            <li><a href="cashflow.php">Cash Flow</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Transfer
                            <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="laporan-transfer.php">Laporan Transfer</a></li>
                            <li><a href="antrian-transfer.php">Antrian Transfer</a></li>
                        </ul>
                    </li>
                    <?php
                    if ($_SESSION['hak_page'] == 'Suci') {
                        echo "<li><a href='rekap-project.php'>Rekap Project</a></li>";
                    } else {
                        echo "";
                    }
                    ?>
                </ul>
            <?php else : ?>
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="home.php">Budget-Ing</a>
            </div>
            <div class="collapse navbar-collapse" id="myNavbar">
                <ul class="nav navbar-nav">
                    <li><a href="home.php">Home</a></li>
                    <li class="active"><a href="list.php">List</a></li>
                    <!-- <li class="active"><a href="request-budget.php">Request Budget</a></li> -->
                </ul>
            <?php endif; ?>
           <ul class="nav navbar-nav navbar-right">
                        <li><a href="/log-notifikasi-aplikasi/index.html" target="_blank"><i class="fa fa-envelope"></i></a></li>
                <li><a href="ubahpassword.php"><span class="glyphicon glyphicon-user"></span><?php echo $_SESSION['nama_user']; ?> (<?php echo $_SESSION['divisi']; ?>)</a></li></a></li>
                <li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> Logout</a></li>
            </ul>
            </div>
        </div>
    </nav>

    <div class="container">

        <?php
        $id = $_GET['id'];
        $select = mysqli_query($koneksi, "SELECT * FROM pengajuan_request WHERE id='$id'");
        $d = mysqli_fetch_assoc($select);

        if ($d["validator"] == null && $d["status_request"] == "Di Ajukan") {
            $con->update("pengajuan_request")->set_value_update("status_request", "Butuh Validasi")->where("id", "=", $d["id"])->save_update();
            $select = mysqli_query($koneksi, "SELECT * FROM pengajuan_request WHERE id='$id'");
            $d = mysqli_fetch_assoc($select);
        }

        $statusKodeProject = ($d['kode_project']) ? 1 : 0;
        $queryTotalBudget = mysqli_query($koneksi, "SELECT sum(total) as total_budget FROM selesai_request WHERE waktu = '$d[waktu]'");
                            $dataTotalBudget = mysqli_fetch_assoc($queryTotalBudget);
        ?>

        <center>
            <h2><?php echo $d['nama']; ?></h2>
        </center>

        <br /><br />

        <form id="myForm" action="request-budget-proses.php" method="post">
            <input type="hidden" name="id" value="<?= $id ?>">

            <div class="row">
                <div class="col-xs-2">Nama Yang Mengajukan</div>
                <div class="col-xs-3">: <b><?php echo $d['pengaju']; ?></b></div>
                <input type="hidden" name="namaUser" value="<?= $_SESSION['nama_user'] ?>">
            </div>

            <div class="row">
                <div class="col-xs-2">Divisi</div>
                <div class="col-xs-3">: <b><?php echo $d['divisi']; ?></b></div>
                <input type="hidden" name="divisiUser" value="<?= $_SESSION['divisi'] ?>">
            </div>

            <div class="row">
                <div class="col-xs-2">Tahun</div>
                <div class="col-xs-3">: <b><?php echo $d['tahun']; ?></b></div>
            </div>

            <div class="row">
                <div class="col-xs-2">Status</div>
                <div class="col-xs-3">: <b><?php echo $d['status_request']; ?></b></div>
            </div>

            <?php if (!$d['kode_project'] && $_SESSION['divisi'] != 'Direksi' && $d['jenis'] == 'B1') : ?>
                <div class="row" style="margin-top: 10px;">
                    <div class="col-xs-4">

                        <div id="namab1" class="form-group">
                            <label for="kodeproject">Kode Project</label>
                            <select class="custom-select form-control" id="kodeproject" name="kodepro[]" multiple>
                                <option selected disabled>Pilih Project</option>
                                <?php
                                $kode = mysqli_query($koneksiJay, "SELECT * FROM project WHERE visible='y' ORDER BY nama");
                                foreach ($kode as $rc) {
                                    $kodepro = $rc['kode'];
                                    $nampro  = $rc['nama'];
                                    echo "<option value='$kodepro'>$kodepro - $nampro</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <p>Note: <strong>Wajib untuk memilih kode project, kode project bisa dipilih lebih dari satu. Setelah klik "SIMPAN" atau "AJUKAN", kode project tidak bisa diubah kembali.</strong></p>
            <?php endif; ?>

            <?php if ($d['status_request'] != 'Disetujui' && $d['declined_note']) : ?>
                <div class="row">
                    <div class="col-xs-2">Alasan Ditolak</div>
                    <div class="col-xs-3">: <b><?php echo $d['declined_note']; ?></b></div>
                </div>
            <?php endif; ?>

            <?php if ($d['status_request'] == 'Di Ajukan') : ?>
                <div class="row">
                    <div class="col-xs-2">Keterangan Pengajuan</div>
                    <div class="col-xs-3">: <b><?= ($d['submission_note']) ? $d['submission_note'] : '-'; ?></b></div>
                </div>
            <?php endif; ?>

            <div class="row ">
                <h3 class="text-center">Item Budget</h3>
            </div>
            <div id="btn-print-budget"><a href="request-budget-print.php?id=<?= $id ?>" target="_blank" class="btn btn-warning pull-right">Print <i class="fas fa-print"></i></a></div>
            <br /><br />
            <?php if ($d['status_request'] == 'Di Ajukan') {
                echo '<div class="alert alert-success" role="alert">
                <p>Budget telah <button class="btn btn-xs btn-success" disabled>DI VALIDASI</button> oleh '.$d["validator"].'.</p>
            </div>';
            } ?>

            <?php if ($d['status_request'] == 'Butuh Validasi') {
                echo '<div class="alert alert-danger" role="alert">
                <p>Budget belum <button class="btn btn-xs btn-danger" disabled>DI VALIDASI</button> oleh Kadiv Finance.</p>
            </div>';
            } ?>

            <?php if ($d['status_request'] == 'Validasi Di Tolak') {
                echo '<div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">DITOLAK VALIDASI</h4>
                <p>Budget telah <button class="btn btn-xs btn-danger" disabled>DI TOLAK VALIDASI</button> oleh Kadiv Finance. Dengan keterangan: <i>'.$d["declined_note"].'</i></p>
            </div>';
            } ?>
            <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
                <div class="panel-body no-padding">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr class="warning">
                                <th>No</th>
                                <th>Nama</th>
                                <th>Kota</th>
                                <th>Status</th>
                                <th>Penerima Uang</th>
                                <th>Harga (IDR)</th>
                                <th>Total Quantity</th>
                                <th>Total Harga (IDR)</th>
                                <?php if ($d['jenis'] == 'Non Rutin') : ?>
                                    <th>Rencana Tanggal Pembayaran</th>
                                <?php endif; ?>
                                <th class="editButtonCol">Edit</th>
                            </tr>
                        </thead>

                        <tbody id="data-body">

                            <?php
                            $i = 1;
                            $sql = mysqli_query($koneksi, "SELECT * FROM selesai_request WHERE id_pengajuan_request = '$id' ORDER BY urutan ASC");
                            while ($a = mysqli_fetch_array($sql)) : ?>

                                <tr>
                                    <th scope="row"><?php echo $i; ?></th>
                                    <input type="hidden" name="idData[]" value="<?= $a['id'] ?>">
                                    <input type="hidden" id="inputNama<?= $i ?>" name="nama[]" value="<?= $a['rincian'] ?>">
                                    <td id="nama<?= $i ?>"><?php echo $a['rincian']; ?></td>
                                    <input type="hidden" id="inputKota<?= $i ?>" name="kota[]" value="<?= $a['kota'] ?>">
                                    <td id="kota<?= $i ?>"><?php echo $a['kota']; ?></td>
                                    <input type="hidden" id="inputStatus<?= $i ?>" name="status[]" value="<?= $a['status']; ?>">
                                    <td id="status<?= $i ?>">
                                        <?php
                                        $logs = $con->select("*")->from("log_item_request")->where("id_item_request", "=", $a['id'])->order_by("id", "DESC")->get();
                                        $label = count($logs) > 0 ? '<i class="fa fa-check text-success"></i>':'';
                                        echo $a['status'] . " ".$label;
                                        ?>
                                        <?php
                                            if (count($logs) > 0) {
                                                echo '<ul>';
                                                foreach ($logs as $log) {
                                                    echo '<li>'.$log['status'].' <i class="fa fa-times text-danger"></i></li>';
                                                }
                                                echo '</ul>';

                                            }
                                        ?>
                                    </td>
                                    <input type="hidden" id="inputPUang<?= $i ?>" name="pUang[]" value="<?= $a['penerima'] ?>">
                                    <td id="pUang<?= $i ?>"><?php echo $a['penerima']; ?></td>
                                    <input type="hidden" id="inputHarga<?= $i ?>" name="harga[]" value="<?= $a['harga'] ?>">
                                    <td id="harga<?= $i ?>"><?= 'Rp. ' . number_format($a['harga']) ?></td>
                                    <input type="hidden" id="inputQuantity<?= $i ?>" name="quantity[]" value="<?= $a['quantity']; ?>">
                                    <td id="quantity<?= $i ?>"><?php echo number_format($a['quantity']); ?></td>
                                    <input class="inputTHarga" type="hidden" id="inputTHarga<?= $i ?>" name="tHarga[]" value="<?= $a['total'] ?>">
                                    <td class="tHarga" id="tHarga<?= $i ?>"><?php echo 'Rp. ' . number_format($a['total']) ?></td>
                                    <?php if ($d['jenis'] == 'Non Rutin') :
                                        $queryTanggal = mysqli_query($koneksi, "SELECT tanggal FROM reminder_tanggal_bayar WHERE selesai_waktu = '$a[waktu]' AND selesai_no = '$a[urutan]'");
                                        $strTanggal = '';
                                        while ($tanggalPembayaran = mysqli_fetch_assoc($queryTanggal)) {
                                            $strTanggal .= date("d-m-Y", strtotime($tanggalPembayaran['tanggal'])) . ",";
                                        }
                                        $strTanggal = rtrim($strTanggal, ", ");
                                    ?>
                                        <input class="inputTanggalBayar" type="hidden" id="inputTanggalBayar<?= $i ?>" name="tanggal_pembayaran[]" value="<?= ($strTanggal) ?  $strTanggal : '-' ?>">
                                        <td id="tanggal_bayar<?= $i ?>"><?= ($strTanggal) ?  $strTanggal : '-' ?></td>
                                    <?php endif; ?>

                                    <td class="editButtonCol"><button type="button" class="btn btn-default btn-small buttonEdit" id="buttonEdit<?= $i ?>">Edit</button></td>
                                </tr>
                                <?php $i++ ?>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div><!-- /.table-responsive -->
            </div>

            <?php if (isset($d['status_request'])) {
                echo '<div class="alert alert-warning" role="alert">
                <h4 class="alert-heading">PERHATIAN!!!</h4>
                <p>Pastikan sebelum melakukan Pengajuan/Validasi/Persetujuan, <button class="btn btn-xs btn-primary" disabled>Simpan</button> terlebih dahulu data anda.</p>
                <hr>
                <p><b>Note: Budget yang telah diajukan/diseujui tidak bisa dirubah kembali.</b></p>
            </div>';
            } ?>
            
            <span data-toggle="modal" data-target="#requestModal">
                <button type="button" class="btn btn-success btn-small pull-right" style="margin-left: 5px; display: none;" data-toggle="tooltip" data-placement="bottom" title="Harap Simpan data terlebih dahulu sebelum mengajukan permohonan budget" id="buttonAjukan" data-id="<?= $id ?>">Ajukan</button>
            </span>
            <?php
            if ($_SESSION['hak_akses'] == 'Manager' && $_SESSION["divisi"] == "FINANCE" && $d['status_request'] == 'Butuh Validasi') {
                echo '<span data-toggle="modal" data-target="#tolakValidasiModal">
                <button type="button" class="btn btn-danger btn-small pull-right" style="margin-left: 5px;" data-toggle="tooltip" data-placement="bottom" id="buttonTolakValidasi" data-id="<?= $id ?>">Tolak Validasi</button>
            </span>';
                echo '<span data-toggle="modal" data-target="#validasiModal">
                <button type="button" class="btn btn-success btn-small pull-right" style="margin-left: 5px;" data-toggle="tooltip" data-placement="bottom" title="Harap Simpan data terlebih dahulu sebelum memvalidasi permohonan budget" id="buttonValidasi" data-id="<?= $id ?>">Validasi</button>
            </span>';
            }
            ?>
            <input type="button" class="btn btn-primary pull-right" id="submitButton" style="margin-left: 5px;display: none;" data-toggle="modal" value="Simpan" />
            <button type="button" id="buttonTambah" class="btn btn-default btn-small pull-right" style="display: none;" onclick="tambah_budget()" margin-left: 5px;display: none;>Tambah</button>
           
            <br /><br />
            <?php
            $queryTotalBiaya = mysqli_query($koneksi, "SELECT sum(total) as total_biaya FROM selesai_request WHERE waktu = '$d[waktu]' AND status != 'UM Burek'");
            $dataTotalBiaya = mysqli_fetch_assoc($queryTotalBiaya);

            $queryTotalBiayaUMBurek = mysqli_query($koneksi, "SELECT sum(total) as total_budget_um_burek FROM selesai_request WHERE waktu = '$d[waktu]' AND status = 'UM Burek'");
            $dataTotalBiayaUMBurek = mysqli_fetch_assoc($queryTotalBiayaUMBurek);
            ?>
            <div class="row">
                <div class="col-xs-2">Total Biaya</div>
                <div class="col-xs-2">: <b class="totalElementBiaya"><?php echo 'Rp. ' . number_format($dataTotalBiaya['total_biaya'], 0, '', ','); ?></b></div>
                <input type="hidden" name="tBiaya" id="totalBiaya" value="<?php echo $dataTotalBiaya['total_biaya'] ?>">
            </div>
            <div class="row">
                <div class="col-xs-2">Total UM Burek <hr/></div>
                <div class="col-xs-2">: <b class="totalElementBiayaUmBurek"><?php echo 'Rp. ' . number_format($dataTotalBiayaUMBurek['total_budget_um_burek'], 0, '', ','); ?></b></div>
                <input type="hidden" name="tBiayaUmBurek" id="totalBiayaUmBurek" value="<?php echo $dataTotalBiayaUMBurek['total_budget_um_burek'] ?>">
            </div>

            <div class="row">
                <div class="col-xs-2">Total Keseluruhan</div>
                <div class="col-xs-2">: <b class="totalElementKeseluruhan"><?php echo 'Rp. ' . number_format($dataTotalBudget['total_budget'], 0, '', ','); ?></b></div>
                <input type="hidden" name="tKeseluruhan" id="totalKeseluruhan" value="<?php echo $dataTotalBudget['total_budget'] ?>">
            </div>
            <?php if ($_SESSION['divisi'] == 'Direksi') : ?>
                <?php
                $queryCommVoucher = mysqli_query($koneksiDigitalMarket, "SELECT harga_pokok_produksi, nomor_project, id_mata_uang FROM comm_voucher WHERE nama_project_internal = '$d[nama]'");
                $commisionVoucher = mysqli_fetch_assoc($queryCommVoucher);

                $queryMataUang = mysqli_query($koneksiDigitalMarket, "SELECT * FROM daftar_mata_uang WHERE id_mata_uang = $commisionVoucher[id_mata_uang]");
                $mataUang = mysqli_fetch_assoc($queryMataUang);
                $simbolMataUang = $mataUang['simbol_mata_uang'];
                $pemisahMataUang = $mataUang['pemisah'];
                ?>
                <?php if ($commisionVoucher) : ?>
                    <div class="row">
                        <div class="col-xs-2">Total Pada Commision Voucher</div>
                        <div class="col-xs-2">: <b class=""><?php echo $simbolMataUang . ' ' . number_format($commisionVoucher['harga_pokok_produksi'], 0, ',', $pemisahMataUang); ?></b></div>
                    </div>
                <?php endif; ?>

                <?php
                $str = explode('-', $d['nama']);
                $strAll = '';
                for ($i = 0; $i < count($str) - 1; $i++) {
                    $strAll .= $str[$i];
                    if ($i < count($str) - 2) $strAll .= ' - ';
                }
                $querySindikasi = mysqli_query($koneksiDigitalMarket, "SELECT * FROM data_sindikasi WHERE nama_project = '$strAll'");
                $sindikasi = mysqli_fetch_assoc($querySindikasi);
                ?>
                <?php if ($sindikasi) : ?>
                    <div class="row">
                        <div class="col-xs-2">Target Sales</div>
                        <div class="col-xs-2">: <b class=""><?php echo "Rp" . number_format($sindikasi['target_sales']) ?></b></div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <br>
            <div class="row" style="margin-bottom: 20px;">
                <!-- <div class="col-xs-2"></div> -->
                <?php if ($hasRoleBudget) { ?>
                    <div class="col-xs-4">
                        <?php $code = strtoupper(md5($d['nama'])); ?>
                        <a href='#requestModal2' class='btn btn-success btn-small buttonAjukan' style="display: none;" id="buttonSetujuiRequest" data-toggle='modal' data-id="<?= $id ?>" data-code="<?= $code ?>">Setujui</a>
                        <a href='#cancelModal' class='btn btn-danger btn-small buttonCancel' style="display: none;" id="buttonTolakRequest" data-toggle='modal' data-id="<?= $id ?>" data-code="<?= $code ?>">Tolak</a>
                        <?php if ($d['jenis'] == 'B2' || $d['jenis'] == 'B1') : ?>
                            <a href='http://180.211.92.134/	digital-market/?continue=projectDocument/printPdf/<?= $commisionVoucher['nomor_project'] ?>?status=view' target="_blank" class='btn btn-primary btn-small buttonView' style="display: none;" id="buttonViewCv" data-toggle='modal'>View Commision Voucher</a>
                        <?php endif; ?>
                    </div>
                <?php } ?>
            </div>

        </form>
    </div>

    <div class="modal fade" id="requestModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    Konfirmasi Pengajuan Request
                </div>
                <div class="modal-body">
                    <p>Masukkan keterangan tambahan (jika ada) dan klik 'submit' untuk untuk melakukan pengajuan budget</p>
                    <div class="form-group" style="margin-top: 2px;">
                        <input type="text" id="keteranganTambahan" class="form-control" id="exampleInputEmail1" placeholder="Keterangan" autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button href="" id="buttonSubmitAjukan" class="btn btn-success success">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="requestModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    Konfirmasi Pengajuan Request
                </div>
                <div class="modal-body">
                    Masukan Kode Berikut untuk menyetujui:
                    <h2 style="text-align: center;" id="kodeApprove"></h2>
                    <input type="text" class="form-control" id="inputKode" name="namaProject" value="<?= $namaProject ?>" autocomplete="off" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <a href="" id="buttonSubmitSetujui" class="btn btn-success success">Submit</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    Konfirmasi Penolakan Request
                </div>
                <div class="modal-body">
                    <p> Masukkan alasan penolakan dan klik 'Submit' untuk menolak pengajuan dana.</p>
                    <div class="form-group" style="margin-top: 2px;">
                        <input type="email" id="alasanTolak" class="form-control" id="exampleInputEmail1" placeholder="Alasan" autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <a href="" id="buttonSubmitCancelAjukan" class="btn btn-success success">Submit</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="myModal" role="dialog" tabindex="-1" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit Budget</h4>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="rincian" class="control-label">Rincian & Keterangan :</label>
                        <input type="text" class="form-control" id="rincianEdit" value="" name="rincian">
                    </div>

                    <div class="form-group">
                        <label for="kota" class="control-label">Kota :</label>
                        <input type="text" class="form-control" id="kotaEdit" value="" name="kota">
                    </div>

                    <div class="form-group">
                        <label for="status">Status :
                            <span data-toggle="modal" href="#modalDeskripsiStatus">
                                <a data-toggle="tooltip" data-placement="top" title="Klik disini untuk melihat deskripsi" class="btn bg-transparent btn-sm">
                                    <i class="fa fa-question-circle"></i>
                                </a>
                            </span>
                        </label>
                        <select class="form-control" id="statusEdit" name="status">
                            <option value="">-</option>
                            <?php if ($d['jenis'] == 'Uang Muka') : ?>
                                <option value="UM Burek">UM Burek</option>
                                <option value="UM">UM</option>
                            <?php else : ?>
                                <option value="UM Burek">UM Burek</option>
                                <option value="UM">UM</option>
                                <option value="Vendor/Supplier">Vendor / Supplier</option>
                                <option value="Honor Eksternal">Honor Eksternal</option>
                                <option value="Biaya Lumpsum">Biaya Lumpsum Operational</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="penerima" class="control-label">Penerima :</label>
                        <input type="text" class="form-control" id="penerimaEdit" value="" name="penerima">
                    </div>

                    <div class="form-group">
                        <label for="harga" class="control-label">Harga (IDR) :</label>
                        <!-- <input type="hidden" class="form-control" id="hargaEdit" value="" name="harga"> -->
                        <input type="text" class="form-control" id="hargaEdit" name="harga" value="" onkeyup=" sum();" <?= in_array($d["status_request"], ["Di Ajukan", "Butuh Validasi"]) ? "readonly":""  ?>>
                    </div>

                    <div class="form-group">
                        <label for="quantity" class="control-label">Quantity :</label>
                        <!-- <input type="hidden" class="form-control" id="quantityEdit" value="" name="quantity"> -->
                        <input type="text" class="form-control" id="quantityEdit" name="quantity" value="" onkeyup="sum();" <?= in_array($d["status_request"], ["Di Ajukan", "Butuh Validasi"]) ? "readonly":""  ?>>
                    </div>

                    <div class="form-group">
                        <label for="total">Total Harga (IDR) :</label>
                        <input type="hidden" class="form-control" id="totalEdit" name="total" onkeyup="sum();" value="">
                        <input type="text" class="form-control" id="totalEditText" onkeyup="sum();" value="" readonly>
                    </div>

                    <div class="row-head-tanggal-bayar" style="display: none;">
                        <div style="display: flex; justify-content: end; margin-bottom: 10px;">
                            <button type="button" class="btn btn-sm btn-primary btn-tambah-row">Tambah Tanggal</button>
                        </div>
                        <div class="row-tanggal-bayar">
                            <div class="form-group">
                                <label for="" class="control-label">Rencana Tanggal Pembayaran Term 1:</label>
                                <input type="date" class="form-control" id="tanggaPembayaranEdit" value="" name="tanggal_pembayaran">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit" name="submit" id="buttonEditModal">Update</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" id="buttonKeluarEditModal">Keluar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="myModal2" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Tambah Budget</h4>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="rincian" class="control-label">Rincian & Keterangan :</label>
                        <input type="text" class="form-control" id="rincianTambah" value="" name="rincian" autocomplete="off" required>
                    </div>

                    <div class="form-group">
                        <label for="kota" class="control-label">Kota :</label>
                        <input type="text" class="form-control" id="kotaTambah" value="" name="kota" autocomplete="off" required>
                    </div>

                    <div class="form-group">
                        <label for="status">Status :
                            <span data-toggle="modal" href="#modalDeskripsiStatus">
                                <a data-toggle="tooltip" data-placement="top" title="Klik disini untuk melihat deskripsi" class="btn bg-transparent btn-sm">
                                    <i class="fa fa-question-circle"></i>
                                </a>
                            </span>
                        </label>
                        <select class="form-control" id="statusTambah" name="status" required>
                            <?php if ($d['jenis'] == 'Uang Muka') : ?>
                                <option value="UM Burek">UM Burek</option>
                                <option value="UM">UM</option>
                            <?php else : ?>
                                <option value="UM Burek">UM Burek</option>
                                <option value="UM">UM</option>
                                <option value="Vendor/Supplier">Vendor / Supplier</option>
                                <option value="Honor Eksternal">Honor Eksternal</option>
                                <option value="Biaya Lumpsum">Biaya Lumpsum Operational</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="penerima" class="control-label">Penerima :</label>
                        <input type="text" class="form-control" id="penerimaTambah" value="" name="penerima" autocomplete="off" required>
                    </div>

                    <div class="form-group">
                        <label for="harga" class="control-label">Harga (IDR) :</label>
                        <input type="text" class="form-control" id="hargaTambah" value="" name="harga" onkeyup="sumTambah();" autocomplete="off" required>
                    </div>

                    <div class="form-group">
                        <label for="quantity" class="control-label">Quantity :</label>
                        <input type="text" class="form-control" id="quantityTambah" value="" name="quantity" onkeyup="sumTambah();" autocomplete="off" required>
                    </div>

                    <div class="form-group">
                        <label for="total">Total Harga (IDR) :</label>
                        <input type="hidden" class="form-control" id="totalTambah" name="total" onkeyup="sumTambah();" value="">
                        <input type="text" class="form-control" id="totalTambahText" name="total" onkeyup="sumTambah();" value="" readonly>
                    </div>

                    <div class="row-head-tanggal-bayar" style="display: none;">
                        <div style="display: flex; justify-content: end; margin-bottom: 10px;">
                            <button type="button" class="btn btn-sm btn-primary btn-tambah-row">Tambah Tanggal</button>
                        </div>
                        <div class="row-tanggal-bayar">
                            <div class="form-group">
                                <label for="" class="control-label">Rencana Tanggal Pembayaran Term 1:</label>
                                <input type="date" class="form-control" id="" value="" name="tanggal_pembayaran">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit" name="submit" id="buttonTambahModal">Update</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal float-left" id="modalDeskripsiStatus" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Deskripsi Status</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="container"></div>
                <div class="modal-body">
                    <h3>Lumpsum Operasional</h3>
                    <p style="text-align: justify;">pengajuan biaya yang tidak perlu realisasi dan tidak ada aspek pajak. <br> Contoh: uang transport, pulsa, atk/pengiriman, konsumsi
                        bpu wajib dibuat oleh user di luar finance (biasanya orang yg terhisap di project), pembuat bpu dan penerima bpu harus orang yang berbeda</p>
                    <h3>Uang Muka</h3>
                    <p style="text-align: justify;">biasanya penggunanaan untuk percepatan operasional, melekat di orang yang mengajukan bpu. bpu harus direalisasikan.</p>
                    <h3>Honor Eksternal</h3>
                    <p style="text-align: justify;">honor perorangan yang perhitungannya belum menggunakan system aplikasi, 1 bpu bisa lebih dari 1 penerima.</p>
                    <h3>Vendor/Supplier</h3>
                    <p style="text-align: justify;">biaya yang dibayarkan kepada badan usaha. 1 bpu 1 penerima.
                        bpu honor eksternal dan vendor / supplier wajib dibuat oleh finance, karena ada kebutuhan kelengkapan dokumen perpajakan.</p>
                </div>
                <div class="modal-footer">
                    <a href="#" data-dismiss="modal" class="btn">Close</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal float-left" id="modalTambahTerm" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Tambah Term</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <?php $queryItemBudget = mysqli_query($koneksi, "SELECT * FROM selesai_request WHERE id_pengajuan_request = '$id' AND status='Vendor/Supplier'"); ?>
                    <div class="form-group">
                        <label for="status">Item Budget :</label>
                        <select class="form-control" id="" name="status" required>
                            <?php while ($item = mysqli_fetch_assoc($queryItemBudget)) : ?>
                                <option value="<?= $item['id'] ?>"><?= $item['rincian'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" data-dismiss="modal" class="btn">Close</a>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="validasiModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    Konfirmasi Validasi Budget
                </div>
                <div class="modal-body">
                    <p>Masukkan keterangan tambahan (jika ada) dan klik 'submit' untuk untuk melakukan pengajuan budget</p>
                    <div class="form-group" style="margin-top: 2px;">
                        <input type="text" id="keteranganTambahanValidasi" class="form-control" id="exampleInputEmail1" placeholder="Keterangan" autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button href="" id="buttonSubmitValidasi" class="btn btn-success success">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="tolakValidasiModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    Konfirmasi Tolak Validasi Budget
                </div>
                <div class="modal-body">
                    <p>Masukkan keterangan tambahan (jika ada) dan klik 'submit' untuk untuk melakukan <b class="text-danger">PENOLAKAN</b> validasi budget</p>
                    <div class="form-group" style="margin-top: 2px;">
                        <input type="text" id="keteranganTambahanTolakValidasi" class="form-control" id="exampleInputEmail1" placeholder="Keterangan" autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button href="" id="buttonSubmitTolakValidasi" class="btn btn-success success">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="logPerubahanItemModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    Log Perubahan Item Budget
                </div>
                <div class="modal-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Rincian</th>
                                <th>Kota</th>
                                <th>Status</th>
                                <th>Penerima</th>
                                <th>Harga</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody id="tableLogPerubahan">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        let numberClicked = '';
        let statusKodeProject = '<?= $statusKodeProject ?>';

        function showLog(e) {
            let id = e.dataset.id;
            // LogPerubahanItemBudget.php?action=getLog&idItemRequest=175
            $.ajax({
                type: 'get',
                url: `LogPerubahanItemBudget.php?action=getLog&idItemRequest=${id}`,
                success: function(data) {
                    let json = JSON.parse(data);
                    $('#logPerubahanItemModal').modal('show');
                    let html = '';
                    var formatter = new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                    });
                    json.forEach((item, index) => {
                        html += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${item.rincian}</td>
                                <td>${item.kota}</td>
                                <td>${item.status}</td>
                                <td>${item.penerima}</td>
                                <td>${formatter.format(item.harga)}</td>
                                <td>${item.quantity}</td>
                                <td>${formatter.format(item.total)}</td>
                            </tr>
                        `;
                    });
                    $('#tableLogPerubahan').html(html);
                }
            });
        }

        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip()
            const arrStatus = ['Honor Jakarta', 'Honor Luar Kota', 'STKB TRK Jakarta', 'STKB TRK Luar Kota', 'STKB OPS', 'Honor Area Head'];
            const arrNamaB2 = ['Respondent Gift', 'Honor Interviewer']
            const divisi = "<?= $_SESSION['divisi']; ?>";
            const hakAkses = "<?= $_SESSION['hak_akses']; ?>";
            const statusProject = "<?= $d['status_request'] ?>";
            const jenis = "<?= $d['jenis'] ?>";


            handleShowedButton(divisi, statusProject, hakAkses, jenis);

            hitungTotalKeseluruhan();

            let buttonClicked = '';
            let idButtonClicked = '';
            let buttonAddQuestion = '';
            let reloadButton = setInterval(reloadButtonEdit(arrStatus, jenis, arrNamaB2), 100);
            setTimeout(function() {
                clearInterval(reloadButton);
            }, 101)

            const buttonSetujuiRequest = document.querySelector('#buttonSetujuiRequest');
            let fullcode = '';
            if (buttonSetujuiRequest !== null) {
                buttonSetujuiRequest.addEventListener("click", function() {
                    let nama = this.getAttribute("data-code");
                    let code1 = nama.substring(0, 4);
                    let code2 = nama.substring(nama.length - 4, nama.length);
                    fullcode = `${code1}APPROVE${code2}`;

                    $('#kodeApprove').text(fullcode);

                    let findLink = document.getElementById("buttonSubmitSetujui");
                    findLink.addEventListener("click", function() {
                        if ($('#inputKode').val() == fullcode) {
                            let id = buttonSetujuiRequest.getAttribute("data-id");
                            findLink.href = "setuju-request-proses.php?id=" + id;
                        } else {
                            alert("Kode Approval Salah");
                            findLink.href = "view-request.php?id=" + id;
                        }
                    })
                })
            }

            const buttonTolakRequest = document.querySelector("#buttonTolakRequest");
            if (buttonTolakRequest !== null) {
                buttonTolakRequest.addEventListener("click", function () {
                    let findLink = document.getElementById("buttonSubmitCancelAjukan");
                    findLink.addEventListener("click", function () {
                        let alasanTolak = document.getElementById("alasanTolak").value;
                        let id = buttonTolakRequest.getAttribute("data-id");
                        findLink.href = "request-budget-tolak.php?id=" + id + "&alasan=" + alasanTolak;
                    })
                })
            }

            const buttonEditModal = document.querySelector("#buttonEditModal");
            buttonEditModal.addEventListener("click", function() {

                updateRow(numberClicked, jenis);

                hitungTotalKeseluruhan(true);

                resetOption(arrStatus);

                $('#myModal').modal('toggle');
            });

            const buttonKeluarEditModal = document.querySelector("#buttonKeluarEditModal");
            buttonKeluarEditModal.addEventListener("click", function() {
                resetOption(arrStatus);
            });

            const buttonTambahModal = document.querySelector("#buttonTambahModal");
            buttonTambahModal.addEventListener("click", function() {
                const nama = $("#rincianTambah").val();
                const kota = $("#kotaTambah").val();
                const status = $("#statusTambah").val();
                const penerima = $("#penerimaTambah").val();
                const harga = $("#hargaTambah").val();
                const quantity = $("#quantityTambah").val();
                const total = $("#totalTambah").val();

                if (jenis == 'Non Rutin') {
                    var tanggal_pembayaran = $('#myModal2 input[name="tanggal_pembayaran"]').map(function() {
                        if (this.value) {
                            val = (this.value).split('-');
                            return val[2] + '-' + val[1] + '-' + val[0];
                        } else {
                            return '-'
                        }
                    }).get();
                    tanggal_pembayaran = (tanggal_pembayaran) ? tanggal_pembayaran.toString() : '-';
                }

                const countTr = document.querySelectorAll("#data-body tr").length + 1;
                html = `<tr>
                                <td>${countTr}</td>
                                <td id="nama${countTr}">${nama}</td>
                                <input type="hidden" id="inputNama${countTr}" name="nama[]" value="${nama}">
                                <td id="kota${countTr}">${kota}</td>
                                <input type="hidden" id="inputKota${countTr}" name="kota[]" value="${kota}">
                                <td id="status${countTr}">${status}</td>
                                <input type="hidden" id="inputStatus${countTr}" name="status[]" value="${status}">
                                <td id="pUang${countTr}">${penerima}</td>
                                <input type="hidden" id="inputPUang${countTr}" name="pUang[]" value="${penerima}">
                                <td id="harga${countTr}">${harga}</td>
                                <input type="hidden" id="inputHarga${countTr}" name="harga[]" value="${harga}">
                                <td id="quantity${countTr}">${quantity}</td>
                                <input type="hidden" id="inputQuantity${countTr}" name="quantity[]" value="${quantity}">
                                <td class="tHarga" id="tHarga${countTr}">${total}</td>
                                <input type="hidden" class="inputTHarga" id="inputTHarga${countTr}" name="tHarga[]" value="${total.replace("Rp. ", "")}">
                                `;
                if (tanggal_pembayaran && jenis == 'Non Rutin') {
                    html += `
                            <td id="tanggal_bayar${countTr}">${tanggal_pembayaran}</td>
                            <input type="hidden" id="inputTanggalBayar${countTr}" name="tanggal_pembayaran[]" value="${tanggal_pembayaran}">
                    `;
                }

                html += `
                                <td><button type="button" class="btn btn-default btn-small buttonEdit" id="buttonEdit${countTr}">Edit</button></td>
                            </tr>
                    `;
                $("#data-body").append(html);

                hitungTotalKeseluruhan(true);

                $("#rincianTambah").val('');
                $("#kotaTambah").val('');
                $("#statusTambah").val('');
                $("#penerimaTambah").val('');
                $("#hargaTambah").val('');
                $("#quantityTambah").val('');
                $("#totalTambah").val('');

                $('#myModal2').modal('toggle');


                let reloadButton = setInterval(reloadButtonEdit(arrStatus, jenis, arrNamaB2), 100);
                setTimeout(function() {
                    clearInterval(reloadButton);
                }, 101)

            });

            $('#submitButton').click(function() {
                var kodePro = $('#kodeproject').map(function() {
                    return (this.value)
                }).get();
                if (kodePro.length == 0 && jenis == 'B1' && statusKodeProject == 0) {
                    alert('Harap pilih kode project')
                } else if (kodePro.length == 1 && jenis == 'B1' && statusKodeProject == 0) {
                    if (kodePro[0] == 'Pilih Project') {
                        alert('Harap pilih kode project');
                    } else {
                        $('#myForm').submit();
                    }
                } else {
                    $('#myForm').submit();
                }
            });

            const buttonAjukan = document.querySelector('#buttonAjukan');
            buttonAjukan.addEventListener('click', function() {
                let findLink = document.getElementById("buttonSubmitAjukan");
                findLink.setAttribute("disable", "")
                findLink.addEventListener("click", function(event) {
                    var kodePro = $('#kodeproject').map(function() {
                        return (this.value)
                    }).get();
                    if (kodePro.length == 0 && jenis == 'B1' && statusKodeProject == 0) {
                        alert('Harap pilih kode project')
                        event.preventDefault();
                    } else if (kodePro.length == 1 && jenis == 'B1' && statusKodeProject == 0) {
                        if (kodePro[0] == 'Pilih Project') {
                            alert('Harap pilih kode project');
                            event.preventDefault();
                        }
                    }
                    let id = buttonAjukan.getAttribute("data-id");
                    let keterangan = document.getElementById("keteranganTambahan").value;
                    let kodepro = $('#kodeproject').val();
                    window.location.href = "ajukan-request-proses.php?id=" + id + "&ket=" + keterangan + "&kodepro=" + kodepro;
                })
            })

            const buttonValidasi = document.querySelector('#buttonSubmitValidasi');
            buttonValidasi.addEventListener("click", function () {
                let id = buttonAjukan.getAttribute("data-id");
                let keterangan = document.getElementById("keteranganTambahanValidasi").value;
                window.location.href = "ValidasiBudget.php?id=" + id + "&keterangan=" + keterangan;
            })

            // buttonSubmitTolakValidasi
            const buttonTolakValidasi = document.querySelector('#buttonSubmitTolakValidasi');
            buttonTolakValidasi.addEventListener("click", function () {
                let id = buttonAjukan.getAttribute("data-id");
                let keterangan = document.getElementById("keteranganTambahanTolakValidasi").value;
                window.location.href = "ValidasiBudget.php?id=" + id + "&tolak&keterangan=" + keterangan;
            })

            $('.btn-tambah-row').click(function() {
                var count = ($('.row-tanggal-bayar-appended').length / 2) + $('.row-tanggal-bayar').length

                $('.row-head-tanggal-bayar').append(`
                    <div class="row-tanggal-bayar-appended">
                        <div style="display: flex; justify-content: end; margin-bottom: 10px;">
                        <button type="button" class="btn btn-sm btn-danger btn-hapus-row">Hapus Tanggal</button>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">Rencana Tanggal Pembayaran Term ${count}:</label>
                            <input type="date" class="form-control" id="" value="" name="tanggal_pembayaran">
                        </div>
                    </div>
                        `)
            })

            $("select[name=status]").change(function() {
                if ($(this).val() == 'Vendor/Supplier' && jenis == 'Non Rutin') {
                    $('.row-head-tanggal-bayar').show();
                } else {
                    $('.row-head-tanggal-bayar').hide();
                }
            })
        });

        $(document).on('click', '.btn-hapus-row', function() {
            $(this).closest('.row-tanggal-bayar-appended').remove();
        })

        function reloadButtonEdit(arrStatus, jenis, arrNamaB2) {
            buttonAddQuestion = document.querySelectorAll(".buttonEdit");
            buttonAddQuestion.forEach(function(e, i) {
                e.addEventListener("click", function() {
                    $('div.row-tanggal-bayar-appended').remove();

                    buttonClicked = e;
                    numberClicked = i + 1;
                    if (arrStatus.includes($(`#status${numberClicked}`).text()) || arrNamaB2.includes($(`#inputNama${numberClicked}`).val())) {
                        $('#rincianEdit').prop('disabled', true);
                        $('#kotaEdit').prop('disabled', true);
                        $('#penerimaEdit').prop('disabled', true);
                        $(`#statusEdit option[value=""]`).text($(`#status${numberClicked}`).text());
                        $(`#statusEdit option[value=""]`).val($(`#status${numberClicked}`).text());
                        $(`#statusEdit`).prop('disabled', true);
                    } else {
                        $(`#statusEdit`).prop('disabled', false);
                        $('#rincianEdit').prop('disabled', false);
                        $('#kotaEdit').prop('disabled', false);
                        $('#penerimaEdit').prop('disabled', false);
                    }

                    if ($(`#inputStatus${numberClicked}`).val() == 'Vendor/Supplier' && jenis == 'Non Rutin') {
                        $('.row-head-tanggal-bayar').show();
                    } else {
                        $('.row-head-tanggal-bayar').hide();
                    }

                    fillingEditModal(numberClicked, jenis);
                    $('#myModal').modal();
                });
            });
        }

        function handleShowedButton(divisi, projectStatus, hakAkses, jenis) {
            const allTotalHarga = document.querySelectorAll(".inputTHarga");
            let total = 0;
            allTotalHarga.forEach(function(e, i) {
                let status = $(`#inputStatus${i+1}`).val();
                if (status != "UM Burek") {
                    let result = e.value;
                    total += parseFloat(result);
                }
            })
            if (divisi == 'Direksi') {
                $('.editButtonCol').show();
                $('#buttonAjukan').hide();

                if (jenis == 'Non Rutin') {
                    $('#buttonTambahTerm').hide();
                }

                if (projectStatus == 'Belum Di Ajukan') {
                    $('#buttonSetujuiRequest').hide();
                    $('#buttonTolakRequest').hide();
                    $('#buttonViewCv').hide();
                    $('#submitButton').hide();
                    $('#buttonTambah').hide();
                } else if (projectStatus == 'Di Ajukan') {
                    $('.editButtonCol').show();
                    $('#buttonSetujuiRequest').show();
                    $('#buttonTolakRequest').show();
                    $('#buttonViewCv').show();
                    $('#submitButton').show();
                    $('#buttonTambah').show();
                } else if (projectStatus == 'Disetujui') {
                    $('#buttonSetujuiRequest').hide();
                    $('#buttonTolakRequest').hide();
                    $('#buttonViewCv').hide();
                    $('#submitButton').hide();
                    $('#buttonTambah').hide();
                } else if (projectStatus == 'Ditolak') {
                    $('#buttonSetujuiRequest').hide();
                    $('#buttonTolakRequest').hide();
                    $('#buttonViewCv').hide();
                    $('#submitButton').hide();
                    $('#buttonTambah').hide();
                }
            } else if (divisi == 'FINANCE' && hakAkses == 'Manager' && projectStatus == 'Butuh Validasi') {
                    $('.editButtonCol').show();
                    $('#buttonAjukan').hide();
                    $('#buttonValidasi').show();
                    $('#submitButton').show();
                    $('#buttonTambah').show();
                    $('#buttonSetujuiRequest').hide();
                    $('#buttonTolakRequest').hide();
                    if (jenis == 'Non Rutin') {
                        $('#buttonTambahTerm').show();
                    }
            } else if (divisi == 'FINANCE' && hakAkses == 'Manager' && jenis == 'Non Rutin' || total <= 1000000) {
                if (projectStatus == 'Belum Di Ajukan') {
                    $('.editButtonCol').show();
                    $('#buttonAjukan').show();
                    $('#submitButton').show();
                    $('#buttonTambah').show();
                    $('#buttonSetujuiRequest').hide();
                    $('#buttonTolakRequest').hide();
                    if (jenis == 'Non Rutin') {
                        $('#buttonTambahTerm').show();
                    }
                } else if (projectStatus == 'Di Ajukan') {
                    $('.editButtonCol').hide();
                    $('#buttonAjukan').hide();
                    $('#submitButton').hide();
                    $('#buttonTambah').hide();
                    $('#buttonSetujuiRequest').show();
                    $('#buttonTolakRequest').show();
                    if (jenis == 'Non Rutin') {
                        $('#buttonTambahTerm').hide();
                    }
                } else if (projectStatus == 'Disetujui') {
                    $('.editButtonCol').hide();
                    $('#buttonAjukan').hide();
                    $('#submitButton').hide();
                    $('#buttonTambah').hide();
                    $('#buttonSetujuiRequest').hide();
                    $('#buttonTolakRequest').hide();
                    if (jenis == 'Non Rutin') {
                        $('#buttonTambahTerm').hide();
                    }
                } else if (projectStatus == 'Ditolak') {
                    $('.editButtonCol').show();
                    $('#buttonAjukan').show();
                    $('#submitButton').show();
                    $('#buttonTambah').show();
                    $('#buttonSetujuiRequest').hide();
                    $('#buttonTolakRequest').hide();
                    if (jenis == 'Non Rutin') {
                        $('#buttonTambahTerm').show();
                    }
                }
            } else if (divisi == 'FINANCE' && hakAkses == 'Manager' && jenis == 'Uang Muka' && total <= 1000000) {
                if (projectStatus == 'Belum Di Ajukan') {
                    $('#buttonSetujuiRequest').hide();
                    $('#buttonTolakRequest').hide();
                    $('#buttonViewCv').hide();
                    $('#submitButton').hide();
                    $('#buttonTambah').hide();
                } else if (projectStatus == 'Di Ajukan') {
                    $('#buttonSetujuiRequest').show();
                    $('#buttonTolakRequest').show();
                    $('#buttonViewCv').hide();
                    $('#submitButton').hide();
                    $('#buttonTambah').hide();
                } else if (projectStatus == 'Disetujui') {
                    $('#buttonSetujuiRequest').hide();
                    $('#buttonTolakRequest').hide();
                    $('#buttonViewCv').hide();
                    $('#submitButton').hide();
                    $('#buttonTambah').hide();
                } else if (projectStatus == 'Ditolak') {
                    $('#buttonSetujuiRequest').hide();
                    $('#buttonTolakRequest').hide();
                    $('#buttonViewCv').hide();
                    $('#submitButton').hide();
                    $('#buttonTambah').hide();
                }
            } else {
                $('#buttonSetujuiRequest').hide();
                $('#buttonTolakRequest').hide();
                if (projectStatus == 'Belum Di Ajukan' || projectStatus == 'Validasi Di Tolak') {
                    $('.editButtonCol').show();
                    $('#buttonAjukan').show();
                    $('#submitButton').show();
                    $('#buttonTambah').show();
                    if (jenis == 'Non Rutin') {
                        $('#buttonTambahTerm').show();
                    }
                } else if (projectStatus == 'Di Ajukan') {
                    $('.editButtonCol').hide();
                    $('#buttonAjukan').hide();
                    $('#submitButton').hide();
                    $('#buttonTambah').hide();
                    if (jenis == 'Non Rutin') {
                        $('#buttonTambahTerm').hide();
                    }
                } else if (projectStatus == 'Disetujui') {
                    $('.editButtonCol').hide();
                    $('#buttonAjukan').hide();
                    $('#submitButton').hide();
                    $('#buttonTambah').hide();
                    if (jenis == 'Non Rutin') {
                        $('#buttonTambahTerm').hide();
                    }
                } else if (projectStatus == 'Ditolak') {
                    $('.editButtonCol').show();
                    $('#buttonAjukan').show();
                    $('#submitButton').show();
                    $('#buttonTambah').show();
                    if (jenis == 'Non Rutin') {
                        $('#buttonTambahTerm').show();
                    }
                }
            }
        }

        function formatNumber(num) {
            return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
        }

        function sum() {
            var txtSecondNumberValue = document.getElementById('hargaEdit').value;
            txtSecondNumberValue = txtSecondNumberValue.replace("Rp. ", "");
            var txtTigaNumberValue = document.getElementById('quantityEdit').value;
            var result = parseFloat(txtSecondNumberValue) * parseFloat(txtTigaNumberValue);
            if (!isNaN(result)) {
                document.getElementById('totalEdit').value = `Rp. ${(result)}`;
                document.getElementById('totalEditText').value = `Rp. ${(formatNumber(result))}`;
            } else {
                document.getElementById('totalEdit').value = 0;
                document.getElementById('totalEditText').value = `Rp. 0`;
            }
        }

        function sumTambah() {
            var txtSecondNumberValue = document.getElementById('hargaTambah').value;
            txtSecondNumberValue = txtSecondNumberValue.replace("Rp. ", "");
            var txtTigaNumberValue = document.getElementById('quantityTambah').value;
            var result = parseFloat(txtSecondNumberValue) * parseFloat(txtTigaNumberValue);
            if (!isNaN(result)) {
                document.getElementById('totalTambah').value = `Rp. ${(result)}`;
                document.getElementById('totalTambahText').value = `Rp. ${(formatNumber(result))}`;
            } else {
                document.getElementById('totalTambah').value = `0`;
                document.getElementById('totalTambahText').value = `Rp. 0`;
            }

        }

        function fillingEditModal(numberClicked, jenis) {
            $(`#rincianEdit`).val($(`#inputNama${numberClicked}`).val());
            $(`#kotaEdit`).val($(`#inputKota${numberClicked}`).val());
            $(`#statusEdit`).val($(`#inputStatus${numberClicked}`).val());
            $(`#penerimaEdit`).val($(`#inputPUang${numberClicked}`).val());
            $(`#hargaEdit`).val($(`#inputHarga${numberClicked}`).val());
            $(`#quantityEdit`).val($(`#inputQuantity${numberClicked}`).val());

            if (jenis == 'Non Rutin') {
                var tanggalBayar = $(`#inputTanggalBayar${numberClicked}`).val().split(',');
                for (let i = 0; i < tanggalBayar.length; i++) {
                    var count = ($('.row-tanggal-bayar-appended').length / 2) + $('.row-tanggal-bayar').length
                    var tempDate = tanggalBayar[i].split('-');
                    if (i == 0) {
                        $('#tanggaPembayaranEdit').val(tempDate[2] + '-' + tempDate[1] + '-' + tempDate[0]);
                    } else {
                        $('.row-head-tanggal-bayar').append(`
                    <div class="row-tanggal-bayar-appended">
                        <div style="display: flex; justify-content: end; margin-bottom: 10px;">
                        <button type="button" class="btn btn-sm btn-danger btn-hapus-row">Hapus Tanggal</button>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label">Rencana Tanggal Pembayaran Term ${count}: </label>
                            <input type="date" class="form-control" id="" value="${tempDate[2] + '-' +tempDate[1] + '-' +tempDate[0]}" name="tanggal_pembayaran">
                        </div>
                    </div>
                        `)
                    }
                }
            }
            sum();
        }

        function hitungTotalKeseluruhan(update = false) {
            const allTotalHarga = document.querySelectorAll(".inputTHarga");
            let total = 0;
            let totalBiaya = 0;
            let totalUmBurek = 0;
            allTotalHarga.forEach(function(e, i) {
                let status = $(`#inputStatus${i+1}`).val();
                let result = e.value;
                
                if (status != "UM Burek") {
                    total += parseFloat(result);
                    totalBiaya += parseFloat(result);
                } else {
                    total += parseFloat(result);
                    totalUmBurek += parseFloat(result);
                }
            })
            if (isNaN(total)) total = 0;
            if (total !== parseInt($('#totalKeseluruhan').val()) || total === 0) {
                var elmBtnAjukan = document.getElementById("buttonAjukan");
                var elmBtnPrint = document.getElementById("btn-print-budget")
                elmBtnAjukan.setAttribute("disabled", ""); 
                elmBtnPrint.style.display = "none"
            }

            $('.totalElementKeseluruhan').text(`Rp. ${formatNumber(total)}`);
            $('#totalKeseluruhan').val((total));

            $('.totalElementBiaya').text(`Rp. ${formatNumber(totalBiaya)}`);
            $('#totalBiaya').val((totalBiaya));

            $('.totalElementBiayaUmBurek').text(`Rp. ${formatNumber(totalUmBurek)}`);
            $('#totalBiayaUmBurek').val((totalUmBurek));
        }

        function resetOption(arrStatus) {
            const valueOption = document.querySelectorAll("#statusEdit option");
            valueOption.forEach(function(e, i) {
                if (arrStatus.includes(e.value)) {
                    $("#statusEdit option[value=" + `"${e.value}"` + "]").val("");
                    $("#statusEdit option[value=" + `"${e.value}"` + "]").text("-");
                }
            })
        };

        function updateRow(numberClicked, jenis) {
            let rincianEdit = $(`#rincianEdit`).val();
            let kotaEdit = $(`#kotaEdit`).val();
            let statusValue = $(`#statusEdit`).val();
            let penerimaEdit = $(`#penerimaEdit`).val();
            let hargaEdit = $(`#hargaEdit`).val();
            let quantityEdit = $(`#quantityEdit`).val();
            let totalEdit = $(`#totalEdit`).val();
            if (jenis == 'Non Rutin') {
                var tanggal_pembayaran = $('#myModal input[name="tanggal_pembayaran"]').map(function() {
                    console.log(this.value);
                    if (this.value) {
                        val = (this.value).split('-');
                        return val[2] + '-' + val[1] + '-' + val[0];
                    }
                }).get();
                tanggal_pembayaran = (tanggal_pembayaran) ? tanggal_pembayaran.toString() : '-';
            }


            $(`#nama${numberClicked}`).text(rincianEdit);
            $(`#kota${numberClicked}`).text(kotaEdit);
            $(`#status${numberClicked}`).text(statusValue);
            $(`#pUang${numberClicked}`).text(penerimaEdit);
            $(`#harga${numberClicked}`).text(formatNumber(hargaEdit));
            $(`#quantity${numberClicked}`).text(formatNumber(quantityEdit));
            $(`#tHarga${numberClicked}`).text(formatNumber(totalEdit));
            $(`#tanggal_bayar${numberClicked}`).text(tanggal_pembayaran);

            hargaEdit = hargaEdit.replace("Rp. ", "");
            totalEdit = totalEdit.replace("Rp. ", "");

            $(`#inputNama${numberClicked}`).val(rincianEdit);
            $(`#inputKota${numberClicked}`).val(kotaEdit);
            $(`#inputStatus${numberClicked}`).val(statusValue);
            $(`#inputPUang${numberClicked}`).val(penerimaEdit);
            $(`#inputHarga${numberClicked}`).val(hargaEdit);
            $(`#inputQuantity${numberClicked}`).val(quantityEdit);
            $(`#inputTHarga${numberClicked}`).val(totalEdit);

            console.log(jenis);
            if (jenis == 'Non Rutin') {
                $(`#inputTanggalBayar${numberClicked}`).val(tanggal_pembayaran);
            }

        }

        function edit_budget(no, waktu) {
            $('#myModal').modal();
        }

        function tambah_budget(waktu, noid) {
            $('.row-head-tanggal-bayar').hide();
            $('div.row-tanggal-bayar-appended').remove();
            $('#myModal2').modal();
        }

        function tambah_term_budget(waktu, noid) {
            //$('.fetched-data').html(data);//menampilkan data ke dalam modal
            $('#modalTambahTerm').modal();
        }

        function upload(waktu) {
            // alert(noid+' - '+waktu);
            $.ajax({
                type: 'post',
                url: 'upload.php',
                data: {
                    waktu: waktu
                },
                success: function(data) {
                    $('.fetched-data').html(data); //menampilkan data ke dalam modal
                    $('#myModal3').modal();
                }
            });
        }
    </script>

</body>

</html>