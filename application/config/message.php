<?php

class Message {

    public function messageTolakPengajuanBudget($pengaju, $namaProject, $divisi, $totalbudget, $penolak, $alasan)
    {
        $msg = "Dear $pengaju, <br><br>
Budget dengan keterangan berikut:<br><br>
Nama Project    : <strong>$namaProject</strong><br>
Pengaju         : <strong>$pengaju</strong><br>
Divisi          : <strong>$divisi</strong><br>
Total Budget    : <strong>Rp. " . number_format($totalbudget, 0, '', ',') . "</strong><br><br>

Telah Ditolak oleh <strong> $penolak </strong> pada <strong> " . date("d/m/Y H:i:s") . "</strong> dengan keterangan <strong>$alasan</strong><br><br>
        ";

        return $msg;
    }


    public function messageCreateProject($namaCreatorBudget, $namaUserPic, $pembuat, $projectName, $divisi, $urlPengajuan, $judul)
    {
        $msg = "
*$judul*
        
Dear $namaCreatorBudget,
Akses untuk pengajuan budget telah dibuka oleh *$pembuat* pada *" . date("d/m/Y H:i:s") . "* dengan keterangan sebagai berikut:

Nama Project       : *$projectName*
PIC Budget            : *$namaUserPic*
Divisi                        : *$divisi*
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
Nama Project       : *$namaProject*
Pengaju                  :*$pengaju*
Divisi                        : *$divisi*
Total Budget         : *Rp. " . number_format($totalbudget, 0, '', ',') . "*
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

    public function messagePembuatanBPUEksternal($dear, $pengaju, $namaProject, $penerima, $totalPengajuan = 0, $keterangan = "", $urlPengajuan)
    {
        $msg = "*Notifikasi Untuk Pengajuan BPU*
Dear $dear, 
BPU telah dibuat dan disetujui oleh:
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

    public function messageStatusPembayaranBPUVendorSuplier($nnamaProject, $item, $term, $arrPenerima, $pembayar, $tanggalBayar, $nomorvoucher, $arrJumlah, $keterangan, $link)
    {
        $msg = "Notifikasi BPU, 
BPU telah dibayar oleh Finance dengan keterangan sebagai berikut:
Nama Project       : *" . $nnamaProject . "*
Item No.           : *$item*
Term               : *$term*
Nama Penerima  : *" . implode(', ', $arrPenerima) . "*
Pembayar           : *$pembayar*
Tanggal Pembayaran : *$tanggalBayar*
Nomer Voucher      : *$nomorvoucher*
Dibayar     : *" . implode(', ', $arrJumlah) . "*
";
if ($keterangan) {
    $msg .= "
Keterangan:*$keterangan*";
}
$msg .= "Klik $link untuk membuka aplikasi budget.";

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

    public function messageProcessBPUFinance($namaProject, $item, $term, $pengaju, $arrPenerima, $arrJumlah, $keterangan = "", $link)
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

Lihat selengkapnya dibawah ini:
 ".
$link;
        return $msg;
    }

    public function messagerequestProcessBPUFinance($namaProject, $item, $term, $pengaju, $arrPenerima, $arrJumlah, $keterangan = "", $link)
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

Lihat selengkapnya dibawah ini:
 ".
$link;
        return $msg;
    }

    public function messageProcessTolakBPUFinance($namaProject, $item, $term, $pengaju, $arrPenerima, $arrJumlah, $keterangan = "", $link)
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
    $msg .="

Lihat selengkapnya dibawah ini:".
    $link;
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
    $msg .= 
    "
Lihat selengkapnya dibawah ini:

$urlPengajuan";

return $msg;
    }

    public function messageApprovePengajuanBPU($userSetuju, $namaProject, $noItem, $term, $arrPenerima, $tanggalBayar, $arrPembayaran, $arrJumlah, $keterangan, $link)
    {
        $msg = "Notifikasi BPU, 
BPU telah di setujui oleh $userSetuju dengan keterangan sebagai berikut:
Nama Project       : *" . $namaProject . "*
Item No.           : *$noItem*
Term               : *$term*
Nama Penerima  : *" . implode(', ', $arrPenerima) . "*
Tanggal Pembayaran : *$tanggalBayar*
Metode Pembayaran  : *" . implode(', ', $arrPembayaran) . "*
Total Diajukan : *" . implode(', ', $arrJumlah) . "*
        ";
        if ($keterangan != "") {
            $msg .= "
 Keterangan: *$keterangan*";
        } 
        $msg .="

Lihat selengkapnya dibawah ini:
".
$link;
        return $msg;
    }

    public function messageTolakPengajuanBPU($userSetuju, $namaProject, $noItem, $term, $arrPenerima, $tanggalBayar, $arrPembayaran, $arrJumlah, $keterangan, $link)
    {
        $msg = "Notifikasi BPU, 
BPU telah di TOLAK oleh $userSetuju dengan keterangan sebagai berikut:
Nama Project       : *" . $namaProject . "*
Item No.           : *$noItem*
Term               : *$term*
Nama Penerima  : *" . implode(', ', $arrPenerima) . "*
Tanggal Pembayaran : *$tanggalBayar*
Metode Pembayaran  : *" . implode(', ', $arrPembayaran) . "*
Total Diajukan : *" . implode(', ', $arrJumlah) . "*
        ";
        if ($keterangan != "") {
            $msg .= "
 Keterangan: *$keterangan*";
        } 

        $msg .="
Lihat selengkapnya dibawah ini:".
    $link;
        return $msg;
    }

    public function messageDissaproveBudget($pengaju, $namaProject, $divisi, $totalbudget, $alasanTolak, $pembuat, $url)
    {
        $msg = "Dear $pengaju, 
Budget dengan keterangan berikut:
Nama Project    : *$namaProject*
Pengaju         : *$pengaju*
Divisi          : *$divisi*
Total Budget    : *Rp. " . number_format($totalbudget, 0, '', ',') . "*

Telah Ditolak oleh *$pembuat* pada *" . date("d/m/Y H:i:s") . "* dengan keterangan *$alasanTolak* 
";

    $msg .= "
Klik $url untuk membuka aplikasi budget.";
        return $msg;
    }

    public function messageSuccessTransferNonVendor($penerima, $jenisPembayaran, $norek, $job, $bank, $totalTransfer, $tanggal)
    {
        $msg = "Kepada $penerima <br><br>
                Berikut informasi status pembayaran Anda:<br><br>
                <table>
                <tr><td>Pembayaran       </td><td>: <strong>$jenisPembayaran</strong></td></tr>
                <tr><td>No. Rekening Anda       </td><td>: <strong>$norek</strong></td></tr>
                <tr><td>Nama Job       </td><td>: <strong>$job</strong></td></tr>
                <tr><td>Bank       </td><td>: <strong>$bank</strong></td></tr>
                <tr><td>Nama Penerima       </td><td>: <strong>$penerima</strong></td></tr>
                <tr><td>Jumlah Dibayarkan       </td><td>: <strong>Rp. " . number_format($totalTransfer, 0, '', '.') . "</strong></td></tr>
                <tr><td>Status       </td><td>: <strong>Terbayar Lunas</strong> Tanggal:  $tanggal</strong></td></tr>
                </table>
                Jika ada pertanyaan lebih lanjut, silahkan email Divisi Finance ke finance@mri-research-ind.com<br><br>
                Hormat kami,<br>
                Divisi Finance <br>
                Marketing Research Indonesia";
        
        return $msg;
    }

    public function messageSuccessTransferVendor($penerima, $jenisPembayaran, $norek, $bank, $totalTransfer, $tanggal, $noInvoice, $tanggalInvoice, $startTerm, $endTerm)
    {
        $msg = "Kepada $penerima <br><br>
                Berikut informasi status pembayaran Anda:<br><br>
                <table>
                <tr><td>No. Invoice       </td><td>: <strong>$noInvoice</strong></td></tr>
                <tr><td>Tanggal Invoice       </td><td>: <strong>$tanggalInvoice</strong></td></tr>
                <tr><td>Term       </td><td>: <strong>$startTerm dari $endTerm</strong></td></tr>
                <tr><td>Jenis Pembayaran       </td><td>: <strong>$jenisPembayaran</strong></td></tr>
                <tr><td>No. Rekening Anda       </td><td>: <strong>$norek</strong></td></tr>
                <tr><td>Bank       </td><td>: <strong>$bank</strong></td></tr>
                <tr><td>Nama Penerima       </td><td>: <strong>$penerima</strong></td></tr>
                <tr><td>Jumlah Dibayarkan       </td><td>: <strong>Rp. " . number_format($totalTransfer, 0, '', '.') . "</strong></td></tr>
                <tr><td>Status       </td><td>: <strong>Terbayar</strong> Tanggal:  $tanggal</strong></td></tr>
                </table>
                Jika ada pertanyaan lebih lanjut, silahkan email Divisi Finance ke finance@mri-research-ind.com<br><br>
                Hormat kami,<br>
                Divisi Finance <br>
                Marketing Research Indonesia";
        
        return $msg;
    }

    public function messageSuccessTransferNonVendorWA($penerima, $jenisPembayaran, $norek, $job, $bank, $totalTransfer, $tanggal)
    {
        $msg = "Notifikasi Informasi Pembayaran
Berikut informasi status pembayaran Anda:

Pembayaran       : *$jenisPembayaran*
No. Rekening Anda       : *$norek*
Nama Job       : *$job*
Bank       : *$bank*
Nama Penerima       : *$penerima*
Jumlah Dibayarkan       : *Rp. " . number_format($totalTransfer, 0, '', '.') . "*
Status       : *Terbayar Lunas Tanggal:  $tanggal*

Jika ada pertanyaan lebih lanjut, silahkan email Divisi Finance ke finance@mri-research-ind.com
Hormat kami,
Divisi Finance 
Marketing Research Indonesia";
        
        return $msg;
    }

    public function messageSuccessTransferVendorWA($penerima, $jenisPembayaran, $norek, $bank, $totalTransfer, $tanggal, $noInvoice, $tanggalInvoice, $startTerm, $endTerm)
    {
        $msg = "Notifikasi Informasi Pembayaran
Berikut informasi status pembayaran Anda:

No. Invoice       : *$noInvoice*
Tanggal Invoice       : *$tanggalInvoice*
Term       : *$startTerm dari $endTerm*
Jenis Pembayaran       : *$jenisPembayaran*
No. Rekening Anda       : *$norek*
Bank       : *$bank*
Nama Penerima       : *$penerima*
Jumlah Dibayarkan       : *Rp. " . number_format($totalTransfer, 0, '', '.') . "*
Status       : *Terbayar Tanggal:  $tanggal*

Jika ada pertanyaan lebih lanjut, silahkan email Divisi Finance ke finance@mri-research-ind.com
Hormat kami,
Divisi Finance 
Marketing Research Indonesia";
        
        return $msg;
    }
}

    