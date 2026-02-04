<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Log_barokah extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
        $this->load->model('Login_model');
		$this->load->model('logbarokah_model');
        $this->load->helper('Rupiah_helper');
	}

	public function index(){
		$this->Login_model->getsqurity() ;
		$isi['css'] 	= 'Log_barokah/Css';
		$isi['content'] = 'Log_barokah/Log_barokah';
		$isi['ajax'] 	= 'Log_barokah/Ajax';
		$this->load->view('Template',$isi);
	}
	
	
    public function pengajar(){
		$this->Login_model->getsqurity() ;
		$isi['css'] 	= 'Log_barokah/Css';
		$isi['content'] = 'Log_barokah_dosen/Log_barokah';
		$isi['ajax'] 	= 'Log_barokah_dosen/Ajax';
		$this->load->view('Template',$isi);
	}

	public function data_list()
	{
		$this->load->helper('url');
		$list = $this->logbarokah_model->get_datatables();
		$no =1;
		$data = array();
		foreach ($list as $datanya) {
			
			$row = array();
			$row[] = $no++;
			$row[] = htmlentities($datanya->nama_lengkap);
            $row[] = $datanya->nama_lembaga;
            $row[] = $datanya->bulan;
            $row[] = $datanya->tahun;
            $row[] = rupiah($datanya->tunjab);
            $row[] = $datanya->mp;
            $row[] = rupiah($datanya->kehadiran);
            $row[] = rupiah($datanya->nominal_kehadiran);
            $row[] = rupiah($datanya->tunkel);
            $row[] = rupiah($datanya->tunanak);
            $row[] = rupiah($datanya->tmp);
            $row[] = rupiah($datanya->tot_kehormatan);
            $row[] = rupiah($datanya->tbk);
            $row[] = rupiah($datanya->potongan);
            $row[] = rupiah($datanya->tot_diterima);
            $row[] = $datanya->tot_status;
			//add html for action
			$row[] = '<a type="button" class="btn btn-outline-danger btn-sm" href="#" 
			title="Track" onclick="edit_potongan('."'".$datanya->id_total_barokah."'".')"><i class="bx bx-edit mr-1" ></i> Edit</a>';
		$data[] = $row;
		}
			$output = array("data" => $data);
		echo json_encode($output);
	}
	
	
    public function data_list_pengajar()
	{
		$this->load->helper('url');
		$list = $this->logbarokah_model->get_datatables_pengajar();
		$no =1;
		$data = array();
		foreach ($list as $datanya) {
			
			$row = array();
			$row[] = $no++;
			$row[] = htmlentities($datanya->nama_lengkap);
            $row[] = $datanya->nama_lembaga;
            $row[] = $datanya->bulan;
            $row[] = $datanya->tahun;
            $row[] = rupiah($datanya->jumlah_sks);
            $row[] = rupiah($datanya->rank);
            $row[] = rupiah($datanya->mengajar);
            $row[] = rupiah($datanya->mp);
            $row[] = rupiah($datanya->dty);
            $row[] = rupiah($datanya->jafung);
            $row[] = $datanya->jumlah_hadir;
            $row[] = rupiah($datanya->nominal_kehadiran);
            $row[] = rupiah($datanya->tunkel);
            $row[] = rupiah($datanya->tun_anak);
            $row[] = rupiah($datanya->tot_kehormatan);
            $row[] = rupiah($datanya->tot_walkes);
            $row[] = rupiah($datanya->khusus);
            $row[] = rupiah($datanya->potongan);
            $row[] = rupiah($datanya->tot_diterima);
            $row[] = $datanya->tot_status;
			//add html for action
			$row[] = '<a type="button" class="btn btn-outline-danger btn-sm" href="#" 
			title="Track" onclick="edit_potongan('."'".$datanya->id_total_barokah_pengajar."'".')"><i class="bx bx-edit mr-1" ></i> Edit</a>';
		$data[] = $row;
		}
			$output = array("data" => $data);
		echo json_encode($output);
	}

    public function ajax_add()
	{
		$this->_validate();
		$data = array(
				'id_tunkel' 	=> '',
				'nama_tunkel' => $this->input->post('nama_tunkel'),
                'besaran_tunkel' 	    => str_replace(".", "",$this->input->post('nominal')),
                );

		$simpan = $this->logbarokah_model->create('potongan',$data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update(){
        // $this->_validate();
         $data = array(
                'bulan' => $this->input->post('bulan'),
                'tahun' => $this->input->post('tahun'),
                'tunjab' => $this->input->post('tunjab'),
                'mp' => $this->input->post('mp'),
                'kehadiran' => $this->input->post('kehadiran'),
                'nominal_kehadiran' => $this->input->post('nominal'),
                'tunkel' => $this->input->post('tunkel'),
                'tunj_anak' => $this->input->post('tunjanak'),
                'tmp' => $this->input->post('tmp'),
                'kehormatan' => $this->input->post('kehormatan'),
                'tbk' => $this->input->post('tbk'),
                'potongan' => $this->input->post('potongan'),
                'diterima' => $this->input->post('diterima'),
                'status' => $this->input->post('status'),
                );
		$this->logbarokah_model->update(array('id_total_barokah' => $this->input->post('id_total_barokah')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_edit($id)
	{
		$data = $this->logbarokah_model->get_by_id($id);
		echo json_encode($data);
	}

	private function _validate()
    {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        if ($this->input->post('nama_tunkel') == '') {
            $data['inputerror'][] = 'nama_tunkel';
            $data['error_string'][] = 'Nama harus diisi';
            $data['status'] = FALSE;
        }

        if ($this->input->post('nominal') == '') {
            $data['inputerror'][] = 'nominal';
            $data['error_string'][] = 'Nominal harus diisi';
            $data['status'] = FALSE;
        }

        if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }



}
