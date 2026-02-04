<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_struktural_model extends CI_Model {

    var $column_order = array(
        null, // 0. No
        'tb.bulan', // 1. Bulan
        'tb.tahun', // 2. Tahun
        'u.nik', // 3. NIK
        'u.nama_lengkap', // 4. Nama
        'l.nama_lembaga', // 5. Lembaga
        'p.jabatan_lembaga', // 6. Jabatan
        'tb.tunjab', // 7. Tunjab
        'tb.mp', // 8. MP
        'tb.kehadiran', // 9. Kehadiran
        'tb.nominal_kehadiran', // 10. Nominal
        'tb.tunkel', // 11. Tunkel
        'tb.tunj_anak', // 12. Tunj Anak
        'tb.tmp', // 13. TMP
        'tb.kehormatan', // 14. Kehormatan
        'tb.tbk', // 15. TBK
        'tb.potongan', // 16. Potongan
        'tb.diterima', // 17. Diterima
        'wl.created_at' // 18. Tgl Kirim
    );
    var $column_search = array('u.nik', 'u.nama_lengkap', 'l.nama_lembaga', 'p.jabatan_lembaga');
    var $order = array('u.nama_lengkap' => 'asc');

    // ... (kode lainnya jika ada) ...

    // DataTables functions
    private function _get_datatables_query($bulan, $tahun, $lembaga)
    {
        $this->db->select('
            tb.*,
            u.nik,
            u.nama_lengkap,
            l.nama_lembaga,
            p.jabatan_lembaga,
            wl.status as wa_status,
            wl.created_at as tgl_kirim
        ');
        $this->db->from('total_barokah tb');
        // JOIN WAJIB: total_barokah -> penempatan (karena nik & id_lembaga ada di penempatan)
        $this->db->join('penempatan p', 'tb.id_penempatan = p.id_penempatan'); 
        // JOIN umana via penempatan
        $this->db->join('umana u', 'p.nik = u.nik');
        // JOIN lembaga via penempatan
        $this->db->join('lembaga l', 'p.id_lembaga = l.id_lembaga');
        // WA Log via id_total_barokah
        $this->db->join('wa_log wl', 'tb.id_total_barokah = wl.id_total_barokah', 'left');
        
        $this->db->where('tb.bulan', $bulan);
        $this->db->where('tb.tahun', $tahun);
        
        if ($lembaga && $lembaga != 'semua') {
            $this->db->where('p.id_lembaga', $lembaga);
        }

        $i = 0;
        foreach ($this->column_search as $item) {
            if ($_POST['search']['value']) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
                if (count($this->column_search) - 1 == $i)
                    $this->db->group_end();
            }
            $i++;
        }

        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    // Get summary data for cards
    public function get_summary_data($bulan, $tahun, $lembaga = null)
    {
        // 1. Total Barokah dari kehadiran_lembaga
        $this->db->select('SUM(kl.jumlah_total) as total_barokah');
        $this->db->from('kehadiran_lembaga kl');
        $this->db->where('kl.bulan', $bulan);
        $this->db->where('kl.tahun', $tahun);
        
        if ($lembaga && $lembaga != 'semua') {
            $this->db->where('kl.id_lembaga', $lembaga);
        }
        
        $result_barokah = $this->db->get()->row();
        
        // 2. Jumlah Umana (penempatan aktif: tgl_selesai > NOW() dan status != 'Tidak Aktif')
        // MUST JOIN with umana to ensure data consistency with Putra/Putri counts
        $this->db->select('COUNT(DISTINCT p.id_penempatan) as jumlah_umana');
        $this->db->from('penempatan p');
        $this->db->join('umana u', 'p.nik = u.nik'); // JOIN to match Putra/Putri logic
        $this->db->where('p.tgl_selesai >', date('Y-m-d'));
        $this->db->where('p.status !=', 'Tidak Aktif');
        
        if ($lembaga && $lembaga != 'semua') {
            $this->db->where('p.id_lembaga', $lembaga);
        }
        
        $result_umana = $this->db->get()->row();
        
        // 3. Umana Putra (penempatan aktif + jk = Laki-laki)
        $this->db->select('COUNT(DISTINCT p.id_penempatan) as umana_putra');
        $this->db->from('penempatan p');
        $this->db->join('umana u', 'p.nik = u.nik');
        $this->db->where('p.tgl_selesai >', date('Y-m-d'));
        $this->db->where('p.status !=', 'Tidak Aktif');
        $this->db->where('u.jk', 'Laki-laki');
        
        if ($lembaga && $lembaga != 'semua') {
            $this->db->where('p.id_lembaga', $lembaga);
        }
        
        $result_putra = $this->db->get()->row();
        
        // 4. Umana Putri (penempatan aktif + jk = Perempuan)
        $this->db->select('COUNT(DISTINCT p.id_penempatan) as umana_putri');
        $this->db->from('penempatan p');
        $this->db->join('umana u', 'p.nik = u.nik');
        $this->db->where('p.tgl_selesai >', date('Y-m-d'));
        $this->db->where('p.status !=', 'Tidak Aktif');
        $this->db->where('u.jk', 'Perempuan'); // Changed from != 'Laki-laki' to = 'Perempuan'
        
        if ($lembaga && $lembaga != 'semua') {
            $this->db->where('p.id_lembaga', $lembaga);
        }
        
        $result_putri = $this->db->get()->row();

        return array(
            'total_barokah' => $result_barokah->total_barokah ?? 0,
            'jumlah_umana' => $result_umana->jumlah_umana ?? 0,
            'umana_putra' => $result_putra->umana_putra ?? 0,
            'umana_putri' => $result_putri->umana_putri ?? 0
        );
    }

    // Get distribusi per lembaga for pie chart
    public function get_distribusi_lembaga($bulan, $tahun)
    {
        $this->db->select('
            l.nama_lembaga,
            COUNT(tb.id_total_barokah) as jumlah_pegawai,
            SUM(tb.diterima) as total_barokah
        ');
        $this->db->from('total_barokah tb');
        $this->db->join('penempatan p', 'tb.id_penempatan = p.id_penempatan');
        $this->db->join('lembaga l', 'p.id_lembaga = l.id_lembaga');
        $this->db->where('tb.bulan', $bulan);
        $this->db->where('tb.tahun', $tahun);
        $this->db->group_by('l.nama_lembaga');
        $this->db->order_by('total_barokah', 'DESC');
        
        return $this->db->get()->result();
    }

    // Get top lembaga for bar chart
    public function get_top_lembaga($bulan, $tahun, $limit = 10)
    {
        $this->db->select('
            l.nama_lembaga,
            COUNT(tb.id_total_barokah) as jumlah_pegawai,
            SUM(tb.diterima) as total_barokah
        ');
        $this->db->from('total_barokah tb');
        $this->db->join('penempatan p', 'tb.id_penempatan = p.id_penempatan');
        $this->db->join('lembaga l', 'p.id_lembaga = l.id_lembaga');
        $this->db->where('tb.bulan', $bulan);
        $this->db->where('tb.tahun', $tahun);
        $this->db->group_by('l.nama_lembaga');
        $this->db->order_by('total_barokah', 'DESC');
        $this->db->limit($limit);
        
        return $this->db->get()->result();
    }

    // Get trend 6 bulan for line chart
    public function get_trend_6_bulan($tahun)
    {
        $bulan_list = array('April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
        
        $this->db->select('
            tb.bulan,
            SUM(tb.diterima) as total_barokah,
            COUNT(tb.id_total_barokah) as jumlah_pegawai
        ');
        $this->db->from('total_barokah tb');
        $this->db->where('tb.tahun', $tahun);
        $this->db->where_in('tb.bulan', $bulan_list);
        $this->db->group_by('tb.bulan');
        $this->db->order_by('FIELD(tb.bulan, "' . implode('","', $bulan_list) . '")');
        $this->db->limit(6);
        
        return $this->db->get()->result();
    }

    // Get trend 4 bulan (backward from selected month) for trend chart
    public function get_trend_4_bulan($bulan, $tahun, $lembaga = null)
    {
        $months = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                       'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
        
        // Get current month index
        $current_index = array_search($bulan, $months);
        
        // Calculate 4 months backward (including current month)
        $target_months = array();
        $target_years = array();
        
        for ($i = 3; $i >= 0; $i--) {
            $index = $current_index - $i;
            $year = $tahun;
            
            // Handle year boundary
            if ($index < 0) {
                $index = 12 + $index; // Wrap to previous year
                // Extract year number and decrement
                $year_parts = explode('/', $tahun);
                $year = ($year_parts[0] - 1) . '/' . ($year_parts[1] - 1);
            }
            
            $target_months[] = $months[$index];
            $target_years[$months[$index]] = $year;
        }
        
        // Build query for each month separately to handle year boundary
        $results = array();
        
        foreach ($target_months as $month) {
            $this->db->select('
                SUM(kl.jumlah_total) as total_barokah
            ');
            $this->db->from('kehadiran_lembaga kl');
            $this->db->where('kl.bulan', $month);
            $this->db->where('kl.tahun', $target_years[$month]);
            
            if ($lembaga && $lembaga != 'semua') {
                $this->db->where('kl.id_lembaga', $lembaga);
            }
            
            $result = $this->db->get()->row();
            
            $results[] = (object) array(
                'bulan' => $month,
                'tahun' => $target_years[$month],
                'total_barokah' => $result->total_barokah ?? 0
            );
        }
        
        return $results;
    }


    // Get breakdown komponen for chart
    public function get_breakdown_komponen($bulan, $tahun, $lembaga = null)
    {
        $this->db->select('
            SUM(tb.tunjab) as total_tunjab,
            SUM(tb.nominal_kehadiran) as total_kehadiran,
            SUM(tb.tunkel) as total_tunkel,
            SUM(tb.tunj_anak) as total_tunj_anak,
            SUM(tb.tmp) as total_tmp,
            SUM(tb.kehormatan) as total_kehormatan,
            SUM(tb.tbk) as total_tbk
        ');
        $this->db->from('total_barokah tb');
        
        // JOIN dengan penempatan untuk filter lembaga
        if ($lembaga && $lembaga != 'semua') {
            $this->db->join('penempatan p', 'tb.id_penempatan = p.id_penempatan');
            $this->db->where('p.id_lembaga', $lembaga);
        }
        
        $this->db->where('tb.bulan', $bulan);
        $this->db->where('tb.tahun', $tahun);
        
        return $this->db->get()->row();
    }

    // Get statistik kehadiran
    public function get_statistik_kehadiran($bulan, $tahun, $lembaga = null)
    {
        $this->db->select('
            k.id_kehadi,
            COUNT(DISTINCT k.id_kehadiran) as jumlah_pegawai,
            AVG(k.jumlah_hadir) as rata_rata_hadir
        ');
        $this->db->from('kehadiran k');
        $this->db->join('penempatan p', 'k.id_penempatan = p.id_penempatan');
        $this->db->where('k.bulan', $bulan);
        $this->db->where('k.tahun', $tahun);
        
        if ($lembaga && $lembaga != 'semua') {
            $this->db->where('p.id_lembaga', $lembaga);
        }
        
        $this->db->group_by('k.id_kehadi');
        
        return $this->db->get()->result();
    }

    // Get breakdown potongan
    public function get_breakdown_potongan($bulan, $tahun, $lembaga = null)
    {
        // Get period dates
        $start_date = $tahun . '-' . $this->get_month_number($bulan) . '-01';
        $end_date = date('Y-m-t', strtotime($start_date));
        
        $this->db->select('
            p.nama_potongan,
            COUNT(DISTINCT pu.id_potongan_umana) as jumlah_pegawai,
            SUM(pu.nominal_potongan) as potongan
        ');
        $this->db->from('potongan_umana pu');
        $this->db->join('potongan p', 'pu.jenis_potongan = p.id_potongan', 'left');
        $this->db->join('penempatan pen', 'pu.id_penempatan = pen.id_penempatan', 'left');
        $this->db->join('umana u', 'pen.nik = u.nik', 'left');
        $this->db->where('pu.min_periode_potongan <=', $end_date);
        $this->db->where('pu.max_periode_potongan >=', $start_date);
        
        if ($lembaga && $lembaga != 'semua') {
            $this->db->where('pen.id_lembaga', $lembaga);
        }
        
        $this->db->group_by('p.nama_potongan');
        $this->db->order_by('potongan', 'DESC');
        
        return $this->db->get()->result();
    }

    // Get detail pegawai for modal
    public function get_detail_pegawai($id)
    {
        $this->db->select('
            tb.*,
            u.nama_lengkap,
            u.nik,
            u.nomor_hp,
            u.nama_bank,
            u.nomor_rekening,
            u.nama_penerima,
            l.nama_lembaga,
            p.jabatan_lembaga,
            tb.kehadiran as jumlah_hadir,
            wl.status as wa_status,
            wl.created_at as wa_sent_at
        ');
        $this->db->from('total_barokah tb');
        // Correct Joins
        $this->db->join('penempatan p', 'tb.id_penempatan = p.id_penempatan');
        $this->db->join('umana u', 'p.nik = u.nik');
        $this->db->join('lembaga l', 'p.id_lembaga = l.id_lembaga');
        $this->db->join('wa_log wl', 'tb.id_total_barokah = wl.id_total_barokah', 'left');
        
        $this->db->where('tb.id_total_barokah', $id);
        
        $result = $this->db->get()->row();
        
        // Get potongan detail
        if ($result) {
            $this->db->select('p.nama_potongan, pu.nominal_potongan');
            $this->db->from('potongan_umana pu');
            $this->db->join('potongan p', 'pu.id_potongan = p.id_potongan');
            $this->db->where('pu.nik', $result->nik);
            $this->db->where('pu.min_periode_potongan <=', date('Y-m-t', strtotime($result->tahun . '-' . $this->get_month_number($result->bulan) . '-01')));
            $this->db->where('pu.max_periode_potongan >=', $result->tahun . '-' . $this->get_month_number($result->bulan) . '-01');
            
            $result->detail_potongan = $this->db->get()->result();
        }
        
        return $result;
    }



    public function get_datatables($bulan, $tahun, $lembaga)
    {
        $this->_get_datatables_query($bulan, $tahun, $lembaga);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered($bulan, $tahun, $lembaga)
    {
        // Optimization: If no search keyword, use lighter count_all
        if (empty($_POST['search']['value'])) {
            return $this->count_all($bulan, $tahun, $lembaga);
        }
        
        // Otherwise proceed with full query for search filtering
        $this->_get_datatables_query($bulan, $tahun, $lembaga);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($bulan, $tahun, $lembaga)
    {
        $this->db->from('total_barokah tb');
        
        if ($lembaga && $lembaga != 'semua') {
            // Must join penempatan to filter by lembaga
            $this->db->join('penempatan p', 'tb.id_penempatan = p.id_penempatan');
            $this->db->where('p.id_lembaga', $lembaga);
        }
        
        $this->db->where('tb.bulan', $bulan);
        $this->db->where('tb.tahun', $tahun);
        
        return $this->db->count_all_results();
    }

    // Get all data for export
    public function get_all_data($bulan, $tahun, $lembaga = null)
    {
        $this->db->select('
            tb.*,
            u.nik,
            u.nama_lengkap,
            l.nama_lembaga,
            p.jabatan_lembaga,
            wl.created_at as tgl_kirim
        ');
        $this->db->from('total_barokah tb');
        $this->db->join('penempatan p', 'tb.id_penempatan = p.id_penempatan');
        $this->db->join('umana u', 'p.nik = u.nik');
        $this->db->join('lembaga l', 'p.id_lembaga = l.id_lembaga');
        $this->db->join('wa_log wl', 'tb.id_total_barokah = wl.id_total_barokah', 'left');
        
        $this->db->where('tb.bulan', $bulan);
        $this->db->where('tb.tahun', $tahun);
        
        if ($lembaga && $lembaga != 'semua') {
            $this->db->where('p.id_lembaga', $lembaga);
        }
        
        $this->db->order_by('u.nama_lengkap', 'ASC');
        
        return $this->db->get()->result();
    }

    // Get latest periode from database
    public function get_latest_periode()
    {
        $this->db->select('bulan, tahun');
        $this->db->from('total_barokah');
        $this->db->order_by('id_total_barokah', 'DESC');
        $this->db->limit(1);
        
        $result = $this->db->get()->row();
        
        // Fallback to default if no data
        if (!$result) {
            return (object) array(
                'bulan' => 'September',
                'tahun' => '2025/2026'
            );
        }
        
        return $result;
    }

    // Helper functions
    private function get_previous_month($bulan)
    {
        $months = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
        $index = array_search($bulan, $months);
        
        if ($index === false || $index == 0) {
            return 'Desember';
        }
        
        return $months[$index - 1];
    }

    private function get_month_number($bulan)
    {
        $months = array(
            'Januari' => '01', 'Februari' => '02', 'Maret' => '03', 'April' => '04',
            'Mei' => '05', 'Juni' => '06', 'Juli' => '07', 'Agustus' => '08',
            'September' => '09', 'Oktober' => '10', 'November' => '11', 'Desember' => '12'
        );
        
        return $months[$bulan] ?? '01';
    }
}
