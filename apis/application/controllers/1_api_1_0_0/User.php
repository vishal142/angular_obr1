<?php defined('BASEPATH') OR exit('No direct script access allowed');

error_reporting(0);
//error_reporting(E_ALL);

require_once('src/OAuth2/Autoloader.php');
require APPPATH . 'libraries/api/REST_Controller.php';
require APPPATH . 'libraries/api/AppExtrasAPI.php';
require APPPATH . 'libraries/api/AppAndroidGCMPushAPI.php';
require APPPATH . 'libraries/api/AppApplePushAPI.php';


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


        *
        *    @SWG\Definition(
        *   definition="Register",
        *   type="object",
        *   description="API Request Format",
        *   allOf={
        *     @SWG\Schema(
        *   @SWG\Property(property="email_id", format="string", type="string"),
        *   @SWG\Property(property="mobile_number", format="string", type="string"),
        *   @SWG\Property(property="login_pwd", format="string", type="string"),
        *   @SWG\Property(property="social_type", format="enum", type="string"),
        *   @SWG\Property(property="login_id", format="string", type="string"),
        *   @SWG\Property(property="account_id", format="string", type="string"),
        *   @SWG\Property(property="user_access_token", format="string", type="string"),
        *       )
        *   },
            ),
        *    @SWG\Definition(
        *   definition="emailVerify",
        *   type="object",
        *   description="API Request Format",
        *   allOf={
        *     @SWG\Schema(
        *   @SWG\Property(property="email_id", format="string", type="string"),
        *  
        *       )
        *   },
            )
        *    @SWG\Definition(
        *   definition="mobileVerify",
        *   type="object",
        *   description="API Request Format",
        *   allOf={
        *     @SWG\Schema(
        *   
        *   @SWG\Property(property="mobile_number", format="string", type="string"),
        *   
        *       )
        *   },
        *    )
        *

             @SWG\Definition(
        *   definition="login",
        *   type="object",
        *   description="API Request Format",
        *   allOf={
        *     @SWG\Schema(
        *   
        *   @SWG\Property(property="username", format="string", type="string"),
        *   @SWG\Property(property="password", format="string", type="string"),
        *   @SWG\Property(property="appname", format="string", type="string"),
        *   @SWG\Property(property="device_uid", format="string", type="string"),
        *   @SWG\Property(property="device_token", format="string", type="string"),
        *   @SWG\Property(property="device_name", format="string", type="string"),
        *   @SWG\Property(property="device_model", format="string", type="string"),
        *   @SWG\Property(property="device_version", format="string", type="string"),
        *   @SWG\Property(property="device_os", format="string", type="string"),
        *   @SWG\Property(property="push_mode", format="string", type="string"),
        *   @SWG\Property(property="appversion", format="string", type="string"),
        *   @SWG\Property(property="timezone", format="string", type="string"),
        *       )
        *   },
            )

        
        *   @SWG\Definition(
        *       definition="logout",
        *       type="object",
        *       description="API Request Format",
        *       allOf={
        *           @SWG\Schema(           
        *               @SWG\Property(property="pass_key", format="string", type="string"),
        *               @SWG\Property(property="user_id", format="string", type="integer"),
        *           )
        *       },
        *   )


        @SWG\Definition(
        *       definition="resendSignupVerification",
        *       type="object",
        *       description="API Request Format",
        *       allOf={
        *           @SWG\Schema(           
        *               @SWG\Property(property="email_id", format="string", type="string"),
        *              
        *           )
        *       },
        *   )
        *    @SWG\Definition(
        *       definition="updateUserModeIsAgent",
        *       type="object",
        *       description="API Request Format",
        *       allOf={
        *           @SWG\Schema(           
        *               @SWG\Property(property="pass_key", format="string", type="string"),
        *               @SWG\Property(property="user_id", format="string", type="integer"),
        *               @SWG\Property(property="user_mode", format="string", type="string"),
        *               @SWG\Property(property="referral_code", format="string", type="integer"),
        *              
        *           )
        *       },
        *   )
        *    @SWG\Definition(
        *       definition="updateProfessionType",
        *       type="object",
        *       description="API Request Format",
        *       allOf={
        *           @SWG\Schema(           
        *               @SWG\Property(property="pass_key", format="string", type="string"),
        *               @SWG\Property(property="user_id", format="string", type="integer"),
        *               @SWG\Property(property="fk_profession_type_id", format="string", type="integer"),
        *              
        *           )
        *       },
        *   )

        *   @SWG\Definition(
        *       definition="forgot_password_step1",
        *       type="object",
        *       description="API Request Format",
        *       allOf={
        *           @SWG\Schema(           
        *               @SWG\Property(property="email_phone", format="string", type="string"),
        *           )
        *       },
        *   )

        *   @SWG\Definition(
        *       definition="forgot_password_step2",
        *       type="object",
        *       description="API Request Format",
        *       allOf={
        *           @SWG\Schema(           
        *               @SWG\Property(property="passcode", format="string", type="string"),
        *               @SWG\Property(property="user_id", format="string", type="integer"),
        *           )
        *       },
        *   )

        *   @SWG\Definition(
        *       definition="forgot_password_step3",
        *       type="object",
        *       description="API Request Format",
        *       allOf={
        *           @SWG\Schema(           
        *               @SWG\Property(property="new_password", format="string", type="string"),
        *               @SWG\Property(property="confirm_password", format="string", type="string"),
        *               @SWG\Property(property="passcode", format="string", type="string"),
        *               @SWG\Property(property="user_id", format="string", type="integer"),
        *           )
        *       },
        *   )

**/

class User extends REST_Controller
{
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
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/profile_model', 'profile_model');
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/agentcode_model', 'agentcode_model');
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/notifications_model', 'notifications_model');
        
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
     *   path="/user/register",
     *   tags={"register"},
     *   summary="register user",
     *   description="This api is used to register by email",
     *   produces={"application/json"},
        
          @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="id of users table",
     *     required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="id of users table",
     *     required=true,
     *     type="string",
            @SWG\Schema(ref="#/definitions/Register"),
     *   ),
        
     *   @SWG\Response(response="200",description="For SUCCESS Response action_status is true, for ERROR response ACTION_STATUS is false",@SWG\Schema(ref="#/definitions/ApiResponseFormatDataset")
     ),
     security={{"oauth2": {"scope"}}}

     * )
     */  

    public function register_post() {
         if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $data['error_message'] = "Invalid Token";
            $this->response(array(
                'raws' => array(
                    'error_message' => $data['error_message'],
                    'publish' => $this->publish
                )
                    ), $this->config->item('http_response_unauthorized'));
            } else {

                $flag = 0;
                $data = array();

                if (!$this->post('email_id')) {
                    $flag = $flag + 1;
                    $error_message='Please enter email address';
                } else {
                    $data['email_id'] = $this->post('email_id', TRUE);
                    
                }

                $data['mobile_number'] = $this->post('mobile_number', TRUE);

                if (!$this->post('login_pwd')) {
                    $flag = $flag + 1;
                    $error_message='Please enter password';
                } else {
                    $data['login_pwd'] = $this->post('login_pwd', TRUE);
                }

                $data['social_type'] = $this->post('social_type', TRUE);
                $data['login_id'] = $this->post('login_id', TRUE);

                if ($data['social_type']!='' && $data['login_id']=='') {
                    $flag = $flag + 1;
                    $error_message='Please enter social type';
                } else {
                    $data['login_id'] = $this->post('login_id', TRUE);
                }
                $data['account_id'] = $this->post('account_id', TRUE);
                if ($data['social_type']!='' && $data['account_id']=='') {
                    $flag = $flag + 1;
                    $error_message='Please enter account id';
                } else {
                    $data['account_id'] = $this->post('account_id', TRUE);
                }
                $data['user_access_token'] = $this->post('user_access_token', TRUE);

                if ($data['social_type']!='' && $data['user_access_token']=='') {
                    $flag = $flag + 1;
                    $error_message='Please enter access token';
                } else {
                    $data['user_access_token'] = $this->post('user_access_token', TRUE);
                }
                 

                if ($flag == 0) {
 
                    $data_error_flag=0;
                    $is_email_exist=$this->user_model->check_emailid($data['email_id']);
                    $is_mobile_exist=$this->user_model->check_mobileno($data['mobile_number']);

                    $is_permission=$this->user_model->checkEmailDataCollection($data['email_id']);

                    if($is_permission>0){
                        $data_error_flag=0;
                        
                    }else{
                        $data_error_flag=1;
                        $data['error_message']='mPokket is currently by invitation only';
                    }

                    if($is_email_exist>0){
                        $data_error_flag=1;
                        $data['error_message']='Email id is already exists';
                    }

                    if($is_mobile_exist>0){
                        $data_error_flag=1;
                        $data['error_message']='Mobile no is already exists';
                    }

                   
                    
                    if($data_error_flag==0){
                            
                            $todaysData=$this->user_model->checkTodayData();
                            if(is_array($todaysData) && count($todaysData)){
                                $totTodaysData=count($todaysData);
                            }else{
                                $totTodaysData=0;
                            }
                            $datacount=10000+intval($totTodaysData)+1;
                            $custId=date('ymd').$datacount;
                            $data['customer_id']=$custId;
                        $this->user_model->addUser($data);
                        //get user_details
                        $data['username']=$data['email_id'];
                        $user_dtl=$this->user_model->fetchUser($data);
                        $user_id=$user_dtl['id'];
                        if($data['social_type']=='F' || $data['social_type']=='T' || $data['social_type']=='G' || $data['social_type']=='L'){
                                $data_social['fk_user_id']=$user_id;
                            if($data['social_type']=='F'){
                                $data_social['facebook_login_id']=$data['login_id'];
                                $data_social['facebook_account_id']=$data['account_id'];
                                $data_social['facebook_user_access_token']=$data['user_access_token'];
                            }
                            if($data['social_type']=='G'){
                                $data_social['googleplus_login_id']=$data['login_id'];
                                $data_social['googleplus_account_id']=$data['account_id'];
                                $data_social['googleplus_user_access_token']=$data['user_access_token'];
                            }
                            if($data['social_type']=='T'){
                                $data_social['twitter_login_id']=$data['login_id'];
                                $data_social['twitter_account_id']=$data['account_id'];
                                $data_social['twitter_user_access_token']=$data['user_access_token'];
                            }
                            if($data['social_type']=='L'){
                                $data_social['linkedin_login_id']=$data['login_id'];
                                $data_social['linkedin_account_id']=$data['account_id'];
                                $data_social['linkedin_user_access_token']=$data['user_access_token'];
                            }

                            $this->user_model->addSocialLogins($data_social);

                            
                        }
                        //add data into user_types
                        $user_types['fk_user_id']=$user_id;
                        $userCode=strtoupper($this->getVerificationCode());
                        $user_types['user_code']='REF'.$userCode;
                        $this->user_model->addUserTypes($user_types);
                        
                        //save verofication code to email
                        $row['fk_user_id']=$user_id;
                        //$verifyCode=strtoupper($this->getVerificationCode());
                        $verifyCode = mt_rand(100000, 999999);
                        $emailVerifyCode='E-'.$verifyCode;
                        $row['verification_type']='E';
                        $row['verification_code']=$emailVerifyCode;
                        $this->user_model->addUserVerificationCode($row);
                        //send email
                        //initialising codeigniter email

                        /*$config=array();
                            
                        $config['protocol']    = 'smtp';
                        $config['smtp_host']    = 'email-smtp.us-west-2.amazonaws.com';
                        $config['smtp_port']    = '587';
                        $config['smtp_crypto']='tls';
                        $config['smtp_user']    = 'AKIAJ77XL2KI7GIA3KAQ';
                        $config['smtp_pass']    = 'Ah64mE19mUZVBxIHYFKy+Z0mjTYxVFDCphWZ0d0W45nS';
                        //


                        //$config['protocol']        = 'sendmail';
                        
                        $config['charset']         = 'utf-8';
                        $config['wordwrap']        = TRUE;
                        $config['mailtype']        = 'html';
                        $config['newline']      = "\r\n"; */
                        $email_config=email_config();
                        $this->email->initialize($email_config);
                        // email sent to user 
                        $admin_email= $this->config->item('admin_email');
                        $admin_email_from= $this->config->item('admin_email_from');

                        $this->email->from($admin_email, $admin_email_from);
                        $this->email->to($data['email_id']);          
                        $this->email->subject('Signup Email');

                        $verify_encrypt_code=$emailVerifyCode.'_'.$user_id;
                        $encrypted_code=setEncryption($verify_encrypt_code);
                        $encrp_base=urlencode(base64_encode($encrypted_code));
                        $email_data['verification_link']= $encrp_base;
                        $email_data['verification_code']= $emailVerifyCode;
                        $email_data['customer_id']= $user_dtl['customer_id'];
                        
                        

                        $email_body= $this->parser->parse('email_templates/signupemailverification', $email_data, true);


                        $this->email->message($email_body);            
                        $this->email->send();
                        //echo $this->email->print_debugger();


                        // email send end
                        //save verofication code to email
                        //$verifyCode=strtoupper($this->getVerificationCode());
                        $verifyCode = mt_rand(100000, 999999);
                        $mobileVerifyCode='M-'.$verifyCode;
                        $row['verification_type']='M';
                        $row['verification_code']=$mobileVerifyCode;
                        $this->user_model->addUserVerificationCode($row);

                        $this->sms->category="MOBVER";
                        $this->sms->code = $mobileVerifyCode;
                        $this->sms->mobile = $data['mobile_number'];
                        $response = $this->sms->sendSmsFinal();



                        $user_level=array();
                        $user_level['fk_user_id']=$user_id;
                        $user_level['fk_mcoin_level_id']='1';
                        $this->user_model->addUserLevel($user_level);

                        $this->response(array(
                                    'raws' => array(
                                        'status' => array(
                                            'access_token_status' => 'true',
                                            'action_status' => 'true'
                                        ),
                                        'publish' => $this->publish
                                    )
                                ), $this->config->item('http_response_ok')
                            );

                    }else{
                        // email id/mobile_no is already exists
                        if($data['error_message']==''){
                            $data['error_message'] = 'Email id OR Mobile no is already exists';
                        }
                       // $data['error_message'] = 'Invalid parameter';
                        //response in json format
                        $this->response(array(
                            'raws' => array(
                                'error_message' => $data['error_message'],
                                'publish' => $this->publish
                            )
                        ), $this->config->item('http_response_bad_request'));

                    }

                    


                } else {
                    if($error_message!=''){
                        $data['error_message']=$error_message;
                    }else{
                        $data['error_message'] = 'Invalid parameter';
                    }
                    //response in json format
                    $this->response(array(
                        'raws' => array(
                            'error_message' => $data['error_message'],
                            'publish' => $this->publish
                        )
                    ), $this->config->item('http_response_bad_request'));
                }
            }
    }

    

    /**
     *  
     *   @SWG\Post(
     *   path="/user/emailUniqueChecking",
     *   tags={"emailExist"},
     *   summary="is email exist",
     *   description="This api is used to check wheather email id is exist or not ",
     *   produces={"application/json"},
        
          @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="id of users table",
     *     required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="id of users table",
     *     required=true,
     *     type="string",
            @SWG\Schema(ref="#/definitions/emailVerify"),
     *   ),
        
     *   @SWG\Response(response="200",description="For SUCCESS Response ACTION_STATUS is true, for ERROR response ACTION_STATUS is false",@SWG\Schema(ref="#/definitions/ApiResponseFormatDataset")
     ),
     security={{"oauth2": {"scope"}}}

     * )
     */  

    public function emailUniqueChecking_post() {

         if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $data['error_message'] = "Invalid Token";
            $this->response(array(
                'raws' => array(
                    'status' => array(
                        'access_token_status' => 'false',
                        'access_token_msg' => $data['error_message']
                    ),
                    'publish' => $this->publish
                )
                    ), $this->config->item('http_response_unauthorized'));
            } else {

                $flag = 0;
                $data = array();

                if (!$this->post('email_id')) {
                    $flag = $flag + 1;
                } else {
                    $data['email_id'] = $this->post('email_id', TRUE);
                }

              

                if ($flag == 0) {

                    $data_error_flag=0;
                    $is_email_exist=$this->user_model->check_emailid($data['email_id']);
                    

                    if($is_email_exist>0){
                        $data_error_flag=1;
                        $data['error_message']='Email id is already exists';
                    }

                   
                    if($data_error_flag==0){

                      

                            $this->response(array(
                                    'raws' => array(
                                        'status' => array(
                                            'access_token_status' => 'true',
                                            'action_status' => 'true'
                                        ),
                                        'publish' => $this->publish
                                    )
                                ), $this->config->item('http_response_ok')
                            );

                    }else{
                        // email id/mobile_no is already exists
                         $data['error_message'] = 'Email id  is already exists';
                        $this->response(array(
                            'raws' => array(
                                'status' => array(
                                    'access_token_status' => 'true',
                                    'action_status' => 'false',
                                    'error_message' => $data['error_message']
                                    
                                ),
                                'publish' => $this->publish
                            )
                        ), $this->config->item('http_response_bad_request'));

                    }

                    


                } else {
                    $data['error_message'] = 'Invalid parameter';
                    //response in json format
                    $this->response(array(
                        'raws' => array(
                            'status' => array(
                                'action_status' => 'false',
                                'fetch_status' => 'false',
                                'error_message' => $data['error_message']
                            ),
                            'publish' => $this->publish
                        )
                    ), $this->config->item('http_response_bad_request'));
                }
            }
    }

  


    /**
     *  
     *   @SWG\Post(
     *   path="/user/mobileUniqueChecking",
     *   tags={"mobileExist"},
     *   summary="is mobile exist",
     *   description="This api is used to check wheather mobile no is exist or not ",
     *   produces={"application/json"},
        
          @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="id of users table",
     *     required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="id of users table",
     *     required=true,
     *     type="string",
            @SWG\Schema(ref="#/definitions/mobileVerify"),
     *   ),
        
     *   @SWG\Response(response="200",description="For SUCCESS Response ACTION_STATUS is true, for ERROR response ACTION_STATUS is false",@SWG\Schema(ref="#/definitions/ApiResponseFormatDataset")
     ),
     security={{"oauth2": {"scope"}}}

    * )
    */  

    public function mobileUniqueChecking_post() {
         if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $data['error_message'] = "Invalid Token";
            $this->response(array(
                'raws' => array(
                    'status' => array(
                        'access_token_status' => 'false',
                        'access_token_msg' => $data['error_message']
                    ),
                    'publish' => $this->publish
                )
                    ), $this->config->item('http_response_unauthorized'));
            } else {

                $flag = 0;
                $data = array();

                if (!$this->post('mobile_number')) {
                    $flag = $flag + 1;
                } else {
                    $data['mobile_number'] = $this->post('mobile_number', TRUE);
                }

              

                if ($flag == 0) {

                    $data_error_flag=0;
                    $is_mobile_exist=$this->user_model->check_mobileno($data['mobile_number']);
                    

                    if($is_mobile_exist>0){
                        $data_error_flag=1;
                        $data['error_message']='Mobile no is already exists';
                    }

                   
                    if($data_error_flag==0){
                            $this->response(array(
                                    'raws' => array(
                                        'status' => array(
                                            'access_token_status' => 'true',
                                            'action_status' => 'true'
                                        ),
                                        'publish' => $this->publish
                                    )
                                ), $this->config->item('http_response_ok')
                            );

                    }else{
                        // email id/mobile_no is already exists
                        
                        $data['error_message'] = 'Mobile no is already exists';
                        $this->response(array(
                            'raws' => array(
                                'status' => array(
                                    'access_token_status' => 'true',
                                    'action_status' => 'false',
                                    'error_message' => $data['error_message']
                                    
                                ),
                                'publish' => $this->publish
                            )
                        ), $this->config->item('http_response_bad_request'));
                    }

                    


                } else {
                    $data['error_message'] = 'Invalid parameter';
                    //response in json format
                    $this->response(array(
                        'raws' => array(
                            'status' => array(
                                'action_status' => 'false',
                                'fetch_status' => 'false',
                                'error_message' => $data['error_message']
                            ),
                            'publish' => $this->publish
                        )
                    ), $this->config->item('http_response_bad_request'));
                }
            }
    }



    /**
     *  
     *   @SWG\Post(
     *   path="/user/login",
     *   tags={"login"},
     *   summary="login user",
     *   description="This api is used to register by email",
     *   produces={"application/json"},
        
         @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="id of users table",
     *     required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="id of users table",
     *     required=true,
     *     type="string",
            @SWG\Schema(ref="#/definitions/login"),
     *   ),
        
     *   @SWG\Response(response="200",description="For SUCCESS Response ACTION_STATUS is true, for ERROR response ACTION_STATUS is false",@SWG\Schema(ref="#/definitions/ApiResponseFormatDataset")
     ),
     security={{"oauth2": {"scope"}}}

     * )
     */  

    public function login_post() {
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $data['error_message'] = "Invalid Token";
            $this->response(array(
                'raws' => array(
                    'status' => array(
                        'access_token_status' => 'false',
                        'access_token_msg' => $data['error_message']
                    ),
                    'publish' => $this->publish
                )
                    ), $this->config->item('http_response_unauthorized'));
            } else {

                $flag = 0;
                $data = array();

                if (!$this->post('username')) {
                    $flag = $flag + 1;
                 } else {
                    $data['username'] = $this->post('username', TRUE);
                }

                if (!$this->post('password')) {
                    $flag = $flag + 1;
                } else {
                    $data['password'] = $this->post('password', TRUE);
                }

               

                /** ************ Start of push messaging paramiters ****************** */        


                if ($this->post('device_uid')) {
                    $data['device_uid'] = $this->post('device_uid', TRUE);
                } else {
                    $flag = $flag + 1;
                }

                if ($this->post('device_token')) {
                    $data['device_token'] = $this->post('device_token', TRUE);
                } else {
                    $flag = $flag + 1;
                }

                if ($this->post('device_name')) {
                    $data['device_name'] = $this->post('device_name', TRUE);
                } else {
                    $flag = $flag + 1;
                }

                if ($this->post('device_model')) {
                    $data['device_model'] = $this->post('device_model', TRUE);
                } else {
                    $flag = $flag + 1;
                }

                if ($this->post('device_version')) {
                    $data['device_version'] = $this->post('device_version', TRUE);
                } else {
                    $flag = $flag + 1;
                }

                if ($this->post('device_os')) {
                    $data['device_os'] = $this->post('device_os', TRUE);
                } else {
                    $flag = $flag + 1;
                }

                if ($this->post('push_mode')) {
                    $data['push_mode'] = $this->post('push_mode', TRUE);
                } else {
                    $flag = $flag + 1;
                }

                if ($this->post('appversion')) {
                    $data['appversion'] = $this->post('appversion', TRUE);
                } else {
                    $data['appversion'] = str_replace('_', '.', $this->config->item('test_api_ver'));
                }

                if ($this->post('timezone')) {
                    $data['timezone'] = $this->post('timezone', TRUE);
                } else {
                   $flag = $flag + 1;
                }

                $data['social_type'] = $this->post('social_type', TRUE);
                $data['social_type'] = $this->post('social_type', TRUE);
                 

                if ($flag == 0) {

                    $login_check=0;
                    
                    

                    if($data['social_type']=='F'){
                        $row['account_id']=$data['password'];
                        $facebookDtl=$this->user_model->fetchFacebookSocialLogins($row);
                        $extras['social_type']=$data['social_type'];
                        $extras['facebookDtl']=$facebookDtl;
                        if(is_array($facebookDtl) & count($facebookDtl)>0){
                            $login_check=1;
                            $user_id=$facebookDtl['fk_user_id'];
                        }else{
                            $login_check=0;
                            $user_id=0;
                        }
                    }else if($data['social_type']=='G'){
                        $row['account_id']=$data['password'];
                        $googleDtl=$this->user_model->fetchGoogleSocialLogins($row);
                        if(is_array($googleDtl) & count($googleDtl)>0){
                            $login_check=1;
                            $user_id=$googleDtl['fk_user_id'];
                        }else{
                            $login_check=0;
                            $user_id=0;
                        }
                    }else{
                        $is_login_email=$this->user_model->checkloginbyemail($data);

                        if($is_login_email==0){
                            $login_check=0;
                            $is_login_mobile=$this->user_model->checkloginbymobile($data);
                            
                            if($is_login_mobile==0){
                                $login_check=0;
                            }else{
                                $login_check=1;
                            }
                        }else{
                            $login_check=1;
                        }

                        if($login_check==1){
                            $login_user_data=$this->user_model->fetchUser($data);

                            if(password_verify($data['password'],$login_user_data['login_pwd'])){
                                $login_check=1;
                            }else{
                               $login_check=0; 
                            }
                        }




                    }


                    if($login_check==1){
                        if($data['social_type']=='F' && $user_id>0){
                            $login_data=$this->user_model->fetchUserDeatils($user_id);
                        }else{
                            $login_data=$this->user_model->fetchUser($data);
                        }
                      
                        $user_id=$login_data['id'];
                        $user_is_block=$login_data['is_block'];
                        $is_email_id_verified=$login_data['is_email_id_verified'];
                        $is_mobile_number_verified=$login_data['is_mobile_number_verified'];
                        // insert data into mobile device table
                        $checkmobiledeviceid=$this->user_model->checkMobileDeviceTable($data);

                        if($checkmobiledeviceid>0){

                            $device_data=$this->user_model->fetchDevice($data);
                            $device_row_id=$device_data['id']; 
                            $row_loginkey['fk_user_mobile_device_id']=$device_row_id;
                            $loginKeysData=$this->user_model->loginkeysCheckingUserById($user_id);
                            
                            if(is_array($loginKeysData) && count($loginKeysData)>0){

                                $user_old_login_id=$loginKeysData['fk_user_id'];

                                //add into notification table

                                $notification_code='USR-LGD';
                               
                                $notificationDtl=$this->notifications_model->getNotificationTypes($notification_code);
                                $notification_data['fk_user_id']=$user_old_login_id;
                                $notification_data['notification_for_mode']='B';
                                $notification_data['fk_notification_type_id']=$notificationDtl['id'];
                                $notification_data['notification_message']=' You are logged out because You are logged in from a different Device';
                                //serialized data
                                $json_data_array['display_name']='';
                                $json_data_array['accepted_id']=$user_old_login_id;
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
                                    $pushType=$notification_code;
                                    $message=$notification_data['notification_message'];
                                    $total_new_notifications=$this->notifications_model->getAllNewNotifications($user_old_login_id);
                                    $display_name='';
                                    $push_message = "{~message~:~" . $message . "~,~total_new_notifications~:~" . $total_new_notifications . "~,~accepted_id~:~" . $user_old_login_id . "~,~user_id~:~" . $user_old_login_id . "~,~name~:~" . $display_name . "~,~profile_image~:~" . $profile_picture_file_url . "~,~push_type~:~" . $notification_code . "~}";
                                   
                                    $this->sendMobilePushNotifications($user_old_login_id,$push_message,$pushType,$message);
                                    
                                //end push message


                            }








                            $mobileDevicerow=$this->user_model->upadteMobileDeviceTable($data);
                          
                        }else{
                            $mobileDevicerow=$this->user_model->addMobileDeviceTable($data);
                            
                        }
                        $device_data=$this->user_model->fetchDevice($data);
                        $device_row_id=$device_data['id'];
                       
                        
                        // insert into loginkey table
                        if($device_row_id>0){
                            $timezoneDTl=$this->user_model->checkTimeZone($data['timezone']);
                            $timezone_id=$timezoneDTl['id'];
                            
                            if($timezone_id>0){
                                $row_loginkey['fk_current_timezone_id']=$timezone_id;
                            }else{
                                $row_loginkey['fk_current_timezone_id']=196;
                            }

                            $row_loginkey['fk_user_id']=$user_id;
                            $row_loginkey['fk_user_mobile_device_id']=$device_row_id;
                            if($is_login==0){
                                $this->user_model->deleteLoginKeys($row_loginkey);
                                $this->user_model->addLoginKeys($row_loginkey);
                            }else{
                                $this->user_model->updateLoginKeys($row_loginkey);
                            }
                            
                            $row=$this->user_model->fetchLoginKeys($row_loginkey);
                            $passkey=$row['id'];

                            $user_types_dtl=$this->user_model->getUserTypes($row_loginkey);
                            $is_agent=$user_types_dtl['is_agent'];
                            
                            $user_mode=$user_types_dtl['user_mode'];
                            if($user_mode!='B' && $user_mode!='L'){

                                if($is_agent>0){
                                      
                                   $user_mode='A'; 
                                }
                            }

                            
                            $fk_profession_type_id=$user_types_dtl['fk_profession_type_id'];
                            $isapp['user_id']=$user_id;
                            $isUserApprove=$this->user_model->isUserApprove($isapp);

                        }

                        

                       // $this->user_model->addUser($data);

                            $this->response(array(
                                    'raws' => array(
                                        'status' => array(
                                            'access_token_status' => 'true',
                                            'action_status' => 'true'
                                        ),
                                        'dataset' => array(
                                            'extras' => $extras,
                                            'user_id' => $user_id,
                                            'passkey' => $passkey,
                                            'is_approve' => $isUserApprove,
                                            'is_block' => $user_is_block,
                                            'user_mode' => $user_mode,
                                            'is_agent' => $is_agent,
                                            'user_code' => $user_types_dtl['user_code'],
                                            'fk_profession_type_id' => $fk_profession_type_id,
                                            'is_email_verified'=>$is_email_id_verified,
                                            'is_mobile_number_verified'=>$is_mobile_number_verified
                                        ),
                                        'publish' => $this->publish
                                    )
                                ), $this->config->item('http_response_ok')
                            );

                    }else{
                        // email id/mobile_no is already exists
                        $data['error_message'] = 'Wrong Username or Password';
                        
                        $this->response(array(
                            'raws' => array(
                                'status' => array(
                                    'access_token_status' => 'true',
                                    'action_status' => 'false',
                                    'error_message' => $data['error_message']
                                    
                                ),
                                'publish' => $this->publish
                            )
                        ), $this->config->item('http_response_bad_request'));

                    }

                    


                } else {
                    $data['error_message'] = 'Invalid parameter';
                    //response in json format
                    $this->response(array(
                        'raws' => array(
                            'status' => array(
                                'action_status' => 'false',
                                'fetch_status' => 'false',
                                'error_message' => $data['error_message']
                            ),
                            'publish' => $this->publish
                        )
                    ), $this->config->item('http_response_bad_request'));
                }
            }
    }


     Public function sendMobilePushNotifications($receiver_id,$push_message,$pushType,$message){
      
        $appExtras = new AppExtrasAPI();
         $check_push = $appExtras->canSendPushToUser($receiver_id);

            if($check_push){

                $device_dtl=$this->user_model->fetchMobileDevice($receiver_id);
                $device_uid=$device_dtl['device_uid'];
               
                $badge_count=$device_dtl['badge_count']+1;
                $device_table_id=$device_dtl['id'];
                $device_os=$device_dtl['device_os'];
                if ($device_os=='iOS') {
                    if($isappactive==0){
                        $dataappactive['badge_count']=1;
                        $dataappactive['id']=$device_table_id;
                       // $this->salesrep_model->updateIsappactive($dataappactive);
                    }
                    $appExtras->sendPushDirect($receiver_id, $device_uid, $push_message, $pushType,$device_os,$message);
      
                }else if($device_os=='And'){
                   
                    $appExtras->sendPushDirect($receiver_id, $device_uid, $push_message, $pushType,$device_os, $message);

                }
            }
    }

    /**
     *  
     *   @SWG\Post(
     *   path="/user/resendSignupVerification",
     *   tags={"resendSignupVerification"},
     *   summary="resend email verification",
     *   description="This api is used to check wheather email id is exist or not ",
     *   produces={"application/json"},
        
          @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="id of users table",
     *     required=true,
     *     type="string",
     *   ),
     *   @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="id of users table",
     *     required=true,
     *     type="string",
            @SWG\Schema(ref="#/definitions/resendSignupVerification"),
     *   ),
        
     *   @SWG\Response(response="200",description="For SUCCESS Response ACTION_STATUS is true, for ERROR response ACTION_STATUS is false",@SWG\Schema(ref="#/definitions/ApiResponseFormatDataset")
     ),
     security={{"oauth2": {"scope"}}}

    * )
    */  

    public function resendSignupVerification_post() {
         if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $data['error_message'] = "Invalid Token";
            $this->response(array(
                'raws' => array(
                    'status' => array(
                        'access_token_status' => 'false',
                        'access_token_msg' => $data['error_message']
                    ),
                    'publish' => $this->publish
                )
                    ), $this->config->item('http_response_unauthorized'));
            } else {

                $flag = 0;
                $data = array();

                if (!$this->post('email_id')) {
                    $flag = $flag + 1;
                } else {
                    $data['email_id'] = $this->post('email_id', TRUE);
                }

              

                if ($flag == 0) {

                    $data_error_flag=0;
                    $is_email_exist=$this->user_model->check_emailid($data['email_id']);
                    

                    if($is_email_exist>0){
                        $data['username']=$data['email_id'];
                        $user_dtl=$this->user_model->fetchUser($data);
                        $user_type['fk_user_id']=$user_dtl['id'];
                        $user_type['verification_type']='E';
                        $verification_details=$this->user_model->getVerificationType($user_type);
                        $emailVerifyCode=$verification_details['verification_code'];
                        //initialising codeigniter email
                         $email_config=email_config();
                        $this->email->initialize($email_config);
                        // email sent to user 
                        $admin_email= $this->config->item('admin_email');
                        $admin_email_from= $this->config->item('admin_email_from');
                        $this->email->from($admin_email, $admin_email_from);
                        $this->email->to($data['email_id']);          
                        $this->email->subject('Signup Email');

                        $verify_encrypt_code=$emailVerifyCode.'_'.$user_dtl['id'];
                        $encrypted_code=setEncryption($verify_encrypt_code);
                         $encrp_base=urlencode(base64_encode($encrypted_code));
                        $email_data['verification_link']= $encrp_base;
                        $email_data['verification_code']= $emailVerifyCode;


                        $email_body= $this->parser->parse('email_templates/signupemailverification', $email_data, true);


                         $this->email->message($email_body);            
                         $this->email->send();
                    }else{
                        $data_error_flag=1;
                        $data['error_message']='Email id does not exist';
                    }

                   
                    if($data_error_flag==0){
                            $this->response(array(
                                    'raws' => array(
                                        'status' => array(
                                            'access_token_status' => 'true',
                                            'action_status' => 'true'
                                        ),
                                        'publish' => $this->publish
                                    )
                                ), $this->config->item('http_response_ok')
                            );

                    }else{
                        // email id/mobile_no is already exists
                        
                        $data['error_message'] = 'Mobile no is already exists';
                        $this->response(array(
                            'raws' => array(
                                'status' => array(
                                    'access_token_status' => 'true',
                                    'action_status' => 'false',
                                    'error_message' => $data['error_message']
                                    
                                ),
                                'publish' => $this->publish
                            )
                        ), $this->config->item('http_response_bad_request'));
                    }

                    


                } else {
                    $data['error_message'] = 'Invalid parameter';
                    //response in json format
                    $this->response(array(
                        'raws' => array(
                            'status' => array(
                                'action_status' => 'false',
                                'fetch_status' => 'false',
                                'error_message' => $data['error_message']
                            ),
                            'publish' => $this->publish
                        )
                    ), $this->config->item('http_response_bad_request'));
                }
            }
    }


    /**
     *  
     *   @SWG\Post(
     *   path="/user/token",
     *   tags={"get token"},
     *   summary="industry names",
     *   description="This api is used to get organization type/industry names depending on memeber id",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="grant_type",
     *     in="formData",
     *     description="client_credentials, e.g. client_credentials",   
     *     required=true,
     *     type="string",
     *   ),
        @SWG\Parameter(
     *     name="client_id",
     *     in="formData",
     *     description="client_id, e.g. 9d911a9a00ef11e48aff0019d114582",       
     *     required=true,
     *     type="string",
     *   ),
        @SWG\Parameter(
     *     name="client_secret",
     *     in="formData",
     *     description="client_secret, e.g. 463ceaeab4db11e3aa520019d119645",   
     *     required=true,
     *     type="string",
     *   ),
     *
     *   @SWG\Response(response="200",description="For SUCCESS Response ACTION_STATUS is true, for ERROR response ACTION_STATUS is false",@SWG\Schema(ref="#/definitions/ApiResponseFormatDataset")
     ),

     * )
     */    

    function token_post() {

        // Handle a request for an OAuth2.0 Access Token and send the response to the client
        $this->oauth_server->handleTokenRequest(OAuth2\Request::createFromGlobals())->send();
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
   
    /**
     *  
     *   @SWG\Post(
     *      path="/user/logout",
     *      tags={"logout"},
     *      summary="logout user",
     *      description="This api is used to logout by user_id and pass_key",
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
     *          description="Post data to logout",
     *          required=true,
     *          type="string",
     *          @SWG\Schema(ref="#/definitions/logout"),
     *      ),
     *   
     *      @SWG\Response(
     *          response="200",
     *          description="For SUCCESS Response message will be <b>success_message</b>, for ERROR response message will be <b>error_message</b>",
     *
     *          @SWG\Schema(
     *              ref="#/definitions/ApiResponseFormat"
     *          )
     *      ),
     *      security={{"oauth2": {"scope"}}}
     *  )
     *
    **/  

    public function logout_post(){

        $error_message = $success_message = $http_response = '';
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('pass_key')){
                $flag       = false;
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }
            if (!$this->post('user_id')){
                $flag       = false;
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

            if($flag) {
                //log in status checking
                $login_status_arr       = $this->user_model->login_status_checking($req_arr);
                //echo $this->db->last_query(); exit;
                //pre($login_status_arr,1);

                if (!empty($login_status_arr) && count($login_status_arr) > 0){
                    $logout_user_arr['key_id']              = $login_status_arr['id'];
                    $logout_user_arr['user_id']             = $login_status_arr['fk_user_id'];
                    $logout_user_arr['mobile_device_id']    = $login_status_arr['fk_user_mobile_device_id'];

                    $affected_rows  = $this->user_model->logout_user($logout_user_arr);
                    if($affected_rows > 0){
                        $http_response      = 'http_response_ok';
                        $success_message    = 'Logout successful';  
                    } else {
                        $http_response      = 'http_response_bad_request';
                        $error_message      = 'Logout not done';  
                    }
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'User is not valid';   
                }
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = 'Invalid parameter';   
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



   
    /**
     *  
     *   @SWG\Post(
     *      path="/user/updateUserModeIsAgent",
     *      tags={"updateUserModeIsAgent"},
     *      summary="logout user",
     *      description="This api is used to logout by user_id and Passkey",
     *      produces={"application/json"},
     *   
     *      @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          description="id of users table",
     *          required=true,
     *          type="string",
     *      ),
     *      @SWG\Parameter(
     *          name="data",
     *          in="body",
     *          description="id of users table",
     *          required=true,
     *          type="string",
     *          @SWG\Schema(ref="#/definitions/updateUserModeIsAgent"),
     *      ),
     *   
     *      @SWG\Response(
     *          response="200",
     *          description="For SUCCESS Response ACTION_STATUS is true, for ERROR response ACTION_STATUS is *          false",
     *
     *          @SWG\Schema(
     *              ref="#/definitions/ApiResponseFormatDataset"
     *          )
     *      ),
     *      security={{"oauth2": {"scope"}}}
     *  )
     *
    **/  

    public function updateUserModeIsAgent_post(){

        $error_message = $success_message = $http_response = '';
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $data        = array();

            if (!$this->post('pass_key')){
                $flag       = false;
            } else {
                $data['pass_key']    = $this->post('pass_key', TRUE);
            }
            if ($this->post('user_id')<0){
                $flag       = false;
            } else {
                $data['user_id']    = $this->post('user_id', TRUE);
            }

             $data['user_mode']    = $this->post('user_mode', TRUE);

             $data['referral_code']    = $this->post('referral_code', TRUE);

            if($data['referral_code']=='' && $data['user_mode']==''){
                 $flag       = false;
            }

            if($flag) {
                //log in status checking
                $login_status_arr       = $this->user_model->login_status_checking($data);
                //echo $this->db->last_query(); exit;
                //pre($login_status_arr,1);

                if (!empty($login_status_arr) && count($login_status_arr) > 0){
                    $is_agent=0;
                    if($data['referral_code']!=''){
                        $is_agent=$this->agentcode_model->check_agentcode($data);
                        if($is_agent>0){
                           $is_agent=1;


                        }


                    }

                    if($data['user_mode']=='' && $is_agent<1){
                        $http_response      = 'http_response_bad_request';
                        $error_message      = 'Wrong Agent Code'; 
                    }else{
                        $is_referral=$this->user_model->checkReferral_Code($data);
                            if(is_array($is_referral) && count($is_referral)>0){
                                $res['fk_user_id']=$data['user_id'];
                                $res['fk_refered_by_user_id']=$is_referral['fk_user_id'];
                                $res['referal_code']=$data['referral_code'];
                                $this->user_model->addUserReferals($res);
                                /*$moins['fk_user_id']=$is_referral['fk_user_id'];
                                $moins['fk_activity_user_id']=$data['user_id'];
                                $moins['fk_mcoin_activity_id']=1;
                                $mCoinDetails=$this->user_model->fetchMastermCoin($moins['fk_mcoin_activity_id']);
                                $moins['referred_connections']=$mCoinDetails['referred_connections'];
                                $this->user_model->addUserMcoins($moins);*/


                            }
                        $data['fk_user_id']=$data['user_id'];
                        $data['is_agent']    =$is_agent;
                        $affected_rows=$this->user_model->updateUserMode($data);
                        if($is_agent>0){
                            $this->agentcode_model->updateAgentCode($data);
                            $data_user_agent_code['fk_user_id']=$data['user_id'];
                            $data_user_agent_code['agent_code']=$data['referral_code'];
                            $this->agentcode_model->addUserAgentCode($data_user_agent_code);
                        }
                        $result_arr=$data;
                        $http_response      = 'http_response_ok';
                        $success_message    = 'updated successfully';  

                    }
                   
                   

                       


                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'User is not valid';   
                }
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = 'Invalid parameter';   
            }
        }

        $raws = array();   
        if($error_message != ''){
            $raws['error_message']      = $error_message;
        } else{
            $raws['success_message']    = $success_message;
        }           
        $raws['publish']    = $this->publish;
         $raws['dataset']       = $result_arr;

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
     *      path="/user/updateProfessionType",
     *      tags={"updateProfessionType"},
     *      summary="logout user",
     *      description="This api is used to logout by user_id and Passkey",
     *      produces={"application/json"},
     *   
     *      @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          description="id of users table",
     *          required=true,
     *          type="string",
     *      ),
     *      @SWG\Parameter(
     *          name="data",
     *          in="body",
     *          description="id of users table",
     *          required=true,
     *          type="string",
     *          @SWG\Schema(ref="#/definitions/updateProfessionType"),
     *      ),
     *   
     *      @SWG\Response(
     *          response="200",
     *          description="For SUCCESS Response ACTION_STATUS is true, for ERROR response ACTION_STATUS is *          false",
     *
     *          @SWG\Schema(
     *              ref="#/definitions/ApiResponseFormatDataset"
     *          )
     *      ),
     *      security={{"oauth2": {"scope"}}}
     *  )
     *
    **/  

    public function updateProfessionType_post(){

        $error_message = $success_message = $http_response = '';
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $data        = array();

            if (!$this->post('pass_key')){
                $flag       = false;
            } else {
                $data['pass_key']    = $this->post('pass_key', TRUE);
            }
            if ($this->post('user_id')<0){
                $flag       = false;
            } else {
                $data['user_id']    = $this->post('user_id', TRUE);
            }

            if ($this->post('fk_profession_type_id')<0){
                $flag       = false;
            } else {
                $data['fk_profession_type_id']    = $this->post('fk_profession_type_id', TRUE);
            }

           

            if($flag) {
                //log in status checking
                $login_status_arr       = $this->user_model->login_status_checking($data);
                //echo $this->db->last_query(); exit;
                //pre($login_status_arr,1);

                if (!empty($login_status_arr) && count($login_status_arr) > 0){
                     $data['fk_user_id']=$data['user_id'];
                    $affected_rows=$this->user_model->updateProfessionType($data);
                   
                    
                        $http_response      = 'http_response_ok';
                        $success_message    = 'Record updated successfully';  
                   
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Wrong user id OR passkey';   
                }
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = 'Invalid parameter';   
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
    

    /**
     *  
     *   @SWG\Post(
     *      path="/user/forgot_password_step1",
     *      tags={"Forgot Password"},
     *      summary="forgot password step1",
     *      description="This api is used to send passcode to email / mobile by using email id or mobile number",
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
     *          description="Post data to send passcode to email / mobile",
     *          required=true,
     *          type="string",
     *          @SWG\Schema(ref="#/definitions/forgot_password_step1"),
     *      ),
     *   
     *      @SWG\Response(
     *          response="200",
     *          description="For SUCCESS Response message will be <b>success_message</b>, for ERROR response message will be <b>error_message</b>",
     *
     *          @SWG\Schema(
     *              ref="#/definitions/ApiResponseFormatDataset"
     *          )
     *      ),
     *      security={{"oauth2": {"scope"}}}
     *  )
     *
    **/ 
    public function forgot_password_step1_post(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('email_phone')){
                $flag = false;
            } else {
                /*if(!filter_var($this->post('email_phone'), FILTER_VALIDATE_EMAIL)){
                    $flag               = false;
                    $error_message      = 'Invalid Email Id format';
                } else {*/
                    $req_arr['username']   = $this->post('email_phone', TRUE);
                //}
            }
            //pre($req_arr,1);

            if($flag) {
                //log in status checking
                $user_details       = $this->user_model->fetchUser($req_arr);

                if (!empty($user_details) && count($user_details) > 0){

                    $email_data['user_id']     = $user_details['id'];
                    $email_data['display_name']= $user_details['display_name'];
                    $email_data['email']       = $user_details['email_id'];
                    $email_data['mobile']      = $user_details['mobile_number'];

                    
                    $passcode_details = $this->user_model->fetchUserPswdResetCode($user_details);
                    if(!empty($passcode_details) && count($passcode_details) > 0){
                        $count = $this->user_model->remove_passcode($passcode_details);
                    }

                    //save password reset code
                    $verifyCode = mt_rand(100000, 999999);
                    $password_reset_code    = 'P-'.$verifyCode;
                    $row['fk_user_id']      = $user_details['id'];
                    $row['passcode']        = strtoupper($password_reset_code);
                    $this->user_model->addUserPswdResetCode($row);


                    //send email
                    //initialising codeigniter email
                    $email_config=email_config();
                    $this->email->initialize($email_config);
                    
                    // email sent to user 
                    $admin_email= $this->config->item('admin_email');
                    $admin_email_from= $this->config->item('admin_email_from');
                    $this->email->from($admin_email, $admin_email_from);
                    $this->email->to($user_details['email_id']);          
                    $this->email->subject('Forget Password: Verification Code');

                    $email_data['verification_code'] = $password_reset_code;                   

                    $email_body= $this->parser->parse('email_templates/forgetpassword', $email_data, true);
                    $this->email->message($email_body);            

                    $send = $this->email->send();
                    // email send end
                    
                    if($send){
                        $result_arr         = array(
                                                'user_id'   => $user_details['id'],
                                                'email'     => $user_details['email_id'],
                                                'mobile'    => $user_details['mobile_number'],
                                            );

                        $http_response      = 'http_response_ok';
                        $success_message    = 'Validation code has been sent';  
                    } else {
                        $http_response      = 'http_response_bad_request';
                        $error_message      = 'Something went wrong in API';  
                    }
                } else {
                    $http_response      = 'http_response_bad_request';
                    $error_message      = 'Provided Email Id / Mobile is not registered';   
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
     *      path="/user/forgot_password_step2",
     *      tags={"Forgot Password"},
     *      summary="forgot password step2",
     *      description="This api is used to validate passcode",
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
     *          description="Post data to validate passcode",
     *          required=true,
     *          type="string",
     *          @SWG\Schema(ref="#/definitions/forgot_password_step2"),
     *      ),
     *   
     *      @SWG\Response(
     *          response="200",
     *          description="For SUCCESS Response message will be <b>success_message</b>, for ERROR response message will be <b>error_message</b>",
     *
     *          @SWG\Schema(
     *              ref="#/definitions/ApiResponseFormatDataset"
     *          )
     *      ),
     *      security={{"oauth2": {"scope"}}}
     *  )
     *
    **/ 
    public function forgot_password_step2_post(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if(!$this->post('passcode')){
                $flag                   = false;
            } else {
                $req_arr['passcode']    = $this->post('passcode', TRUE);
            }

            if(!$this->post('user_id') && $flag) {
                $flag                   = false;
            } else {
                $req_arr['fk_user_id']  = $this->post('user_id', TRUE);
            }

            //pre($req_arr,1);

            if($flag) {
                $passcode_details = $this->user_model->getUserPswdResetCode($req_arr);
                //echo $this->db->last_query(); exit;
                //pre($passcode_details,1);

                if(!empty($passcode_details) && count($passcode_details) > 0){
                    $passcode_validity  = $this->config->item("passcode_validity");
                    $exp_datetime       = date("Y-m-d H:i:s", strtotime($passcode_details['generated_timestamp'] . "+".$passcode_validity." minutes"));

                    if($exp_datetime >= date("Y-m-d H:i:s")){
                        $result_arr         = array(
                                                'passcode'  => $req_arr['passcode'],
                                                'user_id'   => $req_arr['fk_user_id'],
                                            );
                        $http_response      = 'http_response_ok';
                        $success_message    = 'Your provided code matched'; 
                    } else {
                        $http_response      = 'http_response_ok';
                        $error_message      = "Your validation code is expired, please reset password again";
                    }
                } else {
                    $http_response      = 'http_response_bad_request';
                    $error_message      = 'Your provided code did not match';
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
     *      path="/user/forgot_password_step3",
     *      tags={"Forgot Password"},
     *      summary="forgot password step3",
     *      description="This api is used to reset password",
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
     *          description="Post data for reset password",
     *          required=true,
     *          type="string",
     *          @SWG\Schema(ref="#/definitions/forgot_password_step3"),
     *      ),
     *   
     *      @SWG\Response(
     *          response="200",
     *          description="For SUCCESS Response message will be <b>success_message</b>, for ERROR response message will be <b>error_message</b>",
     *
     *          @SWG\Schema(
     *              ref="#/definitions/ApiResponseFormat"
     *          )
     *      ),
     *      security={{"oauth2": {"scope"}}}
     *  )
     *
    **/ 
    public function forgot_password_step3_post(){

        $error_message = $success_message = $http_response = $new_password = $confirm_password = '';

        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = $update_arr = array();

            if(!$this->post('new_password',true)){
                $flag           = false;
                $error_message  = 'New Password is required';
            } elseif($flag && strlen($this->post("new_password",true)) < 3) {
                $flag = false;
                $error_message = "Please enter atleast 3 char";
            } else {
                $new_password   = $this->post('new_password', TRUE);
            }

            if(!$this->post('confirm_password') && $flag) {
                $flag               = false;
                $error_message      = 'Confirm Password is required';
            } else {
                $confirm_password   = $this->post('confirm_password', TRUE);
            }

            if(!$this->post('passcode') && $flag) {
                $flag                   = false;
                $error_message          = 'Verification Code is required';
            } else {
                $req_arr['passcode']    = $this->post('passcode', TRUE);
            }

            if(!$this->post('user_id') && $flag) {
                $flag                   = false;
                $error_message          = 'user_id is required';
            } else {
                $req_arr['fk_user_id']  = $this->post('user_id', TRUE);
                $update_arr['user_id']  = $this->post('user_id', TRUE);
            }

            //pre($req_arr,1);

            if($flag) {
                $passcode_details = $this->user_model->getUserPswdResetCode($req_arr);
                //echo $this->db->last_query(); exit;
                //pre($passcode_details,1);

                if(!empty($passcode_details) && count($passcode_details) > 0){
                    if($new_password == $confirm_password) {
                        $update_arr['password']     = md5($new_password);
                        $status                     = $this->user_model->change_password($update_arr);

                        if($status > 0) {
                            $count = $this->user_model->remove_passcode($req_arr);
                            $http_response      = 'http_response_ok';
                            $success_message    = 'Password changed successfully';

                        } else {
                            $http_response      = 'http_response_bad_request';
                            $error_message      = 'Something went wrong in API';
                        }

                    } else {
                        $http_response          = 'http_response_bad_request';
                        $error_message          = 'Confirm password did not match';
                    }
                } else {
                    $http_response              = 'http_response_bad_request';
                    $error_message              = 'Verification Code does not matched';
                }
            } else {
                $http_response                  = 'http_response_bad_request';
                $error_message                  = ($error_message != '') ? $error_message : 'Invalid parameter';   
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

    

    /**
     *  
     *   @SWG\GET(
     *      path="/user/getProfessionalType",
     *      tags={"getProfessionalType"},
     *      summary="getProfessionalType",
     *      description="This api is used to forgot_password_step1 by email",
     *      produces={"application/json"},
     *   
     *      @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          description="id of users table",
     *          required=true,
     *          type="string",
     *      ),
     *      @SWG\Parameter(
     *          name="data",
     *          in="body",
     *          description="id of users table",
     *          required=true,
     *          type="string",
     *          @SWG\Schema(ref="#/definitions/forgot_password_step1"),
     *      ),
     *   
     *      @SWG\Response(
     *          response="200",
     *          description="For SUCCESS Response ACTION_STATUS is true, for ERROR response ACTION_STATUS is false",
     *
     *          @SWG\Schema(
     *              ref="#/definitions/ApiResponseFormatDataset"
     *          )
     *      ),
     *      security={{"oauth2": {"scope"}}}
     *  )
     *
    **/ 
    public function getProfessionalType_get(){

        $error_message = $success_message = $http_response = '';
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();
            $raws = array();   
           
                //log in status checking
                $user_details       = $this->user_model->getProfessionType();
                $http_response      = 'http_response_ok';
                $success_message    = '';  
                $raws['dataset'] =$user_details;
            
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
     *   @SWG\GET(
     *      path="/user/getGender",
     *      tags={"getGender"},
     *      summary="getGender",
     *      description="This api is used to forgot_password_step1 by email",
     *      produces={"application/json"},
     *   
     *      @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          description="id of users table",
     *          required=true,
     *          type="string",
     *      ),
     *     
     *   
     *      @SWG\Response(
     *          response="200",
     *          description="For SUCCESS Response ACTION_STATUS is true, for ERROR response ACTION_STATUS is false",
     *
     *          @SWG\Schema(
     *              ref="#/definitions/ApiResponseFormatDataset"
     *          )
     *      ),
     *      security={{"oauth2": {"scope"}}}
     *  )
     *
    **/ 
    public function getGender_get(){

        $error_message = $success_message = $http_response = '';
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();
            $raws = array();   
           
                //log in status checking
                $user_details       = $this->user_model->getGender();
                $http_response      = 'http_response_ok';
                $success_message    = '';  
                $raws['dataset'] =$user_details;
            
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




     
    public function verifiedEmail_get($verify_code){

       

            $flag           = true;
            $req_arr        = array();
            $raws = array();  
          
           $ver_code=getDrcyption(base64_decode(urldecode($verify_code)));
           $vcode=explode('_',$ver_code);
           $row['fk_user_id']=$vcode[1];
           $row['verification_code']=$vcode[0];
          
           $row['verification_type']='E';
                //log in status checking
                $is_verify       = $this->user_model->checkVerificationCode($row);
                if($is_verify>0){
                    $verify_data['id']=$vcode[1];
                    $verify_data['is_email_id_verified']=1;
                    $this->user_model->updateEmailVerified($verify_data);
                    $userDetails=$this->user_model->fetchUserDeatils($vcode[1]);
                    $email_id=$userDetails['email_id'];
                    $history['fk_user_id']=$vcode[1];
                    $history['email_id']=$email_id;
                    $this->user_model->addHistoryEmail($history);
                    $http_response      = 'http_response_ok';
                    $success_message    = '';  
                    redirect('https://mpokket.com/#/home/email-verified');
                }else {
                    $http_response      = 'http_response_bad_request';
                    $error_message      = 'Invalid Verification Code ';
                    redirect('https://mpokket.com/user/fail');
                }
               
    }

    public function verifiedChangeEmail_get($verify_code){

       

            $flag           = true;
            $req_arr        = array();
            $raws = array();  
          
           $ver_code=getDrcyption(base64_decode(urldecode($verify_code)));
           $vcode=explode('_',$ver_code);
           $row['fk_user_id']=$vcode[1];
           $row['verification_code']=$vcode[0];
          
           $row['verification_type']='E';
                //log in status checking
                $is_verify       = $this->user_model->checkVerificationCode($row);
                if($is_verify>0){
                    $verify_data['id']=$vcode[1];
                    $verify_data['is_email_id_verified']=1;
                    $this->user_model->updateEmailVerified($verify_data);
                    $verify_data_user['id']=$vcode[1];
                    $verify_data_user['email_id']=$vcode[2];
                    $this->user_model->chnageEmail($verify_data_user);
                    $userDetails=$this->user_model->fetchUserDeatils($vcode[1]);

                    $email_id=$userDetails['email_id'];
                    $history['fk_user_id']=$vcode[1];
                    $history['email_id']=$email_id;
                    $this->user_model->addHistoryEmail($history);

                    $http_response      = 'http_response_ok';
                    $success_message    = '';  
                    redirect('https://mpokket.com/#/home/email-verified');
                }else {
                    $http_response      = 'http_response_bad_request';
                    $error_message      = 'Invalid Verification Code ';
                    redirect('https://mpokket.com/user/fail');
                }
               
    }


    /**
     *  
     *   @SWG\GET(
     *      path="/user/getMatrialStatus",
     *      tags={"getMatrialStatus"},
     *      summary="getMatrialStatus",
     *      description="This api is used to forgot_password_step1 by email",
     *      produces={"application/json"},
     *   
     *      @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          description="id of users table",
     *          required=true,
     *          type="string",
     *      ),
     *     
     *   
     *      @SWG\Response(
     *          response="200",
     *          description="For SUCCESS Response ACTION_STATUS is true, for ERROR response ACTION_STATUS is false",
     *
     *          @SWG\Schema(
     *              ref="#/definitions/ApiResponseFormatDataset"
     *          )
     *      ),
     *      security={{"oauth2": {"scope"}}}
     *  )
     *
    **/ 
    public function getMatrialStatus_get(){

        $error_message = $success_message = $http_response = '';
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();
            $raws = array();   
           
                //log in status checking
                $details['matrial_status']       = $this->agentcode_model->fetchMatrialStatus();
                $http_response      = 'http_response_ok';
                $success_message    = '';  
                $raws['dataset'] =$details;
            
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
     *   @SWG\GET(
     *      path="/user/getRecidenceStatus",
     *      tags={"getRecidenceStatus"},
     *      summary="getRecidenceStatus",
     *      description="This api is used to forgot_password_step1 by email",
     *      produces={"application/json"},
     *   
     *      @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          description="id of users table",
     *          required=true,
     *          type="string",
     *      ),
     *     
     *   
     *      @SWG\Response(
     *          response="200",
     *          description="For SUCCESS Response ACTION_STATUS is true, for ERROR response ACTION_STATUS is false",
     *
     *          @SWG\Schema(
     *              ref="#/definitions/ApiResponseFormatDataset"
     *          )
     *      ),
     *      security={{"oauth2": {"scope"}}}
     *  )
     *
    **/ 
    public function getRecidenceStatus_get(){

        $error_message = $success_message = $http_response = '';
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();
            $raws = array();   
           
                //log in status checking
                $details['recidence_status']       = $this->agentcode_model->fetchResidenceStatus();
                $http_response      = 'http_response_ok';
                $success_message    = '';  
                $raws['dataset'] =$details;
            
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
     *   @SWG\GET(
     *      path="/user/getRecidenceStatus",
     *      tags={"getRecidenceStatus"},
     *      summary="getRecidenceStatus",
     *      description="This api is used to forgot_password_step1 by email",
     *      produces={"application/json"},
     *   
     *      @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          description="id of users table",
     *          required=true,
     *          type="string",
     *      ),
     *     
     *   
     *      @SWG\Response(
     *          response="200",
     *          description="For SUCCESS Response ACTION_STATUS is true, for ERROR response ACTION_STATUS is false",
     *
     *          @SWG\Schema(
     *              ref="#/definitions/ApiResponseFormatDataset"
     *          )
     *      ),
     *      security={{"oauth2": {"scope"}}}
     *  )
     *
    **/ 
    public function getDegreeType_get(){

        $error_message = $success_message = $http_response = '';
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();
            $raws = array();   
           
                //log in status checking
                $details['degreetypes_status']       = $this->agentcode_model->getDegreeType();
                $http_response      = 'http_response_ok';
                $success_message    = '';  
                $raws['dataset'] =$details;
            
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
     *   @SWG\POST(
     *      path="/user/getDegree",
     *      tags={"getRecidenceStatus"},
     *      summary="getRecidenceStatus",
     *      description="This api is used to forgot_password_step1 by email",
     *      produces={"application/json"},
     *   
     *      @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          description="id of users table",
     *          required=true,
     *          type="string",
     *      ),
     *     
     *   
     *      @SWG\Response(
     *          response="200",
     *          description="For SUCCESS Response ACTION_STATUS is true, for ERROR response ACTION_STATUS is false",
     *
     *          @SWG\Schema(
     *              ref="#/definitions/ApiResponseFormatDataset"
     *          )
     *      ),
     *      security={{"oauth2": {"scope"}}}
     *  )
     *
    **/ 
    public function getDegree_get($search_text=''){

        $error_message = $success_message = $http_response = '';
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();
            $raws = array();   
           
                //log in status checking
                $data['search_text']=$search_text;
                $degree   = $this->agentcode_model->getDegree($data);
                if(is_array($degree) && count($degree)>0){
                    $details['degree'] =$degree;
                }else{
                    $details['degree'] =array();
                }
                
                $http_response      = 'http_response_ok';
                $success_message    = '';  
                $raws['dataset'] =$details;
            
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
     *   @SWG\GET(
     *      path="/user/getDegree",
     *      tags={"getDegree"},
     *      summary="getDegree",
     *      description="This api is used to forgot_password_step1 by email",
     *      produces={"application/json"},
     *   
     *      @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          description="id of users table",
     *          required=true,
     *          type="string",
     *      ),
     *     
     *   
     *      @SWG\Response(
     *          response="200",
     *          description="For SUCCESS Response ACTION_STATUS is true, for ERROR response ACTION_STATUS is false",
     *
     *          @SWG\Schema(
     *              ref="#/definitions/ApiResponseFormatDataset"
     *          )
     *      ),
     *      security={{"oauth2": {"scope"}}}
     *  )
     *
    **/ 
    public function getFieldOfStudies_get($search_text=''){

        $error_message = $success_message = $http_response = '';
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();
            $raws = array();   
           
            //log in status checking
            $data['search_text']=$search_text;
            $degree      = $this->agentcode_model->getFieldOfStudies($data);
            if(is_array($degree) && count($degree)>0){
                $details['degree'] =$degree;
            }else{
                $details['degree'] =array();
            }
            $http_response      = 'http_response_ok';
            $success_message    = '';  
            $raws['dataset'] =$details;
            
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
     *   @SWG\GET(
     *      path="/user/getPincodes",
     *      tags={"getPincodes"},
     *      summary="getPincodes",
     *      description="This api is used to forgot_password_step1 by email",
     *      produces={"application/json"},
     *   
     *      @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          description="id of users table",
     *          required=true,
     *          type="string",
     *      ),
     *     
     *   
     *      @SWG\Response(
     *          response="200",
     *          description="For SUCCESS Response ACTION_STATUS is true, for ERROR response ACTION_STATUS is false",
     *
     *          @SWG\Schema(
     *              ref="#/definitions/ApiResponseFormatDataset"
     *          )
     *      ),
     *      security={{"oauth2": {"scope"}}}
     *  )
     *
    **/ 
    public function getPincodes_get($pincode){

        $error_message = $success_message = $http_response = '';
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();
            $raws = array();   
            if($pincode>0){
                //log in status checking
                $details['pincode']       = $this->agentcode_model->getPincodeDetails($pincode);
                $http_response      = 'http_response_ok';
                $success_message    = '';  
                $raws['dataset'] =$details;
            }else{
                $http_response      = 'http_response_bad_request';
                $error_message      =  'Wrone Pincode';   
                $raws['dataset'] =NULL;

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
    
     public function getUser_post(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('user_id')){
                $flag = false;
            } else {
                $req_arr['user_id']   = $this->post('user_id', TRUE);
                
            }
            //pre($req_arr,1);

            if($flag) {
                //log in status checking
                $user_details       = $this->user_model->fetchUserDeatils($req_arr['user_id']);
                $email_data=array();
                if (!empty($user_details) && count($user_details) > 0){
                    if($user_details['is_email_id_verified']>0){
                        
                        if($user_details['is_mobile_number_verified']>0){
                            $email_data['user_id']     = $user_details['id'];
                            $email_data['is_email_id_verified']= $user_details['is_email_id_verified'];
                            $email_data['is_mobile_number_verified']       = $user_details['is_mobile_number_verified'];
                            //send welcome email
                            //send email
                                //initialising codeigniter email
                                /*
                                 $config['protocol']        = 'sendmail';
                                 $config['mailpath']        = '/usr/sbin/sendmail';
                                 $config['charset']         = 'utf-8';
                                 $config['wordwrap']        = TRUE;
                                 $config['mailtype']        = 'html';
                                $this->email->initialize($config);
                                 // email sent to user 
                                $admin_email= $this->config->item('admin_email');
                                $admin_email_from= $this->config->item('admin_email_from');
                                $this->email->from($admin_email, $admin_email_from);
                                $this->email->to($user_details['email_id']);          
                                $this->email->subject('Welcome to mPokket');

                                $res['fk_user_id']=$req_arr['user_id'];
                                $userTypesDtl=$this->user_model->getUserTypes($res);
                                $email_data['referal_code']= $userTypesDtl['user_code'];
                                $email_body= $this->parser->parse('email_templates/welcomemPokket', $email_data, true);


                                $this->email->message($email_body);            
                                $this->email->send();
                                */
                                // email send end
                            //email end
                            $http_response      = 'http_response_ok';
                            $success_message    = '';  
                        }else{
                            $email_data['user_id']     = $user_details['id'];
                            $email_data['is_email_id_verified']= $user_details['is_email_id_verified'];
                            $email_data['is_mobile_number_verified']       = $user_details['is_mobile_number_verified'];
                        
                            $http_response      = 'http_response_bad_request';
                            $error_message    = 'Please verify your mobile number'; 

                        }
                    }else{
                        $http_response      = 'http_response_bad_request';
                        $error_message    = 'Please verify your email address';  

                    }
                    
                } else {
                    $http_response      = 'http_response_bad_request';
                    $error_message      = 'wrong user id';   
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

        $raws['dataset']       = $email_data;
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 
    }

    public function getUserDetails_post(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('user_id')){
                $flag = false;
            } else {
                $req_arr['user_id']   = $this->post('user_id', TRUE);
                
            }
            //pre($req_arr,1);

            if($flag) {
                //log in status checking
                $user_details       = $this->user_model->fetchUserDeatilsAll($req_arr['user_id']);
                $email_data=array();
                if (!empty($user_details) && count($user_details) > 0){
                   

                        $email_data['user_id']     = $user_details['id'];
                        $email_data['customer_id']     = $user_details['customer_id'];
                        $email_data['email_id']     = $user_details['email_id'];
                        $email_data['mobile_number']     = $user_details['mobile_number'];
                        $email_data['is_approved']     = $user_details['mobile_number'];
                        // fetch display name
                       
                        $mainUserDtl=$this->profile_model->fetchTempProfileMain($req_arr);
                        if(is_array($mainUserDtl) && count($mainUserDtl)>0){
                            $email_data['display_name'] = $mainUserDtl['display_name'];
                            
                    
                            if($mainUserDtl['s3_media_version']!=''){
                                
                                $email_data['profile_image_url']       = $this->config->item('bucket_url').$req_arr['user_id'].'/profile/'.$req_arr['user_id'].'.'.$mainUserDtl['profile_picture_file_extension'].'?versionId='.$mainUserDtl['s3_media_version'];
                            }else{
                                $email_data['profile_image_url']='';
                                
                            }

                        }else{
                            $tmpUserDtl=$this->profile_model->fetchTempProfileBasic($req_arr);
                            if(is_array($tmpUserDtl) && count($tmpUserDtl)>0){
                                 $email_data['display_name']     = $tmpUserDtl['display_name'];
                                if($tmpUserDtl['s3_media_version']!='' ){
                                    $email_data['profile_image_url']       = $this->config->item('bucket_url').$req_arr['user_id'].'/profile/'.$req_arr['user_id'].'.'.$tmpUserDtl['profile_picture_file_extension'].'?versionId='.$tmpUserDtl['s3_media_version'];
                                }else{
                                    $email_data['profile_image_url']='';
                                   
                                    
                                }

                            }else{
                                $email_data['display_name']     = $user_details['display_name'];
                                 $email_data['profile_image_url']='';
                            }
                        }
                        

                        $email_data['is_email_id_verified']= $user_details['is_email_id_verified'];
                        $email_data['is_mobile_number_verified']       = $user_details['is_mobile_number_verified'];
                       
                    
                        $http_response      = 'http_response_ok';
                        $success_message    = '';  
                   
                    
                } else {
                    $http_response      = 'http_response_bad_request';
                    $error_message      = 'wrong user id';   
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

        $raws['dataset']       = $email_data;
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 
    }

    public function isSocialRegister_post(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();
            //social type can be F/G/L/T
            if (!$this->post('social_type')){
                $flag = false;
            } else {
                $req_arr['social_type']   = $this->post('social_type', TRUE);
                
            }

           $req_arr['username']   = $this->post('email_id', TRUE);
            
            if (!$this->post('account_id')){
                $flag = false;
            } else {
                $req_arr['account_id']   = $this->post('account_id', TRUE);
                
            }

            if (!$this->post('user_access_token')){
                $flag = false;
            } else {
                $req_arr['user_access_token']   = $this->post('user_access_token', TRUE);
                
            }
            //pre($req_arr,1);

            if($flag) {
                if($req_arr['social_type']=='F'){
                    $facebookDtl=$this->user_model->fetchFacebookSocialLogins($req_arr);
                    if(is_array($facebookDtl) && count($facebookDtl)>0){
                        $detail['is_in_social']=1;
                        $detail['facebook_login_id']=$facebookDtl['facebook_login_id'];
                        $detail['facebook_account_id']=$facebookDtl['facebook_account_id'];
                    }else{

                        $userDTl=$this->user_model->fetchUser($req_arr);
                        if(is_array($userDTl) && count($userDTl)>0){
                            $data['user_id']=$userDTl['id'];
                           $isSocial=$this->user_model->fetchSocialLogins($data);
                           if(is_array($isSocial) && count($isSocial)>0){
                                $socialLogin['facebook_login_id']=$req_arr['username'];
                                $socialLogin['facebook_account_id']=$req_arr['account_id'];
                                $socialLogin['facebook_user_access_token']=$req_arr['user_access_token'];
                                $this->user_model->updateSocialLogins($socialLogin,$isSocial['id']);
                           }else{
                               $socialLogin['fk_user_id']=$userDTl['id'];
                               $socialLogin['facebook_login_id']=$req_arr['username'];
                                $socialLogin['facebook_account_id']=$req_arr['account_id'];
                                $socialLogin['facebook_user_access_token']=$req_arr['user_access_token'];
                               $this->user_model->addSocialLogins($socialLogin);
                           }


                          
                           $facebookDtl='';
                           $facebookDtl=$this->user_model->fetchFacebookSocialLogins($req_arr);
                            if(is_array($facebookDtl) && count($facebookDtl)>0){
                                $detail['is_in_social']=1;
                                $detail['facebook_login_id']=$facebookDtl['facebook_login_id'];
                                $detail['facebook_account_id']=$facebookDtl['facebook_account_id'];
                            }else{
                                $detail['is_in_social']=0;
                                $detail['facebook_login_id']='';
                                $detail['facebook_account_id']='';

                            }

                        }else{
                            $detail['is_in_social']=0;
                            $detail['facebook_login_id']='';
                            $detail['facebook_account_id']='';
                        }
                    }
                }

                if($req_arr['social_type']=='G'){
                    $facebookDtl=$this->user_model->fetchGoogleSocialLogins($req_arr);
                    if(is_array($facebookDtl) && count($facebookDtl)>0){
                        $detail['is_in_social']=1;
                        $detail['googleplus_login_id']=$facebookDtl['googleplus_login_id'];
                        $detail['googleplus_account_id']=$facebookDtl['googleplus_account_id'];
                    }else{
                         $userDTl=$this->user_model->fetchUser($req_arr);
                        if(is_array($userDTl) && count($userDTl)>0){
                           $data['user_id']=$userDTl['id'];
                           $isSocial=$this->user_model->fetchSocialLogins($data);
                           if(is_array($isSocial) && count($isSocial)>0){
                                $socialLogin['googleplus_login_id']=$req_arr['username'];
                               $socialLogin['googleplus_account_id']=$req_arr['account_id'];
                               $socialLogin['googleplus_user_access_token']=$req_arr['user_access_token'];
                                $this->user_model->updateSocialLogins($socialLogin,$isSocial['id']);
                           }else{
                               $socialLogin['fk_user_id']=$userDTl['id'];
                                $socialLogin['googleplus_login_id']=$req_arr['username'];
                               $socialLogin['googleplus_account_id']=$req_arr['account_id'];
                               $socialLogin['googleplus_user_access_token']=$req_arr['user_access_token'];
                               $this->user_model->addSocialLogins($socialLogin);
                           }
                           $facebookDtl='';
                           $facebookDtl=$this->user_model->fetchGoogleSocialLogins($req_arr);
                            if(is_array($facebookDtl) && count($facebookDtl)>0){
                                $detail['is_in_social']=1;
                                $detail['googleplus_login_id']=$facebookDtl['googleplus_login_id'];
                                $detail['googleplus_account_id']=$facebookDtl['googleplus_account_id'];
                            }else{
                                $detail['is_in_social']=0;
                                $detail['facebook_login_id']='';
                                $detail['facebook_account_id']='';

                            }

                        }else{
                            $detail['is_in_social']=0;
                            $detail['facebook_login_id']='';
                            $detail['facebook_account_id']='';
                        }
                    }
                }
                //log in status checking

                $http_response      = 'http_response_ok';
                $success_message    = '';  

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

        $raws['dataset']       = $detail;
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 
    }


    
     /* -------------------------------------
    //end of user controller
    */
}