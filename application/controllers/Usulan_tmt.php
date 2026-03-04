<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Usulan_tmt extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Login_model');
		$this->load->model('Usulan_tmt_model');
		$this->load->helper('string');
	}

	public function index(){
		$this->Login_model->getsqurity();
        
        // Cek Hak Akses
		$isi['css'] 	= 'Usulan_tmt/Css';
		$isi['content'] = 'Usulan_tmt/Pengajuan';
		$isi['ajax'] 	= 'Usulan_tmt/Ajax';
		$this->load->view('Template', $isi);
	}

	public function data_list()
	{
		$list = $this->Usulan_tmt_model->get_datatables();
		$data = array();
		$no = 1;

		foreach ($list as $row) {
			$r = array();
			$r[] = $no++;
			$r[] = htmlentities($row->nama_lengkap);
			
			// Build Rincian Usulan
			$rincian = '<ul class="mb-0 pl-3" style="font-size:0.85rem; padding-left:20px;">';
			// Cek TMT
			if ($row->tmt_lama !== $row->tmt_baru) {
			    $l = $row->tmt_lama ? date('d-m-Y', strtotime($row->tmt_lama)) : '-';
			    $b = date('d-m-Y', strtotime($row->tmt_baru));
			    $rincian .= "<li><b>TMT:</b> <span class='text-muted'><del>{$l}</del></span> &rarr; <b class='text-primary'>{$b}</b></li>";
			}
			// Cek Tunkel
			if ($row->tunj_kel_lama !== $row->tunj_kel_baru) {
			    $rincian .= "<li><b>Tunkel:</b> <span class='text-muted'><del>{$row->tunj_kel_lama}</del></span> &rarr; <b class='text-primary'>{$row->tunj_kel_baru}</b></li>";
			}
			// Cek Tunj Anak
			if ($row->tunj_anak_lama !== $row->tunj_anak_baru) {
			    $rincian .= "<li><b>Tunj Anak:</b> <span class='text-muted'><del>{$row->tunj_anak_lama}</del></span> &rarr; <b class='text-primary'>{$row->tunj_anak_baru}</b></li>";
			}
			// Cek Tunj MP
			if ($row->tunj_mp_lama !== $row->tunj_mp_baru) {
			    $rincian .= "<li><b>Tunj MP:</b> <span class='text-muted'><del>{$row->tunj_mp_lama}</del></span> &rarr; <b class='text-primary'>{$row->tunj_mp_baru}</b></li>";
			}
			// Cek Kehormatan
			if ($row->kehormatan_lama !== $row->kehormatan_baru) {
			    $rincian .= "<li><b>Kehormatan:</b> <span class='text-muted'><del>{$row->kehormatan_lama}</del></span> &rarr; <b class='text-primary'>{$row->kehormatan_baru}</b></li>";
			}
			$rincian .= '</ul>';
			if ($rincian == '<ul class="mb-0 pl-3" style="font-size:0.85rem; padding-left:20px;"></ul>') {
			    $rincian = '<i>Tidak ada usulan komponen</i>';
			}
			
			$r[] = $rincian;
			
			// Dokumen Link
			if ($row->file_dokumen) {
			    $r[] = '<a href="'.base_url('upload/usulan_tmt/'.$row->file_dokumen).'" target="_blank" class="btn btn-sm btn-info"><i class="fa fa-file-pdf-o"></i> Lihat Dokumen</a>';
			} else {
			    $r[] = '-';
			}

			// Status Badge
			if ($row->status == 'pending') {
			    $badge = '<span class="badge badge-warning">Proses</span>';
			} elseif ($row->status == 'approved') {
			    $badge = '<span class="badge badge-success">Disetujui</span>';
			} else {
			    $badge = '<span class="badge badge-danger">Ditolak</span>';
			}
            
            if (!empty($row->catatan_reviewer)) {
                $badge .= '<br><small class="text-muted mt-1 d-block"><i>Catatan Evaluasi: '.htmlentities($row->catatan_reviewer).'</i></small>';
            }
			$r[] = $badge;

			// Aksi Maker
			$aksi = '';
			if ($row->status == 'pending' && $this->session->userdata('jabatan') == 'AdminLembaga') {
			    // $aksi = '<button class="btn btn-sm btn-danger" onclick="hapus_usulan('.$row->id_usulan.')"><i class="fa fa-trash"></i> Hapus</button>';
				$aksi = '-';
			} else if ($row->status == 'rejected' && $this->session->userdata('jabatan') == 'AdminLembaga') {
			    $aksi = '<button class="btn btn-sm btn-warning" onclick="edit_usulan('.$row->id_usulan.')"><i class="fa fa-pencil"></i> Revisi</button>';
			    // $aksi .= '<button class="mt-1 btn btn-sm btn-danger" onclick="hapus_usulan('.$row->id_usulan.')"><i class="fa fa-trash"></i> Hapus</button>';
			} else {
			    $aksi = '<button class="btn btn-sm btn-secondary" disabled><i class="fa fa-ban"></i> Terkunci</button>';
			}
			$r[] = $aksi;

			$data[] = $r;
		}

		echo json_encode(array("data" => $data));
	}

    // Ajax load select2 pegawai di form tambah
    public function get_pegawai_options() {
        $id_lembaga = $this->session->userdata('lembaga');
        if (!$id_lembaga) {
             echo json_encode([]); return;
        }

        $this->db->select('penempatan.id_penempatan, umana.nama_lengkap, umana.tmt_struktural, penempatan.tunj_kel, penempatan.tunj_anak, penempatan.tunj_mp, penempatan.kehormatan');
        $this->db->from('penempatan');
        $this->db->join('umana', 'penempatan.nik = umana.nik');
        $this->db->where('penempatan.id_lembaga', $id_lembaga);
        $this->db->where('penempatan.tgl_selesai >=', date('Y-m-d')); // Pegawai aktif
        $this->db->order_by('umana.nama_lengkap', 'ASC');

        $result = $this->db->get()->result();
        
        $tahun_skrg = (int)date('Y');
        foreach($result as $r) {
            $tahun_awal = !empty($r->tmt_struktural) ? (int)date('Y', strtotime($r->tmt_struktural)) : 0;
            $r->mp = $tahun_awal > 0 ? max(0, $tahun_skrg - $tahun_awal) : 0;
        }

        echo json_encode($result);
    }

	public function ajax_add()
	{
		$this->Login_model->getsqurity();

        if ($this->input->post('id_penempatan') == '' || $this->input->post('tmt_baru') == '') {
            echo json_encode(['status' => false, 'message' => 'Lengkapi form yang wajib diisi.']);
            return;
        }

		// Validasi dan Upload PDF/JPG
		if (empty($_FILES['file_dokumen']['name'])) {
			echo json_encode(["status" => false, "message" => "Bukti SK Lampiran wajb dilampirkan."]);
			return;
		}

		$uploadedFile = $this->_do_upload();

        // Ambil data lama dengan JOIN ke umana untuk dapat tmt_struktural
        $id_penempatan = $this->input->post('id_penempatan', true);
        $this->db->select('umana.tmt_struktural, penempatan.tunj_kel, penempatan.tunj_anak, penempatan.tunj_mp, penempatan.kehormatan');
        $this->db->from('penempatan');
        $this->db->join('umana', 'penempatan.nik = umana.nik');
        $this->db->where('penempatan.id_penempatan', $id_penempatan);
        $penempatan_lama = $this->db->get()->row();

        if(!$penempatan_lama) {
             echo json_encode(["status" => false, "message" => "Pegawai tidak valid."]);
			 return;
        }

        $tahun_skrg = (int)date('Y');
        $tahun_awal = !empty($penempatan_lama->tmt_struktural) ? (int)date('Y', strtotime($penempatan_lama->tmt_struktural)) : 0;
        $mp_lama = $tahun_awal > 0 ? max(0, $tahun_skrg - $tahun_awal) : 0;

		$data = array(
            'id_penempatan'     => $id_penempatan,
            'id_pengirim'       => $this->session->userdata('id_pengguna'),
            'tmt_lama'          => $penempatan_lama->tmt_struktural,
            'tmt_baru'          => $this->input->post('tmt_baru', true),
            'rank_lama'         => $mp_lama,
            'rank_baru'         => null, // Diisi null karena MP dikalkulasi runtime berdasarkan tmt_baru
            'file_dokumen'      => $uploadedFile,
            'keterangan_usulan' => $this->input->post('keterangan_usulan', true),
            'tunj_kel_lama'     => $penempatan_lama->tunj_kel,
            'tunj_kel_baru'     => $this->input->post('tunj_kel_baru', true) ?: $penempatan_lama->tunj_kel,
            'tunj_anak_lama'    => $penempatan_lama->tunj_anak,
            'tunj_anak_baru'    => $this->input->post('tunj_anak_baru', true) ?: $penempatan_lama->tunj_anak,
            'tunj_mp_lama'      => $penempatan_lama->tunj_mp,
            'tunj_mp_baru'      => $this->input->post('tunj_mp_baru', true) ?: $penempatan_lama->tunj_mp,
            'kehormatan_lama'   => $penempatan_lama->kehormatan,
            'kehormatan_baru'   => $this->input->post('kehormatan_baru', true) ?: $penempatan_lama->kehormatan,
            'status'            => 'pending',
            'tanggal_usulan'    => date('Y-m-d H:i:s')
		);

		$this->Usulan_tmt_model->create($data);
		echo json_encode(array("status" => true, "message" => "Usulan perubahan dikirim."));
	}

    public function ajax_hapus($id)
    {
        $this->Login_model->getsqurity();

        // Cari data file
        $usulan = $this->db->get_where('usulan_perubahan_tmt', ['id_usulan' => $id])->row();
        if ($usulan) {
            // Hapus file fisik jika pending atau rejected (untuk menghemat storage)
            if (($usulan->status == 'pending' || $usulan->status == 'rejected') && $usulan->file_dokumen) {
                $file = rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'usulan_tmt' . DIRECTORY_SEPARATOR . $usulan->file_dokumen;
                if (file_exists($file)) {
                    unlink($file);
                }
            }
            $this->Usulan_tmt_model->hapus($id);
            echo json_encode(["status" => true, "message" => "Usulan berhasil dibatalkan/dihapus."]);
        } else {
            echo json_encode(["status" => false, "message" => "Data usulan tidak ditemukan."]);
        }
    }

    public function ajax_edit($id)
    {
        $this->Login_model->getsqurity();
        $this->db->select('usulan_perubahan_tmt.*, umana.nama_lengkap');
        $this->db->from('usulan_perubahan_tmt');
        $this->db->join('penempatan', 'usulan_perubahan_tmt.id_penempatan = penempatan.id_penempatan');
        $this->db->join('umana', 'penempatan.nik = umana.nik');
        $this->db->where('usulan_perubahan_tmt.id_usulan', $id);
        $data = $this->db->get()->row();

        if ($data) {
            echo json_encode($data);
        } else {
            echo json_encode(["status" => false]);
        }
    }

    public function ajax_update()
    {
        $this->Login_model->getsqurity();

        $id_usulan = $this->input->post('id_usulan', true);
        if (empty($id_usulan)) {
            echo json_encode(['status' => false, 'message' => 'ID Usulan tidak ditemukan.']); return;
        }

        // Cari data eksisting
        $usulan_lama = $this->db->get_where('usulan_perubahan_tmt', ['id_usulan' => $id_usulan])->row();
        if (!$usulan_lama) {
            echo json_encode(['status' => false, 'message' => 'Usulan lama tidak ditemukan.']); return;
        }

        $id_penempatan = $this->input->post('id_penempatan', true);
        $this->db->select('umana.tmt_struktural, penempatan.tunj_kel, penempatan.tunj_anak, penempatan.tunj_mp, penempatan.kehormatan');
        $this->db->from('penempatan');
        $this->db->join('umana', 'penempatan.nik = umana.nik');
        $this->db->where('penempatan.id_penempatan', $id_penempatan);
        $penempatan_lama = $this->db->get()->row();

        // Siapkan Dokumen
        $kodeDokumen = $usulan_lama->file_dokumen;
        if (!empty($_FILES['file_dokumen']['name'])) {
            // Upload baru
            $kodeDokumen = $this->_do_upload();
            // Hapus yang lama dari storage jika berhasil upload baru
            if ($usulan_lama->file_dokumen) {
                $fileLama_path = rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'usulan_tmt' . DIRECTORY_SEPARATOR . $usulan_lama->file_dokumen;
                if (file_exists($fileLama_path)) unlink($fileLama_path);
            }
        }

        $tahun_skrg = (int)date('Y');
        $tahun_awal = !empty($penempatan_lama->tmt_struktural) ? (int)date('Y', strtotime($penempatan_lama->tmt_struktural)) : 0;
        $mp_lama = $tahun_awal > 0 ? max(0, $tahun_skrg - $tahun_awal) : 0;

        $data = array(
            'id_penempatan'     => $id_penempatan,
            'id_pengirim'       => $this->session->userdata('id_pengguna'),
            'tmt_lama'          => $penempatan_lama->tmt_struktural,
            'tmt_baru'          => $this->input->post('tmt_baru', true),
            'rank_lama'         => $mp_lama,
            'rank_baru'         => null,
            'file_dokumen'      => $kodeDokumen, // Tetap dokumen lama jika tidak upload baru
            'keterangan_usulan' => $this->input->post('keterangan_usulan', true),
            'tunj_kel_lama'     => $penempatan_lama->tunj_kel,
            'tunj_kel_baru'     => $this->input->post('tunj_kel_baru', true) ?: $penempatan_lama->tunj_kel,
            'tunj_anak_lama'    => $penempatan_lama->tunj_anak,
            'tunj_anak_baru'    => $this->input->post('tunj_anak_baru', true) ?: $penempatan_lama->tunj_anak,
            'tunj_mp_lama'      => $penempatan_lama->tunj_mp,
            'tunj_mp_baru'      => $this->input->post('tunj_mp_baru', true) ?: $penempatan_lama->tunj_mp,
            'kehormatan_lama'   => $penempatan_lama->kehormatan,
            'kehormatan_baru'   => $this->input->post('kehormatan_baru', true) ?: $penempatan_lama->kehormatan,
            'status'            => 'pending', // Dikembalikan ke pending
            'catatan_reviewer'  => null, // Hapus catatan revisi penolakan sebelumnya
            'id_reviewer'       => null,
            'tanggal_verifikasi'=> null,
            'tanggal_usulan'    => date('Y-m-d H:i:s')
        );

        $this->db->where('id_usulan', $id_usulan);
        $this->db->update('usulan_perubahan_tmt', $data);

        echo json_encode(array("status" => true, "message" => "Usulan revisi berhasil dikirim ulang ."));
    }

	private function _do_upload()
	{
		$uploadDir = rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'usulan_tmt' . DIRECTORY_SEPARATOR;

		if (!is_dir($uploadDir)) {
			@mkdir($uploadDir, 0755, true);
		}

		$config['upload_path']      = $uploadDir;
		$config['allowed_types']    = 'pdf|jpg|jpeg|png';
		$config['max_size']         = 5120; // max 5MB
		$config['file_name']        = 'TMT_'.random_string('alnum', 8) . time();
		$config['file_ext_tolower'] = TRUE;

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('file_dokumen')) {
            $err = strip_tags($this->upload->display_errors('', ''));
            echo json_encode(["status" => false, "message" => "Upload gagal: " . $err]);
            exit();
		}

		return $this->upload->data('file_name');
	}
}
