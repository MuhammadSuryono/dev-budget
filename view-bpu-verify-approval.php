<?php
error_reporting(0);
session_start();

require "application/config/database.php";
require_once "application/config/whatsapp.php";
require_once "application/config/message.php";
require_once "application/config/helper.php";

$helper = new Helper();

$con = new Database();
$koneksi = $con->connect();

$helperMessage = new Message();
$whatsapp = new Whastapp();

$url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$url = explode('/', $url);
$url = $url[0] . '/' . $url[1] . '/' . 'login.php';

$idUser = $_SESSION['id_user'];
$queryUser = mysqli_query($koneksi, "SELECT email, e_sign, phone_number FROM tb_user WHERE id_user = '$idUser'");
$user = mysqli_fetch_assoc($queryUser);
$emailUser = $user['email'];
$signUser = $user['e_sign'];
$phoneNumber = $user['phone_number'];

$queryRekening = mysqli_query($koneksiDevelop, "SELECT * FROM kas WHERE stat = 'MRI'");

$querySetting = mysqli_query($koneksi, "SELECT * FROM setting_budget WHERE keterangan = 'approval_bpu'") or die(mysqli_error($koneksi));
$setting = mysqli_fetch_assoc($querySetting);

$oneWeek = date('Y-m-d', strtotime('+7 days'));

$email = [];
$queryEmailFinance = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi='FINANCE' AND aktif='Y' AND status_penerima_email_id IN ('2', '3')");
while ($item = mysqli_fetch_assoc($queryEmailFinance)) {
  array_push($email, $item['phone_number']);
}

$queryReminderPembayaran = mysqli_query($koneksi, "SELECT a.*, b.nama AS nama_project, c.rincian FROM reminder_tanggal_bayar a JOIN pengajuan b ON b.waktu = a.selesai_waktu JOIN selesai c ON c.waktu = a.selesai_waktu AND c.no = a.selesai_no WHERE a.tanggal <= '$oneWeek' AND (has_send_email = 0 OR has_send_email IS NULL)");
while ($item = mysqli_fetch_assoc($queryReminderPembayaran)) {


  if (count($email) > 0) {
    foreach($email  as $phone) {
      $whatsapp->sendMessage($phone, $helperMessage->messageReminderPembayaran($item['nama_project'], $item['rincian'], date('d-m-Y', strtotime($item['tanggal'])), $url));
    }
  }

  mysqli_query($koneksi, "UPDATE reminder_tanggal_bayar SET has_send_email = 1 WHERE id = $item[id]");
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
          <li><a href="saldobpu.php">Saldo BPU</a></li>
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
          <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">Transfer
              <span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li><a href="laporan-transfer.php">Laporan Transfer</a></li>
              <li><a href="antrian-transfer.php">Antrian Transfer</a></li>
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

      
       <ul class="nav navbar-nav navbar-right">
                        <li><a href="/log-notifikasi-aplikasi/index.html" target="_blank"><i class="fa fa-envelope"></i></a></li>
          <li><a href="ubahpassword.php"><span class="glyphicon glyphicon-user"></span><?php echo $_SESSION['nama_user']; ?> (<?php echo $_SESSION['divisi']; ?>)</a></li>
          <li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container">
    <div id="alert-error-already-verify"></div>
    <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
      <div class="panel-body no-padding">
        <div class="row">
          <div class="col-lg-4">
            <h5><i class="fa fa-check"></i><b> APPROVAL DATA BPU</b></h5>
            <hr/>
            <form>
              <div class="form-group">
                <label>Verifikasi Nominal:</label>
                <input type="number" class="form-control" min="100" id="nominal-verify" required/>
              </div>
              <div class="form-group">
                <label>File Pendukung:</label>
                <input type="file" class="form-control" accept="image/*" id="inputImage" required/>
              </div>
            </form>
            <button class="btn btn-success btn-flat btn-block" id="btn-approve" onclick="approve(1)"><i class="fa fa-check"></i> Setujui</button>
            <button class="btn btn-danger btn-flat btn-block" id="btn-disaprove" onclick="approve(0)"><i class="fa fa-times"></i> Ditolak</button>
          </div>
          <div class="col-lg-8 text-center">
            <img id="content-image" style="max-width: 720px;" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTMGwPo04v2vaxbXlOkSuBK1aDQs1ntPnFM9_5P7BhEULVguY4tv4EZMuF88SaA7HZ8a1o&usqp=CAU" />
          </div>
        </div>
      </div>
    </div>
  </div>


  <script type="text/javascript">
  const notifError = document.getElementById('notification-error-nominal')
  const notifErrorForm = document.getElementById('notification-error-form')
  const inputNominalVerify = document.getElementById('nominal-verify')
  const notifAlreadyVerify = document.getElementById('alert-error-already-verify')
  $(document).ready(function() {
    let idBpuVerify = getParameterByName('id')
    let idBpu = getParameterByName('bpu')

    $('#inputImage').change(function() {
      readURLSign(this);
    })

  });
  
  function approve(val) {
    let idBpuVerify = getParameterByName('id')
    let idBpu = getParameterByName('bpu')
    let stateNominal = window.localStorage.getItem('stateNominal');
    httpRequestGet(`/ajax/ajax-bpu-need-verify.php?action=get-data-single&id=${id}&approval=${val}`).then((res) => {
      if (res.is_success) {
        window.location.reload()
      }
    })
  }

  function readURLSign(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      
      reader.onload = function(e) {
        $('#content-image').attr('src', e.target.result);
      }
      
      reader.readAsDataURL(input.files[0]); // convert to base64 string
    }
  }

  function getParameterByName(name, url = window.location.href) {
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
	}
    
  function httpRequestGet(url) {
    return fetch(url)
    .then((response) => response.json())
    .then(data => data);
  }

  function alertError(type = "success", message = "") {
    return `<div class="alert alert-${type}" role="alert">
            ${message}
          </div>`
  }
  
  </script>

</body>

</html>