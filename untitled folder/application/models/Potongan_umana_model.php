<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Potongan_umana_model extends CI_Model {
	var $table = 'potongan_umana';
	var $column_order = array('id_potongan_umana','potongan_umana.id_penempatan','lembaga.id_lembaga','nominal_potongan',null);
	var $column_search = array('id_potongan_umana','potongan_umana.id_penempatan'); 
	var $order = array('id_potongan_umana' => 'desc'); // default order 

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	function _get_datatables_query()
	{
		// $this->db->from($this->table);
		$this->db->select('*');
		$this->db->from('potongan_umana');
		$this->db->join('penempatan', 'penempatan.id_penempatan = potongan_umana.id_penempatan');
		$this->db->join('potongan', 'potongan_umana.jenis_potongan = potongan.id_potongan');
		$this->db->join('umana', 'penempatan.nik = umana.nik'); 
		$this->db->join('lembaga', 'penempatan.id_lembaga = lembaga.id_lembaga'); 
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
		$this->db->update('potongan_umana', $data, $where);
		return $this->db->affected_rows();
	}


	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where('id_tbk',$id);
		$query = $this->db->get();
		return $query->row();
	}

	public function get_perumana()
	{
		if($this->session->userdata('jabatan') == 'AdminLembaga'){
			$today = date('Y-m-d'); // Mendapatkan tanggal hari ini
			$this->db->select('potongan_umana.id_penempatan, potongan_umana.id_potongan_umana, umana.nama_lengkap, umana.gelar_depan, umana.gelar_belakang, lembaga.nama_lembaga, COUNT(potongan_umana.id_penempatan) as jml_ptg, 
			potongan_umana.min_periode_potongan, potongan_umana.max_periode_potongan, SUM(potongan_umana.nominal_potongan) as nominal');
			$this->db->from('umana');
			$this->db->join('penempatan', 'umana.nik = penempatan.nik');
			$this->db->join('potongan_umana', 'potongan_umana.id_penempatan = penempatan.id_penempatan');
			$this->db->join('potongan', 'potongan_umana.jenis_potongan = potongan.id_potongan');
			$this->db->join('lembaga', 'penempatan.id_lembaga = lembaga.id_lembaga');
			$this->db->where('potongan_umana.max_periode_potongan >=', $today);
			$this->db->where('lembaga.id_lembaga', $this->session->userdata('lembaga'));
			$this->db->group_by('potongan_umana.id_penempatan');
// 			$this->db->order_by('potongan_umana.id_potongan_umana', 'asc');
			
		
	
		} else {
			$today = date('Y-m-d'); // Mendapatkan tanggal hari ini
			$this->db->select('potongan_umana.id_penempatan, potongan_umana.id_potongan_umana, umana.nama_lengkap, lembaga.nama_lembaga,  umana.gelar_depan, umana.gelar_belakang, COUNT(potongan_umana.id_penempatan) as jml_ptg, 
			potongan_umana.min_periode_potongan, potongan_umana.max_periode_potongan, SUM(potongan_umana.nominal_potongan) as nominal');
			$this->db->from('umana');
			$this->db->join('penempatan', 'umana.nik = penempatan.nik');
			$this->db->join('potongan_umana', 'potongan_umana.id_penempatan = penempatan.id_penempatan');
			$this->db->join('potongan', 'potongan_umana.jenis_potongan = potongan.id_potongan');
			$this->db->join('lembaga', 'penempatan.id_lembaga = lembaga.id_lembaga');
			$this->db->where('potongan_umana.max_periode_potongan >=', $today);
			$this->db->group_by('potongan_umana.id_penempatan');
			$this->db->order_by('potongan_umana.id_potongan_umana', 'desc');
		}

        $query = $this->db->get();
        return $query->result();
	}
	
	public function get_perumana_by_id($id)
{
    $today = date('Y-m-d'); // Mendapatkan tanggal hari ini

    $this->db->select('potongan_umana.id_potongan_umana, nama_potongan, umana.nama_lengkap, lembaga.nama_lembaga, potongan_umana.id_penempatan as jml_ptg, 
        potongan_umana.min_periode_potongan, potongan_umana.max_periode_potongan, potongan_umana.nominal_potongan as nominal');
    $this->db->from('umana');
    $this->db->join('penempatan', 'umana.nik = penempatan.nik');
    $this->db->join('potongan_umana', 'potongan_umana.id_penempatan = penempatan.id_penempatan ');
    $this->db->join('potongan', 'potongan_umana.jenis_potongan = potongan.id_potongan');
    $this->db->join('lembaga', 'penempatan.id_lembaga = lembaga.id_lembaga');

    $this->db->where('potongan_umana.max_periode_potongan >=', $today);
    $this->db->where('penempatan.id_penempatan', $id);

    $query = $this->db->get();
    return $query->result();
}


	public function total_potongan($id){
		$today = date('Y-m-d'); // Mendapatkan tanggal hari ini
		$this->db->select('SUM(potongan_umana.nominal_potongan) as total_potongan');
		$this->db->from('potongan_umana');
		$this->db->where('max_periode_potongan >=', $today);
		$this->db->where('id_penempatan ='.$id);
		$query = $this->db->get();
    	return $query->row()->total_potongan;
	}
}
