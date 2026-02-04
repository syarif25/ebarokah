<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tunkel extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Login_model');
		$this->load->model('tunkel_model');
        $this->load->helper('Rupiah_helper');
	}

	public function index(){
	    if ($this->session->userdata('jabatan') == 'AdminLembaga' or $this->session->userdata('jabatan') == 'umana' ){
			$this->load->view('Error');
		} else {
    		$this->Login_model->getsqurity() ;
    		$isi['css'] 	= 'Tunkel/Css';
    		$isi['content'] = 'Tunkel/Tunkel';
    		$isi['ajax'] 	= 'Tunkel/Ajax';
    		$this->load->view('Template',$isi);
		}
	}

	public function data_list()
	{
		$this->load->helper('url');
		$list = $this->tunkel_model->get_datatables();
		$no =1;
		$data = array();
		foreach ($list as $datanya) {
			
			$row = array();
			$row[] = $no++;
			$row[] = htmlentities($datanya->nama_tunkel);
            $row[] = rupiah($datanya->besaran_tunkel);
			//add html for action
			$row[] = '<a type="button" class="btn btn-outline-danger btn-sm" href="#" 
			title="Track" onclick="edit_potongan('."'".$datanya->id_tunkel."'".')"><i class="bx bx-edit mr-1" ></i> Edit</a>';
		$data[] = $row;
		}
			$output = array("data" => $data);
		echo json_encode($output);
	}

    public function ajax_add()
	{
		$this->_validate();
		$data = array(
				'id_tunkel' 	=> '',
				'nama_tunkel' => $this->input->post('nama_tunkel'),
                'besaran_tunkel' 	    => str_replace(".", "",$this->input->post('nominal')),
                );

		$simpan = $this->tunkel_model->create('potongan',$data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update(){
        $this->_validate();
         $data = array(
                'nama_tunkel' => $this->input->post('nama_tunkel'),
                'besaran_tunkel' 	    => str_replace(".", "",$this->input->post('nominal')),
                );
           
		$this->tunkel_model->update(array('id_tunkel' => $this->input->post('id_tunkel')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_edit($id)
	{
		$data = $this->tunkel_model->get_by_id($id);
		echo json_encode($data);
	}

	private function _validate()
    {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        if ($this->input->post('nama_tunkel') == '') {
            $data['inputerror'][] = 'nama_tunkel';
            $data['error_string'][] = 'Nama harus diisi';
            $data['status'] = FALSE;
        }

        if ($this->input->post('nominal') == '') {
            $data['inputerror'][] = 'nominal';
            $data['error_string'][] = 'Nominal harus diisi';
            $data['status'] = FALSE;
        }

        if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }



}
