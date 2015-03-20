<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports extends CI_Controller {

	public $data = array();

	function __construct()
    {
    	parent::__construct();
        
        if(! is_logged_in()){
            redirect('login');
        }

    	$this->load->library('form_validation');
    }

	public function index()
	{
		
		$this->layout->view('form_report');
	}
	

	

	
}

/* End of file Index.php */
/* Location: ./application/controllers/index.php */