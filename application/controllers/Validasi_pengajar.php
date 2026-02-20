<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Validasi_pengajar extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Login_model');
        // Load models/helpers as needed later
    }

    public function index()
    {
        // Default redirect or error
        redirect('Kehadiran/pengajar');
    }



    // Helper from Kehadiran.php (Custom implementation)
    function decrypt_url($string) {
        $key = '874jzceroier38!@#%*bjkdwdw)'; // Ganti dengan kunci enkripsi yang diinginkan
        $string = str_replace(array('-', '_'), array('+', '/'), $string);
        $string = base64_decode($string);
        $string = str_replace($key, '', $string);
        return $string;
    }

    public function update_row()
    {
        $this->Login_model->getsqurity();

        // 0. Ambil Input
        $id_kehadiran_pengajar = $this->input->post('id_kehadiran_pengajar');
        $jumlah_hadir          = (int)$this->input->post('jumlah_hadir');
        $jumlah_hadir_15       = (int)$this->input->post('jumlah_hadir_15');
        $jumlah_hadir_10       = (int)$this->input->post('jumlah_hadir_10');
        $jumlah_hadir_piket    = (int)$this->input->post('jumlah_hadir_piket');

        if (!$id_kehadiran_pengajar) {
            echo json_encode(['status' => false, 'message' => 'ID tidak ditemukan']);
            return;
        }

        // 1. Update Database
        $update_data = [
            'jumlah_hadir'       => $jumlah_hadir,
            'jumlah_hadir_15'    => $jumlah_hadir_15,
            'jumlah_hadir_10'    => $jumlah_hadir_10,
            'jumlah_hadir_piket' => $jumlah_hadir_piket
        ];
        $this->db->where('id_kehadiran_pengajar', $id_kehadiran_pengajar);
        $this->db->update('kehadiran_pengajar', $update_data);

        // 2. Fetch Single Row Data (Query copied from index/cetak/view)
        // Note: Using the same query structure but filtering by id_kehadiran_pengajar
        $key = $this->db->query("select tmt_maif, jumlah_hadir_piket, jumlah_hadir_15, jumlah_hadir_10, jafung, lembaga.id_lembaga, kehadiran_lembaga.status, status_sertifikasi, walkes, kehadiran_pengajar.id_kehadiran_pengajar, pengajar.kategori, jabatan_akademik, jumlah_sks, status_sertifikasi, ijazah_terakhir, lembaga.id_bidang, tunj_anak, umana.gelar_depan, umana.gelar_belakang, kehormatan, kehadiran_lembaga.file, tunj_kel, kehadiran_lembaga.id_kehadiran_lembaga, 
		nama_lengkap, status_nikah, tmt_dosen, tmt_guru, kehadiran_pengajar.id_pengajar, kehadiran_pengajar.bulan, kehadiran_pengajar.tahun, jumlah_hadir, nama_lembaga, nominal_transport, status_aktif, pengajar.id_lembaga 
        from umana, pengajar, kehadiran_pengajar, kehadiran_lembaga, lembaga, transport 
        WHERE 
		kehadiran_lembaga.id_kehadiran_lembaga = kehadiran_pengajar.id_kehadiran_lembaga and 
		pengajar.id_pengajar = kehadiran_pengajar.id_pengajar and 
		pengajar.nik = umana.nik and 
		pengajar.id_lembaga = lembaga.id_lembaga and 
		pengajar.kategori_trans = transport.id_transport and 
		DATEDIFF(NOW(), pengajar.tgl_mulai) < pengajar.tgl_selesai and
		kehadiran_pengajar.id_kehadiran_pengajar = '$id_kehadiran_pengajar' ")->row();

        if(!$key){
             echo json_encode(['status' => false, 'message' => 'Data tidak ditemukan setelah update']);
             return;
        }

        // 3. Fetch Master Data
        $tunkel_get = $this->db->get('tunkel')->result();
        if(isset($tunkel_get)) foreach($tunkel_get as $nominaltunkel);

        $tunj_anak_get = $this->db->get('tunjanak')->result();
        if(isset($tunj_anak_get)) foreach($tunj_anak_get as $nominaltunj_anak);

        // 4. Recalculate Logic using Shared Helper
        $config_tahun_query = $this->db->get('pengaturan_tahun_acuan');
        $tahun_acuan_map = [];
        if ($config_tahun_query->num_rows() > 0) {
            foreach ($config_tahun_query->result() as $cfg) {
                $tahun_acuan_map[trim($cfg->id_bidang)] = (int)$cfg->tahun_acuan;
            }
        }

        $meta_data = [
            'nominaltunkel' => isset($nominaltunkel) ? $nominaltunkel : null,
            'nominaltunj_anak' => isset($nominaltunj_anak) ? $nominaltunj_anak : null,
            'tahun_acuan_map' => $tahun_acuan_map
        ];

        $calc = $this->_calculate_row($key, $meta_data);

        // Return Data
        echo json_encode([
            'status' => true,
            'data' => [
                'jml_kehadiran'    => $this->rupiah($calc['jml_kehadiran']),
                'nominal_hadir_15' => $this->rupiah($calc['nominal_hadir_15']),
                'nominal_hadir_10' => $this->rupiah($calc['nominal_hadir_10']),
                'rank_piket'       => $this->rupiah($calc['rank_piket']),
                'barokah_piket'    => $this->rupiah($calc['barokah_piket']),
                'jumlah'           => $this->rupiah($calc['jumlah']),
                'diterima'         => $this->rupiah($calc['diterima']),
            ]
        ]);

    }

    public function kirim_validasi()
    {
        $this->Login_model->getsqurity();
        $id_kehadiran_lembaga = $this->input->post('id_kehadiran_lembaga');

        if (!$id_kehadiran_lembaga) {
            echo json_encode(['status' => false, 'message' => 'ID tidak ditemukan']);
            return;
        }

        // Update Status
        $this->db->where('id_kehadiran_lembaga', $id_kehadiran_lembaga);
        $this->db->update('kehadiran_lembaga', ['status' => 'Terkirim']);

        if ($this->db->affected_rows() > 0) {
            echo json_encode(['status' => true, 'message' => 'Validasi berhasil dikirim!']);
        } else {
            // Check if already updated or error
            $cek = $this->db->get_where('kehadiran_lembaga', ['id_kehadiran_lembaga' => $id_kehadiran_lembaga])->row();
            if ($cek && $cek->status == 'Terkirim') {
                 echo json_encode(['status' => true, 'message' => 'Data sudah terkirim sebelumnya.']);
            } else {
                 echo json_encode(['status' => false, 'message' => 'Gagal mengupdate status.']);
            }
        }
    }

    private function rupiah($angka){
        $hasil_rupiah = "Rp " . number_format($angka,0,',','.');
        return $hasil_rupiah;
    }

    // --- SHARED CALCULATION LOGIC ---
    private function _calculate_row($key, $meta_data) {
        // Extract Meta Data
        $nominaltunkel = $meta_data['nominaltunkel'];
        $nominaltunj_anak = $meta_data['nominaltunj_anak'];
        $tahun_acuan_map = $meta_data['tahun_acuan_map'];

        // Basic Calcs
        $jml_kehadiran = $key->jumlah_hadir * $key->nominal_transport;
        $nominal_hadir_15 = $key->jumlah_hadir_15 * 15000;
        $nominal_hadir_10 = $key->jumlah_hadir_10 * 10000;

        // Tahun Acuan Fallback
        $tahun_default = (int)date('Y');
        if (isset($tahun_acuan_map['Default']))      $tahun_default = $tahun_acuan_map['Default'];
        if (isset($tahun_acuan_map['Pengurus']))     $tahun_default = $tahun_acuan_map['Pengurus'];
        if (isset($tahun_acuan_map['Kantor Pusat'])) $tahun_default = $tahun_acuan_map['Kantor Pusat'];

        $tahun_madrasah = isset($tahun_acuan_map['Bidang DIKJAR-M']) ? $tahun_acuan_map['Bidang DIKJAR-M'] : $tahun_default;
        $tahun_sekolah = isset($tahun_acuan_map['Bidang DIKJAR']) ? $tahun_acuan_map['Bidang DIKJAR'] : $tahun_default;
        $tahun_pt = isset($tahun_acuan_map['Bidang DIKTI']) ? $tahun_acuan_map['Bidang DIKTI'] : $tahun_default;

        // Masa Pengabdian (MP)
        // Check ID Bidang: if implicit in query, ensure it is available in $key object
        // If query select `lembaga.id_bidang`, it is $key->id_bidang
        $id_bidang = (isset($key->id_bidang)) ? $key->id_bidang : ''; 

        if (($key->kategori == 'GTY' && $id_bidang == "Bidang DIKJAR-M") || ($key->kategori == 'GTT' && $id_bidang == "Bidang DIKJAR-M")) {
            $mp = $tahun_madrasah - date("Y", strtotime($key->tmt_guru));
            $masa_p = ($mp == 0) ? 0 : $mp;
        } elseif (($key->kategori == 'GTY' && $id_bidang == "Bidang DIKJAR") || ($key->kategori == 'GTT' && $id_bidang == "Bidang DIKJAR")) {
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
        if ($key->tunj_kel == "Ya" and $mp >= 2){
            $tunkel = $nominaltunkel->besaran_tunkel;
        } else {
            $tunkel = 0;
        }
        if ($key->status_aktif == "Cuti 50%") { $tunkel *= 0.5; } elseif ($key->status_aktif == "Cuti 100%") { $tunkel = 0; }

        // Tunj Anak
        if ($key->tunj_anak == "Ya" ){
            $tunja_anak = $nominaltunj_anak->nominal_tunj_anak;
        } else {
            $tunja_anak = 0;
        }
        if ($key->status_aktif == "Cuti 50%") { $tunja_anak *= 0.5; } elseif ($key->status_aktif == "Cuti 100%") { $tunja_anak = 0; }

        // Walkes
        if ($key->walkes == "Ya" ){
            $tunj_walkes = 100000;
        } else if($key->walkes == "walkes_sklh") {
            $tunj_walkes = 75000;
        } else if($key->walkes == "walkes_amsilati") {
            $tunj_walkes = 25000;
        } else {
            $tunj_walkes = 0;
        }
        if ($key->status_aktif == "Cuti 50%") { $tunj_walkes *= 0.5; } elseif ($key->status_aktif == "Cuti 100%") { $tunj_walkes = 0; }
        
        // Rank / Honor Mengajar
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
            $hitung_potongan = $this->db->query("SELECT SUM(nominal_potongan) as jumlah from potongan_pengajar, pengajar where potongan_pengajar.id_pengajar = pengajar.id_pengajar and potongan_pengajar.id_pengajar = $key->id_pengajar and potongan_pengajar.max_periode_potongan >= CURDATE() ")->result();
        if(!empty($hitung_potongan)) { foreach($hitung_potongan as $jumlah_potongan) { $potongan = $jumlah_potongan->jumlah; } }
        
        // Tambahan
            $tambahan = 0;
        $hitung_tambahan = $this->db->query("SELECT SUM(nominal_tambahan) as jumlah from barokah_tambahan, pengajar where barokah_tambahan.id_pengajar = pengajar.id_pengajar and barokah_tambahan.id_pengajar = $key->id_pengajar and barokah_tambahan.max_periode_tambahan >= CURDATE() ")->result();
        if(!empty($hitung_tambahan)) { foreach($hitung_tambahan as $jumlah_tambahan) { $tambahan = $jumlah_tambahan->jumlah; } }

        $diterima = $barokah_piket + $jml_kehadiran + $nominal_hadir_15 + $nominal_hadir_10 + $tunkel + $tunja_anak + $mengajar + $dty + $jafung + $kehormatan + $tunj_walkes + $tambahan - $potongan;
        $jumlah = $barokah_piket + $jml_kehadiran + $nominal_hadir_15 + $nominal_hadir_10 + $tunkel + $tunja_anak + $mengajar + $dty + $jafung + $kehormatan + $tunj_walkes + $tambahan;

        return [
            'jml_kehadiran'    => $jml_kehadiran,
            'nominal_hadir_15' => $nominal_hadir_15,
            'nominal_hadir_10' => $nominal_hadir_10,
            'rank_piket'       => $rank_piket,
            'barokah_piket'    => $barokah_piket,
            'tunkel'           => $tunkel,
            'tunja_anak'       => $tunja_anak,
            'kehormatan'       => $kehormatan,
            'mengajar'         => $mengajar,
            'rank'             => $rank,
            'dty'              => $dty,
            'jafung'           => $jafung,
            'tunj_walkes'      => $tunj_walkes,
            'tambahan'         => $tambahan,
            'potongan'         => $potongan,
            'diterima'         => $diterima,
            'jumlah'           => $jumlah,
            'mp'               => $masa_p,
            'masa_p'           => $masa_p, // Consistency for View
            'tmt_display'      => (isset($key->tmt_guru) ? $key->tmt_guru : '-') // Simplified display
        ];
    }
    public function reset_json()
    {
        $this->Login_model->getsqurity();

        // --- Guard role
        $jabatan = $this->session->userdata('jabatan');
        if (!in_array($jabatan, ['SuperAdmin', 'Evaluasi'], true)) {
            echo json_encode(['status' => false, 'message' => 'Anda tidak memiliki izin untuk melakukan reset data.']);
            return;
        }

        $idKL = $this->input->post('id_kehadiran_lembaga', true);
        $mode = $this->input->post('mode', true); // 'kehadiran' atau 'status'

        if (!$idKL) {
            echo json_encode(['status' => false, 'message' => 'id_kehadiran_lembaga tidak dikirim.']);
            return;
        }

        // Ambil periode utk validasi
        $periode = $this->db->get_where('kehadiran_lembaga', ['id_kehadiran_lembaga' => $idKL])->row();
        if (!$periode) {
            echo json_encode(['status' => false, 'message' => 'Periode tidak ditemukan.']);
            return;
        }

        $this->db->trans_begin();

        try {
            if ($mode === 'kehadiran') {
                // 1) Hapus data input kehadiran periode ini
                $this->db->where('id_kehadiran_lembaga', $idKL)->delete('kehadiran_pengajar');
                
                // Hapus Snapshot jika ada
                $this->db->where('id_kehadiran_lembaga', $idKL)->delete('total_barokah_pengajar');

                // 2) Status periode -> Belum, nolkan total
                $this->db->where('id_kehadiran_lembaga', $idKL)
                        ->update('kehadiran_lembaga', [
                            'status'       => 'Belum',
                            'jumlah_total' => 0, 
                        ]);

                $msg = 'Input Kehadiran berhasil dihapus. Status periode kembali ke Belum.';

            } else { // mode === 'status' (Reset Validasi)
                // 1) Hanya kembalikan status ke 'Sudah' (bisa diedit/dikirim lagi)
                // Hapus Snapshot karena status mundur
                $this->db->where('id_kehadiran_lembaga', $idKL)->delete('total_barokah_pengajar');
                
                $this->db->where('id_kehadiran_lembaga', $idKL)
                        ->update('kehadiran_lembaga', [
                            'status'       => 'Sudah'
                        ]);

                $msg = 'Status berhasil di-reset ke "Sudah" (Belum Terkirim). Data input aman.';
            }

            if ($this->db->trans_status() === false) {
                throw new Exception('Transaksi gagal.');
            }
            $this->db->trans_commit();

            echo json_encode(['status' => true, 'message' => $msg]);

        } catch (Exception $e) {
            $this->db->trans_rollback();
            echo json_encode(['status' => false, 'message' => 'Reset gagal: ' . $e->getMessage()]);
        }
    }

    public function approve()
    {
        $this->Login_model->getsqurity();
        $id_kehadiran_lembaga = $this->input->post('id_kehadiran_lembaga');

        if (!$id_kehadiran_lembaga) {
             echo json_encode(['status' => false, 'message' => 'ID tidak ditemukan']);
             return;
        }

        // 1. Get List Data (Sama seperti Koreksi)
        // 1. Get List Data (Sama seperti Koreksi)
        $list = $this->db->query("select kehadiran_pengajar.id_kehadiran_pengajar, kehadiran_pengajar.jumlah_hadir, kehadiran_pengajar.jumlah_hadir_15, kehadiran_pengajar.jumlah_hadir_10, kehadiran_pengajar.jumlah_hadir_piket, kehadiran_pengajar.bulan, kehadiran_pengajar.tahun,
        nominal_transport, tmt_guru, tmt_dosen, tmt_maif, umana.ijazah_terakhir, umana.status_aktif,
        pengajar.kategori, lembaga.id_bidang, lembaga.nama_lembaga, pengajar.id_lembaga, pengajar.id_pengajar,
        tunj_kel, tunj_anak, walkes, status_sertifikasi, jabatan_akademik, jafung, kehormatan, jumlah_sks
        from kehadiran_pengajar
        join pengajar on pengajar.id_pengajar = kehadiran_pengajar.id_pengajar
        join umana on umana.nik = pengajar.nik
        join transport on transport.id_transport = pengajar.kategori_trans
        join lembaga on lembaga.id_lembaga = pengajar.id_lembaga
        where kehadiran_pengajar.id_kehadiran_lembaga = '$id_kehadiran_lembaga'
        AND DATEDIFF(NOW(), pengajar.tgl_mulai) < pengajar.tgl_selesai")->result();

        if (empty($list)) {
            echo json_encode(['status' => false, 'message' => 'Data Kosong']);
            return;
        }

        // 2. Load Aux Data
        $tunkel_get = $this->db->get('tunkel')->result();
        if(isset($tunkel_get)) foreach($tunkel_get as $nominaltunkel);

        $tunj_anak_get = $this->db->get('tunjanak')->result();
        if(isset($tunj_anak_get)) foreach($tunj_anak_get as $nominaltunj_anak);

        $this->db->trans_begin();
        $grand_total = 0;

        // 3. Delete existing snapshot if any
        $this->db->where('id_kehadiran_lembaga', $id_kehadiran_lembaga)->delete('total_barokah_pengajar');

        // 4. Calculate & Insert
        // Fetch Tahun Acuan Configuration
        $config_tahun_query = $this->db->get('pengaturan_tahun_acuan');
        $tahun_acuan_map = [];
        if ($config_tahun_query->num_rows() > 0) {
            foreach ($config_tahun_query->result() as $cfg) {
                $tahun_acuan_map[trim($cfg->id_bidang)] = (int)$cfg->tahun_acuan;
            }
        }

        // Meta Data Bundle
        $meta_data = [
            'nominaltunkel' => isset($nominaltunkel) ? $nominaltunkel : null,
            'nominaltunj_anak' => isset($nominaltunj_anak) ? $nominaltunj_anak : null,
            'tahun_acuan_map' => $tahun_acuan_map
        ];

        // 4. Calculate & Insert
        foreach ($list as $key) {
             // Use Shared Calculation Logic
             $calc = $this->_calculate_row($key, $meta_data);

             $grand_total += $calc['diterima'];

             $data_insert = [
                'id_pengajar' => $key->id_pengajar,
                'id_kehadiran_lembaga' => $id_kehadiran_lembaga, 
                'bulan' => $key->bulan, 
                'tahun' => $key->tahun,
                'jumlah_sks' => $key->jumlah_sks,
                'rank' => $calc['rank'],
                'mengajar' => $calc['mengajar'],
                'mp' => $calc['mp'],
                'dty' => $calc['dty'],
                'jafung' => $calc['jafung'],
                'jumlah_hadir' => $key->jumlah_hadir,
                'nominal_kehadiran' => $calc['jml_kehadiran'],
                'jumlah_hadir_15' => $key->jumlah_hadir_15,
                'nominal_hadir_15' => $calc['nominal_hadir_15'],
                'jumlah_hadir_10' => $key->jumlah_hadir_10,
                'nominal_hadir_10' => $calc['nominal_hadir_10'],
                'jumlah_hadir_piket' => $key->jumlah_hadir_piket,
                'rank_piket' => $calc['rank_piket'],
                'barokah_piket' => $calc['barokah_piket'],
                'tunkel' => $calc['tunkel'],
                'tun_anak' => $calc['tunja_anak'], 
                'kehormatan' => $calc['kehormatan'],
                'walkes' => $calc['tunj_walkes'],
                'khusus' => $calc['tambahan'], 
                'potongan' => $calc['potongan'],
                'diterima' => $calc['diterima'],
            ];

            $this->db->insert('total_barokah_pengajar', $data_insert);
        }

        // 5. Update Status
        $this->db->where('id_kehadiran_lembaga', $id_kehadiran_lembaga);
        $this->db->update('kehadiran_lembaga', ['status' => 'acc', 'jumlah_total' => $grand_total]);

        if ($this->db->trans_status() === false) {
             $this->db->trans_rollback();
             echo json_encode(['status' => false, 'message' => 'Gagal menyimpan snapshot.']);
        } else {
             $this->db->trans_commit();
             echo json_encode(['status' => true, 'message' => 'Validasi disetujui dan data terkunci (Snapshot).']);
        }
    }



    public function koreksi($id)
    {
        $this->load->helper('url');
        $this->load->helper('rupiah'); 
        $this->Login_model->getsqurity();
        $decrypted_id = $this->decrypt_url($id);

        // 1. Check Snapshot First
        // 1. Check Snapshot First
        // Rewrite to use JOIN logical link instead of unknown column
        $snapshot = $this->db->query("
            SELECT tbp.*, 
            u.gelar_depan, u.nama_lengkap, u.gelar_belakang,
            p.kategori, u.status_aktif, u.ijazah_terakhir,
            kl.bulan, kl.tahun, l.nama_lembaga, kl.status as status_periode, kl.file, kl.id_kehadiran_lembaga, kl.id_lembaga, tbp.id_total_barokah_pengajar as id_kehadiran_pengajar, kl.status
            FROM kehadiran_lembaga kl
            JOIN total_barokah_pengajar tbp ON tbp.id_kehadiran_lembaga = kl.id_kehadiran_lembaga
            JOIN pengajar p ON p.id_pengajar = tbp.id_pengajar
            JOIN umana u ON u.nik = p.nik
            JOIN lembaga l ON l.id_lembaga = kl.id_lembaga
            WHERE kl.id_kehadiran_lembaga = '$decrypted_id'
            ORDER BY u.nama_lengkap ASC
        ")->result();

        if (!empty($snapshot)) {
             // Use Snapshot Data
             $data['isilist'] = $snapshot;
             $data['is_snapshot'] = true;
        } else {
            // 2. Fetch Live Data (Existing Logic)
            $list2 = $this->db->query("select jumlah_hadir_piket, jumlah_hadir_15, jumlah_hadir_10, jafung, lembaga.id_lembaga, kehadiran_lembaga.status, status_sertifikasi, walkes, kehadiran_pengajar.id_kehadiran_pengajar, pengajar.kategori, jabatan_akademik, jumlah_sks, status_sertifikasi, ijazah_terakhir, id_bidang, tunj_anak, umana.gelar_depan, umana.gelar_belakang, kehormatan, kehadiran_lembaga.file, tunj_kel, kehadiran_lembaga.id_kehadiran_lembaga, 
            nama_lengkap, status_nikah, tmt_dosen, tmt_guru, tmt_maif, kehadiran_pengajar.id_pengajar, kehadiran_pengajar.bulan, kehadiran_pengajar.tahun, jumlah_hadir, nama_lembaga, nominal_transport, status_aktif from umana, pengajar, kehadiran_pengajar, kehadiran_lembaga,
            lembaga, transport WHERE 
            kehadiran_lembaga.id_kehadiran_lembaga = kehadiran_pengajar.id_kehadiran_lembaga and 
            pengajar.id_pengajar = kehadiran_pengajar.id_pengajar and 
            pengajar.nik = umana.nik and 
            pengajar.id_lembaga = lembaga.id_lembaga and 
            pengajar.kategori_trans = transport.id_transport and 
            DATEDIFF(NOW(), pengajar.tgl_mulai) < pengajar.tgl_selesai and
            kehadiran_lembaga.id_kehadiran_lembaga = $decrypted_id order by nama_lengkap asc ")->result();
            
            $data['isilist'] = $list2;
            $data['is_snapshot'] = false;
        }

        // 3. Aux Data (Tunkel/TunjAnak) - Only needed for live calc, but cheap to fetch
		$tunkel_get = $this->db->get('tunkel')->result();
		$tunj_anak_get = $this->db->get('tunjanak')->result();
		$data['isitunkel']  = $tunkel_get;
		$data['isitunj_anak']  = $tunj_anak_get;
        
        $data['id_kehadiran_lembaga'] = $decrypted_id; 
        $data['encrypted_id'] = $id; 

        // Fetch Tahun Acuan Configuration
        $config_tahun_query = $this->db->get('pengaturan_tahun_acuan');
        $tahun_acuan_map = [];
        if ($config_tahun_query->num_rows() > 0) {
            foreach ($config_tahun_query->result() as $cfg) {
                $tahun_acuan_map[trim($cfg->id_bidang)] = (int)$cfg->tahun_acuan;
            }
        }
        $data['tahun_acuan_map'] = $tahun_acuan_map;

        $this->load->view('Validasi_fullscreen/Validasi_pengajar', $data);
    }
}