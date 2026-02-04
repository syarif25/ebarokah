<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tunj_anak extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Login_model');
		$this->load->model('Tunj_anak_model');
        $this->load->helper('Rupiah_helper');
	}

	public function index(){
	    if ($this->session->userdata('jabatan') == 'AdminLembaga' or $this->session->userdata('jabatan') == 'umana' ){
			$this->load->view('Error');
		} else {
    		$this->Login_model->getsqurity() ;
    		$isi['css'] 	= 'Tunjanak/Css';
    		$isi['content'] = 'Tunjanak/Tunj_anak';
    		$isi['ajax'] 	= 'Tunjanak/Ajax';
    		$this->load->view('Template',$isi);
		}
	}

	public function data_list()
	{
		$this->load->helper('url');
		$list = $this->Tunj_anak_model->get_datatables();
		$no =1;
		$data = array();
		foreach ($list as $datanya) {
			
			$row = array();
			$row[] = $no++;
			$row[] = htmlentities($datanya->nama_tunjangan);
            $row[] = rupiah($datanya->nominal_tunj_anak);
			//add html for action
			$row[] = '<a type="button" class="btn btn-outline-danger btn-sm" href="#" 
			title="Track" onclick="edit_tunjangan('."'".$datanya->id_tunj_anak."'".')"><i class="bx bx-edit mr-1" ></i> Edit</a>';
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

		$simpan = $this->Tunj_anak_model->create('potongan',$data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update(){
        $this->_validate();
         $data = array(
                'nominal_tunj_anak' 	    => str_replace(".", "",$this->input->post('nominal')),
                );
		$this->Tunj_anak_model->update(array('id_tunj_anak' => $this->input->post('id_tunj_anak')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_edit($id)
	{
		$data = $this->Tunj_anak_model->get_by_id($id);
		echo json_encode($data);
	}

	private function _validate()
    {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

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
