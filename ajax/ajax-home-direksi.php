<?php

require "../application/config/database.php";
error_reporting(0);

$con = new Database();
$koneksi = $con->connect();

$con->set_host_db(DB_HOST_DIGITALISASI_MARKETING);
$con->set_name_db(DB_DIGITAL_MARKET);
$con->set_user_db(DB_USER_DIGITAL_MARKET);
$con->set_password_db(DB_PASS_DIGITAL_MARKET);
$con->init_connection();
$koneksiDigitalMarket = $con->connect();



$id = $_POST['id'];
$table = $_POST['table'];
$jenis = $_POST['jenis'];

$arrNameB1 = [];
$arrIdB1 = [];
$arrNameB2 = [];
$arrIdB2 = [];
$arrTableB1 = [];
$arrTableB2 = [];
if ($jenis) {
    $project = mysqli_query($koneksiDigitalMarket, "SELECT * FROM comm_voucher WHERE on_budget <> 1");
    while ($p = mysqli_fetch_assoc($project)) {
        $queryUser = mysqli_query($koneksiDigitalMarket, "SELECT * FROM data_user WHERE id_user = $p[research_executive]");
        $user = mysqli_fetch_assoc($queryUser);

        if ($user['dept'] == "76") {
            array_push($arrNameB1, $p['nama_project_internal']);
            array_push($arrIdB1, $p['id_comm_voucher']);
            array_push($arrTableB1, "comm_voucher");
        } else {
            array_push($arrNameB2, $p['nama_project_internal']);
            array_push($arrIdB2, $p['id_comm_voucher']);
            array_push($arrTableB2, "comm_voucher");
        }
    }

    $project = mysqli_query($koneksiDigitalMarket, "SELECT * FROM data_sindikasi");
    while ($p = mysqli_fetch_assoc($project)) {
        $onBudget = unserialize($p['on_budget']);
        $tipeProject = unserialize($p['id_methodology']);
        for ($i = 0; $i < count($tipeProject); $i++) {
            $singleMethod = $tipeProject[$i];
            if (@unserialize($p['on_budget'])) {
                if (!in_array($singleMethod, $onBudget)) {
                    $queryMethodology = mysqli_query($koneksiDigitalMarket, "SELECT methodology FROM data_methodology WHERE id_methodology = $singleMethod");
                    $methodology = mysqli_fetch_assoc($queryMethodology)['methodology'];
                    $queryUser = mysqli_query($koneksiDigitalMarket, "SELECT * FROM data_user WHERE id_user = $p[user_add]");
                    $user = mysqli_fetch_assoc($queryUser);
                    if ($user['dept'] == "76") {
                        array_push($arrNameB1, $p['nama_project'] . ' - ' .  $methodology);
                        array_push($arrIdB1, $p['id']);
                        array_push($arrTableB1, "data_sindikasi");
                    } else {
                        array_push($arrNameB2, $p['nama_project'] . ' - ' . $methodology);
                        array_push($arrIdB2, $p['id']);
                        array_push($arrTableB2, "data_sindikasi");
                    }
                }
            } else {
                $queryMethodology = mysqli_query($koneksiDigitalMarket, "SELECT methodology FROM data_methodology WHERE id_methodology = $singleMethod");
                $methodology = mysqli_fetch_assoc($queryMethodology)['methodology'];
                $queryUser = mysqli_query($koneksiDigitalMarket, "SELECT * FROM data_user WHERE id_user = $p[user_add]");
                $user = mysqli_fetch_assoc($queryUser);
                if ($user['dept'] == "76") {
                    array_push($arrNameB1, $p['nama_project'] . ' - ' .  $methodology);
                    array_push($arrIdB1, $p['id']);
                    array_push($arrTableB1, "data_sindikasi");
                } else {
                    array_push($arrNameB2, $p['nama_project'] . ' - ' . $methodology);
                    array_push($arrIdB2, $p['id']);
                    array_push($arrTableB2, "data_sindikasi");
                }
            }
        }
    }
    if ($jenis == 'B1') {
        $data = [
            'name' => $arrNameB1,
            'id' => $arrIdB1,
            'table' => $arrTableB1
        ];
    } else if ($jenis == 'B2') {
        $data = [
            'name' => $arrNameB2,
            'id' => $arrIdB2,
            'table' => $arrTableB2
        ];
    }
    echo json_encode($data);
    die;
}

if ($id) {
    if ($table == 'comm_voucher') {

        $queryCommVoucher = mysqli_query($koneksiDigitalMarket, "SELECT * FROM comm_voucher WHERE id_comm_voucher=$id");

        $commVoucher = mysqli_fetch_assoc($queryCommVoucher);

        $nama = $commVoucher['nama_project_internal'];

        $researchExecutive = $commVoucher['research_executive'];
        $queryUser = mysqli_query($koneksiDigitalMarket, "SELECT * FROM data_user WHERE id_user=$researchExecutive");
        $user = mysqli_fetch_assoc($queryUser);
        $idUserBudget = $user['id_user_budget'];

        if ($idUserBudget) {
            $queryUser = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE id_user='$idUserBudget'");
            $user = mysqli_fetch_assoc($queryUser);
            $pic = $user['nama_user'];
        } else {
            $idUserBudget = 0;
            $pic = 'Data Tidak ada';
        }

        $nomorProject = $commVoucher['nomor_project'];

        $queryDataRfq = mysqli_query($koneksiDigitalMarket, "SELECT tgl_masuk FROM data_rfq WHERE nomor_rfq='$nomorProject'");

        $dataRfq = mysqli_fetch_assoc($queryDataRfq);

        $tahun = date('Y', strtotime($dataRfq['tgl_masuk']));

        $data = [
            'pic' => $pic,
            'id_user' => $idUserBudget,
            'tahun' => $tahun,
            'nama' => $nama
        ];
    } else if ($table == 'data_sindikasi') {
        $querySindikasi = mysqli_query($koneksiDigitalMarket, "SELECT * FROM data_sindikasi WHERE id=$id");

        $sindikasi = mysqli_fetch_assoc($querySindikasi);

        $nama = $sindikasi['nama_project'];

        $idPic = $sindikasi['id_pic'];
        $queryUser = mysqli_query($koneksiDigitalMarket, "SELECT * FROM data_user WHERE id_user='$idPic'");
        $user = mysqli_fetch_assoc($queryUser);
        $idUserBudget = $user['id_user_budget'];

        if ($idUserBudget) {
            $queryUser = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE id_user='$idUserBudget'");
            $user = mysqli_fetch_assoc($queryUser);
            $pic = $user['nama_user'];
        } else {
            $idUserBudget = 0;
            $pic = 'Data Tidak ada';
        }

        $tahun = date('Y', strtotime($sindikasi['created_at']));

        $data = [
            'pic' => $pic,
            'id_user' => $idUserBudget,
            'tahun' => $tahun,
            'nama' => $nama
        ];
    }
    echo json_encode($data);
}
