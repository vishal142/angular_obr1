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
class Cron_model extends CI_Model
{
    public $_oauth_access_tokens  = 'oauth_access_tokens';
    public $_tbl_user_loan_repayment_schedules = "tbl_user_loan_repayment_schedules";
    public $_tbl_user_loan_variants = 'tbl_user_loan_variants';
    

    function __construct()
    {
        //load the parent constructor
        parent::__construct();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getAllOauth()
     * @ Added Date               : 04-10-2016
     * @ Added By                 : Piyalee    
     * -----------------------------------------------------------------
     * @ Description              : get all oauth
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
    */
    public function getAllOauth($expireDate='')
    {
        $this->db->select("*");
        if(!empty($expireDate))
        {
            $this->db->where("expires <", $expireDate);
        }
        $qry = $this->db->get($this->_oauth_access_tokens);
        return $qry->result_array();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : deleteOauthTokens()
     * @ Added Date               : 04-10-2016
     * @ Added By                 : Piyalee    
     * -----------------------------------------------------------------
     * @ Description              : remove expire oauth
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
    */
    public function deleteOauthTokens($delete_tokens=array())
    {
        $this->db->where_in('access_token', $delete_tokens);
        $this->db->delete($this->_oauth_access_tokens);
        return $this->db->affected_rows();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : loanRepayExpire()
     * @ Added Date               : 04-10-2016
     * @ Added By                 : Piyalee    
     * -----------------------------------------------------------------
     * @ Description              : get all oauth
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
    */
    public function loanRepayExpire($expireDate='')
    {
        $this->db->select("*");
        $this->db->where("payment_status !=", "4-P");
        $qry = $this->db->get($this->_tbl_user_loan_repayment_schedules);
        return $qry->result_array();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : updateBatchLoanRepayment()
     * @ Added Date               : 04-10-2016
     * @ Added By                 : Piyalee    
     * -----------------------------------------------------------------
     * @ Description              : get all oauth
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
    */
    public function updateBatchLoanRepayment($updateBatch=array())
    {
        return $this->db->update_batch($this->_tbl_user_loan_repayment_schedules,$updateBatch,'id');
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getInputValuesLoan()
     * @ Added Date               : 13-10-2016
     * @ Added By                 : Piyalee    
     * -----------------------------------------------------------------
     * @ Description              : get all input loan value
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
    */
    public function getInputValuesLoan($fk_user_loan_id)
    {
        $this->db->select("input_pfpd,input_principle,input_pprm,calc_tst,calc_ra,calc_lfa");
        $this->db->where("fk_user_loan_id", $fk_user_loan_id);
        $qry = $this->db->get($this->_tbl_user_loan_variants);
        return $qry->row_array();
    }

    /*****************************************
     * End of cron model
    ****************************************/
}