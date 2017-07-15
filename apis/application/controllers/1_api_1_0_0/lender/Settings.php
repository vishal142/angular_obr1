<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//error_reporting(0);
error_reporting(E_ALL);
require_once('src/OAuth2/Autoloader.php');
require APPPATH . 'libraries/api/REST_Controller.php';
class Settings extends REST_Controller
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
       // $this->load->model('api_' . $this->config->item('test_api_ver') . '/lender/Profile_model', 'profile');
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/lender/lender_model', 'lender');
       // $this->load->model('api_' . $this->config->item('test_api_ver') . '/lender/settings_model', 'settings');
        $this->load->library('encrypt');
        //$this->push_type = 'P';
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
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : changeEmailStep1()
     * @ Added Date               : 17-11-2016
     * @ Added By                 : Amit pandit
     * -----------------------------------------------------------------
     * @ Description              : Change Email step 1 (OTP sending)
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 
     * @ Modified By              : 
     * 
     
     */
    public function changeEmailStep1_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } else {
            $flag    = true;
            $req_arr = array();
            if (!$this->post('email_id')) {
                $flag          = false;
                $error_message = 'Please enter email address';
            } else {
                $req_arr['email_id'] = $this->post('email_id', TRUE);
            }
            if ($flag) {
                $req_arr1                  = array();
                $plaintext_user_pass_key   = $this->encrypt->decode($this->post('user_pass_key', TRUE));
                $plaintext_user_id         = $this->encrypt->decode($this->post('user_id', TRUE));
                $req_arr1['user_pass_key'] = $plaintext_user_pass_key;
                $req_arr1['user_id']       = $plaintext_user_id;
                $check_session             = $this->lender->checkSessionExist($req_arr1);
                //pre($check_session,1);
                if (!empty($check_session) && count($check_session) > 0) {
                    $req_arr1['email_id']     = $req_arr['email_id'];
                    //save verofication code to email
                    $row['fk_user_id']        = $req_arr1['user_id'];
                    //$verifyCode=$this->getVerificationCode();
                    $verifyCode               = mt_rand(100000, 999999);
                    $emailVerifyCode          = strtoupper('E-' . $verifyCode);
                    $row['verification_type'] = 'E';
                    $row['verification_code'] = $emailVerifyCode;
                    //pre($row,1);
                    $this->user_model->addUserVerificationCode($row);
                    //send email
                    //initialising codeigniter email
                    $email_config = email_config();
                    $this->email->initialize($email_config);
                    // email sent to user 
                    $admin_email      = $this->config->item('admin_email');
                    $admin_email_from = $this->config->item('admin_email_from');
                    $this->email->from($admin_email, $admin_email_from);
                    $this->email->to($req_arr1['email_id']);
                    $this->email->subject('Email Verification');
                    $verify_encrypt_code             = $emailVerifyCode . '_' . $req_arr1['user_id'] . '_' . $req_arr1['email_id'];
                    $encrypted_code                  = setEncryption($verify_encrypt_code);
                    $encrp_base                      = urlencode(base64_encode($encrypted_code));
                    $email_data['verification_link'] = $encrp_base;
                    $email_data['verification_code'] = $emailVerifyCode;
                    $email_body                      = $this->parser->parse('email_templates/changeEmailverification', $email_data, true);
                    $this->email->message($email_body);
                    $this->email->send();
                    // email send end
                    //save verofication code to email
                    // $result_arr       = $user_details_arr;
                    $http_response   = 'http_response_ok';
                    $success_message = 'Email has been sent';
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
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : changeEmailStep
     * @ Added Date               : 17-11-2016
     * @ Added By                 : Amit pandit
     * -----------------------------------------------------------------
     * @ Description              : Change Email step final
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 
     * @ Modified By              : 
     * 
     */
    public function changeEmailStep2_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } else {
            $flag    = true;
            $req_arr = array();
            if (!$this->post('email_id')) {
                $flag          = false;
                $error_message = 'Please enter email address';
            } else {
                $req_arr['email_id'] = $this->post('email_id', TRUE);
            }
            $req_arr['verification_code'] = $this->post('verification_code', TRUE);
            if ($flag) {
                $req_arr1                  = array();
                $plaintext_user_pass_key   = $this->encrypt->decode($this->post('user_pass_key', TRUE));
                $plaintext_user_id         = $this->encrypt->decode($this->post('user_id', TRUE));
                $req_arr1['user_pass_key'] = $plaintext_user_pass_key;
                $req_arr1['user_id']       = $plaintext_user_id;
                $check_session             = $this->lender->checkSessionExist($req_arr1);
                //pre($check_session,1);
                if (!empty($check_session) && count($check_session) > 0) {
                    $req_arr1['email_id'] = $req_arr['email_id'];
                    if ($req_arr['verification_code'] != '') {
                        //save verofication code to email
                        $row['fk_user_id']        = $req_arr1['user_id'];
                        $row['verification_code'] = "E-" . $req_arr['verification_code'];
                        $row['verification_type'] = 'E';
                        //log in status checking
                        $is_verify                = $this->user_model->checkVerificationCode($row);
                        if ($is_verify > 0) {
                            $verify_data['id']       = $req_arr1['user_id'];
                            $verify_data['email_id'] = $req_arr1['email_id'];
                            $this->user_model->chnageEmail($verify_data);
                            $userDetails           = $this->user_model->fetchUserDeatils($req_arr1['user_id']);
                            $email_id              = $userDetails['email_id'];
                            $history['fk_user_id'] = $req_arr1['user_id'];
                            $history['email_id']   = $email_id;
                            $this->user_model->addHistoryEmail($history);
                            $http_response   = 'http_response_ok';
                            $success_message = 'your Email has been successfully changed';
                        } else {
                            $http_response = 'http_response_bad_request';
                            $error_message = 'Invalid Verification Code ';
                        }
                    } else {
                        $userDetails = $this->user_model->fetchUserDeatils($req_arr1['user_id']);
                        if ($userDetails['is_email_id_verified'] == '1') {
                            $http_response   = 'http_response_ok';
                            $success_message = '';
                        } else {
                            $http_response = 'http_response_bad_request';
                            $error_message = 'Invalid Verification Code ';
                        }
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
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : changeMobileStep1
     * @ Added Date               : 17-11-2016
     * @ Added By                 : Amit pandit
     * -----------------------------------------------------------------
     * @ Description              : Change mobile step 1(OTP sending)
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 
     * @ Modified By              : 
     * 
     */
    public function changeMobileStep1_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } else {
            $flag    = true;
            $req_arr = array();
            if (!$this->post('mobile_number')) {
                $flag          = false;
                $error_message = 'Please enter mobile number';
            } else {
                $req_arr['mobile_number'] = $this->post('mobile_number', TRUE);
            }
            if ($flag) {
                $req_arr1                  = array();
                $plaintext_user_pass_key   = $this->encrypt->decode($this->post('user_pass_key', TRUE));
                $plaintext_user_id         = $this->encrypt->decode($this->post('user_id', TRUE));
                $req_arr1['user_pass_key'] = $plaintext_user_pass_key;
                $req_arr1['user_id']       = $plaintext_user_id;
                $check_session             = $this->lender->checkSessionExist($req_arr1);
                //pre($check_session,1);
                if (!empty($check_session) && count($check_session) > 0) {
                    //save verofication code to email
                    $row['fk_user_id']        = $req_arr1['user_id'];
                    //$verifyCode=$this->getVerificationCode();
                    $verifyCode               = mt_rand(100000, 999999);
                    $mobileVerifyCode         = strtoupper('M-' . $verifyCode);
                    $row['verification_type'] = 'M';
                    $row['verification_code'] = $mobileVerifyCode;
                    $this->user_model->addUserVerificationCode($row);
                    //send sms
                    //initialising codeigniter email
                    $this->sms->category = "MOBVER";
                    $this->sms->code     = $mobileVerifyCode;
                    $this->sms->mobile   = $req_arr['mobile_number'];
                    $response            = $this->sms->sendSmsFinal();
                    // sms send end
                    //save verofication code to email
                    $result_arr          = $user_details_arr;
                    $http_response       = 'http_response_ok';
                    $success_message     = 'Verification SMS has been sent';
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
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : changeMobileStep
     * @ Added Date               : 17-11-2016
     * @ Added By                 : Amit pandit
     * -----------------------------------------------------------------
     * @ Description              : Change mobile final
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 
     * @ Modified By              : 
     * 
     */
    public function changeMobileStep2_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } else {
            $flag    = true;
            $req_arr = array();
            if (!$this->post('mobile_number')) {
                $flag          = false;
                $error_message = 'Please enter mobile number';
            } else {
                $req_arr['mobile_number'] = $this->post('mobile_number', TRUE);
            }
            if (!$this->post('verification_code')) {
                $flag          = false;
                $error_message = 'Please enter verification code';
            } else {
                $req_arr['verification_code'] = $this->post('verification_code', TRUE);
            }
            if ($flag) {
                $req_arr1                  = array();
                $plaintext_user_pass_key   = $this->encrypt->decode($this->post('user_pass_key', TRUE));
                $plaintext_user_id         = $this->encrypt->decode($this->post('user_id', TRUE));
                $req_arr1['user_pass_key'] = $plaintext_user_pass_key;
                $req_arr1['user_id']       = $plaintext_user_id;
                $check_session             = $this->lender->checkSessionExist($req_arr1);
                //pre($check_session,1);
                if (!empty($check_session) && count($check_session) > 0) {
                    $req_arr1['mobile_number']     = $req_arr['mobile_number'];
                    $req_arr1['verification_code'] = $req_arr['verification_code'];
                    //save verofication code to email
                    $row['fk_user_id']             = $req_arr1['user_id'];
                    $row['verification_code']      = "M-" . $req_arr1['verification_code'];
                    $row['verification_type']      = 'M';
                    // pre($row, 1);
                    $is_verify                     = $this->user_model->checkVerificationCode($row);
                    if ($is_verify > 0) {
                        $verify_data['id']            = $req_arr1['user_id'];
                        $verify_data['mobile_number'] = $req_arr1['mobile_number'];
                        $this->user_model->chnageMobile($verify_data);
                        $userDetails              = $this->user_model->fetchUserDeatils($req_arr1['user_id']);
                        $mobile_number            = $userDetails['mobile_number'];
                        $history['fk_user_id']    = $req_arr1['user_id'];
                        $history['mobile_number'] = $mobile_number;
                        $this->user_model->addHistoryMobile($history);
                        $http_response   = 'http_response_ok';
                        $success_message = 'Mobile Numeber Changed successfully';
                    } else {
                        $http_response = 'http_response_bad_request';
                        $error_message = 'Invalid Verification Code ';
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
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : changePassword
     * @ Added Date               : 17-11-2016
     * @ Added By                 : Amit pandit
     * -----------------------------------------------------------------
     * @ Description              : Change password
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 
     * @ Modified By              : 
     * 
     *
     */
    public function changePassword_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } else {
            $flag    = true;
            $req_arr = array();
            if (!$this->post('password')) {
                $flag          = false;
                $error_message = 'Please enter password';
            } else {
                $req_arr['password'] = $this->post('password', TRUE);
            }
            if (!$this->post('confirm_password')) {
                $flag          = false;
                $error_message = 'Please enter confirm password';
            } else {
                $req_arr['confirm_password'] = $this->post('confirm_password', TRUE);
            }
            if ($req_arr['password'] != $req_arr['confirm_password']) {
                $flag          = false;
                $error_message = 'Password & Confirm Password does not match';
            }

            //$req_arr['password'] = password_hash($this->post('password', true),PASSWORD_BCRYPT);





            if ($flag) {
                $req_arr1                  = array();
                $plaintext_user_pass_key   = $this->encrypt->decode($this->post('user_pass_key', TRUE));
                $plaintext_user_id         = $this->encrypt->decode($this->post('user_id', TRUE));
                $req_arr1['user_pass_key'] = $plaintext_user_pass_key;
                $req_arr1['user_id']       = $plaintext_user_id;
                $check_session             = $this->lender->checkSessionExist($req_arr1);
                //pre($check_session,1);
                if (!empty($check_session) && count($check_session) > 0) {
                    $req_arr['user_id'] = $req_arr1['user_id'];
                    $user_details_arr   = $this->user_model->update_password($req_arr);
                    $result_arr         = $user_details_arr;
                    $http_response      = 'http_response_ok';
                    $success_message    = 'Password Updated successfully';
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
}