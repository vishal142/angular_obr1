<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//error_reporting(0);
error_reporting(E_ALL);
require_once('src/OAuth2/Autoloader.php');
require APPPATH . 'libraries/api/REST_Controller.php';
class Transactions extends REST_Controller
{
    function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: authorization, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header('Access-Control-Allow-Credentials: true');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            die();
        }
        parent::__construct();
        $this->load->config('rest');
        $this->load->config('serverconfig');
        $developer         = 'www.massoftind.com';
        $this->app_path    = "api_" . $this->config->item('test_api_ver');
        //publish app version
        $version           = str_replace('_', '.', $this->config->item('test_api_ver'));
        $this->publish     = array(
            'version' => $version,
            'developer' => $developer
        );
        //echo $_SERVER['SERVER_ADDR']; exit;
        $dsn               = 'mysql:dbname=' . $this->config->item('oauth_db_database') . ';host=' . $this->config->item('oauth_db_host');
        $dbusername        = $this->config->item('oauth_db_username');
        $dbpassword        = $this->config->item('oauth_db_password');
        $sitemode          = $this->config->item('site_mode');
        $this->path_detail = $this->config->item($sitemode);
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/user_model', 'user_model');
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/lender/lender_model', 'lender_model');
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/lender/Profile_model', 'profile');
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/lender/lender_model', 'lender');
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/lender/product_model', 'product_model');
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/notifications_model', 'notifications_model');
        $this->load->library('encrypt');
        $this->load->library('email');
        $this->load->library('calculation');
        $this->load->library('excel_reader/PHPExcel');
        
        $this->load->library('excel_reader/PHPExcel/iofactory');
        
       // $this->load->library('mpdf');
        OAuth2\Autoloader::register();
        // $dsn is the Data Source Name for your database, for exmaple "mysql:dbname=my_oauth2_db;host=localhost"
        $storage            = new OAuth2\Storage\Pdo(array(
            'dsn' => $dsn,
            'username' => $dbusername,
            'password' => $dbpassword
        ));
        // Pass a storage object or array of storage objects to the OAuth2 server class
        $this->oauth_server = new OAuth2\Server($storage);
        // Add the "Client Credentials" grant type (it is the simplest of the grant types)
        $this->oauth_server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));
        // Add the "Authorization Code" grant type (this is where the oauth magic happens)
        $this->oauth_server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));
    }
    
    public function getWalletAmount_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
            
        } else {
            
            $flag    = true;
            $req_arr = array();
            
            if ($flag) {
                $req_arr1                  = array();
                $plaintext_user_pass_key   = $this->encrypt->decode($this->post('user_pass_key', TRUE));
                $plaintext_user_id         = $this->encrypt->decode($this->post('user_id', TRUE));
                $req_arr1['user_pass_key'] = $plaintext_user_pass_key;
                $req_arr1['user_id']       = $plaintext_user_id;
                $check_session             = $this->lender->checkSessionExist($req_arr1);
                //pre($check_session,1);
                if (!empty($check_session) && count($check_session) > 0) {
                    $wallet_amount = 0;
                    $lock_amount   = 0;
                    $mpokketAcountDtl = $this->product_model->getDetailsMPokketAccount($req_arr1);
                    if (is_array($mpokketAcountDtl) && count($mpokketAcountDtl) > 0) {
                        $data['id']         = $mpokketAcountDtl['id'];
                        $data['account_no'] = $mpokketAcountDtl['mpokket_account_number'];
                        
                        $wallet_amount = $this->product_model->getwalletAmount($data);
                        $lock_amount   = $this->product_model->getLockingAmount($req_arr1);
                    }
                    $data['wallet_amount']  = $wallet_amount;
                    $data['lock_amount']    = $lock_amount;
                    $data['minimum_amount'] = '550.00';
                    $result_arr = $data;
                    $http_response          = 'http_response_ok';
                    $success_message        = '';
                    
                } else {
                    $http_response = 'http_response_invalid_login';
                    $error_message = 'Invalid user details';
                }
            } else {
                $http_response = 'http_response_bad_request';
                $error_message = ($error_message != '') ? $error_message : 'Invalid parameter';
            }
        }
        
        json_response($result_arr, $http_response, $error_message, $success_message);
    }
    
    public function allCashRequest_post()
    {
        
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
            
        } else {
            
            $flag    = true;
            $req_arr = array();
            
            
            $req_arr['search_input_principle']     = $this->post('search_input_principle', TRUE);
            $req_arr['search_input_npm']           = $this->post('search_input_npm', TRUE);
            $req_arr['search_fk_payment_type_id']  = $this->post('search_fk_payment_type_id', TRUE);
            $req_arr['search_city_name']           = $this->post('search_city_name', TRUE);
            $req_arr['search_state_name']          = $this->post('search_state_name', TRUE);
            $req_arr['search_name_of_institution'] = $this->post('search_name_of_institution', TRUE);
            
            if ($flag) {
                $req_arr1                  = array();
                $plaintext_user_pass_key  = $this->encrypt->decode($this->post('user_pass_key', TRUE));
                $plaintext_user_id        = $this->encrypt->decode($this->post('user_id', TRUE));
                $req_arr1['user_pass_key'] = $plaintext_user_pass_key;
                $req_arr1['user_id']       = $plaintext_user_id;
                $check_session            = $this->lender->checkSessionExist($req_arr1);
                //pre($check_session,1);
                if (!empty($check_session) && count($check_session) > 0) {
                    $req_arr['user_id']       = $plaintext_user_id;
                    
                    $wallet_amount = 0;
                    $lock_amount   = 0;
                    $mpokketAcountDtl = $this->product_model->getDetailsMPokketAccount($req_arr);
                    //pre($mpokketAcountDtl,1);
                    if (is_array($mpokketAcountDtl) && count($mpokketAcountDtl) > 0) {
                        $data['id']         = $mpokketAcountDtl['id'];
                        $data['account_no'] = $mpokketAcountDtl['mpokket_account_number'];
                        
                        $wallet_amount = $this->product_model->getwalletAmount($data);
                        $lock_amount   = $this->product_model->getLockingAmount($req_arr);
                    }
                    $result_arr['wallet_amount']  = $wallet_amount;

                    $row        = array();
                    $allRequest = $this->product_model->getAllRequest($req_arr);                   
                   // pre($allRequest,1);

                    if (is_array($allRequest) && count($allRequest) > 0) {
                        $i = 0;
                        foreach ($allRequest as $request) {
                            $img_display_name               = strtoupper(substr($request['display_name'], 0, 1));
                            $row[$i]['img_display_name']    = $img_display_name;
                            $row[$i]['fk_user_loan_id']     = $request['fk_user_loan_id'];
                            $row[$i]['calc_arl']            = $request['calc_arl'];
                            $row[$i]['input_principle']     = $request['input_principle'];
                            $educationDtl                   = $this->profile->getEducationUserMain($req_arr);
                            $row[$i]['name_of_institution'] = $request['name_of_institution'];
                            $row[$i]['payment_type']        = $request['payment_type'];
                            $row[$i]['city_name']           = $request['city_name'];
                            $row[$i]['state_name']           = $request['state_name'];
                            $row[$i]['fk_user_id']          = $request['fk_user_id'];
                            $row[$i]['display_name']        = $request['display_name'];
                            $row[$i]['fk_payment_type_id']  = $request['fk_payment_type_id'];
                            $row[$i]['input_npm']           = $request['input_npm'];
                            $row[$i]['pl']                  = $request['calc_ra'] - $request['calc_lfa'];
                            $row[$i]['calc_lfa']            = $request['calc_lfa'];
                            $row[$i]['loan_request_timestamp'] = $request['loan_request_timestamp'];
                            $row[$i]['irr']                 = 0;
                            
                            
                            
                            
                            $i++;
                        }
                        
                    }
                    $result_arr['dataset']      = $row;
                    $result_arr['cash_request'] = $this->product_model->getProductDisbursed();
                    $result_arr['payment_type'] = $this->product_model->getPaymentType();

                    
                    $result_arr['total_data'] = count($row);
                    
                    //pre($result_arr,1);
                    $http_response   = 'http_response_ok';
                    $success_message = '';
                    
                } else {
                    $http_response = 'http_response_invalid_login';
                    $error_message = 'Invalid user details';
                }
            } else {
                $http_response = 'http_response_bad_request';
                $error_message = ($error_message != '') ? $error_message : 'Invalid parameter';
            }
        }
        
        json_response($result_arr, $http_response, $error_message, $success_message);
    }




    public function withdrawAmount_post(){
        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();
            if (!intval($this->post('amount'))){
                $flag       = false;
                $error_message='amount can not be null';
            } else {
                $req_arr['amount']    = $this->post('amount', TRUE);
            }
            if($flag) {
                $plaintext_user_pass_key  = $this->encrypt->decode($this->post('user_pass_key', TRUE));
                $plaintext_user_id        = $this->encrypt->decode($this->post('user_id', TRUE));
                $req_arr['user_pass_key'] = $plaintext_user_pass_key;
                $req_arr['user_id']       = $plaintext_user_id;
                $check_session            = $this->lender->checkSessionExist($req_arr);
                //pre($check_session,1);
                if (!empty($check_session) && count($check_session) > 0) {
                        $bank_details=$this->profile->getBankDetails($req_arr);
                        //pre($bank_details,1);
                        if(is_array($bank_details) && count($bank_details)>0){
                                $userProfileDTl=$this->profile->fetchTempProfileMain($req_arr);
                                //pre($userProfileDTl,1);
                                $userUserDTl=$this->profile->fetchUserDeatils($req_arr['user_id']);
                                //pre($userUserDTl,1);
                                $mPokket_account=$this->product_model->getDetailsMPokketAccount($req_arr);
                                //pre( $mPokket_account,1);
                                $wallet_amount=$this->product_model->getwalletAmount($mPokket_account);
                               // pre($wallet_amount,1);
                                if($req_arr['amount']<=$wallet_amount){
                                    // add  into mpokket_export table
                                    $data['fk_user_id']=$req_arr['user_id'];
                                    $data['fk_bank_id']=$bank_details['id'];
                                    $data['transaction_type']='I';
                                    $data['beneficiary_code']=$mPokket_account['mpokket_account_number'];
                                    $data['beneficiary_account_number']=$bank_details['account_number'];
                                    $data['beneficiary_name']=$userProfileDTl['f_name'].' '.$userProfileDTl['l_name'];
                                    $data['micr_code']=$bank_details['micr_code'];
                                    $data['instrument_amount']= $req_arr['amount'] ;
                                    $data['ifsc_code']=$bank_details['ifsc_code'];
                                    $data['bank_name']=$bank_details['bank_name'];
                                    $data['bank_branch']=$bank_details['bank_branch'];
                                    $data['beneficiary_email_id']=$userUserDTl['email_id'];
                                    $data['beneficiary_address_01']=$bank_details['bank_address'];
                                    //pre($data,1);
                                    $this->product_model->addmPokketExport($data);
                                    // add into mpokket_fund table
                                    $fund['fk_user_mpokket_account_id']=$mPokket_account['id'];
                                    $fund['transfer_amount']=$req_arr['amount'];
                                    $fund['transfer_type']='W';
                                    $fund['transaction_date']           = date('Y-m-d-H-i-s');
                                   // pre($fund,1);
                                    $this->product_model->addMpokketFunds($fund);

                                    $http_response      = 'http_response_ok';
                                    $success_message    = 'successfully done';  

                            }else{
                                $http_response      = 'http_response_bad_request';
                                $error_message      = 'You Do not Have enough balance to withdraw'; 

                            }

                           

                        }else{
                            $http_response      = 'http_response_bad_request';
                            $error_message      = 'Something went wrong! please try again'; 

                        }
                      
                   
                    } else {
                        $http_response      = 'http_response_invalid_login';
                        $error_message      = 'Invalid user details'; 
                    }
                } else {
                    $http_response      = 'http_response_bad_request';
                    $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
                }
        }

        json_response($result_arr, $http_response, $error_message, $success_message);

    }



    public function giveCash_post(){
        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();


            if (!$this->post('loan_ids')){
                $flag       = false;
                $error_message='loan_id can not be null';
            } else {
                $req_arr['loan_id']    = $this->post('loan_ids', TRUE);
            }

            //pre($req_arr,1);
            if($flag) {
                $plaintext_user_pass_key  = $this->encrypt->decode($this->post('user_pass_key', TRUE));
                $plaintext_user_id        = $this->encrypt->decode($this->post('user_id', TRUE));
                $req_arr['user_pass_key'] = $plaintext_user_pass_key;
                $req_arr['user_id']       = $plaintext_user_id;
                $check_session            = $this->lender->checkSessionExist($req_arr);
                //pre($check_session,1);
                if (!empty($check_session) && count($check_session) > 0) {
                    
                    $allLoanIds=explode(',',$req_arr['loan_id']);
                    
                    if(is_array($allLoanIds) && count($allLoanIds)>0){
                        foreach($allLoanIds as $loanId){
                            
                            if($loanId>0){
                                $this->product_model->assignLoan($loanId,$req_arr['user_id']);
                                $loanDtl=$this->product_model->getUserLoanDtl($req_arr);
                                $loanDisDtl=$this->product_model->getUserLoanDisbursedDtl($req_arr);
                                $loanVarientDtl=$this->product_model->getUserLoanVarrients($req_arr);
                                 //add into notification table

                                $notification_code='TRN-CAP';
                                $notificationDtl=$this->notifications_model->getNotificationTypes($notification_code);
                                $notification_data['fk_user_id']=$loanDtl['fk_user_id'];
                                $notification_data['notification_for_mode']='B';
                                $notification_data['fk_notification_type_id']=$notificationDtl['id'];
                                $notification_data['notification_message']=' Your Cash Request for Rs'. $loanVarientDtl['calc_da'].' has been Approved';
                                //serialized data
                                $json_data_array['display_name']='';
                                $json_data_array['accepted_id']=$req_arr['user_id'];
                                $json_data_array['notification_code']=$notification_code;

                                //if($userDtl['s3_media_version']!=''){
                                   // $profile_picture_file_url = $this->config->item('bucket_url').$req_arr['user_id'].'/profile/'.$req_arr['user_id'].'.'.$userDtl['profile_picture_file_extension'].'?versionId='.$userDtl['s3_media_version'];
                                //}else{
                                    $profile_picture_file_url='';
                                //}
                                $json_data_array['img_url']=$profile_picture_file_url;
                                $json_data_serialize=json_encode($json_data_array);
                                //end of serialized data
                                $notification_data['routing_json']=$json_data_serialize;

                                $this->notifications_model->addUserNotification($notification_data);
                                //send push message
     
                            }
                        }
                    }

                   

                    $http_response      = 'http_response_ok';
                    $success_message    = 'Your cash Approval has been submitted successfully';  
                   
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Invalid user details'; 
                }
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
            }
        }

         json_response($result_arr, $http_response, $error_message, $success_message);

    }
    
    
    
    public function autoAllocate_post()
    {
        
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
            
        } else {
            
            $flag    = true;
            $req_arr = array();
            
            if ($flag) {
                $plaintext_user_pass_key  = $this->encrypt->decode($this->post('user_pass_key', TRUE));
                $plaintext_user_id        = $this->encrypt->decode($this->post('user_id', TRUE));
                $req_arr['user_pass_key'] = $plaintext_user_pass_key;
                $req_arr['user_id']       = $plaintext_user_id;
                $check_session            = $this->lender->checkSessionExist($req_arr);
                //pre($check_session,1);
                if (!empty($check_session) && count($check_session) > 0) {
                    $wallet_amount    = 0;
                    $lock_amount      = 0;
                    $mpokketAcountDtl = $this->product_model->getDetailsMPokketAccount($req_arr);
                    if (is_array($mpokketAcountDtl) && count($mpokketAcountDtl) > 0) {
                        $user_account_id['id']         = $mpokketAcountDtl['id'];
                        $user_account_id['account_no'] = $mpokketAcountDtl['mpokket_account_number'];
                        
                        $wallet_amount = $this->product_model->getwalletAmount($user_account_id);
                        $lock_amount   = $this->product_model->getLockingAmount($req_arr);
                        
                    }
                    $data['wallet_amount'] = $wallet_amount;
                    $data['lock_amount']   = $lock_amount;
                    $data['free_amount']   = $wallet_amount - $lock_amount;
                    $free_amount = $data['free_amount'];
                    if ($free_amount > 0) {
                        $allLoans = $this->product_model->getAllLoans();
                        if (is_array($allLoans) && count($allLoans) > 0) {
                            $i                  = 0;
                            $total_tem_lock_amt = 0;
                            $tmp_lock_amt       = 0;
                            $amtDtl             = array();
                            $loan_id            = array();
                            foreach ($allLoans as $loans) {
                                
                                $total_free_amount = $free_amount - $tmp_lock_amt;
                                
                                if ($total_free_amount >= $loans['calc_arl']) {
                                    
                                    if ($i == 0) {
                                        $amtDtl[$i]['amount']        = $loans['calc_arl'];
                                        $amtDtl[$i]['total_loan_no'] = 1;
                                        $loan_id[]                   = $loans['fk_user_loan_id'];
                                        $tmp_lock_amt                = $loans['calc_arl'];
                                        // $amtDtl[$i]['tmp_lock_amt']=$tmp_lock_amt;
                                        //$total_tem_lock_amt=$tmp_lock_amt;
                                        $i++;
                                        
                                    } else {
                                        
                                        $key = array_search($loans['calc_arl'], array_column($amtDtl, 'amount'));
                                        
                                        
                                        if ($key === false) {
                                            
                                            $amtDtl[$i]['amount']        = $loans['calc_arl'];
                                            $amtDtl[$i]['total_loan_no'] = 1;
                                            $loan_id[]                   = $loans['fk_user_loan_id'];
                                            $tmp_lock_amt                = $tmp_lock_amt + $loans['input_principle'];
                                            $amtDtl[$i]['tmp_lock_amt']  = $tmp_lock_amt;
                                            // $total_tem_lock_amt=$total_tem_lock_amt+$tmp_lock_amt;
                                            $i++;
                                        } else {
                                            
                                            $amtDtl[$key]['total_loan_no'] = $amtDtl[$key]['total_loan_no'] + 1;
                                            
                                            $tmp_lock_amt = $tmp_lock_amt + $amtDtl[$key]['amount'];
                                            
                                            $loan_id[] = $loans['fk_user_loan_id'];
                                            // $total_tem_lock_amt=$total_tem_lock_amt+$tmp_lock_amt;
                                            
                                        }
                                    }
                                    
                                } else {
                                    //exit;
                                }
                            }
                            $result_arr['auto_allocated']   = $amtDtl;
                            $result_arr['loans_ids']        = $loan_id;
                            $result_arr['wallet_amount']    = $data['wallet_amount'];
                            $result_arr['lock_amount']      = $data['lock_amount'];
                            $result_arr['free_amount']      = $data['free_amount'];
                            $result_arr['temp_lock_amount'] = $tmp_lock_amt;
                            $http_response                  = 'http_response_ok';
                            $success_message                = '';
                            
                        } else {
                            $http_response = 'http_response_bad_request';
                            $error_message = 'There is no loans to auto allocate';
                        }
                        
                        
                        
                    } else {
                        $http_response = 'http_response_bad_request';
                        $error_message = 'You have not enough balance to auto allocate';
                    }
                    
                    
                } else {
                    $http_response = 'http_response_invalid_login';
                    $error_message = 'Invalid user details';
                }
            } else {
                $http_response = 'http_response_bad_request';
                $error_message = ($error_message != '') ? $error_message : 'Invalid parameter';
            }
        }
        
        json_response($result_arr, $http_response, $error_message, $success_message);
        
    }



    public function giveCashAutoAllocated_post(){
        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        //pre($this->post());
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('loans')){
                $flag       = false;
                $error_message='loan_id can not be null';
            } else {
                $allLoans    = json_decode($this->post('loans', TRUE),true);

                if(is_array($allLoans) && count($allLoans)>0){
                        $data['allLoans']=$allLoans;
                }else{
                    $flag       = false;
                    $error_message='loan_id can not be null';
                }
            }
            //pre($data['allLoans'],1);

           
            if($flag) {
               $plaintext_user_pass_key  = $this->encrypt->decode($this->post('user_pass_key', TRUE));
                $plaintext_user_id        = $this->encrypt->decode($this->post('user_id', TRUE));
                $req_arr['user_pass_key'] = $plaintext_user_pass_key;
                $req_arr['user_id']       = $plaintext_user_id;
                $check_session            = $this->lender->checkSessionExist($req_arr);
                //pre($check_session,1);
                if (!empty($check_session) && count($check_session) > 0) {
                    

                   // $allLoanIds=explode(',',$req_arr['loan_id']);
                    $wallet_amount=0;
                    $lock_amount=0;
                    $mpokketAcountDtl=$this->product_model->getDetailsMPokketAccount($req_arr);
                    if(is_array($mpokketAcountDtl) && count($mpokketAcountDtl)>0){
                        $data['id']=$mpokketAcountDtl['id'];
                        $data['account_no']=$mpokketAcountDtl['mpokket_account_number'];

                        $wallet_amount=$this->product_model->getwalletAmount($data);
                        $lock_amount=$this->product_model->getLockingAmount($req_arr);

                    }

                    $recent_wallet_amount=$wallet_amount-$lock_amount;

                    //pre($recent_wallet_amount,1);

                    if(is_array($allLoans) && count($allLoans)>0){
                        foreach($allLoans as $loan){
                            $arl_amount=0;
                            $arl_amount=$loan['amount'];
                            $loan_no=0;
                            $loan_no=$loan['total_loan_no'];
                            $allArlAmt=$this->product_model->getAllARL($arl_amount);
                           
                            if($loan_no>1){
                                
                                for($i=0;$i<$loan_no;$i++){
                                    if($arl_amount <= $recent_wallet_amount){
                                        $loanId=0;
                                        $loanId=$allArlAmt[$i]['id'];
                                        $this->product_model->assignLoan($loanId,$req_arr['user_id']);
                                        //add into notification table
                                        $noti['loan_id']=$loanId;
                                        $loanDtl=$this->product_model->getUserLoanDtl($noti);
                                        $loanVarientDtl=$this->product_model->getUserLoanVarrients($noti);

                                        $notification_code='TRN-CAP';
                                        $notificationDtl=$this->notifications_model->getNotificationTypes($notification_code);
                                        $notification_data['fk_user_id']=$loanDtl['fk_user_id'];
                                        $notification_data['notification_for_mode']='B';
                                        $notification_data['fk_notification_type_id']=$notificationDtl['id'];
                                        $notification_data['notification_message']=' Your Cash Request for Rs'. $loanVarientDtl['calc_da'].' has been Approved';
                                        //serialized data
                                        $json_data_array['display_name']='';
                                        $json_data_array['accepted_id']=$req_arr['user_id'];
                                        $json_data_array['notification_code']=$notification_code;

                                        //if($userDtl['s3_media_version']!=''){
                                           // $profile_picture_file_url = $this->config->item('bucket_url').$req_arr['user_id'].'/profile/'.$req_arr['user_id'].'.'.$userDtl['profile_picture_file_extension'].'?versionId='.$userDtl['s3_media_version'];
                                        //}else{
                                            $profile_picture_file_url='';
                                        //}
                                        $json_data_array['img_url']=$profile_picture_file_url;
                                        $json_data_serialize=json_encode($json_data_array);
                                        //end of serialized data
                                        $notification_data['routing_json']=$json_data_serialize;

                                        $this->notifications_model->addUserNotification($notification_data);
                                  
                                    }

                                }
                            }else{
                               
                                if($loan_no>0){
                                  
                                    if($arl_amount <= $recent_wallet_amount){
                                        $loanId=0;
                                        
                                        $loanId=$allArlAmt[0]['id'];
                                        $this->product_model->assignLoan($loanId,$req_arr['user_id']);
                                        //add into notification table
                                        $noti['loan_id']=$loanId;
                                        $loanDtl=$this->product_model->getUserLoanDtl($noti);
                                        $loanVarientDtl=$this->product_model->getUserLoanVarrients($noti);

                                        $notification_code='TRN-CAP';
                                        $notificationDtl=$this->notifications_model->getNotificationTypes($notification_code);
                                        $notification_data['fk_user_id']=$loanDtl['fk_user_id'];
                                        $notification_data['notification_for_mode']='B';
                                        $notification_data['fk_notification_type_id']=$notificationDtl['id'];
                                        $notification_data['notification_message']=' Your Cash Request for Rs'. $loanVarientDtl['calc_da'].' has been Approved';
                                        //serialized data
                                        $json_data_array['display_name']='';
                                        $json_data_array['accepted_id']=$req_arr['user_id'];
                                        $json_data_array['notification_code']=$notification_code;

                                        //if($userDtl['s3_media_version']!=''){
                                           // $profile_picture_file_url = $this->config->item('bucket_url').$req_arr['user_id'].'/profile/'.$req_arr['user_id'].'.'.$userDtl['profile_picture_file_extension'].'?versionId='.$userDtl['s3_media_version'];
                                        //}else{
                                            $profile_picture_file_url='';
                                        //}
                                        $json_data_array['img_url']=$profile_picture_file_url;
                                        $json_data_serialize=json_encode($json_data_array);
                                        //end of serialized data
                                        $notification_data['routing_json']=$json_data_serialize;

                                        $this->notifications_model->addUserNotification($notification_data);
                                       
                                            
                                        //end push message

                                    }

                                }
                            }
                        }

                    }

                    /*if(is_array($allLoanIds) && count($allLoanIds)>0){
                        foreach($allLoanIds as $loanId){
                            
                            if($loanId>0){
                                $this->product_model->assignLoan($loanId,$req_arr['user_id']);
                            }
                        }
                    }*/

                   

                    $http_response      = 'http_response_ok';
                    $success_message    = '';  
                   
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Invalid user details'; 
                }
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
            }
        }

        json_response($result_arr, $http_response, $error_message, $success_message);

    }
    public function lenderTransacDashboard_post()
    {
        
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
            
        } else {
            
            $flag    = true;
            $req_arr = array();
            
            if ($flag) {
                $plaintext_user_pass_key  = $this->encrypt->decode($this->post('user_pass_key', TRUE));
                $plaintext_user_id        = $this->encrypt->decode($this->post('user_id', TRUE));
                $req_arr['user_pass_key'] = $plaintext_user_pass_key;
                $req_arr['user_id']       = $plaintext_user_id;
                $check_session            = $this->lender->checkSessionExist($req_arr);
                //pre($check_session,1);
                if (!empty($check_session) && count($check_session) > 0) {
                    
                    $irr_actual = $this->getXIRRvalue($req_arr['user_id']);
                    
                    $details['irr_actual']    = ($irr_actual > 0) ? number_format($irr_actual, 2) : 0;
                    $details['irr_projected'] = ($irr_actual > 0) ? number_format($irr_actual, 2) : 0;
                    $details['cash_given']    = $this->product_model->totalCashGiven($req_arr);
                    $details['cash_received'] = $this->product_model->totalCashReceived($req_arr);
                    $details['cash_pending']  = $this->product_model->totalCashPending($req_arr);
                    $details['cash_offered']  = $this->product_model->totalCashOffered($req_arr);

                    $all_cash_token = array();
                    $req_arr['search_start_date'] = '';
                    $req_arr['search_end_date']   = '';
                    $req_arr['search_tenure']     ='';
                    $req_arr['search_status']     ='';
                    $req_arr['page']           = $this->post('page', true);
                    $req_arr['page_size']      = $this->post('page_size', true);
                    $req_arr['order']          = $this->post('order', true);
                    $req_arr['order_by']       = $this->post('order_by', true);
                    $req_arr['user_type']         = "L";

                    //pre($req_arr,1);
                    $all_cash_token = $this->product_model->allCashTaken($req_arr);
                    $details['payments'] =  $all_cash_token;
 


                    $result_arr      = $details;
                    $http_response   = 'http_response_ok';
                    $success_message = 'Fetched successfully';
                    
                    
                } else {
                    $http_response = 'http_response_invalid_login';
                    $error_message = 'Invalid user details';
                }
            } else {
                $http_response = 'http_response_bad_request';
                $error_message = ($error_message != '') ? $error_message : 'Invalid parameter';
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
        
        
    }
    
    
    public function getXIRRvalue($user_id)
    {
        $req_arr['user_id']     = $user_id;
        $mpokketLenderAcountDtl = $this->product_model->getDetailsMPokketAccount($req_arr);
        
        $amount_values = array();
        $amount_dates  = array();
        if (is_array($mpokketLenderAcountDtl) && count($mpokketLenderAcountDtl)) {
            if ($mpokketLenderAcountDtl['id'] > 0) {
                $all_cash_flows = $this->product_model->allCashFlow($mpokketLenderAcountDtl);
                if (is_array($all_cash_flows) && count($all_cash_flows) > 0) {
                    $positive_value = 'no';
                    $negetive_value = 'no';
                    foreach ($all_cash_flows as $cashFlow) {
                        if ($cashFlow['transfer_type'] == 'P' || $cashFlow['transfer_type'] == 'R') {
                            if ($cashFlow['transfer_type'] == 'P') {
                                $amount_values[] = '-' . $cashFlow['transfer_amount'];
                                $negetive_value  = 'yes';
                            } else {
                                $amount_values[] = $cashFlow['transfer_amount'];
                                $positive_value  = 'yes';
                            }
                            $dts            = strtotime($cashFlow['transaction_date']);
                            $amount_dates[] = mktime(0, 0, 0, date("m", $dts), date("d", $dts) + 1, date("Y", $dts));
                        }
                        
                    }
                    
                }
            }
        }
        
        if (count($amount_values) > 2) {
            if ($positive_value == 'yes' && $negetive_value == 'yes') {
                $irr_actual = $this->calculation->XIRR($amount_values, $amount_dates, 0.1);
            } else {
                $irr_actual = 0;
            }
            
        } else {
            $irr_actual = 0;
        }
        if ($irr_actual > 0) {
            $irr = $irr_actual;
        } else {
            $irr = 0;
        }
        
        return $irr;
        
        
        
    }
    
    
    public function getAllTransacPayments_post()
    {
        
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
            
        } else {
            
            $flag    = true;
            $req_arr = array();

          /*  if (!intval($this->post('page'))) {
                $flag          = false;
                $error_message = 'page can not be null';
            } else {
                $req_arr['page'] = $this->post('page', TRUE);
            }
            
            if (!intval($this->post('page_size'))) {
                $flag          = false;
                $error_message = 'page_size can not be null';
            } else {
                $req_arr['page_size'] = $this->post('page_size', TRUE);
            }
            
            $page = $req_arr['page'];
            
            if (!empty($page)) {
                $limit = $req_arr['page_size'];
                
                if ($page > 1) {
                    $id = $page - 1;
                } else {
                    $id = '0';
                }
                $pageLimit = $limit * $id;
            }*/
            $req_arr['page']           = $this->post('page', true);
            $req_arr['page_size']      = $this->post('page_size', true);
            $req_arr['order']          = $this->post('order', true);
            $req_arr['order_by']       = $this->post('order_by', true);
            $req_arr['search_status']     = $this->post('search_status', TRUE);
            $req_arr['search_start_date'] = $this->post('search_start_date', TRUE);
            $req_arr['search_end_date']   = $this->post('search_end_date', TRUE);
            $req_arr['search_tenure']     = $this->post('search_tenure', TRUE);
            $req_arr['user_type']         = "L";
            
            //pre($req_arr,1);
            if ($flag) {
                $plaintext_user_pass_key  = $this->encrypt->decode($this->post('user_pass_key', TRUE));
                $plaintext_user_id        = $this->encrypt->decode($this->post('user_id', TRUE));
                $req_arr['user_pass_key'] = $plaintext_user_pass_key;
                $req_arr['user_id']       = $plaintext_user_id;
                $check_session            = $this->lender->checkSessionExist($req_arr);
                //pre($check_session,1);
                if (!empty($check_session) && count($check_session) > 0) {
                   /* $req_arr['page_limit'] = $pageLimit;
                    $req_arr['limit']      = $limit;
*/                    
                    $all_cash_token = array();
                    $all_cash_token = $this->product_model->allCashTaken($req_arr);
                    
                    
                    $result_arr['all_cash_token'] = $all_cash_token;
                    $result_arr['no_cash_token']  = count($all_cash_token);
                    $http_response                = 'http_response_ok';
                    $success_message              = 'details fetched successfully';
                    
                } else {
                    $http_response = 'http_response_invalid_login';
                    $error_message = 'Invalid user details';
                }
            } else {
                $http_response = 'http_response_bad_request';
                $error_message = ($error_message != '') ? $error_message : 'Invalid parameter';
            }
        }
        
        json_response($result_arr, $http_response, $error_message, $success_message);
    }

    public function getloanDetails_post(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!intval($this->post('loan_id'))){
                $flag       = false;
                $error_message='loan id can not be null';
            } else {
                $req_arr['loan_id']    = $this->post('loan_id', TRUE);
            }
         
            if($flag) {
                $plaintext_user_pass_key  = $this->encrypt->decode($this->post('user_pass_key', TRUE));
                $plaintext_user_id        = $this->encrypt->decode($this->post('user_id', TRUE));
                $req_arr['user_pass_key'] = $plaintext_user_pass_key;
                $req_arr['user_id']       = $plaintext_user_id;
                $check_session            = $this->lender->checkSessionExist($req_arr);
                //pre($check_session,1);
                if (!empty($check_session) && count($check_session) > 0) {
                   
                    $all_cash_token=array();
                    $loan_details  = $this->product_model->getLoanDetail($req_arr['loan_id']);
                    $payment_details  = $this->product_model->getLoanRepaymentSchedule($req_arr['loan_id']);
                    
                   
                        $result_arr['loan_details']         = $loan_details;
                        $result_arr['payment_details']         = $payment_details;
                       
                        $http_response      = 'http_response_ok';
                        $success_message    = '';  
                   
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Invalid user details'; 
                }
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
            }
        }

    json_response($result_arr, $http_response, $error_message, $success_message);

    }
    
    public function downloadXLSLoan_post()
    {
        
        
        $flag          = true;
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        $req_arr       = array();
        $req_arr['page']           = $this->post('page', true);
        $req_arr['page_size']      = $this->post('page_size', true);
        $req_arr['order']          = $this->post('order', true);
        $req_arr['order_by']       = $this->post('order_by', true);
        $req_arr['search_status']     = $this->post('search_status', TRUE);
        $req_arr['search_start_date'] = $this->post('search_start_date', TRUE);
        $req_arr['search_end_date']   = $this->post('search_end_date', TRUE);
        $req_arr['search_tenure']     = $this->post('search_tenure', TRUE);
        $req_arr['user_type']         = "L";
            
        if ($flag) {
            $plaintext_user_pass_key  = $this->encrypt->decode($this->post('user_pass_key', TRUE));
            $plaintext_user_id        = $this->encrypt->decode($this->post('user_id', TRUE));
            $req_arr['user_pass_key'] = $plaintext_user_pass_key;
            $req_arr['user_id']       = $plaintext_user_id;
            $check_session            = $this->lender->checkSessionExist($req_arr);
            //pre($check_session,1);
            if (!empty($check_session) && count($check_session) > 0) {
                $userDtl               = $this->user_model->fetchUserDeatils($req_arr['user_id']);
                $all_cash_token = array();
                $all_cash_token = $this->product_model->allCashTaken($req_arr);
                $row['all_cash_token'] = $all_cash_token;
                $row['no_connection']  = count($all_cash_token);
                $cell['title']         = '';
                $cell['highestCol']    = 3;
                $cell['field'][0]['H'] = array(
                    'Date',
                    'Amount',
                    'Status'
                );
                $cell['flag']          = 1;
                $index                 = 0;
                if (is_array($all_cash_token) && count($all_cash_token) > 0) {
                    $i = 0;
                    foreach ($all_cash_token as $allcashtoken) {
                        $index++;
                        if($allcashtoken['payment_status'] == "1-O"){
                            $allcashtoken['payment_status'] = "OVERDUE";
                        }
                        if($allcashtoken['payment_status'] == "2-D"){
                            $allcashtoken['payment_status'] = "DUE";
                        }
                        if($allcashtoken['payment_status'] == "3-U"){
                            $allcashtoken['payment_status'] = "UPCOMING";
                        }
                        if($allcashtoken['payment_status'] == "4-P"){
                            $allcashtoken['payment_status'] = "PAID";
                        }

                        $cell['field'][$index]['F'] = array(
                            array(
                                'W' => $allcashtoken['sch_date']
                            ),
                            array(
                                'N' => $allcashtoken['scheduled_emi']
                            ),
                            array(
                                'N' => $allcashtoken['payment_status']
                            )
                        );
                        
                    }
                    
                }
                $cell['description'] = 'Transaction';
                $file_name           = "transaction_" . date('Y-m-d-H-i-s');
                $save_path           = "/var/www/html/mPokket/apis/assets/uploads/";
                $response            = $this->createExcel($cell, $save_path, $file_name);
                
                //email send  code start
                //initialising codeigniter email
                $email_config = email_config();
                $this->email->initialize($email_config);
                // email sent to buyer 
                $admin_email      = $this->config->item('admin_email');
                $admin_email_from = $this->config->item('admin_email_from');
                $this->email->from($admin_email, $admin_email_from);
                
                
                $this->email->to($userDtl['email_id']);
                $subject = 'Transaction -XLS';
                $this->email->subject($subject);
                $message               = '';
                $email_data['message'] = $message;
                $email_body            = $this->parser->parse('email_templates/emailpdf', $email_data, true);
                
                $this->email->attach($save_path . $file_name . ".xls");
                $this->email->message($email_body);
                $this->email->send();
                // email send end 
                $http_response   = 'http_response_ok';
                $success_message = 'sent successfully';
                
            } else {
                $http_response = 'http_response_invalid_login';
                $error_message = 'Invalid user details';
            }
            
        } else {
            $http_response = 'http_response_bad_request';
            $error_message = ($error_message != '') ? $error_message : 'Invalid parameter';
        }
        
        json_response($result_arr, $http_response, $error_message, $success_message);
        
        
        //echo $html;
    }
    


    public function downloadXLSLoanDtl_post() {
        

            $flag           = true;
            $req_arr        = array();
            $result_arr     = array();
           

            if (!intval($this->post('loan_id'))){
                $flag       = false;
                $error_message='loan id can not be null';
            } else {
                $req_arr['loan_id']    = $this->post('loan_id', TRUE);
            }

          
            if($flag) {


                $plaintext_user_pass_key  = $this->encrypt->decode($this->post('user_pass_key', TRUE));
                $plaintext_user_id        = $this->encrypt->decode($this->post('user_id', TRUE));
                $req_arr['user_pass_key'] = $plaintext_user_pass_key;
                $req_arr['user_id']       = $plaintext_user_id;
                $check_session            = $this->lender->checkSessionExist($req_arr);
                //pre($check_session,1);
                if (!empty($check_session) && count($check_session) > 0) {

                    //$userDtl=$this->user_model->fetchUserDeatils($req_arr['user_id']);
                   // $loan_details  = $this->product_model->getLoanDetail($req_arr['loan_id']);
                    $payment_details  = $this->product_model->getLoanRepaymentSchedule($req_arr['loan_id']);

                    pre($payment_details,1);

                    $cell['title']='';
                    $cell['highestCol'] = 3;
                    $cell['field'][0]['H']=array('Date','Amount','Status');
                    $cell['flag']=1;
                    $index =0;
                    if(is_array($payment_details) && count($payment_details)>0){
                        $i=0;
                        foreach($payment_details as $paymentdetails){
                            $index++;
                           
                            $pStatis='';
                            if($paymentdetails['payment_status']=='1-O'){
                               $pStatis='Overdue';
                            }else if($paymentdetails['payment_status']=='2-D'){
                               $pStatis='Due';
                            }else if($paymentdetails['payment_status']=='3-U'){
                                $pStatis='Upcoming';
                            }else if($paymentdetails['payment_status']=='4-P-'){
                                $pStatis='paid';
                            }

                            $cell['field'][$index]['F']=array(
                                array('W'=>$paymentdetails['sch_date']),
                                array('N'=>$paymentdetails['borrower_emi_amount']),
                                array('N'=>$pStatis),
                            );

                        }

                    }
                   // pre($cell,1);
                    $cell['description']='Transaction Details';
                    $file_name="transaction_details_".date('Y-m-d-H-i-s');
                    $save_path="/var/www/html/mPokket/apis/assets/uploads/";
                    $response=$this->createExcel($cell,$save_path,$file_name);
                   
                    //email send  code start
                    //initialising codeigniter email
                    $email_config=email_config();
                    $this->email->initialize($email_config);
                    // email sent to buyer 
                    $admin_email= $this->config->item('admin_email');
                    $admin_email_from= $this->config->item('admin_email_from');
                    $this->email->from($admin_email, $admin_email_from);

                  
                    $this->email->to($req_arr['email_id']); 
                    $subject='Transaction Details -XLS';                                
                    $this->email->subject($subject);
                    $message='';
                    $email_data['message']= $message;  
                    $email_body= $this->parser->parse('email_templates/emailpdf', $email_data, true);
                   
                    $this->email->attach($save_path.$file_name.".xls");           
                    $this->email->send();
                    // email send end 
                    $http_response      = 'http_response_ok';
                    $error_message      = ''; 
                 }
                 else{
                    $http_response = 'http_response_invalid_login';
                     $error_message = 'Invalid user details';
                 }              
                } else {
                    $http_response      = 'http_response_bad_request';
                    $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
                }
       

             json_response($result_arr, $http_response, $error_message, $success_message);

        //echo $html;
    }














    public function downloadPDFLoan_post()
    {
        
        
        $flag       = true;
        $req_arr    = array();
        $result_arr =array();
        $req_arr    = array();

        $req_arr['page']           = $this->post('page', true);
        $req_arr['page_size']      = $this->post('page_size', true);
        $req_arr['order']          = $this->post('order', true);
        $req_arr['order_by']       = $this->post('order_by', true);
        $req_arr['search_status']     = $this->post('search_status', TRUE);
        $req_arr['search_start_date'] = $this->post('search_start_date', TRUE);
        $req_arr['search_end_date']   = $this->post('search_end_date', TRUE);
        $req_arr['search_tenure']     = $this->post('search_tenure', TRUE);
        $req_arr['user_type']         = "L";
        if ($flag) {
            
            $plaintext_user_pass_key  = $this->encrypt->decode($this->post('user_pass_key', TRUE));
            $plaintext_user_id        = $this->encrypt->decode($this->post('user_id', TRUE));
            $req_arr['user_pass_key'] = $plaintext_user_pass_key;
            $req_arr['user_id']       = $plaintext_user_id;
            $check_session            = $this->lender->checkSessionExist($req_arr);
            //pre($check_session,1);
            if (!empty($check_session) && count($check_session) > 0) {
                
                $userDtl               = $this->user_model->fetchUserDeatils($req_arr['user_id']);
                $all_cash_token        = array();
                $all_cash_token        = $this->product_model->allCashTaken($req_arr);
                
                
                $row['all_cash_token'] = $all_cash_token;
                $row['no_connection']  = count($all_cash_token);
                

               // pre($row,1);
                $html = $this->load->view('pdf/allloan', $row, true);
                //$header = $this->load->view('pdf/header', $row, true);
                
                $file_name = 'loan_details' . date('d_m_Y') . '.pdf';
                
                $this->load->library('mpdf');
                
                $mpdf                   = new mPDF('utf-8', 'A4');
                $mpdf->debug            = true;
                $mpdf->setAutoTopMargin = 'stretch';
                $mpdf->mirrorMargins    = 0; // Use different Odd/Even headers and 
                // set html header for odd pages, write html and output
                // --------------------------------------------------------------------------
                //$mpdf->SetHTMLHeader('{PAGENO}/{nb}');
                //$mpdf->SetHTMLHeader($header);
                
                
                $mpdf->SetHTMLFooter('<span style="margin-left:250px;"> Page {PAGENO} of {nb} </span>');
                /* Note: SetHTMLHeader() and SetHTMLFooter() without a side(2nd argument) 
                - sets ODD page header/footer only as default..so you can also write just
                $mpdf->SetHTMLHeader('{PAGENO}/{nb}'); */
                $mpdf->WriteHTML($html);
                //$mpdf->Output();
                
                $file_path = '/var/www/html/mPokket/apis/assets/uploads/';
                $mpdf->Output($file_path . $file_name, 'F');
                
                
                //email send  code start
                //initialising codeigniter email
                $email_config = email_config();
                $this->email->initialize($email_config);
                // email sent to buyer 
                $admin_email      = $this->config->item('admin_email');
                $admin_email_from = $this->config->item('admin_email_from');
                $this->email->from($admin_email, $admin_email_from);
                
                
                $this->email->to($userDtl['email_id']);
                $subject = 'Transaction -PDF';
                $this->email->subject($subject);
                $message               = '';
                $email_data['message'] = $message;
                $email_body            = $this->parser->parse('email_templates/emailpdf', $email_data, true);
                
                $this->email->attach($file_path . $file_name);
                $this->email->message($email_body);
                $this->email->send();
                // email send end 
                $http_response = 'http_response_ok';
                $error_message = '';
                
            } else {
                $http_response = 'http_response_invalid_login';
                $error_message = 'Invalid user details';
            }
            
        } else {
            $http_response = 'http_response_bad_request';
            $error_message = ($error_message != '') ? $error_message : 'Invalid parameter';
        }
        
        
        json_response($result_arr, $http_response, $error_message, $success_message);
        
        
        //echo $html;
    }
    
    public function createExcel($cell, $save_path, $file_name)
    {
        
        
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("A & M")->setLastModifiedBy("A & M")->setTitle("Office 2007 XLSX Report Document")->setSubject("Office 2007 XLSX Report Document")->setDescription($cell['description'])->setKeywords("office 2007 openxml php")->setCategory("Report file");
        
        if ($cell['flag'] == 1) {
            //SETTING HEADER
            $asciiStart      = 65;
            $totalVals       = $cell['highestCol'];
            $getUptoCellMerg = $asciiStart + $totalVals - 1;
            $getUptoCellMerg = chr($getUptoCellMerg);
            //exit;
            $styleArray      = array(
                'borders' => array(
                    'outline' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array(
                            'argb' => '000000'
                        )
                    )
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            );
            
            $styleArrayHeader = array(
                'borders' => array(
                    'outline' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array(
                            'argb' => '000000'
                        )
                    )
                ),
                'font' => array(
                    'bold' => true
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            );
            
            for ($i = 0; $i < $totalVals; $i++) {
                $clIndx  = $asciiStart + $i;
                $clchars = chr($clIndx);
                $objPHPExcel->getActiveSheet()->getColumnDimension($clchars)->setWidth(24);
            }
            
            //COUNT NUMBER OF TITLE HEADER FOR THE DOCUMENT TO GENERATE
            $totalTitle = count($cell['title']);
            
            //SETTING TITLE HEADER FOR EXCEL DOCUMENT
            $titleCount = 0;
            
            //SETTING SUB HEADERS
            
            $titleCount++;
            
            
            //pr($cell['field']);exit;
            //SET MAIN RECORD
            $borderCount1 = 0;
            foreach ($cell['field'] as $fldKey => $fldVal) {
                if (isset($fldVal['H'])) {
                    $cellCharToStartAscii = $asciiStart;
                    
                    foreach ($fldVal['H'] as $hkey => $hval) {
                        $cellCharToStart = chr($cellCharToStartAscii);
                        $fieldValue      = $hval;
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellCharToStart . $titleCount, $fieldValue);
                        $objPHPExcel->getActiveSheet()->getStyle($cellCharToStart . $titleCount . ':' . $cellCharToStart . $titleCount)->applyFromArray($styleArrayHeader);
                        $cellCharToStartAscii++;
                        if ($borderCount1 == 0) {
                            $borderStart1 = $cellCharToStart . $titleCount;
                        }
                        $borderCount1++;
                    }
                    $titleCount++;
                }
                if (isset($fldVal['F'])) {
                    $cellCharToStartAscii = $asciiStart;
                    foreach ($fldVal['F'] as $fkey => $fval) {
                        foreach ($fval as $fkey1 => $fval1) {
                            //pr($fval);
                            $cellCharToStart = chr($cellCharToStartAscii);
                            $fieldValue      = $fval1;
                            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellCharToStart . $titleCount, $fieldValue);
                            $objPHPExcel->getActiveSheet()->getStyle($cellCharToStart . $titleCount . ':' . $cellCharToStart . $titleCount)->applyFromArray($styleArray);
                            if ($fkey1 == 'W') {
                                $objPHPExcel->getActiveSheet()->getStyle($cellCharToStart . $titleCount . ':' . $cellCharToStart . $titleCount)->applyFromArray(array(
                                    'alignment' => array(
                                        'wrap' => false,
                                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                                    )
                                ));
                            } elseif ($fkey1 == 'C') {
                                $objPHPExcel->getActiveSheet()->getStyle($cellCharToStart . $titleCount . ':' . $cellCharToStart . $titleCount)->applyFromArray(array(
                                    'alignment' => array(
                                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                                    )
                                ));
                            }
                        }
                        $cellCharToStartAscii++;
                    }
                    $titleCount++;
                }
                $borderEnd1 = $cellCharToStart . ($titleCount - 1);
                if (isset($fldVal['FC'])) {
                    $cellCharToStartAscii = $asciiStart;
                    $cellCharToStart      = chr($cellCharToStartAscii);
                    $fieldValue           = "";
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellCharToStart . $titleCount, $fieldValue);
                    $titleCount++;
                }
            }
            //$objPHPExcel->getActiveSheet()->getStyle($borderStart1.':'.$borderEnd1)->applyFromArray(array('borders' => array('outline' => array('style' => PHPExcel_Style_Border::BORDER_THICK,'color' => array('argb' => '000000')))));
            //            echo $borderStart1.':'.$borderEnd1;
            //            exit;
            //SET FOOTER VALUES TO EXCEL
            if (isset($cell['footer'])) {
                //COUNT NUMBER OF FOOTER ROW
                $totalFooterRow = count($cell['footer']);
                foreach ($cell['footer'] as $fldKey => $fldVal) {
                    if (isset($fldVal['H'])) {
                        $cellCharToStartAscii = $asciiStart;
                        foreach ($fldVal['H'] as $hkey => $hval) {
                            $cellCharToStart = chr($cellCharToStartAscii);
                            $fieldValue      = $hval;
                            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellCharToStart . $titleCount, $fieldValue);
                            $objPHPExcel->getActiveSheet()->getStyle($cellCharToStart . $titleCount . ':' . $cellCharToStart . $titleCount)->applyFromArray($styleArrayHeader);
                            $cellCharToStartAscii++;
                        }
                        $titleCount++;
                    }
                    if (isset($fldVal['F'])) {
                        $cellCharToStartAscii = $asciiStart;
                        foreach ($fldVal['F'] as $fkey => $fval) {
                            foreach ($fval as $fkey1 => $fval1) {
                                $cellCharToStart = chr($cellCharToStartAscii);
                                $fieldValue      = $fval1;
                                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellCharToStart . $titleCount, $fieldValue);
                                $objPHPExcel->getActiveSheet()->getStyle($cellCharToStart . $titleCount . ':' . $cellCharToStart . $titleCount)->applyFromArray($styleArray);
                                if ($fkey1 == 'W') {
                                    $objPHPExcel->getActiveSheet()->getStyle($cellCharToStart . $titleCount . ':' . $cellCharToStart . $titleCount)->applyFromArray(array(
                                        'alignment' => array(
                                            'wrap' => true,
                                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                                        )
                                    ));
                                } elseif ($fkey1 == 'C') {
                                    $objPHPExcel->getActiveSheet()->getStyle($cellCharToStart . $titleCount . ':' . $cellCharToStart . $titleCount)->applyFromArray(array(
                                        'alignment' => array(
                                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                                        )
                                    ));
                                }
                            }
                            $cellCharToStartAscii++;
                        }
                    }
                    $titleCount++;
                    if (isset($fldVal['FC'])) {
                        $cellCharToStartAscii = $asciiStart;
                        $cellCharToStart      = chr($cellCharToStartAscii);
                        $fieldValue           = "";
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellCharToStart . $titleCount, $fieldValue);
                        $titleCount++;
                    }
                }
            }
            
            $titleCount++;
            //$fieldValue = html_entity_decode(COPYRIGHT);
            $fieldValue = "Copyright © " . date('Y') . " mPokket Q All rights reserved..";
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $titleCount, $fieldValue);
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $titleCount . ':D' . $titleCount);
            
            if ($totalVals <= 5) {
                $generateReportCellStart = 'A';
                $titleCount++;
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $titleCount . ':D' . $titleCount);
                $objPHPExcel->getActiveSheet()->getStyle($generateReportCellStart . $titleCount . ':' . $generateReportCellStart . $titleCount)->applyFromArray(array(
                    'font' => array(
                        'italic' => true
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                    )
                ));
            } else {
                $generateReportCell      = $asciiStart + $totalVals - 1;
                $generateReportCellStart = chr($generateReportCell - 1);
                $generateReportCellEnd   = chr($generateReportCell);
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells($generateReportCellStart . $titleCount . ':' . $generateReportCellEnd . $titleCount);
                $objPHPExcel->getActiveSheet()->getStyle($generateReportCellStart . $titleCount . ':' . $generateReportCellEnd . $titleCount)->applyFromArray(array(
                    'font' => array(
                        'italic' => true
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
                    )
                ));
            }
            
            $fieldValue = 'Report Generated On ' . date('d-m-Y g:i:s A');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($generateReportCellStart . $titleCount, $fieldValue);
            //$objPHPExcel->getActiveSheet()->getStyle('F'.$titleCount.':'.'F'.$titleCount)->applyFromArray($styleArray);
        } else {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'No Record Found');
        }
        
        
        
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Simple');
        
        
        
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        
        $objPHPExcel->setActiveSheetIndex(0);
        
        $objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
        // $report_path="";
        
        /*if($file_name=='ANM_Report_'){
        $save_path=$save_path.$file_name.date('dMy',strtotime('-2 day'));
        }
        else{
        $save_path=$save_path.$file_name.date('dMy',strtotime('-1 day'));
        }*/
        $save_path = $save_path . $file_name;
        $objWriter->save($save_path . '.xls');
        unset($objPHPExcel);
        return true;
        /*  $objWriter->save('php://output');*/
        
        
    }
    
    
    
    
    
    
    
}