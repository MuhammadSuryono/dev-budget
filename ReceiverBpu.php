<?php
require_once "application/config/database.php";

class ReceiverBpu extends Database
{
    protected $koneksi;
    public function __construct($fromInit = true)
    {
        parent::__construct($fromInit);
        $this->koneksi = $this->connect();
        $this->load_database($this->koneksi);
    }

    public function save_receiver()
    {
        $save = $this->insert("tb_penerima");
        foreach ($_POST as $key => $value) {
            $save->set_value_insert($key, $value);
        }

        return $save->save_insert();
    }

    public function get_receiver_validate()
    {
        return $this->select("tb_penerima")->where("is_validate", true)->where("item_id", $_GET["itemId"])->get();
    }

    public function get_receiver_all()
    {
        return $this->select("tb_penerima")->where("item_id", $_GET["itemId"])->get();
    }

    public function alert($message)
    {
        echo "<script language='javascript'>
            alert('$message')
             document.location.href='".$_SERVER['HTTP_REFERER']."'
      </script>";
    }
}

$receiver = new ReceiverBpu();
$action = $_GET["action"];

if ($action == "save") {
    $isSaved = $receiver->save_receiver();
    if ($isSaved) {
        $receiver->alert("Data berhasil disimpan");
    } else {
        $receiver->alert("Data gagal disimpan");
    }
}

if ($action == "getReceiver") {
    echo json_encode($receiver->get_receiver_validate());
}