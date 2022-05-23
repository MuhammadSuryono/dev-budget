<?php
date_default_timezone_set('Asia/Jakarta');

class Message {

    public function messageTolakPengajuanBudget($pengaju, $namaProject, $divisi, $totalbudget, $penolak, $alasan)
    {
        return "Dear $pengaju, 
Budget dengan keterangan berikut:
Nama Project    : *$namaProject*
Pengaju         : *$pengaju*
Divisi          : *$divisi*
Total Budget    : *Rp. " . number_format($totalbudget, 0, '', ',') . "*

Telah Ditolak oleh *$penolak* pada *" . date("d/m/Y H:i:s") . "* dengan keterangan *$alasan*


Terimakasih";
    }


    public function messageCreateProject($namaCreatorBudget, $namaUserPic, $pembuat, $projectName, $folderBudget, $divisi, $urlPengajuan)
    {
        return "
*Notifikasi Pembukaan Akses Folder Budget*
        
Dear $namaCreatorBudget,
Akses untuk pengajuan budget telah dibuka oleh *$pembuat* pada *" . date("d/m/Y H:i:s") . "* dengan keterangan sebagai berikut:

Nama Project : *$projectName*
Folder Budget: *$folderBudget*
PIC Budget : *$namaUserPic*
Divisi : *$divisi*
Silahkan ajukan budget secepatnya.

Klik link berikut untuk membuka Pengajuan Budget.
http://$urlPengajuan";
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

    public function messageAjukanBudget($creator, $pengaju, $namaProject, $folderBudget, $divisi, $totalbudget = 0,$keterangan = "", $urlPengajuan)
    {
        $msg = "Dear $creator,

Budget telah diajukan dengan keterangan sebagai berikut:
Nama Project : *$namaProject*
Folder Budget: *$folderBudget*
Pengaju : *$pengaju*
Divisi : *$divisi*
Total Budget : *Rp. " . number_format($totalbudget, 0, '', ',') . "*
";
    if ($keterangan != "") {
        $msg .= "
Keterangan: *$keterangan*";
    }
    $msg .= "


Selengkapnya pengajuan anda bisa dilihat dibawah ini.
http://$urlPengajuan";
return $msg;
    }

    public function messagePersetujuanBudget($dear, $pengaju, $namaProject, $folderBudget, $divisi, $totalbudget = 0, $penyetuju, $urlBpu)
    {
        $msg = "*Notifikasi Untuk Persetujuan Budget*

Dear $dear, 
Budget dengan keterangan berikut:
Nama Project    : *$namaProject*
Folder Budget : *$folderBudget*
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
Status           : Dibayar*,  Tanggal : " . $dateBayar . "*
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

    public function messageProcessBPUFinance($namaProject, $verificator, $item, $term, $pengaju, $arrPenerima, $arrJumlah, $keterangan = "", $link)
    {
        $msg = "Notifikasi BPU, 
BPU telah di *verifikasi* oleh $verificator dengan keterangan sebagai berikut:
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

    public function messageProcessTolakBPUFinance($namaProject, $user, $item, $term, $pengaju, $arrPenerima, $arrJumlah, $keterangan = "", $link)
    {
        $msg = "Notifikasi BPU, 
BPU telah di *Tolak* oleh $user dengan keterangan sebagai berikut:
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

    public function messageApprovePengajuanBPU($namaItem, $bpuType, $kota, $userSetuju, $namaProject, $noItem, $term, $arrPenerima, $tanggalBayar, $arrPembayaran, $arrJumlah, $keterangan, $link)
    {
        $msg = "Notifikasi BPU, 
BPU telah di setujui oleh $userSetuju dengan keterangan sebagai berikut:
Nama Project       : *" . $namaProject . "*
Item No.           : *$noItem*
Nama Item          : *$namaItem*
Jenis BPU          : *$bpuType*
Kota               : *$kota*
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
BPU telah di *TOLAK* oleh $userSetuju dengan keterangan sebagai berikut:
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

    public function messageSuccessTransferNonVendor($penerima, $jenisPembayaran, $norek, $job, $bank, $totalTransfer, $tanggal, $rincian, $kota, $jenis)
    {
        $msg = "Kepada $penerima <br><br>
                Berikut informasi status pembayaran Anda:<br><br>
                <table>
                <tr><td>Pembayaran       </td><td>: <strong>$jenisPembayaran</strong></td></tr>
                <tr><td>No. Rekening Anda       </td><td>: <strong>$norek</strong></td></tr>
                <tr><td>Nama Job       </td><td>: <strong>$job</strong></td></tr>
                <tr><td>Nama Item       </td><td>: <strong>$rincian</strong></td></tr>
                <tr><td>Kota       </td><td>: <strong>$kota</strong></td></tr>
                <tr><td>Jenis BPU       </td><td>: <strong>$jenis</strong></td></tr>
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

    public function messageSuccessTransferVendor($penerima, $jenisPembayaran, $norek, $bank, $totalTransfer, $tanggal, $noInvoice, $tanggalInvoice, $startTerm, $endTerm, $rincian, $kota, $jenis)
    {
        $msg = "Kepada $penerima <br><br>
                Berikut informasi status pembayaran Anda:<br><br>
                <table>
                <tr><td>No. Invoice       </td><td>: <strong>$noInvoice</strong></td></tr>
                <tr><td>Tanggal Invoice       </td><td>: <strong>$tanggalInvoice</strong></td></tr>
                <tr><td>Nama Item       </td><td>: <strong>$rincian</strong></td></tr>
                <tr><td>Kota       </td><td>: <strong>$kota</strong></td></tr>
                <tr><td>Jenis BPU       </td><td>: <strong>$jenis</strong></td></tr>
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

    public function messageSuccessTransferNonVendorWA($penerima, $jenisPembayaran, $norek, $job, $bank, $totalTransfer, $tanggal, $rincian, $kota, $jenis, $pengajuanNominal, $biayaTransfer, $jenisPajak, $biayaPajak)
    {
        $msg = "Notifikasi Informasi Pembayaran
Berikut informasi status pembayaran Anda:

Pembayaran : *$jenis*
No. Rekening Anda : *$norek*
Nama Job : *$job*
Nama Item : *$rincian*
Kota : *$kota*
Bank : *$bank*
Nama Penerima : *$penerima*
Jumlah Diajukan : *Rp. " . number_format($pengajuanNominal, 0, '', ',') . "*
Biaya Transfer : *Rp. " . number_format($biayaTransfer, 0, '', ',') . "*";

        if ($jenisPajak != "") {
            $msg .= "
$jenisPajak : *Rp. " . number_format($biayaPajak == "" ? 0 : $biayaPajak, 0, '', ',') . "*";
        }

$msg .= "
Jumlah Dibayarkan : *Rp. " . number_format($totalTransfer, 0, '', '.') . "*
Status : *Terbayar Lunas Tanggal:  $tanggal*

Jika ada pertanyaan lebih lanjut, silahkan email Divisi Finance ke finance@mri-research-ind.com
Hormat kami,
Divisi Finance 
Marketing Research Indonesia";

        return $msg;
    }

    public function messageSuccessTransferVendorWA($penerima, $jenisPembayaran, $norek, $bank, $totalTransfer, $tanggal, $noInvoice, $tanggalInvoice, $startTerm, $endTerm, $rincian, $kota, $jenis, $pengajuanNominal, $biayaTransfer, $jenisPajak, $biayaPajak)
    {
        $msg = "Notifikasi Informasi Pembayaran
Berikut informasi status pembayaran Anda:

No. Invoice : *$noInvoice*
Tanggal Invoice : *$tanggalInvoice*
Nama Item : *$rincian*
Kota : *$kota*
Term : *$startTerm dari $endTerm*
Jenis Pembayaran : *$jenis*
No. Rekening Anda : *$norek*
Bank : *$bank*
Nama Penerima : *$penerima*
Jumlah Diajukan : *Rp. " . number_format($pengajuanNominal, 0, '', ',') . "*
Biaya Transfer : *Rp. " . number_format($biayaTransfer, 0, '', ',') . "*";

        if ($jenisPajak != "") {
            $msg .= "
$jenisPajak       : *Rp. " . number_format($biayaPajak == "" ? 0 : $biayaPajak, 0, '', ',') . "*";
        }
$msg .= "
Jumlah Dibayarkan       : *Rp. " . number_format($totalTransfer, 0, '', '.') . "*
Status       : *Terbayar Tanggal:  $tanggal*

Jika ada pertanyaan lebih lanjut, silahkan email Divisi Finance ke finance@mri-research-ind.com
Hormat kami,
Divisi Finance 
Marketing Research Indonesia";
        
        return $msg;
    }

    public function messageValidasiBudget($receiver, $dataPengajuan, $validator, $urlPengajuan)
    {
        $msg = "Dear $receiver,

Budget telah divalidasi oleh *$validator* dengan keterangan sebagai berikut:
Nama Project : *$dataPengajuan[nama]*
Folder Budget: *$dataPengajuan[jenis]*
Pengaju : *$dataPengajuan[pengaju]*
Divisi : *$dataPengajuan[divisi]*
Total Budget : *Rp. " . number_format($dataPengajuan['totalbudget'], 0, '', ',') . "*
";
        if ($dataPengajuan['ket'] != "") {
            $msg .= "
Keterangan: *$dataPengajuan[ket]*";
        }
        $msg .= "


Selengkapnya pengajuan anda bisa dilihat dibawah ini.
http://$urlPengajuan";
        return $msg;
    }

    public function messagePenolakanValidasiBudget($receiver, $dataPengajuan, $validator, $urlPengajuan)
    {
        $msg = "Dear $receiver,

Budget telah *DITOLAK VALIDASI* oleh *$validator* dengan keterangan sebagai berikut:
Nama Project : *$dataPengajuan[nama]*
Folder Budget: *$dataPengajuan[jenis]*
Pengaju : *$dataPengajuan[pengaju]*
Divisi : *$dataPengajuan[divisi]*
Total Budget : *Rp. " . number_format($dataPengajuan['totalbudget'], 0, '', ',') . "*
";
        if ($dataPengajuan['declined_note'] != "") {
            $msg .= "
Keterangan: *$dataPengajuan[declined_note]*";
        }
        $msg .= "


Selengkapnya pengajuan anda bisa dilihat dibawah ini.
http://$urlPengajuan";
        return $msg;
    }

    public function messageValidasiPenerimaBpu($receiver, $dataPenerima, $item, $validator)
    {
        $msg = "Dear $receiver,

Penerima BPU telah divalidasi oleh *$validator* dengan keterangan sebagai berikut:
Nama Penerima : *$dataPenerima[nama_penerima]*
Jabatan: *$dataPenerima[jabatan]*
Email : *$dataPenerima[email]*
Item Budget : *$item*

Data yang sudah divalidasi tidak dapat diubah kembali dan data yang sudah divalidasi dapat di pilih pada pengajuan BPU
Terimakasih";
        return $msg;
    }

    public function messageValidasiPenerimaBpuDiTolak($receiver, $dataPenerima, $item, $validator, $reason)
    {
        $msg = "Dear $receiver,

Penerima BPU telah *Ditolak* oleh *$validator* dengan keterangan sebagai berikut:
Nama Penerima : *$dataPenerima[nama_penerima]*
Jabatan: *$dataPenerima[jabatan]*
Email : *$dataPenerima[email]*
Item Budget : *$item*
Alasan: *$reason*

Data yang sudah gagal divalidasi tidak dapat diubah kembali, data akan terhapus pada daftar penerima di item yang bersangkutan. Mohon lakukan pengajuan data penerima kembali dengan data yang sesuai.
Terimakasih";
        return $msg;
    }
}

    