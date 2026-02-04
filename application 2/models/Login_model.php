<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login_model extends CI_Model{	
	
    public function getsqurity()
	{
		$login = $this->session->userdata('username');
		if (empty($login)) 
		{
			$this->session->sess_destroy(); 
			redirect('login');
		}

	}

}


	
