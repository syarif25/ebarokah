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
			$jabatanUser = $this->session->userdata('jabatan');

			if ($datanya->status == 'Belum') {
				$row[] = "<span class='badge badge-secondary'>Belum diisi <i class='fa fa-times ms-1'></i></span>";
			
				$aksi  = '<a type="button" class="btn btn-outline-secondary btn-sm" href="#" ';
				$aksi .= 'title="Rekap" onclick="rekap(\''.$encrypted_id.'\')">';
				$aksi .= '<i class="mdi mdi-file-document-box mr-1"></i> Isi Rekap Kehadiran</a>';
			
				$row[] = $aksi;
			
			} elseif ($datanya->status == "Sudah" || $datanya->status == "Revisi") {
                $status_label = ($datanya->status == "Revisi") ? "Revisi / Belum dikirim" : "Belum dikirim";
				$row[] = "<span class='badge badge-danger'>".$status_label." <i class='fa fa-exclamation-circle ms-1'></i></span>";
			
				// rangkai aksi dasar
				$aksi  = '<a type="button" class="btn btn-success btn-xs" href="Validasi_struktural/koreksi/'.$encrypted_id.'">';
				$aksi .= '<i class="mdi mdi-checkbox-marked-circle mr-1"></i> Cek Barokah</a> ';
				
			
				// tambahkan tombol reset hanya untuk SuperAdmin/Evaluasi
				if (in_array($jabatanUser, ['SuperAdmin', 'Evaluasi'])) {
				    $aksi .= ' - <button type="button" class="btn btn-outline-info btn-xs ml-1" onclick="lihat_riwayat('."'".$encrypted_id."'".')"><i class="fa fa-history"></i> Riwayat</button>';
					$aksi .= ' - <button type="button" class="btn btn-sm btn-outline-danger ml-2 btn-reset" '.
							 'data-id="'.$datanya->id_kehadiran_lembaga.'" '.
							 'data-status="'.$datanya->status.'">'.
							 '<i class="fa fa-undo mr-1"></i> Reset</button>';
				}
			
				$row[] = $aksi;
			
			} elseif ($datanya->status == "Terkirim" || $datanya->status == "acc") {
				$row[] = "<span class='badge badge-warning text-dark'>Sedang dikoreksi <i class='fa fa-sync ms-1'></i></span>";
			
				$aksi  = '<a type="button" class="btn btn-success btn-xs" href="Validasi_struktural/koreksi/'.$encrypted_id.'">';
				$aksi .= '<i class="mdi mdi-checkbox-marked-circle mr-1"></i> Cek Barokah</a> ';
			
				if (in_array($jabatanUser, ['SuperAdmin', 'Evaluasi'])) {
				    $aksi .= ' - <button type="button" class="btn btn-outline-info btn-xs ml-1" onclick="lihat_riwayat('."'".$encrypted_id."'".')"><i class="fa fa-history"></i> Riwayat</button>';
					$aksi .= ' - <button type="button" class="btn btn-sm btn-outline-danger ml-2 btn-reset" '.
							 'data-id="'.$datanya->id_kehadiran_lembaga.'" '.
							 'data-status="'.$datanya->status.'">'.
							 '<i class="fa fa-undo mr-1"></i> Reset</button>';
				}
			
				$row[] = $aksi;
			
			} else {
				$row[] = "<span class='badge badge-success'>Sudah transfer <i class='fa fa-check ms-1'></i></span>";
			
				$aksi  = '<a type="button" class="btn btn-info btn-sm" href="Validasi_struktural/koreksi/'.$encrypted_id.'">';
				$aksi .= '<i class="mdi mdi-file-document-box mr-1"></i> Lihat Rekap Barokah</a>';
				if (in_array($jabatanUser, ['SuperAdmin', 'Evaluasi'])) {
				    $aksi .= ' - <button type="button" class="btn btn-outline-info btn-sm ml-1" onclick="lihat_riwayat('."'".$encrypted_id."'".')"><i class="fa fa-history"></i> Riwayat</button>';
					$aksi .= ' - <button type="button" class="btn btn-sm btn-outline-danger ml-2 btn-reset" '.
							 'data-id="'.$datanya->id_kehadiran_lembaga.'" '.
							 'data-status="'.$datanya->status.'">'.
							 '<i class="fa fa-undo mr-1"></i> Reset</button>';
				}
				$row[] = $aksi;
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
			$row[] = htmlentities($datanya->bulan);
			$row[] = htmlentities($datanya->tahun);
		if ($datanya->status == 'Belum'){
			$row[] = "<span class='badge badge-secondary'>Belum diisi<span class='ms-1 fa fa-times'></span></span>";
			//pengajar
			$row[] = '<a type="button" class="btn btn-outline-secondary btn-sm" href="#" 
			title="Rekap" onclick="rekap_pengajar('."'".$encrypted_id."'".')"><i class="mdi mdi-file-document-box mr-1" ></i> Isi Rekap Kehadiran</a>';
		}	elseif ($datanya->status == "Sudah" || $datanya->status == "Revisi") {
            $status_label = ($datanya->status == "Revisi") ? "Revisi / Belum dikirim" : "Belum dikirim";
			$row[] = "<span class='badge badge-danger'>".$status_label." <i class='mdi mdi-alert-circle' data-name='mdi-alert-circle'></i></span>";
			$aksi = '<a type="button" class="btn btn-success btn-xs" href="koreksi_pengajar/'.$encrypted_id.'")"><i class="mdi mdi-checkbox-marked-circle mr-1" ></i> Cek Barokah</a>';
			if (in_array($this->session->userdata('jabatan'), ['SuperAdmin', 'Evaluasi'])) {
			    $aksi .= ' - <button type="button" class="btn btn-outline-info btn-xs ml-1" onclick="lihat_riwayat('."'".$encrypted_id."'".')"><i class="fa fa-history"></i> Riwayat</button>';
			}
			$row[] = $aksi;
		}  elseif ($datanya->status == "Terkirim" || $datanya->status == 'acc') {
			$row[] = "<span class='badge badge-warning text-dark'>Sedang dikoreksi<span class='ms-1 fa fa-redo'></span></span>";
			$aksi = '<a type="button" class="btn btn-success btn-sm" href="koreksi_pengajar/'.$encrypted_id.'")"><i class="mdi mdi-file-document-box mr-1" ></i> Lihat Rekap Kehadiran</a>';
			if (in_array($this->session->userdata('jabatan'), ['SuperAdmin', 'Evaluasi'])) {
			    $aksi .= ' - <button type="button" class="btn btn-outline-info btn-sm ml-1" onclick="lihat_riwayat('."'".$encrypted_id."'".')"><i class="fa fa-history"></i> Riwayat</button>';
			}
			$row[] = $aksi;
		} else {
			$row[] = "<span class='badge badge-success'>Sudah ditransfer<span class='ms-1 fa fa-check'></span></span>";
			$aksi = '<a type="button" class="btn btn-info btn-sm" href="koreksi_pengajar/'.$encrypted_id.'"<i class="mdi mdi-file-document-box mr-1" ></i> Lihat Rekap Barokah</a>';
			if (in_array($this->session->userdata('jabatan'), ['SuperAdmin', 'Evaluasi'])) {
			    $aksi .= ' - <button type="button" class="btn btn-outline-info btn-sm ml-1" onclick="lihat_riwayat('."'".$encrypted_id."'".')"><i class="fa fa-history"></i> Riwayat</button>';
			}
			$row[] = $aksi;
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
		$this->Login_model->getsqurity();
	
		// Ambil input dasar
		$id_kehadiran_lembaga = $this->input->post('id_kehadiran_lembaga');
		$bulan  = $this->input->post('bulan');
		$tahun  = $this->input->post('tahun');
		$id_penempatan   = $this->input->post('id_penempatan');
		$jumlah_hadir    = $this->input->post('jumlah_kehadiran');
	
		// Validasi minimal
		if (empty($id_kehadiran_lembaga) || empty($bulan) || empty($tahun)) {
			echo json_encode(["status"=>false, "message"=>"Konteks periode tidak lengkap."]);
			return;
		}
	
		// Wajib file PDF
		if (empty($_FILES['file']['name'])) {
			echo json_encode([
				"status"  => false,
				"message" => "File absensi (PDF) wajib diunggah sebelum menyimpan."
			]);
			return;
		}
	
		// Upload file
		$uploadedFile = $this->_do_upload();
	
		// Susun data batch
		$data = [];
		if (is_array($id_penempatan)) {
			foreach ($id_penempatan as $i => $idp) {
				// Kalau jumlah hadir kosong → simpan 0
				$jumlah = isset($jumlah_hadir[$i]) && $jumlah_hadir[$i] !== '' ? (int)$jumlah_hadir[$i] : 0;
				if ($jumlah < 0) $jumlah = 0;
	
				$data[] = [
					'bulan'         => $bulan,
					'tahun'         => $tahun,
					'id_penempatan' => $idp,
					'id_kehadi'     => $id_kehadiran_lembaga,
					'jumlah_hadir'  => $jumlah,
				];
			}
		}
	
		// Transaksi
		$this->db->trans_begin();
	
		// Hapus data lama (idempotent)
		$this->db->where('id_kehadi', $id_kehadiran_lembaga)->delete('kehadiran');
	
		if (!empty($data)) {
			$this->db->insert_batch('kehadiran', $data);
		}
	
		// Update status dan file ke tabel periode
		$this->db->where('id_kehadiran_lembaga', $id_kehadiran_lembaga)
				 ->update('kehadiran_lembaga', [
					'status' => 'Sudah',
					'file'   => $uploadedFile
				 ]);
	
		// Commit transaksi
		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			echo json_encode(["status"=>false, "message"=>"Gagal menyimpan data kehadiran."]);
		} else {
			$this->db->trans_commit();
			
			$this->load->helper('hitung_barokah_helper');
			catat_riwayat_barokah($id_kehadiran_lembaga, 'Sudah', 'Admin Lembaga menyimpan Draf kehadiran Struktural.');

			echo json_encode(["status"=>true, "message"=>"Rekap kehadiran berhasil disimpan."]);
		}
	}
	

	
	public function ajax_add_pengajar()
	{
		$this->load->helper('string');

		// Ambil input utama
		$id_kehadiran_lembaga = $this->input->post('id_kehadiran_lembaga');
		$bulan  = $this->input->post('bulan');
		$tahun  = $this->input->post('tahun');
		$id_pengajar = $this->input->post('id_penempatan'); // dari form
		$jumlah_hadir = $this->input->post('jumlah_kehadiran');
		$jumlah_hadir_15 = $this->input->post('jumlah_kehadiran_15');
		$jumlah_hadir_10 = $this->input->post('jumlah_kehadiran_10');
		$jumlah_hadir_piket = $this->input->post('jumlah_kehadiran_piket');

		// 🔒 Validasi file wajib
		if (empty($_FILES['file']['name'])) {
			echo json_encode([
				"status" => false,
				"message" => "File absensi (PDF) wajib diunggah sebelum menyimpan."
			]);
			return;
		}

		// Upload file PDF
		$uploadedFile = $this->_do_upload();

		// Susun data batch kehadiran
		$data = [];
		if (is_array($id_pengajar)) {
			foreach ($id_pengajar as $i => $idp) {
				// Jika input kosong → set ke 0
				$hadir_normal = isset($jumlah_hadir[$i]) && $jumlah_hadir[$i] !== '' ? (int)$jumlah_hadir[$i] : 0;
				$hadir_15 = isset($jumlah_hadir_15[$i]) && $jumlah_hadir_15[$i] !== '' ? (int)$jumlah_hadir_15[$i] : 0;
				$hadir_10 = isset($jumlah_hadir_10[$i]) && $jumlah_hadir_10[$i] !== '' ? (int)$jumlah_hadir_10[$i] : 0;
				$hadir_piket = isset($jumlah_hadir_piket[$i]) && $jumlah_hadir_piket[$i] !== '' ? (int)$jumlah_hadir_piket[$i] : 0;

				// Normalisasi: nilai tidak boleh negatif
				$hadir_normal = max(0, $hadir_normal);
				$hadir_15 = max(0, $hadir_15);
				$hadir_10 = max(0, $hadir_10);
				$hadir_piket = max(0, $hadir_piket);

				$data[] = [
					'bulan' => $bulan,
					'tahun' => $tahun,
					'id_pengajar' => $idp,
					'id_kehadiran_lembaga' => $id_kehadiran_lembaga,
					'jumlah_hadir' => $hadir_normal,
					'jumlah_hadir_15' => $hadir_15,
					'jumlah_hadir_10' => $hadir_10,
					'jumlah_hadir_piket' => $hadir_piket
				];
			}
		}

		// Siapkan update lembaga
		$updateLembaga = [
			'file' => $uploadedFile,
			'status' => 'Sudah'
		];

		// 🚀 Transaksi agar aman
		$this->db->trans_begin();

		// Hapus data lama agar tidak duplikat
		$this->db->where('id_kehadiran_lembaga', $id_kehadiran_lembaga)
				->delete('kehadiran_pengajar');

		// Masukkan data baru
		if (!empty($data)) {
			$this->db->insert_batch('kehadiran_pengajar', $data);
		}

		// Update lembaga
		$this->db->where('id_kehadiran_lembaga', $id_kehadiran_lembaga)
				->update('kehadiran_lembaga', $updateLembaga);

		// Commit/rollback
		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			echo json_encode([
				"status" => false,
				"message" => "Gagal menyimpan data kehadiran pengajar."
			]);
		} else {
			$this->db->trans_commit();
			
			$this->load->helper('hitung_barokah_helper');
			catat_riwayat_barokah($id_kehadiran_lembaga, 'Sudah', 'Admin Lembaga menyimpan Draf kehadiran Pengajar.');

			echo json_encode([
				"status" => true,
				"message" => "Rekap kehadiran pengajar berhasil disimpan."
			]);
		}
	}

	
	public function update_kirim(){
        // $this->_validate_edit();
       $data = array(
			'status' 	=> "Terkirim", 	
		);
        
		$id_keh = $this->input->post('id_kehadiran_lembaga');
		$this->db->update('kehadiran_lembaga',$data, array('id_kehadiran_lembaga' => $id_keh));
		
		$this->load->helper('hitung_barokah_helper');
		catat_riwayat_barokah($id_keh, 'Terkirim', 'Dokumen terkirim ke meja Evaluator untuk diperiksa.');

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
			
			$this->load->helper('hitung_barokah_helper');
			catat_riwayat_barokah($simpan, 'Belum', 'Blanko absensi Struktural dibuat.');
			
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

			$this->load->helper('hitung_barokah_helper');
			catat_riwayat_barokah($simpan, 'Belum', 'Blanko absensi Pengajar dibuat.');

			echo json_encode(array("status" => TRUE));
		}
	}

    public function ajax_riwayat($id)
    {
        $decrypted_id = $this->decrypt_url($id);
        
        // Ambil riwayat log beserta join pelengkap tabel admin/umana untuk nama jika memungkinkan
        $this->db->select('log_riwayat_barokah.*'); 
        $this->db->from('log_riwayat_barokah');
        $this->db->where('id_kehadiran_lembaga', $decrypted_id);
        $this->db->order_by('waktu_eksekusi', 'DESC');
        $logs = $this->db->get()->result();

        $html = '';
        if(empty($logs)){
            $html = '<div class="text-center text-muted py-4"><i class="fa fa-info-circle fa-2x mb-2 d-block"></i>Belum ada riwayat tercatat.</div>';
        } else {
            foreach($logs as $log) {
                $status_class = 'status-Belum';
                $icon = 'fa-circle';
                $title = $log->status_aksi;
                $text_color = 'text-dark';
                
                if($log->status_aksi == 'Belum'){
                    $status_class = 'status-Belum'; $icon = 'fa-file-o'; $title = 'Blanko Dibuat'; $text_color = 'text-secondary';
                } elseif($log->status_aksi == 'Sudah'){
                    $status_class = 'status-Sudah'; $icon = 'fa-save'; $title = 'Disimpan (Draf)'; $text_color = 'text-info';
                } elseif($log->status_aksi == 'Terkirim'){
                    $status_class = 'status-Terkirim'; $icon = 'fa-paper-plane'; $title = 'Terkirim ke Pimpinan / Evaluator'; $text_color = 'text-warning';
                } elseif($log->status_aksi == 'Revisi'){
                    $status_class = 'status-Revisi'; $icon = 'fa-undo'; $title = 'Dikembalikan / Revisi (Ditolak)'; $text_color = 'text-danger';
                } elseif($log->status_aksi == 'acc'){
                    $status_class = 'status-acc'; $icon = 'fa-check-circle'; $title = 'Disetujui (ACC)'; $text_color = 'text-success';
                }
                
                $date_fmt = date('d M Y H:i:s', strtotime($log->waktu_eksekusi));
                $admin_teks = ($log->id_pengguna) ? "diekseskusi oleh User ID: ".$log->id_pengguna : "diekseskusi oleh Sistem";
                
                $html .= '<li class="timeline-item">
                            <div class="timeline-indicator '.$status_class.'"></div>
                            <div class="timeline-content shadow-sm">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="timeline-title '.$text_color.'"><i class="fa '.$icon.' mr-1"></i> '.$title.'</span>
                                <span class="timeline-date"><i class="fa fa-clock-o"></i> '.$date_fmt.'</span>
                            </div>
                            <div class="timeline-user mb-2 text-muted" style="font-size: 0.8rem;"><i class="fa fa-user-circle-o"></i> '.$admin_teks.'</div>';
                
                if(!empty($log->catatan_log)) {
                    $html .= '<p class="mb-0 small text-dark border-top pt-2 mt-2" style="background: #f8f9fa; padding: 6px; border-radius: 4px;"><em>"'.htmlentities($log->catatan_log).'"</em></p>';
                }
                $html .= '</div></li>';
            }
        }

        echo json_encode(['status' => true, 'html' => $html]);
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

    // Folder target sejajar index.php → /uploads
    $uploadDir = rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR;

    // Buat folder jika belum ada
    if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0755, true);
    }

    // Cek lagi (kalau gagal buat)
    if (!is_dir($uploadDir)) {
        echo json_encode([
            'status'       => FALSE,
            'inputerror'   => ['file'],
            'error_string' => ['Folder uploads tidak ditemukan & gagal dibuat. Periksa path/permission.']
        ]);
        exit();
    }

    // Cek tulis
    if (!is_writable($uploadDir)) {
        echo json_encode([
            'status'       => FALSE,
            'inputerror'   => ['file'],
            'error_string' => ['Folder uploads tidak bisa ditulis. Set permission 755/775.']
        ]);
        exit();
    }

    $config['upload_path']      = $uploadDir;
    $config['allowed_types']    = 'pdf|PDF';
    $config['max_size']         = 0;
    $config['file_name']        = random_string('alnum', 50) . $date->getTimestamp();
    $config['file_ext_tolower'] = TRUE;
    $config['detect_mime']      = TRUE;

    $this->load->library('upload', $config);

    if (!$this->upload->do_upload('file')) {
        $err = strip_tags($this->upload->display_errors('', ''));
        echo json_encode([
            'status'       => FALSE,
            'inputerror'   => ['file'],
            'error_string' => [$err ?: 'Upload gagal. Pastikan file PDF & ukuran tidak melebihi batas.']
        ]);
        exit();
    }

    // Kembalikan hanya nama file; simpan di DB seperti biasa
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
