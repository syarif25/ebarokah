<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Kehormatan_pengajar_model extends CI_Model {
	var $table = 'barokah_kehormatan_pengajar';
	var $column_order = array('id_barokah_kehormatan_pengajar','nominal',null);
	var $column_search = array('id_barokah_kehormatan_pengajar','nominal'); 
	var $order = array('id_barokah_kehormatan_pengajar' => 'asc'); // default order 

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
		$this->db->update('barokah_kehormatan_pengajar', $data, $where);
		return $this->db->affected_rows();
	}


	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where('id_barokah_kehormatan_pengajar',$id);
		$query = $this->db->get();

		return $query->row();
	}
}
