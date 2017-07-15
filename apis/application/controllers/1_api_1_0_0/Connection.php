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
    *       title="Connection",
    *       description="all api which are related to profiles screen are listed here",
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
    *       definition="getCurrentConnection",
    *       type="object",
    *       description="API Request Format",
    *       allOf={
    *           @SWG\Schema(
    *               @SWG\Property(property="user_id", format="string", type="integer"),
    *               @SWG\Property(property="pass_key", format="string", type="string"),
    *               @SWG\Property(property="page", format="string", type="integer"),
    *               @SWG\Property(property="page_size", format="string", type="integer"),
    *               @SWG\Property(property="search_text", format="string", type="string"),
    *             
    *          )
    *       },
    *    )
    *   @SWG\Definition(
    *       definition="getSentConnection",
    *       type="object",
    *       description="API Request Format",
    *       allOf={
    *           @SWG\Schema(
    *               @SWG\Property(property="user_id", format="string", type="integer"),
    *               @SWG\Property(property="pass_key", format="string", type="string"),
    *               @SWG\Property(property="page", format="string", type="integer"),
    *               @SWG\Property(property="page_size", format="string", type="integer"),
    *               @SWG\Property(property="search_text", format="string", type="string"),
    *             
    *          )
    *       },
    *    )
    *   @SWG\Definition(
    *       definition="getRecevConnection",
    *       type="object",
    *       description="API Request Format",
    *       allOf={
    *           @SWG\Schema(
    *               @SWG\Property(property="user_id", format="string", type="integer"),
    *               @SWG\Property(property="pass_key", format="string", type="string"),
    *               @SWG\Property(property="page", format="string", type="integer"),
    *               @SWG\Property(property="page_size", format="string", type="integer"),
    *               @SWG\Property(property="search_text", format="string", type="string"),
    *             
    *          )
    *       },
    *    )
    *   @SWG\Definition(
    *       definition="sendConnectionRequest",
    *       type="object",
    *       description="API Request Format",
    *       allOf={
    *           @SWG\Schema(
    *               @SWG\Property(property="user_id", format="string", type="integer"),
    *               @SWG\Property(property="pass_key", format="string", type="string"),
    *               @SWG\Property(property="connection_id", format="string", type="integer"),
    *             
    *          )
    *       },
    *    )
    *   @SWG\Definition(
    *       definition="acceptConnectionRequest",
    *       type="object",
    *       description="API Request Format",
    *       allOf={
    *           @SWG\Schema(
    *               @SWG\Property(property="user_id", format="string", type="integer"),
    *               @SWG\Property(property="pass_key", format="string", type="string"),
    *               @SWG\Property(property="sender_id", format="string", type="integer"),
    *             
    *          )
    *       },
    *    )
    *   @SWG\Definition(
    *       definition="getConnectionDtl",
    *       type="object",
    *       description="API Request Format",
    *       allOf={
    *           @SWG\Schema(
    *               @SWG\Property(property="user_id", format="string", type="integer"),
    *               @SWG\Property(property="pass_key", format="string", type="string"),
    *               @SWG\Property(property="connection_id", format="string", type="integer"),
    *             
    *          )
    *       },
    *    )

  **/
require APPPATH . '/libraries/api/AppExtrasAPI.php';
require APPPATH . '/libraries/api/AppAndroidGCMPushAPI.php';
require APPPATH . '/libraries/api/AppApplePushAPI.php';
class Connection extends REST_Controller{
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
            $this->load->model('api_' . $this->config->item('test_api_ver') . '/connection_model', 'connection_model');
            $this->load->model('api_' . $this->config->item('test_api_ver') . '/profile_model', 'profile_model');
            $this->load->model('api_' . $this->config->item('test_api_ver') . '/login_model', 'login_model');
            $this->load->model('api_' . $this->config->item('test_api_ver') . '/mcoins_model', 'mcoins_model');
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
     *      path="/connection/getNoConnections",
     *      tags={"getNoConnections: "},
     *      summary="get no of connections",
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
     *          @SWG\Schema(ref="#/definitions/getNoConnections"),
     *      ),       
     *  )
     *
    **/ 

    public function getNoConnections_post(){

        

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

            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                //echo $this->db->last_query(); //exit;
                //pre($login_status_arr,1);

                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                    
                   $data['no_connections']   = $this->connection_model->getCurrentConnection($req_arr['user_id']);
                   $data['mcoins']   = $this->mcoins_model->getTotalMcoin($req_arr['user_id']);
                   $data['current_connection']   = $this->connection_model->getCurrentConnection($req_arr['user_id']);
                   $data['invite_sent']   = $this->connection_model->getInviteSent($req_arr['user_id']);
                   $data['invite_sent']   = $this->connection_model->getInviteSent($req_arr['user_id']);
                   $data['invite_received']   = $this->connection_model->getConnectionReceived($req_arr['user_id']);

                    $user_level  = $this->connection_model->getUserLevel($data['mcoins']);
                    
                    if(is_array($user_level) && count($user_level)>0){
                        $level_rank=$user_level[0]['level_rank'];
                        $level=$level_rank-1;
                        $levelRank=$this->connection_model->getUserLevelByRank($level);
                        $data['user_level']=$levelRank['level_name'];
                    }else{
                        $data['user_level']='Blue';
                    }
                   
                   
                        $result_arr         = $data;
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
     *      path="/connection/getCurrentConnection",
     *      tags={"getCurrentConnection: "},
     *      summary="get no of connections",
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
     *          @SWG\Schema(ref="#/definitions/getCurrentConnection"),
     *      ),       
     *  )
     *
    **/ 

    public function getCurrentConnection_post(){

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

            $req_arr['search_text']    = $this->post('search_text', TRUE);
            $search_text=$req_arr['search_text'] ;
            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                //echo $this->db->last_query(); //exit;
                //pre($login_status_arr,1);

                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                    
                   $current_connections  = $this->connection_model->getCurrentConnectionList($req_arr['user_id'],$search_text,$pageLimit,$limit);
                    if(is_array($current_connections) && count($current_connections)>0){
                        $i=0;
                        foreach($current_connections as $current){
                            $id=$current['fk_user_id'];
                            $data[$i]['id']=$current['fk_user_id'];
                            $data[$i]['fk_user_id']=$current['fk_user_id'];
                            $data[$i]['user_id']=$current['user_id'];
                            $data[$i]['fk_connection_id']=$current['fk_connection_id'];
                            //$userDetails=$this->user_model->fetchUserDeatils($id);
                            $data[$i]['display_name']=$current['display_name'];
                            $data[$i]['f_name']=$current['f_name'];
                            $data[$i]['m_name']=$current['m_name'];
                            $data[$i]['l_name']=$current['l_name'];
                            $data[$i]['residence_street1']=$current['residence_street1'];
                            $data[$i]['residence_city']=$current['residence_city'];
                            $data[$i]['residence_state']=$current['residence_state'];
                            $data[$i]['l_name']=$current['l_name'];
                            $data[$i]['l_name']=$current['l_name'];
                            $res['user_id']=$id;
                            $education_details=$this->profile_model->getEducationUserMain($res);
                            $data[$i]['education_id']=$education_details['id'];
                            $data[$i]['degree_name']=$education_details['degree_name'];
                            $data[$i]['year_of_joining']=$education_details['year_of_joining'];
                            $data[$i]['year_of_graduation']=$education_details['year_of_graduation'];
                            $data[$i]['name_of_institution']=$education_details['name_of_institution'];
                            $data[$i]['no_mcoins']=$this->mcoins_model->getTotalMcoin($req_arr['user_id'],$id);
                            if($current['s3_media_version']!=''){
                            $data[$i]['profile_url'] = $this->config->item('bucket_url').$id.'/profile/'.$id.'.'.$current['profile_picture_file_extension'].'?versionId='.$current['s3_media_version'];
                            }else{
                                $data[$i]['profile_url']='';
                            }

                            $i++;
                        }

                    }
                   
                        $result_arr['connection_list']         = $data;
                        $result_arr['no_connection']         = count($data);
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

    /**
     *  
     *   @SWG\Post(
     *      path="/connection/getSentConnection",
     *      tags={"getSentConnection: "},
     *      summary="get no of connections",
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
     *          @SWG\Schema(ref="#/definitions/getSentConnection"),
     *      ),       
     *  )
     *
    **/ 

    public function getSentConnection_post(){

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

            if ($this->post('page')==''){
                $flag       = false;
                $error_message='page can not be null';
            } else {
                $req_arr['page']    = $this->post('page', TRUE);
            }

            if (!intval($this->post('page_size'))){
                $flag       = false;
                $error_message='page size can not be null';
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
                //echo $this->db->last_query(); //exit;
                //pre($login_status_arr,1);

                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                   
                        $current_connections  = $this->connection_model->getSentConnectionList($req_arr['user_id'],$search_text,$pageLimit,$limit);
                  
                        $data=array();
                    if(is_array($current_connections) && count($current_connections)>0){
                        $i=0;
                        foreach($current_connections as $current){
                            if($current['fk_user_id']!=$req_arr['user_id']){
                               
                                $data[$i]['id']=$current['fk_user_id'];
                                //$userDetails=$this->user_model->fetchUserDeatils($id);
                                $param['fk_user_id']=$req_arr['user_id'];
                                $param['fk_connection_id']=$req_arr['user_id'];
                                /*$isConnected=$this->connection_model->isAdded($param);
                                if($isConnected>0){
                                    $data[$i]['display_name']
                                }*/
                                $data[$i]['display_name']=$current['display_name'];
                                $data[$i]['f_name']=$current['f_name'];
                                $data[$i]['m_name']=$current['m_name'];
                                $data[$i]['l_name']=$current['l_name'];
                                $data[$i]['residence_street1']=$current['residence_street1'];
                                $data[$i]['residence_city']=$current['residence_city'];
                                $data[$i]['residence_state']=$current['residence_state'];
                                $data[$i]['l_name']=$current['l_name'];
                                $data[$i]['l_name']=$current['l_name'];
                                $res['user_id']=$id;
                                $education_details=$this->profile_model->getEducationUserMain($res);
                                $data[$i]['education_id']=$education_details['id'];
                                $data[$i]['degree_name']=$education_details['degree_name'];
                                $data[$i]['year_of_joining']=$education_details['year_of_joining'];
                                $data[$i]['year_of_graduation']=$education_details['year_of_graduation'];
                                $data[$i]['name_of_institution']=$education_details['name_of_institution'];
                                if($current['s3_media_version']!=''){
                                    $data[$i]['profile_url'] = $this->config->item('bucket_url').$current['fk_user_id'].'/profile/'.$current['fk_user_id'].'.'.$current['profile_picture_file_extension'].'?versionId='.$current['s3_media_version'];
                                }else{
                                    $data[$i]['profile_url']='';
                                }
                                
                                $i++;
                            }
                        }

                    }
                   
                        $result_arr['connection_list']         = $data;
                        $result_arr['no_connection']         = count($data);
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



    /**
     *  
     *   @SWG\Post(
     *      path="/connection/getGlobalSearch",
     *      tags={"getSuggestion: "},
     *      summary="get no of connections",
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
     *          @SWG\Schema(ref="#/definitions/getGlobalSearch"),
     *      ),       
     *  )
     *
    **/ 

    public function getGlobalSearch_post(){

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

            if ($this->post('page')==''){
                $flag       = false;
                $error_message='page can not be null';
            } else {
                $req_arr['page']    = $this->post('page', TRUE);
            }

            if (!intval($this->post('page_size'))){
                $flag       = false;
                $error_message='page size can not be null';
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
            if (!$this->post('search_text')){
                $flag       = false;
                $error_message='search text can not be null';
            } else {
                $req_arr['search_text']    = $this->post('search_text', TRUE);
            }

           
            $search_text=$req_arr['search_text'] ;
            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                //echo $this->db->last_query(); //exit;
                //pre($login_status_arr,1);

                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                     $current_connections  = $this->connection_model->getSearchConnectionList($search_text,$pageLimit,$limit);
                     $data=array();
                    if(is_array($current_connections) && count($current_connections)>0){
                        $i=0;
                        foreach($current_connections as $current){
                            if($current['fk_user_id']!=$req_arr['user_id']){
                               
                                $data[$i]['id']=$current['fk_user_id'];
                                //$userDetails=$this->user_model->fetchUserDeatils($id);
                                $param['fk_user_id']=$req_arr['user_id'];
                                $param['fk_connection_id']=$current['fk_user_id'];
                                $isConnected=$this->connection_model->isConnected($param);
                               
                                $data[$i]['display_name_connect']=$isConnected;
                                if(is_array($isConnected) && count($isConnected)>0){
                                    if($isConnected['connection_status']=='C'){
                                        $data[$i]['status']='C';
                                    }else{
                                        if($isConnected['fk_user_id']=$req_arr['user_id']){
                                            $data[$i]['status']='S'; // connection sent
                                        }else if($isConnected['fk_connection_id']=$req_arr['user_id']){
                                             $data[$i]['status']='R'; // connection received
                                        }
                                    }
                                }else{
                                    $data[$i]['status']='N';
                                }

                                $data[$i]['display_name']=$current['display_name'];
                                $data[$i]['f_name']=$current['f_name'];
                                $data[$i]['m_name']=$current['m_name'];
                                $data[$i]['l_name']=$current['l_name'];
                                $data[$i]['residence_street1']=$current['residence_street1'];
                                $data[$i]['residence_city']=$current['residence_city'];
                                $data[$i]['residence_state']=$current['residence_state'];
                                $data[$i]['l_name']=$current['l_name'];
                                $data[$i]['l_name']=$current['l_name'];
                                $education_details=$this->profile_model->getEducationUserMain($req_arr);
                                $data[$i]['education_id']=$education_details['id'];
                                $data[$i]['degree_name']=$education_details['degree_name'];
                                $data[$i]['year_of_joining']=$education_details['year_of_joining'];
                                $data[$i]['year_of_graduation']=$education_details['year_of_graduation'];
                                $data[$i]['name_of_institution']=$education_details['name_of_institution'];
                                 if($current['s3_media_version']!=''){
                                    $data[$i]['profile_url'] = $this->config->item('bucket_url').$current['fk_user_id'].'/profile/'.$current['fk_user_id'].'.'.$current['profile_picture_file_extension'].'?versionId='.$current['s3_media_version'];
                                }else{
                                    $data[$i]['profile_url']='';
                                }
                                
                                $i++;
                            }
                        }

                    }
                   
                        $result_arr['connection_list']         = $data;
                        $result_arr['no_connection']         = count($data);
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




    /**
     *  
     *   @SWG\Post(
     *      path="/connection/getSuggestion",
     *      tags={"getSuggestion: "},
     *      summary="get no of connections",
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
     *          @SWG\Schema(ref="#/definitions/getSuggestion"),
     *      ),       
     *  )
     *
    **/ 

    public function getSuggestion_post(){

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

            if ($this->post('page')==''){
                $flag       = false;
                $error_message='page can not be null';
            } else {
                $req_arr['page']    = $this->post('page', TRUE);
            }

            if (!intval($this->post('page_size'))){
                $flag       = false;
                $error_message='page size can not be null';
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

            $req_arr['search_text']    = $this->post('search_text', TRUE);
            $search_text=$req_arr['search_text'] ;
            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                //echo $this->db->last_query(); //exit;
                //pre($login_status_arr,1);

                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                     $current_connections  = $this->connection_model->getSuggestionList($req_arr['user_id'],$pageLimit,$limit);
                     $data=array();
                    if(is_array($current_connections) && count($current_connections)>0){
                        $i=0;
                        foreach($current_connections as $current){
                            if($current['fk_user_id']!=$req_arr['user_id']){
                               
                                $data[$i]['id']=$current['fk_user_id'];
                                
                                $data[$i]['display_name']=$current['display_name'];
                                $data[$i]['f_name']=$current['f_name'];
                                $data[$i]['m_name']=$current['m_name'];
                                $data[$i]['l_name']=$current['l_name'];
                                $data[$i]['residence_street1']=$current['residence_street1'];
                                $data[$i]['residence_city']=$current['residence_city'];
                                $data[$i]['residence_state']=$current['residence_state'];
                                $data[$i]['l_name']=$current['l_name'];
                                $data[$i]['l_name']=$current['l_name'];
                                $education_details=$this->profile_model->getEducationUserMain($req_arr);
                                $data[$i]['education_id']=$education_details['id'];
                                $data[$i]['degree_name']=$education_details['degree_name'];
                                $data[$i]['year_of_joining']=$education_details['year_of_joining'];
                                $data[$i]['year_of_graduation']=$education_details['year_of_graduation'];
                                $data[$i]['name_of_institution']=$education_details['name_of_institution'];
                                 if($current['s3_media_version']!=''){
                                    $data[$i]['profile_url'] = $this->config->item('bucket_url').$current['fk_user_id'].'/profile/'.$current['fk_user_id'].'.'.$current['profile_picture_file_extension'].'?versionId='.$current['s3_media_version'];
                                }else{
                                    $data[$i]['profile_url']='';
                                }
                                
                                $i++;
                            }
                        }

                    }
                   
                        $result_arr['connection_list']         = $data;
                        $result_arr['no_connection']         = count($data);
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
    /**
     *  
     *   @SWG\Post(
     *      path="/connection/getRecevConnection",
     *      tags={"getRecevConnection: "},
     *      summary="get no of connections",
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
     *          @SWG\Schema(ref="#/definitions/getRecevConnection"),
     *      ),       
     *  )
     *
    **/ 

    public function getRecevConnection_post(){

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
            $req_arr['search_text']    = $this->post('search_text', TRUE);
            $search_text=$req_arr['search_text'] ;
            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                //echo $this->db->last_query(); //exit;
                //pre($login_status_arr,1);

                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                    
                   $current_connections  = $this->connection_model->getRecvConnectionList($req_arr['user_id'],$search_text,$pageLimit,$limit);
                    if(is_array($current_connections) && count($current_connections)>0){
                        $i=0;
                        $data=array();
                        foreach($current_connections as $current){
                            
                            $data[$i]['id']=$current['fk_user_id'];
                            //$userDetails=$this->user_model->fetchUserDeatils($id);
                            $data[$i]['display_name']=$current['display_name'];
                            $data[$i]['connection_status']=$current['connection_status'];
                            $data[$i]['f_name']=$current['f_name'];
                            $data[$i]['m_name']=$current['m_name'];
                            $data[$i]['l_name']=$current['l_name'];
                            $data[$i]['residence_street1']=$current['residence_street1'];
                            $data[$i]['residence_city']=$current['residence_city'];
                            $data[$i]['residence_state']=$current['residence_state'];
                            $data[$i]['l_name']=$current['l_name'];
                            $data[$i]['l_name']=$current['l_name'];
                            $education_details=$this->profile_model->getEducationUserMain($req_arr);
                            $data[$i]['education_id']=$education_details['id'];
                            $data[$i]['degree_name']=$education_details['degree_name'];
                            $data[$i]['year_of_joining']=$education_details['year_of_joining'];
                            $data[$i]['year_of_graduation']=$education_details['year_of_graduation'];
                            $data[$i]['name_of_institution']=$education_details['name_of_institution'];
                            if($current['s3_media_version']!=''){
                                $data[$i]['profile_url'] = $this->config->item('bucket_url').$data[$i]['id'].'/profile/'.$data[$i]['id'].'.'.$current['profile_picture_file_extension'].'?versionId='.$current['s3_media_version'];
                            }else{
                                $data[$i]['profile_url']='';
                            }
                            
                            $i++;
                        }

                    }
                   
                        $result_arr['connection_list']         = $data;
                        $result_arr['no_connection']         = count($data);
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

    /**
     *  
     *   @SWG\Post(
     *      path="/connection/sendConnectionRequest",
     *      tags={"sendConnectionRequest: "},
     *      summary="get no of connections",
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
     *          @SWG\Schema(ref="#/definitions/sendConnectionRequest"),
     *      ),       
     *  )
     *
    **/ 

    public function sendConnectionRequest_post(){

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
                $error_message='connection_id can not be null';
            } else {
                $req_arr['connection_id']    = $this->post('connection_id', TRUE);
            }

            if($this->post('user_id')==$this->post('connection_id')){
                $flag       = false;
                $error_message='You can not sent request to yourself';
            }

            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                //echo $this->db->last_query(); //exit;
                //pre($login_status_arr,1);

                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                    $this->connection_model->isAdded($data);
                    $data['fk_user_id']=$req_arr['user_id'];
                    $data['fk_connection_id']=$req_arr['connection_id'];
                    $data['connection_status']='P';
                    $is_add=$this->connection_model->isAdded($data);
                    if($is_add==0){
                        $this->connection_model->add($data);
                        $notification_code='CON-RQT';
                    }else{
                        $notification_code='CON-RRM';
                        
                    }
                        //add into notification table
                        $userDtl=$this->profile_model->fetchTempProfileMain($req_arr);
                        
                        $notificationDtl=$this->notifications_model->getNotificationTypes($notification_code);
                        $notification_data['fk_user_id']=$req_arr['connection_id'];
                        $notification_data['notification_for_mode']='B';
                        $notification_data['fk_notification_type_id']=$notificationDtl['id'];
                        $notification_data['notification_message']=$userDtl['display_name'].' has sent you connection request';
                        //serialized data
                        $json_data_array['display_name']=$userDtl['display_name'];
                        $json_data_array['sender_id']=$req_arr['user_id'];
                        $json_data_array['notification_code']=$notification_code;

                        if($userDtl['s3_media_version']!=''){
                            $profile_picture_file_url = $this->config->item('bucket_url').$req_arr['user_id'].'/profile/'.$req_arr['user_id'].'.'.$userDtl['profile_picture_file_extension'].'?versionId='.$userDtl['s3_media_version'];
                        }else{
                            $profile_picture_file_url='';
                        }
                        $json_data_array['img_url']=$profile_picture_file_url;
                        $json_data_serialize=json_encode($json_data_array);
                        //end of serialized data
                        $notification_data['routing_json']=$json_data_serialize;

                        $this->notifications_model->addUserNotification($notification_data);
                        $total_new_notifications=$this->notifications_model->getAllNewNotifications($req_arr['connection_id']);
                        //send push message
                            $push_message = "{~message~:~" . $notification_data['notification_message'] . "~,~total_new_notifications~:~" . $total_new_notifications . "~,~sender_id~:~" . $req_arr['user_id'] . "~,~user_id~:~" . $req_arr['connection_id'] . "~,~name~:~" . $userDtl['display_name'] . "~,~profile_image~:~" . $profile_picture_file_url . "~,~push_type~:~" . $notification_code . "~}";

                            $pushType=$notification_code;
                            $message=$notification_data['notification_message'];
                           
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

    /**
     *  
     *   @SWG\Post(
     *      path="/connection/acceptConnectionRequest",
     *      tags={"acceptConnectionRequest: "},
     *      summary="get no of connections",
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
     *          @SWG\Schema(ref="#/definitions/acceptConnectionRequest"),
     *      ),       
     *  )
     *
    **/ 

    public function acceptConnectionRequest_post(){

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

            if (!intval($this->post('sender_id'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['sender_id']    = $this->post('sender_id', TRUE);
            }

            if($this->post('user_id')==$this->post('sender_id')){
                $flag       = false;
                $error_message='You can not sent request to yourself';
            }

            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                //echo $this->db->last_query(); //exit;
                //pre($login_status_arr,1);

                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                   
                    $data['fk_user_id']=$req_arr['sender_id'];
                    $data['fk_connection_id']=$req_arr['user_id'];
                    $is_add=$this->connection_model->isAdded($data);
                    if($is_add>0){
                        $this->connection_model->update($data);

                        //add into notification table
                        $userDtl=$this->profile_model->fetchTempProfileMain($req_arr);
                        $notification_code='CON-ACP';
                        $notificationDtl=$this->notifications_model->getNotificationTypes($notification_code);
                        $notification_data['fk_user_id']=$req_arr['sender_id'];
                        $notification_data['notification_for_mode']='B';
                        $notification_data['fk_notification_type_id']=$notificationDtl['id'];
                        $notification_data['notification_message']=$userDtl['display_name'].' has accepted your connection request';
                        //serialized data
                        $json_data_array['display_name']=$userDtl['display_name'];
                        $json_data_array['accepted_id']=$req_arr['user_id'];
                        $json_data_array['notification_code']=$notification_code;

                        if($userDtl['s3_media_version']!=''){
                            $profile_picture_file_url = $this->config->item('bucket_url').$req_arr['user_id'].'/profile/'.$req_arr['user_id'].'.'.$userDtl['profile_picture_file_extension'].'?versionId='.$userDtl['s3_media_version'];
                        }else{
                            $profile_picture_file_url='';
                        }
                        $json_data_array['img_url']=$profile_picture_file_url;
                        $json_data_serialize=json_encode($json_data_array);
                        //end of serialized data
                        $notification_data['routing_json']=$json_data_serialize;

                        $this->notifications_model->addUserNotification($notification_data);
                        //send push message
                            $pushType=$notification_code;
                            $message=$notification_data['notification_message'];
                            $total_new_notifications=$this->notifications_model->getAllNewNotifications($req_arr['sender_id']);

                            $push_message = "{~message~:~" . $message . "~,~total_new_notifications~:~" . $total_new_notifications . "~,~accepted_id~:~" . $req_arr['user_id'] . "~,~user_id~:~" . $req_arr['sender_id'] . "~,~name~:~" . $userDtl['display_name'] . "~,~profile_image~:~" . $profile_picture_file_url . "~,~push_type~:~" . $notification_code . "~}";

                            $this->sendMobilePushNotifications($req_arr['sender_id'],$req_arr['user_id'],$push_message,$pushType,$message);

                        //end push message
                        
                        $http_response      = 'http_response_ok';
                        $success_message    = '';
                    }else{
                         $http_response      = 'http_response_bad_request';
                        $error_message      = 'Wrong connection id';  

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
     *      path="/connection/getConnectionDtl",
     *      tags={"getConnectionDtl: "},
     *      summary="get no of connections",
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
     *          @SWG\Schema(ref="#/definitions/getConnectionDtl"),
     *      ),       
     *  )
     *
    **/ 

    public function getConnectionDtl_post(){

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
                $error_message='user id can not be null';
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
                            $mcoin_details['total']=$this->mcoins_model->getTotalMcoin($req_arr['user_id'],$req_arr['connection_id']);
                            $mcoin_details['signup']=$this->mcoins_model->getTotalMcoin($req_arr['user_id'],$req_arr['connection_id'],1);
                            $mcoin_details['transaction']=$this->mcoins_model->getTotalMcoin($req_arr['user_id'],$req_arr['connection_id'],2);
                            $mcoin_details['delay']=$this->mcoins_model->getTotalMcoin($req_arr['user_id'],$req_arr['connection_id'],3);
                            $res_data['mcoins_details']=$mcoin_details;

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


    /**
     *  
     *   @SWG\Post(
     *      path="/connection/disconnectUser",
     *      tags={"disconnectUser: "},
     *      summary="get no of connections",
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
     *          @SWG\Schema(ref="#/definitions/disconnectUser"),
     *      ),       
     *  )
     *
    **/ 

    public function disconnectUser_post(){

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
                $error_message='connection can not be null';
            } else {
                $req_arr['connection_id']    = $this->post('connection_id', TRUE);
            }

           

            if($flag) {
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                    $row['fk_connection_id']=$req_arr['connection_id'];
                    $row['fk_user_id']=$req_arr['user_id'];
                    $isadded=$this->connection_model->isConnected($row);
                    if(is_array($isadded) && count($isadded)>0){
                        
                        $this->connection_model->disConnectUser($isadded[0]);
                        $http_response      = 'http_response_ok';
                        $success_message    = '';
                    }else{
                        $http_response      = 'http_response_bad_request';
                        $error_message      = 'Wrong connection id';  
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
     *      path="/connection/declineUser",
     *      tags={"declineUser: "},
     *      summary="get no of connections",
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
     *          @SWG\Schema(ref="#/definitions/declineUser"),
     *      ),       
     *  )
     *
    **/ 


    public function cancelUser_post(){

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
                $error_message='user id can not be null';
            } else {
                $req_arr['connection_id']    = $this->post('connection_id', TRUE);
            }

           

            if($flag) {
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                    $row['fk_user_id']=$req_arr['user_id'];
                    $row['fk_connection_id']=$req_arr['connection_id'];
                    $isadded=$this->connection_model->isAdded($row);
                    if($isadded>0){
                        $this->connection_model->deleteUser($row);
                        $http_response      = 'http_response_ok';
                        $success_message    = '';
                    }else{
                        $http_response      = 'http_response_bad_request';
                        $error_message      = 'Wrong connection id';  
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

        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 
    }


    public function declineUser_post(){

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
                $error_message='user id can not be null';
            } else {
                $req_arr['connection_id']    = $this->post('connection_id', TRUE);
            }

           

            if($flag) {
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                    $row['fk_user_id']=$req_arr['connection_id'];
                    $row['fk_connection_id']=$req_arr['user_id'];
                    $isadded=$this->connection_model->isAdded($row);
                    if($isadded>0){
                        $this->connection_model->deleteUser($row);
                        $http_response      = 'http_response_ok';
                        $success_message    = '';
                    }else{
                        $http_response      = 'http_response_bad_request';
                        $error_message      = 'Wrong connection id';  
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
     *      path="/connection/byDateConnection",
     *      tags={"byDateConnection: "},
     *      summary="get no of connections",
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
     *          @SWG\Schema(ref="#/definitions/byDateConnection"),
     *      ),       
     *  )
     *
    **/ 

    public function byDateConnection_post(){

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
                  
                    $mCoinsEarning=$this->mcoins_model->fetchUserMcoins($req_arr);
                    $data=array();
                    if(is_array($mCoinsEarning) && count($mCoinsEarning)>0){
                        $i=0;
                        foreach($mCoinsEarning as $mcoins){
                            $data[$i]['id']=$mcoins['id'];
                            $data[$i]['fk_activity_user_id']=$mcoins['fk_activity_user_id'];
                            $data[$i]['display_name']=$mcoins['display_name'];
                            $data[$i]['fk_mcoin_activity_id']=$mcoins['fk_mcoin_activity_id'];
                            $data[$i]['fk_loan_id']=$mcoins['fk_loan_id'];
                            $data[$i]['non_referred_connections']=$mcoins['non_referred_connections'];
                            $data[$i]['referred_connections']=$mcoins['referred_connections'];
                            $data[$i]['own_activity']=$mcoins['own_activity '];
                            $data[$i]['total_mcoins']=intval($mcoins['non_referred_connections']) + intval($mcoins['referred_connections']) + intval($mcoins['own_activity']);
                            $earning_timestamp=$this->user_model->getServerTimeZone($req_arr['user_id'],$mcoins['earning_timestamp']);
                            $dataTime=strtotime($earning_timestamp);

                            $data[$i]['earning_timestamp']=date('M d,Y',$dataTime);
                            if($mcoins['s3_media_version']!=''){
                                $data[$i]['profile_url'] = $this->config->item('bucket_url').$mcoins['fk_activity_user_id'].'/profile/'.$mcoins['fk_activity_user_id'].'.'.$mcoins['profile_picture_file_extension'].'?versionId='.$mcoins['s3_media_version'];
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



    /**
     *  
     *   @SWG\Post(
     *      path="/connection/byDateConnection",
     *      tags={"byDateConnection: "},
     *      summary="get no of connections",
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
     *          @SWG\Schema(ref="#/definitions/byDateConnection"),
     *      ),       
     *  )
     *
    **/ 

    public function byConnection_post(){

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
                  
                    $mCoinsEarning=$this->mcoins_model->fetchUserMcoinsByuser($req_arr);
                    $data=array();
                    if(is_array($mCoinsEarning) && count($mCoinsEarning)>0){
                        $i=0;
                        foreach($mCoinsEarning as $mcoins){
                            $data[$i]['fk_activity_user_id']=$mcoins['fk_activity_user_id'];
                            $data[$i]['no_mcoins']=$this->mcoins_model->getTotalMcoin($req_arr['user_id'],$mcoins['fk_activity_user_id']);
                            $data[$i]['residence_city']=$mcoins['residence_city'];
                            $data[$i]['residence_state']=$mcoins['residence_state'];
                            $res['user_id']=$mcoins['fk_activity_user_id'];
                            $education_details=$this->profile_model->getEducationUserMain($res);
                            $data[$i]['education_id']=$education_details['id'];
                            $data[$i]['degree_name']=$education_details['degree_name'];
                            $data['name_of_institution']=$education_details['name_of_institution'];
                           


                            $data[$i]['display_name']=$mcoins['display_name'];
                            if($mcoins['s3_media_version']!=''){
                                $data[$i]['profile_url'] = $this->config->item('bucket_url').$mcoins['fk_activity_user_id'].'/profile/'.$mcoins['fk_activity_user_id'].'.'.$mcoins['profile_picture_file_extension'].'?versionId='.$mcoins['s3_media_version'];
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

    /**
     *  
     *   @SWG\Post(
     *      path="/connection/byDateConnection",
     *      tags={"byDateConnection: "},
     *      summary="get no of connections",
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
     *          @SWG\Schema(ref="#/definitions/byDateConnection"),
     *      ),       
     *  )
     *
    **/ 

    public function connectionDetailsByDate_post(){

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

            if (!intval($this->post('user_mcoin_id'))){
                $flag       = false; 
                $error_message='user id can not be null';
            } else {
                $req_arr['user_mcoin_id']    = $this->post('user_mcoin_id', TRUE);
            }

          
           

            if($flag) {
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                  
                    $mcoins=$this->mcoins_model->connectionDetailsBydate($req_arr);
                    $data=array();
                    if(is_array($mcoins) && count($mcoins)>0){
                        $i=0;
                        
                            $data['fk_activity_user_id']=$mcoins['fk_activity_user_id'];
                            $data['fk_mcoin_activity_id']=$mcoins['fk_mcoin_activity_id'];
                            if($data['fk_mcoin_activity_id']==1){
                                $data['activity_message']='Registered Successfully';
                            }
                            $earning_timestamp=$this->user_model->getServerTimeZone($req_arr['user_id'],$mcoins['earning_timestamp']);
                            $dataTime=strtotime($earning_timestamp);
                            $data['earning_timestamp']=date('M d,Y',$dataTime);

                            $data['non_referred_connections']=$mcoins['non_referred_connections'];
                            $data['referred_connections']=$mcoins['referred_connections'];
                            $data['own_activity']=$mcoins['own_activity '];
                            $data['total_mcoins']=intval($mcoins['non_referred_connections']) + intval($mcoins['referred_connections']) + intval($mcoins['own_activity']);

                            $data['residence_city']=$mcoins['residence_city'];
                            $data['residence_state']=$mcoins['residence_state'];
                            $res['user_id']=$mcoins['fk_activity_user_id'];
                            $education_details=$this->profile_model->getEducationUserMain($res);
                            $data['education_id']=$education_details['id'];
                            $data['degree_name']=$education_details['degree_name'];
                            $data['name_of_institution']=$education_details['name_of_institution'];
                            $data['display_name']=$mcoins['display_name'];

                            if($mcoins['s3_media_version']!=''){
                                $data['profile_url'] = $this->config->item('bucket_url').$mcoins['fk_activity_user_id'].'/profile/'.$mcoins['fk_activity_user_id'].'.'.$mcoins['profile_picture_file_extension'].'?versionId='.$mcoins['s3_media_version'];
                           }else{
                                $data['profile_url'] = '';
                         
                           }
                         

                            $i++;
                       
                        
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

    public function connectionDashboardList_post(){

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
                  
                    $allConnections=$this->connection_model->getConnectionDashboard($req_arr['user_id']);
                    $data=array();
                    if(is_array($allConnections) && count($allConnections)>0){
                        $i=0;
                            foreach($allConnections as $connections){
                                $data[$i]['fk_user_id']=$connections['fk_user_id'];
                                $data[$i]['display_name']=$connections['display_name'];
                                $data[$i]['actual_loan_p']=$connections['actual_loan_p'];
                                $data[$i]['loan_disbursed_timestamp']=$connections['loan_disbursed_timestamp'];
                            
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