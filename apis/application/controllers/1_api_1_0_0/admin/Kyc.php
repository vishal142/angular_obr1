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


class Kyc extends REST_Controller{
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
        $this->load->model('api_'.$this->config->item('test_api_ver').'/admin/admin_model','admin');
        $this->load->model('api_'.$this->config->item('test_api_ver').'/admin/kyc_document_model','kyc_document');
        $this->load->model('api_'.$this->config->item('test_api_ver').'/admin/kyc_model','kyc');
        $this->load->library('form_validation');
        $this->load->library('email');
        $this->load->library('encrypt');
       
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
     * @ Function Name            : getAllKYC()
     * @ Added Date               : 08-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : get all degree
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 08-09-2016
     * @ Modified By              : Piyalee
     * 
    */
    public function getAllKYC_post(){
        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())){
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } else {
            $req_arr = $details_arr = array();
            //pre($this->input->post(),1);
            $flag           = true;
            if(empty($this->post('pass_key', true))){
                $flag           = false;
                $error_message  = "Pass Key is required";
            } else {
                $req_arr['pass_key']        = $this->post('pass_key', true);
            }

            if($flag && empty($this->post('admin_user_id', true))) {
                $flag           = false;
                $error_message  = "Admin User Id is required";
            } else {
                $req_arr['admin_user_id']   = $this->post('admin_user_id', true);
            }

            if($flag && empty($this->post('page', true))) {
                $flag           = false;
                $error_message  = "Page is required";
            } else {
                $req_arr['page']            = $this->post('page', true);
            }

            if($flag && empty($this->post('page_size', true))) {
                $flag           = false;
                $error_message  = "Page Size is required";
            } else {
                $req_arr['page_size']       = $this->post('page_size', true);
            }

            $req_arr['order']                   = $this->input->post('order', true);
            $req_arr['order_by']                = $this->input->post('order_by', true);
            $req_arr['searchByDocymentType']    = $this->input->post('searchByDocymentType', true);
            if($flag){
                $check_user = array(
                    'pass_key'      => $this->encrypt->decode($req_arr['pass_key']),
                    'admin_user_id' => $this->encrypt->decode($req_arr['admin_user_id']),
                );
                $checkloginstatus = $this->admin->checkSessionExist($check_user);
                if(!empty($checkloginstatus) && count($checkloginstatus) > 0){
                    $details_arr['dataset'] = $this->kyc->getAllKYC($req_arr);
                    //$details_arr['count']   = $this->kyc->getAllKYCTemplateCount($req_arr);
                    //pre($details_arr,1);
                    if(!empty($details_arr) && count($details_arr) > 0)
                    {
                        $result_arr         = $details_arr;
                        $http_response      = 'http_response_ok';
                        $success_message    = 'All KYC Templates';  
                    } else {
                        $http_response      = 'http_response_bad_request';
                        $error_message      = 'Something went wrong in API';  
                    }
                } else {
                    $http_response  = 'http_response_invalid_login';
                    $error_message  = 'User is invalid';
                }
            } else {
                $http_response = 'http_response_bad_request';
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : loadKYCForm()
     * @ Added Date               : 08-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : get all degree
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 08-09-2016
     * @ Modified By              : Piyalee
     * 
    */
    public function loadKYCForm_post(){
        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())){
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } else {
            $req_arr = $details_arr = array();
            //pre($this->input->post(),1);
            $flag           = true;
            if(empty($this->post('pass_key', true))){
                $flag           = false;
                $error_message  = "Pass Key is required";
            } else {
                $req_arr['pass_key']        = $this->post('pass_key', true);
            }

            if($flag && empty($this->post('admin_user_id', true))) {
                $flag           = false;
                $error_message  = "Admin User Id is required";
            } else {
                $req_arr['admin_user_id']   = $this->post('admin_user_id', true);
            }
            
            if($flag){
                $check_user = array(
                    'pass_key'      => $this->encrypt->decode($req_arr['pass_key']),
                    'admin_user_id' => $this->encrypt->decode($req_arr['admin_user_id']),
                );
                $checkloginstatus = $this->admin->checkSessionExist($check_user);
                if(!empty($checkloginstatus) && count($checkloginstatus) > 0){
                    $details_arr['allProfession'] = $this->admin->getAllProfession();
                    $details_arr['allKYCDocument']= $this->kyc_document->getAllKYCDocument();
                    //pre($details_arr,1);
                    if(!empty($details_arr) && count($details_arr) > 0)
                    {
                        $result_arr         = $details_arr;
                        $http_response      = 'http_response_ok';
                        $success_message    = 'All KYC Templates';  
                    } else {
                        $http_response      = 'http_response_bad_request';
                        $error_message      = 'Something went wrong in API';  
                    }
                } else {
                    $http_response  = 'http_response_invalid_login';
                    $error_message  = 'User is invalid';
                }
            } else {
                $http_response = 'http_response_bad_request';
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }






    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getKYCData()
     * @ Added Date               : 08-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : get degree details
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 08-09-2016
     * @ Modified By              : Piyalee
     * 
    */
    public function getKYCData_post(){
        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())){
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } else {
            $req_arr = $details_arr = array();
            //pre($this->input->post());
            $flag           = true;
            if(empty($this->post('pass_key', true))){
                $flag           = false;
                $error_message  = "Pass Key is required";
            } else {
                $req_arr['pass_key']        = $this->post('pass_key', true);
            }

            if($flag && empty($this->post('admin_user_id', true))) {
                $flag           = false;
                $error_message  = "Admin User Id is required";
            } else {
                $req_arr['admin_user_id']   = $this->post('admin_user_id', true);
            }

            /*if($flag && empty($this->post('docType', true))) {
                $flag           = false;
                $error_message  = "Document type is required";
            } else {*/
                $req_arr['docType'] = $this->post('docType', true);
            //}

            /*if($flag && empty($this->post('professionTypeId', true))) {
                $flag           = false;
                $error_message  = "Profession Type is required";
            } else {*/
                $req_arr['professionTypeId']       = $this->post('professionTypeId', true);
            //}

            //pre($req_arr,1);
            if($flag){
                $check_user = array(
                    'pass_key'      => $this->encrypt->decode($req_arr['pass_key']),
                    'admin_user_id' => $this->encrypt->decode($req_arr['admin_user_id']),
                );
                $checkloginstatus = $this->admin->checkSessionExist($check_user);
                if(!empty($checkloginstatus) && count($checkloginstatus) > 0){
                    $details_arr['dataset'] = $this->kyc->getKYCData($req_arr);
                    $details_arr['allProfession'] = $this->admin->getAllProfession();
                    $details_arr['allKYCDocument']= $this->kyc_document->getAllKYCDocument();
                    //pre($details_arr,1);

                    if(!empty($details_arr) && count($details_arr) > 0)
                    {
                        $result_arr         = $details_arr;
                        $http_response      = 'http_response_ok';
                        $success_message    = 'Get KYC detais';  
                    } else {
                        $http_response      = 'http_response_bad_request';
                        $error_message      = 'Something went wrong in API';  
                    }
                } else {
                    $http_response  = 'http_response_invalid_login';
                    $error_message  = 'User is invalid';
                }
            } else {
                $http_response = 'http_response_bad_request';
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }



    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : editKYCDetail()
     * @ Added Date               : 08-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : update degree details
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 08-09-2016
     * @ Modified By              : Piyalee
     * 
    */
    public function editKYCDetail_post(){
        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())){
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } else {
            $req_arr = $details_arr = array();
            $flag = true;
            if(empty($this->post('pass_key', true))) {
                $flag           = false;
                $error_message  = "Pass Key is required";
            } else {
                $req_arr['pass_key']            = $this->post('pass_key', true);
            }

            if($flag && empty($this->post('admin_user_id', true))) {
                $flag           = false;
                $error_message  = "Admin User Id is required";
            } else {
                $req_arr['admin_user_id']       = $this->post('admin_user_id', true);
            }

            if($flag){
                $check_user = array(
                    'pass_key'      => $this->encrypt->decode($req_arr['pass_key']),
                    'admin_user_id' => $this->encrypt->decode($req_arr['admin_user_id']),
                );
                $checkloginstatus = $this->admin->checkSessionExist($check_user);
                if(!empty($checkloginstatus) && count($checkloginstatus) > 0){
                    $kycData_str_arr = json_decode($this->post('kycData_str'), TRUE);
                    //pre($kycData_str_arr,1);
                    
                    $insertBatchKYCData_arr = $deleteBatchKYCData_arr = array();

                    $deleteBatchKYCData_arr = $this->kyc->getKYCData(
                        array('docType' => $this->post('document_type'), 
                            'professionTypeId' => $this->post('fk_profession_type_id'), 
                            'deleteData' => 'true')
                        );
                    //pre($deleteBatchKYCData_arr,1);

                    $userModeArr = array('B', 'L', 'A');
                    foreach ($userModeArr as $value_userModeArr) {
                        foreach ($kycData_str_arr[$value_userModeArr] as $key => $value) {
                            if($value['fk_document_id'] != ''){
                                $kycData_arr = array();
                            
                                $kycData_arr['fk_document_id']          = $value['fk_document_id'];
                                $kycData_arr['fk_profession_type_id']   = $this->post('fk_profession_type_id');
                                $kycData_arr['user_mode']               = $value['user_mode'];
                                $kycData_arr['document_type']           = $this->post('document_type');
                                $kycData_arr['priority_level']          = $value['priority_level']; 

                                $insertBatchKYCData_arr[] = $kycData_arr; 
                            }                        
                        }
                    }                    
                    //pre($insertBatchKYCData_arr);

                    $documentId = $this->kyc->editKYCDetail($deleteBatchKYCData_arr, $insertBatchKYCData_arr);

                    if($documentId){                    
                        $http_response      = 'http_response_ok';
                        $success_message    = 'KYC template updated successfully';

                    } else {

                        $http_response      = 'http_response_bad_request';
                        $success_message    = 'Something went wrong in API';
                    }
                    

                }else{
                    $http_response  = 'http_response_invalid_login';
                    $error_message  = 'User is invalid';
                }
            }else{
                $http_response      = 'http_response_bad_request';
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }



    //$details_arr = $this->admin->getAllProfession();

    /****************************end of degree controlller**********************/

}
