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
class Agent_model extends CI_Model{
    function __construct(){
        //load the parent constructor
        parent::__construct();        
        $this->tables = $this->config->item('tables'); 
    }



    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : addAgentCode
    * @ Added Date               : 06-05-2016
    * @ Added By                 : Subhankar Pramanik
    * -----------------------------------------------------------------
    * @ Description              : add admin login sesssion
    * -----------------------------------------------------------------
    * @ param                    : array(param)
    * @ return                   : int()
    * -----------------------------------------------------------------
    * 
    */    
    public function addAgentCode($params){
        $this->db->insert($this->tables['master_agent_codes'], $params);
        return $this->db->insert_id();
    }


    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : getAllAgentCode()
    * @ Added Date               : 20-05-2016
    * @ Added By                 : Subhankar    
    * -----------------------------------------------------------------
    * @ Description              : get all Agent code
    * -----------------------------------------------------------------
    * @ param                    : Array(params)    
    * @ return                   : Array
    * -----------------------------------------------------------------
    * 
    */
    public function getAllAgentCode($param = array()){
        $result = $result_arr = array();
        $this->db->select($this->tables['master_agent_codes'].'.*,'.$this->tables['tbl_user_profile_basics'].'.f_name,'.$this->tables['tbl_user_profile_basics'].'.m_name,'.$this->tables['tbl_user_profile_basics'].'.l_name,'.$this->tables['tbl_user_profile_basics'].'.residence_city,'.$this->tables['tbl_user_profile_basics'].'.fk_user_id AS user_id,'.$this->tables['tbl_user_profile_basics'].'.profile_picture_file_extension,'.$this->tables['tbl_user_profile_basics'].'.s3_media_version,'.$this->tables['master_agent_codes'].'.is_active AS status');

        $this->db->join($this->tables['tbl_user_agent_codes'], $this->tables['tbl_user_agent_codes'].'.agent_code = '.$this->tables['master_agent_codes'].'.agent_code', 'left');
        $this->db->join($this->tables['tbl_user_profile_basics'], $this->tables['tbl_user_profile_basics'].'.fk_user_id = '.$this->tables['tbl_user_agent_codes'].'.fk_user_id', 'left');        

        if(!empty($param['searchByCodeorName'])){
            $this->db->like($this->tables['master_agent_codes'].'.agent_code', $param['searchByCodeorName']);
            //$this->db->or_like("CONCAT_WS(' ', ".$this->tables['tbl_user_profile_basics'].".f_name,".$this->tables['tbl_user_profile_basics'].".m_name,".$this->tables['tbl_user_profile_basics'].".l_name)", $param['searchByCodeorName']);
            $this->db->or_like("CONCAT_WS(' ', ".$this->tables['tbl_user_profile_basics'].".f_name,".$this->tables['tbl_user_profile_basics'].".l_name)", $param['searchByCodeorName']);
        }

        if(!empty($param['order_by']) && !empty($param['order'])){
            if($param['order_by'] == 'f_name'){
                $table = $this->tables['tbl_user_profile_basics'];
            } else{
                $table = $this->tables['master_agent_codes'];                
            }
            $this->db->order_by($table.'.'.$param['order_by'], $param['order']);
        } 

        if(!empty($param['page']) && !empty($param['page_size'])){
            $limit = $param['page_size'];
            $offset = $limit*($param['page']-1);
            $this->db->limit($limit, $offset);
        }

        $result = $this->db->get($this->tables['master_agent_codes'])->result_array();
        foreach ($result as $key => $value) {

            $profile_picture_file_url = ($value['profile_picture_file_extension'] != null) ? $this->config->item('bucket_url').$value['user_id'].'/profile/'.$value['user_id'].'.'.$value['profile_picture_file_extension'].'?versionId='.$value['s3_media_version'] : "";
            $value['profile_picture_file_url'] = $profile_picture_file_url;
            $value['added_on_timestamp'] = date("Y-m-d",strtotime($value['added_on_timestamp']));

            $result_arr[] = $value;
        }
        //echo $this->db->last_query();
        return $result_arr;
    }


    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : getAllAgentCodeCount()
    * @ Added Date               : 20-05-2016
    * @ Added By                 : Subhankar    
    * -----------------------------------------------------------------
    * @ Description              : get all Agent code count
    * -----------------------------------------------------------------
    * @ param                    : Array(params)    
    * @ return                   : Array
    * -----------------------------------------------------------------
    * 
    */
    public function getAllAgentCodeCount($param = array()){
        $this->db->select($this->tables['master_agent_codes'].'.id');     

        $this->db->join($this->tables['tbl_user_agent_codes'], $this->tables['tbl_user_agent_codes'].'.agent_code = '.$this->tables['master_agent_codes'].'.agent_code', 'left');
        $this->db->join($this->tables['tbl_user_profile_basics'], $this->tables['tbl_user_profile_basics'].'.fk_user_id = '.$this->tables['tbl_user_agent_codes'].'.id', 'left');

        if(!empty($param['searchByCodeorName'])){
            $this->db->like($this->tables['master_agent_codes'].'.agent_code', $param['searchByCodeorName']);
            $this->db->or_like("CONCAT_WS(' ', ".$this->tables['tbl_user_profile_basics'].".f_name,".$this->tables['tbl_user_profile_basics'].".m_name,".$this->tables['tbl_user_profile_basics'].".l_name)", $param['searchByCodeorName']);
        }

        if(!empty($param['order_by']) && !empty($param['order'])){
            if($param['order_by'] == 'f_name'){
                $table = $this->tables['tbl_user_profile_basics'];
            } else{
                $table = $this->tables['master_agent_codes'];                
            }
            $this->db->order_by($table.'.'.$param['order_by'], $param['order']);
        } 

        $result = $this->db->count_all_results($this->tables['master_agent_codes']);
        //echo $this->db->last_query();
        return $result;
    }


    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : deleteAgentCode()
    * @ Added Date               : 20-05-2016
    * @ Added By                 : Subhankar    
    * -----------------------------------------------------------------
    * @ Description              : Delete Agent code
    * -----------------------------------------------------------------
    * @ param                    : Array(params)    
    * @ return                   : Array
    * -----------------------------------------------------------------
    * 
    */
    public function deleteAgentCode($param = array()){  

        $this->db->where('id', $param['id']);
        $this->db->delete($this->tables['master_agent_codes']);
        return $affected_rows_count = $this->db->affected_rows();
 
    }


    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : batchInsert()
    * @ Added Date               : 20-05-2016
    * @ Added By                 : Subhankar    
    * -----------------------------------------------------------------
    * @ Description              : batch Insert / ignore
    * -----------------------------------------------------------------
    * @ param                    : Array(params)    
    * @ return                   : Array
    * -----------------------------------------------------------------
    * 
    */
    /*public function batchInsert($param = array()){  

        return $this->db->custom_insert_batch($this->tables['master_agent_codes'], $param); 
    }*/
    public function batchInsert($xls_arr = array()){  
        $return_arr = $insert_arr = $update_arr = $delete_arr = array();
        $db_arr = $db_agntcd_arr = $update_agntcd_arr = array();
        $dataset = array();
        //$param = array('is_unavailable' => 'all');
        $db_arr = $this->getAllAgentCode();
        //pre($db_arr,1);

        foreach($db_arr as $db_key => $db_value) {
            $db_agntcd_arr[$db_key] = $db_value['agent_code'];
        }
        
        if(!empty($db_arr) && count($db_arr)>0){
          
            foreach($xls_arr as $xls_key => $xls_value) {  
                $needle = array_search($xls_value['agent_code'], $db_agntcd_arr);
                if(in_array($xls_value['agent_code'], $db_agntcd_arr)){
                    /*$xls_value['id']              = $db_arr[$needle]['id'];
                    $xls_value['status']            = 'update';

                    $update_arr[]       = $xls_value;*/                    
                    //$update_agntcd_arr[]    = $xls_value['agent_code'];                    
                } else {
                    $xls_value['status']            = 'new';
                    $insert_arr[]       = $xls_value;            
                }             
            }

            /*foreach($db_arr as $db_key => $db_value) {             
                if(!in_array($db_value['agent_code'], $update_agntcd_arr)){  
                    $db_value['status']             = 'removed'; 
                    $db_value['is_active']          = 0;                 
                    $delete_arr[]       = $db_value;              
                }                
            } */       
            //pre($insert_arr);
            //pre($update_arr);
            //pre($delete_arr,1);

            $return_arr_temp = array();
            if(!empty($insert_arr) && count($insert_arr) > 0){
                $return_arr_temp = array_merge($return_arr_temp, $insert_arr); 
            }

            /*if(!empty($update_arr) && count($update_arr) > 0){
                $return_arr_temp = array_merge($return_arr_temp, $update_arr); 
            }*/

            /*if(!empty($delete_arr) && count($delete_arr) > 0){
                $return_arr_temp = array_merge($return_arr_temp, $delete_arr); 
            }*/            
            $dataset = $return_arr_temp;   

            if(!empty($insert_arr) && count($insert_arr) > 0){
                $insert_arr = $this->removeElementKey($insert_arr,'status');
                $this->db->insert_batch($this->tables['master_agent_codes'], $insert_arr);
            }
            /*if(!empty($update_arr) && count($update_arr) > 0){
                $update_arr = $this->removeElementKey($update_arr,'status');
                $this->db->update_batch($this->tables['master_agent_codes'], $update_arr, 'id');
            }*/
            /*if(!empty($delete_arr) && count($delete_arr) > 0){
                $delete_arr = $this->removeElementKey($delete_arr,'status');
                $this->db->delete_batch($this->tables['master_agent_codes'], $delete_arr, 'id');
            }*/

        } else {

            foreach($xls_arr as $xls_key => $xls_value) {  
                $xls_value['status']            = 'new';
                $insert_arr[]       = $xls_value;            
            }
            $dataset = $insert_arr;

            $insert_arr = $this->removeElementKey($insert_arr,'status');
            $this->db->insert_batch($this->tables['master_agent_codes'], $insert_arr);
        }

        $return_arr['dataset'] = $dataset;
        $return_arr['count'] = count($dataset);

        return $return_arr;       
    }


    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : checkDuplicateAgentCode()
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
    public function checkDuplicateAgentCode($param = array()){
        $this->db->select("id");
        if($param['agent_code']){
            $this->db->where('agent_code', $param['agent_code']);
        }
        return $this->db->count_all_results($this->tables['master_agent_codes']);
    }

    
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getAgentCodeDetails()
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
    public function getAgentCodeDetails($param = array()){
        $this->db->select("*");
        if($param['agent_code_id']){
            $this->db->where('id', $param['agent_code_id']);
        }
        return $this->db->get($this->tables['master_agent_codes'])->row_array();
    }


    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : updateAgentCode()
    * @ Added Date               : 20-05-2016
    * @ Added By                 : Subhankar    
    * -----------------------------------------------------------------
    * @ Description              : Delete Agent code
    * -----------------------------------------------------------------
    * @ param                    : Array(params)    
    * @ return                   : Array
    * -----------------------------------------------------------------
    * 
    */
    public function updateAgentCode($where = array(),$param = array()){
        $this->db->where($where);
        $this->db->update($this->tables['master_agent_codes'], $param);
        $affected_rows = $this->db->affected_rows(); 
        return $affected_rows;
    }



    function removeElementKey($array, $key){
        foreach($array as $subKey => $subArray){            
            unset($array[$subKey][$key]);            
        }
        return $array;
    }
    /*****************************************
     * End of user model
    ****************************************/
}