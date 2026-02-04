<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Lembaga_model extends CI_Model {
	var $table = 'lembaga';
	var $column_order = array('id_lembaga','nama_lembaga',null);
	var $column_search = array('id_lembaga','nama_lembaga'); 
	var $order = array('id_lembaga' => 'desc'); // default order 

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	function _get_datatables_query()
	{
		$this->db->from($this->table);
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
	
	function get_laporan_perbulan()
	{
		$this->db->select('jumlah_total, bulan, tahun, count(lembaga.id_lembaga) as jml_lembaga');
		$this->db->from('lembaga');
		$this->db->join('kehadiran_lembaga', 'lembaga.id_lembaga = kehadiran_lembaga.id_lembaga');
		$this->db->group_by('bulan, tahun');
		$query = $this->db->get();

		return $query->result();
	}

	function get_laporan_perbulan_perlembaga()
	{
		$this->db->select('jumlah_total, kategori, jumlah_total');
		$this->db->from('lembaga');
		$this->db->join('kehadiran_lembaga', 'lembaga.id_lembaga = kehadiran_lembaga.id_lembaga');
		$this->db->where('kehadiran_lembaga.bulan, "Agustus"');
		$query = $this->db->get();

		return $query->result();
	}
}
