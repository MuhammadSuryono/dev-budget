<?php
session_start();

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
require_once("dompdf/dompdf_config.inc.php");

$id = $_GET['id'];
$select = mysqli_query($koneksi, "SELECT * FROM pengajuan_request WHERE id='$id'");
$d = mysqli_fetch_assoc($select);

$i = 1;
$sql = mysqli_query($koneksi, "SELECT * FROM selesai_request WHERE id_pengajuan_request = '$id' ORDER BY urutan ASC");

$html = "
        <center>
            <h2>" . $d['nama'] . "</h2>
        </center>
        <br>
        <div class='row'>
            <div class='col-xs-3'>Nama Yang Mengajukan : <b>" . $d['pengaju'] . "</div>
        </div>
        <div class='row'>
            <div class='col-xs-3'>Divisi : <b>" . $d['divisi'] . "</div>
        </div>
        <div class='row'>
            <div class='col-xs-3'>Tahun : <b>" . $d['tahun'] . "</div>
        </div>
        <br>
        <table border='1' cellpadding='10' cellspacing='0' style='text-align:center;'>
                        <thead>
                            <tr class='warning'>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Kota</th>
                                <th>Status</th>
                                <th>Penerima Uang</th>
                                <th>Harga (IDR)</th>
                                <th>Total Quantity</th>
                                <th>Total Harga (IDR)</th>
                            </tr>
                        </thead>

                        <tbody id='data-body'>
                        ";
// var_dump($html);
while ($a = mysqli_fetch_array($sql)) {

    $html .= ' 
        <tr>
        <th scope="row">' . $i . '</th>
        <td>' . $a['rincian'] . '</td>
        <td>' . $a['kota'] . '</td>
        <td>' . $a['status'] . '</td>
        <td>' . $a['penerima'] . '</td>
        <td>Rp. ' . $a['harga'] . '</td>
        <td>' . $a['quantity'] . '</td>
        <td>Rp. ' . $a['total'] . '</td>
        </tr>';
    $i++;
}
$html .= '

    </tbody>
    </table>
<br /><br />

<div class="row">
    <div class="col-xs-3">Total Keseluruhan : <b class="totalElement">Rp. ' . number_format($d['totalbudget'], 0, '', ',') . '</b></div>
</div>';

// var_dump($html);
$dompdf = new DOMPDF();
$dompdf->load_html($html);
$dompdf->render();
$output = $dompdf->output();
file_put_contents('/var/www/html/dev-budget/document/test.pdf', $output);
// $dompdf->stream("RequestBudget_" . $d['nama'] . ".pdf", array("Attachment" => false));
