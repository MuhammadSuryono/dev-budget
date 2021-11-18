<?php
// var_dump('test');
session_start();

// include  "../koneksi.php";
// require_once("dompdf_config.inc.php");


function saveDoc($koneksi, $id)
{
    $select = mysqli_query($koneksi, "SELECT * FROM pengajuan_request WHERE id='$id'");
    $id = $_GET['id'];
    $d = mysqli_fetch_assoc($select);

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
                top: 10px
                left:0px;
                margin-left:10px
                height: 2cm;
    
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
    </div>
    </body>
    </html';

    $dompdf = new DOMPDF();
    $dompdf->load_html($html);
    $dompdf->render();
    $output = $dompdf->output();
    file_put_contents('document/asdsadsdaasd.pdf', $output);
    // return 'true';
}
