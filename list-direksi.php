<?php
// error_reporting(0);
session_start();

require "application/config/database.php";

$con = new Database();
$koneksi = $con->connect();

if (!isset($_SESSION['nama_user'])) {
    header("location:login.php");
    // die('location:login.php');//jika belum login jangan lanjut
}

$year = (int)date('Y');
$subTab = ['B1', 'B2', 'Umum'];
$subTabUmum = ['Rutin', 'Non-Rutin']

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
    <!-- DataTables -->
    <link rel="stylesheet" href="datatables/dataTables.bootstrap.css">

    <style>
        iframe {
            width: 1px;
            min-width: 100%;
            *width: 100%;
        }

        /*Hidden class for adding and removing*/
        .lds-dual-ring.hidden {
            display: none;
        }

        /*Add an overlay to the entire page blocking any further presses to buttons or other elements.*/
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, .8);
            z-index: 999;
            opacity: 1;
            transition: all 0.5s;
        }

        /*Spinner Styles*/
        .lds-dual-ring {
            display: inline-block;
            width: 100%;
            height: 100%;
        }

        .lds-dual-ring:after {
            content: " ";
            display: block;
            width: 64px;
            height: 64px;
            margin: 5% auto;
            border-radius: 50%;
            border: 6px solid #fff;
            border-color: #fff transparent #fff transparent;
            animation: lds-dual-ring 1.2s linear infinite;
        }

        @keyframes lds-dual-ring {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>

    <!-- </head> -->

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
                    <li class="active"><a href="list-direksi.php">List</a></li>
                    <li><a href="saldobpu.php">Saldo BPU</a></li>
                    <!--<li><a href="summary.php">Summary</a></li>-->
                    <!-- <li><a href="hak-akses.php">Hak Akses</a></li> -->
                    <li><a href="listfinish-direksi.php">Budget Finish</a></li>
                    <!-- <li><a href="history-direksi.php">History</a></li> -->
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Rekap
                            <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="rekap-finance.php">Ready To Paid (MRI Kas)</a></li>
                            <li><a href="rekap-finance-mripal.php">Ready To Paid (MRI PAL)</a></li>
                            <li><a href="belumrealisasi.php">Belum Realisasi</a></li>
                            <li><a href="cashflow.php">Cash Flow</a></li>
                        </ul>
                    </li>

                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Transfer
                            <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="laporan-transfer.php">Laporan Transfer</a></li>
                            <li><a href="antrian-transfer.php">Antrian Transfer</a></li>
                        </ul>
                    </li>
                </ul>
                <?php
                
                ?>
               <ul class="nav navbar-nav navbar-right">
                        <li><a href="notif-page.php"><i class="fa fa-envelope"></i></a></li>
                    <li><a href="ubahpassword.php"><span class="glyphicon glyphicon-user"></span><?php echo $_SESSION['nama_user']; ?> (<?php echo $_SESSION['divisi']; ?>)</a></li>
                    <li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>


    <div class="container">

        <!-- Nav List budget 2018 - 2019 -->
        <!-- <ul class="nav nav-pills">
            <li class="active"><a data-toggle="pill" href="#2021">List Budget 2021</a></li>
            <li><a data-toggle="pill" href="#2020">List Budget 2020</a></li>
            <li><a data-toggle="pill" href="#menu1">List Budget 2019</a></li>
            <li><a data-toggle="pill" href="#list2018">List Budget 2018</a></li>
            <li><a data-toggle="pill" href="#menu2">UM Burek, Honor SHP PWT, STKB</a></li>
        </ul> -->
        <div id="loader" class="lds-dual-ring hidden overlay"></div>

        <ul class="nav nav-pills">
            <ul class="nav nav-tabs">
                <?php for ($i = $year; $i > $year - 4; $i--) :
                    if ($i == $year) : ?>
                        <li class="active"><a data-toggle="pill" href="#<?= $i ?>" class="tab-year-button">List Budget <?= $i ?></a></li>
                    <?php else : ?>
                        <li><a data-toggle="pill" href="#<?= $i ?>" class="tab-year-button">List Budget <?= $i ?></a></li>
                    <?php endif; ?>
                <?php endfor; ?>
                <li class="umbrek"><a data-toggle="pill" href="#menu2">UM Burek, Honor SHP PWT, STKB</a></li>
                <li class="uangMuka"> <a data-toggle="pill" href="#uangmuka2021">Rekap Monitoring Uang Muka</a></li=>
            </ul>
        </ul>

        <div class="tab-content">

            <?php for ($i = $year; $i > $year - 4; $i--) : ?>

                <div id="<?= $i ?>" class="tab-pane fade <?= ($i == $year) ? "active in" : "" ?>">
                    <ul id="myTab" class="nav nav-tabs" role="tablist">
                        <?php for ($j = 0; $j < count($subTab); $j++) : ?>
                            <li class="<?= ($j == 0) ? "active" : "" ?>" role="presentation">
                                <a href="#<?= $subTab[$j] ?>-<?= $i ?>" id="<?= $subTab[$j] ?>-tab" role="tab" data-toggle="tab" aria-controls="<?= $subTab[$j] ?>" aria-expanded="true" class="<?= ($subTab[$j] == 'Umum') ? "umum-button" : "end-button" ?>">Folder <?= $subTab[$j] ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>

                    <div id="myTabContent" class="tab-content">
                        <?php for ($j = 0; $j < count($subTab); $j++) : ?>
                            <div role="tabpanel" class="tab-pane fade <?= ($j == 0) ? "active in" : "" ?>" id="<?= $subTab[$j] . '-' . $i ?>" aria-labelledby="home-tab">

                                <?php if ($subTab[$j] == 'Umum') : ?>
                                    <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
                                        <div class="panel-body no-padding">
                                            <ul class="nav nav-tabs">
                                                <?php for ($k = 0; $k < count($subTabUmum); $k++) : ?>
                                                    <li class="<?= ($k == 0) ? "active" : "" ?>"><a href="#<?= $subTabUmum[$k] . '-' . $i ?>" class="end-button"><?= $subTabUmum[$k] ?></a>
                                                    </li>
                                                <?php endfor; ?>
                                            </ul>

                                            <div class="tab-content">
                                                <?php for ($k = 0; $k < count($subTabUmum); $k++) : ?>
                                                    <div class="tab-pane fade <?= ($k == 0) ? "active in" : "" ?>" id="<?= $subTabUmum[$k] . '-' . $i ?>">
                                                        <div class="tab-content tab-fetched-data"></div>
                                                    </div>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php else : ?>
                                    <div class="tab-content tab-fetched-data"></div>
                                <?php endif; ?>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            <?php endfor; ?>


            <div id="menu2" class="tab-pane fade">

                <ul id="myTab" class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#umburek" role="tab" id="umburek-tab" data-toggle="tab" aria-controls="umburek">UM Burek</a>
                    </li>
                    <li role="presentation">
                        <a href="#honor" role="tab" id="honor-tab" data-toggle="tab" aria-controls="honor" class="header-btn-honor">Honor SHP dan PWT</a>
                    </li>
                    <li role="presentation">
                        <a href="#stkb" role="tab" id="stkb-tab" data-toggle="tab" aria-controls="stkb" class="header-btn-stkb">STKB</a>
                    </li>

                    <div id="myTabContent" class="tab-content">
                        <!-- Tab -->

                        <!-- UM BUREK -->
                        <div role="tabpanel" class="tab-pane fade in active" id="umburek" aria-labelledby="umburek-tab">
                            <?php
                            // include "listdireksi/umburek.php";
                            ?>
                            <div class="tab-content content-umbrek">Sedang Mengambil data...</div>
                        </div>
                        <!-- //UM BUREK -->

                        <!-- Honor SHP PWT -->
                        <div role="tabpanel" class="tab-pane fade" id="honor" aria-labelledby="honor-tab">
                            <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
                                <div class="panel-body no-padding">

                                    <br><br>

                                    <ul class="nav nav-tabs">
                                        <?php for ($i = $year; $i > $year - 2; $i--) :
                                            if ($i == $year) :
                                        ?><li class="active"><a href="#honor<?= $i ?>" class="btn-honor"><?= $i ?></a></li>
                                            <?php else : ?>
                                                <li><a href="#honor<?= $i ?>" class="btn-honor"><?= $i ?></a></li>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </ul>

                                    <div class="tab-content honor-fetched-data">
                                    </div>

                                </div><!-- /.table-responsive -->
                            </div>
                        </div>
                        <!-- //Honor SHP PWT -->

                        <!-- STKB -->
                        <div role="tabpanel" class="tab-pane fade" id="stkb" aria-labelledby="stkb-tab">
                            <div class="panel panel-warning" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static="">
                                <div class="panel-body no-padding">

                                    <br><br>

                                    <ul class="nav nav-tabs">
                                        <?php for ($i = $year; $i > $year - 2; $i--) :
                                            if ($i == $year) :
                                        ?>
                                                <li class="active"><a href="#stkb<?= $i ?>" class="btn-stkb"><?= $i ?></a></li>
                                            <?php else : ?>
                                                <li><a href="#stkb<?= $i ?>" class="btn-stkb"><?= $i ?></a></li>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </ul>

                                    <div class="tab-content stkb-fetched-data">
                                    </div>
                                </div><!-- /.table-responsive -->
                            </div>
                        </div>
                        <!-- //STKB -->

                    </div>
            </div>

            <div role="tabpanel" class="tab-pane fade" id="uangmuka2021" aria-labelledby="uangmuka-tab">
                <?php
                // include "listdireksi/uangmuka-2021.php";
                ?>
                <div class="tab-content content-uang-muka">Sedang Mengambil data...</div>
            </div>
        </div>
    </div><!-- Content Nav -->
    <!--Container -->

    <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Persetujuan Budget</h4>
                </div>
                <div class="modal-body">
                    <div class="fetched-data"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="myModal2" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Hapus Budget</h4>
                </div>
                <div class="modal-body">
                    <div class="fetched-data"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="myModal3" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Finish Budget</h4>
                </div>
                <div class="modal-body">
                    <div class="fetched-data"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="myModal4" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">BPU UM Burek</h4>
                </div>
                <div class="modal-body">
                    <div class="fetched-data"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="myModal5" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Persetujuan BPU UM Burek</h4>
                </div>
                <div class="modal-body">
                    <div class="fetched-data"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="myModal6" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Dissapprove</h4>
                </div>
                <div class="modal-body">
                    <div class="fetched-data"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Keluar</button>
                </div>
            </div>
        </div>
    </div>


    <script type="text/javascript">
        // setTimeout(function() {
            $(document).ajaxStart(function() {
            // $('#loader').removeClass('hidden');
            $('.tab-fetched-data').html('<p>Sedang mengambil...</p>');
            }).ajaxSuccess(function() {
                // $('#loader').addClass('hidden');
            });
        // }, 500)

        const element = $('li.active .end-button').first();
        // console.log(element.attr('href'));

        if (!element.attr('href').includes('Umum')) {
            const href = element.attr('href').split('-');
            const tahun = href[href.length - 1];
            const tab = href[0];
            $.ajax({
                type: 'post',
                url: 'ajax/ajax-tab-listdireksi.php',
                data: {
                    tahun: tahun,
                    tab: tab
                },
                success: function(data) {
                    // console.log(data);
                    $('.tab-fetched-data').html(data);
                }
            });
        }

        $('.uangMuka').click(function() {
            $.ajax({
                type: 'get',
                url: 'listdireksi/uangmuka-2021.php',
                success: function(data) {
                    // console.log(data);
                    $('.content-uang-muka').html(data);
                }
            });
            })

            $('.umbrek').click(function() {
            $.ajax({
                type: 'get',
                url: 'listdireksi/umburek.php',
                success: function(data) {
                    // console.log(data);
                    $('.content-umbrek').html(data);
                }
            });
            })

        $(document).ready(function() {
            $('.end-button').click(function() {
                if (!$(this).attr('href').includes('Umum')) {
                    const href = $(this).attr('href').split('-');
                    const tahun = href[href.length - 1];
                    const tab = href[0];
                    $.ajax({
                        type: 'post',
                        url: 'ajax/ajax-tab-listdireksi.php',
                        data: {
                            tahun: tahun,
                            tab: tab
                        },
                        success: function(data) {
                            // console.log(data);
                            $('.tab-fetched-data').html(data);
                        }
                    });
                }
            })

            $('.tab-year-button').click(function() {
                // console.log($('.tab-pane.fade.active.in li.active a.end-button'))
                tahun = $(this).attr('href').substring(1);
                tab = 'B1';
                $.ajax({
                    type: 'post',
                    url: 'ajax/ajax-tab-listdireksi.php',
                    data: {
                        tahun: tahun,
                        tab: tab
                    },
                    success: function(data) {
                        // console.log(data);
                        $('.tab-fetched-data').html(data);
                    }
                });
            })

            $('.umum-button').click(function() {
                // console.log($('.tab-pane.fade.active.in li.active a.end-button'))
                const href = $(this).attr('href').split('-');
                const tahun = href[href.length - 1];
                const tab = 'Rutin';
                $.ajax({
                    type: 'post',
                    url: 'ajax/ajax-tab-listdireksi.php',
                    data: {
                        tahun: tahun,
                        tab: tab
                    },
                    success: function(data) {
                        // console.log(data);
                        $('.tab-fetched-data').html(data);
                    }
                });
            })
        })

        $(document).ready(function() {
            $('.btn-honor').click(function() {
                // console.log($(this).text());
                const year = $(this).text();
                $.ajax({
                    type: 'post',
                    url: 'ajax/ajax-honor.php',
                    data: {
                        year: year
                    },
                    success: function(data) {
                        $('.honor-fetched-data').html(data);
                    }
                });
            })

            $('.header-btn-honor').click(function() {
                const year = <?= $year ?>;
                $.ajax({
                    type: 'post',
                    url: 'ajax/ajax-honor.php',
                    data: {
                        year: year
                    },
                    success: function(data) {
                        $('.honor-fetched-data').html(data);
                    }
                });
            })

            $('.header-btn-stkb').click(function() {
                // console.log($(this).text());
                const year = '<?= $year ?>';
                $.ajax({
                    type: 'post',
                    url: 'ajax/ajax-stkb.php',
                    data: {
                        year: year
                    },
                    success: function(data) {
                        $('.stkb-fetched-data').html(data);
                    }
                });
            })

            $('.btn-stkb').click(function() {
                // console.log($(this).text());
                const year = $(this).text();
                $.ajax({
                    type: 'post',
                    url: 'ajax/ajax-stkb.php',
                    data: {
                        year: year
                    },
                    success: function(data) {
                        $('.stkb-fetched-data').html(data);
                    }
                });
            })

            $('#myModal').on('show.bs.modal', function(e) {
                var rowid = $(e.relatedTarget).data('id');
                //menggunakan fungsi ajax untuk pengambilan data
                $.ajax({
                    type: 'post',
                    url: 'approve.php',
                    data: 'rowid=' + rowid,
                    success: function(data) {
                        $('.fetched-data').html(data); //menampilkan data ke dalam modal
                    }
                });
            });
        });

        $(document).ready(function() {
            $('#myModal2').on('show.bs.modal', function(e) {
                var rowid = $(e.relatedTarget).data('id');
                //menggunakan fungsi ajax untuk pengambilan data
                $.ajax({
                    type: 'post',
                    url: 'hapuslist.php',
                    data: 'rowid=' + rowid,
                    success: function(data) {
                        $('.fetched-data').html(data); //menampilkan data ke dalam modal
                    }
                });
            });
        });

        $(document).ready(function() {
            $('#myModal3').on('show.bs.modal', function(e) {
                var rowid = $(e.relatedTarget).data('id');
                //menggunakan fungsi ajax untuk pengambilan data
                $.ajax({
                    type: 'post',
                    url: 'finish.php',
                    data: 'rowid=' + rowid,
                    success: function(data) {
                        $('.fetched-data').html(data); //menampilkan data ke dalam modal
                    }
                });
            });
        });

        function bpu_um(iduser) {
            // alert(noid+' - '+waktu);
            $.ajax({
                type: 'post',
                url: 'bpuum.php',
                data: {
                    iduser: iduser
                },
                success: function(data) {
                    $('.fetched-data').html(data); //menampilkan data ke dalam modal
                    $('#myModal4').modal();
                }
            });
        }

        function edit_budget(term, namapenerima) {
            // alert(noid+' - '+waktu);
            $.ajax({
                type: 'post',
                url: 'setuju_um.php',
                data: {
                    term: term,
                    namapenerima: namapenerima
                },
                success: function(data) {
                    $('.fetched-data').html(data); //menampilkan data ke dalam modal
                    $('#myModal5').modal();
                }
            });
        }

        $(document).ready(function() {
            $('#myModal6').on('show.bs.modal', function(e) {
                var rowid = $(e.relatedTarget).data('id');
                //menggunakan fungsi ajax untuk pengambilan data
                $.ajax({
                    type: 'post',
                    url: 'disapprove.php',
                    data: 'rowid=' + rowid,
                    success: function(data) {
                        $('.fetched-data').html(data); //menampilkan data ke dalam modal
                    }
                });
            });
        });

        $(document).ready(function() {
            $(".nav-tabs a").click(function() {
                $(this).tab('show');
            });
        });

        // $('#B2').load('listdireksi/b2-2018.php');
        // $('#rutin').load('listdireksi/rutin-2018.php');
        // $('#nonrutin').load('listdireksi/nonrutin-2018.php');
        // $('#B22019').load('listdireksi/b2-2019.php');
        // $('#rutin2019').load('listdireksi/rutin-2019.php');
        // $('#nonrutin2019').load('listdireksi/nonrutin-2019.php');
        // $('#honor').load('listdireksi/honor.php');
        // $('#stkb').load('listdireksi/stkb.php');
    </script>


    <!-- </body></html> -->

</body>

</html>