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
        // Explicitly select columns to ensure Snapshot Data takes precedence
        // Explicitly select columns using Structural Join Pattern (Bulan/Tahun/Id_Pengajar)
		$query = $this->db->query("SELECT 
            u.nik, u.nama_lengkap, u.gelar_depan, u.gelar_belakang,
            l.nama_lembaga,
            p.kategori, u.status_sertifikasi, u.jabatan_akademik,
            tbp.id_pengajar, tbp.id_kehadiran_lembaga, tbp.bulan, tbp.tahun,
            tbp.jumlah_sks, tbp.rank, tbp.mengajar, tbp.mp, tbp.dty, tbp.jafung,
            tbp.jumlah_hadir, tbp.nominal_kehadiran,
            tbp.jumlah_hadir_15, tbp.nominal_hadir_15,
            tbp.jumlah_hadir_10, tbp.nominal_hadir_10,
            tbp.jumlah_hadir_piket, tbp.rank_piket, tbp.barokah_piket,
            tbp.tunkel, tbp.tun_anak, tbp.kehormatan, tbp.walkes, tbp.khusus, tbp.potongan,
            tbp.diterima, tbp.status, u.status_aktif, p.ijazah_terakhir
            FROM kehadiran_lembaga kl
            JOIN kehadiran_pengajar kp ON kl.id_kehadiran_lembaga = kp.id_kehadiran_lembaga
            JOIN pengajar p ON kp.id_pengajar = p.id_pengajar
            JOIN umana u ON p.nik = u.nik
            JOIN lembaga l ON p.id_lembaga = l.id_lembaga
            JOIN total_barokah_pengajar tbp ON (
                tbp.id_pengajar = kp.id_pengajar 
                AND tbp.bulan = kl.bulan 
                AND tbp.tahun = kl.tahun
            )
            WHERE kl.id_kehadiran_lembaga = '$id'
            ORDER BY u.nama_lengkap ASC;");
		$results = $query->result();
        foreach($results as $row) {
            $row->is_snapshot = true; // Mark as snapshot
        }
        return $results;
	}

    // New: Fetch Legacy key for Live Calculation on Report View
    function get_datatables_legacy_pengajar($id)
    {
        $id = $this->db->escape_str($id);
        
        // 1. Fetch Raw Data (Legacy Query from Validasi_pengajar)
        $list = $this->db->query("select jumlah_hadir_piket, jumlah_hadir_15, jumlah_hadir_10, jafung, lembaga.id_lembaga, kehadiran_lembaga.status, status_sertifikasi, walkes, kehadiran_pengajar.id_kehadiran_pengajar, pengajar.kategori, jabatan_akademik, jumlah_sks, status_sertifikasi, ijazah_terakhir, id_bidang, tunj_anak, umana.gelar_depan, umana.gelar_belakang, kehormatan, kehadiran_lembaga.file, tunj_kel, kehadiran_lembaga.id_kehadiran_lembaga, 
        nama_lengkap, status_nikah, tmt_dosen, tmt_guru, tmt_maif, kehadiran_pengajar.id_pengajar, kehadiran_pengajar.bulan, kehadiran_pengajar.tahun, jumlah_hadir, nama_lembaga, nominal_transport, status_aktif, pengajar.id_lembaga 
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

        // 4. Calculate and Map to Snapshot Structure
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
                } elseif ($key->id_lembaga == '37') {
                    $hitung_rank = $this->db->query("select nominal from barokah_pengajar where min_tmp_mengajar <= $masa_p and max_tmp_mengajar >= $masa_p and ijazah = '$key->ijazah_terakhir' and kategori = 'Dosen FIK' ")->result();
                    foreach($hitung_rank as $nilai_rank) { $rank = $nilai_rank->nominal; }
                } else {
                    $hitung_rank = $this->db->query("select nominal from barokah_pengajar where min_tmp_mengajar <= $masa_p and max_tmp_mengajar >= $masa_p and ijazah = '$key->ijazah_terakhir' and kategori = 'Dosen UNIB' ")->result();
                    foreach($hitung_rank as $nilai_rank) { $rank = $nilai_rank->nominal; }
                }
            }
            $mengajar = $rank * $key->jumlah_sks;
            if ($key->status_aktif == "Cuti 50%") { $mengajar *= 0.5; } elseif ($key->status_aktif == "Cuti 100%") { $mengajar = 0; }

            $rank_piket = $rank / 4;
            $barokah_piket = $rank_piket * $key->jumlah_hadir_piket;

            // Kehormatan
            $kehormatan = 0;
            if ($key->kategori == 'GTY' || $key->kategori == 'GTT'){
                $hitung_kehormatan = $this->db->query("select nominal from barokah_kehormatan_pengajar where min_masa_pengabdian <= $mp and max_masa_pengabdian >= $mp and kategori = 'Guru' ")->result();
                if(!empty($hitung_kehormatan) and $key->kehormatan == 'Ya') {
                    foreach($hitung_kehormatan as $nilai_kehormatan) { $kehormatan = $nilai_kehormatan->nominal; }
                }
            } else {
                $hitung_kehormatan = $this->db->query("select nominal from barokah_kehormatan_pengajar where min_masa_pengabdian <= $mp and max_masa_pengabdian >= $mp and kategori = 'Dosen' ")->result();
                if(!empty($hitung_kehormatan) and $key->kehormatan == 'Ya') {
                    foreach($hitung_kehormatan as $nilai_kehormatan) { $kehormatan = $nilai_kehormatan->nominal; }
                }
            }
            if ($key->status_aktif == "Cuti 50%") { $kehormatan *= 0.5; } elseif ($key->status_aktif == "Cuti 100%") { $kehormatan = 0; }

            // DTY
            $dty = 0;
            if ($key->kategori == 'GTY' and $key->status_sertifikasi == 'Belum' and $masa_p > 2 ){
                $hitung_dty = $this->db->query("SELECT nominal from barokah_pengajar_tetap where kategori = 'Guru' ")->result();
                if(!empty($hitung_dty)) { foreach($hitung_dty as $nilai_dty) { $dty = $nilai_dty->nominal; } }
            } else if ($key->kategori == 'DTY' and $key->status_sertifikasi == 'Belum' and $masa_p > 2) {
                $hitung_dty = $this->db->query("SELECT nominal from barokah_pengajar_tetap where kategori = 'Dosen' ")->result();
                if(!empty($hitung_dty)) { foreach($hitung_dty as $nilai_dty) { $dty = $nilai_dty->nominal; } }
            }
            if ($key->status_aktif == "Cuti 50%") { $dty *= 0.5; } elseif ($key->status_aktif == "Cuti 100%") { $dty = 0; }

            // Jafung
            $jafung = 0;
            if ($key->jabatan_akademik != '' and $key->jafung == 'Ya' ) {
                $hitung_jafung = $this->db->query("SELECT nominal from barokah_jafung, umana where umana.jabatan_akademik = barokah_jafung.id_barokah_jafung and umana.jabatan_akademik = $key->jabatan_akademik  ")->result();
                if(!empty($hitung_jafung)) { foreach($hitung_jafung as $nilai_jafung) { $jafung = $nilai_jafung->nominal; } }
            }
            if ($key->status_aktif == "Cuti 50%") { $jafung *= 0.5; } elseif ($key->status_aktif == "Cuti 100%") { $jafung = 0; }

            // Potongan
            $potongan = 0;
            // Assuming we use this->db->query from controller logic, or simplified model query
            // Controller used: SELECT SUM(nominal_potongan) ...
             $hitung_potongan = $this->db->query("SELECT SUM(nominal_potongan) as jumlah from potongan_pengajar, pengajar where potongan_pengajar.id_pengajar = pengajar.id_pengajar and potongan_pengajar.id_pengajar = $key->id_pengajar and potongan_pengajar.max_periode_potongan >= CURDATE() ")->result();
            if(!empty($hitung_potongan)) { foreach($hitung_potongan as $jumlah_potongan) { $potongan = $jumlah_potongan->jumlah; } }

             // Tambahan
            $tambahan = 0;
            $hitung_tambahan = $this->db->query("SELECT SUM(nominal_tambahan) as jumlah from barokah_tambahan, pengajar where barokah_tambahan.id_pengajar = pengajar.id_pengajar and barokah_tambahan.id_pengajar = $key->id_pengajar and barokah_tambahan.max_periode_tambahan >= CURDATE() ")->result();
            if(!empty($hitung_tambahan)) { foreach($hitung_tambahan as $jumlah_tambahan) { $tambahan = $jumlah_tambahan->jumlah; } }

            // Map to Object mimicking 'total_barokah_pengajar' struct
            $obj = new stdClass();
            
            // Identity (from umana/pengajar) mixed with Calculations
            $obj->nik = isset($key->nik) ? $key->nik : ''; // Raw might not have nik in select? Checked select: umana.* not selected, but nik joined.
            // Wait, select clause: ..., nama_lengkap, ...
            // Let's ensure we have everything Rincian needed
            $obj->nama_lengkap = $key->nama_lengkap;
            $obj->gelar_depan = $key->gelar_depan;
            $obj->gelar_belakang = $key->gelar_belakang;
            $obj->nama_lembaga = $key->nama_lembaga;
            $obj->kategori = $key->kategori;
            $obj->ijazah_terakhir = $key->ijazah_terakhir;
            $obj->status_aktif = $key->status_aktif;
            
            // TMT fallback
            $obj->tmt_guru = $key->tmt_guru;
            $obj->tmt_dosen = $key->tmt_dosen;
            $obj->tmt_maif = $key->tmt_maif;
            
            // Calculated fields
            $obj->mp = $masa_p;
            $obj->jumlah_sks = $key->jumlah_sks;
            $obj->rank = $rank;
            $obj->mengajar = $mengajar;
            $obj->dty = $dty;
            $obj->jafung = $jafung;
            
            $obj->jumlah_hadir = $key->jumlah_hadir;
            $obj->nominal_kehadiran = $jml_kehadiran;
            
            $obj->jumlah_hadir_15 = $key->jumlah_hadir_15;
            $obj->nominal_hadir_15 = $nominal_hadir_15;
            
            $obj->jumlah_hadir_10 = $key->jumlah_hadir_10;
            $obj->nominal_hadir_10 = $nominal_hadir_10;
            
            $obj->jumlah_hadir_piket = $key->jumlah_hadir_piket;
            $obj->rank_piket = $rank_piket;
            $obj->barokah_piket = $barokah_piket;
            
            $obj->tunkel = $tunkel;
            $obj->tun_anak = $tunja_anak;
            $obj->kehormatan = $kehormatan;
            $obj->walkes = $tunj_walkes;
            $obj->khusus = $tambahan;
            $obj->potongan = $potongan;
            
            // Total not strictly needed if view calculates it, but Rincian view might rely on it if I didn't verify logic.
            // Rincian view recalculates total sum from components (diterima = ...). 
            // BUT it uses these fields.
            
            $results[] = $obj;
        }

        return $results;
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
