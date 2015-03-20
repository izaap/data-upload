<?php
Class User_Model extends CI_Model
{
    protected $_table = 'users';
    function __construct() {
        parent::__construct();
    }

    function getTableName()
    {
        return 'user';
    }

    public function get_by_email($email)
    {
        $this->db->select();
        $this->db->from($this->_table);
        $this->db->where('email', $email);
        $result = $this->db->get();
        return $result->row_array();
    }

    public function get_by_loginid($login_id)
    {
        $this->db->select('users.*,address.organization as company');
        $this->db->from($this->_table);
        $this->db->join("address","address.id=users.address_id");
        $this->db->where('users.login_id', $login_id);
        $result = $this->db->get();
        return $result->row_array();
    }
    
	
    public function update_password($id, $password)
    {
        //encode the given password
        $password = md5($password);
        //prepare data to update
        $data = array('password' => $password);
        //prepare where conditions
        $where = array('id' => $id);
        //update
        $affected_rows = $this->update($where, $data);

        return $affected_rows;
    }
    public function add($data)
    {
        return $this->db->insert($this->_table,$data);
    }
    
    public function update($where,$data)
    {
        $this->db->where($where);
       return $this->db->update($this->_table,$data);
    }
    
    public function delete($where)
    {
        $this->db->where($where);
       return  $this->db->delete($this->_table);
    }
    
    function getDatatableRecords()
    {
        $role = get_user_role();

        $this->load->library('Datatables');
        $this->datatables->from('users');
        $this->datatables->select("users.id,users.address_id,roles.id as role_id,users.login_id,users.name,users.email,roles.name as role,if(users.address_id!=0,address.organization,'') as organization,users.designation,users.cellphone,if(roles.id!=3,'dpn','') as plant_access", FALSE);
 
        $this->datatables->join("roles","roles.id=users.role");
         $this->datatables->join("address","address.id=users.address_id",'left');
        if($role !== 1)
        {
            $user_data = get_user_data();
            $this->datatables->where('users.id', $user_data['id']);
        }
        
        $this->datatables->unset_column('users.id,users.address_id,role_id,plant_access');
       
        $edit = '<a class="btn btn-small" href="'.site_url('user/add').'/$1"><i class="icon-edit"></i> Edit</a>';
        $edit.= '<a class="btn btn-small" onclick="return confirm(\'Are you sures want to delete?\')" href="'.site_url('user/delete').'/$1"><i class="icon-remove"></i> Delete</a>';

        /*
        $edit.= '<a class="btn btn-small" href="'.site_url('address/add').'/$2/$1"><i class="icon-plus"></i> Add/Edit Address</a>';

        $edit.= '<a class="btn btn-small $4" href="'.site_url('plant/index').'/$2/$1"><i class="icon-plus"></i> Add Plant</a>';
        */

        $this->datatables->add_column('Modify', $edit, 'users.id,users.address_id,role_id,plant_access');
        echo $this->datatables->generate();
        exit;
    }

    function get_users_profile($where = array())
    {

    	$this->db->select('users.*,roles.name as role_name,roles.id as role_id,group_concat(plant_details.id) as plant_ids,group_concat(distinct plant_details.plant_name) as plant_names');
    	$this->db->from($this->_table);
        $this->db->join("roles","roles.id=users.role");
        $this->db->join("supply_details","supply_details.address_id=users.address_id",'left');
        $this->db->join("plant_details","plant_details.id=supply_details.plant_id",'left');
    	$this->db->where($where);
    	return $this->db->get()->result_array();
       // echo $this->db->last_query();
       //exit;
    }

    function get_users($where = array())
    {

        $this->db->select('users.*,roles.name as role_name,roles.id as role_id');
        $this->db->from($this->_table);
        $this->db->join("roles","roles.id=users.role");
        $this->db->where($where);
        return $this->db->get()->result_array();
       // echo $this->db->last_query();
       //exit;
    }

    function get_users_roles($where = array())
    {
        $this->db->select('id,roles.name as role_name');
    	$this->db->from('roles');
    	$this->db->where($where);
    	$this->db->order_by('id','DESC');
    	return $this->db->get()->result_array();
    }
    
    function get_by_id( $id = 0 )
    {

    	$this->db->select('users.*,roles.name as role_name');
    	$this->db->from($this->_table);
        $this->db->join("roles","roles.id=users.role");
    	$this->db->where('users.id', $id);
    	return $this->db->get()->row_array();
      
    }

    public function update_gate_openings($where,$data)
    {
        $this->db->where($where);
       return $this->db->update('gate_openings',$data);
    }
    
}
