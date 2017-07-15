<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
* --------------------------------------------------------------------------
* @ Controller Name          : All the page related api call from page controller
* @ Added Date               : 20-06-2016
* @ Added By                 : Vishal
* -----------------------------------------------------------------
* @ Description              : This is the page index page
* -----------------------------------------------------------------
* @ return                   : array
* -----------------------------------------------------------------
* @ Modified Date            : 20-06-2016
* @ Modified By              : Vishal
* 
*/

//All the required library file for API has been included here 
/*require APPPATH . 'libraries/api/AppExtrasAPI.php';
require APPPATH . 'libraries/api/AppAndroidGCMPushAPI.php';
require APPPATH . 'libraries/api/AppApplePushAPI.php';*/

require_once('src/OAuth2/Autoloader.php');
require APPPATH . 'libraries/api/REST_Controller.php';


class User extends REST_Controller{
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
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/admin/User_model', 'user');

        $this->load->library('form_validation');
        $this->load->library('email');
        $this->load->library('encrypt');

        //$this->load->library('calculation');

       $this->encryption->initialize(array(
            'cipher' => 'aes-256',
            'mode'   => 'ctr',
            'key'    => 'SAGLcHZ6nxEBnE4XlJ1nmcPTZaOXOGIX',
        ));


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


    

function getAlluser_post(){
 $error_message = $success_message = $http_response ='';
     $result_arr = array();
     $aboutdetails = array();
    if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) 
        {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        }else{

        $req_arr = $details_arr = array();
          $flag = true;
          if(empty($this->post('pass_key',true))){
          $flag = false;
          $error_message = "pass key is required";
          }else{
            $req_arr['pass_key'] = $this->post('pass_key',true);

           }
          if(empty($this->post('admin_user_id',true))){
              $flag = false;
              $error_message = "admin user id is required";
          }else{
              $req_arr['admin_user_id'] = $this->post('admin_user_id',true);
          }

          if($flag){

             $alluser = $this->user->getAlluser();


             if($alluser > 0 && count($alluser) > 0){

              foreach($alluser as $key=> $val){

                $user_register = date( 'd-M-y',strtotime($val['user_since_timestamp']));
                $alluser[$key]['user_register_timestamp'] = $user_register;
              }



              $result_arr['dataset'] = $alluser;
              $success_message = 'success fetch all user';
              $http_response = 'http_response_ok';

             }else{

              $result_arr['dataset'] = '';
              $success_message = 'No record found';
              $http_response = 'http_response_ok';


             }

          }else{

            $http_response = 'http_response_unauthorized';
          }

       }

   json_response($result_arr, $http_response, $error_message, $success_message);
}


public function userDeatil_post(){

$error_message = $success_message = $http_response ='';
     $result_arr = array();
     $aboutdetails = array();
    if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) 
        {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        }else{

          $req_arr = $details_arr = array();
          $flag = true;
          if(empty($this->post('pass_key',true))){
          $flag = false;
          $error_message = "pass key is required";
          }else{
            $req_arr['pass_key'] = $this->post('pass_key',true);

           }

          if(empty($this->post('admin_user_id',true))){
              $flag = false;
              $error_message = "admin user id is required";
          }else{
              $req_arr['admin_user_id'] = $this->post('admin_user_id',true);
          }

          if(empty($this->post('user_id',true))){
          $flag = false;
          $error_message = "user id is required";

          }else{
            $req_arr['user_id'] = $this->post('user_id',true);

          }

          if($flag){

            $user_id = $req_arr['user_id'];
            
          $userUserDeatail = $this->user->getAlluser(array('user_id'=>$user_id))[0];
          if($userUserDeatail > 0){

          $result_arr['dataset'] = $userUserDeatail;
          $http_response = 'http_response_ok';
          $success_message = 'fetch user detail success';
          }else{

          $result_arr['dataset'] = '';
          $http_response = 'http_response_ok';
          $success_message = 'No record found';


          }

          }else{
            $http_response = 'http_response_unauthorized';
          }

        }

json_response($result_arr, $http_response, $error_message, $success_message);

}


function emailVerify_post(){
$error_message = $success_message = $http_response ='';
     $result_arr = array();
     $aboutdetails = array();
    if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) 
        {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        }else{

          $req_arr = $details_arr = array();
          $flag = true;

          if(empty($this->post('pass_key',true))){
          $flag = false;
          $error_message = "pass key is required";
          }else{
            $req_arr['pass_key'] = $this->post('pass_key',true);

           }

           if(empty($this->post('user_id',true))){
              $flag = false;
              $error_message = 'user id is required';
          }else{
            $req_arr['user_id'] = $this->post('user_id',true);
          }

          if(empty($this->post('is_verify',true))){
            $flag = false;
            $error_message = 'Email Value is required';

          }else{
            $req_arr['email_verify'] = $this->post('is_verify',true);

          }

          if($flag){

            $userStatus = $this->user->getAlluser(array('user_id'=>$req_arr['user_id']))[0];

            $phone_id_verified = $userStatus['is_mobile_number_verified'];

            if($phone_id_verified =='0'){
              $status = '0';
              $req_arr['is_active'] = $status;
            }else{

              $status = '1';
              $req_arr['is_active'] = $status;

            }

            $this->user->is_verify_update($req_arr);

            $result_arr['dataset'] = '';
            $success_message = 'User Email Verify';
            $http_response  = 'http_response_ok';

          }else{
            $http_response = 'http_response_unauthorized';
          }

        }

  json_response($result_arr,$http_response,$error_message,$success_message);

}

public function verifyPhone_post($data = array()){

  $error_message = $success_message = '';
  $result_arr = array();

  if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) 
        {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        }else{
          $flag = true;

          if(empty($this->post('pass_key',true))){

            $flag = false;
            $error_message = 'pass key is required';

          }else{

            $req_arr['pass_key'] = $this->post('pass_key',true);
          }

          if(empty($this->post('admin_user_id',true))){

            $flag = false;
            $error_message = 'admin_user_id is required';

          }else{
            $req_arr['admin_user_id'] = $this->post('admin_user_id');

          }

          if(empty($this->post('user_id',true))){
            $flag = false;
            $error_message = 'User id is required';

          }else{

            $req_arr['user_id'] = $this->post('user_id',true);
          }

          if(empty($this->post('is_verify',true))){
            $flag = false;
            $error_message = 'Phone value is required';

          }else{
            $req_arr['phoneno_verify'] = $this->post('is_verify',true);

          }

          if($flag){

            $userStatus = $this->user->getAlluser(array('user_id'=>$req_arr['user_id']))[0];

            $email_id_verified = $userStatus['is_email_id_verified'];

            if($email_id_verified =='0'){
              $status = '0';
              $req_arr['is_active'] = $status;
            }else{

              $status = '1';
              $req_arr['is_active'] = $status;

            }

            $this->user->is_verify_update($req_arr);
            $result_arr['dataset'] ='';
            $success_message = 'User phone verify';
            $http_response = 'http_response_ok';

          }else{

            $http_response = 'http_response_unauthorized';
          }



        }

  json_response($result_arr,$http_response,$error_message,$success_message);


}

/****************************end of User controlller**********************/

}