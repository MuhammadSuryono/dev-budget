
<?php
$waktu = $_REQUEST['waktu'];
$no    = $_REQUEST['no'];
$term  = $_REQUEST['term'];
$sql = "UPDATE bpu SET statusrtp ='Siap Dibayar' WHERE waktu ='$waktu' AND no ='$no' AND term ='$term'";
if (mysqli_query($koneksi, $sql)) {
  return "success!";
} else {
  return "failed!";
}
?>
