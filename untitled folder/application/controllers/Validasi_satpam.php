<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Validasi_satpam extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Login_model');
		$this->load->model('kehadiran_satpam_model');
        $this->load->helper('Rupiah_helper');
		$this->load->helper('string');
		$this->load->library('Pdf'); 
	}

    public function decrypt_url($string) {
        $key = '874jzceroier38!@#%*bjkdwdw)'; // Ganti dengan kunci enkripsi yang diinginkan
        $string = str_replace(array('-', '_'), array('+', '/'), $string);
        $string = base64_decode($string);
        $string = str_replace($key, '', $string);
        return $string;
    }

    public function koreksi_satpam($id)
    {
        $this->Login_model->getsqurity();
        $decrypted_id = $this->decrypt_url($id);
    
        // 1) Ambil info periode (judul)
        $bulan = $this->db->query("
            SELECT id_kehadiran_lembaga, id_lembaga, bulan, tahun, status, file
            FROM kehadiran_lembaga
            WHERE id_kehadiran_lembaga = ?
            LIMIT 1
        ", [(int)$decrypted_id])->row();
    
        if (!$bulan) {
            show_error('Periode tidak ditemukan: ' . htmlspecialchars($decrypted_id), 404);
            return;
        }
    
        // 2) Query normal (LEFT JOIN agar tidak menghilangkan baris)
        $sql = "
            SELECT 
                ks.id_kehadiran_satpam,
                ks.id_kehadiran_lembaga,
                ks.id_satpam,
                ks.bulan,
                ks.tahun,
                ks.jumlah_hari,
                ks.jumlah_shift,
                ks.jumlah_dinihari,
                u.gelar_depan,
                u.nama_lengkap,
                u.gelar_belakang,
                COALESCE(t.nominal_transport, 0) AS nominal_transport
            FROM kehadiran_satpam ks
            LEFT JOIN satpam s     ON s.id_satpam    = ks.id_satpam
            LEFT JOIN umana  u     ON u.nik          = s.nik
            LEFT JOIN transport t  ON t.id_transport = s.id_transport
            WHERE ks.id_kehadiran_lembaga = ?
            ORDER BY u.nama_lengkap ASC, ks.id_kehadiran_satpam ASC
        ";
        $list = $this->db->query($sql, [(string)$decrypted_id])->result();
    
        // 3) Jika kosong, lakukan “probe” tanpa JOIN
        if (empty($list)) {
            $probe = $this->db->query(
                "SELECT id_kehadiran_satpam, id_satpam, bulan, tahun
                 FROM kehadiran_satpam
                 WHERE id_kehadiran_lembaga = ?
                 ORDER BY id_kehadiran_satpam ASC
                 LIMIT 5",
                [(string)$decrypted_id]
            )->result();
    
            // Jika probe ada hasil, tampilkan bukti ke layar lalu berhenti.
            if (!empty($probe)) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'note'        => 'JOIN menghasilkan 0 baris, tetapi tabel kehadiran_satpam berisi data untuk id ini. Ini berarti masalah ada pada JOIN/kolom referensi.',
                    'id_decrypt'  => $decrypted_id,
                    'sample_rows' => $probe,
                    // cek juga apakah kolom referensi terisi:
                    'ref_check'   => $this->db->query("
                        SELECT COUNT(*) AS satpam_rows FROM satpam WHERE id_satpam IN (
                            SELECT id_satpam FROM kehadiran_satpam WHERE id_kehadiran_lembaga = ?
                        )
                    ", [(string)$decrypted_id])->row(),
                ], JSON_PRETTY_PRINT);
                exit;
            }
    
            // Kalau probe juga 0 → berarti id salah (decrypt / URL)
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'note'       => 'Tidak ada baris di kehadiran_satpam untuk id ini. Cek id terenkripsi/hasil decrypt.',
                'id_decrypt' => $decrypted_id
            ], JSON_PRETTY_PRINT);
            exit;
        }
    
        // 4) Bila ada data, kirim ke view seperti biasa
        $isi['id']      = $id;
        $isi['bulan']   = $bulan;
        $isi['isilist'] = $list;
        $this->load->view('Validasi_fullscreen/Validasi_satpam', $isi);
    }
    

    
      

    public function save_data() {
		$id_satpam = $this->input->post('id_satpam');
		$id_kehadiran_satpam = $this->input->post('id_kehadiran_satpam');
		$id_kehadiran_lembaga = $this->input->post('id_kehadiran_lembaga');
		$bulan = $this->input->post('bulan');
        $tahun = $this->input->post('tahun');
		$jumlah_hari = $this->input->post('jumlah_hari');
        $nominal_transport = $this->input->post('nominal_transport');
        $jumlah_transport = $this->input->post('jumlah_transport');
		$jumlah_shift = $this->input->post('jumlah_shift');
        $rank = $this->input->post('rank');
        $jumlah_barokah = $this->input->post('jumlah_barokah');
		$jumlah_dinihari = $this->input->post('jumlah_dinihari');
		$konsumsi = $this->input->post('konsumsi');
        $jumlah_konsumsi = $this->input->post('jumlah_konsumsi');
		$diterima = $this->input->post('diterima');
		$jumlah_total = $this->input->post('jumlah_total');
		
		for ($i = 0; $i < count($bulan); $i++) {
		  $data = array(
			'id_total_barokah_satpam' => '',
			'id_satpam' => $id_satpam[$i],
			'id_kehadiran_satpam' => $id_kehadiran_satpam[$i],
            'id_kehadiran_lembaga' => $id_kehadiran_lembaga,
			'bulan' => $bulan[$i],
			'tahun' => $tahun[$i],
			'jumlah_hari' => $jumlah_hari[$i],
            'nominal_transport' => $nominal_transport[$i],
            'jumlah_transport' => $jumlah_transport[$i],
            'jumlah_shift' => $jumlah_shift[$i],
            'rank' => $rank[$i],
            'jumlah_barokah' => $jumlah_barokah[$i],
            'jumlah_dinihari' => $jumlah_dinihari[$i],
            'konsumsi' => $konsumsi[$i],
            'jumlah_konsumsi' => $jumlah_konsumsi[$i],
			'diterima' => $diterima[$i]
		  );
		  $this->db->insert('total_barokah_satpam', $data);
		}
		$data2 = array(
			'status' => "acc",
			'jumlah_total' => $jumlah_total
		  );

		$this->db->where('id_kehadiran_lembaga', $id_kehadiran_lembaga);
		$this->db->update('kehadiran_lembaga', $data2);
        echo json_encode(['status' => true]);
        exit;
		
	}


}