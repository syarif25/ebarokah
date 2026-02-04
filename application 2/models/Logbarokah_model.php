<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Logbarokah_model extends CI_Model {
	var $table = 'total_barokah';
	var $column_order = array('id_total_barokah','id_penempatan',null);
	var $column_search = array('id_total_barokah','id_penempatan'); 
	var $order = array('id_total_barokah' => 'asc'); // default order 

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	function _get_datatables_query()
	{
		$this->db->select('*, total_barokah.tunj_anak as tunanak,total_barokah.kehormatan as tot_kehormatan, total_barokah.diterima as tot_diterima, total_barokah.status as tot_status  ');
		$this->db->from('total_barokah');
		$this->db->join('penempatan', 'penempatan.id_penempatan = total_barokah.id_penempatan');
        $this->db->join('lembaga', 'lembaga.id_lembaga = penempatan.id_lembaga');
        $this->db->join('umana', 'umana.nik = penempatan.nik');
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
	
	
	function _get_datatables_query_pengajar()
	{
		$this->db->select('*, total_barokah_pengajar.walkes as tot_walkes, total_barokah_pengajar.tun_anak as tunanak, total_barokah_pengajar.kehormatan as tot_kehormatan, total_barokah_pengajar.diterima as tot_diterima, total_barokah_pengajar.status as tot_status');
		$this->db->from('total_barokah_pengajar');
		$this->db->join('pengajar', 'pengajar.id_pengajar = total_barokah_pengajar.id_pengajar');
		$this->db->join('lembaga', 'lembaga.id_lembaga = pengajar.id_lembaga');
		$this->db->join('umana', 'umana.nik = pengajar.nik');
		
		$i = 0;
		$column_order_pengajar = array('nama_lengkap', 'nama_lembaga'); // Sesuaikan dengan kolom yang ingin di-order
		$order_pengajar = array('id_total_barokah_pengajar' => 'asc'); // Sesuaikan dengan urutan default
		
		if(isset($_POST['order'])) {
			$this->db->order_by($column_order_pengajar[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} else if(isset($order_pengajar)) {
			$order = $order_pengajar;
			$this->db->order_by(key($order), $order[key($order)]);
		}
		
	}

	function get_datatables_pengajar()
	{
		$this->_get_datatables_query_pengajar();
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
		$this->db->update('total_barokah', $data, $where);
		return $this->db->affected_rows();
	}


	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where('id_total_barokah',$id);
		$query = $this->db->get();

		return $query->row();
	}
}
