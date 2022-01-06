<?php
//error_reporting(0);
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
        <?php
        if ($_SESSION['divisi'] == "FINANCE") :
        ?>
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="home-finance.php">Budget-Ing</a>
      </div>
      <div class="collapse navbar-collapse" id="myNavbar">
        <ul class="nav navbar-nav">
          <li class="active"><a href="home-finance.php">Home</a></li>

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
          <li><a href="history-finance.php">History</a></li>
          <li><a href="list.php">Personal</a></li>
          <li><a href="summary-finance.php">Summary</a></li>
          <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">Rekap
              <span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li><a href="rekap-finance.php">Ready To Paid</a></li>
              <li><a href="belumrealisasi.php">Belum Realisasi</a></li>
              <li><a href="cashflow.php">Cash Flow</a></li>
            </ul>
          </li>
          <?php
          if ($_SESSION['hak_page'] == 'Suci') {
            echo "<li><a href='rekap-project.php'>Rekap Project</a></li>";
          } else {
            echo "";
          }
          ?>
        </ul>
      <?php elseif ($_SESSION['divisi'] == 'Direksi') : ?>
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="home-direksi.php">Budget-Ing</a>
      </div>
      <div class="collapse navbar-collapse" id="myNavbar">
        <ul class="nav navbar-nav">
          <li class="active"><a href="home-direksi.php">Home</a></li>
          <li><a href="list-direksi.php">List</a></li>
          <li><a href="saldobpu.php">Saldo BPU</a></li>
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
          <!-- <li><a href="history-direksi.php">History</a></li> -->
        </ul>
      <?php else : ?>
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="home.php">Budget-Ing</a>
      </div>
      <div class="collapse navbar-collapse" id="myNavbar">
        <ul class="nav navbar-nav">
          <li><a href="home.php">Home</a></li>
          <li class="active"><a href="list.php">List</a></li>
          <!-- <li class="active"><a href="request-budget.php">Request Budget</a></li> -->
        </ul>
      <?php endif; ?>

      <?php
      $pengaju = $_SESSION['nama_user'];
      $cari = mysqli_query($koneksi, "SELECT * FROM bpu WHERE pengaju ='$pengaju' AND persetujuan ='Belum Disetujui' OR pengaju ='$pengaju' AND persetujuan ='Pending'");
      $belbyr = mysqli_num_rows($cari);
      ?>
     <ul class="nav navbar-nav navbar-right">
                        <li><a href="/log-notifikasi-aplikasi/index.html" target="_blank"><i class="fa fa-envelope"></i></a></li>
        <li class="dropdown messages-menu">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-inbox"></i><span class="label label-warning"><?= $belbyr ?></span></a>
          <ul class="dropdown-menu">
            <?php
            while ($wkt = mysqli_fetch_array($cari)) {
              $wktulang = $wkt['waktu'];
              $selectnoid = mysqli_query($koneksi, "SELECT * FROM pengajuan WHERE waktu='$wktulang'");
              $noid = mysqli_fetch_assoc($selectnoid);
              $kode = $noid['noid'];
              $project = $noid['nama'];
            ?>
              <li class="header"><a href="view-finance.php?code=<?= $kode ?>">Project <b><?= $project ?></b> BPU Belum Dibayar</a></li>
            <?php
            }
            ?>
          </ul>
        </li>
       <ul class="nav navbar-nav navbar-right">
                        <li><a href="/log-notifikasi-aplikasi/index.html" target="_blank"><i class="fa fa-envelope"></i></a></li>
          <li><a href="ubahpassword.php"><span class="glyphicon glyphicon-user"></span><?php echo $_SESSION['nama_user']; ?> (<?php echo $_SESSION['divisi']; ?>)</a></li>
          <li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <br /><br />

  <div class="container">

    <form action="ubahpasswordproses.php" class="form-horizontal" method="POST" enctype="multipart/form-data">
      <div class="box-body">

        <input type="hidden" name="id_user" value="<?php echo $_SESSION['id_user']; ?>">

        <div class="form-group">
          <label class="col-sm-3 control-label">Password Lama <font color="red">*</font></label>
          <div class="col-sm-4">
            <input type="password" name="passwordlama" class="form-control" required>
          </div>
        </div>

        <div class="form-group">
          <label class="col-sm-3 control-label">Password Baru <font color="red">*</font></label>
          <div class="col-sm-4">
            <input type="password" name="passwordbaru" id="password" class="form-control" required>
          </div>
        </div>

        <div class="form-group">
          <label class="col-sm-3 control-label">ulangi Password Baru <font color="red">*</font></label>
          <div class="col-sm-4">
            <input type="password" id="confirm_password" class="form-control" required>
          </div>
        </div>

        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-7">
            <button type="submit" name="save" value="save" class="btn btn-danger">Submit</button>
          </div>
        </div>
      </div>
    </form>


  </div>

</body>

</html>