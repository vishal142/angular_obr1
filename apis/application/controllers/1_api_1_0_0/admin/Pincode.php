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


class Pincode extends REST_Controller
{
    function __construct()
    {
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
        $this->load->model('api_'.$this->config->item('test_api_ver').'/admin/pincode_model','pincode');
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
     * @ Function Name            : getAllPincodes()
     * @ Added Date               : 01-10-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : get Pincodes
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 01-10-2016
     * @ Modified By              : Piyalee
     * 
    */
    public function getAllPincodes_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) 
        {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } 
        else 
        {
            $req_arr = $details_arr = array();
            $flag    = true;
            if(empty($this->post('pass_key', true)))
            {
                $flag           = false;
                $error_message  = "Pass Key is required";
            }
            else
            {
                $req_arr['pass_key']            = $this->post('pass_key', true);
            }

            if(empty($this->post('admin_user_id', true)))
            {
                $flag           = false;
                $error_message  = "Admin User Id is required";
            }
            else
            {
                $req_arr['admin_user_id']            = $this->post('admin_user_id', true);
            }

            if(empty($this->post('page', true)))
            {
                $flag           = false;
                $error_message  = "Page is required";
            }
            else
            {
                $req_arr['page']            = $this->post('page', true);
            }

            if(empty($this->post('page_size', true)))
            {
                $flag           = false;
                $error_message  = "Page Size is required";
            }
            else
            {
                $req_arr['page_size']            = $this->post('page_size', true);
            }
            $req_arr['order']           = $this->post('order', true);
            $req_arr['order_by']        = $this->post('order_by', true);
            $req_arr['searchByCity']    = $this->post('searchByCity', true);
            $req_arr['searchByState']   = $this->post('searchByState', true);
            $req_arr['searchByPincode'] = $this->post('searchByPincode', true);
            
            if($flag)
            {
                $check_user = array(
                    'pass_key'      => $this->encrypt->decode($req_arr['pass_key']),
                    'admin_user_id' => $this->encrypt->decode($req_arr['admin_user_id']),
                );
                $checkloginstatus = $this->admin->checkSessionExist($check_user);
                if(!empty($checkloginstatus) && count($checkloginstatus) > 0)
                {
                    $pincodeList            = $this->pincode->getAllPincodes($req_arr);
                    $count                  = $this->pincode->getAllPincodesCount($req_arr);
                    $details_arr['dataset'] = $pincodeList;
                    $details_arr['count']   = $count['count_pin'];

                    /*$details_arr['city']    = $this->pincode->getAllRecords('city');
                    $details_arr['state']   = $this->pincode->getAllRecords('state');*/

                    $result_arr             = $details_arr;
                    $http_response          = 'http_response_ok';
                    $success_message        = 'All Pincodes';
                }
                else
                {
                    $http_response  = 'http_response_invalid_login';
                    $error_message  = 'User is invalid';
                }
            }
            else 
            {
                $http_response      = 'http_response_bad_request';  
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }
    
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : pincodeDetails()
     * @ Added Date               : 03-10-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : get Pincodes Details
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 03-10-2016
     * @ Modified By              : Piyalee
     * 
    */
    public function pincodeDetails_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) 
        {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } 
        else 
        {
            $req_arr = $details_arr = array();
            $flag    = true;
            if(empty($this->post('pass_key', true)))
            {
                $flag           = false;
                $error_message  = "Pass Key is required";
            }
            else
            {
                $req_arr['pass_key']        = $this->post('pass_key', true);
            }

            if(empty($this->post('admin_user_id', true)))
            {
                $flag           = false;
                $error_message  = "Admin User Id is required";
            }
            else
            {
                $req_arr['admin_user_id']   = $this->post('admin_user_id', true);
            }

            if(empty($this->post('pincode_id', true)))
            {
                $flag           = false;
                $error_message  = "Pincode Id is required";
            }
            else
            {
                $req_arr['pincode_id']      = $this->post('pincode_id', true);
            }
            
            if($flag)
            {
                $check_user = array(
                    'pass_key'      => $this->encrypt->decode($req_arr['pass_key']),
                    'admin_user_id' => $this->encrypt->decode($req_arr['admin_user_id']),
                );
                $checkloginstatus = $this->admin->checkSessionExist($check_user);
                if(!empty($checkloginstatus) && count($checkloginstatus) > 0)
                {
                    $pincodeDetails         = $this->pincode->getPincodeDetails($req_arr);
                    $details_arr['dataset'] = $pincodeDetails;

                    $result_arr             = $details_arr;
                    $http_response          = 'http_response_ok';
                    $success_message        = 'Pincodes Details';
                }
                else
                {
                    $http_response  = 'http_response_invalid_login';
                    $error_message  = 'User is invalid';
                }
            }
            else 
            {
                $http_response      = 'http_response_bad_request';  
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : importPincode()
     * @ Added Date               : 03-10-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : import pincodes
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 03-10-2016
     * @ Modified By              : Piyalee
     * 
    */
    public function importPincode_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) 
        {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } 
        else 
        {
            $req_arr = $details_arr = array();
            $flag    = true;
            if(empty($this->post('pass_key', true)))
            {
                $flag           = false;
                $error_message  = "Pass Key is required";
            }
            else
            {
                $req_arr['pass_key']       = $this->post('pass_key', true);
            }

            if(empty($this->post('admin_user_id', true)))
            {
                $flag           = false;
                $error_message  = "Admin User Id is required";
            }
            else
            {
                $req_arr['admin_user_id']  = $this->post('admin_user_id', true);
            }
            
            if($flag)
            {
                $check_user = array(
                    'pass_key'      => $this->encrypt->decode($req_arr['pass_key']),
                    'admin_user_id' => $this->encrypt->decode($req_arr['admin_user_id']),
                );
                $checkloginstatus = $this->admin->checkSessionExist($check_user);
                if(!empty($checkloginstatus) && count($checkloginstatus) > 0)
                {
                    $this->load->library('excel_reader/PHPExcel');
                    $this->load->library('excel_reader/PHPExcel/iofactory');
                    if(!empty($_FILES['file_name']['name']) && $_FILES['file_name']['size'] > 0)
                    {
                        $config['upload_path'] = 'assets/uploads';
                        $config['allowed_types'] = 'xls|csv|xlsx';
                        $config['encrypt_name'] = false;

                        $this->load->library('upload');
                        $this->upload->initialize($config);
                        if($this->upload->do_upload('file_name'))
                        {
                            $data1 = $this->upload->data();
                            $file = 'assets/uploads/'.$data1['file_name'];

                            $objPHPExcel = $this->iofactory->load($file);
                            $sheetCount = $objPHPExcel->getSheetCount();

                            $master_pincode_data = array();
                            if(!empty($sheetCount))
                            {
                                for($i=0; $i < $sheetCount; $i++) 
                                {
                                    $csv_data = array();
                                    $csv_data = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                                    //pre($csv_data,1);
                                    $admin_user_id = $check_user['admin_user_id'];

                                    foreach ($csv_data as $key => $value) 
                                    {
                                        if($key==1)
                                        {
                                            continue;
                                        }
                                        else
                                        {
                                            $pincodeArr = array();
                                            $pincodeArr['pin_code']         = $value['B'];
                                            $pincodeArr['post_office']      = $value['A'];
                                            $pincodeArr['city_name']        = $value['H'];
                                            $pincodeArr['district_name']    = $value['I'];
                                            $pincodeArr['state_name']       = $value['J'];
                                            $master_pincode_data[]          = $pincodeArr;
                                        }
                                    }
                                }
                            }
                            //pre($master_pincode_data,1);

                            /* Release memory occupied by php excel object*/
                            $objPHPExcel->disconnectWorksheets();
                            unset($objPHPExcel);

                            //Insert dump data as a batch
                            $flag = false;
                            if(!empty($master_pincode_data))
                            {
                                $details_arr = $this->pincode->batchInsert($master_pincode_data);
                            }
                            else 
                            {
                                $http_response      = 'http_response_bad_request';
                                $error_message      = 'Please do not upload blank file'; 
                            }
                            
                            unset($csv_data);
                            unset($master_pincode_data);
                            unlink($file);

                            if(!empty($details_arr) && count($details_arr) > 0)
                            {
                                $result_arr         = $details_arr;
                                $http_response      = 'http_response_ok';
                                $success_message    = 'File uploaded successfully';

                            }
                            else
                            {
                                $http_response      = 'http_response_bad_request';
                                $error_message      = ($error_message != '') ? $error_message : strip_tags($this->upload->display_errors());
                            }
                        }
                        else
                        {
                            $error_message      = str_replace("<p>","", str_replace("</p>","", $this->upload->display_errors()));  
                            $http_response      = 'http_response_bad_request';
                        }
                    }
                    else
                    {
                        $phpFileUploadErrors = array(
                            '0' => 'There is no error, the file uploaded with success',
                            '1' => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
                            '2' => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
                            '3' => 'The uploaded file was only partially uploaded',
                            '4' => 'No file was uploaded',
                            '6' => 'Missing a temporary folder',
                            '7' => 'Failed to write file to disk.',
                            '8' => 'A PHP extension stopped the file upload.',
                        );
                        $http_response = 'http_response_bad_request';
                        $error_message = $phpFileUploadErrors[$_FILES['file_name']['error']];
                    }
                }
                else
                {
                    $http_response  = 'http_response_invalid_login';
                    $error_message  = 'User is invalid';
                }
            }
            else 
            {
                $http_response      = 'http_response_bad_request';  
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }


    public function suggestPinListCity_post(){
        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) 
        {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } 
        else 
        {
            $req_arr = $details_arr = array();
            $flag    = true;
            if(empty($this->post('pass_key', true)))
            {
                $flag           = false;
                $error_message  = "Pass Key is required";
            }
            else
            {
                $req_arr['pass_key']            = $this->post('pass_key', true);
            }

            if($flag && empty($this->post('admin_user_id', true)))
            {
                $flag           = false;
                $error_message  = "Admin User Id is required";
            }
            else
            {
                $req_arr['admin_user_id']            = $this->post('admin_user_id', true);
            }

            if(!empty($this->post('autoSuggestCity', true))) {
            $value = $this->post('autoSuggestCity', true);
            }
            else {
            $value = "";
            }

            if($flag)
            {
                $req_arr1 = array(
                    'pass_key'      => $this->encrypt->decode($req_arr['pass_key']),
                    'admin_user_id' => $this->encrypt->decode($req_arr['admin_user_id']),
                );
                $check_session  = $this->admin->checkSessionExist($req_arr1);
                if(!empty($check_session) && count($check_session) > 0)
                {
            
                $city  = $this->pincode->getAllRecords('city', $value);
                $result_arr         = $city;
                $http_response      = 'http_response_ok';
                $success_message    = 'City fetched';
                } 
                else 
                {
                    $http_response  = 'http_response_invalid_login';
                    $error_message  = 'User is invalid';
                }
            }
            else
            {
                $http_response = 'http_response_bad_request';
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);

}


public function suggestPinListState_post(){


   $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) 
        {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } 
        else 
        {
            $req_arr = $details_arr = array();
            $flag    = true;
            if(empty($this->post('pass_key', true)))
            {
                $flag           = false;
                $error_message  = "Pass Key is required";
            }
            else
            {
                $req_arr['pass_key']            = $this->post('pass_key', true);
            }

            if($flag && empty($this->post('admin_user_id', true)))
            {
                $flag           = false;
                $error_message  = "Admin User Id is required";
            }
            else
            {
                $req_arr['admin_user_id']            = $this->post('admin_user_id', true);
            }

            if(!empty($this->post('autoSuggestState', true))) {
            $value = $this->post('autoSuggestState', true);
            }
            else {
            $value = "";
            }

            if($flag)
            {
                $req_arr1 = array(
                    'pass_key'      => $this->encrypt->decode($req_arr['pass_key']),
                    'admin_user_id' => $this->encrypt->decode($req_arr['admin_user_id']),
                );
                $check_session  = $this->admin->checkSessionExist($req_arr1);
                if(!empty($check_session) && count($check_session) > 0)
                {
            
                $city  = $this->pincode->getAllRecords('state', $value);
                $result_arr         = $city;
                $http_response      = 'http_response_ok';
                $success_message    = 'state fetched';
                } 
                else 
                {
                    $http_response  = 'http_response_invalid_login';
                    $error_message  = 'User is invalid';
                }
            }
            else
            {
                $http_response = 'http_response_bad_request';
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);

}




    /****************************end of mpokket pincode controlller**********************/

}
