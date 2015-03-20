<?php
Class Login_Model extends CI_Model
{
    protected $_table = 'users';
    function __construct() {
        parent::__construct();
    }
    
    public function login($email, $password)
    {
        $this->load->model("user_model");
        $user = $this->user_model->get_by_email($email);

        if(!count($user))
        {
            $user = $this->user_model->get_by_loginid($email);
        }
        
        $pass = md5($password);
        if ($user && $user['password'] == $pass) {

            $this->session->set_userdata('user_data', $user);

            return true;
        }
        return false;
    }

    public function logout()
    {
    	$this->session->sess_destroy();
    }
}
