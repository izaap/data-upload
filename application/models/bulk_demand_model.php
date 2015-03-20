<?php
Class Bulk_demand_model extends CI_Model
{
    protected $_table = 'demand_details';
    
    function __construct() {
        parent::__construct();
    }

    function getTableName()
    {
        return 'demand_details';
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
    
    public function get_where($where)
    {
        $this->db->where($where);
       return  $this->db->get($this->_table);
    }
    
    public function check_upload($userId,$date,$table)
    {
        $where = array("user_id" => $userId, "date" => $date);
        $this->db->select('*');
    	$this->db->from($table);
    	$this->db->where($where);
    	$result = $this->db->get();
        return $result->result_array();
    }
    
    public function deleteexcel($where,$table)
    {
       $this->db->where($where);
       return  $this->db->delete($table);
    }
 
}
