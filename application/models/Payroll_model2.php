<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Payroll_model extends CI_Model {
	var $table = 'kehadiran_lembaga';
	var $column_order = array('id_kehadiran_lembaga','bulan','tahun',null);
	var $column_search = array('id_kehadiran_lembaga','bulan','tahun','tgl_input'); 
	var $order = array('tgl_input' => 'desc'); // default order 

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
        $this->db->where('kehadiran_lembaga.status !=', "Belum");
        $this->db->where('kehadiran_lembaga.status !=', "Sudah");
        $this->db->where('kehadiran_lembaga.status !=', "Terkirim");
        
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
// 		$this->db->order_by('nama_bank','asc');
        
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
        $cek = $this->db->query("SELECT kategori FROM kehadiran_lembaga WHERE id_kehadiran_lembaga = '$id' ")->row();
    
        if ($cek->kategori == 'Struktural') {
            $query = $this->db->query("SELECT id_total_barokah as id_total, kategori, nama_lengkap, gelar_depan, gelar_belakang, nama_bank, nomor_hp, no_rekening, total_barokah.id_kehadiran, atas_nama, nama_lembaga, total_barokah.bulan, total_barokah.tahun, diterima 
                FROM umana, penempatan, total_barokah, kehadiran_lembaga, lembaga 
                WHERE umana.nik = penempatan.nik 
                    AND total_barokah.id_penempatan = penempatan.id_penempatan 
                    AND kehadiran_lembaga.id_lembaga = lembaga.id_lembaga 
                    AND penempatan.id_lembaga = lembaga.id_lembaga 
                    AND kehadiran_lembaga.id_kehadiran_lembaga = $id 
                    AND total_barokah.bulan = kehadiran_lembaga.bulan 
                    AND total_barokah.tahun = kehadiran_lembaga.tahun order by nama_bank asc ;");
        } else {
            $query = $this->db->query("SELECT id_total_barokah_pengajar as id_total, kehadiran_lembaga.kategori, nama_lengkap, gelar_depan, gelar_belakang, nama_bank, nomor_hp, no_rekening, total_barokah_pengajar.id_kehadiran, atas_nama, nama_lembaga, total_barokah_pengajar.bulan, total_barokah_pengajar.tahun, diterima 
                FROM umana, pengajar, total_barokah_pengajar, kehadiran_lembaga, lembaga 
                WHERE umana.nik = pengajar.nik 
                    AND total_barokah_pengajar.id_pengajar = pengajar.id_pengajar 
                    AND kehadiran_lembaga.id_lembaga = lembaga.id_lembaga 
                    AND pengajar.id_lembaga = lembaga.id_lembaga 
                    AND kehadiran_lembaga.id_kehadiran_lembaga = $id 
                    AND total_barokah_pengajar.bulan = kehadiran_lembaga.bulan 
                    AND total_barokah_pengajar.tahun = kehadiran_lembaga.tahun order by nama_bank asc ;");
        }
        return $query->result();
    }

	public function get_lembaga($id)
	{
		$query = $this->db->query("select id_kehadiran_lembaga, jumlah_total, nama_lembaga, bulan, tahun from kehadiran_lembaga, lembaga WHERE kehadiran_lembaga.id_lembaga = lembaga.id_lembaga and kehadiran_lembaga.id_kehadiran_lembaga =  '$id' ");
		return $query->row();
	}

	function kirimwa($isipesan)
	{
		$pesan = '*'.$isipesan['title'].'*

Periode : '.$isipesan['periode'].' 
Nama Lengkap : '.$isipesan['nama_lengkap'].' 
Lembaga : *'.$isipesan['lembaga'].'*
Jumlah  : '.$isipesan['jumlah'].'
Bank  : '.$isipesan['nama_bank'].'
No Rek : '.$isipesan['norek'].'
Tanggal Kirim : *'.$isipesan['waktu'].'*

untuk detail rician barokah silahkan kunjungi alamat ebarokah.p2s3.com .

Jazakumullah Khairan';

	$link  =  "https://solo.wablas.com/api/send-message";
        $data = [
        'phone' => $isipesan['no_hp'],
        'message' => $pesan,
        ];
         
         
        $curl = curl_init();
        $token =  "fpi23keYVt27yc4FpVj6crgc199h72PrPARBY0ZZ0NLuqOeROfGPxYdzENOvXQRI.WEKyuXvr";
 
        curl_setopt($curl, CURLOPT_HTTPHEADER,
            array(
                "Authorization: $token",
            )
        );
        curl_setopt($curl, CURLOPT_URL, $link);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data)); 
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($curl);
        curl_close($curl); 
        return $result;
	

	}

	
	
}
