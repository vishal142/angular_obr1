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


class Admin extends REST_Controller{
    function __construct(){

        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: authorization, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header('Access-Control-Allow-Credentials: true');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if($method == "OPTIONS") {
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
        $dsn = 'mysql:dbname='.$this->config->item('oauth_db_database').';host='.$this->config->item('oauth_db_host');
        $dbusername = $this->config->item('oauth_db_username');
        $dbpassword = $this->config->item('oauth_db_password');

        /*$sitemode= $this->config->item('site_mode');
        $this->path_detail=$this->config->item($sitemode);*/      
        $this->tables = $this->config->item('tables'); 
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/admin/admin_model', 'admin');
        $this->load->library('form_validation');
        $this->load->library('email');
        $this->load->library('encrypt');

        //$this->load->library('calculation');

       
        $this->push_type = 'P';
        //$this->load->library('mpdf');

         OAuth2\Autoloader::register();

        // $dsn is the Data Source Name for your database, for exmaple "mysql:dbname=my_oauth2_db;host=localhost"
        $storage = new OAuth2\Storage\Pdo(array('dsn' => $dsn, 'username' => $dbusername, 'password' => $dbpassword));

        // Pass a storage object or array of storage objects to the OAuth2 server class
        $this->oauth_server = new OAuth2\Server($storage);

        // Add the "Client Credentials" grant type (it is the simplest of the grant types)
        $this->oauth_server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));

        // Add the "Authorization Code" grant type (this is where the oauth magic happens)
        $this->oauth_server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));
    }


    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : checkUserAuthentication()
    * @ Added Date               : 14-04-2016
    * @ Added By                 : Subhankar
    * -----------------------------------------------------------------
    * @ Description              : check user authentication
    * -----------------------------------------------------------------
    * @ return                   : array
    * -----------------------------------------------------------------
    * @ Modified Date            : 14-04-2016
    * @ Modified By              : Subhankar
    * 
    */
    public function checkUserAuthentication_post(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();

        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $req_arr = array();
            $plaintext_pass_key = $this->encrypt->decode($this->post('pass_key', TRUE));
            $plaintext_admin_id = $this->encrypt->decode($this->post('admin_user_id', TRUE));
            $module = $this->post('module', TRUE);

            $req_arr['pass_key']        = $plaintext_pass_key;
            $req_arr['admin_user_id']   = $plaintext_admin_id;
            $check_session  = $this->admin->checkSessionExist($req_arr);
            //pre($check_session,1);

            if(!empty($check_session) && count($check_session) > 0){

                if($check_session['admin_level'] > 1){

                    $accessable_modules_arr = array(
                        'welcome',
                        'profile.edit',
                        'profile.changepassword',
                        'users.list',
                        'users.basic',
                        'users.education',
                        'users.education-edit',
                        'users.kyc',
                        'users.kyc-edit',
                        'users.bank',
                        'users.bank-edit',
                        'users.interest',
                        'data-collections.list'
                    );

                    if(in_array($module, $accessable_modules_arr)){
                        $check_session['full_name'] = $check_session['f_name'].' '.$check_session['l_name'];
                        $result_arr = $check_session;
                        $http_response = 'http_response_ok';
                        $success_message = 'Already loggedin';                   
                    } else {

                        $affected_rows  = $this->admin->logoutAdmin($req_arr);                      
                        $http_response = 'http_response_unauthorized';
                        $error_message = 'You are not authorized to access this module';
                    }

                } else {
                    $check_session['full_name'] = $check_session['f_name'].' '.$check_session['l_name'];
                    $result_arr = $check_session;
                    $http_response = 'http_response_ok';
                    $success_message = 'Already loggedin';                    
                }

            }else{
                $http_response = 'http_response_invalid_login';
                $error_message = 'Session timeout, Please login again';
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }


    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : logIn()
    * @ Added Date               : 06-04-2016
    * @ Added By                 : Subhankar
    * -----------------------------------------------------------------
    * @ Description              : This is the admin log in page
    * -----------------------------------------------------------------
    * @ return                   : array
    * -----------------------------------------------------------------
    * @ Modified Date            : 06-04-2016
    * @ Modified By              : Subhankar
    * 
    */
    public function logIn_post(){
        $error_message = $success_message = $http_response = '';
        $result_arr = array();

        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';        
        } else {
            //pre($this->post());
            $req_arr = $details_arr = array();
            $flag           = true;
            if(empty($this->post('username', true))){
                $flag           = false;
                $error_message  = "Email id is required";
            } else {
                $req_arr['username'] = $this->post('username', true);
            }

            if($flag && empty($this->post('password', true))){
                $flag           = false;
                $error_message  = "Password is required";
            } else {
                $req_arr['password'] = $this->post('password', true);
            }           

            if($flag){

                /*$data = array();
                $data['username'] = $this->post('username', true);
                $data['password'] = $this->post('password', true);*/

                if(!empty($req_arr['username']) && !empty($req_arr['password'])){
                    $result = $this->admin->checkUser($req_arr);
                    //pre($result,1);
                    if (!empty($result)) {
                        if($result['status']=='active'){
                                                  
                            //fetch admin details
                            $where_user = array('id'=>$result['user_id']);
                            $admin_user_detail = $this->common->select_one_row($this->tables['tbl_admins'], $where_user ,'id, login_email');

                            // Set login session for admin
                            $session_data = array();
                            $session_data['fk_admin_id']        = $admin_user_detail['id'];
                            $session_data['ip_address']         = $_SERVER['REMOTE_ADDR'];
                            $session_data['browser_session_id'] = session_id();
                            $session_data['user_agent']         = $_SERVER['HTTP_USER_AGENT'];
                            //$session_data['gps_location']     = '';
                            //pre($session_data,1);                       
                            $last_id = $this->admin->addAdminLoginSession($session_data);

                            $req_arr = array();
                            $req_arr['pass_key'] = $last_id;
                            $req_arr['admin_user_id'] = $admin_user_detail['id'];

                            $user_session_arr = $this->admin->checkSessionExist($req_arr);
                            $encrypted_pass_key = $this->encrypt->encode($user_session_arr['pass_key']);
                            $encrypted_admin_id = $this->encrypt->encode($result['user_id']);
                            $encrypted_admin_level = $this->encrypt->encode($user_session_arr['admin_level']);

                            $user_session_arr['pass_key']       = $encrypted_pass_key;                     
                            $user_session_arr['admin_user_id']  = $encrypted_admin_id;
                            $user_session_arr['admin_level1']    = $encrypted_admin_level;

                            $result_arr = $user_session_arr;

                            $http_response = 'http_response_ok';
                            $success_message = lang('lbl_success_login_successful');

                        }else{
                            $http_response = 'http_response_bad_request';
                            $error_message = 'Your account is not activated, Please contact your admin';
                        }

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


    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : logOut()
    * @ Added Date               : 14-04-2016
    * @ Added By                 : Subhankar
    * -----------------------------------------------------------------
    * @ Description              : log out admin user
    * -----------------------------------------------------------------
    * @ return                   : array
    * -----------------------------------------------------------------
    * @ Modified Date            : 14-04-2016
    * @ Modified By              : Subhankar
    * 
    */
    public function logOut_post(){
    
        $error_message = $success_message = $http_response = '';
        $result_arr = array();

        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {
            $req_arr = array();

            $plaintext_pass_key = $this->encrypt->decode($this->post('pass_key', TRUE));
            $plaintext_admin_id = $this->encrypt->decode($this->post('admin_user_id', TRUE));

            $req_arr['pass_key']        = $plaintext_pass_key;
            $req_arr['admin_user_id']   = $plaintext_admin_id;

            $affected_rows  = $this->admin->logoutAdmin($req_arr);
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


    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : forgetPassword()
    * @ Added Date               : 14-04-2016
    * @ Added By                 : Subhankar
    * -----------------------------------------------------------------
    * @ Description              : admin forget password
    * -----------------------------------------------------------------
    * @ return                   : array
    * -----------------------------------------------------------------
    * @ Modified Date            : 14-04-2016
    * @ Modified By              : Subhankar
    * 
    */
    public function forgetPassword_post(){
        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        //pre($this->post(),1);

        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $email = '';
            $flag  = true;
            if(empty($this->post('email', true))){
                $flag           = false;
                $error_message  = "Email Id is required";
            } else {
                $email          = $this->post('email', true);
            }

            if($flag){
                $check_email    = $this->admin->checkEmailid($email);

                if(!empty($check_email) && count($check_email) > 0){

                    $password_reset_code = '';
                    $user_id  = $check_email[0]['id'];                     
                    $check_passcode  = $this->admin->checkExistPasscode($user_id);
                    $pass_id = 0;
                    //Update or save password reset code
                    if(!empty($check_passcode) && count($check_passcode) > 0){

                        $save_pass['fk_admin_id']           = $user_id;
                        $save_pass['generated_timestamp']   = date("Y-m-d H:i:s");
                        $where      = array("id"=>$check_passcode['id'], "fk_admin_id"=>$user_id);
                        $update     = $this->common->update($this->tables['tbl_admin_pwd_reset_codes'],$where,$save_pass);
                        if($update){
                            $pass_id                        = $check_passcode['id'];
                        } else {
                            $error_message = 'There is some problem, Please try again';
                            $http_response = 'http_response_bad_request';
                        }
                        $password_reset_code = $check_passcode['passcode'];

                    } else {
                        $password_reset_code                = 'P-'.getVerificationCode();                       
                        $save_pass['id']                    = false;
                        $save_pass['fk_admin_id']           = $user_id;
                        $save_pass['passcode']              = $password_reset_code;
                        $save_pass['generated_timestamp']   = date("Y-m-d H:i:s");
                        $pass_id    = $this->common->add($this->tables['tbl_admin_pwd_reset_codes'],$save_pass);
                    }

                    if($pass_id > 0){

                        //send email
                        //initialising codeigniter email
                        $config['protocol']     = 'sendmail';
                        $config['mailpath']     = '/usr/sbin/sendmail';
                        $config['charset']      = 'utf-8';
                        $config['wordwrap']     = TRUE;
                        $config['mailtype']     = 'html';
                        $this->email->initialize($config);
                        
                        // email sent to user 
                        $admin_email        = $this->config->item('admin_email');
                        $admin_email_from   = $this->config->item('admin_email_from');
                        $this->email->from($admin_email, $admin_email_from);
                        $this->email->to($check_email[0]['login_email']);          
                        $this->email->subject($this->config->item('site_title') . ' - Forget Password: Verification Code');

                        $email_data['verification_code'] = $password_reset_code;                   

                        $email_body = $this->parser->parse('email_templates/forgetpassword', $email_data, true);
                        $this->email->message($email_body);            

                        $send = $this->email->send();
                        // email send end
                    }
                    $result_arr['admin_user_id'] = $user_id;
                    $success_message = 'Password reset link sent to your mail. Check mail for reset password';
                    $http_response   = 'http_response_ok';

                } else {
                    $error_message = 'Please enter registered email id';
                    $http_response = 'http_response_bad_request';
                }
            } else {
                $http_response = 'http_response_bad_request';
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    } 


    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : verifyPasscode()
    * @ Added Date               : 14-04-2016
    * @ Added By                 : Subhankar
    * -----------------------------------------------------------------
    * @ Description              : admin forget password
    * -----------------------------------------------------------------
    * @ return                   : array
    * -----------------------------------------------------------------
    * @ Modified Date            : 14-04-2016
    * @ Modified By              : Subhankar
    * 
    */
    public function verifyPasscode_post(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        //echo 'SP';exit();

        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $req_arr = $details_arr = array();
            //pre($this->post(),1);
            $flag           = true;
            if(empty($this->post('passcode', true))){
                $flag           = false;
                $error_message  = "Pass Key is required";
            } else {
                $req_arr['passcode'] = $this->post('passcode', true);
            }
            
            $req_arr['fk_admin_id'] = $this->post('admin_user_id', TRUE);

            if($flag){
                $passcode_details = $this->admin->getAdminPswdResetCode($req_arr);

                /*echo $this->db->last_query();
                pre($passcode_details,1);*/

                if(!empty($passcode_details) && count($passcode_details) > 0)
                {
                    if($passcode_details['passcode'] == $req_arr['passcode'])
                    {
                        $passcode_validity  = $this->config->item("passcode_validity");
                        $exp_datetime       = date("Y-m-d H:i:s", strtotime($passcode_details['generated_timestamp'] . "+".$passcode_validity." minutes"));

                        if($exp_datetime >= date("Y-m-d H:i:s"))
                        {
                            $result_arr         = array(
                                'passcode'          => $req_arr['passcode'],
                                'admin_user_id'     => $req_arr['fk_admin_id'],
                            );
                            $http_response      = 'http_response_ok';
                            $success_message    = 'Your provided code matched'; 
                        } 
                        else 
                        {
                            $http_response      = 'http_response_ok_no_content';
                            $error_message      = "Your validation code is expired, please reset password again";
                        }
                    }
                    else
                    {
                        $http_response      = 'http_response_bad_request';
                        $error_message      = 'Your provided code did not match';
                    }
                } 
                else 
                {
                    $http_response      = 'http_response_bad_request';
                    $error_message      = 'Your provided code did not match';
                }
            } else {
                $http_response = 'http_response_bad_request';
            }
        }

        json_response($result_arr, $http_response, $error_message, $success_message);
    }


    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : resetPassword()
    * @ Added Date               : 14-04-2016
    * @ Added By                 : Subhankar
    * -----------------------------------------------------------------
    * @ Description              : admin forget password
    * -----------------------------------------------------------------
    * @ return                   : array
    * -----------------------------------------------------------------
    * @ Modified Date            : 14-04-2016
    * @ Modified By              : Subhankar
    * 
    */
    public function resetPassword_post()
    {
        //pre($this->post(),1);
        $error_message = $success_message = $http_response = $new_password = $confirm_password = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) 
        {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {
            
            $req_arr = $details_arr = array();
            $flag    = true;
            if(!trim($this->post('passcode', true)))
            {
                $flag           = false;
                $error_message  = "Pass Code is required";
            }
            else
            {
                $req_arr['passcode']        = $this->post('passcode', true);
            }

            if($flag && !trim($this->post('admin_user_id', true)))
            {
                $flag           = false;
                $error_message  = "Admin User Id is required";
            }
            else
            {
                $req_arr['admin_user_id']   = $this->post('admin_user_id', true);
            }

            if($flag && !trim($this->post('new_password', true)))
            {
                $flag           = false;
                $error_message  = "New Password is required";
            } 
            else 
            {
                $req_arr['new_password'] = $this->post('new_password', TRUE);
            }

            if($flag && !trim($this->post('confirm_password', true)))
            {
                $flag           = false;
                $error_message  = "Confirm Password is required";
            }
            else 
            {
                $req_arr['confirm_password'] = $this->post('confirm_password', TRUE);
            }

            if($flag && ($req_arr['confirm_password'] != $req_arr['new_password']))
            {
                $flag           = false;
                $error_message  = "Confirm Password is does not match";
            }
            /*echo $error_message;
            pre($this->post(),1);*/

            if($flag)
            {
                $pass_arr = array(
                    'passcode'      => $req_arr['passcode'],
                    'fk_admin_id'   => $req_arr['admin_user_id']
                );
                $passcode_details = $this->admin->getAdminPswdResetCode($pass_arr);
                if(!empty($passcode_details) && count($passcode_details) > 0)
                {
                    $update_arr = array(
                        'admin_id' => $req_arr['admin_user_id'],
                        'password' => md5($req_arr['new_password'])
                    );
                    $status        = $this->admin->changePassword($update_arr);
                    if($status > 0) 
                    {
                        $remove_arr = array(
                            'passcode'      => $req_arr['passcode'],
                            'fk_admin_id'   => $req_arr['admin_user_id']
                        );
                        $count = $this->admin->removePasscode($remove_arr);
                        $http_response      = 'http_response_ok';
                        $success_message    = 'Password changed successfully';

                    } else {
                        $http_response      = 'http_response_bad_request';
                        $error_message      = 'Something went wrong in API';
                    }
                } else {
                    $http_response          = 'http_response_bad_request';
                    $error_message          = 'Verification Code does not matched';
                }
            } else {
                $http_response = 'http_response_bad_request';
            } 
        }

        json_response($result_arr, $http_response, $error_message, $success_message);
    }


    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : getAdminUser()
    * @ Added Date               : 14-04-2016
    * @ Added By                 : Subhankar
    * -----------------------------------------------------------------
    * @ Description              : log out admin user
    * -----------------------------------------------------------------
    * @ return                   : array
    * -----------------------------------------------------------------
    * @ Modified Date            : 14-04-2016
    * @ Modified By              : Subhankar
    * 
    */
    public function getAdminUser_post(){   
    
        $error_message = $success_message = $http_response = '';
        $result_arr = array();

        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $req_arr1 = array();
            $plaintext_pass_key = $this->encrypt->decode($this->input->post('pass_key', TRUE));
            $plaintext_admin_id = $this->encrypt->decode($this->input->post('admin_user_id', TRUE));

            $req_arr1['pass_key']        = $plaintext_pass_key;
            $req_arr1['admin_user_id']   = $plaintext_admin_id;
            $check_session  = $this->admin->checkSessionExist($req_arr1);

            if(!empty($check_session) && count($check_session) > 0){

                $req_arr = $details_arr = array();
                $plaintext_admin_id = $this->encrypt->decode($this->post('admin_user_id', TRUE));
                $req_arr['admin_user_id']   = $plaintext_admin_id;

                $details_arr  = $this->admin->getAdminDetails($req_arr);
                //pre($details_arr,1);

                if(!empty($details_arr) && count($details_arr) > 0){
                    $result_arr         = $details_arr;
                    $http_response      = 'http_response_ok';
                    $success_message    = 'Admin details';  
                } else {
                    $http_response      = 'http_response_bad_request';
                    $error_message      = 'User is not valid';  
                }
            } else {
                $http_response  = 'http_response_invalid_login';
                $error_message  = 'User is invalid';
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }   


    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : updateAdminUser()
    * @ Added Date               : 14-04-2016
    * @ Added By                 : Subhankar
    * -----------------------------------------------------------------
    * @ Description              : log out admin user
    * -----------------------------------------------------------------
    * @ return                   : array
    * -----------------------------------------------------------------
    * @ Modified Date            : 14-04-2016
    * @ Modified By              : Subhankar
    * 
    */
    public function updateAdminUser_post(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();

        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $req_arr1 = array();
            $plaintext_pass_key = $this->encrypt->decode($this->input->post('pass_key', TRUE));
            $plaintext_admin_id = $this->encrypt->decode($this->input->post('admin_user_id', TRUE));

            $req_arr1['pass_key']        = $plaintext_pass_key;
            $req_arr1['admin_user_id']   = $plaintext_admin_id;
            $check_session  = $this->admin->checkSessionExist($req_arr1);

            if(!empty($check_session) && count($check_session) > 0){
                $req_arr = array();
                //pre($this->post(),1);
                $flag           = true;
                if(empty($this->post('f_name', true))){
                    $flag           = false;
                    $error_message  = "First Name is required";
                } else {
                    $req_arr['f_name']  = $this->post('f_name', TRUE);
                }

                if($flag && empty($this->post('l_name', true))){
                    $flag           = false;
                    $error_message  = "Last Name is required";
                } else {
                    $req_arr['l_name']  = $this->post('l_name', TRUE);
                }                       

                if($flag){

                    $plaintext_admin_id = $this->encrypt->decode($this->post('admin_user_id', TRUE));
                    $where_prof = array('id' => $plaintext_admin_id);
                    
                    $status = $this->admin->editProfile($req_arr, $where_prof);
                    if($status > 0) {
                        $http_response      = 'http_response_ok';
                        $success_message    = 'Profile updated successfully';

                    } else {
                        $http_response      = 'http_response_bad_request';
                        $error_message      = 'Something went wrong in API';
                    }
                }
            } else {
                $http_response  = 'http_response_invalid_login';
                $error_message  = 'User is invalid';
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }


    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : updateAdminPassword()
    * @ Added Date               : 14-04-2016
    * @ Added By                 : Subhankar
    * -----------------------------------------------------------------
    * @ Description              : log out admin user
    * -----------------------------------------------------------------
    * @ return                   : array
    * -----------------------------------------------------------------
    * @ Modified Date            : 14-04-2016
    * @ Modified By              : Subhankar
    * 
    */
    public function updateAdminPassword_post(){

        $error_message = $success_message = $http_response = $new_password = '';
        $result_arr = array();

        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $req_arr = $update_arr = $details_arr = array();
            //pre($this->post(),1);
            $flag           = true;
            if(!trim($this->post('pass_key', true)))
            {
                $flag           = false;
                $error_message  = "Pass Key is required";
            }
            else
            {
                $req_arr['pass_key']            = $this->post('pass_key', true);
            }

            if($flag && !trim($this->post('admin_user_id', true)))
            {
                $flag           = false;
                $error_message  = "Admin User Id is required";
            }
            else
            {
                $req_arr['admin_user_id']            = $this->post('admin_user_id', true);
            }

            if(!trim($this->post('old_password', true))){
                $flag           = false;
                $error_message  = "Old password is required";
            } else {
                $req_arr['old_password'] = $this->post('old_password', TRUE);
            }

            if($flag && !trim($this->post('new_password', true))){
                $flag           = false;
                $error_message  = "New Password is required";
            } else {
                $req_arr['new_password']  = $this->post('new_password', TRUE);
            }

            if($flag && !trim($this->post('confirm_password', true))){
                $flag           = false;
                $error_message  = "Confirm Password is required";
            } else {
                $req_arr['confirm_password']  = $this->post('confirm_password', TRUE);
            }

            if($flag && ($this->post('new_password', true) != $this->post('confirm_password', true)))
            {
                $flag           = false;
                $error_message  = "Confirm Password does not match";
            }

            if($flag){

                $req_arr1 = array(
                    'pass_key'      => $this->encrypt->decode($req_arr['pass_key']),
                    'admin_user_id' => $this->encrypt->decode($req_arr['admin_user_id']),
                );
                $check_session  = $this->admin->checkSessionExist($req_arr1);
                if(!empty($check_session) && count($check_session) > 0)
                {

                    $plaintext_admin_id = $this->encrypt->decode($this->post('admin_user_id', TRUE));
                    $old_password       = md5($this->post('old_password', TRUE));
                    $chek_arr = array(
                        'id'        => $req_arr1['admin_user_id'], 
                        'login_pwd' => md5($req_arr['old_password'])
                    );

                    $details_arr  = $this->admin->checkAdminUser($chek_arr);
                    //echo $this->db->last_query();
                    //pre($req_arr,1); 
                    if(!empty($details_arr) && count($details_arr) > 0) {
                        $update_arr = array(
                            'admin_id' => $req_arr1['admin_user_id'],
                            'password' => md5($req_arr['new_password'])
                        );
                        $status          = $this->admin->changePassword($update_arr);

                        if($status > 0) {
                            $http_response      = 'http_response_ok';
                            $success_message    = 'Password changed successfully';

                        } else {
                            $http_response      = 'http_response_bad_request';
                            $error_message      = 'Something went wrong in API';
                        }
                    }else{
                        $error_message = 'Old Password is wrong';
                        $http_response = 'http_response_bad_request';
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


    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : getAllProfession()
    * @ Added Date               : 14-04-2016
    * @ Added By                 : Subhankar
    * -----------------------------------------------------------------
    * @ Description              : log out admin user
    * -----------------------------------------------------------------
    * @ return                   : array
    * -----------------------------------------------------------------
    * @ Modified Date            : 14-04-2016
    * @ Modified By              : Subhankar
    * 
    */
    public function getAllProfession_post(){   
    
        $error_message = $success_message = $http_response = '';
        $result_arr = array();

        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {
            $req_arr = $details_arr = array();
            $details_arr = $this->admin->getAllProfession();

            if(!empty($details_arr) && count($details_arr) > 0){
                $result_arr         = $details_arr;
                $http_response      = 'http_response_ok';
                $success_message    = 'All Professions';  
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = 'Something went wrong in API';  
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }



    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : getAllPaymentType()
    * @ Added Date               : 14-04-2016
    * @ Added By                 : Subhankar
    * -----------------------------------------------------------------
    * @ Description              : log out admin user
    * -----------------------------------------------------------------
    * @ return                   : array
    * -----------------------------------------------------------------
    * @ Modified Date            : 14-04-2016
    * @ Modified By              : Subhankar
    * 
    */
    public function getAllPaymentType_post(){   
    
        $error_message = $success_message = $http_response = '';
        $result_arr = array();

        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {
            $req_arr = $details_arr = array();
            $details_arr = $this->admin->getAllPaymentType();

            if(!empty($details_arr) && count($details_arr) > 0){
                $result_arr         = $details_arr;
                $http_response      = 'http_response_ok';
                $success_message    = 'All Payment Types';  
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = 'Something went wrong in API';  
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }



    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getAllAdminUsers()
     * @ Added Date               : 09-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : get all user
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 09-09-2016
     * @ Modified By              : Piyalee
     * 
    */
    public function getAllAdminUsers_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) 
        {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } 
        else 
        {
            $req_arr = $details_arr = array();
            $flag           = true;
            if(empty($this->post('pass_key', true)))
            {
                $flag           = false;
                $error_message  = "Pass Key is required";
            }
            else
            {
                $req_arr['pass_key']  = $this->post('pass_key', true);
            }

            if(empty($this->post('admin_user_id', true)))
            {
                $flag           = false;
                $error_message  = "Admin User Id is required";
            }
            else
            {
                $req_arr['admin_user_id']            = $this->post('admin_user_id', true);
            }

            if(empty($this->post('page', true)))
            {
                $flag           = false;
                $error_message  = "Page is required";
            }
            else
            {
                $req_arr['page']            = $this->post('page', true);
            }

            if(empty($this->post('page_size', true)))
            {
                $flag           = false;
                $error_message  = "Page Size is required";
            }
            else
            {
                $req_arr['page_size']            = $this->post('page_size', true);
            }
            $req_arr['order']           = $this->post('order', true);
            $req_arr['order_by']        = $this->post('order_by', true);
            $req_arr['searchByNameEmail']= $this->post('searchByNameEmail', true);
            
            if($flag)
            {
                $check_user = array(
                    'pass_key'      => $this->encrypt->decode($req_arr['pass_key']),
                    'admin_user_id' => $this->encrypt->decode($req_arr['admin_user_id']),
                );
                $checkloginstatus = $this->admin->checkSessionExist($check_user);
                if(!empty($checkloginstatus) && count($checkloginstatus) > 0)
                {

                    $details_arr['dataset']     = $this->admin->getAllAdminUsers($req_arr);
                    $details_arr['count']       = $this->admin->getAllAdminUsersCount($req_arr);

                    $result_arr         = $details_arr;
                    $http_response      = 'http_response_ok';
                    $success_message    = 'All User';
                }
                else
                {
                    $http_response  = 'http_response_invalid_login';
                    $error_message  = 'User is invalid';
                }
            }
            else 
            {
                $http_response      = 'http_response_bad_request';
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }




    public function addAdminUser_post(){
       $error_message = $success_message = $http_response = '';
        $result_arr = array();

        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $req_arr1 = array();
            $plaintext_pass_key = $this->encrypt->decode($this->input->post('pass_key', TRUE));
            $plaintext_admin_id = $this->encrypt->decode($this->input->post('admin_user_id', TRUE));

            $req_arr1['pass_key']        = $plaintext_pass_key;
            $req_arr1['admin_user_id']   = $plaintext_admin_id;
            $check_session  = $this->admin->checkSessionExist($req_arr1);

            if(!empty($check_session) && count($check_session) > 0){
                $req_arr = array();
                //pre($this->post(),1);
                $flag           = true;

                if(empty($this->post('login_email', true))){
                    $flag           = false;
                    $error_message  = "Email is required";
                } else {
                    $req_arr['login_email']  = $this->post('login_email', TRUE);
                }

                if(empty($this->post('login_pwd', true))){
                    $flag           = false;
                    $error_message  = "password is required";
                } else {
                    $req_arr['login_pwd']  = md5($this->post('login_pwd', TRUE));
                }

                if($flag && empty($this->post('f_name', true))){
                    $flag           = false;
                    $error_message  = "First Name is required";
                } else {
                    $req_arr['f_name']  = $this->post('f_name', TRUE);
                }

                if($flag && empty($this->post('l_name', true))){
                    $flag           = false;
                    $error_message  = "Last Name is required";
                } else {
                    $req_arr['l_name']  = $this->post('l_name', TRUE);
                }

                if($flag && empty($this->post('admin_level', true))){
                    $flag           = false;
                    $error_message  = "Last Name is required";
                } else {
                    $req_arr['admin_level']  = $this->post('admin_level', TRUE);
                }



                if($flag){
                    $req_arr['is_active']   =1;

                    $addArry = array();
                    $addArry['login_email'] = $req_arr['login_email'];
                    $addArry['login_pwd']   = $req_arr['login_pwd'];
                    $addArry['f_name']      = $req_arr['f_name'];
                    $addArry['l_name']      = $req_arr['l_name'];
                    $addArry['admin_level'] = $req_arr['admin_level'];

                    $status = $this->admin->addAdminUser($req_arr);
                    if($status > 0) {
                        $http_response      = 'http_response_ok';
                        $success_message    = 'Profile added successfully';

                    } else {
                        $http_response      = 'http_response_bad_request';
                        $error_message      = 'Something went wrong in API';
                    }
                }
            } else {
                $http_response  = 'http_response_invalid_login';
                $error_message  = 'User is invalid';
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }





    
    /****************************end of admin controlller**********************/

}
