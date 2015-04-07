<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class user extends CI_controller
{
    public $data = array();

    private $_change_password_validation_rules = array(
        array('field' => 'old_password',  'label' => 'Old Password', 'rules' => 'trim|required|xss_clean|min_length[4]|callback_password_check|max_length[20]'),
        array('field' => 'password',  'label' => 'New Password', 'rules' => 'trim|required|xss_clean|min_length[4]|max_length[20]'),
        array('field' => 'conf_password',  'label' => 'Confirm Password', 'rules' => 'trim|required|xss_clean|matches[password]|callback_old_password_check')
    );

    private $_gate_openings_validation_rules= array(
        array('field' => 'first_gate_start_hour',  'label' => 'First gate start hour', 'rules' => 'trim|callback_first_gate_check'),
        array('field' => 'first_gate_start_min',  'label' => 'first gate start min', 'rules' => 'trim'),
        array('field' => 'first_gate_end_hour',  'label' => 'first gate end hour', 'rules' => 'trim'),
        array('field' => 'first_gate_end_min',  'label' => 'first gate end min', 'rules' => 'trim'),
        array('field' => 'second_gate_start_hour',  'label' => 'Second gate start hour', 'rules' => 'trim|callback_second_gate_check'),
        array('field' => 'second_gate_start_min',  'label' => 'Second gate start min', 'rules' => 'trim'),
        array('field' => 'second_gate_end_hour',  'label' => 'Second gate end hour', 'rules' => 'trim'),
        array('field' => 'second_gate_end_min',  'label' => 'Second gate end min', 'rules' => 'trim'),
    );


    function __construct()
    {

        parent::__construct();

        if (!is_logged_in()) {
            redirect('login');
        }

        $this->load->library('form_validation');
        $this->load->model("user_model");
        $this->load->model("address_model");

    }

    function index()
    {
        //get user role
        $role = get_user_role();

        //if user is Admin, diplay user list. Otherwise display profile view.
        if ($role == 1 && $this->input->is_ajax_request()) {
            $this->user_model->getDatatableRecords();
        } else {
            $where = array('users.id' => get_current_user_id());

            $this->data['userdata'] = $this->user_model->get_users_profile($where);

        }

        $this->data['role_access'] = $role;
        $this->layout->view('user/list', $this->data);
    }

    

    function add($edit_id = 0)
    {

        if( isset($_POST['edit_id']) && (int)$_POST['edit_id'] )
            $edit_id = $_POST['edit_id'];       

        //set validation rules
        $this->form_validation->set_rules($this->get_rules($edit_id));
        
        $form = $this->security->xss_clean($_POST);
        
        if ($this->form_validation->run()) 
        {
            $ins_data = array();
            $ins_data['name']         = $form['name'];
            $ins_data['address_id']   = $form['user_company'];
            $ins_data['email']        = $form['email'];
            $ins_data['login_id']     = $form['login_id'];
            $ins_data['fax']          = $form['fax'];
            $ins_data['role']         = $form['user_role'];
            $ins_data['cellphone']    = $form['cellphone'];
            $ins_data['otherphone']   = $form['otherphone'];
            $ins_data['designation']  = $form['designation'];
            $ins_data['plant']        = $form['plant'];
            
        
            if ( strcmp('', $form['password']) !== 0 ) 
            {
                $ins_data['password'] = md5($form['password']);
            }

            if ($edit_id) 
            {
                $ins_data['updated_id']   = get_current_user_id();
                $ins_data['updated_time'] = str2DBDT(date("Y-m-d H:i:s"));
                
                $this->user_model->update(array("id" => $edit_id), $ins_data);

                $this->service_message->set_flash_message('record_update_success');
            } 
            else 
            {
                $ins_data['initial_setup']  = 0;
                $ins_data['created_id']   = get_current_user_id();
                $ins_data['created_time'] = str2DBDT(date("Y-m-d H:i:s"));
                
                $this->user_model->add($ins_data);
                
                $this->service_message->set_flash_message('record_add_success');
            }
            
            redirect('user');

        }

        if ($edit_id) 
        {
            $edit_data = $this->user_model->get_by_id($edit_id);

            if ( !count($edit_data) ) 
            {
                $this->service_message->set_flash_message('record_not_found_error');
                redirect('user');
            }

            $this->data['roles'] = $this->user_model->get_users_roles();

            $this->data['form_data'] = $edit_data;

            $company_list = $this->address_model->get_company_list($edit_data['role']); //array("roles.id"=>$edit_data['role'])
            //echo $this->db->last_query();
        } 
        else
        {
            if ( count($form) ) 
            {

                $this->data['form_data'] = $form;
                $this->data['form_data']['address_id'] = $form['user_company'];
                $this->data['form_data']['role'] = $form['user_role'];
                $this->data['form_data']['id'] = $edit_id;

                $this->data['roles'] = $this->user_model->get_users_roles();
                $company_list=$this->address_model->get_company_list($form['user_role']); //array("roles.id"=>$form['user_role'])
            } 
            else 
            {
                $this->data['form_data'] = array(
                    "id" => 0,
                    "address_id" => 0,
                    'name' => '',
                    'role' => '',
                    "cellphone" => '',
                    "email" => '',
                    "otherphone" => '',
                    "fax" => '',
                    "login_id" => '',
                    "designation" => '',
                    "plant"=>'');

                $this->data['roles'] = $this->user_model->get_users_roles();

                $company_list="";
            }
        }

        $this->data['company_list'] = $company_list;
        $this->layout->view('/user/add', $this->data, true);
    }

    public function delete($id = "")
    {

        $return_arr = array();
        if ($id) 
        {
	    if(get_current_user_id() == $id)
            {
                $this->service_message->set_flash_message('custom_message_error',"Sorry! You can not delete your record.");
                redirect("user");
            }

            $this->user_model->delete(array("id" => $id));
            $this->service_message->set_flash_message('record_delete_success');
            redirect("user");
        }
    }


    
    function get_rules($edit_id = 0)
    {
        $rules = array();
        $rules[] = array('field' => 'login_id', 'label' => 'Login Id', 'rules' => 'required|callback_unique_login_id_check[' . $edit_id . ']');
        $rules[] = array('field' => 'name', 'label' => 'Name', 'rules' => 'required');
        $rules[] = array('field' => 'email', 'label' => 'Email', 'rules' => 'required|valid_email|callback_unique_email_check[' . $edit_id . ']');
       // $rules[] = array('field' => 'cellphone', 'label' => 'Cell Phone', 'rules' => 'required');
       // $rules[] = array('field' => 'otherphone', 'label' => 'Other Phone', 'rules' => 'required');
      //  $rules[] = array('field' => 'fax', 'label' => 'Fax', 'rules' => 'required');
        $rules[] = array('field' => 'user_company', 'label' => 'Company', 'rules' => 'trim|required');
        $rules[] = array('field' => 'user_role', 'label' => 'Type of Participant', 'rules' => 'trim|required');
        if (($edit_id && $this->input->post("password") != "") || !$edit_id)
        {
            $rules[] = array('field' => 'password', 'label' => 'Password', 'rules' => 'trim|required|min_length[4]|matches[conf_password]');
            $rules[] = array('field' => 'conf_password', 'label' => 'Confirmation Password', 'rules' => 'trim|required');
           
        }
        $rules[] = array('field' => 'plant', 'label' => 'Plant', 'rules' => '');
        $rules[] = array('field' => 'designation', 'label' => 'Designation', 'rules' => '');
        return $rules;
    }


    public function unique_email_check($str, $edit_id)
    {
        $where = array();
        if ($edit_id) {
            $where['users.id !='] = $edit_id;
        }
        $where['users.email'] = $str;

        $users_data = $this->user_model->get_users($where);

        if (count($users_data) > 0) {
            $this->form_validation->set_message('unique_email_check', 'Email Already Exist.');
            return false;
        }

        return true;
    }

    public function unique_login_id_check($str, $edit_id)
    {
        $where = array();
        if ($edit_id) {
            $where['users.id !='] = $edit_id;
        }
        $where['users.login_id'] = $str;

        $users_data = $this->user_model->get_users($where);

        if (count($users_data) > 0) {
            $this->form_validation->set_message('unique_login_id_check',
                'Login id Already Exist.');
            return false;
        }

        return true;
    }

    function change_password()
    {
        
        $this->form_validation->set_rules($this->_change_password_validation_rules);

        if ($this->form_validation->run())
        {
            $password = $this->input->post('password');
            $password = md5($password);
            //update user record with new password.
            $this->user_model->update(array('id' => get_current_user_id()), array('password' => $password));

            $this->service_message->set_flash_message('custom_message_success', "Your password has been changed successfully.");
            redirect('user/change_password');
        }    

        $this->layout->view('user/change_password');

    }   

    function password_check($password){

        $result = $this->db->get_where('users',$where = array('id' => get_current_user_id(),'password'=>md5($password) ));

        if($result->num_rows()>0)
        {
            return TRUE;
        }
        else
        {
            $this->form_validation->set_message('password_check', 'Old Password does not matched.');
            return FALSE;
        }    
    }

    function old_password_check($pwd){

        $old_pwd = $this->input->post('old_password');
        
        if($pwd != $old_pwd)
        {
            return TRUE;
        }
        else
        {
            $this->form_validation->set_message('old_password_check', 'New password should not be the same as Old password.');
            return FALSE;
        }    
    }

    function get_rolewise_company($role_id,$sel_id=NULL){

        $company_list = $this->address_model->get_company_list($role_id,$sel_id); //array("roles.id"=>$role_id)
        //echo $this->db->last_query();

        $str = '<span class="add-on"><i class="icon_username"></i> </span>
                <select name="user_company" id="user_company" onchange="wholsaleplant(this.value)" class="span3">
                    <option value="">Select Company</option>';

                            if (!empty($company_list)) {
                                foreach ($company_list as $key => $comp) { 
                                    $sel='';
                                    if ($comp['id'] == $sel_id) {
                                            $sel = "selected";
                                        }
                                                        
                                    $str .= '<option  value="'.$comp['id'].'" '.$sel.'>'.$comp['organization'].'</option>';
                                         
                                }
                            }
                        
                $str .= '</select>';

       echo $str;
       exit;         

    } 

    function get_companywise_plant($address_id,$sel_id=NULL){

        $this->load->model("plant_model");
        $plant_list = $this->plant_model->get_plants($address_id); //array("address_id"=>$address_id)

        $str = '<span class="add-on"><i class="icon_username"></i> </span>
                <select name="plant" id="plant" class="span3">
                    <option value="">Select Plant</option>';

                            if (!empty($plant_list)) {
                                foreach ($plant_list as $key => $plant) { 
                                    $sel='';
                                    if ($plant['id'] == $sel_id) {
                                            $sel = "selected";
                                        }
                                                        
                                    $str .= '<option  value="'.$plant['id'].'" '.$sel.'>'.$plant['plant_name'].'</option>';
                                         
                                }
                            }
                        
                $str .= '</select>';

       echo $str;
       exit;         

    } 

    function gate_openings()
    {
        
       $this->form_validation->set_rules($this->_gate_openings_validation_rules);

        if ($this->form_validation->run())
        {
            $first_gate_start_h = $this->input->post('first_gate_start_hour');
            $first_gate_start_m = $this->input->post('first_gate_start_min');
            $first_gate_end_h   = $this->input->post('first_gate_end_hour');
            $first_gate_end_m   = $this->input->post('first_gate_end_min');

            $firstgate_start = $first_gate_start_h.':'.$first_gate_start_m;
            $firstgate_end   = $first_gate_end_h.':'.$first_gate_end_m;

            $second_gate_start_h = $this->input->post('second_gate_start_hour');
            $second_gate_start_m = $this->input->post('second_gate_start_min');
            $second_gate_end_h   = $this->input->post('second_gate_end_hour');
            $second_gate_end_m   = $this->input->post('second_gate_end_min');
          
            $secondgate_start = $second_gate_start_h.':'.$second_gate_start_m;
            $secondgate_end   = $second_gate_end_h.':'.$second_gate_end_m;

            $insert_data = array(
                'first_gate_opening' => $firstgate_start,
                'first_gate_closing' => $firstgate_end,
                'second_gate_opening' => $secondgate_start,
                'second_gate_closing' => $secondgate_end,
                'updated_date'=> str2DBDT(date("Y-m-d H:i:s"))
                );

            $this->user_model->update_gate_openings(array('id' => 1), $insert_data);

            $this->service_message->set_flash_message('custom_message_success', "Gate openings updated successfully.");
            redirect('user/gate_openings');
        }    

        $this->data['gate_results'] = $this->db->get_where('gate_openings',$where = array('id' => 1))->row_array();
       
        $this->layout->view('user/gate_openings',$this->data);

    } 

    function first_gate_check(){

        //first gate openings
        $first_gate_start_h = $this->input->post('first_gate_start_hour');
        $first_gate_start_m = $this->input->post('first_gate_start_min');
        $first_gate_end_h   = $this->input->post('first_gate_end_hour');
        $first_gate_end_m   = $this->input->post('first_gate_end_min');


        $firstgate_start = $first_gate_start_h.':'.$first_gate_start_m;
        $firstgate_end   = $first_gate_end_h.':'.$first_gate_end_m;


        if(strtotime($firstgate_start) < strtotime($firstgate_end))
        {
            return TRUE;
        }
        else
        {
            $this->form_validation->set_message('first_gate_check', 'First gate closing time should be greater than opening time.');
            return FALSE;
        }
    }

    function second_gate_check(){

        $second_gate_start_h = $this->input->post('second_gate_start_hour');
        $second_gate_start_m = $this->input->post('second_gate_start_min');
        $second_gate_end_h   = $this->input->post('second_gate_end_hour');
        $second_gate_end_m   = $this->input->post('second_gate_end_min');

      
        $secondgate_start = $second_gate_start_h.':'.$second_gate_start_m;
        $secondgate_end   = $second_gate_end_h.':'.$second_gate_end_m;

        if(strtotime($secondgate_start) < strtotime($secondgate_end))
        {
            return TRUE;
        }
        else
        {
            $this->form_validation->set_message('second_gate_check', 'Second gate closing time should be greater than opening time');
            return FALSE;
        }
    }  

    function test(){

        echo date();
        echo phpinfo();
    }  
     
}
