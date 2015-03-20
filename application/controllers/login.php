<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller 
{
    
   	public $data = array();
        
	private $_login_validation_rules = array(
			array('field' => 'email',    'label' => 'Email',    'rules' => 'trim|required|xss_clean'),
			array('field' => 'password', 'label' => 'Password', 'rules' => 'trim|required|xss_clean|min_length[4]|max_length[20]|alpha_dash')
	);

	private $_captcha_validation_rules = array(
			array('field' => 'recaptcha_response_field', 'label' => 'Captcha field', 'rules' => 'required|check_captcha')
	);

	private $_forgot_password_validation_rules = array(
			array('field' => 'email',  'label' => 'Email', 'rules' => 'trim|required|xss_clean|valid_email')
	);

    private $_reset_password_validation_rules = array(
        array('field' => 'password',  'label' => 'Password', 'rules' => 'trim|required|xss_clean|min_length[4]|max_length[20]'),
        array('field' => 'retype_password',  'label' => 'Retype Password', 'rules' => 'trim|required|xss_clean|matches[password]')
    );

    
	function __construct()
	{
		parent::__construct();
        
        
		$this->load->library('form_validation');
        
        $this->load->model("login_model");

        $this->config->load('email_conf');
	}

	public function index()
	{
		$this->login();
	}

	public function login()
	{
       $this->load->model("user_model");

	   if(is_logged_in()){
            redirect("upload/list_view");
        }

		$this->form_validation->set_rules($this->_login_validation_rules);

		if ($this->form_validation->run())
		{
			if ($this->login_model->login($this->input->post('email'), $this->input->post('password')))
			{
				$this->service_message->set_flash_message('login_success');
				 
				putenv("TZ=America/New_York");
					
				$logdata = array(
						'logintime'  => date('h:i a M, d, Y')
				);
					
				$this->session->set_userdata($logdata,$this->data);

                $userdata = get_user_data();
                if($userdata['initial_setup']==1){

				    redirect('upload/list_view');
                }else{
                   
                    $this->user_model->update(array('id'=>$userdata['id']),array('initial_setup'=>1));
                    
                    redirect('user/change_password');
                }
					
					
			}

			$this->service_message->set_message('custom_message_error','Your username and password did not match. Try again or <a href="'.site_url('login/forgot_password').'">click here</a> to retrieve your login details.');
		}

		$this->layout->view('login/login');
	}
	
	public function logout()
	{
		$this->login_model->logout();
	
		// TODO: temporary solution
		$this->session->sess_create();
		$this->service_message->set_flash_message('logout_success');
	
		redirect('login');
	}
    
    public function forgot_password()
    {
        $this->load->model("user_model");
    	$this->form_validation->set_rules($this->_forgot_password_validation_rules);
    	$this->load->library('encrypt');

    	if ($this->form_validation->run())
    	{
    		$email = $this->input->post('email');
       
    		$user = $this->user_model->get_by_email($email, 0);
                     
    		if (count($user)) 
            {
            	

                $str = 'Your Request has been processed.Please click the following link to reset password. ';
                
                $str .= '<a href = "'.base_url().'index.php/login/reset_password/'.$this->encrypt->encode($email).'">Click here</a>';
                
                $this->load->library('email_manager');
                
                $email_details = $this->config->item('email_details');
                

                 if($email_details)
                    $this->email_manager->send_email($email, '', $email_details['mail_from_id'], $email_details['mail_from_name'], "{$email_details['mail_site_name']} - Password Reset Link", $str);  
            
                $this->service_message->set_flash_message('custom_message_success',"Password Reset Link has been sent to your mail id.");

                redirect('login');
            }
    		else
    		{
    			$this->service_message->set_message('password_restore_error');
    		}
    		
    	}

        $this->layout->view('login/forgot_password', $this->data);       
    }

    function reset_password()
    {
        $this->load->model("user_model");
       // $ctr_fn = "/".$this->uri->segment(1)."/".$this->uri->segment(2)."/";
        
        $enc_str =  substr($_SERVER['REQUEST_URI'],strrpos($_SERVER['REQUEST_URI'], 'reset_password/')+15,strlen($_SERVER['REQUEST_URI']));
        
        $this->load->library('encrypt');
     
        $email_id   = $this->encrypt->decode($enc_str);
        $this->data = array('enc_str' => $enc_str);

        //check if email-id is valid
        $result = $this->db->get_where('users',$where = array('email' => $email_id));
        if($result->num_rows() && isset($email_id))
        {
            $user_details = $result->row_array();
            $this->form_validation->set_rules($this->_reset_password_validation_rules);
            if ($this->form_validation->run()) 
            { 
                $password = $this->input->post('password');
                $password = md5($password);
                //update user record with new password.
                $this->user_model->update(array('id' => $user_details['id']), array('password' => $password));


                $str = '';
                $str .= 'Your password has been changed successfully.<br/><br/>';
                //$str .= 'Password : '.$password;

                $this->load->library('email_manager');
               
                $email_details = $this->config->item('email_details');
                
                if($email_details)
                    $this->email_manager->send_email($email_id, '', $email_details['mail_from_id'], $email_details['mail_from_name'], "{$email_details['mail_site_name']} - Password has been changed", $str);  
                
                
                $this->service_message->set_flash_message('custom_message_success', "Your password has been changed successfully.");
                redirect('login');
            }
            $this->layout->view('login/reset_password', $this->data);
        }
        else
        {
            die('Invalid password reset link.');
        }
    }

    
}

?>
