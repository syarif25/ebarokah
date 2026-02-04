<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Kehadiran_struktural_model extends CI_Model {
	var $table = 'lembaga';
	var $column_order = array('id_lembaga','nama_lembaga',null);
	var $column_search = array('id_lembaga','nama_lembaga'); 
	var $order = array('id_lembaga' => 'asc'); // default order 

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	function _get_datatables_query()
	{
		// $this->db->from($this->table);
		$this->db->query = "select lembaga.id_lembaga, lembaga.id_bidang, lembaga.nama_lembaga, count(penempatan.nik) as jml from penempatan, lembaga, umana where penempatan.nik = umana.nik and penempatan.id_lembaga = lembaga.id_lembaga GROUP BY lembaga.id_lembaga";

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
		// $this->_get_datatables_query();
		$this->db->query = "select lembaga.id_lembaga, lembaga.id_bidang, lembaga.nama_lembaga, count(penempatan.nik) as jml from penempatan, lembaga, umana where penempatan.nik = umana.nik and penempatan.id_lembaga = lembaga.id_lembaga and kehadiran_lembaga.id_lembaga = lembaga.id_lembaga GROUP BY kehadiran_lembaga.id_lembaga order by kehadiran.id_kehadiran asc";

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
		$this->db->update('lembaga', $data, $where);
		return $this->db->affected_rows();
	}

	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where('id_lembaga',$id);
		$query = $this->db->get();

		return $query->row();
	}

	public function get_lembaga($id)
	{
		$query = $this->db->query("select id_kehadiran_lembaga, jumlah_total, nama_lembaga, bulan, tahun from kehadiran_lembaga, lembaga WHERE kehadiran_lembaga.id_lembaga = lembaga.id_lembaga and kehadiran_lembaga.id_kehadiran_lembaga =  '$id' ");
		return $query->row();
	}

	function get_datatables_rincian($id)
	{
		// $this->_get_datatables_query_rincian($id);
		$query = $this->db->query("select total_barokah.id_kehadiran, nomor_hp, nama_bank, no_rekening, jumlah_total, nama_lembaga, kehadiran.bulan, kehadiran.tahun, nama_lengkap, tmp, diterima, tmt_struktural, nama_jabatan, barokah, jumlah_hadir, nominal_kehadiran, mp, tunkel, total_barokah.kehadiran, umana.nik from kehadiran, kehadiran_lembaga, penempatan, umana, total_barokah, ketentuan_barokah, lembaga WHERE lembaga.id_lembaga = penempatan.id_lembaga and kehadiran_lembaga.id_kehadiran_lembaga = kehadiran.id_kehadi AND kehadiran_lembaga.id_kehadiran_lembaga = $id AND penempatan.id_penempatan = kehadiran.id_penempatan AND penempatan.nik = umana.nik and total_barokah.id_penempatan = kehadiran.id_penempatan AND penempatan.id_ketentuan = ketentuan_barokah.id_ketentuan GROUP by umana.nik ORDER by ketentuan_barokah.id_ketentuan ASC");
		return $query->result();
	}
	
	function get_jumlah_hadir($id)
	{
		// $this->_get_datatables_query_rincian($id);
		$query = $this->db->query("select nama_lengkap, nama_jabatan, jumlah_hadir from kehadiran, kehadiran_lembaga, penempatan, umana, ketentuan_barokah, lembaga WHERE lembaga.id_lembaga = penempatan.id_lembaga and kehadiran_lembaga.id_kehadiran_lembaga = kehadiran.id_kehadi AND kehadiran_lembaga.id_kehadiran_lembaga = $id AND penempatan.id_penempatan = kehadiran.id_penempatan AND penempatan.nik = umana.nik AND penempatan.id_ketentuan = ketentuan_barokah.id_ketentuan GROUP by umana.nik ORDER by ketentuan_barokah.id_ketentuan ASC");
		return $query->result();
	}
}
