<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan_lembaga extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Login_model');
		$this->load->model('Laporan_model');
        $this->load->helper('rupiah_helper');
        $this->load->library('user_agent');
	}

	public function encrypt_url($string) {
		$key = '874jzceroier38!@#%*bjkdwdw)'; // Key enkripsi
		$encrypted_string = base64_encode($string . $key);
		$encrypted_string = str_replace(array('+', '/', '='), array('-', '_', ''), $encrypted_string);
		return $encrypted_string;
	}
	
	public function decrypt_url($string) {
		$key = '874jzceroier38!@#%*bjkdwdw)'; // Key enkripsi
		$string = str_replace(array('-', '_'), array('+', '/'), $string);
		$string = base64_decode($string);
		$string = str_replace($key, '', $string);
		return $string;
	}

	public function index(){
		$this->Login_model->getsqurity() ;
		
		// Get filter options from database
		$isi['lembaga_list'] = $this->get_lembaga_list();
		$isi['tahun_list'] = $this->get_tahun_list();
		
		$isi['css'] 	= 'laporan_lembaga/Css';
		$isi['content'] = 'laporan_lembaga/Index';
		$isi['ajax'] 	= 'laporan_lembaga/Ajax';
		$this->load->view('Template',$isi);
	}
	
	public function per_bulan(){
		$this->Login_model->getsqurity() ;
		
		$isi['css'] 	= 'laporan_lembaga/Css';
		$isi['content'] = 'laporan_lembaga/Per_bulan';
		$isi['ajax'] 	= 'laporan_lembaga/Ajax_per_bulan';
		$this->load->view('Template',$isi);
	}
	
	private function get_lembaga_list() {
		$query = $this->db->query("
			SELECT DISTINCT lembaga.id_lembaga, lembaga.nama_lembaga 
			FROM kehadiran_lembaga 
			JOIN lembaga ON kehadiran_lembaga.id_lembaga = lembaga.id_lembaga 
			ORDER BY lembaga.nama_lembaga ASC
		");
		return $query->result();
	}
	
	private function get_tahun_list() {
		// Get distinct tahun and extract valid year values
		$query = $this->db->query("
			SELECT DISTINCT tahun 
			FROM kehadiran_lembaga 
			WHERE tahun IS NOT NULL 
			  AND tahun != '' 
			  AND tahun REGEXP '^[0-9]'
			ORDER BY tahun DESC
		");
		
		// Process results to extract clean year values
		$years = array();
		$seen = array();
		
		foreach ($query->result() as $row) {
			$tahun_str = $row->tahun;
			
			// Extract 4-digit years from string (handles "2025", "2025/2026", etc)
			preg_match_all('/\b(20[0-9]{2})\b/', $tahun_str, $matches);
			
			if (!empty($matches[1])) {
				foreach ($matches[1] as $year) {
					if (!isset($seen[$year])) {
						$obj = new stdClass();
						$obj->tahun = $year;
						$obj->display = $year;
						$years[] = $obj;
						$seen[$year] = true;
					}
				}
			}
		}
		
		// Sort by year descending
		usort($years, function($a, $b) {
			return $b->tahun - $a->tahun;
		});
		
		return $years;
	}

	public function data_list()
	{
		$this->load->helper('url');

		// Apply filters if provided
		if (isset($_POST['filter_lembaga']) && !empty($_POST['filter_lembaga'])) {
			$this->db->where('kehadiran_lembaga.id_lembaga', $_POST['filter_lembaga']);
		}
		
		if (isset($_POST['filter_bulan']) && !empty($_POST['filter_bulan'])) {
			$this->db->where('kehadiran_lembaga.bulan', $_POST['filter_bulan']);
		}
		
		if (isset($_POST['filter_tahun']) && !empty($_POST['filter_tahun'])) {
			// Use LIKE to handle formats like "2025", "2025/2026", etc.
			$this->db->like('kehadiran_lembaga.tahun', $_POST['filter_tahun']);
		}

		// Fetch real data from model
		$list = $this->Laporan_model->get_datatables();
		$no = 1;
		$data = array();
		
		foreach ($list as $datanya) {
			$encrypted_id = $this->encrypt_url($datanya->id_kehadiran_lembaga);
			$row = array();
			$row[] = $no++;
			$row[] = htmlentities($datanya->nama_lembaga);
			$row[] = htmlentities($datanya->bulan);
			$row[] = htmlentities($datanya->tahun);
			$row[] = rupiah($datanya->jumlah_total); // Remove span wrapper, Ajax will handle formatting
			
			// Send detail_url for Ajax render function to use
			$row['detail_url'] = base_url('laporan_lembaga/rincian/'.$encrypted_id);
			
			$data[] = $row;
		}

		$output = array("data" => $data);
		echo json_encode($output);
	}
	
	public function data_per_bulan()
	{
		$this->load->helper('url');
		
		// Get bulan and tahun from POST
		$bulan = isset($_POST['bulan']) ? $_POST['bulan'] : '';
		$tahun = isset($_POST['tahun']) ? $_POST['tahun'] : '';
		
		// Apply period filter
		if (!empty($bulan)) {
			$this->db->where('kehadiran_lembaga.bulan', $bulan);
		}
		if (!empty($tahun)) {
			$this->db->like('kehadiran_lembaga.tahun', $tahun);
		}
		
		// Fetch data from model
		$list = $this->Laporan_model->get_datatables();
		$no = 1;
		$data = array();
		
		foreach ($list as $datanya) {
			$encrypted_id = $this->encrypt_url($datanya->id_kehadiran_lembaga);
			$row = array();
			$row[] = $no++;
			$row[] = htmlentities($datanya->nama_lembaga);
			$row[] = "<span class='text-success'>".rupiah($datanya->jumlah_total)."</span>";
			// Action button with encrypted ID
			$row[] = '<a type="button" class="btn btn-outline-danger btn-sm" href="laporan_lembaga/rincian/'.$encrypted_id.'"
			title="Detail" ><i class="bx bx-edit mr-1" ></i> Rincian</a>';
			
			$data[] = $row;
		}

		$output = array(
			"data" => $data,
			"bulan" => $bulan,
			"tahun" => $tahun
		);
		echo json_encode($output);
	}

	public function rincian($encrypted_id = null){
		$this->Login_model->getsqurity() ;
		
		// Decrypt URL parameter
		if ($encrypted_id === null) {
			redirect('laporan_lembaga');
			return;
		}
		
		$id = $this->decrypt_url($encrypted_id);
		
		// Fetch data from model
		$isi['data_rincian'] = $this->Laporan_model->get_datatables_rincian($id);
		$isi['encrypted_id'] = $encrypted_id; // Pass encrypted ID to view for print button
		
		$isi['content'] = 'laporan_lembaga/Rincian';
		$isi['css'] 	= 'laporan_lembaga/Css';
		$isi['ajax'] 	= 'laporan_lembaga/Ajax';
		$this->load->view('Template',$isi);
	}
	
	public function cetak($encrypted_id = null){
		$this->Login_model->getsqurity() ;
		
		// Decrypt URL parameter
		if ($encrypted_id === null) {
			redirect('laporan_lembaga');
			return;
		}
		
		$id = $this->decrypt_url($encrypted_id);
		
		// Fetch data from model
		$data['data_rincian'] = $this->Laporan_model->get_datatables_rincian($id);
		
		// Load print view (without template)
		$this->load->view('laporan_lembaga/Cetak', $data);
	}
}
