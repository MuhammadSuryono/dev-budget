<?php
error_reporting(0);
session_start();
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();


$con->set_name_db(DB_JAY);
$con->init_connection();
$koneksiJay = $con->connect();

if (!isset($_SESSION['nama_user'])) {
    header("location:login.php");
    // die('location:login.php');//jika belum login jangan lanjut
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
                    <li><a href="list.php">List</a></li>
                    <!-- <li class="active"><a href="request-budget.php">Request Budget</a></li> -->
                </ul>
               <ul class="nav navbar-nav navbar-right">
                        <li><a href="notif-page.php"><i class="fa fa-envelope"></i></a></li>
                    <li><a href="ubahpassword.php"><span class="glyphicon glyphicon-user"></span><?php echo $_SESSION['nama_user']; ?> (<?php echo $_SESSION['divisi']; ?>)</a></li>]; ?>)</a></li>
                    <li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> Logout</a></li>
                </ul>
            <?php endif; ?>
            </div>
        </div>
    </nav>


    <div class="container">

        <?php
        $namaProject = ($_GET['nama']) ? $_GET['nama'] : '';
        if (strpos($namaProject, '%20') !== false) {
            str_replace("%20", ' ', $namaProject);
        }
        $tahun =  ($_GET['tahun']) ? $_GET['tahun'] : '';
        $kategori =  ($_GET['kategori']) ? $_GET['kategori'] : '';
        if ($kategori == 'B1') {
            $arrDataB1 = [
                [
                    "Honor Jakarta",
                    "Jabodetabek",
                    "Honor Jakarta",
                    "Shopper/PWT",
                    0,
                    0,
                    0,
                ],
                [
                    "Honor Luar Kota",
                    "Luar Kota",
                    "Honor Luar Jakarta",
                    "Shopper/PWT",
                    0,
                    0,
                    0,
                ],
                [
                    "STKB Transaksi Jakarta",
                    "Jabodetabek",
                    "STKB TRK Jakarta",
                    "TLF",
                    0,
                    0,
                    0,
                ],
                [
                    "STKB Transaksi Luar Kota",
                    "Luar Kota",
                    "STKB TRK Luar Kota",
                    "TLF",
                    0,
                    0,
                    0,
                ],
                [
                    "STKB OPS",
                    "Jabodetabek dan Luar Kota",
                    "STKB OPS",
                    "TLF",
                    0,
                    0,
                    0,
                ]
            ];
        }
        ?>

        <center>
            <h2>Form Permohonan Budget</h2>
        </center>

        <form id="myForm" action="request-budget-proses.php" method="post">
            <br /><br />

            <div class="row">
                <div class="col-xs-2">Nama Yang Mengajukan</div>
                <div class="col-xs-3">: <b><?= $_SESSION['nama_user'] ?></b></div>
                <input type="hidden" name="namaUser" value="<?= $_SESSION['nama_user'] ?>">
            </div>

            <div class="row">
                <div class="col-xs-2">Divisi</div>
                <div class="col-xs-3">: <b><?= $_SESSION['divisi'] ?></b></div>
                <input type="hidden" name="divisiUser" value="<?= $_SESSION['divisi'] ?>">
            </div>

            <br>

            <div class="row">
                <div class="col-lg-2">
                    <label for="namaProject">Nama Project</label>
                    <input type="text" class="form-control" id="namaProject" name="namaProject" value="<?= $namaProject ?>" required>
                </div>
                <div class="col-lg-2">
                    <label for="tahun">Tahun</label>
                    <select class="form-control" id="tahun" name="tahun" required>
                        <option value="" selected>-</option>
                        <?php
                        for ($i = 2017; $i <= 2030; $i++) {
                            if ($i == $tahun) echo "<option selected>$i</option>";
                            else echo "<option>$i</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-lg-2">
                    <label for="kategori">Pilih Kategori</label>
                    <select class="form-control" id="kategori" name="kategori" required>
                        <option value="" selected>-</option>
                        <?php
                        if ($kategori == 'B1') {
                            echo "<option id='jenb1' value='B1' selected>B1</option>";
                            echo "<option id='jenb2' value='B2'>B2</option>";
                            echo "<option id='nonrut' value='nonRutin'>Non Rutin</option>";
                        } else if ($kategori == 'B2') {
                            echo "<option id='jenb1' value='B1'>B1</option>";
                            echo "<option id='jenb2' value='B2' selected>B2</option>";
                            echo "<option id='nonrut' value='nonRutin'>Non Rutin</option>";
                        } else if ($kategori) {
                            echo "<option id='jenb1' value='B1'>B1</option>";
                            echo "<option id='jenb2' value='B2'>B2</option>";
                            echo "<option id='nonrut' value='nonRutin' selected>Non Rutin</option>";
                        } else {
                            echo "<option id='jenb1' value='B1'>B1</option>";
                            echo "<option id='jenb2' value='B2'>B2</option>";
                            echo "<option id='nonrut' value='nonRutin'>Non Rutin</option>";
                        }
                        ?>
                    </select>
                </div>
                <?php if ($kategori == "B1") : ?>
                    <div id="" class="form-group col-lg-3">
                        <label for="kodeproject">Kode Project</label>
                        <select class="custom-select form-control" id="kodeproject" name="kodeProject[]" multiple>
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
                        <!-- <select class="form-control" id="kodeproject" name="kodeProject">
                            <option selected disabled>Pilih Project</option>
                            <?php
                            $kode = mysqli_query($koneksiJay, "SELECT * FROM project WHERE visible='y' ORDER BY nama");
                            foreach ($kode as $rc) {
                                $kodepro = $rc['kode'];
                                $nampro  = $rc['nama'];
                                echo "<option value='$kodepro'>$kodepro - $nampro</option>";
                            }
                            ?>
                        </select> -->
                    </div>
                <?php endif; ?>
            </div>

            <br>



            <div role="tabpanel" class="tab-pane fade in active" id="budget" aria-labelledby="home-tab">

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
                                    <th>Edit</th>
                                </tr>
                            </thead>

                            <tbody id="data-body">
                                <?php if ($kategori == 'B1') : ?>
                                    <?php $j = 1; ?>
                                    <?php foreach ($arrDataB1 as $adb) : ?>
                                        <?php $i = 0; ?>
                                        <?php $total = 0; ?>
                                        <?php while ($i < count($adb)) : ?>
                                            <tr>
                                                <td><?= $j ?></td>
                                                <input type="hidden" id="inputNama<?= $j ?>" name="nama[]" value="<?= $adb[$i] ?>">
                                                <td id="nama<?= $j ?>"><?= $adb[$i++] ?></td>
                                                <input type="hidden" id="inputKota<?= $j ?>" name="kota[]" value="<?= $adb[$i] ?>">
                                                <td id="kota<?= $j ?>"><?= $adb[$i++] ?></td>
                                                <input type="hidden" id="inputStatus<?= $j ?>" name="status[]" value="<?= $adb[$i] ?>">
                                                <td id="status<?= $j ?>"><?= $adb[$i++] ?></td>
                                                <input type="hidden" id="inputPUang<?= $j ?>" name="pUang[]" value="<?= $adb[$i] ?>">
                                                <td id="pUang<?= $j ?>"><?= $adb[$i++] ?></td>
                                                <input type="hidden" id="inputHarga<?= $j ?>" name="harga[]" value="<?= $adb[$i] ?>">
                                                <td id="harga<?= $j ?>"><?= 'Rp. ' . number_format($adb[$i++], 0, '', ','); ?></td>
                                                <input type="hidden" id="inputQuantity<?= $j ?>" name="quantity[]" value="<?= $adb[$i] ?>">
                                                <td id="quantity<?= $j ?>"><?= $adb[$i++] ?></td>
                                                <input type="hidden" id="inputTHarga<?= $j ?>" name="tHarga[]" value="<?= $adb[$i] ?>">
                                                <td class="tHarga" id="tHarga<?= $j ?>"><?= 'Rp. ' . number_format($adb[$i++], 0, '', ','); ?></td>
                                                <td><button type="button" class="btn btn-default btn-small buttonEdit" id="buttonEdit<?= $j ?>">Edit</button></td>
                                            </tr>
                                        <?php endwhile; ?>
                                        <?php $j++; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div><!-- /.table-responsive -->
                </div>

                <br /><br />

                <div class="row">
                    <div class="col-xs-3">Total Keseluruhan</div>
                    <div class="col-xs-3">: <b class="totalElement">Rp. 0</b></div>
                    <input type="hidden" name="tKeseluruhan" id="totalKeseluruhan" value="">
                </div>

                <br /><br />

                <div class="row" style="margin-bottom: 100px;">
                    <button type="button" class="btn btn-default btn-small" onclick="tambah_budget()">Tambah</button>
                    <input type="button" class="btn btn-primary" data-toggle="modal" data-target="#submitModal" value="Submit" />
                </div>
        </form>
    </div>

    <div class="modal fade" id="myModal" role="dialog">
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
                        <label for="status">Status :</label>
                        <select class="form-control" id="statusEdit" name="status">
                            <option value="">-</option>
                            <option value="UM Burek">UM Burek</option>
                            <option value="UM">UM</option>
                            <option value="Vendor/Supplier">Vendor / Supplier</option>
                            <option value="Honor Eksternal">Honor Eksternal</option>
                            <option value="Biaya Lumpsum">Biaya Lumpsum Operational</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="penerima" class="control-label">Penerima :</label>
                        <input type="text" class="form-control" id="penerimaEdit" value="" name="penerima">
                    </div>

                    <div class="form-group">
                        <label for="harga" class="control-label">Harga (IDR) :</label>
                        <input type="text" class="form-control" id="hargaEdit" value="" name="harga" onkeyup="sum();">
                    </div>

                    <div class="form-group">
                        <label for="quantity" class="control-label">Quantity :</label>
                        <input type="text" class="form-control" id="quantityEdit" value="" name="quantity" onkeyup="sum();">
                    </div>

                    <div class="form-group">
                        <label for="total">Total Harga (IDR) :</label>
                        <input type="text" class="form-control" id="totalEdit" name="total" onkeyup="sum();" value="" readonly>
                    </div>

                    <button class="btn btn-primary" type="submit" name="submit" id="buttonEditModal">Update</button>
                </div>
                <div class="modal-footer">
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
                        <label for="status">Status :</label>
                        <select class="form-control" id="statusTambah" name="status" required>
                            <option value="UM Burek">UM Burek</option>
                            <option value="UM">UM</option>
                            <option value="Vendor/Supplier">Vendor / Supplier</option>
                            <option value="Honor Eksternal">Honor Eksternal</option>
                            <option value="Biaya Lumpsum">Biaya Lumpsum Operational</option>
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
                        <input type="text" class="form-control" id="totalTambah" name="total" onkeyup="sumTambah();" value="" readonly>
                    </div>

                    <button class="btn btn-primary" type="submit" name="submit" id="buttonTambahModal">Update</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="submitModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    Confirm Submit
                </div>
                <div class="modal-body">
                    Klik submit untuk menyimpan data
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <a href="#" id="submitButton" class="btn btn-success success">Submit</a>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            const arrStatus = ['Honor Jakarta', 'Honor Luar Jakarta', 'STKB TRK Jakarta', 'STKB TRK Luar Kota', 'STKB OPS'];

            hitungTotalKeseluruhan();
            $('#kategori').on('change', function() {
                if ($('#kategori').val()) {
                    const namaProject = $('#namaProject').val();
                    const tahun = $('#tahun').val();
                    const kategori = $('#kategori').val();
                    $.ajax({
                        type: "post",
                        data: {
                            namaProject: namaProject,
                            tahun: tahun,
                            kategori: kategori,
                        },
                        success: function() {
                            document.location.href = "request-budget.php?nama=" + namaProject + "&tahun=" + tahun + "&kategori=" + kategori;
                        }
                    })
                }
            })

            let buttonClicked = '';
            let idButtonClicked = '';
            let numberClicked = '';
            let buttonAddQuestion = '';
            setInterval(function() {
                buttonAddQuestion = document.querySelectorAll(".buttonEdit");
                buttonAddQuestion.forEach(function(e, i) {
                    e.addEventListener("click", function() {
                        buttonClicked = e;
                        numberClicked = i + 1;
                        if (arrStatus.includes($(`#status${numberClicked}`).text())) {
                            $(`#statusEdit option[value=""]`).text($(`#status${numberClicked}`).text())
                            $(`#statusEdit option[value=""]`).val($(`#status${numberClicked}`).text())
                            $(`#statusEdit`).prop('disabled', 'true');
                        } else {
                            $(`#statusEdit`).prop('disabled', false);
                        }
                        fillingEditModal(numberClicked);
                        $('#myModal').modal();
                    });
                });
            }, 1000)


            const buttonEditModal = document.querySelector("#buttonEditModal");
            buttonEditModal.addEventListener("click", function() {

                updateRow(numberClicked);

                hitungTotalKeseluruhan();

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
                                <input type="hidden" id="inputTHarga${countTr}" name="tHarga[]" value="${total.replace("Rp. ", "")}">
                                <td><button type="button" class="btn btn-default btn-small buttonEdit" id="buttonEdit${countTr}">Edit</button></td>
                            </tr>
                    `;
                $("#data-body").append(html);

                hitungTotalKeseluruhan();

                $("#rincianTambah").val('');
                $("#kotaTambah").val('');
                $("#statusTambah").val('');
                $("#penerimaTambah").val('');
                $("#hargaTambah").val('');
                $("#quantityTambah").val('');
                $("#totalTambah").val('');

                $('#myModal2').modal('toggle');

            });

            $('#submitButton').click(function() {
                $('#myForm').submit();
            });

        });

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
            }
        }

        function sumTambah() {
            var txtSecondNumberValue = document.getElementById('hargaTambah').value;
            txtSecondNumberValue = txtSecondNumberValue.replace("Rp. ", "");
            var txtTigaNumberValue = document.getElementById('quantityTambah').value;
            var result = parseFloat(txtSecondNumberValue) * parseFloat(txtTigaNumberValue);
            if (!isNaN(result)) {
                document.getElementById('totalTambah').value = `Rp. ${(result)}`;
            }
        }

        function fillingEditModal(numberClicked) {
            $(`#rincianEdit`).val($(`#nama${numberClicked}`).text());
            $(`#kotaEdit`).val($(`#kota${numberClicked}`).text());
            $(`#statusEdit`).val($(`#status${numberClicked}`).text());
            $(`#penerimaEdit`).val($(`#pUang${numberClicked}`).text());
            $(`#hargaEdit`).val($(`#harga${numberClicked}`).text());
            $(`#quantityEdit`).val($(`#quantity${numberClicked}`).text());
            sum();
        }

        function hitungTotalKeseluruhan() {
            const allTotalHarga = document.querySelectorAll(".tHarga");
            let total = 0;
            allTotalHarga.forEach(function(e, i) {
                let status = $(`#status${i+1}`).text();
                if (status != "UM Burek") {
                    let result = e.textContent.replace("Rp. ", "");
                    total += parseFloat(result);
                }
            })
            $('.totalElement').text(`Rp. ${formatNumber(total)}`);
            $('#totalKeseluruhan').val((total));
        }

        function resetOption(arrStatus) {
            const valueOption = document.querySelectorAll("#statusEdit option");
            valueOption.forEach(function(e, i) {
                if (arrStatus.includes(e.value)) {
                    console.log(e.value);
                    $("#statusEdit option[value=" + `"${e.value}"` + "]").val("");
                    $("#statusEdit option[value=" + `"${e.value}"` + "]").text("-");
                }
            })
        };

        function updateRow(numberClicked) {
            let rincianEdit = $(`#rincianEdit`).val();
            let kotaEdit = $(`#kotaEdit`).val();
            let statusValue = $(`#statusEdit`).val();
            let penerimaEdit = $(`#penerimaEdit`).val();
            let hargaEdit = $(`#hargaEdit`).val();
            let quantityEdit = $(`#quantityEdit`).val();
            let totalEdit = $(`#totalEdit`).val();

            $(`#nama${numberClicked}`).text(rincianEdit);
            $(`#kota${numberClicked}`).text(kotaEdit);
            $(`#status${numberClicked}`).text(statusValue);
            $(`#pUang${numberClicked}`).text(penerimaEdit);
            $(`#harga${numberClicked}`).text(hargaEdit);
            $(`#quantity${numberClicked}`).text(quantityEdit);
            $(`#tHarga${numberClicked}`).text(totalEdit);

            hargaEdit = hargaEdit.replace("Rp. ", "");
            totalEdit = totalEdit.replace("Rp. ", "");

            $(`#inputNama${numberClicked}`).val(rincianEdit);
            $(`#inputKota${numberClicked}`).val(kotaEdit);
            $(`#inputStatus${numberClicked}`).val(statusValue);
            $(`#inputPUang${numberClicked}`).val(penerimaEdit);
            $(`#inputHarga${numberClicked}`).val(hargaEdit);
            $(`#inputQuantity${numberClicked}`).val(quantityEdit);
            $(`#inputTHarga${numberClicked}`).val(totalEdit);

        }


        // function edit_budget(no, waktu) {
        //     // alert(noid+' - '+waktu);
        //     $.ajax({
        //         type: 'post',
        //         url: 'editpengaju.php',
        //         data: {
        //             no: no,
        //             waktu: waktu
        //         },
        //         success: function(data) {
        //             $('.fetched-data').html(data); //menampilkan data ke dalam modal
        //             $('#myModal').modal();
        //         }
        //     });
        // }

        function tambah_budget(waktu, noid) {
            // alert(noid+' - '+waktu);
            $.ajax({
                type: 'post',
                url: 'tambahpengaju.php',
                data: {
                    waktu: waktu,
                    noid: noid
                },
                success: function(data) {
                    $("#isi_form").html(data);
                    //$('.fetched-data').html(data);//menampilkan data ke dalam modal
                    $('#myModal2').modal();
                }
            });
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