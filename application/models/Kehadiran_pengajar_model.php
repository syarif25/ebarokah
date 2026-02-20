<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Kehadiran_pengajar_model extends CI_Model {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

    // Get list of attendance periods for teachers
    // Replaces Kehadiran::data_list_pengajar query
	function get_datatables($lembaga_id, $is_admin_lembaga = false)
	{
        // Query logic extracted from Kehadiran.php
        if($is_admin_lembaga){
			$this->db->select('lembaga.nama_lembaga, kehadiran_lembaga.status, kehadiran_lembaga.kategori, id_kehadiran_lembaga, kehadiran_lembaga.bulan, kehadiran_lembaga.tahun, COUNT(pengajar.id_pengajar) as jml');
            $this->db->from('kehadiran_lembaga');
            $this->db->join('lembaga', 'kehadiran_lembaga.id_lembaga = lembaga.id_lembaga');
            $this->db->join('pengajar', 'pengajar.id_lembaga = kehadiran_lembaga.id_lembaga');
            $this->db->where('kehadiran_lembaga.kategori', 'Pengajar');
            $this->db->where('lembaga.id_lembaga', $lembaga_id);
            $this->db->where('pengajar.status !=', 'Tidak Aktif');
            $this->db->where('pengajar.tgl_selesai >=', 'CURDATE()', FALSE);
            $this->db->group_by('kehadiran_lembaga.id_kehadiran_lembaga');
            $this->db->order_by('kehadiran_lembaga.id_kehadiran_lembaga', 'desc');
		} else {
			// All data (for SuperAdmin etc)
            $this->db->select('lembaga.nama_lembaga, kehadiran_lembaga.status, kehadiran_lembaga.kategori, id_kehadiran_lembaga, kehadiran_lembaga.bulan, kehadiran_lembaga.tahun, COUNT(pengajar.id_pengajar) as jml');
            $this->db->from('kehadiran_lembaga');
            $this->db->join('lembaga', 'kehadiran_lembaga.id_lembaga = lembaga.id_lembaga');
            $this->db->join('pengajar', 'pengajar.id_lembaga = kehadiran_lembaga.id_lembaga');
            $this->db->where('kehadiran_lembaga.kategori', 'Pengajar');
            $this->db->where('pengajar.status !=', 'Tidak Aktif');
            $this->db->where('pengajar.tgl_selesai >=', 'CURDATE()', FALSE);
            $this->db->group_by('kehadiran_lembaga.id_kehadiran_lembaga');
            $this->db->order_by('kehadiran_lembaga.id_kehadiran_lembaga', 'desc');
		}

		$query = $this->db->get();
		return $query->result();
	}

    // Create new attendance period (Blanko)
    public function create_blanko($data)
    {
        // Check duplicate
        $this->db->where('id_lembaga', $data['id_lembaga']);
		$this->db->where('bulan', $data['bulan']);
		$this->db->where('tahun', $data['tahun']);
		$this->db->where('kategori', 'Pengajar');
		$existingData = $this->db->get('kehadiran_lembaga')->row();

        if ($existingData) {
            return false;
        }

        $this->db->insert('kehadiran_lembaga', $data);
        return true;
    }

    // Save attendance data (Batch Insert)
    // Replaces Kehadiran::ajax_add_pengajar logic
    public function save_kehadiran_batch($id_kehadiran_lembaga, $data_batch, $update_lembaga)
    {
        $this->db->trans_begin();

		// Hapus data lama agar tidak duplikat
		$this->db->where('id_kehadiran_lembaga', $id_kehadiran_lembaga)
				->delete('kehadiran_pengajar');

		// Masukkan data baru
		if (!empty($data_batch)) {
			$this->db->insert_batch('kehadiran_pengajar', $data_batch);
		}

		// Update lembaga
		$this->db->where('id_kehadiran_lembaga', $id_kehadiran_lembaga)
				->update('kehadiran_lembaga', $update_lembaga);

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
            return false;
		} else {
			$this->db->trans_commit();
            return true;
		}
    }
}
