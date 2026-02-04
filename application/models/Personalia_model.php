<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Personalia_model extends CI_Model {
	var $table = 'pengguna';
	var $column_order = array('nik','nama_lengkap',null);
	var $column_search = array('nik','nama_lengkap'); 
	var $order = array('nik' => 'desc'); // default order 

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	function _get_datatables_query()
	{
		if ($this->session->userdata('jabatan') == 'AdminLembaga') { 
		$id_lembaga = $this->session->userdata('lembaga');
			$this->db->select('id_penempatan, ketentuan_barokah.id_ketentuan, umana.nik, nama_lembaga, nama_lengkap,gelar_depan,gelar_belakang, tunj_kel, tunj_anak, kehormatan, nama_jabatan, YEAR(tmt_struktural) as tmt_struktural, status_sertifikasi, jumlah_anak, file_ktp, file_kk, file_sk');
			$this->db->from('umana');
			$this->db->join('penempatan', 'umana.nik = penempatan.nik');
			$this->db->join('lembaga', 'penempatan.id_lembaga = lembaga.id_lembaga');
			$this->db->join('ketentuan_barokah', 'penempatan.id_ketentuan = ketentuan_barokah.id_ketentuan');
			$this->db->where('lembaga.id_lembaga', $id_lembaga);
			$this->db->order_by('id_ketentuan', 'ASC'); // Mengurutkan berdasarkan nama_lembaga secara ascending (abjad)
// 			$this->db->order_by('nama_lengkap', 'ASC'); // Mengurutkan berdasarkan nama_lengkap secara ascending (abjad)
		} else {
			$this->db->select('id_penempatan, umana.nik,nama_lembaga, nama_lengkap,gelar_depan,gelar_belakang, tunj_kel, tunj_anak, kehormatan, nama_jabatan, YEAR(tmt_struktural) as tmt_struktural, status_sertifikasi, jumlah_anak, file_ktp, file_kk, file_sk');
			$this->db->from('umana');
			$this->db->join('penempatan', 'umana.nik = penempatan.nik');
			$this->db->join('lembaga', 'penempatan.id_lembaga = lembaga.id_lembaga');
			$this->db->join('ketentuan_barokah', 'penempatan.id_ketentuan = ketentuan_barokah.id_ketentuan');
			$this->db->order_by('nama_lembaga', 'ASC'); // Mengurutkan berdasarkan nama_lembaga secara ascending (abjad)
			$this->db->order_by('nama_lengkap', 'ASC'); // Mengurutkan berdasarkan nama_lengkap secara ascending (abjad)
		}
		$i = 0;
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order))
		{
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables()
	{
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->result();
	}

    public function create($table,$data)
	{
	    $query = $this->db->insert($table, $data);
	    return $this->db->insert_id();// return last insert id
	}

	public function update($where, $data)
	{
		$this->db->update('pengguna', $data, $where);
		return $this->db->affected_rows();
	}


	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where('id_pengguna',$id);
		$query = $this->db->get();

		return $query->row();
	}
}
