<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once(__DIR__ . "/Config.php");

class data_upload {

	private $_CI;

	public function __construct($options = array())
	{
		$this->_CI = & get_instance();
		$this->_CI->error_message = '';
		$this->initialize($options);    

	}
	
	public function initialize($params = array())
	{
		if(!count($params))
			return FALSE;
	
		foreach ($params as $key => $val)
		{
			$key = "_{$key}";
			if (isset($this->$key))
				$this->$key = $val;
		}
  
	
	}

}