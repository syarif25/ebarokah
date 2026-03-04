<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Usulan_approval extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Login_model');
		$this->load->model('Usulan_tmt_model');
	}

	public function index(){
		$this->Login_model->getsqurity();
        
        // Cek Hak Akses
        if ($this->session->userdata('jabatan') != 'Evaluasi' && $this->session->userdata('jabatan') != 'SuperAdmin') {
            redirect('Dashboard');
        }

		$isi['css'] 	= 'Usulan_approval/Css';
		$isi['content'] = 'Usulan_approval/Persetujuan';
		$isi['ajax'] 	= 'Usulan_approval/Ajax';
		$this->load->view('Template', $isi);
	}

	public function data_list_pending()
	{
		$list = $this->Usulan_tmt_model->get_datatables('pending');
		$data = array();
		$no = 1;

		foreach ($list as $row) {
			$r = array();
			$r[] = $no++;
			$r[] = htmlentities($row->nama_lengkap) . '<br><small class="text-info"><i class="fa fa-building"></i> ' . htmlentities($row->nama_lembaga) . '</small>';
			
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

			// Keterangan Maker
			$r[] = $row->keterangan_usulan ? '<small>' . htmlentities($row->keterangan_usulan) . '</small>' : '-';

			// Aksi Checker
			$aksi = '<button class="btn btn-sm btn-success me-1 mb-1" onclick="acc_usulan('.$row->id_usulan.')" title="Setujui/ACC"><i class="fa fa-check"></i> Setujui</button>';
            $aksi .= '<button class="btn btn-sm btn-danger mb-1" onclick="reject_usulan('.$row->id_usulan.')" title="Tolak Usulan"><i class="fa fa-times"></i> Tolak</button>';
            $r[] = $aksi;

			$data[] = $r;
		}

		echo json_encode(array("data" => $data));
	}

    public function data_list_history()
	{
		$list = $this->Usulan_tmt_model->get_datatables(['approved', 'rejected']);
		$data = array();
		$no = 1;

		foreach ($list as $row) {
			$r = array();
			$r[] = $no++;
			$r[] = htmlentities($row->nama_lengkap) . '<br><small class="text-info"><i class="fa fa-building"></i> ' . htmlentities($row->nama_lembaga) . '</small>';
			
			// Build Rincian Usulan
			$rincian = '<ul class="mb-0 pl-3" style="font-size:0.85rem; padding-left:20px;">';
			if ($row->tmt_lama !== $row->tmt_baru) {
			    $l = $row->tmt_lama ? date('d-m-Y', strtotime($row->tmt_lama)) : '-';
			    $b = date('d-m-Y', strtotime($row->tmt_baru));
			    $rincian .= "<li><b>TMT:</b> {$l} &rarr; {$b}</li>";
			}
			if ($row->tunj_kel_lama !== $row->tunj_kel_baru) $rincian .= "<li><b>Tunkel:</b> {$row->tunj_kel_lama} &rarr; {$row->tunj_kel_baru}</li>";
			if ($row->tunj_anak_lama !== $row->tunj_anak_baru) $rincian .= "<li><b>Tunj Anak:</b> {$row->tunj_anak_lama} &rarr; {$row->tunj_anak_baru}</li>";
			if ($row->tunj_mp_lama !== $row->tunj_mp_baru) $rincian .= "<li><b>Tunj MP:</b> {$row->tunj_mp_lama} &rarr; {$row->tunj_mp_baru}</li>";
			if ($row->kehormatan_lama !== $row->kehormatan_baru) $rincian .= "<li><b>Kehormatan:</b> {$row->kehormatan_lama} &rarr; {$row->kehormatan_baru}</li>";
			$rincian .= '</ul>';
			if ($rincian == '<ul class="mb-0 pl-3" style="font-size:0.85rem; padding-left:20px;"></ul>') $rincian = '<i>Tidak ada usulan komponen</i>';
			
            $r[] = $rincian;

			// Dokumen Link
			if ($row->file_dokumen) {
			    $r[] = '<a href="'.base_url('upload/usulan_tmt/'.$row->file_dokumen).'" target="_blank" class="btn btn-sm btn-info"><i class="fa fa-file-pdf-o"></i> Lihat Dokumen</a>';
			} else {
			    $r[] = '-';
			}

			// Status Badge & Catatan
			if ($row->status == 'approved') {
			    $badge = '<span class="badge badge-success">Disetujui</span>';
			} else {
			    $badge = '<span class="badge badge-danger">Ditolak</span>';
			}
            if (!empty($row->catatan_reviewer)) {
                $badge .= '<br><small class="text-muted mt-1 d-block"><i>Catatan: '.htmlentities($row->catatan_reviewer).'</i></small>';
            }
			$r[] = $badge;

			$data[] = $r;
		}

		echo json_encode(array("data" => $data));
	}

    public function ajax_acc($id)
    {
        $this->Login_model->getsqurity();
        if ($this->session->userdata('jabatan') != 'Evaluasi' && $this->session->userdata('jabatan') != 'SuperAdmin') {
            echo json_encode(["status" => false, "message" => "Akses Ditolak."]); return;
        }

        $catatan = $this->input->post('catatan_reviewer', true);

        // Cari data usulan
        $usulan = $this->db->get_where('usulan_perubahan_tmt', ['id_usulan' => $id])->row();
        if (!$usulan || $usulan->status != 'pending') {
            echo json_encode(["status" => false, "message" => "Usulan tidak valid atau sudah diproses."]); return;
        }

        $this->db->trans_start();

        // 1. Update tmt_struktural di tabel umana
        $penempatan = $this->db->get_where('penempatan', ['id_penempatan' => $usulan->id_penempatan])->row();
        if ($penempatan) {
            $this->db->where('nik', $penempatan->nik);
            $this->db->update('umana', [
                'tmt_struktural' => $usulan->tmt_baru
            ]);

            // 2. Update komponen di tabel penempatan
            $this->db->where('id_penempatan', $usulan->id_penempatan);
            $this->db->update('penempatan', [
                'tunj_kel'   => $usulan->tunj_kel_baru,
                'tunj_anak'  => $usulan->tunj_anak_baru,
                'tunj_mp'    => $usulan->tunj_mp_baru,
                'kehormatan' => $usulan->kehormatan_baru
            ]);
        }

        // 3. Update status usulan
        $this->db->where('id_usulan', $id);
        $this->db->update('usulan_perubahan_tmt', [
            'status' => 'approved',
            'catatan_reviewer' => $catatan,
            'id_reviewer' => $this->session->userdata('id_pengguna'),
            'tanggal_verifikasi' => date('Y-m-d H:i:s')
        ]);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(["status" => false, "message" => "Gagal memproses persetujuan ke database."]);
        } else {
            echo json_encode(["status" => true, "message" => "Usulan berhasil disetujui, Master Data telah diupdate."]);
        }
    }

    public function ajax_reject($id)
    {
        $this->Login_model->getsqurity();
        if ($this->session->userdata('jabatan') != 'Evaluasi' && $this->session->userdata('jabatan') != 'SuperAdmin') {
             echo json_encode(["status" => false, "message" => "Akses Ditolak."]); return;
        }

        $catatan = $this->input->post('catatan_reviewer', true);
        if(empty($catatan)) {
             echo json_encode(["status" => false, "message" => "Alasan penolakan wajib diisi!"]); return;
        }

        // Cari data usulan
        $usulan = $this->db->get_where('usulan_perubahan_tmt', ['id_usulan' => $id])->row();
        if (!$usulan || $usulan->status != 'pending') {
            echo json_encode(["status" => false, "message" => "Usulan tidak valid atau sudah diproses."]); return;
        }

        $this->db->where('id_usulan', $id);
        $this->db->update('usulan_perubahan_tmt', [
            'status' => 'rejected',
            'catatan_reviewer' => $catatan,
            'id_reviewer' => $this->session->userdata('id_pengguna'),
            'tanggal_verifikasi' => date('Y-m-d H:i:s')
        ]);

        echo json_encode(["status" => true, "message" => "Usulan berhasil ditolak."]);
    }
}
