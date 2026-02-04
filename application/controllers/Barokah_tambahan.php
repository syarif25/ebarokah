<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Barokah_tambahan extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Login_model');
		$this->load->model('Barokah_tambahan_model');
        $this->load->helper('Rupiah_helper');
	}

	// public function per_umana(){
	// 	$this->Login_model->getsqurity() ;
	// 	if ($this->session->userdata('jabatan') == 'AdminLembaga' or $this->session->userdata('jabatan') == 'umana' ){
	// 		$this->load->view('Error');
	// 	} else {
	// 	$isi['css'] 	= 'Barokah_tambahan/Css';
	// 	$isi['content'] = 'Barokah_tambahan/Barokah_tambahan';
	// 	$isi['ajax'] 	= 'Barokah_tambahan/Ajax';
	// 	$this->load->view('Template',$isi);
	// 	}
	// }

	public function data_list()
	{
		$this->load->helper('url');
		$list = $this->Barokah_tambahan_model->get_datatables();
		$no =1;
		$data = array();
		foreach ($list as $datanya) {
			
			$row = array();
			$row[] = $no++;
// 			$row[] = htmlentities(ucwords(strtolower($datanya->nama_lengkap)));
			$row[] = htmlentities($datanya->gelar_depan)." ".htmlentities(ucwords(strtolower($datanya->nama_lengkap)))." ".htmlentities($datanya->gelar_belakang);
            $row[] = htmlentities($datanya->nama_lembaga);
			$row[] = htmlentities($datanya->nama_barokah);
			$row[] = '<span class="text-success">'.rupiah($datanya->nominal_tambahan).'</span>';
			$row[] = date_singkat($datanya->min_periode_tambahan)." / ".date_singkat($datanya->max_periode_tambahan);
			//add html for action
			$row[] = '<a type="button" class="btn btn-outline-primary btn-sm" href="#" 
			title="Track" onclick="edit_potongan('."'".$datanya->id_barokah_tambahan."'".')"><i class="fas fa-edit mr-1" ></i> Edit</a>
			<a type="button" class="btn btn-outline-danger btn-sm" href="hapus/'.$datanya->id_barokah_tambahan.'"><i class="fas fa-trash mr-1" ></i> Hapus</a>
			';
		$data[] = $row;
		}
			$output = array("data" => $data);
		echo json_encode($output);
	}
	
	public function data_rincian_perumana($id)
	{
		// $id = 5;
		$this->load->helper('url');
		$list = $this->Barokah_tambahan_model->get_perumana_by_id($id);
		$no =1;
		$data = array();
		foreach ($list as $datanya) {
			
			$row = array();
			$row[] = $no++;
			// $row[] = htmlentities($datanya->nama_lengkap);
            $row[] = htmlentities($datanya->nama_barokah);
			$row[] = '<span class="text-success"> Rp. '.htmlentities(rupiah($datanya->nominal_tambahan)).' </span>';
			// $row[] = '<span class="text-success">'.rupiah($datanya->nominal_potongan).'</span>';
			$row[] = '<span class="text-primary fs-18 font-w400 d-block text-center">'.date_singkat($datanya->min_periode_tambahan)." <br> s.d <br> ".date_singkat($datanya->max_periode_tambahan).'</span>';
			// $row[] = '<span class="text-success"> Aktif </span>';
			//add html for action
			// $row[] = '
			$row[] = '<a type="button" class="btn btn-outline-info btn-sm" href="#" 
			title="Track" onclick="edit_potongan('."'".$datanya->id_barokah_tambahan."'".')"><i class="fas fa-edit mr-1" ></i> Edit</a>
			<a type="button" class="btn btn-outline-danger btn-sm" href="barokah_tambahan/hapus/'.$datanya->id_barokah_tambahan.'"><i class="fas fa-trash mr-1" ></i> Hapus</a>
			';
		$data[] = $row;
		}
		$total_potongan = $this->Barokah_tambahan_model->total_potongan($id);

		$totalnya = htmlentities(rupiah($total_potongan));

		$data_umana = $this->db->query("select nama_lengkap, nama_lembaga
		from umana, pengajar, lembaga
		 where pengajar.nik = umana.nik and pengajar.id_pengajar =  $id and lembaga.id_lembaga = pengajar.id_lembaga ")->row();
		// $totalnya = "Hello";
    
		$output = array("data" => $data, "total_potongan" => $totalnya, "data_umana" => $data_umana);
		echo json_encode($output);
	}
	
	public function get_potongan($id)
	{
		$data = $this->db->get_where('potongan', array('id_potongan' => $id))->row();
		echo json_encode($data);
	}

	
	public function index(){
		$this->Login_model->getsqurity() ;
		$isi['css'] 	= 'Barokah_tambahan/Css';
		$isi['content'] = 'Barokah_tambahan/Barokah_tambahan';
		$isi['ajax'] 	= 'Barokah_tambahan/Ajax';
		$this->load->view('Template',$isi);
	}

	public function data_perumana()
	{
		$this->load->helper('url');
		$list = $this->Barokah_tambahan_model->get_perumana();
		$no =1;
		$data = array();
		foreach ($list as $datanya) {
			
			$row = array();
			$row[] = $no++;
			$row[] = htmlentities($datanya->gelar_depan)." ".htmlentities(ucwords(strtolower($datanya->nama_lengkap)))." ".htmlentities($datanya->gelar_belakang);
            $row[] = htmlentities($datanya->nama_lembaga);
            $row[] = htmlentities($datanya->nama_barokah);
			$row[] = '<span class="fs-8">'.htmlentities($datanya->jml_brkh).' Barokah </span> <br> <span class="text-success"> Rp. '.htmlentities(rupiah($datanya->nominal)).' </span>';
			// $row[] = '<span class="text-success">'.rupiah($datanya->nominal_potongan).'</span>';
			$row[] = '<span class="text-primary fs-18 font-w400 d-block">'.date_singkat($datanya->min_periode_tambahan)." <br> s.d <br> ".date_singkat($datanya->max_periode_tambahan).'</span>';
			//add html for action
			$row[] = '
			<a type="button" class="btn btn-outline-success btn-sm" onclick="data_rincian_perumana('."'".$datanya->id_pengajar."'".')"><i class="fas fa-eye mr-1" ></i> Detail</a>
			';
		$data[] = $row;
		}
			$output = array("data" => $data);
		echo json_encode($output);
	}

    public function ajax_add()
	{
		$data = array(
			'id_barokah_tambahan' => '',
			'id_pengajar' 	=> $this->input->post('id_pengajar'),
			'nama_barokah' 	=> $this->input->post('nama_barokah'),
			'nominal_tambahan' 	=> str_replace(".", "",$this->input->post('nominal_tambahan')),
			'min_periode_tambahan' 		=> $this->input->post('min'),
			'max_periode_tambahan' 		=> $this->input->post('max'),
			);

		$simpan = $this->Barokah_tambahan_model->create('barokah_tambahan',$data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update(){
        // $this->_validate();
         $data = array(
            'id_pengajar' 	=> $this->input->post('id_pengajar2'),
			'nama_barokah' 	=> $this->input->post('nama_barokah'),
			'nominal_tambahan' 	=> str_replace(".", "",$this->input->post('nominal_tambahan')),
			'min_periode_tambahan' 		=> $this->input->post('min'),
			'max_periode_tambahan' 		=> $this->input->post('max'),
        );
           
		$this->Barokah_tambahan_model->update(array('id_barokah_tambahan' => $this->input->post('id')), $data);
		echo json_encode(array("status" => TRUE));
	}

    public function ajax_edit($id)
	{	
	    $data = $this->db->query("select id_barokah_tambahan, pengajar.id_pengajar, nama_lengkap, nama_lembaga, nama_barokah, nominal_tambahan, umana.nik, min_periode_tambahan, max_periode_tambahan from umana, pengajar, barokah_tambahan, lembaga where lembaga.id_lembaga = pengajar.id_lembaga and barokah_tambahan.id_pengajar = pengajar.id_pengajar and barokah_tambahan.id_barokah_tambahan = $id and umana.nik = pengajar.nik")->row();
		echo json_encode($data);
	}

	public function get_pengajar() {
        // $nik = $this->db->query("SELECT nama_jabatan, id_penempatan, nama_lembaga, nominal_tbk, min_periode, max_periode, nama_lengkap FROM penempatan, lembaga, ketentuan_barokah, umana where penempatan.id_ketentuan = ketentuan_barokah.id_ketentuan and penempatan.id_lembaga = lembaga.id_lembaga and penempatan.nik = umana.nik and penempata.id_penempatan = $id ")->row();
        $nik = $this->input->post('nik');
        $data = $this->db->query("SELECT id_pengajar, nama_lembaga FROM pengajar, lembaga where pengajar.id_lembaga = lembaga.id_lembaga and pengajar.nik = '$nik' ")->result();
		echo json_encode($data);
    }
    
    public function hapus($id)
	{
    $this->db->where('id_barokah_tambahan', $id)->delete('barokah_tambahan');
    
    // Redirect ke halaman yang diinginkan
    redirect('barokah_tambahan');
	}
	
	public function get_detail_potongan($id)
	{
	    $data_elemen = $this->db->query("SELECT nama_potongan, nominal_potongan from potongan, barokah_tambahan, penempatan where potongan.id_potongan = barokah_tambahan.jenis_potongan and barokah_tambahan.id_penempatan = penempatan.id_penempatan and barokah_tambahan.id_penempatan = $id and barokah_tambahan.max_periode_tambahan >= DATE(NOW())")->result();
		$data_jumlah = $this->db->query("SELECT sum(nominal_potongan) as jumlah from potongan, barokah_tambahan, penempatan where potongan.id_potongan = barokah_tambahan.jenis_potongan and barokah_tambahan.id_penempatan = penempatan.id_penempatan and barokah_tambahan.id_penempatan = $id and barokah_tambahan.max_periode_tambahan >= DATE(NOW())")->row();
		$data_jumlah->jumlah = rupiah($data_jumlah->jumlah);
		$data = array();
		$html_item = '';
		$no = 1;
		foreach ($data_elemen as $sow) {
			$html_item .= '<tr>';
			$html_item .= '<td><h6>'.$no++.'</h6></td>';
			$html_item .= '<td><h6>'.$sow->nama_potongan.'</h6></td>';
			$html_item .= '<td><h6>'.rupiah($sow->nominal_potongan).'</h6></td>';
			$html_item .= '</tr>';
		}

		$this->output->set_output(json_encode(array("data_jumlah" => $data_jumlah, "html_item" => $html_item)));

	}

  
}
