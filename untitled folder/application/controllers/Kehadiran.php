<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kehadiran extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Login_model');
		$this->load->model('kehadiran_model');
        $this->load->helper('Rupiah_helper');
		$this->load->helper('string');
		$this->load->library('Pdf'); 
	}

	public function index(){
		$this->Login_model->getsqurity() ;
		$isi['css'] 	= 'Kehadiran/Css';
		$isi['content'] = 'Kehadiran/Kehadiran';
		$isi['ajax'] 	= 'Kehadiran/Ajax';
		$this->load->view('Template',$isi);
	}
	
	public function pengajar(){
		$this->Login_model->getsqurity() ;
		$isi['css'] 	= 'Kehadiran/Css';
		$isi['content'] = 'Kehadiran_pengajar/Kehadiran';
		$isi['ajax'] 	= 'Kehadiran_pengajar/Ajax';
		$this->load->view('Template',$isi);
	}
	
	public function kehadiran_log(){
		$this->Login_model->getsqurity() ;
		$isi['css'] 	= 'Log_kehadiran/Css';
		$isi['content'] = 'Log_kehadiran/Kehadiran_umana';
		$isi['ajax'] 	= 'Log_kehadiran/Ajax';
		$this->load->view('Template',$isi);
	}
	
	 public function test_hp()
    {
        // Memuat library Mobile Detect
        $this->load->library('MobileDetect');
        
        // Menggunakan library Mobile Detect
        $detect = new Mobile_Detect;

        // Contoh penggunaan fungsi-fungsi Mobile Detect
        if ($detect->isMobile()) {
            // Perangkat adalah perangkat seluler
            echo "Ini adalah perangkat seluler.";
        } else {
            // Perangkat bukan perangkat seluler
            echo "Ini bukan perangkat seluler.";
        }
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


    public function add($id){
        $decrypted_id = $this->decrypt_url($id);
		$this->Login_model->getsqurity() ;
		$isi['css'] 	= 'Kehadiran/Css';
		$isi['content'] = 'Kehadiran/Add_kehadiran';
		$isi['ajax'] 	= 'Kehadiran/Ajax';
		$isi['kode'] 	= $decrypted_id;
		$this->load->view('Template',$isi);
		// $this->rekap_list($id);
	}
	
	public function add_pengajar($id){
        $decrypted_id = $this->decrypt_url($id);
		$this->Login_model->getsqurity() ;
		$isi['css'] 	= 'Kehadiran/Css';
		$isi['content'] = 'Kehadiran_pengajar/Add_kehadiran';
		$isi['ajax'] 	= 'Kehadiran_pengajar/Ajax';
		$isi['kode'] 	= $decrypted_id;
		$this->load->view('Template',$isi);
		// $this->rekap_list($id);
	}


	public function data_list()
	{
		$this->load->helper('url');
		if($this->session->userdata('jabatan') == 'AdminLembaga'){
			$lembaga = $this->session->userdata('lembaga');
			$list = $this->db->query("SELECT nama_lembaga, id_kehadiran_lembaga, kehadiran_lembaga.status, kehadiran_lembaga.bulan, kehadiran_lembaga.tahun, penempatan.nik, COUNT(penempatan.id_penempatan) as jml FROM lembaga, kehadiran_lembaga, penempatan WHERE kehadiran_lembaga.id_lembaga = lembaga.id_lembaga and penempatan.id_lembaga = kehadiran_lembaga.id_lembaga and lembaga.id_lembaga = $lembaga and kehadiran_lembaga.kategori = 'Struktural' GROUP BY kehadiran_lembaga.id_kehadiran_lembaga order by kehadiran_lembaga.id_kehadiran_lembaga desc ")->result();
		} else {
			$list = $this->db->query("SELECT nama_lembaga, id_kehadiran_lembaga, kehadiran_lembaga.status, kehadiran_lembaga.bulan, kehadiran_lembaga.tahun, penempatan.nik, COUNT(penempatan.id_penempatan) as jml FROM lembaga, kehadiran_lembaga, penempatan WHERE kehadiran_lembaga.id_lembaga = lembaga.id_lembaga and penempatan.id_lembaga = kehadiran_lembaga.id_lembaga and kehadiran_lembaga.kategori = 'Struktural' GROUP BY kehadiran_lembaga.id_kehadiran_lembaga order by kehadiran_lembaga.id_kehadiran_lembaga desc ")->result();
		}
		$no =1;
		$data = array();
		foreach ($list as $datanya) {
			$encrypted_id = $this->encrypt_url($datanya->id_kehadiran_lembaga);
			$row = array();
			$row[] = $no++;
			$row[] = htmlentities($datanya->nama_lembaga);
			$row[] = htmlentities($datanya->jml)." org";
			$row[] = htmlentities($datanya->bulan." ".$datanya->tahun);
		if ($datanya->status == 'Belum'){
			$row[] = "<span class='badge badge-secondary'>Belum diisi<span class='ms-1 fa fa-times'></span></span>";
			$row[] = '<a type="button" class="btn btn-outline-secondary btn-sm" href="#" 
			title="Rekap" onclick="rekap('."'".$encrypted_id."'".')"><i class="mdi mdi-file-document-box mr-1" ></i> Isi Rekap Kehadiran</a>';
		}	elseif ($datanya->status == "Sudah") {
			$row[] = "<span class='badge badge-danger'>Belum dikirim <i class='mdi mdi-alert-circle' data-name='mdi-alert-circle'></i></span>";
			$row[] = '<a type="button" class="btn btn-success btn-xs" href="kehadiran/koreksi/'.$encrypted_id.'")"><i class="mdi mdi-checkbox-marked-circle mr-1" ></i> Cek Barokah</a>
			 ';
			//  <a type="button" class="btn btn-danger btn-xs" href="kehadiran/koreksi/'.$encrypted_id.'")"><i class="mdi mdi-send mr-1" ></i> Kirim Pengajuan</a>
		} elseif ($datanya->status == "Terkirim" or $datanya->status == "acc" ) {
			$row[] = "<span class='badge badge-warning text-dark'>Sedang dikoreksi<span class='ms-1 fa fa-redo'></span></span>";
			$row[] = '<a type="button" class="btn btn-success btn-xs" href="kehadiran/koreksi/'.$encrypted_id.'")"><i class="mdi mdi-checkbox-marked-circle mr-1" ></i> Cek Barokah</a>
			 ';
			//  <a type="button" class="btn btn-danger btn-xs" href="kehadiran/koreksi/'.$encrypted_id.'")"><i class="mdi mdi-send mr-1" ></i> Kirim Pengajuan</a>
		} else {
			$row[] = "<span class='badge badge-success'>Sudah transfer<span class='ms-1 fa fa-check'></span></span>";
			$row[] = '<a type="button" class="btn btn-info btn-sm" href="laporan/rincian/'.$encrypted_id.'">
			<i class="mdi mdi-file-document-box mr-1" ></i> Lihat Rekap Barokah</a>';
		}
			//add html for action
			
		    $data[] = $row;
		}
		$output = array("data" => $data);
		echo json_encode($output);
	}
	
	public function data_list_pengajar()
	{
		$this->load->helper('url');
		if($this->session->userdata('jabatan') == 'AdminLembaga'){
			$lembaga = $this->session->userdata('lembaga');
			// $list = $this->db->query("SELECT nama_lembaga, kategori, id_kehadiran_lembaga, kehadiran_lembaga.status, kehadiran_lembaga.bulan, kehadiran_lembaga.tahun, penempatan.nik, COUNT(pengajar.id_penempatan) as jml FROM lembaga, kehadiran_lembaga, penempatan WHERE kehadiran_lembaga.id_lembaga = lembaga.id_lembaga and penempatan.id_lembaga = kehadiran_lembaga.id_lembaga and lembaga.id_lembaga = $lembaga kehadiran_lembaga.kategori = 'Pengajar' GROUP BY kehadiran_lembaga.id_kehadiran_lembaga order by kehadiran_lembaga.id_kehadiran_lembaga desc ")->result();
			$list = $this->db->query("SELECT nama_lembaga, kehadiran_lembaga.status, kehadiran_lembaga.kategori, id_kehadiran_lembaga, kehadiran_lembaga.status, kehadiran_lembaga.bulan, kehadiran_lembaga.tahun, pengajar.nik, COUNT(pengajar.id_pengajar) as jml FROM lembaga, kehadiran_lembaga, pengajar WHERE kehadiran_lembaga.id_lembaga = lembaga.id_lembaga and pengajar.id_lembaga = kehadiran_lembaga.id_lembaga and kehadiran_lembaga.kategori = 'Pengajar' and lembaga.id_lembaga = $lembaga and pengajar.status != 'Tidak Aktif' and pengajar.tgl_selesai >= CURDATE() GROUP BY kehadiran_lembaga.id_kehadiran_lembaga order by kehadiran_lembaga.id_kehadiran_lembaga desc ")->result();
		} else {
			// guru dan dosen
			$list = $this->db->query("SELECT nama_lembaga, kehadiran_lembaga.status, kehadiran_lembaga.kategori, id_kehadiran_lembaga, kehadiran_lembaga.status, kehadiran_lembaga.bulan, kehadiran_lembaga.tahun, pengajar.nik, COUNT(pengajar.id_pengajar) as jml FROM lembaga, kehadiran_lembaga, pengajar WHERE kehadiran_lembaga.id_lembaga = lembaga.id_lembaga and pengajar.id_lembaga = kehadiran_lembaga.id_lembaga and kehadiran_lembaga.kategori = 'Pengajar' and pengajar.status != 'Tidak Aktif' and pengajar.tgl_selesai >= CURDATE() GROUP BY kehadiran_lembaga.id_kehadiran_lembaga order by kehadiran_lembaga.id_kehadiran_lembaga desc ")->result();
		}
		$no =1;
		$data = array();
		foreach ($list as $datanya) {
			$encrypted_id = $this->encrypt_url($datanya->id_kehadiran_lembaga);
			$row = array();
			$row[] = $no++;
			$row[] = htmlentities($datanya->nama_lembaga);
			// $row[] = htmlentities($datanya->kategori);
			$row[] = htmlentities($datanya->jml)." org";
			$row[] = htmlentities($datanya->bulan." ".$datanya->tahun);
		if ($datanya->status == 'Belum'){
			$row[] = "<span class='badge badge-secondary'>Belum diisi<span class='ms-1 fa fa-times'></span></span>";
			//pengajar
			$row[] = '<a type="button" class="btn btn-outline-secondary btn-sm" href="#" 
			title="Rekap" onclick="rekap_pengajar('."'".$encrypted_id."'".')"><i class="mdi mdi-file-document-box mr-1" ></i> Isi Rekap Kehadiran</a>';
		}	elseif ($datanya->status == "Sudah") {
			$row[] = "<span class='badge badge-danger'>Belum dikirim <i class='mdi mdi-alert-circle' data-name='mdi-alert-circle'></i></span>";
			$row[] = '<a type="button" class="btn btn-success btn-xs" href="koreksi_pengajar/'.$encrypted_id.'")"><i class="mdi mdi-checkbox-marked-circle mr-1" ></i> Cek Barokah</a>
			 ';
		}  elseif ($datanya->status == "Terkirim" || $datanya->status == 'acc') {
			$row[] = "<span class='badge badge-warning text-dark'>Sedang dikoreksi<span class='ms-1 fa fa-redo'></span></span>";
			$row[] = '<a type="button" class="btn btn-success btn-sm" href="koreksi_pengajar/'.$encrypted_id.'")"><i class="mdi mdi-file-document-box mr-1" ></i> Lihat Rekap Kehadiran</a>';
		} else {
			$row[] = "<span class='badge badge-success'>Sudah ditransfer<span class='ms-1 fa fa-check'></span></span>";
			$row[] = '<a type="button" class="btn btn-info btn-sm" href="koreksi_pengajar/'.$encrypted_id.'"<i class="mdi mdi-file-document-box mr-1" ></i> Lihat Rekap Barokah</a>';
		}
			//add html for action
			
		    $data[] = $row;
		}
		$output = array("data" => $data);
		echo json_encode($output);
	}
	
	public function data_list_log()
	{
		$this->load->helper('url');
		$list = $this->db->get('kehadiran')->result();
		$no =1;
		$data = array();
		foreach ($list as $datanya) {
			$row = array();
			$row[] = $no++;
			$row[] = htmlentities($datanya->bulan)." - ".htmlentities($datanya->tahun);
			$row[] = htmlentities($datanya->id_penempatan);
			$row[] = htmlentities($datanya->id_kehadi);
			$row[] = htmlentities($datanya->jumlah_hadir);
			$row[] = htmlentities($datanya->tgl_input);
			$row[] = '<a type="button" class="btn btn-outline-success btn-sm" href="#" 
			title="Rekap" onclick="edit('."'".$datanya->id_kehadiran."'".')"><i class="mdi mdi-pencil mr-1" ></i> Edit</a> - 
			<a type="button" class="btn btn-outline-danger btn-sm" href="hapus/'.$datanya->id_kehadiran.'">
			<i class="mdi mdi-delete mr-1" ></i> Hapus</a>';
		
			//add html for action
			
		    $data[] = $row;
		}
		$output = array("data" => $data);
		echo json_encode($output);
	}
	
	public function ajax_log_add()
	{
		$data = array(
            'id_kehadiran' 	    => '',
            'bulan' 			=> $this->input->post('bulan'),
            'tahun' 			=> $this->input->post('tahun'),
            'id_penempatan' 	=> $this->input->post('id_penempatan'),
			'jumlah_hadir' 		=> $this->input->post('jumlah_hadir'),
        );

		$simpan = $this->kehormatan_model->create('kehadiran',$data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update(){
        // $this->_validate_edit();
       $data = array(
			'id_penempatan' 	=> $this->input->post('penempatan'), 	
			'id_kehadi' 	=> $this->input->post('kehadiran'), 
			'jumlah_hadir' 	=> $this->input->post('jumlah'),   
        );
        
		$this->db->update('kehadiran',$data, array('id_kehadiran' => $this->input->post('id_kehadiran')));
		echo json_encode(array("status" => TRUE));
	}

	
	public function ajax_edit_log($id)
	{
		$query = $this->db->get_where('kehadiran', array('id_kehadiran' => $id));

		if ($query->num_rows() > 0) {
			$row = $query->row();
			// Assuming $row contains the data for the specific record
			echo json_encode($row);
		} else {
			// If the record with the given $id is not found
			echo json_encode(array('error' => 'Record not found'));
		}
	}


	public function hapus($id)
	{
    $this->db->where('id_kehadiran', $id)->delete('kehadiran');
    
    // Set pesan notifikasi menggunakan flashdata
    $this->session->set_flashdata('success', 'Data berhasil dihapus.');
    
    // Redirect ke halaman yang diinginkan
    redirect('kehadiran/kehadiran_log');
	}

	public function rekap_list()
	{
		$list = $this->db->query("SELECT * from penempatan, lembaga, umana where penempatan.nik = umana.nik and penempatan.id_lembaga = lembaga.id_lembaga and lembaga.id_lembaga = 1 ")->result();
		$no =1;
		$data = array();
		foreach ($list as $datanya) {
			
			$row = array();
			$row[] = $datanya->nik;
			$row[] = htmlentities($datanya->nama_lengkap);
            $row[] = htmlentities($datanya->id_ketentuan);
			$row[] = htmlentities($datanya->nama_lembaga);
			$row[] = "<input type='text' name='hadir[]' class='form-control'> <input type='hidden' name='nik_umana[]' value=".$datanya->nik.">";
			$row[] = "<input type='text' class='form-control'>";
			$row[] = "<input type='text' class='form-control'>";
			//add html for action
			$row[] = '<a type="button" class="btn btn-outline-success btn-sm" href="#" 
			title="Rekap" onclick="rekap('."'".$datanya->id_lembaga."'".')"><i class="mdi mdi-file-document-box mr-1" ></i> Rekap Kehadiran</a>';
		    $data[] = $row;
		}
		$output = array("data" => $data);
		echo json_encode($output);
	}

    public function ajax_add()
	{
		// $this->validate();
		$nik_umana		= $this->input->post('id_penempatan');
		$id_kehadiran_lembaga 	= $this->input->post('id_kehadiran_lembaga');
		$id_penempatan	= $this->input->post('id_penempatan');
		$bulan			= $this->input->post('bulan');
		$tahun			= $this->input->post('tahun');
		$jumlah_hadir	= $this->input->post('jumlah_kehadiran');
		$data = array();
    
		$index = 0; // Set index array awal dengan 0
		foreach($nik_umana as $nik){ // Kita buat perulangan berdasarkan nis sampai data terakhir
		array_push($data, array(
			'id_kehadiran' 	=> '',
			'bulan' 		=> $bulan,
			'tahun'			=> $tahun,
			'id_penempatan'	=> $nik,
			'id_kehadi'		=> $id_kehadiran_lembaga,  // Ambil dan set data nama sesuai index array dari $index
			'jumlah_hadir'	=>$jumlah_hadir[$index],  // Ambil dan set data telepon sesuai index array dari $index
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

		$this->db->insert_batch('kehadiran', $data); 
		//  $this->db->update('kehadiran_lembaga',array('id_kehadiran_lembaga' => $this->input->post('id_kehadiran_lembaga')), $data2);
		$this->db->where('id_kehadiran_lembaga', $this->input->post('id_kehadiran_lembaga'));
		$this->db->update('kehadiran_lembaga', $data2);	

		// $simpan = $this->kehadiran_model->create('lembaga',$data);
		// echo json_encode(array("status" => TRUE));
	}
	
	public function ajax_add_pengajar()
	{
		// $this->validate();
		$nik_umana		= $this->input->post('id_penempatan');
		$id_kehadiran_lembaga 	= $this->input->post('id_kehadiran_lembaga');
		$id_penempatan	= $this->input->post('id_penempatan');
		$bulan			= $this->input->post('bulan');
		$tahun			= $this->input->post('tahun');
		$jumlah_hadir	= $this->input->post('jumlah_kehadiran');
		$jumlah_hadir_15	= $this->input->post('jumlah_kehadiran_15');
		$jumlah_hadir_10	= $this->input->post('jumlah_kehadiran_10');
		$jumlah_hadir_piket	= $this->input->post('jumlah_kehadiran_piket');
		$data = array();
    
		$index = 0; // Set index array awal dengan 0
		foreach($nik_umana as $nik){ // Kita buat perulangan berdasarkan nis sampai data terakhir
		array_push($data, array(
			'id_kehadiran_pengajar' 	=> '',
			'bulan' 		=> $bulan,
			'tahun'			=> $tahun,
			'id_pengajar'	=> $nik,
			'id_kehadiran_lembaga'		=> $id_kehadiran_lembaga,  // Ambil dan set data nama sesuai index array dari $index
			'jumlah_hadir'	=>$jumlah_hadir[$index],  // Ambil dan set data kehadiran sesuai index array dari $index
			'jumlah_hadir_15'	=>$jumlah_hadir_15[$index],
			'jumlah_hadir_10'	=>$jumlah_hadir_10[$index],
			'jumlah_hadir_piket'		=>$jumlah_hadir_piket[$index],
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

		$this->db->insert_batch('kehadiran_pengajar', $data); 
		$this->db->where('id_kehadiran_lembaga', $this->input->post('id_kehadiran_lembaga'));
		$this->db->update('kehadiran_lembaga', $data2);	
	}
	
	public function update_kirim(){
        // $this->_validate_edit();
       $data = array(
			'status' 	=> "Terkirim", 	
		);
        
		$this->db->update('kehadiran_lembaga',$data, array('id_kehadiran_lembaga' => $this->input->post('id_kehadiran_lembaga')));
		echo json_encode(array("status" => TRUE));
	}
	
	public function koreksi($id){
	    $decrypted_id = $this->decrypt_url($id);
		$list = $this->db->query("select lembaga.id_lembaga, id_kehadiran, kehadiran_lembaga.status, ketentuan_barokah.id_ketentuan, id_bidang, tunj_anak, tunj_mp, umana.gelar_depan, umana.gelar_belakang, kehormatan, kehadiran_lembaga.file, tunj_kel, id_kehadiran_lembaga, nama_lengkap, nama_jabatan, status_nikah, tmt_struktural, kehadiran.id_penempatan, kehadiran.bulan, kehadiran.tahun, jumlah_hadir, nama_lembaga, barokah, nominal_transport from umana, penempatan, kehadiran, kehadiran_lembaga, lembaga, ketentuan_barokah, transport WHERE 
		kehadiran_lembaga.id_kehadiran_lembaga = kehadiran.id_kehadi and 
		penempatan.id_penempatan = kehadiran.id_penempatan and 
		penempatan.nik = umana.nik and 
		penempatan.id_lembaga = lembaga.id_lembaga and 
		penempatan.id_ketentuan = ketentuan_barokah.id_ketentuan and 
		penempatan.kategori_trans = transport.id_transport and 
		DATEDIFF(NOW(), penempatan.tgl_mulai) < penempatan.tgl_selesai and
		kehadiran_lembaga.id_kehadiran_lembaga = $decrypted_id order by ketentuan_barokah.id_ketentuan asc ")->result();
		$no = 1;
		$tunkel_get = $this->db->get('tunkel')->result();
		$tunj_anak_get = $this->db->get('tunjanak')->result();
		// $kehormatan = $this->db->query('select * from ');
		$this->Login_model->getsqurity() ;

		$isi['css'] 	= 'Kehadiran/Css';
		$isi['content'] = 'Validasi/Jumlah_kehadiran';
		$isi['ajax'] 	= 'Validasi/Ajax';
		$isi['isitunkel']  = $tunkel_get;
		$isi['isitunj_anak']  = $tunj_anak_get;
		$isi['isilist']  = $list;
		$this->load->view('Template',$isi);
		
	}
	
	public function koreksi_pengajar($id){
	    $decrypted_id = $this->decrypt_url($id);
		//pengajar
		$list2 = $this->db->query("select jumlah_hadir_piket, jumlah_hadir_15, jumlah_hadir_10, jafung, lembaga.id_lembaga, kehadiran_lembaga.status, status_sertifikasi, walkes, kehadiran_pengajar.id_kehadiran_pengajar, pengajar.kategori, jabatan_akademik, jumlah_sks, status_sertifikasi, ijazah_terakhir, id_bidang, tunj_anak, umana.gelar_depan, umana.gelar_belakang, kehormatan, kehadiran_lembaga.file, tunj_kel, kehadiran_lembaga.id_kehadiran_lembaga, 
		nama_lengkap, status_nikah, tmt_dosen, tmt_guru, tmt_maif, kehadiran_pengajar.id_pengajar, kehadiran_pengajar.bulan, kehadiran_pengajar.tahun, jumlah_hadir, nama_lembaga, nominal_transport from umana, pengajar, kehadiran_pengajar, kehadiran_lembaga,
		lembaga, transport WHERE 
		kehadiran_lembaga.id_kehadiran_lembaga = kehadiran_pengajar.id_kehadiran_lembaga and 
		pengajar.id_pengajar = kehadiran_pengajar.id_pengajar and 
		pengajar.nik = umana.nik and 
		pengajar.id_lembaga = lembaga.id_lembaga and 
		pengajar.kategori_trans = transport.id_transport and 
		DATEDIFF(NOW(), pengajar.tgl_mulai) < pengajar.tgl_selesai and
		kehadiran_lembaga.id_kehadiran_lembaga = $decrypted_id order by nama_lengkap asc ")->result();
		$no = 1;
		$tunkel_get = $this->db->get('tunkel')->result();
		$tunj_anak_get = $this->db->get('tunjanak')->result();
		// $kehormatan = $this->db->query('select * from ');
		$this->Login_model->getsqurity() ;

		$isi['css'] 	= 'Kehormatan/Css';
		$isi['content'] = 'Validasi/Jumlah_kehadiran_pengajar';
		$isi['ajax'] 	= 'Validasi/Ajax';
		$isi['isitunkel']  = $tunkel_get;
		$isi['isitunj_anak']  = $tunj_anak_get;
		$isi['isilist']  = $list2;
		$this->load->view('Template',$isi);
		
	}
	
	public function get_absen($id)
	{
		$this->load->helper('url');
		$data_elemen = $this->kehadiran_model->get_lembaga($id);
		$list = $this->kehadiran_model->get_datatables_rincian($id);
		$data = array();
    	$no=1;
		$html_item = '';
		foreach ($list as $sow) {
			$html_item .= '<tr>';
			$html_item .= '<td><h6>'.$no++.'</h6></td>';
			$html_item .= '<td><h6>'.$sow->nama_lengkap.'</h6> <input type="hidden" name="id_kehadiran[]" value="'.$sow->id_kehadiran.'" ></td>';
			$html_item .= '<td><h6>'.$sow->nama_jabatan.'</h6></td>';
			$html_item .= '<td><h6>'.$sow->jumlah_hadir.'</h6><input type="hidden" name="diterima[]" value="'.rupiah($sow->diterima).'" ></td>';
			$html_item .= '</tr>';
		}
		$this->output->set_output(json_encode(array("data_elemen" => $data_elemen, "html_item" => $html_item)));
	}
	
	
	public function get_total_absen($id)
	{
		$this->load->helper('url');
		$data_elemen = $this->kehadiran_model->get_lembaga($id);
		$list = $this->kehadiran_model->get_jumlah_hadir($id);
		$data = array();
    	$no=1;
		$html_item = '';
		foreach ($list as $sow) {
			$html_item .= '<tr>';
			$html_item .= '<td><h6>'.$no++.'</h6></td>';
			$html_item .= '<td><h6>'.$sow->nama_lengkap.'</h6> </td>';
			$html_item .= '<td><h6>'.$sow->nama_jabatan.'</h6></td>';
			$html_item .= '<td><h6>'.$sow->jumlah_hadir.'</h6></td>';
			$html_item .= '</tr>';
		}
		$this->output->set_output(json_encode(array("data_elemen" => $data_elemen, "html_item" => $html_item)));
	}

	public function blanko_add()
	{
		$this->db->where('id_lembaga', $this->input->post('id_lembaga'));
		$this->db->where('bulan', $this->input->post('bulan'));
		$this->db->where('tahun', $this->input->post('tahun'));
		$this->db->where('kategori', 'Struktural');
		$existingData = $this->db->get('kehadiran_lembaga')->row();
	
		if ($existingData) { 
			echo json_encode(array("status" => false));
		}else {
			// $this->_validate();
			$data = array(
				'id_kehadiran_lembaga' 	=> '',
				'id_lembaga' 	=> $this->input->post('id_lembaga'),
				'kategori' 	    => 'Struktural',
				'bulan' 	    => $this->input->post('bulan'),
				'tahun' 	    => $this->input->post('tahun'),
				'status' 	    => 'Belum',
			);
			$simpan = $this->kehadiran_model->create('kehadiran_lembaga',$data);
			echo json_encode(array("status" => TRUE));
		}
	}
	
	public function blanko_pengajar_add()
	{
		$this->db->where('id_lembaga', $this->input->post('id_lembaga_pengajar'));
		$this->db->where('bulan', $this->input->post('bulan_pengajar'));
		$this->db->where('tahun', $this->input->post('tahun_pengajar'));
		$this->db->where('kategori', 'Pengajar');
		$existingData = $this->db->get('kehadiran_lembaga')->row();
	
		if ($existingData) { 
			echo json_encode(array("status" => false));
		}else {
			// $this->_validate();
			$data = array(
				'id_kehadiran_lembaga' 	=> '',
				'id_lembaga' 	=> $this->input->post('id_lembaga_pengajar'),
				'kategori' 	    => $this->input->post('kategori_pengajar'),
				'bulan' 	    => $this->input->post('bulan_pengajar'),
				'tahun' 	    => $this->input->post('tahun_pengajar'),
				'status' 	    => 'Belum',
			);
			$simpan = $this->kehadiran_model->create('kehadiran_lembaga',$data);
			echo json_encode(array("status" => TRUE));
		}
	}


	public function ajax_edit($id)
	{
		$data = $this->kehadiran_model->get_by_id($id);
		echo json_encode($data);
	}
	
	public function cetak($id){
		$list2 = $this->db->query("select  nama_pimpinan, id_kehadiran, jabatan_lembaga, ketentuan_barokah.id_ketentuan, id_bidang, tunj_anak, tunj_mp, umana.gelar_depan, umana.gelar_belakang, kehormatan, kehadiran_lembaga.file, tunj_kel, id_kehadiran_lembaga, nama_lengkap, nama_jabatan, status_nikah, tmt_struktural, kehadiran.id_penempatan, kehadiran.bulan, kehadiran.tahun, jumlah_hadir, nama_lembaga, barokah, nominal_transport from umana, penempatan, kehadiran, kehadiran_lembaga, lembaga, ketentuan_barokah, transport WHERE 
		kehadiran_lembaga.id_kehadiran_lembaga = kehadiran.id_kehadi and 
		penempatan.id_penempatan = kehadiran.id_penempatan and 
		penempatan.nik = umana.nik and 
		penempatan.id_lembaga = lembaga.id_lembaga and 
		penempatan.id_ketentuan = ketentuan_barokah.id_ketentuan and 
		penempatan.kategori_trans = transport.id_transport and 
		DATEDIFF(NOW(), penempatan.tgl_mulai) < penempatan.tgl_selesai and
		kehadiran_lembaga.id_kehadiran_lembaga = $id order by ketentuan_barokah.id_ketentuan asc ")->result();
		$tunkel_get = $this->db->get('tunkel')->result();
		$tunj_anak_get = $this->db->get('tunjanak')->result();
		// $kehormatan = $this->db->query('select * from ');
		$this->Login_model->getsqurity() ;

	
		$isi['isitunkel']  = $tunkel_get;
		$isi['isitunj_anak']  = $tunj_anak_get;
		$isi['isilist']  = $list2;
		$this->load->view('Kehadiran/Cetak',$isi);	
	}
	
	public function cetak_pengajar($id){
		$list2 = $this->db->query("select tmt_maif, status_aktif, jumlah_hadir_piket, jumlah_hadir_15, jumlah_hadir_10, jafung, kehadiran_lembaga.status, status_sertifikasi, walkes, kehadiran_pengajar.id_kehadiran_pengajar, pengajar.kategori, jabatan_akademik, jumlah_sks, status_sertifikasi, ijazah_terakhir, id_bidang, tunj_anak, umana.gelar_depan, umana.gelar_belakang, kehormatan, kehadiran_lembaga.file, tunj_kel, kehadiran_lembaga.id_kehadiran_lembaga, 
		nama_lengkap, status_nikah, tmt_dosen, tmt_guru, kehadiran_pengajar.id_pengajar, kehadiran_pengajar.bulan, kehadiran_pengajar.tahun, jumlah_hadir, nama_lembaga, nominal_transport from umana, pengajar, kehadiran_pengajar, kehadiran_lembaga,
		lembaga, transport WHERE 
		kehadiran_lembaga.id_kehadiran_lembaga = kehadiran_pengajar.id_kehadiran_lembaga and 
		pengajar.id_pengajar = kehadiran_pengajar.id_pengajar and 
		pengajar.nik = umana.nik and 
		pengajar.id_lembaga = lembaga.id_lembaga and 
		pengajar.kategori_trans = transport.id_transport and 
		DATEDIFF(NOW(), pengajar.tgl_mulai) < pengajar.tgl_selesai and
		kehadiran_lembaga.id_kehadiran_lembaga = $id order by nama_lengkap asc ")->result();
		$tunkel_get = $this->db->get('tunkel')->result();
		$tunj_anak_get = $this->db->get('tunjanak')->result();
		// $kehormatan = $this->db->query('select * from ');
		$this->Login_model->getsqurity() ;

		$isi['isitunkel']  = $tunkel_get;
		$isi['isitunj_anak']  = $tunj_anak_get;
		$isi['isilist']  = $list2;
		$this->load->view('Kehadiran_pengajar/Cetak',$isi);	
	}
	
	public function cetak_potongan_pengajar($id){
    // Query untuk mendapatkan data potongan pengajar
    $query = "
        SELECT 
            u.nama_lengkap, 
            pot.nama_potongan, 
            pp.nominal_potongan,
            l.nama_lembaga,
            l.id_bidang,
            p.tgl_mulai,
            p.tgl_selesai
        FROM 
            umana u
        JOIN 
            pengajar p ON u.nik = p.nik
        JOIN 
            potongan_pengajar pp ON pp.id_pengajar = p.id_pengajar
        JOIN 
            potongan pot ON pp.jenis_potongan = pot.id_potongan
        JOIN
            lembaga l ON p.id_lembaga = l.id_lembaga
        WHERE 
            pp.max_periode_potongan >= DATE_FORMAT(NOW(), '%Y-%m-01')
            AND p.id_lembaga = ?
            AND DATEDIFF(NOW(), p.tgl_mulai) < p.tgl_selesai
        ORDER BY 
            u.nama_lengkap ASC;
    ";

    // Menjalankan query dengan parameter binding
    $list2 = $this->db->query($query, array($id))->result();

    // Keamanan login
    $this->Login_model->getsqurity();

    // Menyimpan hasil query dalam array untuk dikirim ke view
    $isi['isilist'] = $list2;

    // Memuat view dengan data yang sudah dipersiapkan
    $this->load->view('Kehadiran_pengajar/Potongan_pelajar', $isi); 
}

    public function cetak_potongan_struktural($id){
		// Query to get the deduction data of the teachers
		$query = "
			SELECT 
				u.nama_lengkap, 
				pot.nama_potongan, 
				pp.nominal_potongan,
				l.nama_lembaga,
				l.id_bidang
			FROM 
				umana u
			JOIN 
				penempatan p ON u.nik = p.nik
			JOIN 
				potongan_umana pp ON pp.id_penempatan = p.id_penempatan
			JOIN 
				potongan pot ON pp.jenis_potongan = pot.id_potongan
			JOIN
				lembaga l ON p.id_lembaga = l.id_lembaga
			WHERE 
				pp.max_periode_potongan >= DATE_FORMAT(NOW(), '%Y-%m-01')
				AND p.id_lembaga = ?
			ORDER BY u.nama_lengkap ASC
		";
	
		// Execute the query with parameter binding
		$list2 = $this->db->query($query, array($id))->result();
	
		// Login security
		$this->Login_model->getsqurity();
	
		// Store the query result in an array to send to the view
		$isi['isilist'] = $list2;
	
		// Load the view with the prepared data
		$this->load->view('Kehadiran/Potongan_struktural', $isi);
	}


	public function _do_upload()
	{
		$date = new DateTime();
		$timezone = time() + (60 * 60 * 7);
		$config['upload_path']          = 'upload/';
        $config['allowed_types']        = 'pdf';
        $config['max_size']             = 0; //set max size allowed in Kilobyte
        $config['file_name']            = random_string('alnum',50).$date->getTimestamp(); //just milisecond timestamp fot unique name

        $this->load->library('upload', $config);

        if(!$this->upload->do_upload('file')) //upload and validate
        {
            $data['inputerror'][] = 'file';
			$data['error_string'][] = 'Upload error: File harus PDF  '; //show ajax error
			$data['status'] = FALSE;
			echo json_encode($data);
			exit();
		}
		return $this->upload->data('file_name');
	}

	private function validate()
    {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        if ($this->input->post('file') == '') {
            $data['inputerror'][] = 'file';
            $data['error_string'][] = 'Berkas harus diisi';
            $data['status'] = FALSE;
        }

       if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }

   


}
