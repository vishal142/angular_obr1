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

//All the required library file for API has been included here 
/*require APPPATH . 'libraries/api/AppExtrasAPI.php';
require APPPATH . 'libraries/api/AppAndroidGCMPushAPI.php';
require APPPATH . 'libraries/api/AppApplePushAPI.php';*/

require_once('src/OAuth2/Autoloader.php');
require APPPATH . 'libraries/api/REST_Controller.php';


class User_referals extends REST_Controller
{
    function __construct()
    {
        
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: authorization, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header('Access-Control-Allow-Credentials: true');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            die();
        }
        
        
        $this->load->config('rest');
        
        /*$this->load->config('serverconfig');
        $developer = 'www.massoftind.com';
        $this->app_path = "api_" . $this->config->item('test_api_ver');
        //publish app version
        $version = str_replace('_', '.', $this->config->item('test_api_ver'));
        
        $this->publish = array(
        'version' => $version,
        'developer' => $developer
        );*/
        
        //echo $_SERVER['SERVER_ADDR']; exit;
        $dsn        = 'mysql:dbname=' . $this->config->item('oauth_db_database') . ';host=' . $this->config->item('oauth_db_host');
        $dbusername = $this->config->item('oauth_db_username');
        $dbpassword = $this->config->item('oauth_db_password');
        
        /*$sitemode= $this->config->item('site_mode');
        $this->path_detail=$this->config->item($sitemode);*/
        $this->tables = $this->config->item('tables');
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/admin/admin_model', 'admin');
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/User_referals_model', 'User_referals_model');
        $this->load->library('form_validation');
        $this->load->library('email');
        $this->load->library('encrypt');
        
        //$this->load->library('calculation');
        
        
        $this->push_type = 'P';
        //$this->load->library('mpdf');
        
        OAuth2\Autoloader::register();
        
        // $dsn is the Data Source Name for your database, for exmaple "mysql:dbname=my_oauth2_db;host=localhost"
        $storage = new OAuth2\Storage\Pdo(array(
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
     * @ Function Name            : userReferals()
     * @ Added Date               : 19-10-2016
     * @ Added By                 : AMIT PANDIT
     * -----------------------------------------------------------------
     * @ Description              : Invite user referal
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            :
     * @ Modified By              : 
     * 
    */
    
    public function getInviteUserOSDetails_post(){
        
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
            
        } else {
            
            $details_arr = array();
            $os_info = $this->getOS($_SERVER['HTTP_USER_AGENT']);
            //pre($os_info,1);            
            if (!empty($os_info)) {
                $details_arr            = array(
                    'os_info' => $os_info
                );
                $result_arr      = $details_arr;
                $http_response   = 'http_response_ok';
                $success_message = 'success';
            } else {
                $http_response = 'http_response_bad_request';
                $error_message = 'Somthing went wrong';
            }                         
        }

        json_response($result_arr, $http_response, $error_message, $success_message);
    }    


     /*
     * --------------------------------------------------------------------------
     * @ Function Name            : userReferals()
     * @ Added Date               : 19-10-2016
     * @ Added By                 : AMIT PANDIT
     * -----------------------------------------------------------------
     * @ Description              : Invite user referal
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            :
     * @ Modified By              : 
     * 
     */
    
    public function userReferals_post()
    {
        
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
            
        } else {
            
            $req_arr1 = $req_arr = $details_arr = array();
            $flag     = true;
            /*if (empty($this->post('pass_key', true))) {
                $flag          = false;
                $error_message = "Pass Key is required";
            } else {
                $req_arr['pass_key'] = $this->post('pass_key', true);
            }
            
            if ($flag && empty($this->post('admin_user_id', true))) {
                $flag          = false;
                $error_message = "Admin User Id is required";
            } else {
                $req_arr['admin_user_id'] = $this->post('admin_user_id', true);
            }*/
            if ($flag && empty($this->post('name', true))) {
                $flag          = false;
                $error_message = "Name is required";
            } else {
                $req_arr['invitee_name'] = $this->post('name', true);
            }
            if ($flag && empty($this->post('email', true))) {
                $flag          = false;
                $error_message = "Email is required";
            } else {
                $req_arr['email_id'] = $this->post('email', true);
            }
            if ($flag && empty($this->post('mobile_number', true))) {
                $flag          = false;
                $error_message = "mobile_number required";
            } else {
                $req_arr['mobile_no'] = $this->post('mobile_number', true);
            }
            if ($this->input->post('user_code')) {
                $req_arr['user_code'] = $this->input->post('user_code');
            } else {
                
                $req_arr['user_code'] = '';
            }
            
            if ($flag && !empty($this->input->post('user_code'))) {
                /*$plaintext_pass_key = $this->encrypt->decode($req_arr['pass_key']);
                $plaintext_admin_id = $this->encrypt->decode($req_arr['admin_user_id']);
                
                $req_arr1['pass_key']      = $plaintext_pass_key;
                $req_arr1['admin_user_id'] = $plaintext_admin_id;
                $check_session             = $this->admin->checkSessionExist($req_arr1);
                
                if (!empty($check_session) && count($check_session) > 0) {*/
                    $user_data              = array();
                    $user_code              = array();
                    $check_mail['email_id'] = $req_arr['email_id'];
                    $check_user_email       = $this->User_referals_model->validateUser_email($check_mail);
                    if ($check_user_email > 0) {
                        $http_response = 'http_response_bad_request';
                        $error_message = 'Email Id already exists';
                        
                    } else {
                        
                        $user_code['user_code'] = $req_arr['user_code'];
                        $validate_code          = $this->User_referals_model->validateReferal_code($user_code);                        
                        
                        if ($validate_code == 1) {
                            
                            $user_id = $this->User_referals_model->getUser_id($user_code);
                            $user_id = implode($user_id);
                            
                            $user_param                 = array();
                            $user_param['fk_user_id']   = $user_id;
                            $user_param['invitee_name'] = $req_arr['invitee_name'];
                            $user_param['email_id']     = $req_arr['email_id'];
                            $user_param['mobile_no']    = $req_arr['mobile_no'];
                            $user_param['user_code']    = $req_arr['user_code'];
                            
                            $Referal_data = $this->User_referals_model->addInvitedUsers($user_param);
                            
                            if (!empty($Referal_data)) {
                                $data            = array(
                                    'Inserted_id' => $Referal_data
                                );
                                $result_arr      = $data;
                                $http_response   = 'http_response_ok';
                                $success_message = 'success';
                            } else {
                                $http_response = 'http_response_bad_request';
                                $error_message = 'Somthing went wrong';
                            }                          
                            
                        } else {
                            
                            $http_response = 'http_response_bad_request';
                            $error_message = 'Invalid Invitation code';
                        }
                    }
                /*} else {
                    
                    $http_response = 'http_response_invalid_login';
                    $error_message = 'User is invalid';
                }*/
            } else {
                
                $success_message = "Registered without Invitation code";
                $http_response   = 'http_response_ok';
            }
            
            
            
            
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }    
        
  

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : addUsers()
     * @ Added Date               : 26-10-2016
     * @ Added By                 : AMIT PANDIT
     * -----------------------------------------------------------------
     * @ Description              : Add users to admin user collections
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 
     * @ Modified By              : 
     * 
     */
    public function addUsers_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } else {
            $req_arr1 = $req_arr = $details_arr = $data_id = array();
            $flag     = true;
            /*if (empty($this->post('pass_key', true))) {
                $flag          = false;
                $error_message = "Pass Key is required";
            } else {
                $req_arr['pass_key'] = $this->post('pass_key', true);
            }
            if ($flag && empty($this->post('admin_user_id', true))) {
                $flag          = false;
                $error_message = "Admin User Id is required";
            } else {
                $req_arr['admin_user_id'] = $this->post('admin_user_id', true);
            }*/
            if ($flag && empty($this->post('inviteeName', true))) {
                $flag          = false;
                $error_message = "Name is required";
            } else {
                $req_arr['user_name'] = $this->post('inviteeName', true);
            }
            if ($flag && empty($this->post('inviteeEmail', true))) {
                $flag          = false;
                $error_message = "Email is required";
            } else {
                $req_arr['user_email'] = $this->post('inviteeEmail', true);
            }
            if ($flag && empty($this->post('inviteeMobile', true))) {
                $flag          = false;
                $error_message = "Mobile is required";
            } else {
                $req_arr['user_phone'] = $this->post('inviteeMobile', true);
            }
            if ($flag) {
                /*$plaintext_pass_key        = $this->encrypt->decode($req_arr['pass_key']);
                $plaintext_admin_id        = $this->encrypt->decode($req_arr['admin_user_id']);
                $req_arr1['pass_key']      = $plaintext_pass_key;
                $req_arr1['admin_user_id'] = $plaintext_admin_id;
                $check_session             = $this->admin->checkSessionExist($req_arr1);
                if (!empty($check_session) && count($check_session) > 0) {*/
                    $param               = array();
                    $param['user_name']  = $req_arr['user_name'];
                    $param['user_email'] = $req_arr['user_email'];
                    $param['user_phone'] = $req_arr['user_phone'];
                    //echo $config_data['status'];die;
                    $checkEmail          = $this->User_referals_model->checkDuplicateEmail($param);
                    if ($checkEmail == 0) {
                        $result = $this->User_referals_model->addUsers($param);
                        if ($result) {
                            $result_arr      = $result;
                            $http_response   = 'http_response_ok';
                            $success_message = 'User added successfully';
                        } else {
                            $http_response = 'http_response_bad_request';
                            $error_message = 'User not added';
                        }
                    } else {
                        $http_response = 'http_response_bad_request';
                        $error_message = 'User with this email is already exist';
                    }
                /*} else {
                    $http_response = 'http_response_invalid_login';
                    $error_message = 'User is invalid';
                }*/
            } else {
                $http_response = 'http_response_bad_request';
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }

    public function getOS($user_agent) {   

        $os_platform    =   "Unknown OS Platform";
        $os_array       =   array(
                '/windows nt 10.0/i'        =>  'Windows',
                '/windows nt 6.2/i'         =>  'Windows',
                '/windows nt 6.1/i'         =>  'Windows',
                '/windows nt 6.0/i'         =>  'Windows',
                '/windows nt 5.2/i'         =>  'Windows',
                '/windows nt 5.1/i'         =>  'Windows',
                '/windows xp/i'             =>  'Windows',
                '/windows nt 5.0/i'         =>  'Windows',
                '/windows me/i'             =>  'Windows',
                '/win98/i'                  =>  'Windows',
                '/win95/i'                  =>  'Windows',
                '/win16/i'                  =>  'Windows',
                '/macintosh|mac os x/i'     =>  'Mac OS X',
                '/mac_powerpc/i'            =>  'Mac OS 9',
                '/linux/i'                  =>  'Linux',
                '/ubuntu/i'                 =>  'Ubuntu',
                '/iphone/i'                 =>  'ios',
                '/ipod/i'                   =>  'ios',
                '/ipad/i'                   =>  'ios',
                '/android/i'                =>  'android',
                '/blackberry/i'             =>  'BlackBerry',
                '/webos/i'                  =>  'Mobile'
            );

        foreach ($os_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $os_platform    =   $value;
            }
        } 
        return $os_platform;
    }











}