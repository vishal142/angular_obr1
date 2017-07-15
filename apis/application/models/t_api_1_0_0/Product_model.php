<?php
/* * ******************************************************************
 * User model for Mobile Api 
  ---------------------------------------------------------------------
 * @ Added by                 : Mousumi Bakshi 
 * @ Framework                : CodeIgniter
 * @ Added Date               : 12-09-2016
  ---------------------------------------------------------------------
 * @ Details                  : It Cotains all the api related product
  ---------------------------------------------------------------------
 ***********************************************************************/
class Product_model extends CI_Model
{

     public $_table = 'master_products';
     public $_table_prod_variants = 'master_product_variants';
     public $_table_tier_usage_fee_discounts = 'master_tier_usage_fee_discounts';
     public $_table_user_custom_adjustment = 'tbl_user_custom_adjustments';
     public $_table_user_loan = 'tbl_user_loans';
     public $_table_user_loan_variant = 'tbl_user_loan_variants';
     public $_table_user_loan_disbursement = 'tbl_user_loan_disbursement';
     public $_table_user_loan_extras = 'tbl_user_loan_extras';
     public $_table_user_profile_basic = 'tbl_user_profile_basics';
     public $_table_user_profile_education = 'tbl_user_profile_educations';
     public $_master_pincode = 'master_pincodes';
     public $_master_payment_type = 'master_payment_types';
     public $_table_user_mpokket_account = 'tbl_user_mpokket_accounts';
     public $_table_mpokket_fund = 'tbl_mpokket_funds';
     public $_table_user_loan_repayment_sch = 'tbl_user_loan_repayment_schedules';
     public $_table_master_user_level = 'master_user_levels';
     public $_table_user_referal = 'tbl_user_referals';
     public $_table_master_mcoin_earnings = 'master_mcoin_earnings';
     public $_table_user_loan_mcoin_earning = 'tbl_user_loan_mcoin_earnings';
     public $_table_user_mcoins_earning = 'tbl_user_mcoins_earnings';
     public $_table_cash_transfer = 'tbl_cash_transfers';
     public $_table_mpokket_export = 'tbl_mpokket_exports';
   
     

     
    function __construct()
    {
       
        //load the parent constructor
        parent::__construct();        
         
    }

    public function fetchProductMcoins($params=array()){

      $this->db->where('fk_product_id',$params['fk_product_id']);
      $this->db->from($this->_table_master_mcoin_earnings);
      $ow=$this->db->get()->result_array();
      return $ow;

    }

    public function addUserLoanMcoin($params){
      $this->db->insert($this->_table_user_loan_mcoin_earning,$params);
         $insert_id = $this->db->insert_id();
        return $insert_id;
    }

     public function addUserMcoin($params){
      $this->db->insert($this->_table_user_mcoins_earning,$params);
         $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    public function fetchUserLoanMcoin($params=array()){

      $this->db->where('fk_mcoin_activity_id',$params['fk_mcoin_activity_id']);
      $this->db->where('fk_user_loan_id',$params['fk_user_loan_id']);
      $this->db->from($this->_table_user_loan_mcoin_earning);
      $ow=$this->db->get()->row_array();
      return $ow;

    }
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getProduct()
     * @ Added Date               : 01-09-2016
     * @ Added By                 : Mousumi Bakshi
     * -----------------------------------------------------------------
     * @ Description              : This function is used for total of non referred conncetion points
    */
    public function getProduct($params=array())
    {

        $this->db->where('fk_profession_type_id',$params['fk_profession_type_id']);
        $this->db->where('fk_payment_type_id',$params['fk_payment_type_id']);
        if($params['product_type']=='P' || $params['product_type']=='R'){
          $this->db->where('product_type',$params['product_type']);
        }
        $this->db->where('is_available','1');
        $this->db->from($this->_table);
        $ow=$this->db->get()->result_array();
        return $ow;
    }

    public function getProductDtl($params=array())
    {

        $this->db->where('id',$params['fk_product_id']);
        $this->db->from($this->_table);
        $ow=$this->db->get()->row_array();
        return $ow;
    }


    public function getProductDisbursed()
    {
        $this->db->select('DISTINCT(calc_da) as amount'); 
        $this->db->from($this->_table_prod_variants);
        //$this->db->where($this->_table.'.is_available2','1');
        $this->db->join($this->_table,$this->_table_prod_variants.'.fk_product_id='.$this->_table.'.id AND '.$this->_table.'.is_available=1');
        $ow=$this->db->get()->result_array();
        return $ow;
    }

    public function getProductDisbursedNPM($params=array())
    {
        $this->db->select('input_npm,id');
        $this->db->where('calc_da',$params['amount']); 
        $this->db->from($this->_table_prod_variants);
        $ow=$this->db->get()->result_array();
        return $ow;
    }
    

     public function getProductVarient($params=array())
    {
        
        $this->db->where('fk_product_id',$params['fk_product_id']);
        $this->db->from($this->_table_prod_variants);
        $ow=$this->db->get()->result_array();
        return $ow;
    }

    public function getProductVarientDtl($params=array())
    {
        
        $this->db->where('id',$params['product_varrient_id']);
        $this->db->from($this->_table_prod_variants);
        $ow=$this->db->get()->row_array();
        return $ow;
    }

    public function getTierUsageFeeDiscount($params){
        $this->db->where('fk_product_id',$params['fk_product_id']);

        if($params['mcoins_user_level']>0){
          $this->db->where('fk_user_level_id',$params['mcoins_user_level']);
        }
        $this->db->from($this->_table_tier_usage_fee_discounts);
        $row=$this->db->get()->row_array();
        return $row;
    }

     public function getTierUsageFeeDiscountDtl($params){
        $this->db->select($this->_table_tier_usage_fee_discounts.'.*,'.$this->_table_master_user_level.'.level_name');
        $this->db->where('fk_product_id',$params['fk_product_id']);
        
        if($params['mcoins_user_level']>0){
          $this->db->where('fk_user_level_id',$params['mcoins_user_level']);
        }
        $this->db->join($this->_table_master_user_level,$this->_table_tier_usage_fee_discounts.'.fk_user_level_id='.$this->_table_master_user_level.'.id');
        $this->db->from($this->_table_tier_usage_fee_discounts);
        $this->db->order_by($this->_table_tier_usage_fee_discounts.'.fk_user_level_id');
        $row=$this->db->get()->result_array();
        return $row;
    }

    public function getCustomAdjustment($params){
        $this->db->where('fk_user_id',$params['user_id']);
        $this->db->from($this->_table_user_custom_adjustment);
        $row=$this->db->get()->row_array();
        return $row;

    }

     public function addUserLoans($params){
        $this->db->insert($this->_table_user_loan,$params);
         $insert_id = $this->db->insert_id();
        return $insert_id;

    }

     public function addUserLoansVarient($params){
        $this->db->insert($this->_table_user_loan_variant,$params);
        $insert_id = $this->db->insert_id();
        return $insert_id;

    }

    public function addUserLoansDisburesment($params){
        $this->db->insert($this->_table_user_loan_disbursement,$params);
        $insert_id = $this->db->insert_id();
        return $insert_id;

    }

    public function addUserLoansExtras($params){
        $this->db->insert($this->_table_user_loan_extras,$params);
        $insert_id = $this->db->insert_id();
        return $insert_id;

    }

    public function addUserLoanRepayment($params){
      $this->db->insert($this->_table_user_loan_repayment_sch,$params);
      $insert_id = $this->db->insert_id();
      return $insert_id;

    }

    public function addMpokketFunds($params){
      $this->db->insert($this->_table_mpokket_fund,$params);
      $insert_id = $this->db->insert_id();
      return $insert_id;

    }


    public function updateUserLoans($params,$id){
      $this->db->where('id',$id);
      $this->db->update($this->_table_user_loan,$params);
     

    }

    public function getAllRequest($params){
       
        $this->db->select($this->_table_user_loan.'.fk_user_id,'.$this->_table_user_loan.'.fk_payment_type_id,'.$this->_table_user_loan_variant.'.*,'.$this->_table_user_profile_basic.'.display_name,'.$this->_table_user_profile_education.'.name_of_institution,'.$this->_table_user_profile_education.'.id as education_id,'.$this->_master_pincode.'.city_name,'.$this->_master_pincode.'.state_name,'.$this->_master_payment_type.'.payment_type');

        $this->db->where('loan_offered_by_user_id',NULL);

        if($params['search_input_principle']!=''){
          $this->db->where('calc_arl',$params['search_input_principle']);
        }

        if($params['search_input_npm']!=''){
          $this->db->where('input_npm',$params['search_input_npm']);
        }

        if($params['search_fk_payment_type_id']!=''){
          $this->db->where('fk_payment_type_id',$params['search_fk_payment_type_id']);
        }

        if($params['search_city_name']!=''){
          $this->db->where('city_name',$params['search_city_name']);
        }

        if($params['search_state_name']!=''){
          $this->db->where('state_name',$params['search_state_name']);
        }

        if($params['search_name_of_institution']!=''){
          $this->db->where('name_of_institution',$params['search_name_of_institution']);
        }

        $this->db->join($this->_table_user_loan_variant,$this->_table_user_loan.'.id='.$this->_table_user_loan_variant.'.fk_user_loan_id');
       $this->db->join($this->_table_user_profile_basic,$this->_table_user_loan.'.fk_user_id='.$this->_table_user_profile_basic.'.fk_user_id');
       $this->db->join($this->_table_user_profile_education,$this->_table_user_loan.'.fk_user_id='.$this->_table_user_profile_education.'.fk_user_id AND '.$this->_table_user_profile_education.'.show_in_profile=1');
       $this->db->join($this->_master_pincode,$this->_table_user_profile_education.'.fk_pincode_id='.$this->_master_pincode.'.id');
       $this->db->join($this->_master_payment_type,$this->_table_user_loan.'.fk_payment_type_id='.$this->_master_payment_type.'.id');
      


        $allRecords=$this->db->get($this->_table_user_loan)->result_array();
        return $allRecords;
    }


    public function getAllLoans(){
      $this->db->select($this->_table_user_loan_variant.'.*');
       $this->db->where('loan_offered_by_user_id',NULL);
       $this->db->join($this->_table_user_loan_variant,$this->_table_user_loan.'.id='.$this->_table_user_loan_variant.'.fk_user_loan_id');

       $allRecords=$this->db->get($this->_table_user_loan)->result_array();

        return $allRecords;
    }

    public function getLoanDetail($loan_id){
      $this->db->select($this->_table_user_loan.".fk_user_id,loan_offered_by_user_id,unique_loan_code,DATE_FORMAT(loan_offered_timestamp,'%b %d,%Y') as approve_date,DATE_FORMAT(loan_disbursed_timestamp,'%b %d,%Y') as accepted_date,is_loan_closed,".$this->_table_user_loan_variant.".*,".$this->_table_user_loan_disbursement.".lender_lpfa,mpokket_ufa,mpokket_stufa,mpokket_rufa, borrower_tfdb,lender_arl,borrower_da");


       $this->db->where($this->_table_user_loan.'.id',$loan_id);
       $this->db->join($this->_table_user_loan_variant,$this->_table_user_loan.'.id='.$this->_table_user_loan_variant.'.fk_user_loan_id');
      $this->db->join($this->_table_user_loan_disbursement,$this->_table_user_loan.'.id='.$this->_table_user_loan_disbursement.'.fk_user_loan_id');

       $allRecords=$this->db->get($this->_table_user_loan)->row_array();

        return $allRecords;
    }

     public function getLoanRepaymentSchedule($loan_id){

      $this->db->select($this->_table_user_loan_repayment_sch.".*,DATE_FORMAT(scheduled_payment_date,'%b %d,%Y') as sch_date");
      $this->db->where('fk_user_loan_id',$loan_id);
      $this->db->from($this->_table_user_loan_repayment_sch);
      $row=$this->db->get()->row_array();
      return $row;

    }

    public function assignLoan($id,$lender_id){

      $this->db->where('id',$id);
      $this->db->from($this->_table_user_loan);
      $row=$this->db->get()->row_array();
      if($lender_id>0){
        if($row['loan_offered_by_user_id']==NULL || $row['loan_offered_by_user_id']<1){
          $this->db->where('id',$id);
          $this->db->set('loan_offered_by_user_id',$lender_id);
          $dt=date('Y-m-d H:i:s');
          $this->db->set('loan_offered_timestamp',$dt);
          $this->db->update($this->_table_user_loan);
        }
      }

    }

    public function getAllARL($amt){
      $this->db->select($this->_table_user_loan.'.*');
      $this->db->where($this->_table_user_loan_variant.'.calc_arl',$amt);
      $this->db->where('loan_offered_by_user_id',NULL);
      $this->db->where('loan_action_type',NULL);
      $this->db->order_by($this->_table_user_loan.'.id');
      $this->db->join($this->_table_user_loan_variant,$this->_table_user_loan.'.id='.$this->_table_user_loan_variant.'.fk_user_loan_id');
      $row=$this->db->get($this->_table_user_loan)->result_array();
     
      return $row;

    }

     public function totalCashGiven($param){
      $cask_given=0;
      $this->db->select_sum($this->_table_user_loan_variant.'.calc_arl');
      $this->db->where('loan_offered_by_user_id',$param['user_id']);
      $this->db->where('loan_action_type','A');
      $this->db->join($this->_table_user_loan_variant,$this->_table_user_loan.'.id='.$this->_table_user_loan_variant.'.fk_user_loan_id');
      $row=$this->db->get($this->_table_user_loan)->row_array();
      $cask_given=$row['calc_arl'];
      return $cask_given;
    }


    public function totalCashReceived($param){
      $cask_Received=0;
      $this->db->select_sum('lender_pl');
      $this->db->where('loan_offered_by_user_id',$param['user_id']);
      $this->db->where('loan_action_type','A');
      $this->db->where('payment_status','4-P');
      $this->db->join($this->_table_user_loan_repayment_sch,$this->_table_user_loan.'.id='.$this->_table_user_loan_repayment_sch.'.fk_user_loan_id');
      $row=$this->db->get($this->_table_user_loan)->row_array();
      $cask_Received=$row['lender_pl'];
      return $cask_Received;
    }

    public function totalCashPending($param){
      $cask_Received=0;
      $this->db->select_sum('lender_pl');
      $this->db->where('loan_offered_by_user_id',$param['user_id']);
      $this->db->where('loan_action_type','A');
      $this->db->where('payment_status!=','4-P');
      $this->db->where('payment_status!=',NULL);
      $this->db->join($this->_table_user_loan_repayment_sch,$this->_table_user_loan.'.id='.$this->_table_user_loan_repayment_sch.'.fk_user_loan_id');
      $row=$this->db->get($this->_table_user_loan)->row_array();
      $cask_Received=$row['lender_pl'];
      return $cask_Received;
    }

     public function totalCashOffered($param){
      $cask_offered=0;
      $this->db->select_sum($this->_table_user_loan_variant.'.calc_arl');
      $this->db->where('loan_offered_by_user_id',$param['user_id']);
      $this->db->where('loan_action_type',NULL);
      $this->db->join($this->_table_user_loan_variant,$this->_table_user_loan.'.id='.$this->_table_user_loan_variant.'.fk_user_loan_id');
      $row=$this->db->get($this->_table_user_loan)->row_array();
      $cask_offered=$row['calc_arl'];
      return $cask_offered;
    }







    public function totalCaskTaken($params){

      $cask_taken=0;
      $this->db->select_sum('transfer_amount');
      $this->db->where('transfer_type','R');
      $this->db->where('fk_user_mpokket_account_id',$params['id']);
      $this->db->from($this->_table_mpokket_fund);
      $rowf=$this->db->get()->row_array();
      $cask_taken=$rowf['transfer_amount'];

      return $cask_taken;
    }

    public function totalOverdue($param){
      $this->db->select($this->_table_user_loan_repayment_sch.".*,DATE_FORMAT(scheduled_payment_date,'%b %d,%Y') as sch_date");

      $this->db->where('payment_status','1-O');
      $this->db->where('fk_user_id',$param['user_id']);
      $this->db->where('loan_action_type','A');
      $this->db->join($this->_table_user_loan_repayment_sch,$this->_table_user_loan.'.id='.$this->_table_user_loan_repayment_sch.'.fk_user_loan_id');
      $row=$this->db->get($this->_table_user_loan)->result_array();
     
      return $row;
    }

    public function allCashTaken($param){
      $pageLimit=$param['page_limit'];
      $limit=$param['limit'];
      $this->db->select($this->_table_user_loan_repayment_sch.".*,DATE_FORMAT(scheduled_payment_date,'%b %d,%Y') as sch_date,".$this->_table_user_loan_disbursement.".borrower_da,lender_arl");
      if($param['user_type']=='B'){
        $this->db->where('fk_user_id',$param['user_id']);
      }

      if($param['user_type']=='L'){
        $this->db->where('loan_offered_by_user_id',$param['user_id']);
      }

      $this->db->where('loan_action_type','A');

      if($param['search_status']=='C'){
        $this->db->where('is_loan_closed','1');
      }

      if($param['search_status']=='O'){
        $this->db->where('is_loan_closed','0');
      }

      if($req_arr['search_start_date']!='' && $req_arr['search_end_date']!=''){
        $this->db->where("scheduled_payment_date BETWEEN '".$req_arr['search_start_date']."' AND '".$req_arr['search_end_date']."' ");
      }

      if($param['search_tenure']>0){
        $this->db->where('input_npm',$param['search_tenure']);
      }

      $this->db->order_by('payment_status','asc');
      $this->db->order_by('scheduled_payment_date','desc');
      $this->db->join($this->_table_user_loan_repayment_sch,$this->_table_user_loan.'.id='.$this->_table_user_loan_repayment_sch.'.fk_user_loan_id');
      $this->db->join($this->_table_user_loan_variant,$this->_table_user_loan.'.id='.$this->_table_user_loan_variant.'.fk_user_loan_id');
      $this->db->join($this->_table_user_loan_disbursement,$this->_table_user_loan.'.id='.$this->_table_user_loan_disbursement.'.fk_user_loan_id');
      $row=$this->db->get($this->_table_user_loan,$limit,$pageLimit)->result_array();
     
      return $row;

    }

    public function totalUpcoming($param){
      $this->db->select($this->_table_user_loan_repayment_sch.".*,DATE_FORMAT(scheduled_payment_date,'%b %d,%Y') as sch_date");
      $this->db->where('payment_status','3-U');
      $this->db->where('fk_user_id',$param['user_id']);
      $this->db->where('loan_action_type','A');
      $this->db->join($this->_table_user_loan_repayment_sch,$this->_table_user_loan.'.id='.$this->_table_user_loan_repayment_sch.'.fk_user_loan_id');
      $row=$this->db->get($this->_table_user_loan)->result_array();
      return $row;
    }

 

    public function getDetailsMPokketAccount($params){

      $this->db->where('fk_user_id',$params['user_id']);
      $this->db->from($this->_table_user_mpokket_account);
      $row=$this->db->get()->row_array();
      return $row;

    }

    public function getwalletAmount($params){
      $amount=0;
      $this->db->select_sum('transfer_amount');
      $this->db->where('transfer_type','F');
      $this->db->where('fk_user_mpokket_account_id',$params['id']);
      $this->db->from($this->_table_mpokket_fund);
      $rowf=$this->db->get()->row_array();
      $fund_amount=$rowf['transfer_amount'];

      $amount=0;
      $this->db->select_sum('transfer_amount');
      $this->db->where('transfer_type','R');
      $this->db->where('fk_user_mpokket_account_id',$params['id']);
      $this->db->from($this->_table_mpokket_fund);
      $row=$this->db->get()->row_array();
      $receipt_amount=$row['transfer_amount'];

      $this->db->select_sum('transfer_amount');
      $this->db->where('transfer_type','P');
      $this->db->where('fk_user_mpokket_account_id',$params['id']);
      $this->db->from($this->_table_mpokket_fund);
      $rowp=$this->db->get()->row_array();
      $payment_amount=$rowp['transfer_amount'];

      $this->db->select_sum('transfer_amount');
      $this->db->where('transfer_type','W');
      $this->db->where('fk_user_mpokket_account_id',$params['id']);
      $this->db->from($this->_table_mpokket_fund);
      $row_w=$this->db->get()->row_array();
      $wallet_amount=$row_w['transfer_amount'];
      $tot_credit_amount=$fund_amount+$receipt_amount;
      $amount=$tot_credit_amount-($payment_amount+$wallet_amount);


      //print_r($row);
     
      return $amount;

    }

    public function getLockingAmount($params){
      $lock_amount=0;
      $this->db->select_sum('calc_arl');
      $this->db->where('loan_offered_by_user_id',$params['user_id']);
      $this->db->where('loan_action_type',NULL);
      $this->db->from($this->_table_user_loan);
      $this->db->join($this->_table_user_loan_variant,$this->_table_user_loan.'.id='.$this->_table_user_loan_variant.'.fk_user_loan_id');
      $row=$this->db->get()->row_array();
      $lock_amount=$row['input_principle'];
      return $lock_amount;

    }

    public function getApproveLoans($params){
      $lock_amount=0;
      $this->db->select($this->_table_user_loan_variant.'.fk_user_loan_id,input_principle,input_npm,calc_da,calc_ra,calc_tfdb,'.$this->_table_user_loan_extras.'.user_specific_usage_fee_discount_amount,   tier_usage_fee_discount_amount');
      $this->db->where('fk_user_id',$params['user_id']);
      $this->db->where('loan_offered_by_user_id >',0);
      $this->db->where('loan_action_type',NULL);
      $this->db->order_by('loan_offered_timestamp');
      $this->db->from($this->_table_user_loan);
      $this->db->join($this->_table_user_loan_variant,$this->_table_user_loan.'.id='.$this->_table_user_loan_variant.'.fk_user_loan_id');
      $this->db->join($this->_table_user_loan_extras,$this->_table_user_loan.'.id='.$this->_table_user_loan_extras.'.fk_user_loan_id');
      $row=$this->db->get()->result_array();
      return $row;

    }

      public function getLoanRepayment($params=array())
    {
        
        $this->db->where('id',$params['repayment_id']);
        $this->db->where('fk_user_loan_id',$params['loan_id']);
        $this->db->from($this->_table_user_loan_repayment_sch);
        $ow=$this->db->get()->row_array();
        return $ow;
    }

    public function updateLoanRepayment($params,$id){
      $this->db->where('id',$id);
      $this->db->update($this->_table_user_loan_repayment_sch,$params);
     

    }

    public function updateLoanUser($params,$id){
      $this->db->where('id',$id);
      $this->db->update($this->_table_user_loan,$params);
     

    }

    public function getLoanRepaymentLast($params=array())
    {
        
        $this->db->where('fk_user_loan_id',$params['loan_id']);
        $this->db->order_by('id','desc');
        //$this->db->from($this->_table_user_loan_repayment_sch);
        $ow=$this->db->get($this->_table_user_loan_repayment_sch,0,1)->row_array();
        return $ow;
    }

    /// agent 

    public function allRepayments($param){
      $pageLimit=$param['page_limit'];
      $limit=$param['limit'];
      $this->db->select($this->_table_user_loan_repayment_sch.".*,DATE_FORMAT(scheduled_payment_date,'%b %d,%Y') as sch_date");
      if($param['user_type']=='B'){
        //$this->db->where('fk_user_id',$param['user_id']);
      }

      if($param['user_type']=='L'){
        //$this->db->where('loan_offered_by_user_id',$param['user_id']);
      }

      $this->db->where('loan_action_type','A');

      if($param['search_status']=='C'){
        $this->db->where('is_loan_closed','1');
      }

      if($param['search_status']=='O'){
        $this->db->where('is_loan_closed','0');
      }

      if($param['search_start_date']!='' && $param['search_end_date']!=''){
        $this->db->where("scheduled_payment_date BETWEEN '".$param['search_start_date']."' AND '".$param['search_end_date']."' ");
      }

      if($param['search_tenure']>0){
        $this->db->where('input_npm',$param['search_tenure']);
      }

      $this->db->order_by('payment_status');
      $this->db->join($this->_table_user_loan_repayment_sch,$this->_table_user_loan.'.id='.$this->_table_user_loan_repayment_sch.'.fk_user_loan_id');
      $this->db->join($this->_table_user_loan_variant,$this->_table_user_loan.'.id='.$this->_table_user_loan_variant.'.fk_user_loan_id');
      $this->db->join($this->_table_user_referal,$this->_table_user_loan.'.fk_user_id='.$this->_table_user_referal.'.fk_user_id AND
       '.$this->_table_user_referal.'.fk_refered_by_user_id='.$param['user_id']);
      $row=$this->db->get($this->_table_user_loan,$limit,$pageLimit)->result_array();
     
      return $row;

    }

    public function allAgentCashTaken($param){
      $pageLimit=$param['page_limit'];
      $limit=$param['limit'];
      $this->db->select($this->_table_user_profile_basic.'.display_name,'.$this->_table_user_loan_variant.".fk_user_loan_id,input_principle,DATE_FORMAT(loan_request_timestamp,'%b %d,%Y') as sch_date,"."");
      
      $this->db->where('loan_offered_by_user_id',NULL);


      if($param['search_start_date']!='' && $param['search_end_date']!=''){
        $this->db->where("scheduled_payment_date BETWEEN '".$param['search_start_date']."' AND '".$param['search_end_date']."' ");
      }

      if($param['search_tenure']>0){
        $this->db->where('input_npm',$param['search_tenure']);
      }

      $this->db->order_by($this->_table_user_loan.'.id');
      $this->db->join($this->_table_user_loan_variant,$this->_table_user_loan.'.id='.$this->_table_user_loan_variant.'.fk_user_loan_id');
      $this->db->join($this->_table_user_referal,$this->_table_user_loan.'.fk_user_id='.$this->_table_user_referal.'.fk_user_id AND
       '.$this->_table_user_referal.'.fk_refered_by_user_id='.$param['user_id']);
      $this->db->join($this->_table_user_profile_basic,$this->_table_user_loan.'.fk_user_id='.$this->_table_user_profile_basic.'.fk_user_id');
      $row=$this->db->get($this->_table_user_loan,$limit,$pageLimit)->result_array();
     
      return $row;

    }

    public function allUserCashTaken($user_id){
      $pageLimit=$param['page_limit'];
      $limit=$param['limit'];
      $this->db->select($this->_table_user_profile_basic.'.display_name,'.$this->_table_user_loan_variant.".fk_user_loan_id,input_principle,DATE_FORMAT(loan_request_timestamp,'%b %d,%Y') as sch_date,"."");
      
      $this->db->where('loan_offered_by_user_id',NULL);
      $this->db->where($this->_table_user_loan.'.fk_user_id',$user_id);


      $this->db->order_by($this->_table_user_loan.'.id');
      $this->db->join($this->_table_user_loan_variant,$this->_table_user_loan.'.id='.$this->_table_user_loan_variant.'.fk_user_loan_id');
      $this->db->join($this->_table_user_profile_basic,$this->_table_user_loan.'.fk_user_id='.$this->_table_user_profile_basic.'.fk_user_id');
      $row=$this->db->get($this->_table_user_loan)->result_array();
     
      return $row;

    }

    public function allUserCashMade($user_id){
      $pageLimit=$param['page_limit'];
      $limit=$param['limit'];
      $this->db->select($this->_table_user_profile_basic.'.display_name,'.$this->_table_user_loan_repayment_sch.".fk_user_loan_id,borrower_emi_amount,DATE_FORMAT(scheduled_payment_date,'%b %d,%Y') as sch_date,"."");
      
      $this->db->where('payment_status','4-P');
      $this->db->where($this->_table_user_loan.'.fk_user_id',$user_id);


      $this->db->order_by($this->_table_user_loan.'.id');
      $this->db->join($this->_table_user_loan_repayment_sch,$this->_table_user_loan.'.id='.$this->_table_user_loan_repayment_sch.'.fk_user_loan_id');
      $this->db->join($this->_table_user_profile_basic,$this->_table_user_loan.'.fk_user_id='.$this->_table_user_profile_basic.'.fk_user_id');
      $row=$this->db->get($this->_table_user_loan)->result_array();
     
      return $row;

    }

    public function allUserCashDue($user_id){
      $pageLimit=$param['page_limit'];
      $limit=$param['limit'];
      $this->db->select($this->_table_user_profile_basic.'.display_name,'.$this->_table_user_loan_repayment_sch.".fk_user_loan_id,borrower_emi_amount,DATE_FORMAT(scheduled_payment_date,'%b %d,%Y') as sch_date,"."");
      
      $this->db->where('payment_status','1-O');
      $this->db->where($this->_table_user_loan.'.fk_user_id',$user_id);


      $this->db->order_by($this->_table_user_loan.'.id');
      $this->db->join($this->_table_user_loan_repayment_sch,$this->_table_user_loan.'.id='.$this->_table_user_loan_repayment_sch.'.fk_user_loan_id');
      $this->db->join($this->_table_user_profile_basic,$this->_table_user_loan.'.fk_user_id='.$this->_table_user_profile_basic.'.fk_user_id');
      $row=$this->db->get($this->_table_user_loan)->result_array();
     
      return $row;

    }

    public function getUserLoanDtl($params=array())
    {
        
        $this->db->where('id',$params['loan_id']);
        $this->db->from($this->_table_user_loan);
        $ow=$this->db->get()->row_array();
        return $ow;
    }

     public function getUserLoanDisbursedDtl($params=array())
    {
        
        $this->db->where('fk_user_loan_id',$params['loan_id']);
        $this->db->from($this->_table_user_loan_disbursement);
        $ow=$this->db->get()->row_array();
        return $ow;
    }

    public function getUserLoanVarrients($params=array())
    {
        
        $this->db->where('fk_user_loan_id',$params['loan_id']);
        $this->db->from($this->_table_user_loan_variant);
        $ow=$this->db->get()->row_array();
        return $ow;
    }

    public function allCashFlow($params){

      $this->db->where('fk_user_mpokket_account_id',$params['id']);
      $this->db->from($this->_table_mpokket_fund);
      $rows=$this->db->get()->result_array();
      return $rows;
    }

    public function addCashTransfer($params){

      $this->db->insert($this->_table_cash_transfer,$params);
     
    }

    public function addmPokketExport($params){
      $this->db->insert($this->_table_mpokket_export,$params);
      
    }


    

}