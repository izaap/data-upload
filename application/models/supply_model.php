<?php
Class Supply_model extends CI_Model
{
    protected $_table = 'supply_details';
    
    function __construct() {
        parent::__construct();
    }

    function getTableName()
    {
        return 'supply_details';
    }

    public function insert($data)
    {
         $this->db->insert_batch($this->_table,$data);
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
    
    public function get_address_supply($where){

        $this->db->select("s.*,p.plant_name");
        $this->db->from("supply_details s");
        $this->db->join("plant_details p","p.id=s.plant_id");
        $this->db->where($where);
        $result = $this->db->get()->result_array();

        if(is_array($result) && !empty($result))
            return $result;
        else
            return 0;
    }
    
}
