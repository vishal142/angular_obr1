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
require_once('src/OAuth2/Autoloader.php');
require APPPATH . 'libraries/api/AppExtrasAPI.php';
require APPPATH . 'libraries/api/AppAndroidGCMPushAPI.php';
require APPPATH . 'libraries/api/AppApplePushAPI.php';
require APPPATH . 'libraries/api/REST_Controller.php';


class User extends REST_Controller
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
        $dsn        = 'mysql:dbname=' . $this->config->item('oauth_db_database') . ';host=' . $this->config->item('oauth_db_host');
        $dbusername = $this->config->item('oauth_db_username');
        $dbpassword = $this->config->item('oauth_db_password');
        
        /*$sitemode= $this->config->item('site_mode');
        $this->path_detail=$this->config->item($sitemode);*/
        $this->tables = $this->config->item('tables');
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/admin/admin_model', 'admin');
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/admin/user_model', 'user');
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/notifications_model', 'notifications_model');
        
        $this->load->library('form_validation');
        $this->load->library('email');
        $this->load->library('encrypt');
        
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
    
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getAllUsers()
     * @ Added Date               : 09-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : get all user
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 09-09-2016
     * @ Modified By              : Piyalee
     * 
     */
    public function getAllUsers_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } else {
            $req_arr = $details_arr = array();
            $flag    = true;
            if (empty($this->post('pass_key', true))) {
                $flag          = false;
                $error_message = "Pass Key is required";
            } else {
                $req_arr['pass_key'] = $this->post('pass_key', true);
            }
            
            if (empty($this->post('admin_user_id', true))) {
                $flag          = false;
                $error_message = "Admin User Id is required";
            } else {
                $req_arr['admin_user_id'] = $this->post('admin_user_id', true);
            }
            
            if (empty($this->post('page', true))) {
                $flag          = false;
                $error_message = "Page is required";
            } else {
                $req_arr['page'] = $this->post('page', true);
            }
            
            if (empty($this->post('page_size', true))) {
                $flag          = false;
                $error_message = "Page Size is required";
            } else {
                $req_arr['page_size'] = $this->post('page_size', true);
            }
            $req_arr['order']             = $this->post('order', true);
            $req_arr['order_by']          = $this->post('order_by', true);
            $req_arr['searchByNameEmail'] = $this->post('searchByNameEmail', true);
            $req_arr['searchUserMode']    = $this->post('searchUserMode', true);
            $req_arr['searchProfession']  = $this->post('searchProfession', true);
            
            if ($flag) {
                $check_user       = array(
                    'pass_key' => $this->encrypt->decode($req_arr['pass_key']),
                    'admin_user_id' => $this->encrypt->decode($req_arr['admin_user_id'])
                );
                $checkloginstatus = $this->admin->checkSessionExist($check_user);
                if (!empty($checkloginstatus) && count($checkloginstatus) > 0) {
                    $details_arr['professions'] = $this->user->getAllProfessions();
                    $details_arr['dataset']     = $this->user->getAllUsers($req_arr);
                    if (!empty($details_arr['dataset'])) {
                        foreach ($details_arr['dataset'] as $key => $user) {
                            $details_arr['dataset'][$key]['connection_count'] = $this->user->getCurrentConnection($user['id']);
                            $details_arr['dataset'][$key]['mcoin_count']      = $this->user->getTotalMcoin($user['id']);
                            
                            $details_arr['dataset'][$key]['image_path'] = 'assets/img/user_avatar.png';
                            if (isset($details_arr['dataset'][$key]['profile_picture_file_extension']) && $details_arr['dataset'][$key]['profile_picture_file_extension'] != "" && $details_arr['dataset'][$key]['s3_media_version'] != "") {
                                $profile_picture_file_url                   = $this->config->item('bucket_url') . $details_arr['dataset'][$key]['id'] . '/profile/' . $details_arr['dataset'][$key]['id'] . '.' . $details_arr['dataset'][$key]['profile_picture_file_extension'] . '?versionId=' . $details_arr['dataset'][$key]['s3_media_version'];
                                $details_arr['dataset'][$key]['image_path'] = $profile_picture_file_url;
                            }
                        }
                    }
                    $count                = $this->user->getAllUsersCount($req_arr);
                    $details_arr['count'] = $count['count_user'];
                    
                    $result_arr      = $details_arr;
                    $http_response   = 'http_response_ok';
                    $success_message = 'All User';
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
     * @ Function Name            : getUserBasic()
     * @ Added Date               : 09-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : get all user
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 09-09-2016
     * @ Modified By              : Piyalee
     * 
     */
    public function getUserBasic_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } else {
            $req_arr = $details_arr = array();
            //pre($this->post(),1);
            $flag    = true;
            if (empty($this->post('pass_key', true))) {
                $flag          = false;
                $error_message = "Pass Key is required";
            } else {
                $req_arr['pass_key'] = $this->post('pass_key', true);
            }
            
            if (empty($this->post('admin_user_id', true))) {
                $flag          = false;
                $error_message = "Admin User Id is required";
            } else {
                $req_arr['admin_user_id'] = $this->post('admin_user_id', true);
            }
            
            if (empty($this->post('userId', true))) {
                $flag          = false;
                $error_message = "User Id is required";
            } else {
                $req_arr['userId'] = $this->post('userId', true);
            }
            
            if ($flag) {
                $check_user       = array(
                    'pass_key' => $this->encrypt->decode($req_arr['pass_key']),
                    'admin_user_id' => $this->encrypt->decode($req_arr['admin_user_id'])
                );
                $checkloginstatus = $this->admin->checkSessionExist($check_user);
                if (!empty($checkloginstatus) && count($checkloginstatus) > 0) {
                    $UserBasicDetails = $this->user->getTempUserBasicDetails($req_arr);
                    if (empty($UserBasicDetails)) {
                        $UserBasicDetails = $this->user->getMainUserBasicDetails($req_arr);
                        if (!empty($UserBasicDetails)) {
                            $UserBasicDetails['profile_picture_allowed']         = 'A';
                            $UserBasicDetails['f_name_allowed']                  = 'A';
                            $UserBasicDetails['m_name_allowed']                  = 'A';
                            $UserBasicDetails['l_name_allowed']                  = 'A';
                            $UserBasicDetails['admin_status_profile_name']       = 'A';
                            $UserBasicDetails['admin_message_profile_name']      = '';
                            $UserBasicDetails['residence_street1_allowed']       = 'A';
                            $UserBasicDetails['residence_street2_allowed']       = 'A';
                            $UserBasicDetails['residence_street3_allowed']       = 'A';
                            $UserBasicDetails['residence_zipcode_allowed']       = 'A';
                            $UserBasicDetails['admin_status_residence_address']  = 'A';
                            $UserBasicDetails['admin_message_residence_address'] = '';
                            $UserBasicDetails['permanent_street1_allowed']       = 'A';
                            $UserBasicDetails['permanent_street2_allowed']       = 'A';
                            $UserBasicDetails['permanent_street3_allowed']       = 'A';
                            $UserBasicDetails['permanent_zipcode_allowed']       = 'A';
                            $UserBasicDetails['admin_status_permanent_address']  = 'A';
                            $UserBasicDetails['admin_message_permanent_address'] = '';
                            $UserBasicDetails['admin_status_other_info']         = 'A';
                            $UserBasicDetails['admin_message_other_info']        = '';
                            $UserBasicDetails['utp_id']                          = null;
                        }
                    }
                    
                    if (!empty($UserBasicDetails) && count($UserBasicDetails) > 0) {
                        $UserBasicDetails['image_path'] = 'assets/img/user-default.svg';
                        if ($UserBasicDetails['profile_picture_file_extension'] != "") {
                            $profile_picture_file_url       = $this->config->item('bucket_url') . $req_arr['userId'] . '/profile/' . $req_arr['userId'] . '.' . $UserBasicDetails['profile_picture_file_extension'] . '?versionId=' . $UserBasicDetails['s3_media_version'];
                            $UserBasicDetails['image_path'] = $profile_picture_file_url;
                        }
                    }
                    $result_arr    = $UserBasicDetails;
                    $http_response = 'http_response_ok';
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
     * @ Function Name            : saveBasicEditInfo()
     * @ Added Date               : 13-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : save basic info of user
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 13-09-2016
     * @ Modified By              : Piyalee
     * 
     */
    public function saveBasicEditInfo_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } else {
            $req_arr = $details_arr = array();
            //pre($this->post(),1);
            $flag    = true;
            if (empty($this->post('pass_key', true))) {
                $flag          = false;
                $error_message = "Pass Key is required";
            } else {
                $req_arr['pass_key'] = $this->post('pass_key', true);
            }
            
            if (empty($this->post('admin_user_id', true))) {
                $flag          = false;
                $error_message = "Admin User Id is required";
            } else {
                $req_arr['admin_user_id'] = $this->post('admin_user_id', true);
            }
            
            if (empty($this->post('userId', true))) {
                $flag          = false;
                $error_message = "User Id is required";
            } else {
                $req_arr['userId'] = $this->post('userId', true);
            }
            $req_arr['profile_picture_allowed']    = $this->post('profile_picture_allowed', true);
            $req_arr['f_name_allowed']             = $this->post('f_name_allowed', true);
            $req_arr['m_name_allowed']             = $this->post('m_name_allowed', true);
            $req_arr['l_name_allowed']             = $this->post('l_name_allowed', true);
            $req_arr['admin_status_profile_name']  = $this->post('admin_status_profile_name', true);
            $req_arr['admin_message_profile_name'] = $this->post('admin_message_profile_name', true);
            
            $req_arr['residence_street1_allowed']       = $this->post('residence_street1_allowed', true);
            $req_arr['residence_street2_allowed']       = $this->post('residence_street2_allowed', true);
            $req_arr['residence_street3_allowed']       = $this->post('residence_street3_allowed', true);
            $req_arr['residence_zipcode_allowed']       = $this->post('residence_zipcode_allowed', true);
            //$req_arr['residence_phone_allowed']  = $this->post('residence_phone_allowed', true);
            $req_arr['admin_status_residence_address']  = $this->post('admin_status_residence_address', true);
            $req_arr['admin_message_residence_address'] = $this->post('admin_message_residence_address', true);
            
            $req_arr['permanent_street1_allowed']       = $this->post('permanent_street1_allowed', true);
            $req_arr['permanent_street2_allowed']       = $this->post('permanent_street2_allowed', true);
            $req_arr['permanent_street3_allowed']       = $this->post('permanent_street3_allowed', true);
            $req_arr['permanent_zipcode_allowed']       = $this->post('permanent_zipcode_allowed', true);
            //$req_arr['permanent_phone_allowed']  = $this->post('permanent_phone_allowed', true);
            $req_arr['admin_status_permanent_address']  = $this->post('admin_status_permanent_address', true);
            $req_arr['admin_message_permanent_address'] = $this->post('admin_message_permanent_address', true);
            
            $req_arr['admin_status_other_info']  = $this->post('admin_status_other_info', true);
            $req_arr['admin_message_other_info'] = $this->post('admin_message_other_info', true);
            $req_arr['utp_id']                   = $this->post('utp_id', true);
            $req_arr['display_name']             = $this->post('display_name', true);
            
            if ($flag) {
                $check_user       = array(
                    'pass_key' => $this->encrypt->decode($req_arr['pass_key']),
                    'admin_user_id' => $this->encrypt->decode($req_arr['admin_user_id'])
                );
                $checkloginstatus = $this->admin->checkSessionExist($check_user);
                if (!empty($checkloginstatus) && count($checkloginstatus) > 0) {
                    if ($req_arr['utp_id'] != 'null' || empty($req_arr['utp_id'])) {
                        if ($req_arr['admin_status_profile_name'] == 'A' && $req_arr['admin_status_residence_address'] == 'A' && $req_arr['admin_status_permanent_address'] == 'A' && $req_arr['admin_status_other_info'] == 'A') {
                            $checkDpName = $this->user->checkDisplayNameUnique($req_arr);
                            if (!empty($checkDpName)) {
                                $http_response = 'http_response_bad_request';
                                $error_message = "Can't approve, display name should unique";
                            } else {
                                $tempBasic = $this->user->fetchUserTempBasic($req_arr);
                                if (!empty($tempBasic)) {
                                    $whereMain     = array(
                                        'fk_user_id' => $req_arr['userId']
                                    );
                                    $approveTime   = date('Y-m-d H:i:s');
                                    $updateMainArr = array(
                                        //'id' => $tempBasic['id'],
                                        'fk_user_id' => $req_arr['userId'],
                                        'profile_picture_file_extension' => ($tempBasic['profile_picture_file_extension']) ? $tempBasic['profile_picture_file_extension'] : '',
                                        's3_media_version' => ($tempBasic['s3_media_version']) ? $tempBasic['s3_media_version'] : '',
                                        'display_name' => $tempBasic['display_name'],
                                        'f_name' => $tempBasic['f_name'],
                                        'm_name' => $tempBasic['m_name'],
                                        'l_name' => $tempBasic['l_name'],
                                        'residence_street1' => $tempBasic['residence_street1'],
                                        'residence_street2' => $tempBasic['residence_street2'],
                                        'residence_street3' => $tempBasic['residence_street3'],
                                        'residence_post_office' => $tempBasic['residence_post_office'],
                                        'residence_city' => $tempBasic['residence_city'],
                                        'residence_district' => $tempBasic['residence_district'],
                                        'residence_state' => $tempBasic['residence_state'],
                                        'residence_zipcode' => $tempBasic['residence_zipcode'],
                                        'residence_phone' => $tempBasic['residence_phone'],
                                        'permanent_street1' => $tempBasic['permanent_street1'],
                                        'permanent_street2' => $tempBasic['permanent_street2'],
                                        'permanent_street3' => $tempBasic['permanent_street3'],
                                        'permanent_post_office' => $tempBasic['permanent_post_office'],
                                        'permanent_city' => $tempBasic['permanent_city'],
                                        'permanent_district' => $tempBasic['permanent_district'],
                                        'permanent_state' => $tempBasic['permanent_state'],
                                        'permanent_zipcode' => $tempBasic['permanent_zipcode'],
                                        'permanent_phone' => $tempBasic['permanent_phone'],
                                        'fk_profession_type_id' => $tempBasic['fk_profession_type_id'],
                                        'date_of_birth' => $tempBasic['date_of_birth'],
                                        'fathers_name' => $tempBasic['fathers_name'],
                                        'fk_gender_id' => $tempBasic['fk_gender_id'],
                                        'fk_marital_status_id' => $tempBasic['fk_marital_status_id'],
                                        'fk_residence_status_id' => $tempBasic['fk_residence_status_id'],
                                        'addition_timestamp' => $tempBasic['profile_addition_timestamp'],
                                        'fk_admin_id' => $check_user['admin_user_id'],
                                        'approved_timestamp' => $approveTime
                                    );
                                    
                                    $checkMainBasic = $this->user->checkUserBasicInfo($req_arr);
                                    if (!empty($checkMainBasic)) {
                                        $updateMain = $this->user->updateBasicMain($updateMainArr, $whereMain);
                                    } else {
                                        $updateMain = $this->user->insertInBacisMain($updateMainArr);
                                    }
                                    //echo $updateMain; exit();
                                    if ($updateMain) {
                                        /** Add To History Basic Start **/
                                        $insertHistoryArr = $updateMainArr;
                                        unset($insertHistoryArr['id']);
                                        $insertHistoryArr['fk_user_id']         = $req_arr['userId'];
                                        $insertHistoryArr['replaced_timestamp'] = date('Y-m-d H:i:s');
                                        $histryId                               = $this->user->insertInHistory($insertHistoryArr);
                                        /** Add To History Basic End **/
                                        
                                        /** Assign to mPokket Account Start **/
                                        $checkAccount = $this->user->checkUserMpokket($req_arr);
                                        if (empty($checkAccount)) {
                                            $getAccount = $this->user->getAvailableMpokketAccount();
                                            if (!empty($getAccount)) {
                                                $insertMpokketArr = array(
                                                    'mpokket_account_number' => $getAccount['mpokket_account_number'],
                                                    'fk_user_id' => $req_arr['userId']
                                                );
                                                $insertMpokket    = $this->user->insertUserMpokket($insertMpokketArr);
                                                if ($insertMpokket) {
                                                    $whereMpokket = array(
                                                        'mpokket_account_number' => $getAccount['mpokket_account_number']
                                                    );
                                                    $upMpokketArr = array(
                                                        'status' => 'AL'
                                                    );
                                                    $update       = $this->user->updateMpokketAccount($upMpokketArr, $whereMpokket);
                                                }
                                            }
                                        }
                                        /** Assign to mPokket Account End **/
                                        if ($histryId) {
                                            /** Delete From temporary Basic **/
                                            $whereTemp = array(
                                                'id' => $req_arr['utp_id'],
                                                'fk_user_id' => $req_arr['userId']
                                            );
                                            $delete    = $this->user->deleteFromTempBasic($whereTemp);
                                        }
                                        $apprArr = array(
                                            'userId' => $req_arr['userId'],
                                            'admin_user_id' => $check_user['admin_user_id']
                                        );
                                        $this->allApproveUserDetails($apprArr);
                                    }
                                }
                                $http_response   = 'http_response_ok';
                                $success_message = 'Basic Info saved successfully';
                            }
                        } else {
                            $where           = array(
                                'fk_user_id' => $req_arr['userId'],
                                'id' => $req_arr['utp_id']
                            );
                            $upTempArr       = array(
                                'profile_picture_allowed' => ($req_arr['profile_picture_allowed'] == 'null') ? '1' : $req_arr['profile_picture_allowed'],
                                'f_name_allowed' => ($req_arr['f_name_allowed'] == 'null') ? '1' : $req_arr['f_name_allowed'],
                                'm_name_allowed' => ($req_arr['m_name_allowed'] == 'null') ? '1' : $req_arr['m_name_allowed'],
                                'l_name_allowed' => ($req_arr['l_name_allowed'] == 'null') ? '1' : $req_arr['l_name_allowed'],
                                'admin_status_profile_name' => $req_arr['admin_status_profile_name'],
                                'admin_message_profile_name' => $req_arr['admin_message_profile_name'],
                                'residence_street1_allowed' => ($req_arr['residence_street1_allowed'] == 'null') ? '1' : $req_arr['residence_street1_allowed'],
                                'residence_street2_allowed' => ($req_arr['residence_street2_allowed'] == 'null') ? '1' : $req_arr['residence_street2_allowed'],
                                'residence_street3_allowed' => ($req_arr['residence_street3_allowed'] == 'null') ? '1' : $req_arr['residence_street3_allowed'],
                                'residence_zipcode_allowed' => ($req_arr['residence_zipcode_allowed'] == 'null') ? '1' : $req_arr['residence_zipcode_allowed'],
                                //'residence_phone_allowed'   => $req_arr['residence_phone_allowed'],
                                'admin_status_residence_address' => $req_arr['admin_status_residence_address'],
                                'admin_message_residence_address' => $req_arr['admin_message_residence_address'],
                                'permanent_street1_allowed' => ($req_arr['permanent_street1_allowed'] == 'null') ? '1' : $req_arr['permanent_street1_allowed'],
                                'permanent_street2_allowed' => ($req_arr['permanent_street2_allowed'] == 'null') ? '1' : $req_arr['permanent_street2_allowed'],
                                'permanent_street3_allowed' => ($req_arr['permanent_street3_allowed'] == 'null') ? '1' : $req_arr['permanent_street3_allowed'],
                                'permanent_zipcode_allowed' => ($req_arr['permanent_zipcode_allowed'] == 'null') ? '1' : $req_arr['permanent_zipcode_allowed'],
                                //'permanent_phone_allowed'   => $req_arr['permanent_phone_allowed'],
                                'admin_status_permanent_address' => $req_arr['admin_status_permanent_address'],
                                'admin_message_permanent_address' => $req_arr['admin_message_permanent_address'],
                                'admin_status_other_info' => $req_arr['admin_status_other_info'],
                                'admin_message_other_info' => $req_arr['admin_message_other_info']
                            );
                            //print_r($upTempArr); exit();
                            $update          = $this->user->updateBasicTempInfo($upTempArr, $where);
                            $http_response   = 'http_response_ok';
                            $success_message = 'Basic Info saved successfully';
                        }
                    } else {
                        $apprArr = array(
                            'userId' => $req_arr['userId'],
                            'admin_user_id' => $check_user['admin_user_id']
                        );
                        $this->allApproveUserDetails($apprArr);
                        $http_response   = 'http_response_ok';
                        $success_message = 'Basic Info saved successfully';
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
     * @ Function Name            : getUserEducationLists()
     * @ Added Date               : 15-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : get all user Education
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 15-09-2016
     * @ Modified By              : Piyalee
     * 
     */
    public function getUserEducationLists_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } else {
            $req_arr = $details_arr = array();
            //pre($this->post(),1);
            $flag    = true;
            if (empty($this->post('pass_key', true))) {
                $flag          = false;
                $error_message = "Pass Key is required";
            } else {
                $req_arr['pass_key'] = $this->post('pass_key', true);
            }
            
            if (empty($this->post('admin_user_id', true))) {
                $flag          = false;
                $error_message = "Admin User Id is required";
            } else {
                $req_arr['admin_user_id'] = $this->post('admin_user_id', true);
            }
            
            if (empty($this->post('userId', true))) {
                $flag          = false;
                $error_message = "User Id is required";
            } else {
                $req_arr['userId'] = $this->post('userId', true);
            }
            
            $req_arr['order']    = $this->input->post('order', true);
            $req_arr['order_by'] = $this->input->post('order_by', true);
            
            if ($flag) {
                $check_user       = array(
                    'pass_key' => $this->encrypt->decode($req_arr['pass_key']),
                    'admin_user_id' => $this->encrypt->decode($req_arr['admin_user_id'])
                );
                $checkloginstatus = $this->admin->checkSessionExist($check_user);
                if (!empty($checkloginstatus) && count($checkloginstatus) > 0) {
                    $checkTempEdu       = $this->user->checkTempEducation($req_arr['userId']);
                    $tempEducationLists = array();
                    $mainEduIds         = '';
                    if (!empty($checkTempEdu) && count($checkTempEdu)) {
                        $whereTemp          = array(
                            'userId' => $req_arr['userId']
                        );
                        $tempEducationLists = $this->user->getEducationTempLists($whereTemp);
                        if (!empty($tempEducationLists) && count($tempEducationLists) > 0) {
                            foreach ($tempEducationLists as $key => $tempEdu) {
                                if ($tempEdu['fk_profile_education_id']) {
                                    $mainEduIds .= ($mainEduIds == '') ? $tempEdu['fk_profile_education_id'] : ',' . $tempEdu['fk_profile_education_id'];
                                }
                            }
                        }
                    }
                    $req_arr['mainIds'] = $mainEduIds;
                    $UserEducationLists = $this->user->getEducationMainLists($req_arr);
                    
                    if (!empty($UserEducationLists) && count($UserEducationLists) > 0) {
                        foreach ($UserEducationLists as $key => $education) {
                            $UserEducationLists[$key]['education_status']          = 'A';
                            $UserEducationLists[$key]['admin_message_education']   = null;
                            $UserEducationDetails[$key]['fk_profile_education_id'] = null;
                        }
                    }
                    
                    $userTotalEducation     = array_merge($UserEducationLists, $tempEducationLists);
                    $details_arr['dataset'] = $userTotalEducation;
                    $result_arr             = $details_arr;
                    $http_response          = 'http_response_ok';
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
     * @ Function Name            : getUserEducationDetails()
     * @ Added Date               : 16-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : get user Education details
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 16-09-2016
     * @ Modified By              : Piyalee
     * 
     */
    public function getUserEducationDetails_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } else {
            $req_arr = $details_arr = array();
            //pre($this->post(),1);
            $flag    = true;
            if (empty($this->post('pass_key', true))) {
                $flag          = false;
                $error_message = "Pass Key is required";
            } else {
                $req_arr['pass_key'] = $this->post('pass_key', true);
            }
            
            if (empty($this->post('admin_user_id', true))) {
                $flag          = false;
                $error_message = "Admin User Id is required";
            } else {
                $req_arr['admin_user_id'] = $this->post('admin_user_id', true);
            }
            
            if (empty($this->post('userId', true))) {
                $flag          = false;
                $error_message = "User Id is required";
            } else {
                $req_arr['userId'] = $this->post('userId', true);
            }
            
            if (empty($this->post('educationId', true))) {
                $flag          = false;
                $error_message = "Education Id is required";
            } else {
                $req_arr['educationId'] = $this->post('educationId', true);
            }
            
            if (empty($this->post('eduStatus', true))) {
                $req_arr['eduStatus'] = 'A';
            } else {
                $req_arr['eduStatus'] = $this->post('eduStatus', true);
            }
            
            if ($flag) {
                $check_user       = array(
                    'pass_key' => $this->encrypt->decode($req_arr['pass_key']),
                    'admin_user_id' => $this->encrypt->decode($req_arr['admin_user_id'])
                );
                $checkloginstatus = $this->admin->checkSessionExist($check_user);
                if (!empty($checkloginstatus) && count($checkloginstatus) > 0) {
                    if ($req_arr['eduStatus'] == 'A') {
                        $UserEducationDetails = $this->user->getEducationMainDetails($req_arr);
                        if (!empty($UserEducationDetails) && count($UserEducationDetails) > 0) {
                            $image_url = '';
                            if ($UserEducationDetails['is_file_uploaded'] != 0) {
                                //$image_url = $this->config->item('bucket_url') . $req_arr['userId'] . '/education/' . $UserEducationDetails['id'] . '.' . $UserEducationDetails['file_name'] . '?versionId=' . $UserEducationDetails['s3_media_version'];
                                $image_url = $this->config->item('bucket_url') . $req_arr['userId'] . '/education/' . $UserEducationDetails['file_name'] . '?versionId=' . $UserEducationDetails['s3_media_version'];
                            }
                            $UserEducationDetails['image_path']              = $image_url;
                            $UserEducationDetails['education_status']        = 'A';
                            $UserEducationDetails['admin_message_education'] = '';
                        }
                    } else {
                        $UserEducationDetails = $this->user->getEducationTempDetails($req_arr);
                    }
                    if (!empty($UserEducationDetails) && count($UserEducationDetails) > 0) {
                        $image_url = '';
                        if ($UserEducationDetails['is_file_uploaded'] != 0) {
                            //$image_url = $this->config->item('bucket_url') . $req_arr['userId'] . '/education/' . $UserEducationDetails['id'] . '.' . $UserEducationDetails['file_name'] . '?versionId=' . $UserEducationDetails['s3_media_version'];
                            $image_url = $this->config->item('bucket_url') . $req_arr['userId'] . '/education/' . $UserEducationDetails['file_name'] . '?versionId=' . $UserEducationDetails['s3_media_version'];
                        }
                        $UserEducationDetails['image_path'] = $image_url;
                    }
                    $details_arr['dataset'] = $UserEducationDetails;
                    $result_arr             = $details_arr;
                    $http_response          = 'http_response_ok';
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
     * ---------------------------------------------------------
     * @ Function Name            : saveEducationInfo()
     * @ Added Date               : 16-09-2016
     * @ Added By                 : Piyalee
     * ---------------------------------------------------------
     * @ Description              : save Education details
     * ---------------------------------------------------------
     * @ return                   : array
     * ---------------------------------------------------------
     * @ Modified Date            : 16-09-2016
     * @ Modified By              : Piyalee
     * 
     */
    public function saveEducationInfo_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } else {
            $req_arr = $details_arr = array();
            //pre($this->post(),1);
            $flag    = true;
            if (empty($this->post('pass_key', true))) {
                $flag          = false;
                $error_message = "Pass Key is required";
            } else {
                $req_arr['pass_key'] = $this->post('pass_key', true);
            }
            
            if (empty($this->post('admin_user_id', true))) {
                $flag          = false;
                $error_message = "Admin User Id is required";
            } else {
                $req_arr['admin_user_id'] = $this->post('admin_user_id', true);
            }
            
            if (empty($this->post('userId', true))) {
                $flag          = false;
                $error_message = "User Id is required";
            } else {
                $req_arr['userId'] = $this->post('userId', true);
            }
            
            if (empty($this->post('educationId', true))) {
                $flag          = false;
                $error_message = "Education Id is required";
            } else {
                $req_arr['educationId'] = $this->post('educationId', true);
            }
            $req_arr['fk_profile_education_id'] = $this->post('fk_profile_education_id', true);
            $req_arr['education_status']        = $this->post('education_status', true);
            $req_arr['Neweducation_status']     = $this->post('Neweducation_status', true);
            $req_arr['admin_message_education'] = $this->post('admin_message_education', true);
            //print_r($this->post()); exit();
            if ($flag) {
                $check_user       = array(
                    'pass_key' => $this->encrypt->decode($req_arr['pass_key']),
                    'admin_user_id' => $this->encrypt->decode($req_arr['admin_user_id'])
                );
                $checkloginstatus = $this->admin->checkSessionExist($check_user);
                if (!empty($checkloginstatus) && count($checkloginstatus) > 0) {
                    if ($req_arr['education_status'] != 'A') {
                        if ($req_arr['Neweducation_status'] == 'A') {
                            $UserEducationDetails = $this->user->getEducationTempDetails($req_arr);
                            if (!empty($UserEducationDetails)) {
                                $approveDate   = date('Y-m-d H:i:s');
                                $percentage    = ($UserEducationDetails['marks'] == 0) ? 0 : (($UserEducationDetails['marks'] / $UserEducationDetails['out_of_range']) * 100);
                                $updateMainArr = array(
                                    //'id' => $UserEducationDetails['id'],
                                    'fk_user_id' => $UserEducationDetails['fk_user_id'],
                                    'fk_degree_type_id' => $UserEducationDetails['fk_degree_type_id'],
                                    'fk_degree_id' => $UserEducationDetails['fk_degree_id'],
                                    'fk_field_of_study_id' => $UserEducationDetails['fk_field_of_study_id'],
                                    'year_of_joining' => $UserEducationDetails['year_of_joining'],
                                    'year_of_graduation' => $UserEducationDetails['year_of_graduation'],
                                    'show_in_profile' => $UserEducationDetails['show_in_profile'],
                                    'name_of_institution' => $UserEducationDetails['name_of_institution'],
                                    'fk_pincode_id' => $UserEducationDetails['fk_pincode_id'],
                                    'work_status' => $UserEducationDetails['work_status'],
                                    'grades_marks' => $percentage,
                                    'marks' => $UserEducationDetails['marks'],
                                    'out_of_range' => $UserEducationDetails['out_of_range'],
                                    'is_file_uploaded' => $UserEducationDetails['is_file_uploaded'],
                                    'file_name' => $UserEducationDetails['file_name'],
                                    's3_media_version' => $UserEducationDetails['s3_media_version'],
                                    'addition_datetime' => $UserEducationDetails['education_addition_datetime'],
                                    'fk_admin_id' => $check_user['admin_user_id'],
                                    'approved_timestamp' => $approveDate
                                );
                                if ($req_arr['fk_profile_education_id'] == 'null' || $req_arr['fk_profile_education_id'] == '') {
                                    $changeEdu = $this->user->insertMainEducation($updateMainArr);
                                } else {
                                    $where     = array(
                                        'id' => $UserEducationDetails['fk_profile_education_id']
                                    );
                                    $changeEdu = $this->user->updateMainEducation($updateMainArr, $where);
                                }
                                if ($changeEdu) {
                                    $insHstryArr = $updateMainArr;
                                    unset($insHstryArr['id']);
                                    $insHstryArr['approved_timestamp'] = $approveDate;
                                    $insHstryArr['replaced_timestamp'] = date("Y-m-d H:i:s");
                                    $insHiostry                        = $this->user->insertHistoryEducation($insHstryArr);
                                    if ($insHiostry) {
                                        $whereDelTmp = array(
                                            'id' => $UserEducationDetails['id']
                                        );
                                        $delete      = $this->user->deleteFromTempEducation($whereDelTmp);
                                    }
                                    
                                    $apprArr = array(
                                        'userId' => $req_arr['userId'],
                                        'admin_user_id' => $check_user['admin_user_id']
                                    );
                                    $this->allApproveUserDetails($apprArr);
                                }
                            }
                        } else {
                            $updateTempArr = array(
                                'education_status' => $req_arr['Neweducation_status'],
                                'admin_message_education' => (($req_arr['admin_message_education'] == 'null') ? NULL : $req_arr['admin_message_education'])
                            );
                            $whereTemp     = array(
                                'id' => $req_arr['educationId']
                            );
                            $update        = $this->user->updateTempEducation($updateTempArr, $whereTemp);
                        }
                        
                        $this->editShowIn_profileEdu($req_arr['userId']);
                    }
                    
                    $success_message = "Education details saved successfully";
                    $http_response   = 'http_response_ok';
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
    
    
    
    
    public function editShowIn_profileEdu($userId)
    {
        
        $details_arr       = $update_export = array();
        //$userId=2;
        $details_arr['id'] = $this->user->getAllEducationId($userId);
        $getShowinId       = $this->user->getShowInId($userId);
        
        foreach ($details_arr['id'] as $key => $value) {
            if ($value['id'] != $getShowinId['id']) {
                $Pending_data                    = array();
                $Pending_data['id']              = $value['id'];
                $Pending_data['show_in_profile'] = '0';
                
                $update_export[] = $Pending_data;
            } else {
                $Pending_data                    = array();
                $Pending_data['id']              = $getShowinId['id'];
                $Pending_data['show_in_profile'] = '1';
                
                $update_export[] = $Pending_data;
            }
        }
        $update = $this->user->batchUpdateEducationShow($update_export, 'id');
    }
    
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getUserKycLists()
     * @ Added Date               : 19-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : get all user Kyc
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 19-09-2016
     * @ Modified By              : Piyalee
     * 
     */
    public function getUserKycLists_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } else {
            $req_arr = $details_arr = array();
            //pre($this->post(),1);
            $flag    = true;
            if (empty($this->post('pass_key', true))) {
                $flag          = false;
                $error_message = "Pass Key is required";
            } else {
                $req_arr['pass_key'] = $this->post('pass_key', true);
            }
            
            if (empty($this->post('admin_user_id', true))) {
                $flag          = false;
                $error_message = "Admin User Id is required";
            } else {
                $req_arr['admin_user_id'] = $this->post('admin_user_id', true);
            }
            
            if (empty($this->post('userId', true))) {
                $flag          = false;
                $error_message = "User Id is required";
            } else {
                $req_arr['userId'] = $this->post('userId', true);
            }
            $req_arr['order']    = $this->input->post('order', true);
            $req_arr['order_by'] = $this->input->post('order_by', true);
            
            if ($flag) {
                $check_user       = array(
                    'pass_key' => $this->encrypt->decode($req_arr['pass_key']),
                    'admin_user_id' => $this->encrypt->decode($req_arr['admin_user_id'])
                );
                $checkloginstatus = $this->admin->checkSessionExist($check_user);
                if (!empty($checkloginstatus) && count($checkloginstatus) > 0) {
                    $mainKycIds   = '';
                    $tempKycLists = $this->user->getKycTempLists($req_arr);
                    /*echo $this->db->last_query();
                    print_r($tempKycLists); exit();*/
                    if (!empty($tempKycLists) && count($tempKycLists) > 0) {
                        foreach ($tempKycLists as $key => $tempKyc) {
                            if (!empty($tempKyc['fk_profile_kyc_id'])) {
                                $mainKycIds .= ($mainKycIds == '') ? $tempKyc['fk_profile_kyc_id'] : ',' . $tempKyc['fk_profile_kyc_id'];
                            }
                        }
                    }
                    
                    $req_arr['mainIds'] = $mainKycIds;
                    $UserKycLists       = $this->user->getKycMainLists($req_arr);
                    
                    if (!empty($UserKycLists) && count($UserKycLists) > 0) {
                        foreach ($UserKycLists as $key => $kyc) {
                            $UserKycLists[$key]['kyc_status']          = 'A';
                            $UserKycLists[$key]['admin_message_kyc']   = null;
                            $UserKycDetails[$key]['fk_profile_kyc_id'] = null;
                        }
                    }
                    
                    $userTotalKyc           = array_merge($UserKycLists, $tempKycLists);
                    $details_arr['dataset'] = $userTotalKyc;
                    $result_arr             = $details_arr;
                    $http_response          = 'http_response_ok';
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
     * @ Function Name            : getUserKycDetails()
     * @ Added Date               : 19-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : get user KYC details
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 19-09-2016
     * @ Modified By              : Piyalee
     * 
     */
    public function getUserKycDetails_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } else {
            $req_arr = $details_arr = array();
            //pre($this->post(),1);
            $flag    = true;
            if (empty($this->post('pass_key', true))) {
                $flag          = false;
                $error_message = "Pass Key is required";
            } else {
                $req_arr['pass_key'] = $this->post('pass_key', true);
            }
            
            if (empty($this->post('admin_user_id', true))) {
                $flag          = false;
                $error_message = "Admin User Id is required";
            } else {
                $req_arr['admin_user_id'] = $this->post('admin_user_id', true);
            }
            
            if (empty($this->post('userId', true))) {
                $flag          = false;
                $error_message = "User Id is required";
            } else {
                $req_arr['userId'] = $this->post('userId', true);
            }
            
            if (empty($this->post('kycId', true))) {
                $flag          = false;
                $error_message = "KYC Id is required";
            } else {
                $req_arr['kycId'] = $this->post('kycId', true);
            }
            
            if (empty($this->post('kycStatus', true))) {
                $req_arr['kycStatus'] = 'A';
            } else {
                $req_arr['kycStatus'] = $this->post('kycStatus', true);
            }
            
            if ($flag) {
                $check_user       = array(
                    'pass_key' => $this->encrypt->decode($req_arr['pass_key']),
                    'admin_user_id' => $this->encrypt->decode($req_arr['admin_user_id'])
                );
                $checkloginstatus = $this->admin->checkSessionExist($check_user);
                if (!empty($checkloginstatus) && count($checkloginstatus) > 0) {
                    $UserKycDetails = array();
                    if ($req_arr['kycStatus'] == 'A') {
                        $UserKycDetails = $this->user->getKycMainDetails($req_arr);
                        if (!empty($UserKycDetails) && count($UserKycDetails) > 0) {
                            $UserKycDetails['kyc_status']        = 'A';
                            $UserKycDetails['admin_message_kyc'] = '';
                            $UserKycDetails['fk_profile_kyc_id'] = "";
                        }
                    } else {
                        $UserKycDetails = $this->user->getKycTempDetails($req_arr);
                    }
                    if (!empty($UserKycDetails) && count($UserKycDetails) > 0) {
                        $front_image_url = '';
                        if ($UserKycDetails['front_file_name']) {
                            //$front_image_url = $this->config->item('bucket_url') . $req_arr['userId'] . '/kyc/' . $UserKycDetails['id'] . '_front.' . $UserKycDetails['front_file_name'] . '?versionId=' . $UserKycDetails['front_s3_media_version'];
                            $front_image_url = $this->config->item('bucket_url') . $req_arr['userId'] . '/kyc/' . $UserKycDetails['front_file_name'] . '?versionId=' . $UserKycDetails['front_s3_media_version'];
                        }
                        $UserKycDetails['front_image_path'] = $front_image_url;
                        
                        $back_image_url = '';
                        if ($UserKycDetails['back_file_name']) {
                            //$back_image_url = $this->config->item('bucket_url') . $req_arr['userId'] . '/kyc/' . $UserKycDetails['id'] . '_back.' . $UserKycDetails['back_file_name'] . '?versionId=' . $UserKycDetails['back_s3_media_version'];
                            $back_image_url = $this->config->item('bucket_url') . $req_arr['userId'] . '/kyc/' . $UserKycDetails['back_file_name'] . '?versionId=' . $UserKycDetails['back_s3_media_version'];
                        }
                        $UserKycDetails['back_image_path'] = $back_image_url;
                    }
                    $details_arr['dataset'] = $UserKycDetails;
                    $result_arr             = $details_arr;
                    $http_response          = 'http_response_ok';
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
     * ---------------------------------------------------------
     * @ Function Name            : saveKycInfo()
     * @ Added Date               : 20-09-2016
     * @ Added By                 : Piyalee
     * ---------------------------------------------------------
     * @ Description              : save Education details
     * ---------------------------------------------------------
     * @ return                   : array
     * ---------------------------------------------------------
     * @ Modified Date            : 20-09-2016
     * @ Modified By              : Piyalee
     * 
     */
    public function saveKycInfo_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } else {
            $req_arr = $details_arr = array();
            //pre($this->post(),1);
            $flag    = true;
            if (empty($this->post('pass_key', true))) {
                $flag          = false;
                $error_message = "Pass Key is required";
            } else {
                $req_arr['pass_key'] = $this->post('pass_key', true);
            }
            
            if (empty($this->post('admin_user_id', true))) {
                $flag          = false;
                $error_message = "Admin User Id is required";
            } else {
                $req_arr['admin_user_id'] = $this->post('admin_user_id', true);
            }
            
            if (empty($this->post('userId', true))) {
                $flag          = false;
                $error_message = "User Id is required";
            } else {
                $req_arr['userId'] = $this->post('userId', true);
            }
            
            if (empty($this->post('kycId', true))) {
                $flag          = false;
                $error_message = "KYC Id is required";
            } else {
                $req_arr['kycId'] = $this->post('kycId', true);
            }
            $req_arr['fk_profile_kyc_id'] = $this->post('fk_profile_kyc_id', true);
            $req_arr['kyc_status']        = $this->post('kyc_status', true);
            $req_arr['Newkyc_status']     = $this->post('Newkyc_status', true);
            $req_arr['admin_message_kyc'] = $this->post('admin_message_kyc', true);
            //print_r($this->post()); exit();
            if ($flag) {
                $check_user       = array(
                    'pass_key' => $this->encrypt->decode($req_arr['pass_key']),
                    'admin_user_id' => $this->encrypt->decode($req_arr['admin_user_id'])
                );
                $checkloginstatus = $this->admin->checkSessionExist($check_user);
                if (!empty($checkloginstatus) && count($checkloginstatus) > 0) {
                    if ($req_arr['kyc_status'] != 'A') {
                        if ($req_arr['Newkyc_status'] == 'A') {
                            $UserKycDetails = $this->user->getKycTempDetails($req_arr);
                            if (!empty($UserKycDetails)) {
                                $approveDate   = date('Y-m-d H:i:s');
                                $updateMainArr = array(
                                    //'id' => $UserKycDetails['id'],
                                    'fk_user_id' => $UserKycDetails['fk_user_id'],
                                    'fk_kyc_template_id' => $UserKycDetails['fk_kyc_template_id'],
                                    'kyc_data' => $UserKycDetails['kyc_data'],
                                    'front_file_name' => (($UserKycDetails['front_file_name']) ? $UserKycDetails['front_file_name'] : ''),
                                    'front_s3_media_version' => (($UserKycDetails['front_s3_media_version']) ? $UserKycDetails['front_s3_media_version'] : ''),
                                    'back_file_name' => (($UserKycDetails['back_file_name']) ? $UserKycDetails['back_file_name'] : ''),
                                    'back_s3_media_version' => (($UserKycDetails['back_s3_media_version']) ? $UserKycDetails['back_s3_media_version'] : ''),
                                    'addition_datetime' => $UserKycDetails['kyc_addition_datetime'],
                                    'fk_admin_id' => $check_user['admin_user_id'],
                                    'approved_timestamp' => $approveDate,
                                    'is_active' => 1
                                );
                                //print_r($updateMainArr); exit();
                                if ($req_arr['fk_profile_kyc_id'] == 'null' || $req_arr['fk_profile_kyc_id'] == '') {
                                    $checkKyc      = array(
                                        'fk_user_id' => $UserKycDetails['fk_user_id'],
                                        'fk_kyc_template_id' => $UserKycDetails['fk_kyc_template_id']
                                    );
                                    $checkExistKyc = $this->user->checkTEmpleteKycUnique($checkKyc);
                                    /*echo $this->db->last_query();
                                    print_r($checkExistKyc); exit();*/
                                    if (!empty($checkExistKyc)) {
                                        $where     = array(
                                            'id' => $checkExistKyc['id']
                                        );
                                        $changeKyc = $this->user->updateMainKyc($updateMainArr, $where);
                                    } else {
                                        $changeKyc = $this->user->insertMainKyc($updateMainArr);
                                    }
                                } else {
                                    $where     = array(
                                        'id' => $UserKycDetails['fk_profile_kyc_id']
                                    );
                                    $changeKyc = $this->user->updateMainKyc($updateMainArr, $where);
                                }
                                if ($changeKyc) {
                                    $insHstryArr = $updateMainArr;
                                    unset($insHstryArr['id']);
                                    unset($insHstryArr['is_active']);
                                    $insHstryArr['approved_timestamp'] = $approveDate;
                                    $insHstryArr['replaced_timestamp'] = date("Y-m-d H:i:s");
                                    $insHiostry                        = $this->user->insertHistoryKyc($insHstryArr);
                                    if ($insHiostry) {
                                        $whereDelTmp = array(
                                            'id' => $UserKycDetails['id']
                                        );
                                        $delete      = $this->user->deleteFromTempKyc($whereDelTmp);
                                    }
                                    
                                    $apprArr = array(
                                        'userId' => $req_arr['userId'],
                                        'admin_user_id' => $check_user['admin_user_id']
                                    );
                                    $this->allApproveUserDetails($apprArr);
                                }
                            }
                        } else {
                            $updateTempArr = array(
                                'kyc_status' => $req_arr['Newkyc_status'],
                                'admin_message_kyc' => (($req_arr['admin_message_kyc'] == 'null') ? NULL : $req_arr['admin_message_kyc'])
                            );
                            $whereTemp     = array(
                                'id' => $req_arr['kycId']
                            );
                            $update        = $this->user->updateTempKyc($updateTempArr, $whereTemp);
                        }
                    }
                    $success_message = "Kyc details saved successfully";
                    $http_response   = 'http_response_ok';
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
     * @ Function Name            : getUserBankLists()
     * @ Added Date               : 19-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : get all user Bank
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 19-09-2016
     * @ Modified By              : Piyalee
     * 
     */
    public function getUserBankLists_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } else {
            $req_arr = $details_arr = array();
            //pre($this->post(),1);
            $flag    = true;
            if (empty($this->post('pass_key', true))) {
                $flag          = false;
                $error_message = "Pass Key is required";
            } else {
                $req_arr['pass_key'] = $this->post('pass_key', true);
            }
            
            if (empty($this->post('admin_user_id', true))) {
                $flag          = false;
                $error_message = "Admin User Id is required";
            } else {
                $req_arr['admin_user_id'] = $this->post('admin_user_id', true);
            }
            
            if (empty($this->post('userId', true))) {
                $flag          = false;
                $error_message = "User Id is required";
            } else {
                $req_arr['userId'] = $this->post('userId', true);
            }
            $req_arr['order']    = $this->input->post('order', true);
            $req_arr['order_by'] = $this->input->post('order_by', true);
            
            if ($flag) {
                $check_user       = array(
                    'pass_key' => $this->encrypt->decode($req_arr['pass_key']),
                    'admin_user_id' => $this->encrypt->decode($req_arr['admin_user_id'])
                );
                $checkloginstatus = $this->admin->checkSessionExist($check_user);
                if (!empty($checkloginstatus) && count($checkloginstatus) > 0) {
                    $mainBankIds   = '';
                    $tempBankLists = $this->user->getBankTempLists($req_arr);
                    /*echo $this->db->last_query();
                    print_r($tempBankLists); exit();*/
                    if (!empty($tempBankLists) && count($tempBankLists) > 0) {
                        foreach ($tempBankLists as $key => $tempBank) {
                            if ($tempBank['fk_profile_bank_id']) {
                                $mainBankIds .= ($mainBankIds == '') ? $tempBank['fk_profile_bank_id'] : ',' . $tempBank['fk_profile_bank_id'];
                            }
                        }
                    }
                    
                    $req_arr['mainIds'] = $mainBankIds;
                    $UserBankLists      = $this->user->getBankMainLists($req_arr);
                    
                    if (!empty($UserBankLists) && count($UserBankLists) > 0) {
                        foreach ($UserBankLists as $key => $bank) {
                            $UserBankLists[$key]['bank_status']          = 'A';
                            $UserBankLists[$key]['admin_message_bank']   = null;
                            $UserBankDetails[$key]['fk_profile_bank_id'] = null;
                        }
                    }
                    
                    $userTotalBank          = array_merge($UserBankLists, $tempBankLists);
                    $details_arr['dataset'] = $userTotalBank;
                    $result_arr             = $details_arr;
                    $http_response          = 'http_response_ok';
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
     * @ Function Name            : getUserBankDetails()
     * @ Added Date               : 19-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : get user Bank details
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 19-09-2016
     * @ Modified By              : Piyalee
     * 
     */
    public function getUserBankDetails_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } else {
            $req_arr = $details_arr = array();
            //pre($this->post(),1);
            $flag    = true;
            if (empty($this->post('pass_key', true))) {
                $flag          = false;
                $error_message = "Pass Key is required";
            } else {
                $req_arr['pass_key'] = $this->post('pass_key', true);
            }
            
            if (empty($this->post('admin_user_id', true))) {
                $flag          = false;
                $error_message = "Admin User Id is required";
            } else {
                $req_arr['admin_user_id'] = $this->post('admin_user_id', true);
            }
            
            if (empty($this->post('userId', true))) {
                $flag          = false;
                $error_message = "User Id is required";
            } else {
                $req_arr['userId'] = $this->post('userId', true);
            }
            
            if (empty($this->post('bankId', true))) {
                $flag          = false;
                $error_message = "Bank Id is required";
            } else {
                $req_arr['bankId'] = $this->post('bankId', true);
            }
            
            if (empty($this->post('bankStatus', true))) {
                $req_arr['bankStatus'] = 'A';
            } else {
                $req_arr['bankStatus'] = $this->post('bankStatus', true);
            }
            
            if ($flag) {
                $check_user       = array(
                    'pass_key' => $this->encrypt->decode($req_arr['pass_key']),
                    'admin_user_id' => $this->encrypt->decode($req_arr['admin_user_id'])
                );
                $checkloginstatus = $this->admin->checkSessionExist($check_user);
                if (!empty($checkloginstatus) && count($checkloginstatus) > 0) {
                    $UserBankDetails = array();
                    if ($req_arr['bankStatus'] == 'A') {
                        $UserBankDetails = $this->user->getBankMainDetails($req_arr);
                        if (!empty($UserBankDetails) && count($UserBankDetails) > 0) {
                            $UserBankDetails['bank_status']        = 'A';
                            $UserBankDetails['admin_message_bank'] = '';
                            $UserBankDetails['fk_profile_bank_id'] = "";
                        }
                    } else {
                        $UserBankDetails = $this->user->getBankTempDetails($req_arr);
                    }
                    $details_arr['dataset'] = $UserBankDetails;
                    $result_arr             = $details_arr;
                    $http_response          = 'http_response_ok';
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
     * ---------------------------------------------------------
     * @ Function Name            : saveBankInfo()
     * @ Added Date               : 20-09-2016
     * @ Added By                 : Piyalee
     * ---------------------------------------------------------
     * @ Description              : save Education details
     * ---------------------------------------------------------
     * @ return                   : array
     * ---------------------------------------------------------
     * @ Modified Date            : 20-09-2016
     * @ Modified By              : Piyalee
     * 
     */
    public function saveBankInfo_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } else {
            $req_arr = $details_arr = array();
            //pre($this->post(),1);
            $flag    = true;
            if (empty($this->post('pass_key', true))) {
                $flag          = false;
                $error_message = "Pass Key is required";
            } else {
                $req_arr['pass_key'] = $this->post('pass_key', true);
            }
            
            if (empty($this->post('admin_user_id', true))) {
                $flag          = false;
                $error_message = "Admin User Id is required";
            } else {
                $req_arr['admin_user_id'] = $this->post('admin_user_id', true);
            }
            
            if (empty($this->post('userId', true))) {
                $flag          = false;
                $error_message = "User Id is required";
            } else {
                $req_arr['userId'] = $this->post('userId', true);
            }
            
            if (empty($this->post('bankId', true))) {
                $flag          = false;
                $error_message = "Bank Id is required";
            } else {
                $req_arr['bankId'] = $this->post('bankId', true);
            }
            $req_arr['fk_profile_bank_id'] = $this->post('fk_profile_bank_id', true);
            $req_arr['bank_status']        = $this->post('bank_status', true);
            $req_arr['Newbank_status']     = $this->post('Newbank_status', true);
            $req_arr['admin_message_bank'] = $this->post('admin_message_bank', true);
            //print_r($this->post()); exit();
            if ($flag) {
                $check_user       = array(
                    'pass_key' => $this->encrypt->decode($req_arr['pass_key']),
                    'admin_user_id' => $this->encrypt->decode($req_arr['admin_user_id'])
                );
                $checkloginstatus = $this->admin->checkSessionExist($check_user);
                if (!empty($checkloginstatus) && count($checkloginstatus) > 0) {
                    if ($req_arr['bank_status'] != 'A') {
                        if ($req_arr['Newbank_status'] == 'A') {
                            $UserBankDetails = $this->user->getBankTempDetails($req_arr);
                            if (!empty($UserBankDetails)) {
                                $approveDate   = date('Y-m-d H:i:s');
                                $updateMainArr = array(
                                    //'id' => $UserBankDetails['id'],
                                    'fk_user_id' => $UserBankDetails['fk_user_id'],
                                    'fk_bank_id' => $UserBankDetails['fk_bank_id'],
                                    'account_number' => $UserBankDetails['account_number'],
                                    'addition_datetime' => $UserBankDetails['addition_datetime'],
                                    'fk_admin_id' => $check_user['admin_user_id'],
                                    'is_primary' => $UserBankDetails['is_primary'],
                                    
                                    'approved_timestamp' => $approveDate
                                );
                                //print_r($updateMainArr); exit();
                                if ($req_arr['fk_profile_bank_id'] == 'null' || $req_arr['fk_profile_bank_id'] == '') {
                                    $checkBank      = array(
                                        'fk_user_id' => $UserBankDetails['fk_user_id'],
                                        'fk_bank_id' => $UserBankDetails['fk_bank_id'],
                                        'account_number' => $UserBankDetails['account_number']
                                    );
                                    $checkExistBank = $this->user->checkTEmpleteBankUnique($checkBank);
                                    
                                    //pre($checkExistBank,1);
                                    if (!empty($checkExistBank)) {
                                        $where      = array(
                                            'id' => $checkExistBank['id']
                                        );

                                        if ($updateMainArr['is_primary'] == 'Y') {
                                            
                                            $updateArry = array();
                                            
                                            $updateArry['is_primary'] = 'N';
                                            $updateAll                = $this->user->updateAllPrimary($updateArry, $updateMainArr['fk_user_id']);
                                            
                                            $changeBank = $this->user->updateMainBank($updateMainArr, $where);
                                        }else{

                                             $changeBank = $this->user->updateMainBank($updateMainArr, $where);
        

                                    }

                                    } else {
                                        if ($updateMainArr['is_primary'] == 'Y') {
                                            
                                            $updateArry = array();
                                            
                                            $updateArry['is_primary'] = 'N';
                                            $updateAll                = $this->user->updateAllPrimary($updateArry, $updateMainArr['fk_user_id']);
                                            
                                            $changeBank = $this->user->insertMainBank($updateMainArr);
                                        } else {
                                            
                                            $changeBank = $this->user->insertMainBank($updateMainArr);
                                        }
                                    }
                                } else {
                                    $where      = array(
                                        'id' => $UserBankDetails['fk_profile_bank_id']
                                    );
                                    $changeBank = $this->user->updateMainBank($updateMainArr, $where);
                                }
                                if ($changeBank) {
                                    $insHstryArr = $updateMainArr;
                                    unset($insHstryArr['is_active']);
                                    unset($insHstryArr['id']);
                                    $insHstryArr['approved_timestamp'] = $approveDate;
                                    $insHstryArr['replaced_timestamp'] = date("Y-m-d H:i:s");
                                    $insHiostry                        = $this->user->insertHistoryBank($insHstryArr);
                                    if ($insHiostry) {
                                        $whereDelTmp = array(
                                            'id' => $UserBankDetails['id']
                                        );
                                        $delete      = $this->user->deleteFromTempBank($whereDelTmp);
                                    }
                                    
                                    $apprArr = array(
                                        'userId' => $req_arr['userId'],
                                        'admin_user_id' => $check_user['admin_user_id']
                                    );
                                    $this->allApproveUserDetails($apprArr);
                                }
                            }
                        } else {
                            $updateTempArr = array(
                                'bank_status' => $req_arr['Newbank_status'],
                                'admin_message_bank' => (($req_arr['admin_message_bank'] == 'null') ? NULL : $req_arr['admin_message_bank'])
                            );
                            $whereTemp     = array(
                                'id' => $req_arr['bankId']
                            );
                            $update        = $this->user->updateTempBank($updateTempArr, $whereTemp);
                        }
                    }
                    $success_message = "Bank details saved successfully";
                    $http_response   = 'http_response_ok';
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
     * @ Function Name            : getAdjustmentDetails()
     * @ Added Date               : 19-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : get user Bank details
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 19-09-2016
     * @ Modified By              : Piyalee
     * 
     */
    public function getAdjustmentDetails_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } else {
            $req_arr = $details_arr = array();
            //pre($this->post(),1);
            $flag    = true;
            if (empty($this->post('pass_key', true))) {
                $flag          = false;
                $error_message = "Pass Key is required";
            } else {
                $req_arr['pass_key'] = $this->post('pass_key', true);
            }
            
            if (empty($this->post('admin_user_id', true))) {
                $flag          = false;
                $error_message = "Admin User Id is required";
            } else {
                $req_arr['admin_user_id'] = $this->post('admin_user_id', true);
            }
            
            if (empty($this->post('userId', true))) {
                $flag          = false;
                $error_message = "User Id is required";
            } else {
                $req_arr['userId'] = $this->post('userId', true);
            }
            
            if ($flag) {
                $check_user       = array(
                    'pass_key' => $this->encrypt->decode($req_arr['pass_key']),
                    'admin_user_id' => $this->encrypt->decode($req_arr['admin_user_id'])
                );
                $checkloginstatus = $this->admin->checkSessionExist($check_user);
                if (!empty($checkloginstatus) && count($checkloginstatus) > 0) {
                    $UserAdjustDetails      = $this->user->getAdjustmentDetails($req_arr);
                    $details_arr['dataset'] = $UserAdjustDetails;
                    $result_arr             = $details_arr;
                    $http_response          = 'http_response_ok';
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
     * @ Function Name            : updateAdjustmentDetails()
     * @ Added Date               : 20-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : update user Adjustment details
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 20-09-2016
     * @ Modified By              : Piyalee
     * 
     */
    public function updateAdjustmentDetails_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } else {
            $req_arr = $details_arr = array();
            //pre($this->post(),1);
            $flag    = true;
            if (empty($this->post('pass_key', true))) {
                $flag          = false;
                $error_message = "Pass Key is required";
            } else {
                $req_arr['pass_key'] = $this->post('pass_key', true);
            }
            
            if (empty($this->post('admin_user_id', true))) {
                $flag          = false;
                $error_message = "Admin User Id is required";
            } else {
                $req_arr['admin_user_id'] = $this->post('admin_user_id', true);
            }
            
            if (empty($this->post('userId', true))) {
                $flag          = false;
                $error_message = "User Id is required";
            } else {
                $req_arr['userId'] = $this->post('userId', true);
            }
            
            if (empty($this->post('interest_adjustment', true))) {
                $flag          = false;
                $error_message = "Interest Adjustment is required";
            } else {
                $req_arr['interest_adjustment'] = $this->post('interest_adjustment', true);
            }
            
            if (empty($this->post('credit_limit_adjustment', true))) {
                $flag          = false;
                $error_message = "Credit Limit Adjustment is required";
            } else {
                $req_arr['credit_limit_adjustment'] = $this->post('credit_limit_adjustment', true);
            }
            
            if (empty($this->post('usage_fee_discount_amount', true))) {
                $flag          = false;
                $error_message = "Usage Fee Discount Amount is required";
            } else {
                $req_arr['usage_fee_discount_amount'] = $this->post('usage_fee_discount_amount', true);
            }
            
            if ($flag) {
                $check_user       = array(
                    'pass_key' => $this->encrypt->decode($req_arr['pass_key']),
                    'admin_user_id' => $this->encrypt->decode($req_arr['admin_user_id'])
                );
                $checkloginstatus = $this->admin->checkSessionExist($check_user);
                if (!empty($checkloginstatus) && count($checkloginstatus) > 0) {
                    $updateAdjustArr  = array(
                        'fk_user_id' => $req_arr['userId'],
                        'interest_adjustment' => $req_arr['interest_adjustment'],
                        'credit_limit_adjustment' => $req_arr['credit_limit_adjustment'],
                        'usage_fee_discount_amount' => $req_arr['usage_fee_discount_amount'],
                        'is_active' => 1
                    );
                    $whereCheck       = array(
                        'fk_user_id' => $req_arr['userId']
                    );
                    $checkAdjustExist = $this->user->checkAdjustExist($whereCheck);
                    if (!empty($checkAdjustExist)) {
                        $whereAdjust         = array(
                            'id' => $checkAdjustExist['id']
                        );
                        $updateAdjustDetails = $this->user->updateAdjustmentDetails($updateAdjustArr, $whereAdjust);
                        $success_message     = "Update Adjustment details successfully";
                    } else {
                        $updateAdjustDetails = $this->user->insertAdjustmentDetails($updateAdjustArr);
                        $success_message     = "Added Adjustment details successfully";
                    }
                    $http_response = 'http_response_ok';
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
    
    function allApproveUserDetails($req_arr)
    {
        $approve      = false;
        $userType     = $this->user->getUserType($req_arr['userId']);
        $approveBasic = $this->user->checkForBasicApproval($req_arr['userId']);
        if (empty($approveBasic['temp_data']) && !empty($approveBasic['main_data'])) {
            $approveKyc = $this->user->checkForKycApproval($req_arr['userId']);
            if (empty($approveKyc['temp_data']) && !empty($approveKyc['main_data'])) {
                $approveBank = $this->user->checkForBankApproval($req_arr['userId']);
                if (empty($approveBank['temp_data']) && !empty($approveBank['main_data'])) {
                    if ($userType['user_mode'] == 'B') {
                        $approveEdu = $this->user->checkForEduApproval($req_arr['userId']);
                        if (empty($approveEdu['temp_data']) && !empty($approveEdu['main_data'])) {
                            $approve = true;
                        }
                    } else {
                        $approve = true;
                    }
                } else {
                    $approve = false;
                }
            } else {
                $approve = false;
            }
        } else {
            $approve = false;
        }
        
        if ($approve) {
            $checkApprove = $this->user->checkUserApprove($req_arr['userId']);
            if (empty($checkApprove)) {
                $insAppr  = array(
                    'fk_user_id' => $req_arr['userId'],
                    'fk_approved_by_admin_id' => $req_arr['admin_user_id'],
                    'complete_approval_timestamp' => date('Y-m-d H:i:s')
                );
                $apprveId = $this->user->approveUserAdd($insAppr);
                
                if ($apprveId) {
                    
                    $UserBasicDetails = $this->user->getUserDetails($req_arr);
                    
                    //send email
                    //initialising codeigniter email
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
                    $this->email->to($UserBasicDetails['email_id']);
                    $this->email->subject('Profile Approved: mPokket');
                    
                    $getUserType                = $this->user->getUserType($req_arr['userId']);
                    $email_data['first_name']   = $UserBasicDetails['f_name'];
                    $email_data['referal_code'] = $getUserType['user_code'];
                    
                    $email_body = $this->parser->parse('email_templates/welcomemPokket', $email_data, true);
                    $this->email->message($email_body);
                    $send = $this->email->send();
                    // email send end
                    
                    //$checkMobileDeviceid = $this->user->checkMobileDeviceTable($req_arr['userId']);
                    //if($checkMobileDeviceid > 0){
                    
                    //add into notification table
                    $notification_code                            = 'ADM-APV';
                    $notificationDetailsArr                       = $this->notifications_model->getNotificationTypes($notification_code);
                    $notification_data['fk_user_id']              = $req_arr['userId'];
                    $notification_data['notification_for_mode']   = 'B';
                    $notification_data['fk_notification_type_id'] = $notificationDetailsArr['id'];
                    $notification_data['notification_message']    = 'Your profile is approved by Admin';
                    //serialized data
                    $json_data_array['display_name']              = $UserBasicDetails['display_name'];
                    $json_data_array['accepted_id']               = $req_arr['userId'];
                    $json_data_array['notification_code']         = $notification_code;
                    
                    $json_data_array['img_url']        = '';
                    $json_data_serialize               = json_encode($json_data_array);
                    //end of serialized data
                    $notification_data['routing_json'] = $json_data_serialize;
                    
                    $this->notifications_model->addUserNotification($notification_data);
                    
                    //send push message
                    $pushType                 = $notification_code;
                    $message                  = $notification_data['notification_message'];
                    $total_new_notifications  = $this->notifications_model->getAllNewNotifications($req_arr['userId']);
                    $display_name             = $UserBasicDetails['display_name'];
                    $profile_picture_file_url = '';
                    $push_message             = "{~message~:~" . $message . "~,~total_new_notifications~:~" . $total_new_notifications . "~,~accepted_id~:~" . $req_arr['userId'] . "~,~user_id~:~" . $req_arr['userId'] . "~,~name~:~" . $display_name . "~,~profile_image~:~" . $profile_picture_file_url . "~,~push_type~:~" . $notification_code . "~}";
                    
                    $this->sendMobilePushNotifications($req_arr['userId'], $push_message, $pushType, $message);
                    //end push message
                    //}                   
                }
            }
            
            /** Check For Referals **/
            $referalUser = $this->user->checkReferalUser($req_arr['userId']);
            if (!empty($referalUser) && count($referalUser) > 0) {
                $userRefType = $this->user->getUserType($referalUser['fk_refered_by_user_id']);
                if ($userRefType['user_mode'] == 'B') {
                    $mcoinPoint = $this->user->getMcoinEarning(1);
                    //print_r($mcoinPoint); exit();
                    if (!empty($mcoinPoint)) {
                        $insertMcoinArr = array(
                            'fk_user_id' => $referalUser['fk_refered_by_user_id'],
                            'fk_activity_user_id' => $referalUser['fk_user_id'],
                            'fk_mcoin_activity_id' => 1,
                            'non_referred_connections' => $mcoinPoint[0]['non_referred_connections'],
                            'referred_connections' => $mcoinPoint[0]['referred_connections'],
                            'own_activity' => $mcoinPoint[0]['own_activity']
                        );
                        $mcoinEarning   = $this->user->insertMcoinEarning($insertMcoinArr);
                        $checkUserLevel = $this->user->checkUserLevel($referalUser['fk_refered_by_user_id']);
                        if (!empty($checkUserLevel)) {
                            $mcoin_points = $checkUserLevel['total_mcoin_points'] + $mcoinPoint[0]['referred_connections'];
                            $userLevel    = $this->user->fetchUserLevelPerPoint($mcoin_points);
                            $updateArr    = array(
                                'fk_user_id' => $referalUser['fk_refered_by_user_id'],
                                'fk_mcoin_level_id' => $userLevel['id'],
                                'total_mcoin_points' => $mcoin_points,
                                'update_timestamp' => date('Y-m-d H:i:s')
                            );
                            $whereLevel   = array(
                                'id' => $checkUserLevel['id']
                            );
                            $update       = $this->user->updateUserLevels($updateArr, $whereLevel);
                        }
                    }
                }
                if ($userRefType['user_mode'] == 'A') {
                    $rewardPoint = $this->user->getRewardEarning(1);
                    //print_r($rewardPoint); exit();
                    if (!empty($rewardPoint)) {
                        $insertRewardArr = array(
                            'fk_user_id' => $referalUser['fk_refered_by_user_id'],
                            'fk_activity_user_id' => $referalUser['fk_user_id'],
                            'fk_reward_activity_id' => 1,
                            'reward_point' => $rewardPoint['reward_point']
                        );
                        $rewardEarning   = $this->user->insertRewardEarning($insertRewardArr);
                    }
                }
            }
            
            /** Check for user level **/
            $checkUserLevel = $this->user->checkUserLevel($req_arr['userId']);
            if (empty($checkUserLevel)) {
                $insertLevelArr = array(
                    'fk_user_id' => $req_arr['userId'],
                    'fk_mcoin_level_id' => 1,
                    'total_mcoin_points' => 0,
                    'update_timestamp' => date('Y-m-d H:i:s')
                );
                $insert         = $this->user->insertUserLevels($insertLevelArr);
            }
        }
    }
    
    
    
    
    public function sendMobilePushNotifications($receiver_id, $push_message, $pushType, $message)
    {
        $appExtras  = new AppExtrasAPI();
        $check_push = $appExtras->canSendPushToUser($receiver_id);
        
        if ($check_push) {
            
            $device_dtl = $this->user->fetchMobileDevice($receiver_id);
            $device_uid = $device_dtl['device_uid'];
            
            $badge_count     = $device_dtl['badge_count'] + 1;
            $device_table_id = $device_dtl['id'];
            $device_os       = $device_dtl['device_os'];
            if ($device_os == 'iOS') {
                if ($isappactive == 0) {
                    $dataappactive['badge_count'] = 1;
                    $dataappactive['id']          = $device_table_id;
                    // $this->salesrep_model->updateIsappactive($dataappactive);
                }
                $appExtras->sendPushDirect($receiver_id, $device_uid, $push_message, $pushType, $device_os, $message);
                
            } else if ($device_os == 'And') {
                
                $appExtras->sendPushDirect($receiver_id, $device_uid, $push_message, $pushType, $device_os, $message);
            }
        }
    }
    
    
    public function updateIsBlock_post()
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
                $error_message = "userId is required";
            } else {
                $req_arr['id'] = $this->post('userId', true);
            }
            
            if ($flag && empty($this->post('is_block', true))) {
                $flag          = false;
                $error_message = "is_block is required";
            } else {
                $req_arr['is_block'] = $this->post('is_block', true);
            }
        }
        if ($flag) {
            $plaintext_pass_key = $this->encrypt->decode($req_arr['pass_key']);
            $plaintext_admin_id = $this->encrypt->decode($req_arr['admin_user_id']);
            
            $req_arr1['pass_key']      = $plaintext_pass_key;
            $req_arr1['admin_user_id'] = $plaintext_admin_id;
            $check_session             = $this->admin->checkSessionExist($req_arr1);
            
            if (!empty($check_session) && count($check_session) > 0) {
                $config_data       = array();
                $param['id']       = $req_arr['id'];
                $param['is_block'] = $req_arr['is_block'];
                //echo $config_data['status'];die;
                $status            = $this->user->updateIsBlock($param);
                if ($status) {
                    
                    $result_arr      = $status;
                    $http_response   = 'http_response_ok';
                    $success_message = 'Status update successfully';
                    
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
    
    
    
    
    
    
    
    /****************************end of user controlller**********************/
    
}
