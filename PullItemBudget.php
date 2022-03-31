<?php
require_once "application/config/database.php";
class PullItemBudget extends Database
{
    protected $koneksi;
    public function __construct($fromInit = true)
    {
        parent::__construct($fromInit);
        $this->koneksi = $this->connect();
        $this->load_database($this->koneksi);
    }

    public function pull_item_budget()
    {
        $waktu = $_POST["waktu"];
        $pengajuanRequest = $this->select("*")->from("pengajuan_request")->where("waktu", '=', $waktu)->first();
        $itemRequest = $this->select("*")->from("selesai_request")->where("id_pengajuan_request", '=', $pengajuanRequest["id"])->get();

        foreach ($itemRequest as $item) {
            $this->insert("selesai")
                ->set_value_insert("no", $item["urutan"])
                ->set_value_insert("rincian", $item["rincian"])
                ->set_value_insert("kota", $item["kota"])
                ->set_value_insert("status", $item["status"])
                ->set_value_insert("penerima", $item["penerima"])
                ->set_value_insert("harga", $item["harga"])
                ->set_value_insert("quantity", $item["quantity"])
                ->set_value_insert("total", $item["total"])
                ->set_value_insert("pengaju", $item["pengaju"])
                ->set_value_insert("divisi", $item["divisi"])
                ->set_value_insert("waktu", $waktu)->save_insert();
        }

        echo "OK";
    }
}

$pull = new PullItemBudget();
$pull->pull_item_budget();