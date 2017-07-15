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
class Bank_model extends CI_Model{
    function __construct(){
        //load the parent constructor
        parent::__construct();        
        $this->tables = $this->config->item('tables'); 
    }


    public function getCity($params=array()){
        //pre($params,1);

        $this->db->select('bank_city');
        $this->db->select('bank_city');
        $this->db->like($this->tables['master_banks'].'.bank_city', $params['bank_city']);
        $result = $this->db->get($this->tables['master_banks'])->result_array();

        return $result;

    }

    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : getAllBank()
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
    public function getAllBank($param = array()){

        $this->db->select($this->tables['master_banks'].'.id, '.$this->tables['master_banks'].'.ifsc_code, '.$this->tables['master_banks'].'.bank_name, '.$this->tables['master_banks'].'.bank_branch, '.$this->tables['master_banks'].'.bank_city, '.$this->tables['master_banks'].'.bank_state');          

        if(!empty($param['searchByCity'])){            
            $this->db->like($this->tables['master_banks'].'.bank_city', $param['searchByCity']);
        }

        if(!empty($param['searchByState'])){
            $this->db->like($this->tables['master_banks'].'.bank_state', $param['searchByState']);
        }

        if(!empty($param['searchByIfscNameCity'])){
            $where = "(".$this->tables['master_banks'].".`ifsc_code` LIKE '%".$param['searchByIfscNameCity']."%' OR  ".$this->tables['master_banks'].".`bank_name` LIKE '%".$param['searchByIfscNameCity']."%' OR ".$this->tables['master_banks'].".`bank_city` LIKE '%".$param['searchByIfscNameCity']."%' )";
            $this->db->where($where);
            /*$this->db->like($this->tables['master_banks'].'.ifsc_code', $param['searchByIfscNameCity']);
            $this->db->or_like($this->tables['master_banks'].'.bank_name', $param['searchByIfscNameCity']);
            $this->db->or_like($this->tables['master_banks'].'.bank_city', $param['searchByIfscNameCity']);*/
        }

        if(!empty($param['order_by']) && !empty($param['order'])){           
            $this->db->order_by($this->tables['master_banks'].'.'.$param['order_by'], $param['order']);
        } 

        if(!empty($param['page']) && !empty($param['page_size'])){
            $limit = $param['page_size'];
            $offset = $limit*($param['page']-1);
            $this->db->limit($limit, $offset);
        }

        if(empty($param['is_unavailable']) || $param['is_unavailable'] != 'all'){ 
            $this->db->where($this->tables['master_banks'].'.is_unavailable', 0);           
        }       

        $result = $this->db->get($this->tables['master_banks'])->result_array();
        //die($this->db->last_query());
        return $result;
    }


    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : getAllBankCount()
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
    public function getAllBankCount($param = array()){
        //$this->db->select($this->tables['master_banks'].'.id, '.$this->tables['master_banks'].'.bank_city, '. $this->tables['master_banks'] .'.bank_state'); 
        $this->db->select($this->tables['master_banks'].'.id'); 

        if(!empty($param['searchByCity'])){            
            $this->db->like($this->tables['master_banks'].'.bank_city', $param['searchByCity']);
        }

        if(!empty($param['searchByState'])){
            $this->db->like($this->tables['master_banks'].'.bank_state', $param['searchByState']);
        }
        
        if(!empty($param['searchByIfscNameCity'])){
            $where = "(".$this->tables['master_banks'].".`ifsc_code` LIKE '%".$param['searchByIfscNameCity']."%' OR  ".$this->tables['master_banks'].".`bank_name` LIKE '%".$param['searchByIfscNameCity']."%' OR ".$this->tables['master_banks'].".`bank_city` LIKE '%".$param['searchByIfscNameCity']."%' )";
            $this->db->where($where);
            /*$this->db->like($this->tables['master_banks'].'.ifsc_code', $param['searchByIfscNameCity']);
            $this->db->or_like($this->tables['master_banks'].'.bank_name', $param['searchByIfscNameCity']);
            $this->db->or_like($this->tables['master_banks'].'.bank_city', $param['searchByIfscNameCity']);*/
        }
        $this->db->where($this->tables['master_banks'].'.is_unavailable', 0);

        /*if($param['order_by'] && $param['order']){           
            $this->db->order_by($this->tables['master_banks'].'.'.$param['order_by'], $param['order']);
        }*/

        $result = $this->db->count_all_results($this->tables['master_banks']);
        //$result = $this->db->get($this->tables['master_banks'])->result_array();
        //echo $this->db->last_query();
        return $result;
    }


    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : getAllRecords()
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
    public function getAllRecords($param, $value){
        //pre($value,1);
        $result = array();
        if($value != ''){
            $this->db->distinct();
            $this->db->select($this->tables['master_banks'].'.bank_'.$param);

            //$this->db->like($this->tables['master_banks'].'.bank_'.$param, $value);
            $where = "(".$this->tables['master_banks']."`.bank_".$param."` LIKE '".$value."%')";
            $this->db->where($where);
            $this->db->where($this->tables['master_banks'].'.is_unavailable', 0);
            $this->db->order_by($this->tables['master_banks'].'.bank_'.$param, 'ASC');

            $result = $this->db->get($this->tables['master_banks'])->result_array();
            //echo $this->db->last_query();die;
        }
        return $result;
    }

    /*public function getAllRecords($param)
    {
        $this->db->distinct();
        $this->db->select($param.'_name');
        $this->db->order_by($param.'_name', 'ASC');
        $result = $this->db->get('master_pincodes')->result_array();
        //echo $this->db->last_query();
        return $result;
    }*/
  
    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : getBankDetails()
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
    public function getBankDetails($param = array()){
        $this->db->select($this->tables['master_banks'].'.*'); 

        $this->db->where($this->tables['master_banks'].'.id', $param['id']);
        $this->db->where($this->tables['master_banks'].'.is_unavailable', 0);

        $result = $this->db->get($this->tables['master_banks'])->row_array();
        //echo $this->db->last_query();
        return $result;
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
    public function batchInsert($xls_arr = array()){  
        $return_arr = $insert_arr = $update_arr = $delete_arr = array();
        $db_arr = $db_ifsc_arr = $update_ifsc_arr = array();
        $dataset = array();
        $param = array('is_unavailable' => 'all');
        $db_arr = $this->getAllBank($param);

        foreach($db_arr as $db_key => $db_value) {
            $db_ifsc_arr[$db_key] = $db_value['ifsc_code'];
        }
        
        if(!empty($db_arr) && count($db_arr)>0){
          
            foreach($xls_arr as $xls_key => $xls_value) {  
                $needle = array_search($xls_value['ifsc_code'], $db_ifsc_arr);
                if(in_array($xls_value['ifsc_code'], $db_ifsc_arr)){
                    $xls_value['id'] = $db_arr[$needle]['id'];
                    $xls_value['status']            = 'update';
                    $xls_value['is_unavailable']    = 0;                 
                    $update_arr[] = $xls_value;                    
                    $update_ifsc_arr[] = $xls_value['ifsc_code'];                    
                } else {
                    $xls_value['status']            = 'new';
                    $xls_value['is_unavailable']    = 0;                 
                    $insert_arr[] = $xls_value;            
                }             
            }

            foreach($db_arr as $db_key => $db_value) {             
                if(!in_array($db_value['ifsc_code'], $update_ifsc_arr)){  
                    $db_value['status']             = 'removed'; 
                    $db_value['is_unavailable']     = 1;                 
                    $delete_arr[] = $db_value;              
                }                
            }        
            /*pre($insert_arr);
            pre($update_arr);
            pre($delete_arr,1);*/

            $return_arr_temp = array();
            if(!empty($insert_arr) && count($insert_arr) > 0){
                $return_arr_temp = array_merge($return_arr_temp, $insert_arr); 
            }

            if(!empty($update_arr) && count($update_arr) > 0){
                $return_arr_temp = array_merge($return_arr_temp, $update_arr); 
            }

            if(!empty($delete_arr) && count($delete_arr) > 0){
                $return_arr_temp = array_merge($return_arr_temp, $delete_arr); 
            }            
            $dataset = $return_arr_temp;   

            if(!empty($insert_arr) && count($insert_arr) > 0){
                $insert_arr = $this->removeElementKey($insert_arr,'status');
                $this->db->insert_batch($this->tables['master_banks'], $insert_arr);
            }
            if(!empty($update_arr) && count($update_arr) > 0){
                $update_arr = $this->removeElementKey($update_arr,'status');
                $this->db->update_batch($this->tables['master_banks'], $update_arr, 'id');
            }
            if(!empty($delete_arr) && count($delete_arr) > 0){
                $delete_arr = $this->removeElementKey($delete_arr,'status');
                $this->db->update_batch($this->tables['master_banks'], $delete_arr, 'id');
            }

        } else {

            foreach($xls_arr as $xls_key => $xls_value) {  
                $xls_value['status']            = 'new';
                $xls_value['is_unavailable']    = 0;                 
                $insert_arr[] = $xls_value;            
            }
            $dataset = $insert_arr;

            $insert_arr = $this->removeElementKey($insert_arr,'status');
            $this->db->insert_batch($this->tables['master_banks'], $insert_arr);
        }

        $return_arr['dataset'] = $dataset;
        $return_arr['count'] = count($dataset);

        return $return_arr;       
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