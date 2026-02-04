<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Log_kehadiran_lemb extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Login_model');
		$this->load->model('log_kehadiran_lemb_model');
        $this->load->helper('Rupiah_helper');
	}

	public function index(){
		$this->Login_model->getsqurity() ;
		$isi['css'] 	= 'Log_kehadiran_lemb/Css';
		$isi['content'] = 'Log_kehadiran_lemb/Log_kehadiran';
		$isi['ajax'] 	= 'Log_kehadiran_lemb/Ajax';
		$this->load->view('Template',$isi);
	}

	public function data_list()
	{
		$this->load->helper('url');
		$list = $this->log_kehadiran_lemb_model->get_datatables();
		$no =1;
		$data = array();
		foreach ($list as $datanya) {
			
			$row = array();
			$row[] = $no++;
			$row[] = htmlentities($datanya->nama_lembaga);
			$row[] = htmlentities($datanya->kategori);
            $row[] = htmlentities($datanya->bulan);
            $row[] = htmlentities($datanya->tahun);
            $row[] = rupiah($datanya->jumlah_total);
            $row[] = htmlentities($datanya->status);
            $row[] = '<a href="" class="btn btn-info"> <i class="fas fa-file"></i></a>';
			//add html for action
			$row[] = '<a type="button" class="btn btn-outline-success btn-sm" href="#" 
			title="Track" onclick="edit_kehadiran('."'".$datanya->id_kehadiran_lembaga."'".')"><i class="mdi mdi-pencil mr-1" ></i> </a>
            <a type="button" class="btn btn-outline-danger btn-sm" href="Log_kehadiran_lemb/hapus/'.$datanya->id_kehadiran_lembaga.'">
			<i class="mdi mdi-delete mr-1" ></i> </a>';
		$data[] = $row;
		}
			$output = array("data" => $data);
		echo json_encode($output);
	}

    public function ajax_add()
	{
		// $this->_validate();
		$data = array(
				'id_lembaga' 	=> $this->input->post('id_lembaga'),
                'bulan'   => $this->input->post('bulan'),
				'tahun'   => $this->input->post('tahun'),
                'jumlah_total'   => $this->input->post('jumlah_total'),
                'status'   => $this->input->post('status'),
                );

		$simpan = $this->log_kehadiran_lemb_model->create('kehadiran_lembaga',$data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update(){
        // $this->_validate();
         $data = array(
            'id_lembaga' 	=> $this->input->post('id_lembaga'),
            'bulan'   => $this->input->post('bulan'),
            'tahun'   => $this->input->post('tahun'),
            'jumlah_total'   => $this->input->post('jumlah_total'),
            'status'   => $this->input->post('status'),
                );
           
		$this->log_kehadiran_lemb_model->update(array('id_kehadiran_lembaga' => $this->input->post('id_kehadiran_lembaga')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_edit($id)
	{
		$data = $this->log_kehadiran_lemb_model->get_by_id($id);
		echo json_encode($data);
	}

    public function hapus($id)
	{
    $this->db->where('id_kehadiran_lembaga', $id)->delete('kehadiran_lembaga');
    
    // Set pesan notifikasi menggunakan flashdata
    $this->session->set_flashdata('success', 'Data berhasil dihapus.');
    
    // Redirect ke halaman yang diinginkan
    redirect('Log_kehadiran_lemb');
	}
	

}
