<?php
error_reporting(0);
session_start();

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

if (!isset($_SESSION['nama_user'])) {
    die("Anda belum login"); //jika belum login jangan lanjut
}


$code   = $_GET['code'];
$waktu  = $_GET['waktu'];
$termm  = $_GET['term'];

$query = mysqli_query($koneksi, "SELECT * FROM bpu where no='$code' AND waktu='$waktu' AND term='$termm'");
$i = 1;

$selectproject = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE waktu='$waktu'");
$ba = mysqli_fetch_assoc($selectproject);

$project = $ba['nama'];
$plusterm = $d['term'] + 1;

while ($d = mysqli_fetch_array($query)) {

?>



    <!doctype html>
    <html>

    <head>
        <meta charset="utf-8">
        <title>BPU Project MRI</title>

        <link rel="stylesheet" type="text/css" href="print.css" />

        <link rel="stylesheet" type="text/css" href="css/styleprint.css" />


        <script type="text/javascript">
            /*--This JavaScript method for Print command--*/

            function PrintDoc() {

                var toPrint = document.getElementById('printarea');

                var popupWin = window.open('', '_blank', 'width=700,height=400,location=no,left=200px');

                popupWin.document.open();

                popupWin.document.write('<html><title>::Preview::</title><link rel="stylesheet" type="text/css" href="css/styleprint.css" /></head><body onload="window.print()">')

                popupWin.document.write(toPrint.innerHTML);

                popupWin.document.write('</html>');

                popupWin.document.close();

            }

            /*--This JavaScript method for Print Preview command--*/

            function PrintPreview() {

                var toPrint = document.getElementById('printarea');

                var popupWin = window.open('', '_blank', 'width=700,height=500,location=no,left=200px');

                popupWin.document.open();

                popupWin.document.write('<html><title>::Print Preview::</title><link rel="stylesheet" type="text/css" href="print.css" media="screen"/></head><body">')

                popupWin.document.write(toPrint.innerHTML);

                popupWin.document.write('</html>');

                popupWin.document.close();

            }
        </script>

    </head>

    <body id="printarea">

        <div class="invoice-box">
            <table cellpadding="0" cellspacing="0">
                <tr class="top">
                    <td colspan="2">
                        <table>
                            <tr>
                                <td class="title">
                                    <b>Project <?php echo $project ?></b>
                                </td>

                                <td>
                                    Kode BPU : <b><?php echo strtoupper($project);
                                                    echo "-";
                                                    echo $d['no'];
                                                    echo "-";
                                                    echo $plusterm; ?></b><br>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <tr>
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                <?php
                                $sql2 = mysqli_query($koneksi, "SELECT * FROM selesai WHERE waktu='$waktu' AND no='$code'");
                                $c2 = mysqli_fetch_assoc($sql2);
                                ?>
                                Rincian dan Keterangan :<br />
                                <u><?php echo $c2['rincian']; ?></u>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <br>

            <table border="1">
                <thead>
                    <th>No</th>
                    <th>Kota</th>
                    <th>Jenis</th>
                    <th>Pengaju BPU</th>
                    <th>Tanggal Permintaan</th>
                    <th>Total (IDR)</th>
                    <th>Term</th>
                    <th>Persetujuan</th>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    $sql = mysqli_query($koneksi, "SELECT * FROM selesai WHERE waktu='$waktu' AND no='$code'");
                    while ($c = mysqli_fetch_array($sql)) {

                    ?>
                        <tr>
                            <td><?php echo $c['no']; ?></td>
                            <td><?php echo $c['kota']; ?></td>
                            <td><?php echo $c['status']; ?></td>
                            <td><?php echo $c['pengaju']; ?> (<?php echo $c['divisi']; ?>)</td>
                            <td><?php echo $d['tglcair']; ?></td>
                            <td><?php echo 'Rp. ' . number_format($d['jumlahbayar'], 0, '', ','); ?></td>
                            <td><?php echo $plusterm ?></td>
                            <td><?php echo $d['persetujuan']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <br>

            <table border="1">

                <tr>
                    <td>Nama Bank :</td>
                    <td colspan="2"><strong><?php echo $d['namabank']; ?></strong></td>
                </tr>

                <tr>
                    <td>Nomor Rekening :</td>
                    <td colspan="1"><strong><?php echo $d['norek']; ?></strong></td>
                </tr>

                <tr>
                    <td>Nama Penerima :</td>
                    <td colspan="1"><strong><?php echo $d['namapenerima']; ?></strong></td>
                </tr>

                <tr>
                    <td>Tanggal Dibayar :</td>
                    <td colspan="1"><strong><?php echo $d['tanggalbayar']; ?></strong></td>
                </tr>

                <tr>
                    <td>Nomor Voucher :</td>
                    <td colspan="1"><strong><?php echo $d['novoucher']; ?></strong></td>
                </tr>

                <tr>
                    <td>Status :</td>
                    <td colspan="1"><strong><?php echo $d['status']; ?></strong></td>
                </tr>

            </table>
            <br>
        </div>

    <?php } ?>
    </body>
    <input type="button" value="Print" class="btn" onclick="PrintDoc()" />

    </html>