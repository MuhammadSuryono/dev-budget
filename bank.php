<?php
error_reporting(0);
session_start();
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
$con->load_database($koneksi);

require_once "application/config/helper.php";
$helper = new Helper();

if (isset($_POST["action"])) {
    if ($_POST["action"] == "create") {
        $save = $con
            ->insert('bank')->set_value_insert('namabank', $_POST['bank_name'])
            ->set_value_insert('kodebank', $_POST['code_bank'])->save_insert();

        if ($save) {
            echo '<script>alert("Berhasil simpan data")</script>';
            echo "<script> document.location.href='/bank.php'; </script>";
        } else {
            echo '<script>alert("Gagal simpan data")</script>';
            echo "<script> document.location.href='".$_SERVER['HTTP_REFERER']."'; </script>";
        }
    }

    if ($_POST["action"] == "update") {
        $save = $con
            ->update('bank')->set_value_update('namabank', $_POST['bank_name'])
            ->set_value_update('kodebank', $_POST['code_bank'])->where('no', '=', $_GET['id'])->save_update();

        if ($save) {
            echo '<script>alert("Berhasil simpan data")</script>';
            echo "<script> document.location.href='/bank.php'; </script>";
        } else {
            echo '<script>alert("Gagal simpan data")</script>';
            echo "<script> document.location.href='".$_SERVER['HTTP_REFERER']."'; </script>";
        }
    }
}

if (isset($_GET['action'])) {
    if ($_GET['action'] == 'edit') {
        $bank = $con->select('*')->from('bank')->where("no", "=", $_GET["id"])->first();
    }

    if ($_GET['action'] == 'delete') {
        $delete = $con->delete('bank')->where("no", "=", $_GET["id"])->save_delete();
        if ($delete) {
            echo '<script>alert("Berhasil simpan data")</script>';
            echo "<script> document.location.href='/bank.php'; </script>";
        } else {
            echo '<script>alert("Gagal simpan data")</script>';
            echo "<script> document.location.href='".$_SERVER['HTTP_REFERER']."'; </script>";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Form Pengajuan Budget</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" defer></script>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap.min.css" rel="stylesheet"/>

    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap.min.js"></script>

</head>
<body>

<nav class="navbar navbar-inverse">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="home-direksi.php">Budget-Ing</a>
        </div>
        <div class="collapse navbar-collapse" id="myNavbar">
            <ul class="nav navbar-nav">
                <?php if ($_SESSION['hak_akses'] == 'HRD') { ?>
                    <li><a href="home-direksi.php">Home</a></li>
                    <li><a href="list-direksi.php">List</a></li>
                    <li><a href="saldobpu.php">Saldo BPU</a></li>
                    <!--<li><a href="summary.php">Summary</a></li>-->
                    <li><a href="listfinish-direksi.php">Budget Finish</a></li>
                <?php } else { ?>
                    <li><a href="home-finance.php">Home</a></li>
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
                    <li><a href="saldobpu.php">Saldo BPU</a></li>
                    <li><a href="history-finance.php">History</a></li>
                    <li><a href="list.php">Personal</a></li>
                    <li><a href="summary-finance.php">Summary</a></li>
                    <li><a href="bank.php">Bank</a></li>
                <?php } ?>
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">Rekap
                        <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="rekap-finance.php">Ready To Paid (MRI Kas)</a></li>
                        <li><a href="rekap-finance-mripal.php">Ready To Paid (MRI PAL)</a></li>
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
            </ul>


            <?php if ($_SESSION['hak_akses'] != 'HRD') { ?>

                <ul class="nav navbar-nav navbar-right">
                    <li><a href="/log-notifikasi-aplikasi/index.html" target="_blank"><i class="fa fa-envelope"></i></a></li>

                    <li><a href="ubahpassword.php"><span class="glyphicon glyphicon-user"></span><?php echo $_SESSION['nama_user']; ?> (<?php echo $_SESSION['divisi']; ?>)</a></li>
                    <li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> Logout</a></li>
                </ul>
            <?php } else {

                ?>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="/log-notifikasi-aplikasi/index.html" target="_blank"><i class="fa fa-envelope"></i></a></li>


                    <li><a href="ubahpassword.php"><span class="glyphicon glyphicon-user"></span><?php echo $_SESSION['nama_user']; ?> (<?php echo $_SESSION['divisi']; ?>)</a></li>
                    <li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> Logout</a></li>
                </ul>
            <?php } ?>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-4">
            <form method="post" action="/bank.php?action<?= isset($bank) ? '=update&id='.$bank[no] : '' ?>">
                <input name="action" value="<?= isset($bank) ? 'update' : 'create' ?>" type="hidden">
                <div class="form-group">
                    <label>Nama Bank</label>
                    <input class="form-control" type="text" name="bank_name" value="<?= isset($bank) ? $bank['namabank'] : '' ?>" required>
                </div>
                <div class="form-group">
                    <label>Swift Code Bank</label>
                    <input class="form-control" type="text" name="code_bank" value="<?= isset($bank) ? $bank['kodebank'] : '' ?>" required>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
            </form>
        </div>
        <div class="col-lg-8">
            <?php
            $banks = $con->select("*")->order_by('no', 'desc')->get('bank');
            ?>
            <table id="example" class="table table-hover table-striped">
                <thead class="bg-success" style="height: 50px">
                    <th class="text-center" width="5%">No</th>
                    <th class="text-center">Nama Bank</th>
                    <th class="text-center">Swift Code Bank</th>
                    <th class="text-center">Aksi</th>
                </thead>
                <tbody>
                <?php
                if (count($banks) == 0) {
                    echo '<tr>
                    <td colspan="4" class="text-center">Tidak Ada Data</td>
                </tr>';
                } else {
                    $no = 1;
                    foreach ($banks as $bank) {
                        echo '<tr><td>'.$no++.'</td><td>'.$bank["namabank"].'</td><td>'.$bank["kodebank"].'</td><td><a href="/bank.php?id='.$bank[no].'&action=edit" class="btn btn-sm btn-primary">Edit</a>&nbsp;<a href="/bank.php?id='.$bank[no].'&action=delete" class="btn btn-sm btn-danger">Hapus</a> </td></tr>';
                    }
                }
                ?>

                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#example').DataTable();
    } );
</script>

</body>

</html>