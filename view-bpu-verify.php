<?php
error_reporting(0);
session_start();

require "application/config/database.php";
require_once "application/config/whatsapp.php";
require_once "application/config/message.php";
require_once "application/config/helper.php";

$helper = new Helper(true);

$con = new Database();
$koneksi = $con->connect();

$helperMessage = new Message();
$whatsapp = new Whastapp();

$idVerify = $_GET['id'];
$idBpu = $_GET['bpu'];

$query = mysqli_query($koneksi, "SELECT * FROM bpu_verify WHERE id = '$idVerify'");
$dataVerify = [];
while ($row = mysqli_fetch_assoc($query)) {
    $dataVerify = $row;
}

$query = mysqli_query($koneksi, "SELECT * FROM bpu WHERE noid = '$idBpu'");
$dataBpu = [];
while ($row = mysqli_fetch_assoc($query)) {
    $dataBpu = $row;
}

$url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$port = $_SERVER['SERVER_PORT'];
$url = explode('/', $url);
$hostProtocol = $url[0];

if ($port != "") {
$hostProtocol = $hostProtocol . ":" . $port;
}

$host = $hostProtocol;
if ($port == "" || $port == "80" || $port == "7793") {
  $host = $hostProtocol. '/'. $url[1];
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
  <style>
    input[list]:focus {
      outline: none;
    }
    input[list] + div[list] {
      display: none;
      position: absolute;
      width: 100%;
      max-height: 164px;
      overflow-y: auto;
      max-width: 330px;
      background: #FFF;
      border: var(--border);
      border-top: none;
      border-radius: 0 0 5px 5px;
      box-shadow: 0 3px 3px -3px #333;
      z-index: 100;
    }
    input[list] + div[list] span {
      display: block;
      padding: 7px 5px 7px 20px;
      color: #069;
      text-decoration: none;
      cursor: pointer;
    }
    input[list] + div[list] span:not(:last-child) {
      border-bottom: 1px solid #EEE;
    }
    input[list] + div[list] span:hover {
      background: rgba(100, 120, 140, .2);
    }
  </style>

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
                        <li><a href="notif-page.php"><i class="fa fa-envelope"></i></a></li>
          <li><a href="ubahpassword.php"><span class="glyphicon glyphicon-user"></span><?php echo $_SESSION['nama_user']; ?> (<?php echo $_SESSION['divisi']; ?>)</a></li>
          <li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container-fluid">
    <div id="alert-error-already-verify">
      <?php if (isset($_GET['status']) || $dataVerify["is_verified"]) {
        if ($_GET['status'] == "success" && $dataVerify["is_need_approved"]) {
          echo '<div class="alert alert-success" role="alert">
            Data berhasil disimpan dan menunggu persetujuan oleh Manager Finance
          </div>';
        }

        if ($dataVerify["is_approved"] && !$dataVerify["is_need_approved"] && $dataVerify["is_verified"] && !$dataVerify["status_approved"]) {
          echo '<div class="alert alert-danger" role="alert">
            Data bpu DITOLAK oleh '.$dataBpu["approveby"].'
          </div>';
        }

        if ($dataVerify["is_approved"] && !$dataVerify["is_need_approved"] && $dataVerify["is_verified"] && $dataVerify["status_approved"]) {
          echo '<div class="alert alert-success" role="alert">
            Data bpu DI PERIKSA oleh '.$dataBpu["checkby"].'
          </div>';
        }
      } ?>
    </div>
    <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
      <div class="panel-body no-padding">
        <div class="row">
          <div class="col-lg-6">
            <h5><i class="fa fa-check"></i><b> VERIFY DATA BPU</b></h5>
            <hr/>
            <div id="notification-error-nominal"></div>
            <div id="notification-error-form"></div>
            <form>
              <div class="form-group">
                <label>Verifikasi Nominal:</label>
                <input type="number" class="form-control" min="100" id="nominal-verify" value="<?= isset($dataVerify["total_verify"]) ? $dataVerify["total_verify"]:"" ?>" <?= $dataVerify["is_verified"] ? "disabled":"" ?> required/>
              </div>
              <?php 
              if (isset($dataVerify["is_verified"])) {
                if (!$dataVerify["is_verified"]) {
                  echo '<div class="form-group">
                  <label>File Pendukung:</label>
                  <input type="file" class="form-control" accept="image/*" id="inputImage" required/>
                  <small>Maksimal size upload file 100kb. File yang didukung <i>(.png,.jpg,.jpeg,.pdf,.doc,.docx)</i></small>
                </div>';
                }
              }
                
              ?>

            </form>
            <?php 
              if (isset($dataVerify["is_verified"])) {
                if (!$dataVerify["is_verified"]) {
                  echo '<button class="btn btn-primary btn-flat btn-block" id="btn-submit" onclick="submit()"><i class="fa fa-check"></i> Verifikasi</button>';
                }
              }
                
              ?>

            <?php
              if (isset($dataBpu["statusbpu"]) && $dataVerify["is_verified"]) {
                $statusbpu = $dataBpu["statusbpu"];
                $isEksternalNonVendor =  $statusbpu == 'Honor Eksternal' || $statusbpu == 'Honor Area Head' || $statusbpu == 'STKB OPS' || $statusbpu == 'STKB TRK Luar Kota' || $statusbpu == 'Honor Luar Kota' || $statusbpu == 'Honor Jakarta' || $statusbpu == 'STKB TRK Jakarta';
                
                if ($isEksternalNonVendor && !$dataVerify["is_need_approved"] && !$dataVerify["is_approved"]) {
                  include 'form/verify-eksternal-non-vendor.php';
                } else if (!$dataVerify["is_need_approved"] && !$dataVerify["is_approved"]) {
                  include 'form/verify-eksternal-vendor.php';
                }

                if ($dataVerify["is_need_approved"] && $dataVerify["is_verified"] || ($dataVerify["is_verified"] && $dataVerify["is_approved"])) {
                  include 'form/verify-eksternal-info.php';
                }
              }
            ?>
            
          </div>
          <div class="col-lg-6 text-center">
          <iframe id="inlineFrameExample" id="content-image"
              title="Inline Frame Example"
              width="900"
              height="800"
              src="<?= $dataVerify['document'] != '' ? 'http://'.$host.'/document/'.$dataVerify['document']:'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTMGwPo04v2vaxbXlOkSuBK1aDQs1ntPnFM9_5P7BhEULVguY4tv4EZMuF88SaA7HZ8a1o&usqp=CAU' ?>">
          </iframe>
            <!-- <img id="content-image" style="max-width: 720px;" src="<?= $dataVerify['document'] != '' ? 'fileupload/'.$dataVerify['document']:'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTMGwPo04v2vaxbXlOkSuBK1aDQs1ntPnFM9_5P7BhEULVguY4tv4EZMuF88SaA7HZ8a1o&usqp=CAU' ?>" /> -->
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
    
    getDataBpu(idBpuVerify, idBpu);
    
    $('#nominal-verify').change((data) => {
      let value = data.target.value
      let stateNominal = window.localStorage.getItem('stateNominal');

      if (value.length > 3 && value !== stateNominal) {
        notifError.innerHTML = alertError("danger", "<i class='fa fa-times'></i> Nominal yang anda masukkan tidak sessuai")
      } else if (value.length > 3 && value === stateNominal) {
        notifError.innerHTML = alertError("success", "<i class='fa fa-check'></i> Nominal yang anda masukkan sessuai")
      }
    })

    $('#inputImage').change(function() {
      readURLSign(this);
    })

  });
  
  function submit() {
    let idBpuVerify = getParameterByName('id')
    let idBpu = getParameterByName('bpu')
    let stateNominal = window.localStorage.getItem('stateNominal');
    const nominal = $('#nominal-verify').val()
    const input = document.getElementById('inputImage');
		let file = input.files[0];
    
    if ((file === undefined || nominal.length < 3) || stateNominal !== nominal) {
      notifErrorForm.innerHTML = alertError("danger", "Form tidak boleh kosong")
      
      setTimeout(() => {
        notifErrorForm.innerHTML = ""
      }, 3000)
    } else {
      document.getElementById("btn-submit").disabled = true;
      document.getElementById('btn-submit').value = "Memverifikasi"
      uploadFile(file, nominal, idBpuVerify, idBpu).then((res) => {
        window.location.reload()
      })
    }
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

  function getDataBpu(id, idBpu) {
    httpRequestGet(`ajax/ajax-bpu-need-verify.php?action=get-data-single&id=${id}&id-bpu=${idBpu}`).then((res) => {
      if (res.data.length > 0 && res.data !== null) {
        let data = res.data
        data = data[0]
        window.localStorage.setItem('stateNominal', data.pengajuan_jumlah)

        if (data.is_verified === '1' && data.is_need_approved === '1') {
          // document.getElementById("btn-submit").disabled = true;
          // notifAlreadyVerify.innerHTML = alertError('success', `Data BPU sudah di <b>VERIFIKASI</b> oleh <b>${data.checkby}</b> pada <b>${data.tglcheck}</b> `)
        }
      }
    })
  }

  function alertError(type = "success", message = "") {
    return `<div class="alert alert-${type}" role="alert">
            ${message}
          </div>`
  }

  function uploadFile(file, nominal, id, idBpu) {
		const fd = new FormData();
		fd.append('file', file);
		let url = `ajax/ajax-bpu-need-verify.php?action=simpan-verifikasi&id=${id}&id-bpu=${idBpu}&nominal=${nominal}`

		return fetch(url, {
			method: 'POST',
			body: fd
		})
		.then(res => res.json())
		.then(data => data)
	}
  </script>

</body>

</html>