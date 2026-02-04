<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tunjangan_bk extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Login_model');
		$this->load->model('Tbk_model');
        $this->load->helper('Rupiah_helper');
	}

	public function index(){
		$this->Login_model->getsqurity() ;
		$isi['css'] 	= 'TBK/css';
		$isi['content'] = 'TBK/TBK';
		$isi['ajax'] 	= 'TBK/Ajax';
		$this->load->view('Template',$isi);
	}

	public function data_list()
	{
		$this->load->helper('url');
		$list = $this->Tbk_model->get_datatables();
		$no =1;
		$data = array();
		foreach ($list as $datanya) {
			
			$row = array();
			$row[] = $no++;
			$namaLengkap = ucwords(strtolower($datanya->nama_lengkap));
            $row[] = htmlentities($datanya->gelar_depan)." ".htmlentities($namaLengkap)." ".htmlentities($datanya->gelar_belakang);
            $row[] = htmlentities($datanya->nama_lembaga);
            $row[] = htmlentities($datanya->jenis_tbk);
			$row[] = rupiah($datanya->nominal_tbk);
			$row[] = date_singkat($datanya->min_periode)." / ".date_singkat($datanya->max_periode);
			//add html for action
			$row[] = '<a type="button" class="btn btn-outline-primary btn-sm" href="#" 
			title="Track" onclick="edit_tbk('."'".$datanya->id_tbk."'".')"><i class="fas fa-edit mr-1" ></i> Edit</a>
			<a type="button" class="btn btn-outline-danger btn-sm" href="tunjangan_bk/hapus/'.$datanya->id_tbk.'"><i class="fas fa-trash mr-1" ></i> Hapus</a>
			';
			
		$data[] = $row;
		}
			$output = array("data" => $data);
		echo json_encode($output);
	}

    public function ajax_add()
	{
		$data = array(
			'id_tbk' 			=> '',
			'id_penempatan' 	=> $this->input->post('id_penempatan'),
			'nominal_tbk' 	    => str_replace(".", "",$this->input->post('nominal')),
			'jenis_tbk' 		=> $this->input->post('jenis_tbk'),
			'min_periode' 		=> $this->input->post('min'),
			'max_periode' 		=> $this->input->post('max'),
			);

		$simpan = $this->Tbk_model->create('t_beban_kerja',$data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update(){
        // $this->_validate();
         $data = array(
                'nominal_tbk' 	    => str_replace(".", "",$this->input->post('nominal')),
                'jenis_tbk' 		=> $this->input->post('jenis_tbk'),
    			'min_periode' 		=> $this->input->post('min'),
    			'max_periode' 		=> $this->input->post('max'),
                );
           
		$this->Tbk_model->update(array('id_tbk' => $this->input->post('id_tbk')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_edit($id)
	{
	    $data = $this->db->query("select id_tbk, nama_lengkap, nama_lembaga, umana.nik, nominal_tbk, min_periode,max_periode, jenis_tbk from umana, penempatan, t_beban_kerja, lembaga where lembaga.id_lembaga = penempatan.id_lembaga and t_beban_kerja.id_penempatan = penempatan.id_penempatan and t_beban_kerja.id_tbk = $id and umana.nik = penempatan.nik")->row();
		echo json_encode($data);
	}
	
	public function get_detail_tbk($id)
	{
	    $data_elemen = $this->db->query("SELECT jenis_tbk, nominal_tbk from t_beban_kerja, penempatan where t_beban_kerja.id_penempatan = penempatan.id_penempatan and t_beban_kerja.id_penempatan = $id and t_beban_kerja.max_periode >= DATE(NOW())")->result();
		$data_jumlah = $this->db->query("SELECT jenis_tbk, nominal_tbk, sum(nominal_tbk) as jumlah from t_beban_kerja, penempatan where t_beban_kerja.id_penempatan = penempatan.id_penempatan and t_beban_kerja.id_penempatan = $id and t_beban_kerja.max_periode >= DATE(NOW())")->row();
		$data_jumlah->jumlah = rupiah($data_jumlah->jumlah);
		$data = array();
		$html_item = '';
		$no = 1;
		foreach ($data_elemen as $sow) {
			$html_item .= '<tr>';
			$html_item .= '<td><h6>'.$no++.'</h6></td>';
			$html_item .= '<td><h6>'.$sow->jenis_tbk.'</h6></td>';
			$html_item .= '<td><h6>'.rupiah($sow->nominal_tbk).'</h6></td>';
			$html_item .= '</tr>';
		}

		$this->output->set_output(json_encode(array("data_jumlah" => $data_jumlah, "html_item" => $html_item)));

	}

	public function get_penempatan() {
	    $nik = $this->input->post('nik');
        // $nik = $this->db->query("SELECT nama_jabatan, id_penempatan, nama_lembaga, nominal_tbk, min_periode, max_periode, nama_lengkap FROM penempatan, lembaga, ketentuan_barokah, umana where penempatan.id_ketentuan = ketentuan_barokah.id_ketentuan and penempatan.id_lembaga = lembaga.id_lembaga and penempatan.nik = umana.nik and penempata.id_penempatan = $id ")->row();
        $data = $this->db->query("SELECT nama_jabatan, id_penempatan, nama_lembaga FROM penempatan, lembaga, ketentuan_barokah where penempatan.id_ketentuan = ketentuan_barokah.id_ketentuan and penempatan.id_lembaga = lembaga.id_lembaga and penempatan.nik = '$nik' ")->result();
		echo json_encode($data);
    }
    
    public function hapus($id)
	{
    $this->db->where('id_tbk', $id)->delete('t_beban_kerja');
    $this->session->set_flashdata('success', 'Data berhasil dihapus.');
     $this->session->set_flashdata('action', 'delete');
    // Redirect ke halaman yang diinginkan
    redirect('tunjangan_bk');
	}

	private function _validate()
    {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        if ($this->input->post('masa_pengabdian') == '') {
            $data['inputerror'][] = 'masa_pengabdian';
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
