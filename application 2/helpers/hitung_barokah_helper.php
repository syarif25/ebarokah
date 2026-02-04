<?php
defined('BASEPATH') or exit('No direct script access allowed');

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
            kl.id_kehadiran_lembaga, kl.bulan, kl.tahun, kl.file, kl.status as status_pengajuan,
            l.id_lembaga, l.nama_lembaga, l.id_bidang,
            k.id_kehadiran, k.jumlah_hadir, k.id_penempatan,
            u.nik, u.nama_lengkap, u.gelar_depan, u.gelar_belakang,
            u.tmt_struktural, p.tunj_kel, p.tunj_anak, p.tunj_mp, p.kehormatan,
            kb.nama_jabatan AS nama_jabatan, kb.barokah AS tunjab,
            t.nominal_transport
        FROM kehadiran k
        JOIN kehadiran_lembaga kl ON kl.id_kehadiran_lembaga = k.id_kehadi
        JOIN penempatan p ON p.id_penempatan = k.id_penempatan
        JOIN umana u ON u.nik = p.nik
        JOIN lembaga l ON l.id_lembaga = p.id_lembaga
        JOIN ketentuan_barokah kb ON kb.id_ketentuan = p.id_ketentuan
        JOIN transport t ON t.id_transport = p.kategori_trans
        WHERE kl.id_kehadiran_lembaga = ?
        ORDER BY kb.id_ketentuan ASC
        ";
        $rows = $CI->db->query($sql, [$idL])->result();
        if (!$rows) return ['rows'=>[], 'periode'=>null, 'totals'=>[]];

        // referensi
        $tunkel_ref     = $CI->db->get('tunkel')->row();
        $tunjanak_ref   = $CI->db->get('tunjanak')->row();
        $kehormatan_ref = $CI->db->get('barokah_kehormatan')->result();

        // tahun acuan (mengikuti kode koreksi TERBARU yang kamu kirim)
        $sekolah = 2026;
        $madrasah = 2026;
        $fakultas = 2026;
        $kantor_pusat = 2025;

        // agregat
        $total_tunjab=$total_tmp=$total_kehadiran=$total_tunkel=$total_tunjanak=0;
        $total_kehormatan=$total_tbk=$total_barokah=$total_potongan=$grand_total=0;

        foreach ($rows as $r) {
            // tahun_skrg per bidang
            switch ($r->id_bidang ?? '') {
                case 'Bidang DIKTI':    $tahun_skrg = $fakultas; break;
                case 'Bidang DIKJAR-M': $tahun_skrg = $madrasah; break;
                case 'Bidang DIKJAR':   $tahun_skrg = $sekolah;  break;
                default:                $tahun_skrg = $kantor_pusat;
            }

            // MP
            $tahun_awal = !empty($r->tmt_struktural) ? (int)date('Y', strtotime($r->tmt_struktural)) : 0;
            $r->mp  = max(0, $tahun_skrg - $tahun_awal);

            // TMP
            $r->tmp = ($r->tunj_mp != "Tidak" && $r->mp >= 3) ? floor($r->mp / 3) * 10000 : 0;

            // Kehadiran
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
