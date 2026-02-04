<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Tbk_model extends CI_Model {
	var $table = 't_beban_kerja';
	var $column_order = array('id_tbk','id_penempatan',null);
	var $column_search = array('id_tbk','id_penempatan'); 
	var $order = array('id_tbk' => 'asc'); // default order 

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	function _get_datatables_query()
	{
		// $this->db->from($this->table);
	    if($this->session->userdata('jabatan') == 'AdminLembaga'){
			$this->db->select('*');
			$this->db->from('t_beban_kerja');
			$this->db->join('penempatan', 'penempatan.id_penempatan = t_beban_kerja.id_penempatan');
			$this->db->join('umana', 'penempatan.nik = umana.nik'); 
			$this->db->join('lembaga', 'penempatan.id_lembaga = lembaga.id_lembaga'); 
			$this->db->where('lembaga.id_lembaga', $this->session->userdata('lembaga'));
			
		} else {
			$this->db->select('*');
			$this->db->from('t_beban_kerja');
			$this->db->join('penempatan', 'penempatan.id_penempatan = t_beban_kerja.id_penempatan');
			$this->db->join('umana', 'penempatan.nik = umana.nik'); 
			$this->db->join('lembaga', 'penempatan.id_lembaga = lembaga.id_lembaga'); 
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
		$this->db->update('t_beban_kerja', $data, $where);
		return $this->db->affected_rows();
	}


	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where('id_tbk',$id);
		$query = $this->db->get();

		return $query->row();
	}
}
