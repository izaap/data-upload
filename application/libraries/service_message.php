<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Service_message {

    private $_CI;

    private $_message   = null;

    private $_template = '_partials/service_message.php';
    
    public function __construct()
    {        
        $this->_CI =& get_instance();

        $this->_CI->load->language('service_messages');
    }

    public function get_message() 
    {
        if ($this->_message) {
            return $this->_message;
        }
        return $this->_CI->session->flashdata('service_message');
    }

    public function set_message($message_key, $replace_items = null) 
    {                                                                                                               
        $message = $this->_prepare_message($message_key, $replace_items);
        $this->_message = $message;
    }    

    public function set_flash_message($message_key, $replace_items = null) 
    {
        $message = $this->_prepare_message($message_key, $replace_items);
        $this->_CI->session->set_flashdata('service_message', $message);
    }    

    public function render($template = null)
    {
		
        $data = array(
            'service_message' => $this->get_message()
        );
		//echo '<pre>';print_r($data);exit;
        if (!$template) {
            $template = $this->_template;
        }
		if(isset($data['service_message']['header']))
		{
			return 'header';
		}
		if(isset($data['service_message']['message']))
		{
			return $this->_CI->load->view($template, $data, true);
		} 
		else {
			return '';
		}
    }

    private function _prepare_message($message_key, $replace_items = null)
    {
        $service_message = $this->_CI->lang->line($message_key);      
        if ($service_message) {
            if ($replace_items) {
                if (!is_array($replace_items)) {
                    $replace_items = array($replace_items);
                }
                $service_message['message'] = vsprintf($service_message['message'], $replace_items);
            }             
            return $service_message;
        } 
        return false;                                    
    }
}

?>