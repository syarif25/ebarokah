<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan_waktu extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Login_model');
        $this->load->model('Laporan_waktu_model');
    }

    public function index(){
        // $this->Login_model->getsqurity();
        if ($this->session->userdata('jabatan') == 'AdminLembaga' or $this->session->userdata('jabatan') == 'umana' ){
			$this->load->view('Error');
		} else {
            $isi['css']     = 'Laporan_waktu/Css';
            $isi['content'] = 'Laporan_waktu/Laporan';
            $isi['ajax']    = 'Laporan_waktu/Ajax';
            $this->load->view('Template', $isi);
        }
    }
    
    public function data_list()
    {
        $this->load->helper('url');
        $list = $this->Laporan_waktu_model->get_datatables();
        $data = array();
        $no = isset($_POST['start']) ? $_POST['start'] : 0;
        foreach ($list as $datanya) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = htmlentities($datanya->nama_lembaga);
            $row[] = htmlentities($datanya->kategori);
            $row[] = htmlentities($datanya->bulan . ' - ' . $datanya->tahun);
            
            $wupload = $datanya->waktu_upload ? "<span class='badge badge-info'>".$datanya->waktu_upload."</span>" : "<span class='text-muted'>-</span>";
            $wacc = $datanya->waktu_acc ? "<span class='badge badge-primary'>".$datanya->waktu_acc."</span>" : "<span class='text-muted'>-</span>";
            $wtransfer = $datanya->waktu_transfer ? "<span class='badge badge-success'>".$datanya->waktu_transfer."</span>" : "<span class='text-muted'>-</span>";

            $row[] = $wupload;
            $row[] = $wacc;
            $row[] = $wtransfer;
            
            // Hitung durasi jika ada waktu upload dan transfer
            $durasi = "<span class='text-muted'>-</span>";
            if($datanya->waktu_upload && $datanya->waktu_transfer){
                $t1 = strtotime($datanya->waktu_upload);
                $t2 = strtotime($datanya->waktu_transfer);
                $diff = $t2 - $t1;
                
                if($diff >= 0){
                    $days = floor($diff / (60 * 60 * 24));
                    $hours = floor(($diff - ($days * 60 * 60 * 24)) / (60 * 60));
                    $minutes = floor(($diff - ($days * 60 * 60 * 24) - ($hours * 60 * 60)) / 60);
                    
                    if($days > 0){
                        $durasi = "<strong>".$days . " Hari " . $hours . " Jam</strong>";
                    } else if ($hours > 0){
                        $durasi = "<strong>".$hours . " Jam " . $minutes . " Menit</strong>";
                    } else {
                        $durasi = "<strong>".$minutes . " Menit</strong>";
                    }
                }
            }
            
            $row[] = $durasi;

            $data[] = $row;
        }

        $output = array(
            "draw" => isset($_POST['draw']) ? $_POST['draw'] : null,
            "recordsTotal" => $this->Laporan_waktu_model->count_all(),
            "recordsFiltered" => $this->Laporan_waktu_model->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }
}
