<?php
Class Wholesale_dispath_model extends CI_Model
{
    protected $_table = 'wholesale_dispath_period_data';
    
    function __construct() {
        parent::__construct();
    }

    function getTableName()
    {
        return 'wholesale_dispath_period_data';
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

    function get_unit_wise_data($date = NULL, $user_id = 0)
    {
        $where = array('user_id' => $user_id);
        if( !is_null($date) )
        {
            $where['date'] = $date;
            $this->db->where($where);
            $result = $this->db->get($this->_table)->result_array();
        }
        else
        {
            $where['date'] = date('Y-m-d');
            $this->db->where($where);
            $result = $this->db->get($this->_table)->result_array();

            if(!count($result))
            {
                $where['date'] = date('Y-m-d', strtotime("yesterday"));
                $this->db->where($where);
                $result = $this->db->get($this->_table)->result_array();
            }
        }

        $data = array();
        foreach ($result as $row) 
        {
            $data[$row['unit_no']][$row['type']] = $row; 
        }

        return $data;
    }
    
}
