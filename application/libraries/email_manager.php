<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Email_manager
{
	private $_CI;

	public function __construct($options = array())
	{
		$this->_CI = & get_instance();
		$this->_CI->error_message = '';
	//	$this->_CI->load->helper('email_config');

		foreach ($options as $key => $value) 
		{
			$key = "_{$key}";
			if (isset($this->$key))
				$this->$key = $value;	
		}
		
	}
	
	public function send_email($to, $toname, $from, $from_name, $subject, $message, $cc = array(),$attachments = array())
	{
		$this->_CI->config->load('email_conf');
	
		$this->_CI->load->library('email', $this->_CI->config->item('email'));
		$this->_CI->email->set_newline("\r\n");
	
		$this->_CI->email->from($from,$from_name);
		$this->_CI->email->to($to);
		$this->_CI->email->cc($cc);
		$this->_CI->email->subject($subject);
		$this->_CI->email->message($message);
		foreach ($attachments as $file)
			$this->_CI->email->attach($file);
		
		if ( ! $this->_CI->email->Send())
			return FALSE;
		
		return TRUE;
	}

}