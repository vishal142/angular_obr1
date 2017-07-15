<?php defined('BASEPATH') OR exit('No direct script access allowed');

error_reporting(0);
//error_reporting(E_ALL);

require_once('src/OAuth2/Autoloader.php');
require APPPATH . 'libraries/api/REST_Controller.php';
require APPPATH . '/libraries/api/AppExtrasAPI.php';
require APPPATH . '/libraries/api/AppAndroidGCMPushAPI.php';
require APPPATH . '/libraries/api/AppApplePushAPI.php';



    /**
    *   @SWG\Swagger(
    *       schemes={"https"},
    *       host="apis.finzo.in",
    *       basePath="/api_1_0_0"
    *   ),
    *
    *   @SWG\Info(
    *       title="Connection",
    *       description="all api which are related to agent screen are listed here",
    *       version="2.0.0"
    *   ),
    *   @SWG\Definition(
    *       definition="getNoConnections",
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
    *       definition="getProduct",
    *       type="object",
    *       description="API Request Format",
    *       allOf={
    *           @SWG\Schema(
    *               @SWG\Property(property="user_id", format="string", type="integer"),
    *               @SWG\Property(property="pass_key", format="string", type="string"),
    *               @SWG\Property(property="fk_profession_type_id", format="string", type="integer"),
    *               @SWG\Property(property="fk_payment_type_id", format="string", type="integer"),
    *               @SWG\Property(property="product_type", format="string", type="string"),
    *             
    *          )
    *       },
    *    )

  **/

class Agent extends REST_Controller{
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
            $this->load->model('api_' . $this->config->item('test_api_ver') . '/login_model', 'login_model');
            $this->load->model('api_' . $this->config->item('test_api_ver') . '/product_model', 'product_model');
            $this->load->model('api_' . $this->config->item('test_api_ver') . '/agent_model', 'agent_model');
            $this->load->model('api_' . $this->config->item('test_api_ver') . '/mcoins_model', 'mcoins_model');
             $this->load->model('api_' . $this->config->item('test_api_ver') . '/notifications_model', 'notifications_model');
            
            $this->load->library('email');
            $this->load->library('calculation');
           
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

    *    @SWG\Definition(
    *   definition="getAllRepayments",
    *   type="object",
    *   description="API Request Format",
    *   allOf={
    *     @SWG\Schema(
    *       @SWG\Property(property="user_id", format="string", type="integer"),
    *       @SWG\Property(property="pass_key", format="string", type="string"),
    *      @SWG\Property(property="pass_key", format="string", type="string"),
    *       
    *       )
    *   },
        ) 
    **/
    public function getAllRepayments_post(){

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
            if ($this->post('user_id')<0){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

            if (!intval($this->post('page'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['page']    = $this->post('page', TRUE);
            }

             if (!$this->post('user_type')){
                $flag       = false;
                $error_message='user type can not be null';
            } else {
                $req_arr['user_type']    = $this->post('user_type', TRUE);
            }

            if (!intval($this->post('page_size'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['page_size']    = $this->post('page_size', TRUE);
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
            }

            $req_arr['search_status']    = $this->post('search_status', TRUE);
            $req_arr['search_start_date']    = $this->post('search_start_date', TRUE);
            $req_arr['search_end_date']    = $this->post('search_end_date', TRUE);
            $req_arr['search_tenure']    = $this->post('search_tenure', TRUE);
           
           
            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                

                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                    $req_arr['page_limit']=$pageLimit;
                    $req_arr['limit']=$limit;
                  
                    $all_cash_token=array();
                    $all_cash_token  = $this->product_model->allRepayments($req_arr);
                    
                   
                        $result_arr['all_cash_token']         = $all_cash_token;
                        $result_arr['no_connection']         = count($all_cash_token);
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

        $raws['dataset']       = $result_arr;

        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 
    }

    public function getAllCashRequest_post(){

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
            if ($this->post('user_id')<0){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

            if (!intval($this->post('page'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['page']    = $this->post('page', TRUE);
            }

            
            if (!intval($this->post('page_size'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['page_size']    = $this->post('page_size', TRUE);
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
            }

            
           
           
            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                

                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                    $req_arr['page_limit']=$pageLimit;
                    $req_arr['limit']=$limit;
                  
                    $all_cash_token=array();
                    $all_cash_token  = $this->product_model->allAgentCashTaken($req_arr);
                    
                   
                        $result_arr['all_cash_token']         = $all_cash_token;
                        $result_arr['no_connection']         = count($all_cash_token);

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

        $raws['dataset']       = $result_arr;

        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 
    }

    public function getRewardList_post(){

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

          
           

            if($flag) {
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                  
                    $reWardEarning=$this->agent_model->fetchUserRewards($req_arr);
                    $data=array();
                    if(is_array($reWardEarning) && count($reWardEarning)>0){
                        $i=0;
                        foreach($reWardEarning as $rewards){
                            $data[$i]['fk_activity_user_id']=$rewards['fk_activity_user_id'];
                            $data[$i]['no_rward']=$this->agent_model->getTotalReward($req_arr['user_id'],$rewards['fk_activity_user_id']);
                            $data[$i]['residence_city']=$rewards['residence_city'];
                            $data[$i]['residence_state']=$rewards['residence_state'];
                            $res['user_id']=$rewards['fk_activity_user_id'];
                            $education_details=$this->profile_model->getEducationUserMain($res);
                            $data[$i]['education_id']=$education_details['id'];
                            $data[$i]['degree_name']=$education_details['degree_name'];
                            $data[$i]['name_of_institution']=$education_details['name_of_institution'];
                           


                            $data[$i]['display_name']=$rewards['display_name'];
                            if($rewards['s3_media_version']!=''){
                                $data[$i]['profile_url'] = $this->config->item('bucket_url').$rewards['fk_activity_user_id'].'/profile/'.$rewards['fk_activity_user_id'].'.'.$rewards['profile_picture_file_extension'].'?versionId='.$rewards['s3_media_version'];
                           }else{
                                $data[$i]['profile_url'] = '';
                         
                           }
                         

                            $i++;
                        }
                        
                    } 

                        $http_response      = 'http_response_ok';
                        $success_message    = '';
                   
                   
                   $result_arr['connection_list']         = $data;
                   $result_arr['no_connection']         = count($data);
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
        $raws['dataset']       = $result_arr;
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 
    }

    public function getRewardDtl_post(){

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

            if (!intval($this->post('connection_id'))){
                $flag       = false;
                $error_message='connection id can not be null';
            } else {
                $req_arr['connection_id']    = $this->post('connection_id', TRUE);
            }

           

            if($flag) {
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                    $row['user_id']=$req_arr['connection_id'];
                    $profileBasic=$this->profile_model->fetchTempProfileMain($row);
                    if(is_array($profileBasic) && count($profileBasic)>0){
                            $res_data['connection_id']=$profileBasic['fk_user_id'];
                            $res_data['display_name']=$profileBasic['display_name'];
                            $res_data['f_name']=$profileBasic['f_name'];
                            $res_data['m_name']=$profileBasic['m_name'];
                            $res_data['l_name']=$profileBasic['l_name'];
                            $res_data['residence_street1']=$profileBasic['residence_street1'];
                            $res_data['residence_city']=$profileBasic['residence_city'];
                            $res_data['residence_state']=$profileBasic['residence_state'];
                          
                            $res['user_id']=$req_arr['connection_id'];
                            $education_details=$this->profile_model->getEducationUserMain($res);
                            $res_data['education_id']=$education_details['id'];
                            $res_data['degree_name']=$education_details['degree_name'];
                            $res_data['year_of_joining']=$education_details['year_of_joining'];
                            $res_data['year_of_graduation']=$education_details['year_of_graduation'];
                            $res_data['name_of_institution']=$education_details['name_of_institution'];
                            $reward_details['total']=$this->agent_model->getTotalReward($req_arr['user_id'],$req_arr['connection_id']);
                            $reward_details['signup']=$this->agent_model->getTotalReward($req_arr['user_id'],$req_arr['connection_id'],1);
                            $reward_details['transaction']=$this->agent_model->getTotalReward($req_arr['user_id'],$req_arr['connection_id'],2);
                            $reward_details['delay']=$this->agent_model->getTotalReward($req_arr['user_id'],$req_arr['connection_id'],3);
                            $res_data['reward_details']=$reward_details;

                            if($profileBasic['s3_media_version']!=''){
                                $res_data['profile_url'] = $this->config->item('bucket_url').$profileBasic['fk_user_id'].'/profile/'.$profileBasic['fk_user_id'].'.'.$profileBasic['profile_picture_file_extension'].'?versionId='.$profileBasic['s3_media_version'];
                            }else{
                                $res_data['profile_url']='';
                            }
                                

                        $http_response      = 'http_response_ok';
                        $success_message    = '';

                    }else{
                        $http_response      = 'http_response_bad_request';
                        $error_message      = 'No record found';  
                    }

                    
                   $result_arr['connection_list']         = $res_data;
                     
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
        $raws['dataset'] = $result_arr;
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 
    }

    public function getAllUnregisteredUsers_post(){

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


           

            if($flag) {
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                    
                    $allUsers=$this->agent_model->getUnregisteredUsers($req_arr['user_id']);
                    if(is_array($allUsers) && count($allUsers)>0){
                        $i=0;
                        $all_user=array();
                        foreach($allUsers as $user){
                            $all_user[$i]['invitee_name']=$user['invitee_name'];
                            $all_user[$i]['name']=substr($user['invitee_name'],0,1);
                            $all_user[$i]['email_id']=$user['email_id'];
                            $all_user[$i]['mobile_no']=$user['mobile_no'];
                            $i++;
                        }
                    }
                    $http_response      = 'http_response_ok';
                    $success_message    = '';

                    
                   $result_arr['users_list']         = $all_user;
                   $result_arr['no_users']         = count($allUsers);
                     
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
        $raws['dataset'] = $result_arr;
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 
    }

    public function getAllInCompleteUsers_post(){

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


           

            if($flag) {
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                    
                    $allUsers=$this->agent_model->getInCompleteProfile($req_arr['user_id']);
                    $http_response      = 'http_response_ok';
                    $success_message    = '';
                    $all_user=array();
                     $i=0;
                        foreach($allUsers as $user){
                             $all_user[$i]['id']=$user['id'];
                            $all_user[$i]['invitee_name']=$user['display_name'];
                            $all_user[$i]['name']=substr($user['email_id'],0,1);
                            $all_user[$i]['email_id']=$user['email_id'];
                            $all_user[$i]['mobile_number']=$user['mobile_number'];
                            $i++;
                        }
                   $result_arr['users_list']         = $all_user;
                   $result_arr['no_users']         = count($all_user);
                     
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
        $raws['dataset'] = $result_arr;
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 
    }


    public function getUserDtl_post(){

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
            if ($this->post('user_id')<0){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

            if (!intval($this->post('connection_id'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['connection_id']    = $this->post('connection_id', TRUE);
            }

        
           
           
            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                

                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                   
                    $all_cash_request=array();
                    $all_cash_request  = $this->product_model->allUserCashTaken($req_arr['connection_id']);
                    $all_cash_made  = $this->product_model->allUserCashMade($req_arr['connection_id']);
                     $all_cash_due  = $this->product_model->allUserCashDue($req_arr['connection_id']);
                     $all_mCoins  = $this->mcoins_model->getTotalMcoin($req_arr['connection_id']);

                    
                   
                        $result_arr['all_cash_request']         = $all_cash_request;
                        $result_arr['no_cash_request']         = count($all_cash_request);
                        $result_arr['all_cash_made']         = $all_cash_made;
                        $result_arr['no_cash_made']         = count($all_cash_made);
                        $result_arr['all_cash_due']         = $all_cash_due;
                        $result_arr['no_cash_due']         = count($all_cash_due);
                        $result_arr['total_mcoins']         = $all_mCoins;

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

        $raws['dataset']       = $result_arr;

        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 
    }


    public function getReminder_post(){

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
            if ($this->post('user_id')<0){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

            if (!intval($this->post('connection_id'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['connection_id']    = $this->post('connection_id', TRUE);
            }

        
           
           
            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                

                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                   
                        //add into notification table

                                $notification_code='AGN-IPR';
                                $notificationDtl=$this->notifications_model->getNotificationTypes($notification_code);
                                $notification_data['fk_user_id']=$req_arr['connection_id'];
                                $notification_data['notification_for_mode']='B';
                                $notification_data['fk_notification_type_id']=$notificationDtl['id'];
                                $notification_data['notification_message']=' OOPS! You still did not complete your profile';
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
                                    $pushType=$notification_code;
                                    $message=$notification_data['notification_message'];
                                    $total_new_notifications=$this->notifications_model->getAllNewNotifications($req_arr['connection_id']);
                                    $display_name='';
                                    $push_message = "{~message~:~" . $message . "~,~total_new_notifications~:~" . $total_new_notifications . "~,~accepted_id~:~" . $req_arr['user_id'] . "~,~user_id~:~" . $req_arr['connection_id'] . "~,~name~:~" . $display_name . "~,~profile_image~:~" . $profile_picture_file_url . "~,~push_type~:~" . $notification_code . "~}";
                                   
                                    $this->sendMobilePushNotifications($req_arr['connection_id'],$req_arr['user_id'],$push_message,$pushType,$message);
                                    
                                //end push message

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

        $raws['dataset']       = $result_arr;

        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 
    }

    public function getResend_post(){

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
            if ($this->post('user_id')<0){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

            if (!$this->post('email_id')){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['email_id']    = $this->post('email_id', TRUE);
            }

        
           
           
            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                

                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                   
                        //send email
                        //initialising codeigniter email
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
                        $this->email->to($req_arr['email_id']);          
                        $this->email->subject('mPokket: Reminder Email');

                        $email_data['verification_code']= $req_arr['email_id'];
                        
                        

                        $email_body= $this->parser->parse('email_templates/reminder', $email_data, true);


                        $this->email->message($email_body);            
                        $this->email->send();

                        // email send end

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

        $raws['dataset']       = $result_arr;

        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 
    }
    
    Public function sendMobilePushNotifications($receiver_id,$user_id,$push_message,$pushType,$message){
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