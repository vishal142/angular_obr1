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
class Pincode_model extends CI_Model
{
    public $_master_pincodes    = 'master_pincodes';

    function __construct()
    {
        //load the parent constructor
        parent::__construct();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getAllPincodes()
     * @ Added Date               : 03-10-2016
     * @ Added By                 : Piyalee    
     * -----------------------------------------------------------------
     * @ Description              : get all pincodes
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
    */
    public function getAllPincodes($param = array())
    {
        $this->db->select('*');
        if(!empty($param['order_by']) && !empty($param['order']))
        {
            $this->db->order_by($param['order_by'],$param['order']);
        }

        if(!empty($param['searchByCity']))
        {            
            $this->db->like('city_name', $param['searchByCity']);
        }

        if(!empty($param['searchByState']))
        {
            $this->db->like('state_name', $param['searchByState']);
        }

        if(!empty($param['searchByPincode']))
        {
            $where = "(pin_code LIKE '%".$param['searchByPincode']."%' OR  post_office LIKE '%".$param['searchByPincode']."%')";
            $this->db->where($where);
        }

        if(!empty($param['page']) && !empty($param['page_size']))
        {
            $limit  = $param['page_size'];
            $offset = $limit*($param['page']-1);
            $this->db->limit($limit, $offset);
        }
        $result = $this->db->get($this->_master_pincodes)->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getAllPincodesCount()
     * @ Added Date               : 03-10-2016
     * @ Added By                 : Piyalee    
     * -----------------------------------------------------------------
     * @ Description              : get all pincodes count
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
    */
    public function getAllPincodesCount($param = array())
    {
        $this->db->select('count(*) as count_pin');

        if(!empty($param['searchByCity']))
        {            
            $this->db->like('city_name', $param['searchByCity']);
        }

        if(!empty($param['searchByState']))
        {
            $this->db->like('state_name', $param['searchByState']);
        }

        if(!empty($param['searchByPincode']))
        {
            $where = "(pin_code LIKE '%".$param['searchByPincode']."%' OR  post_office LIKE '%".$param['searchByPincode']."%')";
            $this->db->where($where);
        }
        $result = $this->db->get($this->_master_pincodes)->row_array();
        //echo $this->db->last_query();
        return $result;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getAllRecords()
     * @ Added Date               : 03-10-2016
     * @ Added By                 : Piyalee    
     * -----------------------------------------------------------------
     * @ Description              : get all city/state
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
    */
    public function getAllRecords($param,$value)
    {

        $result = array();
        if($value != ''){
        $this->db->distinct();
        $this->db->select($param.'_name');
        $where = "(".$param."_name` LIKE '".$value."%')";
            $this->db->where($where);
        $this->db->order_by($param.'_name', 'ASC');
        $result = $this->db->get($this->_master_pincodes)->result_array();
        //echo $this->db->last_query();
    }
        return $result;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getPincodeDetails()
     * @ Added Date               : 03-10-2016
     * @ Added By                 : Piyalee    
     * -----------------------------------------------------------------
     * @ Description              : get pincodes details
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
    */
    public function getPincodeDetails($param = array())
    {
        $this->db->select('*');
        
        if(!empty($param['pincode_id']))
        {
            $this->db->like('id',$param['pincode_id']);
        }
        $result = $this->db->get($this->_master_pincodes)->row_array();
        //echo $this->db->last_query();
        return $result;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : batchInsert()
     * @ Added Date               : 03-10-2016
     * @ Added By                 : Piyalee    
     * -----------------------------------------------------------------
     * @ Description              : insert batch(imported data)
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
    */
    public function batchInsert($imprt_arr = array())
    {
        $return_arr = $insert_arr = $update_arr = $delete_arr = array();
        $db_arr = $db_pin_arr = $update_pin_arr = array();
        $dataset = array();
        $db_arr = $this->getAllPincodes(array());

        foreach ($db_arr as $db_key => $db_value) 
        {
            $db_pin_arr[$db_key] = $db_value['pin_code'];
        }

        if(!empty($db_arr) && count($db_arr) > 0)
        {
            //Generate Update Array
            foreach ($imprt_arr as $xl_key => $xl_value)
            {
                $needle = array_search($xl_value['pin_code'], $db_pin_arr);
                if(in_array($xl_value['pin_code'], $db_pin_arr))
                {
                    $xl_value['id']     = $db_arr[$needle]['id'];
                    $xl_value['status'] = 'update';
                    $update_arr[]       = $xl_value;        
                    $update_pin_arr[]   = $xl_value['pin_code'];
                } 
                else 
                {
                    $xl_value['status'] = 'new';
                    $insert_arr[]       = $xl_value;
                }
            }

            //Generate Delete Array
            /*foreach($db_arr as $db_key => $db_value) 
            {             
                if(!in_array($db_value['pin_code'], $update_pin_arr))
                {  
                    $db_value['status']             = 'removed';
                    $delete_arr[]                   = $db_value;              
                }
            }*/

            $return_arr_temp = array();
            if(!empty($insert_arr) && count($insert_arr) > 0)
            {
                $return_arr_temp = array_merge($return_arr_temp, $insert_arr); 
            }
            if(!empty($update_arr) && count($update_arr) > 0)
            {
                $return_arr_temp = array_merge($return_arr_temp, $update_arr); 
            }
            if(!empty($delete_arr) && count($delete_arr) > 0)
            {
                $return_arr_temp = array_merge($return_arr_temp, $delete_arr); 
            }
            $dataset = $return_arr_temp;   

            if(!empty($insert_arr) && count($insert_arr) > 0)
            {
                $insert_arr = $this->removeElementKey($insert_arr,'status');
                $this->db->insert_batch($this->_master_pincodes, $insert_arr);
            }
            if(!empty($update_arr) && count($update_arr) > 0)
            {
                $update_arr = $this->removeElementKey($update_arr,'status');
                $this->db->update_batch($this->_master_pincodes, $update_arr, 'id');
            }
            /*if(!empty($delete_arr) && count($delete_arr) > 0)
            {
                $delete_arr = $this->removeElementKey($delete_arr,'status');
                $this->db->update_batch($this->_master_pincodes, $delete_arr, 'id');
            }*/
        }
        else 
        {
            foreach($imprt_arr as $xls_key => $xls_value) 
            {  
                $xls_value['status']    = 'new';
                $insert_arr[]           = $xls_value;            
            }
            $dataset = $insert_arr;

            $insert_arr = $this->removeElementKey($insert_arr,'status');
            $this->db->insert_batch($this->_master_pincodes, $insert_arr);
        }

        $return_arr['dataset']  = $dataset;
        $return_arr['count']    = count($dataset);

        return $return_arr;  
    }

    function removeElementKey($array, $key)
    {
        foreach($array as $subKey => $subArray)
        {            
            unset($array[$subKey][$key]);            
        }
        return $array;
    }


    /*****************************************
     * End of pincode model
    ****************************************/
}