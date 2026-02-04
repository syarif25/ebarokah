<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kehormatan_pengajar extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Login_model');
		$this->load->model('kehormatan_pengajar_model');
        $this->load->helper('Rupiah_helper');
	}

	public function index(){
		$this->Login_model->getsqurity() ;
		$isi['css'] 	= 'Kehormatan_pengajar/Css';
		$isi['content'] = 'Kehormatan_pengajar/Kehormatan_pengajar';
		$isi['ajax'] 	= 'Kehormatan_pengajar/Ajax';
		$this->load->view('Template',$isi);
	}

	public function data_list()
	{
		$this->load->helper('url');

		$list = $this->kehormatan_pengajar_model->get_datatables();
		$no =1;
		$data = array();
		foreach ($list as $datanya) {
			
			$row = array();
			$row[] = $no++;
			$row[] = htmlentities($datanya->kategori);
			$row[] = htmlentities($datanya->min_masa_pengabdian.' - '.$datanya->max_masa_pengabdian);
            $row[] = rupiah($datanya->nominal);
			//add html for action
			$row[] = '<a type="button" class="btn btn-outline-danger btn-sm" href="#" 
			title="Track" onclick="edit_user('."'".$datanya->id_barokah_kehormatan_pengajar."'".')"><i class="bx bx-edit mr-1" ></i> Edit</a>';
		$data[] = $row;
		}
			$output = array("data" => $data);
		echo json_encode($output);
	}

    public function ajax_add()
	{
		$this->_validate();
		$data = array(
            'id_barokah_kehormatan_pengajar' 	    => '',
            'kategori' 					=> $this->input->post('kategori'),
			'min_masa_pengabdian' 	=> $this->input->post('min_masa_pengabdian'),
            'max_masa_pengabdian' 	=> $this->input->post('max_masa_pengabdian'),
            'nominal' 				=> str_replace(".", "",$this->input->post('nominal ')),
        );

		$simpan = $this->kehormatan_pengajar_model->create('barokah_kehormatan_pengajar',$data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_update(){
        $this->_validate();
       $data = array(
        'kategori' 				=> $this->input->post('kategori'),
		'min_masa_pengabdian' 	=> $this->input->post('min_masa_pengabdian'),
        'max_masa_pengabdian' 	=> $this->input->post('max_masa_pengabdian'),
        'nominal' 				=> str_replace(".", "",$this->input->post('nominal')),
        );
        
		$this->kehormatan_pengajar_model->update(array('id_barokah_kehormatan_pengajar' => $this->input->post('id_barokah_kehormatan_pengajar')), $data);
		echo json_encode(array("status" => TRUE));
	}

	public function ajax_edit($id)
	{
		$data = $this->kehormatan_pengajar_model->get_by_id($id);
		echo json_encode($data);
	}

	private function _validate()
    {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        if ($this->input->post('min_masa_pengabdian') == '') {
            $data['inputerror'][] = 'min_masa_pengabdian';
            $data['error_string'][] = ' harus diisi';
            $data['status'] = FALSE;
        }

        if ($this->input->post('max_masa_pengabdian') == '') {
            $data['inputerror'][] = 'max_masa_pengabdian';
            $data['error_string'][] = ' harus diisi';
            $data['status'] = FALSE;
        }

        if ($this->input->post('nominal') == '') {
            $data['inputerror'][] = 'nominal';
            $data['error_string'][] = 'Nominal harus diisi';
            $data['status'] = FALSE;
        }

      if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }

   


}
