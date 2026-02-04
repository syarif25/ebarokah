<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Log_kehadiran_lemb_model extends CI_Model {
	var $table = 'kehadiran_lembaga';
	var $column_order = array('id_kehadiran_lembaga','id_lembaga',null);
	var $column_search = array('id_kehadiran_lembaga','id_lembaga'); 
	var $order = array('id_kehadiran_lembaga' => 'asc'); // default order 

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	function _get_datatables_query()
	{
		$this->db->select('*');
		$this->db->from('kehadiran_lembaga');
		$this->db->join('lembaga', 'lembaga.id_lembaga = kehadiran_lembaga.id_lembaga');
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
		$this->db->update('kehadiran_lembaga', $data, $where);
		return $this->db->affected_rows();
	}


	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where('id_kehadiran_lembaga',$id);
		$query = $this->db->get();

		return $query->row();
	}
}
