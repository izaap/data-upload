<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Upload_type extends CI_controller
{
    public $data = array();

    function __construct()
    {

        parent::__construct();

        if (!is_logged_in()) {
            redirect('login');
        }

        $this->load->library('form_validation');
        $this->load->model("user_model");


    }

    function index($address_id=0)
    {
        //get user role
        $role = get_user_role();
        $user_data = get_user_data(); 
        $user_name = rtrim($user_data['name']);

        $this->data['userlist'] = array();
        $error_message = '';
        
        //set form rules
        $this->form_validation->set_rules('type', 'Type', 'required');
        if($role == 1)
        {
          //  $this->form_validation->set_rules('user_id', 'Select User', 'required');
        $this->data['userlist'] = $this->user_model->get_users(array("role !=" =>
                1));
        }

       if ($this->form_validation->run() === TRUE){
            

            if($role !=1){
                $user_id = get_current_user_id();
            }
            else
            {
                $user_id=$this->input->post('user_id');
            }  

            //$this->session->set_userdata('upload_user',$user_id);  
            
            if($this->input->post('type')=='web')
            {
                
                //check time of add
                $access = $access = ($role==1)?TRUE:check_time_of("add");
                
                if($access)
                {
                    redirect('upload_form/'.$user_id);
                }
                else
                {
                   $this->service_message->set_flash_message('web_form_add_error'); 
                   redirect('upload_type'); 
                }

            }else{
                redirect('upload');
            }

        }  

        $this->data['role'] = $role;
        $this->data['user_name'] = $user_name;
        $this->layout->view('upload_type', $this->data);
    }

}
