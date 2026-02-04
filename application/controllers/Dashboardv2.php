<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboardv2 extends CI_Controller {
	public function __construct()
    {
        parent::__construct();
        $this->load->model('Login_model');
        $this->load->model('Dashboardv2_model');
        $this->load->model('Login_model');
        $this->load->helper('Rupiah_helper');
    }

    public function index()
    {
        $this->Login_model->getsqurity();

        $isi['css']     = 'Dashboardv2/Css';
        $isi['content'] = 'Dashboardv2/Dashboardv2';
        $isi['ajax']    = 'Dashboardv2/Ajax';

        $this->load->view('Template', $isi);
    }

    public function get_laporan()
    {
        $periode = $this->input->post('periode'); // format: YYYY-MM

        if (!$periode) {
            echo json_encode(["data" => []]);
            return;
        }

        // pisahkan bulan dan tahun
        $bulan = date('m', strtotime($periode . "-01"));
        $tahun = date('Y', strtotime($periode . "-01"));

        $data_laporan = $this->Dashboardv2_model->get_laporan_bulanan($bulan, $tahun);

        $no = 1;
        $result = [];

        foreach ($data_laporan as $r) {
            $row = [];

            $row[] = $no++;
            $row[] = $r->nik;
            $row[] = $r->nama_lembaga;
            $row[] = $r->nama_lengkap;
            $row[] = $r->nama_jabatan;
            $row[] = $r->kehadiran;
            $row[] = rupiah($r->nominal_kehadiran);
            $row[] = rupiah($r->tunkel);
            $row[] = rupiah($r->tunj_anak);
            $row[] = rupiah($r->tmp);
            $row[] = rupiah($r->kehormatan);
            $row[] = rupiah($r->tbk);
            $row[] = rupiah($r->barokah_khusus);

            // total awal = diterima + potongan
            $total_awal = $r->diterima + $r->potongan;
            $row[] = rupiah($total_awal);

            $row[] = rupiah($r->potongan);
            $row[] = rupiah($r->diterima);
            $row[] = $r->timestamp;

            $result[] = $row;
        }

        echo json_encode([
            "data" => $result
        ]);
    }
}