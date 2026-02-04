<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Login_model');
		$this->load->model('Laporan_model');
		$this->load->library('user_agent');
		$this->load->helper('Rupiah_helper');
	}

public function index(){
    $this->Login_model->getsqurity() ;
		$bln = date('m');
		switch ($bln) {
			case '2':
				$b1 = 'Januari';
				break;
			case '3':
				$b1 = 'Februari';
				break;
			case '4':
				$b1 = 'Maret';
				break;
			case '5':
				$b1 = 'April';
				break;
			case '6':
				$b1 = 'Mei';
				break;
			case '7':
				$b1 = 'Juni';
				break;
			case '8':
				$b1 = 'Juli';
				break;
			case '9':
				$b1 = 'Agustus';
				break;
			case '10':
				$b1 = 'September';
				break;
			case '11':
				$b1 = 'Oktober';
				break;
			case '12':
				$b1 = 'November';
				break;
				default:
					$b1 = 'Desember';
					break;
			}
			$bln2 = date('m');
			switch ($bln2) {
				case '3':
					$b2 = 'Januari';
					break;
				case '4':
					$b2 = 'Februari';
					break;
			   case '5':
				   $b2 = 'Maret';
				   break;
			   case '6':
				   $b2 = 'April';
				   break;
			   case '7':
				   $b2 = 'Mei';
				   break;
			   case '8':
				   $b2 = 'Juni';
				   break;
			   case '9':
				   $b2 = 'Juli';
				   break;
			   case '10':
				   $b2 = 'Agustus';
				   break;
			   case '11':
				   $b2 = 'September';
				   break;
			   case '12':
				   $b2 = 'Oktober';
				   break;
			   case '1':
				   $b2 = 'November';
				   break;
				default:
					$b2 = 'Desember';
					break;
			}
		$this->Login_model->getsqurity() ;
	    if ($this->agent->is_mobile())
        {
            $nik = $this->session->userdata('nik');
            // $isi['total']        = $this->db->query("SELECT diterima from total_barokah, penempatan where ")->row();
			$isi['total'] 		    = $this->db->query("SELECT SUM(diterima) as bulan_ini from total_barokah, penempatan where penempatan.nik = '$nik' and penempatan.id_penempatan = total_barokah.id_penempatan and bulan = '$b1' and tahun = '2024/2025' ")->row();
            $isi['total_guru'] 		= $this->db->query("SELECT SUM(diterima) as guru from total_barokah_pengajar, pengajar where pengajar.nik = '$nik' and pengajar.id_pengajar = total_barokah_pengajar.id_pengajar and bulan = '$b1' and tahun = '2024/2025' ")->row();
            $isi['total_lalu'] 		= $this->db->query("SELECT SUM(diterima) as bulan_lalu from total_barokah, penempatan where penempatan.nik = '$nik' and penempatan.id_penempatan = total_barokah.id_penempatan and bulan = '$b2' and tahun = '2023/2024' ")->row();
            $isi['ajax'] 		= 'Mobile/Ajax';
			$isi['content'] 	= 'Dashboard';
			$this->load->view('Mobile/Mobile',$isi);
        }
        else
        {
			$nik = $this->session->userdata('nik');
			$isi['total'] 		= $this->db->query("SELECT SUM(diterima) as bulan_ini from total_barokah, penempatan where penempatan.nik = '$nik' and penempatan.id_penempatan = total_barokah.id_penempatan and bulan = '$b1' and tahun = '2024/2025' ")->row();
			$isi['total_guru'] 		= $this->db->query("SELECT SUM(diterima) as guru from total_barokah_pengajar, pengajar where pengajar.nik = '$nik' and pengajar.id_pengajar = total_barokah_pengajar.id_pengajar and bulan = '$b1' and tahun = '2024/2025' ")->row();
            $isi['total_lalu'] 		= $this->db->query("SELECT SUM(diterima) as bulan_lalu from total_barokah, penempatan where penempatan.nik = '$nik' and penempatan.id_penempatan = total_barokah.id_penempatan and bulan = '$b2' and tahun = '2023/2024' ")->row();
            $isi['css'] 	= 'Css';
			$isi['content'] = 'Dashboard';
			$isi['ajax'] 	= 'Ajax';
			$this->load->view('Template',$isi);
        }
		
	}
	
	public function lembaga(){
	    $this->Login_model->getsqurity() ;
		$isi['css'] 	= 'Css';
		$isi['content'] = 'Lembaga';
		$isi['ajax'] 	= 'Ajax';
		$this->load->view('Template',$isi);
	}
	
	public function petugas(){
	    $this->Login_model->getsqurity() ;
		$bln = date('m');
		switch ($bln) {
			case '2':
				$b1 = 'Januari';
				break;
			case '3':
				$b1 = 'Februari';
				break;
			case '4':
				$b1 = 'Maret';
				break;
			case '5':
				$b1 = 'April';
				break;
			case '6':
				$b1 = 'Mei';
				break;
			case '7':
				$b1 = 'Juni';
				break;
			case '8':
				$b1 = 'Juli';
				break;
			case '9':
				$b1 = 'Agustus';
				break;
			case '10':
				$b1 = 'September';
				break;
			case '11':
				$b1 = 'Oktober';
				break;
			case '12':
				$b1 = 'November';
				break;
				default:
					$b1 = 'Desember';
					break;
			}
			$bln2 = date('m');
			switch ($bln2) {
				case '3':
					$b2 = 'Januari';
					break;
				case '4':
					$b2 = 'Februari';
					break;
			   case '5':
				   $b2 = 'Maret';
				   break;
			   case '6':
				   $b2 = 'April';
				   break;
			   case '7':
				   $b2 = 'Mei';
				   break;
			   case '8':
				   $b2 = 'Juni';
				   break;
			   case '9':
				   $b2 = 'Juli';
				   break;
			   case '10':
				   $b2 = 'Agustus';
				   break;
			   case '11':
				   $b2 = 'September';
				   break;
			   case '12':
				   $b2 = 'Oktober';
				   break;
			   case '1':
				   $b2 = 'November';
				   break;
				default:
					$b2 = 'Desember';
					break;
			}
		
		$isi['barokah_bulan'] 		= $this->db->query("select sum(jumlah_total) as bulan_ini, count(id_lembaga) as lembaga from kehadiran_lembaga where bulan = '$b1' and tahun = '2024/2025' ")->row();
		$isi['barokah_bulan_lalu'] 	= $this->db->query("select sum(jumlah_total) as bulan_lalu, count(id_lembaga) as lembaga from kehadiran_lembaga where bulan = '$b2' and tahun = '2024/2025' ")->row(); 
		$isi['bulan_lalu_nama']     = $b2;
		$isi['bulan_ini_nama']      = $b1;
		$isi['lembaga_cair'] 		= $this->db->query("select nama_lembaga, kehadiran_lembaga.jumlah_total from lembaga, kehadiran_lembaga WHERE lembaga.id_lembaga = kehadiran_lembaga.id_lembaga and kehadiran_lembaga.status = 'selesai' and kehadiran_lembaga.bulan = '$b1' ")->result();
		$isi['umana_pa'] 			= $this->db->query("SELECT COUNT(nik) as putra from umana WHERE jk = 'Laki-laki'")->row();
		$isi['umana_pi'] 			= $this->db->query("SELECT COUNT(nik) as putri from umana WHERE jk != 'Laki-laki'")->row();
		$isi['jumlah_jabatan'] 		= $this->db->query("SELECT COUNT(id_penempatan) as jumlah, ketentuan_barokah.nama_jabatan from penempatan, ketentuan_barokah WHERE penempatan.id_ketentuan = ketentuan_barokah.id_ketentuan GROUP by ketentuan_barokah.nama_jabatan")->result();
		$isi['jum_jabatan']			= $this->db->query('SELECT nama_jabatan, COUNT(*) AS jumlah_karyawan FROM ketentuan_barokah JOIN penempatan ON ketentuan_barokah.id_ketentuan = penempatan.id_ketentuan GROUP BY nama_jabatan')->result();
		$isi['labels'] 				= array_column($isi['jum_jabatan'], 'nama_jabatan');
        $isi['series'] 				= array_column($isi['jum_jabatan'], 'jumlah_karyawan');
		$isi['css'] 				= 'Css';
		$isi['content'] 			= 'Statistik';
		$isi['ajax'] 				= 'Ajax';
		$this->load->view('Template',$isi);
	}

	public function barokah_statistik()
	{
		// $list = $this->db->query("SELECT COUNT(nis) as jml,  madrasah from pendaftaran, mahasiswa, 
		// pendaftaran where mahasiswa.nis = pendaftaran.nis_mahasiswa and pendaftaran.tahun_akademik =
		//  tahun_akademik.id_tahun and tahun_akademik.status = 'Aktif' GROUP BY madrasah")->result();
		
		$list = $this->Laporan_model->getData();

		$bulan = array();
		$total_tbk = array();
		$total_tunjab = array();
		$total_kehormatan = array();
		$total_kehadiran = array();
		$total_tunkel = array();
		foreach ($list as $datanya) {
			
			$bulan[] 			= $datanya->bulan;
			$total_tbk[] 		= $datanya->total_tbk;
			$total_tunjab[] 	= $datanya->total_tunjab;
			$total_kehormatan[] = $datanya->total_kehormatan;
			$total_kehadiran[] 	= $datanya->total_kehadiran;
			$total_tunkel[] 	= $datanya->total_tunkel;
		}
		$output = array(
			'title' => 'Statistik Barokah ',
			'subtitle' => '',
			'kategori' => 'ini kategori',
			'bulan' => $bulan,
			'total_tbk' => $total_tbk,
			'total_tunjab' => $total_tunjab,
			'total_kehormatan' => $total_kehormatan,
			'total_kehadiran' => $total_kehadiran,
			'total_tunkel' => $total_tunkel,
		);
		echo json_encode($output);
	}

	public function get_cuti_akan_habis()
	{
		$this->load->model('Cuti_model');
		
		// Auto-selesaikan cuti yang sudah lewat tanggal
		$this->Cuti_model->selesaikan_cuti_otomatis();
		
		// Get cuti yang akan habis dalam 14 hari
		$data = $this->Cuti_model->get_cuti_akan_habis(14);
		echo json_encode($data);
	}
}

