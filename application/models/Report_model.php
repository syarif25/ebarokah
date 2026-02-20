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
        // Policy change starting 2026: Use calendar year format (Jan-Dec)
        // Before 2026: Use academic year format (Jul-Jun)
        if ((int)$tahun >= 2026) {
            // Calendar year format for 2026 onwards (e.g., "2026", "2027")
            return (string)$tahun;
        }
        
        // Academic year format for years before 2026 (e.g., "2025/2026")
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
                MIN(tb.tahun)        AS label_tahun,
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
                MIN(tbp.tahun)         AS label_tahun,
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
                MIN(tbs.tahun)         AS label_tahun,
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
                tb.bulan AS label_bulan, tb.tahun AS label_tahun,
                l.nama_lembaga AS lembaga, 'Struktural' AS jenis, tb.diterima AS nominal
            FROM total_barokah tb
            JOIN penempatan p ON p.id_penempatan = tb.id_penempatan
            JOIN lembaga l    ON l.id_lembaga    = p.id_lembaga
            WHERE p.nik = ? AND tb.`timestamp` >= ? AND tb.`timestamp` < ?

            UNION ALL

            -- Pengajar
            SELECT YEAR(tbp.`timestamp`) AS y, MONTH(tbp.`timestamp`) AS m,
                tbp.bulan AS label_bulan, tbp.tahun AS label_tahun,
                l.nama_lembaga AS lembaga, 'Pengajar' AS jenis, tbp.diterima AS nominal
            FROM total_barokah_pengajar tbp
            JOIN pengajar pg ON pg.id_pengajar = tbp.id_pengajar
            JOIN lembaga l   ON l.id_lembaga   = pg.id_lembaga
            WHERE pg.nik = ? AND tbp.`timestamp` >= ? AND tbp.`timestamp` < ?

            UNION ALL

            -- Satpam (lewat kehadiran_lembaga untuk nama lembaga)
            SELECT YEAR(tbs.`tgl_input`) AS y, MONTH(tbs.`tgl_input`) AS m,
                tbs.bulan AS label_bulan, tbs.tahun AS label_tahun,
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
                        MAX(t.bulan)         AS label_bulan,
                        CASE 
                            WHEN LENGTH(MAX(t.tahun)) = 4 THEN MAX(t.tahun)
                            WHEN MAX(t.bulan) IN ('Januari','Februari','Maret','April','Mei','Juni') 
                            THEN SUBSTRING(MAX(t.tahun), 6, 4)
                            ELSE SUBSTRING(MAX(t.tahun), 1, 4)
                        END AS label_tahun,
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
                        MAX(t.bulan)         AS label_bulan,
                        CASE 
                            WHEN LENGTH(MAX(t.tahun)) = 4 THEN MAX(t.tahun)
                            WHEN MAX(t.bulan) IN ('Januari','Februari','Maret','April','Mei','Juni') 
                            THEN SUBSTRING(MAX(t.tahun), 6, 4)
                            ELSE SUBSTRING(MAX(t.tahun), 1, 4)
                        END AS label_tahun,
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
                        MAX(t.bulan)         AS label_bulan,
                        CASE 
                            WHEN LENGTH(MAX(t.tahun)) = 4 THEN MAX(t.tahun)
                            WHEN MAX(t.bulan) IN ('Januari','Februari','Maret','April','Mei','Juni') 
                            THEN SUBSTRING(MAX(t.tahun), 6, 4)
                            ELSE SUBSTRING(MAX(t.tahun), 1, 4)
                        END AS label_tahun,
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
                    SELECT CONCAT(l.nama_lembaga, ' (', t.bulan, ' ', t.tahun, ')') AS lembaga,
                        'Pengajar' AS jenis, SUM(t.diterima) AS nominal,
                        CONCAT(pg.id_lembaga, '-', month(str_to_date(concat(t.bulan,' 1 2000'),'%M %d %Y')), '-', substring(t.tahun, 1, 4)) AS ent_id
                    FROM total_barokah_pengajar t
                    JOIN pengajar pg ON pg.id_pengajar = t.id_pengajar
                    JOIN lembaga  l  ON l.id_lembaga  = pg.id_lembaga
                    WHERE pg.nik = ? AND t.`timestamp` >= ? AND t.`timestamp` < ?
                    GROUP BY l.nama_lembaga, t.bulan, t.tahun, pg.id_lembaga
                    ORDER BY l.nama_lembaga
                ";
                // Note: ent_id construction is a bit hacky to reverse engineer Month ID and Year. 
                // Easier: just pass t.bulan and t.tahun string in ID? 
                // The parser expects int-int. Let's send raw IDs and let breakdown handle lookup or just valid ID.
                // Actually, the parser expects 2025. DB has 2024/2025. 
                // Let's rely on standard grouping. 
                // Simple fix: ent_id = id_lembaga. breakdown will sum all.
                // BETTER FIX: ent_id = id_lembaga. The user asked for "Barokah Pokok 1.2jt" -> implying sum.
                // User complaint: "masak hadir sampai 58 kali". 
                // IF we separate them in list, then "ent_id" must carry the specific month to filter breakdown.
                // Let's retry the SQL with a robust ent_id.
                
                // Since I can't easily map "September" back to "9" in SQL easily without case, 
                // I will use a simpler approach: Just grouping by ID and Month/Year string in SQL.
                // But the breakdown expects integer M and Y.
                // I will update detail_bulanan to just return the rows.
                // I'll skip complex ent_id generation in SQL and do it in PHP? No, model returns array.
                // Let's use `elt(field(bulan,'Januari',...), 1, ...)` or similar? No, too complex.
                
                // Revised Approach for this method: 
                // Just use the strings in ent_id? "4-September-2025/2026". 
                // Then parser in controller needs to handle that. 
                // Controller parser: `(int)$parts[1]` casts "September" to 0. Bad.
                
                // OK, I'll fallback to: NOT separating them in list ID, but separating in List Display?
                // No, then click opens both.
                
                // Let's make `_nama_bulan_id` accessible or replicate logic? 
                // Replicate logic in SQL is hard.
                
                // Wait! I can use `MONTH(str_to_date(t.bulan, '%M'))` if locale is English. But it is Indonesian.
                // I will stick to returning the raw row, and let Controller formatting logic handle the display/ID generation? 
                // But `detail_bulanan_by_kategori` is called by `barokah_detail` which returns JSON directly.
                
                // Okay, I will try to map manually in SQL for the `ent_id` if possible, OR just modify the Controller `barokah_detail` 
                // to iterate the results and build the `ent_id` there before sending JSON. 
                // YES, modifying `Report.php::barokah_detail` is safer for logic.
                // So here I will just return the raw fields needed.
                
                $sql = "
                    SELECT l.nama_lembaga, t.bulan, t.tahun,
                        'Pengajar' AS jenis, SUM(t.diterima) AS nominal,
                        pg.id_lembaga
                    FROM total_barokah_pengajar t
                    JOIN pengajar pg ON pg.id_pengajar = t.id_pengajar
                    JOIN lembaga  l  ON l.id_lembaga  = pg.id_lembaga
                    WHERE pg.nik = ? AND t.`timestamp` >= ? AND t.`timestamp` < ?
                    GROUP BY l.nama_lembaga, t.bulan, t.tahun, pg.id_lembaga
                    ORDER BY l.nama_lembaga DESC
                ";
                $bind = [$nik, $start, $end];
                break;

            case 'satpam':
                $sql = "
                    SELECT COALESCE(l.nama_lembaga,'(Satpam)') AS nama_lembaga, t.bulan, t.tahun,
                        'Satpam' AS jenis,
                        SUM(t.diterima) AS nominal,
                        kl.id_lembaga
                    FROM total_barokah_satpam t
                    JOIN satpam sp ON sp.id_satpam = t.id_satpam
                    LEFT JOIN kehadiran_lembaga kl ON kl.id_kehadiran_lembaga = t.id_kehadiran_lembaga
                    LEFT JOIN lembaga l ON l.id_lembaga = kl.id_lembaga
                    WHERE sp.nik = ? AND t.`tgl_input` >= ? AND t.`tgl_input` < ?
                    GROUP BY nama_lembaga, t.bulan, t.tahun, kl.id_lembaga
                    ORDER BY nama_lembaga DESC
                ";
                $bind = [$nik, $start, $end];
                break;

            default: // struktural
                $sql = "
                    SELECT l.nama_lembaga, t.bulan, t.tahun,
                         'Struktural' AS jenis, SUM(t.diterima) AS nominal,
                        p.id_lembaga
                    FROM total_barokah t
                    JOIN penempatan p ON p.id_penempatan = t.id_penempatan
                    JOIN lembaga   l ON l.id_lembaga    = p.id_lembaga
                    WHERE p.nik = ? AND t.`timestamp` >= ? AND t.`timestamp` < ?
                    GROUP BY l.nama_lembaga, t.bulan, t.tahun, p.id_lembaga
                    ORDER BY l.nama_lembaga DESC
                ";
                $bind = [$nik, $start, $end];
        }

        $res = $this->db->query($sql, $bind)->result_array();
        
        // Post-processing to match expected JSON structure and build ID
        // Map bulan name to integer
        $mapBulan = ['Januari'=>1,'Februari'=>2,'Maret'=>3,'April'=>4,'Mei'=>5,'Juni'=>6,'Juli'=>7,'Agustus'=>8,'September'=>9,'Oktober'=>10,'November'=>11,'Desember'=>12];
        
        return array_map(function($r) use ($mapBulan) {
             // Parse Year from "2024/2025" -> take first part? Or second? 
             // _tahun_ajaran logic: July->Dec uses year/(year+1). Jan->Jun uses (year-1)/year.
             // But for ID, we just need something we can reconstruct.
             // Actually, `barokah_breakdown` parser I wrote expects "id-m-y". 
             // I should pass the `y` that generated this `tahun_ajaran`? 
             // `_tahun_ajaran` is ambiguous (2025/2026 can be generated by 2025 or 2026).
             // Let's pass the raw `tahun` string? No breakdown parser expects int.
             // Actually, I can update the breakdown parser to handle the slash! 
             // BUT, `breakdown_struktural` needs `_tahun_ajaran` compatible inputs.
             // Ideally I pass the exact strings `bulan` and `tahun` to the breakdown query manually.
             // Let's try to pass integer M and simple integer Y (e.g. 2025). 
             // If I assume Y is the start year... 
             // Let's do this: 
             $mInt = $mapBulan[$r['bulan']] ?? 0;
             $yInt = (int)substr($r['tahun'], 0, 4); 
             if ($mInt < 7) $yInt++; // If Jan-Jun, it's the second year part of "2024/2025" usually? 
             // Wait: 2024/2025. July 2024. Jan 2025.
             // If row says "Januari" "2024/2025", that means Jan 2025. 
             // So if m < 7, year is part 2. if m >= 7, year is part 1.
             
             // Check string "2024/2025"
             $partsY = explode('/', $r['tahun']);
             $yReal = (isset($partsY[1]) && $mInt < 7) ? (int)$partsY[1] : (int)$partsY[0];

             return [
                 'lembaga' => $r['nama_lembaga'] . ' (' . $r['bulan'] . ' ' . $r['tahun'] . ')',
                 'jenis'   => $r['jenis'],
                 'nominal' => $r['nominal'],
                 'ent_id'  => ($r['id_lembaga']??0) . '-' . $mInt . '-' . $yReal
             ];
        }, $res);
    }

    public function breakdown_pengajar_by_month($nik, $startTs, $endTs, $entId = null, $specM=null, $specY=null)
    {
        $whereL = '';
        $bind   = [$nik, $startTs, $endTs];

        if (!empty($entId)) {
            $whereL .= ' AND pg.id_lembaga = ? ';
            $bind[] = $entId;
        }
        if (!empty($specM) && !empty($specY)) {
             $nmBulan = $this->_nama_bulan_id($specM);
             $nmTahun = $this->_tahun_ajaran($specM, $specY);
             $whereL .= ' AND tbp.bulan = ? AND tbp.tahun = ? ';
             $bind[] = $nmBulan;
             $bind[] = $nmTahun;
        }

        $sql = "
            SELECT
                COALESCE(SUM(tbp.jumlah_sks),0)        AS sks,
                COALESCE(SUM(tbp.mengajar),0)          AS mengajar,
                COALESCE(SUM(tbp.dty),0)               AS gty_dty,
                COALESCE(SUM(tbp.jafung),0)            AS jafung,
                COALESCE(SUM(tbp.nominal_kehadiran),0) AS kehadiran,
                COALESCE(SUM(tbp.jumlah_hadir),0)      AS jhadir,
                COALESCE(SUM(tbp.jumlah_hadir_15),0)   AS jhadir15,
                COALESCE(SUM(tbp.nominal_hadir_15),0)  AS nhadir15,
                COALESCE(SUM(tbp.jumlah_hadir_10),0)   AS jhadir10,
                COALESCE(SUM(tbp.nominal_hadir_10),0)  AS nhadir10,
                COALESCE(SUM(tbp.walkes),0)            AS walkes,
                COALESCE(SUM(tbp.kehormatan),0)        AS kehormatan,
                COALESCE(SUM(tbp.khusus),0)            AS tambahan,
                COALESCE(SUM(tbp.barokah_piket),0)     AS piket,
                COALESCE(SUM(tbp.tun_anak),0)          AS tun_anak,
                COALESCE(SUM(tbp.tunkel),0)            AS tunkel,
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


    public function breakdown_struktural_by_month($nik, $startTs, $endTs, $id_lembaga = null, $specM=null, $specY=null)
    {
        // --- Komponen nominal dari total_barokah ---
        $bind  = [$nik, $startTs, $endTs];
        $extra = '';
        if (!empty($id_lembaga)) { $extra .= ' AND p.id_lembaga = ? '; $bind[] = $id_lembaga; }
        if (!empty($specM) && !empty($specY)) {
             $nmBulan = $this->_nama_bulan_id($specM);
             $nmTahun = $this->_tahun_ajaran($specM, $specY);
             $extra .= ' AND t.bulan = ? AND t.tahun = ? ';
             $bind[] = $nmBulan;
             $bind[] = $nmTahun;
        }

        $sql = "
            SELECT
                COALESCE(SUM(t.tunjab),0)   AS barokah_pokok,     -- ditampilkan sebagai 'Barokah Pokok'
                COALESCE(SUM(t.nominal_kehadiran),0) AS kehadiran_nominal, -- 'Kehadiran' (rupiah)
                COALESCE(SUM(t.kehadiran),0)         AS kehadiran,              -- Total diterima
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
        // Warning: If specM/Y is used, we should also filter kehadiran? 
        // Kehadiran table usually has tgl_input or similar. It might not have 'bulan/tahun' columns explicitly matched to payload.
        // But `detail_bulanan` logic relies on `total_barokah` table which has the aggregate.
        // `kehadiran` table is raw daily data.
        // If we want "Hadir Normal X Kali", we need to query `kehadiran` for the specific month.
        // `kehadiran` table structure: tgl_input.
        // If we use specM/specY, we should construct a date range for THAT month to query `kehadiran`.
        // $startH = "$specY-$specM-01"; $endH = ...
        
        $bindH  = [$nik];
        $extraH = '';
        if (!empty($id_lembaga)) { $extraH .= ' AND p.id_lembaga = ? '; $bindH[] = $id_lembaga; }
        
        // Determine window for Kehadiran count
        if (!empty($specM) && !empty($specY)) {
             $s = (new DateTime("$specY-$specM-01"))->format('Y-m-d H:i:s');
             $e = (new DateTime("$specY-$specM-01"))->modify('+1 month')->format('Y-m-d H:i:s');
             $bindH[] = $s; 
             $bindH[] = $e;
        } else {
             $bindH[] = $startTs;
             $bindH[] = $endTs;
        }

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


    // Report_model.php
    public function breakdown_satpam_by_month($nik, $startTs, $endTs, $id_lembaga = null, $specM=null, $specY=null)
    {
        $whereL = '';
        $bind   = [$nik, $startTs, $endTs];
        if (!empty($id_lembaga)) { $whereL .= ' AND kl.id_lembaga = ? '; $bind[] = $id_lembaga; }
        if (!empty($specM) && !empty($specY)) {
             $nmBulan = $this->_nama_bulan_id($specM);
             $nmTahun = $this->_tahun_ajaran($specM, $specY);
             $whereL .= ' AND t.bulan = ? AND t.tahun = ? ';
             $bind[] = $nmBulan;
             $bind[] = $nmTahun;
        }

        $sql = "
            SELECT
            -- Komponen 1: Kehadiran 1 (hari × nominal_transport)
            COALESCE(SUM(t.jumlah_hari),0)                                 AS j_hari,
            COALESCE(SUM(t.nominal_transport * t.jumlah_hari),0)           AS k1_total,
            CASE WHEN COALESCE(SUM(t.jumlah_hari),0)=0
                THEN 0
                ELSE ROUND(SUM(t.nominal_transport * t.jumlah_hari)
                            / NULLIF(SUM(t.jumlah_hari),0))
            END                                                            AS k1_unit,
 
            -- Komponen 2: Kehadiran 2 (shift × rank)
            COALESCE(SUM(t.jumlah_shift),0)                                AS j_shift,
            COALESCE(SUM(t.rank * t.jumlah_shift),0)                       AS k2_total,
            CASE WHEN COALESCE(SUM(t.jumlah_shift),0)=0
                THEN 0
                ELSE ROUND(SUM(t.rank * t.jumlah_shift)
                            / NULLIF(SUM(t.jumlah_shift),0))
            END                                                            AS k2_unit,
 
            -- Komponen 3: Kehadiran 3 (dinihari × konsumsi)
            COALESCE(SUM(t.jumlah_dinihari),0)                             AS j_dinihari,
            COALESCE(SUM(t.konsumsi * t.jumlah_dinihari),0)                AS k3_total,
            CASE WHEN COALESCE(SUM(t.jumlah_dinihari),0)=0
                THEN 0
                ELSE ROUND(SUM(t.konsumsi * t.jumlah_dinihari)
                            / NULLIF(SUM(t.jumlah_dinihari),0))
            END                                                            AS k3_unit,
 
            -- Grand total (sesuai kolom diterima)
            COALESCE(SUM(t.diterima),0)                                    AS total
            FROM total_barokah_satpam t
            JOIN satpam sp ON sp.id_satpam = t.id_satpam
            LEFT JOIN kehadiran_lembaga kl ON kl.id_kehadiran_lembaga = t.id_kehadiran_lembaga
            WHERE sp.nik = ?
            AND t.`tgl_input` >= ?
            AND t.`tgl_input` < ?
            {$whereL}
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
