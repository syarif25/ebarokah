<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Cuti_model extends CI_Model {
	var $table = 'cuti_umana';
	var $column_order = array('cuti_umana.id_cuti', 'umana.nama_lengkap', 'cuti_umana.jenis_cuti', 'cuti_umana.tanggal_mulai', 'cuti_umana.tanggal_selesai', 'cuti_umana.status', null);
	var $column_search = array('umana.nama_lengkap', 'umana.nik', 'cuti_umana.jenis_cuti', 'cuti_umana.keterangan');
	var $order = array('cuti_umana.id_cuti' => 'desc');

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	function _get_datatables_query()
	{
		$this->db->select('cuti_umana.*, umana.nama_lengkap, umana.gelar_depan, umana.gelar_belakang, umana.jk, umana.nomor_hp');
		$this->db->from($this->table);
		$this->db->join('umana', 'cuti_umana.nik = umana.nik', 'left');

		$i = 0;
		foreach ($this->column_search as $item) {
			if(isset($_POST['search']['value'])) {
				if($i===0) {
					$this->db->group_start();
					$this->db->like($item, $_POST['search']['value']);
				} else {
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if(count($this->column_search) - 1 == $i)
					$this->db->group_end();
			}
			$i++;
		}
		
		if(isset($_POST['order'])) {
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} else if(isset($this->order)) {
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables()
	{
		$this->_get_datatables_query();
		if(isset($_POST['length']) && $_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered()
	{
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all()
	{
		$this->db->from($this->table);
		return $this->db->count_all_results();
	}

	// Get cuti yang sedang aktif
	public function get_cuti_aktif()
	{
		$this->db->select('cuti_umana.*, umana.nama_lengkap, umana.gelar_depan, umana.gelar_belakang');
		$this->db->from($this->table);
		$this->db->join('umana', 'cuti_umana.nik = umana.nik', 'left');
		$this->db->where('cuti_umana.status', 'Aktif');
		$this->db->order_by('cuti_umana.tanggal_selesai', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	// Get cuti yang akan habis dalam X hari
	public function get_cuti_akan_habis($hari = 14)
	{
		$this->db->select('cuti_umana.*, umana.nama_lengkap, umana.gelar_depan, umana.gelar_belakang, 
			DATEDIFF(cuti_umana.tanggal_selesai, CURDATE()) as sisa_hari');
		$this->db->from($this->table);
		$this->db->join('umana', 'cuti_umana.nik = umana.nik', 'left');
		$this->db->where('cuti_umana.status', 'Aktif');
		$this->db->where('DATEDIFF(cuti_umana.tanggal_selesai, CURDATE()) <=', $hari);
		$this->db->where('DATEDIFF(cuti_umana.tanggal_selesai, CURDATE()) >=', 0);
		$this->db->order_by('sisa_hari', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	// Create new cuti record
	public function create($data)
	{
		$query = $this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	// Update cuti record
	public function update($where, $data)
	{
		$this->db->update($this->table, $data, $where);
		return $this->db->affected_rows();
	}

	// Get cuti by ID
	public function get_by_id($id)
	{
		$this->db->select('cuti_umana.*, umana.nama_lengkap, umana.gelar_depan, umana.gelar_belakang');
		$this->db->from($this->table);
		$this->db->join('umana', 'cuti_umana.nik = umana.nik', 'left');
		$this->db->where('cuti_umana.id_cuti', $id);
		$query = $this->db->get();
		return $query->row();
	}

	// Cek apakah umana sudah punya cuti aktif
	public function count_cuti_aktif_by_nik($nik, $exclude_id = null)
	{
		$this->db->from($this->table);
		$this->db->where('nik', $nik);
		$this->db->where('status', 'Aktif');
		if ($exclude_id) {
			$this->db->where('id_cuti !=', $exclude_id);
		}
		return $this->db->count_all_results();
	}

	// Auto-selesaikan cuti yang sudah lewat tanggal
	public function selesaikan_cuti_otomatis()
	{
		// Update status cuti menjadi Selesai jika tanggal_selesai sudah lewat
		$this->db->where('status', 'Aktif');
		$this->db->where('tanggal_selesai <', date('Y-m-d'));
		$this->db->update($this->table, array('status' => 'Selesai'));
		
		$affected = $this->db->affected_rows();
		
		// Update status_aktif umana kembali ke Aktif untuk cuti yang baru selesai
		if ($affected > 0) {
			$this->db->select('nik');
			$this->db->from($this->table);
			$this->db->where('status', 'Selesai');
			$this->db->where('tanggal_selesai <', date('Y-m-d'));
			$this->db->where('diupdate_pada >=', date('Y-m-d 00:00:00'));
			$query = $this->db->get();
			
			foreach ($query->result() as $row) {
				// Cek apakah masih ada cuti aktif lainnya
				$check = $this->count_cuti_aktif_by_nik($row->nik);
				if ($check == 0) {
					// Tidak ada cuti aktif lagi, set status_aktif = Aktif
					$this->db->where('nik', $row->nik);
					$this->db->update('umana', array('status_aktif' => 'Aktif'));
				}
			}
		}
		
		return $affected;
	}

	// Get list umana yang tidak sedang cuti (untuk dropdown)
	public function get_umana_available()
	{
		$this->db->select('umana.*');
		$this->db->from('umana');
		$this->db->where('umana.nik NOT IN (
			SELECT nik FROM cuti_umana WHERE status = "Aktif"
		)', NULL, FALSE);
		$this->db->order_by('umana.nama_lengkap', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}

	// Count total cuti aktif
	public function count_total_cuti_aktif()
	{
		$this->db->from($this->table);
		$this->db->where('status', 'Aktif');
		return $this->db->count_all_results();
	}
}
