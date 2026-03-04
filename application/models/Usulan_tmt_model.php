<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Usulan_tmt_model extends CI_Model {
	var $table = 'usulan_perubahan_tmt';
	var $column_order = array('id_usulan','nama_lengkap','tmt_baru','status','tanggal_usulan',null);
	var $column_search = array('umana.nama_lengkap','id_usulan'); 
	var $order = array('id_usulan' => 'desc');

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	function _get_datatables_query($status = null)
	{
		$this->db->select('usulan_perubahan_tmt.*, umana.nama_lengkap, lembaga.nama_lembaga, umana.tmt_struktural');
		$this->db->from($this->table);
		$this->db->join('penempatan', 'usulan_perubahan_tmt.id_penempatan = penempatan.id_penempatan');
		$this->db->join('umana', 'penempatan.nik = umana.nik');
		$this->db->join('lembaga', 'penempatan.id_lembaga = lembaga.id_lembaga');

        // Filter Lembaga untuk Maker (AdminLembaga)
        if ($this->session->userdata('jabatan') == 'AdminLembaga') {
            $this->db->where('penempatan.id_lembaga', $this->session->userdata('lembaga'));
        }
        
        // Filter Filter Status Khusus untuk Approval
        if ($status !== null) {
            if (is_array($status)) {
                $this->db->where_in('usulan_perubahan_tmt.status', $status);
            } else {
                $this->db->where('usulan_perubahan_tmt.status', $status);
            }
        }

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

	function get_datatables($status = null)
	{
		$this->_get_datatables_query($status);
		$query = $this->db->get();
		return $query->result();
	}

    public function create($data)
	{
	    $this->db->insert($this->table, $data);
	    return $this->db->insert_id();
	}

    public function hapus($id_usulan)
    {
        $this->db->where('id_usulan', $id_usulan);
        return $this->db->delete($this->table);
    }
}
