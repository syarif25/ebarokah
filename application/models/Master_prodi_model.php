<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Master_prodi_model extends CI_Model {
	var $table = 'master_prodi';
	var $column_order = array('master_prodi.id_prodi','lembaga.nama_lembaga','master_prodi.nama_prodi',null);
	var $column_search = array('lembaga.nama_lembaga','master_prodi.nama_prodi'); 
	var $order = array('master_prodi.id_prodi' => 'desc'); 

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	function _get_datatables_query()
	{
		$this->db->select('master_prodi.*, lembaga.nama_lembaga');
		$this->db->from($this->table);
		$this->db->join('lembaga', 'lembaga.id_lembaga = master_prodi.id_lembaga');

		$i = 0;
		if(isset($_POST['order'])) 
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
	    return $this->db->insert_id();
	}

	public function update($where, $data)
	{
		$this->db->update($this->table, $data, $where);
		return $this->db->affected_rows();
	}

	public function delete_by_id($id)
	{
		$this->db->where('id_prodi', $id);
		$this->db->delete($this->table);
	}

	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where('id_prodi',$id);
		$query = $this->db->get();
		return $query->row();
	}

	public function get_all_lembaga_fakultas()
	{
		$this->db->from('lembaga');
		$this->db->where('id_bidang', 'Bidang DIKTI');
		$this->db->order_by('nama_lembaga', 'asc');
		return $this->db->get()->result();
	}
}
