<?php
session_start();
require_once "application/config/database.php";
require_once "application/config/whatsapp.php";
class PengajuanUangKas extends Database
{
    protected $koneksi;
    protected $whatsapp;
    public function __construct($fromInit = true)
    {
        parent::__construct($fromInit);
        $this->koneksi = $this->connect();
        $this->load_database($this->koneksi);
        $this->whatsapp = new Whastapp();
    }

    public function formPengajuan()
    {
        $dataPengajuan = $this->select("*")->from('pengajuan')->where('noid', '=', $_GET['code'])->first();
        $items = $this->select()->where('waktu', '=', $dataPengajuan['waktu'])->get('selesai');
        require_once "form/pengajuan-uang-kas.php";
    }

    public function createRequest()
    {
        try {
            foreach ($_POST['dataValueList'] as $value) {
                $explode = explode(";", $value);
                $this->insert('pengajuan_kas_item')->set_value_insert('id_pengajuan_budget', $_GET['code'])
                    ->set_value_insert('item_id', $explode[0])
                    ->set_value_insert('total_pengajuan', $explode[6])->save_insert();
            }
            echo json_encode(['status' => true]);
        } catch (Exception $exception) {
            echo json_encode(['status' => false]);
        }
    }

    public function createPengajuan()
    {
        try {
            $dataPengajuan = $this->select()->from('pengajuan_kas')->where('id_pengajuan_budget', '=', $_GET['code'])->first();
            $lastStep = 11;
            $status = 11;
            if ($dataPengajuan['last_step'] != '') {
                $lastStep = $dataPengajuan['last_step'];
                $status = $dataPengajuan['last_step'];
            }
            $this->update('pengajuan_kas')
                ->set_value_update('status', $status)
                ->set_value_update('last_step', $lastStep)
                ->set_value_update('created_by', $_SESSION['nama_user'])
                ->where('id_pengajuan_budget', '=', $_GET['code'])->save_update();

            $currentFlow = $this->select()->from('flow_pengajuan_kas')->where('status_code', '=', $status)->first();
            $userNext = $this->select()->from('tb_user')->where('id_user', '=', $currentFlow['pic'])->first();
            $this->whatsapp->sendMessage($userNext['phone_number'], $this->messageRequestPeresetujuanPengajuan($userNext['nama_user'], $currentFlow['flow_name']));
            $creator = $this->select()->from('tb_user')->where('nama_user', '=', $dataPengajuan['created_by'])->first();
            $this->whatsapp->sendMessage($creator['phone_number'], $this->messageNextPengajuan($dataPengajuan['created_by'], $userNext['nama_user'], $currentFlow['flow_name']));
            echo json_encode(['status' => true, 'query' => $this->get_query()]);
        } catch (Exception $exception) {
            echo json_encode(['status' => false]);
        }
    }

    public function rejectPengajuan()
    {
        try {
            $dataPengajuan = $this->select()->from('pengajuan_kas')->where('id_pengajuan_budget', '=', $_GET['code'])->first();
            $this->update('pengajuan_kas')
                ->set_value_update('status', $dataPengajuan['status'] . '0')
                ->set_value_update('reject_by', $_SESSION['nama_user'])
                ->set_value_update('description', $_POST['description'])
                ->where('id_pengajuan_budget', '=', $_GET['code'])->save_update();

            $creator = $this->select()->from('tb_user')->where('nama_user', '=', $dataPengajuan['created_by'])->first();
            $resp = $this->whatsapp->sendMessage($creator['phone_number'], $this->messagePenolakan($dataPengajuan['created_by'], $dataPengajuan['description']));
            echo json_encode(['status' => true, 'query' => $this->get_query(), 'resp_wa' => $resp]);
        } catch (Exception $exception) {
            echo json_encode(['status' => false]);
        }
    }

    public function validationPengajuan()
    {
        try {
            $dataPengajuan = $this->select('a.*, b.flow_name, b.sequence')->from('pengajuan_kas a')->join('flow_pengajuan_kas b', 'a.status = b.status_code')->where('a.id_pengajuan_budget', '=', $_GET['code'])->first();
            $nextStatus = $dataPengajuan['status'];
            $currentFlow = $this->select()->from('flow_pengajuan_kas')->where('status_code', '=', $dataPengajuan['status'])->first();
            if ($dataPengajuan['status'] == 33) $nextStatus = 1;
            if ($dataPengajuan['status'] != 33 && $dataPengajuan['status'] != 1) {
                $flow = $this->select()->from('flow_pengajuan_kas')->where('sequence', '=', $dataPengajuan['sequence'] + 1)->first();
                $nextStatus = $flow['status_code'];
            }
            $this->update('pengajuan_kas')
                ->set_value_update('status', $nextStatus)
                ->set_value_update('last_step', $nextStatus)
                ->set_value_update($currentFlow['as'], $_SESSION['nama_user'])
                ->where('id_pengajuan_budget', '=', $_GET['code'])->save_update();

            if ($nextStatus != 1) {
                $userNext = $this->select()->from('tb_user')->where('id_user', '=', $flow['pic'])->first();
                $this->whatsapp->sendMessage($userNext['phone_number'], $this->messageRequestPeresetujuanPengajuan($userNext['nama_user'], $flow['flow_name']));
                $creator = $this->select()->from('tb_user')->where('nama_user', '=', $dataPengajuan['created_by'])->first();
                $this->whatsapp->sendMessage($creator['phone_number'], $this->messageNextPengajuan($dataPengajuan['created_by'], $userNext['nama_user'], $flow['flow_name']));
            } else {
                $flow = $this->select()->from('flow_pengajuan_kas')->where('status_code', '=', $nextStatus)->first();
                $creator = $this->select()->from('tb_user')->where('nama_user', '=', $dataPengajuan['created_by'])->first();
                $this->whatsapp->sendMessage($creator['phone_number'], $this->messageNextPengajuan($dataPengajuan['created_by'], $_SESSION['nama_user'], $flow['flow_name']));
            }
            echo json_encode(['status' => true, 'query' => $this->get_query()]);
        } catch (Exception $exception) {
            echo json_encode(['status' => false]);
        }
    }

    public function messagePenolakan($creator, $description)
    {
        $description = isset($description) ? $description : "-";
        $pengajuan = $this->select()->from('pengajuan')->where('noid', '=', $_GET['code'])->first();

        return "
Dear $creator
Pengajuan pengisian Kas *$pengajuan[nama]* telah *DITOLAK* oleh *$_SESSION[nama_user]* dengan keterangan $description
        
Silahkan lakukan perubahan sebelum diajukan kembali
Terimakasih
        ";
    }

    public function messageRequestPeresetujuanPengajuan($nextUser, $status)
    {
        $pengajuan = $this->select()->from('pengajuan')->where('noid', '=', $_GET['code'])->first();

        return "
Dear $nextUser
Pengajuan pengisian Kas *$pengajuan[nama]* dalam status *$status*

Silahkan lakukan proses selanjutnya
Terimakasih
        ";
    }

    public function messageNextPengajuan($creator, $nextUser, $status)
    {
        $pengajuan = $this->select()->from('pengajuan')->where('noid', '=', $_GET['code'])->first();

        return "
Dear $creator
Pengajuan pengisian Kas *$pengajuan[nama]* anda *$status* oleh *$nextUser*

Terimakasih
        ";
    }
}

$pengajuan = new PengajuanUangKas();
if ($_GET['action'] == 'formPengajuan') $pengajuan->formPengajuan();
if ($_GET['action'] == 'createRequest') $pengajuan->createRequest();
if ($_GET['action'] == 'createRequestPengajuan') $pengajuan->createPengajuan();
if ($_GET['action'] == 'rejectRequest') $pengajuan->rejectPengajuan();
if ($_GET['action'] == 'acceptRequest') $pengajuan->validationPengajuan();

