<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Libur_pesantren extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('Login_model');
	}

	public function index() {
        $this->Login_model->getsqurity();
		$data['libur'] = $this->db->order_by('tahun DESC, bulan DESC')->get('libur_pesantren')->result();
		
		$data['content'] = 'Master_data/Libur_pesantren';
		$data['css'] = 'css';
		$data['ajax'] = 'ajax';
		$this->load->view('Template', $data);
	}

	public function simpan() {
        $this->Login_model->getsqurity();
        $id_libur = $this->input->post('id_libur');
		$data = array(
			'bulan' => $this->input->post('bulan'),
			'tahun' => $this->input->post('tahun'),
			'jumlah_hari' => $this->input->post('jumlah_hari'),
			'keterangan' => $this->input->post('keterangan')
		);

        if (!empty($id_libur)) {
            $this->db->where('id_libur', $id_libur);
            $this->db->update('libur_pesantren', $data);
            $this->session->set_flashdata('pesan', 'Data libur berhasil diperbarui.');
        } else {
            $this->db->insert('libur_pesantren', $data);
            $this->session->set_flashdata('pesan', 'Data libur berhasil ditambahkan.');
        }

		redirect('Libur_pesantren');
	}

	public function hapus($id) {
        $this->Login_model->getsqurity();
		$this->db->where('id_libur', $id);
		$this->db->delete('libur_pesantren');
        $this->session->set_flashdata('pesan', 'Data libur berhasil dihapus.');
		redirect('Libur_pesantren');
	}
}
