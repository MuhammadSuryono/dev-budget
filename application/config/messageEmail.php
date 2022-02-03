<?php

class MessageEmail
{
    public function applyBudget($creator, $pengaju, $namaProject, $divisi, $totalbudget = 0,$keterangan = "", $urlPengajuan)
    {
        $msg = "Kepada $creator <br><br>
                Berikut informasi status pembayaran Anda:<br><br>
                <table>
                <tr><td>Nama Project       </td><td>: <strong>$namaProject</strong></td></tr>
                <tr><td>Pengaju       </td><td>: <strong>$pengaju</strong></td></tr>
                <tr><td>Divisi       </td><td>: <strong>$divisi</strong></td></tr>
                <tr><td>Total Budget       </td><td>: <strong>Rp. " . number_format($totalbudget) . "</strong></td></tr>
                </table>
                <br>
                Keterangan: $keterangan<br>
                Selengkapnya pengajuan anda bisa dilihat dibawah ini.<br/>
                http://$urlPengajuan
                ";

        return $msg;
    }

    public function createBudget($namaCreatorBudget, $namaUserPic, $pembuat, $projectName, $divisi, $urlPengajuan)
    {
        $msg = "Kepada $namaCreatorBudget <br><br>
                Akses untuk pengajuan budget telah dibuka oleh <b>$pembuat<b> pada " . date("d/m/Y H:i:s") . " dengan keterangan sebagai berikut:<br><br>
                <table>
                <tr><td>Nama Project       </td><td>: <strong>$projectName</strong></td></tr>
                <tr><td>PIC Budget       </td><td>: <strong>$namaUserPic</strong></td></tr>
                <tr><td>Divisi       </td><td>: <strong>$divisi</strong></td></tr>
                </table>
                <br>
                Silahkan ajukan budget melalui link dibawah ini.<br/>
                http://$urlPengajuan";

        return $msg;
    }

    public function approvedBudget($dear, $pengaju, $namaProject, $divisi, $totalbudget = 0, $penyetuju, $urlBpu)
    {
        $msg = "Kepada $dear <br><br>
                Budget dengan keterangan sebagai berikut:<br><br>
                <table>
                <tr><td>Nama Project       </td><td>: <strong>$namaProject</strong></td></tr>
                <tr><td>Pengaju       </td><td>: <strong>$pengaju</strong></td></tr>
                <tr><td>Divisi       </td><td>: <strong>$divisi</strong></td></tr>
                <tr><td>Total Budget       </td><td>: <strong>Rp. " . number_format($totalbudget) . "</strong></td></tr>
                </table>
                <br>
                Telah disetujui oleh <b>$penyetuju</b> pada ". date('d/m/Y H:i:s') . ".<br/>
                <br>
                Klik link berikut untuk pembuatan BPU<br>
                http://$urlBpu";

        return $msg;
    }

    public function applyBpuKadiv($dear, $pengaju, $namaProject, $penerima, $totalPengajuan = 0, $keterangan = "", $urlPengajuan)
    {
        $msg = "Kepada $dear <br><br>
                BPU telah diketahui oleh KADIV denagn keterangan sebagai berikut:<br><br>
                <table>
                <tr><td>Nama Project       </td><td>: <strong>$namaProject</strong></td></tr>
                <tr><td>Pengaju       </td><td>: <strong>$pengaju</strong></td></tr>
                <tr><td>Penerima       </td><td>: <strong>$penerima</strong></td></tr>
                <tr><td>Total Budget       </td><td>: <strong>Rp. " . number_format($totalPengajuan) . "</strong></td></tr>
                </table>
                <br>
                Keterangan : $keterangan <br/>
                <br>
                Klik link berikut untuk melihat informasi BPU<br>
                http://$urlPengajuan";

        return $msg;
    }

    public function statusPaymentBPUVendor($nnamaProject, $item, $term, $arrPenerima, $pembayar, $tanggalBayar, $nomorvoucher, $arrJumlah, $keterangan, $link)
    {
        $msg = "Informasi Pembayaran <br><br>
                BPU telah dibayar oleh Finance dengan keterangan sebagai berikut::<br><br>
                <table>
                <tr><td>Nama Project       </td><td>: <strong>$nnamaProject</strong></td></tr>
                <tr><td>Nomor Item       </td><td>: <strong>$item</strong></td></tr>
                <tr><td>Term       </td><td>: <strong>$term</strong></td></tr>
                <tr><td>Nama Penerima       </td><td>: <strong>".implode(', ', $arrPenerima)."</strong></td></tr>
                <tr><td>Pembayaran       </td><td>: <strong>$pembayar</strong></td></tr>
                <tr><td>Tanggal Pembayaran       </td><td>: <strong>$tanggalBayar</strong></td></tr>
                <tr><td>Nomor Voucher       </td><td>: <strong>$nomorvoucher</strong></td></tr>
                <tr><td>Dibayar       </td><td>: <strong>".implode(', ', $arrJumlah)."</strong></td></tr>
                </table>
                <br>
                Keterangan : $keterangan <br/>
                <br>
                Klik link berikut untuk melihat informasi BPU<br>
                $link";

        return $msg;
    }

    public function applyBPU($dear, $pengaju, $namaProject, $penerima, $totalPengajuan = 0, $keterangan = "", $urlPengajuan)
    {
        $msg = "Kepada $dear <br><br>
                Informasi Pengajuan BPU<br>
                BPU telah diajukan dengan keterangan sebagai berikut:<br><br>
                <table>
                <tr><td>Nama Project       </td><td>: <strong>$namaProject</strong></td></tr>
                <tr><td>Pengaju       </td><td>: <strong>$pengaju</strong></td></tr>
                <tr><td>Penerima       </td><td>: <strong>$penerima</strong></td></tr>
                <tr><td>Jumlah Yang Diajukan       </td><td>: <strong>Rp. ".number_format($totalPengajuan, 0, '', ',')."</strong></td></tr>
                </table>
                <br>
                Keterangan : $keterangan <br/>
                <br>
                Klik link berikut untuk melihat pengajuan anda<br>
                http://$urlPengajuan";

        return $msg;
    }

    public function applyBPUEksternal($dear, $pengaju, $namaProject, $penerima, $totalPengajuan = 0, $keterangan = "", $urlPengajuan)
    {
        $msg = "Kepada $dear <br><br>
                Informasi Pengajuan BPU<br>
                BPU telah diajukan  dan disetujui dengan keterangan sebagai berikut:<br><br>
                <table>
                <tr><td>Nama Project       </td><td>: <strong>$namaProject</strong></td></tr>
                <tr><td>Pengaju       </td><td>: <strong>$pengaju</strong></td></tr>
                <tr><td>Penerima       </td><td>: <strong>$penerima</strong></td></tr>
                <tr><td>Jumlah Yang Diajukan       </td><td>: <strong>Rp. ".number_format($totalPengajuan, 0, '', ',')."</strong></td></tr>
                </table>
                <br>
                Keterangan : $keterangan <br/>
                <br>
                Klik link berikut untuk melihat pengajuan anda<br>
                http://$urlPengajuan";

        return $msg;
    }

    public function approvedApplyBPU($userSetuju, $namaProject, $noItem, $term, $arrPenerima, $tanggalBayar, $arrPembayaran, $arrJumlah, $keterangan, $link)
    {
        $msg = "Informasi BPU <br><br>
                BPU telah disetujui oleh $userSetuju dengan keterangan sebagai berikut:<br><br>
                <table>
                <tr><td>Nama Project       </td><td>: <strong>$namaProject</strong></td></tr>
                <tr><td>Nomor Item       </td><td>: <strong>$noItem</strong></td></tr>
                <tr><td>Term       </td><td>: <strong>$term</strong></td></tr>
                <tr><td>Nama Penerima       </td><td>: <strong>".implode(', ', $arrPenerima)."</strong></td></tr>
                <tr><td>Tanggal Pembayaran       </td><td>: <strong>$tanggalBayar</strong></td></tr>
                <tr><td>Metode Pembayaran       </td><td>: <strong>". implode(', ', $arrPembayaran) ."</strong></td></tr>
                <tr><td>Total       </td><td>: <strong>".implode(', ', $arrJumlah)."</strong></td></tr>
                </table>
                <br>
                Keterangan : $keterangan <br/>
                <br>
                Klik link berikut untuk melihat informasi BPU<br>
                $link";

        return $msg;
    }

    public function rejectedApplyBPU($userSetuju, $namaProject, $noItem, $term, $arrPenerima, $tanggalBayar, $arrPembayaran, $arrJumlah, $keterangan, $link, $alasanTolakBpu)
    {
        $msg = "Informasi BPU <br><br>
                BPU telah disetujui oleh $userSetuju dengan keterangan sebagai berikut:<br><br>
                <table>
                <tr><td>Nama Project       </td><td>: <strong>$namaProject</strong></td></tr>
                <tr><td>Nomor Item       </td><td>: <strong>$noItem</strong></td></tr>
                <tr><td>Term       </td><td>: <strong>$term</strong></td></tr>
                <tr><td>Nama Penerima       </td><td>: <strong>".implode(', ', $arrPenerima)."</strong></td></tr>
                <tr><td>Tanggal Pembayaran       </td><td>: <strong>$tanggalBayar</strong></td></tr>
                <tr><td>Metode Pembayaran       </td><td>: <strong>". implode(', ', $arrPembayaran) ."</strong></td></tr>
                <tr><td>Total       </td><td>: <strong>".implode(', ', $arrJumlah)."</strong></td></tr>
                </table>
                <br>
                 ". $alasanTolakBpu != "" ? "Ditolak Dengan Alasan <b>$alasanTolakBpu</b>" : "Ditolak Tanpa Alasan" . "
                <br>
                Klik link berikut untuk melihat informasi BPU<br>
                $link";

        return $msg;
    }
}