<?php

class Message {
    public function messageCreateProject($namaCreatorBudget, $namaUserPic, $pembuat, $projectName, $divisi, $urlPengajuan, $judul)
    {
        $msg = "
*$judul*
        
Dear $namaCreatorBudget,
Akses untuk pengajuan budget telah dibuka oleh *$pembuat* pada *" . date("d/m/Y H:i:s") . "* dengan keterangan sebagai berikut:

Nama Project    : *$projectName*
PIC Budget      : *$namaUserPic*
Divisi          : *$divisi*
Silahkan ajukan budget secepatnya.

Klik link berikut untuk membuka Pengajuan Budget.
http://$urlPengajuan";

        return $msg;
    }

    public function alertMessage($message, $nextLink = "")
    {
      return "
      <script language='javascript'>
      alert('$message')
      </script>
      <script> document.location.href='$nextLink'; </script>
      ";
    }

    public function messageAjukanBudget($creator, $pengaju, $namaProject, $divisi, $totalbudget = 0,$keterangan = "", $urlPengajuan)
    {
        $msg = "Dear $creator,
Budget telah diajukan dengan keterangan sebagai berikut:
Nama Project    : *$namaProject*
Pengaju         : *$pengaju*
Divisi          : *$divisi*
Total Budget    : *Rp. " . number_format($totalbudget, 0, '', ',') . "*
";
    if ($keterangan != "") {
        $msg .= "Keterangan: *$keterangan*";
    }
    $msg .= "
Selengkapnya pengajuan anda bisa dilihat dibawah ini.
http://$urlPengajuan";
return $msg;
    }

    public function messagePersetujuanBudget($dear, $pengaju, $namaProject, $divisi, $totalbudget = 0, $penyetuju, $urlBpu)
    {
        $msg = "*Notifikasi Untuk Persetujuan Budget*
Dear $dear, 
Budget dengan keterangan berikut:
Nama Project    : *$namaProject*
Pengaju         : *$pengaju*
Divisi          : *$divisi*
Total Budget    : *Rp. " . number_format($totalbudget, 0, '', ',') . "*

Telah disetujui oleh *$penyetuju* pada *" . date("d/m/Y H:i:s") . "*

Klik link berikut untuk pembuatan BPU
http://$urlBpu

Terimakasih";

return $msg;
    }

    public function messagePengajuanBPU($dear, $pengaju, $namaProject, $penerima, $totalPengajuan = 0, $keterangan = "", $urlPengajuan)
    {
        $msg = "*Notifikasi Untuk Pengajuan BPU*
Dear $dear, 
BPU telah diajukan dengan keterangan sebagai berikut:
Nama Project    : *$namaProject*
Pengaju         : *$pengaju*
Penerima          : *$penerima*
Jumlah Yang Diajukan    : *Rp. " . number_format($totalPengajuan, 0, '', ',') . "*

";

if ($keterangan != "") {
    $msg .= "Keterangan: *$keterangan*";
}
$msg .= "
Selengkapnya pengajuan anda bisa dilihat dibawah ini.
http://$urlPengajuan";

return $msg;
    }

    public function messagePembayaranBPU($namaProject, $no, $term, $namapenerima, $pembayar, $tanggalbayar, $nomorvoucher, $jumlahbayar = 0, $keterangan = "")
    {
        $msg = "*Notifikasi BPU*
 
BPU telah dibayar oleh Finance dengan keterangan sebagai berikut:
Nama Project       : $namaProject
Item No.           : $no
Term               : $term
Nama Penerima      : $namapenerima
Pembayar           : $pembayar
Tanggal Pembayaran : $tanggalbayar
Nomer Voucher      : $nomorvoucher
Jumlah Dibayar     : Rp. " . number_format($jumlahbayar, 0, '', ',') . "";
        if ($keterangan) {
        $msg .= "Keterangan: $keterangan ";
        }
        return $msg;
    }

    public function messageStatusPembayaranBPUVendorSuplier($penerima, $noInvoice, $tanggalInvoice, $term,$jenisPembayaran, $noRekening, $bank, $jumlahBayar, $dateBayar)
    {
        $msg = "Kepada " . $penerima . ", 
Berikut informasi status pembayaran yang akan Anda terima:
No.Invoice       : " . $noInvoice . "
Tgl. Invoice     : " . $tanggalInvoice . "
Term             : " . $term  . "
Jenis Pembayaran : " . $jenisPembayaran . "
No. Rekening Anda : " . $noRekening . "
Bank             : " . $bank . "
Nama Penerima    : " . $penerima . "
Jumlah Dibayarkan : Rp. " . number_format($jumlahBayar, 0, '', '.') . "
Status           : *Dibayar*,  Tanggal : *" . $dateBayar . "*
Jika ada pertanyaan lebih lanjut, silahkan email Divisi Finance ke *finance@mri-research-ind.com*.
Hormat kami,
Finance Marketing Research Indonesia
";
        return $msg;
    }

    public function messageStatusPembayranBPUNonVendor($penerima, $namaPembayaran, $noRekening, $bank, $jumlahBayar, $dateBayar)
    {
        $msg = "Kepada " . $penerima . ", 
Berikut informasi status pembayaran yang akan Anda terima:
Nama Pembayaran : " . $namaPembayaran . "
No. Rekening Anda : " . $noRekening . "
Bank             : " . $bank . "
Nama Penerima    : " . $penerima . "
Jumlah Dibayarkan : Rp. " . number_format($jumlahBayar, 0, '', '.') . "
Status           : Dibayar</strong>,  Tanggal : " . $dateBayar . "</strong>
Jika ada pertanyaan lebih lanjut, silahkan email Divisi Finance ke finance@mri-research-ind.com.
Hormat kami,
Finance Marketing Research Indonesia
";
        return $msg;
    }

    public function messageReminderPembayaran($namaProject, $rincian, $tanggalBayar, $link = '')
    {
        $msg = "Reminder Pembayaran,
Nama Project      : " . $namaProject . "
Nama Item Budget  : " . $rincian . "
Tanggal Bayar     : " . $tanggalBayar  .  "

Lihat selengkapnya dibawah ini:
$link
";
        return $msg;
    }

    public function messageProcessBPUFinance($namaProject, $item, $term, $pengaju, $arrPenerima, $arrJumlah, $keterangan = "")
    {
        $msg = "Notifikasi BPU, 
BPU telah di *verifikasi* oleh Finance dengan keterangan sebagai berikut:
Nama Project   : *" . $namaProject . "*
Item No.       : *$item*
Term           : *$term*
Nama Pengaju   : *" . $pengaju . "*
Nama Penerima  : *" . implode(', ', $arrPenerima) . "*
Total Diajukan : *" . implode(', ', $arrJumlah) . "*
        ";
    if ($keterangan != "") {
        $msg .= "
Keterangan:* $keterangan *";
    }
    $msg .="

    
Terimakasih";
        return $msg;
    }

    public function messageProcessTolakBPUFinance($namaProject, $item, $term, $pengaju, $arrPenerima, $arrJumlah, $keterangan = "")
    {
        $msg = "Notifikasi BPU, 
BPU telah di *Tolak* oleh Finance dengan keterangan sebagai berikut:
Nama Project   : *" . $namaProject . "*
Item No.       : *$item*
Term           : *$term*
Nama Pengaju   : *" . $pengaju . "*
Nama Penerima  : *" . implode(', ', $arrPenerima) . "*
Total Diajukan : *" . implode(', ', $arrJumlah) . "*
        ";
    if ($keterangan != "") {
        $msg .= "
Keterangan:* $keterangan *";
    }
        return $msg;
    }

    public function messagePengajuanBPUKadiv($dear, $pengaju, $namaProject, $penerima, $totalPengajuan = 0, $keterangan = "", $urlPengajuan)
    {
        $msg = "*Notifikasi Untuk Pengajuan BPU*
Dear $dear, 
BPU telah diketahui oleh KADIV dengan keterangan sebagai berikut:
Nama Project    : *$namaProject*
Pengaju         : *$pengaju*
Penerima          : *$penerima*
Jumlah Yang Diajukan    : *Rp. " . number_format($totalPengajuan, 0, '', ',') . "*

";

if ($keterangan != "") {
    $msg .= "Keterangan: *$keterangan*";
}
$msg .= "

Terimakasih";

return $msg;
    }
}