<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_satpam_model extends CI_Model {

    var $column_order = array(
        null, // 0. No
        'tbs.bulan', // 1. Bulan
        'tbs.tahun', // 2. Tahun
        'u.nik', // 3. NIK
        'u.nama_lengkap', // 4. Nama
        'tbs.jumlah_hari', // 5. Jml Hari
        'tbs.nominal_transport', // 6. Transport
        'tbs.jumlah_shift', // 7. Jml Shift
        'tbs.jumlah_dinihari', // 8. Jml Dinihari
        'tbs.konsumsi', // 9. Konsumsi
        'tbs.rank', // 10. Rank
        'tbs.jumlah_barokah', // 11. Jml Barokah
        'tbs.diterima', // 12. Diterima
        'tbs.tgl_input' // 13. Tgl Kirim
    );
    var $column_search = array('u.nik', 'u.nama_lengkap');
    var $order = array('u.nama_lengkap' => 'asc');

    // Get summary data for cards
    public function get_summary_data($bulan, $tahun)
    {
        // 1. Total Barokah dari total_barokah_satpam
        $this->db->select('SUM(tbs.diterima) as total_barokah');
        $this->db->from('total_barokah_satpam tbs');
        $this->db->where('tbs.bulan', $bulan);
        $this->db->where('tbs.tahun', $tahun);
        
        $result_barokah = $this->db->get()->row();
        
        // 2. Jumlah Satpam (status != 'Tidak Aktif' AND tgl_selesai > NOW)
        $this->db->select('COUNT(DISTINCT s.id_satpam) as jumlah_satpam');
        $this->db->from('satpam s');
        $this->db->join('umana u', 's.nik = u.nik');
        $this->db->where('s.status !=', 'Tidak Aktif');
        $this->db->where('s.tgl_selesai >', date('Y-m-d'));
        
        $result_satpam = $this->db->get()->row();
        
        // 3. Satpam Putra
        $this->db->select('COUNT(DISTINCT s.id_satpam) as satpam_putra');
        $this->db->from('satpam s');
        $this->db->join('umana u', 's.nik = u.nik');
        $this->db->where('s.status !=', 'Tidak Aktif');
        $this->db->where('s.tgl_selesai >', date('Y-m-d'));
        $this->db->where('u.jk', 'Laki-laki');
        
        $result_putra = $this->db->get()->row();
        
        // 4. Satpam Putri
        $this->db->select('COUNT(DISTINCT s.id_satpam) as satpam_putri');
        $this->db->from('satpam s');
        $this->db->join('umana u', 's.nik = u.nik');
        $this->db->where('s.status !=', 'Tidak Aktif');
        $this->db->where('s.tgl_selesai >', date('Y-m-d'));
        $this->db->where('u.jk', 'Perempuan');
        
        $result_putri = $this->db->get()->row();

        return array(
            'total_barokah' => $result_barokah->total_barokah ?? 0,
            'jumlah_satpam' => $result_satpam->jumlah_satpam ?? 0,
            'satpam_putra' => $result_putra->satpam_putra ?? 0,
            'satpam_putri' => $result_putri->satpam_putri ?? 0
        );
    }

    // Get latest periode from database
    public function get_latest_periode()
    {
        $this->db->select('bulan, tahun');
        $this->db->from('total_barokah_satpam');
        $this->db->order_by('id_total_barokah_satpam', 'DESC');
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

    // Get breakdown komponen
    public function get_breakdown_komponen($bulan, $tahun)
    {
        $this->db->select('
            SUM(tbs.nominal_transport) as total_transport,
            SUM(tbs.jumlah_transport) as total_jumlah_transport,
            SUM(tbs.jumlah_shift) as total_shift,
            SUM(tbs.jumlah_dinihari) as total_dinihari,
            SUM(tbs.konsumsi) as total_konsumsi,
            SUM(tbs.jumlah_konsumsi) as total_jumlah_konsumsi,
            SUM(tbs.rank) as total_rank
        ');
        $this->db->from('total_barokah_satpam tbs');
        $this->db->where('tbs.bulan', $bulan);
        $this->db->where('tbs.tahun', $tahun);
        
        return $this->db->get()->row();
    }

    // Get statistik kehadiran
    public function get_statistik_kehadiran($bulan, $tahun)
    {
        $this->db->select('
            SUM(ks.jumlah_hari) as total_hari,
            SUM(ks.jumlah_shift) as total_shift,
            SUM(ks.jumlah_dinihari) as total_dinihari
        ');
        $this->db->from('kehadiran_satpam ks');
        $this->db->where('ks.bulan', $bulan);
        $this->db->where('ks.tahun', $tahun);
        
        return $this->db->get()->row();
    }

    // Get trend 4 bulan
    public function get_trend_4_bulan($bulan, $tahun)
    {
        $months = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                       'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
        
        $current_index = array_search($bulan, $months);
        $target_months = array();
        $target_years = array();
        
        for ($i = 3; $i >= 0; $i--) {
            $index = $current_index - $i;
            $year = $tahun;
            
            if ($index < 0) {
                $index = 12 + $index;
                $year_parts = explode('/', $tahun);
                $year = ($year_parts[0] - 1) . '/' . ($year_parts[1] - 1);
            }
            
            $target_months[] = $months[$index];
            $target_years[$months[$index]] = $year;
        }
        
        $results = array();
        
        foreach ($target_months as $month) {
            $this->db->select('SUM(tbs.diterima) as total_barokah');
            $this->db->from('total_barokah_satpam tbs');
            $this->db->where('tbs.bulan', $month);
            $this->db->where('tbs.tahun', $target_years[$month]);
            
            $result = $this->db->get()->row();
            
            $results[] = (object) array(
                'bulan' => $month,
                'tahun' => $target_years[$month],
                'total_barokah' => $result->total_barokah ?? 0
            );
        }
        
        return $results;
    }

    // DataTables query
    private function _get_datatables_query($bulan, $tahun)
    {
        $this->db->select('
            tbs.*,
            u.nik,
            u.nama_lengkap,
            tbs.tgl_input as tgl_kirim
        ');
        $this->db->from('total_barokah_satpam tbs');
        $this->db->join('satpam s', 'tbs.id_satpam = s.id_satpam');
        $this->db->join('umana u', 's.nik = u.nik');
        
        $this->db->where('tbs.bulan', $bulan);
        $this->db->where('tbs.tahun', $tahun);

        // Search
        $i = 0;
        foreach ($this->column_search as $item) {
            if ($_POST['search']['value']) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }

                if (count($this->column_search) - 1 == $i) {
                    $this->db->group_end();
                }
            }
            $i++;
        }

        // Order
        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    public function get_datatables($bulan, $tahun)
    {
        $this->_get_datatables_query($bulan, $tahun);
        if ($_POST['length'] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered($bulan, $tahun)
    {
        // Optimization: If no search keyword, use lighter count_all
        if (empty($_POST['search']['value'])) {
            return $this->count_all($bulan, $tahun);
        }
        
        $this->_get_datatables_query($bulan, $tahun);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($bulan, $tahun)
    {
        $this->db->from('total_barokah_satpam tbs');
        $this->db->where('tbs.bulan', $bulan);
        $this->db->where('tbs.tahun', $tahun);
        
        return $this->db->count_all_results();
    }

    // Get all data for export
    public function get_all_data($bulan, $tahun)
    {
        $this->db->select('
            tbs.*,
            u.nik,
            u.nama_lengkap,
            tbs.tgl_input as tgl_kirim
        ');
        $this->db->from('total_barokah_satpam tbs');
        $this->db->join('satpam s', 'tbs.id_satpam = s.id_satpam');
        $this->db->join('umana u', 's.nik = u.nik');
        
        $this->db->where('tbs.bulan', $bulan);
        $this->db->where('tbs.tahun', $tahun);
        
        $this->db->order_by('u.nama_lengkap', 'ASC');
        
        return $this->db->get()->result();
    }
}
