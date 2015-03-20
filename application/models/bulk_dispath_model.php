<?php
Class Bulk_dispath_model extends CI_Model
{
    protected $_table = 'bulk_dispath_period_data';
    
    function __construct() {
        parent::__construct();
    }

    function getTableName()
    {
        return 'bulk_dispath_period_data';
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

    function get_data($user_id = 0, $date = null)
    {
       $where = array('user_id' => $user_id);

       if( !is_null($date) )
       {
            $where['date'] = $date;
            $this->db->where($where);
            return $this->db->get($this->_table)->row_array();
       }
       else
       {
            $where['date'] = date('Y-m-d');
            $this->db->where($where);
            $result = $this->db->get($this->_table)->row_array();

            if(count($result))
                return $result;

            $where['date'] = date('Y-m-d', strtotime("yesterday"));
            $this->db->where($where);
            return $this->db->get($this->_table)->row_array();
       }

       
    }
}
