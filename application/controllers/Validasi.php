<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Validasi extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Login_model');
		$this->load->library('Pdf'); 
		$this->load->helper('Rupiah_helper');
	}

	public function index(){
		$this->Login_model->getsqurity() ;
		if ($this->session->userdata('jabatan') == 'AdminLembaga' or $this->session->userdata('jabatan') == 'umana' ){
			$this->load->view('Error');
		} else {
    		$isi['css'] 	= 'Validasi/Css';
    		$isi['content'] = 'Validasi/Validasi';
    		$isi['ajax'] 	= 'Validasi/Ajax';
    		$this->load->view('Template',$isi);
		}
	}

	public function jumlah(){
		$this->Login_model->getsqurity() ;
		if ($this->session->userdata('jabatan') == 'AdminLembaga' or $this->session->userdata('jabatan') == 'umana' ){
			$this->load->view('Error');
		} else {
    		$isi['css'] 	= 'Validasi/Css';
    		$isi['content'] = 'Validasi/Jumlah_kehadiran';
    		$isi['ajax'] 	= 'Validasi/Ajax';
    		$this->load->view('Template',$isi);
		}
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
		$list = $this->db->query("SELECT  kehadiran_lembaga.bulan, kehadiran_lembaga.tahun, lembaga.id_lembaga, lembaga.id_bidang, kehadiran_lembaga.id_kehadiran_lembaga, kehadiran_lembaga.status, lembaga.nama_lembaga ,  kehadiran_lembaga.kategori  from
		 lembaga, umana, kehadiran_lembaga where kehadiran_lembaga.id_lembaga = lembaga.id_lembaga and kehadiran_lembaga.status != 'Belum' and kehadiran_lembaga.status != 'Sudah' and kehadiran_lembaga.status != 'selesai' GROUP BY kehadiran_lembaga.id_kehadiran_lembaga order by kehadiran_lembaga.id_kehadiran_lembaga desc ")->result();
		// $list = $this->db->get($query);
		$no =1;
		$data = array();
		foreach ($list as $datanya) {
			$encrypted_id = $this->encrypt_url($datanya->id_kehadiran_lembaga);
			$row = array();
			$row[] = $no++;
			$row[] = htmlentities($datanya->nama_lembaga);
            $row[] = htmlentities($datanya->bulan." ".$datanya->tahun);
			// $row[] = htmlentities($datanya->jml)." org";
			$row[] = htmlentities($datanya->kategori);
		    if ($datanya->status == 'acc'){
				$row[] = '<a class="btn btn-success  me-1 px-3"><i class="fa fa-check m-0"></i> Jml Kehadiran</a>';
				$row[] = '<span class="text-success">Sudah dikoreksi </span><br><small>(Menunggu ditransfer)</small>';
			} elseif ($datanya->status == 'Terkirim') {
				if ($datanya->kategori == 'Struktural'){
					$row[] = '<a href="Validasi_struktural/koreksi/'.$encrypted_id.'"  class="btn btn-info  me-1 px-3"><i class="fa fa-clipboard m-0"></i> Jml Kehadiran</a>';
				} elseif ($datanya->kategori == 'Satpam'){
					$row[] = '<a href="validasi_satpam/koreksi_satpam/'.$encrypted_id.'"  target="_blank" class="btn btn-secondary  me-1 px-3"><i class="fa fa-clipboard m-0"></i> Jml Kehadiran</a>';
				} else {
					$row[] = '<a href="validasi_pengajar/koreksi/'.$encrypted_id.'"  class="btn btn-info  me-1 px-3"><i class="fa fa-clipboard m-0"></i> Jml Kehadiran</a>';
				}
				$row[] = '<span class="text-danger">Belum dikoreksi</span>';
			} else {
			    $row[] = '<a href="#"  class="btn btn-info  me-1 px-3"><i class="fa fa-check m-0"></i> </a>';
				$row[] = '<span class="text-info">Sudah Ditransfer</span>';
			}
			
			//add html for action
			// onclick="jumlah('."'".$datanya->id_kehadiran_lembaga."'".')"
			// href="validasi/koreksi/'.$datanya->id_kehadiran_lembaga.'" 
		    $data[] = $row;
		}
		$output = array("data" => $data);
		echo json_encode($output);
	}

	public function koreksi($id){
	    $decrypted_id = $this->decrypt_url($id);
		$list = $this->db->query("select id_kehadiran, kehadiran_lembaga.status, ketentuan_barokah.id_ketentuan, tunj_mp, id_bidang, tunj_anak, umana.gelar_depan, umana.gelar_belakang, kehormatan, kehadiran_lembaga.file, tunj_kel, id_kehadiran_lembaga, nama_lengkap, nama_jabatan, status_nikah, tmt_struktural, kehadiran.id_penempatan, kehadiran.bulan, kehadiran.tahun, jumlah_hadir, nama_lembaga, barokah, nominal_transport from umana, penempatan, kehadiran, kehadiran_lembaga, lembaga, ketentuan_barokah, transport WHERE 
		kehadiran_lembaga.id_kehadiran_lembaga = kehadiran.id_kehadi and 
		penempatan.id_penempatan = kehadiran.id_penempatan and 
		penempatan.nik = umana.nik and 
		penempatan.id_lembaga = lembaga.id_lembaga and 
		penempatan.id_ketentuan = ketentuan_barokah.id_ketentuan and 
		penempatan.kategori_trans = transport.id_transport and 
		DATEDIFF(NOW(), penempatan.tgl_mulai) < penempatan.tgl_selesai and
		kehadiran_lembaga.id_kehadiran_lembaga = $decrypted_id order by ketentuan_barokah.id_ketentuan asc ")->result();
		$no = 1;
// 		$data_elemen = $this->db->query("SELECT jenis_tbk, nominal_tbk from t_beban_kerja, penempatan, sum(nominal_tbk) as jumlah where t_beban_kerja.id_penempatan = penempatan.id_penempatan and t_beban_kerja.id_penempatan = $decrypted_id and t_beban_kerja.max_periode >= DATE(NOW())")->result();
		$tunkel_get = $this->db->get('tunkel')->result();
		$tunj_anak_get = $this->db->get('tunjanak')->result();
		
		$this->Login_model->getsqurity() ;
        if ($this->session->userdata('jabatan') == 'AdminLembaga' or $this->session->userdata('jabatan') == 'umana' ){
			$this->load->view('Error');
		} else {
		    
    		$isi['css'] 	= 'Validasi/Css';
    		$isi['content'] = 'Validasi/Jumlah_kehadiran';
    		$isi['ajax'] 	= 'Validasi/Ajax';
    		$isi['isitunkel']  = $tunkel_get;
    		$isi['isitunj_anak']  = $tunj_anak_get;
    		$isi['isilist']  = $list;
    // 		$isi['isitbk']  = $data_elemen;
    		$this->load->view('Template',$isi);
		}
		
	}

	public function save_data() {
		$id = $this->input->post('id_kehadiran_lembaga');
		$bulan = $this->input->post('bulan');
		$id_kehadiran = $this->input->post('id_kehadiran');
		$tahun = $this->input->post('tahun');
		$nik = $this->input->post('nik_umana');
		$jabatan = $this->input->post('jabatan');
		$tmt_struktural = $this->input->post('tmt_struktural');
		$mp = $this->input->post('mp');
		$tunjab = $this->input->post('tunjab');
		$jml_hadir = $this->input->post('jml_hadir');
		$nominal_kehadiran = $this->input->post('nominal_kehadiran');
		$tunkel = $this->input->post('tunkel');
		$tunj_anak = $this->input->post('tunj_anak');
		$tmp = $this->input->post('tmp');
		$kehormatan = $this->input->post('kehormatan');
		$tbk = $this->input->post('tbk');
		$potongan = $this->input->post('potongan');
		$diterima = $this->input->post('diterima');
		$jumlah_total = $this->input->post('jumlah_total');
		
		for ($i = 0; $i < count($bulan); $i++) {
		  $data = array(
			'id_total_barokah' => '',
			'id_penempatan' => $nik[$i],
			'id_kehadiran' => $id_kehadiran[$i],
			'bulan' => $bulan[$i],
			'tahun' => $tahun[$i],
			'tunjab' => $tunjab[$i],
			'mp' => $mp[$i],
			'kehadiran' => $jml_hadir[$i],
			'nominal_kehadiran' => $nominal_kehadiran[$i],
			'tunkel' => $tunkel[$i],
			'tunj_anak' => $tunj_anak[$i],
			'tmp' => $tmp[$i],
			'kehormatan' => $kehormatan[$i],
			'tbk' => $tbk[$i],
			'potongan' => $potongan[$i],
			'barokah_khusus' => '0',
			'diterima' => $diterima[$i]
		  );
		  $this->db->insert('total_barokah', $data);
		//   $jumlah_total = array_sum($diterima[$i]);
		}
		$data2 = array(
			'status' => "acc",
			'jumlah_total' => $jumlah_total
		  );

		$this->db->where('id_kehadiran_lembaga', $id);
		$this->db->update('kehadiran_lembaga', $data2);
		// redirect('validasi/index');
		// Tampilkan pesan sukses atau kembali ke halaman sebelumnya
	  }

	  public function koreksi_pengajar($id){
	    $decrypted_id = $this->decrypt_url($id);
		//pengajar
		$list2 = $this->db->query("select pengajar.status as status_aktif, jumlah_hadir_piket, jumlah_hadir_15, jumlah_hadir_10, jafung, lembaga.id_lembaga, kehadiran_lembaga.status, status_sertifikasi, walkes, kehadiran_pengajar.id_kehadiran_pengajar, pengajar.kategori, jabatan_akademik, jumlah_sks, status_sertifikasi, ijazah_terakhir, id_bidang, tunj_anak, umana.gelar_depan, umana.gelar_belakang, kehormatan, kehadiran_lembaga.file, tunj_kel, kehadiran_lembaga.id_kehadiran_lembaga, 
		nama_lengkap, status_nikah, tmt_dosen, tmt_guru,tmt_maif, kehadiran_pengajar.id_pengajar, kehadiran_pengajar.bulan, kehadiran_pengajar.tahun, jumlah_hadir, nama_lembaga, nominal_transport from umana, pengajar, kehadiran_pengajar, kehadiran_lembaga,
		lembaga, transport WHERE 
		kehadiran_lembaga.id_kehadiran_lembaga = kehadiran_pengajar.id_kehadiran_lembaga and 
		pengajar.id_pengajar = kehadiran_pengajar.id_pengajar and 
		pengajar.nik = umana.nik and 
		pengajar.id_lembaga = lembaga.id_lembaga and 
		pengajar.kategori_trans = transport.id_transport and 
		DATEDIFF(NOW(), pengajar.tgl_mulai) < pengajar.tgl_selesai and
		kehadiran_lembaga.id_kehadiran_lembaga = $decrypted_id order by nama_lengkap asc ")->result();
		$no = 1;
		$tunkel_get = $this->db->get('tunkel')->result();
		$tunj_anak_get = $this->db->get('tunjanak')->result();
		// $kehormatan = $this->db->query('select * from ');
		$this->Login_model->getsqurity() ;

		$isi['css'] 	= 'Validasi/Css';
		$isi['content'] = 'Validasi/Jumlah_kehadiran_pengajar';
		$isi['ajax'] 	= 'Validasi/Ajax';
		$isi['isitunkel']  = $tunkel_get;
		$isi['isitunj_anak']  = $tunj_anak_get;
		$isi['isilist']  = $list2;
		$this->load->view('Template',$isi);
		
	}

	public function save_data_pengajar() {
		$id = $this->input->post('id_kehadiran_lembaga');
		$bulan = $this->input->post('bulan');
		$id_kehadiran = $this->input->post('id_kehadiran');
		$tahun = $this->input->post('tahun');
		$nik = $this->input->post('nik_umana');
		$tmt_dosen = $this->input->post('tmt_dosen');
		$mp = $this->input->post('mp');
		$jumlah_sks = $this->input->post('jumlah_sks');
		$rank = $this->input->post('rank');
		$mengajar = $this->input->post('mengajar');
		$dty = $this->input->post('dty');
		$jafung = $this->input->post('jafung');
		$jml_hadir = $this->input->post('jml_hadir');
		$nominal_kehadiran = $this->input->post('nominal_kehadiran');
		$jml_hadir_15 = $this->input->post('jml_hadir_15');
		$nominal_kehadiran_15 = $this->input->post('nominal_kehadiran_15');
		$jml_hadir_10 = $this->input->post('jml_hadir_10');
		$nominal_kehadiran_10 = $this->input->post('nominal_kehadiran_10');
		$rank_piket = $this->input->post('rank_piket');
		$barokah_piket = $this->input->post('barokah_piket');
		$tunkel = $this->input->post('tunkel');
		$tunkel = $this->input->post('tunkel');
		$tunj_anak = $this->input->post('tunj_anak');
		$tmp = $this->input->post('tmp');
		$kehormatan = $this->input->post('kehormatan');
		$tunj_walkes = $this->input->post('tunj_walkes');
		$tambahan = $this->input->post('bk');
		$potongan = $this->input->post('potongan');
		$diterima = $this->input->post('diterima');
		$jumlah_total = $this->input->post('jumlah_total');
		
		for ($i = 0; $i < count($bulan); $i++) {
		  $data = array(
			'id_total_barokah_pengajar' => '',
			'id_pengajar' => $nik[$i],
			'id_kehadiran' => $id_kehadiran[$i],
			'bulan' => $bulan[$i],
			'tahun' => $tahun[$i],
			'jumlah_sks' => $jumlah_sks[$i],
			'rank' => $rank[$i],
			'mengajar' => $mengajar[$i],
			'mp' => $mp[$i],
			'dty' => $dty[$i],
			'jafung' => $jafung[$i],
			'jumlah_hadir' => $jml_hadir[$i],
			'nominal_kehadiran' => $nominal_kehadiran[$i],
			'jumlah_hadir_15' => $jml_hadir_15[$i],
			'nominal_hadir_15' => $nominal_kehadiran_15[$i],
			'jumlah_hadir_10' => $jml_hadir_10[$i],
			'nominal_hadir_10' => $nominal_kehadiran_10[$i],
			'jumlah_hadir_piket' => $jumlah_hadir_piket[$i],
			'rank_piket' => $rank_piket[$i],
			'barokah_piket' => $barokah_piket[$i],
			'tunkel' => $tunkel[$i],
			'tun_anak' => $tunj_anak[$i],
			// 'tmp' => $tmp[$i],
			'kehormatan' => $kehormatan[$i],
			'walkes' => $tunj_walkes[$i],
			'khusus' => $tambahan[$i],
			'potongan' => $potongan[$i],
			'diterima' => $diterima[$i]
		  );
		  $this->db->insert('total_barokah_pengajar', $data);
		//   $jumlah_total = array_sum($diterima[$i]);
		}
		$data2 = array(
			'status' => "acc",
			'jumlah_total' => $jumlah_total
		  );

		$this->db->where('id_kehadiran_lembaga', $id);
		$this->db->update('kehadiran_lembaga', $data2);
		// redirect('validasi/index');
		// Tampilkan pesan sukses atau kembali ke halaman sebelumnya
	  }

	public function ajax_edit($id)
	  {
		  // $data = $this->db->get_where('t_beban_kerja', array('id_tbk' => $id))->row();
		  $data = $this->db->query("SELECT id_kehadiran, nama_lengkap, penempatan.kehormatan, tunj_kel, nominal_transport, jumlah_hadir FROM umana, penempatan, kehadiran, transport WHERE umana.nik = penempatan.nik and kehadiran.id_penempatan = penempatan.id_penempatan and penempatan.kategori_trans = transport.id_transport and kehadiran.id_kehadiran = $id")->row();
		  echo json_encode($data);
	  }
	  
	 public function ajax_edit_pengajar($id)
	  {
		  $data = $this->db->query("SELECT jumlah_hadir_15, jumlah_hadir_10, jumlah_hadir_piket, id_kehadiran_pengajar, nama_lengkap, pengajar.kehormatan, tunj_kel, nominal_transport,
		   jumlah_hadir FROM umana, pengajar, kehadiran_pengajar, transport WHERE umana.nik = pengajar.nik and kehadiran_pengajar.id_pengajar = pengajar.id_pengajar and pengajar.kategori_trans = transport.id_transport and kehadiran_pengajar.id_kehadiran_pengajar = $id")->row();
		  echo json_encode($data);
	  }

	  public function ajax_update(){
        // $this->_validate();
		if($this->input->post('jumlah_hadir') == ''){

		}else{
         $data = array(
                'jumlah_hadir' 		=> 	$this->input->post('jumlah_hadir'),
                );
				$this->db->update('kehadiran', $data, array('id_kehadiran' => $this->input->post('id_kehadiran')));
		}
		$this->session->set_flashdata('success', 'Data berhasil disimpan.');
   
		
		echo json_encode(array("status" => TRUE));
	}
	
	public function ajax_update_pengajar(){
        // $this->_validate();
		if($this->input->post('jumlah_hadir') == ''){

		}else{
         $data = array(
                'jumlah_hadir' 		=> 	$this->input->post('jumlah_hadir'),
                'jumlah_hadir_15' 		=> 	$this->input->post('jumlah_hadir_15'),
				'jumlah_hadir_10' 		=> 	$this->input->post('jumlah_hadir_10'),
				'jumlah_hadir_piket' 		=> 	$this->input->post('jumlah_hadir_piket'),
                );
				$this->db->update('kehadiran_pengajar', $data, array('id_kehadiran_pengajar' => $this->input->post('id_kehadiran')));
		}
		$this->session->set_flashdata('success', 'Data berhasil disimpan.');
   
		
		echo json_encode(array("status" => TRUE));
	}
	
	public function cetak($id){
		$list2 = $this->db->query("select id_kehadiran, kehadiran_lembaga.kategori as kategori_pengajar, pengajar.kategori as kategori, jabatan_akademik, jumlah_sks, status_sertifikasi, ijazah_terakhir, id_bidang, tunj_anak, umana.gelar_depan, umana.gelar_belakang, kehormatan, kehadiran_lembaga.file, tunj_kel, id_kehadiran_lembaga, 
		nama_lengkap, status_nikah, tmt_dosen, tmt_guru, kehadiran.id_penempatan, kehadiran.bulan, kehadiran.tahun, jumlah_hadir, nama_lembaga, nominal_transport from umana, pengajar, kehadiran, kehadiran_lembaga,
		lembaga, transport WHERE 
		kehadiran_lembaga.id_kehadiran_lembaga = kehadiran.id_kehadi and 
		pengajar.id_pengajar = kehadiran.id_penempatan and 
		pengajar.nik = umana.nik and 
		pengajar.id_lembaga = lembaga.id_lembaga and 
		pengajar.kategori_trans = transport.id_transport and 
		DATEDIFF(NOW(), pengajar.tgl_mulai) < pengajar.tgl_selesai and
		kehadiran_lembaga.id_kehadiran_lembaga = $id ")->result();
		$tunkel_get = $this->db->get('tunkel')->result();
		$tunj_anak_get = $this->db->get('tunjanak')->result();
		// $kehormatan = $this->db->query('select * from ');
		$this->Login_model->getsqurity() ;

		$isi['css'] 	= 'Validasi/Css';
		$isi['content'] = 'Validasi/Jumlah_hadir_pengajar';
		$isi['ajax'] 	= 'Validasi/Ajax';
		$isi['isitunkel']  = $tunkel_get;
		$isi['isitunj_anak']  = $tunj_anak_get;
		$isi['isilist']  = $list2;
		$this->load->view('Validasi/Cetak',$isi);	
	}
}
