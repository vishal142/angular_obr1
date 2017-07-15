<?php
/* * ******************************************************************
* User model for Mobile Api 
---------------------------------------------------------------------
* @ Added by                 : Subhankar 
* @ Framework                : CodeIgniter
* @ Added Date               : 02-03-2016
---------------------------------------------------------------------
* @ Details                  : It Cotains all the api related methods
---------------------------------------------------------------------
***********************************************************************/
class Degree_model extends CI_Model
{
    function __construct()
    {
        //load the parent constructor
        parent::__construct();        
        $this->tables = $this->config->item('tables'); 
    }

    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : getAllDegree()
    * @ Added Date               : 08-09-2016
    * @ Added By                 : Piyalee    
    * -----------------------------------------------------------------
    * @ Description              : get all degree
    * -----------------------------------------------------------------
    * @ param                    : Array(params)    
    * @ return                   : Array
    * -----------------------------------------------------------------
    * 
    */
    public function getAllDegree($param = array())
    {
        $this->db->select('*');

        if(!empty($param['order_by']) && !empty($param['order'])){
            $this->db->order_by($param['order_by'],$param['order']);
        }

        if(!empty($param['searchByName']))
        {
            $this->db->like('degree_name',$param['searchByName']);
        }

        if(!empty($param['page']) && !empty($param['page_size']))
        {
            $limit = $param['page_size'];
            $offset = $limit*($param['page']-1);
            $this->db->limit($limit, $offset);
        }
        $result = $this->db->get($this->tables['master_degrees'])->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : getAllDegreeCount()
    * @ Added Date               : 08-09-2016
    * @ Added By                 : Piyalee    
    * -----------------------------------------------------------------
    * @ Description              : get all degree
    * -----------------------------------------------------------------
    * @ param                    : Array(params)    
    * @ return                   : Array
    * -----------------------------------------------------------------
    * 
    */
    public function getAllDegreeCount($param = array())
    {
        $this->db->select('count(*) as count_degree');
        if(!empty($param['order_by']) && !empty($param['order'])){
            $this->db->order_by($param['order_by'],$param['order']);
        }

        if(!empty($param['searchByName']))
        {
            $this->db->like('degree_name',$param['searchByName']);
        }
        $result = $this->db->get($this->tables['master_degrees'])->row_array();
        //echo $this->db->last_query();
        return $result;
    }

    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : addDegree()
    * @ Added Date               : 08-09-2016
    * @ Added By                 : Piyalee    
    * -----------------------------------------------------------------
    * @ Description              : get all degree
    * -----------------------------------------------------------------
    * @ param                    : Array(params)    
    * @ return                   : Array
    * -----------------------------------------------------------------
    * 
    */
    public function addDegree($param = array())
    {
        $this->db->insert($this->tables['master_degrees'], $param);
        $insert_id = $this->db->insert_id(); 
        return $insert_id;
    }

    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : checkDuplicateDegree()
    * @ Added Date               : 08-09-2016
    * @ Added By                 : Piyalee    
    * -----------------------------------------------------------------
    * @ Description              : get all degree
    * -----------------------------------------------------------------
    * @ param                    : Array(params)    
    * @ return                   : Array
    * -----------------------------------------------------------------
    * 
    */
    public function checkDuplicateDegree($param = array())
    {
        $this->db->select("*");
        if($param['degree_name'])
        {
            $this->db->where('degree_name', $param['degree_name']);
        }
        if(isset($param['degree_id']))
        {
            $this->db->where('id != ', $param['degree_id']);
        }
        $qry = $this->db->get($this->tables['master_degrees']);
        return $qry->result_array();
    }

    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : getDegreeDetailById()
    * @ Added Date               : 08-09-2016
    * @ Added By                 : Piyalee    
    * -----------------------------------------------------------------
    * @ Description              : get all degree
    * -----------------------------------------------------------------
    * @ param                    : Array(params)    
    * @ return                   : Array
    * -----------------------------------------------------------------
    * 
    */
    public function getDegreeDetailById($param = array())
    {
        $this->db->select("*");
        if($param['degreeId'])
        {
            $this->db->where('id', $param['degreeId']);
        }
        $qry = $this->db->get($this->tables['master_degrees']);
        return $qry->row_array();
    }

    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : updateDegree()
    * @ Added Date               : 08-09-2016
    * @ Added By                 : Piyalee    
    * -----------------------------------------------------------------
    * @ Description              : get all degree
    * -----------------------------------------------------------------
    * @ param                    : Array(params)    
    * @ return                   : Array
    * -----------------------------------------------------------------
    * 
    */
    public function updateDegree($where = array(),$param = array())
    {
        $this->db->where($where);
        $this->db->update($this->tables['master_degrees'], $param);
        $affected_rows = $this->db->affected_rows(); 
        return $affected_rows;
    }

    /*****************************************
     * End of degree model
    ****************************************/
}