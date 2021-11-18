<?php

if ($_POST['save'] == "save") {

	$id_user      = $_POST['id_user'];
	$passwordlama = md5($_POST['passwordlama']);
	$passwordbaru = md5($_POST['passwordbaru']);

	$cariuser = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE id_user='$id_user'");
	$cu = mysqli_fetch_array($cariuser);
	$passnya = $cu['password'];

	if ($passnya == $passwordlama) {

		$updatepass = mysqli_query($koneksi, "UPDATE tb_user SET password='$passwordbaru' WHERE id_user='$id_user'");

		echo "<div class='register-logo'><b>Ubah Password Berhasil</b></div>
  			<div class='box box-primary'>
  				<div class='register-box-body'>
  					<p>Password berhasil di ubah !!</p>
  					<div class='row'>
  						<div class='col-xs-8'></div>
  						<div class='col-xs-4'>
  							<button type='button' onclick=location.href='login.php' class='btn btn-block btn-warning'>Back</button>
  						</div>
  					</div>
  				</div>
  			</div>";
	} else {

		echo "<div class='register-logo'><b>Oops!</b> Rubah password GAGAL!!</div>
  			<div class='box box-primary'>
  				<div class='register-box-body'>
  					<p>Password lama anda salah. Silahkan masukkan password lama dengan benar</p>
  					<div class='row'>
  						<div class='col-xs-8'></div>
  						<div class='col-xs-4'>
  							<button type='button' onclick=location.href='ubahpassword.php' class='btn btn-block btn-warning'>Back</button>
  						</div>
  					</div>
  				</div>
  			</div>";
	}
}
?>
</div>