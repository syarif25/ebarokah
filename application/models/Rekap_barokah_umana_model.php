<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Rekap_barokah_umana_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Ambil data rekap total barokah per Umana untuk periode tertentu.
     *
     * Pendekatan: agregasi per NIK di level subquery terlebih dahulu
     * untuk menghindari Cartesian Product / double counting pada Umana
     * yang memiliki lebih dari satu penempatan struktural atau pengajar.
     *
     * Filter status payroll: hanya status = 'selesai'.
     * Filter penempatan aktif:
     *   - penempatan: status != 'Tidak Akti' (typo di database)
     *   - pengajar   : status = 'Aktif'
     *
     * @param  string $bulan  Nama bulan, contoh: "November"
     * @param  string $tahun  Tahun acuan, contoh: "2024/2025"
     * @return array
     */
    public function get_rekap($bulan, $tahun)
    {
        $bulan = $this->db->escape_str($bulan);
        $tahun = $this->db->escape_str($tahun);

        $sql = "
            SELECT
                u.nik,
                TRIM(CONCAT_WS(' ',
                    NULLIF(TRIM(u.gelar_depan), ''),
                    NULLIF(TRIM(u.nama_lengkap), ''),
                    NULLIF(TRIM(u.gelar_belakang), '')
                )) AS nama_lengkap_lengkap,
                COALESCE(str.barokah_struktural, 0)  AS barokah_struktural,
                COALESCE(pgj.barokah_mengajar, 0)    AS barokah_mengajar,
                (
                    COALESCE(str.barokah_struktural, 0)
                  + COALESCE(pgj.barokah_mengajar, 0)
                )                                     AS total_barokah,
                CONCAT_WS(', ',
                    NULLIF(ket_str.keterangan_struktural, ''),
                    NULLIF(ket_pgj.keterangan_pengajar,   '')
                )                                     AS keterangan

            FROM umana u

            -- === Sub-query 1: Barokah Struktural per NIK (hanya status selesai) ===
            LEFT JOIN (
                SELECT
                    p.nik,
                    SUM(tb.diterima) AS barokah_struktural
                FROM total_barokah tb
                JOIN penempatan p ON p.id_penempatan = tb.id_penempatan
                WHERE tb.bulan  = '$bulan'
                  AND tb.tahun  = '$tahun'
                  AND tb.status = 'selesai'
                GROUP BY p.nik
            ) str ON str.nik = u.nik

            -- === Sub-query 2: Barokah Pengajar per NIK (hanya status selesai) ===
            LEFT JOIN (
                SELECT
                    pg.nik,
                    SUM(tbp.diterima) AS barokah_mengajar
                FROM total_barokah_pengajar tbp
                JOIN pengajar pg ON pg.id_pengajar = tbp.id_pengajar
                WHERE tbp.bulan  = '$bulan'
                  AND tbp.tahun  = '$tahun'
                  AND tbp.status = 'selesai'
                GROUP BY pg.nik
            ) pgj ON pgj.nik = u.nik

            -- === Sub-query 3: Keterangan jabatan struktural aktif per NIK ===
            -- Catatan: nilai tidak aktif di tabel penempatan adalah 'Tidak Akti' (typo di DB)
            LEFT JOIN (
                SELECT
                    p.nik,
                    GROUP_CONCAT(
                        DISTINCT CONCAT(l.nama_lembaga, ' (', p.jabatan_lembaga, ')')
                        ORDER BY l.nama_lembaga ASC
                        SEPARATOR ', '
                    ) AS keterangan_struktural
                FROM penempatan p
                JOIN lembaga l ON l.id_lembaga = p.id_lembaga
                WHERE p.status != 'Tidak Akti'
                GROUP BY p.nik
            ) ket_str ON ket_str.nik = u.nik

            -- === Sub-query 4: Keterangan lembaga pengajar aktif per NIK ===
            LEFT JOIN (
                SELECT
                    pg.nik,
                    GROUP_CONCAT(
                        DISTINCT CONCAT(l.nama_lembaga, ' (', pg.kategori, ')')
                        ORDER BY l.nama_lembaga ASC
                        SEPARATOR ', '
                    ) AS keterangan_pengajar
                FROM pengajar pg
                JOIN lembaga l ON l.id_lembaga = pg.id_lembaga
                WHERE pg.status = 'Aktif'
                GROUP BY pg.nik
            ) ket_pgj ON ket_pgj.nik = u.nik

            -- Hanya tampilkan Umana yang memiliki data barokah pada periode ini
            WHERE str.nik IS NOT NULL
               OR pgj.nik  IS NOT NULL

            ORDER BY u.nama_lengkap ASC
        ";

        return $this->db->query($sql)->result();
    }

    /**
     * Ambil daftar periode unik (bulan + tahun) dari kedua tabel payroll.
     * Digunakan untuk mengisi dropdown filter periode di view berdasarkan timestamp.
     *
     * @return array
     */
    public function get_periode()
    {
        $sql = "
            SELECT DISTINCT
                MONTH(timestamp) AS num_bulan,
                YEAR(timestamp)  AS tahun
            FROM (
                SELECT timestamp FROM total_barokah         WHERE timestamp IS NOT NULL
                UNION
                SELECT timestamp FROM total_barokah_pengajar WHERE timestamp IS NOT NULL
            ) AS periode
            ORDER BY tahun ASC, num_bulan ASC
        ";

        $rows = $this->db->query($sql)->result();

        $bulan_indo = [
            1 => 'Januari', 2 => 'Februari',  3 => 'Maret',    4 => 'April',
            5 => 'Mei',     6 => 'Juni',       7 => 'Juli',     8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        foreach ($rows as &$row) {
            $row->nama_bulan = $bulan_indo[(int)$row->num_bulan] ?? $row->num_bulan;
        }

        return $rows;
    }

    /**
     * Ambil data rekap struktural khusus untuk umana yang berafiliasi dengan Bendahara.
     */
    public function get_lembaga()
    {
        $this->db->select('id_lembaga, nama_lembaga');
        $this->db->from('lembaga');
        $this->db->order_by('nama_lembaga', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Ambil array NIK unik dari lembaga yang difilter (Struktural + Mengajar)
     */
    public function get_nik_by_lembaga($id_lembaga, $periode_filter)
    {
        $id_lembaga = $this->db->escape_str($id_lembaga);
        
        $sql = "
            SELECT nik FROM penempatan WHERE id_lembaga = '$id_lembaga' AND (tgl_selesai IS NULL OR tgl_selesai >= '$periode_filter')
            UNION
            SELECT nik FROM pengajar WHERE id_lembaga = '$id_lembaga' AND (tgl_selesai IS NULL OR tgl_selesai >= '$periode_filter')
        ";
        
        $result = $this->db->query($sql)->result_array();
        
        $array_nik = [];
        foreach ($result as $row) {
            $array_nik[] = $row['nik'];
        }
        
        return array_unique($array_nik);
    }

    public function get_rekap_struktural($bulan, $tahun, $array_nik = [])
    {
        $bulan = $this->db->escape_str($bulan);
        $tahun = $this->db->escape_str($tahun);

        // Normalisasi input bulan dari Frontend
        $map_angka_ke_teks = [
            '1' => 'Januari', '01' => 'Januari', '2' => 'Februari', '02' => 'Februari',
            '3' => 'Maret', '03' => 'Maret', '4' => 'April', '04' => 'April',
            '5' => 'Mei', '05' => 'Mei', '6' => 'Juni', '06' => 'Juni',
            '7' => 'Juli', '07' => 'Juli', '8' => 'Agustus', '08' => 'Agustus',
            '9' => 'September', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        $map_teks_ke_angka = [
            'Januari' => '01', 'Februari' => '02', 'Maret' => '03', 'April' => '04',
            'Mei' => '05', 'Juni' => '06', 'Juli' => '07', 'Agustus' => '08',
            'September' => '09', 'Oktober' => '10', 'November' => '11', 'Desember' => '12'
        ];

        // Jika input berupa angka, ubah ke teks. Jika sudah teks, biarkan.
        $bulan_teks = isset($map_angka_ke_teks[$bulan]) ? $map_angka_ke_teks[$bulan] : $bulan;
        // Ambil format angka 2 digit untuk filter DATE
        $bulan_angka = isset($map_teks_ke_angka[$bulan_teks]) ? $map_teks_ke_angka[$bulan_teks] : '01';

        // Pecah tahun akademik (misal "2024/2025") agar format DATE valid
        $tahun_date = $tahun;
        if (strpos($tahun, '/') !== false) {
            $tahun_arr = explode('/', $tahun);
            $tahun_date = $tahun_arr[0];
        }
        $periode_filter = $tahun_date . '-' . $bulan_angka . '-01';

        $this->db->select('
            penempatan.nik,
            umana.nama_lengkap,
            SUM(total_barokah.diterima) AS nominal_struktural,
            GROUP_CONCAT(DISTINCT lembaga.nama_lembaga SEPARATOR ", ") AS keterangan_lembaga,
            MIN(total_barokah.id_total_barokah) AS min_id_total_barokah
        ');
        $this->db->from('penempatan');

        // JOIN menggunakan kolom varchar bulan dan tahun bawaan tabel
        $join_condition = "total_barokah.id_penempatan = penempatan.id_penempatan "
                        . "AND total_barokah.bulan = '$bulan_teks' "
                        . "AND total_barokah.tahun = '$tahun' "
                        . "AND total_barokah.status IN ('selesai', 'Sukses')";
        log_message('error', 'JOIN CONDITION STRUKTURAL: ' . $join_condition);
        $this->db->join('total_barokah', $join_condition, 'left');

        $this->db->join('umana', 'umana.nik = penempatan.nik', 'left');
        $this->db->join('lembaga', 'lembaga.id_lembaga = penempatan.id_lembaga', 'left');

        // Filter kedaluwarsa jabatan (jabatan yang masih aktif di bulan tersebut)
        $this->db->where("(penempatan.tgl_selesai IS NULL OR penempatan.tgl_selesai >= '$periode_filter')", NULL, FALSE);

        if (!empty($array_nik)) {
            $this->db->where_in('penempatan.nik', $array_nik);
        }

        $this->db->group_by('penempatan.nik');

        // Hanya tampilkan Umana yang memang memiliki barokah di periode tersebut
        $this->db->having('nominal_struktural IS NOT NULL');

        $this->db->order_by('min_id_total_barokah', 'ASC');

        $result = $this->db->get()->result();

        log_message('error', 'LAST QUERY STRUKTURAL: ' . $this->db->last_query());

        return $result;
    }

    public function get_rekap_mengajar($bulan, $tahun, $array_nik = [])
    {
        $bulan = $this->db->escape_str($bulan);
        $tahun = $this->db->escape_str($tahun);

        // Normalisasi input bulan dari Frontend
        $map_angka_ke_teks = [
            '1' => 'Januari', '01' => 'Januari', '2' => 'Februari', '02' => 'Februari',
            '3' => 'Maret', '03' => 'Maret', '4' => 'April', '04' => 'April',
            '5' => 'Mei', '05' => 'Mei', '6' => 'Juni', '06' => 'Juni',
            '7' => 'Juli', '07' => 'Juli', '8' => 'Agustus', '08' => 'Agustus',
            '9' => 'September', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        $map_teks_ke_angka = [
            'Januari' => '01', 'Februari' => '02', 'Maret' => '03', 'April' => '04',
            'Mei' => '05', 'Juni' => '06', 'Juli' => '07', 'Agustus' => '08',
            'September' => '09', 'Oktober' => '10', 'November' => '11', 'Desember' => '12'
        ];

        // Jika input berupa angka, ubah ke teks. Jika sudah teks, biarkan.
        $bulan_teks = isset($map_angka_ke_teks[$bulan]) ? $map_angka_ke_teks[$bulan] : $bulan;
        // Ambil format angka 2 digit untuk filter DATE
        $bulan_angka = isset($map_teks_ke_angka[$bulan_teks]) ? $map_teks_ke_angka[$bulan_teks] : '01';
        
        $tahun_date = $tahun;
        if (strpos($tahun, '/') !== false) {
            $tahun_arr = explode('/', $tahun);
            $tahun_date = trim($tahun_arr[0]);
        }
        $periode_filter = $tahun_date . '-' . $bulan_angka . '-01';

        // 3. Main Query (Kalkulasi Nominal & Keterangan)
        $this->db->select('
            pengajar.nik,
            umana.nama_lengkap,
            SUM(total_barokah_pengajar.diterima) AS nominal_mengajar,
            GROUP_CONCAT(DISTINCT lembaga.nama_lembaga SEPARATOR ", ") AS keterangan_mengajar,
            MIN(total_barokah_pengajar.id_total_barokah_pengajar) AS min_id_total_barokah
        ');
        $this->db->from('pengajar');
        
        // Filter menggunakan kolom varchar
        $join_cond = "total_barokah_pengajar.id_pengajar = pengajar.id_pengajar "
                   . "AND total_barokah_pengajar.bulan = '$bulan_teks' "
                   . "AND TRIM(REPLACE(total_barokah_pengajar.tahun, 'Tahun ', '')) = '$tahun'";
        $this->db->join('total_barokah_pengajar', $join_cond, 'left');
        $this->db->join('lembaga', 'lembaga.id_lembaga = pengajar.id_lembaga', 'left');
        $this->db->join('umana', 'umana.nik = pengajar.nik', 'left');

        $this->db->where("(pengajar.tgl_selesai IS NULL OR pengajar.tgl_selesai >= '$periode_filter')", NULL, FALSE);
        
        if (!empty($array_nik)) {
            $this->db->where_in('pengajar.nik', $array_nik);
        }
        
        $this->db->group_by('pengajar.nik');
        $this->db->having('nominal_mengajar IS NOT NULL');

        $query_result = $this->db->get()->result_array();

        // 4. Format Return dengan NIK sebagai Key
        $result = [];
        foreach ($query_result as $row) {
            $result[$row['nik']] = [
                'nominal_mengajar' => $row['nominal_mengajar'],
                'keterangan_mengajar' => $row['keterangan_mengajar'],
                'nama_lengkap' => $row['nama_lengkap'],
                'min_id_total_barokah' => $row['min_id_total_barokah']
            ];
        }

        return $result;
    }

    public function get_detail_struktural_umana($nik, $bulan, $tahun)
    {
        $nik = $this->db->escape_str($nik);
        $bulan = $this->db->escape_str($bulan);
        $tahun = $this->db->escape_str($tahun);

        $map_angka_ke_teks = [
            '1' => 'Januari', '01' => 'Januari', '2' => 'Februari', '02' => 'Februari',
            '3' => 'Maret', '03' => 'Maret', '4' => 'April', '04' => 'April',
            '5' => 'Mei', '05' => 'Mei', '6' => 'Juni', '06' => 'Juni',
            '7' => 'Juli', '07' => 'Juli', '8' => 'Agustus', '08' => 'Agustus',
            '9' => 'September', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        $bulan_teks = isset($map_angka_ke_teks[$bulan]) ? $map_angka_ke_teks[$bulan] : $bulan;

        $this->db->select('lembaga.nama_lembaga, total_barokah.diterima AS nominal_struktural');
        $this->db->from('penempatan');
        $join_condition = "total_barokah.id_penempatan = penempatan.id_penempatan "
                        . "AND total_barokah.bulan = '$bulan_teks' "
                        . "AND total_barokah.tahun = '$tahun' "
                        . "AND total_barokah.status IN ('selesai', 'Sukses')";
        $this->db->join('total_barokah', $join_condition, 'inner');
        $this->db->join('lembaga', 'lembaga.id_lembaga = penempatan.id_lembaga', 'left');
        $this->db->where('penempatan.nik', $nik);
        $this->db->order_by('total_barokah.diterima', 'DESC');
        
        return $this->db->get()->result_array();
    }

    public function get_detail_mengajar_umana($nik, $bulan, $tahun)
    {
        $nik = $this->db->escape_str($nik);
        $bulan = $this->db->escape_str($bulan);
        $tahun = $this->db->escape_str($tahun);

        $map_angka_ke_teks = [
            '1' => 'Januari', '01' => 'Januari', '2' => 'Februari', '02' => 'Februari',
            '3' => 'Maret', '03' => 'Maret', '4' => 'April', '04' => 'April',
            '5' => 'Mei', '05' => 'Mei', '6' => 'Juni', '06' => 'Juni',
            '7' => 'Juli', '07' => 'Juli', '8' => 'Agustus', '08' => 'Agustus',
            '9' => 'September', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        $bulan_teks = isset($map_angka_ke_teks[$bulan]) ? $map_angka_ke_teks[$bulan] : $bulan;

        $this->db->select('lembaga.nama_lembaga, total_barokah_pengajar.diterima');
        $this->db->from('pengajar');
        $join_cond = "total_barokah_pengajar.id_pengajar = pengajar.id_pengajar "
                   . "AND total_barokah_pengajar.bulan = '$bulan_teks' "
                   . "AND TRIM(REPLACE(total_barokah_pengajar.tahun, 'Tahun ', '')) = '$tahun'";
        $this->db->join('total_barokah_pengajar', $join_cond, 'inner');
        $this->db->join('lembaga', 'lembaga.id_lembaga = pengajar.id_lembaga', 'left');
        $this->db->where('pengajar.nik', $nik);
        $this->db->order_by('total_barokah_pengajar.diterima', 'DESC');
        
        return $this->db->get()->result_array();
    }
}
