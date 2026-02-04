<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Penempatan extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Login_model');
		$this->load->model('Penempatan_model');
        $this->load->helper('Rupiah_helper');
		$this->load->helper('string');
	}

	public function index(){
	    if ($this->session->userdata('jabatan') == 'AdminLembaga' or $this->session->userdata('jabatan') == 'umana' ){
			$this->load->view('Error');
		} else {
    		$this->Login_model->getsqurity() ;
    		$isi['css'] 	= 'Penempatan/Css';
    		$isi['content'] = 'Penempatan/Penempatan';
    		$isi['ajax'] 	= 'Penempatan/Ajax';
    		$this->load->view('Template',$isi);
		}
	}

    public function add_penempatan(){
        if ($this->session->userdata('jabatan') == 'AdminLembaga' or $this->session->userdata('jabatan') == 'umana' ){
			$this->load->view('Error');
		} else {
    		$this->Login_model->getsqurity() ;
    		$isi['css'] 	= 'Penempatan/Css';
    		$isi['content'] = 'Penempatan/Add_penempatan';
    		$isi['ajax'] 	= 'Penempatan/Ajax';
    		$this->load->view('Template',$isi);
		}
	}

	public function data_list()
	{
		$this->load->helper('url');
		$list = $this->Penempatan_model->get_datatables();
		$no =1;
		$data = array();
		foreach ($list as $datanya) {
			
			$row = array();
			$row[] = $no++;
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
			
            // $row[] = htmlentities($datanya->id_bidang);
             $namaLengkap = ucwords(strtolower($datanya->nama_lengkap));
            $row[] = htmlentities($datanya->gelar_depan)." ".htmlentities($namaLengkap)." ".htmlentities($datanya->gelar_belakang);
            // $row[] = htmlentities($datanya->gelar_depan." ".$datanya->nama_lengkap." ".$datanya->gelar_belakang);
			// $row[] = htmlentities($datanya->nama_lembaga);
			$row[] ='<a href="#">
				<strong>'.htmlentities($datanya->nama_lembaga).'</strong></a>
				<br><a href="" class="btn btn-primary light btn-xs mb-1">'.htmlentities($datanya->nama_jabatan).'</a>';
				
			$row[] =date_singkat($datanya->tgl_mulai).' <br> Sampai <br> '. date_singkat($datanya->tgl_selesai);
			// $row[] = htmlentities($datanya->tunj_kel)."<br>".htmlentities($datanya->kehormatan);
		    if($datanya->tunj_kel == "Ya" and $datanya->kehormatan == "Ya" and $datanya->tunj_anak == "Ya"){
				$row[] = '<span class="badge badge-success">Tungkel<i class="ms-1 fa fa-check"></i></span> <br>
				<span class="badge badge-secondary">Kehormatan<i class="ms-1 fa fa-check"></i></span><br>
				<span class="badge badge-danger">Tunj Anak<i class="ms-1 fa fa-check"></i></span>';
			} elseif ($datanya->tunj_kel == "Ya" and $datanya->kehormatan == "Ya" and $datanya->tunj_anak != "Ya"){
				$row[] = '<span class="badge badge-success">Tungkel<span class="ms-1 fa fa-check"></span></span> <br>
				<span class="badge badge-secondary">Kehormatan<span class="ms-1 fa fa-check"></span></span>';
			} elseif ($datanya->tunj_kel != "Ya" and $datanya->kehormatan == "Ya" and $datanya->tunj_anak == "Ya"){
				$row[] = '<span class="badge badge-secondary">Kehormatan<span class="ms-1 fa fa-check"></span></span> <br>
				<span class="badge badge-danger">Tunj Anak<span class="ms-1 fa fa-check"></span></span>';
			} elseif ($datanya->tunj_kel == "Ya" and $datanya->kehormatan != "Ya" and $datanya->tunj_anak == "Ya"){
				$row[] = '<span class="badge badge-success">Tunkel<span class="ms-1 fa fa-check"></span></span> <br>
				<span class="badge badge-danger">Tunj Anak<span class="ms-1 fa fa-check"></span></span>';
			} elseif ($datanya->tunj_kel == "Ya" and $datanya->kehormatan != "Ya" and $datanya->tunj_anak != "Ya"){
				$row[] = '<span class="badge badge-success">Tungkel<span class="ms-1 fa fa-check"></span></span>';
			} elseif ($datanya->tunj_kel != "Ya" and $datanya->kehormatan == "ya" and $datanya->tunj_anak != "Ya"){
				$row[] = '<span class="badge badge-secondary">Kehormatan<span class="ms-1 fa fa-check"></span></span>';
			} elseif ($datanya->kehormatan != "Ya" and $datanya->tunj_kel != "Ya" and $datanya->tunj_anak == "Ya"){
				$row[] = '<span class="badge badge-secondary">Tunj Anak<span class="ms-1 fa fa-check"></span></span>';
			} else {
				$row[] = '<span class="badge badge-warning">Tidak Ada<span class="ms-1 fa fa-ban"></span></span>';
			}
			
			$row[] = '<td class="py-2 text-end">
						<div class="dropdown"><button class="btn btn-primary tp-btn-light sharp" type="button" data-bs-toggle="dropdown" aria-expanded="false"><span class="fs--1"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" height="18px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"></rect><circle fill="#000000" cx="5" cy="12" r="2"></circle><circle fill="#000000" cx="12" cy="12" r="2"></circle><circle fill="#000000" cx="19" cy="12" r="2"></circle></g></svg></span></button>
							<div class="dropdown-menu dropdown-menu-end border py-0" style="margin: 0px;">
								<div class="py-2">
								<a class="dropdown-item text-danger" href="upload/'.$datanya->file_sk.'">FIle SK</a></div>
								<a class="dropdown-item text-success" href="#" onclick="edit_penempatan('."'".$datanya->id_penempatan."'".')"> <i class="bx bx-edit mr-1" ></i> Edit</a>
							</div>
						</div>
					</td>';
			//add html for action
			$data[] = $row;
		}
		$output = array("data" => $data);
		echo json_encode($output);
	}
	
	public function pengajar(){
		$this->Login_model->getsqurity() ;
		$isi['css'] 	= 'Pengajar/Css';
		$isi['content'] = 'Pengajar/Pengajar';
		$isi['ajax'] 	= 'Pengajar/Ajax';
		$this->load->view('Template',$isi);
	}

	public function add_pengajar(){
		$this->Login_model->getsqurity() ;
		$isi['css'] 	= 'Pengajar/Css';
		$isi['content'] = 'Pengajar/Add_Pengajar';
		$isi['ajax'] 	= 'Pengajar/Ajax';
		$this->load->view('Template',$isi);
	}

	public function data_list_pengajar()
	{
		$this->load->helper('url');
		$list = $this->Penempatan_model->get_datatables_pengajar();
		$no =1;
		$data = array();
		foreach ($list as $datanya) {
			
			$row = array();
			$row[] = $no++;
			// if($datanya->jk == "Laki-laki"){
            //     $row[] = '<span> Laki-laki </span>';
            // } else {
            //     $row[] = '<span> Perempuan </span>';
            // }
			
            // $row[] = htmlentities($datanya->id_bidang);
            $row[] = htmlentities($datanya->gelar_depan." ".$datanya->nama_lengkap." ".$datanya->gelar_belakang);
			// $row[] = htmlentities($datanya->nama_lembaga);
			$row[] ='<a href="#">
				<strong>'.htmlentities($datanya->nama_lembaga).'</strong></a>
				<br><a href="" class="btn btn-primary light btn-xs mb-1">'.htmlentities($datanya->kategori).'</a>';
			$row[] = htmlentities($datanya->jumlah_sks);
				
			$row[] =date_singkat($datanya->tgl_mulai).' <br> Sampai <br> '. date_singkat($datanya->tgl_selesai);
			// $row[] = htmlentities($datanya->tunj_kel)."<br>".htmlentities($datanya->kehormatan);
			if($datanya->tunj_kel == "Ya" and $datanya->kehormatan == "Ya" and $datanya->tunj_anak == "Ya" and $datanya->walkes == "Ya"){
				$row[] = '<span class="badge badge-success">Tungkel<i class="ms-1 fa fa-check"></i></span> <br>
				<span class="badge badge-secondary">Kehormatan<i class="ms-1 fa fa-check"></i></span><br>
				<span class="badge badge-danger">Tunj Anak<i class="ms-1 fa fa-check"></i></span>
				<span class="badge badge-info">Wali kelas<i class="ms-1 fa fa-check"></i></span>';
			} elseif ($datanya->tunj_kel == "Ya" and $datanya->kehormatan == "Ya" and $datanya->tunj_anak == "Tidak" and $datanya->walkes == "Tidak"){
				$row[] = '<span class="badge badge-success">Tungkel<span class="ms-1 fa fa-check"></span></span> <br>
				<span class="badge badge-secondary">Kehormatan<span class="ms-1 fa fa-check"></span></span>';
			} elseif ($datanya->tunj_kel == "Tidak" and $datanya->kehormatan == "Ya" and $datanya->tunj_anak == "Ya" and $datanya->walkes == "Tidak"){
				$row[] = '<span class="badge badge-secondary">Kehormatan<span class="ms-1 fa fa-check"></span></span> <br>
				<span class="badge badge-danger">Tunj Anak<span class="ms-1 fa fa-check"></span></span>';
			} elseif ($datanya->tunj_kel == "Ya" and $datanya->kehormatan == "Tidak" and $datanya->tunj_anak == "Ya"){
				$row[] = '<span class="badge badge-success">Tunkel<span class="ms-1 fa fa-check"></span></span> <br>
				<span class="badge badge-danger">Tunj Anak<span class="ms-1 fa fa-check"></span></span>';
			} elseif ($datanya->tunj_kel == "Ya" and $datanya->kehormatan == "Tidak" and $datanya->tunj_anak == "Tidak"){
				$row[] = '<span class="badge badge-success">Tungkel<span class="ms-1 fa fa-check"></span></span>';
			} elseif ($datanya->tunj_kel == "Tidak" and $datanya->kehormatan == "Ya" and $datanya->tunj_anak == "Tidak"){
				$row[] = '<span class="badge badge-secondary">Kehormatan<span class="ms-1 fa fa-check"></span></span>';
			} elseif ($datanya->kehormatan == "Tidak" and $datanya->tunj_kel == "Tidak" and $datanya->tunj_anak == "Ya"){
				$row[] = '<span class="badge badge-secondary">Tunj Anak<span class="ms-1 fa fa-check"></span></span>';
			} else {
				$row[] = '<span class="badge badge-warning">Tidak Ada<span class="ms-1 fa fa-ban"></span></span>';
			}
			$row[] = htmlentities($datanya->status);
			$row[] = '<td class="py-2 text-end">
						<div class="dropdown"><button class="btn btn-primary tp-btn-light sharp" type="button" data-bs-toggle="dropdown" aria-expanded="false"><span class="fs--1"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" height="18px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"></rect><circle fill="#000000" cx="5" cy="12" r="2"></circle><circle fill="#000000" cx="12" cy="12" r="2"></circle><circle fill="#000000" cx="19" cy="12" r="2"></circle></g></svg></span></button>
							<div class="dropdown-menu dropdown-menu-end border py-0" style="margin: 0px;">
								<div class="py-2">
								<a class="dropdown-item text-danger" href="upload/'.$datanya->file_sk.'">FIle SK</a></div>
								<a class="dropdown-item text-success" href="#" onclick="edit_penempatan('."'".$datanya->id_pengajar."'".')"> <i class="bx bx-edit mr-1" ></i> Edit</a>
							</div>
						</div>
					</td>';
			//add html for action
			$data[] = $row;
		}
		$output = array("data" => $data);
		echo json_encode($output);
	}


    public function get_umana($id)
	{
		$data = $this->Penempatan_model->get_akun($id);
		echo json_encode($data);
	}

    public function ajax_add()
	{
		$this->_validate();
		$data = array(
            'id_penempatan' 	=> '',
            'nik' 	            => $this->input->post('nik'),
            'id_ketentuan' 	    => $this->input->post('id_ketentuan'),
			'id_lembaga' 	    => $this->input->post('lembaga'),
			'tunj_kel' 	   		=> $this->input->post('tunkel'),
			'tunj_anak' 	   	=> $this->input->post('tunj_anak'),
			'kehormatan' 	   	=> $this->input->post('kehormatan'),
			'tgl_mulai' 	    => $this->input->post('tgl_mulai'),
			'tgl_selesai' 	    => $this->input->post('tgl_selesai'),
			'kategori_trans' 	=> $this->input->post('transport'),
			'jabatan_lembaga' 	=> $this->input->post('jabatan_lembaga'),
			'file_sk'				=> '',
			'status'            => 'Aktif',
        );

		if(!empty($_FILES['file_sk']['name']))
		{
			$upload = $this->_do_upload();
			$data['file_sk'] = $upload;
		}

		$simpan = $this->Penempatan_model->create('penempatan',$data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update(){
        // $this->_validate_edit();
       $data = array(
			'nik' 	            => $this->input->post('nik'),
			'id_ketentuan' 	    => $this->input->post('id_ketentuan'),
			'id_lembaga' 	    => $this->input->post('lembaga'),
			'tgl_mulai' 	    => $this->input->post('tgl_mulai'),
			'tgl_selesai' 	    => $this->input->post('tgl_selesai'),
			'tunj_kel' 	   		=> $this->input->post('tunkel'),
			'tunj_anak' 	   	=> $this->input->post('tunj_anak'),
			'tunj_mp' 	   	    => $this->input->post('tunj_mp'),
			'kehormatan' 	   	=> $this->input->post('kehormatan'),
			'kategori_trans' 	=> $this->input->post('transport'),
			'jabatan_lembaga' 	=> $this->input->post('jabatan_lembaga'),
			'status'			=> $this->input->post('status'),
			
        );
        
        if(!empty($_FILES['file_sk']['name']))
		{
			$upload = $this->_do_upload();
			$data['file_sk'] = $upload;
		}
        
		$this->Penempatan_model->update(array('id_penempatan' => $this->input->post('id_penempatan')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_edit($id)
	{
		$data = $this->Penempatan_model->get_by_id($id);
		echo json_encode($data);
	}
	
	public function pengajar_add()
	{
		// $this->_validate();
		if ($this->input->post('file') == ''){
			$data = array(
				'id_pengajar' 		=> '',
				'nik' 	            => $this->input->post('nik'),
				'kategori' 	    	=> $this->input->post('kategori'),
				'jumlah_sks' 	    => $this->input->post('jumlah_sks'),
				'id_lembaga' 	    => $this->input->post('lembaga'),
				'tunj_kel' 	   		=> $this->input->post('tunkel'),
				'tunj_anak' 	   	=> $this->input->post('tunj_anak'),
				'kehormatan' 	   	=> $this->input->post('kehormatan'),
				'walkes' 	   		=> $this->input->post('walikelas'),
				'jafung' 	   		=> $this->input->post('jafung'),
				'tgl_mulai' 	    => $this->input->post('tgl_mulai'),
				'tgl_selesai' 	    => $this->input->post('tgl_selesai'),
				'kategori_trans' 	=> $this->input->post('transport'),
				'status'            => 'Aktif',
			);
		} else {
			$data = array(
				'id_pengajar' 		=> '',
				'nik' 	            => $this->input->post('nik'),
				'kategori' 	    	=> $this->input->post('kategori'),
				'jumlah_sks' 	    => $this->input->post('jumlah_sks'),
				'id_lembaga' 	    => $this->input->post('lembaga'),
				'tunj_kel' 	   		=> $this->input->post('tunkel'),
				'tunj_anak' 	   	=> $this->input->post('tunj_anak'),
				'kehormatan' 	   	=> $this->input->post('kehormatan'),
				'walkes' 	   		=> $this->input->post('walikelas'),
				'jafung' 	   		=> $this->input->post('jafung'),
				'tgl_mulai' 	    => $this->input->post('tgl_mulai'),
				'tgl_selesai' 	    => $this->input->post('tgl_selesai'),
				'kategori_trans' 	=> $this->input->post('transport'),
				'file_sk'			=> '',
				'status'            => 'Aktif',
			);
		
			if(!empty($_FILES['file']['name']))
			{
				$upload = $this->_do_upload();
				$data['file'] = $upload;
			}
		}

		$simpan = $this->Penempatan_model->create('pengajar',$data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_edit_pengajar($id)
	{
		$data = $this->Penempatan_model->get_by_id_pengajar($id);
		echo json_encode($data);
	}
	
	public function ajax_update_pengajar(){
        // $this->_validate_edit();
       $data = array(
			'nik' 	            => $this->input->post('nik'),
			'kategori' 	    	=> $this->input->post('kategori'),
			'jumlah_sks' 	    => $this->input->post('jumlah_sks'),
			'id_lembaga' 	    => $this->input->post('lembaga'),
			'tunj_kel' 	   		=> $this->input->post('tunkel'),
			'tunj_anak' 	   	=> $this->input->post('tunj_anak'),
			'kehormatan' 	   	=> $this->input->post('kehormatan'),
			'walkes' 	   		=> $this->input->post('walikelas'),
			'jafung' 	   		=> $this->input->post('jafung'),
			'tgl_mulai' 	    => $this->input->post('tgl_mulai'),
			'tgl_selesai' 	    => $this->input->post('tgl_selesai'),
			'kategori_trans' 	=> $this->input->post('transport'),
			'file_sk'			=> '',
			'status'			=> $this->input->post('status'),
			
        );
        
        if(!empty($_FILES['file_sk']['name']))
		{
			$upload = $this->_do_upload();
			$data['file_sk'] = $upload;
		}
        
		$this->Penempatan_model->update_pengajar(array('id_pengajar' => $this->input->post('id_pengajar')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function _do_upload()
	{
		$date = new DateTime();
		$timezone = time() + (60 * 60 * 7);
		$config['upload_path']          = 'upload/';
        $config['allowed_types']        = 'pdf';
        $config['max_size']             = 0; //set max size allowed in Kilobyte
        $config['file_name']            = random_string('alnum',50).$date->getTimestamp(); //just milisecond timestamp fot unique name

        $this->load->library('upload', $config);

        if(!$this->upload->do_upload('file_sk')) //upload and validate
        {
            $data['inputerror'][] = 'file_sk';
			$data['error_string'][] = 'Upload error: File harus PDF  '; //show ajax error
			$data['status'] = FALSE;
			echo json_encode($data);
			exit();
		}
		return $this->upload->data('file_name');
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

        if ($this->input->post('id_ketentuan') == '') {
            $data['inputerror'][] = 'id_ketentuan';
            $data['error_string'][] = 'Jabatan harus dipilih';
            $data['status'] = FALSE;
        }

		if ($this->input->post('lembaga') == '') {
            $data['inputerror'][] = 'lembaga';
            $data['error_string'][] = 'Lembaga harus diisi';
            $data['status'] = FALSE;
        }

		if ($this->input->post('tunkel') == '') {
            $data['inputerror'][] = 'tunkel';
            $data['error_string'][] = 'Tunkel harus diisi';
            $data['status'] = FALSE;
        }

		if ($this->input->post('status') == '') {
            $data['inputerror'][] = 'status';
            $data['error_string'][] = 'Status harus diisi';
            $data['status'] = FALSE;
        }

		// if ($this->input->post('file_sk') == '') {
        //     $data['inputerror'][] = 'file_sk';
        //     $data['error_string'][] = 'File harus diisi';
        //     $data['status'] = FALSE;
        // }

      if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }

   


}
