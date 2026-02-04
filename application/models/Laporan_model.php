<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan_model extends CI_Model {
	var $table = 'kehadiran_lembaga';
	var $column_order = array('id_kehadiran_lembaga','bulan','tahun',null);
	var $column_search = array('id_kehadiran_lembaga','bulan','tahun'); 
	var $order = array('id_kehadiran_lembaga' => 'asc'); // default order 

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	function _get_datatables_query()
	{
		// $this->db->from($this->table);

        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->join('lembaga', 'kehadiran_lembaga.id_lembaga = lembaga.id_lembaga', 'left');
        // $this->db->where('kehadiran_lembaga.status', array('acc', 'selesai'));
        // $this->db->where_not_in('kehadiran_lembaga.status', array('Belum', 'selesai'));

        
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

	function _get_datatables_query_rincian($id)
	{
		// $this->db->from($this->table);

        $this->db->select('*');
		$this->db->from('kehadiran_lembaga');
		$this->db->join('kehadiran', 'kehadiran.id_kehadi = kehadiran_lembaga.id_kehadiran_lembaga', 'left');
		$this->db->join('penempatan', 'kehadiran.id_penempatan = penempatan.id_penempatan', 'left');
		$this->db->join('umana', 'penempatan.nik = umana.nik', 'left');
		$this->db->join('ketentuan_barokah', 'penempatan.id_ketentuan = ketentuan_barokah.id_ketentuan', 'left');
		$this->db->join('total_barokah', 'penempatan.id_penempatan = total_barokah.id_penempatan', 'left');
		$this->db->where('kehadiran_lembaga.id_kehadiran_lembaga', $id);
		
        
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

	function get_datatables_rincian($id)
	{
		// $this->_get_datatables_query_rincian($id);
		$query_lama = $this->db->query("select umana.gelar_depan, umana.gelar_belakang, id_kehadiran_lembaga, total_barokah.kehormatan, potongan, jabatan_lembaga, jumlah_total, nama_lembaga, kehadiran.bulan, kehadiran.tahun, nama_lengkap, tmp, diterima, tmt_struktural, nama_jabatan, barokah, jumlah_hadir, nominal_kehadiran, mp, tunkel, total_barokah.tunj_anak, tbk, total_barokah.kehadiran, umana.nik from kehadiran, kehadiran_lembaga, penempatan, umana, total_barokah, ketentuan_barokah, lembaga WHERE lembaga.id_lembaga = penempatan.id_lembaga and kehadiran_lembaga.id_kehadiran_lembaga = kehadiran.id_kehadi AND kehadiran_lembaga.id_kehadiran_lembaga = $id AND penempatan.id_penempatan = kehadiran.id_penempatan AND penempatan.nik = umana.nik and total_barokah.id_penempatan = kehadiran.id_penempatan AND penempatan.id_ketentuan = ketentuan_barokah.id_ketentuan and total_barokah.bulan = kehadiran_lembaga.bulan AND total_barokah.tahun = kehadiran_lembaga.tahun GROUP by umana.nik ORDER by ketentuan_barokah.id_ketentuan ASC");
// 		$query =  $this->db->query("SELECT u.nik, u.nama_lengkap, tb.*, p.id_penempatan FROM total_barokah tb
//             INNER JOIN penempatan p ON tb.id_penempatan = p.id_penempatan
//             INNER JOIN umana u ON p.nik = u.nik
//             INNER JOIN lembaga l ON l.id_lembaga = p.id_lembaga
//             WHERE u.nik = $id
//             ORDER BY id_total_barokah DESC;");
		return $query_lama->result();
	}
	

	function get_datatables_rincian_pengajar($id)
	{
		if (empty($id)) {
			log_message('error', 'Empty ID in get_datatables_rincian_pengajar');
			return array();
		}
		$id = $this->db->escape_str($id); // Escape untuk aman
		$query = $this->db->query("SELECT u.nik, u.nama_lengkap, l.nama_lembaga, tbp.*, p.id_pengajar 
								FROM total_barokah_pengajar tbp
								INNER JOIN pengajar p ON tbp.id_pengajar = p.id_pengajar
								INNER JOIN umana u ON p.nik = u.nik
								INNER JOIN lembaga l ON l.id_lembaga = p.id_lembaga
								WHERE u.nik = '$id'
								ORDER BY id_total_barokah_pengajar DESC;");
		return $query->result();
	}
	
	function get_datatables_perumana()
	{
		// $this->_get_datatables_query_rincian($id);
		$query = $this->db->query("select penempatan.nik, nama_lengkap, alamat_domisili FROM umana, penempatan WHERE umana.nik = penempatan.nik group by umana.nik");
		return $query->result();
	}

	function get_datatables_rincian_perumana($id)
	{
	    $query = $this->db->query("select id_total_barokah, gelar_depan, gelar_belakang, jumlah_total, nama_lembaga, total_barokah.bulan, total_barokah.tahun, nama_lengkap, tmp, diterima, tmt_struktural, nama_jabatan, barokah, jumlah_hadir, nominal_kehadiran, mp, tunkel, total_barokah.kehadiran, umana.nik 
		from kehadiran, kehadiran_lembaga, penempatan, umana, total_barokah, ketentuan_barokah, lembaga 
		WHERE lembaga.id_lembaga = penempatan.id_lembaga and kehadiran_lembaga.id_kehadiran_lembaga = kehadiran.id_kehadi AND penempatan.id_penempatan = kehadiran.id_penempatan AND penempatan.nik = umana.nik and total_barokah.id_penempatan = kehadiran.id_penempatan AND penempatan.id_ketentuan = ketentuan_barokah.id_ketentuan 
		and umana.nik = $id and kehadiran_lembaga.status = 'selesai' and  YEAR(timestamp) = YEAR(CURRENT_DATE) AND MONTH(timestamp) >= MONTH(CURRENT_DATE) - 2 GROUP by total_barokah.id_total_barokah ORDER BY id_total_barokah DESC");
		return $query->result();
	}
	
	public function getJumlahTotalBulanIni()
	{
	    
	    switch ($bln2) {
				case '1':
					$b2 = 'Januari';
					break;
				case '2':
					$b2 = 'Februari';
					break;
			   case '3':
				   $b2 = 'Maret';
				   break;
			   case '4':
				   $b2 = 'April';
				   break;
			   case '5':
				   $b2 = 'Mei';
				   break;
			   case '6':
				   $b2 = 'Juni';
				   break;
			   case '7':
				   $b2 = 'Juli';
				   break;
			   case '8':
				   $b2 = 'Agustus';
				   break;
			   case '9':
				   $b2 = 'September';
				   break;
			   case '10':
				   $b2 = 'Oktober';
				   break;
			   case '11':
				   $b2 = 'Nopember';
				   break;
				default:
					$b2 = 'Desember';
					break;
			}
			
    $bulanIni = $b2;
    $tahunIni = "2022/2023";
    
    $this->db->select_sum('jumlah_total');
    $this->db->where('bulan', $bulanIni);
    $this->db->where('tahun', $tahunIni);
    $query = $this->db->get('kehadiran_lembaga');
    
    return $query->row()->jumlah_total;
	}

	

    public function getData() {
        $query = $this->db->query("
            SELECT bulan, SUM(tbk) AS total_tbk, SUM(tunjab) AS total_tunjab, SUM(kehormatan) AS total_kehormatan, SUM(nominal_kehadiran) AS total_kehadiran, SUM(tunkel) AS total_tunkel
            FROM total_barokah
            GROUP BY bulan ORDER BY id_total_barokah asc
        ");

        return $query->result();
    
	}

    // Get Rincian Data for Satpam (Snapshot)
    function get_datatables_rincian_satpam($id)
    {
        // Query ke total_barokah_satpam (snapshot)
        // Join ke umana untuk nama lengkap
        $this->db->select('tb.*, u.nama_lengkap, u.gelar_depan, u.gelar_belakang');
        $this->db->from('total_barokah_satpam tb');
        $this->db->join('satpam s', 'tb.id_satpam = s.id_satpam');
        $this->db->join('umana u', 's.nik = u.nik');
        $this->db->where('tb.id_kehadiran_lembaga', $id);
        $this->db->order_by('u.nama_lengkap', 'ASC');
        return $this->db->get()->result();
    }
}
