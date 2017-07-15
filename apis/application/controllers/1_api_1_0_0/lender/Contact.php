<?php
defined('BASEPATH') OR exit('No direct script access allowed');

error_reporting(0);
//error_reporting(E_ALL);

require_once('src/OAuth2/Autoloader.php');
require APPPATH . 'libraries/api/REST_Controller.php';



class Contact extends REST_Controller
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
        $developer      = 'www.massoftind.com';
        $this->app_path = "api_" . $this->config->item('test_api_ver');
        //publish app version
        $version        = str_replace('_', '.', $this->config->item('test_api_ver'));
        
        $this->publish = array(
            'version' => $version,
            'developer' => $developer
        );
        
        //echo $_SERVER['SERVER_ADDR']; exit;
        
        $dsn        = 'mysql:dbname=' . $this->config->item('oauth_db_database') . ';host=' . $this->config->item('oauth_db_host');
        $dbusername = $this->config->item('oauth_db_username');
        $dbpassword = $this->config->item('oauth_db_password');
        
        
        $sitemode          = $this->config->item('site_mode');
        $this->path_detail = $this->config->item($sitemode);
        
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/user_model', 'user_model');
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/profile_model', 'profile_model');
        
        
        $this->load->library('email');
        
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
    
    
    public function contactUs_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
            
        } else {
            
            $req_arr = array();
            $flag    = true;
            if (empty($this->post('name', true))) {
                $flag          = false;
                $error_message = "name is required";
            } else {
                $req_arr['name'] = $this->post('name', true);
            }
            if ($flag && empty($this->post('emailId', true))) {
                $flag          = false;
                $error_message = "Email Id is required";
            } else {
                $req_arr['emailId'] = $this->post('emailId', true);
            }
            if ($flag && empty($this->post('phone', true))) {
                $flag          = false;
                $error_message = "Phone number required";
            } else {
                $req_arr['phone'] = $this->post('phone', true);
            }
            if ($flag && empty($this->post('type', true))) {
                $flag          = false;
                $error_message = "Type required";
            } else {
                $req_arr['type'] = $this->post('type', true);
            }
            if ($flag && empty($this->post('title', true))) {
                $flag          = false;
                $error_message = "Title required";
            } else {
                $req_arr['title'] = $this->post('title', true);
            }
            if ($flag && empty($this->post('message', true))) {
                $flag          = false;
                $error_message = "message required";
            } else {
                $req_arr['message'] = $this->post('message', true);
            }
            
            //pre($req_arr,1);
            if ($flag) {
                
                //initialising codeigniter email
                $config['protocol']     = 'sendmail';
                $config['mailpath']     = '/usr/sbin/sendmail';
                $config['charset']      = 'utf-8';
                $config['wordwrap']     = TRUE;
                $config['mailtype']     = 'html';
                $this->email->initialize($config);
                
                // email sent to user 
                $admin_email= 'subhankar.pramanik@massoftind.com'; 
                //$admin_email= $this->config->item('admin_email');
                $user_email = $req_arr['emailId'];
                $user_email_from= $req_arr['name'];
                $this->email->from($user_email, $user_email_from);
                //$this->email->from($user_email);
                
                $this->email->to($admin_email);          
                $this->email->subject('Contact Us - '.$req_arr['title']);
                
                $email_data = $req_arr;  
                //pre($email_data,1);                 
                
                $email_body = $this->parser->parse('email_templates/contactUs', $email_data, true);
                $this->email->message($email_body);            
                
                $send = $this->email->send();
                // email send end
                //$send          = true;
                $error_message = '';
                
                if ($send) {
                   // $result_arr      = $req_arr;
                    $http_response   = 'http_response_ok';
                    $success_message = 'Mail sent Successfully';
                } else {
                    $http_response = 'http_response_bad_request';
                    $error_message = ($error_message) ? $error_message : 'Something went wrong in API';
                }                
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);        
    }
    
    
}