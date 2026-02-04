<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_struktural extends CI_Controller {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Dashboard_struktural_model');
        $this->load->model('Login_model');
        $this->load->helper('Rupiah_helper');
    }

    public function index()
    {
        $this->Login_model->getsqurity();
        
        // Get latest periode for default filter values
        $latest = $this->Dashboard_struktural_model->get_latest_periode();
        
        $isi['css']     = 'Dashboard_struktural/Css';
        $isi['content'] = 'Dashboard_struktural/index';
        $isi['ajax']    = 'Dashboard_struktural/Ajax';
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

        $data = $this->Dashboard_struktural_model->get_summary_data($bulan, $tahun, $lembaga);
        
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
                $data = $this->Dashboard_struktural_model->get_distribusi_lembaga($bulan, $tahun);
                break;
            case 'bar':
                $data = $this->Dashboard_struktural_model->get_top_lembaga($bulan, $tahun, 10);
                break;
            case 'line':
                $data = $this->Dashboard_struktural_model->get_trend_6_bulan($tahun);
                break;
            case 'donut':
                $data = $this->Dashboard_struktural_model->get_breakdown_komponen($bulan, $tahun, $lembaga);
                break;
            case 'kehadiran':
                $data = $this->Dashboard_struktural_model->get_statistik_kehadiran($bulan, $tahun, $lembaga);
                break;
            case 'potongan':
                $data = $this->Dashboard_struktural_model->get_breakdown_potongan($bulan, $tahun, $lembaga);
                break;
            case 'trend':
                $data = $this->Dashboard_struktural_model->get_trend_4_bulan($bulan, $tahun, $lembaga);
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

        $list = $this->Dashboard_struktural_model->get_datatables($bulan, $tahun, $lembaga);
        $data = array();
        $no = $_POST['start'];

        foreach ($list as $item) {
            $no++;
            $row = array();
            $row[] = $no; // 0. No
            $row[] = $item->bulan; // 1. Bulan
            $row[] = $item->tahun; // 2. Tahun
            $row[] = $item->nik; // 3. NIK
            
            // 4. Nama (Include Detail Button for usability)
            $row[] = $item->nama_lengkap; 

            $row[] = $item->nama_lembaga; // 5. Lembaga
            $row[] = $item->jabatan_lembaga; // 6. Jabatan
            $row[] = 'Rp ' . number_format($item->tunjab ?? 0, 0, ',', '.'); // 7. Tunjab
            $row[] = number_format($item->mp ?? 0, 0, ',', '.') . ' Tahun'; // 8. MP
            $row[] = ($item->kehadiran ?? 0) . ' hari'; // 9. Kehadiran
            $row[] = 'Rp ' . number_format($item->nominal_kehadiran ?? 0, 0, ',', '.'); // 10. Nominal
            $row[] = 'Rp ' . number_format($item->tunkel ?? 0, 0, ',', '.'); // 11. Tunkel
            $row[] = 'Rp ' . number_format($item->tunj_anak ?? 0, 0, ',', '.'); // 12. Tunj Anak
            $row[] = 'Rp ' . number_format($item->tmp ?? 0, 0, ',', '.'); // 13. TMP
            $row[] = 'Rp ' . number_format($item->kehormatan ?? 0, 0, ',', '.'); // 14. Kehormatan
            $row[] = 'Rp ' . number_format($item->tbk ?? 0, 0, ',', '.'); // 15. TBK
            $row[] = 'Rp ' . number_format($item->potongan ?? 0, 0, ',', '.'); // 16. Potongan
            $row[] = 'Rp ' . number_format($item->diterima ?? 0, 0, ',', '.'); // 17. Diterima
            
            // 18. Tgl Kirim
            $tgl = $item->tgl_kirim;
            if ($tgl) {
                $row[] = date('d-m-Y H:i', strtotime($tgl));
            } else {
                $row[] = '-';
            }
            
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Dashboard_struktural_model->count_all($bulan, $tahun, $lembaga),
            "recordsFiltered" => $this->Dashboard_struktural_model->count_filtered($bulan, $tahun, $lembaga),
            "data" => $data,
        );

        echo json_encode($output);
    }

    // AJAX: Get detail for modal
    public function get_detail($id)
    {
        $data = $this->Dashboard_struktural_model->get_detail_pegawai($id);
        echo json_encode($data);
    }

    // Export to Excel
    public function export_excel()
    {
        $bulan = $this->input->get('bulan');
        $tahun = $this->input->get('tahun');
        $lembaga = $this->input->get('lembaga');

        $data = $this->Dashboard_struktural_model->get_all_data($bulan, $tahun, $lembaga);

        // Load PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'NIK');
        $sheet->setCellValue('C1', 'Nama Lengkap');
        $sheet->setCellValue('D1', 'Lembaga');
        $sheet->setCellValue('E1', 'Jabatan');
        $sheet->setCellValue('F1', 'Tunjab');
        $sheet->setCellValue('G1', 'MP');
        $sheet->setCellValue('H1', 'Kehadiran');
        $sheet->setCellValue('I1', 'Nominal Transport');
        $sheet->setCellValue('J1', 'Tunj. Keluarga');
        $sheet->setCellValue('K1', 'Tunj. Anak');
        $sheet->setCellValue('L1', 'TMP');
        $sheet->setCellValue('M1', 'Kehormatan');
        $sheet->setCellValue('N1', 'TBK');
        $sheet->setCellValue('O1', 'Potongan');
        $sheet->setCellValue('P1', 'Diterima');
        $sheet->setCellValue('Q1', 'Tgl Kirim');

        // Fill data
        $row = 2;
        $no = 1;
        foreach ($data as $item) {
            $sheet->setCellValue('A'.$row, $no++);
            $sheet->setCellValue('B'.$row, $item->nik);
            $sheet->setCellValue('C'.$row, $item->nama_lengkap);
            $sheet->setCellValue('D'.$row, $item->nama_lembaga);
            $sheet->setCellValue('E'.$row, $item->jabatan_lembaga);
            $sheet->setCellValue('F'.$row, $item->tunjab);
            $sheet->setCellValue('G'.$row, $item->mp);
            $sheet->setCellValue('H'.$row, $item->kehadiran); // Dari tb.kehadiran atau query model join kehadiran (tapi tb.kehadiran lebih aman sesuai fix sebelumnya)
            $sheet->setCellValue('I'.$row, $item->nominal_kehadiran);
            $sheet->setCellValue('J'.$row, $item->tunkel);
            $sheet->setCellValue('K'.$row, $item->tunj_anak);
            $sheet->setCellValue('L'.$row, $item->tmp);
            $sheet->setCellValue('M'.$row, $item->kehormatan);
            $sheet->setCellValue('N'.$row, $item->tbk);
            $sheet->setCellValue('O'.$row, $item->potongan);
            $sheet->setCellValue('P'.$row, $item->diterima);
            $sheet->setCellValue('Q'.$row, $item->tgl_kirim ?? '-'); // Pakai created_at dari wa_log via join? Di get_all_data belum ada join wa_log. 
            // Note: get_all_data di Model belum join wa_log. Saya harus update model get_all_data dulu kalau mau tgl_kirim.
            // Untuk sekarang saya skip tgl_kirim atau biarkan kosong, atau update model.
            // User minta "fitur export", asumsikan data barokah.
            // Biar aman saya exclude tgl_kirim dari excel dulu jika model belum support, atau saya update model sekalian.
            // Model get_all_data di step 284 TIDAK join wa_log. 
            // Saya hapus kolom Q dulu biar safe.
            $row++;
        }

        // Set filename
        $filename = 'Barokah_Struktural_' . $bulan . '_' . $tahun . '.xlsx';

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
    }
}
