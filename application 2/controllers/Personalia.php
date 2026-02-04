<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Personalia extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Login_model');
		$this->load->model('Personalia_model');
	}

	public function index(){
		$this->Login_model->getsqurity() ;
        $id = $this->session->userdata('lembaga');
        $query = $this->db->query("SELECT nama_lembaga FROM lembaga WHERE id_lembaga = ?", array($id));
        $nama_lembaga_row = $query->row();
		$isi['css'] 	= 'Personalia/Css';
		$isi['content'] = 'Personalia/Personalia';
		$isi['ajax'] 	= 'Personalia/Ajax';
        if ($nama_lembaga_row) {
            $isi['nama_lembaga'] = $nama_lembaga_row->nama_lembaga;
        } else {
            $isi['nama_lembaga'] = "Lembaga Tidak Ditemukan";
        }
		$this->load->view('Template',$isi);
	}

	public function data_list()
	{
		$this->load->helper('url');

		$list = $this->Personalia_model->get_datatables();

		$no =1;
		$data = array();
		foreach ($list as $datanya) {
            $tbk = $this->db->query("SELECT count(nominal_tbk) as jumlah from t_beban_kerja, penempatan where t_beban_kerja.id_penempatan = penempatan.id_penempatan and t_beban_kerja.id_penempatan = $datanya->id_penempatan and t_beban_kerja.max_periode >= DATE(NOW())")->row();
			$potongan = $this->db->query("SELECT count(nominal) as jumlah from potongan, potongan_umana, penempatan where potongan.id_potongan = potongan_umana.jenis_potongan and potongan_umana.id_penempatan = penempatan.id_penempatan and potongan_umana.id_penempatan = $datanya->id_penempatan  and potongan_umana.max_periode_potongan >= DATE(NOW())")->row();
			$row = array();
			$row[] = $no++;
			$row[] = htmlentities($datanya->gelar_depan)." ".htmlentities($datanya->nama_lengkap)." ".htmlentities($datanya->gelar_belakang);
            $row[] = htmlentities($datanya->nama_jabatan);
            $row[] = htmlentities($datanya->nama_lembaga);
            // $row[] = htmlentities($datanya->tmt_struktural);
            if ($datanya->tunj_kel != 'Ya'){
                $row[] = '<i class="fas fa-ban"></i>';
            } else {
                $row[] = '<span class="badge badge-success"><i class="ms-1 fa fa-check"></i> </span>';
            }
            if ($datanya->tunj_anak != 'Ya'){
                $row[] = '<i class="fas fa-ban"></i>';
            } else {
                $row[] = '<span class="badge badge-success"><i class="ms-1 fa fa-check"></i> </span>';
            }
            if ($datanya->kehormatan != 'Ya'){
                $row[] = '<i class="fas fa-ban"></i>';
            } else {
                $row[] = '<span class="badge badge-success"><i class="ms-1 fa fa-check"></i> </span>';
            }
            if ($datanya->status_sertifikasi != 'Sudah'){
                $row[] = '<span class="text-danger"> Tidak</span>';
            } else {
                $row[] = '<span class="text-success"> Sudah</span>';
            }
            $row[] = '<span class="text-primary fs-5">'.$tbk->jumlah.' Tunjangan</span> <button class="btn btn-warning btn-xs text-dark" onclick="detail_tbk('."'".$datanya->id_penempatan."'".')"> <i class="fas fa-info-circle"></i> Lihat Detail</button>';
            $row[] = '<span class="text-primary fs-5">'.$potongan->jumlah.' Potongan</span><a class="btn btn-info btn-xs" onclick="detail_potongan('."'".$datanya->id_penempatan."'".')"> <i class="fas fa-info-circle"></i> Detail</a>';
            // if ($datanya->file_ktp == '') {
            //     $row[] = '<span class="text-danger">Belum Lengkap</span>';
            // } else {
            //     $row[] = '<a class="btn btn-primary btn-xs" target="_blank" href="' . base_url("Upload/" . $datanya->file_ktp) . '"> <i class="fas fa-file"> lihat </i> </a>';
            // }
            // if ($datanya->file_kk == '') {
            //     $row[] = '<span class="text-danger">Belum Lengkap</span>';
            // } else {
            //     $row[] = '<a class="btn btn-primary btn-xs" target="_blank" href="' . base_url("Upload/" . $datanya->file_kk) . '"> <i class="fas fa-file"> lihat </i> </a>';
            // }
            // if ($datanya->file_sk == '') {
            //     $row[] = '<span class="text-danger">Belum Lengkap</span>';
            // } else {
            //     $row[] = '<a class="btn btn-primary btn-xs" target="_blank" href="' . base_url("Upload/" . $datanya->file_sk) . '"> <i class="fas fa-file"> lihat </i> </a>';
            // }
            // $row[] = htmlentities($datanya->jumlah_anak);
			//add html for action
// 			$row[] = '<a type="button" class="btn btn-outline-danger btn-sm" href="#" 
// 			title="Track" onclick="edit_lampiran('."'".$datanya->nik."'".')"><i class="fas fa-edit" ></i> Edit</a>';
		$data[] = $row;
		}
			$output = array("data" => $data);
		echo json_encode($output);
	}

    public function ajax_add()
	{
		$this->_validate();
		$data = array(
				'id_pengguna' 		=> '',
				'username' 	=> $this->input->post('username'),
                'password' 	=> password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                'no_hp' 	=> $this->input->post('no_hp'),
                'jabatan' 	=> $this->input->post('jabatan'),
                'lembaga' 	=> $this->input->post('lembaga'),
				);

		$simpan = $this->Personalia_model->create('pengguna',$data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update(){
        $this->_validate_edit();
        if ($this->input->post('password') == '') {

            $data = array(
                    'username' 		=> $this->input->post('username'),
                    // 'password' 		=> password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                    'jabatan' 		=> $this->input->post('jabatan'),
                    'no_hp' 	    => $this->input->post('no_hp'),
                    'lembaga' 	=> $this->input->post('lembaga'),
                );
            } else {
                $data = array(
                    'username' 		=> $this->input->post('username'),
                    'password' 		=> password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                    'jabatan' 		=> $this->input->post('jabatan'),
                    'no_hp' 	    => $this->input->post('no_hp'),
                    'lembaga' 	=> $this->input->post('lembaga'),
                );
            }
		$this->Personalia_model->update(array('id_pengguna' => $this->input->post('id_pengguna')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_edit($id)
	{
		$data = $this->Personalia_model->get_by_id($id);
		echo json_encode($data);
	}

	private function _validate()
    {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        if ($this->input->post('username') == '') {
            $data['inputerror'][] = 'username';
            $data['error_string'][] = 'Nama harus diisi';
            $data['status'] = FALSE;
        }

        if ($this->input->post('password') == '') {
            $data['inputerror'][] = 'password';
            $data['error_string'][] = 'Password harus diisi';
            $data['status'] = FALSE;
        }

        if ($this->input->post('jabatan') == '') {
            $data['inputerror'][] = 'jabatan';
            $data['error_string'][] = 'Jabatan harus dipilih';
            $data['status'] = FALSE;
        }
        
         if ($this->input->post('lembaga') == '') {
            $data['inputerror'][] = 'lembaga';
            $data['error_string'][] = 'lembaga harus dipilih';
            $data['status'] = FALSE;
        }

      if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }

    private function _validate_edit()
    {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        if ($this->input->post('username') == '') {
            $data['inputerror'][] = 'username';
            $data['error_string'][] = 'Nama harus diisi';
            $data['status'] = FALSE;
        }

       

        if ($this->input->post('jabatan') == '') {
            $data['inputerror'][] = 'jabatan';
            $data['error_string'][] = 'Jabatan harus dipilih';
            $data['status'] = FALSE;
        }

      if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }


}
