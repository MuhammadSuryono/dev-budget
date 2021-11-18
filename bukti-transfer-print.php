<?php
session_start();

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();
require_once("dompdf/dompdf_config.inc.php");

$id = $_GET['id'];
$select = mysqli_query($koneksiTransfer, "SELECT * FROM data_transfer WHERE transfer_req_id='$id'");
$d = mysqli_fetch_assoc($select); ?>

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

    <style>
        body {
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
            font-family: "Roboto", sans-serif;
        }

        .card {
            width: 23em;
            height: 100%;
            padding: 1em;
        }

        .header {
            display: grid;
            grid-template-columns: 1fr;
            grid-auto-rows: 50px;
            margin-bottom: 0.5em;
        }

        .logo {
            display: flex;
            /* new */
            align-items: center;
            /* new */
            justify-content: center;
            /* new */
        }

        .logo img {
            width: 25%;
        }

        .blue-line {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            text-align: center;
            position: relative;
        }

        .line {
            width: 100%;
            /*or whatever width you want the effect of <hr>*/
            border-top: 1px solid #295d8d;
            margin-top: 0.7em;
        }

        .line-text {
            font-weight: bold;
            letter-spacing: 0.4px;
            color: #79d020;
            /* color: #00db02; */
            display: flex;
            /* new */
            align-items: center;
            /* new */
            justify-content: center;
            /* new */
        }

        .line-text span {
            margin-right: 0.3em;
        }

        .line:first-child:before {
            content: "\2022";
            position: absolute;
            font-size: 1.5rem;
            color: #295d8d;
            top: -2px;
            left: -3px;
        }

        .line:last-child::after {
            content: "\2022";
            position: absolute;
            font-size: 1.5rem;
            color: #295d8d;
            right: -3px;
            top: -2px;
        }

        .body {
            width: 100%;
        }

        .body table {
            width: 100%;
        }

        td:first-child {
            text-transform: uppercase;
            font-weight: bold;
            text-align: left;
        }

        td:last-child {
            text-align: right;
        }

        td {
            color: rgba(0, 0, 0, 0.672);
            padding: 0.7em 0;
        }

        .blue-text {
            color: #155a9d;
        }
    </style>
</head>

<body>
    <div class="card">
        <section class="header">
            <div class="logo">
                <img src="images/logo-bca.png" alt="" />
            </div>
        </section>

        <section class="blue-line">
            <span class="line"></span>
            <span class="line-text"><span>&#10004;</span>BERHASIL</span>
            <span class="line"></span>
        </section>

        <section class="body">
            <table>
                <tr>
                    <td>Tanggal Transaksi</td>
                    <td><?= $d['jadwal_transfer'] ?></td>
                </tr>
                <tr>
                    <td>Nama Penerima</td>
                    <td><?= strtoupper($d['pemilik_rekening']) ?></td>
                </tr>
                <tr>
                    <td>Bank Tujuan</td>
                    <td><?= strtoupper($d['bank']) ?></td>
                </tr>
                <tr>
                    <td>No. Rekening Tujuan</td>
                    <td><?= strtoupper($d['norek']) ?></td>
                </tr>
                <tr>
                    <td>Dari Rekening</td>
                    <td><?= strtoupper($d['rekening_sumber']) ?></td>
                </tr>
                <tr>
                    <td>Nominal</td>
                    <td>IDR <?= number_format($d['jumlah'], 2)  ?></td>
                </tr>
                <tr>
                    <td>Biaya Admin</td>
                    <td>IDR <?= number_format($d['biaya_trf'], 2)  ?></td>
                </tr>
                <tr>
                    <td>Layanan Transfer</td>
                    <td>
                        <?php
                        if ($d['kode_bank'] == 'CENAIDJA') echo 'Online';
                        else echo 'LLG';
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>Jenis Transaksi</td>
                    <td>Transfer ke <?= $d['bank'] ?></td>
                </tr>
                <tr>
                    <td>Berita Transfer</td>
                    <td><?= strtoupper($d['berita_transfer']) ?></td>
                </tr>
                <tr>
                    <td>No. Referensi</td>
                    <td class="blue-text"><?= $d['transfer_req_id'] ?></td>
                </tr>
            </table>
        </section>
    </div>
</body>

</html>