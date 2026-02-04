<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Potongan_pengajar_model extends CI_Model {
	var $table = 'potongan_pengajar';
	var $column_order = array('id_potongan_pengajar','potongan_pengajar.id_pengajar','lembaga.id_lembaga','nominal_potongan',null);
	var $column_search = array('id_potongan_pengajar','potongan_pengajar.id_pengajar'); 
	var $order = array('id_potongan_pengajar' => 'desc'); // default order 

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	function _get_datatables_query()
	{
		// $this->db->from($this->table);
		$this->db->select('*');
		$this->db->from('potongan_pengajar');
		$this->db->join('pengajar', 'pengajar.id_pengajar = potongan_pengajar.id_pengajar');
		$this->db->join('potongan', 'potongan_pengajar.jenis_potongan = potongan.id_potongan');
		$this->db->join('umana', 'pengajar.nik = umana.nik'); 
		$this->db->join('lembaga', 'pengajar.id_lembaga = lembaga.id_lembaga'); 
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
		$this->db->update('potongan_pengajar', $data, $where);
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
    			$this->db->select('potongan_pengajar.id_pengajar, gelar_depan, gelar_belakang, potongan_pengajar.id_potongan_pengajar, umana.nama_lengkap, lembaga.nama_lembaga, COUNT(potongan_pengajar.id_pengajar) as jml_ptg, 
    			potongan_pengajar.min_periode_potongan, potongan_pengajar.max_periode_potongan, SUM(potongan_pengajar.nominal_potongan) as nominal');
    			$this->db->from('umana');
    			$this->db->join('pengajar', 'umana.nik = pengajar.nik');
    			$this->db->join('potongan_pengajar', 'potongan_pengajar.id_pengajar = pengajar.id_pengajar');
    			$this->db->join('potongan', 'potongan_pengajar.jenis_potongan = potongan.id_potongan');
    			$this->db->join('lembaga', 'pengajar.id_lembaga = lembaga.id_lembaga');
    			$this->db->where('potongan_pengajar.max_periode_potongan >=', $today);
    			$this->db->group_by('potongan_pengajar.id_pengajar');
    			$this->db->order_by('potongan_pengajar.id_potongan_pengajar','desc');
    			$this->db->where('lembaga.id_lembaga', $this->session->userdata('lembaga'));
    		
    		} else {
    			$today = date('Y-m-d'); // Mendapatkan tanggal hari ini
    			$this->db->select('potongan_pengajar.id_pengajar, gelar_depan, gelar_belakang, potongan_pengajar.id_potongan_pengajar, umana.nama_lengkap, lembaga.nama_lembaga, COUNT(potongan_pengajar.id_pengajar) as jml_ptg, 
    			potongan_pengajar.min_periode_potongan, potongan_pengajar.max_periode_potongan, SUM(potongan_pengajar.nominal_potongan) as nominal');
    			$this->db->from('umana');
    			$this->db->join('pengajar', 'umana.nik = pengajar.nik');
    			$this->db->join('potongan_pengajar', 'potongan_pengajar.id_pengajar = pengajar.id_pengajar');
    			$this->db->join('potongan', 'potongan_pengajar.jenis_potongan = potongan.id_potongan');
    			$this->db->join('lembaga', 'pengajar.id_lembaga = lembaga.id_lembaga');
    			$this->db->where('potongan_pengajar.max_periode_potongan >=', $today);
    			$this->db->group_by('potongan_pengajar.id_pengajar');
    			$this->db->order_by('potongan_pengajar.id_potongan_pengajar','desc');
    		}

        $query = $this->db->get();
        return $query->result();

        $query = $this->db->get();
        return $query->result();
	}
	
	public function get_perumana_by_id($id)
{
    $today = date('Y-m-d'); // Mendapatkan tanggal hari ini

    $this->db->select('potongan_pengajar.id_potongan_pengajar, nama_potongan, umana.nama_lengkap, lembaga.nama_lembaga, potongan_pengajar.id_pengajar as jml_ptg, 
        potongan_pengajar.min_periode_potongan, potongan_pengajar.max_periode_potongan, potongan_pengajar.nominal_potongan as nominal');
    $this->db->from('umana');
    $this->db->join('pengajar', 'umana.nik = pengajar.nik');
    $this->db->join('potongan_pengajar', 'potongan_pengajar.id_pengajar = pengajar.id_pengajar ');
    $this->db->join('potongan', 'potongan_pengajar.jenis_potongan = potongan.id_potongan');
    $this->db->join('lembaga', 'pengajar.id_lembaga = lembaga.id_lembaga');
    $this->db->where('potongan_pengajar.max_periode_potongan >=', $today);
    $this->db->where('pengajar.id_pengajar', $id);
    $query = $this->db->get();
    return $query->result();
}


	public function total_potongan($id){
		$today = date('Y-m-d'); // Mendapatkan tanggal hari ini
		$this->db->select('SUM(potongan_pengajar.nominal_potongan) as total_potongan');
		$this->db->from('potongan_pengajar');
		$this->db->where('max_periode_potongan >=', $today);
		$this->db->where('id_pengajar ='.$id);
		$query = $this->db->get();
    	return $query->row()->total_potongan;
	}
}
