<?php

require_once "../application/config/database.php";
require_once "../application/config/message.php";

class Wewenang extends Database
{
    protected $koneksi;
    public function __construct($fromInit = true)
    {
        parent::__construct($fromInit);
        $this->koneksi = $this->connect();
        $this->load_database($this->koneksi);
    }

    public function create()
    {
        return $this->insert('tb_role_bpu')->set_value_insert('folder_name', $_POST['folderName'])
            ->set_value_insert('bpu', $_POST['jenisBpu'])
            ->set_value_insert('create_bpu', $_POST['creatorBpu'])
            ->set_value_insert('validate_bpu', $_POST['validatorBpu'])
            ->set_value_insert('approver_bpu', $_POST['approverBpu'])
            ->set_value_insert('condition', $_POST['kondisiBpu'])
            ->set_value_insert('value_condition', $_POST['valueKondisi'])
            ->set_value_insert('knowledge_bpu', $_POST['knowledgeBpu'])
            ->save_insert();
    }

    public function updateWewenang()
    {
        return $this->update('tb_role_bpu')->set_value_update('folder_name', $_POST['folderName'])
            ->set_value_update('bpu', $_POST['jenisBpu'])
            ->set_value_update('create_bpu', $_POST['creatorBpu'])
            ->set_value_update('validate_bpu', $_POST['validatorBpu'])
            ->set_value_update('approver_bpu', $_POST['approverBpu'])
            ->set_value_update('knowledge_bpu', $_POST['knowledgeBpu'])
            ->set_value_update('condition', $_POST['kondisiBpu'])
            ->set_value_update('value_condition', $_POST['valueKondisi'])
            ->where('id', '=', $_POST['id'])
            ->save_update();
    }
}

$wewenang = new Wewenang();
$message = new Message();
$previous = $_SERVER['HTTP_REFERER'];

if (isset($_POST['id'])) {
    $updated = $wewenang->updateWewenang();
    if ($updated) {
        echo $message->alertMessage("Berhasil mengubah data wewenang", $previous);
    } else {
        echo $message->alertMessage("Gagal mengubah data wewenang", $previous);
    }
} else {
    $issaved = $wewenang->create();
    if ($issaved) {
        echo $message->alertMessage("Berhasil nambahkan data wewenang", $previous);
    } else {
        echo $message->alertMessage("Gagal menambahkan data wewenang", $previous);
    }

}