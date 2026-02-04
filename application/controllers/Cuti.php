<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cuti extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Login_model');
		$this->load->model('Cuti_model');
		$this->load->model('Umana_model');
		$this->load->helper('Rupiah_helper');
	}

	public function index(){
		if ($this->session->userdata('jabatan') == 'AdminLembaga' or $this->session->userdata('jabatan') == 'umana') {
			$this->load->view('Error');
		} else {
			$this->Login_model->getsqurity();
			
			// Auto-selesaikan cuti yang sudah lewat tanggal
			$this->Cuti_model->selesaikan_cuti_otomatis();
			
			$isi['css'] = 'Cuti/Css';
			$isi['content'] = 'Cuti/Cuti';
			$isi['ajax'] = 'Cuti/Ajax';
			$this->load->view('Template', $isi);
		}
	}

	public function data_list()
	{
		$this->load->helper('url');
		$list = $this->Cuti_model->get_datatables();
		$data = array();
		$no = $_POST['start'];
		
		foreach ($list as $datanya) {
			$no++;
			$row = array();
			$row[] = $no;
			
			// Foto berdasarkan jenis kelamin
			if($datanya->jk == "Laki-laki"){
				$row[] = '<div class="image-bx">
					<img style="height:40px;" src="assets/cowok.png" alt="" class="img-fluid rounded">
					<span class="active"></span>
				</div>';
			} else {
				$row[] = '<div class="image-bx">
					<img style="height:40px;" src="assets/putri.jpg" alt="" class="rounded-circle">
					<span class="active"></span>
				</div>';
			}
			
			// Nama lengkap dengan gelar
			$namaLengkap = ucwords(strtolower($datanya->nama_lengkap));
			$row[] = htmlentities($datanya->gelar_depan)." ".htmlentities($namaLengkap)." ".htmlentities($datanya->gelar_belakang);
			
			// Jenis Cuti dengan badge
			if($datanya->jenis_cuti == 'Cuti 100%'){
				$row[] = '<span class="badge badge-danger">'.$datanya->jenis_cuti.'</span>';
			} else {
				$row[] = '<span class="badge badge-warning">'.$datanya->jenis_cuti.'</span>';
			}
			
			// Tanggal mulai dan selesai
			$row[] = date_singkat($datanya->tanggal_mulai).' <br> <small class="text-muted">s/d</small> <br> '.date_singkat($datanya->tanggal_selesai);
			
			// Hitung sisa hari
			$today = new DateTime();
			$end_date = new DateTime($datanya->tanggal_selesai);
			$interval = $today->diff($end_date);
			$sisa_hari = $interval->format('%r%a');
			
			if($datanya->status == 'Aktif'){
				if($sisa_hari < 0){
					$row[] = '<span class="badge badge-danger">Lewat '.(abs($sisa_hari)).' hari</span>';
				} elseif($sisa_hari <= 14){
					$row[] = '<span class="badge badge-warning"><i class="fa fa-clock"></i> '.$sisa_hari.' hari lagi</span>';
				} else {
					$row[] = '<span class="badge badge-info">'.$sisa_hari.' hari lagi</span>';
				}
			} else {
				$row[] = '<span class="badge badge-secondary">-</span>';
			}
			
			// Status dengan badge
			if($datanya->status == 'Aktif'){
				$row[] = '<span class="badge badge-primary">Aktif</span>';
			} elseif($datanya->status == 'Selesai'){
				$row[] = '<span class="badge badge-success">Selesai</span>';
			} else {
				$row[] = '<span class="badge badge-danger">Dibatalkan</span>';
			}
			
			// Action buttons
			$action = '<td class="py-2 text-end">
				<div class="dropdown">
					<button class="btn btn-primary tp-btn-light sharp" type="button" data-bs-toggle="dropdown" aria-expanded="false">
						<span class="fs--1">
							<svg xmlns="http://www.w3.org/2000/svg" width="18px" height="18px" viewBox="0 0 24 24">
								<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
									<rect x="0" y="0" width="24" height="24"></rect>
									<circle fill="#000000" cx="5" cy="12" r="2"></circle>
									<circle fill="#000000" cx="12" cy="12" r="2"></circle>
									<circle fill="#000000" cx="19" cy="12" r="2"></circle>
								</g>
							</svg>
						</span>
					</button>
					<div class="dropdown-menu dropdown-menu-end border py-0">';
			
			if($datanya->status == 'Aktif'){
				$action .= '<a class="dropdown-item text-success" href="#" onclick="edit_cuti(\''.$datanya->id_cuti.'\')">
					<i class="bx bx-edit mr-1"></i> Edit
				</a>';
				$action .= '<a class="dropdown-item text-primary" href="#" onclick="selesaikan_cuti(\''.$datanya->id_cuti.'\')">
					<i class="bx bx-check mr-1"></i> Selesaikan
				</a>';
				$action .= '<a class="dropdown-item text-danger" href="#" onclick="batalkan_cuti(\''.$datanya->id_cuti.'\')">
					<i class="bx bx-x mr-1"></i> Batalkan
				</a>';
			} else {
				$action .= '<a class="dropdown-item text-info" href="#" onclick="view_cuti(\''.$datanya->id_cuti.'\')">
					<i class="bx bx-show mr-1"></i> Lihat Detail
				</a>';
			}
			
			$action .= '</div></div></td>';
			$row[] = $action;
			
			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->Cuti_model->count_all(),
			"recordsFiltered" => $this->Cuti_model->count_filtered(),
			"data" => $data,
		);
		echo json_encode($output);
	}

	public function ajax_add()
	{
		$this->_validate();
		
		$data = array(
			'nik' => $this->input->post('nik'),
			'jenis_cuti' => $this->input->post('jenis_cuti'),
			'tanggal_mulai' => $this->input->post('tanggal_mulai'),
			'tanggal_selesai' => $this->input->post('tanggal_selesai'),
			'keterangan' => $this->input->post('keterangan'),
			'status' => 'Aktif',
			'dibuat_oleh' => $this->session->userdata('nik'),
		);

		$insert_id = $this->Cuti_model->create($data);
		
		// Update status_aktif di tabel umana
		if($insert_id){
			$this->db->where('nik', $this->input->post('nik'));
			$this->db->update('umana', array('status_aktif' => $this->input->post('jenis_cuti')));
		}
		
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_edit($id)
	{
		$data = $this->Cuti_model->get_by_id($id);
		echo json_encode($data);
	}

	public function ajax_update()
	{
		$this->_validate();
		
		$data = array(
			'nik' => $this->input->post('nik'),
			'jenis_cuti' => $this->input->post('jenis_cuti'),
			'tanggal_mulai' => $this->input->post('tanggal_mulai'),
			'tanggal_selesai' => $this->input->post('tanggal_selesai'),
			'keterangan' => $this->input->post('keterangan'),
		);

		$this->Cuti_model->update(array('id_cuti' => $this->input->post('id_cuti')), $data);
		
		// Update status_aktif di tabel umana jika jenis cuti berubah
		$this->db->where('nik', $this->input->post('nik'));
		$this->db->update('umana', array('status_aktif' => $this->input->post('jenis_cuti')));
		
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_selesaikan($id)
	{
		$cuti = $this->Cuti_model->get_by_id($id);
		
		if($cuti){
			// Update status cuti menjadi Selesai
			$this->Cuti_model->update(array('id_cuti' => $id), array('status' => 'Selesai'));
			
			// Cek apakah masih ada cuti aktif lainnya
			$check = $this->Cuti_model->count_cuti_aktif_by_nik($cuti->nik);
			
			if($check == 0){
				// Tidak ada cuti aktif lagi, set status_aktif = Aktif
				$this->db->where('nik', $cuti->nik);
				$this->db->update('umana', array('status_aktif' => 'Aktif'));
			}
			
			echo json_encode(array("status" => TRUE, "message" => "Cuti berhasil diselesaikan"));
		} else {
			echo json_encode(array("status" => FALSE, "message" => "Data cuti tidak ditemukan"));
		}
	}

	public function ajax_batalkan($id)
	{
		$cuti = $this->Cuti_model->get_by_id($id);
		
		if($cuti){
			// Update status cuti menjadi Dibatalkan
			$this->Cuti_model->update(array('id_cuti' => $id), array('status' => 'Dibatalkan'));
			
			// Cek apakah masih ada cuti aktif lainnya
			$check = $this->Cuti_model->count_cuti_aktif_by_nik($cuti->nik);
			
			if($check == 0){
				// Tidak ada cuti aktif lagi, set status_aktif = Aktif
				$this->db->where('nik', $cuti->nik);
				$this->db->update('umana', array('status_aktif' => 'Aktif'));
			}
			
			echo json_encode(array("status" => TRUE, "message" => "Cuti berhasil dibatalkan"));
		} else {
			echo json_encode(array("status" => FALSE, "message" => "Data cuti tidak ditemukan"));
		}
	}

	public function get_umana_available()
	{
		$data = $this->Cuti_model->get_umana_available();
		echo json_encode($data);
	}

	private function _validate()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if ($this->input->post('nik') == '') {
			$data['inputerror'][] = 'nik';
			$data['error_string'][] = 'Umana harus dipilih';
			$data['status'] = FALSE;
		}

		if ($this->input->post('jenis_cuti') == '') {
			$data['inputerror'][] = 'jenis_cuti';
			$data['error_string'][] = 'Jenis cuti harus dipilih';
			$data['status'] = FALSE;
		}

		if ($this->input->post('tanggal_mulai') == '') {
			$data['inputerror'][] = 'tanggal_mulai';
			$data['error_string'][] = 'Tanggal mulai harus diisi';
			$data['status'] = FALSE;
		}

		if ($this->input->post('tanggal_selesai') == '') {
			$data['inputerror'][] = 'tanggal_selesai';
			$data['error_string'][] = 'Tanggal selesai harus diisi';
			$data['status'] = FALSE;
		}

		// Validasi tanggal mulai tidak boleh lebih kecil dari hari ini (kecuali edit)
		if ($this->input->post('tanggal_mulai') != '' && !$this->input->post('id_cuti')) {
			$today = date('Y-m-d');
			if ($this->input->post('tanggal_mulai') < $today) {
				$data['inputerror'][] = 'tanggal_mulai';
				$data['error_string'][] = 'Tanggal mulai tidak boleh di masa lalu';
				$data['status'] = FALSE;
			}
		}

		// Validasi durasi cuti maksimal 3 bulan
		if ($this->input->post('tanggal_mulai') != '' && $this->input->post('tanggal_selesai') != '') {
			$start = new DateTime($this->input->post('tanggal_mulai'));
			$end = new DateTime($this->input->post('tanggal_selesai'));
			$interval = $start->diff($end);
			
			// Hitung total hari
			$total_days = $interval->days;
			
			// 3 bulan = approximately 90 hari
			if ($total_days > 90) {
				$data['inputerror'][] = 'tanggal_selesai';
				$data['error_string'][] = 'Masa cuti tidak boleh lebih dari 3 bulan (90 hari)';
				$data['status'] = FALSE;
			}
			
			// Validasi tanggal selesai harus lebih besar dari tanggal mulai
			if ($end <= $start) {
				$data['inputerror'][] = 'tanggal_selesai';
				$data['error_string'][] = 'Tanggal selesai harus lebih besar dari tanggal mulai';
				$data['status'] = FALSE;
			}
		}

		// Validasi: cek apakah umana sudah punya cuti aktif
		if ($this->input->post('nik') != '') {
			$exclude_id = $this->input->post('id_cuti') ? $this->input->post('id_cuti') : null;
			$check = $this->Cuti_model->count_cuti_aktif_by_nik($this->input->post('nik'), $exclude_id);
			
			if ($check > 0) {
				$data['inputerror'][] = 'nik';
				$data['error_string'][] = 'Umana ini sedang memiliki cuti aktif. Selesaikan cuti yang aktif terlebih dahulu.';
				$data['status'] = FALSE;
			}
		}

		if ($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}
}
