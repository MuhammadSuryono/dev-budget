<?php

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
$page=$_GET['page'];

switch($page)

{
	case "1";
	include "isicashflow.php";
	break;
}
