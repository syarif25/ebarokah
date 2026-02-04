<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tahun_acuan extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Login_model');
		$this->load->model('Tahun_acuan_model');
        $this->load->library('user_agent');
	}

	public function index(){
        // Check access - adjust roles as needed or allow same as Lembaga
	    if ($this->session->userdata('jabatan') == 'AdminLembaga' or $this->session->userdata('jabatan') == 'umana' ){
			$this->load->view('Error');
		} else {
            $this->Login_model->getsqurity() ;
            $isi['css'] 	= 'Tahun_acuan/Css';
            $isi['content'] = 'Tahun_acuan/Tahun_acuan';
            $isi['ajax'] 	= 'Tahun_acuan/Ajax';
            $this->load->view('Template',$isi);
		}
	}

	public function data_list()
	{
		$this->load->helper('url');
		$list = $this->Tahun_acuan_model->get_datatables();
		$no = 1;
		$data = array();
		foreach ($list as $datanya) {
			
			$row = array();
			$row[] = $no++;
            $row[] = htmlentities($datanya->id_bidang);
			$row[] = htmlentities($datanya->tahun_acuan);
            $row[] = htmlentities($datanya->keterangan);
            
			//add html for action
			$row[] = '<a type="button" class="btn btn-outline-primary btn-sm" href="javascript:void(0)" 
			title="Edit" onclick="edit_data('."'".$datanya->id."'".')"><i class="bx bx-edit mr-1" ></i> Ubah</a>
            <a type="button" class="btn btn-outline-danger btn-sm" href="javascript:void(0)" 
            title="Hapus" onclick="delete_data('."'".$datanya->id."'".')"><i class="bx bx-trash mr-1" ></i> Hapus</a>';
		    $data[] = $row;
		}
		$output = array("data" => $data);
		echo json_encode($output);
	}

    public function ajax_add()
	{
		$this->_validate();
		$data = array(
            'id_bidang' 	=> $this->input->post('id_bidang'),
            'tahun_acuan' 	=> $this->input->post('tahun_acuan'),
            'keterangan' 	=> $this->input->post('keterangan'),
        );

		$insert = $this->Tahun_acuan_model->create('pengaturan_tahun_acuan',$data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update(){
        $this->_validate();
       $data = array(
            'id_bidang' 	=> $this->input->post('id_bidang'),
            'tahun_acuan' 	=> $this->input->post('tahun_acuan'),
            'keterangan' 	=> $this->input->post('keterangan'),
        );
        
		$this->Tahun_acuan_model->update(array('id' => $this->input->post('id')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_edit($id)
	{
		$data = $this->Tahun_acuan_model->get_by_id($id);
		echo json_encode($data);
	}

    public function ajax_delete($id)
    {
        $this->Tahun_acuan_model->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
    }

	private function _validate()
    {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        if ($this->input->post('id_bidang') == '') {
            $data['inputerror'][] = 'id_bidang';
            $data['error_string'][] = 'Bidang harus dipilih';
            $data['status'] = FALSE;
        }

        if ($this->input->post('tahun_acuan') == '') {
            $data['inputerror'][] = 'tahun_acuan';
            $data['error_string'][] = 'Tahun Acuan harus diisi';
            $data['status'] = FALSE;
        }
        
        if ($this->input->post('keterangan') == '') {
            $data['inputerror'][] = 'keterangan';
            $data['error_string'][] = 'Keterangan harus diisi';
            $data['status'] = FALSE;
        }

      if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }
}
