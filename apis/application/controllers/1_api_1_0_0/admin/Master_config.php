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


class Master_config extends REST_Controller
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
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/admin/master_config_model', 'master_config');
        $this->load->library('form_validation');
        $this->load->library('email');
        $this->load->library('encrypt');
        
        //$this->load->library('calculation');
        
        
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
     * @ Function Name            : getConfigBasic()
     * @ Added Date               : 
     * @ Added By                 : 
     * -----------------------------------------------------------------
     * @ Description              : Fetching Configuration data
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 
     * @ Modified By              : 
     * 
    */
    
    
    public function getConfigBasic_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
            
        } else {
            $req_arr = $details_arr = $select = array();
            $flag    = true;
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
            
            if ($flag) {
                $check_user       = array(
                    'pass_key' => $this->encrypt->decode($req_arr['pass_key']),
                    'admin_user_id' => $this->encrypt->decode($req_arr['admin_user_id'])
                );
                $checkloginstatus = $this->admin->checkSessionExist($check_user);
                if (!empty($checkloginstatus) && count($checkloginstatus) > 0) {
                    $details_arr = $this->master_config->getConfig_data();
                    
                    if (!empty($details_arr) && count($details_arr) > 0) {
                        $result_arr      = $details_arr;
                        $http_response   = 'http_response_ok';
                        $success_message = 'Configuration detail successfully fetched';
                    } else {
                        $http_response = 'http_response_bad_request';
                        $error_message = 'Something went wrong in API';
                    }
                } else {
                    $http_response = 'http_response_invalid_login';
                    $error_message = 'User is invalid';
                }
            } else {
                $http_response = "http_response_bad_request";
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
        
    }
    
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : ediConfigBasic()
     * @ Added Date               : 
     * @ Added By                 : 
     * -----------------------------------------------------------------
     * @ Description              : Edit Configuration data
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 
     * @ Modified By              : 
     * 
     */
    
    public function ediConfigBasic_post()
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
            
            if ($flag && empty($this->post('id', true))) {
                $flag          = false;
                $error_message = "Id is required";
            } else {
                $where_id['id'] = $this->post('id', true);
            }           

            $req_arr['mcoin_expiry_days'] = $this->post('mcoin_expiry_days', true);
            $req_arr['agent_hurdle_days'] = $this->post('agent_hurdle_days', true);
            $req_arr['service_tax_rate'] = $this->post('service_tax_rate', true);
            $req_arr['swach_bharat_cess_rate'] = $this->post('swach_bharat_cess_rate', true);
            $req_arr['krishi_kalyan_cess_rate'] = $this->post('krishi_kalyan_cess_rate', true);
        }
                       
            
        if ($flag) {
            $plaintext_pass_key = $this->encrypt->decode($req_arr['pass_key']);
            $plaintext_admin_id = $this->encrypt->decode($req_arr['admin_user_id']);
            
            $req_arr1['pass_key']      = $plaintext_pass_key;
            $req_arr1['admin_user_id'] = $plaintext_admin_id;
            $check_session             = $this->admin->checkSessionExist($req_arr1);
            
            if (!empty($check_session) && count($check_session) > 0) {
                $config_data                            = array();
                $config_data['mcoin_expiry_days']       = $req_arr['mcoin_expiry_days'];
                $config_data['agent_hurdle_days']       = $req_arr['agent_hurdle_days'];
                $config_data['service_tax_rate']        = $req_arr['service_tax_rate'];
                $config_data['swach_bharat_cess_rate']  = $req_arr['swach_bharat_cess_rate'];
                $config_data['krishi_kalyan_cess_rate'] = $req_arr['krishi_kalyan_cess_rate'];
                
                $config_data['fk_admin_id'] = $plaintext_admin_id;
                $config_data['is_active']   = 1;
                
                $config_id = $this->master_config->update_config($where_id, $config_data);
               //echo $config_id;

               if ($config_id) {
                                    
                    //$result_arr      = $details_arr;
                    $http_response   = 'http_response_ok';
                    $success_message = 'Basic configuration updated successfully';
                    
                } else {
                    $http_response   = 'http_response_ok';
                    $success_message = 'Already updated';
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
    
    
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getConfigMcoins()
     * @ Added Date               : 
     * @ Added By                 : akp
     * -----------------------------------------------------------------
     * @ Description              : Fetch master mcoin earning
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 
     * @ Modified By              : 
     * 
     */
    
    
    public function getConfigMcoins_post()
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
                        
            
            if ($flag) {
                $check_user       = array(
                    'pass_key' => $this->encrypt->decode($req_arr['pass_key']),
                    'admin_user_id' => $this->encrypt->decode($req_arr['admin_user_id'])
                );
                $checkloginstatus = $this->admin->checkSessionExist($check_user);
                if (!empty($checkloginstatus) && count($checkloginstatus) > 0) {
                    $select = array();                  
                    
                    $details_arr = $this->master_config->getMcoinEarning_data();
                    
                    if (!empty($details_arr) && count($details_arr) > 0) {
                        $result_arr      = $details_arr;
                        $http_response   = 'http_response_ok';
                        $success_message = 'New user referral Mcoins successfully fetched';
                    } else {
                        $http_response = 'http_response_bad_request';
                        $error_message = 'Something went wrong in API';
                    }
                } else {
                    $http_response = 'http_response_invalid_login';
                    $error_message = 'User is invalid';
                }
            } else {
                $http_response = "http_response_bad_request";
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }
    
    
     /*
     * --------------------------------------------------------------------------
     * @ Function Name            : ediConfigMcoins()
     * @ Added Date               : 
     * @ Added By                 : 
     * -----------------------------------------------------------------
     * @ Description              : Edit Configuration data
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 
     * @ Modified By              : 
     * 
     */   
    public function ediConfigMcoins_post()
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
            
            if ($flag && empty($this->post('id', true))) {
                $flag          = false;
                $error_message = "Id is required";
            } else {
                $where_id['id'] = $this->post('id', true);
            }           
            
            $req_arr['referred_connections'] = $this->post('referred_connections', true);
            $req_arr['own_activity'] = $this->post('own_activity', true);
            
            if ($flag) {
                $plaintext_pass_key = $this->encrypt->decode($req_arr['pass_key']);
                $plaintext_admin_id = $this->encrypt->decode($req_arr['admin_user_id']);
                
                $req_arr1['pass_key']      = $plaintext_pass_key;
                $req_arr1['admin_user_id'] = $plaintext_admin_id;
                $check_session             = $this->admin->checkSessionExist($req_arr1);
                
                if (!empty($check_session) && count($check_session) > 0) {
                    $config_data                             = array();
                    $config_data['non_referred_connections'] = '0';
                    $config_data['referred_connections']     = $req_arr['referred_connections'];
                    $config_data['own_activity']             = $req_arr['own_activity'];
                    
                    $config_id = $this->master_config->updateEarnings($where_id, $config_data);
                    
                    if ($config_id) {
                        
                        $http_response   = 'http_response_ok';
                        $success_message = 'New user referral Mcoins updated successfully';
                        
                    } else {
                        $http_response   = 'http_response_ok';
                    $success_message = 'Already updated';
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
     * @ Function Name            : getConfigRewards()
     * @ Added Date               : 
     * @ Added By                 : akp
     * -----------------------------------------------------------------
     * @ Description              : Fetch rewards earning data
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 
     * @ Modified By              : 
     * 
     */
    
    
    
    public function getConfigRewards_post()
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
            
            
            if ($flag) {
                $check_user       = array(
                    'pass_key' => $this->encrypt->decode($req_arr['pass_key']),
                    'admin_user_id' => $this->encrypt->decode($req_arr['admin_user_id'])
                );
                $checkloginstatus = $this->admin->checkSessionExist($check_user);
                if (!empty($checkloginstatus) && count($checkloginstatus) > 0) {
                   
                    $details_arr = $this->master_config->getRewards_data();
                  
                    if (!empty($details_arr) && count($details_arr) > 0) {
                        $result_arr      = $details_arr;
                        $http_response   = 'http_response_ok';
                        $success_message = 'New user referral rewards successfully fetched';
                    } else {
                        $http_response = 'http_response_bad_request';
                        $error_message = 'Something went wrong in API';
                    }
                } else {
                    $http_response = 'http_response_invalid_login';
                    $error_message = 'User is invalid';
                }
            } else {
                $http_response = "http_response_bad_request";
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }
    
    
    
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : ediConfigRewards()
     * @ Added Date               : 
     * @ Added By                 : akp
     * -----------------------------------------------------------------
     * @ Description              : Edit rewards earning data
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 
     * @ Modified By              : 
     * 
     */
    
    
    
    public function ediConfigRewards_post()
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
            
            if ($flag && empty($this->post('id', true))) {
                $flag          = false;
                $error_message = "Id is required";
            } else {
                $where_id['id'] = $this->post('id', true);
            }
            
            $req_arr['reward_point'] = $this->post('reward_point', true);
            
            if ($flag) {
                $plaintext_pass_key = $this->encrypt->decode($req_arr['pass_key']);
                $plaintext_admin_id = $this->encrypt->decode($req_arr['admin_user_id']);
                
                $req_arr1['pass_key']      = $plaintext_pass_key;
                $req_arr1['admin_user_id'] = $plaintext_admin_id;
                $check_session             = $this->admin->checkSessionExist($req_arr1);
                
                if (!empty($check_session) && count($check_session) > 0) {
                    $config_data                          = array();
                    $config_data['fk_reward_activity_id'] = 1;
                    $config_data['reward_point']          = $req_arr['reward_point'];
                    
                    $reward_id = $this->master_config->update_reward_config($where_id, $config_data);
                    
                    if ($reward_id) {
                        
                        $http_response   = 'http_response_ok';
                        $success_message = 'New user referral rewards updated successfully';
                        
                    } else {
                        $http_response = 'http_response_ok';
                        $success_message = 'Already updated';
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
     * @ Function Name            : getConfigTierUserLevel()
     * @ Added Date               : 
     * @ Added By                 : akp
     * -----------------------------------------------------------------
     * @ Description              : Edit Configuration data
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 
     * @ Modified By              : 
     * 
     */
    
    
    
    
    public function getConfigTierUserLevel_post()
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
            
            if ($flag) {
                $check_user       = array(
                    'pass_key' => $this->encrypt->decode($req_arr['pass_key']),
                    'admin_user_id' => $this->encrypt->decode($req_arr['admin_user_id'])
                );
                $checkloginstatus = $this->admin->checkSessionExist($check_user);
                if (!empty($checkloginstatus) && count($checkloginstatus) > 0) {
                    
                    $details_arr = $this->master_config->getTierUsage_data();
                    
                    if (!empty($details_arr) && count($details_arr) > 0) {
                        $result_arr      = $details_arr;
                        $http_response   = 'http_response_ok';
                        $success_message = 'Tier usage successfully fetched';
                    } else {
                        $http_response = 'http_response_bad_request';
                        $error_message = 'Something went wrong in API';
                    }
                } else {
                    $http_response = 'http_response_invalid_login';
                    $error_message = 'User is invalid';
                }
            } else {
                $http_response = "http_response_bad_request";
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }
    
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : editConfigTierUserLevel()
     * @ Added Date               : 
     * @ Added By                 : akp
     * -----------------------------------------------------------------
     * @ Description              : Edit Tier usage level data
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 
     * @ Modified By              : 
     * 
     */

    
    public function editConfigTierUserLevel_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
            
        } else {
            
            $req_arr = array();
            $flag    = true;
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
            
            
            if ($flag) {
                $check_user       = array(
                    'pass_key' => $this->encrypt->decode($req_arr['pass_key']),
                    'admin_user_id' => $this->encrypt->decode($req_arr['admin_user_id'])
                );
                $checkloginstatus = $this->admin->checkSessionExist($check_user);
                if (!empty($checkloginstatus) && count($checkloginstatus) > 0) {
                    //echo $this->post('getTierUsage_data');
                    
                    $tier_data = json_decode($this->post('allConfigTierUserLevelData_str'), TRUE);
                    //print_r($tier_data);die;
                    
                    
                    $getTierUsage_data = array();
                    
                    foreach ($tier_data as $key => $value) {
                        $tier_level_update_arr = array();
                        
                        $tier_level_update_arr['id']                      = $value['id'];
                        $tier_level_update_arr['qualifying_mcoin_points'] = $value['qualifying_mcoin_points'];
                        $tier_level_update_arr['credit_limit']            = $value['credit_limit'];
                        
                        $getTierUsage_data[] = $tier_level_update_arr;
                    }
                    
                    //pre($getTierUsage_data,1);
                    
                    // print_r($where_id);die;
                    
                    $update = $this->master_config->batchUpdateLevel($getTierUsage_data, 'id');
                    if ($update) {
                        
                        $http_response   = 'http_response_ok';
                        $success_message = 'Tier user level updated successfully';
                        
                    } else {
                        $http_response = 'http_response_ok';
                        $success_message = 'Already updated';
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













