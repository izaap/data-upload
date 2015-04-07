<?php
Class Uploads_view_model extends CI_Model
{
    protected $_table = '';
    
    function __construct() {
        parent::__construct();
    }
    
    function get_where($where)
    {
        $this->db->from("uploads_view");
        $this->db->where($where);
        $res = $this->db->get();
        return $res->row_array();
    }
    
    function getUploadsRecords()
    {
        $role = get_user_role();

        $this->load->library('Datatables');
        $this->datatables->from('uploads_view uv');
        $this->datatables->select("users.id,(CASE WHEN roles.id=3 THEN 'wholesale_demand' WHEN roles.id=2 THEN 'bulk_demand' END) as type,DATE_FORMAT(date,'%W , %d-%m-%Y') as date,DATE_FORMAT(date,'%Y-%m-%d') as editdate,users.name,address.organization,roles.name as role,IF(roles.id='3',group_concat(distinct plant_details.plant_name),'') as plant_names,DATE_FORMAT(submission_date,'%d/%m/%Y %H:%i %s') as submission_date",FALSE,FALSE);
        //users.email,users.cellphone

        $this->datatables->join("users","users.id=uv.user_id");
        $this->datatables->join("roles","roles.id=users.role");
        $this->datatables->join("address","address.id=users.address_id");
        $this->datatables->join("supply_details","address.id=supply_details.address_id",'left');
        $this->datatables->join("plant_details","plant_details.id=supply_details.plant_id",'left');
        if($role !== 1)
        {
            $user_data = get_user_data();
            $this->datatables->where('users.id', get_logged_user_id());
        }
        $this->datatables->group_by('users.id,uv.date');
        $this->datatables->unset_column('users.id,type,editdate');
        
        //$edit = '<a class="btn btn-small" href="'.site_url('upload_form').'/$2/$4">Edit</a>';
        $edit  = '<a class="btn btn-small" href="'.site_url('view').'/$2/$4">View</a>';
        $edit .= '<a class="btn btn-small m_left_10" href="'.site_url('download').'/$2/$4/excel">Download</a>';
  
        $this->datatables->add_column('Modify', $edit, 'type,users.id,date,editdate');
       
        $this->datatables->add_column('Download', '<input type="checkbox" class="checkdownloads" name="download[]" value="$1/$3">', 'users.id,date,editdate,type');
        echo $this->datatables->generate();
        //echo $this->db->last_query();
        exit;
    } 
    
    public function download_all_excel($searchkeywords)
    {
        $role = get_user_role();
         
    	$this->db->from("uploads_view uv");
        $this->db->select('user_id,date,type,label,users.name,users.email,roles.name as role,users.cellphone');
        $this->db->join("users","users.id=uv.user_id");
        $this->db->join("roles","roles.id=users.role");
        if($role !== 1)
        {
            $user_data = get_user_data();
            $this->db->where('users.id', get_logged_user_id());
        }
        if(!empty($searchkeywords))
            $this->db->like('uv.date',$searchkeywords);
            $this->db->or_like('users.name',$searchkeywords);
    	
    	$res = $this->db->get();
        //echo $this->db->last_query();
        //exit;
        return $res->result_array();
    }
}