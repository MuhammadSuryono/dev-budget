<?php
error_reporting(0);
session_start();

require "application/config/database.php";
require_once "application/config/whatsapp.php";
require_once "application/config/message.php";

$con = new Database();
$koneksi = $con->connect();

$helperMessage = new Message();
$whatsapp = new Whastapp();

if (!isset($_SESSION['nama_user'])) {
  header("location:login.php");
  // die('location:login.php');//jika belum login jangan lanjut
}

$id = $_GET["id"];

$query = mysqli_query($koneksi, "SELECT a.id, a.is_approval_long_term,a.is_disapprove_long_term, a.id_bpu, a.tanggal_jatuh_tempo, a.tanggal_perpanjangan, a.reason, a.document, b.no as no_urut, b.term, c.nama, c.jenis FROM tb_jatuh_tempo a LEFT JOIN bpu b ON a.id_bpu = b.noid LEFT JOIN pengajuan c ON b.waktu = c.waktu
WHERE a.id = '$id' ORDER BY a.tanggal_jatuh_tempo desc");

$data = $query->fetch_all(MYSQLI_ASSOC);
if (count($data) > 0) {
  $data = $data[0];
}

$isManagement = $_SESSION['level'] == 'Managemen' ? true : false;

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
        <a class="navbar-brand" href="<?= $isManagement ? 'home-direksi.php' : 'home-finance.php' ?>">Budget-Ing</a>
      </div>
      <div class="collapse navbar-collapse" id="myNavbar">
        <ul class="nav navbar-nav">
          <li class="active"><a href="<?= $isManagement ? 'home-direksi.php' : 'home-finance.php' ?>">Home</a></li>

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

  <div class="container">
    <div id="alert-error-already-verify"></div>
    <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
      <div class="panel-body no-padding">
        <div class="row">
          <div class="col-lg-4">
            <h5><i class="fa fa-calendar-alt"></i><b> Berita Acara Pengajuan Tanggal Jatuh Tempo</b></h5>
            <hr/>
            <div id="notification-error-nominal"></div>
            <div id="notification-error-form"></div>
            <form>
              <div class="form-group">
                <label>Nama Peroject:</label>
                <input type="text" class="form-control" min="100" id="project-name" value="<?= isset($data['nama']) ? $data['nama'] : '' ?>" disabled/>
              </div>
              <div class="form-group">
                <label>Jenis Item Budget:</label>
                <input type="text" class="form-control" min="100" id="type-bpu" value="<?= isset($data['jenis']) ? $data['jenis'] : '' ?>" disabled/>
              </div>
              <div class="form-group">
                <label>Nomor Item Budget:</label>
                <input type="text" class="form-control" min="100" id="number-of-item" value="<?= isset($data['no_urut']) ? $data['no_urut'] : '' ?>" disabled/>
              </div>
              <div class="form-group">
                <label>Term:</label>
                <input type="text" class="form-control" min="100" id="term-of-item" value="<?= isset($data['term']) ? $data['term'] : '' ?>" disabled/>
              </div>
              <div class="form-group">
                <label>Tanggal Jatuh Tempo Sebelumnya:</label>
                <input type="text" class="form-control" min="100" id="previous-date" value="<?= isset($data['tanggal_jatuh_tempo']) ? $data['tanggal_jatuh_tempo'] : '' ?>" disabled/>
              </div>
              <div class="form-group">
                <label>Tanggal Jatuh Tempo Yang Diajukan:</label>
                <input type="date" class="form-control" min="<?=date('Y-m-d') ?>" value="<?= isset($data['tanggal_perpanjangan']) ? explode(' ',$data['tanggal_perpanjangan'])[0] : '' ?>" id="new-date" <?= $_SESSION["level"] == "Managemen" ? "disabled":"" ?>/>
              </div>
              <div class="form-group">
                <label>Keterangan Di Perpanjang:</label>
                <textarea class="form-control" id="reason" id="reason" required <?= $_SESSION["level"] == "Managemen" ? "disabled":"" ?>><?= $_SESSION["level"] == "Managemen" ? $data['reason'] : ""?></textarea>
              </div>
            </form>
            <?php if ($_SESSION["level"] != "Managemen") {
              echo '<div class="form-group">
              <label>File Pendukung:</label>
              <input type="file" class="form-control" accept="image/*" id="inputImage"/>
              <small class="text-danger"><i>*Tidak mendukung format file selain gambar</i></small>
            </div>';
              echo '<button class="btn btn-primary btn-flat btn-block" id="btn-submit" onclick="submit()"><i class="fa fa-check"></i> Ajukan Pengajuan</button>';
            } else {
              $disabled = $data["is_approval_long_term"] == '1' || $data["is_disapprove_long_term"] == '1' ? 'disabled' : '';
              echo '<button class="btn btn-success btn-flat btn-block" id="btn-approve" onclick="approve(1)" '.$disabled.'><i class="fa fa-check"></i> Setujui Pengajuan</button>
              <button class="btn btn-danger btn-flat btn-block" id="btn-disapprove" onclick="approve(0)" '.$disabled.'><i class="fa fa-times"></i> Tolak Pengajuan</button>';
            } ?>
          </div>
          <div class="col-lg-8 text-center">
            <img id="content-image" style="max-width: 720px;" src="<?= isset($data['document']) ? '/fileupload/'.$data['document'] : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTMGwPo04v2vaxbXlOkSuBK1aDQs1ntPnFM9_5P7BhEULVguY4tv4EZMuF88SaA7HZ8a1o&usqp=CAU'?>" />
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
  const isAlreadyApprove = "<?= json_encode($data["is_approval_long_term"] == '1' && $data["is_disapprove_long_term"] == '0' ? true : false) ?>";
  const isAlreadyDisApprove = "<?= json_encode($data["is_approval_long_term"] == '0' && $data["is_disapprove_long_term"] == '1' ? true : false) ?>";

  $(document).ready(function() {
    let idBpuVerify = getParameterByName('id')
    let idBpu = getParameterByName('bpu')

    $('#inputImage').change(function() {
      readURLSign(this);
    })

    if (isAlreadyApprove) {
      notifAlreadyVerify.innerHTML = alertError("success", "Pengajuan Perpanjangan Jatuh Tempo Data BPU sudah di Setujui")
    } else if (isAlreadyDisApprove) {
      notifAlreadyVerify.innerHTML = alertError("danger", "Pengajuan Perpanjangan Jatuh Tempo Data BPU Tidak di Setujui")
    }

  });
  
  function submit() {
    let idTanggalJatuhTempo = getParameterByName('id')
    let idBpu = getParameterByName('bpu')
    const tanggalDiperpanjang = $('#new-date').val()
    const reason = $('#reason').val();
    const input = document.getElementById('inputImage');
		let file = input.files[0];
    
    if ((tanggalDiperpanjang == "" || tanggalDiperpanjang === undefined) || (reason.length < 10 || reason === "")) {
      notifErrorForm.innerHTML = alertError("danger", "Form tidak boleh kosong")
      
      setTimeout(() => {
        notifErrorForm.innerHTML = ""
      }, 3000)
    } else {
      document.getElementById("btn-submit").disabled = true;
      document.getElementById('btn-submit').value = "Mengunggah"
      uploadFile(file, tanggalDiperpanjang, reason, idTanggalJatuhTempo, idBpu).then((res) => {
        if (res.is_success) {
          window.location.href = "/home-finance.php"
        }
      })
    }
  }

  function approve(isApprove) {
    let idTanggalJatuhTempo = getParameterByName('id')
    let idBpu = getParameterByName('bpu')
    const tanggalDiperpanjang = $('#new-date').val()

    let url = `/ajax/ajax-um-jatuh-tempo.php?action=approve-jatuh-tempo&id=${idTanggalJatuhTempo}&id-bpu=${idBpu}&tanggal-perpanjang=${tanggalDiperpanjang}&approve=${isApprove}`

    document.getElementById("btn-approve").disabled = true;
    document.getElementById("btn-disapprove").disabled = true;
    httpRequestGet(url).then((res) => {
      if (res.is_success) {
        notifAlreadyVerify.innerHTML = alertError("success", `Perpanjangan Jatuh Tempo berhasil di ${isApprove === 1 ? "Setujui" : "Tidak Disetujui"} `)
      } else {
        document.getElementById("btn-approve").disabled = false;
      document.getElementById("btn-disapprove").disabled = false;
        notifAlreadyVerify.innerHTML = alertError("danger", `Terjadi masalah ketika melakukan penyimpanan data"} `)
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

  function uploadFile(file, tanggalDiperpanjang, reason, id, idBpu) {
		const fd = new FormData();
		fd.append('file', file);
		let url = `/ajax/ajax-um-jatuh-tempo.php?action=update-jatuh-tempo&id=${id}&id-bpu=${idBpu}&tanggal-perpanjang=${tanggalDiperpanjang}&reason=${reason}`

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