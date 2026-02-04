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
      if ($datanya->status == "selesai"){
        $row[] = "
          <span class='text-success'>Sudah ditransfer</span>
          <a href='".site_url('walog/index?id='.$datanya->id_kehadiran_lembaga)."' 
            target='_blank' 
            class='badge bg-info ms-1'>
            <i class='bi bi-whatsapp'></i> Log WA
          </a>";

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

    
    $update_data = array();
		
    foreach($id_kehadiran AS $key => $val){
      $update_data[] = array(
       "id_kehadiran" => $id_kehadiran[$key],
       'status'        => 'selesai'
      );
    }

    //update tabel total_barokah
    $this->db->update_batch('total_barokah', $update_data, 'id_kehadiran' );

    

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
        'no_hp'         => $nomor_pengirim[$key],
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
			$html_item .= '<tr>';
				$html_item .= '<td><h6>'.$no++.'</h6></td>';
				$html_item .= '<td><h6>'.$sow->gelar_depan.' '.$sow->nama_lengkap.' '.$sow->gelar_belakang.'</h6></td>';
				$html_item .= '<td><h6>'.$sow->nama_bank.'</h6> <input type="text" name="id_total[]" value="'.$sow->id_total.'" >
        <input type="text" name="id_kehadiran_lembaga_log[]" value="'.$sow->id_kehadiran_lembaga.'" ><input type="hidden" name="kategori[]" value="'.$sow->kategori.'" >
        <input type="hidden" name="bulan[]" value="'.$sow->bulan.'" ><input type="hidden" name="tahun[]" value="'.$sow->tahun.'" ></td>';
        $html_item .= '<td><h6> '.$sow->no_rekening.'</h6><input type="hidden" name="nama_bank[]" value="'.$sow->nama_bank.'" ><input type="hidden" name="nomor_hp[]" value="'.$sow->nomor_hp.'" ><input type="hidden" name="norek[]" value="'.$sow->no_rekening.'" ></td>';
			 $html_item .= '<td><h6>'.$sow->atas_nama.'</h6><input type="hidden" name="nama_lengkap[]" value="'.$sow->gelar_depan.' '.$sow->nama_lengkap.' '.$sow->gelar_belakang.'"><input type="hidden" name="nama_lembaga[]" value="'.$sow->nama_lembaga.'" ></td>';
        $html_item .= '<td><h6>'.$sow->diterima.'</h6><input type="hidden" name="diterima[]" value="'.rupiah($sow->diterima).'" ></td>';
        if ($sow->nama_bank == "Bank Mandiri") {
            $html_item .= '<td><h6>' . ($sow->diterima - 2500) . '</h6></td>';
             $html_item .= '<td><h6>';
            $html_item .= 'BMRIIDJA';
            $html_item .= '<td><h6>'."REFF-BIF".$sow->id_total.'</h6></td>';
            $html_item .= '<td>BIF</td>';
        } else if ($sow->nama_bank == "Bank BCA") {
             $html_item .= '<td><h6>' . ($sow->diterima - 2500) . '</td>';
             $html_item .= '<td><h6>';
            $html_item .= 'CENAIDJA';
            $html_item .= '<td><h6>'."REFF-BIF".$sow->id_total.'</h6></td>';
             $html_item .= '<td>BIF</td>';
        } else if ($sow->nama_bank == "Bank Jatim") {
             $html_item .= '<td><h6>' . ($sow->diterima - 2500) . '</td>';
             $html_item .= '<td><h6>';
            $html_item .= 'PDJTIDJ1';
            $html_item .= '<td><h6>'."REFF-BIF".$sow->id_total.'</h6></td>';
            $html_item .= '<td>BIF</td>';
        } else if ($sow->nama_bank == "Bank Muamalat") {
             $html_item .= '<td><h6>' . ($sow->diterima - 2500) . '</td>';
             $html_item .= '<td><h6>';
            $html_item .= 'MUABIDJA'; 
            $html_item .= '<td><h6>'."REFF-BIF".$sow->id_total.'</h6></td>';
            $html_item .= '<td>BIF</td>';
        } else if ($sow->nama_bank == "Bank BTN") {
            $html_item .= '<td><h6>' . ($sow->diterima - 2500) . '</td>';
            $html_item .= '<td><h6>';
            $html_item .= 'BTANIDJA'; 
            $html_item .= '<td><h6>'."REFF-BIF".$sow->id_total.'</h6></td>';
            $html_item .= '<td>BIF</td>';
        } else if ($sow->nama_bank == "Bank CIMB") {
            $html_item .= '<td><h6>' . ($sow->diterima - 2500) . '</td>';
             $html_item .= '<td><h6>';
            $html_item .= 'BNIAIDJA'; 
            $html_item .= '<td><h6>'."REFF-BIF".$sow->id_total.'</h6></td>';
            $html_item .= '<td>BIF</td>';
        } else if ($sow->nama_bank == "Bank BNI") {
            $html_item .= '<td><h6>' . ($sow->diterima - 2500) . '</h6></td>';
             $html_item .= '<td><h6>';
            $html_item .= 'BNINIDJA'; 
            $html_item .= '<td><h6>'."REFF-BIF".$sow->id_total.'</h6></td>';
            $html_item .= '<td>BIF</td>';
        } else if ($sow->nama_bank == "Bank Syari'ah Indonesia") {
            $html_item .= '<td><h6>' . ($sow->diterima - 2500) . '</h6></td>';
             $html_item .= '<td><h6>';
            $html_item .= 'BSMDIDJA'; 
            $html_item .= '<td><h6>'."REFF-BIF".$sow->id_total.'</h6></td>';
            $html_item .= '<td>BIF</td>';
        } else {
            $html_item .= '<td><h6>' . ($sow->diterima - 1000) . '</h6></td>';
             $html_item .= '<td><h6>';
            $html_item .= 'BRINIDJA';
            $html_item .= '<td><h6>'."REFF-IFT".$sow->id_total.'</h6></td>';
            $html_item .= '<td>IFT</td>';
        }
        $html_item .= '</h6></td>';
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

    public function export($id){
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
   


}
