<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_pengajar_model extends CI_Model {

    var $column_order = array(
        null, // 0. No
        'tbp.bulan', // 1. Bulan
        'tbp.tahun', // 2. Tahun
        'u.nik', // 3. NIK
        'u.nama_lengkap', // 4. Nama
        'l.nama_lembaga', // 5. Lembaga
        'p.kategori', // 6. Kategori
        'tbp.mengajar', // 7. Mengajar
        'tbp.dty', // 8. DTY
        'tbp.jumlah_hadir', // 9. Jumlah Hadir
        'tbp.nominal_kehadiran', // 10. Kehadiran (total nominal)
        'tbp.tunkel', // 11. Tunkel
        'tbp.tun_anak', // 12. Tunj Anak
        'tbp.kehormatan', // 13. Kehormatan
        'tbp.walkes', // 14. Walkes
        'tbp.potongan', // 15. Potongan
        'tbp.diterima', // 16. Diterima
        'wl.created_at' // 17. Tgl Kirim
    );
    var $column_search = array('u.nik', 'u.nama_lengkap', 'l.nama_lembaga', 'p.kategori');
    var $order = array('u.nama_lengkap' => 'asc');

    // Get summary data for cards
    public function get_summary_data($bulan, $tahun, $lembaga = null)
    {
        // 1. Total Barokah dari total_barokah_pengajar
        $this->db->select('SUM(tbp.diterima) as total_barokah');
        $this->db->from('total_barokah_pengajar tbp');
        
        if ($lembaga && $lembaga != 'semua') {
            $this->db->join('pengajar p', 'tbp.id_pengajar = p.id_pengajar');
            $this->db->where('p.id_lembaga', $lembaga);
        }
        
        $this->db->where('tbp.bulan', $bulan);
        $this->db->where('tbp.tahun', $tahun);
        
        $result_barokah = $this->db->get()->row();
        
        // 2. Jumlah Pengajar (status != 'Tidak Aktif' AND tgl_selesai > NOW())
        $this->db->select('COUNT(DISTINCT p.id_pengajar) as jumlah_pengajar');
        $this->db->from('pengajar p');
        $this->db->join('umana u', 'p.nik = u.nik');
        $this->db->where('p.status !=', 'Tidak Aktif');
        $this->db->where('p.tgl_selesai >', date('Y-m-d'));
        
        if ($lembaga && $lembaga != 'semua') {
            $this->db->where('p.id_lembaga', $lembaga);
        }
        
        $result_pengajar = $this->db->get()->row();
        
        // 3. Pengajar Putra (status aktif + jk = Laki-laki)
        $this->db->select('COUNT(DISTINCT p.id_pengajar) as pengajar_putra');
        $this->db->from('pengajar p');
        $this->db->join('umana u', 'p.nik = u.nik');
        $this->db->where('p.status !=', 'Tidak Aktif');
        $this->db->where('p.tgl_selesai >', date('Y-m-d'));
        $this->db->where('u.jk', 'Laki-laki');
        
        if ($lembaga && $lembaga != 'semua') {
            $this->db->where('p.id_lembaga', $lembaga);
        }
        
        $result_putra = $this->db->get()->row();
        
        // 4. Pengajar Putri (status aktif + jk = Perempuan)
        $this->db->select('COUNT(DISTINCT p.id_pengajar) as pengajar_putri');
        $this->db->from('pengajar p');
        $this->db->join('umana u', 'p.nik = u.nik');
        $this->db->where('p.status !=', 'Tidak Aktif');
        $this->db->where('p.tgl_selesai >', date('Y-m-d'));
        $this->db->where('u.jk', 'Perempuan');
        
        if ($lembaga && $lembaga != 'semua') {
            $this->db->where('p.id_lembaga', $lembaga);
        }
        
        $result_putri = $this->db->get()->row();

        return array(
            'total_barokah' => $result_barokah->total_barokah ?? 0,
            'jumlah_pengajar' => $result_pengajar->jumlah_pengajar ?? 0,
            'pengajar_putra' => $result_putra->pengajar_putra ?? 0,
            'pengajar_putri' => $result_putri->pengajar_putri ?? 0
        );
    }

    // Get latest periode from database
    public function get_latest_periode()
    {
        $this->db->select('bulan, tahun');
        $this->db->from('total_barokah_pengajar');
        $this->db->order_by('id_total_barokah_pengajar', 'DESC');
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

    // Get distribusi per lembaga for pie chart  
    public function get_distribusi_lembaga($bulan, $tahun)
    {
        // TODO: Implement based on table structure
        return [];
    }

    // Get top lembaga for bar chart
    public function get_top_lembaga($bulan, $tahun, $limit = 10)
    {
        $this->db->select('
            l.nama_lembaga,
            SUM(kl.jumlah_total) as total_barokah
        ');
        $this->db->from('kehadiran_lembaga kl');
        $this->db->join('lembaga l', 'kl.id_lembaga = l.id_lembaga');
        $this->db->where('kl.bulan', $bulan);
        $this->db->where('kl.tahun', $tahun);
        $this->db->where('kl.kategori', 'Pengajar'); // Filter pengajar only
        $this->db->group_by('l.nama_lembaga');
        $this->db->order_by('total_barokah', 'DESC');
        $this->db->limit($limit);
        
        return $this->db->get()->result();
    }

    // Get trend for line chart
    public function get_trend_6_bulan($tahun)
    {
        // TODO: Implement based on table structure
        return [];
    }

    // Get trend 4 bulan
    public function get_trend_4_bulan($bulan, $tahun, $lembaga = null)
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
            $this->db->select('SUM(kl.jumlah_total) as total_barokah');
            $this->db->from('kehadiran_lembaga kl');
            $this->db->where('kl.bulan', $month);
            $this->db->where('kl.tahun', $target_years[$month]);
            $this->db->where('kl.kategori', 'Pengajar'); // Filter pengajar
            
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

    // Get breakdown komponen
    public function get_breakdown_komponen($bulan, $tahun, $lembaga = null)
    {
        $this->db->select('
            SUM(tbp.mengajar) as total_mengajar,
            SUM(tbp.dty) as total_dty,
            SUM(tbp.nominal_kehadiran + tbp.nominal_hadir_15 + tbp.nominal_hadir_10 + tbp.barokah_piket) as total_kehadiran,
            SUM(tbp.tunkel) as total_tunkel,
            SUM(tbp.tun_anak) as total_tunj_anak,
            SUM(tbp.kehormatan) as total_kehormatan,
            SUM(tbp.walkes) as total_walkes
        ');
        $this->db->from('total_barokah_pengajar tbp');
        
        if ($lembaga && $lembaga != 'semua') {
            $this->db->join('pengajar p', 'tbp.id_pengajar = p.id_pengajar');
            $this->db->where('p.id_lembaga', $lembaga);
        }
        
        $this->db->where('tbp.bulan', $bulan);
        $this->db->where('tbp.tahun', $tahun);
        
        return $this->db->get()->row();
    }

    // Get statistik kehadiran
    public function get_statistik_kehadiran($bulan, $tahun, $lembaga = null)
    {
        // TODO: Implement based on table structure
        return [];
    }

    // Get breakdown potongan
    public function get_breakdown_potongan($bulan, $tahun, $lembaga = null)
    {
        // TODO: Implement based on table structure
        return [];
    }

    // DataTables query
    private function _get_datatables_query($bulan, $tahun, $lembaga)
    {
        $this->db->select('
            tbp.*,
            u.nik,
            u.nama_lengkap,
            l.nama_lembaga,
            p.kategori,
            tbp.timestamp as tgl_kirim
        ');
        $this->db->from('total_barokah_pengajar tbp');
        $this->db->join('pengajar p', 'tbp.id_pengajar = p.id_pengajar');
        $this->db->join('umana u', 'p.nik = u.nik');
        $this->db->join('lembaga l', 'p.id_lembaga = l.id_lembaga');
        
        $this->db->where('tbp.bulan', $bulan);
        $this->db->where('tbp.tahun', $tahun);
        
        if ($lembaga && $lembaga != 'semua') {
            $this->db->where('p.id_lembaga', $lembaga);
        }

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
                    $this->db-> group_end();
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

    public function get_datatables($bulan, $tahun, $lembaga)
    {
        $this->_get_datatables_query($bulan, $tahun, $lembaga);
        if ($_POST['length'] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered($bulan, $tahun, $lembaga)
    {
        // Optimization: If no search keyword, use lighter count_all
        if (empty($_POST['search']['value'])) {
            return $this->count_all($bulan, $tahun, $lembaga);
        }
        
        $this->_get_datatables_query($bulan, $tahun, $lembaga);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($bulan, $tahun, $lembaga)
    {
        $this->db->from('total_barokah_pengajar tbp');
        
        if ($lembaga && $lembaga != 'semua') {
            $this->db->join('pengajar p', 'tbp.id_pengajar = p.id_pengajar');
            $this->db->where('p.id_lembaga', $lembaga);
        }
        
        $this->db->where('tbp.bulan', $bulan);
        $this->db->where('tbp.tahun', $tahun);
        
        return $this->db->count_all_results();
    }

    // Get all data for export
    public function get_all_data($bulan, $tahun, $lembaga = null)
    {
        $this->db->select('
            tbp.*,
            u.nik,
            u.nama_lengkap,
            l.nama_lembaga,
            p.kategori,
            wl.created_at as tgl_kirim
        ');
        $this->db->from('total_barokah_pengajar tbp');
        $this->db->join('pengajar p', 'tbp.id_pengajar = p.id_pengajar');
        $this->db->join('umana u', 'p.nik = u.nik');
        $this->db->join('lembaga l', 'p.id_lembaga = l.id_lembaga');
        $this->db->join('wa_log wl', 'tbp.id_total_barokah_pengajar = wl.id_total_barokah_pengajar', 'left');
        
        $this->db->where('tbp.bulan', $bulan);
        $this->db->where('tbp.tahun', $tahun);
        
        if ($lembaga && $lembaga != 'semua') {
            $this->db->where('p.id_lembaga', $lembaga);
        }
        
        $this->db->order_by('u.nama_lengkap', 'ASC');
        
        return $this->db->get()->result();
    }
}
