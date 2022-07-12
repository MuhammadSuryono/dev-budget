<?php

require_once "application/config/database.php";
class PengajuanUangKas extends Database
{
    protected $koneksi;
    public function __construct($fromInit = true)
    {
        parent::__construct($fromInit);
        $this->koneksi = $this->connect();
        $this->load_database($this->koneksi);
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
}

$pengajuan = new PengajuanUangKas();
if ($_GET['action'] == 'formPengajuan') $pengajuan->formPengajuan();
if ($_GET['action'] == 'createRequest') $pengajuan->createRequest();

