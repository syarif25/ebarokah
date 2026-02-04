<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Barokah_tambahan_model extends CI_Model {
	var $table = 'barokah_tambahan';
	var $column_order = array('id_barokah_tambahan','barokah_tambahan.id_pengajar','lembaga.id_lembaga','nominal_tambahan',null);
	var $column_search = array('id_barokah_tambahan','barokah_tambahan.id_pengajar'); 
	var $order = array('id_barokah_tambahan' => 'desc'); // default order 

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
			$this->db->from('barokah_tambahan');
    		$this->db->join('pengajar', 'pengajar.id_pengajar = barokah_tambahan.id_pengajar');
    		$this->db->join('umana', 'pengajar.nik = umana.nik');
    		$this->db->join('lembaga', 'pengajar.id_lembaga = lembaga.id_lembaga'); 
			$this->db->where('lembaga.id_lembaga', $this->session->userdata('lembaga'));
			
		} else {
    		$this->db->select('*');
    		$this->db->from('barokah_tambahan');
    		$this->db->join('pengajar', 'pengajar.id_pengajar = barokah_tambahan.id_pengajar');
    		$this->db->join('umana', 'pengajar.nik = umana.nik'); 
    		$this->db->join('lembaga', 'pengajar.id_lembaga = lembaga.id_lembaga'); 
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
		$this->db->update('barokah_tambahan', $data, $where);
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
			$this->db->select('barokah_tambahan.id_pengajar, nama_barokah, gelar_depan, gelar_belakang, barokah_tambahan.id_barokah_tambahan, umana.nama_lengkap, nominal_tambahan, lembaga.nama_lembaga, COUNT(barokah_tambahan.id_pengajar) as jml_brkh, 
			barokah_tambahan.min_periode_tambahan, barokah_tambahan.max_periode_tambahan, SUM(barokah_tambahan.nominal_tambahan) as nominal');
			$this->db->from('umana');
			$this->db->join('pengajar', 'umana.nik = pengajar.nik');
			$this->db->join('barokah_tambahan', 'barokah_tambahan.id_pengajar = pengajar.id_pengajar');
			$this->db->join('lembaga', 'pengajar.id_lembaga = lembaga.id_lembaga');
			$this->db->where('barokah_tambahan.max_periode_tambahan >=', $today);
			$this->db->group_by('barokah_tambahan.id_pengajar');
			$this->db->order_by('barokah_tambahan.id_barokah_tambahan','desc');
			$this->db->where('lembaga.id_lembaga', $this->session->userdata('lembaga'));
		
		} else {
			$today = date('Y-m-d'); // Mendapatkan tanggal hari ini
			$this->db->select('barokah_tambahan.id_pengajar, nama_barokah, gelar_depan, gelar_belakang, barokah_tambahan.id_barokah_tambahan, umana.nama_lengkap, nominal_tambahan, lembaga.nama_lembaga, COUNT(barokah_tambahan.id_pengajar) as jml_brkh, 
			barokah_tambahan.min_periode_tambahan, barokah_tambahan.max_periode_tambahan, SUM(barokah_tambahan.nominal_tambahan) as nominal');
			$this->db->from('umana');
			$this->db->join('pengajar', 'umana.nik = pengajar.nik');
			$this->db->join('barokah_tambahan', 'barokah_tambahan.id_pengajar = pengajar.id_pengajar');
			$this->db->join('lembaga', 'pengajar.id_lembaga = lembaga.id_lembaga');
			$this->db->where('barokah_tambahan.max_periode_tambahan >=', $today);
			$this->db->group_by('barokah_tambahan.id_pengajar');
			$this->db->order_by('barokah_tambahan.id_barokah_tambahan','desc');
		}

        $query = $this->db->get();
        return $query->result();
	}
	
	public function get_perumana_by_id($id)
{
    $today = date('Y-m-d'); // Mendapatkan tanggal hari ini

    $this->db->select('barokah_tambahan.id_barokah_tambahan, nama_barokah, nominal_tambahan, umana.nama_lengkap, lembaga.nama_lembaga, barokah_tambahan.id_pengajar as jml_ptg, 
        barokah_tambahan.min_periode_tambahan, barokah_tambahan.max_periode_tambahan, barokah_tambahan.nominal_tambahan as nominal');
    $this->db->from('umana');
    $this->db->join('pengajar', 'umana.nik = pengajar.nik');
    $this->db->join('barokah_tambahan', 'barokah_tambahan.id_pengajar = pengajar.id_pengajar ');
    $this->db->join('lembaga', 'pengajar.id_lembaga = lembaga.id_lembaga');
    $this->db->where('barokah_tambahan.max_periode_tambahan >=', $today);
    $this->db->where('pengajar.id_pengajar', $id);
    $query = $this->db->get();
    return $query->result();
}


	public function total_potongan($id){
		$today = date('Y-m-d'); // Mendapatkan tanggal hari ini
		$this->db->select('SUM(barokah_tambahan.nominal_tambahan) as total_potongan');
		$this->db->from('barokah_tambahan');
		$this->db->where('max_periode_tambahan >=', $today);
		$this->db->where('id_pengajar ='.$id);
		$query = $this->db->get();
    	return $query->row()->total_potongan;
	}
}
