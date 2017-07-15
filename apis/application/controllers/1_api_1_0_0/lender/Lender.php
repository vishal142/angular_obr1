<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//error_reporting(0);
error_reporting(E_ALL);
require_once('src/OAuth2/Autoloader.php');
require APPPATH . 'libraries/api/REST_Controller.php';


class Lender extends REST_Controller
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
        $this->tables = $this->config->item('tables'); 
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/user_model', 'user_model');
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/profile_model', 'profile_model');
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
    

    public function logIn_post(){
        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        $login_pwd = '';
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';        
        } else {
            //pre($this->post());
            $req_arr = $details_arr = array();
            $flag           = true;
            if(empty($this->post('email', true))){
                $flag           = false;
                $error_message  = "Email id is required";
            } else {
                $req_arr['email_id'] = $this->post('email', true);
            }

            if($flag && empty($this->post('password', true))){
                $flag           = false;
                $error_message  = "Password is required";
            } else {
                $login_pwd = $req_arr['login_pwd'] = $this->post('password', true);
            }           

            if($flag){

                /*$data = array();
                $data['username'] = $this->post('username', true);
                $data['password'] = $this->post('password', true);*/
                $login_check = true;
                if(!empty($req_arr['email_id']) && !empty($req_arr['login_pwd'])){
                    $result = $this->lender_model->checkUser($req_arr);
                    //pre($result,1);
                    $login_check = (password_verify($login_pwd, $result['login_pwd'])) ? true :false;
                    if($login_check) {
                        //if($result['status']=='active'){
                                                  
                            //fetch user details
                            $where_user = array('id'=>$result['user_id']);
                            $admin_user_detail = $this->common->select_one_row($this->tables['tbl_users'], $where_user ,'id, email_id');

                            //pre($admin_user_detail,1);
                            // Set login session for admin
                            $session_data = array();
                            $session_data['fk_user_id']         = $admin_user_detail['id'];
                            $session_data['ip_address']         = $_SERVER['REMOTE_ADDR'];
                            $session_data['browser_session_id'] = session_id();
                            $session_data['user_agent']         = $_SERVER['HTTP_USER_AGENT'];
                            //$session_data['gps_location']     = '';
                            //pre($session_data,1);                       
                            $last_id = $this->lender_model->addUserLoginSession($session_data);
                            
                            $req_arr = array();
                            $req_arr['user_pass_key'] = $last_id;
                            $req_arr['user_id'] = $admin_user_detail['id'];

                            //pre($req_arr,1);

                            $user_session_arr = $this->lender_model->checkSessionExist($req_arr);

                            //pre($user_session_arr,1);
                            $encrypted_user_pass_key = $this->encrypt->encode($req_arr['user_pass_key']);
                            $encrypted_user_id = $this->encrypt->encode($req_arr['user_id']);

                            $user_session_arr['user_pass_key']      = $encrypted_user_pass_key;
                            $user_session_arr['user_id']            = $encrypted_user_id;
                           

                            $result_arr = $user_session_arr;
                            //pre($result_arr,1 );

                            $http_response = 'http_response_ok';
                            $success_message = lang('lbl_success_login_successful');

                        /*}else{
                            $http_response = 'http_response_bad_request';
                            $error_message = 'Your account is not activated, Please verify Email Id and mobile number';
                        }*/

                    }else{
                        $http_response = 'http_response_invalid_login';
                        $error_message = lang('lbl_api_invalid_user_id_pass_key');
                    }
                }else{

                    $http_response = 'http_response_bad_request';
                    $error_message = lang('lbl_username_and_password_required');
                }
            } else {
                $http_response = 'http_response_bad_request';
            }
        } 
        json_response($result_arr, $http_response, $error_message, $success_message);
    }




    public function checkUserAuthentication_post(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();

        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {
            //pre($this->post());
            $req_arr = array();
            $plaintext_user_pass_key = $this->encrypt->decode($this->post('user_pass_key', TRUE));
            $plaintext_user_id = $this->encrypt->decode($this->post('user_id', TRUE));            
            //$plaintext_user_pass_key = $this->post('user_pass_key', TRUE);
            //$plaintext_user_id = $this->post('user_id', TRUE);            

            $req_arr['user_pass_key']   = $plaintext_user_pass_key;
            $req_arr['user_id']         = $plaintext_user_id;
            //pre($req_arr,1);
            $check_session  = $this->lender_model->checkSessionExist($req_arr);
            //pre($check_session,1);

            if(!empty($check_session) && count($check_session) > 0){
                    
                $check_session['full_name'] = $check_session['f_name'].' '.$check_session['l_name'];
                $check_session['display_name'] = $check_session['display_name'] ;
                $http_response = 'http_response_ok';
                $success_message = 'Already loggedin';

                $result_arr =  $check_session;                   
        

            }else{
                $http_response = 'http_response_invalid_login';
                $error_message = 'You are not logged in. Please log in and try again';
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }


    public function logOut_post(){
    
        $error_message = $success_message = $http_response = '';
        $result_arr = array();

        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {
            $req_arr = array();
            //pre($this->post());
            $plaintext_user_pass_key = $this->encrypt->decode($this->post('user_pass_key', TRUE));
            $plaintext_user_id = $this->encrypt->decode($this->post('user_id', TRUE));

            $req_arr['user_pass_key']       = $plaintext_user_pass_key;
            $req_arr['user_id']             = $plaintext_user_id;
            // pre($req_arr,1);

            $affected_rows  = $this->lender_model->logoutUser($req_arr);
            if($affected_rows > 0){
                $http_response      = 'http_response_ok';
                $success_message    = 'Logout successful';  
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = 'Logout not done';  
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }

    }