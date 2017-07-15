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
class Transaction_model extends CI_Model
{
    public $_tbl_user_mpokket_accounts = 'tbl_user_mpokket_accounts';
    public $_tbl_mpokket_funds = 'tbl_mpokket_funds';
    public $_tbl_user_profile_basics = 'tbl_user_profile_basics';
    public $_tbl_mpokket_imports = 'tbl_mpokket_imports';
    public $_tbl_mpokket_exports = 'tbl_mpokket_exports';
    public $_tbl_cash_transfers = 'tbl_cash_transfers';
    public $_master_transaction_types = 'master_transaction_types';
    public $_tbl_user_types = 'tbl_user_types';
    public $_tbl_users = 'tbl_users';
    public $_tbl_admins='tbl_admins';
    public $_master_mpokket_accounts = 'master_mpokket_accounts';
    public $_master_banks = 'master_banks';

 





    
    function __construct()
    {
        // 
        //load the parent constructor
        parent::__construct();
        // $this->tables = $this->config->item('tables'); 
    }
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getAllTransaction()
     * @ Added Date               : 19-10-2016
     * @ Added By                 : Amit pandit
     * -----------------------------------------------------------------
     * @ Description              : get all Transaction Details
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
     */
    public function getAllTransaction($param = array())
    {
        //print_r($param);die;
        $this->db->select('
        tmf.transaction_date,
        tuma.mpokket_account_number ,
        CONCAT(tupb.f_name, '.' ,, " " ,tupb.m_name, '.',," " ,tupb.l_name ) AS name,
        tmf.transfer_amount,       
        tmf.transfer_type,
        tu.email_id,
        tu.mobile_number,
        tu.display_name,
        tut.user_mode,
        tut.is_agent,
        tupb.profile_picture_file_extension,
        tupb.s3_media_version,
        tu.id as user_id

        ');
        $this->db->from($this->_tbl_mpokket_funds.' AS tmf');
        $this->db->join($this->_tbl_user_mpokket_accounts.' AS tuma', 'tuma.id=tmf.fk_user_mpokket_account_id', 'left');
        $this->db->join($this->_tbl_user_profile_basics.' AS tupb', 'tupb.fk_user_id=tuma.fk_user_id', 'left');
        $this->db->join($this->_tbl_users.' AS tu', 'tupb.fk_user_id=tu.id', 'left');
        $this->db->join($this->_tbl_user_types.' AS tut', 'tut.fk_user_id=tu.id', 'left');

       /* $this->db->join($this->_tbl_user_profile_basics.' AS tupb', 'tupb.fk_user_id=tuma.fk_user_id', 'left');*/


        /* $this->db->join('tbl_user_profile_basics', 'tbl_user_profile_basics.fk_user_id=tbl_user_mpokket_accounts.fk_user_id', 'left');*/

        if (!empty($param['filterByTransferType'])) {
            $this->db->like('transfer_type', $param['filterByTransferType']);
        }
       

        if (!empty($param['searchByACNoName'])) {

            $where = "(tupb.f_name LIKE '%" . $param['searchByACNoName'] . "%' OR
            tupb.l_name LIKE '%" . $param['searchByACNoName'] . "%' OR
            CONCAT(tupb.f_name,' ',tupb.l_name) LIKE '%" . $param['searchByACNoName'] . "%' OR
            CONCAT(tupb.f_name,' ',tupb.m_name) LIKE '%" . $param['searchByACNoName'] . "%' OR
            CONCAT(tupb.f_name,' ',tupb.m_name,' ',tupb.l_name) LIKE '%" . $param['searchByACNoName'] . "%' OR
            tuma.mpokket_account_number LIKE '%" . $param['searchByACNoName'] . "%'
                    )";
            $this->db->where($where);
        }

        if(!empty($param['order_by']) && !empty($param['order'])){
            if($param['order_by'] == 'f_name'){
                $table = 'tupb';
            } else if($param['order_by'] == 'mpokket_account_number'){
                $table = 'tuma';
            } else{
                $table = 'tmf';                
            }
            $this->db->order_by($table.'.'.$param['order_by'], $param['order']);
        }

        if (!empty($param['page']) && !empty($param['page_size'])) {
            $limit  = $param['page_size'];
            $offset = $limit * ($param['page'] - 1);
            $this->db->limit($limit, $offset);
        }
        $result = $this->db->get()->result_array();

      // print_r($result);die;

       /* foreach ($result as $key => $value) {

            $profile_picture_file_url = ($value['profile_picture_file_extension'] != "") ? $this->config->item('bucket_url').$value['user_id'].'/profile/'.$value['user_id'].'.'.$value['profile_picture_file_extension'].'?versionId='.$value['s3_media_version'] : "";
            $value['profile_picture_file_url'] = $profile_picture_file_url;
           // $value['addition_timestamp '] = date("Y-m-d",strtotime($value['  addition_timestamp']));

            $result_arr[] = $value;
        }
       pre($result_arr,1);die;*/

        return $result;
    }


    


    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getAllTransaction_count()
     * @ Added Date               : 19-10-2016
     * @ Added By                 : Amit pandit
     * -----------------------------------------------------------------
     * @ Description              : get all Transaction Count
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
     */
    public function getAllTransaction_count($param = array())

    {
        $this->db->select('tmf.id');
        $this->db->from($this->_tbl_mpokket_funds.' AS tmf');
        $this->db->join($this->_tbl_user_mpokket_accounts.' AS tuma', 'tuma.id=tmf.fk_user_mpokket_account_id', 'left');
        $this->db->join($this->_tbl_user_profile_basics.' AS tupb', 'tupb.fk_user_id=tuma.fk_user_id', 'left');


    
        if (!empty($param['filterByTransferType'])) {
            $this->db->like('transfer_type', $param['filterByTransferType']);
        }
        if (!empty($param['searchByACNoName'])) {

            $where = "(tupb.f_name LIKE '%" . $param['searchByACNoName'] . "%' OR
            tupb.l_name LIKE '%" . $param['searchByACNoName'] . "%' OR
            CONCAT(tupb.f_name,' ',tupb.l_name) LIKE '%" . $param['searchByACNoName'] . "%' OR
            CONCAT(tupb.f_name,' ',tupb.m_name) LIKE '%" . $param['searchByACNoName'] . "%' OR
            CONCAT(tupb.f_name,' ',tupb.m_name,' ',tupb.l_name) LIKE '%" . $param['searchByACNoName'] . "%' OR
            tuma.mpokket_account_number LIKE '%" . $param['searchByACNoName'] . "%'
                    )";
            $this->db->where($where);
        }
        $result = $this->db->count_all_results();
        return $result;
    }

    public function checkUtr($param =array())
 {
    //$this->db->select('id');
    $this->db->where($param);
    $result = $this->db->count_all_results($this->_tbl_mpokket_imports);
    return $result;
   
 }

 public function getFkAccountId($param = array())
 {
    $this->db->select('id');
    $result=$this->db->where('mpokket_account_number',$param);
    $result = $this->db->get($this->_tbl_user_mpokket_accounts)->row_array();

    return $result['id'];
 }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : batchImportTransaction()
     * @ Added Date               : 19-10-2016
     * @ Added By                 : Amit pandit
     * -----------------------------------------------------------------
     * @ Description              : Import Transactions Details
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
     */
    public function batchImportTransaction($importParam = array(),$fundParam=array())
    {
        $result['import']=$this->db->insert_batch($this->_tbl_mpokket_imports, $importParam);
        $result['fund']  =$this->db->insert_batch($this->_tbl_mpokket_funds, $fundParam);
        return $result;


        //die($this->db->last_query());
    }
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getAllImportHistory()
     * @ Added Date               : 20-10-2016
     * @ Added By                 : Amit pandit
     * -----------------------------------------------------------------
     * @ Description              : get all Imported Transaction History
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
     */
    public function getAllImportHistory($param = array())
    {
        $this->db->select('
            
            tbl_mpokket_imports.id,
            tbl_mpokket_imports.import_timestamp as date,
            tbl_mpokket_imports.mpokket_account_number,
            tbl_mpokket_imports.amount,
            tbl_mpokket_imports.party_name,
            tbl_mpokket_imports.remitting_bank,
            tbl_mpokket_imports.utr_no,
            tbl_mpokket_imports.value_date,
            CONCAT(ta.f_name, '.' ," " ,ta.l_name ) AS admin_name
            ');
         $this->db->join($this->_tbl_admins.' AS ta', 'ta.id=tbl_mpokket_imports.fk_imported_by_admin_id', 'left');
        //$this->db->join()
        //$this->db->select('*');
        if (!empty($param['page']) && !empty($param['page_size'])) {
            $limit  = $param['page_size'];
            $offset = $limit * ($param['page'] - 1);
            $this->db->limit($limit, $offset);
        }
        if ($param['order_by'] && $param['order']) {
            $this->db->order_by($param['order_by'], $param['order']);
        }
        if (!empty($param['filterByAmount'])) {
            $this->db->like($this->_tbl_mpokket_imports . '.amount', $param['filterByAmount']);
        }
        if (!empty($param['filterByDate'])) {
            $this->db->like($this->_tbl_mpokket_imports . '.value_date', $param['filterByDate']);
        }
        if (!empty($param['searchByACNo'])) {
            $this->db->like($this->_tbl_mpokket_imports . '.mpokket_account_number', $param['searchByACNo']);
        }

        //pre($param,1);
        $result = $this->db->get($this->_tbl_mpokket_imports)->result_array();

        $newArr = array();
        foreach ($result as $key => $value) {
            $tempArr = array();
            $tempArr = $value;
            $tempArr['date'] = date("Y-m-d",strtotime($value['date']));
            $newArr[] = $tempArr;
        }
        return $newArr;

    }

  


    /*public function getAllImportHistory_date($param = array())
    {
        $this->db->select('value_date');
        $this->db->distinct();
        $result = $this->db->get($this->_tbl_mpokket_imports)->result_array();
        return $result;
    }*/

    public function getAllImportHistory_amount($param = array())
    {
        $this->db->select('amount');
        $this->db->distinct();

        $result = $this->db->get($this->_tbl_mpokket_imports)->result_array();
        //echo $this->db->last_query();
        return $result;
    }





    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getAllImportHistory_count()
     * @ Added Date               : 19-10-2016
     * @ Added By                 : Amit pandit
     * -----------------------------------------------------------------
     * @ Description              : Get count all imported Transaction history
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
     */
    public function getAllImportHistory_count($param = array())
    {
        $this->db->select('tbl_mpokket_imports.id');
       
        if ($param['order_by'] && $param['order']) {
            $this->db->order_by($param['order_by'], $param['order']);
        }
        if (!empty($param['filterByAmount'])) {
            $this->db->like($this->_tbl_mpokket_imports . '.amount', $param['filterByAmount']);
        }
        if (!empty($param['filterByDate'])) {
            $this->db->like($this->_tbl_mpokket_imports . '.value_date', $param['filterByDate']);
        }
        if (!empty($param['searchByACNo'])) {
            $this->db->like($this->_tbl_mpokket_imports . '.mpokket_account_number', $param['searchByACNo']);
        }

        $result = $this->db->count_all_results($this->_tbl_mpokket_imports);
        //echo $this->db->last_query();
        return $result;
    }
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getPendingExportTrasaction()
     * @ Added Date               : 20-10-2016
     * @ Added By                 : Amit pandit
     * -----------------------------------------------------------------
     * @ Description              : get all Pending Transactions for Export
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
     */
    public function getPendingExportTrasaction($param = array())
    {
        //pre($param,1);
        $this->db->select('tme.*,
                            mb.bank_branch');
         $this->db->join($this->_master_banks. ' AS mb ', 'mb.id=tme.fk_bank_id', 'left');

        $this->db->where('fk_exported_by_admin_id', null, false);
        if (!empty($param['page']) && !empty($param['page_size'])) {
            $limit  = $param['page_size'];
            $offset = $limit * ($param['page'] - 1);
            $this->db->limit($limit, $offset);
        }
        if(!empty($param['order_by']) && !empty($param['order'])) {
            $this->db->order_by($param['order_by'], $param['order']);
        }
        $result = $this->db->get($this->_tbl_mpokket_exports . ' AS tme ')->result_array();
        //echo $this->db->last_query();
        return $result;
    }
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getPendingExportCount()
     * @ Added Date               : 19-10-2016
     * @ Added By                 : Amit pandit
     * -----------------------------------------------------------------
     * @ Description              : get count all Pending transactions for Export
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
     */
    public function getPendingExportCount($param = array())
    {
        $this->db->select('id');
        $this->db->where('fk_exported_by_admin_id', null, false);
        $this->db->where('export_timestamp', null, false);
        $this->db->where('export_file_name', null, false);
        $this->db->where('daily_export_series', null, false);
        $result = $this->db->count_all_results($this->_tbl_mpokket_exports);

        //echo $result;die;
        //echo $this->db->last_query();
        return $result;
    }
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : batchUpdateExport()
     * @ Added Date               : 19-10-2016
     * @ Added By                 : Amit pandit
     * -----------------------------------------------------------------
     * @ Description              : Update all pending Exported Transactions 
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
     */
    public function batchUpdateExport($param = array(), $where_key = '')
    {
        return $this->db->update_batch($this->_tbl_mpokket_exports, $param, $where_key);
        //die($this->db->last_query());
    }
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getExportHistory()
     * @ Added Date               : 19-10-2016
     * @ Added By                 : Amit pandit
     * -----------------------------------------------------------------
     * @ Description              : get all Exported Transactions History
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
     */
    public function getExportHistory($param = array())
    {
        $this->db->select('id,
        export_request_timestamp as request_date,
        beneficiary_code as account_number,
        instrument_amount as amount,
        beneficiary_account_number ,
        transaction_type ,
        bank_name ,
        beneficiary_name,            
        export_file_name as file_name');
        $where = "fk_exported_by_admin_id is  NOT NULL
        AND export_timestamp is NOT NULL
        AND export_file_name is NOT NULL
        AND daily_export_series is NOT NULL
        ";

       /* $this->db->join($this->_tbl_admins.' AS ta', 'ta.id=tbl_mpokket_exports.fk_exported_by_admin_id', 'left');*/

        $this->db->where($where);

        if (!empty($param['page']) && !empty($param['page_size'])) {
            $limit  = $param['page_size'];
            $offset = $limit * ($param['page'] - 1);
            $this->db->limit($limit, $offset);
        }
        if ($param['order_by'] && $param['order']) {
            $this->db->order_by($param['order_by'], $param['order']);
        }
        if (!empty($param['filterByTransactionType'])) {
            $this->db->like('transaction_type', $param['filterByTransactionType']);
        }
        if (!empty($param['filterByDate'])) {
            $this->db->like($this->_tbl_mpokket_exports . '.export_request_timestamp', $param['filterByDate']);
        }                
        if (!empty($param['searchByACNoName'])) {
            $where = "(" . $this->_tbl_mpokket_exports . ".`beneficiary_name` LIKE '%" . $param['searchByACNoName'] . "%' OR
            " . $this->_tbl_mpokket_exports . ".`beneficiary_account_number` LIKE '%" . $param['searchByACNoName'] . "%' )";
            $this->db->where($where);
        }
        $result = $this->db->get($this->_tbl_mpokket_exports)->result_array();
        //echo $this->db->last_query();

        $newArr = array();
        foreach ($result as $key => $value) {
            $tempArr = array();
            $tempArr = $value;
            $tempArr['request_date'] = date("Y-m-d",strtotime($value['request_date']));
            $newArr[] = $tempArr;
        }
        return $newArr;
    }
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getExportHistory()
     * @ Added Date               : 19-10-2016
     * @ Added By                 : Amit pandit
     * -----------------------------------------------------------------
     * @ Description              : get all count Exported Transactions History
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
     */
    public function getExportHistoryCount($param = array())
    {
        $this->db->select('id');
        $where = "fk_exported_by_admin_id is  NOT NULL
        AND export_timestamp is NOT NULL
        AND export_file_name is NOT NULL
        AND daily_export_series is NOT NULL
        ";
        $this->db->where($where);

        if ($param['order_by'] && $param['order']) {
            $this->db->order_by($param['order_by'], $param['order']);
        }
        if (!empty($param['filterByTransactionType'])) {
            $this->db->like('transaction_type', $param['filterByTransactionType']);
        }
        if (!empty($param['filterByDate'])) {
            $this->db->like($this->_tbl_mpokket_exports . '.export_request_timestamp', $param['filterByDate']);
        }                
        if (!empty($param['searchByACNoName'])) {
            $where = "(" . $this->_tbl_mpokket_exports . ".`beneficiary_name` LIKE '%" . $param['searchByACNoName'] . "%' OR
            " . $this->_tbl_mpokket_exports . ".`beneficiary_account_number` LIKE '%" . $param['searchByACNoName'] . "%' )";
            $this->db->where($where);
        }
        
        $result = $this->db->count_all_results($this->_tbl_mpokket_exports);
        //echo $this->db->last_query();
        return $result;
    }

    public function getMaxExportSeries($date)
    {
        $this->db->select_max('daily_export_series');
        $start_date = $date . " " . "00:00:00";
        $end_date   = $date . " " . "23:59:59";
        $where      = "`export_timestamp` BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
        $this->db->where($where);
        $result = $this->db->get($this->_tbl_mpokket_exports)->row_array();
        //pre($result,1);
        //echo $this->db->last_query(); exit;
        return $result;
    }


    public function getSingleImportDetails($param = array())
    {
        $this->db->select('tmi.*, CONCAT(ta.f_name, '.' ," " ,ta.l_name ) AS admin_name');

        $this->db->join($this->_tbl_admins.' AS ta', 'ta.id=tmi.fk_imported_by_admin_id', 'left');

        if(!empty($param)){
            $this->db->where($param);
        }

        $result = $this->db->get($this->_tbl_mpokket_imports. ' AS tmi')->row_array();
        $result['import_timestamp'] = date("Y-m-d",strtotime($result['import_timestamp']));
        //pre($result,1);
        return $result;
    }


    public function getSingleExportDetails($param = array())
    {
        $this->db->select('tme.*, CONCAT(ta.f_name, '.' ," " ,ta.l_name ) AS admin_name');
        $this->db->join($this->_tbl_admins.' AS ta', 'ta.id=tme.fk_exported_by_admin_id', 'left');

        if(!empty($param)){
            $this->db->where($param);
        }
        $result = $this->db->get($this->_tbl_mpokket_exports. ' AS tme')->row_array();
        $result['export_request_timestamp'] = date("Y-m-d",strtotime($result['export_request_timestamp']));
        return $result;
    }
}
?>


