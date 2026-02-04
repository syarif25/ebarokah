<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lembaga extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Login_model');
		$this->load->model('lembaga_model');
        $this->load->helper('Rupiah_helper');
        $this->load->library('user_agent');
	}

	public function index(){
	    if ($this->session->userdata('jabatan') == 'AdminLembaga' or $this->session->userdata('jabatan') == 'umana' ){
			$this->load->view('Error');
		} else {
		$this->Login_model->getsqurity() ;
		$isi['css'] 	= 'Lembaga/Css';
		$isi['content'] = 'Lembaga/Lembaga';
		$isi['ajax'] 	= 'Lembaga/Ajax';
		$this->load->view('Template',$isi);
		}
	}

	public function data_list()
	{
		$this->load->helper('url');
		$list = $this->lembaga_model->get_datatables();
		$no =1;
		$data = array();
		foreach ($list as $datanya) {
			
			$row = array();
			$row[] = $no++;
			$row[] = htmlentities($datanya->nama_lembaga);
            $row[] = htmlentities($datanya->id_bidang);
            $row[] = htmlentities($datanya->tenaga_pengajar);
            $row[] = htmlentities($datanya->nama_pimpinan);
			//add html for action
			$row[] = '<a type="button" class="btn btn-outline-danger btn-sm" href="#" 
			title="Track" onclick="edit_user('."'".$datanya->id_lembaga."'".')"><i class="bx bx-edit mr-1" ></i> Edit</a>';
		    $data[] = $row;
		}
		$output = array("data" => $data);
		echo json_encode($output);
	}

    public function ajax_add()
	{
		$this->_validate();
		$data = array(
            'id_lembaga' 	=> '',
            'nama_lembaga' 	=> $this->input->post('nama_lembaga'),
            'id_bidang' 	=> $this->input->post('id_bidang'),
            'tenaga_pengajar' 	=> $this->input->post('tenaga_pengajar'),
            'nama_pimpinan' 	=> $this->input->post('nama_pimpinan'),
        );

		$simpan = $this->lembaga_model->create('lembaga',$data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update(){
        $this->_validate();
       $data = array(
            'nama_lembaga' 	=> $this->input->post('nama_lembaga'),
            'id_bidang' 	=> $this->input->post('id_bidang'),
            'tenaga_pengajar' 	=> $this->input->post('tenaga_pengajar'),
            'nama_pimpinan' 	=> $this->input->post('nama_pimpinan'),
        );
        
		$this->lembaga_model->update(array('id_lembaga' => $this->input->post('id_lembaga')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_edit($id)
	{
		$data = $this->lembaga_model->get_by_id($id);
		echo json_encode($data);
	}
	
	function test(){
		if ($this->agent->is_mobile('iphone'))
        {
                // $this->load->view('iphone/home');
                echo " ini iphone";
        }
        elseif ($this->agent->is_mobile())
        {
                $this->load->view('Umana/Mobile');
                // echo "ini hp";
        }
        else
        {
                // $this->load->view('web/home');
                echo "ini web";
        }
	}

	private function _validate()
    {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        if ($this->input->post('nama_lembaga') == '') {
            $data['inputerror'][] = 'nama_lembaga';
            $data['error_string'][] = 'Nama harus diisi';
            $data['status'] = FALSE;
        }

        if ($this->input->post('id_bidang') == '') {
            $data['inputerror'][] = 'id_bidang';
            $data['error_string'][] = 'Bidang harus dipilih';
            $data['status'] = FALSE;
        }
        
        if ($this->input->post('tenaga_pengajar') == '') {
            $data['inputerror'][] = 'tenaga_pengajar';
            $data['error_string'][] = 'harus dipilih';
            $data['status'] = FALSE;
        }
        
        if ($this->input->post('nama_pimpinan') == '') {
            $data['inputerror'][] = 'nama_pimpinan';
            $data['error_string'][] = 'harus diisi';
            $data['status'] = FALSE;
        }

      if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }
    
    public function backupDatabase()
	{
		// Load library database dan utilitas
		$this->load->database();
		$this->load->dbutil();

		// Konfigurasi backup
		$config = array(
			'format' => 'zip', // Format file backup (zip, gzip, txt)
			'filename' => 'backup_db_' . date('YmdHis') . '.sql', // Nama file backup dengan timestamp
			'add_drop' => TRUE, // Tambahkan perintah DROP TABLE di dalam file backup
			'add_insert' => TRUE, // Tambahkan perintah INSERT di dalam file backup
			'newline' => "\n" // Karakter newline yang digunakan dalam file backup
		);

		// Buat file backup
		$backup = $this->dbutil->backup($config);

		// Simpan file backup ke server
		$this->load->helper('file');
		write_file('./assets/db/' . $config['filename'], $backup);

		// Menampilkan pesan sukses
		echo 'Database berhasil di-backup.';
	}

   


}
