<?php
require_once "application/config/database.php";
$con = new Database();
$koneksi = $con->connect();
if ($_POST['save'] == "save") {

	$id_user      = $_POST['id_user'];
	$passwordlama = md5($_POST['passwordlama']);
	$passwordbaru = md5($_POST['passwordbaru']);

	$cariuser = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE id_user='$id_user'");
	$cu = mysqli_fetch_array($cariuser);
	$passnya = $cu['password'];

	if ($passnya == $passwordlama) {

		$updatepass = mysqli_query($koneksi, "UPDATE tb_user SET password='$passwordbaru' WHERE id_user='$id_user'");

        echo '<script>
alert("Berhasil mengubah password anda");
window.location.href="ubahpassword.php";
</script>';
	} else {

		echo '<script>
alert("Password lama tidak sesuai");
window.location.href="ubahpassword.php";
</script>';
	}
}
?>
</div>