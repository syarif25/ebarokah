<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Kehadiran_satpam_model extends CI_Model {
	var $table = 'lembaga';
	var $column_order = array('id_lembaga','nama_lembaga',null);
	var $column_search = array('id_lembaga','nama_lembaga'); 
	var $order = array('id_lembaga' => 'asc'); // default order 

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}


	function get_datatables()
	{
		$this->db->query = "select lembaga.id_lembaga, lembaga.id_bidang, lembaga.nama_lembaga, count(penempatan.nik) as jml from penempatan, 
        lembaga, umana where penempatan.nik = umana.nik and penempatan.id_lembaga = lembaga.id_lembaga and 
        kehadiran_lembaga.id_lembaga = lembaga.id_lembaga and lembaga.idlembaga = '59' GROUP BY kehadiran_lembaga.id_lembaga order by kehadiran.id_kehadiran asc";

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

}
