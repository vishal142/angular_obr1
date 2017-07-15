<?php
defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting(0);
//error_reporting(E_ALL);
require_once('src/OAuth2/Autoloader.php');
require APPPATH . 'libraries/api/REST_Controller.php';


class Registration extends REST_Controller
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
        //$this->load->model('api_' . $this->config->item('test_api_ver') . '/profile_model', 'profile_model');
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/lender/registration_model', 'registration');

        $this->load->model('api_' . $this->config->item('test_api_ver') . '/lender/lender_model', 'lender_model');
        
        $this->load->library('email');
          $this->load->library('encrypt');
        $this->push_type = 'P';
        //$this->load->library('mpdf');
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

    public function userRegistration_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();

        if(!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } else {
            $req_arr = array();
            $flag    = true;
            if ($flag && empty($this->post('email', true))) {
                $flag          = false;
                $error_message = "Email Id is required";
            } else {
                $req_arr['email'] = $this->post('email', true);
            }
            if ($flag && empty($this->post('phone', true))) {
                $flag          = false;
                $error_message = "Phone number required";
            } else {
                $req_arr['phone'] = $this->post('phone', true);
            }
            if (empty($this->post('password', true))) {
                $flag          = false;
                $error_message = "password is required";
            } else {
                //$req_arr['password'] = md5($this->post('password', true));
                $req_arr['password'] = password_hash($this->post('password', true),PASSWORD_BCRYPT);
            }
            // pre($req_arr,1);
            if ($flag) {
                $insertArr                  = array();
                $insertArr['email_id']      = $req_arr['email'];
                $insertArr['mobile_number'] = $req_arr['phone'];
                $insertArr['login_pwd']     = $req_arr['password'];
                $insertArr['display_name']  = $req_arr['email'];
                //pre($insertArr,1);
                $data_error_flag            = 0;
                $is_permission              = $this->user_model->checkEmailDataCollection($data['email_id']);
                $is_email_exist             = $this->registration->check_emailid($insertArr['email_id']);
                $is_mobile_exist            = $this->registration->check_mobileno($insertArr['mobile_number']);
                /*if($is_permission>0){
                    $data_error_flag=0;
                }else{
                    $data_error_flag=1;
                    $error_message='mPokket is currently by invitation only';
                }*/
                if ($is_email_exist > 0) {
                    $data_error_flag = 1;
                    $error_message   = 'Email id is already exists';
                }
                if ($is_mobile_exist > 0) {
                    $data_error_flag = 1;
                    $error_message   = 'Mobile no is already exists';
                }
                if ($data_error_flag == 0) {
                    $todaysData = $this->user_model->checkTodayData();
                    if (is_array($todaysData) && count($todaysData)) {
                        $totTodaysData = count($todaysData);
                    } else {
                        $totTodaysData = 0;
                    }
                    $datacount                = 10000 + intval($totTodaysData) + 1;
                    $custId                   = date('ymd') . $datacount;
                    $insertArr['customer_id'] = $custId;
                    //pre($insertArr,1);
                    $user_id = $this->registration->addUser($insertArr);
                    //get user_details
                    $insertArr['username']    = $insertArr['email_id'];
                    $user_dtl                 = $this->user_model->fetchUser($insertArr);
                    //$user_id                  = $user_dtl['id'];
                    //add data into user_types
                    //pre($user_id,1);
                    $user_types['fk_user_id'] = $user_id;
                    //pre($user_types);
                    $userCode                 = strtoupper($this->getVerificationCode());
                    $user_types['user_code']  = 'REF' . $userCode;
                    $this->user_model->addUserTypes($user_types);
                    $row['fk_user_id']        = $user_id;


                    //****Email OTP *//////


                    //$verifyCode=strtoupper($this->getVerificationCode());
                    $verifyCode               = mt_rand(100000, 999999);
                    $emailVerifyCode          = 'E-' . $verifyCode;
                    $row['verification_type'] = 'E';
                    $row['verification_code'] = $emailVerifyCode;
                    //pre($row,1);
                    $this->user_model->addUserVerificationCode($row);
                    //send email
                    //initialising codeigniter email
                    /*$config['protocol']    = 'smtp';
                    $config['smtp_host']    = 'email-smtp.us-east-1.amazonaws.com';
                    $config['smtp_port']    = '25';
                    $config['smtp_crypto']='tls';
                    $config['smtp_user']    = 'AKIAJKGML4LDY7B7ZFAA';
                    $config['smtp_pass']    = 'Ag+hZ/SZq+79L53OiKRpRcxUVE+G+ulIHT0uHc5khTH1';*/
                    $config['protocol'] = 'sendmail';
                    $config['mailpath'] = '/usr/sbin/sendmail';
                    $config['charset']  = 'utf-8';
                    $config['wordwrap'] = TRUE;
                    $config['mailtype'] = 'html';
                    $this->email->initialize($config);
                    // email sent to user 
                    $admin_email      = $this->config->item('admin_email');
                    $admin_email_from = $this->config->item('admin_email_from');
                    $this->email->from($admin_email, $admin_email_from);
                    $this->email->to($insertArr['email_id']);
                    $this->email->subject('Signup Email');
                    $verify_encrypt_code             = $emailVerifyCode . '_' . $user_id;
                    $encrypted_code                  = setEncryption($verify_encrypt_code);
                    $encrp_base                      = urlencode(base64_encode($encrypted_code));
                    $email_data['verification_link'] = $encrp_base;
                    $email_data['verification_code'] = $emailVerifyCode;
                    //pre($email_data,1);
                    $email_body                      = $this->parser->parse('email_templates/signupemailverification', $email_data, true);
                    $this->email->message($email_body);
                    $this->email->send();


                    ////Send Mobile veryfication code /////
                    $verifyCode               = mt_rand(100000, 999999);
                    $mobileVerifyCode         = 'M-' . $verifyCode;
                    $row['verification_type'] = 'M';
                    $row['verification_code'] = $mobileVerifyCode;
                    //pre($row,1);
                    $this->user_model->addUserVerificationCode($row);
                    $this->sms->category             = "MOBVER";
                    $this->sms->code                 = $mobileVerifyCode;
                    $this->sms->mobile               = $insertArr['mobile_number'];
                    $response                        = $this->sms->sendSmsFinal();

                    $user_level                      = array();
                    $user_level['fk_user_id']        = $user_id;
                    $user_level['fk_mcoin_level_id'] = '1';
                    $this->user_model->addUserLevel($user_level);
                    $getUserDetails = $this->registration->fetchUser($user_id);


                    //pre($admin_user_detail,1);
                    // Set login session for admin
                    $session_data = array();
                    $session_data['fk_user_id']         = $user_id;
                    $session_data['ip_address']         = $_SERVER['REMOTE_ADDR'];
                    $session_data['browser_session_id'] = session_id();
                    $session_data['user_agent']         = $_SERVER['HTTP_USER_AGENT'];
                    //$session_data['gps_location']     = '';
                    //pre($session_data,1);                       
                    $user_pass_key = $this->lender_model->addUserLoginSession($session_data);
                    
                    $req_arr = array();
                    $req_arr['user_pass_key'] = $user_pass_key;
                    $req_arr['user_id'] = $user_id;

                    //pre($req_arr,1);

                    $user_session_arr = $this->lender_model->checkSessionExist($req_arr);

                    //pre($user_session_arr,1);
                    $encrypted_user_pass_key = $this->encrypt->encode($user_session_arr['user_pass_key']);
                    $encrypted_user_id = $this->encrypt->encode($req_arr['user_id']);
                    //$encrypted_user_pass_key = $user_session_arr['user_pass_key'];
                    //$encrypted_user_id = $req_arr['user_id'];

                    $getUserDetails['user_pass_key'] = $encrypted_user_pass_key;                     
                    $getUserDetails['user_id']  = $encrypted_user_id;
                   

                    $result_arr = $user_session_arr;

                    //pre($getUserDetails,1);

                    $result_arr      = $getUserDetails;
                    $http_response   = 'http_response_ok';
                    $success_message = 'Registration successfull';
                } else {
                    $http_response = 'http_response_bad_request';
                    //$error_message = 'Email id OR Mobile no is already exists';
                }
            } else {
                $http_response = 'http_response_bad_request';
                //$error_message = 'Invalid parameter';
            }
        }

        json_response($result_arr, $http_response, $error_message, $success_message);
    }


    public function getVerificationCode()
    {
        $microTime = microtime();
        list($a_dec, $a_sec) = explode(" ", $microTime);
        $dec_hex = dechex($a_dec * 1000000);
        $sec_hex = dechex($a_sec);
        $this->ensure_length($dec_hex, 2);
        $this->ensure_length($sec_hex, 2);
        $guid = "";
        $guid .= $dec_hex;
        $guid .= $this->create_guid_section(2);
        $guid .= $sec_hex;
        $guid .= $this->create_guid_section(2);
        return $guid;
    }
    public function ensure_length(&$string, $length)
    {
        $strlen = strlen($string);
        if ($strlen < $length) {
            $string = str_pad($string, $length, "0");
        } else if ($strlen > $length) {
            $string = substr($string, 0, $length);
        }
    }
    public function create_guid_section($characters)
    {
        $return = "";
        for ($i = 0; $i < $characters; $i++) {
            $return .= dechex(mt_rand(0, 15));
        }
        return $return;
    }



    public function verifyOtp_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } else {
            $req_arr = array();
            $flag    = true;
            /*if (empty($this->post('user_id', true))) {
                $flag          = false;
                $error_message = "userId is required";
            } else {
                $req_arr['fk_user_id'] = $this->post('user_id', true);
            }*/
            if ($flag && empty($this->post('verify_type', true))) {
                $flag          = false;
                $error_message = "verification type is required";
            } else {
                $req_arr['verification_type'] = $this->post('verify_type', true);
            }
            if ($flag && empty($this->post('code', true))) {
                $flag          = false;
                $error_message = "verification code required";
            } else {
                $req_arr['code'] = $this->post('code', true);
            }
            //pre($req_arr,1);
            if ($flag) {

                $req_arr1 = array();
                $plaintext_user_pass_key = $this->encrypt->decode($this->post('user_pass_key', TRUE));
                $plaintext_user_id = $this->encrypt->decode($this->post('user_id', TRUE));            
                //$plaintext_user_pass_key = $this->post('user_pass_key', TRUE);
                //$plaintext_user_id = $this->post('user_id', TRUE);            

                $req_arr1['user_pass_key']   = $plaintext_user_pass_key;
                $req_arr1['user_id']         = $plaintext_user_id;
                //pre($req_arr,1);
                $check_session  = $this->lender_model->checkSessionExist($req_arr1);
 
                if(!empty($check_session) && count($check_session) > 0){
                    $req_arr['fk_user_id'] = $req_arr1['user_id'];
                    $req_arr['verification_code'] = ($req_arr['verification_type'] == 'E') ? "E-" . $req_arr['code'] : "M-" . $req_arr['code'];
                    $verifyCode                   = $this->registration->getVerificationCode($req_arr);
                    if ($verifyCode) {

                        if(($req_arr['verification_type'] == 'E')){
                        $isEmailVerify = $this->registration->verifyEmail($req_arr['fk_user_id']);
                        }
                        if(($req_arr['verification_type'] == 'M')){
                        $isEmailVerify = $this->registration->verifyMobile($req_arr['fk_user_id']);
                        }

                        $checkVerifications = $this->registration->checkAllIsActive($req_arr['fk_user_id']);

                       if($checkVerifications['is_email_id_verified'] == 1 && $checkVerifications['is_mobile_number_verified'] == 1 && $checkVerifications['is_active'] == 0){

                        $activateUser = $this->registration->activateUser($req_arr['fk_user_id']);

                       }

                        $http_response   = 'http_response_ok';
                        $success_message = 'OTP verify Successfully';
                    } else {
                        $http_response = 'http_response_bad_request';
                        $error_message = 'Invalid OTP';
                    }

                }else{
                    $http_response = 'http_response_invalid_login';
                    $error_message = 'You are not logged in. Please log in and try again';
                }
            } else {
                $http_response = 'http_response_bad_request';
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }



    public function resendVerificationCode_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
            
        } else {
            
            $req_arr = array();
            $flag    = true;
            /*if (empty($this->post('user_id', true))) {
                $flag          = false;
                $error_message = "userId is required";
            } else {
                $req_arr['fk_user_id'] = $this->post('userId', true);
            }*/

            if(empty($this->post('user_pass_key', true)))
            {
                $flag           = false;
                $error_message  = "Pass Key is required";
            }
            else
            {
                $req_arr['user_pass_key'] = $this->post('user_pass_key', true);
            }

            if($flag && empty($this->post('user_id', true)))
            {
                $flag           = false;
                $error_message  = "User Id is required";
            }
            else
            {
                $req_arr['fk_user_id']            = $this->post('user_id', true);
            }


            if (empty($this->post('type', true))) {
                $flag          = false;
                $error_message = "type is required";
            } else {
                $req_arr['verification_type'] = $this->post('type', true);
            }


            if (($req_arr['verification_type'] == 'E') && (empty($this->post('emailId', true)))) {
                $flag          = false;
                $error_message = "emailId is required";
            } else {
                $req_arr['email_id'] = $this->post('emailId', true);
            }


            if (($req_arr['verification_type'] == 'M') && (empty($this->post('phone', true)))) {
                $flag          = false;
                $error_message = "phone is required";
            } else {
                $req_arr['mobile_number'] = $this->post('phone', true);
            }


            //pre($req_arr,1);
            if ($flag) {

                $req_arr1 = array();
                $plaintext_user_pass_key = $this->encrypt->decode($this->post('user_pass_key', TRUE));
                $plaintext_user_id = $this->encrypt->decode($this->post('user_id', TRUE));

                $req_arr1['user_pass_key']   = $plaintext_user_pass_key;
                $req_arr1['user_id']         = $plaintext_user_id;
                $check_session  = $this->lender_model->checkSessionExist($req_arr1);
                //pre($check_session,1);
 
                if(!empty($check_session) && count($check_session) > 0){
                    $req_arr['fk_user_id'] = $req_arr1['user_id'];

                    if($req_arr['verification_type'] == 'E'){

                        $row['fk_user_id']        = $req_arr['fk_user_id'];
                        $verifyCode               = mt_rand(100000, 999999);
                        $emailVerifyCode          = 'E-' . $verifyCode;
                        $row['verification_type'] = 'E';
                        $row['verification_code'] = $emailVerifyCode;
                        //pre($row,1);
                        $this->user_model->addUserVerificationCode($row);
                        //send email
                        //initialising codeigniter email
                        /*$config['protocol']    = 'smtp';
                        $config['smtp_host']    = 'email-smtp.us-east-1.amazonaws.com';
                        $config['smtp_port']    = '25';
                        $config['smtp_crypto']='tls';
                        $config['smtp_user']    = 'AKIAJKGML4LDY7B7ZFAA';
                        $config['smtp_pass']    = 'Ag+hZ/SZq+79L53OiKRpRcxUVE+G+ulIHT0uHc5khTH1';*/
                        $config['protocol'] = 'sendmail';
                        $config['mailpath'] = '/usr/sbin/sendmail';
                        $config['charset']  = 'utf-8';
                        $config['wordwrap'] = TRUE;
                        $config['mailtype'] = 'html';
                        $this->email->initialize($config);

                        $admin_email      = $this->config->item('admin_email');
                        $admin_email_from = $this->config->item('admin_email_from');
                        $this->email->from($admin_email, $admin_email_from);
                        $this->email->to($req_arr['email_id']);
                        $this->email->subject('Signup Email');
                        $verify_encrypt_code             = $emailVerifyCode . '_' . $row['fk_user_id'];
                        $encrypted_code                  = setEncryption($verify_encrypt_code);
                        $encrp_base                      = urlencode(base64_encode($encrypted_code));
                        $email_data['verification_link'] = $encrp_base;
                        $email_data['verification_code'] = $emailVerifyCode;
                        //pre($email_data,1);
                        $email_body                      = $this->parser->parse('email_templates/signupemailverification', $email_data, true);
                        $this->email->message($email_body);
                        $send = $this->email->send();
                    }
                    if($req_arr['verification_type'] == 'M'){
                        $row['fk_user_id']        = $req_arr['fk_user_id'];
                        $verifyCode               = mt_rand(100000, 999999);
                        $mobileVerifyCode         = 'M-' . $verifyCode;
                        $row['verification_type'] = 'M';
                        $row['verification_code'] = $mobileVerifyCode;
                        //pre($row,1);
                        $this->user_model->addUserVerificationCode($row);
                        $this->sms->category             = "MOBVER";
                        $this->sms->code                 = $mobileVerifyCode;
                        $this->sms->mobile               = $req_arr['mobile_number'];;
                        $response                        = $this->sms->sendSmsFinal();
                    }

                    $send = $response = true;                    
                    if ((($req_arr['verification_type'] == 'E') && $send) || (($req_arr['verification_type'] == 'M') && $response)) {
                    //if(!$send || !$response) {
                        $http_response   = 'http_response_ok';
                        $success_message = 'OTP sent Successfully';
                    } else {
                        $http_response = 'http_response_bad_request';
                        $error_message = ($error_message) ? $error_message : 'Something went wrong in API';
                    }
                }
                else 
                {
                    $http_response  = 'http_response_invalid_login';
                    $error_message  = 'User is invalid';
                }
            }
            else
            {
                $http_response = 'http_response_bad_request';
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);        
    }
    

   public function addUserMode_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
            
        } else {
            
            $req_arr = array();
            $flag    = true;
            if (empty($this->post('user_mode', true))) {
                $flag          = false;
                $error_message = "user_mode is required";
            } else {
                $req_arr['user_mode'] = $this->post('user_mode', true);
                $req_arr['fk_profession_type_id'] = 5;
            }

            /*if (empty($this->post('userId', true))) {
                $flag          = false;
                $error_message = "userId is required";
            } else {
                $req_arr['fk_user_id'] = $this->post('userId', true);
            }*/
            if (!empty($this->post('referal_code', true))) {
               $req_arr['referal_code'] = $this->post('referal_code', true);

            } else {
                $req_arr['referal_code'] ="";
            }

            if($flag){

                $req_arr1 = array();
                $plaintext_user_pass_key = $this->encrypt->decode($this->post('user_pass_key', TRUE));
                $plaintext_user_id = $this->encrypt->decode($this->post('user_id', TRUE));

                $req_arr1['user_pass_key']   = $plaintext_user_pass_key;
                $req_arr1['user_id']         = $plaintext_user_id;
                $check_session  = $this->lender_model->checkSessionExist($req_arr1);
 
                if(!empty($check_session) && count($check_session) > 0){
                    $req_arr['fk_user_id'] = $req_arr1['user_id'];

                    $error_flag = false;

                    if(($req_arr['referal_code'] != "") && ($req_arr['referal_code'] != 'undefined')){
                        $ValidateRefCode =$this->registration->ValidateRefCode($req_arr['referal_code']);

                        if($ValidateRefCode > 0){
                            $userRef =array();
                            $userRef['fk_user_id']             =  $req_arr['fk_user_id'];
                            $userRef['fk_refered_by_user_id']  = $ValidateRefCode['fk_refered_by_user_id'];
                            $userRef['referal_code']           =  $req_arr['referal_code'];

                            $addUserReferals = $this->registration->addUserReferals($userRef);
                            $http_response   = 'http_response_ok';
                            $success_message = 'User referal added Successful';
                        } else {
                            $error_flag = true;
                            $http_response = 'http_response_bad_request';
                            $error_message = 'Referal Code Is Not Valid';
                        }
                    }


                    if($error_flag == false){

                        $checkType = $this->registration->addUserType($req_arr);
                        if ($checkType) {
                            $http_response   = 'http_response_ok';
                            $success_message = (($req_arr['referal_code'] != "") && ($req_arr['referal_code'] != 'undefined')) ? 'User mode added and User referal added Successful' : 'User mode added Successful';
                        
                        } else {
                            $http_response = 'http_response_bad_request';
                            $error_message = 'something went wrong in API';
                        } 
                    } 
                }else{
                    $http_response = 'http_response_invalid_login';
                    $error_message = 'You are not logged in. Please log in and try again';
                }            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }


}



