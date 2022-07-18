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
        @font-face {
          font-family: 'Open Sans';
          font-style: normal;
          font-weight: normal;
          src: url(http://themes.googleusercontent.com/static/fonts/opensans/v8/cJZKeOuBrn4kERxqtaUH3aCWcynf_cDxXwCLxiixG1c.ttf) format('truetype');
        }
    </style>
</head>
<body>";

$html .= '
       <br>
       <h3><center>FORM PENGAJUAN MRI PALL & MRI KAS (NON PROJECT)</center></h3>
       <table border="1" cellpadding="10" width="100%" cellspacing="0" style="width=100%;">
           <thead style="text-align:center; font-size: 9pt; text-transform: uppercase">
               <tr class="warning">
                   <th  style="width:5%">No Item</th>
                   <th >Tanggal Jatuh Tempo</th>
                   <th >Keterangan</th>
                   <th >All Budget</th>';

foreach($dd as $d) {
    $type_kas = 'KAS';
    if ($d['type_kas'] == 'mri-pall') { $type_kas = 'PALL'; }

    $bank = 'Bank Mandiri';
    if ($d['bank'] == 'CENAIDJA') { $bank = 'Bank BCA'; }

    $html .=      '<th>'.$bank.' ('.$type_kas.')<br>'.$d['rekening'].'</th>';
}
$html .=       '</tr>
           </thead>
           <tbody style="font-size: 80%;">
           	';
while ($a = mysqli_fetch_array($select)) {


    $html .= '<tr style="font-size: 8pt">
           			<td>'.$a['item_id'].'</td>
           			<td style="text-align: center">'.date('d M Y', strtotime($a['jatuh_tempo'])).'</td>
           			<td>'.$a['rincianItem'].'</td>
           			<td>Rp. '.number_format($a['totalBudget'], 0, ',', '.').'</td>';

    foreach($dd as $d) {
        ${"term1" . $d['rekening']} = 0;

        if ($a['id_rekening'] == $d['id_kas']) {
            ${"term1" . $d['rekening']} = $a['total_pengajuan'];
        }
        $html .=	'<td>Rp. '.number_format(${"term1" . $d['rekening']}, 0, ',', '.').'</td>';

        ${"totalterm1" . $d['rekening']} += ${"term1" . $d['rekening']};
        ${"totalterm2" . $d['rekening']} += ${"term2" . $d['rekening']};

    }
    $html .=	'</tr>';


}

$html .= '<tr style="font-weight: bold">
 				<td colspan="4" style="text-align: center">Total</td>';
foreach($dd as $d) {

    $html .= '<td>Rp. '.number_format(${"totalterm1" . $d['rekening']}, 0, ',', '.').'</td>';
}
$html .= '</tr>
 			';
$html .='</tbody>
           </table>';

$html .='	<table style="font-size: 80%; width: 100%; margin-top: 20px; text-align: center;">
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
					<td style="height: 100px;"><img src="uploads/sign/'.$ttd['ttd_created'].'" width="100px" alt="cerated"></td>
					<td><img src="uploads/sign/'.$ttd['ttd_checker1'].'" width="100px" alt="check1"></td>
					<td><img src="uploads/sign/'.$ttd['ttd_checker2'].'" width="100px" alt="check2"></td>
					<td></td>
					<td><img src="uploads/sign/'.$ttd['ttd_approval'].'" width="100px" alt="approved"></td>

				</tr>
				<tr>
					<td><u>'.$ttd['created_by'].'</u></td>
					<td><u>'.$ttd['checker_1'].'</u></td>
					<td><u>'.$ttd['checker_2'].'</u></td>
					<td></td>
					<td><u>'.$ttd['approval_by'].'</u></td>
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
    return "<b >" . $hari_ini . "</b>";
}
