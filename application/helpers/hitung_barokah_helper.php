<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('hitung_wajib_hadir_bulanan')) {
    /**
     * Menghitung wajib hadir dalam 1 bulan berdasarkan wajib hadir per minggu
     * Kantor masuk: Sabtu - Kamis (Jumat libur)
     * 
     * @param int $bulan   Bulan (1-12)
     * @param int $tahun   Tahun (misal: 2024)
     * @param int $wajib_hadir_per_minggu  Dari ketentuan_barokah.wajib_hadir
     * @return int Jumlah hari wajib hadir dalam bulan tersebut
     */
    function hitung_wajib_hadir_bulanan($bulan, $tahun, $wajib_hadir_per_minggu)
    {
        // Konversi String Bulan (e.g. 'Januari') ke Angka (1-12)
        $map_bulan = [
            'januari'=>1, 'februari'=>2, 'maret'=>3, 'april'=>4, 'mei'=>5, 'juni'=>6,
            'juli'=>7, 'agustus'=>8, 'september'=>9, 'oktober'=>10, 'november'=>11, 'desember'=>12
        ];
        $bln = isset($map_bulan[strtolower($bulan)]) ? $map_bulan[strtolower($bulan)] : (int)date('n');

        // Total hari dalam bulan (tanpa dependency calendar extension)
        $total_hari = (int)date('t', mktime(0, 0, 0, $bln, 1, $tahun));
        
        // Hitung jumlah Jumat dalam bulan
        $jumlah_jumat = 0;
        $hari_pertama_bulan = mktime(0, 0, 0, $bln, 1, $tahun);
        $nama_hari_pertama = date('N', $hari_pertama_bulan); // 1=Senin, 5=Jumat, 6=Sabtu, 7=Minggu
        
        // Cari Jumat pertama dalam bulan
        if ($nama_hari_pertama <= 5) {
            // Jika bulan dimulai Senin-Jumat
            $tanggal_jumat_pertama = 5 - $nama_hari_pertama + 1;
        } else {
            // Jika bulan dimulai Sabtu/Minggu
            $tanggal_jumat_pertama = 12 - $nama_hari_pertama + 1;
        }
        
        // Hitung semua Jumat dalam bulan (setiap 7 hari)
        for ($tanggal = $tanggal_jumat_pertama; $tanggal <= $total_hari; $tanggal += 7) {
            $jumlah_jumat++;
        }
        
        // Total hari kerja = total hari - jumat
        $total_hari_kerja = $total_hari - $jumlah_jumat;
        
        // Wajib hadir bulanan (proporsional)
        // Formula: (hari kerja dalam bulan ÷ 6 hari kerja per minggu) × wajib hadir per minggu
        $hari_kerja_per_minggu = 6; // Sabtu-Kamis
        $wajib_hadir_bulanan = round(($total_hari_kerja / $hari_kerja_per_minggu) * $wajib_hadir_per_minggu);
        
        return (int)$wajib_hadir_bulanan;
    }
}

if (!function_exists('hitung_periode_barokah')) {
    /**
     * Hitung rekap periode barokah struktural.
     * @param CI_Controller $CI  Instance CI (kirim $this dari controller)
     * @param int|string    $idL id_kehadiran_lembaga
     * @return array ['rows'=>[], 'periode'=>object|null, 'totals'=>array]
     */
    function hitung_periode_barokah($CI, $idL)
    {
        // ambil data utama (persis seperti koreksi())
        $sql = "
        SELECT 
            kl.id_kehadiran_lembaga, kl.bulan, kl.tahun, kl.file, kl.status as status_pengajuan, kl.catatan_umum_pimpinan,
            l.id_lembaga, l.nama_lembaga, l.id_bidang,
            k.id_kehadiran, k.jumlah_hadir, k.jumlah_tugas, k.jumlah_izin, k.jumlah_sakit, k.id_penempatan,
            u.nik, u.nama_lengkap, u.gelar_depan, u.gelar_belakang,
            u.tmt_struktural, p.tunj_kel, p.tunj_anak, p.tunj_mp, p.kehormatan,
            kb.nama_jabatan AS nama_jabatan, kb.barokah AS tunjab, kb.wajib_hadir,
            t.nominal_transport
        FROM kehadiran k
        JOIN kehadiran_lembaga kl ON kl.id_kehadiran_lembaga = k.id_kehadi
        JOIN penempatan p ON p.id_penempatan = k.id_penempatan
        JOIN umana u ON u.nik = p.nik
        JOIN lembaga l ON l.id_lembaga = p.id_lembaga
        JOIN ketentuan_barokah kb ON kb.id_ketentuan = p.id_ketentuan
        JOIN transport t ON t.id_transport = p.kategori_trans
        WHERE kl.id_kehadiran_lembaga = ?
        ORDER BY kb.id_ketentuan ASC, u.nama_lengkap ASC
        ";
        $rows = $CI->db->query($sql, [$idL])->result();
        if (!$rows) return ['rows'=>[], 'periode'=>null, 'totals'=>[]];

        // referensi
        $tunkel_ref     = $CI->db->get('tunkel')->row();
        $tunjanak_ref   = $CI->db->get('tunjanak')->row();
        $kehormatan_ref = $CI->db->get('barokah_kehormatan')->result();

        // Load konfigurasi tahun acuan dari database
        $config_tahun_query = $CI->db->get('pengaturan_tahun_acuan');
        $tahun_acuan_map = [];
        
        if ($config_tahun_query->num_rows() > 0) {
            foreach ($config_tahun_query->result() as $cfg) {
                // Trim key untuk keamanan pencocokan
                $key = trim($cfg->id_bidang ?? '');
                $tahun_acuan_map[$key] = (int)$cfg->tahun_acuan;
            }
        }
        
        // Tentukan tahun default (Fallback Priority: Default -> Pengurus -> Kantor Pusat -> Tahun Ini)
        if (isset($tahun_acuan_map['Default'])) {
            $tahun_default = $tahun_acuan_map['Default'];
        } elseif (isset($tahun_acuan_map['Pengurus'])) {
            $tahun_default = $tahun_acuan_map['Pengurus']; // Sesuai screenshot DB Anda
        } elseif (isset($tahun_acuan_map['Kantor Pusat'])) {
            $tahun_default = $tahun_acuan_map['Kantor Pusat'];
        } else {
            $tahun_default = (int)date('Y');
        }

        // ==========================================
        // Ambil Data Perbandingan Komponen Bulan Lalu & Catatan
        // ==========================================
        $komponen_sebelumnya = [];
        $catatan_khusus = [];

        // Cari ID Kehadiran Lembaga bulan sebelumnya yang sudah valid
        // Kita menggunakan id_kehadiran_lembaga yg paling terakhir yg BUKAN current ID
        // BUB FIX: Pastikan status 'acc'/'selesai' DAN benar-benar sudah ada datanya di total_barokah
        $last_periode = $CI->db->query("
            SELECT DISTINCT kl.id_kehadiran_lembaga 
            FROM kehadiran_lembaga kl
            JOIN total_barokah tb ON kl.id_kehadiran_lembaga = tb.id_kehadiran
            WHERE kl.id_lembaga = ? 
            AND kl.id_kehadiran_lembaga < ? 
            AND kl.status IN ('acc', 'selesai', 'Sudah')
            ORDER BY kl.id_kehadiran_lembaga DESC 
            LIMIT 1
        ", [$rows[0]->id_lembaga, $idL])->row();

        if ($last_periode) {
            $tb_prev = $CI->db->query("
                SELECT p.nik, 
                       tb.tunjab AS tunjab_prev, 
                       tb.tmp AS tmp_prev, 
                       tb.tunkel AS tunkel_prev, 
                       tb.tunj_anak AS tunj_anak_prev, 
                       tb.kehormatan AS kehormatan_prev,
                       (tb.tunjab + tb.tmp + tb.tunkel + tb.tunj_anak + tb.kehormatan) AS total_tetap_prev 
                FROM total_barokah tb
                JOIN penempatan p ON p.id_penempatan = tb.id_penempatan
                WHERE tb.id_kehadiran = ?
            ", [$last_periode->id_kehadiran_lembaga])->result();
            
            foreach ($tb_prev as $p) {
                // Simpan dalam format Array Associative untuk melacak detail tiap komponen
                $komponen_sebelumnya[$p->nik] = [
                    'tunjab' => (int)$p->tunjab_prev,
                    'tmp' => (int)$p->tmp_prev,
                    'tunkel' => (int)$p->tunkel_prev,
                    'tunj_anak' => (int)$p->tunj_anak_prev,
                    'kehormatan' => (int)$p->kehormatan_prev,
                    'total_tetap_prev' => (int)$p->total_tetap_prev
                ];
            }
        }

        // Ambil Catatan Khusus Umana yg sudah tersimpan di bulan INI (jika ada)
        $tb_current = $CI->db->query("
            SELECT id_penempatan, catatan_khusus_umana 
            FROM total_barokah 
            WHERE id_kehadiran = ?
        ", [$idL])->result();
        foreach ($tb_current as $tc) {
            $catatan_khusus[$tc->id_penempatan] = $tc->catatan_khusus_umana;
        }

        // agregat
        $total_tunjab=$total_tmp=$total_kehadiran=$total_tunkel=$total_tunjanak=0;
        $total_kehormatan=$total_tbk=$total_barokah=$total_potongan=$grand_total=0;

        foreach ($rows as $r) {
            // Match id_bidang dengan config
            $id_bidang_row = trim($r->id_bidang ?? '');
            
            if (!empty($id_bidang_row) && isset($tahun_acuan_map[$id_bidang_row])) {
                $tahun_skrg = $tahun_acuan_map[$id_bidang_row];
            } else {
                $tahun_skrg = $tahun_default;
            }

            // MP
            $tahun_awal = !empty($r->tmt_struktural) ? (int)date('Y', strtotime($r->tmt_struktural)) : 0;
            $r->mp  = max(0, $tahun_skrg - $tahun_awal);

            // Wajib Hadir Bulanan
            $wajib_hadir_per_minggu = (int)($r->wajib_hadir ?? 0);
            $bulan_int = is_numeric($r->bulan) ? (int)$r->bulan : date('n', strtotime($r->bulan)); 
            $tahun_int = (int)$r->tahun;
            $r->wajib_hadir_bulanan = hitung_wajib_hadir_bulanan($bulan_int, $tahun_int, $wajib_hadir_per_minggu);

            // Persentase Kehadiran (dengan bobot)
            // Hadir = 1, Izin = 0.25, Sakit = 0
            $jumlah_hadir = (int)$r->jumlah_hadir;
            $jumlah_tugas = (int)($r->jumlah_tugas ?? 0); // New Field
            $jumlah_izin = (int)($r->jumlah_izin ?? 0);
            $jumlah_sakit = (int)($r->jumlah_sakit ?? 0);
            
            // Tugas and Sakit considered equivalent to Hadir for weight (1.0)
            // User Formula: (Hadir + Tugas + Sakit + Cuti) + (Izin x 0.25)
            $kehadiran_efektif = ($jumlah_hadir * 1) + ($jumlah_tugas * 1) + ($jumlah_izin * 0.25) + ($jumlah_sakit * 1.0);
            
            if ($r->wajib_hadir_bulanan > 0) {
                $r->persentase_kehadiran = round(($kehadiran_efektif / $r->wajib_hadir_bulanan) * 100, 2);
            } else {
                $r->persentase_kehadiran = 0;
            }

            // TMP
            $r->tmp = ($r->tunj_mp != "Tidak" && $r->mp >= 3) ? floor($r->mp / 3) * 10000 : 0;

            // Kehadiran (Hanya Hadir Fisik yang dapat uang transport)
            // User Request: Tugas hanya untuk persentase, tidak dapat nominal uang.
            $r->nominal_kehadiran = (int)$r->jumlah_hadir * (int)$r->nominal_transport;

            // Tunkel
            $r->tunkel = ($r->tunj_kel == "Ya" && $r->mp >= 2)
                ? (int)($tunkel_ref->besaran_tunkel ?? 0) : 0;

            // Tunjangan Anak
            $r->tunj_anak = ($r->tunj_anak == "Ya")
                ? (int)($tunjanak_ref->nominal_tunj_anak ?? 0) : 0;

            // Kehormatan
            $r->nilai_kehormatan = 0;
            if (!empty($r->kehormatan) && strtolower($r->kehormatan) == 'ya') {
                foreach ($kehormatan_ref as $ref) {
                    if ($r->mp >= (int)$ref->min_masa_pengabdian && $r->mp <= (int)$ref->max_masa_pengabdian) {
                        $r->nilai_kehormatan = (int)$ref->nominal_kehormatan;
                        break;
                    }
                }
            }

            // TBK
            $r->tbk = (int)($CI->db->query("
                SELECT SUM(nominal_tbk) AS jumlah_tbk
                FROM t_beban_kerja
                WHERE id_penempatan = ?
                  AND (max_periode IS NULL OR max_periode >= CURDATE())
            ", [$r->id_penempatan])->row()->jumlah_tbk ?? 0);

            // POTONGAN
            $r->potongan = (int)($CI->db->query("
                SELECT SUM(nominal_potongan) AS jumlah
                FROM potongan_umana
                WHERE id_penempatan = ?
                  AND (max_periode_potongan IS NULL OR max_periode_potongan >= CURDATE())
            ", [$r->id_penempatan])->row()->jumlah ?? 0);

            // total per orang
            $r->jumlah_barokah =
                (int)$r->tunjab + (int)$r->tmp + (int)$r->nominal_kehadiran +
                (int)$r->tunkel + (int)$r->tunj_anak + (int)$r->nilai_kehormatan +
                (int)$r->tbk;

            $r->diterima = (int)$r->jumlah_barokah - (int)$r->potongan;

            // ==========================================
            // Logika Evaluasi Warning & Map Catatan
            // ==========================================
            $r->catatan_khusus = isset($catatan_khusus[$r->id_penempatan]) ? $catatan_khusus[$r->id_penempatan] : '';
            
            $total_tetap_sekarang = (int)$r->tunjab + (int)$r->tmp + (int)$r->tunkel + (int)$r->tunj_anak + (int)$r->nilai_kehormatan;
            $r->is_warning = false;
            $r->selisih_komponen = 0;
            $r->komponen_berubah = []; // Array pelacak sel HTML mana yang harus di-highlight merah

            if (isset($komponen_sebelumnya[$r->nik])) {
                $prev = $komponen_sebelumnya[$r->nik];
                
                // Deteksi satu-persatu komponen
                if ((int)$r->tunjab !== $prev['tunjab'])             $r->komponen_berubah[] = 'tunjab';
                if ((int)$r->tmp !== $prev['tmp'])                   $r->komponen_berubah[] = 'tmp';
                if ((int)$r->tunkel !== $prev['tunkel'])             $r->komponen_berubah[] = 'tunkel';
                if ((int)$r->tunj_anak !== $prev['tunj_anak'])       $r->komponen_berubah[] = 'tunj_anak';
                if ((int)$r->nilai_kehormatan !== $prev['kehormatan']) $r->komponen_berubah[] = 'kehormatan';

                $tetap_prev = $prev['total_tetap_prev'];
                if ($total_tetap_sekarang !== $tetap_prev) {
                    $r->is_warning = true;
                    $r->selisih_komponen = $total_tetap_sekarang - $tetap_prev;
                }
            } // Hapus else-if (jika data lama kosong, abaikan warning agar tidak semua kuning)

            // agregat
            $total_tunjab     += (int)$r->tunjab;
            $total_tmp        += (int)$r->tmp;
            $total_kehadiran  += (int)$r->nominal_kehadiran;
            $total_tunkel     += (int)$r->tunkel;
            $total_tunjanak   += (int)$r->tunj_anak;
            $total_kehormatan += (int)$r->nilai_kehormatan;
            $total_tbk        += (int)$r->tbk;
            $total_barokah    += (int)$r->jumlah_barokah;
            $total_potongan   += (int)$r->potongan;
            $grand_total      += (int)$r->diterima;
        }

        $periode = (object)[
            'bulan' => $rows[0]->bulan,
            'tahun' => $rows[0]->tahun,
            'file'  => $rows[0]->file,
            'status'=> $rows[0]->status_pengajuan,
            'id_kehadiran_lembaga' => $rows[0]->id_kehadiran_lembaga,
            'id_lembaga'           => $rows[0]->id_lembaga,
            'catatan_umum_pimpinan'=> $rows[0]->catatan_umum_pimpinan ?? ''
        ];

        return [
            'rows'   => $rows,
            'periode'=> $periode,
            'totals' => [
                'total_tunjab'    => $total_tunjab,
                'total_tmp'       => $total_tmp,
                'total_kehadiran' => $total_kehadiran,
                'total_tunkel'    => $total_tunkel,
                'total_tunjanak'  => $total_tunjanak,
                'total_kehormatan'=> $total_kehormatan,
                'total_tbk'       => $total_tbk,
                'total_barokah'   => $total_barokah,
                'total_potongan'  => $total_potongan,
                'grand_total'     => $grand_total,
            ]
        ];
    }
}

if ( ! function_exists('catat_riwayat_barokah')) {
    /**
     * Helper untuk mencatat jejak rekam (Audit Trail) alur usulan kehadiran barokah.
     * @param int $id_kehadiran_lembaga ID master periode absensi
     * @param string $status_aksi Status (Belum, Sudah, Terkirim, Revisi, acc)
     * @param string $catatan (Opsional) Alasan atau catatan khusus
     */
    function catat_riwayat_barokah($id_kehadiran_lembaga, $status_aksi, $catatan = '') {
        $CI =& get_instance();
        // Ambil session username atau id_user, menyesuaikan key yang ada di sistem E-Barokah
        $id_pengguna = $CI->session->userdata('id_user') ? $CI->session->userdata('id_user') : ($CI->session->userdata('id_admin') ? $CI->session->userdata('id_admin') : NULL);
        
        // Pastikan tz jakarta
        date_default_timezone_set('Asia/Jakarta');

        $data = array(
            'id_kehadiran_lembaga' => (int)$id_kehadiran_lembaga,
            'status_aksi'          => $status_aksi,
            'waktu_eksekusi'       => date('Y-m-d H:i:s'),
            'id_pengguna'          => (int)$id_pengguna,
            'catatan_log'          => $catatan
        );
        return $CI->db->insert('log_riwayat_barokah', $data);
    }
}
