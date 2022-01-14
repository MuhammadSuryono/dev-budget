<?php
require_once "../application/config/database.php";
require_once "../application/config/message.php";
require_once "../application/config/email.php";

class Callback extends Database {
    private $dataInput;
    private $koneksi;
    private $koneksiBridgeTransfer;
    private $dataTransfer;
    private $dataBpu;
    public function __construct()
    {
        $this->set_name_db(DB_APP);
        $this->init_connection();
        $this->koneksi = $this->connect();

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
    }

    public function get_input()
    {
        return $this->dataInput;
    }

    private function is_success_process_transfer()
	{
		return $this->dataInput['response']['TransactionID'] == $this->dataInput['transfer_req_id'];
	}

    private function message()
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
            $ketPembayaran = $explodeInvoice[count($explodeInvoice) - 1]; // [KETERANGAN]
            return $messageHelper->messageSuccessTransferVendor($this->dataBpu['namapenerima'], $ketPembayaran, $this->dataTransfer['norek'], $this->dataTransfer['bank'], $this->dataTransfer['jumlah'], $this->dataInput['response']['TransactionDate'], $numberInvoce, $dateInvoice, $startTerm, $endTerm);
        } else {
            return $messageHelper->messageSuccessTransferNonVendor($this->dataBpu['namapenerima'], $this->dataBpu['ket_pembayaran'], $this->dataTransfer['norek'], $this->dataBpu['nama'], $this->dataTransfer['bank'], $this->dataTransfer['jumlah'], $this->dataInput['response']['TransactionDate']);
        }
    }

    private function get_data_bpu()
    {
        $noid = $this->dataTransfer['noid_bpu'];
        $query = mysqli_query($this->koneksi, "SELECT a.*, b.nama FROM bpu a LEFT JOIN pengajuan b ON a.waktu = b.waktu WHERE a.noid = '$noid'");
        $data = [];
        while ($row = mysqli_fetch_assoc($query)) {
            $data[] = $row;
        }
		$this->dataBpu = $data[0];
    }

    private function send_email()
    {
        $emailHelper = new Email();
        $message = $this->message();

        $emailHelper->sendEmail($message, "Laporan Transaksi Transfer", $this->dataTransfer['email_pemilik_rekening']);
    }
}

$callback = new Callback();
$callback->callback_transfer();