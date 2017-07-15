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
class Admin_data_collections extends REST_Controller
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
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/admin/adminDataCollections_model', 'adminDataCollections_model');
        $this->load->library('form_validation');
        $this->load->library('email');
        $this->load->library('encrypt');
        /*    $this->load->library('excel_reader/PHPExcel');
        $this->load->library('excel_reader/PHPExcel/iofactory');*/
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
     * @ Function Name            : getAllUserData()
     * @ Added Date               : 27-10-2016
     * @ Added By                 : AMIT PANDIT
     * -----------------------------------------------------------------
     * @ Description              : Fetch all user details from admin collections
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 
     * @ Modified By              : 
     * 
     */
    public function getAllUserData_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } else {
            $req_arr1 = $req_arr = $details_arr = array();
            $where    = array();
            $param    = array();
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
            $param['page']      = $this->post('page', true);
            $param['page_size'] = $this->post('page_size', true);
            $param['order']     = $this->post('order', true);
            $param['order_by']  = $this->post('order_by', true);


            if ($this->post('filterByStatus') != '') {
                $where['is_approved'] = $this->post('filterByStatus', true);
            }
            if ($flag) {
                $plaintext_pass_key        = $this->encrypt->decode($req_arr['pass_key']);
                $plaintext_admin_id        = $this->encrypt->decode($req_arr['admin_user_id']);
                $req_arr1['pass_key']      = $plaintext_pass_key;
                $req_arr1['admin_user_id'] = $plaintext_admin_id;
                $check_session             = $this->admin->checkSessionExist($req_arr1);
                if (!empty($check_session) && count($check_session) > 0) {
                    $details_arr['dataset'] = $this->adminDataCollections_model->getAllUserData($param, $where);
                    $details_arr['count']   = $this->adminDataCollections_model->getAllUserData_count($param, $where);
                    if (!empty($details_arr)) {
                        $result_arr      = $details_arr;
                        $http_response   = 'http_response_ok';
                        $success_message = 'Admin Data collections fethed successfully';
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
     * @ Function Name            : sendMailToUser()
     * @ Added Date               : 27-10-2016
     * @ Added By                 : AMIT PANDIT
     * -----------------------------------------------------------------
     * @ Description              : Send email to users 
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 
     * @ Modified By              : 
     * 
     */
    public function sendMailToUser_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } else {
            $req_arr1                  = array();
            $plaintext_pass_key        = $this->encrypt->decode($this->input->post('pass_key', TRUE));
            $plaintext_admin_id        = $this->encrypt->decode($this->input->post('admin_user_id', TRUE));
            $req_arr1['pass_key']      = $plaintext_pass_key;
            $req_arr1['admin_user_id'] = $plaintext_admin_id;
            $check_session             = $this->admin->checkSessionExist($req_arr1);
            if (!empty($check_session) && count($check_session) > 0) {
                $req_arr = array();
                $flag    = true;
                $send    = false;
                if ($flag && empty($this->post('userId', true))) {
                    $flag          = false;
                    $error_message = " User Id is required";
                } else {
                    $whereId['id'] = $this->post('userId', true);
                }
                $ifMailSent = $this->adminDataCollections_model->checkIfMailSent($whereId);
                if ($flag && $ifMailSent['is_mail_sent'] == 0) {
                    $getEmailId         = $this->adminDataCollections_model->getEmailId($whereId);
                    $user_email         = $getEmailId['user_email'];
                    //echo $user_email;die;
                    $config['protocol'] = 'sendmail';
                    $config['mailpath'] = '/usr/sbin/sendmail';
                    $config['charset']  = 'utf-8';
                    $config['wordwrap'] = TRUE;
                    $config['mailtype'] = 'html';
                    $this->email->initialize($config);
                    // email sent to user 
                    $admin_email      = $this->config->item('admin_email');
                    $admin_email_from = $this->config->item('admin_email_from');
                    $this->email->from($admin_email, $admin_email_from);
                    $this->email->to($user_email);
                    $this->email->subject('Welcome to mPokket');
                    $email_data['user_email'] = $user_email;
                    $email_body               = $this->parser->parse('email_templates/UserWelcomeMail', $email_data, true);
                    $this->email->message($email_body);
                    $send          = $this->email->send();
                    // email send end

                    $error_message = '';
                    if ($send) {
                        $update                        = array();
                        $where['id']                   = $whereId['id'];
                        $update['is_mail_sent']        = 1;
                        $update['mail_sent_timestamp'] = date('Y-m-d G:i:s');
                        //pre($update);
                        // pre($whereId);die;
                        $status                        = $this->adminDataCollections_model->updateEmailSentStatus($where, $update);
                        if ($status) {
                            $http_response   = 'http_response_ok';
                            $success_message = 'Invitation sent to User successfully';
                        } else {
                            $http_response = 'http_response_bad_request';
                            $error_message = 'Email sent but user status not updated';
                        }
                    } else {
                        $http_response = 'http_response_bad_request';
                        $error_message = ($error_message) ? $error_message : 'Something went wrong ';
                    }
                } else {
                    $http_response   = 'http_response_ok';
                    $success_message = 'Email already sent to this User';
                }
            } else {
                $http_response = 'http_response_invalid_login';
                $error_message = 'User is invalid';
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : updateApprovedStatus()
     * @ Added Date               : 27-10-2016
     * @ Added By                 : AMIT PANDIT
     * -----------------------------------------------------------------
     * @ Description              : Approved Users status
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 
     * @ Modified By              : 
     * 
     */
    public function updateApprovedStatus_post()
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
            if ($flag && empty($this->post('userId', true))) {
                $flag          = false;
                $error_message = "User Id is required";
            } else {
                $req_arr['id'] = $this->post('userId', true);
            }
            if ($flag) {
                $plaintext_pass_key        = $this->encrypt->decode($req_arr['pass_key']);
                $plaintext_admin_id        = $this->encrypt->decode($req_arr['admin_user_id']);
                $req_arr1['pass_key']      = $plaintext_pass_key;
                $req_arr1['admin_user_id'] = $plaintext_admin_id;
                $check_session             = $this->admin->checkSessionExist($req_arr1);
                if (!empty($check_session) && count($check_session) > 0) {
                    $update                         = array();
                    $whereId                        = array();
                    $whereId['id']                  = $req_arr['id'];
                    $update['is_approved']          = 1;
                    $update['approved_by_admin_id'] = $req_arr1['admin_user_id'];
                    $update['approved_timestamp']   = date('Y-m-d G:i:s');
                    /*//pre($whereId);
                    pre($update);die;*/
                    $isApproved                     = $this->adminDataCollections_model->checkIsApproved($whereId, $update);
                    //echo $isApproved['is_approved'];die;
                    if ($isApproved['is_approved'] == 0) {
                        $status = $this->adminDataCollections_model->updateApprovedStatus($whereId, $update);
                        if ($status) {
                            $result_arr      = $status;
                            $http_response   = 'http_response_ok';
                            $success_message = 'User Approved successfully';
                        } else {
                            $http_response = 'http_response_bad_request';
                            $error_message = 'User approval failed';
                        }
                    } else {
                        $http_response   = 'http_response_ok';
                        $success_message = 'Already approved';
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
    
}