<?php defined('BASEPATH') OR exit('No direct script access allowed');

error_reporting(0);
//error_reporting(E_ALL);

require_once('src/OAuth2/Autoloader.php');
require APPPATH . 'libraries/api/REST_Controller.php';



    /**
    *   @SWG\Swagger(
    *       schemes={"https"},
    *       host="apis.finzo.in",
    *       basePath="/api_1_0_0"
    *   ),
    *
    *   @SWG\Info(
    *       title="Profile Module",
    *       description="all api which are related to profiles screen are listed here",
    *       version="2.0.0"
    *   ),
    *   @SWG\Definition(
    *       definition="changePassword",
    *       type="object",
    *       description="API Request Format",
    *       allOf={
    *           @SWG\Schema(
    *               @SWG\Property(property="user_id", format="string", type="integer"),
    *               @SWG\Property(property="pass_key", format="string", type="string"),
    *               @SWG\Property(property="password", format="string", type="integer"),
    *               @SWG\Property(property="confirm_password", format="string", type="string"),
    *             
    *          )
    *       },
    *    )
    *   @SWG\Definition(
    *       definition="changeEmailStep1",
    *       type="object",
    *       description="API Request Format",
    *       allOf={
    *           @SWG\Schema(
    *               @SWG\Property(property="user_id", format="string", type="integer"),
    *               @SWG\Property(property="pass_key", format="string", type="string"),
    *               @SWG\Property(property="email_id", format="string", type="string"),
     *             
    *          )
    *       },
    *    )
    *   @SWG\Definition(
    *       definition="changeEmailStep2",
    *       type="object",
    *       description="API Request Format",
    *       allOf={
    *           @SWG\Schema(
    *               @SWG\Property(property="user_id", format="string", type="integer"),
    *               @SWG\Property(property="pass_key", format="string", type="string"),
    *               @SWG\Property(property="email_id", format="string", type="string"),
    *               @SWG\Property(property="verification_code", format="string", type="string"),
    *             
    *          )
    *       },
    *    )
    *   @SWG\Definition(
    *       definition="changeMobileStep1",
    *       type="object",
    *       description="API Request Format",
    *       allOf={
    *           @SWG\Schema(
    *               @SWG\Property(property="user_id", format="string", type="integer"),
    *               @SWG\Property(property="pass_key", format="string", type="string"),
    *               @SWG\Property(property="mobile_number", format="string", type="integer"),
     *             
    *          )
    *       },
    *    )
    *   @SWG\Definition(
    *       definition="changeMobileStep2",
    *       type="object",
    *       description="API Request Format",
    *       allOf={
    *           @SWG\Schema(
    *               @SWG\Property(property="user_id", format="string", type="integer"),
    *               @SWG\Property(property="pass_key", format="string", type="string"),
    *               @SWG\Property(property="mobile_number", format="string", type="integer"),
    *               @SWG\Property(property="verification_code", format="string", type="string"),
    *             
    *          )
    *       },
    *    )
    *   @SWG\Definition(
    *       definition="addTicket",
    *       type="object",
    *       description="API Request Format",
    *       allOf={
    *           @SWG\Schema(
    *               @SWG\Property(property="user_id", format="string", type="integer"),
    *               @SWG\Property(property="pass_key", format="string", type="string"),
    *               @SWG\Property(property="subject", format="string", type="string"),
    *               @SWG\Property(property="description", format="string", type="string"),
    *             
    *          )
    *       },
    *    )
    *   @SWG\Definition(
    *       definition="fetchTickit",
    *       type="object",
    *       description="API Request Format",
    *       allOf={
    *           @SWG\Schema(
    *               @SWG\Property(property="user_id", format="string", type="integer"),
    *               @SWG\Property(property="pass_key", format="string", type="string"),
    *             
    *          )
    *       },
    *    )
    *   @SWG\Definition(
    *       definition="fetchTickitDetails",
    *       type="object",
    *       description="API Request Format",
    *       allOf={
    *           @SWG\Schema(
    *               @SWG\Property(property="user_id", format="string", type="integer"),
    *               @SWG\Property(property="pass_key", format="string", type="string"),
    *              @SWG\Property(property="ticket_id", format="string", type="integer"),
    *             
    *          )
    *       },
    *    )
    *   @SWG\Definition(
    *       definition="addTickitThreads",
    *       type="object",
    *       description="API Request Format",
    *       allOf={
    *           @SWG\Schema(
    *               @SWG\Property(property="user_id", format="string", type="integer"),
    *               @SWG\Property(property="pass_key", format="string", type="string"),
    *              @SWG\Property(property="ticket_id", format="string", type="integer"),
    *             
    *          )
    *       },
    *    )
    **/

class Settings extends REST_Controller{
     function __construct()
    {

        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: authorization, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header('Access-Control-Allow-Credentials: true');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if($method == "OPTIONS") {
         die();
        }

        parent::__construct();
            $this->load->config('rest');
            $this->load->config('serverconfig');
            $developer = 'www.massoftind.com';
            $this->app_path = "api_" . $this->config->item('test_api_ver');
            //publish app version
            $version = str_replace('_', '.', $this->config->item('test_api_ver'));

            $this->publish = array(
                'version' => $version,
                'developer' => $developer
            );
            
            //echo $_SERVER['SERVER_ADDR']; exit;
            
            $dsn = 'mysql:dbname='.$this->config->item('oauth_db_database').';host='.$this->config->item('oauth_db_host');
            $dbusername = $this->config->item('oauth_db_username');
            $dbpassword = $this->config->item('oauth_db_password');
           

            $sitemode= $this->config->item('site_mode');
            $this->path_detail=$this->config->item($sitemode);      
            
            $this->load->model('api_' . $this->config->item('test_api_ver') . '/user_model', 'user_model');
            $this->load->model('api_' . $this->config->item('test_api_ver') . '/settings_model', 'settings_model');
            $this->load->model('api_' . $this->config->item('test_api_ver') . '/profile_model', 'profile_model');
            $this->load->model('api_' . $this->config->item('test_api_ver') . '/login_model', 'login_model');
            
            $this->load->library('email');
           
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



    /**
     *  
     *   @SWG\Post(
     *      path="/settings/changePassword",
     *      tags={"Profile: "},
     *      summary="change Password",
     *      description="This api is used to change password",
     *      produces={"application/json"},
     *   
     *      @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          description="Authorization Token",
     *          required=true,
     *          type="string",
     *      ),
     *      @SWG\Parameter(
     *          name="data",
     *          in="body",
     *          description="Post data to change password",
     *          required=true,
     *          type="string",
     *          @SWG\Schema(ref="#/definitions/changePassword"),
     *      ),       
     *  )
     *
    **/ 

    public function changePassword_post(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('pass_key')){
                $flag       = false;
                $error_message='passkey can not be null';
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }
            if (!intval($this->post('user_id'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

            if (!$this->post('password')){
                $flag       = false;
                $error_message='Please enter password';
            } else {
                $req_arr['password']    = $this->post('password', TRUE);
            }

            if (!$this->post('confirm_password')){
                $flag       = false;
                $error_message='Please enter confirm password';
            } else {
                $req_arr['confirm_password']    = $this->post('confirm_password', TRUE);
            }

            if($req_arr['password'] !=$req_arr['confirm_password']){
                $flag       = false;
                $error_message='Password & Confirm Password does not match';
            }

            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                //echo $this->db->last_query(); //exit;
                //pre($login_status_arr,1);

                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                    
                    $user_details_arr   = $this->user_model->update_password($req_arr);
                    
                        $result_arr         = $user_details_arr;
                        $http_response      = 'http_response_ok';
                        $success_message    = 'Password Updated successfully';  
                   
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Invalid user details'; 
                }
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
            }
        }

        $raws = array();   
        if($error_message != ''){
            $raws['error_message']      = $error_message;
        } else{
            $raws['success_message']    = $success_message;
        }        

        $raws['data']       = $result_arr;
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 
    }  

    /**
     *  
     *   @SWG\Post(
     *      path="/settings/changeEmailStep1",
     *      tags={"Profile: "},
     *      summary="change Password",
     *      description="This api is used to change email id step 1 ",
     *      produces={"application/json"},
     *   
     *      @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          description="Authorization Token",
     *          required=true,
     *          type="string",
     *      ),
     *      @SWG\Parameter(
     *          name="data",
     *          in="body",
     *          description="Post data to change email id step 1",
     *          required=true,
     *          type="string",
     *          @SWG\Schema(ref="#/definitions/changeEmailStep1"),
     *      ),       
     *  )
     *
    **/ 

    public function changeEmailStep1_post(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('pass_key')){
                $flag       = false;
                $error_message='passkey can not be null';
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }
            if (!intval($this->post('user_id'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

            if (!$this->post('email_id')){
                $flag       = false;
                $error_message='Please enter email address';
            } else {
                $req_arr['email_id']    = $this->post('email_id', TRUE);
            }

            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                //echo $this->db->last_query(); //exit;
                //pre($login_status_arr,1);

                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                    
                    //save verofication code to email
                        $row['fk_user_id']=$req_arr['user_id'];
                        //$verifyCode=$this->getVerificationCode();
                         $verifyCode = mt_rand(100000, 999999);
                        $emailVerifyCode=strtoupper('E-'.$verifyCode);
                        $row['verification_type']='E';
                        $row['verification_code']=$emailVerifyCode;
                        $this->user_model->addUserVerificationCode($row);
                        //send email
                        //initialising codeigniter email
                         $email_config=email_config();
                        $this->email->initialize($email_config);
                         // email sent to user 
                        $admin_email= $this->config->item('admin_email');
                        $admin_email_from= $this->config->item('admin_email_from');
                        $this->email->from($admin_email, $admin_email_from);
                        $this->email->to($req_arr['email_id']);          
                        $this->email->subject('Email Verification');

                        $verify_encrypt_code=$emailVerifyCode.'_'.$req_arr['user_id'].'_'.$req_arr['email_id'];
                        $encrypted_code=setEncryption($verify_encrypt_code);
                        $encrp_base=urlencode(base64_encode($encrypted_code));
                        $email_data['verification_link']= $encrp_base;
                        $email_data['verification_code']= $emailVerifyCode;
                        
                        

                        $email_body= $this->parser->parse('email_templates/changeEmailverification', $email_data, true);


                         $this->email->message($email_body);            
                         $this->email->send();

                        // email send end
                        //save verofication code to email
                    
                        $result_arr         = $user_details_arr;
                        $http_response      = 'http_response_ok';
                        $success_message    = 'Email has been sent';  
                   
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Invalid user details'; 
                }
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
            }
        }

        $raws = array();   
        if($error_message != ''){
            $raws['error_message']      = $error_message;
        } else{
            $raws['success_message']    = $success_message;
        }        

        $raws['data']       = $result_arr;
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 
    }


    public function editEmail_post(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('pass_key')){
                $flag       = false;
                $error_message='passkey can not be null';
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }
            if (!intval($this->post('user_id'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

            if (!$this->post('email_id')){
                $flag       = false;
                $error_message='Please enter email address';
            } else {
                $req_arr['email_id']    = $this->post('email_id', TRUE);
            }

            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                //echo $this->db->last_query(); //exit;
                //pre($login_status_arr,1);

                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                    
                    //save verofication code to email
                        $row['fk_user_id']=$req_arr['user_id'];
                        //$verifyCode=$this->getVerificationCode();
                        $verifyCode = mt_rand(100000, 999999);
                        $emailVerifyCode=strtoupper('E-'.$verifyCode);
                        $row['verification_type']='E';
                        $row['verification_code']=$emailVerifyCode;
                        $this->user_model->addUserVerificationCode($row);

                        $verify_data['id']=$req_arr['user_id'];
                        $verify_data['email_id']=$req_arr['email_id'];
                        $this->user_model->editEmail($verify_data);
                        //send email
                        //initialising codeigniter email
                         $email_config=email_config();
                        $this->email->initialize($email_config);
                         // email sent to user 
                        $admin_email= $this->config->item('admin_email');
                        $admin_email_from= $this->config->item('admin_email_from');
                        $this->email->from($admin_email, $admin_email_from);
                        $this->email->to($req_arr['email_id']);          
                        $this->email->subject('Email Verification');

                        $verify_encrypt_code=$emailVerifyCode.'_'.$req_arr['user_id'];
                        $encrypted_code=setEncryption($verify_encrypt_code);
                        $encrp_base=urlencode(base64_encode($encrypted_code));
                        $email_data['verification_link']= $encrp_base;
                        $email_data['verification_code']= $emailVerifyCode;
                        
                        

                        $email_body= $this->parser->parse('email_templates/signupemailverification', $email_data, true);


                         $this->email->message($email_body);            
                         $this->email->send();

                        // email send end
                        //save verofication code to email
                    
                        $result_arr         = $user_details_arr;
                        $http_response      = 'http_response_ok';
                        $success_message    = 'Email has been sent';  
                   
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Invalid user details'; 
                }
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
            }
        }

        $raws = array();   
        if($error_message != ''){
            $raws['error_message']      = $error_message;
        } else{
            $raws['success_message']    = $success_message;
        }        

        $raws['data']       = $result_arr;
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 
    }
    

    /**
     *  
     *   @SWG\Post(
     *      path="/settings/changeEmailStep2",
     *      tags={"Profile: "},
     *      summary="change Password",
     *      description="This api is used to change email id step 2",
     *      produces={"application/json"},
     *   
     *      @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          description="Authorization Token",
     *          required=true,
     *          type="string",
     *      ),
     *      @SWG\Parameter(
     *          name="data",
     *          in="body",
     *          description="Post data to change email id step 2",
     *          required=true,
     *          type="string",
     *          @SWG\Schema(ref="#/definitions/changeEmailStep2"),
     *      ),       
     *  )
     *
    **/ 

    public function changeEmailStep2_post(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('pass_key')){
                $flag       = false;
                $error_message='passkey can not be null';
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }
            if (!intval($this->post('user_id'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

            if (!$this->post('email_id')){
                $flag       = false;
                $error_message='Please enter email address';
            } else {
                $req_arr['email_id']    = $this->post('email_id', TRUE);
            }

            $req_arr['verification_code']    = $this->post('verification_code', TRUE);

         

            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                //echo $this->db->last_query(); //exit;
                //pre($login_status_arr,1);

                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                    if($req_arr['verification_code']!=''){
                    //save verofication code to email
                       $row['fk_user_id']=$req_arr['user_id'];
                       $row['verification_code']=$req_arr['verification_code'];
                       $row['verification_type']='E';
                        //log in status checking
                        $is_verify       = $this->user_model->checkVerificationCode($row);
                        if($is_verify>0){
                            $verify_data['id']=$req_arr['user_id'];
                            $verify_data['email_id']=$req_arr['email_id'];
                            $this->user_model->chnageEmail($verify_data);
                            $userDetails=$this->user_model->fetchUserDeatils($req_arr['user_id']);
                            $email_id=$userDetails['email_id'];
                            $history['fk_user_id']=$req_arr['user_id'];
                            $history['email_id']=$email_id;
                            $this->user_model->addHistoryEmail($history);
                            $http_response      = 'http_response_ok';
                            $success_message    = '';  
                            
                        }else {
                            $http_response      = 'http_response_bad_request';
                            $error_message      = 'Invalid Verification Code ';
                           
                        }
                    }else{
                        $userDetails=$this->user_model->fetchUserDeatils($req_arr['user_id']);
                         if($userDetails['is_email_id_verified']=='1'){
                            $http_response      = 'http_response_ok';
                            $success_message    = '';  
                            
                        }else{
                            $http_response      = 'http_response_bad_request';
                            $error_message      = 'Invalid Verification Code ';
                           
                        }
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

        $raws = array();   
        if($error_message != ''){
            $raws['error_message']      = $error_message;
        } else{
            $raws['success_message']    = $success_message;
        }        

        $raws['data']       = $result_arr;
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 
    }
    


    /**
     *  
     *   @SWG\Post(
     *      path="/settings/changeMobileStep1",
     *      tags={"Profile: "},
     *      summary="change Password",
     *      description="This api is used to change mobile no step 1",
     *      produces={"application/json"},
     *   
     *      @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          description="Authorization Token",
     *          required=true,
     *          type="string",
     *      ),
     *      @SWG\Parameter(
     *          name="data",
     *          in="body",
     *          description="Post data to change mobile no step 1",
     *          required=true,
     *          type="string",
     *          @SWG\Schema(ref="#/definitions/changeMobileStep1"),
     *      ),       
     *  )
     *
    **/ 

    public function changeMobileStep1_post(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('pass_key')){
                $flag       = false;
                $error_message='passkey can not be null';
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }
            if (!intval($this->post('user_id'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

            if (!$this->post('mobile_number')){
                $flag       = false;
                $error_message='Please enter mobile number';
            } else {
                $req_arr['mobile_number']    = $this->post('mobile_number', TRUE);
            }

            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                //echo $this->db->last_query(); //exit;
                //pre($login_status_arr,1);

                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                    
                    //save verofication code to email
                        $row['fk_user_id']=$req_arr['user_id'];
                        //$verifyCode=$this->getVerificationCode();
                        $verifyCode = mt_rand(100000, 999999);
                        $mobileVerifyCode=strtoupper('M-'.$verifyCode);
                        $row['verification_type']='M';
                        $row['verification_code']=$mobileVerifyCode;
                        $this->user_model->addUserVerificationCode($row);
                        //send sms
                        //initialising codeigniter email
                        $this->sms->category="MOBVER";
                        $this->sms->code = $mobileVerifyCode;
                        $this->sms->mobile = $req_arr['mobile_number'];
                        $response = $this->sms->sendSmsFinal();

                        // sms send end
                        //save verofication code to email
                    
                        $result_arr         = $user_details_arr;
                        $http_response      = 'http_response_ok';
                        $success_message    = 'Verification SMS has been sent';  
                   
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Invalid user details'; 
                }
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
            }
        }

        $raws = array();   
        if($error_message != ''){
            $raws['error_message']      = $error_message;
        } else{
            $raws['success_message']    = $success_message;
        }        

        $raws['data']       = $result_arr;
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 
    }
    

    /**
     *  
     *   @SWG\Post(
     *      path="/settings/changeMobileStep2",
     *      tags={"Profile: "},
     *      summary="change Password",
     *      description="This api is used to change mobile no step 2",
     *      produces={"application/json"},
     *   
     *      @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          description="Authorization Token",
     *          required=true,
     *          type="string",
     *      ),
     *      @SWG\Parameter(
     *          name="data",
     *          in="body",
     *          description="Post data to change mobile no step 2",
     *          required=true,
     *          type="string",
     *          @SWG\Schema(ref="#/definitions/changeMobileStep2"),
     *      ),       
     *  )
     *
    **/ 

     public function editMobile_post(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('pass_key')){
                $flag       = false;
                $error_message='passkey can not be null';
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }
            if (!intval($this->post('user_id'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

            if (!$this->post('mobile_number')){
                $flag       = false;
                $error_message='Please enter mobile number';
            } else {
                $req_arr['mobile_number']    = $this->post('mobile_number', TRUE);
            }

            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                //echo $this->db->last_query(); //exit;
                //pre($login_status_arr,1);

                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                    
                    //save verofication code to email
                        $row['fk_user_id']=$req_arr['user_id'];
                        //$verifyCode=$this->getVerificationCode();
                        $verifyCode = mt_rand(100000, 999999);
                        $mobileVerifyCode=strtoupper('M-'.$verifyCode);
                        $row['verification_type']='M';
                        $row['verification_code']=$mobileVerifyCode;
                        $this->user_model->addUserVerificationCode($row);

                        $verify_data['id']=$req_arr['user_id'];
                        $verify_data['mobile_number']=$req_arr['mobile_number'];
                        $this->user_model->editMobile($verify_data);
                        //send sms
                        //initialising codeigniter email
                        $this->sms->category="MOBVER";
                        $this->sms->code = $mobileVerifyCode;
                        $this->sms->mobile = $req_arr['mobile_number'];
                        $response = $this->sms->sendSmsFinal();


                        // sms send end
                        //save verofication code to email
                    
                        $result_arr         = $user_details_arr;
                        $http_response      = 'http_response_ok';
                        $success_message    = 'Verification SMS has been sent';  
                   
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Invalid user details'; 
                }
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
            }
        }

        $raws = array();   
        if($error_message != ''){
            $raws['error_message']      = $error_message;
        } else{
            $raws['success_message']    = $success_message;
        }        

        $raws['data']       = $result_arr;
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 
    }

    public function changeMobileStep2_post(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('pass_key')){
                $flag       = false;
                $error_message='passkey can not be null';
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }
            if (!intval($this->post('user_id'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

            if (!$this->post('mobile_number')){
                $flag       = false;
                $error_message='Please enter mobile number';
            } else {
                $req_arr['mobile_number']    = $this->post('mobile_number', TRUE);
            }

            if (!$this->post('verification_code')){
                $flag       = false;
                $error_message='Please enter verification code';
            } else {
                $req_arr['verification_code']    = $this->post('verification_code', TRUE);
            }

         

            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                //echo $this->db->last_query(); //exit;
                //pre($login_status_arr,1);

                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                    
                    //save verofication code to email
                       $row['fk_user_id']=$req_arr['user_id'];
                       $row['verification_code']=$req_arr['verification_code'];
                       $row['verification_type']='M';
                        //log in status checking
                        $is_verify       = $this->user_model->checkVerificationCode($row);
                        if($is_verify>0){
                            $verify_data['id']=$req_arr['user_id'];
                            $verify_data['mobile_number']=$req_arr['mobile_number'];
                            $this->user_model->chnageMobile($verify_data);

                            $userDetails=$this->user_model->fetchUserDeatils($req_arr['user_id']);
                            $mobile_number=$userDetails['mobile_number'];
                            $history['fk_user_id']=$req_arr['user_id'];
                            $history['mobile_number']=$mobile_number;
                            $this->user_model->addHistoryMobile($history);

                            $http_response      = 'http_response_ok';
                            $success_message    = '';  
                            
                        }else {
                            $http_response      = 'http_response_bad_request';
                            $error_message      = 'Invalid Verification Code ';
                           
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

        $raws = array();   
        if($error_message != ''){
            $raws['error_message']      = $error_message;
        } else{
            $raws['success_message']    = $success_message;
        }        

        $raws['data']       = $result_arr;
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 
    }


    /**
     *  
     *   @SWG\Post(
     *      path="/settings/addTicket",
     *      tags={"Profile: "},
     *      summary="add ticket",
     *      description="This api is used to add ticket",
     *      produces={"application/json"},
     *   
     *      @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          description="Authorization Token",
     *          required=true,
     *          type="string",
     *      ),
     *      @SWG\Parameter(
     *          name="data",
     *          in="body",
     *          description="Post data to add ticket",
     *          required=true,
     *          type="string",
     *          @SWG\Schema(ref="#/definitions/addTicket"),
     *      ),       
     *  )
     *
    **/ 

    public function addTicket_post(){


        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('pass_key')){
                $flag       = false;
                $error_message='passkey can not be null';
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }
            if (!intval($this->post('user_id'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
                $data['fk_user_id']= $this->post('user_id', TRUE);
            }

            if (trim($this->post('subject'))==''){
                $flag       = false;
                $error_message='Please enter subject';
            } else {
                $req_arr['subject']    = $this->post('subject', TRUE);
                $data['title']= $this->post('subject', TRUE);
            }

            if (trim($this->post('description'))==''){
                $flag       = false;
                $error_message='Please enter description';
            } else {
                $req_arr['description']    = $this->post('description', TRUE);
               
            }

            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                
                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                        
                        $tick_id=$this->settings_model->addTicket($data);
                        $ticket_no='MPK-'.date('Ymd').'-'.str_pad($tick_id, 6, '0', STR_PAD_LEFT);
                        $this->settings_model->updateTickitID($ticket_no,$tick_id);
                        $row['fk_support_ticket_id']=$tick_id;
                        $row['fk_user_id']=$data['fk_user_id'];
                        $row['description']=$req_arr['description'];
                        $this->settings_model->addTickeThreads($row);


                        // sms send end
                        //save verofication code to email
                        $tckt['ticket_no']=$ticket_no;
                        $result_arr         = $tckt;
                        $http_response      = 'http_response_ok';
                        $success_message    = 'SMS has been sent';  
                   
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Invalid user details'; 
                }
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
            }
        }

        $raws = array();   
        if($error_message != ''){
            $raws['error_message']      = $error_message;
        } else{
            $raws['success_message']    = $success_message;
        }        

        $raws['data']       = $result_arr;
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 


    }
    /**
     *  
     *   @SWG\Post(
     *      path="/settings/fetchTickit",
     *      tags={"Profile: "},
     *      summary="add ticket",
     *      description="This api is used to fetch ticket",
     *      produces={"application/json"},
     *   
     *      @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          description="Authorization Token",
     *          required=true,
     *          type="string",
     *      ),
     *      @SWG\Parameter(
     *          name="data",
     *          in="body",
     *          description="Post data to fetch ticket",
     *          required=true,
     *          type="string",
     *          @SWG\Schema(ref="#/definitions/fetchTickit"),
     *      ),       
     *  )
     *
    **/
    public function fetchTickit_post(){


        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('pass_key')){
                $flag       = false;
                $error_message='passkey can not be null';
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }
            if (!intval($this->post('user_id'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
                $data['fk_user_id']= $this->post('user_id', TRUE);
            }

           
             $raws = array();   
            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                
                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                        
                        $all_ticket=$this->settings_model->fetchAllTicket($data['fk_user_id']);
                        if(is_array($all_ticket) && count($all_ticket)>0){
                            $i=0;
                            foreach($all_ticket as $ticket){
                               
                                $ticket_details[$i]['id']=$ticket['id'];
                                $ticket_details[$i]['ticket_id']=$ticket['ticket_id'];
                                $ticket_details[$i]['fk_user_id']=$ticket['fk_user_id'];
                                $ticket_details[$i]['title']=$ticket['title'];
                                $ticket_details[$i]['status']=$ticket['status'];
                                $added_timestamp=$this->user_model->getServerTimeZone($req_arr['user_id'],$ticket['added_timestamp']);
                                $dataTime=strtotime($added_timestamp);
                                $showTimeFormat=date('M d,Y h:i A',$dataTime);
                                $ticket_details[$i]['added_timestamp']=$showTimeFormat;
                                $i++;

                            }
                        }
                         $details['ticket_details']=$ticket_details;
                         $details['no_ticket']=count($ticket_details);
                        // sms send end
                        //save verofication code to email
                        $raws['dataset'] =$details;
                        $result_arr         = $user_details_arr;
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

       
        if($error_message != ''){
            $raws['error_message']      = $error_message;
        } else{
            $raws['success_message']    = $success_message;
        }        

        
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 


    }
    /**
     *  
     *   @SWG\Post(
     *      path="/settings/fetchTickitDetails",
     *      tags={"Profile: "},
     *      summary="add ticket",
     *      description="This api is used to fetch ticket details",
     *      produces={"application/json"},
     *   
     *      @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          description="Authorization Token",
     *          required=true,
     *          type="string",
     *      ),
     *      @SWG\Parameter(
     *          name="data",
     *          in="body",
     *          description="Post data to fetch ticket details",
     *          required=true,
     *          type="string",
     *          @SWG\Schema(ref="#/definitions/fetchTickitDetails"),
     *      ),       
     *  )
     *
    **/
    public function fetchTickitDetails_post(){


        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('pass_key')){
                $flag       = false;
                $error_message='passkey can not be null';
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }

            if (!intval($this->post('user_id'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
                $data['user_id']= $this->post('user_id', TRUE);
            }

             if (!intval($this->post('ticket_id'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['ticket_id']    = $this->post('ticket_id', TRUE);
                $data['id']= $this->post('ticket_id', TRUE);
            }

           
             $raws = array();   
            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                
                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                        
                        $all_ticket=$this->settings_model->fetchTicketConversation($data);
                        if(is_array($all_ticket) && count($all_ticket)>0){
                            $i=0;
                            foreach($all_ticket as $ticket){
                                $ticket_details[$i]['id']=$ticket['id'];
                                $ticket_details[$i]['ticket_id']=$ticket['ticket_id'];
                                $ticket_details[$i]['fk_user_id']=$ticket['fk_user_id'];
                                $ticket_details[$i]['fk_admin_id']=$ticket['fk_admin_id'];
                                $ticket_details[$i]['title']=$ticket['title'];
                                $ticket_details[$i]['description']=$ticket['description'];
                                $ticket_details[$i]['status']=$ticket['status'];

                                $added_timestamp=$this->user_model->getServerTimeZone($req_arr['user_id'],$ticket['added_timestamp']);

                                $dataTime=strtotime($added_timestamp);
                                $showTimeFormat=date('M d,Y h:i A',$dataTime);
                                $ticket_details[$i]['added_timestamp']=$showTimeFormat;
                                $i++;

                            }
                        }
                        $details['ticket_details']=$ticket_details;
                        // sms send end
                        //save verofication code to email
                        $raws['dataset'] =$details;
                        $result_arr         = $user_details_arr;
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

       
        if($error_message != ''){
            $raws['error_message']      = $error_message;
        } else{
            $raws['success_message']    = $success_message;
        }        

       
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 


    }
    /**
     *  
     *   @SWG\Post(
     *      path="/settings/addTickitThreads",
     *      tags={"Profile: "},
     *      summary="add ticket",
     *      description="This api is used to add ticket threads",
     *      produces={"application/json"},
     *   
     *      @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          description="Authorization Token",
     *          required=true,
     *          type="string",
     *      ),
     *      @SWG\Parameter(
     *          name="data",
     *          in="body",
     *          description="Post data to add ticket threads",
     *          required=true,
     *          type="string",
     *          @SWG\Schema(ref="#/definitions/fetchTickitDetails"),
     *      ),       
     *  )
     *
    **/
    public function addTickitThreads_post(){


        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('pass_key')){
                $flag       = false;
                $error_message='passkey can not be null';
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }

            if (!intval($this->post('user_id'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
                $data['fk_user_id']= $this->post('user_id', TRUE);
            }

            if (!$this->post('ticket_id')){
                $flag       = false;
                $error_message='Please enter ticket id';
            } else {
                $req_arr['ticket_id']    = $this->post('ticket_id', TRUE);
                $data['fk_support_ticket_id']= $this->post('ticket_id', TRUE);
            }

            if (!$this->post('description')){
                $flag       = false;
                $error_message='Please enter description';
            } else {
                $req_arr['description']    = $this->post('description', TRUE);
                $data['description']    = $this->post('description', TRUE);
            }

            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                
                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                        
                        
                        $this->settings_model->addTickeThreads($data);


                        // sms send end
                        //save verofication code to email
                    
                       
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

        $raws = array();   
        if($error_message != ''){
            $raws['error_message']      = $error_message;
        } else{
            $raws['success_message']    = $success_message;
        }        

       
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 


    }

    public function getVerificationCode(){
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
    
   

    public function ensure_length(&$string, $length) {
        $strlen = strlen($string);
        if ($strlen < $length) {
            $string = str_pad($string, $length, "0");
        } else if ($strlen > $length) {
            $string = substr($string, 0, $length);
        }
    }

    public function create_guid_section($characters) {
        $return = "";
        for ($i = 0; $i < $characters; $i++) {
            $return .= dechex(mt_rand(0, 15));
        }
        return $return;
    }

    function test_get(){

        $this->sms->category="TESTMSG";
        $this->sms->ignore = 'Mousumi';
        $this->sms->mobile = '9874314610';
        $response = $this->sms->sendSmsFinal();
        //print_r($response);
    }    
    
    /* -------------------------------------

    //end of user controller
    */
}