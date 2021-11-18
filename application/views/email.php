<?php
// var_dump($data_transfer);
// die;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta charset="utf-8" />
    <title>Document</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;500&display=swap" rel="stylesheet" />

</head>

<body style='padding: 0; margin-left: 30%; display: flex; justify-content: center; font-family: "Roboto", sans-serif;'>
    <p>
        Kepada <?= $data_bpu->namapenerima ?><br><br>
        Berikut informasi status pembayaran yang akan Anda terima:<br><br>
        Nama Pembayaran : <strong>" . $bpu['ket_pembayaran'] . "</strong><br>
    </p>
    echo " " . $bpu['namapenerima'] . ",


    No Rekening : <strong>" . $bpu['norek'] . "</strong><br>
    Bank : <strong>" . $bank['namabank'] . "</strong><br>
    Penerima : <strong>" . $bpu['namapenerima'] . "</strong><br>
    Status : <strong>Dibayar</strong><br>
    Jumlah Diajukan : <strong>Rp. " . number_format($jumlahbayar, 0, '', '.') . "</strong><br><br>
    Informasi update status pembayaran akan kami kirimkan kembali melalui email. Jika ada pertanyaan lebih lanjut, silahkan menghubungi kami di xxx.<br><br>
    Terima kasih atas perhatian Anda.<br><br>
    Hormat kami,<br>
    Finance Marketing Research Indonesia
    ";
    <div class="card" style=' width: 23em; height: 100%; padding: 1em;'>
        <section class="header" style='display: grid; grid-template-columns: 1fr; grid-auto-rows: 50px;margin-bottom: 0.5em;'>
            <div class="logo" style="display: flex;align-items: center;justify-content: center; margin-left:40%;">
                <img src="<?= base_url('assets/images/logo-bca.png') ?>" alt="" style="width: 25%;" />
            </div>
        </section>

        <table style="width: 100%;">
            <tr>
                <td style="width: 33.3%; border-bottom: 1px solid #295d8d;margin-top: 3px"></td>
                <td class="line-text" style="width: 33.3%; font-weight: bold; color: #79d020;">
                    <span>
                        <img src="<?= base_url('assets/images/checklist.png') ?>" style="width: 20px; text-align: center;vertical-align: middle;" />BERHASIL
                    </span>
                </td>
                <td style="width: 33.3%; border-bottom: 1px solid #295d8d;margin-top: 3px"></td>
            </tr>
        </table>

        <section class="body" style=" width: 100%;">
            <table style="width: 100%;">
                <tr>
                    <td style="color: rgba(0, 0, 0, 0.672); padding: 0.7em 0;">TANGGAL TRANSAKSI</td>
                    <td style="text-align: right;color: rgba(0, 0, 0, 0.672); padding: 0.7em 0;"><?= $data_transfer->jadwal_transfer ?></td>
                </tr>
                <tr>
                    <td style="color: rgba(0, 0, 0, 0.672); padding: 0.7em 0;">NAMA PENERIMA</td>
                    <td style="text-align: right;color: rgba(0, 0, 0, 0.672); padding: 0.7em 0;"><?= strtoupper($data_transfer->pemilik_rekening) ?></td>
                </tr>
                <tr>
                    <td style="color: rgba(0, 0, 0, 0.672); padding: 0.7em 0;">BANK TUJUAN</td>
                    <td style="text-align: right;color: rgba(0, 0, 0, 0.672); padding: 0.7em 0;"><?= strtoupper($data_transfer->bank) ?></td>
                </tr>
                <tr>
                    <td style="color: rgba(0, 0, 0, 0.672); padding: 0.7em 0;">NO. REKENING TUJUAN</td>
                    <td style="text-align: right;color: rgba(0, 0, 0, 0.672); padding: 0.7em 0;"><?= strtoupper($data_transfer->norek) ?></td>
                </tr>
                <tr>
                    <td style="color: rgba(0, 0, 0, 0.672); padding: 0.7em 0;">DARI REKENING</td>
                    <td style="text-align: right;color: rgba(0, 0, 0, 0.672); padding: 0.7em 0;"><?= strtoupper($data_transfer->rekening_sumber) ?></td>
                </tr>
                <tr>
                    <td style="color: rgba(0, 0, 0, 0.672); padding: 0.7em 0;">NOMINAL</td>
                    <td style="text-align: right;color: rgba(0, 0, 0, 0.672); padding: 0.7em 0;">IDR <?= number_format($data_transfer->jumlah, 2)  ?></td>
                </tr>
                <tr>
                    <td style="color: rgba(0, 0, 0, 0.672); padding: 0.7em 0;">BIAYA ADMIN</td>
                    <td style="text-align: right;color: rgba(0, 0, 0, 0.672); padding: 0.7em 0;">IDR <?= number_format($data_transfer->biaya_trf, 2)  ?></td>
                </tr>
                <tr>
                    <td style="color: rgba(0, 0, 0, 0.672); padding: 0.7em 0;">LAYANAN TRANSFER</td>
                    <td style="text-align: right;color: rgba(0, 0, 0, 0.672); padding: 0.7em 0;">

                        <?php
                        if ($data_transfer->kode_bank == 'CENAIDJA') echo 'Online';
                        else echo 'LLG';
                        ?>
                    </td>
                </tr>
                <tr>
                    <td style="color: rgba(0, 0, 0, 0.672); padding: 0.7em 0;">JENIS TRANSAKSI</td>
                    <td style="text-align: right;color: rgba(0, 0, 0, 0.672); padding: 0.7em 0;">Transfer ke <?= $data_transfer->bank ?></td>
                </tr>
                <tr>
                    <td style="color: rgba(0, 0, 0, 0.672); padding: 0.7em 0;">BERITA TRANSFER</td>
                    <td style="text-align: right;color: rgba(0, 0, 0, 0.672); padding: 0.7em 0;"><?= strtoupper($data_transfer->berita_transfer) ?></td>
                </tr>
                <tr>
                    <td style="color: rgba(0, 0, 0, 0.672); padding: 0.7em 0;">NO. REFERENSI</td>
                    <td class="blue-text" style="text-align: right;color: rgba(0, 0, 0, 0.672); padding: 0.7em 0; color: #155a9d;"><?= $data_transfer->transfer_req_id ?></td>
                </tr>
            </table>
        </section>
    </div>
</body>

</html>