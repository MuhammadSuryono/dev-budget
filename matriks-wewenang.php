<?php
 error_reporting(0);
session_start();

require "application/config/database.php";
$con = new Database();
$koneksi = $con->connect();
$con->load_database($koneksi);

if (!isset($_SESSION['nama_user'])) {
    header("location:login.php");
}

$idUser = $_SESSION['id_user'];
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Form Pengajuan Budget 1</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">

</head>

<style>
    .text-blink {
        animation: blinker 1s linear infinite;
    }

    @keyframes blinker {
        50% {
            opacity: 0;
        }
    }

    .alert-blink {
        animation: blinker-alert 5s linear infinite;
    }

    @keyframes blinker-alert {
        50% {
            opacity: 0;
        }
    }
</style>

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
                <li class="active"><a href="home-direksi.php">Home</a></li>
                <li><a href="list-direksi.php">List</a></li>
                <li><a href="saldobpu.php">Saldo BPU</a></li>
                <!--<li><a href="summary.php">Summary</a></li>-->
                <!-- <li><a href="hak-akses.php">Hak Akses</a></li> -->
                <li><a href="listfinish-direksi.php">Budget Finish</a></li>
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
                <li><a href="matriks-wewenang.php">Matriks Wewenang</a></li>

                <!-- <li><a href="history-direksi.php">History</a></li> -->
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="/log-notifikasi-aplikasi/index.html" target="_blank"><i class="fa fa-envelope"></i></a></li>

                <li><a href="ubahpassword.php"><span class="glyphicon glyphicon-user"></span><?php echo $_SESSION['nama_user']; ?> (<?php echo $_SESSION['divisi']; ?>)</a></li>
                <li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<br /><br />

<div class="container-fluid">

    <?php
    $dataRoles = $con->select("*")->from("tb_role_bpu")->get();
    ?>

    <h4><strong>Matriks Wewenang Aplikasi Budget Online<strong></h4>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#roleBpuModal"> Tambah Wewenang</button>
    <br/>
    <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
        <div class="panel-body no-padding">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr class="text-center">
                        <td>No</td>
                        <td>Nama Folder</td>
                        <td>Jenis BPU</td>
                        <td>Pembuat BPU</td>
                        <td>Mengetahui BPU</td>
                        <td>Validator BPU</td>
                        <td>Approver BPU</td>
                        <td>Kondisi BPU</td>
                        <td>Nilai Kondisi BPU</td>
                        <td>Aksi</td>
                    </tr>
                </thead>
                <tbody>
                <?php
                $no = 1;
                $dataKondisi = [
                  "less" => "<",
                  "more" => ">"
                ];
                foreach ($dataRoles as $role) {
                    $creator = $con->select("nama_user")->from("tb_user")->where("id_user", "=", $role['create_bpu'])->first();
                    $knowledge = $con->select("nama_user")->from("tb_user")->where("id_user", "=", $role['knowledge_bpu'])->first();
                    $validator = $con->select("nama_user")->from("tb_user")->where("id_user", "=", $role['validate_bpu'])->first();
                    $approver = $con->select("nama_user")->from("tb_user")->where("id_user", "=", $role['approver_bpu'])->first();
                    ?>



                    <tr>
                        <td><?= $no ?></td>
                        <td><?= $role['folder_name'] ?></td>
                        <td><?= $role['bpu'] ?></td>
                        <td><?= $creator['nama_user'] ?></td>
                        <td><?= $knowledge['nama_user'] ?></td>
                        <td><?= $validator['nama_user'] ?></td>
                        <td><?= $approver['nama_user'] ?></td>
                        <td class="text-center"><?= $dataKondisi[$role['condition']] ?></td>
                        <td><?= number_format($role['value_condition']) ?></td>
                        <td>
                            <button type="button" class="btn btn-primary btn-sm" name="edit"
                                    data-id="<?= $role['id'] ?>"
                                    data-folder="<?= $role['folder_name'] ?>"
                                    data-bpu="<?= $role['bpu'] ?>"
                                    data-creator="<?= $role['create_bpu'] ?>"
                                    data-knowledge="<?= $role['knowledge_bpu'] ?>"
                                    data-validator="<?= $role['validate_bpu'] ?>"
                                    data-approver="<?= $role['approver_bpu'] ?>"
                                    data-condition="<?= $role['condition'] ?>"
                                    data-valueCondition="<?= $role['value_condition'] ?>"
                                    onclick="onEdit(this)">Edit</button>
                            <button type="button" class="btn btn-danger btn-sm" name="delete" id="delete" data-id="<?= $role['id'] ?>" onclick="onDelete(this)">Hapus</button>
                        </td>
                    </tr>
                <?php
                    $no++;
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>

    <br>

</div>

<div class="modal fade" id="deleteRoleModal" tabindex="-1" role="dialog" aria-labelledby="deleteRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Konfirmasi Delete
            </div>
            <div class="modal-body">
                <p id="message-delete">Apakah anda yakin ingin menhapus data ini?</p>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-danger success" id="btn-delete-modal">Ya, Hapus</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="roleBpuModal" tabindex="-1" role="dialog" aria-labelledby="roleBpuLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Tambah Wewenang BPU
            </div>
            <form action="proses/Wewenang.php" id="form_wewenang" method="post">
            <div class="modal-body">
                <?php
                $users = $con->select("*")->from("tb_user")->get();
                ?>
                <div class="form-group">
                    <label for="folderName">Nama Folder</label>
                    <select name="folderName" id="folderName" class="form-control" required>
                        <option value="">-- Pilih Folder --</option>
                        <?php
                        $dataFolder = $con->select("*")->from("tb_folder")->get();
                        foreach ($dataFolder as $folder) {
                            ?>
                            <option value="<?= $folder['kode'] ?>"><?= $folder['folder_name'] ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Jenis BPU</label>
                    <select name="jenisBpu" id="jenisBpu" class="form-control" required>
                        <option value="">-- Pilih Jenis BPU --</option>
                        <option value="Vendor/Supplier">Vendor/Supplier</option>
                        <option value="Honor Eksternal">Honor Eksternal</option>
                        <option value="STKB TRK Luar Kota">STKB TRK Luar Kota</option>
                        <option value="STKB OPS">STKB OPS</option>
                        <option value="Honor Luar Kota">Honor Luar Kota</option>
                        <option value="Honor Jakarta">Honor Jakarta</option>
                        <option value="STKB TRK Jakarta">STKB TRK Jakarta
                        <option value="Honor SHP Jabodetabek">Honor SHP Jabodetabek</option>
                        <option value="Honor Area Head">Honor Area Head</option>
                        <option value="Honor SHI/PWT Luar Kota">Honor SHI/PWT Luar Kota</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="creatorBpu">Pembuat BPU</label>
                    <select name="creatorBpu" id="creatorBpu" class="form-control" required>
                        <option value="">-- Pilih Pembuat BPU --</option>
                        <?php
                        foreach ($users as $user) {
                            ?>
                            <option value="<?= $user['id_user'] ?>"><?= $user['nama_user'] ?> - <?= $user['divisi'] ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="knowledgeBpu">Mengetahui BPU</label>
                    <select name="knowledgeBpu" id="knowledgeBpu" class="form-control">
                        <option value="">-- Pilih Mengetahui BPU --</option>
                        <?php
                        foreach ($users as $user) {
                            ?>
                            <option value="<?= $user['id_user'] ?>"><?= $user['nama_user'] ?> - <?= $user['divisi'] ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="validatorBpu">Validator BPU</label>
                    <select name="validatorBpu" id="validatorBpu" class="form-control" required>
                        <option value="">-- Pilih Validator BPU --</option>
                        <?php
                        foreach ($users as $user) {
                            ?>
                            <option value="<?= $user['id_user'] ?>"><?= $user['nama_user'] ?> - <?= $user['divisi'] ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="approverBpu">Approver BPU</label>
                    <select name="approverBpu" id="approverBpu" class="form-control" required>
                        <option value="">-- Pilih Approver BPU --</option>
                        <?php
                        foreach ($users as $user) {
                            ?>
                            <option value="<?= $user['id_user'] ?>"><?= $user['nama_user'] ?> - <?= $user['divisi'] ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="kondisiBpu">Kondisi BPU</label>
                    <select name="kondisiBpu" id="kondisiBpu" class="form-control">
                        <option value="">-- Pilih Kondisi BPU --</option>
                        <option value="more">Lebih Dari</option>
                        <option value="less">Kurang Dari</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="valueKondisi">Nilai Kondisi</label>
                    <input class="form-control" name="valueKondisi" id="valueKondisi" type="text" />
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="buttonSubmitEmail" class="btn btn-success success">Submit</button>
            </div>
            </form>
        </div>
    </div>
</div>

</body>
<script>
    function onDelete(e) {
        $('#deleteRoleModal').modal('show');
        let id = e.getAttribute('data-id');
        let btnDelete = document.getElementById('btn-delete-modal');

        btnDelete.setAttribute('href', `/proses/Wewenang.php?action=delete&id=${id}`);
    }

    function onEdit(e) {
        $('#roleBpuModal').modal('show');

        let optionsFolder = document.getElementById('folderName')
        let optionBpu = document.getElementById('jenisBpu')
        let optionCreator = document.getElementById('creatorBpu')
        let optionKnowledge = document.getElementById('knowledgeBpu')
        let optionValidator = document.getElementById('validatorBpu')
        let optionApprover = document.getElementById('approverBpu')
        let optionCondition = document.getElementById('kondisiBpu')
        let valueCondition = document.getElementById('valueKondisi')
        let formWewenang = document.getElementById("form_wewenang")

        let dataSet = e.dataset
        let id = dataSet.id;
        let folderName = dataSet.folder;
        let jenisBpu = dataSet.bpu;
        let creatorBpu = dataSet.creator;
        let knowledgeBpu = dataSet.knowledge;
        let validatorBpu = dataSet.validator;
        let approverBpu = dataSet.approver;
        let conditionBpu = dataSet.condition;
        let valueConditionBpu = dataSet.valuecondition;

        optionsFolder.value = folderName
        optionBpu.value = jenisBpu
        optionCreator.value = creatorBpu
        optionKnowledge.value = knowledgeBpu
        optionValidator.value = validatorBpu
        optionApprover.value = approverBpu
        optionCondition.value = conditionBpu
        valueCondition.value = valueConditionBpu === undefined ? "" : valueConditionBpu

        formWewenang.setAttribute('action', `${formWewenang.getAttribute('action')}?id=${id}&action=update`)

    }
</script>
</html>