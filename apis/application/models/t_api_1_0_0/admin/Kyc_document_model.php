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
class Kyc_document_model extends CI_Model
{

    public $_master_kyc_documents = 'master_kyc_documents';

    function __construct()
    {
        //load the parent constructor
        parent::__construct();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getAllKYCDocument()
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
    public function getAllKYCDocument($param = array()){
        $this->db->select('*');

        if(!empty($param['searchByDocumentName'])){
            $this->db->like('document_name',$param['searchByDocumentName']);
        }

        if(!empty($param['order_by']) && !empty($param['order'])){
            $this->db->order_by($param['order_by'],$param['order']);
        }

        if(!empty($param['page']) && !empty($param['page_size'])){
            $limit = $param['page_size'];
            $offset = $limit*($param['page']-1);
            $this->db->limit($limit, $offset);
        }
        $result = $this->db->get($this->_master_kyc_documents)->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getAllKYCDocumentCount()
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
    public function getAllKYCDocumentCount($param = array()){
        $this->db->select('id');

        if(!empty($param['searchByDocumentName'])){
            $this->db->like('document_name',$param['searchByDocumentName']);
        }

        if(!empty($param['order_by']) && !empty($param['order'])){
            $this->db->order_by($param['order_by'],$param['order']);
        }

        $result = $this->db->count_all_results($this->_master_kyc_documents);
        //echo $this->db->last_query();
        return $result;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : addKYCDocument()
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
    public function addKYCDocument($param = array()){
        $this->db->insert($this->_master_kyc_documents, $param);
        $insert_id = $this->db->insert_id(); 
        return $insert_id;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : checkDuplicateKYCDocument()
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
    public function checkDuplicateKYCDocument($param = array()){
        $this->db->select("*");
        if($param['document_name']){
            $this->db->where('document_name', $param['document_name']);
        }        
        if($param['documentNameId']){
            $this->db->where('id !=', $param['documentNameId']);
        }        
        $result = $this->db->count_all_results($this->_master_kyc_documents);
        return $result;
    }

    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : getDocumentNameDetailById()
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
    public function getDocumentNameDetailById($param = array()){
        $this->db->select("*");
        if($param['documentNameId']){
            $this->db->where('id', $param['documentNameId']);
        }
        return $this->db->get($this->_master_kyc_documents)->row_array();
    }

    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : editKYCDocument()
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
    public function editKYCDocument($where = array(),$param = array()){
        $this->db->where($where);
        $this->db->update($this->_master_kyc_documents, $param);
        $affected_rows = $this->db->affected_rows(); 
        return $affected_rows;
    }

    /*****************************************
     * End of degree model
    ****************************************/
}