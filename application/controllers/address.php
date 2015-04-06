<?php

class address extends CI_controller
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
        $this->load->model("address_model");
        $this->load->model("demand_model");
        $this->load->model("supply_model");
        $this->load->model("plant_model");
    }

    function index()
    {
        
        //get user role
         $role = get_user_role();
        
        //if user is Admin, diplay Address list. Otherwise display current user address.
        if ($role == 1 && $this->input->is_ajax_request())
        {
            $this->address_model->getDatatableRecords();
           
        } else 
        {
        
            $this->data['useraddress'] = $this->address_model->get_address(get_current_user_id());


        }

        $this->data['role_access'] = $role;
        $this->layout->view('user/address/list', $this->data);
    }


    public function add($type=2,$edit_id = "")
    {
        
        //get user role
         $role = get_user_role();
         
        if( isset($_POST['edit_id']) && (int)$_POST['edit_id'] )
            $edit_id = $_POST['edit_id'];
            
                //set validation rules
                $this->form_validation->set_rules($this->get_rules($edit_id));
                
                $form = $this->security->xss_clean($_POST);
        /*        
            if($role !=1){
                $user_id = get_current_user_id();
            }
            else{
                $user_id=$this->uri->segment(4);
            }    
        */ 
            
        if ($this->form_validation->run()) 
        {
            
                $ins_data = array();
                $ins_data['organization']       = $form['organization'];
                $ins_data['type_of_participant']= $form['user_role'];
                $ins_data['mailing_address']    = $form['mailing_address'];
                $ins_data['location']           = $form['location'];
                $ins_data['city']               = $form['city'];
                $ins_data['telephone']          = $form['telephone'];
                $ins_data['deliverypoint']      = $form['deliverypoint'];

            if ($edit_id) 
            {
                $ins_data['updated_id']   = get_current_user_id();
                $ins_data['updated_time'] = str2DBDT(date("Y-m-d H:i:s"));
                
                $this->address_model->update(array("id" => $edit_id), $ins_data);

                $this->service_message->set_flash_message('record_update_success');
            } 
            else 
            {
                $ins_data['created_id']   = get_current_user_id();
                $ins_data['created_time'] = str2DBDT(date("Y-m-d H:i:s"));
                
                $lastInsertid = $this->address_model->add($ins_data);
                
                /*
                $where = array('id' => $user_id);

                $update_address_id['address_id'] = $lastInsertid;
                
                $this->user_model->update($where, $update_address_id);
                */

                $this->service_message->set_flash_message('record_add_success');

            }
            redirect('address');

        }

        if ($edit_id) 
        {
            
            $edit_data = $this->address_model->get_user_address(array("address.id" => $edit_id));
            
            if (!count($edit_data[0])) 
            {

                $this->service_message->set_flash_message('record_not_found_error');
                
                redirect('address/add');
            }

            $this->data['form_data'] = (array )$edit_data[0];

        } 
        else
        {
            if (count($form)) 
            {

                $this->data['form_data'] = $form;
                $this->data['form_data']['id'] = $edit_id;
            } 
            else 
            {
                $this->data['form_data'] = array(
                    "id" => '',
                    'organization' => '',
                    'type_of_participant' => '',
                    "mailing_address" => '',
                    "main_address" => '',
                    "location" => '',
                    "city" => '',
                    "telephone" => '',
                    "deliverypoint" => '');
                 
            }
        }
        
        $this->data['roles'] = $this->user_model->get_users_roles();

        $this->data['form_data']['role'] = $role; 
        //$this->data['form_data']['user_id'] = $user_id; 
        $getdemand_details = $this->demand_model->get_address_demands(array('address_id' => $edit_id));

        $getsupply_details = $this->supply_model->get_address_supply(array('s.address_id' => $edit_id)); 

        $this->data['form_data']['demand_details'] = $getdemand_details;

        $this->data['form_data']['supply_details'] = $getsupply_details;

        $this->data['plants'] = $this->plant_model->get_unique_plants($edit_id); 
        //echo $this->db->last_query();
        $this->data['type'] = $type;
        $this->layout->view('/user/address/add', $this->data, true);
    }

    public function add_wholesale($edit_id = "")
    {
        
        //get user role
         $role = get_user_role();
         
        if( isset($_POST['edit_id']) && (int)$_POST['edit_id'] )
            $edit_id = $_POST['edit_id'];
            
        //set validation rules
        $this->form_validation->set_rules($this->get_rules($edit_id));
        
        $form = $this->security->xss_clean($_POST);
            
        if ($this->form_validation->run()) 
        {
            
                $ins_data = array();
                $ins_data['organization']       = $form['organization'];
                $ins_data['type_of_participant']= 3;
                $ins_data['mailing_address']    = $form['mailing_address'];
                $ins_data['location']           = $form['location'];
                $ins_data['city']               = $form['city'];
                $ins_data['telephone']          = $form['telephone'];
                $ins_data['deliverypoint']      = $form['deliverypoint'];

            $company_details_id = 0;
            if ($edit_id) 
            {
                $ins_data['updated_id']   = get_current_user_id();
                $ins_data['updated_time'] = str2DBDT(date("Y-m-d H:i:s"));
                
                $this->address_model->update(array("id" => $edit_id), $ins_data);

                $this->service_message->set_flash_message('record_update_success');

                $company_details_id = $edit_id;
            } 
            else 
            {
                $ins_data['created_id']   = get_current_user_id();
                $ins_data['created_time'] = str2DBDT(date("Y-m-d H:i:s"));
                
                $lastInsertid = $this->address_model->add($ins_data);                

                $this->service_message->set_flash_message('record_add_success');

                $company_details_id = $lastInsertid;

            }

            $this->add_supply_details( $company_details_id );

            redirect('address');

        }

        if ($edit_id) 
        {
            
            $edit_data = $this->address_model->get_user_address(array("address.id" => $edit_id));
            
            if (!count($edit_data[0])) 
            {

                $this->service_message->set_flash_message('record_not_found_error');
                
                redirect('address/add_wholesale');
            }

            $this->data['form_data'] = (array )$edit_data[0];

        } 
        else
        {
            if (count($form)) 
            {

                $this->data['form_data'] = $form;
                $this->data['form_data']['id'] = $edit_id;
            } 
            else 
            {
                $this->data['form_data'] = array(
                    "id" => '',
                    'organization' => '',
                    'type_of_participant' => '',
                    "mailing_address" => '',
                    "main_address" => '',
                    "location" => '',
                    "city" => '',
                    "telephone" => '',
                    "deliverypoint" => '');
                 
            }
        }
        
        $this->data['roles'] = $this->user_model->get_users_roles();

        $this->data['form_data']['role'] = $role; 
        
        $this->data['form_data']['supply_details'] = $this->get_supply_details($edit_id);

        $this->data['plants'] = $this->plant_model->get_unique_plants($edit_id); 
        //echo $this->db->last_query();
        $this->layout->view('/user/address/add_wholesale', $this->data, true);
    }


    function get_supply_details( $address_id = 0 )
    {
        $supply_details = array();

        $source_power = array();
        $amount_power = array();
        $plant        = array();


        if($this->input->post('source_power1'))
            $source_power = $this->input->post('source_power1');

        if($this->input->post('amount_power1'))
            $amount_power = $this->input->post('amount_power1');

        if($this->input->post('plant_sel'))
            $plant = $this->input->post('plant_sel');

        
        //print_r($_POST);die;
        if($source_power && $amount_power && $plant )
        {

            
            foreach($source_power as $key => $val)
            {

                $supply_details[] = array('plant_id' => $plant[$key], 'source' => $source_power[$key], 'power' => $amount_power[$key]);
                 
            }           

        }
        else
        {

            $supply_details = $this->supply_model->get_address_supply(array('s.address_id' => $address_id));
        }  

        return $supply_details;

    }

    function add_supply_details( $address_id = 0 )
    {

        if( !$address_id )
            return FALSE;

        $source_power = $this->input->post('source_power1');
        $amount_power = $this->input->post('amount_power1');
        $plant        = $this->input->post('plant_sel');

        if(count($source_power) && $source_power[0]!="" && $amount_power[0]!="" && $plant[0]!=""){

            //delete before adding new supply's
            $this->supply_model->delete(array("address_id" => $address_id));

            $batch_insert = array();

            foreach($this->input->post('source_power1') as $key => $val){

                if(!empty($plant[$key]) && !empty($source_power[$key]) && !empty($amount_power[$key])){

                    $ins_data = array();
                    $ins_data['address_id']   = $address_id;
                    $ins_data['plant_id']     = $plant[$key];
                    $ins_data['source']       = $source_power[$key];
                    $ins_data['power']        = $amount_power[$key];
                    $ins_data['created_id']   = get_current_user_id();
                    $ins_data['created_time'] = str2DBDT(date("Y-m-d H:i:s"));

                    array_push($batch_insert,$ins_data);
               } 
            }

            $lastInsertid = $this->supply_model->insert($batch_insert);

           return TRUE;

        }

        return FALSE; 
    }

    public function delete($id = "")
    { 

        $update_data = array();
        $update_data['address_id'] = '';
        if ($id) 
        {

            $this->address_model->delete(array("id" => $id));
            
            $this->user_model->update(array("address_id" => $id),$update_data);
            
            $this->service_message->set_flash_message('record_delete_success');
            redirect("address");
        }
    }
    
    function get_rules($edit_id = 0)
    {
        $rules = array();
        $rules[] = array('field' => 'organization', 'label' => 'Organization', 'rules' => 'required');
        //$rules[] = array('field' => 'user_role', 'label' => 'Type of Participant', 'rules' => 'trim|required');
        $rules[] = array('field' => 'mailing_address', 'label' => 'Mailing Address', 'rules' => 'required');

        //$rules[] = array('field' => 'main_address', 'label' => 'Main Address', 'rules' => 'required');
        $rules[] = array('field' => 'location', 'label' => 'Location', 'rules' => 'required');
        $rules[] = array('field' => 'city', 'label' => 'City', 'rules' => 'required');
        $rules[] = array('field' => 'telephone', 'label' => 'Telephone', 'rules' => 'trim|required|numeric');
        $rules[] = array('field' => 'deliverypoint', 'label' => 'Connection Point', 'rules' => 'trim|required');
        $rules[] = array('field' => 'plant_sel[]', 'label' => 'All Plants', 'rules' => 'trim|required');
        $rules[] = array('field' => 'source_power1[]', 'label' => 'All Recipient of Power', 'rules' => 'trim|required');
        $rules[] = array('field' => 'amount_power1[]', 'label' => 'All Contracted Power (MW)', 'rules' => 'trim|required');
              
        return $rules;
    }

    function add_demand(){

        $source_power = $this->input->post('source_power');
        $amount_power = $this->input->post('amount_power');

        if(count($source_power) && $source_power[0]!="" && $source_power[0]!=""){

            //delete before adding new demands
            $this->demand_model->delete(array("address_id" => $this->input->post('address_id')));

            $batch_insert = array();

            foreach($this->input->post('source_power') as $key => $val){

                $ins_data = array();
                $ins_data['user_id']      = '';
                $ins_data['address_id']   = $this->input->post('address_id');
                $ins_data['source']       = $source_power[$key];
                $ins_data['power']        = $amount_power[$key];
                $ins_data['created_id']   = get_current_user_id();
                $ins_data['created_time'] = str2DBDT(date("Y-m-d H:i:s"));

                array_push($batch_insert,$ins_data);
            }

            $lastInsertid = $this->demand_model->insert($batch_insert);

            $output['status'] = "success";
            $output['msg'] = "Record(s) created successfully";

        }else{

            $output['status'] = "error";
            $output['msg'] = "Sorry, Please add atleast one record!";
        }  

        echo json_encode($output); exit; 
    }

    function add_supply(){

        //print_r($_POST); exit;

        $source_power = $this->input->post('source_power1');
        $amount_power = $this->input->post('amount_power1');
        $plant        = $this->input->post('plant_sel');

        if(count($source_power) && $source_power[0]!="" && $amount_power[0]!="" && $plant[0]!=""){

            //delete before adding new supply's
            $this->supply_model->delete(array("address_id" => $this->input->post('address_id')));

            $batch_insert = array();

            foreach($this->input->post('source_power1') as $key => $val){

                if(!empty($plant[$key]) && !empty($source_power[$key]) && !empty($amount_power[$key])){

                    $ins_data = array();
                    $ins_data['address_id']   = $this->input->post('address_id');
                    $ins_data['plant_id']     = $plant[$key];
                    $ins_data['source']       = $source_power[$key];
                    $ins_data['power']        = $amount_power[$key];
                    $ins_data['created_id']   = get_current_user_id();
                    $ins_data['created_time'] = str2DBDT(date("Y-m-d H:i:s"));

                    array_push($batch_insert,$ins_data);
               } 
            }

            $lastInsertid = $this->supply_model->insert($batch_insert);

            $output['status'] = "success";
            $output['msg'] = "Record(s) created successfully";

        }else{

            $output['status'] = "error";
            $output['msg'] = "Sorry, Please add atleast one record!";
        }  

        echo json_encode($output); exit;  
    }


    
    public function add_bulk_user($edit_id = "")
    {
        
        $role = get_user_role();
         
        if( isset($_POST['edit_id']) && (int)$_POST['edit_id'] )
            $edit_id = $_POST['edit_id'];
            
        //set validation rules
        $this->form_validation->set_rules($this->get_bulk_user_rules($edit_id));
        
        $form = $this->security->xss_clean($_POST);
        
            
        if ($this->form_validation->run()) 
        {
            
            $ins_data = array();
            $ins_data['organization']       = $form['organization'];
            $ins_data['type_of_participant']= 2;
            $ins_data['mailing_address']    = $form['mailing_address'];
            $ins_data['location']           = $form['location'];
            $ins_data['city']               = $form['city'];
            $ins_data['telephone']          = $form['telephone'];
            $ins_data['deliverypoint']      = $form['deliverypoint'];

            $company_details_id = 0;
            if ($edit_id) 
            {
                $ins_data['updated_id']   = get_current_user_id();
                $ins_data['updated_time'] = str2DBDT(date("Y-m-d H:i:s"));
                
                $this->address_model->update(array("id" => $edit_id), $ins_data);

                $this->service_message->set_flash_message('record_update_success');

                $company_details_id = $edit_id;
            } 
            else 
            {
                $ins_data['created_id']   = get_current_user_id();
                $ins_data['created_time'] = str2DBDT(date("Y-m-d H:i:s"));
                
                $lastInsertid = $this->address_model->add($ins_data);
                
                $this->service_message->set_flash_message('record_add_success');

                $company_details_id = $lastInsertid;

            }

            $this->add_demand_details($company_details_id);

            redirect('address');

        }

        if ($edit_id) 
        {
            
            $edit_data = $this->address_model->get_user_address(array("address.id" => $edit_id));
            
            if (!count($edit_data[0])) 
            {

                $this->service_message->set_flash_message('record_not_found_error');
                
                redirect('address/add');
            }

            $this->data['form_data'] = (array )$edit_data[0];

        } 
        else
        {
            if (count($form)) 
            {

                $this->data['form_data'] = $form;
                $this->data['form_data']['id'] = $edit_id;
            } 
            else 
            {
                $this->data['form_data'] = array(
                    "id" => '',
                    'organization' => '',
                    'type_of_participant' => '',
                    "mailing_address" => '',
                    "main_address" => '',
                    "location" => '',
                    "city" => '',
                    "telephone" => '',
                    "deliverypoint" => '');
                 
            }
        }
        
        $this->data['roles'] = $this->user_model->get_users_roles();

        $this->data['form_data']['role'] = $role; 
        

        $this->data['form_data']['demand_details'] = $this->get_demand_details($edit_id);

        $this->data['plants'] = $this->plant_model->get_unique_plants($edit_id); 
        
        $this->layout->view('/user/address/add_bulk_user', $this->data, true);
    }

    function get_demand_details( $address_id = 0 )
    {
        $demand_details = array();

        $source_power = $this->input->post('source_power');
        $amount_power = $this->input->post('amount_power');

        if(count($source_power) && $source_power[0]!="" && $source_power[0]!="")
        {

            //delete before adding new demands
            $demand_details = array();

            foreach($source_power as $key => $val)
            {
                $demand_details[] = array('source' => $source_power[$key], 'power' => $amount_power[$key]);
            }
            

        }
        else
        {
           $demand_details = $this->demand_model->get_address_demands(array('address_id' => $address_id));
        }

        return $demand_details;
    }

    function add_demand_details( $address_id = 0 )
    {
        if( !$address_id )
            return FALSE;

        $source_power = $this->input->post('source_power');
        $amount_power = $this->input->post('amount_power');

        if(count($source_power) && $source_power[0]!="" && $source_power[0]!="")
        {

            //delete before adding new demands
            $this->demand_model->delete(array("address_id" => $address_id));

            $batch_insert = array();

            foreach($this->input->post('source_power') as $key => $val)
            {

                $ins_data = array();
                $ins_data['user_id']      = '';
                $ins_data['address_id']   = $address_id;
                $ins_data['source']       = $source_power[$key];
                $ins_data['power']        = $amount_power[$key];
                $ins_data['created_id']   = get_current_user_id();
                $ins_data['created_time'] = str2DBDT(date("Y-m-d H:i:s"));

                array_push($batch_insert,$ins_data);
            }

            $lastInsertid = $this->demand_model->insert($batch_insert);

            return TRUE;

        }
         

        return FALSE; 
    }

    function get_bulk_user_rules($edit_id = 0)
    {
        $rules = array();
        $rules[] = array('field' => 'organization', 'label' => 'Organization', 'rules' => 'required');
        //$rules[] = array('field' => 'user_role', 'label' => 'Type of Participant', 'rules' => 'trim|required');
        $rules[] = array('field' => 'mailing_address', 'label' => 'Mailing Address', 'rules' => 'required');

        //$rules[] = array('field' => 'main_address', 'label' => 'Main Address', 'rules' => 'required');
        $rules[] = array('field' => 'location', 'label' => 'Location', 'rules' => 'required');
        $rules[] = array('field' => 'city', 'label' => 'City', 'rules' => 'required');
        $rules[] = array('field' => 'telephone', 'label' => 'telephone', 'rules' => 'trim|required|numeric');
        $rules[] = array('field' => 'deliverypoint', 'label' => 'deliverypoint', 'rules' => 'trim|required');

        $rules[] = array('field' => 'source_power[]', 'label' => 'Source of power supply', 'rules' => 'trim|required');
        $rules[] = array('field' => 'amount_power[]', 'label' => 'Contracted Power', 'rules' => 'trim|required');

        
        return $rules;
    }


}

?>