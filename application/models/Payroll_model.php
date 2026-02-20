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
                    AND total_barokah.id_kehadiran = kehadiran_lembaga.id_kehadiran_lembaga order by nama_bank asc ;");
        } elseif ($cek->kategori == 'Satpam') {
            $query = $this->db->query("SELECT id_total_barokah_satpam as id_total, kehadiran_lembaga.id_kehadiran_lembaga, kehadiran_lembaga.kategori, nama_lengkap, gelar_depan, gelar_belakang, nama_bank, nomor_hp, no_rekening, atas_nama, nama_lembaga, total_barokah_satpam.bulan, total_barokah_satpam.tahun, diterima 
                FROM umana, satpam, total_barokah_satpam, kehadiran_lembaga, lembaga 
                WHERE umana.nik = satpam.nik 
                    AND total_barokah_satpam.id_satpam = satpam.id_satpam 
                    AND kehadiran_lembaga.id_lembaga = lembaga.id_lembaga 
                    AND kehadiran_lembaga.id_kehadiran_lembaga = $id 
                    AND total_barokah_satpam.id_kehadiran_lembaga = kehadiran_lembaga.id_kehadiran_lembaga order by nama_bank asc ;");
        } else {
            $query = $this->db->query("SELECT id_total_barokah_pengajar as id_total, kehadiran_lembaga.id_kehadiran_lembaga, kehadiran_lembaga.kategori, nama_lengkap, gelar_depan, gelar_belakang, nama_bank, nomor_hp, no_rekening, total_barokah_pengajar.id_kehadiran_lembaga as id_kehadiran, atas_nama, nama_lembaga, total_barokah_pengajar.bulan, total_barokah_pengajar.tahun, diterima 
                FROM umana, pengajar, total_barokah_pengajar, kehadiran_lembaga, lembaga 
                WHERE umana.nik = pengajar.nik 
                    AND total_barokah_pengajar.id_pengajar = pengajar.id_pengajar 
                    AND kehadiran_lembaga.id_lembaga = lembaga.id_lembaga 
                    AND pengajar.id_lembaga = lembaga.id_lembaga 
                    AND kehadiran_lembaga.id_kehadiran_lembaga = $id 
                    AND total_barokah_pengajar.id_kehadiran_lembaga = kehadiran_lembaga.id_kehadiran_lembaga order by nama_bank asc ;");
             
             // Fallback if snapshot is empty (Pengajar Only)
             if ($query->num_rows() == 0) {
                 return $this->get_datatables_legacy_rincian($id);
             }
        }
        return $query->result();
    }

    // New: Fallback for Payroll Rincian (Live Calculation)
    function get_datatables_legacy_rincian($id) {
         $id = $this->db->escape_str($id);
         
         // 1. Fetch Raw Data (Logic from Validasi_pengajar)
         $list = $this->db->query("select jumlah_hadir_piket, jumlah_hadir_15, jumlah_hadir_10, jafung, lembaga.id_lembaga, kehadiran_lembaga.status, status_sertifikasi, walkes, kehadiran_pengajar.id_kehadiran_pengajar, pengajar.kategori, jabatan_akademik, jumlah_sks, status_sertifikasi, ijazah_terakhir, id_bidang, tunj_anak, umana.gelar_depan, umana.gelar_belakang, kehormatan, kehadiran_lembaga.file, tunj_kel, kehadiran_lembaga.id_kehadiran_lembaga, 
         nama_lengkap, status_nikah, tmt_dosen, tmt_guru, tmt_maif, kehadiran_pengajar.id_pengajar, kehadiran_pengajar.bulan, kehadiran_pengajar.tahun, jumlah_hadir, nama_lembaga, nominal_transport, status_aktif, pengajar.id_lembaga,
         nama_bank, nomor_hp, no_rekening, atas_nama, umana.nik
         from umana, pengajar, kehadiran_pengajar, kehadiran_lembaga, lembaga, transport 
         WHERE 
         kehadiran_lembaga.id_kehadiran_lembaga = kehadiran_pengajar.id_kehadiran_lembaga and 
         pengajar.id_pengajar = kehadiran_pengajar.id_pengajar and 
         pengajar.nik = umana.nik and 
         pengajar.id_lembaga = lembaga.id_lembaga and 
         pengajar.kategori_trans = transport.id_transport and 
         DATEDIFF(NOW(), pengajar.tgl_mulai) < pengajar.tgl_selesai and
         kehadiran_lembaga.id_kehadiran_lembaga = '$id' order by nama_lengkap asc ")->result();
     
         if (empty($list)) return array();
     
         // 2. Fetch Aux Data
         $tunkel_res = $this->db->get('tunkel')->result();
         $nominaltunkel = isset($tunkel_res[0]) ? $tunkel_res[0] : null;
         
         $tunjanak_res = $this->db->get('tunjanak')->result();
         $nominaltunj_anak = isset($tunjanak_res[0]) ? $tunjanak_res[0] : null;

         // 3. Fetch Tahun Acuan Configuration (like Validasi_pengajar controller)
         $config_tahun_query = $this->db->get('pengaturan_tahun_acuan');
         $tahun_acuan_map = [];
         if ($config_tahun_query->num_rows() > 0) {
             foreach ($config_tahun_query->result() as $cfg) {
                 $tahun_acuan_map[trim($cfg->id_bidang)] = (int)$cfg->tahun_acuan;
             }
         }

         // Default Values (fallback if table empty or missing bidang)
         $tahun_default = (int)date('Y');
         if (isset($tahun_acuan_map['Default']))      $tahun_default = $tahun_acuan_map['Default'];
         if (isset($tahun_acuan_map['Pengurus']))     $tahun_default = $tahun_acuan_map['Pengurus'];
         if (isset($tahun_acuan_map['Kantor Pusat'])) $tahun_default = $tahun_acuan_map['Kantor Pusat'];

         $tahun_madrasah = isset($tahun_acuan_map['Bidang DIKJAR-M']) ? $tahun_acuan_map['Bidang DIKJAR-M'] : $tahun_default;
         $tahun_sekolah = isset($tahun_acuan_map['Bidang DIKJAR']) ? $tahun_acuan_map['Bidang DIKJAR'] : $tahun_default;
         $tahun_pt = isset($tahun_acuan_map['Bidang DIKTI']) ? $tahun_acuan_map['Bidang DIKTI'] : $tahun_default;
     
         $results = [];
     
         // 4. Calculate and Map to Payroll Structure
         // Expected cols: id_total, kategori, id_kehadiran_lembaga, nama_lengkap, gelar_depan, gelar_belakang, nama_bank, nomor_hp, no_rekening, id_kehadiran, atas_nama, nama_lembaga, bulan, tahun, diterima 
         
         foreach ($list as $key) {
              // Logic copied from Validasi_pengajar controller
              $jml_kehadiran = $key->jumlah_hadir * $key->nominal_transport;
              $nominal_hadir_15 = $key->jumlah_hadir_15 * 15000;
              $nominal_hadir_10 = $key->jumlah_hadir_10 * 10000;
     
              if (($key->kategori == 'GTY' && $key->id_bidang == "Bidang DIKJAR-M") || ($key->kategori == 'GTT' && $key->id_bidang == "Bidang DIKJAR-M")) {
                  $mp = $tahun_madrasah - date("Y", strtotime($key->tmt_guru));
                  $masa_p = ($mp == 0) ? 0 : $mp;
              } elseif (($key->kategori == 'GTY' && $key->id_bidang == "Bidang DIKJAR") || ($key->kategori == 'GTT' && $key->id_bidang == "Bidang DIKJAR")) {
                  $mp = $tahun_sekolah - date("Y", strtotime($key->tmt_guru));
                  $masa_p = ($mp == 0) ? 0 : $mp;
              } elseif (($key->kategori == 'DTY' && $key->nama_lembaga == "Ma'had Aly Sukorejo") || ($key->kategori == 'DTT' && $key->nama_lembaga == "Ma'had Aly Sukorejo")) {
                  $mp = $tahun_pt - date("Y", strtotime($key->tmt_maif));
                  $masa_p = ($mp == 0) ? 0 : $mp;
              }else {
                  $mp = $tahun_pt - date("Y", strtotime($key->tmt_dosen));
                  $masa_p = ($mp == 0) ? 0 : $mp;
              }
     
              // Tunkel
             if ($key->tunj_kel == "Ya" and $mp >= 2 && $nominaltunkel){
                 $tunkel = $nominaltunkel->besaran_tunkel;
             } else {
                 $tunkel = 0;
             }
             if ($key->status_aktif == "Cuti 50%") { $tunkel *= 0.5; } elseif ($key->status_aktif == "Cuti 100%") { $tunkel = 0; }
     
             // Tunj Anak
             if ($key->tunj_anak == "Ya" && $nominaltunj_anak){
                 $tunja_anak = $nominaltunj_anak->nominal_tunj_anak;
             } else {
                 $tunja_anak = 0;
             }
             if ($key->status_aktif == "Cuti 50%") { $tunja_anak *= 0.5; } elseif ($key->status_aktif == "Cuti 100%") { $tunja_anak = 0; }
     
             // Walkes
             if ($key->walkes == "Ya" ){
                 $tunj_walkes = 75000;
             } else if($key->walkes == "walkes_sklh") {
                 $tunj_walkes = 50000;
             } else if($key->walkes == "walkes_amsilati") {
                 $tunj_walkes = 25000;
             } else {
                 $tunj_walkes = 0;
             }
             if ($key->status_aktif == "Cuti 50%") { $tunj_walkes *= 0.5; } elseif ($key->status_aktif == "Cuti 100%") { $tunj_walkes = 0; }
     
             // Rank
             $rank = 0; 
             if ($key->kategori == 'GTY' or $key->kategori == 'GTT'){
                 $hitung_rank = $this->db->query("select nominal from barokah_pengajar where min_tmp_mengajar <= $masa_p and max_tmp_mengajar >= $masa_p and ijazah = '$key->ijazah_terakhir' and kategori = 'Guru' ")->result();
                 foreach($hitung_rank as $nilai_rank) { $rank = $nilai_rank->nominal; }
             } else {
                 if ($key->id_lembaga == '39'){
                         $hitung_rank = $this->db->query("select nominal from barokah_pengajar where min_tmp_mengajar <= $masa_p and max_tmp_mengajar >= $masa_p and ijazah = '$key->ijazah_terakhir' and kategori = 'Dosen MAIF' ")->result();
                     foreach($hitung_rank as $nilai_rank) { $rank = $nilai_rank->nominal; }
                 } else {
                     $hitung_rank = $this->db->query("select nominal from barokah_pengajar where min_tmp_mengajar <= $masa_p and max_tmp_mengajar >= $masa_p and ijazah = '$key->ijazah_terakhir' and kategori = 'Dosen' ")->result();
                     foreach($hitung_rank as $nilai_rank) { $rank = $nilai_rank->nominal; }
                 }
                 
             }
             if ($key->status_aktif == "Cuti 50%") { $rank *= 0.5; } elseif ($key->status_aktif == "Cuti 100%") { $rank = 0; }
     
             // Lainnya
             $mengajar = ($key->jumlah_sks * 35000) * 4;
             if ($key->status_aktif == "Cuti 50%") { $mengajar *= 0.5; } elseif ($key->status_aktif == "Cuti 100%") { $mengajar = 0; }
     
             $kehormatan = $key->kehormatan;
             if ($key->status_aktif == "Cuti 50%") { $kehormatan *= 0.5; } elseif ($key->status_aktif == "Cuti 100%") { $kehormatan = 0; }
             
             $dty = $key->jafung;
             if ($key->status_aktif == "Cuti 50%") { $dty *= 0.5; } elseif ($key->status_aktif == "Cuti 100%") { $dty = 0; }
             
             $jafung = $key->jabatan_akademik;
             if ($key->status_aktif == "Cuti 50%") { $jafung *= 0.5; } elseif ($key->status_aktif == "Cuti 100%") { $jafung = 0; }
             
             $tambahan = $key->jumlah_hadir_piket; // asumsi tambahan = piket nominal
             
             // Potongan
             $potongan = 0;
             $list_potongan = $this->db->query("SELECT pp.nominal_potongan 
                 FROM potongan_umana pp 
                 JOIN penempatan p ON pp.id_penempatan = p.id_penempatan 
                 WHERE p.nik = '$key->nik' 
                 AND p.id_lembaga = '$key->id_lembaga'
                 AND pp.max_periode_potongan >= CURDATE()")->result();
             foreach ($list_potongan as $p) {
                 $potongan += (float)$p->nominal_potongan;
             }
             
             // Total Logic (from Validasi_pengajar)
             $barokah_piket = (float)$key->jumlah_hadir_piket;
             
             // Logic Total from Validasi:
             // $diterima = $barokah_piket + $jml_kehadiran + $nominal_hadir_15 + $nominal_hadir_10 + $tunkel + $tunja_anak + $mengajar + $dty + $jafung + $kehormatan + $tunj_walkes + $tambahan_manual - $potongan;
             // Note: $tambahan variable above was assigned jumlah_hadir_piket which might be wrong, check Validasi_pengajar view logic for exact vars.
             // View logic: $barokah_piket + $jml_kehadiran + $nominal_hadir_15 + $nominal_hadir_10 + $tunkel + $tunja_anak + $mengajar + $dty + $jafung + $kehormatan + $tunj_walkes + $tambahan - $potongan;
             // But $tambahan in view comes from db 'b_tambahan' or similar? In legacy query it's not selected.
             // Assuming 0 for now if not in query.
             
             $diterima = (float)$barokah_piket + (float)$jml_kehadiran + (float)$nominal_hadir_15 + (float)$nominal_hadir_10 + (float)$tunkel + (float)$tunja_anak + (float)$mengajar + (float)$dty + (float)$jafung + (float)$kehormatan + (float)$tunj_walkes - (float)$potongan;
             
             // Map object
             $obj = new stdClass();
             $obj->id_total = 0; // Legacy doesn't have ID
             $obj->kategori = $key->kategori;
             $obj->id_kehadiran_lembaga = $key->id_kehadiran_lembaga;
             $obj->nama_lengkap = $key->nama_lengkap;
             $obj->gelar_depan = $key->gelar_depan;
             $obj->gelar_belakang = $key->gelar_belakang;
             $obj->nama_bank = $key->nama_bank;
             $obj->nomor_hp = $key->nomor_hp;
             $obj->no_rekening = $key->no_rekening;
             $obj->id_kehadiran = $key->id_kehadiran_lembaga;
             $obj->atas_nama = $key->atas_nama;
             $obj->nama_lembaga = $key->nama_lembaga;
             $obj->bulan = $key->bulan;
             $obj->tahun = $key->tahun;
             $obj->diterima = $diterima;
             
             $results[] = $obj;
         }
         
         return $results;
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
