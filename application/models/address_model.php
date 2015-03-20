<?php
Class Address_Model extends CI_Model
{
    protected $_table = 'address';
    
    function __construct() {
        parent::__construct();
    }

    function getTableName()
    {
        return 'address';
    }

    public function add($data)
    {
         $this->db->insert($this->_table,$data);
       return $this->db->insert_id();
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
        $this->datatables->from('address');
        $this->datatables->select("address.id,address.organization,roles.name as role_name,address.mailing_address as mailing_address,address.city as city,address.location as location,address.deliverypoint as deliverypoint,if(address.type_of_participant=3,'add_wholsale','add_bulk_user') as type", FALSE);
        
        $this->datatables->join("roles","roles.id=address.type_of_participant",'left');

        $this->datatables->unset_column('address.id,type');
        //$this->datatables->unset_column('user_id');

        $edit = '<a class="btn btn-small" href="'.site_url('address').'/$2/$1"><i class="icon-edit"></i> Edit</a>';

        if( $role === 1)
        {

            $edit.= '<a class="btn btn-small" onclick="return confirm(\'Are you sures want to delete?\')" href="'.site_url('address/delete').'/$1"><i class="icon-remove"></i> Delete</a>';
            
        }

        //$edit.= '<a class="btn btn-small $2"  href="'.site_url('plant/index').'/$1"><i class="icon-plus"></i> Add/Edit Plant</a>';
            

        $this->datatables->add_column('Modify', $edit, 'address.id,type');
       
        echo $this->datatables->generate();
        
        exit;
    }
    
    function get_address($userId)
    {

        $where = array("users.id" => $userId);
        
        $this->db->select("address.id as id,users.id as user_id,roles.id as role_id,address.organization as organization,address.mailing_address as mailing_address,address.main_address as main_address,address.location as location,address.city as city,deliverypoint");
        $this->db->from($this->_table);
        $this->db->join("users","users.address_id=address.id");
        $this->db->join("roles","roles.id=users.role");
        $this->db->where($where);
        $res=$this->db->get();
        
        return $res->row_array();
    }
    function get_user_address($where)
    {
        
        $this->db->select("address.id as id,address.organization as organization,address.type_of_participant,address.mailing_address as mailing_address,address.main_address as main_address,address.location as location,address.city as city,telephone,deliverypoint");
    	$this->db->from($this->_table);
       // $this->db->join("roles","roles.id=users.role");
    	$this->db->where($where);
    	return $this->db->get()->result_array();
        
    }

    function get_company_list($role_id,$sel_id=NULL)
    {   
        
        $sel ='';     
        if(!empty($sel_id)){
            $sel =" AND u.address_id !='$sel_id'";
        }
       
        $query ="SELECT a.id,a.organization
                    FROM  address a 
                  LEFT JOIN (select u.address_id,u.role from users u JOIN roles r ON(r.id=u.role) ".$sel." group by u.address_id,u.id) t  ON t.address_id = a.id";
     
                $query .= " WHERE a.type_of_participant='$role_id' GROUP BY a.id HAVING  COUNT(a.id) < 2 ";  

        $qry = $this->db->query($query);

        return $qry->result_array();

         /*
        $this->db->select("address.id,address.organization");
        $this->db->from($this->_table);
        $this->db->join("roles","roles.id=address.type_of_participant");
        $this->db->where('address.id NOT IN (SELECT address_id FROM users WHERE role=roles.id '.$sel.')', NULL, FALSE);
        $this->db->where($where);
        $this->db->group_by('address.id');
        return $this->db->get()->result_array();
        */

        
    }

}
