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
class Virtual_account_model extends CI_Model
{
    public $_master_mpokket_accounts    = 'master_mpokket_accounts';
    public $_tbl_user_mpokket_accounts  = 'tbl_user_mpokket_accounts';
    public $_tbl_user_profile_basics    = 'tbl_user_profile_basics';

    function __construct()
    {
        //load the parent constructor
        parent::__construct();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getAllAccounts()
     * @ Added Date               : 21-09-2016
     * @ Added By                 : Piyalee    
     * -----------------------------------------------------------------
     * @ Description              : get all accounts
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
    */
    public function getAllAccounts($param = array())
    {
        $this->db->select('mact.*, upb.profile_picture_file_extension, upb.s3_media_version, upb.fk_user_id, upb.f_name, upb.m_name, upb.l_name, upb.residence_city, umact.assigned_timestamp');
        $this->db->join($this->_tbl_user_mpokket_accounts." as umact","umact.mpokket_account_number = mact.mpokket_account_number","LEFT");
        $this->db->join($this->_tbl_user_profile_basics." as upb","upb.fk_user_id = umact.fk_user_id","LEFT");
        if(!empty($param['order_by']) && !empty($param['order']))
        {
            $this->db->order_by($param['order_by'],$param['order']);
        }

        if(!empty($param['searchByAccNo']))
        {
            $this->db->like('mact.mpokket_account_number',$param['searchByAccNo']);
        }

        if(!empty($param['page']) && !empty($param['page_size']))
        {
            $limit  = $param['page_size'];
            $offset = $limit*($param['page']-1);
            $this->db->limit($limit, $offset);
        }
        $result = $this->db->get($this->_master_mpokket_accounts." as mact")->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getAllAccountsCount()
     * @ Added Date               : 21-09-2016
     * @ Added By                 : Piyalee    
     * -----------------------------------------------------------------
     * @ Description              : get all accounts count
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
    */
    public function getAllAccountsCount($param = array())
    {
        $this->db->select('count(*) as count_accnt');
        $this->db->join($this->_tbl_user_mpokket_accounts." as umact","umact.mpokket_account_number = mact.mpokket_account_number","LEFT");
        $this->db->join($this->_tbl_user_profile_basics." as upb","upb.fk_user_id = umact.fk_user_id","LEFT");

        if(!empty($param['searchByAccNo']))
        {
            $this->db->like('mact.mpokket_account_number',$param['searchByAccNo']);
        }
        $result = $this->db->get($this->_master_mpokket_accounts." as mact")->row_array();
        //echo $this->db->last_query();
        return $result;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : batchInsert()
     * @ Added Date               : 22-09-2016
     * @ Added By                 : Piyalee    
     * -----------------------------------------------------------------
     * @ Description              : insert batch(imported data)
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
    */
    public function batchInsert($xls_arr = array())
    {
        $return_arr = $insert_arr = array();
        $db_arr     = $db_acc_arr = array();
        $dataset    = array();
        $db_arr     = $this->getAllAccounts();
        
        foreach($db_arr as $db_key => $db_value) 
        {
            $db_acc_arr[$db_key] = $db_value['mpokket_account_number'];
        }
        
        if(!empty($db_arr) && count($db_arr)>0)
        {
            foreach($xls_arr as $xls_key => $xls_value) 
            {
                if(!in_array($xls_value['mpokket_account_number'], $db_acc_arr))
                {
                    $xls_value['status']    = 'new';
                    $insert_arr[]           = $xls_value;            
                }             
            }

            $return_arr_temp = array();
            if(!empty($insert_arr) && count($insert_arr) > 0)
            {
                $return_arr_temp = array_merge($return_arr_temp, $insert_arr); 
            }
      
            $dataset = $return_arr_temp;
            if(!empty($insert_arr) && count($insert_arr) > 0)
            {
                $insert_arr = $this->removeElementKey($insert_arr,'status');
                $this->db->insert_batch($this->_master_mpokket_accounts, $insert_arr);
            }
        } 
        else 
        {
            foreach($xls_arr as $xls_key => $xls_value) 
            {  
                $xls_value['status']    = 'new';
                $insert_arr[]           = $xls_value;            
            }
            $dataset    = $insert_arr;
            $insert_arr = $this->removeElementKey($insert_arr,'status');
            $this->db->insert_batch($this->_master_mpokket_accounts, $insert_arr);
        }
        $return_arr['dataset'] = $dataset;
        $return_arr['count'] = count($dataset);
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

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : checkForUsageAccount()
     * @ Added Date               : 22-09-2016
     * @ Added By                 : Piyalee    
     * -----------------------------------------------------------------
     * @ Description              : check account number is in use or not
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
    */
    public function checkForUsageAccount($param = array())
    {
        $this->db->select('*');
        if($param['mpokket_account_number'])
        {
            $this->db->where('mpokket_account_number', $param['mpokket_account_number']);
        }
        $qry = $this->db->get($this->_tbl_user_mpokket_accounts);
        return $qry->result_array();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getAccountNumberDetails()
     * @ Added Date               : 22-09-2016
     * @ Added By                 : Piyalee    
     * -----------------------------------------------------------------
     * @ Description              : get account number details
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
     */
    public function getAccountNumberDetails($param = array())
    {
        $this->db->select('*');
        if($param['accountId'])
        {
            $this->db->where('id', $param['accountId']);
        }
        $qry = $this->db->get($this->_master_mpokket_accounts);
        return $qry->row_array();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : deleteMpokketAccount()
     * @ Added Date               : 22-09-2016
     * @ Added By                 : Piyalee    
     * -----------------------------------------------------------------
     * @ Description              : get account number details
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
     */
    public function deleteMpokketAccount($param = array())
    {
        $this->db->where('id', $param['accountId']);
        $this->db->delete($this->_master_mpokket_accounts);
        return $this->db->affected_rows();
    }


    /*****************************************
     * End of mpokket account model
    ****************************************/
}