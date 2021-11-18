<?php
//error_reporting(0);
 include ('koneksi.php');

 date_default_timezone_set("Asia/Bangkok");

  $waktu        = $_POST['waktu'];
  $pengaju      = $_POST['pengaju'];
  $divisi       = $_POST['divisi'];
  $nama_gambar  = $_FILES['gambar'] ['name'];
  $lokasi       = $_FILES['gambar'] ['tmp_name']; // Menyiapkan tempat nemapung gambar yang diupload
  $lokasitujuan ="./uploads"; // Menguplaod gambar kedalam folder ./image
  $upload       = move_uploaded_file($lokasi,$lokasitujuan."/".$nama_gambar);
  $timestam     = date("Y-m-d h:i:sa");
  // $disapprove    = $_POST['disapprove'];


  //periksa apakah udah submit
  if (isset($_POST['submit']))
  {


      $bikinbayar = mysqli_query($koneksi,"INSERT INTO upload (no,waktu,pengaju,divisi,gambar,status,timestam,disapprove)
                                             VALUES ('0','$waktu','$pengaju','$divisi','$nama_gambar','Belum Dibayar','$timestam','Validasi')");



    if ($bikinbayar){
        $selbay = mysqli_query($koneksi,"SELECT noid FROM pengajuan WHERE waktu='$waktu'");
        $s = mysqli_fetch_assoc($selbay);
        $noid = $s['noid'];
        echo "<script language='javascript'>";
        echo "alert(Upload File Berhasil')";
        echo "</script>";
        echo "<script> document.location.href='view.php?code=".$noid."'; </script>";
      }
    }
