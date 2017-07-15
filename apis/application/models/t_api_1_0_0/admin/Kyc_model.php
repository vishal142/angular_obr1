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
class Kyc_model extends CI_Model
{

    public $_master_kyc_templates       = 'master_kyc_templates';
    public $_master_kyc_documents       = 'master_kyc_documents';
    public $_master_profession_types    = 'master_profession_types';


    function __construct(){
        //load the parent constructor
        parent::__construct();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getAllKYC()
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
    public function getAllKYC($param = array()){
        $this->db->select($this->_master_kyc_templates.'.*, '.$this->_master_kyc_documents.'.document_name, '.$this->_master_profession_types.'.profession_type');

        $this->db->join($this->_master_profession_types, $this->_master_profession_types.'.id = '.$this->_master_kyc_templates.'.fk_profession_type_id', 'left');
        
        $this->db->join($this->_master_kyc_documents, $this->_master_kyc_documents.'.id = '.$this->_master_kyc_templates.'.fk_document_id', 'left');

        if(!empty($param['searchByDocymentType'])){
            $this->db->like('document_type',$param['searchByDocymentType']);
        }

        if(!empty($param['order_by']) && !empty($param['order'])){
            $this->db->order_by($param['order_by'],$param['order']);
        }
        
        /*if(!empty($param['page']) && !empty($param['page_size'])){
            $limit = $param['page_size'];
            $offset = $limit*($param['page']-1);
            $this->db->limit($limit, $offset);
        }*/


        /*$this->db->where('fk_profession_type_id', 1);
        $this->db->where('user_mode', 'A');
        $this->db->where('document_type', 'A');*/

        $result = $this->db->get($this->_master_kyc_templates)->result_array();

        //pre($result,1);

        $new_arr = array();
        foreach ($result as $key => $value) {

            if(!array_key_exists($value['document_type'], $new_arr)){
                $new_arr[$value['document_type']] = array();
            }
            if(!array_key_exists($value['fk_profession_type_id'], $new_arr[$value['document_type']])){
                $new_arr[$value['document_type']][$value['fk_profession_type_id']] = array();
            }


            $new_arr[$value['document_type']][$value['fk_profession_type_id']]['fk_profession_type_id'] = '';

            if(!array_key_exists('B', $new_arr[$value['document_type']][$value['fk_profession_type_id']])){
                $new_arr[$value['document_type']][$value['fk_profession_type_id']]['B'] = array();
            }else{
                if($new_arr[$value['document_type']][$value['fk_profession_type_id']]['fk_profession_type_id'] == ''){
                    $new_arr[$value['document_type']][$value['fk_profession_type_id']]['fk_profession_type_id'] = (isset($new_arr[$value['document_type']][$value['fk_profession_type_id']]['B']['A'][0]['fk_profession_type_id'])) ? $new_arr[$value['document_type']][$value['fk_profession_type_id']]['B']['A'][0]['fk_profession_type_id'] : ((isset($new_arr[$value['document_type']][$value['fk_profession_type_id']]['B']['M'][0]['fk_profession_type_id'])) ? $new_arr[$value['document_type']][$value['fk_profession_type_id']]['B']['M'][0]['fk_profession_type_id'] : '');

                    $new_arr[$value['document_type']][$value['fk_profession_type_id']]['profession_type'] = (isset($new_arr[$value['document_type']][$value['fk_profession_type_id']]['B']['A'][0]['profession_type'])) ? $new_arr[$value['document_type']][$value['fk_profession_type_id']]['B']['A'][0]['profession_type'] : ((isset($new_arr[$value['document_type']][$value['fk_profession_type_id']]['B']['M'][0]['profession_type'])) ? $new_arr[$value['document_type']][$value['fk_profession_type_id']]['B']['M'][0]['profession_type'] : '');              
                }                
            } 

            if(!array_key_exists('L', $new_arr[$value['document_type']][$value['fk_profession_type_id']])){
                $new_arr[$value['document_type']][$value['fk_profession_type_id']]['L'] = array();
            }else{
                if($new_arr[$value['document_type']][$value['fk_profession_type_id']]['fk_profession_type_id'] == ''){                
                    $new_arr[$value['document_type']][$value['fk_profession_type_id']]['fk_profession_type_id'] = (isset($new_arr[$value['document_type']][$value['fk_profession_type_id']]['L']['A'][0]['fk_profession_type_id'])) ? $new_arr[$value['document_type']][$value['fk_profession_type_id']]['L']['A'][0]['fk_profession_type_id'] : ((isset($new_arr[$value['document_type']][$value['fk_profession_type_id']]['L']['M'][0]['fk_profession_type_id'])) ? $new_arr[$value['document_type']][$value['fk_profession_type_id']]['L']['M'][0]['fk_profession_type_id'] : '');

                    $new_arr[$value['document_type']][$value['fk_profession_type_id']]['profession_type'] = (isset($new_arr[$value['document_type']][$value['fk_profession_type_id']]['L']['A'][0]['profession_type'])) ? $new_arr[$value['document_type']][$value['fk_profession_type_id']]['L']['A'][0]['profession_type'] : ((isset($new_arr[$value['document_type']][$value['fk_profession_type_id']]['L']['M'][0]['profession_type'])) ? $new_arr[$value['document_type']][$value['fk_profession_type_id']]['L']['M'][0]['profession_type'] : '');
                }
            }

            if(!array_key_exists('A', $new_arr[$value['document_type']][$value['fk_profession_type_id']])){
                $new_arr[$value['document_type']][$value['fk_profession_type_id']]['A'] = array();
            }else{
                if($new_arr[$value['document_type']][$value['fk_profession_type_id']]['fk_profession_type_id'] == ''){              
                    $new_arr[$value['document_type']][$value['fk_profession_type_id']]['fk_profession_type_id'] = (isset($new_arr[$value['document_type']][$value['fk_profession_type_id']]['A']['A'][0]['fk_profession_type_id'])) ? $new_arr[$value['document_type']][$value['fk_profession_type_id']]['A']['A'][0]['fk_profession_type_id'] : ((isset($new_arr[$value['document_type']][$value['fk_profession_type_id']]['A']['M'][0]['fk_profession_type_id'])) ? $new_arr[$value['document_type']][$value['fk_profession_type_id']]['A']['M'][0]['fk_profession_type_id'] : '');

                    $new_arr[$value['document_type']][$value['fk_profession_type_id']]['profession_type'] = (isset($new_arr[$value['document_type']][$value['fk_profession_type_id']]['A']['A'][0]['profession_type'])) ? $new_arr[$value['document_type']][$value['fk_profession_type_id']]['A']['A'][0]['profession_type'] : ((isset($new_arr[$value['document_type']][$value['fk_profession_type_id']]['A']['M'][0]['profession_type'])) ? $new_arr[$value['document_type']][$value['fk_profession_type_id']]['A']['M'][0]['profession_type'] : '');
                }
            }

            $new_arr[$value['document_type']][$value['fk_profession_type_id']][$value['user_mode']][$value['priority_level']][] = $value;           
        }
        //pre($new_arr,1);
        $docArr = array('I','A','F','D');
        foreach ($docArr as $key => $value){
            if(!array_key_exists($value, $new_arr)){
                $new_arr[$value] = array();
            }
        }
        //pre($new_arr,1);
        return $new_arr;
    }

    

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getKYCData()
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
    public function getKYCData($param = array()){
        //pre($param);
        /*$this->db->select($this->_master_kyc_templates.'.*, '.$this->_master_kyc_documents.'.document_name, '.$this->_master_profession_types.'.profession_type');

        $this->db->join($this->_master_profession_types, $this->_master_profession_types.'.id = '.$this->_master_kyc_templates.'.fk_profession_type_id', 'left');
        
        $this->db->join($this->_master_kyc_documents, $this->_master_kyc_documents.'.id = '.$this->_master_kyc_templates.'.fk_document_id', 'left');*/

        $this->db->select('*');
        $this->db->where('fk_profession_type_id', $param['professionTypeId']);
        $this->db->where('document_type', $param['docType']);
        $this->db->order_by('id', 'asc');

        $result = $this->db->get($this->_master_kyc_templates)->result_array();
        //echo $this->db->last_query();
        //pre($result); 
        if(array_key_exists('deleteData', $param) && !empty($param['deleteData'])){        
            return $result;
        }

        $new_arr = array();
        if(!empty($result)){
            foreach ($result as $key => $value) {

                if(!array_key_exists('B', $new_arr)){
                    $new_arr['B'] = array();
                }
                if(!array_key_exists('L', $new_arr)){
                    $new_arr['L'] = array();
                }
                if(!array_key_exists('A', $new_arr)){
                    $new_arr['A'] = array();
                }

                $new_arr[$value['user_mode']][] = $value;           
            }
        } else {
            $new_arr['B'] = array();
            $new_arr['L'] = array();
            $new_arr['A'] = array();           
        }


        $new_arr['fk_profession_type_id'] = $param['professionTypeId'];
        $new_arr['document_type'] = $param['docType'];

        //pre($new_arr,1);   

        return $new_arr;
    }




    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : editKYCDetail()
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
    public function editKYCDetail($delete_arr = array(), $insert_arr = array()){
        if(!empty($delete_arr) && count($delete_arr) > 0){
            $this->db->delete_batch($this->_master_kyc_templates, $delete_arr, 'id');
        }

        if(!empty($insert_arr) && count($insert_arr) > 0){
            $this->db->insert_batch($this->_master_kyc_templates, $insert_arr);
        }
        return true;
    }






    /*****************************************
     * End of degree model
    ****************************************/
}