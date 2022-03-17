<?php
require_once "application/config/database.php";
require_once "application/config/whatsapp.php";
require_once "application/config/message.php";
session_start();

class ReceiverBpu extends Database
{
    protected $koneksi;
    public $dataPenerima;
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

    public function handle_upload_file()
    {
        $extension = pathinfo($_FILES["document"]["name"], PATHINFO_EXTENSION);
        $nama_gambar = time() . "." . $extension;
        $target_file = "uploads/" . $nama_gambar;
        $isUploaded = move_uploaded_file($_FILES["document"]["tmp_name"], $target_file);
        return ["isUploaded" => $isUploaded, "path" => $target_file];
    }

    public function send_notification_to_user()
    {
        $wa = new Whastapp();
        $msg = new Message();

        $dataPengajuan = $this->select("a.*, b.rincian, c.pengaju, c.pembuat")->from("tb_penerima a")
            ->join("selesai b", "a.item_id = b.id")->join("pengajuan c", "b.waktu = c.waktu")
            ->where("a.id", "=", $_GET["id"])->first();
        $penerimaNotif = [];
        $userPengaju = $this->get_user_by_name($dataPengajuan["pengaju"]);
        $userPembuat = $this->get_user_by_name($dataPengajuan["pembuat"]);

        $penerimaNotif[] = $userPengaju;

        if ($userPembuat["nama_user"] != $_SESSION["nama_user"]) {
            $penerimaNotif[] = $userPembuat;
        }

        foreach ($penerimaNotif as $key => $value) {
            $this->dataPenerima[] = $value["nama_user"];
            $wa->sendMessage($value["phone_number"], $msg->messageValidasiPenerimaBpu($value["nama_user"], $dataPengajuan, $dataPengajuan["rincian"], $_SESSION["nama_user"]));
        }
    }

    public function get_user_by_name($name)
    {
        return $this->select("*")->from("tb_user")->where("nama_user", "=", $name)->first();
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
        $receiver->send_notification_to_user();
        $penerimaNotif = implode(", ", $receiver->dataPenerima);
        echo json_encode(["message" => "Berhasil memvalidasi data penerima. Pemberitahuan telha dikirimkan ke $penerimaNotif melalui pesan whatsapp"]);
    } else {
        echo json_encode(["message" => "Data gagal divalidasi, Coba lagi!. Jika masih menemukan kesalahan yang sama, informasikan pada tim IT."]);
    }
}

if ($action == "uploadDocument") {
    $upload = $receiver->handle_upload_file();
    if ($upload["isUploaded"]) {
        echo json_encode(["message" => "Berhasil menambahkan dokumen", "path" => $upload["path"]]);
    } else {
        echo json_encode(["message" => "Gagal menambahkan dokumen, Coba lagi!. Jika masih menemukan kesalahan yang sama, informasikan pada tim IT."]);
    }
}