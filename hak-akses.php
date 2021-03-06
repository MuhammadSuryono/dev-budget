<?php
error_reporting(0);
session_start();

if (!isset($_SESSION['nama_user'])) {
    header("location:login.php");
    // die('location:login.php');//jika belum login jangan lanjut
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Form Pengajuan Budget</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
</head>

<body>

    <nav class="navbar navbar-inverse">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="home-direksi.php">Budget-Ing</a>
            </div>
            <div class="collapse navbar-collapse" id="myNavbar">
                <ul class="nav navbar-nav">
                    <li><a href="home-direksi.php">Home</a></li>
                    <li><a href="list-direksi.php">List</a></li>
                    <li><a href="saldobpu.php">Saldo BPU</a></li>
                    <!--<li><a href="summary.php">Summary</a></li>-->
                    <li class="active"><a href="hak-akses.php">Hak Akses</a></li>
                    <li><a href="listfinish-direksi.php">Budget Finish</a></li>
                    <!-- <li><a href="history-direksi.php">History</a></li> -->
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Rekap
                            <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="rekap-finance.php">Ready To Paid</a></li>
                            <li><a href="belumrealisasi.php">Belum Realisasi</a></li>
                            <li><a href="cashflow.php">Cash Flow</a></li>
                        </ul>
                    </li>
                </ul>

                <?php
                
                ?>
               <ul class="nav navbar-nav navbar-right">
                        <li><a href="/log-notifikasi-aplikasi/index.html" target="_blank"><i class="fa fa-envelope"></i></a></li>
                    

                    <li><a href="ubahpassword.php"><span class="glyphicon glyphicon-user"></span><?php echo $_SESSION['nama_user']; ?> (<?php echo $_SESSION['divisi']; ?>)</a></li>
                    <li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <br /><br />

    <div class="container">

        <ul id="myTab" class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#B1" id="B1-tab" role="tab" data-toggle="tab" aria-controls="B1" aria-expanded="true">Data User Finance</a>
            </li>
        </ul>

        <div id="myTabContent" class="tab-content">
            <!-- Tab -->

            <div role="tabpanel" class="tab-pane fade in active" id="B1" aria-labelledby="home-tab">
                <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
                    <div class="panel-body no-padding">
                        <br>

                        <table class="table table-striped table-bordered">
                            <thead>
                                <th>#</th>
                                <th>Nama</th>
                                <th style="text-align: center;">Verifikasi BPU</th>
                                <th style="text-align: center;">Eksternal BPU</th>
                            </thead>

                            <tbody>
                                <?php
                                $i = 1;
                                $user = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE divisi = 'FINANCE' ORDER BY nama_user");
                                while ($a = mysqli_fetch_array($user)) {
                                    $buttonAkses = unserialize($a['hak_button']);
                                ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo $a['nama_user']; ?></td>
                                        <td style="text-align: center;"><input type="checkbox" name="" id="" data-id="<?= $a['id_user'] ?>" data-akses="verifikasi_bpu" <?= in_array("verifikasi_bpu", $buttonAkses) ? "checked" : "" ?>></td>
                                        <td style="text-align: center;"><input type="checkbox" name="" id="" data-id="<?= $a['id_user'] ?>" data-akses="eksternal_bpu" <?= in_array("eksternal_bpu", $buttonAkses) ? "checked" : "" ?>></td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- // Container -->


    <script type="text/javascript">
        $(document).ready(function() {
            // console.log('here');
            $('input[type=checkbox]').change(function() {
                const id = $(this).data('id');
                const akses = $(this).data('akses');
                const isChecked = $(this).is(':checked');

                // console.log(id);
                // console.log(akses);
                // console.log(isChecked);

                $.ajax({
                    url: "ajax/ajax-hak-akses.php",
                    type: 'post',
                    data: {
                        id: id,
                        akses: akses,
                        isChecked: isChecked
                    },
                    success: function() {
                        document.location.href = window.location.href;
                    }
                })
            })
        })
    </script>

</body>

</html>