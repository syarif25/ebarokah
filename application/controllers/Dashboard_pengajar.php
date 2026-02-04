<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_pengajar extends CI_Controller {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Dashboard_pengajar_model');
        $this->load->model('Login_model');
        $this->load->helper('Rupiah_helper');
    }

    public function index()
    {
        $this->Login_model->getsqurity();
        
        // Get latest periode for default filter values
        $latest = $this->Dashboard_pengajar_model->get_latest_periode();
        
        $isi['css']     = 'Dashboard_pengajar/Css';
        $isi['content'] = 'Dashboard_pengajar/index';
        $isi['ajax']    = 'Dashboard_pengajar/Ajax';
        $isi['default_bulan'] = $latest->bulan;
        $isi['default_tahun'] = $latest->tahun;
        $this->load->view('Template', $isi);
    }

    // AJAX: Get summary data for cards
    public function get_summary()
    {
        $bulan = $this->input->post('bulan');
        $tahun = $this->input->post('tahun');
        $lembaga = $this->input->post('lembaga');

        $data = $this->Dashboard_pengajar_model->get_summary_data($bulan, $tahun, $lembaga);
        
        echo json_encode($data);
    }

    // AJAX: Get chart data
    public function get_chart_data()
    {
        $type = $this->input->post('type');
        $bulan = $this->input->post('bulan');
        $tahun = $this->input->post('tahun');
        $lembaga = $this->input->post('lembaga');

        $data = [];

        switch ($type) {
            case 'pie':
                $data = $this->Dashboard_pengajar_model->get_distribusi_lembaga($bulan, $tahun);
                break;
            case 'bar':
                $data = $this->Dashboard_pengajar_model->get_top_lembaga($bulan, $tahun, 10);
                break;
            case 'line':
         $data = $this->Dashboard_pengajar_model->get_trend_6_bulan($tahun);
                break;
            case 'donut':
                $data = $this->Dashboard_pengajar_model->get_breakdown_komponen($bulan, $tahun, $lembaga);
                break;
            case 'kehadiran':
                $data = $this->Dashboard_pengajar_model->get_statistik_kehadiran($bulan, $tahun, $lembaga);
                break;
            case 'potongan':
                $data = $this->Dashboard_pengajar_model->get_breakdown_potongan($bulan, $tahun, $lembaga);
                break;
            case 'trend':
                $data = $this->Dashboard_pengajar_model->get_trend_4_bulan($bulan, $tahun, $lembaga);
                break;
        }

        echo json_encode($data);
    }

    // AJAX: Get table data for DataTables
    public function get_table_data()
    {
        $bulan = $this->input->post('bulan');
        $tahun = $this->input->post('tahun');
        $lembaga = $this->input->post('lembaga');

        $list = $this->Dashboard_pengajar_model->get_datatables($bulan, $tahun, $lembaga);
        $data = [];
        $no = $_POST['start'];

        foreach ($list as $item) {
            $no++;
            $row = [];
            
            $row[] = $no; // 0. No
            $row[] = $item->bulan; // 1. Bulan
            $row[] = $item->tahun; // 2. Tahun
            $row[] = $item->nik; // 3. NIK
            $row[] = $item->nama_lengkap; // 4. Nama
            $row[] = $item->nama_lembaga; // 5. Lembaga
            $row[] = $item->kategori; // 6. Kategori (Guru/Dosen)
            $row[] = 'Rp ' . number_format($item->mengajar ?? 0, 0, ',', '.'); // 7. Mengajar
            $row[] = 'Rp ' . number_format($item->dty ?? 0, 0, ',', '.'); // 8. DTY
            $row[] = ($item->jumlah_hadir ?? 0) . ' hari'; // 9. Jml Hadir
            
            // 10. Kehadiran = nominal_kehadiran + nominal_hadir_15 + nominal_hadir_10 + barokah_piket
            $total_kehadiran = ($item->nominal_kehadiran ?? 0) + 
                             ($item->nominal_hadir_15 ?? 0) + 
                             ($item->nominal_hadir_10 ?? 0) + 
                             ($item->barokah_piket ?? 0);
            $row[] = 'Rp ' . number_format($total_kehadiran, 0, ',', '.');
            
            $row[] = 'Rp ' . number_format($item->tunkel ?? 0, 0, ',', '.'); // 11. Tunkel
            $row[] = 'Rp ' . number_format($item->tun_anak ?? 0, 0, ',', '.'); // 12. Tunj Anak
            $row[] = 'Rp ' . number_format($item->kehormatan ?? 0, 0, ',', '.'); // 13. Kehormatan
            $row[] = 'Rp ' . number_format($item->walkes ?? 0, 0, ',', '.'); // 14. Walkes
            $row[] = 'Rp ' . number_format($item->potongan ?? 0, 0, ',', '.'); // 15. Potongan
            $row[] = 'Rp ' . number_format($item->diterima ?? 0, 0, ',', '.'); // 16. Diterima
            
            // 17. Tgl Kirim
            $tgl_kirim = '-';
            if (!empty($item->tgl_kirim) && $item->tgl_kirim != '0000-00-00 00:00:00') {
                $tgl_kirim = date('d/m/Y H:i', strtotime($item->tgl_kirim));
            }
            $row[] = $tgl_kirim;

            $data[] = $row;
        }

        $output = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Dashboard_pengajar_model->count_all($bulan, $tahun, $lembaga),
            "recordsFiltered" => $this->Dashboard_pengajar_model->count_filtered($bulan, $tahun, $lembaga),
            "data" => $data
        ];

        echo json_encode($output);
    }

    // Export to Excel
    public function export_excel()
    {
        $bulan = $this->input->get('bulan');
        $tahun = $this->input->get('tahun');
        $lembaga = $this->input->get('lembaga');

        $data = $this->Dashboard_pengajar_model->get_all_data($bulan, $tahun, $lembaga);

        // Load PhpSpreadsheet
        require_once APPPATH . 'third_party/PhpSpreadsheet/vendor/autoload.php';

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set Headers
        $headers = ['No', 'Bulan', 'Tahun', 'NIK', 'Nama', 'Lembaga', 'Kategori', 
                   'Mengajar', 'DTY', 'Jml Hadir', 'Kehadiran', 'Tunkel', 
                   'Tunj Anak', 'Kehormatan', 'Walkes', 'Potongan', 'Diterima', 'Tgl Kirim'];
        
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }

        // Fill Data
        $row = 2;
        $no = 1;
        foreach ($data as $item) {
            $total_kehadiran = ($item->nominal_kehadiran ?? 0) + 
                             ($item->nominal_hadir_15 ?? 0) + 
                             ($item->nominal_hadir_10 ?? 0) + 
                             ($item->barokah_piket ?? 0);
            
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item->bulan);
            $sheet->setCellValue('C' . $row, $item->tahun);
            $sheet->setCellValue('D' . $row, $item->nik);
            $sheet->setCellValue('E' . $row, $item->nama_lengkap);
            $sheet->setCellValue('F' . $row, $item->nama_lembaga);
            $sheet->setCellValue('G' . $row, $item->kategori);
            $sheet->setCellValue('H' . $row, $item->mengajar ?? 0);
            $sheet->setCellValue('I' . $row, $item->dty ?? 0);
            $sheet->setCellValue('J' . $row, $item->jumlah_hadir ?? 0);
            $sheet->setCellValue('K' . $row, $total_kehadiran);
            $sheet->setCellValue('L' . $row, $item->tunkel ?? 0);
            $sheet->setCellValue('M' . $row, $item->tun_anak ?? 0);
            $sheet->setCellValue('N' . $row, $item->kehormatan ?? 0);
            $sheet->setCellValue('O' . $row, $item->walkes ?? 0);
            $sheet->setCellValue('P' . $row, $item->potongan ?? 0);
            $sheet->setCellValue('Q' . $row, $item->diterima ?? 0);
            $sheet->setCellValue('R' . $row, $item->tgl_kirim ?? '-');
            $row++;
        }

        // Set Auto Size
        foreach (range('A', 'R') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Output
        $filename = 'Barokah_Pengajar_' . $bulan . '_' . str_replace('/', '-', $tahun) . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
    }
}
