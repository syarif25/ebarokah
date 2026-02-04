<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan_satpam extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Login_model');
		$this->load->model('Laporan_model');
		$this->load->helper('rupiah_helper');
		$this->load->helper('string');
		$this->load->helper('url');
		$this->load->library('Pdf');
	}

	public function index()
	{
		$this->Login_model->getsqurity();
        $id_lembaga_satpam = 59; // Hardcoded Satpam ID
        
        $isi['css'] 	= 'laporan_satpam/Css';
		$isi['content'] = 'laporan_satpam/Index';
		$isi['ajax'] 	= 'laporan_satpam/Ajax';
        
        $data_period = $this->db->query("
            SELECT kl.*, l.nama_lembaga 
            FROM kehadiran_lembaga kl
            JOIN lembaga l ON kl.id_lembaga = l.id_lembaga
            WHERE kl.id_lembaga = ? AND kl.status != 'Belum'
            ORDER BY kl.tahun DESC, kl.bulan DESC
        ", [$id_lembaga_satpam])->result();

        foreach ($data_period as $dp) {
            $dp->id_encrypted = $this->encrypt_url($dp->id_kehadiran_lembaga);
        }
        $isi['data_period'] = $data_period;

		$this->load->view('Template', $isi);
	}

	public function rincian($id)
	{
		$this->Login_model->getsqurity();
        $decrypted_id = $this->decrypt_url($id);

        if (!$decrypted_id) {
            show_404();
        }

		$isi['css'] 	= 'laporan_satpam/Css';
		$isi['content'] = 'laporan_satpam/Rincian';
		$isi['ajax'] 	= 'laporan_satpam/Ajax';
        $isi['id_enkripsi'] = $id;

        // Ambil data header lembaga
        $header = $this->db->query("
            SELECT kl.*, l.nama_lembaga 
            FROM kehadiran_lembaga kl
            JOIN lembaga l ON kl.id_lembaga = l.id_lembaga
            WHERE kl.id_kehadiran_lembaga = ?
        ", [$decrypted_id])->row();

        $isi['header'] = $header;
		$this->load->view('Template', $isi);
	}

    public function cetak($id)
    {
        $this->Login_model->getsqurity();
        $decrypted_id = $this->decrypt_url($id);

        if (!$decrypted_id) {
            show_404();
        }

         // Ambil data header lembaga
         $header = $this->db->query("
            SELECT kl.*, l.nama_lembaga 
            FROM kehadiran_lembaga kl
            JOIN lembaga l ON kl.id_lembaga = l.id_lembaga
            WHERE kl.id_kehadiran_lembaga = ?
        ", [$decrypted_id])->row();

        $data['header'] = $header;
        $data['data'] = $this->Laporan_model->get_datatables_rincian_satpam($decrypted_id);
    
        $this->load->view('laporan_satpam/Cetak', $data);
    }
    
    // AJAX Method for DataTable
	public function data_rincian($id)
	{
        $decrypted_id = $this->decrypt_url($id);
		$list = $this->Laporan_model->get_datatables_rincian_satpam($decrypted_id);
		$data = array();
		$no = 1;

		foreach ($list as $r) {
            $row = array();
			$row[] = $no++;
			$row[] = htmlentities($r->nama_lengkap);
			
            // Kehadiran
            $row[] = $r->jumlah_hari;
            $row[] = rupiah($r->nominal_transport);
            $row[] = rupiah($r->jumlah_transport);
            
            // Shift
            $row[] = $r->jumlah_shift;
            $row[] = rupiah($r->rank); // insentif
            $row[] = rupiah($r->jumlah_barokah);

            // Dinihari
            $row[] = $r->jumlah_dinihari;
            $row[] = rupiah($r->konsumsi);
            $row[] = rupiah($r->jumlah_konsumsi);
            
            // Danru
            $row[] = rupiah($r->nominal_danru);

            // Total diterima
            $row[] = "<b>" . rupiah($r->diterima) . "</b>";
			
			$data[] = $row;
		}

		$output = array(
			"data" => $data,
		);
		echo json_encode($output);
	}

    // Encryption Helpers
    public function encrypt_url($string) {
        $key = '874jzceroier38!@#%*bjkdwdw)'; 
        $encrypted_string = base64_encode($string . $key);
        $encrypted_string = str_replace(array('+', '/', '='), array('-', '_', ''), $encrypted_string);
        return $encrypted_string;
    }
    
    public function decrypt_url($string) {
        $key = '874jzceroier38!@#%*bjkdwdw)'; 
        $string = str_replace(array('-', '_'), array('+', '/'), $string);
        $string = base64_decode($string);
        $string = str_replace($key, '', $string);
        return $string;
    }
}
