<?php
require_once "../application/config/database.php";
require_once "../application/config/message.php";
require_once "../application/config/email.php";
require_once "../application/config/whatsapp.php";

class Callback extends Database {
    private $dataInput;
    private $koneksi;
    private $koneksiBridgeTransfer;
    private $dataTransfer;
    private $dataBpu;
    private $subjectEmail;
    private $dataInputResponse;
    private $phoneNumberReceiver;
    public function __construct()
    {
        $this->set_name_db(DB_APP);
        $this->init_connection();
        $this->koneksi = $this->connect();
        $this->load_database($this->koneksi);

        $this->set_name_db(DB_TRANSFER);
        $this->init_connection();
        $this->koneksiBridgeTransfer = $this->connect();

        $this->set_input_post_data();
    }

    public function callback_transfer() 
    {
        $isSuccessTransfer = $this->is_success_process_transfer();
        if ($isSuccessTransfer) {
            $this->get_data_transfer();
            $this->get_data_bpu();
            $this->send_email();
            $this->send_whatsapp();
        } else {
            var_dump("Error Transfer: ", $this->dataInput);
            var_dump("Error code: ", $this->dataInputResponse->ErrorCode);
        }
    }

    private function get_data_transfer()
	{
        $transferReqId = $this->dataInput['transfer_req_id'];
        $query = mysqli_query($this->koneksiBridgeTransfer, "SELECT * FROM data_transfer WHERE transfer_req_id = '$transferReqId'");
        $data = [];
        while ($row = mysqli_fetch_assoc($query)) {
            $data[] = $row;
        }
		$this->dataTransfer = $data[0];
	}

    private function set_input_post_data()
    {
        $this->dataInput = json_decode(file_get_contents('php://input'), true);
        $this->dataInputResponse = json_decode($this->dataInput['response']);
    }

    public function get_input()
    {
        return $this->dataInput;
    }

    private function is_success_process_transfer()
	{
		return $this->dataInputResponse->TransactionID == $this->dataInput['transfer_req_id'];
	}

    private function messageEmail()
    {
        $messageHelper = new Message();
        if ($this->dataBpu['statusbpu'] == "Vendor/Supplier") {
            $invoice = $this->dataBpu['ket_pembayaran'];
            $explodeInvoice = explode(".", $invoice);
            $numberInvoce = $explodeInvoice[1];
            
            $dateFormat = $explodeInvoice[2]; // [DATE]
            $day = $dateFormat[0].$dateFormat[1];
            $month = $dateFormat[2].$dateFormat[3];
            $year = "20".$dateFormat[4].$dateFormat[5];
            $dateInvoice = $year . "-" . $month . "-" . $day;

            $explodeTerm = explode("/", $explodeInvoice[3]);
            $startTerm = str_replace('T', '', $explodeTerm[0]); // [START TERM]
            $endTerm = $explodeTerm[1]; // [END TERM PEMBAYARAN]
            $ketPembayaran = $this->dataBpu['rincian']; // [KETERANGAN]
            $this->subjectEmail = "Laporan Transaksi Transfer " . $ketPembayaran;
            return $messageHelper->messageSuccessTransferVendor($this->dataBpu['namapenerima'], $ketPembayaran, $this->dataTransfer['norek'], $this->dataTransfer['bank'], $this->dataTransfer['jumlah'], $this->dataInputResponse->TransactionDate, $numberInvoce, $dateInvoice, $startTerm, $endTerm);
        } else {
            $this->subjectEmail = "Laporan Transaksi Transfer " . $this->dataBpu['rincian'];
            return $messageHelper->messageSuccessTransferNonVendor($this->dataBpu['namapenerima'], $this->dataBpu['rincian'], $this->dataTransfer['norek'], $this->dataBpu['nama'], $this->dataTransfer['bank'], $this->dataTransfer['jumlah'], $this->dataInputResponse->TransactionDate);
        }
    }

    private function get_data_bpu()
    {
        $noid = $this->dataTransfer['noid_bpu'];
        $query = mysqli_query($this->koneksi, "SELECT a.*, b.nama, c.rincian FROM bpu a LEFT JOIN pengajuan b ON a.waktu = b.waktu LEFT JOIN selesai c ON a.waktu = c.waktu AND a.no = c.no WHERE a.noid = '$noid'");
        $data = [];
        while ($row = mysqli_fetch_assoc($query)) {
            $data[] = $row;
        }
		$this->dataBpu = $data[0];
    }

    private function send_email()
    {
        $emailHelper = new Email();
        $message = $this->messageEmail();

        $emailHelper->sendEmail($message, $this->subjectEmail, $this->dataTransfer['email_pemilik_rekening']);
    }

    private function send_whatsapp()
    {
        $wa = new Whastapp();
        $message = $this->message_wa();
        $this->get_receiver_whatsapp();

        $phoneNumberReceiver = $this->phoneNumberReceiver;
        for ($i=0; $i < count($phoneNumberReceiver); $i++) { 
            $wa->sendMessage($phoneNumberReceiver[$i], $message);
        }
    }

    private function message_wa()
    {
        $messageHelper = new Message();
        if ($this->dataBpu['statusbpu'] == "Vendor/Supplier") {
            $invoice = $this->dataBpu['ket_pembayaran'];
            $explodeInvoice = explode(".", $invoice);
            $numberInvoce = $explodeInvoice[1];
            
            $dateFormat = $explodeInvoice[2]; // [DATE]
            $day = $dateFormat[0].$dateFormat[1];
            $month = $dateFormat[2].$dateFormat[3];
            $year = "20".$dateFormat[4].$dateFormat[5];
            $dateInvoice = $year . "-" . $month . "-" . $day;

            $explodeTerm = explode("/", $explodeInvoice[3]);
            $startTerm = str_replace('T', '', $explodeTerm[0]); // [START TERM]
            $endTerm = $explodeTerm[1]; // [END TERM PEMBAYARAN]
            $ketPembayaran = $this->dataBpu['rincian']; // [KETERANGAN]
            $this->subjectEmail = "Laporan Transaksi Transfer " . $ketPembayaran;
            return $messageHelper->messageSuccessTransferVendorWA($this->dataBpu['namapenerima'], $ketPembayaran, $this->dataTransfer['norek'], $this->dataTransfer['bank'], $this->dataTransfer['jumlah'], $this->dataInputResponse->TransactionDate, $numberInvoce, $dateInvoice, $startTerm, $endTerm);
        } else {
            $this->subjectEmail = "Laporan Transaksi Transfer " . $this->dataBpu['rincian'];
            return $messageHelper->messageSuccessTransferNonVendorWA($this->dataBpu['namapenerima'], $this->dataBpu['rincian'], $this->dataTransfer['norek'], $this->dataBpu['nama'], $this->dataTransfer['bank'], $this->dataTransfer['jumlah'], $this->dataInputResponse->TransactionDate);
        }
    }

    private function get_receiver_whatsapp()
    {
        $userDireksi = $this->select("phone_number")->from("tb_user")->where('divisi', '=', 'Direksi')->first();
        if ($userDireksi['phone_number'] != '') array_push($this->phoneNumberReceiver, $userDireksi['phone_number']);

        if ($this->dataBpu['statusbpu'] == "UM" || $this->dataBpu['statusbpu'] == "UM Burek") {
            $userFinanceUM = $this->select("phone_number")->from("tb_user")->where('divisi', '=', 'Finance')->where('hak_akses', '=', 'Level 2')->where('level', '=', 'Manager')->first();
            if ($userFinanceUM['phone_number'] != '') array_push($this->phoneNumberReceiver, $userFinanceUM['phone_number']);
        }

        $userFinance = $this->select("phone_number")->from("tb_user")->where('divisi', '=', 'Finance')->where('status_penerima_email_id', '=', '3')->get();
        foreach ($userFinance as $user) {
            if ($user['phone_number'] != '') array_push($this->phoneNumberReceiver, $user['phone_number']);
        }

        if ($this->dataBpu['divisi'] != 'FINANCE' || $this->dataBpu['pengaju'] != 'Sistem') {
            $userPengaju = $this->select("phone_number")->from("tb_user")->where('nama_user', '=', $this->dataBpu['pengaju'])->first();
            if ($userPengaju['phone_number'] != '') array_push($this->phoneNumberReceiver, $userPengaju['phone_number']);

            $userDivisiManager = $this->select("phone_number")->from("tb_user")->where('divisi', '=', $this->dataBpu['divisi'])->where('hak_akses', '=', 'Manager')->get();
            foreach ($userDivisiManager as $user) {
                if ($user['phone_number'] != '') array_push($this->phoneNumberReceiver, $user['phone_number']);
            }
        }
    }
}

$callback = new Callback();
$callback->callback_transfer();