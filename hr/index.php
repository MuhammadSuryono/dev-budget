<?php
session_start();
date_default_timezone_set('Asia/Jakarta');
$time_start = micro_time();
function micro_time()
{
	$temp = explode(" ", microtime());
	return bcadd($temp[0], $temp[1], 6);
}

include("../db.php");
$page = $_GET['p'];

$r = mysql_query("select * from page where pageid='$page'");
$title = mysql_result($r, 0, 'desc');
$filephp = mysql_result($r, 0, 'file');
function cekid($str)
{
	$value['rows'] = '0';
	if ($str != '') {
		$r = mysql_query("select * from user where noid='$str'");
		if (mysql_num_rows($r) == 1) {
			$value['rows'] = 1;
			$value['name'] = mysql_result($r, 0, 'name');
			$value['divisi'] = mysql_result($r, 0, 'divisi');
			$value['grant'] = mysql_result($r, 0, 'grant');
		}
	}
	return $value;
}

$userid = cekid($_SESSION['id']);

// FUNGSI LOGOUT
if ($_GET['a'] == 'logout' && $_SESSION['id'] != '') {
	session_destroy();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $title; ?></title>
	<link rel="stylesheet" href="../styles.css" />
	<script type="text/javascript" language="javascript" src="../js/jquery.js"></script>
</head>

<body>

	<div id="container">
		<div id="header">
			<!-- Header start -->
			<a href="../index.php"><img src="../img/mri.png" height="64" title="MRI System Home" /></a>
			<!-- Header end -->
		</div>
		<div id="header_title">
			<u>M</u>arketing <u>R</u>esearch <u>I</u>ndonesia
		</div>

		<?php
		$splitted_string = explode("-", $title);
		$pgnav = trim($splitted_string[1]);
		?>
		<div id="breadcrumb">
			<table width="100%">
				<tr>
					<td align="left"><?php echo "<strong>Your Position :</strong> <a href='../index.php' style='text-decoration:none; color:#ffffff'> <u>Home</u></a> » " . $pgnav; ?></td>
					<td align="right"><?php echo $userid['name'] . " [ " . $userid['divisi'] . " ] &nbsp;&nbsp;&nbsp; <a href='../index.php?a=logout' onclick=\"return confirm('Logout Sistem MRI?');\" style=' color:#FFF'><img src='../img/shut_down.png' style='vertical-align:text-bottom' /><strong>&nbsp;Logout&nbsp;</strong></a>"; ?></td>
				</tr>
			</table>
		</div>

		<div id="content">
			<!-- Body start -->
			<?php include $filephp; ?>
			<!-- Body end -->
		</div>
	</div>
	<!-- <script type="text/javascript" src="../js/jquery-1.11.1.min.js"></script> -->
	<script type="text/javascript" src="chosen/chosen.jquery.min.js"></script>
	<link href="chosen/chosen.min.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript">
		var config = {
			'.chosen-select': {},
			'.chosen-select-deselect': {
				allow_single_deselect: true
			},
			'.chosen-select-no-single': {
				disable_search_threshold: 10
			},
			'.chosen-select-no-results': {
				no_results_text: 'Oops, nothing found!'
			},
			'.chosen-select-width': {
				width: "95%"
			}
		}
		for (var selector in config) {
			$(selector).chosen(config[selector]);
		}
		jQuery(document).ready(function() {
			jQuery(".chosen").data("placeholder", "Select Frameworks...").chosen();
		});
	</script>

</body>

</html>