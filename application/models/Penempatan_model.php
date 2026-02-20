<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Penempatan_model extends CI_Model {
	var $table = 'penempatan';
	var $column_order = array('id_penempatan',null);
	var $column_search = array('id_penempatan'); 
	var $order = array('id_penempatan' => 'desc'); // default order 

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	function _get_datatables_query()
	{
		// $this->db->from($this->table);
        $this->db->select('*');
		$this->db->from('penempatan');
		$this->db->join('ketentuan_barokah', 'penempatan.id_ketentuan = ketentuan_barokah.id_ketentuan');
		$this->db->join('lembaga', 'penempatan.id_lembaga = lembaga.id_lembaga');
        $this->db->join('umana', 'penempatan.nik = umana.nik'); 

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
	
	function _get_datatables_pengajar()
	{
		// $this->db->from($this->table);
		$id_lemb = $this->session->userdata('lembaga');
        $this->db->select('*');
		$this->db->from('pengajar');
		$this->db->join('lembaga', 'pengajar.id_lembaga = lembaga.id_lembaga');
        $this->db->join('umana', 'pengajar.nik = umana.nik'); 
        if ($this->session->userdata('jabatan') == 'AdminLembaga'){
        $this->db->where('pengajar.id_lembaga', $id_lemb);
        } else {
            
        }
		$this->db->order_by('id_pengajar', 'DESC');
		$i = 0;
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order2[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order2))
		{
			$order2 = $this->order2;
			$this->db->order_by(key($order2), $order2[key($order2)]);
		}
	}

	function get_datatables_pengajar()
	{
		$this->_get_datatables_pengajar();
		$query = $this->db->get();
		return $query->result();
	}

    public function create($table,$data)
	{
	    $query = $this->db->insert($table, $data);
	    return $this->db->insert_id();// return last insert id
	}

    public function get_akun($id)
	{
		$this->db->from('umana');
		$this->db->where('nik',$id);
		$query = $this->db->get();

		return $query->row();
	}

	public function update($where, $data)
	{
		$this->db->update('penempatan', $data, $where);
		return $this->db->affected_rows();
	}
	
	public function update_pengajar($where, $data)
	{
		$this->db->update('pengajar', $data, $where);
		return $this->db->affected_rows();
	}


	public function get_by_id($id)
	{
		// $this->db->from($this->table);
		$this->db->select('*');
		$this->db->from('penempatan');
		$this->db->join('ketentuan_barokah', 'penempatan.id_ketentuan = ketentuan_barokah.id_ketentuan');
        $this->db->join('umana', 'penempatan.nik = umana.nik'); 
		$this->db->where('penempatan.id_penempatan',$id);
		$query = $this->db->get();

		return $query->row();
	}
	
	public function get_by_id_pengajar($id)
	{
		$this->db->select('*');
		$this->db->from('pengajar');
		$this->db->join('umana', 'pengajar.nik = umana.nik'); 
		$this->db->where('pengajar.id_pengajar',$id);
		$query = $this->db->get();

		return $query->row();
	}
	public function get_statistik_pengajar()
	{
		$id_lemb = $this->session->userdata('lembaga');
		$isAdminLembaga = $this->session->userdata('jabatan') == 'AdminLembaga';

		// Hitung berdasarkan kategori
		$this->db->select('kategori, COUNT(*) as total');
		$this->db->from('pengajar');
		if ($isAdminLembaga) {
			$this->db->where('id_lembaga', $id_lemb);
		}
        $this->db->where('status', 'Aktif');
		$this->db->group_by('kategori');
		$query = $this->db->get();
		$result = $query->result();

		$stats = [
			'GTY' => 0, 'GTT' => 0, 'DTY' => 0, 'DTT' => 0
		];

		foreach ($result as $row) {
			if (isset($stats[$row->kategori])) {
				$stats[$row->kategori] = $row->total;
			}
		}

		$stats['guru'] = $stats['GTY'] + $stats['GTT'];
		$stats['dosen'] = $stats['DTY'] + $stats['DTT'];

		return $stats;
	}
}
