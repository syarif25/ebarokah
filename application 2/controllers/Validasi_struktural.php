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
            ]);
        }

        // update status & total periode
        $this->db->where('id_kehadiran_lembaga', $idL)->update('kehadiran_lembaga', [
            'status'       => 'acc',
            'jumlah_total' => (int)$totals['grand_total'],
            'tgl_input'    => date('Y-m-d H:i:s'),
        ]);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(['status'=>false,'message'=>'Gagal menyimpan data.']));
        }
        $this->db->trans_commit();

        return $this->output->set_content_type('application/json')
            ->set_output(json_encode([
                'status'=>true,
                'message'=>'Periode berhasil disetujui & disimpan.',
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
        $id_kehadiran_lembaga = $this->input->post('id_kehadiran_lembaga'); // bisa varchar/int sesuai skema
        $jumlah_hadir_baru   = (int) $this->input->post('jumlah_hadir');
    
        if (!$id_kehadiran || $jumlah_hadir_baru < 0) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Input tidak valid.'
                ]));
        }
    
        // --- 1) Update jumlah_hadir saja di tabel kehadiran
        $this->db->where('id_kehadiran', $id_kehadiran)
                 ->update('kehadiran', ['jumlah_hadir' => $jumlah_hadir_baru]);
    
        // --- 2) Ambil data baris setelah update (untuk dihitung ulang)
        $row = $this->db->query("
            SELECT 
                k.id_kehadiran, k.jumlah_hadir, k.id_penempatan, k.bulan, k.tahun,
                k.id_kehadi AS id_kehadiran_lembaga,
                u.tmt_struktural, p.tunj_kel, p.tunj_anak, p.tunj_mp, p.kehormatan,
                kb.barokah AS tunjab,
                t.nominal_transport
            FROM kehadiran k
            JOIN penempatan p         ON p.id_penempatan = k.id_penempatan
            JOIN umana u              ON u.nik = p.nik
            JOIN ketentuan_barokah kb ON kb.id_ketentuan = p.id_ketentuan
            JOIN transport t          ON t.id_transport = p.kategori_trans
            WHERE k.id_kehadiran = ?
            LIMIT 1
        ", [$id_kehadiran])->row();
    
        if (!$row) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Data kehadiran tidak ditemukan.'
                ]));
        }
    
        // --- 3) Siapkan referensi (sekali ambil)
        $ref_tunkel    = $this->db->get('tunkel')->row();         // kolom: besaran_tunkel
        $ref_tunjanak  = $this->db->get('tunjanak')->row();       // kolom: nominal_tunj_anak
        $ref_kehor     = $this->db->get('barokah_kehormatan')->result(); // min/max/nominal_kehormatan
    
        // --- 4) Hitung ulang komponen BARIS INI (rumus sama seperti di controller halaman)
        $tahun_awal = !empty($row->tmt_struktural) ? (int)date('Y', strtotime($row->tmt_struktural)) : 0;
        $tahun_skrg = (int)date('Y');
        $mp  = max(0, $tahun_skrg - $tahun_awal);
    
        $tmp = ($row->tunj_mp != "Tidak" && $mp >= 3) ? floor($mp / 3) * 10000 : 0;
        $nominal_kehadiran = (int)$row->jumlah_hadir * (int)$row->nominal_transport;
    
        $tunkel_nom = ($row->tunj_kel == 'Ya' && $mp >= 2) ? (int)($ref_tunkel->besaran_tunkel ?? 0) : 0;
        $tunjanak_nom = ($row->tunj_anak == 'Ya') ? (int)($ref_tunjanak->nominal_tunj_anak ?? 0) : 0;
    
        $kehormatan_nom = 0;
        if (!empty($row->kehormatan) && strtolower($row->kehormatan) === 'ya') {
            foreach ($ref_kehor as $rk) {
                if ($mp >= (int)$rk->min_masa_pengabdian && $mp <= (int)$rk->max_masa_pengabdian) {
                    $kehormatan_nom = (int)$rk->nominal_kehormatan;
                    break;
                }
            }
        }
    
        // TBK (aktif sampai max_periode) & POTONGAN (aktif sampai max_periode_potongan)
        $tbk_row = $this->db->query("
            SELECT SUM(nominal_tbk) AS jumlah_tbk
            FROM t_beban_kerja
            WHERE id_penempatan = ?
              AND (max_periode IS NULL OR max_periode >= CURDATE())
        ", [$row->id_penempatan])->row();
        $tbk = (int)($tbk_row->jumlah_tbk ?? 0);
    
        $pot_row = $this->db->query("
            SELECT SUM(nominal_potongan) AS jumlah
            FROM potongan_umana
            WHERE id_penempatan = ?
              AND (max_periode_potongan IS NULL OR max_periode_potongan >= CURDATE())
        ", [$row->id_penempatan])->row();
        $potongan = (int)($pot_row->jumlah ?? 0);
    
        $jumlah_barokah = (int)$row->tunjab + $tmp + $nominal_kehadiran + $tunkel_nom + $tunjanak_nom + $kehormatan_nom + $tbk;
        $diterima       = $jumlah_barokah - $potongan;
    
        // --- 5) Hitung ulang TOTAL seluruh periode (berdasarkan id_kehadi)
        $semua = $this->db->query("
            SELECT 
                k.id_kehadiran, k.jumlah_hadir, k.id_penempatan,
                u.tmt_struktural, p.tunj_kel, p.tunj_anak, p.tunj_mp, p.kehormatan,
                kb.barokah AS tunjab,
                t.nominal_transport
            FROM kehadiran k
            JOIN penempatan p         ON p.id_penempatan = k.id_penempatan
            JOIN umana u              ON u.nik = p.nik
            JOIN ketentuan_barokah kb ON kb.id_ketentuan = p.id_ketentuan
            JOIN transport t          ON t.id_transport = p.kategori_trans
            WHERE k.id_kehadi = ?
        ", [$id_kehadiran_lembaga])->result();
    
        $total_kehadiran = 0;
        $total_barokah   = 0;
        $grand_total     = 0;
    
        if ($semua) {
            foreach ($semua as $s) {
                // MP per baris
                $mp_s = !empty($s->tmt_struktural) ? max(0, (int)date('Y') - (int)date('Y', strtotime($s->tmt_struktural))) : 0;
                $tmp_s = ($s->tunj_mp != "Tidak" && $mp_s >= 3) ? floor($mp_s / 3) * 10000 : 0;
                $kehadiran_s = (int)$s->jumlah_hadir * (int)$s->nominal_transport;
    
                $tunkel_s   = ($s->tunj_kel == 'Ya' && $mp_s >= 2) ? (int)($ref_tunkel->besaran_tunkel ?? 0) : 0;
                $tunjanak_s = ($s->tunj_anak == 'Ya') ? (int)($ref_tunjanak->nominal_tunj_anak ?? 0) : 0;
    
                $kehor_s = 0;
                if (!empty($s->kehormatan) && strtolower($s->kehormatan) === 'ya') {
                    foreach ($ref_kehor as $rk) {
                        if ($mp_s >= (int)$rk->min_masa_pengabdian && $mp_s <= (int)$rk->max_masa_pengabdian) {
                            $kehor_s = (int)$rk->nominal_kehormatan;
                            break;
                        }
                    }
                }
    
                $tbk_s = (int)($this->db->query("
                            SELECT SUM(nominal_tbk) AS j 
                            FROM t_beban_kerja 
                            WHERE id_penempatan = ? 
                              AND (max_periode IS NULL OR max_periode >= CURDATE())
                        ", [$s->id_penempatan])->row()->j ?? 0);
    
                $pot_s = (int)($this->db->query("
                            SELECT SUM(nominal_potongan) AS j 
                            FROM potongan_umana 
                            WHERE id_penempatan = ? 
                              AND (max_periode_potongan IS NULL OR max_periode_potongan >= CURDATE())
                        ", [$s->id_penempatan])->row()->j ?? 0);
    
                $barokah_s  = (int)$s->tunjab + $tmp_s + $kehadiran_s + $tunkel_s + $tunjanak_s + $kehor_s + $tbk_s;
                $diterima_s = $barokah_s - $pot_s;
    
                $total_kehadiran += $kehadiran_s;
                $total_barokah   += $barokah_s;
                $grand_total     += $diterima_s;
            }
        }
    
        // --- 6) Response JSON untuk diupdate di UI (tidak menyimpan total apa pun)
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => true,
                'row' => [
                    'id_kehadiran'      => (int)$row->id_kehadiran,
                    'jumlah_hadir'      => (int)$row->jumlah_hadir,
                    'nominal_kehadiran' => (int)$nominal_kehadiran,
                    'jumlah_barokah'    => (int)$jumlah_barokah,
                    'diterima'          => (int)$diterima
                ],
                'totals' => [
                    'total_kehadiran' => (int)$total_kehadiran,
                    'total_barokah'   => (int)$total_barokah,
                    'grand_total'     => (int)$grand_total
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