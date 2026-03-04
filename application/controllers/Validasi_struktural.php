<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Validasi_struktural extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Login_model');
		$this->load->model('kehadiran_satpam_model');
        $this->load->helper('Rupiah_helper');
		$this->load->helper('string');
        $this->load->helper('hitung_barokah_helper');
		$this->load->library('Pdf'); 
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

    function koreksi_i(){
        $this->load->view('Validasi_fullscreen/Validasi_struktural');
    }

    public function koreksi($id)
    {
        $this->Login_model->getsqurity();
        $idL = $this->decrypt_url($id);

        $res = hitung_periode_barokah($this, $idL);
        if (!$res['periode']) show_error('Data periode tidak ditemukan', 404);

        $data = [
            'isilist'          => $res['rows'],
            'periode'          => $res['periode'],
            'total_tunjab'     => $res['totals']['total_tunjab'],
            'total_tmp'        => $res['totals']['total_tmp'],
            'total_kehadiran'  => $res['totals']['total_kehadiran'],
            'total_tunkel'     => $res['totals']['total_tunkel'],
            'total_tunjanak'   => $res['totals']['total_tunjanak'],
            'total_kehormatan' => $res['totals']['total_kehormatan'],
            'total_tbk'        => $res['totals']['total_tbk'],
            'total_barokah'    => $res['totals']['total_barokah'],
            'total_potongan'   => $res['totals']['total_potongan'],
            'grand_total'      => $res['totals']['grand_total'],
        ];
        $this->load->view('Validasi_fullscreen/Validasi_struktural', $data);
    }
   
    public function save_data()
    {
        $this->Login_model->getsqurity();

        $idL = $this->input->post('id_kehadiran_lembaga', true);
        if (!$idL) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(['status'=>false,'message'=>'ID periode tidak dikirim.']));
        }

        // Tangkap catatan jika disertakan oleh Pimpinan
        $catatan_umum = $this->input->post('catatan_umum_pimpinan', true);
        $catatan_khusus = $this->input->post('catatan_khusus'); // array
        $action_type = $this->input->post('action_type', true) ?: 'setuju'; // 'setuju' | 'revisi'

        // (opsional) guard role & status 'Terkirim' di sini

        $res = hitung_periode_barokah($this, $idL);
        if (!$res['periode']) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(['status'=>false,'message'=>'Data periode tidak ditemukan.']));
        }

        $rows   = $res['rows'];
        $totals = $res['totals'];

        $this->db->trans_begin();

        // bersihkan rekap lama berdasarkan id_kehadiran pada periode
        $ids = array_map(fn($r)=>(int)$r->id_kehadiran, $rows);
        if ($ids) $this->db->where_in('id_kehadiran', $ids)->delete('total_barokah');

        // insert rekap sesuai angka yang TAMPIL di view
        foreach ($rows as $r) {
            // Ektraksi catatan untuk id_penempatan ini (jika ada)
            $catatan_khusus_umana = isset($catatan_khusus[$r->id_penempatan]) ? $catatan_khusus[$r->id_penempatan] : '';
            
            $this->db->insert('total_barokah', [
                // 'id_total_barokah' => AI,
                'id_penempatan'        => $r->id_penempatan,
                'id_kehadiran'         => $idL,
                'bulan'                => $r->bulan,
                'tahun'                => $r->tahun,
                'tunjab'               => (int)$r->tunjab,
                'mp'                   => (int)$r->mp,
                'kehadiran'            => (int)$r->jumlah_hadir,
                'nominal_kehadiran'    => (int)$r->nominal_kehadiran,
                'tunkel'               => (int)$r->tunkel,
                'tunj_anak'            => (int)$r->tunj_anak,
                'tmp'                  => (int)$r->tmp,
                'kehormatan'           => (int)$r->nilai_kehormatan,
                'tbk'                  => (int)$r->tbk,
                'potongan'             => (int)$r->potongan,
                'barokah_khusus'       => 0,
                'diterima'             => (int)$r->diterima,
                'catatan_khusus_umana' => $catatan_khusus_umana,
            ]);
        }

        // Tentukan status berdasarkan aksi: Jika revisi kembali jadi 'Revisi', jika setuju jadi 'acc'
        $status_baru = ($action_type === 'revisi') ? 'Revisi' : 'acc';

        // update status & total periode
        $this->db->where('id_kehadiran_lembaga', $idL)->update('kehadiran_lembaga', [
            'status'       => $status_baru,
            'jumlah_total' => (int)$totals['grand_total'],
            'tgl_input'    => date('Y-m-d H:i:s'),
            'catatan_umum_pimpinan' => $catatan_umum
        ]);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(['status'=>false,'message'=>'Gagal menyimpan data.']));
        }
        $this->db->trans_commit();

        $this->load->helper('hitung_barokah_helper');
        $keterangan_log = ($action_type === 'revisi') ? 'Dokumen dikembalikan u/ revisi.' : 'Dokumen disetujui (ACC).';
        if (!empty($catatan_umum)) {
            $keterangan_log .= ' Catatan: ' . $catatan_umum;
        }
        catat_riwayat_barokah($idL, $status_baru, $keterangan_log);

        $msg_sukses = ($action_type === 'revisi') ? 'Dokumen dikembalikan ke Admin Lembaga untuk revisi.' : 'Periode berhasil disetujui & disimpan.';

        return $this->output->set_content_type('application/json')
            ->set_output(json_encode([
                'status'=>true,
                'message'=> $msg_sukses,
                'summary'=>[
                    'jumlah_total'=>(int)$totals['grand_total'],
                    'jumlah_baris'=>count($rows)
                ]
            ]));
    }

    
    public function detail_tbk($id_penempatan) {
        $data = $this->db->query("
            SELECT jenis_tbk as nama_kegiatan, nominal_tbk, max_periode
            FROM t_beban_kerja
            WHERE id_penempatan = ?
            AND (max_periode IS NULL OR max_periode >= CURDATE())
        ", [$id_penempatan])->result();
        echo json_encode($data);
    }
    
    public function update_row()
    {
        // --- 0) Ambil & validasi input
        $id_kehadiran        = (int) $this->input->post('id_kehadiran');
        $id_kehadiran_lembaga = $this->input->post('id_kehadiran_lembaga');
        $jumlah_hadir_baru   = (int) $this->input->post('jumlah_hadir');
        $jumlah_tugas_baru   = (int) $this->input->post('jumlah_tugas'); // Get new field
        $jumlah_izin_baru    = (int) $this->input->post('jumlah_izin');
        $jumlah_sakit_baru   = (int) $this->input->post('jumlah_sakit');
    
        if (!$id_kehadiran || $jumlah_hadir_baru < 0 || $jumlah_tugas_baru < 0 || $jumlah_izin_baru < 0 || $jumlah_sakit_baru < 0) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Input tidak valid.'
                ]));
        }
    
        // --- 1) Update semua field kehadiran di tabel kehadiran
        $this->db->where('id_kehadiran', $id_kehadiran)
                 ->update('kehadiran', [
                     'jumlah_hadir' => $jumlah_hadir_baru,
                     'jumlah_tugas' => $jumlah_tugas_baru, // Update db
                     'jumlah_izin'  => $jumlah_izin_baru,
                     'jumlah_sakit' => $jumlah_sakit_baru
                 ]);
    
        // --- 2) Gunakan helper untuk hitung ulang periode
        $res = hitung_periode_barokah($this, $id_kehadiran_lembaga);
        
        if (!$res['rows']) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Data kehadiran tidak ditemukan.'
                ]));
        }
        
        // Cari row yang di-edit
        $row = null;
        foreach ($res['rows'] as $r) {
            if ((int)$r->id_kehadiran === $id_kehadiran) {
                $row = $r;
                break;
            }
        }
    
        if (!$row) {
            return $this->output
                ->set_content_type(' application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Data tidak ditemukan setelah update.'
                ]));
        }
    
        // --- 3) Response JSON untuk diupdate di UI
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'row' => [
                    'id_kehadiran'      => (int)$row->id_kehadiran,
                    "jumlah_hadir"      => (int)$row->jumlah_hadir,
                    "jumlah_tugas"      => (int)($row->jumlah_tugas ?? 0), // Return updated tugas
                    "jumlah_izin"       => (int)$row->jumlah_izin,
                    "jumlah_sakit"      => (int)$row->jumlah_sakit, // Add sakit to return data
                    "persentase_kehadiran" => $row->persentase_kehadiran,
                    // Update nominal2 lain...
                    "nominal_kehadiran" => rupiah($row->nominal_kehadiran),
                ],
                'totals' => [
                    'total_kehadiran' => (int)$res['totals']['total_kehadiran'],
                    'total_barokah'   => (int)$res['totals']['total_barokah'],
                    'grand_total'     => (int)$res['totals']['grand_total']
                ]
            ]));
    }
    
    

    public function update_kirim()
    {
        $id = $this->input->post('id_kehadiran_lembaga');
        if (!$id) {
            echo json_encode(['status'=>false, 'message'=>'ID tidak dikirim']);
            return;
        }

        $data = ['status' => 'Terkirim'];
        $this->db->update('kehadiran_lembaga', $data, ['id_kehadiran_lembaga' => $id]);

        $this->load->helper('hitung_barokah_helper');
        catat_riwayat_barokah($id, 'Terkirim', 'Dikirim ulang oleh Admin Lembaga untuk direview Evaluator / SuperAdmin');

        echo json_encode(['status' => true, 'message' => 'Periode berhasil dikirim.']);
    }

    public function reset_json()
    {
        $this->Login_model->getsqurity();

        // --- Guard role
        $jabatan = $this->session->userdata('jabatan');
        if (!in_array($jabatan, ['SuperAdmin', 'Evaluasi'], true)) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Anda tidak memiliki izin untuk melakukan reset data.'
                ]));
        }

        $idKL = $this->input->post('id_kehadiran_lembaga', true);
        $mode = $this->input->post('mode', true); // 'kehadiran' atau 'total'

        if (!$idKL) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(['status'=>false,'message'=>'id_kehadiran_lembaga tidak dikirim.']));
        }
        if (!in_array($mode, ['kehadiran', 'total'], true)) {
            $mode = 'kehadiran';
        }

        // Ambil periode utk validasi ringan
        $periode = $this->db->select('id_kehadiran_lembaga')
                            ->from('kehadiran_lembaga')
                            ->where('id_kehadiran_lembaga', $idKL)
                            ->get()->row();
        if (!$periode) {
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(['status'=>false,'message'=>'Periode tidak ditemukan.']));
        }

        // Daftar id_kehadiran (untuk mode=total)
        $rows = $this->db->select('id_kehadiran')
                        ->from('kehadiran')
                        ->where('id_kehadi', $idKL)
                        ->get()->result_array();
        $idKehadiranList = array_map(function($r){ return (int)$r['id_kehadiran']; }, $rows);

        $this->db->trans_begin();

        try {
            if ($mode === 'kehadiran') {
                // 1) Hapus data input kehadiran periode ini
                $this->db->where('id_kehadi', $idKL)->delete('kehadiran');

                // 2) Status periode -> Belum, nolkan total
                $this->db->where('id_kehadiran_lembaga', $idKL)
                        ->update('kehadiran_lembaga', [
                            'status'       => 'Belum',
                            'jumlah_total' => 0,
                        ]);

                $msg = 'Data kehadiran berhasil dihapus. Status periode dikembalikan ke Belum.';

            } else { // mode === 'total'
                // 1) Hapus rekap total_barokah berdasarkan id_kehadiran periode ini
                if (!empty($idKL)) {
                    $this->db->where_in('id_kehadiran', $idKL)->delete('total_barokah');
                }

                // 2) Status periode -> Sudah, nolkan total (kehadiran tetap ada)
                $this->db->where('id_kehadiran_lembaga', $idKL)
                        ->update('kehadiran_lembaga', [
                            'status'       => 'Sudah',
                            'jumlah_total' => 0,
                        ]);

                $msg = 'Total barokah berhasil dihapus. Status periode diatur ke Sudah.';
            }

            if ($this->db->trans_status() === false) {
                throw new Exception('Transaksi gagal.');
            }
            $this->db->trans_commit();

            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(['status'=>true, 'message'=>$msg]));

        } catch (Exception $e) {
            $this->db->trans_rollback();
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(['status'=>false,'message'=>'Reset gagal (transaksi dibatalkan).']));
        }
    }



}