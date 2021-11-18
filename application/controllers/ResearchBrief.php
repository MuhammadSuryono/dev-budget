<?php

defined('BASEPATH') or exit('No direct script access allowed');

class ResearchBrief extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->session->userdata('masuk') != true) {
            $url = base_url();
            redirect($url);
        }

        role_access();

        $this->load->model('Rfq_model');
        $this->load->model('Request_model');
        $this->load->model('ProjectDocument_model');
        $this->load->model('Perusahaan_model');
        $this->load->model('Customer_model');
        $this->load->library('form_validation');
        date_default_timezone_set('Asia/Jakarta');
    }

    public function index()
    {
        $data['perusahaan'] = $this->Perusahaan_model->getAllPerusahaan();

        $this->form_validation->set_rules('id_perusahaan', 'Perusahaan', 'required');
        $this->form_validation->set_rules('id_customer[]', 'Customer', 'required');
        $this->form_validation->set_rules('questionPp[]', 'Company Profile Question', 'required');
        $this->form_validation->set_rules('questionLbr[]', 'Background Research Question', 'required');
        $this->form_validation->set_rules('questionm[]', 'Methodology Question', 'required');
        $this->form_validation->set_rules('questionSr[]', 'Sampling and Respondent Question', 'required');
        $this->form_validation->set_rules('questionDs[]', 'Sampling Distribution Question', 'required');
        $this->form_validation->set_rules('questiont[]', 'Timeline Question', 'required');
        $this->form_validation->set_rules('questionb[]', 'Budget Question', 'required');
        $this->form_validation->set_rules('questionHt[]', 'Other Technical Question', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('templates/header');
            $this->load->view('researchbrief/index', $data);
            $this->load->view('templates/footer');
        } else {
            // $dom = new DOMDocument;
            // $dom->loadHTML(base_url('researchBrief/index'));
            var_dump($dom);
            die();

            $data = [
                'id_perusahaan' => $this->input->post('id_perusahaan'),
                'id_customer' => serialize($this->input->post('id_customer')),
                'profil_perusahaan' => serialize($this->input->post('questionPp[]')),
                'latar_belakang_research' => serialize($this->input->post('questionLbr[]')),
                'methodology ' => serialize($this->input->post('questionm[]')),
                'sampling_dan_responden' =>  serialize($this->input->post('questionSr[]')),
                'distribusi_sampling' => serialize($this->input->post('questionDs[]')),
                'timeline ' => serialize($this->input->post('questiont[]')),
                'budget ' => serialize($this->input->post('questionb[]')),
                'hal_teknis_lainnya' => serialize($this->input->post('questionHt[]')),
                'created_by' => $this->session->userdata('ses_id'),
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d')
            ];

            $this->db->insert('research_brief', $data);
            $this->session->set_flashdata('flash', 'Ditambahkan');
            redirect('dasboard');
        }
    }
}
