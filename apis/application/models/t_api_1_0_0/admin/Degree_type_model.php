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
class Degree_type_model extends CI_Model
{

    public $_master_degree_types = 'master_degree_types';

    function __construct()
    {
        //load the parent constructor
        parent::__construct();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getAllDegreeType()
     * @ Added Date               : 27-09-2016
     * @ Added By                 : Piyalee    
     * -----------------------------------------------------------------
     * @ Description              : get all degree
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
    */
    public function getAllDegreeType($param = array())
    {
        $this->db->select('*');

        if(!empty($param['order_by']) && !empty($param['order'])){
            $this->db->order_by($param['order_by'],$param['order']);
        }

        if(!empty($param['searchByType']))
        {
            $this->db->like('degree_type',$param['searchByType']);
        }

        if(!empty($param['page']) && !empty($param['page_size']))
        {
            $limit = $param['page_size'];
            $offset = $limit*($param['page']-1);
            $this->db->limit($limit, $offset);
        }
        $result = $this->db->get($this->_master_degree_types)->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getAllDegreeTypeCount()
     * @ Added Date               : 27-09-2016
     * @ Added By                 : Piyalee    
     * -----------------------------------------------------------------
     * @ Description              : get all degree
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
    */
    public function getAllDegreeTypeCount($param = array())
    {
        $this->db->select('count(*) as count_degreetype');

        if(!empty($param['order_by']) && !empty($param['order'])){
            $this->db->order_by($param['order_by'],$param['order']);
        }

        if(!empty($param['searchByType']))
        {
            $this->db->like('degree_type',$param['searchByType']);
        }

        $result = $this->db->get($this->_master_degree_types)->row_array();
        //echo $this->db->last_query();
        return $result;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : addDegreeType()
     * @ Added Date               : 27-09-2016
     * @ Added By                 : Piyalee    
     * -----------------------------------------------------------------
     * @ Description              : get all degree type
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
    */
    public function addDegreeType($param = array())
    {
        $this->db->insert($this->_master_degree_types, $param);
        $insert_id = $this->db->insert_id(); 
        return $insert_id;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : checkDuplicateDegreeType()
     * @ Added Date               : 27-09-2016
     * @ Added By                 : Piyalee    
     * -----------------------------------------------------------------
     * @ Description              : get all degree
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
    */
    public function checkDuplicateDegreeType($param = array())
    {
        $this->db->select("*");
        if($param['degree_type'])
        {
            $this->db->where('degree_type', $param['degree_type']);
        }
        if(isset($param['degree_id']))
        {
            $this->db->where('id != ', $param['degree_id']);
        }
        $qry = $this->db->get($this->_master_degree_types);
        return $qry->result_array();
    }

    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : getDegreeTypeDetailById()
    * @ Added Date               : 27-09-2016
    * @ Added By                 : Piyalee    
    * -----------------------------------------------------------------
    * @ Description              : get details degree type
    * -----------------------------------------------------------------
    * @ param                    : Array(params)    
    * @ return                   : Array
    * -----------------------------------------------------------------
    * 
    */
    public function getDegreeTypeDetailById($param = array())
    {
        $this->db->select("*");
        if($param['degreeId'])
        {
            $this->db->where('id', $param['degreeId']);
        }
        $qry = $this->db->get($this->_master_degree_types);
        return $qry->row_array();
    }

    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : updateDegreeType()
    * @ Added Date               : 28-09-2016
    * @ Added By                 : Piyalee    
    * -----------------------------------------------------------------
    * @ Description              : update degree type
    * -----------------------------------------------------------------
    * @ param                    : Array(params)    
    * @ return                   : Array
    * -----------------------------------------------------------------
    * 
    */
    public function updateDegreeType($where = array(),$param = array())
    {
        $this->db->where($where);
        $this->db->update($this->_master_degree_types, $param);
        $affected_rows = $this->db->affected_rows(); 
        return $affected_rows;
    }

    /*****************************************
     * End of degree model
    ****************************************/
}