<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// Include librari PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Payroll extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Login_model');
		$this->load->model('Payroll_model');
        $this->load->helper('Rupiah_helper');
	}

	public function index(){
		$this->Login_model->getsqurity();
		if ($this->session->userdata('jabatan') == 'AdminLembaga' or $this->session->userdata('jabatan') == 'umana' ){
			$this->load->view('Error');
		} else {
    		$isi['css'] 	= 'Payroll/Css';
    		$isi['content'] = 'Payroll/Payroll';
    		$isi['ajax'] 	= 'Payroll/Ajax';
    		$this->load->view('Template',$isi);
		}
	}

  public function log_wa(){
		$this->Login_model->getsqurity();
		if ($this->session->userdata('jabatan') == 'AdminLembaga' or $this->session->userdata('jabatan') == 'umana' ){
			$this->load->view('Error');
		} else {
    		$isi['content'] = '';
    		$this->load->view('Log_wa/Log_wa',$isi);
		}
	}

	
	public function data_list()
	{
		$this->load->helper('url');

		$list = $this->Payroll_model->get_datatables();
		$no =1;
		$data = array();
		foreach ($list as $datanya) {
			
			$row = array();
			$row[] = $no++;
			$row[] = htmlentities($datanya->nama_lembaga);
			$row[] = htmlentities($datanya->kategori);
      $row[] = htmlentities($datanya->bulan." ".$datanya->tahun);
      $row[] = "<span class='text-success'>".rupiah($datanya->jumlah_total)."</span>";
      
      // OPTIMIZED: Menggunakan data dari JOIN, tidak perlu query lagi
      // Data sudah ada di $datanya->success_count, pending_count, failed_count
      $badge_html = '';
      if ($datanya->success_count > 0) {
          $badge_html .= "<span class='badge badge-success me-1' title='Berhasil terkirim'><i class='fa fa-check'></i> {$datanya->success_count}</span>";
      }
      if ($datanya->pending_count > 0) {
          $badge_html .= "<span class='badge badge-warning me-1' title='Menunggu'><i class='fa fa-clock'></i> {$datanya->pending_count}</span>";
      }
      if ($datanya->failed_count > 0) {
          $badge_html .= "<span class='badge badge-danger me-1' title='Gagal terkirim'><i class='fa fa-times'></i> {$datanya->failed_count}</span>";
      }
      $row[] = $badge_html ?: "<span class='text-muted'>-</span>";
      
      if ($datanya->status == "selesai"){
        $row[] = "
            <span class='text-success'>Sudah ditransfer</span>
            <a href='javascript:void(0)' 
              class='badge bg-info' 
              onclick='showWaLog(" . json_encode($datanya->id_kehadiran_lembaga) . ", " . json_encode($datanya->bulan) . ", " . json_encode($datanya->tahun) . ")'>
                <i class='bi bi-whatsapp'></i> Log WA
            </a>
        ";

      $row[] = '
          <a type="button" class="btn btn-primary btn-sm" 
            onclick="selesai('."'".$datanya->id_kehadiran_lembaga."'".')""
            title="Track">
            <i class="fas fa-eye mr-1"></i> Lihat Rincian
          </a>';
      } else {
        $row[] = "<span class='text-danger'> Belum ditransfer</span>";
        $row[] = ' <a type="button" class="btn btn-outline-primary btn-sm" onclick="edit_stts('."'".$datanya->id_kehadiran_lembaga."'".')""
			title="Track" ><i class="fas fa-check mr-1" ></i> Sudah Transfer</a>';
      }
      
			//add html for action
			
			// onclick="rincian('."'".$datanya->id_kehadiran_lembaga."'".')"
		$data[] = $row;
		}
			$output = array("data" => $data);
		echo json_encode($output);
	}

  public function save()
	{
    $id_kehadiran = $this->input->post('id_total');

    $data2 = array(
			'status' => "selesai"
		  );
    
      // update tabel kehadiran lembaga
		$this->db->where('id_kehadiran_lembaga', $this->input->post('id_kehadiran_lembaga'));
		$this->db->update('kehadiran_lembaga', $data2);

    
      // Ambil kategori lembaga
      $qCat = $this->db->select('kategori')
                       ->where('id_kehadiran_lembaga', $this->input->post('id_kehadiran_lembaga'))
                       ->get('kehadiran_lembaga')
                       ->row();
      $kategori = $qCat ? $qCat->kategori : 'Struktural';

      // Tentukan tabel & PK
      $tabel_target = 'total_barokah';
      $pk_target    = 'id_total_barokah'; // Default Struktural

      if ($kategori == 'Satpam') {
          $tabel_target = 'total_barokah_satpam';
          $pk_target    = 'id_total_barokah_satpam';
      } elseif ($kategori == 'Pengajar') {
          $tabel_target = 'total_barokah_pengajar';
          $pk_target    = 'id_total_barokah_pengajar';
      }

      $update_data = array();
		
      foreach($id_kehadiran AS $key => $val){
        $update_data[] = array(
         $pk_target => $val,
         'status'   => 'selesai'
        );
      }

      //update tabel sesuai kategori
      if (!empty($update_data)) {
          $this->db->update_batch($tabel_target, $update_data, $pk_target);
      }

    

    $timezone       = time() + (60 * 60 * 7);
	  $jam            = gmdate('Y-m-d', $timezone);
	  $nomor_pengirim = $this->input->post('nomor_hp');
    $periode        = $this->input->post('periode');
    $nama_lengkap   = $this->input->post('nama_lengkap');
    $lembaga        = $this->input->post('nama_lembaga');
    $jumlah         = $this->input->post('diterima');
    $norek          = $this->input->post('norek');
    $nama_bank      = $this->input->post('nama_bank');
    $bulan          = $this->input->post('bulan');
    $tahun          = $this->input->post('tahun');

    
    $isipesan = array();

    foreach($nama_lengkap as $key => $val) {
    $isipesan[$key] = array(
        'title'         => 'Transfer Barokah Berhasil',
        'nomor_hp'         => $nomor_pengirim[$key],
        'periode'       => $periode,
        'nama_lengkap'  => $nama_lengkap[$key],
        'lembaga'       => $lembaga[$key],
        'jumlah'        => $jumlah[$key],
        'norek'         => $norek[$key],
        'nama_bank'     => $nama_bank[$key],
        'waktu'         => $jam,
        // Kolom tambahan untuk wa_log
        'bulan'         	=> $this->input->post('bulan')[$key],   
        'tahun'        	 	=> $this->input->post('tahun')[$key],   
        'id_kehadiran_lembaga'        	 	=> $this->input->post('id_kehadiran_lembaga_log')[$key],   
        'kategori'      	=> $this->input->post('kategori')[$key],
        'nama_umana'    => $this->input->post('nama_lengkap')[$key] ?? null,
        'id_total_barokah' => $id_kehadiran[$key] ?? null
    );

    // print_r($isipesan[$key]);
    $send_pengirim = $this->Payroll_model->kirimwa($isipesan[$key]);
}

    echo json_encode(array("status" => TRUE));
	}

	public function get_byid($id)
	{
		$this->load->helper('url');
		$data_elemen = $this->Payroll_model->get_lembaga($id);
		$list = $this->Payroll_model->get_datatables_rincian($id);
		$data = array();
        $no=1;
		$html_item = '';
		foreach ($list as $sow) {
            // Logika admin fee
            $isAdminLow = true; 
            // Cek bank yang kena 2500 (BIF)
            $banks2500 = ["Bank Mandiri", "Bank BCA", "Bank Jatim", "Bank Muamalat", "Bank BTN", "Bank CIMB", "Bank BNI"];
            
            $nominal = $sow->diterima;
            $adminFee = 1000;
            $biFastCode = "BRINIDJA";
            $type = "IFT";
            $ref = "REFF-IFT".$sow->id_total;

            if (in_array($sow->nama_bank, $banks2500)) {
                $adminFee = 2500;
                $ref = "REFF-BIF".$sow->id_total;
                $type = "BIF";
                
                // Set BiFast Code per bank
                switch ($sow->nama_bank) {
                    case "Bank Mandiri": $biFastCode = "BMRIIDJA"; break;
                    case "Bank BCA":     $biFastCode = "CENAIDJA"; break;
                    case "Bank Jatim":   $biFastCode = "PDJTIDJ1"; break;
                    case "Bank Muamalat":$biFastCode = "MUABIDJA"; break;
                    case "Bank BTN":     $biFastCode = "BTANIDJA"; break;
                    case "Bank CIMB":    $biFastCode = "BNIAIDJA"; break;
                    case "Bank BNI":     $biFastCode = "BNINIDJA"; break;
                    case "Bank Syari'ah Indonesia": $biFastCode = "BSMDIDJA"; break;
                }
            }

            $netAmount = $nominal - $adminFee;

			$html_item .= '<tr>';
				$html_item .= '<td>'.$no++.'</td>';
				$html_item .= '<td>'.$sow->gelar_depan.' '.$sow->nama_lengkap.' '.$sow->gelar_belakang.'</td>';
				$html_item .= '<td>'.$sow->nama_bank.' <input type="hidden" name="id_total[]" value="'.$sow->id_total.'" >
                    <input type="hidden" name="id_kehadiran_lembaga_log[]" value="'.$sow->id_kehadiran_lembaga.'" ><input type="hidden" name="kategori[]" value="'.$sow->kategori.'" >
                    <input type="hidden" name="bulan[]" value="'.$sow->bulan.'" ><input type="hidden" name="tahun[]" value="'.$sow->tahun.'" ></td>';
                $html_item .= '<td>'.$sow->no_rekening.'<input type="hidden" name="nama_bank[]" value="'.$sow->nama_bank.'" ><input type="hidden" name="nomor_hp[]" value="'.$sow->nomor_hp.'" ><input type="hidden" name="norek[]" value="'.$sow->no_rekening.'" ></td>';
			    $html_item .= '<td>'.$sow->atas_nama.'<input type="hidden" name="nama_lengkap[]" value="'.$sow->gelar_depan.' '.$sow->nama_lengkap.' '.$sow->gelar_belakang.'"><input type="hidden" name="nama_lembaga[]" value="'.$sow->nama_lembaga.'" ></td>';
                
                // Nominal Asli (Reverted to plain number)
                $html_item .= '<td>'.$nominal.'<input type="hidden" name="diterima[]" value="'.$nominal.'" ></td>';
                
                // Net Amount After Admin (Formatted)
                $html_item .= '<td>' . $netAmount . '</td>';
                
                // BI-Fast Info
                $html_item .= '<td>'.$biFastCode.'</td>';
                $html_item .= '<td>'.$ref.'</td>';
                $html_item .= '<td>'.$type.'</td>';

            $html_item .= '<td>'.$sow->nama_lembaga.' - '.$sow->kategori.'</td>';
            $html_item .= '</tr>';
		}
		$this->output->set_output(json_encode(array("data_elemen" => $data_elemen, "html_item" => $html_item)));
	}

	public function rincian($id)
	{
		// $data['data'] = $this->Payroll_model->get_datatables_rincian($id);
		// echo json_encode($data); // kirim data sebagai JSON
		$list = $this->Payroll_model->get_datatables_rincian($id);
		$this->Login_model->getsqurity() ;
        
		$isi['css'] 	= 'Laporan/Css';
		$isi['content'] = 'Laporan/Rincian_kehadiran';
		$isi['ajax'] 	= 'Laporan/Ajax';
		// $isi['isitunkel']  = $tunkel_get;
		$isi['isilist']  = $list;
		$this->load->view('Template',$isi);
	}

    public function export($id)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $ambil_data = $this->Payroll_model->get_datatables_rincian($id);
        foreach($ambil_data as $dataawal);
        // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
        $style_col = [
          'font' => ['bold' => true], // Set font nya jadi bold
          'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
          ],
          'borders' => [
            'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
            'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
            'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
            'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
          ]
        ];
        // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
        $style_row = [
          'alignment' => [
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
          ],
          'borders' => [
            'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
            'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
            'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
            'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
          ]
        ];
        $sheet->setCellValue('A1', "Data Rekap Jumlah Barokah  $dataawal->nama_lembaga"); // Set kolom A1 dengan tulisan "DATA SISWA"
        $sheet->mergeCells('A1:E1'); // Set Merge Cell pada kolom A1 sampai E1
        $sheet->getStyle('A1')->getFont()->setBold(true); // Set bold kolom A1
        // Buat header tabel nya pada baris ke 3
        $sheet->setCellValue('A3', "NO"); // Set kolom A3 dengan tulisan "NO"
        $sheet->setCellValue('B3', "NAMA UMANA"); // Set kolom B3 dengan tulisan "NIS"
        $sheet->setCellValue('C3', "NAMA BANK"); // Set kolom C3 dengan tulisan "NAMA"
        $sheet->setCellValue('D3', "NOMOR REKENING"); // Set kolom D3 dengan tulisan "JENIS KELAMIN"
        $sheet->setCellValue('E3', "ATAS NAMA"); // Set kolom E3 dengan tulisan "ALAMAT"
        $sheet->setCellValue('F3', "JUMLAH"); // Set kolom E3 dengan tulisan "ALAMAT"
        // Apply style header yang telah kita buat tadi ke masing-masing kolom header
        $sheet->getStyle('A3')->applyFromArray($style_col);
        $sheet->getStyle('B3')->applyFromArray($style_col);
        $sheet->getStyle('C3')->applyFromArray($style_col);
        $sheet->getStyle('D3')->applyFromArray($style_col);
        $sheet->getStyle('E3')->applyFromArray($style_col);
        $sheet->getStyle('F3')->applyFromArray($style_col);
        // Panggil function view yang ada di SiswaModel untuk menampilkan semua data siswanya
        
        $no = 1; // Untuk penomoran tabel, di awal set dengan 1
        $numrow = 4; // Set baris pertama untuk isi tabel adalah baris ke 4
        foreach($ambil_data as $data){ // Lakukan looping pada variabel siswa
          $sheet->setCellValue('A'.$numrow, $no);
          $sheet->setCellValue('B'.$numrow, $data->nama_lengkap);
          $sheet->setCellValue('C'.$numrow, $data->nama_bank);
          $sheet->setCellValue('D'.$numrow, "'".$data->no_rekening);
          $sheet->setCellValue('E'.$numrow, $data->atas_nama);
          $sheet->setCellValue('F'.$numrow, $data->diterima);
          // Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
          $sheet->getStyle('A'.$numrow)->applyFromArray($style_row);
          $sheet->getStyle('B'.$numrow)->applyFromArray($style_row);
          $sheet->getStyle('C'.$numrow)->applyFromArray($style_row);
          $sheet->getStyle('D'.$numrow)->applyFromArray($style_row);
          $sheet->getStyle('E'.$numrow)->applyFromArray($style_row);
          $sheet->getStyle('F'.$numrow)->applyFromArray($style_row); 
          $no++; // Tambah 1 setiap kali looping
          $numrow++; // Tambah 1 setiap kali looping
        }
        // Set width kolom
        $sheet->getColumnDimension('A')->setWidth(5); // Set width kolom A
        $sheet->getColumnDimension('B')->setWidth(15); // Set width kolom B
        $sheet->getColumnDimension('C')->setWidth(25); // Set width kolom C
        $sheet->getColumnDimension('D')->setWidth(20); // Set width kolom D
        $sheet->getColumnDimension('E')->setWidth(30); // Set width kolom E
        $sheet->getColumnDimension('F')->setWidth(30); // Set width kolom E
        $sheet->setCellValue('E'.$numrow, 'Total');
        $sheet->getStyle('E'.$numrow)->applyFromArray($style_row);
        $sheet->setCellValue('F'.$numrow, $data->jumlah_total);
        $sheet->getStyle('F'.$numrow)->applyFromArray($style_row);
        // $sheet->getStyle('F'.$numrow)->applyFromArray($style_row);
        // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
        $sheet->getDefaultRowDimension()->setRowHeight(-1);
        // Set orientasi kertas jadi LANDSCAPE
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        // Set judul file excel nya
        $sheet->setTitle("Barokah ");
        // Proses file excel
        $nama_file = $dataawal->nama_lembaga." ".$dataawal->bulan." ".$dataawal->tahun;
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename={$nama_file}.xlsx"); // Set nama file excel nya
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }

    public function get_wa_log()
{
    $id_lembaga = $this->input->get('id_lembaga');
    $bulan = $this->input->get('bulan');
    $tahun = $this->input->get('tahun');

    // Ambil semua id_total_barokah milik lembaga+periode ini
    $idList = [];
    $q1 = $this->db->select('*, id_total_barokah AS id')
        ->from('wa_log')
        ->join('kehadiran_lembaga', 'kehadiran_lembaga.id_kehadiran_lembaga = wa_log.id_kehadiran_lembaga')
        ->where('wa_log.id_kehadiran_lembaga', $id_lembaga)
        // ->where('total_barokah_satpam.bulan', $bulan)
        // ->where('total_barokah_satpam.tahun', $tahun)
        ->get()->result();
    foreach ($q1 as $r) $idList[] = $r->id;

    $q2 = $this->db->select('*, id_total_barokah AS id')
        ->from('wa_log')
        ->join('kehadiran_lembaga', 'kehadiran_lembaga.id_kehadiran_lembaga = wa_log.id_kehadiran_lembaga')
        ->where('wa_log.id_kehadiran_lembaga', $id_lembaga)
        // ->where('total_barokah_pengajar.bulan', $bulan)
        // ->where('total_barokah_pengajar.tahun', $tahun)
        ->get()->result();
    foreach ($q2 as $r) $idList[] = $r->id;

    $q3 = $this->db->select('id_total_barokah AS id')
        ->from('total_barokah')
        ->join('kehadiran_lembaga', 'kehadiran_lembaga.id_kehadiran_lembaga = total_barokah.id_kehadiran')
        ->where('kehadiran_lembaga.id_lembaga', $id_lembaga)
        // ->where('total_barokah.bulan', $bulan)
        // ->where('total_barokah.tahun', $tahun)
        ->get()->result();
    foreach ($q3 as $r) $idList[] = $r->id;

    if (empty($idList)) {
        echo json_encode([]);
        return;
    }

    $logs = $this->db
        ->where_in('id_total_barokah', $idList)
        ->order_by('status', 'DESC')
        ->get('wa_log')
        ->result();

    echo json_encode($logs);
}

public function resend_wa($id)
{
    $log = $this->db->where('id_wa_log', $id)->get('wa_log')->row();
    if (!$log) {
        echo json_encode(['status' => false, 'message' => 'Data tidak ditemukan']);
        return;
    }

    // Kirim ulang ke nomor ujicoba tunggal (mode perbaikan)
    //jika develop aktifkan kode dibawah, jika sudah production matikan kode dibawah
    // $log->nomor_hp = '6281249057246';  // contoh 1 nomor tetap

    $payload = [
        'title' => 'Transfer Barokah Berhasil',
        'periode' => $log->bulan.' '.$log->tahun,
        'nama_lengkap' => $log->nama_penerima,
        'lembaga' => $log->nama_lembaga,
        'jumlah' => $log->jumlah,
        'nama_bank' => $log->nama_bank,
        'norek' => $log->nomor_rekening,
        'waktu' => date('Y-m-d'), // Tanggal sekarang untuk resend
        'nomor_hp' => $log->nomor_hp,
        'id_total_barokah' => $log->id_total_barokah,
        'kategori' => $log->kategori,
        'id_wa_log' => $log->id_wa_log
    ];

    $res = $this->Payroll_model->kirimWaUlang($payload);
    echo json_encode(['status' => true, 'message' => 'Pesan dikirim ulang', 'response' => $res]);
}

public function bulk_resend_failed()
{
    $id_lembaga = $this->input->post('id_lembaga');
    $bulan = $this->input->post('bulan');
    $tahun = $this->input->post('tahun');
    
    // Get all failed WA logs for this lembaga
    $idList = [];
    
    // Query dari get_wa_log, tapi filtered by status = 'failed'
    $q1 = $this->db->select('*, id_total_barokah AS id')
        ->from('wa_log')
        ->join('kehadiran_lembaga', 'kehadiran_lembaga.id_kehadiran_lembaga = wa_log.id_kehadiran_lembaga')
        ->where('wa_log.id_kehadiran_lembaga', $id_lembaga)
        ->where('wa_log.status', 'failed')
        ->get()->result();
    
    foreach ($q1 as $r) $idList[] = $r->id;
    
    if (empty($idList)) {
        echo json_encode(['status' => false, 'message' => 'Tidak ada WA yang failed']);
        return;
    }
    
    // Get detail logs
    $logs = $this->db
        ->where_in('id_total_barokah', $idList)
        ->where('status', 'failed')
        ->get('wa_log')
        ->result();
    
    // Kirim semua failed logs
    $success_count = 0;
    $failed_count = 0;
    
    foreach ($logs as $log) {
        $payload = [
            'title' => 'Transfer Barokah Berhasil',
            'periode' => $log->bulan.' '.$log->tahun,
            'nama_lengkap' => $log->nama_penerima,
            'lembaga' => $log->nama_lembaga,
            'jumlah' => $log->jumlah,
            'nama_bank' => $log->nama_bank,
            'norek' => $log->nomor_rekening,
            'waktu' => date('Y-m-d'),
            'nomor_hp' => $log->nomor_hp,
            'id_total_barokah' => $log->id_total_barokah,
            'kategori' => $log->kategori,
            'id_wa_log' => $log->id_wa_log
        ];
        
        $res = $this->Payroll_model->kirimWaUlang($payload);
        $response = json_decode($res, true);
        
        if (isset($response['status']) && $response['status'] == true) {
            $success_count++;
        } else {
            $failed_count++;
        }
    }
    
    echo json_encode([
        'status' => true, 
        'message' => "Resend selesai: {$success_count} berhasil, {$failed_count} gagal",
        'success_count' => $success_count,
        'failed_count' => $failed_count,
        'total' => count($logs)
    ]);
}
   


}
