<?php
session_start();
require_once "application/config/database.php";
require_once "application/config/whatsapp.php";
require_once "application/config/message.php";

class ValidasiBudget extends Database
{
    protected $koneksi;
    protected $notifPenerima = "";
    public function __construct($fromInit = true)
    {
        parent::__construct($fromInit);
        $this->koneksi = $this->connect();
        $this->load_database($this->koneksi);
    }

    public function validasi_budget()
    {
        $isUpdated = $this->update_submission();
        if (!$isUpdated) {
            $message = "Budget tidak dapat di validasi, terjadi kesalahan ketika menyimpan data. Moho coba lagi, jika masih terjaid masalah yang sama silahkan hubungi Tim IT.";
            $this->alert($message);
        }

        $this->send_notification();
        $message = "Budget telah di validasi oleh $_SESSION[nama_user] dan notifikasi telah dikirimkan ke " . $this->notifPenerima;
        $this->alert($message);
    }

    protected function update_submission()
    {
        $id = $_GET["id"];
        $keterangan = $_GET["keterangan"];
        $validator = $_SESSION["nama_user"];

        return $this->update("pengajuan_request")->set_value_update("status_request", "Di Ajukan")
            ->set_value_update("on_revision_status", 1)
            ->set_value_update("validator", $validator)
            ->set_value_update("ket", $keterangan)
            ->where("id", "=", $id)->save_update();
    }

    protected function send_notification()
    {
        $whatsapp = new Whastapp();
        $message = new Message();
        $host = $this->host_url();
        $id = $_GET["id"];

        $dataSubmission = $this->get_submission();
        $direksi = $this->select("*")->from("tb_user")->where("divisi", "=", "Direksi")->where("aktif", "=", "Y")->first();
        $creator = $this->select("*")->from("tb_user")->where("nama_user", "=", $dataSubmission["pembuat"])->first();
        $pengaju = $this->select("*")->from("tb_user")->where("nama_user", "=", $dataSubmission["pengaju"])->first();

        $url =  $host. '/view-request.php?id='.$id.'&session='.base64_encode(json_encode(["id_user" => $direksi["id_user"], "timeout" => time()]));
        $msg = $message->messageValidasiBudget($direksi["nama_user"], $dataSubmission, $_SESSION["nama_user"], $url);
        $whatsapp->sendMessage($direksi["phone_number"], $msg);
        $this->notifPenerima .= sprintf("%s (%s),", $direksi["nama_user"], $direksi["phone_number"]);

        $url =  $host. '/view-request.php?id='.$id.'&session='.base64_encode(json_encode(["id_user" => $creator["id_user"], "timeout" => time()]));
        $msg = $message->messageValidasiBudget($creator["nama_user"], $dataSubmission, $_SESSION["nama_user"], $url);
        $whatsapp->sendMessage($creator["phone_number"], $msg);
        $this->notifPenerima .= sprintf("%s (%s),", $creator["nama_user"], $creator["phone_number"]);

        $url =  $host. '/view-request.php?id='.$id.'&session='.base64_encode(json_encode(["id_user" => $pengaju["id_user"], "timeout" => time()]));
        $msg = $message->messageValidasiBudget($pengaju["nama_user"], $dataSubmission, $_SESSION["nama_user"], $url);
        $whatsapp->sendMessage($pengaju["phone_number"], $msg);
        $this->notifPenerima .= sprintf("%s (%s)", $pengaju["nama_user"], $pengaju["phone_number"]);

    }

    protected function get_submission()
    {
        return $this->select("*")->from("pengajuan_request")->where("id", "=", $_GET["id"])->first();
    }

    protected function host_url()
    {
        $url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        $port = $_SERVER['SERVER_PORT'];
        $url = explode('/', $url);
        $hostProtocol = $url[0];
        if ($port != "" || $port != "80") {
            $hostProtocol = $hostProtocol . ":" . $port;
        }
        return $hostProtocol. '/'. $url[1];
    }

    protected function alert($notification)
    {
        echo "<script language='javascript'>
            alert('$notification!!')
             document.location.href='".$_SERVER['HTTP_REFERER']."'
      </script>";
    }
}

$validasi = new ValidasiBudget();
$validasi->validasi_budget();