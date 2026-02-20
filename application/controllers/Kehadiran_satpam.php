<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kehadiran_satpam extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Login_model');
		$this->load->model('kehadiran_satpam_model');
        $this->load->helper('Rupiah_helper');
		$this->load->helper('string');
		$this->load->library('Pdf'); 
	}

	public function index(){
		$this->Login_model->getsqurity() ;
		$isi['css'] 	= 'Kehadiran_satpam/Css';
		$isi['content'] = 'Kehadiran_satpam/Kehadiran_satpam';
		$isi['ajax'] 	= 'Kehadiran_satpam/Ajax';
		$this->load->view('Template',$isi);
	}

	public function data_list()
	{
		$this->load->helper('url');
		$id_lembaga = 59;

		$this->db->select('
			l.nama_lembaga,
			kl.id_kehadiran_lembaga,
			kl.bulan,
			kl.tahun,
			kl.status,
            (SELECT COUNT(*) FROM kehadiran_satpam ks WHERE ks.id_kehadiran_lembaga = kl.id_kehadiran_lembaga) as jumlah_personil
		');
		$this->db->from('kehadiran_lembaga kl');
		$this->db->join('lembaga l', 'kl.id_lembaga = l.id_lembaga');
		$this->db->where('l.id_lembaga', $id_lembaga);
		$this->db->group_by('
			kl.id_kehadiran_lembaga,
			l.nama_lembaga,
			kl.bulan,
			kl.tahun,
			kl.status
		');
		$this->db->order_by('kl.id_kehadiran_lembaga', 'DESC');

		$list = $this->db->get()->result();

		$no =1;
		$data = array();
		foreach ($list as $datanya) {
			$row = array();
			$row[] = $no++;
			$row[] = htmlentities($datanya->nama_lembaga);
			$row[] = $datanya->jumlah_personil . " org";
			$row[] = htmlentities($datanya->bulan);
			$row[] = htmlentities($datanya->tahun);
			// Logic tombol Reset: Hanya untuk Super Admin (Bukan AdminLembaga)
			$btnReset = '';
			if ($this->session->userdata('jabatan') != 'AdminLembaga') {
				$btnReset = '<button type="button" class="btn btn-sm btn-outline-danger ml-2 btn-reset"
							data-id="'.$datanya->id_kehadiran_lembaga.'">
						<i class="fa fa-undo mr-1"></i> Reset
					</button>';
			}

			if ($datanya->status == 'Belum'){
				$row[] = "<span class='badge badge-secondary'>Belum diisi<span class='ms-1 fa fa-times'></span></span>";
				$row[] = '<a type="button" class="btn btn-outline-secondary btn-sm"
							 href="kehadiran_satpam/add_kehadiran/'.$this->encrypt_url($datanya->id_kehadiran_lembaga).'"
							 target="_blank" title="Isi Rekap">
							 <i class="mdi mdi-file-document-box mr-1"></i> Isi Rekap Kehadiran
						  </a>';
			} elseif ($datanya->status == "Sudah") {
				$row[] = "<span class='badge badge-warning text-dark'>Sedang dikoreksi<span class='ms-1 fa fa-redo'></span></span>";
				$row[] = '
					<a type="button" class="btn btn-success btn-xs"
					   href="validasi_satpam/koreksi_satpam/'.$this->encrypt_url($datanya->id_kehadiran_lembaga).'">
					   <i class="mdi mdi-checkbox-marked-circle mr-1"></i> Cek Barokah
					</a>' . $btnReset;
			} elseif ($datanya->status == "Terkirim" || $datanya->status == "acc") {
				$row[] = "<span class='badge badge-warning text-dark'>Sedang dikoreksi<span class='ms-1 fa fa-redo'></span></span>";
				$row[] = '
					<a type="button" class="btn btn-success btn-xs"
					   href="validasi_satpam/koreksi_satpam/'.$this->encrypt_url($datanya->id_kehadiran_lembaga).'">
					   <i class="mdi mdi-checkbox-marked-circle mr-1"></i> Cek Barokah
					</a>';
			} else {
				$row[] = "<span class='badge badge-success'>Sudah transfer<span class='ms-1 fa fa-check'></span></span>";
				$row[] = '
					<a type="button" class="btn btn-info btn-sm"
					   href="Laporan_satpam/rincian/'.$this->encrypt_url($datanya->id_kehadiran_lembaga).'">
					   <i class="mdi mdi-file-document-box mr-1"></i> Lihat Laporan Final
					</a>' . $btnReset;
			}
			
			//add html for action
			
		    $data[] = $row;
		}
		$output = array("data" => $data);
		echo json_encode($output);
	}

	public function blanko_add()
	{
		$id_lembaga = $this->input->post('id_lembaga');
		$bulan      = $this->input->post('bulan');
		$tahun      = $this->input->post('tahun');
		$kategori   = 'Satpam';

		// Validasi wajib (server-side)
		$missing = [];
		if (empty($id_lembaga)) $missing[] = 'id_lembaga';
		if (empty($bulan))      $missing[] = 'bulan';
		if (empty($tahun))      $missing[] = 'tahun';

		if (!empty($missing)) {
			echo json_encode([
				"status" => false,
				"message" => "Form belum lengkap. Mohon isi: ".implode(', ', $missing),
				"inputerror" => $missing,
				"error_string" => array_map(function($x){ return "Wajib diisi"; }, $missing)
			]);
			return;
		}

		// Cek duplikasi
		$this->db->where('id_lembaga', $id_lembaga);
		$this->db->where('bulan', $bulan);
		$this->db->where('tahun', $tahun);
		$this->db->where('kategori', $kategori);
		$existingData = $this->db->get('kehadiran_lembaga')->row();

		if ($existingData) {
			echo json_encode([
				"status"  => false,
				"message" => "Blanko sudah ada untuk periode ini."
			]);
			return;
		}

		// Insert
		$data = [
			'id_kehadiran_lembaga' => '',
			'id_lembaga'           => $id_lembaga,
			'kategori'             => $kategori,
			'bulan'                => $bulan,
			'tahun'                => $tahun,
			'status'               => 'Belum',
		];
		$this->kehadiran_satpam_model->create('kehadiran_lembaga', $data);

		echo json_encode([
			"status"  => true,
			"message" => "Blanko berhasil dibuat."
		]);
	}


	public function add_kehadiran($id){
        $decrypted_id = $this->decrypt_url($id);
		$this->Login_model->getsqurity() ;
		$isi['css'] 	= 'Kehadiran_satpam/Css';
		$isi['content'] = 'Kehadiran_satpam/Add_kehadiran';
		$isi['ajax'] 	= 'Kehadiran_satpam/Ajax';
		$isi['kode'] 	= $decrypted_id;
		$this->load->view('Template',$isi);
		// $this->rekap_list($id);
	}

	 
    public function encrypt_url($string) {
        $key = '874jzceroier38!@#%*bjkdwdw)'; // Ganti dengan kunci enkripsi yang diinginkan
        $encrypted_string = base64_encode($string . $key);
        $encrypted_string = str_replace(array('+', '/', '='), array('-', '_', ''), $encrypted_string);
        return $encrypted_string;
    }
    
    public function decrypt_url($string) {
        $key = '874jzceroier38!@#%*bjkdwdw)'; // Ganti dengan kunci enkripsi yang diinginkan
        $string = str_replace(array('-', '_'), array('+', '/'), $string);
        $string = base64_decode($string);
        $string = str_replace($key, '', $string);
        return $string;
    }

	public function ajax_add_kehadiran()
{
    $id_kehadiran_lembaga = $this->input->post('id_kehadiran_lembaga');
    $bulan  = $this->input->post('bulan');
    $tahun  = $this->input->post('tahun');

    $id_satpam       = $this->input->post('id_satpam');       // array
    $jumlah_hari     = $this->input->post('jumlah_hari');     // array
    $jumlah_shift    = $this->input->post('jumlah_shift');    // array
    $jumlah_dini     = $this->input->post('jumlah_dini');     // array

    // Validasi periode
    if (empty($id_kehadiran_lembaga) || empty($bulan) || empty($tahun)) {
        echo json_encode(["status"=>false,"message"=>"Data periode tidak lengkap."]); return;
    }

    // File wajib
    if (empty($_FILES['file']['name'])) {
        echo json_encode(["status"=>false,"message"=>"File absensi (PDF) wajib diunggah."]); return;
    }

    // Upload PDF
    $uploadedFile = $this->_do_upload(); // pastikan _do_upload() mengarah ke FCPATH.'uploads/', hanya PDF.

    // Susun data batch
    $data = [];
    $n = is_array($id_satpam) ? count($id_satpam) : 0;
    for ($i=0; $i<$n; $i++) {
        $ids = $id_satpam[$i] ?? null;
        if (!$ids) continue;

        $h  = isset($jumlah_hari[$i])  && $jumlah_hari[$i]  !== '' ? (int)$jumlah_hari[$i]  : 0;
        $s  = isset($jumlah_shift[$i]) && $jumlah_shift[$i] !== '' ? (int)$jumlah_shift[$i] : 0;
        $dn = isset($jumlah_dini[$i])  && $jumlah_dini[$i]  !== '' ? (int)$jumlah_dini[$i]  : 0;

        $data[] = [
            'bulan'                => $bulan,
            'tahun'                => $tahun,
            'id_satpam'            => $ids,
            'id_kehadiran_lembaga' => $id_kehadiran_lembaga,
            'jumlah_hari'          => max(0,$h),
            'jumlah_shift'         => max(0,$s),
            'jumlah_dinihari'      => max(0,$dn),
        ];
    }

    if (empty($data)) {
        echo json_encode(["status"=>false,"message"=>"Tidak ada baris data terkirim."]); return;
    }

    // Transaksi
    $this->db->trans_begin();

    // Idempotent: hapus data lama periode ini
    $this->db->where('id_kehadiran_lembaga', $id_kehadiran_lembaga)->delete('kehadiran_satpam');

    // Insert batch
    $this->db->insert_batch('kehadiran_satpam', $data);
    $dberr = $this->db->error();
    if (!empty($dberr['message'])) {
        $this->db->trans_rollback();
        echo json_encode(["status"=>false,"message"=>"DB error: ".$dberr['message']]); return;
    }

    // Update status + file pada header periode
    $this->db->where('id_kehadiran_lembaga', $id_kehadiran_lembaga)
             ->update('kehadiran_lembaga', ['file'=>$uploadedFile, 'status'=>'Sudah']);

    if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        echo json_encode(["status"=>false,"message"=>"Transaksi gagal."]); return;
    }
    $this->db->trans_commit();

    echo json_encode(["status"=>true,"message"=>"Rekap kehadiran satpam berhasil disimpan."]);
}

public function _do_upload()
{
    $date = new DateTime();

    // Folder target sejajar index.php → /uploads
    $uploadDir = rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR;

    // Buat folder jika belum ada
    if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0755, true);
    }

    // Cek lagi (kalau gagal buat)
    if (!is_dir($uploadDir)) {
        echo json_encode([
            'status'       => FALSE,
            'inputerror'   => ['file'],
            'error_string' => ['Folder uploads tidak ditemukan & gagal dibuat. Periksa path/permission.']
        ]);
        exit();
    }

    // Cek tulis
    if (!is_writable($uploadDir)) {
        echo json_encode([
            'status'       => FALSE,
            'inputerror'   => ['file'],
            'error_string' => ['Folder uploads tidak bisa ditulis. Set permission 755/775.']
        ]);
        exit();
    }

    $config['upload_path']      = $uploadDir;
    $config['allowed_types']    = 'pdf|PDF';
    $config['max_size']         = 0;
    $config['file_name']        = random_string('alnum', 50) . $date->getTimestamp();
    $config['file_ext_tolower'] = TRUE;
    $config['detect_mime']      = TRUE;

    $this->load->library('upload', $config);

    if (!$this->upload->do_upload('file')) {
        $err = strip_tags($this->upload->display_errors('', ''));
        echo json_encode([
            'status'       => FALSE,
            'inputerror'   => ['file'],
            'error_string' => [$err ?: 'Upload gagal. Pastikan file PDF & ukuran tidak melebihi batas.']
        ]);
        exit();
    }

    // Kembalikan hanya nama file; simpan di DB seperti biasa
    return $this->upload->data('file_name');
}

public function reset_json()
{
    $this->Login_model->getsqurity(); // jika ada guard
    $idKL  = $this->input->post('id_kehadiran_lembaga', true);
    $scope = $this->input->post('scope', true); // 'total' atau 'all'

    if (!$idKL) {
        echo json_encode(["status"=>false,"message"=>"id_kehadiran_lembaga tidak dikirim."]);
        return;
    }
    if (!in_array($scope, ['total','all'], true)) {
        $scope = 'total';
    }

    $this->db->trans_begin();

    // 1) selalu hapus rekap total
    $this->db->where('id_kehadiran_lembaga', $idKL)
             ->delete('total_barokah_satpam');

    // 2) opsional hapus input harian
    if ($scope === 'all') {
        $this->db->where('id_kehadiran_lembaga', $idKL)
                 ->delete('kehadiran_satpam');
    }

    // 3) kembalikan status lembaga ke Belum, nolkan total (file dibiarkan, tapi bisa juga dikosongkan)
    $this->db->where('id_kehadiran_lembaga', $idKL)
             ->update('kehadiran_lembaga', [
                 'status'       => 'Belum',
                 'jumlah_total' => 0,
                 // 'file' => null, // aktifkan kalau ingin hapus file lampiran saat reset
             ]);

    if ($this->db->trans_status() === false) {
        $this->db->trans_rollback();
        echo json_encode(["status"=>false,"message"=>"Reset gagal (transaksi dibatalkan)."]);
        return;
    }
    $this->db->trans_commit();

    $msg = ($scope==='all')
        ? 'Input + Total berhasil direset. Status dikembalikan ke Belum.'
        : 'Total berhasil direset. Status dikembalikan ke Belum.';
    echo json_encode(["status"=>true,"message"=>$msg]);
}


}