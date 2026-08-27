<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rekap_kehadiran_pengajar extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Login_model');
		$this->load->model('Laporan_model');
		$this->load->helper('rupiah_helper');
	}

	public function index(){
		$this->Login_model->getsqurity();
		
		$isi['lembaga_list'] = $this->get_lembaga_list();
		
		$isi['css'] 	= 'Rekap_kehadiran_pengajar/Css';
		$isi['content'] = 'Rekap_kehadiran_pengajar/Index';
		$isi['ajax'] 	= 'Rekap_kehadiran_pengajar/Ajax';
		$this->load->view('Template',$isi);
	}
	
	private function get_lembaga_list() {
		$query = $this->db->query("
			SELECT id_lembaga, nama_lembaga 
			FROM lembaga 
            WHERE tenaga_pengajar = 'Ya'
			ORDER BY nama_lembaga ASC
		");
		return $query->result();
	}
	
	// Fungsi helper untuk menghasilkan rentang bulan tahun
	private function generate_month_year_range($start_month, $start_year, $end_month, $end_year) {
		$months = [
			'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,
			'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
			'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12
		];
		
		$months_rev = array_flip($months);
		
		$start_m = $months[$start_month];
		$end_m = $months[$end_month];
		
		$result = [];
		$current_m = $start_m;
		$current_y = $start_year;
		
		while ($current_y < $end_year || ($current_y == $end_year && $current_m <= $end_m)) {
			$result[] = $months_rev[$current_m] . '-' . $current_y;
			$current_m++;
			if ($current_m > 12) {
				$current_m = 1;
				$current_y++;
			}
		}
		
		return $result;
	}

	public function get_data()
	{
		$id_lembaga = $this->input->post('id_lembaga');
		$start_month = $this->input->post('start_month');
		$start_year = $this->input->post('start_year');
		$end_month = $this->input->post('end_month');
		$end_year = $this->input->post('end_year');
		
		// Validasi input
		if(empty($id_lembaga) || empty($start_month) || empty($start_year) || empty($end_month) || empty($end_year)) {
			echo json_encode(['status' => 'error', 'message' => 'Lengkapi semua filter']);
			return;
		}
		
		$months = [
			'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,
			'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
			'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12
		];

		// Cek validitas range
		if($start_year > $end_year || ($start_year == $end_year && $months[$start_month] > $months[$end_month])) {
			echo json_encode(['status' => 'error', 'message' => 'Periode awal tidak boleh lebih besar dari periode akhir']);
			return;
		}

		$month_year_range = $this->generate_month_year_range($start_month, $start_year, $end_month, $end_year);
		
		// Ambil data mentah
		$raw_data = $this->Laporan_model->get_rekap_kehadiran_pivot($id_lembaga, $month_year_range);
		
		// Proses pivot
		$pivot = [];
		foreach ($raw_data as $row) {
			$key = $row->id_pengajar;
			// Gunakan string bulan dan 2 digit tahun agar ringkas. Namun, tahun dari db mungkin berupa format tahun ajaran, jadi kita cek.
            // Sesuai konfirmasi user, mulai 2025 formatnya angka 2025, 2026 dst.
			$tahun = trim($row->tahun);
			// ambil 2 digit terakhir kalau itu angka 4 digit
			$thn_short = strlen($tahun) == 4 ? substr($tahun, 2, 2) : $tahun;
            // antisipasi kalau format masih 2024/2025, kita ambil setelah '/' atau fallback.
            if(strpos($tahun, '/') !== false) {
                $parts = explode('/', $tahun);
                $thn_short = substr($parts[0], 2, 2) . '/' . substr($parts[1], 2, 2);
            }
			$bulan_key = $row->bulan . '-' . $thn_short; // format: "Januari-25"
			
			if (!isset($pivot[$key])) {
				$gelar_depan = $row->gelar_depan ? $row->gelar_depan . ' ' : '';
				$gelar_belakang = $row->gelar_belakang ? ', ' . $row->gelar_belakang : '';
				
				$pivot[$key] = [
					'nama' => trim($gelar_depan . $row->nama_lengkap . $gelar_belakang),
					'lembaga' => $row->nama_lembaga,
					'bulan_data' => []
				];
			}
			
			// Hitung total hadir
			$total_hadir = $row->jumlah_hadir + $row->jumlah_hadir_15 + $row->jumlah_hadir_10 + $row->jumlah_hadir_piket;
			
            if(!isset($pivot[$key]['bulan_data'][$bulan_key])) {
                $pivot[$key]['bulan_data'][$bulan_key] = 0;
            }
			$pivot[$key]['bulan_data'][$bulan_key] += $total_hadir;
		}
		
		// Siapkan data untuk dikirim ke view
		$columns = [];
		foreach ($month_year_range as $bt) {
			$parts = explode('-', $bt);
            $thn_short = strlen($parts[1]) == 4 ? substr($parts[1], 2, 2) : $parts[1];
			$columns[] = $parts[0] . '-' . $thn_short; // "Januari-25"
		}
		
		echo json_encode([
			'status' => 'success',
			'columns' => $columns,
			'data' => array_values($pivot)
		]);
	}
}
