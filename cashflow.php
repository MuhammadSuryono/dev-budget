<?php
error_reporting(0);
session_start();

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

if (!isset($_SESSION['nama_user'])) {
  header("location:login.php");
  // die('location:login.php');//jika belum login jangan lanjut
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>Form Pengajuan Budget</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">

</head>

<body>

  <nav class="navbar navbar-inverse">
    <div class="container-fluid">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <?php if ($_SESSION['hak_akses'] == 'HRD') { ?>
          <a class="navbar-brand" href="home-direksi.php">Budget-Ing</a>
        <?php } else { ?>
          <a class="navbar-brand" href="home-finance.php">Budget-Ing</a>
        <?php } ?>
      </div>
      <div class="collapse navbar-collapse" id="myNavbar">
        <ul class="nav navbar-nav">
          <?php if ($_SESSION['hak_akses'] == 'HRD') { ?>
            <li class="active"><a href="home-direksi.php">Home</a></li>
            <li><a href="list-direksi.php">List</a></li>
            <li><a href="saldobpu.php">Data User</a></li>
            <!--<li><a href="summary.php">Summary</a></li>-->
            <li><a href="listfinish-direksi.php">Budget Finish</a></li>
            <li class="dropdown">
              <a class="dropdown-toggle" data-toggle="dropdown" href="#">Rekap
                <span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="rekap-finance.php">Ready To Paid</a></li>
                <li><a href="belumrealisasi.php">Belum Realisasi</a></li>
                <li><a href="cashflow.php">Cash Flow</a></li>
              </ul>
            </li>
          <?php } else { ?>
            <li><a href="home-finance.php">Home</a></li>
            <?php
            $aksesSes = $_SESSION['hak_akses'];
            if ($aksesSes == 'Fani') {
            ?>
              <li><a href="list-finance-fani.php">List</a></li>
            <?php } else if ($aksesSes == 'Manager') {
            ?>
              <li><a href="list-finance-budewi.php">List</a></li>
            <?php
            } else {
            ?>
              <li><a href="list-finance.php">List</a></li>
            <?php } ?>
            <li><a href="saldobpu.php">Data User</a></li>
            <li><a href="history-finance.php">History</a></li>
            <li><a href="list.php">Personal</a></li>
            <li><a href="summary-finance.php">Summary</a></li>
            <li class="dropdown">
              <a class="dropdown-toggle" data-toggle="dropdown" href="#">Rekap
                <span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="rekap-finance.php">Ready To Paid (MRI Kas)</a></li>
                <li><a href="rekap-finance-mripal.php">Ready To Paid (MRI PAL)</a></li>
                <li><a href="belumrealisasi.php">Belum Realisasi</a></li>
                <li><a href="cashflow.php">Cash Flow</a></li>
              </ul>
            </li>
          <?php } ?>
          <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">Transfer
              <span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li><a href="laporan-transfer.php">Laporan Transfer</a></li>
              <li><a href="antrian-transfer.php">Antrian Transfer</a></li>
            </ul>
          </li>
        </ul>
        <?php if ($_SESSION['hak_akses'] != 'HRD') { ?>
          
          <ul class="nav navbar-nav navbar-right">
            
            <li><a href="#"><span class="glyphicon glyphicon-user"></span><?php echo $_SESSION['nama_user']; ?> (<?php echo $_SESSION['divisi']; ?>)</a></li>
            <li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> Logout</a></li>
          </ul>
        <?php } else {
          
        ?>
          <ul class="nav navbar-nav navbar-right">
            

            <li><a href="#"><span class="glyphicon glyphicon-user"></span><?php echo $_SESSION['nama_user']; ?> (<?php echo $_SESSION['divisi']; ?>)</a></li>
            <li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> Logout</a></li>
          </ul>
        <?php } ?>
      </div>
    </div>
  </nav>


  <div class="container">
    <form class="form-inline" action="cashflow.php?page=1" method="POST">
      <div class="form-group">
        <label for="email">Dari Tanggal :</label>
        <input type="date" class="form-control" name="daritgl">
      </div>
      <div class="form-group">
        <label for="pwd">Sampai :</label>
        <input type="date" class="form-control" name="sampaitgl">
      </div>
      <button type="submit" class="btn btn-success" name="submit">Submit</button>
    </form>
  </div>

  <br /><br />

  <?php
  include "isi4.php";
  ?>

  <script type="text/javascript">
    function edit_budget(no, waktu) {
      // alert(noid+' - '+waktu);
      $.ajax({
        type: 'post',
        url: 'bayarbudget.php',
        data: {
          no: no,
          waktu: waktu
        },
        success: function(data) {
          $('.fetched-data').html(data); //menampilkan data ke dalam modal
          $('#myModal').modal();
        }
      });
    }

    function eksternal_finance(no, waktu) {
      // alert(noid+' - '+waktu);
      $.ajax({
        type: 'post',
        url: 'financeeksternal.php',
        data: {
          no: no,
          waktu: waktu
        },
        success: function(data) {
          $('.fetched-data').html(data); //menampilkan data ke dalam modal
          $('#myModal3').modal();
        }
      });
    }
  </script>

</body>

</html>