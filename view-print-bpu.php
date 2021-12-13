<?php
session_start();

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
require_once("dompdf/dompdf_config.inc.php");

$no = $_GET['no'];
$waktu = $_GET['waktu'];
$term = $_GET['term'];

$queryAllBpu = mysqli_query($koneksi, "SELECT a.*, b.* FROM bpu a LEFT JOIN bank b ON a.namabank = b.kodebank WHERE a.no='$no' AND a.waktu = '$waktu' AND a.term = '$term' group by b.kodebank");

$queryBpu = mysqli_query($koneksi, "SELECT * FROM bpu WHERE no='$no' AND waktu = '$waktu' AND term = '$term'");
$bpu = mysqli_fetch_assoc($queryBpu);

$select = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE waktu='$waktu'");
$d = mysqli_fetch_assoc($select);

$i = 1;
$sql = mysqli_query($koneksi, "SELECT * FROM selesai WHERE waktu = '$waktu' AND no='$no'");
$selesai = mysqli_fetch_assoc($sql);

$queryTtdPengaju = mysqli_query($koneksi, "SELECT e_sign FROM tb_user WHERE nama_user = '$bpu[pengaju]'");
$ttdPengaju = mysqli_fetch_assoc($queryTtdPengaju);

$queryTtdMengetahui = mysqli_query($koneksi, "SELECT e_sign FROM tb_user WHERE nama_user = '$bpu[acknowledged_by]'");
$ttdMengetahui = mysqli_fetch_assoc($queryTtdMengetahui);

$queryTtdCheck = mysqli_query($koneksi, "SELECT e_sign FROM tb_user WHERE nama_user = '$bpu[checkby]'");
$ttdCheck = mysqli_fetch_assoc($queryTtdCheck);

$queryTtdApprove = mysqli_query($koneksi, "SELECT e_sign FROM tb_user WHERE nama_user = '$bpu[approveby]'");
$ttdApprove = mysqli_fetch_assoc($queryTtdApprove);

$i = 1;
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

        .sign{
            width: 100%; 
            text-align:center;
            border-collapse: collapse;
        }

        .sign td{
            width:25%;
        }

        /** Define the footer rules **/
        footer {
            position: fixed; 
            bottom: 1cm; 
            left: 1cm; 
            height: 0cm;
            font-size:12px;
        }

        .centered {
            position: absolute;
            top: 50%;
            left: 50%;
        }
        .container {
            position: relative;
            text-align: center;
            color: white;
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

        <p>Kepada Finance & Accounting</p>

        <br>
        <div class='row'>
            <div class='col-xs-3'>Project <span style='margin-left: 74px'>: <b> " . $d['nama'] . "</span></div>
        </div>
        <div class='row'>
            <div class='col-xs-3'>Keperluan <span style='margin-left: 53px'>: <b> Pengajuan BPU</div>
        </div>
        <div class='row'>
            <div class='col-xs-3'>Tanggal <span style='margin-left: 63px'> : <b> " . date("F d, Y", strtotime($bpu['waktu']))  . "</div>
        </div>
        <br>
        <table border='1' cellpadding='10' cellspacing='0' style='text-align:center'; width='100%';>
            <thead>
                <tr class='warning'>
                    <th>No</th>
                    <th style='text-align:left'>Keterangan</th>
                    <th>Total</th>
                    <th>Nama Penerima</th>
                    <th>Nama Bank</th>
                    <th>No. Rekening</th>
                    <th>Metode Pembayaran</th>
                    <th>No. Voucher</th>
                </tr>
            </thead>

            <tbody id='data-body'>";

while ($item = mysqli_fetch_assoc($queryAllBpu)) :

    $html .= "
                <tr>
                <th scope='row'>" . $i++ . "</th>
                <td style='text-align:left'>" . $selesai['rincian'] . "</td>
                <td> Rp. ";
    if ($bpu['jumlah'] != 0) {
        $html .= number_format($item['jumlah']);
    } else {

        $html .= number_format($item['pengajuan_jumlah']);
    }

    $html .= "</td>
                <td>" . $item['namapenerima'] . "</td>
                <td>" . $item['namabank'] . "</td>
                <td>" . $item['norek'] . "</td>
                <td>" . $item['metode_pembayaran'] . "</td>
                <td>" . $item['novoucher'] . "</td>
                </tr>";

endwhile;
$html .= "
        </tbody>
        </table>
    <br />
    <table class='sign'>
        <tr>
            <th>Mengajukan</th>";
if ($bpu['from_another_app'] != 1) {
    $html .= "<th>Mengetahui</th>
            <th>Memverifikasi</th>
            <th>Menyetujui</th>";
}
$html .= "
        </tr>
        <tr>
            <td style='height:100px;>
           
                <div class='container'>
                    " . (($bpu['pengaju'] != null) ? ' <img style="width:80px; height: 80px;" src="uploads/sign/' . $ttdPengaju['e_sign'] . '" alt="">' : '-') . "
                    <br>
                    </div>
                    " . (($bpu['pengaju'] != null) ? $bpu['created_at'] : '-') . "
            </td>";

if ($bpu['from_another_app'] != 1) {
    $html .= "
            <td style='height:100px;'>
                <div class='container'>
                    " . (($bpu['acknowledged_by'] != null) ? ' <img style="width:80px; height: 80px;" src="uploads/sign/' . $ttdMengetahui['e_sign'] . '" alt="">' : '-') . "
                    <br>
                    </div>
                    " . (($bpu['acknowledged_by'] != null) ? $bpu['tgl_acknowledged'] : '-') . "
            </td>
             <td style='height:100px;'>
                <div class='container'>
                    " . (($bpu['checkby'] != null) ? ' <img style="width:80px; height: 80px;" src="uploads/sign/' . $ttdCheck['e_sign'] . '" alt="">' : '-') . "
                    <br>
                    </div>
                    " . (($bpu['checkby'] != null) ? $bpu['tglcheck'] : '-') . "
            </td>
            <td style='height:100px;'>
            
                <div class='container'>
                    " . (($bpu['approveby'] != null) ? ' <img style="width:80px; height: 80px;" src="uploads/sign/' . $ttdApprove['e_sign'] . '" alt="">' : '-') . "
                    <br>
                    </div>
                    " . (($bpu['approveby'] != null) ? $bpu['tglapprove'] : '-') . "
            </td>";
}
$html .= "</tr>
        <tr>
            <td>(" . (($bpu['pengaju'] != null) ? $bpu['pengaju'] : '-') . ")</td>";
if ($bpu['from_another_app'] != 1) {
    $html .= "<td>(" . (($bpu['acknowledged_by'] != null) ? $bpu['acknowledged_by'] : '-') . ")</td>
            <td>(" . (($bpu['checkby'] != null) ? $bpu['checkby'] : '-') . ")</td>
            <td>(" . (($bpu['approveby'] != null) ? $bpu['approveby'] : '-') . ")</td>";
}
$html .= "
        </tr>
    </table>";

if ($bpu['from_another_app'] != 1) {
    $html .= "<footer> Dokumen ini dibuat melalui Aplikasi Budget</footer>";
} else {
    $html .= "<footer> Dokumen ini dibuat melalui Aplikasi dan Disetujui oleh sistem</footer>";
}
$html .= "
</body>
</html>";

//    <div class='div-sign' style='heigh:100px; border:1px solid black; width:100%;'>
//         <div class='div-sub-sign' style='border:1px solid green; height:100px; display:inline-block;'></div>
//         <div class='div-sub-sign' style='border:1px solid green;'></div>
//         <div class='div-sub-sign' style='border:1px solid green;'></div>
//         <div class='div-sub-sign' style='border:1px solid green;'></div>
//    </div>



// var_dump($html);
// die;
$dompdf = new DOMPDF();
$dompdf->set_paper('letter', 'landscape');
$dompdf->load_html($html);
$dompdf->render();
$dompdf->stream("Budget_" . $d['nama'] . ".pdf", array("Attachment" => false));
