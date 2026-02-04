<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Penempatan_satpam extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Login_model');
		$this->load->model('Penempatan_satpam_model');
        $this->load->helper('Rupiah_helper');
		$this->load->helper('string');
	}

	public function index(){
	    if ($this->session->userdata('jabatan') == 'AdminLembaga' or $this->session->userdata('jabatan') == 'umana' ){
			$this->load->view('Error');
		} else {
    		$this->Login_model->getsqurity() ;
    		$isi['css'] 	= 'Penempatan_satpam/Css';
    		$isi['content'] = 'Penempatan_satpam/Penempatan_satpam';
    		$isi['ajax'] 	= 'Penempatan_satpam/Ajax';
    		$this->load->view('Template',$isi);
		}
	}

 	public function data_list()
	{
		$this->load->helper('url');
		$list = $this->Penempatan_satpam_model->get_datatables();
		$no =1;
		$data = array();
		foreach ($list as $datanya) {
			
			$row = array();
			$row[] = $no++;
			$namaLengkap = ucwords(strtolower($datanya->nama_lengkap));
            $row[] = htmlentities($datanya->gelar_depan)." ".htmlentities($namaLengkap)." ".htmlentities($datanya->gelar_belakang);
        	$row[] =date_singkat($datanya->tgl_mulai).' <br> Sampai <br> '. date_singkat($datanya->tgl_selesai);
            $row[] =date_singkat($datanya->tgl_mulai).' <br> Sampai <br> '. date_singkat($datanya->tgl_selesai);
            $row[] = ucwords(strtolower($datanya->status));
			$row[] = '<div class="d-flex justify-content-center">
						<a href="upload/'.$datanya->file_sk.'" target="_blank" class="btn btn-warning shadow btn-sm me-1" title="Lihat SK"><i class="fa fa-file-pdf"></i> SK</a>
						<a href="javascript:void(0)" onclick="edit_satpam('."'".$datanya->id_satpam."'".')" class="btn btn-primary shadow btn-sm" title="Edit"><i class="fa fa-edit"></i> Edit</a>
					  </div>';
			//add html for action
			$data[] = $row;
		}
		$output = array("data" => $data);
		echo json_encode($output);
	}
	
	public function get_umana($nik)
    {
        $this->db->where('nik', $nik);
        $query = $this->db->get('umana')->row();

        if ($query) {
            echo json_encode($query);
        } else {
            echo json_encode(null);
        }
    }

    public function ajax_add()
    {
        $this->_validate(); 

        $data = array(
            'id_satpam' => '', 
            'nik'          => $this->input->post('nik'),
            'id_transport'    => $this->input->post('transport'),
            'tgl_mulai'    => $this->input->post('tgl_mulai'),
            'tgl_selesai'  => $this->input->post('tgl_selesai'),
            'status'       => $this->input->post('status'),
            'is_danru'     => $this->input->post('is_danru'),
            'file_sk'      => '',
        );

        // Upload file jika ada
        if (!empty($_FILES['file_sk']['name'])) {
            $upload = $this->_do_upload();
            $data['file_sk'] = $upload;
        }

        // Simpan ke database
        $this->Penempatan_satpam_model->create('satpam', $data);

        echo json_encode(array("status" => TRUE));
    }

    public function ajax_update()
    {
        $this->_validate(); // Bisa pakai _validate yang sama, karena inputnya identik

        $id = $this->input->post('id_satpam');

        $data = array(
            'nik'          => $this->input->post('nik'),
            'id_transport' => $this->input->post('transport'),
            'tgl_mulai'    => $this->input->post('tgl_mulai'),
            'tgl_selesai'  => $this->input->post('tgl_selesai'),
            'status'       => $this->input->post('status'),
            'is_danru'     => $this->input->post('is_danru'),
        );

        // Jika ada file SK baru di-upload, replace dengan yang lama
        if (!empty($_FILES['file_sk']['name'])) {
            $upload = $this->_do_upload();
            $data['file_sk'] = $upload;

            // Opsional: hapus file lama kalau mau
            $old_data = $this->Penempatan_satpam_model->get_by_id($id);
            if ($old_data && !empty($old_data->file_sk) && file_exists('upload/'.$old_data->file_sk)) {
                @unlink('upload/'.$old_data->file_sk);
            }
        }

        // Update data ke database
        $this->Penempatan_satpam_model->update(array('id_satpam' => $id), $data);

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

	public function ajax_edit($id)
	{
		$data = $this->Penempatan_satpam_model->get_by_id($id);
		echo json_encode($data);
	}
	
	private function _validate()
    {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        if($this->input->post('nik') == '')
        {
            $data['inputerror'][] = 'nik';
            $data['error_string'][] = 'NIK wajib diisi';
            $data['status'] = FALSE;
        }

        if($this->input->post('transport') == '')
        {
            $data['inputerror'][] = 'transport';
            $data['error_string'][] = 'Transport wajib dipilih';
            $data['status'] = FALSE;
        }

        if($this->input->post('tgl_mulai') == '')
        {
            $data['inputerror'][] = 'tgl_mulai';
            $data['error_string'][] = 'Tanggal mulai wajib diisi';
            $data['status'] = FALSE;
        }

        if($this->input->post('tgl_selesai') == '')
        {
            $data['inputerror'][] = 'tgl_selesai';
            $data['error_string'][] = 'Tanggal selesai wajib diisi';
            $data['status'] = FALSE;
        }

        if($data['status'] === FALSE)
        {
            echo json_encode($data);
            exit();
        }
    }



}
