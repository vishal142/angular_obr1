<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
* --------------------------------------------------------------------------
* @ Controller Name          : All the admin related api call from admin controller
* @ Added Date               : 06-04-2016
* @ Added By                 : Subhankar
* -----------------------------------------------------------------
* @ Description              : This is the admin index page
* -----------------------------------------------------------------
* @ return                   : array
* -----------------------------------------------------------------
* @ Modified Date            : 06-04-2016
* @ Modified By              : Subhankar
* 
*/

class Cron extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('api_'.$this->config->item('test_api_ver').'/admin/Cron_model');
        $this->load->library('calculation');
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : oauthAccessExpire()
     * @ Added Date               : 04-10-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : delete 1hour expire oauth token
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 04-10-2016
     * @ Modified By              : Piyalee
     * 
    */
    public function oauthAccessExpire()
    {

        //$this->writeLog("crons_log.txt", "oauthAccessExpire".date('Y-m-d H:i:s').'\n');
        $dateExp = date("Y-m-d H:i:s",strtotime('-1 hour'));
        $oauth_details = $this->Cron_model->getAllOauth($dateExp);

        $delete_ids = array();
        if(!empty($oauth_details) && count($oauth_details) > 0)
        {
            foreach ($oauth_details as $ouath_key => $ouath_value) 
            {
                $delete_ids[] = $ouath_value['access_token'];
            }
        }
        //print_r($delete_ids); exit();
        $deleteUpdate = '';
        if(!empty($delete_ids) && count($delete_ids))
        {
            $deleteUpdate = $this->Cron_model->deleteOauthTokens($delete_ids);
        }
        if($deleteUpdate)
        {
            echo "Token deleted successfully";
        }
        else
        {
            echo "No more expiry tokens";
        }
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : loanRepaymentExpire()
     * @ Added Date               : 05-10-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : delete 1hour expire oauth token
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 05-10-2016
     * @ Modified By              : Piyalee
     * 
    */
    public function loanRepaymentExpire()
    {

        $this->writeLog("crons_log.txt", "---loanRepaymentExpire".date('Y-m-d H:i:s').'\n');
        $loanRepay = $this->Cron_model->loanRepayExpire();
        $curDate = date("Y-m-d");

        $updateArr = array();
        foreach ($loanRepay as $loan_key => $loan_value) 
        {
            if($loan_value['scheduled_payment_date'] < $curDate)
            {
                $diff = date_diff(date_create($loan_value['scheduled_payment_date']), date_create($curDate));
                $diff_days = $diff->days;
                
                if($diff_days > 1 && $diff_days <= 15)
                {
                    $loan_value['days_past_due_dpd'] = $diff_days;
                    $inputs = $this->Cron_model->getInputValuesLoan($loan_value['fk_user_loan_id']);
                    $loan_details = $this->calculation->oneTimePenaltyCalc($inputs['input_principle'], $inputs['input_pfpd'], $diff_days, $inputs['input_pprm'], $inputs['calc_tst'], $inputs['calc_ra'], $inputs['calc_lfa']);

                    $loan_value['borrower_tpf']             = $loan_details['tpf'];
                    $loan_value['mpokket_prm']              = $loan_details['prm'];
                    $loan_value['mpokket_strp']             = $loan_details['strp'];
                    $loan_value['mpokket_rrp']              = $loan_details['rrp'];
                    $loan_value['borrower_emi_amount']      = $inputs['calc_ra'] + $loan_value['borrower_tpf'];
                    $loan_value['lender_repayment_amount']  = $loan_value['lender_pl'] + ($loan_value['borrower_tpf'] - $loan_value['mpokket_prm']);
                    $loan_value['payment_status'] = '1-O';
                    $updateArr[] = $loan_value;
                }
                else if($diff_days > 15 && ($loan_value['payment_status'] != '2-D'))
                {
                    $loan_value['payment_status'] = '2-D';
                    $updateArr[] = $loan_value;
                }
            }
        }
        //pre($updateArr);
        if(!empty($updateArr) && count($updateArr) > 0)
        {
            $update = $this->Cron_model->updateBatchLoanRepayment($updateArr);
            /*if($update)
            {*/
                echo "Loan Repayment updated successfully";
           /* }
            else
            {
                echo "Somthing went wrong, please try again";
            }*/
        }
        else
        {
            echo "No more records for update";
        }
    }


    public function writeLog($file, $log_data) {
        file_put_contents('/var/www/html/apis/log/'.$file, $log_data, FILE_APPEND | LOCK_EX);
    }

   
    /****************************end of cron controlller**********************/

}
