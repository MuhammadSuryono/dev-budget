<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CommisionVoucher extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->session->userdata('masuk') != true) {
            $url = base_url();
            redirect($url);
        }

        role_access();

        $this->load->model('ProjectDocument_model');
        $this->load->model('Methodology_model');
        $this->load->model('CommisionVoucher_model');
        $this->load->model('Rfq_model');
        $this->load->model('Customer_model');
        $this->load->library('form_validation');
        $this->load->helper('download');
        date_default_timezone_set('Asia/Jakarta');
    }
    public function index()
    {
        $data['doc'] = $this->ProjectDocument_model->getDealDocument();
        $data['methodology'] = $this->db->select('methodology')->select('keterangan')->get('data_methodology')->result_array();

        if ($this->input->get('rfq')) {
            $data['rfq'] = $this->input->get('rfq');
        }


        $this->form_validation->set_rules('projectNumber', 'Project Number', 'required');
        $this->form_validation->set_rules('projectName', 'Project Name', 'required');
        $this->form_validation->set_rules('internalProjectName', 'Internal Project Name', 'required');
        $this->form_validation->set_rules('client', 'Client', 'required');
        // $this->form_validation->set_rules('phoneNumber', 'Phone Number', 'required');
        $this->form_validation->set_rules('projectNumber', 'Project Number', 'required');
        $this->form_validation->set_rules('projectType[]', 'Project Type', 'required');
        $this->form_validation->set_rules('address', 'Address', 'required');
        $this->form_validation->set_rules('valueAddedTax', 'Value Added Tax', 'required');
        $this->form_validation->set_rules('hargaPokokProduksi', 'Harga Pokok Produksi', 'required');
        $this->form_validation->set_rules('managementFee', 'Management Fee', 'required');
        // $this->form_validation->set_rules('contactPersonName[]', 'Contact Person Name', 'required');
        // $this->form_validation->set_rules('contactPersonNumber[]', 'Contact Person Number', 'required');
        // $this->form_validation->set_rules('contractValue', 'Contract Value', 'required');
        // $this->form_validation->set_rules('termsPayment[]', 'Terms of Payment', 'required');
        // $this->form_validation->set_rules('loa[]', 'LOA', 'required');
        // $this->form_validation->set_rules('paymentDate[]', 'Date', 'required');
        $this->form_validation->set_rules('researchExecutive', 'Research Executive', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('templates/header');
            $this->load->view('commisionvoucher/index', $data);
            $this->load->view('templates/footer');
        } else {
            $termsPayment = $this->input->post('termsPayment[]');
            $totalTermsPayment = 0;
            for ($i = 0; $i < count($termsPayment); $i++) {
                $totalTermsPayment += $termsPayment[$i];
            }
            if ($totalTermsPayment != 100) {
                $this->session->set_flashdata('flash2', 'Terms Payment harus 100%');
                redirect('commisionVoucher');
            }

            $queryCheck = $this->db->get_where('comm_voucher', ['nomor_project' => $this->input->post('projectNumber')])->row_array();
            if ($queryCheck) {
                $this->CommisionVoucher_model->editCommVoucher();
                $this->session->set_flashdata('flash', 'Berhasil Diubah');
                redirect('commisionVoucher');
            } else {
                $this->session->set_flashdata('flash', 'Berhasil Ditambahkan');
                $this->CommisionVoucher_model->setCommVoucher();
                redirect('commisionVoucher');
            }
        }
    }

    public function checkData()
    {
        $rfq = $this->input->post('rfq');
        $commisionVoucher = $this->db->get_where('comm_voucher', ['nomor_project' => $rfq])->row_array();
        $user = $this->ProjectDocument_model->getCustomerDocument($rfq)[0];

        $projectName = '';
        $internalProjectName = '';
        $projectNumber = '';
        $client = '';
        $address = '';
        $phone = '';
        $fax = '';
        $projectType = '';
        $contactPersonName = '';
        $contactPersonJabatan = '';
        $contactPersonNumber = '';
        $valueAddedTax = '';
        $hargaPokokProduksi = '';
        $managementFee = '';
        $contractValue = '';
        $termsPayment = '';
        $loa = '';
        $paymentDate = '';
        $invoiceDate = '';
        $confirmLeter = '';
        $researchExecutive = '';

        if ($commisionVoucher) {
            $projectName = $commisionVoucher['nama_project'];
            $internalProjectName = $commisionVoucher['nama_project_internal'];
            $projectNumber = $commisionVoucher['nomor_project'];
            $client = $commisionVoucher['client'];
            $address = $commisionVoucher['alamat'];
            $phone = $commisionVoucher['telp'];
            $fax = ($commisionVoucher['fax']) ? $commisionVoucher['fax'] : '';
            $arrProjectType = unserialize($commisionVoucher['tipe_project']);
            $projectType = [];
            foreach ($arrProjectType as $apt) {
                $string = explode('-', $apt);
                $name = '';
                for ($i = 0; $i < count($string); $i++) {
                    $name .= trim($string[$i]);
                    if ($i < count($string) - 1 && $i == 0) $name .= ' - ';
                    else if ($i < count($string) - 1) $name .= '-';
                }
                array_push($projectType, $name);
            }
            $contactPersonName = unserialize($commisionVoucher['nama_contact_person']);
            $contactPersonNumber = unserialize($commisionVoucher['nomor_contact_person']);
            $contactPersonJabatan = unserialize($commisionVoucher['jabatan_contact_person']);
            $valueAddedTax = $commisionVoucher['value_added_tax'];
            $hargaPokokProduksi = $commisionVoucher['harga_pokok_produksi'];
            $managementFee = $commisionVoucher['management_fee'];
            $contractValue = $commisionVoucher['contract_value'];
            $termsPayment = unserialize($commisionVoucher['terms_of_payment']);
            $loa = unserialize($commisionVoucher['based_on_loa']);
            $paymentDate = unserialize($commisionVoucher['payment_date']);
            $invoiceDate = unserialize($commisionVoucher['invoice_date']);
            $confirmLeter = $commisionVoucher['letter_to_followed_by'];
            $researchExecutive = $commisionVoucher['research_executive'];
        } else {
            $projectName = ($user['nama_project']) ? $user['nama_project'] : '';
            $projectNumber = $rfq;
            $client = ($user['nama']) ? $user['nama'] : '';
            $address = ($user['alamat']) ? $user['alamat'] : '';
            $phone = ($user['telp']) ? $user['telp'] : '';
            $fax = ($user['fax']) ? $user['fax'] : '';
            if (@unserialize($user['id_methodology']) !== false) {
                $arrProjectType = unserialize($user['id_methodology']);
                $projectType = [];
                foreach ($arrProjectType as $apt) {
                    $queryProjectType = $this->Methodology_model->getMethodologyById($apt);
                    $name = $queryProjectType['methodology'] . ' - ' . $queryProjectType['keterangan'];
                }
                array_push($projectType, $name);
            } else {
                $queryProjectType = $this->Methodology_model->getMethodologyById($user['id_methodology']);
                $projectType = [$queryProjectType['methodology'] . ' - ' . $queryProjectType['keterangan']];
            }

            if (@unserialize($user['id_customer']) !== false) {
                $arrCustomer = unserialize($user['id_customer']);
                $contactPersonName = [];
                $contactPersonNumber = [];
                $contactPersonJabatan = [];
                for ($i = 0; $i < count($arrCustomer); $i++) {
                    $data = $this->Customer_model->getCustomerById($arrCustomer[$i]);
                    array_push($contactPersonName, $data['nama']);
                    array_push($contactPersonNumber, $data['hp1']);

                    $dataJabatan = $this->Jabatan_model->getJabatanById($data['jabatan']);
                    array_push($contactPersonJabatan, $dataJabatan['jabatan']);
                }
                // $contactPersonName = ($user['id_customer']) ? unserialize($user['id_customer']) : "";
                // $contactPersonNumber = ($user['id_customer']) ? unserialize($user['id_customer']) : "";
            } else {
                $data = $this->Customer_model->getCustomerById($user['id_customer']);
                $contactPersonName = ($data["nama"]) ? [$data['nama']] : [];
                $contactPersonNumber = ($data['hp1']) ? [$data['hp1']] : [];
            }
        }

        $queryRfq = $this->Rfq_model->getRfqById($projectNumber);
        if (@unserialize($queryRfq['id_customer']) === false) {
            $arrCustomer = $queryRfq['id_customer'];
            $queryCustomer = $this->Customer_model->getCustomerById($arrCustomer);
            $namaCustomer = $queryCustomer['nama'];
            $emailCustomer = $queryCustomer['email1'];
        } else {
            $arrCustomer = unserialize($queryRfq['id_customer']);
            $namaCustomer = [];
            $emailCustomer = [];
            foreach ($arrCustomer as $dataRfq) {
                $queryCustomer = $this->Customer_model->getCustomerById($dataRfq);
                array_push($namaCustomer, $queryCustomer['nama']);
                array_push($emailCustomer, $queryCustomer['email1']);
            }
        }


        $dataRfq = [
            'projectNumber' => $projectNumber,
            'enterDate' => $queryRfq['tgl_masuk'],
            'projectCode' => $queryRfq['kode_project'],
            'customerName' => $namaCustomer,
            'emailCustomer' => $emailCustomer,
            'idResearchBrief' => $queryRfq['id_research_brief']
        ];

        $data = [
            'projectName' => $projectName,
            'internalProjectName' => $internalProjectName,
            'projectNumber' => $projectNumber,
            'client' => $client,
            'address' => $address,
            'phone' => $phone,
            'fax' => $fax,
            'projectType' => $projectType,
            'contactPersonName' => $contactPersonName,
            'jabatan' => $contactPersonJabatan,
            'contactPersonNumber' => $contactPersonNumber,
            'valueAddedTax' => $valueAddedTax,
            'hargaPokokProduksi' => $hargaPokokProduksi,
            'managementFee' => $managementFee,
            'contractValue' => $contractValue,
            'termsPayment' => $termsPayment,
            'loa' => $loa,
            'paymentDate' => $paymentDate,
            'invoiceDate' => $invoiceDate,
            'confirmLetter' => $confirmLeter,
            'researchExecutive' => $researchExecutive,
            'dataRfq' => $dataRfq
        ];
        echo json_encode($data);
    }
}
