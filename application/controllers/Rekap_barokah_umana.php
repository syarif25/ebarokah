<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rekap_barokah_umana extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Login_model');
        $this->load->model('Rekap_barokah_umana_model');
        $this->load->helper('Rupiah_helper');

        // Proteksi login & pengecekan role untuk semua endpoint dalam controller ini
        $this->Login_model->getsqurity();
        $jabatan = $this->session->userdata('jabatan');
        if ($jabatan != 'SuperAdmin' && $jabatan != 'Evaluasi') {
            $this->load->view('Error');
            exit; // Menggunakan exit agar tidak melanjutkan eksekusi ke method apapun
        }
    }

    /**
     * Halaman utama Rekap Total Barokah Umana.
     * Hanya dapat diakses oleh SuperAdmin dan Evaluasi.
     */
    public function index()
    {
        $isi['css']     = 'Rekap_barokah_umana/Css';
        $isi['content'] = 'Rekap_barokah_umana/Rekap';
        $isi['ajax']    = 'Rekap_barokah_umana/Ajax';
        $this->load->view('Template', $isi);
    }

    /**
     * Endpoint AJAX: kembalikan daftar periode unik untuk dropdown filter.
     * Format: JSON array of { bulan, tahun }
     */
    public function get_periode()
    {
        $list = $this->Rekap_barokah_umana_model->get_periode();
        echo json_encode($list);
    }

    public function get_lembaga()
    {
        $list = $this->Rekap_barokah_umana_model->get_lembaga();
        echo json_encode($list);
    }

    /**
     * Endpoint AJAX: kembalikan data rekap dalam format DataTables.
     * Menerima POST: bulan, tahun.
     */
    public function data_list()
    {
        $bulan = $this->input->post('bulan');
        $tahun = $this->input->post('tahun');
        $id_lembaga = $this->input->post('id_lembaga');

        log_message('error', 'NILAI BULAN DARI POST: ' . $bulan);

        // Validasi input
        if (empty($bulan) || empty($tahun)) {
            header('Content-Type: application/json');
            echo json_encode(['data' => []]);
            return;
        }

        $array_nik = [];

        // Normalisasi bulan dan tahun untuk parameter get_nik_by_lembaga
        $map_teks_ke_angka = [
            'Januari' => '01', 'Februari' => '02', 'Maret' => '03', 'April' => '04',
            'Mei' => '05', 'Juni' => '06', 'Juli' => '07', 'Agustus' => '08',
            'September' => '09', 'Oktober' => '10', 'November' => '11', 'Desember' => '12',
            '1' => '01', '01' => '01', '2' => '02', '02' => '02', '3' => '03', '03' => '03',
            '4' => '04', '04' => '04', '5' => '05', '05' => '05', '6' => '06', '06' => '06',
            '7' => '07', '07' => '07', '8' => '08', '08' => '08', '9' => '09', '09' => '09',
            '10' => '10', '11' => '11', '12' => '12'
        ];
        $bulan_angka = isset($map_teks_ke_angka[$bulan]) ? $map_teks_ke_angka[$bulan] : '01';
        
        $tahun_date = $tahun;
        if (strpos($tahun, '/') !== false) {
            $tahun_arr = explode('/', $tahun);
            $tahun_date = trim($tahun_arr[0]);
        }
        $periode_filter = $tahun_date . '-' . $bulan_angka . '-01';

        if (!empty($id_lembaga)) {
            $array_nik = $this->Rekap_barokah_umana_model->get_nik_by_lembaga($id_lembaga, $periode_filter);
            if (empty($array_nik)) {
                header('Content-Type: application/json');
                echo json_encode(['data' => []]);
                return;
            }
        }

        $data_struktural = $this->Rekap_barokah_umana_model->get_rekap_struktural($bulan, $tahun, $array_nik);
        $data_mengajar = $this->Rekap_barokah_umana_model->get_rekap_mengajar($bulan, $tahun, $array_nik);

        $data_final = [];

        // Looping 1: Memproses Data Struktural & Irisan Mengajar
        foreach ($data_struktural as $row) {
            $nik = $row->nik;
            $nominal_struktural = $row->nominal_struktural ?? 0;
            $nominal_mengajar = 0;
            $keterangan_gabungan = $row->keterangan_lembaga;
            $sort_id = $row->min_id_total_barokah ?? 999999999;

            if (isset($data_mengajar[$nik])) {
                $nominal_mengajar = $data_mengajar[$nik]['nominal_mengajar'] ?? 0;
                
                $ket_arr = explode(', ', $keterangan_gabungan);
                $ket_arr_mengajar = explode(', ', $data_mengajar[$nik]['keterangan_mengajar']);
                $merged_ket = array_merge($ket_arr, $ket_arr_mengajar);
                $merged_ket = array_unique(array_filter(array_map('trim', $merged_ket)));
                $keterangan_gabungan = implode(', ', $merged_ket);
                
                unset($data_mengajar[$nik]);
            }

            $jumlah = $nominal_struktural + $nominal_mengajar;

            $item = [];
            $item['sort_id']     = $sort_id;
            $item['nama']        = htmlentities($row->nama_lengkap ?? '');
            $item['struktural']  = rupiah($nominal_struktural);
            $item['mengajar']    = rupiah($nominal_mengajar);
            $item['satpam']      = "";
            $item['kepanitiaan'] = "";
            $item['jumlah']      = rupiah($jumlah);
            $item['keterangan']  = htmlentities($keterangan_gabungan ?? '-');
            $item['aksi']        = "<button class='btn btn-sm btn-info shadow btn-xs sharp btn-detail' data-nik='".$nik."' data-bulan='".$bulan."' data-tahun='".$tahun."'><i class='fa fa-eye'></i> Detail</button>";
            
            $data_final[] = $item;
        }

        // Looping 2: Memproses Sisa Data Murni Mengajar (Tanpa Struktural)
        foreach ($data_mengajar as $nik => $dt) {
            $nominal_struktural = 0;
            $nominal_mengajar = $dt['nominal_mengajar'] ?? 0;
            $jumlah = $nominal_mengajar;
            $keterangan_gabungan = $dt['keterangan_mengajar'];
            $nama_lengkap = isset($dt['nama_lengkap']) && !empty($dt['nama_lengkap']) ? $dt['nama_lengkap'] : "UMANA - " . $nik;
            $sort_id = $dt['min_id_total_barokah'] ?? 999999999;

            $item = [];
            $item['sort_id']     = $sort_id;
            $item['nama']        = htmlentities($nama_lengkap);
            $item['struktural']  = rupiah($nominal_struktural);
            $item['mengajar']    = rupiah($nominal_mengajar);
            $item['satpam']      = "";
            $item['kepanitiaan'] = "";
            $item['jumlah']      = rupiah($jumlah);
            $item['keterangan']  = htmlentities($keterangan_gabungan ?? '-');
            $item['aksi']        = "<button class='btn btn-sm btn-info shadow btn-xs sharp btn-detail' data-nik='".$nik."' data-bulan='".$bulan."' data-tahun='".$tahun."'><i class='fa fa-eye'></i> Detail</button>";
            
            $data_final[] = $item;
        }

        // Mengurutkan secara global berdasarkan id_total_barokah terkecil
        usort($data_final, function($a, $b) {
            return $a['sort_id'] <=> $b['sort_id'];
        });

        // Menata ulang nomor urut setelah di-sorting
        $no = 1;
        foreach ($data_final as &$row) {
            $row['no'] = $no++;
            unset($row['sort_id']);
        }

        header('Content-Type: application/json');
        echo json_encode(['data' => $data_final]);
    }

    public function get_detail_rincian()
    {
        $nik = $this->input->post('nik');
        $bulan = $this->input->post('bulan');
        $tahun = $this->input->post('tahun');

        if (empty($nik) || empty($bulan) || empty($tahun)) {
            echo json_encode(['status' => 'error', 'message' => 'Parameter tidak lengkap']);
            return;
        }

        // Ambil nama lengkap Umana (opsional untuk judul)
        $umana = $this->db->select('nama_lengkap')->where('nik', $nik)->get('umana')->row();
        $nama_lengkap = $umana ? $umana->nama_lengkap : 'UMANA - ' . $nik;

        $data_struktural = $this->Rekap_barokah_umana_model->get_detail_struktural_umana($nik, $bulan, $tahun);
        $data_mengajar = $this->Rekap_barokah_umana_model->get_detail_mengajar_umana($nik, $bulan, $tahun);

        // Hitung total keseluruhan
        $total = 0;
        foreach ($data_struktural as $st) {
            $total += (float)$st['nominal_struktural'];
        }
        foreach ($data_mengajar as $mg) {
            $total += (float)$mg['diterima'];
        }

        echo json_encode([
            'status' => 'success', 
            'nama' => $nama_lengkap, 
            'struktural' => $data_struktural, 
            'mengajar' => $data_mengajar, 
            'total' => $total
        ]);
    }
}
