<?php
defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting(0);
//error_reporting(E_ALL);
require_once('src/OAuth2/Autoloader.php');
require APPPATH . 'libraries/api/REST_Controller.php';
class Conversations extends REST_Controller
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
        $developer         = 'www.massoftind.com';
        $this->app_path    = "api_" . $this->config->item('test_api_ver');
        //publish app version
        $version           = str_replace('_', '.', $this->config->item('test_api_ver'));
        $this->publish     = array(
            'version' => $version,
            'developer' => $developer
        );
        //echo $_SERVER['SERVER_ADDR']; exit;
        $dsn               = 'mysql:dbname=' . $this->config->item('oauth_db_database') . ';host=' . $this->config->item('oauth_db_host');
        $dbusername        = $this->config->item('oauth_db_username');
        $dbpassword        = $this->config->item('oauth_db_password');
        $sitemode          = $this->config->item('site_mode');
        $this->path_detail = $this->config->item($sitemode);
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/user_model', 'user_model');
        //$this->load->model('api_' . $this->config->item('test_api_ver') . '/profile_model', 'profile_model');
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/lender/Profile_model', 'profile');
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/lender/lender_model', 'lender');
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/lender/conversations_model', 'conversations');
        // $this->load->model('api_' . $this->config->item('test_api_ver') . '/admin/conversations_model', 'conversations_model');
        $this->load->library('email');
        $this->load->library('encrypt');
        // $this->push_type = 'P';
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
        $this->oauth_server->addGrantType(new OAuth2\GrantType\ClientCredentials($sconversationstorage));
        // Add the "Authorization Code" grant type (this is where the oauth magic happens)
        $this->oauth_server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));
    }


    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getAllTicket()
     * @ Added Date               : 16-11-1016
     * @ Added By                 : 
     * -----------------------------------------------------------------
     * @ Description              : Fetch all User Tickets
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 
     * @ Modified By              : 
     * 
     */
    public function getAllTicket_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } else {
            $flag    = true;
            $param['page']           = $this->post('page', true);
            $param['page_size']      = $this->post('page_size', true);
            $param['order']          = $this->post('order', true);
            $param['order_by']       = $this->post('order_by', true);
            $param['filterByStatus'] = $this->post('filterByStatus', true);
            if ($flag) {
                $req_arr1                  = array();
                $plaintext_user_pass_key   = $this->encrypt->decode($this->post('user_pass_key', TRUE));
                $plaintext_user_id         = $this->encrypt->decode($this->post('user_id', TRUE));
                $req_arr1['user_pass_key'] = $plaintext_user_pass_key;
                $req_arr1['user_id']       = $plaintext_user_id;
                $check_session             = $this->lender->checkSessionExist($req_arr1);
                //pre($req_arr1,1);
                if (!empty($check_session) && count($check_session) > 0) {
                    $all_ticket = $this->conversations->fetchAllTicket($param, $req_arr1['user_id']);
                    if(is_array($all_ticket) && count($all_ticket) > 0) {
                        $i = 0;
                        foreach ($all_ticket as $ticket) {                            
                            $dataTime       = strtotime($ticket['added_timestamp']);
                            $showTimeFormat = date('M d,Y h:i A', $dataTime);
                            $all_ticket[$i]['added_timestamp'] = $showTimeFormat;

                            $i++;
                        }
                    }

                    $ticket_count = $this->conversations->fetchAllTicketCount($param, $req_arr1['user_id']);

                    $details['dataset']        = $all_ticket;
                    $details['count']          = $ticket_count;
                    $result_arr                = $details;

                    $http_response             = 'http_response_ok';
                    $success_message           = '';
                } else {
                    $http_response = 'http_response_invalid_login';
                    $error_message = 'You are not logged in please log in ';
                }
            } else {
                $http_response = 'http_response_bad_request';
                $error_message = ($error_message != '') ? $error_message : 'Invalid parameter';
            }
        }
       json_response($result_arr, $http_response, $error_message, $success_message);
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : fetchTickitDetails()
     * @ Added Date               : 16-11-1016
     * @ Added By                 : 
     * -----------------------------------------------------------------
     * @ Description              : Fetch perticulat Ticket details
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 
     * @ Modified By              : 
     * 
     */
    public function fetchTickitDetails_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } else {
            $flag    = true;
            $req_arr = array();
            if (!intval($this->post('ticket_id'))) {
                $flag          = false;
                $error_message = 'ticket_id can not be null';
            } else {
                $req_arr['ticket_id'] = $this->post('ticket_id', TRUE);
            }
            
            if ($flag) {
                $where                     = array();
                $plaintext_user_pass_key   = $this->encrypt->decode($this->post('user_pass_key', TRUE));
                $plaintext_user_id         = $this->encrypt->decode($this->post('user_id', TRUE));
                $req_arr['user_pass_key'] = $plaintext_user_pass_key;
                $req_arr['user_id']       = $plaintext_user_id;
                $check_session             = $this->lender->checkSessionExist($req_arr);
                $where['tust.id']          = $req_arr['ticket_id'];
                //pre($where,1);
                if (!empty($check_session) && count($check_session) > 0) {
                    $details_arr     = $this->conversations->getConversationDetails($where);
                    $result_arr['dataset'] = $details_arr;
                    $http_response   = 'http_response_ok';
                    $success_message = '';
                } else {
                    $http_response = 'http_response_invalid_login';
                    $error_message = 'Invalid user details';
                }
            } else {
                $http_response = 'http_response_bad_request';
                $error_message = ($error_message != '') ? $error_message : 'Invalid parameter';
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }
 /*
     * --------------------------------------------------------------------------
     * @ Function Name            : addTicket()
     * @ Added Date               : 16-11-1016
     * @ Added By                 : 
     * -----------------------------------------------------------------
     * @ Description              : Add Ticket 
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 
     * @ Modified By              : 
     * 
     */

    public function addTicket_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } else {
            $flag    = true;
            $req_arr = array();
            if (trim($this->post('subject')) == '') {
                $flag          = false;
                $error_message = 'Please enter subject';
            } else {
                $req_arr['subject'] = $this->post('subject', TRUE);
            }
            if (trim($this->post('description')) == '') {
                $flag          = false;
                $error_message = 'Please enter description';
            } else {
                $req_arr['description'] = $this->post('description', TRUE);
            }
            if ($flag) {
                $plaintext_user_pass_key   = $this->encrypt->decode($this->post('user_pass_key', TRUE));
                $plaintext_user_id         = $this->encrypt->decode($this->post('user_id', TRUE));
                $req_arr['user_pass_key'] = $plaintext_user_pass_key;
                $req_arr['user_id']       = $plaintext_user_id;
                $check_session             = $this->lender->checkSessionExist($req_arr);
                if (!empty($check_session) && count($check_session) > 0) {
                    $row                = array();
                    $data               = array();
                    $data['fk_user_id'] = $plaintext_user_id;
                    $data['title']      = $req_arr['subject'];
                    $tick_id            = $this->conversations->addTicket($data);
                    $ticket_no          = 'MPK-' . date('Ymd') . '-' . str_pad($tick_id, 6, '0', STR_PAD_LEFT);
                    $this->conversations->updateTickitID($ticket_no, $tick_id);
                    $row['fk_support_ticket_id'] = $tick_id;
                    $row['fk_user_id']           = $data['fk_user_id'];
                    $row['description']          = $req_arr['description'];
                    $this->conversations->addTickeThreads($row);
                    $tckt['ticket_no'] = $ticket_no;
                    $result_arr        = $tckt;
                    $http_response     = 'http_response_ok';
                    $success_message   = 'ticket added successfully';
                } else {
                    $http_response = 'http_response_invalid_login';
                    $error_message = 'Invalid user details';
                }
            } else {
                $http_response = 'http_response_bad_request';
                $error_message = ($error_message != '') ? $error_message : 'Invalid parameter';
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }

/*
     * --------------------------------------------------------------------------
     * @ Function Name            : addTickitThreads()
     * @ Added Date               : 16-11-1016
     * @ Added By                 : 
     * -----------------------------------------------------------------
     * @ Description              : Add description / chat
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 
     * @ Modified By              : 
     * 
     */
    public function addTickitThreads_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } else {
            $flag    = true;
            $req_arr = array();

            if (!$this->post('ticket_id')) {
                $flag          = false;
                $error_message = 'Please enter ticket id';
            } else {
                $req_arr['ticket_id'] = $this->post('ticket_id', TRUE);
            }
            if (!$this->post('description')) {
                $flag          = false;
                $error_message = 'Please enter description';
            } else {
                $req_arr['description'] = $this->post('description', TRUE);
            }
            if ($flag) {
                $plaintext_user_pass_key   = $this->encrypt->decode($this->post('user_pass_key', TRUE));
                $plaintext_user_id         = $this->encrypt->decode($this->post('user_id', TRUE));
                $req_arr['user_pass_key'] = $plaintext_user_pass_key;
                $req_arr['user_id']       = $plaintext_user_id;
                $check_session             = $this->lender->checkSessionExist($req_arr);
                if (!empty($check_session) && count($check_session) > 0) {
                    $config_data = array();
                    $config_data['fk_user_id']           = $plaintext_user_id;
                    $config_data['fk_support_ticket_id'] = $req_arr['ticket_id'];
                    $config_data['description']          = $req_arr['description'];
                    //pre($config_data);die;

                    $last_id = $this->conversations->addTickeThreads($config_data);
                    $data = array(
                        'id'                      => $last_id,
                        'fk_support_ticket_id'    => $req_arr['ticket_id'],
                        'fk_user_id'              => $plaintext_user_id,
                        'fk_admin_id'             => NULL,
                        'description'             => $req_arr['description'],
                        'added_timestamp'         => date("M j, Y g:i a", time()),
                        'is_unread'               => 1
                    );
                    //pre($data);die;
                    if($last_id) {                                    
                        $result_arr      = $data;
                        $http_response   = 'http_response_ok';
                        $success_message = 'Conversations added successfully';
                        
                    } else {
                        $http_response   = 'http_response_ok';
                        $success_message = 'somthing went wrong';
                    }

                    $http_response   = 'http_response_ok';
                    $success_message = '';
                } else {
                    $http_response = 'http_response_invalid_login';
                    $error_message = 'Invalid user details';
                }
            } else {
                $http_response = 'http_response_bad_request';
                $error_message = ($error_message != '') ? $error_message : 'Invalid parameter';
            }
        }
        

        json_response($result_arr, $http_response, $error_message, $success_message);
    }
}