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
class Studies_model extends CI_Model
{

    public $_master_field_of_studies = 'master_field_of_studies';

    function __construct()
    {
        //load the parent constructor
        parent::__construct();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getAllStudies()
     * @ Added Date               : 28-09-2016
     * @ Added By                 : Piyalee    
     * -----------------------------------------------------------------
     * @ Description              : get all studies
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
    */
    public function getAllStudies($param = array())
    {
        $this->db->select('*');

        if(!empty($param['order_by']) && !empty($param['order'])){
            $this->db->order_by($param['order_by'],$param['order']);
        }

        if(!empty($param['searchByStudy']))
        {
            $this->db->like('field_of_study',$param['searchByStudy']);
        }

        if(!empty($param['page']) && !empty($param['page_size']))
        {
            $limit = $param['page_size'];
            $offset = $limit*($param['page']-1);
            $this->db->limit($limit, $offset);
        }
        $result = $this->db->get($this->_master_field_of_studies)->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getAllStudiesCount()
     * @ Added Date               : 28-09-2016
     * @ Added By                 : Piyalee    
     * -----------------------------------------------------------------
     * @ Description              : get count studies
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
    */
    public function getAllStudiesCount($param = array())
    {
        $this->db->select('count(*) as count_study');

        if(!empty($param['order_by']) && !empty($param['order'])){
            $this->db->order_by($param['order_by'],$param['order']);
        }

        if(!empty($param['searchByStudy']))
        {
            $this->db->like('field_of_study',$param['searchByStudy']);
        }

        $result = $this->db->get($this->_master_field_of_studies)->row_array();
        //echo $this->db->last_query();
        return $result;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : addStudyField()
     * @ Added Date               : 27-09-2016
     * @ Added By                 : Piyalee    
     * -----------------------------------------------------------------
     * @ Description              : add study field
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
    */
    public function addStudyField($param = array())
    {
        $this->db->insert($this->_master_field_of_studies, $param);
        $insert_id = $this->db->insert_id(); 
        return $insert_id;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : checkDuplicateStudyField()
     * @ Added Date               : 29-09-2016
     * @ Added By                 : Piyalee    
     * -----------------------------------------------------------------
     * @ Description              : check field of study
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
    */
    public function checkDuplicateStudyField($param = array())
    {
        $this->db->select("*");
        if($param['field_of_study'])
        {
            $this->db->where('field_of_study', $param['field_of_study']);
        }
        if(isset($param['study_id']))
        {
            $this->db->where('id != ', $param['study_id']);
        }
        $qry = $this->db->get($this->_master_field_of_studies);
        return $qry->result_array();
    }

    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : getStudyDetailById()
    * @ Added Date               : 01-10-2016
    * @ Added By                 : Piyalee    
    * -----------------------------------------------------------------
    * @ Description              : get details study
    * -----------------------------------------------------------------
    * @ param                    : Array(params)    
    * @ return                   : Array
    * -----------------------------------------------------------------
    * 
    */
    public function getStudyDetailById($param = array())
    {
        $this->db->select("*");
        if($param['studyId'])
        {
            $this->db->where('id', $param['studyId']);
        }
        $qry = $this->db->get($this->_master_field_of_studies);
        return $qry->row_array();
    }

    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : updateStudy()
    * @ Added Date               : 01-10-2016
    * @ Added By                 : Piyalee    
    * -----------------------------------------------------------------
    * @ Description              : update study
    * -----------------------------------------------------------------
    * @ param                    : Array(params)    
    * @ return                   : Array
    * -----------------------------------------------------------------
    * 
    */
    public function updateStudy($where = array(),$param = array())
    {
        $this->db->where($where);
        $this->db->update($this->_master_field_of_studies, $param);
        $affected_rows = $this->db->affected_rows(); 
        return $affected_rows;
    }

    /*****************************************
     * End of degree model
    ****************************************/
}