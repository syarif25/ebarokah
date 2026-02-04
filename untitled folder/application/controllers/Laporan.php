<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class laporan extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Login_model');
		$this->load->model('Laporan_model');
        $this->load->helper('Rupiah_helper');
        $this->load->library('user_agent');
	}

	public function index(){
		$this->Login_model->getsqurity() ;
		$isi['css'] 	= 'Laporan/Css';
		$isi['content'] = 'Laporan/Laporan';
		$isi['ajax'] 	= 'Laporan/Ajax';
		$this->load->view('Template',$isi);
	}

	public function per_umana(){
		$this->Login_model->getsqurity() ;
		$isi['css'] 	= 'Laporan/Css';
		$isi['content'] = 'Laporan/Laporan_perumana';
		$isi['ajax'] 	= 'Laporan/Ajax';
		$this->load->view('Template',$isi);
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

	public function data_list()
	{
		$this->load->helper('url');

		$list = $this->Laporan_model->get_datatables();
		$no =1;
		$data = array();
		foreach ($list as $datanya) {
			$encrypted_id = $this->encrypt_url($datanya->id_kehadiran_lembaga);
			$row = array();
			$row[] = $no++;
			$row[] = htmlentities($datanya->nama_lembaga);
            $row[] = htmlentities($datanya->bulan." ".$datanya->tahun);
            $row[] = "<span class='text-success'>".rupiah($datanya->jumlah_total)."</span>";
			//add html for action
			$row[] = '<a type="button" class="btn btn-outline-danger btn-sm" href="laporan/rincian/'.$encrypted_id.'"
			title="Track" ><i class="bx bx-edit mr-1" ></i> Rincian</a>';
			// onclick="rincian('."'".$datanya->id_kehadiran_lembaga."'".')"
		$data[] = $row;
		}
			$output = array("data" => $data);
		echo json_encode($output);
	}

	public function data_list_umana()
	{
		$this->load->helper('url');

		$list = $this->Laporan_model->get_datatables_perumana();
		$no =1;
		$data = array();
		foreach ($list as $datanya) {
			$encrypted_id2 = $this->encrypt_url($datanya->nik);
			$row = array();
			$row[] = $no++;
			$row[] = htmlentities($datanya->nik);
			$row[] = htmlentities($datanya->nama_lengkap);
			$row[] = htmlentities($datanya->alamat_domisili);
			//add html for action
			$row[] = '<a type="button" class="btn btn-outline-danger btn-sm" href="rincian_umana/'.$datanya->nik.'"
			title="Track" ><i class="bx bx-edit mr-1" ></i> Rincian</a>';
			// onclick="rincian('."'".$datanya->id_kehadiran_lembaga."'".')"
		$data[] = $row;
		}
			$output = array("data" => $data);
		echo json_encode($output);
	}

	public function fullscreen(){
		$this->load->view('Detail_fullscreen.php');
	}

    public function rincian($id)
	{
		// $data['data'] = $this->Laporan_model->get_datatables_rincian($id);
		// echo json_encode($data); // kirim data sebagai JSON
		$decrypted_id = $this->decrypt_url($id);
		$list = $this->Laporan_model->get_datatables_rincian($decrypted_id);
		$this->Login_model->getsqurity() ;

		$isi['css'] 	= 'Laporan/Css';
		$isi['content'] = 'Laporan/Rincian_kehadiran';
		$isi['ajax'] 	= 'Laporan/Ajax';
		// $isi['isitunkel']  = $tunkel_get;
		$isi['isilist']  = $list;
		$this->load->view('Template',$isi);
	}

	public function rincian_umana($id)
	{
		// $data['data'] = $this->Laporan_model->get_datatables_rincian($id);
		// echo json_encode($data); // kirim data sebagai JSON
		$decrypted_id2 = $this->decrypt_url($id);
		$list = $this->Laporan_model->get_datatables_rincian_perumana($id);
		$this->Login_model->getsqurity() ;

		$isi['css'] 	= 'Laporan/Css';
		$isi['content'] = 'Laporan/Rincian_perumana';
		$isi['ajax'] 	= 'Laporan/Ajax';
		// $isi['isitunkel']  = $tunkel_get;
		$isi['isilist']  = $list;
		$this->load->view('Template',$isi);
	}
	
		public function get_kehadiran_data()
	{
		$id = $this->session->userdata('nik');
		$list = $this->Laporan_model->get_datatables_rincian_perumana($id);
		// Ubah data menjadi format JSON
		$data = json_encode($list);
		echo $data;
	}
	
	
	public function kehadiran()
	{
		$id = $this->session->userdata('nik');
		$list = $this->Laporan_model->get_datatables_rincian_perumana($id);
		$this->Login_model->getsqurity() ;

		if ($this->agent->is_mobile())
		{
			$isi['isilist'] = $list;
			$isi['ajax'] 	= 'Mobile/Ajax';
			$isi['content'] = 'Mobile/Barokah';
			$this->load->view('Mobile/Mobile',$isi);
			
		}
		else
		{
			$isi['css'] 	= 'Laporan/Css';
			$isi['content'] = 'Laporan/Rincian_perumana';
			$isi['ajax'] 	= 'Laporan/Ajax';
			// $isi['isitunkel']  = $tunkel_get;
			$isi['isilist']  = $list;
			$this->load->view('Template',$isi);
		}
	}
	
	public function kehadiran_pengajar()
	{
		$id = $this->session->userdata('nik');
		$list = $this->Laporan_model->get_datatables_rincian_pengajar($id);
		$this->Login_model->getsqurity() ;

		if ($this->agent->is_mobile())
		{
			$isi['isilist'] = $list;
			$isi['ajax'] 	= 'Mobile/Ajax';
			$isi['content'] = 'Mobile/Barokah_pengajar';
// 			$isi['content'] = 'Dashboard';
			$this->load->view('Mobile/Mobile',$isi);
			
		}
		else
		{
			$isi['css'] 	= 'Laporan/Css';
			$isi['content'] = 'Laporan/Rincian_pengajar';
			$isi['ajax'] 	= 'Laporan/Ajax';
			// $isi['isitunkel']  = $tunkel_get;
			$isi['isilist']  = $list;
			$this->load->view('Template',$isi);
		}
	}
	
	public function get_detail_barokah_umana($id)
	{
		$data = $this->db->get_where('total_barokah', array('id_total_barokah' => $id))->row();
		$data->tunjab = rupiah($data->tunjab);
		$data->nominal_kehadiran = rupiah($data->nominal_kehadiran);
		$data->tunkel = rupiah($data->tunkel);
		$data->tunj_anak = rupiah($data->tunj_anak);
		$data->tmp = rupiah($data->tmp);
		$data->potongan = rupiah($data->potongan);
		$data->diterima = rupiah($data->diterima);
		$data->tbk = rupiah($data->tbk);
		$data->kehormatan = rupiah($data->kehormatan);
		$data->jml_hadir = $data->kehadiran;
		
		$id_penempatan = $data->id_penempatan;
		$tgl = $data->timestamp;

		$list = $this->db->query("select penempatan.id_penempatan, nama_lengkap, nama_potongan,nominal_potongan as nominal from umana, 
		penempatan, potongan, potongan_umana WHERE potongan.id_potongan = potongan_umana.jenis_potongan and potongan_umana.id_penempatan = penempatan.id_penempatan and umana.nik = penempatan.nik 
		and penempatan.id_penempatan = '$id_penempatan' and potongan_umana.max_periode_potongan >= '$tgl' and potongan_umana.min_periode_potongan < '$tgl' ")->result();
		
		foreach ($list as $item) {
			$item->nominal = number_format($item->nominal, 0, ',', '.');
		}

		$this->output->set_output(json_encode(array( "data" => $data, "list" => $list)));
	}
   
    public function get_detail_barokah_pengajar($id)
	{
		$data = $this->db->get_where('total_barokah_pengajar', array('id_total_barokah_pengajar' => $id))->row();
		$data->jumlah_sks = rupiah($data->jumlah_sks);
		$data->rank = rupiah($data->rank);
		$data->mengajar = rupiah($data->mengajar);
		$data->dty = rupiah($data->dty);
		$data->jafung = rupiah($data->jafung);
		
		$data->nominal_kehadiran = rupiah($data->nominal_kehadiran);
		$data->nominal_kehadiran_15 = rupiah($data->nominal_hadir_15);
		$data->nominal_kehadiran_10 = rupiah($data->nominal_hadir_10);
		
		$data->tunkel = rupiah($data->tunkel);
		$data->tun_anak = rupiah($data->tun_anak);
		$data->mp = rupiah($data->mp);
		
		
		$data->walkes = rupiah($data->walkes);
		$data->potongan = rupiah($data->potongan);
		$data->khusus = rupiah($data->khusus);
		$data->diterima = rupiah($data->diterima);
		$data->kehormatan = rupiah($data->kehormatan);
		$data->barokah_piket = rupiah($data->barokah_piket);
		
		$id_pengajar = $data->id_pengajar;
		$tgl = $data->timestamp;

		$list = $this->db->query("select pengajar.id_pengajar, nama_lengkap, nama_potongan,nominal_potongan as nominal from umana, 
		pengajar, potongan, potongan_pengajar WHERE potongan.id_potongan = potongan_pengajar.jenis_potongan and potongan_pengajar.id_pengajar = pengajar.id_pengajar and umana.nik = pengajar.nik 
		and pengajar.id_pengajar = '$id_pengajar' and potongan_pengajar.max_periode_potongan >= '$tgl' and potongan_pengajar.min_periode_potongan < '$tgl' ")->result();
		
		foreach ($list as $item) {
			$item->nominal = number_format($item->nominal, 0, ',', '.');
		}

		$this->output->set_output(json_encode(array( "data" => $data, "list" => $list)));
	}
	
	public function per_bulan(){
		$this->Login_model->getsqurity() ;
		$isi['css'] 	= 'Laporan_perbulan/Css';
		$isi['content'] = 'Laporan_perbulan/Laporan_perbulan';
		$isi['ajax'] 	= 'Laporan_perbulan/Ajax';
		$this->load->view('Template',$isi);
	}

	public function data_list_perbulan()
	{
		$this->load->helper('url');

		$list = $this->Laporan_model->get_laporan_perbulan();
		$no =1;
		$data = array();
		foreach ($list as $datanya) {
			$row = array();
			$row[] = $no++;
			$row[] = htmlentities($datanya->bulan);
			$row[] = rupiah($datanya->jumlah_total);
			$row[] = htmlentities($datanya->jml_lembaga).' Lembaga';
			//add html for action
			$row[] = '<a type="button" class="btn btn-outline-danger btn-sm" onclick="detail('."'".$datanya->bulan.' '.$datanya->tahun."'".')" ><i class="bx bx-edit mr-1" ></i> Rincian</a>';
		$data[] = $row;
		}
		$output = array("data" => $data);
		echo json_encode($output);
	}

	public function data_list_perbulan_perlembaga()
	{
		$this->load->helper('url');
		// Mengambil data bulan dan tahun dari request Ajax
		$bulan_tahun = $this->input->post('bulan_tahun');

		// Memisahkan bulan dan tahun
		list($bulan, $tahun) = explode(' ', $bulan_tahun);

		// Query data berdasarkan bulan dan tahun
		$query = $this->db->query("SELECT kehadiran_lembaga.id_kehadiran_lembaga, lembaga.nama_lembaga, kehadiran_lembaga.jumlah_total
									FROM lembaga
									JOIN kehadiran_lembaga ON lembaga.id_lembaga = kehadiran_lembaga.id_lembaga
									WHERE kehadiran_lembaga.bulan = '$bulan' AND kehadiran_lembaga.tahun = '$tahun'");
		$data = $query->result_array();

		$output = array("data" => array());
		$no = 1;
		// var_dump($bulan_tahun);
		foreach ($data as $datanya) {
			$row = array();
			$row[] = $no++;
			$row[] = htmlentities($datanya['nama_lembaga']);
			$row[] = rupiah($datanya['jumlah_total']);
			// Add html for action
			$row[] = '<a target="_blank" href="Kehadiran/Cetak/'.$datanya['id_kehadiran_lembaga'].'" class="btn btn-outline-info btn-sm" ><i class="bx bx-edit mr-1" ></i> Lihat</a>';
			$output['data'][] = $row;
		}
		echo json_encode($output);
	}

}
