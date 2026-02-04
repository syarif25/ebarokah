<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Report_model extends CI_Model {
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

    private function _nama_bulan_id($n) {
        $m = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        return $m[(int)$n] ?? $n;
    }
    
    private function _tahun_ajaran($bulan, $tahun) {
        if ((int)$bulan >= 7) { // mulai TA di Juli
            return $tahun . '/' . ($tahun + 1);
        } else {
            return ($tahun - 1) . '/' . $tahun;
        }
    }    

	public function get_profile_by_nik($nik)
    {
        $row = $this->db
            ->select("TRIM(CONCAT(COALESCE(gelar_depan,''),' ',nama_lengkap,
                     CASE WHEN COALESCE(gelar_belakang,'')<>'' THEN CONCAT(', ',gelar_belakang) ELSE '' END)) AS nama_gelar", false)
            ->from('umana')
            ->where('nik', $nik)
            ->get()->row_array();

        return $row ?: ['nama_gelar' => '-'];
    }

    public function count_pengabdian_aktif($nik)
    {
        $today = date('Y-m-d');

        // penempatan
        $q1 = $this->db->select('COUNT(*) AS c', false)
            ->from('penempatan')
            ->where('nik', $nik)
            ->group_start()
                ->where('tgl_mulai IS NULL', null, false)
                ->or_where('tgl_mulai <=', $today)
            ->group_end()
            ->group_start()
                ->where('tgl_selesai IS NULL', null, false)
                ->or_where('tgl_selesai >=', $today)
            ->group_end()
            ->get()->row()->c;

        // pengajar
        $q2 = $this->db->select('COUNT(*) AS c', false)
            ->from('pengajar')
            ->where('nik', $nik)
            ->group_start()
                ->where('tgl_mulai IS NULL', null, false)
                ->or_where('tgl_mulai <=', $today)
            ->group_end()
            ->group_start()
                ->where('tgl_selesai IS NULL', null, false)
                ->or_where('tgl_selesai >=', $today)
            ->group_end()
            ->get()->row()->c;

        // satpam
        $q3 = $this->db->select('COUNT(*) AS c', false)
            ->from('satpam')
            ->where('nik', $nik)
            ->group_start()
                ->where('tgl_mulai IS NULL', null, false)
                ->or_where('tgl_mulai <=', $today)
            ->group_end()
            ->group_start()
                ->where('tgl_selesai IS NULL', null, false)
                ->or_where('tgl_selesai >=', $today)
            ->group_end()
            ->get()->row()->c;

        return (int)$q1 + (int)$q2 + (int)$q3;
    }

    public function list_struktural_aktif($nik)
    {
        $today = date('Y-m-d');

        return $this->db->select('l.nama_lembaga, p.jabatan_lembaga')
            ->from('penempatan p')
            ->join('lembaga l', 'l.id_lembaga = p.id_lembaga', 'left')
            ->where('p.nik', $nik)
            ->group_start()
                ->where('p.tgl_mulai IS NULL', null, false)->or_where('p.tgl_mulai <=', $today)
            ->group_end()
            ->group_start()
                ->where('p.tgl_selesai IS NULL', null, false)->or_where('p.tgl_selesai >=', $today)
            ->group_end()
            ->order_by('l.nama_lembaga', 'ASC')
            ->get()->result_array();
    }

    public function list_pengajar_aktif($nik)
    {
        $today = date('Y-m-d');

        return $this->db->select('l.nama_lembaga, pg.kategori', false)
            ->from('pengajar pg')
            ->join('lembaga l', 'l.id_lembaga = pg.id_lembaga', 'left')
            ->where('pg.nik', $nik)
            ->group_start()
                ->where('pg.tgl_mulai IS NULL', null, false)->or_where('pg.tgl_mulai <=', $today)
            ->group_end()
            ->group_start()
                ->where('pg.tgl_selesai IS NULL', null, false)->or_where('pg.tgl_selesai >=', $today)
            ->group_end()
            ->order_by('l.nama_lembaga', 'ASC')
            ->get()->result_array();
    }

    public function sum_barokah_last_two_months($nik, $startTs, $endTs)
    {
        $combine = [];
        $acc = function($rows) use (&$combine) {
            foreach ($rows as $r) {
                $key = sprintf('%04d-%02d', (int)$r['y'], (int)$r['m']);
                if (!isset($combine[$key])) {
                    $combine[$key] = [
                        'y' => (int)$r['y'],
                        'm' => (int)$r['m'],
                        'label_bulan' => $r['label_bulan'] ?? null,
                        'label_tahun' => $r['label_tahun'] ?? null,
                        'total' => 0
                    ];
                }
                $combine[$key]['total'] += (int)$r['total'];
                if (!$combine[$key]['label_bulan'] && !empty($r['label_bulan'])) {
                    $combine[$key]['label_bulan'] = $r['label_bulan'];
                }
                if (!$combine[$key]['label_tahun'] && !empty($r['label_tahun'])) {
                    $combine[$key]['label_tahun'] = $r['label_tahun'];
                }
            }
        };

        // STRUKTURAL (tb.timestamp)
        $acc($this->db->query("
            SELECT YEAR(tb.`timestamp`) AS y,
                MONTH(tb.`timestamp`) AS m,
                MIN(tb.bulan)        AS label_bulan,
                YEAR(tb.`timestamp`) AS label_tahun,
                COALESCE(SUM(tb.diterima),0) AS total
            FROM total_barokah tb
            JOIN penempatan p ON p.id_penempatan = tb.id_penempatan
            WHERE p.nik = ? AND tb.`timestamp` >= ? AND tb.`timestamp` < ?
            GROUP BY y, m
        ", [$nik, $startTs, $endTs])->result_array());

        // PENGAJAR (tbp.timestamp)
        $acc($this->db->query("
            SELECT YEAR(tbp.`timestamp`) AS y,
                MONTH(tbp.`timestamp`) AS m,
                MIN(tbp.bulan)         AS label_bulan,
                YEAR(tbp.`timestamp`)  AS label_tahun,
                COALESCE(SUM(tbp.diterima),0) AS total
            FROM total_barokah_pengajar tbp
            JOIN pengajar pg ON pg.id_pengajar = tbp.id_pengajar
            WHERE pg.nik = ? AND tbp.`timestamp` >= ? AND tbp.`timestamp` < ?
            GROUP BY y, m
        ", [$nik, $startTs, $endTs])->result_array());

        // SATPAM (tbs.tgl_input)
        $acc($this->db->query("
            SELECT YEAR(tbs.`tgl_input`) AS y,
                MONTH(tbs.`tgl_input`) AS m,
                MIN(tbs.bulan)         AS label_bulan,
                YEAR(tbs.`tgl_input`)  AS label_tahun,
                COALESCE(SUM(tbs.diterima),0) AS total
            FROM total_barokah_satpam tbs
            JOIN satpam sp ON sp.id_satpam = tbs.id_satpam
            WHERE sp.nik = ? AND tbs.`tgl_input` >= ? AND tbs.`tgl_input` < ?
            GROUP BY y, m
        ", [$nik, $startTs, $endTs])->result_array());

        return $combine;
    }


    public function list_barokah_last_two_months($nik, $startTs, $endTs)
    {
        $sql = "
        SELECT u.y, u.m, u.label_bulan, u.label_tahun, u.lembaga, u.jenis, u.nominal
        FROM (
            -- Struktural
            SELECT YEAR(tb.`timestamp`) AS y, MONTH(tb.`timestamp`) AS m,
                tb.bulan AS label_bulan, YEAR(tb.`timestamp`) AS label_tahun,
                l.nama_lembaga AS lembaga, 'Struktural' AS jenis, tb.diterima AS nominal
            FROM total_barokah tb
            JOIN penempatan p ON p.id_penempatan = tb.id_penempatan
            JOIN lembaga l    ON l.id_lembaga    = p.id_lembaga
            WHERE p.nik = ? AND tb.`timestamp` >= ? AND tb.`timestamp` < ?

            UNION ALL

            -- Pengajar
            SELECT YEAR(tbp.`timestamp`) AS y, MONTH(tbp.`timestamp`) AS m,
                tbp.bulan AS label_bulan, YEAR(tbp.`timestamp`) AS label_tahun,
                l.nama_lembaga AS lembaga, 'Pengajar' AS jenis, tbp.diterima AS nominal
            FROM total_barokah_pengajar tbp
            JOIN pengajar pg ON pg.id_pengajar = tbp.id_pengajar
            JOIN lembaga l   ON l.id_lembaga   = pg.id_lembaga
            WHERE pg.nik = ? AND tbp.`timestamp` >= ? AND tbp.`timestamp` < ?

            UNION ALL

            -- Satpam (lewat kehadiran_lembaga untuk nama lembaga)
            SELECT YEAR(tbs.`tgl_input`) AS y, MONTH(tbs.`tgl_input`) AS m,
                tbs.bulan AS label_bulan, YEAR(tbs.`tgl_input`) AS label_tahun,
                COALESCE(l.nama_lembaga,'(Tidak terkait lembaga)') AS lembaga,
                'Satpam' AS jenis, tbs.diterima AS nominal
            FROM total_barokah_satpam tbs
            JOIN satpam sp ON sp.id_satpam = tbs.id_satpam
            LEFT JOIN kehadiran_lembaga kl ON kl.id_kehadiran_lembaga = tbs.id_kehadiran_lembaga
            LEFT JOIN lembaga l ON l.id_lembaga = kl.id_lembaga
            WHERE sp.nik = ? AND tbs.`tgl_input` >= ? AND tbs.`tgl_input` < ?
        ) u
        ORDER BY u.y DESC, u.m DESC, u.lembaga, u.jenis
        ";

        return $this->db->query($sql, [
            $nik, $startTs, $endTs,  // Struktural
            $nik, $startTs, $endTs,  // Pengajar
            $nik, $startTs, $endTs   // Satpam
        ])->result_array();
    }

    public function list_barokah_bulanan($nik, $bulan, $tahun)
    {
        $bulan_nama   = $this->_nama_bulan_id($bulan);
        $tahun_ajaran = $this->_tahun_ajaran($bulan, $tahun);

        $sql = "
            SELECT u.lembaga, u.jenis, u.nominal
            FROM (
                -- Struktural
                SELECT 
                    l.nama_lembaga AS lembaga,
                    'Struktural'   AS jenis,
                    COALESCE(tb.diterima,0) AS nominal
                FROM total_barokah tb
                JOIN penempatan p ON p.id_penempatan = tb.id_penempatan
                JOIN lembaga l    ON l.id_lembaga    = p.id_lembaga
                WHERE p.nik = ?
                AND (tb.bulan = ? OR tb.bulan = ?)
                AND (tb.tahun = ? OR tb.tahun = ?)

                UNION ALL

                -- Pengajar
                SELECT 
                    l.nama_lembaga AS lembaga,
                    'Pengajar'     AS jenis,
                    COALESCE(tbp.diterima,0) AS nominal
                FROM total_barokah_pengajar tbp
                JOIN pengajar pg ON pg.id_pengajar = tbp.id_pengajar
                JOIN lembaga l   ON l.id_lembaga   = pg.id_lembaga
                WHERE pg.nik = ?
                AND (tbp.bulan = ? OR tbp.bulan = ?)
                AND (tbp.tahun = ? OR tbp.tahun = ?)

                UNION ALL

                -- Satpam (tanpa lembaga)
                SELECT 
                    '(Tidak terkait lembaga)' AS lembaga,
                    'Satpam' AS jenis,
                    COALESCE(tbs.diterima,0) AS nominal
                FROM total_barokah_satpam tbs
                JOIN satpam sp ON sp.id_satpam = tbs.id_satpam
                WHERE sp.nik = ?
                AND (tbs.bulan = ? OR tbs.bulan = ?)
                AND (tbs.tahun = ? OR tbs.tahun = ?)
            ) u
            ORDER BY u.lembaga, u.jenis
        ";

        return $this->db->query($sql, [
            // Struktural
            $nik, $bulan, $bulan_nama, $tahun, $tahun_ajaran,
            // Pengajar
            $nik, $bulan, $bulan_nama, $tahun, $tahun_ajaran,
            // Satpam
            $nik, $bulan, $bulan_nama, $tahun, $tahun_ajaran,
        ])->result_array();
    }

    public function sum_bulanan_by_kategori($nik, $k, $start, $end)
    {
        switch (strtolower($k)) {
            case 'pengajar':
                $sql = "
                    SELECT YEAR(t.`timestamp`) AS y,
                        MONTH(t.`timestamp`) AS m,
                        MIN(t.bulan)         AS label_bulan,
                        YEAR(t.`timestamp`)  AS label_tahun,
                        COALESCE(SUM(t.diterima),0) AS total
                    FROM total_barokah_pengajar t
                    JOIN pengajar pg ON pg.id_pengajar = t.id_pengajar
                    WHERE pg.nik = ? AND t.`timestamp` >= ? AND t.`timestamp` < ?
                    GROUP BY y,m
                    ORDER BY y,m
                ";
                $bind = [$nik, $start, $end];
                break;

            case 'satpam':
                $sql = "
                    SELECT YEAR(t.`tgl_input`) AS y,
                        MONTH(t.`tgl_input`) AS m,
                        MIN(t.bulan)         AS label_bulan,
                        YEAR(t.`tgl_input`)  AS label_tahun,
                        COALESCE(SUM(t.diterima),0) AS total
                    FROM total_barokah_satpam t
                    JOIN satpam sp ON sp.id_satpam = t.id_satpam
                    WHERE sp.nik = ? AND t.`tgl_input` >= ? AND t.`tgl_input` < ?
                    GROUP BY y,m
                    ORDER BY y,m
                ";
                $bind = [$nik, $start, $end];
                break;

            default: // struktural
                $sql = "
                    SELECT YEAR(t.`timestamp`) AS y,
                        MONTH(t.`timestamp`) AS m,
                        MIN(t.bulan)         AS label_bulan,
                        YEAR(t.`timestamp`)  AS label_tahun,
                        COALESCE(SUM(t.diterima),0) AS total
                    FROM total_barokah t
                    JOIN penempatan p ON p.id_penempatan = t.id_penempatan
                    WHERE p.nik = ? AND t.`timestamp` >= ? AND t.`timestamp` < ?
                    GROUP BY y,m
                    ORDER BY y,m
                ";
                $bind = [$nik, $start, $end];
        }

        return $this->db->query($sql, $bind)->result_array();
    }

    public function detail_bulanan_by_kategori($nik, $k, $start, $end)
    {
        switch (strtolower($k)) {
            case 'pengajar':
                $sql = "
                    SELECT l.nama_lembaga AS lembaga, 'Pengajar' AS jenis, t.diterima AS nominal,
                        pg.id_lembaga AS ent_id
                    FROM total_barokah_pengajar t
                    JOIN pengajar pg ON pg.id_pengajar = t.id_pengajar
                    JOIN lembaga  l  ON l.id_lembaga  = pg.id_lembaga
                    WHERE pg.nik = ? AND t.`timestamp` >= ? AND t.`timestamp` < ?
                    ORDER BY l.nama_lembaga
                ";
                $bind = [$nik, $start, $end];
                break;

            case 'satpam':
                $sql = "
                    SELECT COALESCE(l.nama_lembaga,'(Satpam)') AS lembaga,
                        'Satpam' AS jenis,
                        SUM(t.diterima) AS nominal,
                        kl.id_lembaga AS ent_id
                    FROM total_barokah_satpam t
                    JOIN satpam sp ON sp.id_satpam = t.id_satpam
                    LEFT JOIN kehadiran_lembaga kl ON kl.id_kehadiran_lembaga = t.id_kehadiran_lembaga
                    LEFT JOIN lembaga l ON l.id_lembaga = kl.id_lembaga
                    WHERE sp.nik = ? AND t.`tgl_input` >= ? AND t.`tgl_input` < ?
                    GROUP BY lembaga, jenis, ent_id
                    ORDER BY lembaga
                ";
                $bind = [$nik, $start, $end];
                break;

            default: // struktural
                $sql = "
                    SELECT l.nama_lembaga AS lembaga, 'Struktural' AS jenis, t.diterima AS nominal,
                        p.id_lembaga AS ent_id
                    FROM total_barokah t
                    JOIN penempatan p ON p.id_penempatan = t.id_penempatan
                    JOIN lembaga   l ON l.id_lembaga    = p.id_lembaga
                    WHERE p.nik = ? AND t.`timestamp` >= ? AND t.`timestamp` < ?
                    ORDER BY l.nama_lembaga
                ";
                $bind = [$nik, $start, $end];
        }

        return $this->db->query($sql, $bind)->result_array();
    }

    public function breakdown_pengajar_by_month($nik, $startTs, $endTs, $entId = null)
    {
        $whereL = '';
        $bind   = [$nik, $startTs, $endTs];

        if (!empty($entId)) {
            $whereL = ' AND pg.id_lembaga = ? ';
            $bind[] = $entId;
        }

        $sql = "
            SELECT
                COALESCE(SUM(tbp.jumlah_sks),0)        AS sks,
                COALESCE(SUM(tbp.mengajar),0)          AS mengajar,
                COALESCE(SUM(tbp.dty),0)               AS gty_dty,
                COALESCE(SUM(tbp.jafung),0)            AS jafung,
                COALESCE(SUM(tbp.nominal_kehadiran),0) AS kehadiran,
                COALESCE(SUM(tbp.jumlah_hadir_15),0)   AS jhadir15,
                COALESCE(SUM(tbp.nominal_hadir_15),0)  AS nhadir15,
                COALESCE(SUM(tbp.jumlah_hadir_10),0)   AS jhadir10,
                COALESCE(SUM(tbp.nominal_hadir_10),0)  AS nhadir10,
                COALESCE(SUM(tbp.walkes),0)            AS walkes,
                COALESCE(SUM(tbp.kehormatan),0)        AS kehormatan,
                COALESCE(SUM(tbp.khusus),0)            AS tambahan,
                COALESCE(SUM(tbp.barokah_piket),0)     AS piket,
                COALESCE(SUM(tbp.tun_anak),0)          AS tun_anak,
                COALESCE(SUM(tbp.potongan),0)          AS potongan,
                COALESCE(SUM(tbp.diterima),0)          AS total
            FROM total_barokah_pengajar tbp
            JOIN pengajar pg ON pg.id_pengajar = tbp.id_pengajar
            WHERE pg.nik = ? AND tbp.`timestamp` >= ? AND tbp.`timestamp` < ? {$whereL}
        ";
        $row = $this->db->query($sql, $bind)->row_array() ?: [];
        $row['per_sks'] = (!empty($row['sks']) && (int)$row['sks']>0)
            ? (int) round(((int)$row['mengajar']) / (int)$row['sks']) : 0;
        return $row;
    }


    public function breakdown_struktural_by_month($nik, $startTs, $endTs, $id_lembaga = null)
    {
        // --- Komponen nominal dari total_barokah ---
        $bind  = [$nik, $startTs, $endTs];
        $extra = '';
        if (!empty($id_lembaga)) { $extra = ' AND p.id_lembaga = ? '; $bind[] = $id_lembaga; }

        $sql = "
            SELECT
                COALESCE(SUM(t.tunjab),0)   AS barokah_pokok,     -- ditampilkan sebagai 'Barokah Pokok'
                COALESCE(SUM(t.nominal_kehadiran),0) AS kehadiran_nominal, -- 'Kehadiran' (rupiah)
                COALESCE(SUM(t.tunkel),0)           AS tunkel,            -- Tunjangan Keluarga
                COALESCE(SUM(t.tunj_anak),0)         AS tun_anak,          -- Tunjangan Anak
                COALESCE(SUM(t.tmp),0)              AS tmp,               -- Tunjangan Masa Pengabdian
                COALESCE(SUM(t.tbk),0)              AS tbk,               -- Tunjangan Beban Kerja
                COALESCE(SUM(t.kehormatan),0)       AS kehormatan,        -- Tunjangan Kehormatan
                COALESCE(SUM(t.potongan),0)         AS potongan,          -- Potongan (total)
                COALESCE(SUM(t.diterima),0)         AS total              -- Total diterima
            FROM total_barokah t
            JOIN penempatan p ON p.id_penempatan = t.id_penempatan
            WHERE p.nik = ?
            AND t.`timestamp` >= ?
            AND t.`timestamp` < ?
            {$extra}
        ";
        $row = $this->db->query($sql, $bind)->row_array() ?: [];

        // --- Jumlah hadir normal (KALI) dari tabel kehadiran (opsional, untuk subtitle) ---
        $bindH  = [$nik, $startTs, $endTs];
        $extraH = '';
        if (!empty($id_lembaga)) { $extraH = ' AND p.id_lembaga = ? '; $bindH[] = $id_lembaga; }

        // Ambil total hadir normal dalam window bulan yang sama
        $sqlH = "
            SELECT COALESCE(SUM(k.jumlah_hadir),0) AS jhadir
            FROM kehadiran k
            JOIN penempatan p ON p.id_penempatan = k.id_penempatan
            WHERE p.nik = ?
            AND k.`tgl_input` >= ?
            AND k.`tgl_input` < ?
            {$extraH}
        ";
        $jhadir = (int)($this->db->query($sqlH, $bindH)->row()->jhadir ?? 0);

        $row['jhadir_normal'] = $jhadir; // untuk subtitle "Hadir Normal N Kali"
        return $row;
    }


    
    public function breakdown_satpam_by_month($nik, $startTs, $endTs, $entId = null)
    {
        // $entId di sini = id_lembaga dari kehadiran_lembaga (opsional)
        $whereL = '';
        $bind   = [$nik, $startTs, $endTs];
        if (!empty($entId)) {
            $whereL = ' AND kl.id_lembaga = ? ';
            $bind[] = $entId;
        }

        $sql = "
            SELECT
                COALESCE(SUM(t.nominal_transport * t.jumlah_transport),0) AS transport,
                COALESCE(SUM(t.konsumsi * t.jumlah_konsumsi),0)           AS konsumsi,
                COALESCE(SUM(t.diterima),0)                               AS total
            FROM total_barokah_satpam t
            JOIN satpam sp ON sp.id_satpam = t.id_satpam
            LEFT JOIN kehadiran_lembaga kl ON kl.id_kehadiran_lembaga = t.id_kehadiran_lembaga
            WHERE sp.nik = ? AND t.`tgl_input` >= ? AND t.`tgl_input` < ? {$whereL}
        ";
        return $this->db->query($sql, $bind)->row_array() ?: [];
    }

    // === POTONGAN: PENGAJAR ===
    // === POTONGAN: PENGAJAR (tabel potongan_pengajar) ===
// Ambil semua potongan yang periode-nya overlap dengan bulan [startTs, endTs),
// difilter NIK dan opsional id_lembaga (baris yang diklik).
public function list_potongan_pengajar_by_month($nik, $startTs, $endTs, $id_lembaga = null)
{
    // gunakan range bulan: [start, end) supaya semua potongan dalam bulan itu ter-cover
    $bind  = [$nik, $endTs, $startTs];
    $extra = '';
    if (!empty($id_lembaga)) { $extra = ' AND pg.id_lembaga = ? '; $bind[] = $id_lembaga; }

    $sql = "
        SELECT nama, SUM(nominal) AS nominal
        FROM (
            SELECT p.nama_potongan AS nama, pp.nominal_potongan AS nominal
            FROM potongan_pengajar pp
            JOIN potongan p  ON p.id_potongan = pp.jenis_potongan
            JOIN pengajar pg ON pg.id_pengajar = pp.id_pengajar
            WHERE pg.nik = ?
              -- periode overlap dengan bulan yg dipilih:
              AND (pp.min_periode_potongan IS NULL OR pp.min_periode_potongan <  ?)
              AND (pp.max_periode_potongan IS NULL OR pp.max_periode_potongan >= ?)
              {$extra}
        ) t
        GROUP BY nama
        ORDER BY nama ASC
    ";

    return $this->db->query($sql, $bind)->result_array();
}

// === POTONGAN: STRUKTURAL (tabel potongan_umana) ===
public function list_potongan_struktural_by_month($nik, $startTs, $endTs, $id_lembaga = null)
{
    $bind  = [$nik, $endTs, $startTs];
    $extra = '';
    if (!empty($id_lembaga)) { $extra = ' AND pen.id_lembaga = ? '; $bind[] = $id_lembaga; }

    $sql = "
        SELECT nama, SUM(nominal) AS nominal
        FROM (
            SELECT p.nama_potongan AS nama, pu.nominal_potongan AS nominal
            FROM potongan_umana pu
            JOIN potongan p    ON p.id_potongan = pu.jenis_potongan
            JOIN penempatan pen ON pen.id_penempatan = pu.id_penempatan
            WHERE pen.nik = ?
              -- periode overlap dengan bulan yg dipilih:
              AND (pu.min_periode_potongan IS NULL OR pu.min_periode_potongan <  ?)
              AND (pu.max_periode_potongan IS NULL OR pu.max_periode_potongan >= ?)
              {$extra}
        ) t
        GROUP BY nama
        ORDER BY nama ASC
    ";

    return $this->db->query($sql, $bind)->result_array();
}



	
}
