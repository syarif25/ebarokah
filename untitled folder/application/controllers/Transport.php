<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transport extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Login_model');
		$this->load->model('Transport_model');
        $this->load->helper('Rupiah_helper');
	}

	public function index(){
	    if ($this->session->userdata('jabatan') == 'AdminLembaga' or $this->session->userdata('jabatan') == 'umana' ){
			$this->load->view('Error');
		} else {
    		$this->Login_model->getsqurity() ;
    		$isi['css'] 	= 'Transport/Css';
    		$isi['content'] = 'Transport/Transport';
    		$isi['ajax'] 	= 'Transport/Ajax';
    		$this->load->view('Template',$isi);
		}
	}

	public function data_list()
	{
		$this->load->helper('url');

		$list = $this->Transport_model->get_datatables();
		$no =1;
		$data = array();
		foreach ($list as $datanya) {
			
			$row = array();
			$row[] = $no++;
			$row[] = htmlentities($datanya->nama_transport);
            $row[] = htmlentities($datanya->kategori_transport);
            $row[] = rupiah($datanya->nominal_transport);
			//add html for action
			$row[] = '<a type="button" class="btn btn-outline-danger btn-sm" href="#" 
			title="Track" onclick="edit_transport('."'".$datanya->id_transport."'".')"><i class="bx bx-edit mr-1" ></i> Edit</a>';
		$data[] = $row;
		}
			$output = array("data" => $data);
		echo json_encode($output);
	}

    public function ajax_add()
	{
		$this->_validate();
		$data = array(
            'id_transport' 	=> '',
            'nama_transport' 	=> $this->input->post('alamat'),
            'kategori_transport' 	    => $this->input->post('kategori'),
            'nominal_transport' 	    => str_replace(".", "",$this->input->post('nominal')),
        );

		$simpan = $this->Transport_model->create('transport',$data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update(){
        $this->_validate();
       $data = array(
            'nama_transport' 	=> $this->input->post('alamat'),
            'kategori_transport' 	    => $this->input->post('kategori'),
            'nominal_transport' 	    => str_replace(".", "",$this->input->post('nominal')),
        );
        
		$this->Transport_model->update(array('id_transport' => $this->input->post('id_transport')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_edit($id)
	{
		$data = $this->Transport_model->get_by_id($id);
		echo json_encode($data);
	}

	private function _validate()
    {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        if ($this->input->post('alamat') == '') {
            $data['inputerror'][] = 'alamat';
            $data['error_string'][] = 'Nama harus diisi';
            $data['status'] = FALSE;
        }

        if ($this->input->post('kategori') == '') {
            $data['inputerror'][] = 'kategori';
            $data['error_string'][] = 'Kategori harus diisi';
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
