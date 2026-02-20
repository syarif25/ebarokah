<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// Include librari PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\IOFactory;

class Umana extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Login_model');
		$this->load->model('Umana_model');
		$this->load->helper('Rupiah_helper');
        $this->load->library('user_agent');
        // $this->load->library('PHPExcel');
	}

	public function index(){
	    if ($this->session->userdata('jabatan') == 'AdminLembaga' or $this->session->userdata('jabatan') == 'umana' ){
			$this->load->view('Error');
		} else {
    		$this->Login_model->getsqurity() ;
    		$isi['css'] 	= 'Pengguna/Css';
    		$isi['content'] = 'Umana/Umana';
    		$isi['ajax'] 	= 'Umana/Ajax';
    		$this->load->view('Template',$isi);
		}
	}

    public function Profil(){
		$this->Login_model->getsqurity();
        if ($this->agent->is_mobile())
		{
			// $isi['css'] 	= 'Css';
            $isi['content'] = 'Mobile/Profil';
            $isi['ajax'] 	= 'Profil/Ajax';
            $this->load->view('Mobile/Mobile',$isi);
			
		}
		else
		{
            $isi['css'] 	= 'Profil/Css';
            $isi['content'] = 'Profil/Profil';
            $isi['ajax'] 	= 'Profil/Ajax';
            $this->load->view('Template',$isi);
        }
	}

	public function data_list()
	{
		$this->load->helper('url');

		$list = $this->Umana_model->get_datatables();
		$no =1;
		$data = array();
		foreach ($list as $datanya) {
			
			$row = array();
			if($datanya->jk == "Laki-laki"){
                $row[] = '<div class="image-bx">
                <img style=" height:40px; " src="assets/cowok.png" data-src="assets/cowok.png" alt="" class="img-fluid rounded">
                <span class="active"></span>
            </div>' ;
            } else {
                $row[] = '<div class="image-bx">
                <img style=" height:40px; " src="assets/putri.jpg" data-src="assets/putri.jpgg" alt="" class="rounded-circle">
                <span class="active"></span>
            </div>' ;
            }
			$row[] = htmlentities($datanya->nik);
            // $row[] = htmlentities($datanya->niy);
            $namaLengkap = ucwords(strtolower($datanya->nama_lengkap));
            $row[] = htmlentities($datanya->gelar_depan)." ".htmlentities($namaLengkap)." ".htmlentities($datanya->gelar_belakang);

            // $row[] = htmlentities($datanya->bidang_keahlian);
            $row[] = htmlentities($datanya->alamat_domisili);
             $row[] = htmlentities($datanya->nomor_hp);
			//add html for action
			$row[] = '<a type="button" class="btn btn-success btn-sm" href="#" 
			title="Track" onclick="edit_umana('."'".$datanya->nik."'".')"><i class="bx bx-edit mr-1" ></i> Edit</a>
            <a type="button" class="btn btn-danger btn-sm" href="#" 
			title="Track" onclick="delete_umana('."'".$datanya->nik."'".')"><i class="bx bx-trash mr-1" ></i> Hapus</a>
            ';
		$data[] = $row;
		}
		$output = array("data" => $data);
		echo json_encode($output);
	}
	
	public function ganti_password(){
        $this->_validate_pwd();
        $data = array(
            'password' 	=> password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                );
           
        $this->Umana_model->update(array('nik' => $this->input->post('nik')), $data);
        echo json_encode(array("status" => TRUE));
	}

    public function ajax_add()
	{
		$this->_validate();
		$data = array(
				'nik' 		    => $this->input->post('nik'),
				'niy' 	        => $this->input->post('niy'),
                'nidn' 	        => $this->input->post('nidn'),
                'nama_lengkap' 	=> $this->input->post('nama_lengkap'),
                'gelar_depan' 	=> $this->input->post('gelar_depan'),
                'gelar_belakang' => $this->input->post('gelar_belakang'),
                'jk' 	        => $this->input->post('jk'),
                'bidang_keahlian' 	=> $this->input->post('bidang_keahlian'),
                'no_rekening' 	=> $this->input->post('no_rekening'),
                'atas_nama' 	    => $this->input->post('atas_nama'),
                'nama_bank' 	    => $this->input->post('nama_bank'),
                'tgl_lahir' 	    => $this->input->post('tgl_lahir'),
                'nomor_hp' 	        => $this->input->post('nomor_hp'),
                'tmt_struktural' 	=> $this->input->post('tmt_struktural'),
                'tmt_guru' 	    => $this->input->post('tmt_guru'),
                'tmt_dosen' 	    => $this->input->post('tmt_dosen'),
                'tmt_maif' 	    => $this->input->post('tmt_maif'),
                'alamat_domisili' 	=> $this->input->post('alamat_domisili'),
                'ijazah_terakhir' 	=> $this->input->post('ijazah_terakhir'),
                'jabatan_akademik' 	=> $this->input->post('jabatan_akademik'),
                'status_sertifikasi' => $this->input->post('status_sertifikasi'),
                'status_nikah' 	     => $this->input->post('status_nikah'),
                'status_aktif' 	     => $this->input->post('status_aktif'),
                'ikatan_khidmah' 	 => $this->input->post('ikatan_khidmah'),
                // 'file_ktp' 	         => '',
                // 'file_ijazah' 	     => '',
                // 'sk_awal_struktural' => '',
                // 'sk_awal_madrasah' 	 => '',
                // 'sk_awal_sekolah' 	 => '',
                // 'sk_jafung' 	     => '',
                // 'data_penunjang'     => '',
                'password' 	         => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
				);

		$simpan = $this->Umana_model->create('umana',$data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update(){
        $this->_validate();
        if($this->input->post('password') == ''){
            $data = array(
            'nik' 		    => $this->input->post('nik'),
            'niy' 	        => $this->input->post('niy'),
            'nidn' 	        => $this->input->post('nidn'),
            'nama_lengkap' 	=> $this->input->post('nama_lengkap'),
            'gelar_depan' 	=> $this->input->post('gelar_depan'),
            'gelar_belakang' => $this->input->post('gelar_belakang'),
            'jk' 	        => $this->input->post('jk'),
            'bidang_keahlian' 	=> $this->input->post('bidang_keahlian'),
            'no_rekening' 	=> $this->input->post('no_rekening'),
            'atas_nama' 	    => $this->input->post('atas_nama'),
            'nama_bank' 	    => $this->input->post('nama_bank'),
            'tgl_lahir' 	    => $this->input->post('tgl_lahir'),
            'nomor_hp' 	        => $this->input->post('nomor_hp'),
            'tmt_struktural' 	=> $this->input->post('tmt_struktural'),
            'tmt_guru' 	    => $this->input->post('tmt_guru'),
            'tmt_dosen' 	    => $this->input->post('tmt_dosen'),
            'tmt_maif' 	    => $this->input->post('tmt_maif'),
            'alamat_domisili' 	=> $this->input->post('alamat_domisili'),
            'ijazah_terakhir' 	=> $this->input->post('ijazah_terakhir'),
            'jabatan_akademik' 	=> $this->input->post('jabatan_akademik'),
            'status_sertifikasi' => $this->input->post('status_sertifikasi'),
            'status_nikah' 	     => $this->input->post('status_nikah'),
            'status_aktif' 	     => $this->input->post('status_aktif'),
            'ikatan_khidmah' 	 => $this->input->post('ikatan_khidmah'),
            
            );
            
        } else {
         
           $data = array(
            'nik' 		    => $this->input->post('nik'),
            'niy' 	        => $this->input->post('niy'),
            'nidn' 	        => $this->input->post('nidn'),
            'nama_lengkap' 	=> $this->input->post('nama_lengkap'),
            'gelar_depan' 	=> $this->input->post('gelar_depan'),
            'gelar_belakang' => $this->input->post('gelar_belakang'),
            'jk' 	        => $this->input->post('jk'),
            'bidang_keahlian' 	=> $this->input->post('bidang_keahlian'),
            'no_rekening' 	=> $this->input->post('no_rekening'),
            'atas_nama' 	    => $this->input->post('atas_nama'),
            'nama_bank' 	    => $this->input->post('nama_bank'),
            'tgl_lahir' 	    => $this->input->post('tgl_lahir'),
            'nomor_hp' 	        => $this->input->post('nomor_hp'),
            'tmt_struktural' 	=> $this->input->post('tmt_struktural'),
            'tmt_guru' 	        => $this->input->post('tmt_guru'),
            'tmt_dosen' 	    => $this->input->post('tmt_dosen'),
            'alamat_domisili' 	=> $this->input->post('alamat_domisili'),
            'ijazah_terakhir' 	=> $this->input->post('ijazah_terakhir'),
            'jabatan_akademik' 	=> $this->input->post('jabatan_akademik'),
            'status_sertifikasi' => $this->input->post('status_sertifikasi'),
            'status_nikah' 	     => $this->input->post('status_nikah'),
            'status_aktif' 	     => $this->input->post('status_aktif'),
            'ikatan_khidmah' 	 => $this->input->post('ikatan_khidmah'),
            
            'password' 	=> password_hash($this->input->post('password'), PASSWORD_DEFAULT),
            );
        }
           
// 		$this->Umana_model->update(array('nik' => $this->input->post('nik1')), $data);
		
		// Lakukan update ke database
        $data2 = array(
            'nik' => $this->input->post('nik'),
            // ... tambahkan kolom lain yang perlu diupdate
        );
   
		$this->Umana_model->update(array('nik' => $this->input->post('nik1')), $data);
        //update penempatan, pengajar
        $this->db->where('nik', $this->input->post('nik1')); // Ganti 'id_tabel' dengan kolom yang sesuai
        $this->db->update('penempatan', $data2);
        $this->db->where('nik', $this->input->post('nik1')); // Ganti 'id_tabel' dengan kolom yang sesuai
        $this->db->update('pengajar', $data2);
        
		echo json_encode(array("status" => TRUE));
	}

    public function import() {
        // Konfigurasi upload file
        $config['upload_path']   = './upload/';
        $config['allowed_types'] = 'xls|xlsx';
        $config['max_size']      = 1024; // Batasan ukuran file (dalam kilobyte)
     
        $this->load->library('upload', $config);
     
        if (!$this->upload->do_upload('file')) {
           $this->session->set_flashdata('success', 'Data eror diimpor.');
           echo '<script>toastr.warning("Data eror diimpor.");</script>';
           $error = array('error' => $this->upload->display_errors());
           $this->session->set_flashdata('error', 'Data Gagal diimpor.');
           $this->session->set_flashdata('action', 'add');
           redirect('umana');
        } else {
           $file = $this->upload->data('file_name');
     
           // Memuat file Excel menggunakan PHPSpreadsheet
           $spreadsheet = IOFactory::load('./upload/' . $file);
           $worksheet = $spreadsheet->getActiveSheet();
           $data = $worksheet->toArray();
     
           // Mengimpor data ke database
           $column_names = $worksheet->getRowIterator(1)->current();
           $table_name = 'umana'; // Ganti dengan nama tabel yang sesuai di database Anda
           $batch_data = array();
           foreach ($data as $row) {
              $record = array();
              $column_index = 0;
              foreach ($row as $cell) {
                 $column_name = $worksheet->getCellByColumnAndRow($column_index + 1, 1)->getValue();
                 $record[$column_name] = $cell;
                 $column_index++;
              }
              $batch_data[] = $record;
           }
           $this->db->insert_batch($table_name, $batch_data);
     
           // Menghapus file yang diunggah
           unlink('./upload/' . $file);
     
           // Menampilkan notifikasi sukses menggunakan toastr
           $this->session->set_flashdata('success', 'Data berhasil diimpor.');
           $this->session->set_flashdata('action', 'add');
           redirect('umana');
        }
     }
    
    public function simpan_lampiran() {
        $config['upload_path'] = 'upload/';
        $config['allowed_types'] = 'pdf|jpg|png|jpeg';
        $this->load->library('upload', $config);
    
        $error_message = array();
        $success = TRUE; // Inisialisasi status keberhasilan
    
        $nik = $this->input->post('nik');
        $file_data = array(
            'jumlah_anak' => $this->input->post('jumlah_anak')
        );
    
        if (!empty($_FILES['file_ktp']['name'])) {
            if ($this->upload->do_upload('file_ktp')) {
                $upload_data = $this->upload->data();
                $file_data['file_ktp'] = $upload_data['file_name'];
            } else {
                $error_message[] = $this->upload->display_errors();
                $success = FALSE;
            }
        }

        if (!empty($_FILES['file_kk']['name'])) {
            if ($this->upload->do_upload('file_kk')) {
                $upload_data = $this->upload->data();
                $file_data['file_kk'] = $upload_data['file_name'];
            } else {
                $error_message[] = $this->upload->display_errors();
                $success = FALSE;
            }
        }

        if (!empty($_FILES['sk_awal']['name'])) {
            if ($this->upload->do_upload('sk_awal')) {
                $upload_data = $this->upload->data();
                $file_data['sk_awal'] = $upload_data['file_name'];
            } else {
                $error_message[] = $this->upload->display_errors();
                $success = FALSE;
            }
        }

        if (!empty($_FILES['sk_guru']['name'])) {
            if ($this->upload->do_upload('sk_guru')) {
                $upload_data = $this->upload->data();
                $file_data['sk_guru'] = $upload_data['file_name'];
            } else {
                $error_message[] = $this->upload->display_errors();
                $success = FALSE;
            }
        }

        if (!empty($_FILES['sk_dosen']['name'])) {
            if ($this->upload->do_upload('sk_dosen')) {
                $upload_data = $this->upload->data();
                $file_data['sk_dosen'] = $upload_data['file_name'];
            } else {
                $error_message[] = $this->upload->display_errors();
                $success = FALSE;
            }
        }
    
        // Lakukan hal serupa untuk file_kk, sk_guru, dan sk_dosen
    
        if (!empty($error_message)) {
            echo json_encode(array("status" => FALSE, "error" => implode('<br>', $error_message)));
        } else {
            $update_condition = array('nik' => $nik);
            $this->Umana_model->update($update_condition, $file_data);
            echo json_encode(array("status" => $success));
        }
    }

	public function ajax_edit($id)
	{
		$data = $this->Umana_model->get_by_id($id);
		echo json_encode($data);
	}
	
	public function hapus($id)
    {
     $this->db->where('nik', $id)->delete('umana');
     echo json_encode(array("status" => TRUE));
    }  
    
    private function _validate_pwd(){
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        if ($this->input->post('password') == '') {
            $data['inputerror'][] = 'password';
            $data['error_string'][] = 'password harus diisi';
            $data['status'] = FALSE;
        }

        if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }

	private function _validate()
    {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        if ($this->input->post('nik') == '') {
            $data['inputerror'][] = 'nik';
            $data['error_string'][] = 'NIK harus diisi';
            $data['status'] = FALSE;
        }

        if ($this->input->post('niy') == '') {
            $data['inputerror'][] = 'niy';
            $data['error_string'][] = 'NIY harus diisi';
            $data['status'] = FALSE;
        }

        // if ($this->input->post('nidn') == '') {
        //     $data['inputerror'][] = 'nidn';
        //     $data['error_string'][] = 'NIDN harus diisi';
        //     $data['status'] = FALSE;
        // }

        if ($this->input->post('nama_lengkap') == '') {
            $data['inputerror'][] = 'nama_lengkap';
            $data['error_string'][] = 'Nama harus diisi';
            $data['status'] = FALSE;
        }

        if ($this->input->post('jk') == '') {
            $data['inputerror'][] = 'jk';
            $data['error_string'][] = 'Jenis Kelamin harus diisi';
            $data['status'] = FALSE;
        }

        if ($this->input->post('no_rekening') == '') {
            $data['inputerror'][] = 'no_rekening';
            $data['error_string'][] = 'Nomor Rekening harus diisi';
            $data['status'] = FALSE;
        }

        if ($this->input->post('nama_bank') == '') {
            $data['inputerror'][] = 'nama_bank';
            $data['error_string'][] = 'Nama Bank harus diisi';
            $data['status'] = FALSE;
        }

        if ($this->input->post('tgl_lahir') == '') {
            $data['inputerror'][] = 'tgl_lahir';
            $data['error_string'][] = 'Tanggal Lahir harus diisi';
            $data['status'] = FALSE;
        }

        if ($this->input->post('alamat_domisili') == '') {
            $data['inputerror'][] = 'alamat_domisili';
            $data['error_string'][] = 'Alamat harus diisi';
            $data['status'] = FALSE;
        }

      if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }

   


}
