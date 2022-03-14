<?php
require_once "application/config/database.php";
session_start();

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

    public function validate_receiver()
    {
        $save = $this->update("tb_penerima")
            ->set_value_update("is_validate", true)
            ->set_value_update("validator", $_SESSION["nama_user"])
            ->where("id", "=", $_GET["id"]);
        return $save->save_update();
    }

    public function get_receiver_validate()
    {
        return $this->select("*")->from("tb_penerima")->where("is_validate", "=", true)->where("item_id", "=", $_GET["itemId"])->get();
    }

    public function get_receiver_all()
    {
        return $this->select("*")->from("tb_penerima")->where("item_id", "=", $_GET["itemId"])->get();
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
        echo json_encode(["message" => "Berhasil menambahkan data penerima. Data penerima akan divalidasi oleh Manager Finance"]);
    } else {
        echo json_encode(["message" => "Data gagal ditambahkan, Coba lagi!. Jika masih menemukan kesalahan yang sama, informasikan pada tim IT."]);
    }
}

if ($action == "getReceiver") {
    echo json_encode($receiver->get_receiver_validate());
}

if ($action == "validate") {
    $isSaved = $receiver->validate_receiver();
    if ($isSaved) {
        echo json_encode(["message" => "Berhasil memvalidasi data penerima"]);
    } else {
        echo json_encode(["message" => "Data gagal divalidasi, Coba lagi!. Jika masih menemukan kesalahan yang sama, informasikan pada tim IT."]);
    }
}