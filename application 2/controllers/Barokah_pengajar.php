<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Barokah_pengajar extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Login_model');
		$this->load->model('Barokah_pengajar_model');
        $this->load->helper('Rupiah_helper');
	}

	public function index(){
		$this->Login_model->getsqurity() ;
		$isi['css'] 	= 'Barokah_pengajar/Css';
		$isi['content'] = 'Barokah_pengajar/Barokah_pengajar';
		$isi['ajax'] 	= 'Barokah_pengajar/Ajax';
		$this->load->view('Template',$isi);
	}

	public function data_list()
	{
		$this->load->helper('url');

		$list = $this->Barokah_pengajar_model->get_datatables();
		$no =1;
		$data = array();
		foreach ($list as $datanya) {
			
			$row = array();
			$row[] = $no++;
			$row[] = htmlentities($datanya->kategori);
            $row[] = htmlentities($datanya->min_tmp_mengajar.' - '.$datanya->max_tmp_mengajar);
            $row[] = htmlentities($datanya->ijazah);
            $row[] = rupiah($datanya->nominal);
			//add html for action
		    $row[] = '<a type="button" class="btn btn-outline-primary btn-sm" href="#" 
			title="Track" onclick="edit_barokah('."'".$datanya->id_barokah_pengajar."'".')"><i class="fas fa-edit mr-1" ></i> Edit</a>
			<a type="button" class="btn btn-outline-danger btn-sm" href="barokah_pengajar/hapus/'.$datanya->id_barokah_pengajar.'"><i class="fas fa-trash mr-1" ></i> Hapus</a>
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
            'id_barokah_pengajar' 	=> '',
            'kategori' 				=> $this->input->post('kategori'),
			'golongan' 				=> '',
            'min_tmp_mengajar' 	    => $this->input->post('min_tmp_mengajar'),
			'max_tmp_mengajar' 	    => $this->input->post('max_tmp_mengajar'),
			'ijazah' 				=> $this->input->post('ijazah'),
            'nominal' 	    		=> str_replace(".", "",$this->input->post('nominal')),
        );

		$simpan = $this->Barokah_pengajar_model->create('barokah_pengajar',$data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update(){
        $this->_validate();
       $data = array(
		'kategori' 				=> $this->input->post('kategori'),
		'golongan' 				=> $this->input->post('golongan'),
		'min_tmp_mengajar' 	    => $this->input->post('min_tmp_mengajar'),
		'max_tmp_mengajar' 	    => $this->input->post('max_tmp_mengajar'),
		'ijazah' 				=> $this->input->post('ijazah'),
		'nominal' 	    		=> str_replace(".", "",$this->input->post('nominal')),
        );
        
		$this->Barokah_pengajar_model->update(array('id_barokah_pengajar' => $this->input->post('id_barokah_pengajar')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_edit($id)
	{
		$data = $this->Barokah_pengajar_model->get_by_id($id);
		echo json_encode($data);
	}
	
	public function hapus($id)
	{
    $this->db->where('id_barokah_pengajar', $id)->delete('barokah_pengajar');
    
    // Set pesan notifikasi menggunakan flashdata
    $this->session->set_flashdata('success', 'Data berhasil dihapus.');
    
    // Redirect ke halaman yang diinginkan
    redirect('barokah_pengajar');
	}

	private function _validate()
    {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        if ($this->input->post('kategori') == '') {
            $data['inputerror'][] = 'kategori';
            $data['error_string'][] = ' harus diisi';
            $data['status'] = FALSE;
        }

        if ($this->input->post('max_tmp_mengajar') == '') {
            $data['inputerror'][] = 'max_tmp_mengajar';
            $data['error_string'][] = 'harus diisi';
            $data['status'] = FALSE;
        }

        if ($this->input->post('min_tmp_mengajar') == '') {
            $data['inputerror'][] = 'min_tmp_mengajar';
            $data['error_string'][] = 'harus diisi';
            $data['status'] = FALSE;
        }

		if ($this->input->post('ijazah') == '') {
            $data['inputerror'][] = 'ijazah';
            $data['error_string'][] = 'harus diisi';
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
