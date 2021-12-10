<?php
session_start();

function saveDoc($koneksi, $id, $name)
{
    $select = mysqli_query($koneksi, "SELECT * FROM pengajuan_request WHERE id='$id'");
    $d = mysqli_fetch_assoc($select);

    $i = 1;
    $sql = mysqli_query($koneksi, "SELECT * FROM selesai_request WHERE id_pengajuan_request = '$id' ORDER BY urutan ASC");

    $html = "<!DOCTYPE html>
    <html>
    <head>
        <style>
            /** 
                Set the margins of the page to 0, so the footer and the header
                can be of the full height and width !
             **/
            @page {
                margin: 0cm 0cm;
            }

            table {
                border-collapse: collapse;
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
                                <tr class='warning'style='page-break-after: always;'>
                                <th>No</th>
                                <th style='width:10%'>Nama</th>
                                <th>Kota</th>
                                <th style='width:1%'>Status</th>
                                <th style='width:1%'>Penerima Uang</th>
                                <th style='width:15%'>Harga (IDR)</th>
                                <th>Total Quantity</th>
                                <th style='width:25%'>Total Harga (IDR)</th>
                                </tr>
                            </thead>
    
                            <tbody id='data-body'>
                            ";
    while ($a = mysqli_fetch_array($sql)) {

        $html .= ' 
            <tr>
            <th scope="row">' . $i . '</th>
            <td>' . $a['rincian'] . '</td>
            <td>' . $a['kota'] . '</td>
            <td>' . $a['status'] . '</td>
            <td>' . $a['penerima'] . '</td>
            <td>Rp. ' . number_format((int)$a['harga']) . '</td>
            <td>' . number_format((int)$a['quantity']) . '</td>
            <td>Rp. ' . number_format((int)$a['total']) . '</td>
            </tr>';
        $i++;
    }
    $html .= '
    
        </tbody>
        </table>
        </font>
    <br /><br />
    
    <div class="row">
        <div class="col-xs-3">Total Keseluruhan : <b class="totalElement">Rp. ' . number_format($d['totalbudget'], 0, '', ',') . '</b></div>
    </div>
    </body>
    </html>';

    $dompdf = new DOMPDF();
    $dompdf->load_html($html);
    $dompdf->render();
    $output = $dompdf->output();
    file_put_contents("document/$name.pdf", $output);
}

function saveDocApproved($koneksi, $id, $name)
{
    $select = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE noid='$id'");
    $d = mysqli_fetch_assoc($select);
    $waktu = $d['waktu'];

    $budget = ($d['totalbudget']) ? number_format($d['totalbudget'], 0, '', ',') : '0';

    $i = 1;
    $sql = mysqli_query($koneksi, "SELECT * FROM selesai WHERE waktu = '$waktu'");
    $html = "<!DOCTYPE html>
    <html>
<head>
    <style>
        /** 
            Set the margins of the page to 0, so the footer and the header
            can be of the full height and width !
         **/
        @page {
            margin: 0cm 0cm;
        }

        table {
            border-collapse: collapse;
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
            <div class='col-xs-3'>Nama <span style='margin-left: 40px'>: <b> " . $d['pengaju'] . "</span></div>
        </div>
        <div class='row'>
            <div class='col-xs-3'>Divisi/Unit <span style='margin-left: 6px'>: <b> " . $d['divisi'] . "</span></div>
        </div>
        <div class='row'>
            <div class='col-xs-3'>Project <span style='margin-left: 33px'>: <b> " . $d['nama'] . "</span></div>
        </div>
        <div class='row'>
            <div class='col-xs-3'>Keperluan <span style='margin-left: 12px'>: <b> -</div>
        </div>
        <div class='row'>
            <div class='col-xs-3'>Tanggal <span style='margin-left: 22px'> : <b> " . date("F d, Y", strtotime($d['waktu']))  . "</div>
        </div>
        <br>
        
        <table border='1' cellpadding='10' cellspacing='0' style='text-align:center;'>
                        <thead>
                            <tr class='warning'style='page-break-after: always;'>
                            <th>No</th>
                            <th style='width:10%'>Nama</th>
                            <th>Kota</th>
                            <th style='width:1%'>Status</th>
                            <th style='width:1%'>Penerima Uang</th>
                            <th style='width:15%'>Harga (IDR)</th>
                            <th>Total Quantity</th>
                            <th style='width:25%'>Total Harga (IDR)</th>
                            </tr>
                        </thead>

                        <tbody id='data-body'>
                        ";
    $checkName = [];
    while ($a = mysqli_fetch_array($sql)) {

        if (!in_array($a["rincian"], $checkName)) {

            $html .= ' 
            <tr>
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
            array_push($checkName, $a['rincian']);
        }
    }
    $html .= '

    </tbody>
    </table>
    </font>
<br /><br />

<div class="row">
    <div class="col-xs-3">Total Keseluruhan : <b class="totalElement">Rp. ' . $budget . '</b></div>
</div>
<footer> Data ini dibuat melalui Aplikasi Budget dan telah disetujui oleh ' . $d['penyetuju'] . ' pada ' . $d['date_approved'] . '</footer>
</body>
</html>';

    $dompdf = new DOMPDF();
    $dompdf->load_html($html);
    $dompdf->render();
    $output = $dompdf->output();
    file_put_contents("document/$name.pdf", $output);
}

function saveDocBudget($koneksi, $id, $name)
{
    $select = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE noid='$id'");
    $d = mysqli_fetch_assoc($select);
    $waktu = $d['waktu'];

    $budget = ($d['totalbudget']) ? number_format($d['totalbudget'], 0, '', ',') : '0';

    $i = 1;
    $sql = mysqli_query($koneksi, "SELECT * FROM selesai WHERE waktu = '$waktu'");
    $html = "<!DOCTYPE html>
    <html>
<head>
    <style>
        /** 
            Set the margins of the page to 0, so the footer and the header
            can be of the full height and width !
         **/
        @page {
            margin: 0cm 0cm;
        }

        table {
            border-collapse: collapse;
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
    </style>
</head>
<body>
<header>
<img src='images/logomri.png' style='width:150px;text-align:left' alt='' class='header'>
<br>
</header>
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

    $html .= "<br>
    
        <table border='1' cellpadding='10' cellspacing='0' style='text-align:center;'>
                        <thead>
                            <tr class='warning'style='page-break-after: always;'>
                            <th>No</th>
                            <th style='width:10%'>Nama</th>
                            <th>Kota</th>
                            <th style='width:1%'>Status</th>
                            <th style='width:1%'>Penerima Uang</th>
                            <th style='width:15%'>Harga (IDR)</th>
                            <th>Total Quantity</th>
                            <th style='width:25%'>Total Harga (IDR)</th>
                            </tr>
                        </thead>

                        <tbody id='data-body'>
                        ";
    $checkName = [];
    while ($a = mysqli_fetch_array($sql)) {

        if (!in_array($a["rincian"], $checkName)) {

            $html .= ' 
            <tr>
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
            array_push($checkName, $a['rincian']);
        }
    }
    $html .= '

    </tbody>
    </table>
    </font>
<br /><br />

<div class="row">
    <div class="col-xs-3">Total Keseluruhan : <b class="totalElement">Rp. ' . $budget . '</b></div>
</div>
</body>
</html';

    $dompdf = new DOMPDF();
    $dompdf->load_html($html);
    $dompdf->render();
    $output = $dompdf->output();
    file_put_contents("document/$name.pdf", $output);
}
