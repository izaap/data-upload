<?php
Class Plant_model extends CI_Model
{
    protected $_table = 'plant_details';
    
    function __construct() {
        parent::__construct();
    }

    function getTableName()
    {
        return 'plant_details';
    }

    public function insert($data)
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
    
    public function get_where($where)
    {
        $this->db->where($where);
       return  $this->db->get($this->_table);
    }


    function getDatatableRecords()
    {
        
        $role = get_user_role();
         
        $this->load->library('Datatables');
        $this->datatables->from('plant_details');
        $this->datatables->select("id,plant_name,location,telephone,no_units", FALSE);
        
        $this->datatables->unset_column('id');

        $edit = '<a class="btn btn-small" href="javascript:;" onclick="add_plant(\'edit\',$1)"><i class="icon-edit"></i> Edit</a>';

        $edit.= '<a class="btn btn-small" onclick="return confirm(\'Are you sures want to delete?\')" href="'.site_url('plant/delete').'/$1"><i class="icon-remove"></i> Delete</a>';
                        
        $this->datatables->add_column('Modify', $edit, 'id,');
       
        echo $this->datatables->generate();
        
        exit;
    }
    
    public function get_plants($address_id){

        $this->db->select("p.*");
        $this->db->from("plant_details p");
        $this->db->join("supply_details s","p.id=s.plant_id");
        $this->db->where('s.address_id',$address_id);
        $this->db->group_by('s.plant_id');
        $result = $this->db->get()->result_array();

        if(is_array($result) && !empty($result))
            return $result;
        else
            return 0;
    }

    function get_unique_plants($address_id){

        $where = "";
        if(!empty($address_id)){
            $where = " WHERE s.address_id !='$address_id'";
        }
        
        $query ="SELECT p.id as plant_id, p.plant_name
                    FROM  plant_details p 
                  LEFT JOIN (select s.plant_id,s.address_id from address a JOIN supply_details s ON(a.id=s.address_id) ".$where." group by s.plant_id,s.address_id) t  ON t.plant_id = p.id";
     
                $query .= " GROUP BY p.id HAVING  COUNT(p.id) < 2 ";  

        $qry = $this->db->query($query); 
         
        $result = $qry->result_array();

        if(is_array($result) && !empty($result))
            return $result;
        else
            return 0;
    }

    function get_plant_supply_details($address_id){

        $this->db->select("p.*,group_concat(s.source) as source,group_concat(s.power) as power");
        $this->db->from("plant_details p");
        $this->db->join("supply_details s","p.id=s.plant_id");
        $this->db->where('s.address_id',$address_id);
        $this->db->group_by('s.plant_id');
        $this->db->order_by('p.no_units','ASC');
        $result = $this->db->get()->result_array();

        return $result;
       
    }
    
}
