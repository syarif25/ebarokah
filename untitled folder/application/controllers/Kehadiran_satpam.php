<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kehadiran_satpam extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Login_model');
		$this->load->model('kehadiran_satpam_model');
        $this->load->helper('Rupiah_helper');
		$this->load->helper('string');
		$this->load->library('Pdf'); 
	}

	public function index(){
		$this->Login_model->getsqurity() ;
		$isi['css'] 	= 'Kehadiran_satpam/Css';
		$isi['content'] = 'Kehadiran_satpam/Kehadiran_satpam';
		$isi['ajax'] 	= 'Kehadiran_satpam/Ajax';
		$this->load->view('Template',$isi);
	}

	public function data_list()
	{
		$this->load->helper('url');
		$id_lembaga = 58;

		$this->db->select('
			l.nama_lembaga,
			kl.id_kehadiran_lembaga,
			kl.bulan,
			kl.tahun,
			kl.status
		');
		$this->db->from('kehadiran_lembaga kl');
		$this->db->join('lembaga l', 'kl.id_lembaga = l.id_lembaga');
		$this->db->where('l.id_lembaga', $id_lembaga);
		$this->db->group_by('
			kl.id_kehadiran_lembaga,
			l.nama_lembaga,
			kl.bulan,
			kl.tahun,
			kl.status
		');
		$this->db->order_by('kl.id_kehadiran_lembaga', 'DESC');

		$list = $this->db->get()->result();

		$no =1;
		$data = array();
		foreach ($list as $datanya) {
			$row = array();
			$row[] = $no++;
			$row[] = htmlentities($datanya->nama_lembaga);
			$row[] = " org";
			$row[] = htmlentities($datanya->bulan." ".$datanya->tahun);
		if ($datanya->status == 'Belum'){
			$row[] = "<span class='badge badge-secondary'>Belum diisi<span class='ms-1 fa fa-times'></span></span>";
			$row[] = '<a type="button" class="btn btn-outline-secondary btn-sm" href="kehadiran_satpam/add_kehadiran/'.$this->encrypt_url($datanya->id_kehadiran_lembaga).'" target="_blank"
			title="Isi Rekap"  ><i class="mdi mdi-file-document-box mr-1" ></i> Isi Rekap Kehadiran</a>';
		}	elseif ($datanya->status == "Sudah") {
			$row[] = "<span class='badge badge-warning text-dark'>Sedang dikoreksi<span class='ms-1 fa fa-redo'></span></span>";
			$row[] = '<a type="button" class="btn btn-success btn-xs" href="kehadiran/koreksi/'.$datanya->id_kehadiran_lembaga.'")"><i class="mdi mdi-checkbox-marked-circle mr-1" ></i> Cek Barokah</a>
			 ';
			//  <a type="button" class="btn btn-danger btn-xs" href="kehadiran/koreksi/'.$datanya->id_kehadiran_lembaga.'")"><i class="mdi mdi-send mr-1" ></i> Kirim Pengajuan</a>
		} else {
			$row[] = "<span class='badge badge-success'>Sudah transfer<span class='ms-1 fa fa-check'></span></span>";
			$row[] = '<a type="button" class="btn btn-info btn-sm" href="laporan/rincian/'.$datanya->id_kehadiran_lembaga.'">
			<i class="mdi mdi-file-document-box mr-1" ></i> Lihat Rekap Barokah</a>';
		}
			//add html for action
			
		    $data[] = $row;
		}
		$output = array("data" => $data);
		echo json_encode($output);
	}

	public function blanko_add()
	{
		$this->db->where('id_lembaga', $this->input->post('id_lembaga'));
		$this->db->where('bulan', $this->input->post('bulan'));
		$this->db->where('tahun', $this->input->post('tahun'));
		$this->db->where('kategori', 'Satpam');
		$existingData = $this->db->get('kehadiran_lembaga')->row();
	
		if ($existingData) { 
			echo json_encode(array("status" => false));
		}else {
			// $this->_validate();
			$data = array(
				'id_kehadiran_lembaga' 	=> '',
				'id_lembaga' 	=> $this->input->post('id_lembaga'),
				'kategori' 	    => 'Satpam',
				'bulan' 	    => $this->input->post('bulan'),
				'tahun' 	    => $this->input->post('tahun'),
				'status' 	    => 'Belum',
			);
			$simpan = $this->kehadiran_satpam_model->create('kehadiran_lembaga',$data);
			echo json_encode(array("status" => TRUE));
		}
	}

	public function add_kehadiran($id){
        $decrypted_id = $this->decrypt_url($id);
		$this->Login_model->getsqurity() ;
		$isi['css'] 	= 'Kehadiran_satpam/Css';
		$isi['content'] = 'Kehadiran_satpam/Add_kehadiran';
		$isi['ajax'] 	= 'Kehadiran_satpam/Ajax';
		$isi['kode'] 	= $decrypted_id;
		$this->load->view('Template',$isi);
		// $this->rekap_list($id);
	}

	 
    public function encrypt_url($string) {
        $key = '874jzceroier38!@#%*bjkdwdw)'; // Ganti dengan kunci enkripsi yang diinginkan
        $encrypted_string = base64_encode($string . $key);
        $encrypted_string = str_replace(array('+', '/', '='), array('-', '_', ''), $encrypted_string);
        return $encrypted_string;
    }
    
    public function decrypt_url($string) {
        $key = '874jzceroier38!@#%*bjkdwdw)'; // Ganti dengan kunci enkripsi yang diinginkan
        $string = str_replace(array('-', '_'), array('+', '/'), $string);
        $string = base64_decode($string);
        $string = str_replace($key, '', $string);
        return $string;
    }

	public function ajax_add_kehadiran()
	{
		$nik_umana		= $this->input->post('id_satpam');
		$bulan			= $this->input->post('bulan');
		$tahun			= $this->input->post('tahun');
		$id_satpam		= $this->input->post('id_satpam');
		$id_kehadiran_lembaga 	= $this->input->post('id_kehadiran_lembaga');
		$jumlah_hari		= $this->input->post('jumlah_hari');
		$jumlah_shift		= $this->input->post('jumlah_shift');
		$jumlah_dini		= $this->input->post('jumlah_dini');
		$data = array();
    
		$index = 0; 
		foreach($nik_umana as $nik){ // perulangan berdasarkan nik sampai data terakhir
		array_push($data, array(
			'id_kehadiran_satpam' 	=> '',
			'bulan' 				=> $bulan,
			'tahun'					=> $tahun,
			'id_satpam'				=> $nik,
			'id_kehadiran_lembaga'	=> $id_kehadiran_lembaga, 
			'jumlah_hari'			=>$jumlah_hari[$index],
			'jumlah_shift'			=>$jumlah_shift[$index],
			'jumlah_dinihari'			=>$jumlah_dini[$index],
		));
		
		$index++;
		}

		$data2 = array(
			'file'		=> '',
            'status' => 'Sudah',
        );

		if(!empty($_FILES['file']['name']))
		{
			$upload = $this->_do_upload();
			$data2['file'] = $upload;
		}

		$this->db->insert_batch('kehadiran_satpam', $data); 
		$this->db->where('id_kehadiran_lembaga', $this->input->post('id_kehadiran_lembaga'));
		$this->db->update('kehadiran_lembaga', $data2);	
	}



}