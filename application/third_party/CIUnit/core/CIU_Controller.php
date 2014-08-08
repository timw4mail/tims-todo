<?php

if (! defined('BASEPATH')) {
	exit('No direct script access');
}

class CIU_Controller extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->database('test');
	}
	
	public function index()
	{
		return;
	}
	
}

/* End of file CIU_Controller.php */
/* Location: ./application/third_party/CIUnit/core/CIU_Controller.php */