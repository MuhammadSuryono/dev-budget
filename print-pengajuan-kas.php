<?php
session_start();

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

$con->set_name_db(DB_MRI_TRANSFER);
$con->init_connection();
$koneksiTransfer = $con->connect();

$con->set_name_db(DB_DEVELOP);
$con->init_connection();
$koneksiDevelop = $con->connect();

require_once("dompdf/dompdf_config.inc.php");

$code = $_GET['code'];
$select = mysqli_query($koneksi,
    "SELECT p.*, s.rincian as rincianItem, s.total as totalBudget, s.no as noItem FROM pengajuan_kas_item p 
    JOIN selesai s ON s.id = p.item_id WHERE id_pengajuan_budget='$code' and term = '$_GET[term]'");

$db = mysqli_query($koneksi, "SELECT b.id_rekening, a.rekening, a.bank, a.type_kas, a.id_kas
								FROM `pengajuan_kas_item` b
								JOIN develop.kas a ON a.id_kas = b.id_rekening
								WHERE b.id_pengajuan_budget ='$code' and  b.term = '$_GET[term]'
								GROUP BY b.id_rekening");
$dd = array();
while ($d = mysqli_fetch_array($db)) {
    $dd[] = $d;
}

$approve = mysqli_query($koneksi, "SELECT a.*, b.e_sign AS ttd_created, c.e_sign AS ttd_checker1, d.e_sign AS ttd_checker2, e.e_sign AS ttd_approval
								FROM pengajuan_kas a 
								LEFT JOIN tb_user b ON a.created_by=b.nama_user
								LEFT JOIN tb_user c ON a.checker_1=c.nama_user
								LEFT JOIN tb_user d ON a.checker_2=d.nama_user
								LEFT JOIN tb_user e ON a.approval_by=e.nama_user
								WHERE a.id_pengajuan_budget ='$code' and a.term = '$_GET[term]'
								");
$ttd = mysqli_fetch_assoc($approve);


$html = "<html>
<head>
    <style>

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
<body>";

$html .= '
       <br>
       <h3><center>FORM PENGAJUAN MRI PALL & MRI KAS (NON PROJECT)</center></h3>
       <table border="1" cellpadding="10" cellspacing="0" style="width=100%;">
           <thead style="text-align:center; font-size: 100%;">
               <tr class="warning">
                   <th rowspan="2" style="width:5%">No Item</th>
                   <th rowspan="2">Tanggal Jatuh Tempo</th>
                   <th rowspan="2">Keterangan</th>
                   <th rowspan="2">All Budget</th>';

foreach($dd as $d) {
    $type_kas = 'KAS';
    if ($d['type_kas'] == 'mri-pall') { $type_kas = 'PALL'; }

    $bank = 'Bank Mandiri';
    if ($d['bank'] == 'CENAIDJA') { $bank = 'Bank BCA'; }

    $html .=      '<th colspan="2">'.$bank.' ('.$type_kas.')<br>'.$d['rekening'].'</th>';
}
$html .=       '</tr>
               		<tr class="warning">';

foreach($dd as $d) {
    $html .=       '<th>Term 1</th>
                   <th>Term 2</th>';

    ${"totalterm1" . $d['rekening']} = 0;
    ${"totalterm2" . $d['rekening']} = 0;
}
$html .=       '</tr>
           </thead>
           <tbody style="font-size: 80%;">
           	';
while ($a = mysqli_fetch_array($select)) {


    $html .= '<tr>
           			<td>'.$a['item_id'].'</td>
           			<td>'.date('d M Y', strtotime($a['jatuh_tempo'])).'</td>
           			<td>'.$a['rincianItem'].'</td>
           			<td>'.number_format($a['totalBudget'], 0, ',', '.').'</td>';

    foreach($dd as $d) {
        ${"term1" . $d['rekening']} = 0;
        ${"term2" . $d['rekening']} = 0;

        if ($a['id_rekening'] == $d['id_kas'] AND $a['term'] == 1) {
            ${"term1" . $d['rekening']} = $a['total_pengajuan'];
            ${"term2" . $d['rekening']} = 0;
        } else if ($a['id_rekening'] == $d['id_kas'] AND $a['term'] == 2) {
            ${"term1" . $d['rekening']} = 0;
            ${"term2" . $d['rekening']} = $a['total_pengajuan'];
        }
        $html .=	'<td>'.number_format(${"term1" . $d['rekening']}, 0, ',', '.').'</td>
           			<td>'.number_format(${"term2" . $d['rekening']}, 0, ',', '.').'</td>';

        ${"totalterm1" . $d['rekening']} += ${"term1" . $d['rekening']};
        ${"totalterm2" . $d['rekening']} += ${"term2" . $d['rekening']};

    }
    $html .=	'</tr>';


}

$html .= '<tr>
 				<td colspan="4">Total</td>';
foreach($dd as $d) {

    $html .= '<td>'.number_format(${"totalterm1" . $d['rekening']}, 0, ',', '.').'</td>
 				<td>'.number_format(${"totalterm2" . $d['rekening']}, 0, ',', '.').'</td>';
}
$html .= '</tr>
 			';
$html .='</tbody>
           </table>';

$html .='	<table style="font-size: 80%; width: 80%; margin-top: 20px; text-align: center;">
			<tbody>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td>'.hari_ini().", ". date('d F Y').'</td>

				</tr>
				<tr>
					<td>Dibuat oleh,</td>
					<td colspan="2">Dicek oleh,</td>
					<td></td>
					<td>Disetujui oleh,</td>

				</tr>
				<tr>
					<td style="height: 100px;"><img src="uploads/sign/'.$ttd['ttd_created'].'" width="100px"></td>
					<td><img src="uploads/sign/'.$ttd['ttd_checker1'].'" width="100px"></td>
					<td><img src="uploads/sign/'.$ttd['ttd_checker2'].'" width="100px"></td>
					<td></td>
					<td><img src="uploads/sign/'.$ttd['ttd_approval'].'" width="100px"></td>

				</tr>
				<tr>
					<td>'.$ttd['created_by'].'</td>
					<td>'.$ttd['checker_1'].'</td>
					<td>'.$ttd['checker_2'].'</td>
					<td></td>
					<td>'.$ttd['approval_by'].'</td>
				</tr>
			</tbody>
           </table>
           ';

$html .= '
<footer> Data ini dibuat melalui Aplikasi Budget dan telah disetujui oleh pada </footer>
</body>
</html';

// echo $html;
$dompdf = new DOMPDF();
$dompdf->load_html($html);
$dompdf->render();
$dompdf->stream($_GET['code'] . ".pdf", array("Attachment" => false));


function hari_ini(){
    $hari = date ("D");

    switch($hari){
        case 'Sun':
            $hari_ini = "Minggu";
            break;

        case 'Mon':
            $hari_ini = "Senin";
            break;

        case 'Tue':
            $hari_ini = "Selasa";
            break;

        case 'Wed':
            $hari_ini = "Rabu";
            break;

        case 'Thu':
            $hari_ini = "Kamis";
            break;

        case 'Fri':
            $hari_ini = "Jumat";
            break;

        case 'Sat':
            $hari_ini = "Sabtu";
            break;

        default:
            $hari_ini = "Tidak di ketahui";
            break;
    }
    return "<b >" . $hari_ini . "< /b>";
}
