<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master_prodi extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Login_model');
		$this->load->model('Master_prodi_model');
	}

	public function index(){
        $this->Login_model->getsqurity();
        
        // Cek akses khusus admin dll jika diperlukan
        if ($this->session->userdata('jabatan') == 'AdminLembaga' || $this->session->userdata('jabatan') == 'umana' ){
			$this->load->view('Error');
		} else {
            $isi['css'] 	= 'Master_prodi/Css';
            $isi['content'] = 'Master_prodi/Index';
            $isi['ajax'] 	= 'Master_prodi/Ajax';
            $isi['fakultas'] = $this->Master_prodi_model->get_all_lembaga_fakultas();
            $this->load->view('Template',$isi);
		}
	}

	public function data_list()
	{
		$list = $this->Master_prodi_model->get_datatables();
		$no =1;
		$data = array();
		foreach ($list as $datanya) {
			$row = array();
			$row[] = $no++;
			$row[] = htmlentities($datanya->nama_lembaga);
            $row[] = htmlentities($datanya->nama_prodi);
			$row[] = '<a type="button" class="btn btn-outline-danger btn-sm" href="javascript:void(0)" 
			title="Edit" onclick="edit_prodi('."'".$datanya->id_prodi."'".')"><i class="fa fa-edit mr-1" ></i> Edit</a>
            <a type="button" class="btn btn-outline-danger btn-sm" href="javascript:void(0)" 
			title="Hapus" onclick="hapus_prodi('."'".$datanya->id_prodi."'".')"><i class="fa fa-trash mr-1" ></i> Hapus</a>';
		    $data[] = $row;
		}
		$output = array("data" => $data);
		echo json_encode($output);
	}

    public function ajax_add()
	{
		$this->_validate();
		$data = array(
            'id_lembaga' 	=> $this->input->post('id_lembaga'),
            'nama_prodi' 	=> $this->input->post('nama_prodi'),
        );
		$simpan = $this->Master_prodi_model->create('master_prodi',$data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update(){
        $this->_validate();
        $data = array(
            'id_lembaga' 	=> $this->input->post('id_lembaga'),
            'nama_prodi' 	=> $this->input->post('nama_prodi'),
        );
		$this->Master_prodi_model->update(array('id_prodi' => $this->input->post('id_prodi')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_edit($id)
	{
		$data = $this->Master_prodi_model->get_by_id($id);
		echo json_encode($data);
	}

    public function ajax_delete($id)
    {
        $this->Master_prodi_model->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
    }

	private function _validate()
    {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        if ($this->input->post('id_lembaga') == '') {
            $data['inputerror'][] = 'id_lembaga';
            $data['error_string'][] = 'Fakultas harus dipilih';
            $data['status'] = FALSE;
        }

        if ($this->input->post('nama_prodi') == '') {
            $data['inputerror'][] = 'nama_prodi';
            $data['error_string'][] = 'Nama prodi harus diisi';
            $data['status'] = FALSE;
        }
        
      if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }
}
