<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * --------------------------------------------------------------------------
 * @ Controller Name          : All Transaction related api 
 * @ Added Date               : 19-10-2016
 * @ Added By                 : Amit pandit
 * -----------------------------------------------------------------
 * @ Description              : 
 * -----------------------------------------------------------------
 * @ return                   : array
 * -----------------------------------------------------------------
 * @ Modified Date            : 
 * @ Modified By              : 
 * 
 */
//All the required library file for API has been included here 
/*require APPPATH . 'libraries/api/AppExtrasAPI.php';
require APPPATH . 'libraries/api/AppAndroidGCMPushAPI.php';
require APPPATH . 'libraries/api/AppApplePushAPI.php';*/
require_once('src/OAuth2/Autoloader.php');
require APPPATH . 'libraries/api/REST_Controller.php';
class Conversations extends REST_Controller
{
    function __construct()
    {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: authorization, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header('Access-Control-Allow-Credentials: true');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
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
        $dsn          = 'mysql:dbname=' . $this->config->item('oauth_db_database') . ';host=' . $this->config->item('oauth_db_host');
        $dbusername   = $this->config->item('oauth_db_username');
        $dbpassword   = $this->config->item('oauth_db_password');
        /*$sitemode= $this->config->item('site_mode');
        $this->path_detail=$this->config->item($sitemode);*/
        $this->tables = $this->config->item('tables');
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/admin/admin_model', 'admin');
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/admin/conversations_model', 'conversations_model');
        
        $this->load->library('form_validation');
        $this->load->library('email');
        $this->load->library('encrypt');
        $this->load->library('excel_reader/PHPExcel');
        $this->load->library('excel_reader/PHPExcel/iofactory');
        //$this->load->library('calculation');
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


    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getAllConversations()
     * @ Added Date               : 26-10-2016
     * @ Added By                 : AMIT PANDIT
     * -----------------------------------------------------------------
     * @ Description              : Fetch all list of tickets and conversations 
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 
     * @ Modified By              : 
     * 
    */
    public function getAllConversations_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } else {
            $req_arr1 = $req_arr = $details_arr = array();
            $flag     = true;
            if (empty($this->post('pass_key', true))) {
                $flag          = false;
                $error_message = "Pass Key is required";
            } else {
                $req_arr['pass_key'] = $this->post('pass_key', true);
            }
            if ($flag && empty($this->post('admin_user_id', true))) {
                $flag          = false;
                $error_message = "Admin User Id is required";
            } else {
                $req_arr['admin_user_id'] = $this->post('admin_user_id', true);
            }
            $param['page']           = $this->post('page', true);
            $param['page_size']      = $this->post('page_size', true);
            $param['order']          = $this->post('order', true);
            $param['order_by']       = $this->post('order_by', true);
            $param['filterByStatus'] = $this->post('filterByStatus', true);
            $param['searchByTitle']  = $this->post('searchByTitle', true);
            
            if ($flag) {
                $plaintext_pass_key        = $this->encrypt->decode($req_arr['pass_key']);
                $plaintext_admin_id        = $this->encrypt->decode($req_arr['admin_user_id']);
                $req_arr1['pass_key']      = $plaintext_pass_key;
                $req_arr1['admin_user_id'] = $plaintext_admin_id;
                $check_session             = $this->admin->checkSessionExist($req_arr1);
                if (!empty($check_session) && count($check_session) > 0) {
                    
                    $details_arr['dataset'] = $this->conversations_model->getAllConversations($param);
                    $details_arr['count']   = $this->conversations_model->getAllConversationsCount($param);
                    
                    if (!empty($details_arr)) {
                        $result_arr      = $details_arr;
                        $http_response   = 'http_response_ok';
                        $success_message = 'Support tickets fetched successfully';
                        
                    } else {
                        $http_response = 'http_response_bad_request';
                        $error_message = 'Something went wrong in API';
                    }
                } else {
                    $http_response = 'http_response_invalid_login';
                    $error_message = 'User is invalid';
                }
            } else {
                $http_response = 'http_response_bad_request';
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }
    
   /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getAllConversations()
     * @ Added Date               : 26-10-1016
     * @ Added By                 : 
     * -----------------------------------------------------------------
     * @ Description              : Fetch all conversations 
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 
     * @ Modified By              : 
     * 
     */
    
    
    public function getConversationDetails_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } else {
            $req_arr1 = $req_arr = $details_arr = array();
            $flag     = true;
            if (empty($this->post('pass_key', true))) {
                $flag          = false;
                $error_message = "Pass Key is required";
            } else {
                $req_arr['pass_key'] = $this->post('pass_key', true);
            }
            if ($flag && empty($this->post('admin_user_id', true))) {
                $flag          = false;
                $error_message = "Admin User Id is required";
            } else {
                $req_arr['admin_user_id'] = $this->post('admin_user_id', true);
            }
            
            if ($flag && empty($this->post('ticketId', true))) {
                $flag          = false;
                $error_message = "Ticket Id is required";
            } else {
                $where['tust.id'] = $this->post('ticketId', true);
            }
            
            if ($flag) {
                $plaintext_pass_key        = $this->encrypt->decode($req_arr['pass_key']);
                $plaintext_admin_id        = $this->encrypt->decode($req_arr['admin_user_id']);
                $req_arr1['pass_key']      = $plaintext_pass_key;
                $req_arr1['admin_user_id'] = $plaintext_admin_id;
                $check_session             = $this->admin->checkSessionExist($req_arr1);
                if (!empty($check_session) && count($check_session) > 0) {
                    
                    $details_arr = $this->conversations_model->getConversationDetails($where);
                    //pre($details_arr);die;                                        
                    
                    if (!empty($details_arr)) {
                        $result_arr      = $details_arr;
                        $http_response   = 'http_response_ok';
                        $success_message = 'Conversations Details fetched successfully';
                        
                    } else {
                        $http_response = 'http_response_bad_request';
                        $error_message = 'Something went wrong in API';
                    }
                } else {
                    $http_response = 'http_response_invalid_login';
                    $error_message = 'User is invalid';
                }
            } else {
                $http_response = 'http_response_bad_request';
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }


    public function addConversationThreads_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
            
        } else {
            
            $req_arr1 = $req_arr = $details_arr = $data_id = array();
            $flag     = true;
            if (empty($this->post('pass_key', true))) {
                $flag          = false;
                $error_message = "Pass Key is required";
            } else {
                $req_arr['pass_key'] = $this->post('pass_key', true);
            }
            
            if ($flag && empty($this->post('admin_user_id', true))) {
                $flag          = false;
                $error_message = "Admin User Id is required";
            } else {
                $req_arr['admin_user_id'] = $this->post('admin_user_id', true);
            }           
            
            if ($flag && empty($this->post('ticketId', true))) {
                $flag          = false;
                $error_message = "ticketId is required";
            } else {
                $req_arr['fk_support_ticket_id'] = $this->post('ticketId', true);
            }  

            if ($flag && empty($this->post('description', true))) {
                $flag          = false;
                $error_message = "Description is required";
            } else {
                $req_arr['description'] = $this->post('description', true);
            }   
        }
                       
        if ($flag) {
            $plaintext_pass_key = $this->encrypt->decode($req_arr['pass_key']);
            $plaintext_admin_id = $this->encrypt->decode($req_arr['admin_user_id']);
            
            $req_arr1['pass_key']      = $plaintext_pass_key;
            $req_arr1['admin_user_id'] = $plaintext_admin_id;
            $check_session             = $this->admin->checkSessionExist($req_arr1);
            
            if (!empty($check_session) && count($check_session) > 0) {
                $config_data                            = array();
                $config_data['fk_support_ticket_id']    = $req_arr['fk_support_ticket_id'];
                $config_data['fk_admin_id']             = $req_arr1['admin_user_id'];
                $config_data['description']             = $req_arr['description'];
               
                //pre($config_data);die;
                $last_id = $this->conversations_model->addConversationThreads($config_data);

                $data = array(
                    'id'                      => $last_id,
                    'fk_support_ticket_id'    => $req_arr['fk_support_ticket_id'],
                    'fk_user_id'              => NULL,
                    'fk_admin_id'             => $req_arr1['admin_user_id'],
                    'description'             => $req_arr['description'],
                    'added_timestamp'         => date("M j, Y g:i a", time()),
                    'is_unread'               => 1
                );

               if($last_id) {                                    
                    $result_arr      = $data;
                    $http_response   = 'http_response_ok';
                    $success_message = 'Conversations added successfully';
                    
                } else {
                    $http_response   = 'http_response_ok';
                    $success_message = 'somthing went wrong';
                }
            } else {
                $http_response = 'http_response_invalid_login';
                $error_message = 'User is invalid';
            }
        } else {
            $http_response = 'http_response_bad_request';
        }

        json_response($result_arr, $http_response, $error_message, $success_message);
    }



    public function updateConversation_post()
    {

        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
            
        } else {
            
            $req_arr1 = $req_arr = $details_arr = $data_id = array();
            $flag     = true;
            if (empty($this->post('pass_key', true))) {
                $flag          = false;
                $error_message = "Pass Key is required";
            } else {
                $req_arr['pass_key'] = $this->post('pass_key', true);
            }
            
            if ($flag && empty($this->post('admin_user_id', true))) {
                $flag          = false;
                $error_message = "Admin User Id is required";
            } else {
                $req_arr['admin_user_id'] = $this->post('admin_user_id', true);
            }           
            
            if ($flag && empty($this->post('ticketId', true))) {
                $flag          = false;
                $error_message = "ticketId is required";
            } else {
                $req_arr['id'] = $this->post('ticketId', true);
            }  

            if ($flag && empty($this->post('status', true))) {
                $flag          = false;
                $error_message = "status is required";
            } else {
                $req_arr['status'] = $this->post('status', true);
            }   
        }
     if ($flag) {
            $plaintext_pass_key = $this->encrypt->decode($req_arr['pass_key']);
            $plaintext_admin_id = $this->encrypt->decode($req_arr['admin_user_id']);
            
            $req_arr1['pass_key']      = $plaintext_pass_key;
            $req_arr1['admin_user_id'] = $plaintext_admin_id;
            $check_session             = $this->admin->checkSessionExist($req_arr1);
            
            if (!empty($check_session) && count($check_session) > 0) {
                $config_data                            = array();
                $param['id']        = $req_arr['id'];
                $param['status']    = $req_arr['status'];
                //echo $config_data['status'];die;
                $status = $this->conversations_model->updateConversation($param);
              if ($status) {
                                    
                    $result_arr      = $status;
                    $http_response   = 'http_response_ok';
                    $success_message = 'Ticket status Updated successfully';
                    
                } else {
                    $http_response   = 'http_response_ok';
                    $success_message = 'Same as given status';
                }
            } else {
                $http_response = 'http_response_invalid_login';
                $error_message = 'User is invalid';
            }
        } else {
            $http_response = 'http_response_bad_request';
        }

        json_response($result_arr, $http_response, $error_message, $success_message);
    }

}