<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Helper: hitung_pengajar_helper.php
 * Single Source of Truth untuk kalkulasi gaji Pengajar (Guru & Dosen).
 *
 * Semua query DB bersifat DEFENSIVE: cek !== false sebelum memanggil ->row()/->result()
 * agar tidak terjadi Fatal Error jika query gagal.
 */
if (!function_exists('hitung_row_pengajar')) {

    function hitung_row_pengajar($key, $meta_data)
    {
        $CI =& get_instance();

        // ── Unpack meta_data ─────────────────────────────────────
        $nominaltunkel    = isset($meta_data['nominaltunkel'])    ? $meta_data['nominaltunkel']    : null;
        $nominaltunj_anak = isset($meta_data['nominaltunj_anak']) ? $meta_data['nominaltunj_anak'] : null;
        $tahun_acuan_map  = isset($meta_data['tahun_acuan_map'])  ? $meta_data['tahun_acuan_map']  : array();
        $walkes_config    = isset($meta_data['walkes_config'])    ? $meta_data['walkes_config']    : array('Ya'=>100000,'walkes_sklh'=>75000,'walkes_amsilati'=>25000);
        $periode_date     = isset($meta_data['periode_date'])     ? $meta_data['periode_date']     : date('Y-m-01');

        // ── Kehadiran dasar ──────────────────────────────────────
        $jml_kehadiran    = (int)$key->jumlah_hadir    * (int)$key->nominal_transport;
        $nominal_hadir_15 = (int)$key->jumlah_hadir_15 * 15000;
        $nominal_hadir_10 = (int)$key->jumlah_hadir_10 * 10000;

        // ── Tahun Acuan ──────────────────────────────────────────
        $tahun_default  = (int)date('Y');
        if (isset($tahun_acuan_map['Default']))        $tahun_default = $tahun_acuan_map['Default'];
        if (isset($tahun_acuan_map['Pengurus']))       $tahun_default = $tahun_acuan_map['Pengurus'];
        if (isset($tahun_acuan_map['Kantor Pusat']))   $tahun_default = $tahun_acuan_map['Kantor Pusat'];

        $tahun_madrasah = isset($tahun_acuan_map['Bidang DIKJAR-M']) ? $tahun_acuan_map['Bidang DIKJAR-M'] : $tahun_default;
        $tahun_sekolah  = isset($tahun_acuan_map['Bidang DIKJAR'])   ? $tahun_acuan_map['Bidang DIKJAR']   : $tahun_default;
        $tahun_pt       = isset($tahun_acuan_map['Bidang DIKTI'])    ? $tahun_acuan_map['Bidang DIKTI']    : $tahun_default;

        $id_bidang      = isset($key->id_bidang) ? $key->id_bidang : '';
        $kategori       = isset($key->kategori)  ? $key->kategori  : '';

        // ── Masa Pengabdian (MP) ─────────────────────────────────
        if (($kategori == 'GTY' || $kategori == 'GTT') && $id_bidang == 'Bidang DIKJAR-M') {
            $mp = $tahun_madrasah - (int)date('Y', strtotime($key->tmt_guru));
        } elseif (($kategori == 'GTY' || $kategori == 'GTT') && $id_bidang == 'Bidang DIKJAR') {
            $mp = $tahun_sekolah - (int)date('Y', strtotime($key->tmt_guru));
        } elseif (($kategori == 'DTY' || $kategori == 'DTT') && isset($key->nama_lembaga) && $key->nama_lembaga == "Ma'had Aly Sukorejo") {
            $mp = $tahun_pt - (int)date('Y', strtotime($key->tmt_maif));
        } else {
            $tmt_dosen_val = isset($key->tmt_dosen) && $key->tmt_dosen ? $key->tmt_dosen : date('Y-m-d');
            $mp = $tahun_pt - (int)date('Y', strtotime($tmt_dosen_val));
        }
        $masa_p = ($mp > 0) ? (int)$mp : 0;

        // ── Tunkel ───────────────────────────────────────────────
        $tunkel = 0;
        if (isset($key->tunj_kel) && $key->tunj_kel == 'Ya' && $masa_p >= 2 && $nominaltunkel) {
            $tunkel = (int)$nominaltunkel->besaran_tunkel;
        }
        $status_aktif = isset($key->status_aktif) ? $key->status_aktif : '';
        if ($status_aktif == 'Cuti 50%')  $tunkel = (int)round($tunkel * 0.5);
        if ($status_aktif == 'Cuti 100%') $tunkel = 0;

        // ── Tunjangan Anak ───────────────────────────────────────
        $tunja_anak = 0;
        if (isset($key->tunj_anak) && $key->tunj_anak == 'Ya' && $nominaltunj_anak) {
            $tunja_anak = (int)$nominaltunj_anak->nominal_tunj_anak;
        }
        if ($status_aktif == 'Cuti 50%')  $tunja_anak = (int)round($tunja_anak * 0.5);
        if ($status_aktif == 'Cuti 100%') $tunja_anak = 0;

        // ── Walkes — dari $walkes_config (tabel master_tarif_walkes) ─
        $walkes_key  = isset($key->walkes) ? $key->walkes : '';
        $tunj_walkes = isset($walkes_config[$walkes_key]) ? (int)$walkes_config[$walkes_key] : 0;
        if ($status_aktif == 'Cuti 50%')  $tunj_walkes = (int)round($tunj_walkes * 0.5);
        if ($status_aktif == 'Cuti 100%') $tunj_walkes = 0;

        // ── Rank / Honor Mengajar ────────────────────────────────
        $rank     = 0;
        $ijazah   = isset($key->ijazah_terakhir) ? $key->ijazah_terakhir : '';
        $id_lemb  = isset($key->id_lembaga)      ? (int)$key->id_lembaga : 0;

        if ($kategori == 'GTY' || $kategori == 'GTT') {
            $q = $CI->db->query(
                "SELECT nominal FROM barokah_pengajar WHERE min_tmp_mengajar <= $masa_p AND max_tmp_mengajar >= $masa_p AND ijazah = ? AND kategori = 'Guru'",
                array($ijazah)
            );
            if ($q !== false) {
                $res = $q->result();
                if (!empty($res)) $rank = (int)$res[0]->nominal;
            }
        } else {
            $kat_dosen = ($id_lemb == 39) ? 'Dosen MAIF' : (($id_lemb == 37) ? 'Dosen FIK' : 'Dosen UNIB');
            $q = $CI->db->query(
                "SELECT nominal FROM barokah_pengajar WHERE min_tmp_mengajar <= $masa_p AND max_tmp_mengajar >= $masa_p AND ijazah = ? AND kategori = ?",
                array($ijazah, $kat_dosen)
            );
            if ($q !== false) {
                $res = $q->result();
                if (!empty($res)) $rank = (int)$res[0]->nominal;
            }
        }

        $jumlah_sks    = isset($key->jumlah_sks)      ? (int)$key->jumlah_sks      : 0;
        $jumlah_piket  = isset($key->jumlah_hadir_piket) ? (int)$key->jumlah_hadir_piket : 0;
        $mengajar      = (int)round($rank * $jumlah_sks);
        if ($status_aktif == 'Cuti 50%')  $mengajar = (int)round($mengajar * 0.5);
        if ($status_aktif == 'Cuti 100%') $mengajar = 0;

        $rank_piket    = ($rank > 0) ? (float)($rank / 4) : 0;
        $barokah_piket = (int)round($rank_piket * $jumlah_piket);

        // ── Kehormatan ───────────────────────────────────────────
        $kehormatan     = 0;
        $kat_kehormatan = (in_array($kategori, array('GTY','GTT'))) ? 'Guru' : 'Dosen';
        if (isset($key->kehormatan) && strtolower($key->kehormatan) == 'ya') {
            $q = $CI->db->query(
                "SELECT nominal FROM barokah_kehormatan_pengajar WHERE min_masa_pengabdian <= $masa_p AND max_masa_pengabdian >= $masa_p AND kategori = ?",
                array($kat_kehormatan)
            );
            if ($q !== false) {
                $res = $q->result();
                if (!empty($res)) $kehormatan = (int)$res[0]->nominal;
            }
        }
        if ($status_aktif == 'Cuti 50%')  $kehormatan = (int)round($kehormatan * 0.5);
        if ($status_aktif == 'Cuti 100%') $kehormatan = 0;

        // ── DTY ──────────────────────────────────────────────────
        $dty          = 0;
        $status_serti = isset($key->status_sertifikasi) ? $key->status_sertifikasi : '';
        if ($kategori == 'GTY' && $status_serti == 'Belum' && $masa_p > 2) {
            $q = $CI->db->query("SELECT nominal FROM barokah_pengajar_tetap WHERE kategori = 'Guru'");
            if ($q !== false) {
                $res = $q->result();
                if (!empty($res)) $dty = (int)$res[0]->nominal;
            }
        } elseif ($kategori == 'DTY' && $status_serti == 'Belum' && $masa_p > 2) {
            $q = $CI->db->query("SELECT nominal FROM barokah_pengajar_tetap WHERE kategori = 'Dosen'");
            if ($q !== false) {
                $res = $q->result();
                if (!empty($res)) $dty = (int)$res[0]->nominal;
            }
        }
        if ($status_aktif == 'Cuti 50%')  $dty = (int)round($dty * 0.5);
        if ($status_aktif == 'Cuti 100%') $dty = 0;

        // ── Jafung ───────────────────────────────────────────────
        $jafung     = 0;
        $jabatan_ak = isset($key->jabatan_akademik) ? $key->jabatan_akademik : '';
        $jafung_flag = isset($key->jafung) ? $key->jafung : '';
        if ($jabatan_ak != '' && $jafung_flag == 'Ya') {
            $q = $CI->db->query(
                "SELECT bj.nominal FROM barokah_jafung bj, umana u WHERE u.jabatan_akademik = bj.id_barokah_jafung AND u.jabatan_akademik = '$jabatan_ak'"
            );
            if ($q !== false) {
                $res = $q->result();
                if (!empty($res)) $jafung = (int)$res[0]->nominal;
            }
        }
        if ($status_aktif == 'Cuti 50%')  $jafung = (int)round($jafung * 0.5);
        if ($status_aktif == 'Cuti 100%') $jafung = 0;

        // ── Potongan — filter historis (bukan CURDATE) ───────────
        $id_pengajar = isset($key->id_pengajar) ? (int)$key->id_pengajar : 0;
        $potongan    = 0;
        $q = $CI->db->query(
            "SELECT SUM(nominal_potongan) AS jumlah FROM potongan_pengajar WHERE id_pengajar = $id_pengajar AND (min_periode_potongan IS NULL OR min_periode_potongan <= LAST_DAY('$periode_date')) AND (max_periode_potongan IS NULL OR max_periode_potongan >= '$periode_date')"
        );
        if ($q !== false) {
            $r = $q->row();
            if ($r && isset($r->jumlah)) $potongan = (int)$r->jumlah;
        }

        // ── Tambahan — filter historis ────────────────────────────
        $tambahan = 0;
        $q = $CI->db->query(
            "SELECT SUM(nominal_tambahan) AS jumlah FROM barokah_tambahan WHERE id_pengajar = $id_pengajar AND (min_periode_tambahan IS NULL OR min_periode_tambahan <= LAST_DAY('$periode_date')) AND (max_periode_tambahan IS NULL OR max_periode_tambahan >= '$periode_date')"
        );
        if ($q !== false) {
            $r = $q->row();
            if ($r && isset($r->jumlah)) $tambahan = (int)$r->jumlah;
        }

        // ── Grand Total ──────────────────────────────────────────
        $jumlah   = $jml_kehadiran + $nominal_hadir_15 + $nominal_hadir_10
                  + $barokah_piket + $mengajar + $tunkel + $tunja_anak
                  + $tunj_walkes + $kehormatan + $dty + $jafung + $tambahan;
        $diterima = $jumlah - $potongan;

        // ── Return ───────────────────────────────────────────────
        return array(
            // Identitas
            'id_pengajar'        => $id_pengajar,
            'jumlah_sks'         => $jumlah_sks,
            'mp'                 => $masa_p,
            // Komponen Kehadiran
            'jumlah_hadir'       => (int)$key->jumlah_hadir,
            'nominal_kehadiran'  => $jml_kehadiran,
            'jumlah_hadir_15'    => (int)$key->jumlah_hadir_15,
            'nominal_hadir_15'   => $nominal_hadir_15,
            'jumlah_hadir_10'    => (int)$key->jumlah_hadir_10,
            'nominal_hadir_10'   => $nominal_hadir_10,
            'jumlah_hadir_piket' => $jumlah_piket,
            'rank_piket'         => $rank_piket,
            'barokah_piket'      => $barokah_piket,
            // Komponen Gaji
            'rank'               => $rank,
            'mengajar'           => $mengajar,
            'dty'                => $dty,
            'jafung'             => $jafung,
            'tunkel'             => $tunkel,
            'tun_anak'           => $tunja_anak,
            'walkes'             => $tunj_walkes,
            'kehormatan'         => $kehormatan,
            'khusus'             => $tambahan,
            'potongan'           => $potongan,
            // Total
            'jumlah'             => $jumlah,
            'diterima'           => $diterima,
            // Alias untuk view AJAX
            'jml_kehadiran'      => $jml_kehadiran,
            'tunja_anak'         => $tunja_anak,
            'tunj_walkes'        => $tunj_walkes,
            'tambahan'           => $tambahan,
            'masa_p'             => $masa_p,
        );
    }
}

/**
 * build_meta_pengajar() — Query aux data sekali di luar loop.
 */
if (!function_exists('build_meta_pengajar')) {

    function build_meta_pengajar($periode_date = null)
    {
        $CI =& get_instance();

        // Walkes dari tabel master (bukan hardcode)
        $walkes_config = array();
        $q = $CI->db->get('master_tarif_walkes');
        if ($q !== false) {
            foreach ($q->result() as $w) {
                $walkes_config[$w->kode_walkes] = (int)$w->nominal;
            }
        }
        if (empty($walkes_config)) {
            $walkes_config = array('Ya' => 100000, 'walkes_sklh' => 75000, 'walkes_amsilati' => 25000);
        }

        // Referensi tunjangan
        $nominaltunkel    = null;
        $nominaltunj_anak = null;
        $q = $CI->db->get('tunkel');
        if ($q !== false) {
            $r = $q->result();
            if (!empty($r)) $nominaltunkel = $r[0];
        }
        $q = $CI->db->get('tunjanak');
        if ($q !== false) {
            $r = $q->result();
            if (!empty($r)) $nominaltunj_anak = $r[0];
        }

        // Tahun acuan per bidang
        $tahun_acuan_map = array();
        $q = $CI->db->get('pengaturan_tahun_acuan');
        if ($q !== false) {
            foreach ($q->result() as $cfg) {
                $tahun_acuan_map[trim($cfg->id_bidang)] = (int)$cfg->tahun_acuan;
            }
        }

        return array(
            'nominaltunkel'    => $nominaltunkel,
            'nominaltunj_anak' => $nominaltunj_anak,
            'tahun_acuan_map'  => $tahun_acuan_map,
            'walkes_config'    => $walkes_config,
            'periode_date'     => $periode_date ? $periode_date : date('Y-m-01'),
        );
    }
}
