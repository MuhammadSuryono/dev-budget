<?php

require_once "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
$page = $_GET['page'];

switch ($page) {
	case "1";
		include "tambahbudget.php";
		break;

	case "2";
		include "konfirm.php";
		break;
}
