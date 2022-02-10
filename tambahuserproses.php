<?php
require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();


$con->set_name_db(DB_CUTI);
$con->init_connection();
$koneksiCuti = $con->connect();


$nama_user = $_POST['nama_user'];
$divisi    = $_POST['divisi'];
$jabatan   = $_POST['jabatan'];
$hak_akses = $_POST['hak_akses'];

if ($_POST['save'] == "save") {

  function random_username($string)
  {
    require_once "application/config/database.php";

    $con = new Database();
    $koneksi = $con->connect();

    while (1) {
      $nrRand = rand(0, 999);

      $username = str_pad($nrRand, 3, "0", STR_PAD_LEFT);
      $getData = mysqli_query($koneksi, "SELECT id_user FROM tb_user WHERE id_user='$username'");
      if (!mysqli_fetch_assoc($getData)) {
        return $username;
      }
    }
  }

  $getJabatan = mysqli_query($koneksi, "SELECT saldo FROM jabatan WHERE nama_jabatan='$jabatan'");
  $limit = mysqli_fetch_assoc($getJabatan)['saldo'];

  $cariuserbudget   = $koneksi->query("SELECT * FROM tb_user WHERE nama_user ='$nama_user'");
  $cub = mysqli_num_rows($cariuserbudget);

  $cariuserhc       = $koneksiCuti->query("SELECT * FROM tb_user WHERE nama_user ='$nama_user'");
  $cuh = mysqli_num_rows($cariuserhc);

  if (empty($_POST['nama_user']) || empty($_POST['divisi']) || empty($_POST['hak_akses'])) {
    echo "<div class='register-logo'><b>Oops!</b> Data Tidak Lengkap.</div>
    <div class='box box-primary'>
      <div class='register-box-body'>
        <p>Divisi dan hak akses tidak boleh kosong</p>
        <div class='row'>
          <div class='col-xs-8'></div>
          <div class='col-xs-4'>
            <button type='button' onclick=location.href='saldobpu.php' class='btn btn-block btn-warning'>Back</button>
          </div>
        </div>
      </div>
    </div>";
  } else if ($cub == 1) {
    echo "<div class='register-logo'><b>Oops!</b> Pembuataan user GAGAL !!</div>
			<div class='box box-primary'>
				<div class='register-box-body'>
					<p>Nama user sudah terdaftar di budget online. Harap masukkan nama lain !!</p>
					<div class='row'>
						<div class='col-xs-8'></div>
						<div class='col-xs-4'>
							<button type='button' onclick=location.href='saldobpu.php' class='btn btn-block btn-warning'>Back</button>
						</div>
					</div>
				</div>
			</div>";
  } else if ($cuh == 1) {
    $cariuserhc  = $koneksiCuti->query("SELECT * FROM tb_user WHERE nama_user ='$nama_user'");
    $userhc   = mysqli_fetch_array($cariuserhc);
    $username = $userhc['id_user'];
    $password = md5('12345');

    $insertkebudget = $koneksi->query("INSERT INTO tb_user (id_user, nama_user, divisi, password, hak_akses, aktif, saldo, level)
                                                  VALUES ('$username', '$nama_user', '$divisi', '$password', '$hak_akses', 'Y', '$limit', '$jabatan')");

    echo "<script language='javascript'>";
    echo "alert('User $nama_user Berhasil Ditambahkan!')";
    echo "</script>";
    echo "<script> document.location.href='saldobpu.php'; </script>";
  } else {

    $id_user2 = random_username($nama_user);
    $password2 = md5('12345');

    $insertkebudget = $koneksi->query("INSERT INTO tb_user (id_user, nama_user, divisi, password, hak_akses, aktif, saldo, level)
                                                    VALUES ('$id_user2', '$nama_user', '$divisi', '$password2', '$hak_akses', 'Y', '$limit', '$jabatan')");

    echo "<script language='javascript'>";
    echo "alert('User $nama_user Berhasil Ditambahkan Dengan ID: $id_user2')";
    echo "</script>";
    echo "<script> document.location.href='saldobpu.php'; </script>";
  }
}
