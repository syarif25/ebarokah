<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan_pengajar extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Login_model');
		$this->load->model('Laporan_model');
        $this->load->helper('rupiah_helper');
        $this->load->library('user_agent');
        $this->load->library('Pdf');
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
		
        // Reuse filter logic from Laporan_lembaga if improved, or simple one for now
		$isi['lembaga_list'] = $this->get_lembaga_list();
		$isi['tahun_list'] = $this->get_tahun_list();
		
		$isi['css'] 	= 'Laporan_pengajar/Css';
		$isi['content'] = 'Laporan_pengajar/Index';
		$isi['ajax'] 	= 'Laporan_pengajar/Ajax';
		$this->load->view('Template',$isi);
	}
	
	private function get_lembaga_list() {
		$query = $this->db->query("
			SELECT DISTINCT lembaga.id_lembaga, lembaga.nama_lembaga 
			FROM kehadiran_lembaga 
            JOIN lembaga ON kehadiran_lembaga.id_lembaga = lembaga.id_lembaga 
            WHERE kehadiran_lembaga.kategori = 'Pengajar'
			ORDER BY lembaga.nama_lembaga ASC
		");
		return $query->result();
	}
	
	private function get_tahun_list() {
		$query = $this->db->query("
			SELECT DISTINCT tahun 
			FROM kehadiran_lembaga 
            WHERE kategori = 'Pengajar'
			ORDER BY tahun DESC
		");
		return $query->result();
	}

	public function data_list()
	{
		$this->load->helper('url');

		// Base Query customization for Pengajar category
        // Note: Laporan_model->get_datatables() usually fetches ALL. 
        // We need to verify if we can filter by category 'Pengajar' via POST or modifier.
        // Assuming we need to modify Laporan_model or use a Where clause here.

        $this->db->where('kehadiran_lembaga.kategori', 'Pengajar');
        $this->db->where_in('kehadiran_lembaga.status', ['acc', 'selesai', 'Sudah Transfer']);

		// Apply filters
		if (isset($_POST['filter_lembaga']) && !empty($_POST['filter_lembaga'])) {
			$this->db->where('kehadiran_lembaga.id_lembaga', $_POST['filter_lembaga']);
		}
		
		if (isset($_POST['filter_bulan']) && !empty($_POST['filter_bulan'])) {
			$this->db->where('kehadiran_lembaga.bulan', $_POST['filter_bulan']);
		}
		
		if (isset($_POST['filter_tahun']) && !empty($_POST['filter_tahun'])) {
			$this->db->like('kehadiran_lembaga.tahun', $_POST['filter_tahun']);
		}

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
			// Calculate total for pengajar might be complex if not stored in kehadiran_lembaga.jumlah_total properly?
            // Usually validasi_pengajar updates kehadiran_lembaga.jumlah_total? 
            // If not, we might show 0 or need to sum it. 
            // Let's assume Validasi_pengajar/approve updates it (check later).
			$row[] = rupiah($datanya->jumlah_total); 
            
            $status_badge = '';
            if($datanya->status == 'acc') $status_badge = '<span class="badge badge-success">Disetujui</span>';
            elseif($datanya->status == 'selesai') $status_badge = '<span class="badge badge-primary">Selesai</span>';
            else $status_badge = '<span class="badge badge-info">'.$datanya->status.'</span>';

            $row[] = $status_badge;
			
			// Action
			$aksi = '<a href="'.base_url('Laporan_pengajar/rincian/'.$encrypted_id).'" class="btn btn-sm btn-info"><i class="fa fa-eye"></i> Rincian</a>';
            $aksi .= ' <a href="'.base_url('Laporan_pengajar/cetak/'.$encrypted_id).'" target="_blank" class="btn btn-sm btn-warning"><i class="fa fa-print"></i> Cetak</a>';
			$row[] = $aksi;
			
			$data[] = $row;
		}

		$output = array("data" => $data);
		echo json_encode($output);
	}

	public function rincian($encrypted_id = null){
		$this->Login_model->getsqurity() ;
		
		if ($encrypted_id === null) {
			redirect('Laporan_pengajar');
			return;
		}
		
		$id = $this->decrypt_url($encrypted_id);
		
		// Fetch snapshot data
		$isi['data_rincian'] = $this->Laporan_model->get_datatables_rincian_pengajar($id);
		
		if (empty($isi['data_rincian'])) {
			// Fallback: Use Legacy Data (Live Calculation) via Model Helper
            // This ensures data is displayed even without snapshot
			$isi['data_rincian'] = $this->Laporan_model->get_datatables_legacy_pengajar($id);
		}
        
        // Get header info (periode, lembaga)
        $isi['header_info'] = $this->db->query("SELECT * FROM kehadiran_lembaga JOIN lembaga ON kehadiran_lembaga.id_lembaga = lembaga.id_lembaga WHERE id_kehadiran_lembaga = '$id'")->row();

		$isi['encrypted_id'] = $encrypted_id;
		
		$this->load->view('Laporan_pengajar/Rincian', $isi);
	}
	
	public function cetak($encrypted_id = null){
		$this->Login_model->getsqurity() ;
		
		if ($encrypted_id === null) {
			redirect('Laporan_pengajar');
			return;
		}
		
		$id = $this->decrypt_url($encrypted_id);
		
		// Gunakan raw query agar view PDF mendapatkan semua field mentah dari DB
		$data['isilist'] = $this->Laporan_model->get_raw_for_pdf_pengajar($id);
		
        $data['header_info'] = $this->db->query("SELECT * FROM kehadiran_lembaga JOIN lembaga ON kehadiran_lembaga.id_lembaga = lembaga.id_lembaga WHERE id_kehadiran_lembaga = '$id'")->row();
		
		// Data tunkel & tunj_anak — dibutuhkan oleh view PDF untuk kalkulasi
		$data['isitunkel']    = $this->db->get('tunkel')->result();
		$data['isitunj_anak'] = $this->db->get('tunjanak')->result();

		// Tahun acuan dinamis dari DB
		$cfg_tahun = $this->db->get('pengaturan_tahun_acuan');
		$tahun_acuan_map = [];
		if ($cfg_tahun->num_rows() > 0) {
			foreach ($cfg_tahun->result() as $cfg) {
				$tahun_acuan_map[trim($cfg->id_bidang)] = (int)$cfg->tahun_acuan;
			}
		}
		$data['tahun_acuan_map'] = $tahun_acuan_map;

		// Walkes Config dari DB
		$walkes_rows = $this->db->get('master_tarif_walkes')->result();
		$walkes_config = [];
		foreach ($walkes_rows as $w) {
			$walkes_config[$w->kode_walkes] = (int)$w->nominal;
		}
		if (empty($walkes_config)) {
			$walkes_config = ['Ya' => 75000, 'walkes_sklh' => 50000];
		}
		$data['walkes_config'] = $walkes_config;

		$this->load->view('Kehadiran_pengajar/Cetak', $data);
	}
}
