<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengguna extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Login_model');
		$this->load->model('Pengguna_model');
	}

	public function index(){
		$this->Login_model->getsqurity() ;
		if ($this->session->userdata('jabatan') == 'AdminLembaga' or $this->session->userdata('jabatan') == 'umana' ){
			$this->load->view('Error');
		} else {
    		$isi['css'] 	= 'Pengguna/Css';
    		$isi['content'] = 'Pengguna/Pengguna';
    		$isi['ajax'] 	= 'Pengguna/Ajax';
    		$this->load->view('Template',$isi);
		}
	}

	public function data_list()
	{
		$this->load->helper('url');

		$list = $this->Pengguna_model->get_datatables();
		$no =1;
		$data = array();
		foreach ($list as $datanya) {
			
			$row = array();
			$row[] = $no++;
			$row[] = htmlentities($datanya->username);
            $row[] = htmlentities($datanya->jabatan);
            $row[] = htmlentities($datanya->nama_lembaga);
            $row[] = htmlentities($datanya->no_hp);
			//add html for action
			$row[] = '<a type="button" class="btn btn-outline-danger btn-sm" href="#" 
			title="Track" onclick="edit_user('."'".$datanya->id_pengguna."'".')"><i class="bx bx-edit mr-1" ></i> Edit</a>';
		$data[] = $row;
		}
			$output = array("data" => $data);
		echo json_encode($output);
	}

    public function ajax_add()
	{
		$this->_validate();
		$data = array(
				'id_pengguna' 		=> '',
				'username' 	=> $this->input->post('username'),
                'password' 	=> password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                'no_hp' 	=> $this->input->post('no_hp'),
                'jabatan' 	=> $this->input->post('jabatan'),
                'lembaga' 	=> $this->input->post('lembaga'),
				);

		$simpan = $this->Pengguna_model->create('pengguna',$data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update(){
        $this->_validate_edit();
        if ($this->input->post('password') == '') {

            $data = array(
                    'username' 		=> $this->input->post('username'),
                    // 'password' 		=> password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                    'jabatan' 		=> $this->input->post('jabatan'),
                    'no_hp' 	    => $this->input->post('no_hp'),
                    'lembaga' 	=> $this->input->post('lembaga'),
                );
            } else {
                $data = array(
                    'username' 		=> $this->input->post('username'),
                    'password' 		=> password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                    'jabatan' 		=> $this->input->post('jabatan'),
                    'no_hp' 	    => $this->input->post('no_hp'),
                    'lembaga' 	=> $this->input->post('lembaga'),
                );
            }
		$this->Pengguna_model->update(array('id_pengguna' => $this->input->post('id_pengguna')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_edit($id)
	{
		$data = $this->Pengguna_model->get_by_id($id);
		echo json_encode($data);
	}

	private function _validate()
    {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        if ($this->input->post('username') == '') {
            $data['inputerror'][] = 'username';
            $data['error_string'][] = 'Nama harus diisi';
            $data['status'] = FALSE;
        }

        if ($this->input->post('password') == '') {
            $data['inputerror'][] = 'password';
            $data['error_string'][] = 'Password harus diisi';
            $data['status'] = FALSE;
        }

        if ($this->input->post('jabatan') == '') {
            $data['inputerror'][] = 'jabatan';
            $data['error_string'][] = 'Jabatan harus dipilih';
            $data['status'] = FALSE;
        }
        
         if ($this->input->post('lembaga') == '') {
            $data['inputerror'][] = 'lembaga';
            $data['error_string'][] = 'lembaga harus dipilih';
            $data['status'] = FALSE;
        }

      if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }

    private function _validate_edit()
    {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        if ($this->input->post('username') == '') {
            $data['inputerror'][] = 'username';
            $data['error_string'][] = 'Nama harus diisi';
            $data['status'] = FALSE;
        }

       

        if ($this->input->post('jabatan') == '') {
            $data['inputerror'][] = 'jabatan';
            $data['error_string'][] = 'Jabatan harus dipilih';
            $data['status'] = FALSE;
        }

      if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }


}
