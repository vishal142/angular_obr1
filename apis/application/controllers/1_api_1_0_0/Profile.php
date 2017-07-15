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
    *       definition="addEducation",
    *       type="object",
    *       description="API Request Format",
    *       allOf={
    *           @SWG\Schema(
    *               @SWG\Property(property="user_id", format="string", type="integer"),
    *               @SWG\Property(property="pass_key", format="string", type="string"),
    *               @SWG\Property(property="fk_degree_type_id", format="string", type="integer"),
    *               @SWG\Property(property="fk_degree_id", format="string", type="integer"),
    *               @SWG\Property(property="degree_name", format="string", type="string"),
    *               @SWG\Property(property="study_name", format="string", type="string"),
    *               @SWG\Property(property="fk_field_of_study_id", format="string", type="integer"),
    *               @SWG\Property(property="year_of_joining", format="string", type="integer"),
    *               @SWG\Property(property="year_of_graduation", format="string", type="integer"),
    *               @SWG\Property(property="name_of_institution", format="string", type="string"),
    *               @SWG\Property(property="fk_pincode_id", format="string", type="integer"),
    *               @SWG\Property(property="grades_marks", format="string", type="integer"), 
    *               @SWG\Property(property="has_part_time_job", format="string", type="integer"), 
                    @SWG\Property(property="img_name", format="file", type="file"), 
    *          )
    *       },
    *    )

    *    @SWG\Definition(
    *   definition="fetchAllEducation",
    *   type="object",
    *   description="API Request Format",
    *   allOf={
    *     @SWG\Schema(
    *       @SWG\Property(property="user_id", format="string", type="integer"),
    *       @SWG\Property(property="pass_key", format="string", type="string"),
    *       
    *       )
    *   },
        )

    *    @SWG\Definition(
    *   definition="fetchEducation",
    *   type="object",
    *   description="API Request Format",
    *   allOf={
    *     @SWG\Schema(
    *       @SWG\Property(property="user_id", format="string", type="integer"),
    *       @SWG\Property(property="pass_key", format="string", type="string"),
    *       @SWG\Property(property="education_id", format="string", type="string"),
    *       
    *       )
    *   },
        )

   
    *   @SWG\Definition(
    *       definition="profile_details",
    *       type="object",
    *       description="API Request Format",
    *       allOf={
    *           @SWG\Schema(           
    *               @SWG\Property(property="pass_key", format="string", type="string"),
    *               @SWG\Property(property="user_id", format="string", type="integer"),
    *           )
    *       },
    *   )
    *
    *   @SWG\Definition(
    *       definition="addKyc",
    *       type="object",
    *       description="Add KYC",
    *       allOf={
    *           @SWG\Schema(           
    *               @SWG\Property(property="fk_kyc_template_id", format="string", type="integer"),
    *               @SWG\Property(property="kyc_data", format="string", type="string"),
    *               @SWG\Property(property="user_id", format="string", type="integer"),
    *               @SWG\Property(property="pass_key", format="string", type="string")
    *            )
    *       },
    *   ),
    
    *   @SWG\Definition(
    *       definition="getKycDocuments",
    *       type="object",
    *       description="get KYC",
    *       allOf={
    *           @SWG\Schema(           
    *               @SWG\Property(property="user_id", format="string", type="string"),
    *               @SWG\Property(property="pass_key", format="string", type="integer"),
    *               @SWG\Property(property="document_type", format="string", type="string"),
    *               @SWG\Property(property="user_mode", format="string", type="string"),
    *               @SWG\Property(property="fk_profession_type_id", format="string", type="string"),
    *            )
    *       },
    *   ),
    *   @SWG\Definition(
    *       definition="addBank",
    *       type="object",
    *       description="get KYC",
    *       allOf={
    *           @SWG\Schema(           
    *               @SWG\Property(property="user_id", format="string", type="integer"),
    *               @SWG\Property(property="pass_key", format="string", type="string"),
    *               @SWG\Property(property="fk_bank_id", format="string", type="integer"),
    *               @SWG\Property(property="account_number", format="string", type="integer"),
    *            )
    *       },
    *   ),
    *   @SWG\Definition(
    *       definition="deleteProfileImage",
    *       type="object",
    *       description="delete profile iamge",
    *       allOf={
    *           @SWG\Schema(           
    *               @SWG\Property(property="user_id", format="string", type="integer"),
    *               @SWG\Property(property="pass_key", format="string", type="string"),
    *               @SWG\Property(property="version_id", format="string", type="string"),
    *               @SWG\Property(property="file_extension", format="string", type="string"),
    *            )
    *       },
    *   ),
    *   @SWG\Definition(
    *       definition="deleteKycImage",
    *       type="object",
    *       description="delete lyc image",
    *       allOf={
    *           @SWG\Schema(           
    *               @SWG\Property(property="user_id", format="string", type="integer"),
    *               @SWG\Property(property="pass_key", format="string", type="string"),
    *               @SWG\Property(property="version_id", format="string", type="string"),
    *               @SWG\Property(property="file_extension", format="string", type="string"),
    *               @SWG\Property(property="image_type", format="string", type="string"),
    *               @SWG\Property(property="kyc_id", format="string", type="integer"),
    *            )
    *       },
    *   ),
    *   @SWG\Definition(
    *       definition="deleteKycImage",
    *       type="object",
    *       description="delete lyc image",
    *       allOf={
    *           @SWG\Schema(           
    *               @SWG\Property(property="user_id", format="string", type="integer"),
    *               @SWG\Property(property="pass_key", format="string", type="string"),
    *               @SWG\Property(property="version_id", format="string", type="string"),
    *               @SWG\Property(property="file_extension", format="string", type="string"),
    *               @SWG\Property(property="image_type", format="string", type="string"),
    *               @SWG\Property(property="kyc_id", format="string", type="integer"),
    *            )
    *       },
    *   ),
    *   @SWG\Definition(
    *       definition="deleteEducationImage",
    *       type="object",
    *       description="delete lyc image",
    *       allOf={
    *           @SWG\Schema(           
    *               @SWG\Property(property="user_id", format="string", type="integer"),
    *               @SWG\Property(property="pass_key", format="string", type="string"),
    *               @SWG\Property(property="version_id", format="string", type="string"),
    *               @SWG\Property(property="file_extension", format="string", type="string"),
    *               @SWG\Property(property="education_id", format="string", type="integer"),
    *            )
    *       },
    *   ),

    **/

class Profile extends REST_Controller{
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
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/agentcode_model', 'agentcode_model');
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
     *      path="/profile/fetch_profile_details",
     *      tags={"fetch_profile_details: "},
     *      summary="fetch profile details",
     *      description="This api is used to fetch profile details",
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
     *          description="Post data to fetch profile details",
     *          required=true,
     *          type="string",
     *          @SWG\Schema(ref="#/definitions/profile_details"),
     *      ),       
     *   

     *  )
     *
    **/ 

    public function fetch_profile_details_post(){

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
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }
            if (!$this->post('user_id')){
                $flag       = false;
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

            if($flag) {
                $user_details_arr=array();
                $result_arr=array();
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                //echo $this->db->last_query(); //exit;
                //pre($login_status_arr,1);

                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                    
                    $user_details_arr   = $this->profile_model->getProfileDetails($login_status_arr);
                    $userMaindata=$this->profile_model->fetchTempProfileMain($req_arr);
                    $countdata=count($user_details_arr);
                    
                     
                    if(!empty($user_details_arr) && count($user_details_arr) > 0){
                        if(is_array($userMaindata) && count($userMaindata)>0){
                            $user_details_arr['is_restore_enable']='1';
                        }else{
                            $user_details_arr['is_restore_enable']='0';
                        }
                       
                        if($user_details_arr['s3_media_version']!='' && $user_details_arr['s3_media_version']!=NULL){
                             $profile_picture_file_url = $this->config->item('bucket_url').$req_arr['user_id'].'/profile/'.$req_arr['user_id'].'.'.$user_details_arr['profile_picture_file_extension'].'?versionId='.$user_details_arr['s3_media_version'];
                            $user_details_arr['img_url']=$profile_picture_file_url;
                            $user_details_arr['date_of_birth']=getJsDate($user_details_arr['date_of_birth']);

                        }else{
                            $user_details_arr['img_url']='';
                        }

                        if($user_details_arr['date_of_birth']!='0000-00-00'){
                            $date=date("d-M-Y",strtotime($user_details_arr['date_of_birth']));
                        }else{
                            $date='0000-00-00';
                        }

                        $user_details_arr['date_of_birth']=$date;

                        $user_details_arr['total_row']=$countdata;

                        $result_arr         = $user_details_arr;
                        $http_response      = 'http_response_ok';
                        $success_message    = 'Fetched profile details';  
                    } else {
                        if(is_array($userMaindata) && count($userMaindata)>0){
                            $user_details_arr['is_restore_enable']='1';
                        }else{
                            $user_details_arr['is_restore_enable']='0';
                        }
                        $http_response      = 'http_response_ok';
                        $error_message      = ''; 
                        $user_details['total_row']=$countdata;
                        $user_details['admin_status_profile_name']='P';
                        $user_details['admin_status_residence_address']='P';
                        $user_details['admin_status_other_info']='P';
                        $user_details['admin_status_permanent_address']='P';
                        $result_arr         = $user_details;
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
     *   @SWG\Delete(
     *      path="/profile/deleteProfileImage",
     *      tags={"deleteProfileImage"},
     *      summary="fetch profile details",
     *      description="This api is used to fetch profile details",
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
     *          description="Post data to fetch profile details",
     *          required=true,
     *          type="string",
     *          @SWG\Schema(ref="#/definitions/deleteProfileImage"),
     *      ),       
     *   

     *  )
     *
    **/
    public function deleteProfileImage_delete(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {


            $flag           = true;
            $req_arr        = array();

            if (!$this->delete('pass_key')){
                $flag       = false;
            } else {
                $req_arr['pass_key']    = $this->delete('pass_key', TRUE);
            }

            if (!$this->delete('user_id')){
                $flag       = false;
            } else {
                $req_arr['user_id']    = $this->delete('user_id', TRUE); 
                
            }

            if (!$this->delete('version_id')){
                $flag       = false;
            } else {
                $req_arr['version_id']    = $this->delete('version_id', TRUE);
                
            }

            if (!$this->delete('file_extension')){
                $flag       = false;
            } else {
                $req_arr['file_extension']    = $this->delete('file_extension', TRUE);
                
            }

            

            
            $raws = array();   
            if($flag) {
                //log in status checking
                $login_status_arr = $this->login_model->login_status_checking($req_arr);
                
                
                if(is_array($login_status_arr) &&  count($login_status_arr) > 0){

                        //  GET DATA FROM TEMP PROFILE BASIC TABLE               
                        $tempProfileBasic_details = $this->profile_model->fetchTempProfileBasic($req_arr);
                   
                        //  AVAILABLE DATA FOR ADMIN APPROVAL
                        if(!empty($tempProfileBasic_details) && count($tempProfileBasic_details) > 0){
                            $data['id']    = $tempProfileBasic_details['id'];
                            $data['has_profile_picture']    = 0;
                            $data['profile_picture_file_extension']    = NULL;
                            $data['s3_media_version']    = NULL;
                            $this->profile_model->updateTempProfileBasic($data);
                             $aws_target_file_path='resources/'.$req_arr['user_id'].'/profile/'.$req_arr['user_id'].'.'.$req_arr['file_extension'];
                                $response = $this->aws->deletefile($this->aws->bucket,$aws_target_file_path,$req_arr['version_id']);
                            

                        } else {    // NOT AVAILABLE ANY DATA FOR APPROVAL
                            $maindata = $this->profile_model->fetchTempProfileMain($req_arr);
                            if(is_array($maindata) && count($maindata)>0){
                                $data['id']    = $maindata['id'];
                                $data['fk_user_id']    = $maindata['fk_user_id'];
                               
                                $data['display_name']    = $maindata['display_name'];
                                $data['f_name']    = $maindata['f_name'];
                                $data['m_name']    = $maindata['m_name'];
                                $data['l_name']    = $maindata['l_name'];
                                $data['residence_street1']    = $maindata['residence_street1'];
                                $data['residence_street2']    = $maindata['residence_street2'];
                                $data['residence_street3']    = $maindata['residence_street3'];
                                $data['residence_post_office']    = $maindata['residence_post_office'];
                                $data['residence_city']    = $maindata['residence_city'];
                                $data['residence_district']    = $maindata['residence_district'];
                                $data['residence_state']    = $maindata['residence_state'];
                                $data['residence_zipcode']    = $maindata['residence_zipcode'];
                                $data['residence_phone']    = $maindata['residence_phone'];
                                $data['permanent_street1']    = $maindata['permanent_street1'];
                                $data['permanent_street2']    = $maindata['permanent_street2'];
                                $data['permanent_street3']    = $maindata['permanent_street3'];
                                $data['permanent_post_office']    = $maindata['permanent_post_office'];
                                $data['permanent_city']    = $maindata['permanent_city'];
                                $data['permanent_district']    = $maindata['permanent_district'];
                                $data['permanent_state']    = $maindata['permanent_state'];
                                $data['permanent_zipcode']    = $maindata['permanent_zipcode'];
                                $data['permanent_phone']    = $maindata['permanent_phone'];
                                $data['fk_profession_type_id']    = $maindata['fk_profession_type_id'];
                                $data['date_of_birth']    = $maindata['date_of_birth'];
                                $data['fathers_name']    = $maindata['fathers_name'];
                                $data['fk_gender_id']    = $maindata['fk_gender_id'];
                                $data['fk_marital_status_id']    = $maindata['fk_marital_status_id'];
                                $data['fk_residence_status_id']    = $maindata['fk_residence_status_id'];
                                
                                $data['has_profile_picture']    = 0;
                                $data['profile_picture_file_extension']    = NULL;
                                $data['s3_media_version']    = NULL;
                                $addTempProfileBasic_id   = $this->profile_model->addTempProfileBasic($data);
                                
                            }else{
                                $http_response      = 'http_response_bad_request';
                                $error_message      =  'Invalid parameter'; 
                            }
                            
                        }

                            
                       
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Wrong username or Password'; 
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
     *   path="/profile/fetchAllEducation",
     *   tags={"fetchAllEducation"},
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
            @SWG\Schema(ref="#/definitions/fetchAllEducation"),
     *   ),
        
   

     * )
     */  

    public function fetchAllEducation_post(){

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
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }
            if (!$this->post('user_id')){
                $flag       = false;
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
                
            }

            
              $raws = array();   
            if($flag) {
                //log in status checking
                $login_status_arr = $this->login_model->login_status_checking($req_arr);
                
                
                if(is_array($login_status_arr) &&  count($login_status_arr) > 0){

                            $education_list_tmp = $this->profile_model->getAllEducationTmp($req_arr);

                            if(is_array($education_list_tmp) && count($education_list_tmp)>0){
                                $i=0;
                                foreach($education_list_tmp as $education_tmp){
                                    $data[$i]['id']=$education_tmp['id'];
                                    $data[$i]['fk_user_id']=$education_tmp['fk_user_id'];
                                    $data[$i]['fk_degree_type_id']=$education_tmp['fk_degree_type_id'];
                                    $degree_type_details=$this->agentcode_model->getDegreeTypeName($education_tmp['fk_degree_type_id']);
                                    $data[$i]['degree_type']=$degree_type_details['degree_type'];
                                    $data[$i]['fk_degree_id']=$education_tmp['fk_degree_id'];
                                    $degree_details=$this->agentcode_model->getDegreeName($education_tmp['fk_degree_id']);
                                    $data[$i]['degree_name']=$degree_details['degree_name'];
                                    $data[$i]['fk_field_of_study_id']=$education_tmp['fk_field_of_study_id'];
                                    $study_details=$this->agentcode_model->getFieldOfStudyName($education_tmp);
                                    $data[$i]['field_of_study']=$study_details['field_of_study'];
                                    $data[$i]['year_of_joining']=$education_tmp['year_of_joining'];
                                    $data[$i]['year_of_graduation']=$education_tmp['year_of_graduation'];
                                    $data[$i]['name_of_institution']=$education_tmp['name_of_institution'];
                                    $data[$i]['fk_pincode_id']=$education_tmp['fk_pincode_id'];
                                    $data[$i]['marks']=$education_tmp['marks'];
                                    $data[$i]['out_of_range']=$education_tmp['out_of_range'];
                                    $data[$i]['is_file_uploaded']=$education_tmp['is_file_uploaded'];
                                    $data[$i]['file_extension']=$education_tmp['file_extension'];
                                    $data[$i]['education_status']=$education_tmp['education_status'];
                                    $data[$i]['work_status']=$education_tmp['work_status'];
                                    $pincode_details=$this->agentcode_model->getPincodeDetailsId($education_tmp['fk_pincode_id']);
                                    $data[$i]['pin_code']=$pincode_details['pin_code'];
                                    $data[$i]['city_name']=$pincode_details['city_name'];
                                    $data[$i]['state_name']=$pincode_details['state_name'];

                                    $data[$i]['admin_message_education']=$education_tmp['admin_message_education'];
                                    if($education_tmp['is_file_uploaded']=='1'){
                                        $data[$i]['img_url']=$this->config->item('bucket_url').$req_arr['user_id'].'/education/'.$education_tmp['id'].'.'.$education_tmp['file_extension'].'?versionId='.$education_tmp['s3_media_version'];
                                    }
                                    $data[$i]['admin_message_education']=$education_tmp['admin_message_education'];
                                    $i++;
                                }
                            }

                            $education_list = $this->profile_model->getAllEducation($req_arr);

                            if(is_array($education_list) && count($education_list)>0){
                               
                                foreach($education_list as $education){
                                        $is_in_tmp=$this->profile_model->iSInTmpTable($education);
                                        
                                    if($is_in_tmp==0){
                                        $data[$i]['id']=$education['id'];
                                        $data[$i]['fk_user_id']=$education['fk_user_id'];
                                        $data[$i]['fk_degree_type_id']=$education['fk_degree_type_id'];
                                        $degree_type_details=$this->agentcode_model->getDegreeTypeName($education['fk_degree_type_id']);
                                        $data[$i]['degree_type']=$degree_type_details['degree_type'];
                                        $data[$i]['fk_degree_id']=$education['fk_degree_id'];
                                        $degree_details=$this->agentcode_model->getDegreeName($education['fk_degree_id']);
                                        $data[$i]['degree_name']=$degree_details['degree_name'];
                                        $data[$i]['fk_field_of_study_id']=$education['fk_field_of_study_id'];
                                         $study_details=$this->agentcode_model->getFieldOfStudyName($education['fk_field_of_study_id']);
                                        $data[$i]['field_of_study']=$study_details['field_of_study'];
                                        $data[$i]['year_of_joining']=$education['year_of_joining'];
                                        $data[$i]['year_of_graduation']=$education['year_of_graduation'];
                                        $data[$i]['name_of_institution']=$education['name_of_institution'];
                                        $data[$i]['fk_pincode_id']=$education['fk_pincode_id'];
                                        $data[$i]['marks']=$education['marks'];
                                        $data[$i]['out_of_range']=$education['out_of_range'];
                                        $data[$i]['is_file_uploaded']=$education['is_file_uploaded'];
                                        $data[$i]['file_extension']=$education['file_extension'];
                                        $data[$i]['education_status']='A';
                                        $data[$i]['work_status']=$education['work_status'];
                                        $data[$i]['admin_message_education']='';
                                         $pincode_details=$this->agentcode_model->getPincodeDetails($education['fk_pincode_id']);
                                         $pincode_details=$this->agentcode_model->getPincodeDetailsId($education['fk_pincode_id']);
                                        $data[$i]['pin_code']=$pincode_details['pin_code'];
                                        $data[$i]['city_name']=$pincode_details['city_name'];
                                        $data[$i]['state_name']=$pincode_details['state_name'];

                                        if($education['is_file_uploaded']=='1'){
                                        $data[$i]['img_url']=$this->config->item('bucket_url').$req_arr['user_id'].'/education/'.$education['id'].'.'.$education['file_extension'].'?versionId='.$education['s3_media_version'];
                                        }
                                        $i++;
                                    }
                                }
                            }
                           
                            $raws['dataset'] =$data;
                            $raws['total_row'] =count($data);
                            $http_response      = 'http_response_ok';
                            $success_message    = ''; 
                       
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Wrong username or Password'; 
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
     *   path="/profile/fetchEducation",
     *   tags={"fetchEducation"},
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
            @SWG\Schema(ref="#/definitions/fetchEducation"),
     *   ),
        
   

     * )
     */  

    public function fetchEducation_post(){

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
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }
            if (!$this->post('user_id')){
                $flag       = false;
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
                
            }

            if (!$this->post('education_id')){
                $flag       = false;
            } else {
                $req_arr['education_id']    = $this->post('education_id', TRUE);
                
            }


            
            $raws = array();   
            if($flag) {
                //log in status checking
                $login_status_arr = $this->login_model->login_status_checking($req_arr);
                
                
                if(is_array($login_status_arr) &&  count($login_status_arr) > 0){
                    $education_details=array();
                            $education = $this->profile_model->getEducationTmp($req_arr['education_id']);
                            if(is_array($education) && count($education)>0){
                                $isinEducationMain=$this->profile_model->isinEducationMain($req_arr);
                                if($isinEducationMain>0){
                                    $education_details['is_enable_restore']='1';
                                }else{
                                    $education_details['is_enable_restore']='0';
                                }
                            }else{
                                
                               $education = $this->profile_model->getEducation($req_arr['education_id']);
                            }

                           
                                        $education_details['id']=$education['id'];

                                        $education_details['fk_user_id']=$education['fk_user_id'];
                                        $education_details['fk_degree_type_id']=$education['fk_degree_type_id'];
                                        $degree_type_details=$this->agentcode_model->getDegreeTypeName($education['fk_degree_type_id']);
                                        $education_details['degree_type']=$degree_type_details['degree_type'];
                                        $education_details['fk_degree_id']=$education['fk_degree_id'];
                                        $degree_details=$this->agentcode_model->getDegreeName($education['fk_degree_id']);
                                        $education_details['degree_name']=$degree_details['degree_name'];
                                        $education_details['fk_field_of_study_id']=$education['fk_field_of_study_id'];
                                        $study_details=$this->agentcode_model->getFieldOfStudyName($education);
                                      
                                        $education_details['field_of_study']=$study_details['field_of_study'];
                                        $education_details['year_of_joining']=$education['year_of_joining'];
                                        $education_details['year_of_graduation']=$education['year_of_graduation'];
                                        $education_details['name_of_institution']=$education['name_of_institution'];
                                        $education_details['fk_pincode_id']=$education['fk_pincode_id'];
                                        $education_details['marks']=$education['marks'];
                                        $education_details['out_of_range']=$education['out_of_range'];
                                        if($education['s3_media_version']!=''){
                                            $education_details['is_file_uploaded']='1';
                                        }else{
                                          $education_details['is_file_uploaded']='0';  
                                        }
                                        $education_details['file_extension']=$education['file_extension'];
                                        $education_details['education_status']=$education['education_status'];
                                        $education_details['work_status']=$education['work_status'];
                                        $education_details['s3_media_version']=$education['s3_media_version'];
                                        $education_details['admin_message_education']='';
                                         $pincode_details=$this->agentcode_model->getPincodeDetails($education['fk_pincode_id']);
                                         $pincode_details=$this->agentcode_model->getPincodeDetailsId($education['fk_pincode_id']);
                                        $education_details['pin_code']=$pincode_details['pin_code'];
                                        $education_details['city_name']=$pincode_details['city_name'];
                                        $education_details['state_name']=$pincode_details['state_name'];

                                        if($education['is_file_uploaded']=='1'){
                                            $education_details['img_url']=$this->config->item('bucket_url').$req_arr['user_id'].'/education/'.$education['id'].'.'.$education['file_extension'].'?versionId='.$education['s3_media_version'];
                                        }

                            $data['education_details']=$education_details;
                            $raws['dataset'] =$data;
                            $http_response      = 'http_response_ok';
                            $success_message    = ''; 
                       
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Wrong username or Password'; 
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
     *      path="/profile/edit_profile_details",
     *      tags={"Profile: "},
     *      summary="edit basic profile details",
     *      description="This api is used to edit profile details",
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
     *         name="pass_key",
     *         in="formData",
     *         description="passkey ",
     *         required=true,
     *         type="string"
     *     ),     
     *      @SWG\Parameter(
     *         name="user_id",
     *         in="formData",
     *         description="passkey ",
     *         required=true,
     *         type="string"
     *     ),     
     *      @SWG\Parameter(
     *         name="profile_name",
     *         in="formData",
     *         description="passkey ",
     *         required=true,
     *         type="string"
     *     ),     
     *      @SWG\Parameter(
     *         name="first_name",
     *         in="formData",
     *         description="passkey ",
     *         required=true,
     *         type="string"
     *     ),     
     *      @SWG\Parameter(
     *         name="middle_name",
     *         in="formData",
     *         description="passkey ",
     *         required=false,
     *         type="string"
     *     ),     
     *      @SWG\Parameter(
     *         name="last_name",
     *         in="formData",
     *         description="passkey ",
     *         required=true,
     *         type="string"
     *     ),     
     *      @SWG\Parameter(
     *         name="res_street1",
     *         in="formData",
     *         description="passkey ",
     *         required=true,
     *         type="string"
     *     ),     
     *      @SWG\Parameter(
     *         name="res_street2",
     *         in="formData",
     *         description="passkey ",
     *         required=false,
     *         type="string"
     *     ),     
     *      @SWG\Parameter(
     *         name="res_street3",
     *         in="formData",
     *         description="passkey ",
     *         required=false,
     *         type="string"
     *     ),     
     *      @SWG\Parameter(
     *         name="res_pincode",
     *         in="formData",
     *         description="passkey ",
     *         required=true,
     *         type="string"
     *     ),     
     *      @SWG\Parameter(
     *         name="res_city",
     *         in="formData",
     *         description="passkey ",
     *         required=true,
     *         type="string"
     *     ),     
     *      @SWG\Parameter(
     *         name="res_state",
     *         in="formData",
     *         description="passkey ",
     *         required=true,
     *         type="string"
     *     ),     
     *      @SWG\Parameter(
     *         name="pmnt_street1",
     *         in="formData",
     *         description="passkey ",
     *         required=true,
     *         type="string"
     *     ),     
     *      @SWG\Parameter(
     *         name="pmnt_street2",
     *         in="formData",
     *         description="passkey ",
     *         required=false,
     *         type="string"
     *     ),     
     *      @SWG\Parameter(
     *         name="pmnt_street3",
     *         in="formData",
     *         description="passkey ",
     *         required=false,
     *         type="string"
     *     ),     
     *      @SWG\Parameter(
     *         name="pmnt_pincode",
     *         in="formData",
     *         description="passkey ",
     *         required=true,
     *         type="string"
     *     ),     
     *      @SWG\Parameter(
     *         name="pmnt_city",
     *         in="formData",
     *         description="passkey ",
     *         required=true,
     *         type="string"
     *     ),     
     *   
     *      @SWG\Parameter(
     *         name="pmnt_state",
     *         in="formData",
     *         description="passkey ",
     *         required=true,
     *         type="string"
     *     ),     
     *      @SWG\Parameter(
     *         name="fk_profession_type_id",
     *         in="formData",
     *         description="passkey ",
     *         required=true,
     *         type="integer"
     *     ),     
     *      @SWG\Parameter(
     *         name="dob",
     *         in="formData",
     *         description="passkey ",
     *         required=true,
     *         type="string"
     *     ),     
     *      @SWG\Parameter(
     *         name="father_name",
     *         in="formData",
     *         description="passkey ",
     *         required=true,
     *         type="string"
     *     ),     
     *      @SWG\Parameter(
     *         name="gender",
     *         in="formData",
     *         description="passkey ",
     *         required=true,
     *         type="string"
     *     ),     
     *      @SWG\Parameter(
     *         name="pmnt_city",
     *         in="formData",
     *         description="passkey ",
     *         required=true,
     *         type="string"
     *     ),     
     *      @SWG\Parameter(
     *         name="marital_status",
     *         in="formData",
     *         description="passkey ",
     *         required=true,
     *         type="string"
     *     ),     
     *      @SWG\Parameter(
     *         name="residence_status",
     *         in="formData",
     *         description="passkey ",
     *         required=true,
     *         type="string"
     *     ),     
     *      @SWG\Parameter(
     *         name="profile_image",
     *         in="formData",
     *         description="passkey ",
     *         required=true,
     *         type="file"
     *     ),     
    
     * )
     */  
    public function edit_profile_details_post(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();

        //if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            //$error_message = 'Invalid Token';
           // $http_response = 'http_response_unauthorized';
        
        //} else {

            $flag           = true;
            $has_profile_picture = false;
            $req_arr        = $data = array();

            if(!$this->post('pass_key') && $flag){
                $flag       = false;
                $error_message='passkey can not be null';
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }
            if(!$this->post('user_id') && $flag){
                $flag       = false;
                $error_message='User id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
                $data['fk_user_id']    = $req_arr['user_id'];
            }

            

            if(!$this->post('profile_name') && $flag){
                $flag       = false;
                $error_message='Profile name can not be null';
            } else {
                $data['display_name']    = $this->post('profile_name', TRUE);
            }
            if(!$this->post('first_name') && $flag){
                $flag       = false;
                $error_message='first name can not be null';
            } else {
                if(!ctype_alpha($this->post('first_name'))){
                    $flag       = false;
                    $error_message='Name should contain only alphabets';
                }else{
                   $data['f_name']    = $this->post('first_name', TRUE); 
                }
                
            }            
            $data['m_name']    = $this->post('middle_name', TRUE);
            if($this->post('middle_name', TRUE)!=''){
                if(!ctype_alpha($this->post('middle_name', TRUE))){
                    $flag       = false;
                    $error_message='Middle name should be alphabets only';
                }
            }

            if(!$this->post('last_name') && $flag){
                $flag       = false;
                $error_message='last name can not be null';
            } else {
                if(!ctype_alpha($this->post('last_name'))){
                    $flag       = false;
                    $error_message='Name should contain only alphabets';
                }else{
                   $data['l_name']    = $this->post('last_name', TRUE); 
                }
            }
            if(!$this->post('res_street1') && $flag){
                $flag       = false;
                $error_message='street1 can not be null';
            } else {
                $data['residence_street1']    = $this->post('res_street1', TRUE);
            }

            if($this->post('res_street2')){
                $data['residence_street2']    = $this->post('res_street2', TRUE);
            }else{
                 $data['residence_street2']='';
            }

            if($this->post('res_street3')){
           
                $data['residence_street3']    = $this->post('res_street3', TRUE);
            }else{
                $data['residence_street3'] ='';
            }

            if(!$this->post('res_pincode') && $flag){
                $flag       = false;
                $error_message='pin code can not be null';
            } else {
                $data['residence_zipcode']    = $this->post('res_pincode', TRUE);
            }
            if(!$this->post('res_city') && $flag){
                $flag       = false;
                $error_message='city can not be null';
            } else {
                $data['residence_city']    = $this->post('res_city', TRUE);
            }
            if(!$this->post('res_state') && $flag){
                $flag       = false;
                $error_message='State can not be null';
            } else {
                $data['residence_state']    = $this->post('res_state', TRUE);
            }

            if(!$this->post('pmnt_street1') && $flag){
                $flag       = false;
                $error_message='Street1 can not be null';
            } else {
                $data['permanent_street1']    = $this->post('pmnt_street1', TRUE);
            }

            if($this->post('pmnt_street2')){
                $data['permanent_street2']    = $this->post('pmnt_street2', TRUE);
            }else{
                 $data['permanent_street2'] ='';
            }

            if($this->post('pmnt_street3')){
                $data['permanent_street3']    = $this->post('pmnt_street3', TRUE);
            }else{
                $data['permanent_street3'] ='';

            }
            if(!$this->post('pmnt_pincode') && $flag){
                $flag       = false;
                $error_message='pincode can not be null';
            } else {
                 if(!intval($this->post('pmnt_pincode'))){
                    $flag       = false;
                    $error_message='Name should contain only numbers';
                }else{
                   $data['permanent_zipcode']    = $this->post('pmnt_pincode', TRUE); 
                }
                
            }
            if(!$this->post('pmnt_city') && $flag){
                $flag       = false;
                $error_message='City can not be null';
            } else {
                $data['permanent_city']    = $this->post('pmnt_city', TRUE);
            }
            if(!$this->post('pmnt_state') && $flag){
                $flag       = false;
                $error_message='State can not be null';
            } else {
                $data['permanent_state']    = $this->post('pmnt_state', TRUE);
            }
            if(!$this->post('occupation') && $flag){
                $flag       = false;
                $error_message='fk_profession id can not be null';
            } else {
                $data['fk_profession_type_id']    = $this->post('occupation', TRUE);
            }
            if(!$this->post('dob') && $flag){
                $flag       = false;
                $error_message='dob can not be null';
            } else {
                $dob   = $this->post('dob', TRUE);
                $data['date_of_birth'] =getDatabaseDate($dob);

            }            
            if(!$this->post('father_name') && $flag){
                $flag       = false;
                $error_message='father name can not be null';
            } else {
                $data['fathers_name']    = $this->post('father_name', TRUE);
            }
            if(!$this->post('gender') && $flag){
                $flag       = false;
                $error_message='gender can not be null';
            } else {
                $data['fk_gender_id']    = $this->post('gender', TRUE);
            }
            if(!$this->post('marital_status') && $flag){
                $flag       = false;
                $error_message='marital status can not be null';
            } else {
                $data['fk_marital_status_id']    = $this->post('marital_status', TRUE);
            }
            if(!$this->post('residence_status') && $flag){
                $error_message='resedence status can not be null';
                $flag       = false;
            } else {
                $data['fk_residence_status_id']    = $this->post('residence_status', TRUE);
            }

            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                //pre($login_status_arr,1);

                //if(!empty($login_status_arr) && count($login_status_arr) > 0){

                    $addTempProfileBasic_id = 0;    
                    $profile_picture_file_url = ''; 
                    $action_status = true;

                    //  GET DATA FROM TEMP PROFILE BASIC TABLE               
                    $tempProfileBasic_details = $this->profile_model->fetchTempProfileBasic($req_arr);
                    $profile_picture_file_url='';
                    //  IF PROFILE PICTURE FILE UPLOADED
                    // image upload 
                       

                      
                    //  IF FILE UPLOAD DONE SUCCESSFULLY
                    if($action_status){

                        //  GET DATA FROM MAIN PROFILE BASIC TABLE               
                        $user_BasicProfileDetails_arr = $this->profile_model->getBasicProfileDetailsFromMain($login_status_arr);

                        //  COMPARE WITH PREVIOUS VALUE
                        if(!empty($user_BasicProfileDetails_arr) && count($user_BasicProfileDetails_arr) > 0){
                            $profile_name_arr = array(
                                                    'display_name',
                                                    'f_name',
                                                    'm_name',
                                                    'l_name',
                                                );

                            $residence_address_arr = array(
                                                    'residence_street1',
                                                    'residence_street2',
                                                    'residence_street3',
                                                    'residence_zipcode',
                                                    'residence_city',
                                                    'residence_state',
                                                );

                            $permanent_address_arr = array(
                                                    'permanent_street1',
                                                    'permanent_street2',
                                                    'permanent_street3',
                                                    'permanent_zipcode',
                                                    'permanent_city',
                                                    'permanent_state',
                                                );

                            $other_info_arr = array(
                                                    'fk_profession_type_id',
                                                    'date_of_birth',
                                                    'fathers_name',
                                                    'fk_gender_id',
                                                    'fk_marital_status_id',
                                                    'fk_residence_status_id',
                                                );

                            $admin_status_profile_name = $admin_status_residence_address = $admin_status_permanent_address = $admin_status_other_info = true;

                            foreach ($data as $key => $value) {
                                if(in_array($key, $profile_name_arr) && $admin_status_profile_name){
                                    if($data[$key] != $user_BasicProfileDetails_arr[$key]){
                                        $admin_status_profile_name = false;
                                    }
                                }
                                else if(in_array($key, $residence_address_arr) && $admin_status_residence_address){
                                    if($data[$key] != $user_BasicProfileDetails_arr[$key]){
                                        $admin_status_residence_address = false;
                                    }
                                }
                                else if(in_array($key, $permanent_address_arr) && $admin_status_permanent_address){
                                    if($data[$key] != $user_BasicProfileDetails_arr[$key]){
                                        $admin_status_permanent_address = false;
                                    }
                                }
                                else if(in_array($key, $other_info_arr) && $admin_status_other_info){
                                    if($data[$key] != $user_BasicProfileDetails_arr[$key]){
                                        $admin_status_other_info = false;                                    
                                    }
                                }
                            }

                            if(!$admin_status_profile_name){
                                $data['admin_status_profile_name']      = 'P';
                            }
                            if(!$admin_status_residence_address){
                                $data['admin_status_residence_address'] = 'P';
                            }
                            if(!$admin_status_permanent_address){
                                $data['admin_status_permanent_address'] = 'P';
                            }
                            if(!$admin_status_other_info){
                                $data['admin_status_other_info']        = 'P';
                            }

                        } else {    // IF NOT EXIST IN BASIC PROFILE DETAILS MAIN 
                            $data['admin_status_profile_name']      = 'P';
                            $data['admin_status_residence_address'] = 'P';
                            $data['admin_status_permanent_address'] = 'P';
                            $data['admin_status_other_info']        = 'P';
                        }                  


                        //  AVAILABLE DATA FOR ADMIN APPROVAL
                        if(!empty($tempProfileBasic_details) && count($tempProfileBasic_details) > 0){
                            $data['id']    = $tempProfileBasic_details['id'];

                            $this->profile_model->updateTempProfileBasic($data);
                            $addTempProfileBasic_id   = $tempProfileBasic_details['id'];


                        } else {    // NOT AVAILABLE ANY DATA FOR APPROVAL

                            if(is_array($user_BasicProfileDetails_arr) && count($user_BasicProfileDetails_arr)>0){
                                
                                $data['fk_profile_basic_id']= $user_BasicProfileDetails_arr['id'];

                                 $data['id']= $user_BasicProfileDetails_arr['id'];
                       
                    if($user_BasicProfileDetails_arr['profile_picture_file_extension']!=''){
                        $data['profile_picture_file_extension'] = $user_BasicProfileDetails_arr['profile_picture_file_extension'];
                        $data['s3_media_version']    = $user_BasicProfileDetails_arr['s3_media_version'];
                        $data['has_profile_picture']='1';

                                    
                                }
                                $addTempProfileBasic_id   = $this->profile_model->addTempProfileBasic($data);
                            }else{

                                $addTempProfileBasic_id   = $this->profile_model->addTempProfileBasic($data);


                            }
                        }


                         $image_field_name='file';
                        
                        if ($_FILES[$image_field_name]['name'] != "") {
                            list($width, $height, $type, $attr) = getimagesize($_FILES[$image_field_name]['tmp_name']);
                            $img_name_path = strtolower($_FILES[$image_field_name]["name"]);
                            $imgerr = pathinfo($img_name_path, PATHINFO_EXTENSION);
                            $imgerr='jpg';
                            $config['upload_path'] = $this->config->item('temp_upload_file_path');
                            $config['allowed_types'] = 'gif|jpg|png|jpeg';
                            $strtotm=strtotime(date('Y-m-d H:i:s'));
                            $filename = $data['fk_user_id'].'_'.$strtotm;
                            $config['file_name'] =  $filename .'.'.$imgerr;
                            $this->load->library('upload');
                            $this->upload->initialize($config);
                            if ($this->upload->do_upload($image_field_name)) {
                                $dataIMG = array();
                                $dataIMG = $this->upload->data();
                                $aws_target_file_path='resources/'.$data['fk_user_id'].'/profile/'.$data['fk_user_id'].'.'.$imgerr;
                                $aws_temp_name=$this->config->item('temp_upload_file_path').$dataIMG['file_name'];
                                $response = $this->aws->uploadfile($this->aws->bucket,$aws_target_file_path,$aws_temp_name,'public-read');
                                $res['s3_media_version'] =$response['VersionId'];
                                if($res['s3_media_version']!=''){

                                    $profile_picture_file_url = $this->config->item('bucket_url').$data['fk_user_id'].'/profile/'.$data['fk_user_id'].'.'.$imgerr.'?versionId='.$res['s3_media_version'];

                                    $res['has_profile_picture ']='1';
                                    $res['profile_picture_file_extension']=$imgerr;
                                    $res['s3_media_version']=$res['s3_media_version'];
                                    $this->profile_model->updateProfileBasic($res,$data['fk_user_id']);
                                    unlink($aws_temp_name);
                                }
                                
                            }else{
                                $http_response      = 'http_response_ok'; 
                                //$error_message = $this->upload->display_errors();  
                                $error_message = 'File Type is not supported! Only GIF,JPG,PNG file can upoload';  
                            }

                        }

                        
                        if($addTempProfileBasic_id > 0){
                            $result_arr         = array(
                                                    'id'        => $addTempProfileBasic_id,
                                                    'user_id'   => $req_arr['user_id'],
                                                    'file_url'  => $profile_picture_file_url
                                                );
                            if($error_message=='File Type is not supported! Only GIF,JPG,PNG file can upoload'){
                                $http_response      = 'http_response_bad_request';
                            }else{
                                $http_response      = 'http_response_ok';
                            }
                            
                            $success_message    = 'Successfully Updated';  

                        } else {
                            $http_response      = 'http_response_bad_request';
                            $error_message      = 'Something went wrong'; 
                        }
                    } else {
                        $http_response      = 'http_response_bad_request';
                    }

                   

               // } else {
                  // $http_response      = 'http_response_invalid_login';
                  //  $error_message      = 'Invalid user details'; 
                //}
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
            }
       // }

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
     *   path="/profile/addEducation",
     *   tags={"addEducation"},
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
            @SWG\Schema(ref="#/definitions/addEducation"),
     *   ),
     
        
   

     * )
     */  

    public function addEducation_post(){

     

        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        //if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            //$error_message = 'Invalid Token';
            //$http_response = 'http_response_unauthorized';
        
       // } else {

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
                $data['fk_user_id']=$this->post('user_id', TRUE);
            }

            if ($this->post('fk_degree_type_id')==''){
               
                $flag       = false;
            } else {
                $req_arr['fk_degree_type_id']    = $this->post('fk_degree_type_id', TRUE);
                $data['fk_degree_type_id']=$this->post('fk_degree_type_id', TRUE);
            }

            if ($this->post('degree_name')=='' && $this->post('fk_degree_id')<0){
               
                $flag       = false;
            } else {
                $req_arr['degree_name']    = $this->post('degree_name', TRUE);
            }

            if ($this->post('study_name')=='' && $this->post('fk_field_of_study_id')<0){
               
                $flag       = false;
            } else {
                $req_arr['study_name']    = $this->post('study_name', TRUE);
                
            }

            
            $req_arr['work_status']    = $this->post('work_status', TRUE);
            if($req_arr['work_status']==''){
                 $req_arr['work_status']    ='N';
            }
            $data['work_status']    = $req_arr['work_status'];
                
         

            if ($this->post('year_of_joining')==''){
              
                $flag       = false;
            } else {
                $req_arr['year_of_joining']    = $this->post('year_of_joining', TRUE);
                $data['year_of_joining']=$this->post('year_of_joining', TRUE);
            }

            $req_arr['year_of_graduation']    = $this->post('year_of_graduation', TRUE);
            $data['year_of_graduation']=$this->post('year_of_graduation', TRUE);

            if($data['year_of_joining']>0 && $data['year_of_graduation']>0){
                if($data['year_of_joining'] > $data['year_of_graduation']){
                    $flag       = false;
                    $error_message="Joining year should be less than graduation year";
                }


            }

            if (!$this->post('name_of_institution')){
               
                $flag       = false;
            } else {
                $req_arr['name_of_institution']    = $this->post('name_of_institution', TRUE);
                $data['name_of_institution']=$this->post('name_of_institution', TRUE);
            }

            $req_arr['marks']    = $this->post('marks', TRUE);
            $data['marks']=$this->post('marks', TRUE);

            $req_arr['out_of_range']    = $this->post('out_of_range', TRUE);
            $data['out_of_range']=$this->post('out_of_range', TRUE);

            if($data['marks']>0 && $data['out_of_range']>0){

                if($data['marks'] > $data['out_of_range']){
                    $flag       = false;
                    $error_message="Marks should be less than Out of range";

                }
            }


            if ($this->post('fk_pincode_id')==''){
              
                $flag       = false;
            } else {
                $req_arr['fk_pincode_id']    = $this->post('fk_pincode_id', TRUE);
                $data['fk_pincode_id']=$this->post('fk_pincode_id', TRUE);
               
            }
            
            if($this->post('fk_field_of_study_id')>0){
                $data['fk_field_of_study_id']=$this->post('fk_field_of_study_id', TRUE);

            }else{
                $fk_field_of_study_id=$this->agentcode_model->addFieldOfStudies($req_arr);
                $data['fk_field_of_study_id']=$fk_field_of_study_id;
            }

            if($this->post('fk_degree_id')>0){
                $data['fk_degree_id']=$this->post('fk_degree_id', TRUE);

            }else{
                $fk_degree_id=$this->agentcode_model->addDegree($req_arr);
                $data['fk_degree_id']=$fk_degree_id;
            }

            $req_arr['education_id']    = $this->post('education_id', TRUE);
           
            if($flag) {
                //log in status checking
                $login_status_arr = $this->login_model->login_status_checking($req_arr);
                //echo $this->db->last_query(); //exit;
                //pre($login_status_arr,1);

                $login_status_arr=1;
                if( count($login_status_arr) > 0){

                    if($req_arr['education_id']>0){
                        
                        $education_details_tmp=$this->profile_model->getEducationTmp($req_arr['education_id']);
                         if(is_array($education_details_tmp) && count($education_details_tmp)>0){
                             $data['id']=$req_arr['education_id'];
                             $this->profile_model->updateEducation($data);
                             $http_response      = 'http_response_ok';
                             $success_message    = ''; 

                         }else{
                            //add but in main table
                            $education_details=$this->profile_model->getEducation($req_arr['education_id']);
                             if(is_array($education_details) && count($education_details)>0){

                                $data['id']=$req_arr['education_id'];
                                $data['fk_profile_education_id']=$req_arr['education_id'];
                                 $image_field_name='file';
                                if ($_FILES[$image_field_name]['name'] == "") {
                                    $data['file_extension']=$education_details['file_extension'];
                                    $data['s3_media_version']=$education_details['s3_media_version'];
                                    if($education_details['s3_media_version']!='' && $education_details['s3_media_version']!=NULL){
                                        $data['is_file_uploaded']=1;

                                    }
                                }
                                $this->profile_model->addEducation($data);
                                $http_response      = 'http_response_ok';
                                $success_message    = ''; 
                             }else{

                                $http_response      = 'http_response_bad_request';
                                $error_message      = 'Wrong Education id'; 

                             }


                         }






                    }else{
                        //add
                        if($req_arr['education_id']<1){
                            $tempEducation=$this->profile_model->getAllEducationTmp($req_arr);
                            if(is_array($tempEducation) && count($tempEducation)>0){
                                $tempEducation=$this->profile_model->getEducationTmpShowInProfile($req_arr);
                                if($data['year_of_joining'] > $tempEducation['year_of_joining']){
                                    $show_in_profile='1';
                                    //$this->profile_model->updateEducationTmpShowInProfile($tempEducation['id']);
                                }else{
                                    $show_in_profile='0';
                                }

                            }else{
                                $allEducation=$this->profile_model->getAllEducation($req_arr);

                                if(is_array($allEducation) && count($allEducation)>0){
                                    $mainEducation=$this->profile_model->getEducationShowInProfile($req_arr);
                                    if($data['year_of_joining'] > $mainEducation['year_of_joining']){
                                        $show_in_profile='1';
                                        //$this->profile_model->updateEducationShowInProfile($mainEducation['id']);
                                    }else{
                                        $show_in_profile='0';
                                    }
                                }else{
                                    $show_in_profile='1';
                                }

                            }

                            //$data['show_in_profile']=$show_in_profile;
                            $user_details_arr   = $this->profile_model->addEducation($data);
                            $req_arr['education_id'] = $user_details_arr;

                            $http_response      = 'http_response_ok';
                            $success_message    = ''; 
                        }else{
                            $http_response      = 'http_response_bad_request';
                            $error_message      = 'Something went wrong! Plesae try again'; 
                        }
                    }

                    if($req_arr['education_id']>0){

                        // image upload 
                        $image_field_name='file';
                        if ($_FILES[$image_field_name]['name'] != "") {
                            list($width, $height, $type, $attr) = getimagesize($_FILES[$image_field_name]['tmp_name']);
                            $img_name_path = strtolower($_FILES[$image_field_name]["name"]);
                            $imgerr = pathinfo($img_name_path, PATHINFO_EXTENSION);
                            $config['upload_path'] = $this->config->item('temp_upload_file_path');
                            $config['allowed_types'] = 'gif|jpg|png|jpeg';
                            $filename=strtotime(date('Y-m-d H:i:s'));
                            $config['file_name'] =  $filename .'.'.$imgerr;
                            $this->load->library('upload');
                            $this->upload->initialize($config);
                            if ($this->upload->do_upload($image_field_name)) {
                                $dataIMG = array();
                                $dataIMG = $this->upload->data();
                                $aws_target_file_path='resources/'.$req_arr['user_id'].'/education/'.$req_arr['education_id'].'.'.$imgerr;
                                $aws_temp_name=$this->config->item('temp_upload_file_path').$dataIMG['file_name'];
                                $response = $this->aws->uploadfile($this->aws->bucket,$aws_target_file_path,$aws_temp_name,'public-read');
                                $res['s3_media_version'] =$response['VersionId'];
                                if($res['s3_media_version']!=''){
                                    $res['education_id']=$req_arr['education_id'];
                                    $res['file_extension']=$imgerr;
                                    $this->profile_model->updateEducationTmp($res);
                                    unlink($aws_temp_name);
                                }
                                
                            }

                        }

                    }
                    
                    

                } else {
                    $http_response      = 'http_response_bad_request';
                    $error_message      = 'Invalid user details'; 
                }
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
            }
        //}

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
     *   @SWG\POST(
     *      path="/profile/getKycDocuments",
     *      tags={"getKycDocuments"},
     *      summary="getKycDocuments",
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
     *   @SWG\Parameter(
     *     name="data",
     *     in="body",
     *     description="id of users table",
     *     required=true,
     *     type="string",
            @SWG\Schema(ref="#/definitions/getKycDocuments"),
     *   ),
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
    public function getKycDocuments_post(){

        $error_message = $success_message = $http_response = '';
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

                $flag           = true;
                $req_arr        = array();
                $raws = array();  

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

                if (!$this->post('fk_profession_type_id')){
                    $flag       = false;
                } else {
                    $req_arr['fk_profession_type_id']    = $this->post('fk_profession_type_id', TRUE);
                }

                if (!$this->post('document_type')){
                    $flag       = false;
                } else {
                    $req_arr['document_type']    = $this->post('document_type', TRUE);
                }

                if (!$this->post('user_mode')){
                    $flag       = false;
                } else {
                    $req_arr['user_mode']    = $this->post('user_mode', TRUE);
                }

                
                
                //log in status checking
                
              
                if($flag) {
                    //log in status checking
                    $login_status_arr = $this->login_model->login_status_checking($req_arr);
                    //echo $this->db->last_query(); //exit;
                    //pre($login_status_arr,1);

                   
                    if(is_array($login_status_arr) && count($login_status_arr) > 0){

                       //log in status checking
                       
                        $data['kyc_docs']       = $this->profile_model->getKycDocuments($req_arr);
                        
                        $data['kyc_docs_mandatory']       = $this->profile_model->getTotalKycDocumentsMandatory($req_arr);
                        $data['kyc_docs_mandatory_data']       = $this->profile_model->getKycDocumentsMandatory($req_arr);

                        $data['kyc_docs_any']       = $this->profile_model->getTotalKycDocumentsAny($req_arr);
                        $data['kyc_docs_any_data']       = $this->profile_model->getKycDocumentsAny($req_arr);
                        $raws['dataset'] =$data;
                        
                        

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
     *   @SWG\POST(
     *      path="/profile/addKyc",
     *      tags={"addKyc"},
     *      summary="addKyc",
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
     *       @SWG\Parameter(
     *         name="fk_kyc_template_id",
     *         in="formData",
     *         description="passkey ",
     *         required=true,
     *         type="integer"
     *     ),    
     *       @SWG\Parameter(
     *         name="kyc_data",
     *         in="formData",
     *         description="kyc_data ",
     *         required=true,
     *         type="string"
     *     ),    
     *       @SWG\Parameter(
     *         name="user_id",
     *         in="formData",
     *         description="user_id ",
     *         required=true,
     *         type="integer"
     *     ),    
     *       @SWG\Parameter(
     *         name="pass_key",
     *         in="formData",
     *         description="pass_key ",
     *         required=true,
     *         type="integer"
     *     ),    
      *       @SWG\Parameter(
     *         name="front_img",
     *         in="formData",
     *         description="pass_key ",
     *         required=true,
     *         type="file"
     *     ),      
     *       @SWG\Parameter(
     *         name="back_img",
     *         in="formData",
     *         description="pass_key ",
     *         required=true,
     *         type="file"
     *     ),    
     *  )
     *
    **/ 
    public function addKyc_post(){

        

        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        $raws = array();   
        //if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

           // $error_message = 'Invalid Token';
           // $http_response = 'http_response_unauthorized';
        
        //} else {

                $flag           = true;
                $req_arr        = array();

                if (!$this->post('pass_key')){
                    $flag       = false;
                    $error_message='Please enter pass key';
                } else {
                    $req_arr['pass_key']    = $this->post('pass_key', TRUE);
                }
                if (!$this->post('user_id')){
                    $flag       = false;
                    $error_message='Please enter user id';
                } else {
                    $req_arr['user_id']    = $this->post('user_id', TRUE);
                    $data['fk_user_id']=$this->post('user_id', TRUE);
                }

               

                if (!$this->post('fk_kyc_template_id')){
                    $flag       = false;
                    $error_message='Please enter template id';
                } else {
                    $req_arr['fk_kyc_template_id']    = $this->post('fk_kyc_template_id', TRUE);
                    $data['fk_kyc_template_id']=$this->post('fk_kyc_template_id', TRUE);

                    
                }

                //type can be front/back
                $req_arr['type']    = $this->post('type', TRUE);

                $req_arr['kyc_data']    = $this->post('kyc_data', TRUE);
                $data['kyc_data']    = $this->post('kyc_data', TRUE);

            

                $req_arr['kyc_id']    = $this->post('kyc_id', TRUE);

                if($flag) {
                //log in status checking
                $login_status_arr = $this->login_model->login_status_checking($req_arr);
                
                if(is_array($login_status_arr) && count($login_status_arr) > 0){

                   

                    if($req_arr['kyc_id']>0){
                        //update
                      
                       
                        

                             $kyc_details_tmp=$this->profile_model->getKycTmp($req_arr['kyc_id']);
                             if(is_array($kyc_details_tmp) && count($kyc_details_tmp)>0){
                                $data['id']=$req_arr['kyc_id'];
                                $this->profile_model->updateKyc($data);
                                 $http_response      = 'http_response_ok';
                                $success_message    = ''; 

                             }else{
                                 $kyc_details=$this->profile_model->getKyc($req_arr['kyc_id']);

                                if(is_array($kyc_details) && count($kyc_details)>0){
                                    $data['fk_profile_kyc_id']=$req_arr['kyc_id'];
                                    $data['id']=$req_arr['kyc_id'];
                                    $data['front_file_extension']=$kyc_details['front_file_extension'];
                                    $data['front_s3_media_version']=$kyc_details['front_s3_media_version'];
                                    $data['back_file_extension']=$kyc_details['back_file_extension'];
                                    $data['back_s3_media_version']=$kyc_details['back_s3_media_version'];
                                    $this->profile_model->addKyc($data);

                                    $http_response      = 'http_response_ok';
                                    $success_message    = ''; 

                                }else{
                                    $http_response      = 'http_response_bad_request';
                                    $error_message      = 'Wrong Education id'; 
                                }
                             }
                        

                        

                    }else{
                        //add
                        if($req_arr['kyc_id']<1){
                            $kyc_arr   = $this->profile_model->addKyc($data);
                            $req_arr['kyc_id'] = $kyc_arr;

                            $http_response      = 'http_response_ok';
                            $success_message    = ''; 
                        }else{
                            $http_response      = 'http_response_bad_request';
                            $error_message      = 'Something went wrong! Plesae try again'; 
                        }
                    }

                    if($req_arr['kyc_id']>0){
                        // image upload 
                        $image_field_name='file';

                        if ($_FILES[$image_field_name]['name'] != "" && $req_arr['type']=='front') {
                            list($width, $height, $type, $attr) = getimagesize($_FILES[$image_field_name]['tmp_name']);
                            $img_name_path = strtolower($_FILES[$image_field_name]["name"]);
                            $imgerr = pathinfo($img_name_path, PATHINFO_EXTENSION);
                            $config['upload_path'] = $this->config->item('temp_upload_file_path');
                            $config['allowed_types'] = 'gif|jpg|png|jpeg';
                            $filename=strtotime(date('Y-m-d H:i:s'));
                            $config['file_name'] =  $filename .'.'.$imgerr;
                            $this->load->library('upload');
                            $this->upload->initialize($config);
                            if ($this->upload->do_upload($image_field_name)) {
                                $dataIMG = array();
                                $dataIMG = $this->upload->data();
                                $aws_target_file_path='resources/'.$req_arr['user_id'].'/kyc/'.$req_arr['kyc_id'].'_front.'.$imgerr;
                                $aws_temp_name=$this->config->item('temp_upload_file_path').$dataIMG['file_name'];
                                $response = $this->aws->uploadfile($this->aws->bucket,$aws_target_file_path,$aws_temp_name,'public-read');
                                $s3_media_version =$response['VersionId'];
                                if($s3_media_version!=''){
                                   
                                    $res['is_front_file_uploaded']='1';
                                    $res['front_file_extension']=$imgerr;
                                    $res['front_s3_media_version']=$s3_media_version;
                                    $this->profile_model->updateKycTmp($res,$req_arr['kyc_id']);
                                    unlink($aws_temp_name);
                                }
                                
                            }
                        }

                        $image_field_name='file';
                        if ($_FILES[$image_field_name]['name'] != "" && $req_arr['type']=='back') {
                            $raws['dataset']['details']['back_tmp_name']= $_FILES[$image_field_name]['tmp_name'];
                                list($width, $height, $type, $attr) = getimagesize($_FILES[$image_field_name]['tmp_name']);
                                $img_name_path = strtolower($_FILES[$image_field_name]["name"]);
                                $imgerr = pathinfo($img_name_path, PATHINFO_EXTENSION);
                                $config['upload_path'] = $this->config->item('temp_upload_file_path');
                                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                                $filename=strtotime(date('Y-m-d H:i:s'));
                                $config['file_name'] =  $filename .'.'.$imgerr;
                                $this->load->library('upload');
                                $this->upload->initialize($config);
                                if ($this->upload->do_upload($image_field_name)) {
                                    $dataIMG = array();
                                    $dataIMG = $this->upload->data();
                                    $aws_target_file_path='resources/'.$req_arr['user_id'].'/kyc/'.$req_arr['kyc_id'].'_back.'.$imgerr;
                                    $aws_temp_name=$this->config->item('temp_upload_file_path').$dataIMG['file_name'];
                                    $response = $this->aws->uploadfile($this->aws->bucket,$aws_target_file_path,$aws_temp_name,'public-read');
                                    $s3_media_version =$response['VersionId'];
                                    if($s3_media_version!=''){

                                        $res['is_back_file_uploaded']='1';
                                        $res['back_file_extension']=$imgerr;
                                        $res['back_s3_media_version']=$s3_media_version;
                                        $this->profile_model->updateKycTmp($res,$req_arr['kyc_id']);
                                        unlink($aws_temp_name);
                                    }
                                    
                                }

                            }

                            $raws['dataset']['details']['kyc_id']=$req_arr['kyc_id'];
                        
                    }

                   
                    
                    

                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Invalid user details'; 
                }
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
            }
        //}

        
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
     *   path="/profile/fetchAllKyc",
     *   tags={"fetchAllKyc"},
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
            @SWG\Schema(ref="#/definitions/fetchAllKyc"),
     *   ),
        
   

     * )
     */  

    public function fetchAllKyc_post(){

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
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }
            if (!$this->post('user_id')){
                $flag       = false;
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
                
            }

            if (!$this->post('fk_profession_type_id')){
                $flag       = false;
            } else {
                $req_arr['fk_profession_type_id']    = $this->post('fk_profession_type_id', TRUE);
            }

            if (!$this->post('document_type')){
                $flag       = false;
            } else {
                $req_arr['document_type']    = $this->post('document_type', TRUE);
            }

            if (!$this->post('user_mode')){
                $flag       = false;
            } else {
                $req_arr['user_mode']    = $this->post('user_mode', TRUE);
            }


            
              $raws = array();   
            if($flag) {
                //log in status checking
                $login_status_arr = $this->login_model->login_status_checking($req_arr);
                
                
                if(is_array($login_status_arr) &&  count($login_status_arr) > 0){

                            $kyc_list_tmp = $this->profile_model->getAllKycTmp($req_arr);
                            $i=0;
                            if(is_array($kyc_list_tmp) && count($kyc_list_tmp)>0){

                                    foreach($kyc_list_tmp as $kyc_tmp){
                                        $data[$i]['id']=$kyc_tmp['id'];
                                        $data[$i]['fk_user_id']=$kyc_tmp['fk_user_id'];
                                        $data[$i]['fk_kyc_template_id']=$kyc_tmp['fk_kyc_template_id'];
                                        $template_details=$this->profile_model->getTemplateDetails($kyc_tmp['fk_kyc_template_id']);
                                        $data[$i]['document_name']=$template_details['document_name'];
                                        $data[$i]['document_type']=$template_details['document_type'];
                                        $data[$i]['kyc_data']=$kyc_tmp['kyc_data'];
                                        $data[$i]['front_file_extension']=$kyc_tmp['front_file_extension'];
                                        $data[$i]['front_s3_media_version']=$kyc_tmp['front_s3_media_version'];
                                        $data[$i]['back_file_extension']=$kyc_tmp['back_file_extension'];
                                        $data[$i]['back_s3_media_version']=$kyc_tmp['back_s3_media_version'];
                                        $data[$i]['is_front_file_uploaded']=$kyc_tmp['is_front_file_uploaded'];
                                        $data[$i]['is_back_file_uploaded']=$kyc_tmp['is_back_file_uploaded'];
                                        $data[$i]['kyc_status']=$kyc_tmp['kyc_status'];
                                        
                                        $data[$i]['admin_message_kyc']=$kyc_tmp['admin_message_kyc'];
                                        if($kyc_tmp['front_s3_media_version']!=''){
                                            $data[$i]['front_img_url']=$this->config->item('bucket_url').$req_arr['user_id'].'/kyc/'.$kyc_tmp['id'].'_front.'.$kyc_tmp['front_file_extension'].'?versionId='.$kyc_tmp['front_s3_media_version'];
                                        }else{
                                            $data[$i]['front_img_url']='';
                                        }

                                        if($kyc_tmp['back_s3_media_version']!=''){
                                         $data[$i]['back_img_url']=$this->config->item('bucket_url').$req_arr['user_id'].'/kyc/'.$kyc_tmp['id'].'_back.'.$kyc_tmp['back_file_extension'].'?versionId='.$kyc_tmp['back_s3_media_version'];
                                        }else{
                                            $data[$i]['back_img_url']='';
                                        }
                                         $i++;
                                        }
                                     }
                          

                            $kyc_list = $this->profile_model->getAllKyc($req_arr);

                            if(is_array($kyc_list) && count($kyc_list)>0){
                               
                                foreach($kyc_list as $kyc){
                                        $is_in_tmp=$this->profile_model->iSInTmpKyc($kyc);
                                    if($is_in_tmp==0){
                                        $data[$i]['id']=$kyc['id'];
                                        $data[$i]['fk_user_id']=$kyc['fk_user_id'];
                                        $data[$i]['fk_kyc_template_id']=$kyc['fk_kyc_template_id'];
                                         $template_details=$this->profile_model->getTemplateDetails($kyc['fk_kyc_template_id']);
                                        $data[$i]['document_name']=$template_details['document_name'];
                                        $data[$i]['document_type']=$template_details['document_type'];
                                        $data[$i]['kyc_data']=$kyc['kyc_data'];
                                        $data[$i]['front_file_extension']=$kyc['front_file_extension'];
                                        $data[$i]['front_s3_media_version']=$kyc['front_s3_media_version'];
                                        $data[$i]['back_file_extension']=$kyc['back_file_extension'];
                                        $data[$i]['back_s3_media_version']=$kyc['back_s3_media_version'];
                                        $data[$i]['is_front_file_uploaded']='1';
                                        $data[$i]['is_back_file_uploaded']='1';
                                        $data[$i]['kyc_status']='A';
                                        
                                        $data[$i]['admin_message_kyc']='';
                                        if($kyc['front_s3_media_version']!=''){
                                        $data[$i]['front_img_url']=$this->config->item('bucket_url').$req_arr['user_id'].'/kyc/'.$kyc['id'].'_front.'.$kyc['front_file_extension'].'?versionId='.$kyc['front_s3_media_version'];
                                        }else{
                                          $data[$i]['front_img_url']='';  
                                        }
                                        if($kyc['back_s3_media_version']!=''){
                                         $data[$i]['back_img_url']=$this->config->item('bucket_url').$req_arr['user_id'].'/kyc/'.$kyc['id'].'_back.'.$kyc['back_file_extension'].'?versionId='.$kyc['back_s3_media_version'];
                                        }else{
                                          $data[$i]['back_img_url']='';  
                                        }
                                        
                                        $i++;
                                    }
                                }
                            }
                             $raws['is_mandatory_add']='0';
                            if(is_array($data) && count($data)==1){
                                $kyc_docs_mandatory_data      = $this->profile_model->getKycDocumentsMandatory($req_arr);

                                    $raws['template_id']=$kyc_docs_mandatory_data['template_id']; 
                                if($kyc_docs_mandatory_data['template_id']==$data[0]['fk_kyc_template_id']){
                                    $raws['is_mandatory_add']='1';
                                }else{
                                    
                                   $raws['is_mandatory_add']='0'; 
                                }
                            }else{
                                if(is_array($data) && count($data)==2){
                                    $raws['is_mandatory_add']='1';
                                }
                            }

                            $raws['dataset'] =$data;
                            $raws['total_row'] =count($data);
                            $http_response      = 'http_response_ok';
                            $success_message    = ''; 
                       
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Wrong username or Password'; 
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
     *   path="/profile/fetchKyc",
     *   tags={"fetchEducation"},
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
            @SWG\Schema(ref="#/definitions/fetchKyc"),
     *   ),
        
   

     * )
     */  

    public function fetchKyc_post(){

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
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }
            if (!$this->post('user_id')){
                $flag       = false;
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
                
            }

            if (!$this->post('kyc_id')){
                $flag       = false;
            } else {
                $req_arr['kyc_id']    = $this->post('kyc_id', TRUE);
                
            }


            
            $raws = array();   
            if($flag) {
                //log in status checking
                $login_status_arr = $this->login_model->login_status_checking($req_arr);
                
                $details=array();
                if(is_array($login_status_arr) &&  count($login_status_arr) > 0){
                            
                            $details['kyc_details'] = $this->profile_model->getKycDetail($req_arr);
                            $isinKycMain=$this->profile_model->isinKycMain($req_arr);
                            if($isinKycMain>0){
                                $details['is_enable_restore']='1';
                                
                            }else{
                                $details['is_enable_restore']='0';
                            }

                            $isinKycTemp=$this->profile_model->isinKycTemp($req_arr);
                             if($isinKycMain=0){
                                $details['kyc_details']['kyc_status']='A';
                             }

                             $template_details=$this->profile_model->getTemplateDetails($details['kyc_details']['fk_kyc_template_id']);

                                        $details['kyc_details']['document_name']=$template_details['document_name'];
                                        $details['kyc_details']['document_type']=$template_details['document_type'];
                                        if($details['kyc_details']['front_s3_media_version']!='' && $details['kyc_details']['front_s3_media_version']!='null'){
                            $details['front_img_url']=$this->config->item('bucket_url').$req_arr['user_id'].'/kyc/'.$details['kyc_details']['id'].'_front.'.$details['kyc_details']['front_file_extension'].'?versionId='.$details['kyc_details']['front_s3_media_version'];
                        }else{
                            $details['front_img_url']='';
                        }

                    if($details['kyc_details']['back_s3_media_version']!='' && $details['kyc_details']['back_s3_media_version']!='null'){
                            $details['back_img_url']=$this->config->item('bucket_url').$req_arr['user_id'].'/kyc/'.$details['kyc_details']['id'].'_back.'.$details['kyc_details']['back_file_extension'].'?versionId='.$details['kyc_details']['back_s3_media_version'];
                        }else{
                            $details['back_img_url']='';
                        }
                                    
                           
                            
                            $http_response      = 'http_response_ok';
                            $success_message    = ''; 
                       
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Wrong username or Password'; 
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

        $raws['dataset'] =$details;
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 
    } 


    public function getIfscDetails_post(){

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
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }
            if (!$this->post('user_id')){
                $flag       = false;
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
                
            }

            if (!$this->post('ifsc_code')){
                $flag       = false;
            } else {
                $req_arr['ifsc_code']    = $this->post('ifsc_code', TRUE);
                
            }


            
            $raws = array();   
            if($flag) {
                //log in status checking
                $login_status_arr = $this->login_model->login_status_checking($req_arr);
                
                
                if(is_array($login_status_arr) &&  count($login_status_arr) > 0){

                            $details['ifsc_details'] = $this->profile_model->fetchIfscDetails($req_arr);

                            if(is_array($details['ifsc_details']) && count($details['ifsc_details'])>0){
                                $raws['dataset'] =$details;
                                $success_message    = ''; 
                            }else{
                                $details['ifsc_details']=array();
                                $raws['dataset']=$details;
                                $success_message    = 'Wrong IFSC Code, No data found'; 
                            }

                            $raws['dataset'] =$details;
                            $http_response      = 'http_response_ok';
                            $success_message    = ''; 
                       
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Wrong username or Password'; 
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
     *   @SWG\POST(
     *      path="/profile/addBank",
     *      tags={"addBank"},
     *      summary="addBank",
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
     *     name="data",
     *     in="body",
     *     description="id of users table",
     *     required=true,
     *     type="string",
            @SWG\Schema(ref="#/definitions/addBank"),
     *   ),   
     *   
   
     *  )
     *
    **/ 
    public function addBank_post(){

        

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
                } else {
                    $req_arr['pass_key']    = $this->post('pass_key', TRUE);
                }
                if (!$this->post('user_id')){
                    $flag       = false;
                } else {
                    $req_arr['user_id']    = $this->post('user_id', TRUE);
                    $data['fk_user_id']=$this->post('user_id', TRUE);
                }

                if (!$this->post('fk_bank_id')){
                    $flag       = false;
                } else {
                    $req_arr['fk_bank_id']    = $this->post('fk_bank_id', TRUE);
                    $data['fk_bank_id']    = $this->post('fk_bank_id', TRUE);
                }

                if (!$this->post('account_number')){
                    $flag       = false;
                } else {
                    $req_arr['account_number']    = $this->post('account_number', TRUE);
                    $data['account_number']    = $this->post('account_number', TRUE);
                }

                $req_arr['is_primary']    = $this->post('is_primary', TRUE);

                if($req_arr['is_primary']=='Y'){
                    $data['is_primary']=$req_arr['is_primary'];
                }else{
                    $data['is_primary']='N';
                }

                $req_arr['bank_id']    = $this->post('bank_id', TRUE);

                if($flag) {
                //log in status checking
                $login_status_arr = $this->login_model->login_status_checking($req_arr);
                
                if(is_array($login_status_arr) && count($login_status_arr) > 0){

                   

                    if($req_arr['bank_id']>0){
                        //update
                            $bank_details_tmp=$this->profile_model->getBankTmp($req_arr['bank_id']);
                             if(is_array($bank_details_tmp) && count($bank_details_tmp)>0){
                                $data['id']=$req_arr['bank_id'];
                                $this->profile_model->updateBank($data);

                                $http_response      = 'http_response_ok';
                                $success_message    = ''; 

                              }else{

                                $bank_details=$this->profile_model->getBank($req_arr['bank_id']);
                                if(is_array($bank_details) && count($bank_details)>0){
                                    $data['fk_user_id']=$req_arr['user_id'];
                                    $data['id']=$req_arr['bank_id'];
                                    $data['fk_profile_bank_id']=$req_arr['bank_id'];
                                    $this->profile_model->addBankTmp($data);

                                    $http_response      = 'http_response_ok';
                                    $success_message    = ''; 
                                }else{

                                    $http_response      = 'http_response_bad_request';
                                    $error_message      = 'Wrong Education id';

                                }
                            }
                           
                        

                    }else{
                        //add
                        if($req_arr['bank_id']<1){
                            $bank_id   = $this->profile_model->addBankTmp($data);
                            $req_arr['bank_id'] = $bank_id;

                            $http_response      = 'http_response_ok';
                            $success_message    = ''; 
                        }else{
                            $http_response      = 'http_response_bad_request';
                            $error_message      = 'Something went wrong! Plesae try again'; 
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

    public function setIsPrimary_post(){

        

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
                } else {
                    $req_arr['pass_key']    = $this->post('pass_key', TRUE);
                }

                if (!$this->post('user_id')){
                    $flag       = false;
                } else {
                    $req_arr['user_id']    = $this->post('user_id', TRUE);
                    $data['fk_user_id']=$this->post('user_id', TRUE);
                }

                if (!$this->post('is_primary')){
                    $flag       = false;
                } else {
                    $req_arr['is_primary']    = $this->post('is_primary', TRUE);
                }

                if (!$this->post('bank_id')){
                    $flag       = false;
                } else {
                    $req_arr['bank_id']    = $this->post('bank_id', TRUE);
                    
                }

                if($flag) {
                //log in status checking
                $login_status_arr = $this->login_model->login_status_checking($req_arr);
                
                if(is_array($login_status_arr) && count($login_status_arr) > 0){

                   

                    if($req_arr['bank_id']>0){
                        //update
                                if($req_arr['is_primary']=='N'){
                                    $profileTmpDtl=$this->profile_model->getTotalIsPrimaryNo($req_arr);
                                    if($profileTmpDtl>0){
                                        $this->profile_model->getUpdateIsPrimaryNo($req_arr);
                                        $http_response      = 'http_response_ok';
                                        $success_message    = ''; 
                                    }else{
                                        $http_response      = 'http_response_bad_request';

                                        $error_message      = 'Atleast one bank needs to set Primary';


                                    }
                                }

                                if($req_arr['is_primary']=='Y'){
                                    $profileTmpDtl=$this->profile_model->getTotalIsPrimaryNo($req_arr);
                                    if($profileTmpDtl>0){
                                        $this->profile_model->getUpdateIsPrimaryNo($req_arr);
                                        $this->profile_model->getUpdateIsPrimaryYes($req_arr);
                                        $http_response      = 'http_response_ok';
                                        $success_message    = ''; 
                                    }else{
                                        $this->profile_model->getUpdateIsPrimaryYes($req_arr);
                                        $http_response      = 'http_response_ok';
                                        $success_message    = ''; 

                                    }
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
     *   path="/profile/fetchAllBank",
     *   tags={"fetchAllBank"},
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
            @SWG\Schema(ref="#/definitions/fetchAllBank"),
     *   ),
        
   

     * )
     */  

    public function fetchAllBank_post(){

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
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }
            if (!$this->post('user_id')){
                $flag       = false;
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
                
            }

            
              $raws = array();   
            if($flag) {
                //log in status checking
                $login_status_arr = $this->login_model->login_status_checking($req_arr);
                
                $data=array();
                if(is_array($login_status_arr) &&  count($login_status_arr) > 0){

                            $bank_list_tmp = $this->profile_model->getAllBankTmp($req_arr);
                            $i=0;
                            if(is_array($bank_list_tmp) && count($bank_list_tmp)>0){
                                
                                foreach($bank_list_tmp as $bank_tmp){
                                    $data[$i]['id']=$bank_tmp['id'];
                                    $data[$i]['fk_user_id']=$bank_tmp['fk_user_id'];
                                    $data[$i]['fk_bank_id']=$bank_tmp['fk_bank_id'];
                                    $ifsc_bank_details=$this->profile_model->fetchIfscBankDetails($bank_tmp['fk_bank_id']);
                                    $data[$i]['bank_name']=$ifsc_bank_details['bank_name'];
                                    $data[$i]['ifsc_code']=$ifsc_bank_details['ifsc_code'];
                                    $data[$i]['bank_branch']=$ifsc_bank_details['bank_branch'];
                                    $data[$i]['bank_city']=$ifsc_bank_details['bank_city'];
                                    $data[$i]['account_number']=$bank_tmp['account_number'];
                                    $data[$i]['bank_status']=$bank_tmp['bank_status'];
                                    $data[$i]['is_primary']=($bank_tmp['is_primary']=='Y')?'true':'false';
                                    $data[$i]['admin_message_bank']=$bank_tmp['admin_message_bank'];
                                    
                                    $i++;
                                }
                            }

                            $bank_list = $this->profile_model->getAllBank($req_arr);

                            if(is_array($bank_list) && count($bank_list)>0){
                               
                                foreach($bank_list as $banks){
                                        $is_in_tmp=$this->profile_model->iSInTmpTableBank($banks);
                                    if($is_in_tmp==0){
                                        $data[$i]['id']=$banks['id'];
                                        $data[$i]['fk_user_id']=$banks['fk_user_id'];
                                        $data[$i]['fk_bank_id']=$banks['fk_bank_id'];
                                        $ifsc_bank_details=$this->profile_model->fetchIfscBankDetails($banks['fk_bank_id']);
                                        $data[$i]['ifsc_code']=$ifsc_bank_details['ifsc_code'];
                                        $data[$i]['bank_name']=$ifsc_bank_details['bank_name'];
                                        $data[$i]['bank_branch']=$ifsc_bank_details['bank_branch'];
                                        $data[$i]['bank_city']=$ifsc_bank_details['bank_city'];
                                        $data[$i]['account_number']=$banks['account_number'];
                                        $data[$i]['bank_status']='A';
                                        $data[$i]['is_primary']=($banks['is_primary']=='Y')?'true':'false';
                                        $data[$i]['admin_message_bank']=$banks['admin_message_bank'];
                                        $i++;
                                    }
                                }
                            }
                           
                            
                            $raws['total_row'] =count($data);
                            $http_response      = 'http_response_ok';
                            $success_message    = ''; 
                       
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Wrong username or Password'; 
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

       $raws['dataset'] =$data;
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
     *   path="/profile/fetchBank",
     *   tags={"fetchBank"},
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
            @SWG\Schema(ref="#/definitions/fetchBank"),
     *   ),
        
   

     * )
     */  

    public function fetchBank_post(){

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
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }
            if (!$this->post('user_id')){
                $flag       = false;
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
                
            }

            if (!$this->post('bank_id')){
                $flag       = false;
            } else {
                $req_arr['bank_id']    = $this->post('bank_id', TRUE);
                
            }
            $raws = array();   
            if($flag) {
                //log in status checking
                $login_status_arr = $this->login_model->login_status_checking($req_arr);
                
                
                if(is_array($login_status_arr) &&  count($login_status_arr) > 0){

                            $bank_details= $this->profile_model->getBankTmpDetails($req_arr['bank_id']);
                            

                            if(is_array($bank_details) && count($bank_details)>0){
                                $data['id']=$bank_details['id'];
                                $data['fk_user_id']=$bank_details['fk_user_id'];
                                $data['fk_bank_id']=$bank_details['fk_bank_id'];
                                $ifsc_bank_details=$this->profile_model->fetchIfscBankDetails($bank_details['fk_bank_id']);
                                $data['ifsc_code']=$ifsc_bank_details['ifsc_code'];
                                $data['bank_name']=$ifsc_bank_details['bank_name'];
                                $data['bank_branch']=$ifsc_bank_details['bank_branch'];
                                $data['bank_city']=$ifsc_bank_details['bank_city'];
                                $data['account_number']=$bank_details['account_number'];
                                $data['bank_status']=$bank_details['bank_status'];
                                $data['admin_message_bank']=$bank_details['admin_message_bank'];
                                $isinBankMain=$this->profile_model->isinBankMain($req_arr);
                                if($isinBankMain>0){
                                    $data['is_enable_restore']='1';
                                }else{
                                    $data['is_enable_restore']='0';
                                }
                            }else{
                                $data=array();
                            }
                            
                            $raws['dataset'] =$data;
                            $http_response      = 'http_response_ok';
                            $success_message    = ''; 
                       
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Wrong username or Password'; 
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

   

    public function restore_post(){
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
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }

            if (!$this->post('user_id')){
                $flag       = false;
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
                
            }

            if (!$this->post('type')){
                $flag       = false;
            } else {
                $req_arr['type']    = $this->post('type', TRUE);
                
            }

            $req_arr['kyc_id']    = $this->post('kyc_id', TRUE);
            $req_arr['education_id']    = $this->post('education_id', TRUE);
            $req_arr['bank_id']    = $this->post('bank_id', TRUE);

            $raws = array();   
            if($flag) {
                //log in status checking
                $login_status_arr = $this->login_model->login_status_checking($req_arr);
                
                
                if(is_array($login_status_arr) &&  count($login_status_arr) > 0){

                            if($req_arr['type']=='basic'){
                                $this->profile_model->deleteProfileBasic($req_arr);
                            }else if($req_arr['type']=='kyc'){
                                if($req_arr['kyc_id']>0 && $req_arr['user_id']>0){
                                    $this->profile_model->deleteProfileKyc($req_arr);
                                }
                            }else if($req_arr['type']=='education'){
                                if($req_arr['education_id']>0 && $req_arr['user_id']>0){
                                    $this->profile_model->deleteProfileEducation($req_arr);
                                }
                            }else if($req_arr['type']=='bank'){
                                if($req_arr['bank_id']>0 && $req_arr['user_id']>0){
                                    $this->profile_model->deleteProfileBank($req_arr);
                                }
                            }
                            
                            $http_response      = 'http_response_ok';
                            $success_message    = ''; 
                       
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Wrong username or Password'; 
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
     *   @SWG\Delete(
     *      path="/profile/deleteEducationImage",
     *      tags={"deleteEducationImage"},
     *      summary="fetch profile details",
     *      description="This api is used to fetch profile details",
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
     *          description="Post data to fetch profile details",
     *          required=true,
     *          type="string",
     *          @SWG\Schema(ref="#/definitions/deleteEducationImage"),
     *      ),       
     *   

     *  )
     *
    **/
    public function deleteEducationImage_delete(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {


            $flag           = true;
            $req_arr        = array();

            if (!$this->delete('pass_key')){
                $flag       = false;
            } else {
                $req_arr['pass_key']    = $this->delete('pass_key', TRUE);
            }

            if (!$this->delete('user_id')){
                $flag       = false;
            } else {
                $req_arr['user_id']    = $this->delete('user_id', TRUE);
                
            }

            if (!$this->delete('version_id')){
                $flag       = false;
            } else {
                $req_arr['version_id']    = $this->delete('version_id', TRUE);
                
            }

            if (!$this->delete('file_extension')){
                $flag       = false;
            } else {
                $req_arr['file_extension']    = $this->delete('file_extension', TRUE);
                
            }

            if (!$this->delete('education_id')){
                $flag       = false;
            } else {
                $req_arr['education_id']    = $this->delete('education_id', TRUE);
                
            }

            
            $raws = array();   
            if($flag) {
                //log in status checking
                $login_status_arr = $this->login_model->login_status_checking($req_arr);
                
                
                if(is_array($login_status_arr) &&  count($login_status_arr) > 0){


                               

                        //  GET DATA FROM TEMP PROFILE BASIC TABLE               
                        $tempEducation_details = $this->profile_model->getEducationUserTmp($req_arr);
                   
                        //  AVAILABLE DATA FOR ADMIN APPROVAL
                        if(!empty($tempEducation_details) && count($tempEducation_details) > 0){
                            $data['id']    = $tempEducation_details['id'];
                            $data['is_file_uploaded']    = 0;
                            $data['file_extension']    = NULL;
                            $data['s3_media_version']    = NULL;
                            $this->profile_model->updateTempEducation($data);
                             $aws_target_file_path='resources/'.$req_arr['user_id'].'/education/'.$req_arr['education_id'].'.'.$req_arr['file_extension'];
                                $response = $this->aws->deletefile($this->aws->bucket,$aws_target_file_path,$req_arr['version_id']);
                            

                        } else {    // NOT AVAILABLE ANY DATA FOR APPROVAL
                            $maindata = $this->profile_model->getEducationMain($req_arr);
                            if(is_array($maindata) && count($maindata)>0){
                                $data['id']    = $maindata['id'];
                                $data['fk_user_id']    = $maindata['fk_user_id'];
                               
                                $data['fk_degree_type_id']    = $maindata['fk_degree_type_id'];
                                $data['fk_degree_id']    = $maindata['fk_degree_id'];
                                $data['fk_field_of_study_id']    = $maindata['fk_field_of_study_id'];
                                $data['year_of_joining']    = $maindata['year_of_joining'];
                                $data['year_of_graduation']    = $maindata['year_of_graduation'];
                                $data['name_of_institution']    = $maindata['name_of_institution'];
                                $data['fk_pincode_id']    = $maindata['fk_pincode_id'];
                                $data['has_part_time_job']    = $maindata['has_part_time_job'];
                                $data['grades_marks']    = $maindata['grades_marks'];
                                $data['is_file_uploaded']    = '0';
                                $data['file_extension']    = NULL;
                                $data['s3_media_version']    =NULL;
                                $data['fk_admin_id']    = $maindata['fk_admin_id'];
                                
                                $addTempProfileBasic_id   = $this->profile_model->updateEducationMain($data);
                                
                            }else{
                                $http_response      = 'http_response_bad_request';
                                $error_message      =  'Something went wrong'; 
                            }
                            
                        }

                            
                       
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Wrong username or Password'; 
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
     *   @SWG\Delete(
     *      path="/profile/deleteKycImage",
     *      tags={"deleteKycImage"},
     *      summary="fetch profile details",
     *      description="This api is used to fetch profile details",
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
     *          description="Post data to fetch profile details",
     *          required=true,
     *          type="string",
     *          @SWG\Schema(ref="#/definitions/deleteKycImage"),
     *      ),       
     *   

     *  )
     *
    **/
    public function deleteKycImage_delete(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {


            $flag           = true;
            $req_arr        = array();

            if (!$this->delete('pass_key')){
                $flag       = false;
            } else {
                $req_arr['pass_key']    = $this->delete('pass_key', TRUE);
            }

            if (!$this->delete('user_id')){
                $flag       = false;
            } else {
                $req_arr['user_id']    = $this->delete('user_id', TRUE);
                
            }

            if (!$this->delete('version_id')){
                $flag       = false;
            } else {
                $req_arr['version_id']    = $this->delete('version_id', TRUE);
                
            }

            if (!$this->delete('file_extension')){
                $flag       = false;
            } else {
                $req_arr['file_extension']    = $this->delete('file_extension', TRUE);
                
            }

            if (!$this->delete('kyc_id')){
                $flag       = false;
            } else {
                $req_arr['kyc_id']    = $this->delete('kyc_id', TRUE);
                
            }
            // image_type can be front/back
            if (!$this->delete('image_type')){
                $flag       = false;
            } else {
                $req_arr['image_type']    = $this->delete('image_type', TRUE);
                
            }

            
            $raws = array();   
            if($flag) {
                //log in status checking
                $login_status_arr = $this->login_model->login_status_checking($req_arr);
                
                
                if(is_array($login_status_arr) &&  count($login_status_arr) > 0){


                               

                        //  GET DATA FROM TEMP PROFILE BASIC TABLE               
                        $tempKyc_details = $this->profile_model->isinKycTemp($req_arr);
                        
                        //  AVAILABLE DATA FOR ADMIN APPROVAL
                        if(!empty($tempKyc_details) && count($tempKyc_details) > 0){
                            $data['id']    = $req_arr['kyc_id'];
                            if($req_arr['image_type']=='front'){
                                $data['is_front_file_uploaded']    = 0;
                                $data['front_file_extension']    = NULL;
                                $data['front_s3_media_version']    = NULL;
                                $this->profile_model->updateKyc($data);
                                 $aws_target_file_path='resources/'.$req_arr['user_id'].'/kyc/'.$req_arr['kyc_id'].'_front.'.$req_arr['file_extension'];
                                    $response = $this->aws->deletefile($this->aws->bucket,$aws_target_file_path,$req_arr['version_id']);
                            }

                            if($req_arr['image_type']=='back'){
                                $data['is_back_file_uploaded']    = 0;
                                $data['back_file_extension']    = NULL;
                                $data['back_s3_media_version']    = NULL;
                                $this->profile_model->updateKyc($data);
                                 $aws_target_file_path='resources/'.$req_arr['user_id'].'/kyc/'.$req_arr['kyc_id'].'_back.'.$req_arr['file_extension'];
                                    $response = $this->aws->deletefile($this->aws->bucket,$aws_target_file_path,$req_arr['version_id']);
                            }

                            $http_response      = 'http_response_ok';
                            $success_message    = ''; 
                            

                        } else {    // NOT AVAILABLE ANY DATA FOR APPROVAL
                            $maindata = $this->profile_model->getKycMain($req_arr);
                            if(is_array($maindata) && count($maindata)>0){
                                $data['id']    = $maindata['id'];
                                $data['fk_user_id']    = $maindata['fk_user_id'];
                               
                                $data['fk_kyc_template_id']    = $maindata['fk_kyc_template_id'];
                                $data['kyc_data']    = $maindata['kyc_data'];

                                if($req_arr['image_type']=='front'){
                                    $data['is_front_file_uploaded']    = '0';
                                    $data['front_file_extension']    = NULL;
                                    $data['front_s3_media_version']    = NULL;
                                }else{
                                    $data['is_front_file_uploaded']    = '1';
                                    $data['front_file_extension']    = $maindata['front_file_extension'];
                                    $data['front_s3_media_version']    = $maindata['front_s3_media_version'];
                                }

                                if($req_arr['image_type']=='back'){
                                    $data['is_back_file_uploaded']    = '0';
                                    $data['back_file_extension']    = NULL;
                                    $data['back_s3_media_version']    = NULL;
                                }else{
                                    $data['is_back_file_uploaded']    = '1';
                                    $data['back_file_extension']    = $maindata['back_file_extension'];
                                    $data['back_s3_media_version']    = $maindata['back_s3_media_version'];
                                }

                                $data['kyc_addition_datetime']    = $maindata['addition_datetime'];
                                $data['fk_admin_id']    = $maindata['fk_admin_id'];
                                $this->profile_model->updateKyc($data);

                                $http_response      = 'http_response_ok';
                                $success_message    = ''; 
                                
                            }else{
                                $http_response      = 'http_response_bad_request';
                                $error_message      =  'Something went wrong'; 
                            }
                            
                        }

                            
                       
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Wrong username or Password'; 
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


    function test_get(){

        $this->sms->category="MOBVER";
        $this->sms->code = '859652';
        $this->sms->mobile = '9874314610';
        $response = $this->sms->sendSmsFinal();
        print_r($response);
    }    
    
    /* -------------------------------------

    //end of user controller
    */
}