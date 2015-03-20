<?php 

class Plant extends CI_controller
{
    public $data = array();

    function __construct()
    {

        parent::__construct();

        if (!is_logged_in()) {
            redirect('login');
        }

        $this->load->library('form_validation');
        $this->load->model("plant_model");


    }

    function index()
    {
        
        //get user role
        $role = get_user_role();
        
        if ($this->input->is_ajax_request())
        {
            $this->plant_model->getDatatableRecords();
        }
           
        $this->data['role_access'] = $role;

        $this->layout->view('user/plant/list', $this->data);
    }

    public function delete($id = "")
    { 

        if ($id) 
        {

            $this->plant_model->delete(array("id" => $id));
                    
            $this->service_message->set_flash_message('record_delete_success');
            redirect("plant");
        }
    }

    function add($edit_id=""){

        try 
        {
            $this->load->library("form_validation");

            if( isset($_POST['edit_id']) && (int)$_POST['edit_id'] )
                $edit_id = $_POST['edit_id'];

            $this->data['edit_id'] = $edit_id;

            //get edited data
            if ($edit_id) 
            {
                $edit_data = $this->plant_model->get_where(array("id"=>$edit_id))->row_array();
            }else{
                $edit_data = array(
                    'plant_name' => '',
                    'location' => '',
                    "telephone" => '',
                    "no_units" => '');
            }   

            $this->data['form_data'] = $edit_data;

            $this->form_validation->set_rules($this->get_rules($edit_id));
                
            if ($this->form_validation->run() === TRUE)
            {
                $ins_data = array();
                $ins_data['plant_name']   = $this->input->post('plant_name');
                $ins_data['location']     = $this->input->post('location');
                $ins_data['telephone']    = $this->input->post('telephone');
                $ins_data['no_units']     = $this->input->post('no_units');
                $ins_data['created_id']   = get_current_user_id();

                if ($edit_id) 
                {                    
                    $this->plant_model->update(array("id" => $edit_id),$ins_data);

                    $msg="Plant has been updated successfully!.";
                } 
                else 
                {
                    $ins_data['created_time'] = str2DBDT(date("Y-m-d H:i:s"));

                    $lastInsertid = $this->plant_model->insert($ins_data);

                    $msg="Plant has been added successfully!.";
                }    


                $output = array('status' => 'success', 'message' => $msg);

            }else{

                $content = $this->load->view('user/plant/add_plant', $this->data, TRUE);
            
                $output = array('status' => 'warning', 'content' => $content);
            }  

        }
        catch (Exception $e)
        {
            $output = array('status' => 'failed', 'message' => $e->getMessage());
        }
        
        echo json_encode($output);
        exit;
        //$this->_ajax_output($output, TRUE);    
    }

    function get_rules($edit_id = 0)
    {
        $rules = array();
        $rules[] = array('field' => 'plant_name', 'label' => 'Plant Nmae', 'rules' => 'required|callback_unique_plant_check['.$edit_id.']');
        $rules[] = array('field' => 'location', 'label' => 'Location', 'rules' => 'required');
        $rules[] = array('field' => 'telephone', 'label' => 'telephone', 'rules' => 'trim|required|integer');
        $rules[] = array('field' => 'no_units', 'label' => 'No.of units', 'rules' => 'trim|required|integer');

        
        return $rules;
    }

    public function unique_plant_check($str,$edit_id) 
    {
        $where = array();
        if ($edit_id) 
        {
            $where['id !='] = $edit_id;
        }
        $where['plant_name'] = $str;
        $users_data = $this->plant_model->get_where($where)->num_rows();
        if($users_data > 0){
            $this->form_validation->set_message('unique_plant_check', 'Plant Already Exist.');
            return FALSE;
        }

        return TRUE;
    }

    /*
    function index($address_id=0)
    {

         //get user role
        $role = get_user_role();

        if($address_id==0){
            $this->service_message->set_flash_message('plant_address_empty');
            redirect('address');
               
        } 

        $this->data['address_id'] = $address_id;

        $where = array('address_id' => $address_id);

        $this->data['plant_data'] = $this->plant_model->get_plants($where);


        $this->data['role_access'] = $role;
        $this->layout->view('user/plant/add', $this->data);
    }

    function add(){

        //$user_id = $this->input->post('user_id');
        $address_id = $this->input->post('address_id');

        $plant_name = $this->input->post('plant_name');
        $no_units   = $this->input->post('no_units');
        $telephone  = $this->input->post('telephone');
        $location   = $this->input->post('location');

        if(count($plant_name) && $plant_name[0]!="" && $plant_name[0]!=""){

            //delete before adding new demands
            $this->plant_model->delete(array("address_id" => $this->input->post('address_id')));

            $batch_insert = array();

            foreach($this->input->post('plant_name') as $key => $val){

                if($plant_name[$key]!="" && $location[$key]!="" && $no_units[$key]!="" && $telephone[$key]!=""){

                    $ins_data = array();
                    //$ins_data['user_id']      = $user_id;
                    $ins_data['address_id']   = $address_id;
                    $ins_data['plant_name']   = $plant_name[$key];
                    $ins_data['location']     = $location[$key];
                    $ins_data['telephone']    = $telephone[$key];
                    $ins_data['no_units']     = $no_units[$key];
                    $ins_data['created_id']   = get_current_user_id();
                    $ins_data['created_time'] = str2DBDT(date("Y-m-d H:i:s"));

                    array_push($batch_insert,$ins_data);
                }
            }

            $lastInsertid = $this->plant_model->insert($batch_insert);

            $msg = "plant_message_success";

        }else{

            $msg = "plant_message_error";
        }

        $this->service_message->set_flash_message($msg);
        
        //redirect('plant/index/'.$address_id); 
        redirect('address');    
    }

    */
}
