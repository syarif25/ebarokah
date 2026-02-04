<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Payroll_model extends CI_Model {
	var $table = 'kehadiran_lembaga';
	var $column_order = array('id_kehadiran_lembaga','nama_lembaga','kategori','bulan','tahun',null);
	var $column_search = array('nama_lembaga','kategori','bulan','tahun'); 
	var $order = array('tgl_input' => 'desc'); // default order 

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	function _get_datatables_query()
	{
		// Optimized query: JOIN dengan subquery untuk count WA status
        $this->db->select('
            kehadiran_lembaga.*,
            lembaga.nama_lembaga,
            COALESCE(wa_count.success_count, 0) as success_count,
            COALESCE(wa_count.pending_count, 0) as pending_count,
            COALESCE(wa_count.failed_count, 0) as failed_count
        ');
        $this->db->from($this->table);
        $this->db->join('lembaga', 'kehadiran_lembaga.id_lembaga = lembaga.id_lembaga', 'left');
        
        // Subquery untuk count WA status - OPTIMIZED: hanya 1 query untuk semua rows
        $this->db->join('(
            SELECT 
                id_kehadiran_lembaga,
                SUM(CASE WHEN status = "success" THEN 1 ELSE 0 END) as success_count,
                SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_count,
                SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed_count
            FROM wa_log
            GROUP BY id_kehadiran_lembaga
        ) wa_count', 'wa_count.id_kehadiran_lembaga = kehadiran_lembaga.id_kehadiran_lembaga', 'left');
        
        $this->db->where('kehadiran_lembaga.status !=', "Belum");
        $this->db->where('kehadiran_lembaga.status !=', "Sudah");
        $this->db->where('kehadiran_lembaga.status !=', "Terkirim");
        
		$i = 0;
		
		// Individual column search
		foreach($this->column_search as $item) {
			if(isset($_POST['columns'][$i]['search']['value']) && $_POST['columns'][$i]['search']['value'] != '') {
				$this->db->like($item, $_POST['columns'][$i]['search']['value']);
			}
			$i++;
		}
		
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
            $query = $this->db->query("SELECT id_total_barokah as id_total, kategori, kehadiran_lembaga.id_kehadiran_lembaga, nama_lengkap, gelar_depan, gelar_belakang, nama_bank, nomor_hp, no_rekening, total_barokah.id_kehadiran, atas_nama, nama_lembaga, total_barokah.bulan, total_barokah.tahun, diterima 
                FROM umana, penempatan, total_barokah, kehadiran_lembaga, lembaga 
                WHERE umana.nik = penempatan.nik 
                    AND total_barokah.id_penempatan = penempatan.id_penempatan 
                    AND kehadiran_lembaga.id_lembaga = lembaga.id_lembaga 
                    AND penempatan.id_lembaga = lembaga.id_lembaga 
                    AND kehadiran_lembaga.id_kehadiran_lembaga = $id 
                    AND total_barokah.bulan = kehadiran_lembaga.bulan 
                    AND total_barokah.tahun = kehadiran_lembaga.tahun order by nama_bank asc ;");
        } elseif ($cek->kategori == 'Satpam') {
            $query = $this->db->query("SELECT id_total_barokah_satpam as id_total, kehadiran_lembaga.id_kehadiran_lembaga, kehadiran_lembaga.kategori, nama_lengkap, gelar_depan, gelar_belakang, nama_bank, nomor_hp, no_rekening, atas_nama, nama_lembaga, total_barokah_satpam.bulan, total_barokah_satpam.tahun, diterima 
                FROM umana, satpam, total_barokah_satpam, kehadiran_lembaga, lembaga 
                WHERE umana.nik = satpam.nik 
                    AND total_barokah_satpam.id_satpam = satpam.id_satpam 
                    AND kehadiran_lembaga.id_lembaga = lembaga.id_lembaga 
                    AND kehadiran_lembaga.id_kehadiran_lembaga = $id 
                    AND total_barokah_satpam.bulan = kehadiran_lembaga.bulan 
                    AND total_barokah_satpam.tahun = kehadiran_lembaga.tahun order by nama_bank asc ;");
        } else {
            $query = $this->db->query("SELECT id_total_barokah_pengajar as id_total, kehadiran_lembaga.id_kehadiran_lembaga, kehadiran_lembaga.kategori, nama_lengkap, gelar_depan, gelar_belakang, nama_bank, nomor_hp, no_rekening, total_barokah_pengajar.id_kehadiran, atas_nama, nama_lembaga, total_barokah_pengajar.bulan, total_barokah_pengajar.tahun, diterima 
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

	public function get_wa_status_count($id_kehadiran_lembaga)
	{
		// Count WA status dari wa_log berdasarkan id_kehadiran_lembaga
		$this->db->select("
			SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as success_count,
			SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
			SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_count
		");
		$this->db->from('wa_log');
		$this->db->where('id_kehadiran_lembaga', $id_kehadiran_lembaga);
		$result = $this->db->get()->row();
		
		// Return default jika tidak ada data
		if (!$result) {
			return (object) ['success_count' => 0, 'pending_count' => 0, 'failed_count' => 0];
		}
		
		return $result;
	}

	public function kirimwa($isipesan)
{
    $pesan = '*'.$isipesan['title'].'*

Periode : '.$isipesan['periode'].' 
Nama Lengkap : '.$isipesan['nama_lengkap'].' 
Lembaga : *'.$isipesan['lembaga'].'*
Jumlah  : '.$isipesan['jumlah'].'
Bank  : '.$isipesan['nama_bank'].'
No Rek : '.$isipesan['norek'].'
Tanggal Kirim : *'.$isipesan['waktu'].'*

Untuk detail rincian barokah silakan kunjungi ebarokah.p2s3.com

Jazakumullah Khairan';

    $testingMode = false; // ← PRODUCTION MODE: kirim ke nomor asli
    $nomor_hp = $testingMode 
        ? "081249057246" 
        : preg_replace('/^0/', '62', $isipesan['nomor_hp']); // ubah 0 ke 62


    $logData = [
        'id_total_barokah'    => $isipesan['id_total_barokah'] ?? null,
        'id_kehadiran_lembaga'=> $isipesan['id_kehadiran_lembaga'] ?? null,
        'bulan'               => $isipesan['bulan'] ?? null,
        'tahun'               => $isipesan['tahun'] ?? null,
        'nama_umana'          => $isipesan['nama_umana'] ?? null,
        'kategori'            => $isipesan['kategori'] ?? null,
        'nama_lembaga'        => $isipesan['lembaga'],
        'nomor_hp'            => $nomor_hp,
        'nama_bank'           => $isipesan['nama_bank'],
        'nomor_rekening'      => $isipesan['norek'],
        'nama_penerima'       => $isipesan['nama_lengkap'],
        'jumlah'              => (int) str_replace('.', '', $isipesan['jumlah']),
        'status'              => 'pending',
        'created_at'          => date('Y-m-d H:i:s')
    ];

    $this->db->insert('wa_log', $logData);
    $log_id = $this->db->insert_id();

    $link = "https://solo.wablas.com/api/send-message";
    $token = "fpi23keYVt27yc4FpVj6crgc199h72PrPARBY0ZZ0NLuqOeROfGPxYdzENOvXQRI.WEKyuXvr";

    $data = [
        'phone'   => $nomor_hp,
        'message' => $pesan
    ];

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_HTTPHEADER, ["Authorization: $token"]);
    curl_setopt($curl, CURLOPT_URL, $link);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

    $start = microtime(true);
    $result = curl_exec($curl);
    $end = microtime(true);

    $curl_info = curl_getinfo($curl);
    $curl_error = curl_error($curl);

    // Jika curl gagal total
    if ($result === false) {
        $result = json_encode(['status' => false, 'message' => $curl_error]);
    }

    curl_close($curl);

    $status = 'failed';
    $response_json = json_decode($result, true);
    if (isset($response_json['status']) && $response_json['status'] == true) {
        $status = 'success';
    }

    $this->db->where('id_wa_log', $log_id);
    $this->db->update('wa_log', [
        'status'     => $status,
        'response'   => $result,
        'http_code'  => $curl_info['http_code'] ?? null,
        'duration'   => round($end - $start, 5),
        'curl_error' => $curl_error,
        'updated_at' => date('Y-m-d H:i:s')
    ]);

    return $result;
}

public function kirimWaUlang($isipesan)
{
    $pesan = '*'.$isipesan['title'].'*

Periode : '.$isipesan['periode'].' 
Nama Lengkap : '.$isipesan['nama_lengkap'].' 
Lembaga : *'.$isipesan['lembaga'].'*
Jumlah  : '.$isipesan['jumlah'].'
Bank  : '.$isipesan['nama_bank'].'
No Rek : '.$isipesan['norek'].'
Tanggal Kirim : *'.$isipesan['waktu'].'*

Untuk detail rincian barokah silakan kunjungi ebarokah.p2s3.com

Jazakumullah Khairan';

    $testingMode = false; // ← PRODUCTION MODE: kirim ke nomor asli
    $nomor_hp = $testingMode 
        ? "081249057246" 
        : preg_replace('/^0/', '62', $isipesan['nomor_hp']); // ubah 0 ke 62


    $link = "https://solo.wablas.com/api/send-message";
    $token = "fpi23keYVt27yc4FpVj6crgc199h72PrPARBY0ZZ0NLuqOeROfGPxYdzENOvXQRI.WEKyuXvr";

    $data = [
        'phone'   => $nomor_hp,
        'message' => $pesan
    ];

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_HTTPHEADER, ["Authorization: $token"]);
    curl_setopt($curl, CURLOPT_URL, $link);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

    $start = microtime(true);
    $result = curl_exec($curl);
    $end = microtime(true);

    $curl_info = curl_getinfo($curl);
    $curl_error = curl_error($curl);

    // Jika curl gagal total
    if ($result === false) {
        $result = json_encode(['status' => false, 'message' => $curl_error]);
    }

    curl_close($curl);

    $status = 'failed';
    $response_json = json_decode($result, true);
    if (isset($response_json['status']) && $response_json['status'] == true) {
        $status = 'success';
    }

	$id_log = $isipesan['id_wa_log'];
    $this->db->where('id_wa_log', $id_log);
    $this->db->update('wa_log', [
        'status'     => $status,
        'response'   => $result,
        'http_code'  => $curl_info['http_code'] ?? null,
        'duration'   => round($end - $start, 5),
        'curl_error' => $curl_error,
        'updated_at' => date('Y-m-d H:i:s')
    ]);

    return $result;
}

	
}
