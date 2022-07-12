<?php
session_start();

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
require_once("dompdf/dompdf_config.inc.php");
$html = "<html>
<body>";

//$html .= '
//        <br>
//        <table border="1" cellpadding="10" cellspacing="0" style="text-align:center;">
//            <thead>
//                <tr class="warning">
//                    <th rowspan="2" style="width:5%">No Item BPU</th>
//                    <th rowspan="2">Tanggal Jatuh Tempo</th>
//                    <th rowspan="2">Keterangan</th>
//                    <th rowspan="2">All Budget</th>
//                    <th colspan="2">Bank BCA (PALL)</th>
//                    <th colspan="2">Bank Mandiri (KAS)</th>
//                </tr>
//                <tr class="warning">
//                    <th>Term 1</th>
//                    <th>Term 2</th>
//                    <th>Term 1</th>
//                    <th>Term 2</th>
//                </tr>
//            </thead>
//            <tbody>';

$html .= '
<footer> Data ini dibuat melalui Aplikasi Budget dan telah disetujui oleh pada </footer>
</body>
</html';
$dompdf = new DOMPDF();
$dompdf->load_html($html);
$dompdf->render();
$dompdf->stream("Nama File.pdf", array("Attachment" => false));
