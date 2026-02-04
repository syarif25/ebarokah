<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Potongan extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Login_model');
		$this->load->model('Potongan_model');
        $this->load->helper('Rupiah_helper');
	}

	public function index(){
	    if ($this->session->userdata('jabatan') == 'AdminLembaga' or $this->session->userdata('jabatan') == 'umana' ){
			$this->load->view('Error');
		} else {
    		$this->Login_model->getsqurity() ;
    		$isi['css'] 	= 'Potongan/Css';
    		$isi['content'] = 'Potongan/Potongan';
    		$isi['ajax'] 	= 'Potongan/Ajax';
    		$this->load->view('Template',$isi);
		}
	}

	public function data_list()
	{
		$this->load->helper('url');
		$list = $this->Potongan_model->get_datatables();
		$no =1;
		$data = array();
		foreach ($list as $datanya) {
			
			$row = array();
			$row[] = $no++;
			$row[] = htmlentities($datanya->nama_potongan);
            $row[] = rupiah($datanya->nominal);
			//add html for action
			$row[] = '<a type="button" class="btn btn-outline-danger btn-sm" href="#" 
			title="Track" onclick="edit_potongan('."'".$datanya->id_potongan."'".')"><i class="bx bx-edit mr-1" ></i> Edit</a>';
		$data[] = $row;
		}
			$output = array("data" => $data);
		echo json_encode($output);
	}

    public function ajax_add()
	{
		$this->_validate();
		$data = array(
				'id_potongan' 	=> '',
				'nama_potongan' => $this->input->post('nama_potongan'),
                'nominal' 	    => str_replace(".", "",$this->input->post('nominal')),
                );

		$simpan = $this->Potongan_model->create('potongan',$data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update(){
        $this->_validate();
         $data = array(
                'nama_potongan' => $this->input->post('nama_potongan'),
                'nominal' 	    => str_replace(".", "",$this->input->post('nominal')),
                );
           
		$this->Potongan_model->update(array('id_potongan' => $this->input->post('id_potongan')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_edit($id)
	{
		$data = $this->Potongan_model->get_by_id($id);
		echo json_encode($data);
	}

	private function _validate()
    {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        if ($this->input->post('nama_potongan') == '') {
            $data['inputerror'][] = 'nama_potongan';
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
