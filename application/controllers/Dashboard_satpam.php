<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_satpam extends CI_Controller {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Dashboard_satpam_model');
        $this->load->model('Login_model');
        $this->load->helper('Rupiah_helper');
    }

    public function index()
    {
        $this->Login_model->getsqurity();
        
        // Get latest periode for default filter values
        $latest = $this->Dashboard_satpam_model->get_latest_periode();
        
        $isi['css']     = 'Dashboard_satpam/Css';
        $isi['content'] = 'Dashboard_satpam/index';
        $isi['ajax']    = 'Dashboard_satpam/Ajax';
        $isi['default_bulan'] = $latest->bulan;
        $isi['default_tahun'] = $latest->tahun;
        $this->load->view('Template', $isi);
    }

    // AJAX: Get summary data for cards
    public function get_summary()
    {
        $bulan = $this->input->post('bulan');
        $tahun = $this->input->post('tahun');

        $data = $this->Dashboard_satpam_model->get_summary_data($bulan, $tahun);
        
        echo json_encode($data);
    }

    // AJAX: Get chart data
    public function get_chart_data()
    {
        $type = $this->input->post('type');
        $bulan = $this->input->post('bulan');
        $tahun = $this->input->post('tahun');

        $data = [];

        switch ($type) {
            case 'donut':
            case 'komponen':
                $data = $this->Dashboard_satpam_model->get_breakdown_komponen($bulan, $tahun);
                break;
            case 'kehadiran':
            case 'bar':
                $data = $this->Dashboard_satpam_model->get_statistik_kehadiran($bulan, $tahun);
                break;
            case 'trend':
                $data = $this->Dashboard_satpam_model->get_trend_4_bulan($bulan, $tahun);
                break;
        }

        echo json_encode($data);
    }

    // AJAX: Get table data for DataTables
    public function get_table_data()
    {
        $bulan = $this->input->post('bulan');
        $tahun = $this->input->post('tahun');

        $list = $this->Dashboard_satpam_model->get_datatables($bulan, $tahun);
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
            $row[] = ($item->jumlah_hari ?? 0) . ' hari'; // 5. Jml Hari
            $row[] = 'Rp ' . number_format($item->nominal_transport ?? 0, 0, ',', '.'); // 6. Transport
            $row[] = ($item->jumlah_shift ?? 0); // 7. Jml Shift
            $row[] = ($item->jumlah_dinihari ?? 0); // 8. Jml Dinihari
            $row[] = 'Rp ' . number_format($item->konsumsi ?? 0, 0, ',', '.'); // 9. Konsumsi
            $row[] = ($item->rank ?? 0); // 10. Rank
            $row[] = 'Rp ' . number_format($item->jumlah_barokah ?? 0, 0, ',', '.'); // 11. Jml Barokah
            $row[] = 'Rp ' . number_format($item->diterima ?? 0, 0, ',', '.'); // 12. Diterima
            
            // 13. Tgl Kirim
            $tgl_kirim = '-';
            if (!empty($item->tgl_kirim) && $item->tgl_kirim != '0000-00-00 00:00:00') {
                $tgl_kirim = date('d/m/Y H:i', strtotime($item->tgl_kirim));
            }
            $row[] = $tgl_kirim;

            $data[] = $row;
        }

        $output = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Dashboard_satpam_model->count_all($bulan, $tahun),
            "recordsFiltered" => $this->Dashboard_satpam_model->count_filtered($bulan, $tahun),
            "data" => $data
        ];

        echo json_encode($output);
    }

    // Export to Excel
    public function export_excel()
    {
        $bulan = $this->input->get('bulan');
        $tahun = $this->input->get('tahun');

        $data = $this->Dashboard_satpam_model->get_all_data($bulan, $tahun);

        // Load PhpSpreadsheet
        require_once APPPATH . 'third_party/PhpSpreadsheet/vendor/autoload.php';

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set Headers
        $headers = ['No', 'Bulan', 'Tahun', 'NIK', 'Nama', 'Jml Hari', 'Transport', 
                   'Jml Shift', 'Jml Dinihari', 'Konsumsi', 'Rank', 'Jml Barokah', 'Diterima', 'Tgl Kirim'];
        
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }

        // Fill Data
        $row = 2;
        $no = 1;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $item->bulan);
            $sheet->setCellValue('C' . $row, $item->tahun);
            $sheet->setCellValue('D' . $row, $item->nik);
            $sheet->setCellValue('E' . $row, $item->nama_lengkap);
            $sheet->setCellValue('F' . $row, $item->jumlah_hari ?? 0);
            $sheet->setCellValue('G' . $row, $item->nominal_transport ?? 0);
            $sheet->setCellValue('H' . $row, $item->jumlah_shift ?? 0);
            $sheet->setCellValue('I' . $row, $item->jumlah_dinihari ?? 0);
            $sheet->setCellValue('J' . $row, $item->konsumsi ?? 0);
            $sheet->setCellValue('K' . $row, $item->rank ?? 0);
            $sheet->setCellValue('L' . $row, $item->jumlah_barokah ?? 0);
            $sheet->setCellValue('M' . $row, $item->diterima ?? 0);
            $sheet->setCellValue('N' . $row, $item->tgl_kirim ?? '-');
            $row++;
        }

        // Set Auto Size
        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Output
        $filename = 'Barokah_Satpam_' . $bulan . '_' . str_replace('/', '-', $tahun) . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
    }
}
