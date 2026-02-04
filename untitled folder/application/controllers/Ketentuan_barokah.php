<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ketentuan_barokah extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Login_model');
		$this->load->model('ketentuan_model');
        $this->load->helper('Rupiah_helper');
	}

	public function index(){
	    if ($this->session->userdata('jabatan') == 'AdminLembaga' or $this->session->userdata('jabatan') == 'umana' ){
			$this->load->view('Error');
		} else {
    		$this->Login_model->getsqurity() ;
    		$isi['css'] 	= 'Ketentuan/Css';
    		$isi['content'] = 'Ketentuan/Ketentuan';
    		$isi['ajax'] 	= 'Ketentuan/Ajax';
    		$this->load->view('Template',$isi);
		}
	}

	public function data_list()
	{
		$this->load->helper('url');

		$list = $this->ketentuan_model->get_datatables();
		$no =1;
		$data = array();
		foreach ($list as $datanya) {
			
			$row = array();
			$row[] = $no++;
			$row[] = htmlentities($datanya->nama_jabatan);
            $row[] = htmlentities($datanya->kategori);
            $row[] = rupiah($datanya->barokah);
			//add html for action
		    $row[] = '<a type="button" class="btn btn-outline-primary btn-sm" href="#" 
			title="Track" onclick="edit_user('."'".$datanya->id_ketentuan."'".')"><i class="fas fa-edit mr-1" ></i> Edit</a>
			<a type="button" class="btn btn-outline-danger btn-sm" href="ketentuan_barokah/hapus/'.$datanya->id_ketentuan.'"><i class="fas fa-trash mr-1" ></i> Hapus</a>
			';
		$data[] = $row;
		}
			$output = array("data" => $data);
		echo json_encode($output);
	}

    public function ajax_add()
	{
		$this->_validate();
		$data = array(
            'id_ketentuan' 	=> '',
            'nama_jabatan' 	=> $this->input->post('nama_jabatan'),
            'kategori' 	    => $this->input->post('kategori'),
            'barokah' 	    => str_replace(".", "",$this->input->post('barokah')),
        );

		$simpan = $this->ketentuan_model->create('ketentuan_barokah',$data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update(){
        $this->_validate();
       $data = array(
            'nama_jabatan' 	=> $this->input->post('nama_jabatan'),
            'kategori' 	    => $this->input->post('kategori'),
            'barokah' 	    => str_replace(".", "",$this->input->post('barokah')),
        );
        
		$this->ketentuan_model->update(array('id_ketentuan' => $this->input->post('id_ketentuan')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_edit($id)
	{
		$data = $this->ketentuan_model->get_by_id($id);
		echo json_encode($data);
	}
	
	public function hapus($id)
	{
    $this->db->where('id_ketentuan', $id)->delete('ketentuan_barokah');
    
    // Set pesan notifikasi menggunakan flashdata
    $this->session->set_flashdata('success', 'Data berhasil dihapus.');
    
    // Redirect ke halaman yang diinginkan
    redirect('ketentuan_barokah');
	}

	private function _validate()
    {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        if ($this->input->post('nama_jabatan') == '') {
            $data['inputerror'][] = 'nama_jabatan';
            $data['error_string'][] = 'Nama harus diisi';
            $data['status'] = FALSE;
        }

        if ($this->input->post('kategori') == '') {
            $data['inputerror'][] = 'kategori';
            $data['error_string'][] = 'Kategori harus diisi';
            $data['status'] = FALSE;
        }

        if ($this->input->post('barokah') == '') {
            $data['inputerror'][] = 'barokah';
            $data['error_string'][] = 'Nominal harus diisi';
            $data['status'] = FALSE;
        }

      if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }

   


}
