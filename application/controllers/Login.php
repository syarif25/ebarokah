<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
    public function index()
	{
		$this->load->view('Login');
		// $this->load->model('Login_model');
	}

	function aksi_login(){
	$username 	= $this->input->post('username');
		$password 	= $this->input->post('password');
		
		$this->db->select('pengguna.*, lembaga.*');
        $this->db->from('pengguna');
        $this->db->join('lembaga', 'pengguna.lembaga = lembaga.id_lembaga');
        $this->db->where('pengguna.username', $username);
        $user = $this->db->get()->row_array();
        
        
		if ($user) {
            // jika password yg diinput sesuai dgn didatabase
            if (password_verify($password, $user['password'])) {
                
                    $data['username']       = $user['username'];
                    $data['id_pengguna']    = $user['id_pengguna'];
                    $data['jabatan']  		= $user['jabatan'];
                    $data['lembaga']        = $user['lembaga'];
                    $data['foto']           = $user['foto'];
                    $data['tenaga_pengajar']        = $user['tenaga_pengajar'];
                    $this->session->set_userdata($data);
                    if ($user['jabatan'] == 'AdminLembaga'){
                        redirect('Dashboard/lembaga');
                    } else {
                        redirect('Dashboard/petugas');
                    }
            } else {
                // jika password yg diinput tidak sesuai dengan didatabase
                $this->session->set_flashdata('login-failed-1', 'Gagal');
                redirect('login');
            }
        } 

        $umana = $this->db->get_where('umana', ['nomor_hp' => $username])->row_array();
        if($umana){
            // jika password yg diinput sesuai dgn didatabase
            if (password_verify($password, $umana['password'])) {
                
                $data['username']       = $umana['nama_lengkap'];
                $data['nik']            = $umana['nik'];
                $data['jk']             = $umana['jk'];
                $data['jabatan']        = 'umana';
                $this->session->set_userdata($data);
            redirect('Report');
            } else {
                // jika password yg diinput tidak sesuai dengan didatabase
                $this->session->set_flashdata('login-failed-1', 'Gagal');
                redirect('login');
            }
        }
       
        // jika username dan passsword salah
        $this->session->set_flashdata('login-failed-2', 'Gagal');
        redirect('login');
	
	}

	function logout(){
	  // hapus session
        $this->session->unset_userdata('username');

        // tampilkan flash message
        $this->session->set_flashdata('logout-success', 'Berhasil');
        redirect('login');
	}

}
