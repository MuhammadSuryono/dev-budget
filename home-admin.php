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

$idUser = $_SESSION['id_user'];
$queryUser = mysqli_query($koneksi, "SELECT email FROM tb_user WHERE id_user = '$idUser'");
$emailUser = mysqli_fetch_row($queryUser)[0];
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
        <a class="navbar-brand" href="home-admin.php">Budget-Ing</a>
      </div>
      <div class="collapse navbar-collapse" id="myNavbar">
        <ul class="nav navbar-nav">
          <li class="active"><a href="home-admin.php">Home</a></li>
          <li><a href="list-all.php">List</a></li>
          <!-- <li><a href="history.php">History</a></li> -->
        </ul>
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

    <a href="home.php?page=1"><button type="button" class="btn btn-primary">Tambah Baru</button></a>

    <br /><br />


    <?php

    include "isi.php";

    ?>

  </div>

  <div class="modal fade" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          Pendaftaran Email
        </div>
        <div class="modal-body">
          <p>Silahkan masukkan email anda untuk melengkapi data diri anda</p>
          <input type="email" class="form-control" id="email" name="email" value="" autocomplete="off" required>
        </div>
        <div class="modal-footer">
          <button type="submit" id="buttonSubmitEmail" class="btn btn-success success">Submit</button>
        </div>
      </div>
    </div>
  </div>

</body>

<script>
  const emailUser = <?= json_encode($emailUser); ?>;
  const idUser = <?= json_encode($idUser); ?>;

  $(document).ready(function() {
    if (emailUser == null) {
      $('#emailModal').modal({
        backdrop: 'static',
        keyboard: false
      });
    }
    $('#buttonSubmitEmail').click(function() {
      const email = $('#email').val();
      if (!email) {
        alert('Masukkan Email Anda');
      } else {
        $.ajax({
          url: "pendaftaran-email.php",
          type: "post",
          data: {
            email: email,
            id: idUser
          },
          success: function(result) {
            if (result == true) {
              alert('Pendaftaran Email Berhasil');
              $('#emailModal').modal('hide');
            } else {
              alert('Pendaftaran Email Gagal, ' + result);
            }
          }
        })
      }
    })
  })
</script>

</html>