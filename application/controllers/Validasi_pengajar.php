<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Validasi_pengajar extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Login_model');
        // Single Source of Truth: semua kalkulasi gaji pengajar ada di helper ini
        $this->load->helper('hitung_pengajar_helper');
    }

    public function index()
    {
        redirect('Kehadiran/pengajar');
    }



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

        $id_kehadiran_pengajar = $this->input->post('id_kehadiran_pengajar');
        $jumlah_hadir          = (int)$this->input->post('jumlah_hadir');
        $jumlah_hadir_15       = (int)$this->input->post('jumlah_hadir_15');
        $jumlah_hadir_10       = (int)$this->input->post('jumlah_hadir_10');
        $jumlah_hadir_piket    = (int)$this->input->post('jumlah_hadir_piket');

        if (!$id_kehadiran_pengajar) {
            echo json_encode(array('status' => false, 'message' => 'ID tidak ditemukan'));
            return;
        }

        $id_safe = (int)$id_kehadiran_pengajar;

        // Simpan perubahan input
        $this->db->where('id_kehadiran_pengajar', $id_safe);
        $this->db->update('kehadiran_pengajar', array(
            'jumlah_hadir'       => $jumlah_hadir,
            'jumlah_hadir_15'    => $jumlah_hadir_15,
            'jumlah_hadir_10'    => $jumlah_hadir_10,
            'jumlah_hadir_piket' => $jumlah_hadir_piket
        ));

        // Ambil data lengkap setelah update — query defensive (cek false)
        $q = $this->db->query("
            select tmt_maif, kehadiran_pengajar.jumlah_hadir_piket, kehadiran_pengajar.jumlah_hadir_15,
                   kehadiran_pengajar.jumlah_hadir_10, jafung, lembaga.id_lembaga, kehadiran_lembaga.status,
                   status_sertifikasi, walkes, kehadiran_pengajar.id_kehadiran_pengajar, pengajar.kategori,
                jumlah_sks, ijazah_terakhir, lembaga.id_bidang, tunj_anak,
                   umana.gelar_depan, umana.gelar_belakang, kehormatan, kehadiran_lembaga.file, tunj_kel,
                   kehadiran_lembaga.id_kehadiran_lembaga, nama_lengkap, status_nikah, tmt_dosen, tmt_guru,
                   kehadiran_pengajar.id_pengajar, kehadiran_pengajar.bulan, kehadiran_pengajar.tahun,
                   kehadiran_pengajar.jumlah_hadir, nama_lembaga, nominal_transport, status_aktif,
                   pengajar.id_lembaga
            from umana, pengajar, kehadiran_pengajar, kehadiran_lembaga, lembaga, transport
            WHERE kehadiran_lembaga.id_kehadiran_lembaga = kehadiran_pengajar.id_kehadiran_lembaga
              AND pengajar.id_pengajar = kehadiran_pengajar.id_pengajar
              AND pengajar.nik = umana.nik
              AND pengajar.id_lembaga = lembaga.id_lembaga
              AND pengajar.kategori_trans = transport.id_transport
              AND DATEDIFF(NOW(), pengajar.tgl_mulai) < pengajar.tgl_selesai
              AND kehadiran_pengajar.id_kehadiran_pengajar = $id_safe
        ");

        if ($q === false) {
            echo json_encode(array('status' => false, 'message' => 'Query data gagal: ' . $this->db->error()['message']));
            return;
        }

        $key = $q->row();

        if (!$key) {
            echo json_encode(array('status' => false, 'message' => 'Data tidak ditemukan setelah update'));
            return;
        }

        // Bangun periode_date — tanpa ?? agar kompatibel PHP 5.6
        $_map_bln = array('januari'=>'01','februari'=>'02','maret'=>'03','april'=>'04','mei'=>'05','juni'=>'06',
                          'juli'=>'07','agustus'=>'08','september'=>'09','oktober'=>'10','november'=>'11','desember'=>'12');
        $bln_raw  = strtolower(trim($key->bulan));
        if (is_numeric($bln_raw)) {
            $bln_num = str_pad((int)$bln_raw, 2, '0', STR_PAD_LEFT);
        } else {
            $bln_num = isset($_map_bln[$bln_raw]) ? $_map_bln[$bln_raw] : date('m');
        }
        $periode_date = $key->tahun . '-' . $bln_num . '-01';

        // Kalkulasi via Single Source of Truth
        $meta = build_meta_pengajar($periode_date);
        $calc = hitung_row_pengajar($key, $meta);

        echo json_encode(array(
            'status' => true,
            'data'   => array(
                'jml_kehadiran'    => $this->rupiah($calc['jml_kehadiran']),
                'nominal_hadir_15' => $this->rupiah($calc['nominal_hadir_15']),
                'nominal_hadir_10' => $this->rupiah($calc['nominal_hadir_10']),
                'rank_piket'       => $this->rupiah($calc['rank_piket']),
                'barokah_piket'    => $this->rupiah($calc['barokah_piket']),
                'jumlah'           => $this->rupiah($calc['jumlah']),
                'diterima'         => $this->rupiah($calc['diterima']),
            )
        ));
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

    // _calculate_row() telah DIHAPUS.
    // Semua kalkulasi kini terpusat di hitung_pengajar_helper.php → hitung_row_pengajar()
    // Ini adalah Single Source of Truth untuk kalkulasi gaji pengajar.
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

    /**
     * Sinkronkan daftar pengajar:
     * Menambahkan pengajar yang baru/aktif ke periode yang sedang berjalan
     * tanpa menghapus data pengajar yang sudah diinput sebelumnya.
     */
    public function sync_pengajar_json()
    {
        $this->Login_model->getsqurity();
        $idKL = $this->input->post('id_kehadiran_lembaga', true);

        if (!$idKL) {
            echo json_encode(['status' => false, 'message' => 'ID Periode tidak ditemukan.']);
            return;
        }

        // 1. Ambil info periode & lembaga
        $periode = $this->db->get_where('kehadiran_lembaga', ['id_kehadiran_lembaga' => $idKL])->row();
        if (!$periode) {
            echo json_encode(['status' => false, 'message' => 'Data periode tidak ditemukan.']);
            return;
        }

        $id_lembaga = $periode->id_lembaga;
        $bulan = $periode->bulan;
        $tahun = $periode->tahun;

        // 2. Cari pengajar yang HARUSNYA ada (Aktif per hari ini)
        // Logika disamakan dengan Add_kehadiran.php
        $active_teachers = $this->db->query("
            SELECT id_pengajar 
            FROM pengajar 
            WHERE id_lembaga = ? 
              AND status IN ('Aktif', 'Cuti 50%', 'Cuti 100%')
              AND tgl_mulai <= CURDATE()
              AND (tgl_selesai IS NULL OR tgl_selesai >= CURDATE())
        ", [$id_lembaga])->result();

        if (empty($active_teachers)) {
            echo json_encode(['status' => false, 'message' => 'Tidak ditemukan pengajar aktif untuk lembaga ini.']);
            return;
        }

        // 3. Cari pengajar yang SUDAH ada di periode ini
        $existing_ids = [];
        $existing_query = $this->db->select('id_pengajar')
                                  ->get_where('kehadiran_pengajar', ['id_kehadiran_lembaga' => $idKL])
                                  ->result();
        foreach ($existing_query as $ex) {
            $existing_ids[] = $ex->id_pengajar;
        }

        // 4. Identifikasi yang BELUM ada (Missing)
        $data_insert = [];
        foreach ($active_teachers as $at) {
            if (!in_array($at->id_pengajar, $existing_ids)) {
                $data_insert[] = [
                    'id_kehadiran_lembaga' => $idKL,
                    'id_pengajar'          => $at->id_pengajar,
                    'bulan'                => $bulan,
                    'tahun'                => $tahun,
                    'jumlah_hadir'         => 0,
                    'jumlah_hadir_15'      => 0,
                    'jumlah_hadir_10'      => 0,
                    'jumlah_hadir_piket'   => 0
                ];
            }
        }

        if (empty($data_insert)) {
            echo json_encode(['status' => true, 'message' => 'Daftar pengajar sudah sinkron. Tidak ada data baru.']);
            return;
        }

        // 5. Eksekusi Insert Missing
        $this->db->insert_batch('kehadiran_pengajar', $data_insert);

        if ($this->db->affected_rows() > 0) {
            echo json_encode(['status' => true, 'message' => count($data_insert) . ' pengajar baru berhasil ditambahkan ke daftar.']);
        } else {
            echo json_encode(['status' => false, 'message' => 'Gagal menyinkronkan data.']);
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

        // Ambil info periode untuk filter potongan historis
        $periode = $this->db->get_where('kehadiran_lembaga', ['id_kehadiran_lembaga' => $id_kehadiran_lembaga])->row();
        if (!$periode) {
            echo json_encode(['status' => false, 'message' => 'Periode tidak ditemukan']);
            return;
        }

        // Konversi bulan Indonesia → angka → format YYYY-MM-01
        $_map_bln = ['januari'=>'01','februari'=>'02','maret'=>'03','april'=>'04','mei'=>'05','juni'=>'06',
                     'juli'=>'07','agustus'=>'08','september'=>'09','oktober'=>'10','november'=>'11','desember'=>'12'];
        $bln_raw      = strtolower(trim($periode->bulan));
        $bln_num      = is_numeric($bln_raw) ? str_pad((int)$bln_raw, 2, '0', STR_PAD_LEFT)
                      : ($_map_bln[$bln_raw] ?? date('m'));
        $periode_date = $periode->tahun . '-' . $bln_num . '-01';

        // Query semua pengajar di periode ini
        $list = $this->db->query("
            SELECT kp.*,
                   kl.bulan, kl.tahun,
                   p.kategori, p.id_lembaga, p.id_pengajar,
                   p.tunj_kel, p.tunj_anak, p.walkes, p.kehormatan, p.jafung,
                   p.jumlah_sks,
                   u.status_aktif, u.ijazah_terakhir, u.jabatan_akademik, u.status_sertifikasi,
                   u.tmt_guru, u.tmt_dosen, u.tmt_maif,
                   l.nama_lembaga, l.id_bidang,
                   t.nominal_transport
            FROM kehadiran_pengajar kp
            JOIN kehadiran_lembaga kl ON kl.id_kehadiran_lembaga = kp.id_kehadiran_lembaga
            JOIN pengajar p           ON p.id_pengajar = kp.id_pengajar
            JOIN umana u              ON u.nik = p.nik
            JOIN lembaga l            ON l.id_lembaga = p.id_lembaga
            JOIN transport t          ON t.id_transport = p.kategori_trans
            WHERE kp.id_kehadiran_lembaga = ?
              AND DATEDIFF(NOW(), p.tgl_mulai) < p.tgl_selesai
        ", [$id_kehadiran_lembaga])->result();

        if (empty($list)) {
            echo json_encode(['status' => false, 'message' => 'Data Kosong']);
            return;
        }

        // Bangun meta_data sekali (efisien, tidak query DB berkali-kali)
        $meta = build_meta_pengajar($periode_date);

        $this->db->trans_begin();
        $grand_total = 0;

        // Bersihkan snapshot lama
        $this->db->where('id_kehadiran_lembaga', $id_kehadiran_lembaga)->delete('total_barokah_pengajar');

        // Kalkulasi & insert snapshot via Single Source of Truth
        foreach ($list as $key) {
            $calc = hitung_row_pengajar($key, $meta);
            $grand_total += $calc['diterima'];

            $this->db->insert('total_barokah_pengajar', [
                'id_pengajar'          => $key->id_pengajar,
                'id_kehadiran_lembaga' => $id_kehadiran_lembaga,
                'bulan'                => $key->bulan,
                'tahun'                => $key->tahun,
                'jumlah_sks'           => $calc['jumlah_sks'],
                'rank'                 => $calc['rank'],
                'mengajar'             => $calc['mengajar'],
                'mp'                   => $calc['mp'],
                'dty'                  => $calc['dty'],
                'jafung'               => $calc['jafung'],
                'jumlah_hadir'         => $calc['jumlah_hadir'],
                'nominal_kehadiran'    => $calc['nominal_kehadiran'],
                'jumlah_hadir_15'      => $calc['jumlah_hadir_15'],
                'nominal_hadir_15'     => $calc['nominal_hadir_15'],
                'jumlah_hadir_10'      => $calc['jumlah_hadir_10'],
                'nominal_hadir_10'     => $calc['nominal_hadir_10'],
                'jumlah_hadir_piket'   => $calc['jumlah_hadir_piket'],
                'rank_piket'           => $calc['rank_piket'],
                'barokah_piket'        => $calc['barokah_piket'],
                'tunkel'               => $calc['tunkel'],
                'tun_anak'             => $calc['tun_anak'],
                'kehormatan'           => $calc['kehormatan'],
                'walkes'               => $calc['walkes'],
                'khusus'               => $calc['khusus'],
                'potongan'             => $calc['potongan'],
                'diterima'             => $calc['diterima'],
            ]);
        }

        // Update status periode
        $this->db->where('id_kehadiran_lembaga', $id_kehadiran_lembaga);
        $this->db->update('kehadiran_lembaga', ['status' => 'acc', 'jumlah_total' => $grand_total]);

        if ($this->db->trans_status() === false) {
             $this->db->trans_rollback();
             echo json_encode(['status' => false, 'message' => 'Gagal menyimpan snapshot.']);
        } else {
             $this->db->trans_commit();
             echo json_encode(['status' => true, 'message' => 'Validasi disetujui.']);
        }
    }



    public function koreksi($id)
    {
        $this->load->helper('url');
        $this->load->helper('rupiah'); 
        $this->Login_model->getsqurity();
        $decrypted_id = $this->decrypt_url($id);

        // 1. Check Snapshot First
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
             $data['isilist'] = $snapshot;
             $data['is_snapshot'] = true;
        } else {
            // 2. Fetch Live Data
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

        // 3. Aux Data
		$tunkel_get = $this->db->get('tunkel')->result();
		$tunj_anak_get = $this->db->get('tunjanak')->result();
		$data['isitunkel']    = $tunkel_get;
		$data['isitunj_anak'] = $tunj_anak_get;
        
        $data['id_kehadiran_lembaga'] = $decrypted_id; 
        $data['encrypted_id'] = $id; 

        // 4. Tahun Acuan
        $config_tahun_query = $this->db->get('pengaturan_tahun_acuan');
        $tahun_acuan_map = [];
        if ($config_tahun_query->num_rows() > 0) {
            foreach ($config_tahun_query->result() as $cfg) {
                $tahun_acuan_map[trim($cfg->id_bidang)] = (int)$cfg->tahun_acuan;
            }
        }
        $data['tahun_acuan_map'] = $tahun_acuan_map;

        // 5. Walkes Config — dari DB agar view tidak perlu hardcode
        $walkes_rows = $this->db->get('master_tarif_walkes')->result();
        $walkes_config = [];
        foreach ($walkes_rows as $w) {
            $walkes_config[$w->kode_walkes] = (int)$w->nominal;
        }
        if (empty($walkes_config)) {
            $walkes_config = ['Ya' => 100000, 'walkes_sklh' => 75000, 'walkes_amsilati' => 25000];
        }
        $data['walkes_config'] = $walkes_config;

        // 6. Periode Date — untuk filter potongan historis di view (live calc)
        $periode_row = $this->db->get_where('kehadiran_lembaga', ['id_kehadiran_lembaga' => $decrypted_id])->row();
        if ($periode_row) {
            $_map_bln = ['januari'=>'01','februari'=>'02','maret'=>'03','april'=>'04','mei'=>'05','juni'=>'06',
                         'juli'=>'07','agustus'=>'08','september'=>'09','oktober'=>'10','november'=>'11','desember'=>'12'];
            $bln_raw  = strtolower(trim($periode_row->bulan));
            $bln_num  = is_numeric($bln_raw) ? str_pad((int)$bln_raw, 2, '0', STR_PAD_LEFT)
                      : ($_map_bln[$bln_raw] ?? date('m'));
            $data['periode_date'] = $periode_row->tahun . '-' . $bln_num . '-01';
        } else {
            $data['periode_date'] = date('Y-m-01');
        }

        // Enkripsi id_lembaga untuk keamanan link Potongan di view
        if (isset($periode_row) && $periode_row) {
            $key_enc = '874jzceroier38!@#%*bjkdwdw)';
            $enc_tmp = base64_encode($periode_row->id_lembaga . $key_enc);
            $data['enc_id_lembaga'] = str_replace(['+', '/', '='], ['-', '_', ''], $enc_tmp);
        } else {
            $data['enc_id_lembaga'] = '';
        }

        $this->load->view('Validasi_fullscreen/Validasi_pengajar', $data);
    }
}