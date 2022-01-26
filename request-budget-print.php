<?php
session_start();

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
require_once("dompdf/dompdf_config.inc.php");

$id = $_GET['id'];
$select = mysqli_query($koneksi, "SELECT * FROM pengajuan_request WHERE id='$id'");
$d = mysqli_fetch_assoc($select);

$queryTotalBiaya = mysqli_query($koneksi, "SELECT sum(total) as total_biaya FROM selesai_request WHERE id_pengajuan_request = '$id' AND status != 'UM Burek'");
$dataTotalBiaya = mysqli_fetch_assoc($queryTotalBiaya);

$queryTotalBiayaUMBurek = mysqli_query($koneksi, "SELECT sum(total) as total_budget_um_burek FROM selesai_request WHERE id_pengajuan_request = '$id' AND status = 'UM Burek'");
$dataTotalBiayaUMBurek = mysqli_fetch_assoc($queryTotalBiayaUMBurek);

$i = 1;
$sql = mysqli_query($koneksi, "SELECT * FROM selesai_request WHERE id_pengajuan_request = '$id' ORDER BY urutan ASC");

$html = "<html>
<head>
    <style>
        /** 
            Set the margins of the page to 0, so the footer and the header
            can be of the full height and width !
         **/
        @page {
            margin: 0cm 0cm;
        }

        /** Define now the real margins of every page in the PDF **/
        body {
            margin-top: 2cm;
            margin-left: 2cm;
            margin-right: 2cm;
            margin-bottom: 2cm;
        }

        /** Define the header rules **/
        img.header {
            position: fixed;
            top: 10px;
            left:0px;
            margin-left:70px;

            /** Extra personal styles **/
            line-height: 1.5cm;
        }

        /** Define the footer rules **/
        footer {
            position: fixed; 
            bottom: 1cm; 
            left: 1cm; 
            height: 0cm;
            font-size:12px;
        }

        table
        {
        border-collapse:unset;   
        }
    </style>
</head>
<body>
<header>
<img src='images/logomri.png' style='width:150px;text-align:left' alt='' class='header'>
<br>
</header>
<p style='text-align: center;font-size:14px;'>Soho Pancoran, Tower Splendor 19th Floor, Unit 15,16,17 Jl.Let Jend MT Haryono Kav 2-3, Jakarta Selatan, 12810</p>
        <h2 style='text-align: center;'>PENGAJUAN BIAYA</h2>

        <p>Kepada Yth.</p>
        <p>Finance & Accounting</p>

        <br>
        <div class='row'>
            <div class='col-xs-3'>Nama <span style='margin-left: 81px'>: <b> " . $d['pengaju'] . "</span></div>
        </div>
        <div class='row'>
            <div class='col-xs-3'>Divisi/Unit <span style='margin-left: 47px'>: <b> " . $d['divisi'] . "</span></div>
        </div>
        <div class='row'>
            <div class='col-xs-3'>Project <span style='margin-left: 74px'>: <b> " . $d['nama'] . "</span></div>
        </div>
        <div class='row'>
            <div class='col-xs-3'>Keperluan <span style='margin-left: 53px'>: <b> -</div>
        </div>
        <div class='row'>
            <div class='col-xs-3'>Tanggal <span style='margin-left: 63px'> : <b> " . date("F d, Y", strtotime($d['waktu']))  . "</div>
        </div>";

if ($d['submission_note']) {
    $html .= "<div class='row'>
                        <div class='col-xs-3'>Alasan Pengajuan <span style=''> : <b> "  . $d['submission_note'] . "</div>
                    </div>";
}
if ($d['declined_note']) {
    $html .= "<div class='row'>
                        <div class='col-xs-3'>Alasan Penolakan <span style=''> : <b> "  . $d['declined_note'] . "</div>
                    </div>";
}
$html .= " 
        <br>
        
        <table border='1' cellpadding='10' cellspacing='0' style='text-align:center;'>
                        <thead>
                            <tr style='page-break-after: always; width:100%'>
                                <th >No</th>
                                <th>Nama</th>
                                <th>Kota</th>
                                <th>Status</th>
                                <th>Penerima Uang</th>
                                <th>Harga (IDR)</th>
                                <th >Total Quantity</th>
                                <th>Total Harga (IDR)</th>
                            </tr>
                        </thead>

                        <tbody id='data-body'>
                        ";
// var_dump($html);
while ($a = mysqli_fetch_array($sql)) {

    $html .= ' 
        <tr style="page-break-after: always;" >
        <th scope="row">' . $i . '</th>
        <td>' . $a['rincian'] . '</td>
        <td>' . $a['kota'] . '</td>
        <td>' . $a['status'] . '</td>
        <td>' . $a['penerima'] . '</td>
        <td>Rp. ' . number_format($a['harga']) . '</td>
        <td>' . number_format($a['quantity']) . '</td>
        <td>Rp. ' . number_format($a['total']) . '</td>
        </tr>';
    $i++;
}
$html .= '

    </tbody>
    </table>
    </font>
<br /><br />
<div class="row">
                <div class="col-xs-2">Total Biaya : <b class="totalElementBiaya">Rp. ' . number_format($dataTotalBiaya['total_biaya'], 0, '', ',') . '</b></div>
            </div>
            <div class="row">
                <div class="col-xs-2">Total UM Burek : <b class="totalElementBiayaUmBurek"><Rp. ' . number_format($dataTotalBiayaUMBurek['total_budget_um_burek'], 0, '', ',').'</b></div>
            </div>
<div class="row">
    <div class="col-xs-3">Total Keseluruhan : <b class="totalElement">Rp. ' . number_format($dataTotalBiaya['total_biaya'] + $dataTotalBiayaUMBurek['total_budget_um_burek'], 0, '', ',') . '</b></div>
</div>
</body>
</html';

// var_dump($html);
$dompdf = new DOMPDF();
$dompdf->load_html($html);
$dompdf->render();
// $output = $dompdf->output();
// file_put_contents('document/test100.pdf', $output);
$dompdf->stream("RequestBudget_" . $d['nama'] . ".pdf", array("Attachment" => false));
