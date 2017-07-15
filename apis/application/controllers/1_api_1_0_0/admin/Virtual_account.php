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


class Virtual_account extends REST_Controller
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
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/admin/admin_model', 'admin');
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/admin/virtual_account_model', 'account');
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
     * @ Function Name            : getAllAccounts()
     * @ Added Date               : 21-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : get all accounts
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 21-09-2016
     * @ Modified By              : Piyalee
     * 
    */
    public function getAllAccounts_post()
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
            $req_arr['searchByAccNo']   = $this->post('searchByAccNo', true);
            
            if($flag)
            {
                $check_user = array(
                    'pass_key'      => $this->encrypt->decode($req_arr['pass_key']),
                    'admin_user_id' => $this->encrypt->decode($req_arr['admin_user_id']),
                );
                $checkloginstatus = $this->admin->checkSessionExist($check_user);
                if(!empty($checkloginstatus) && count($checkloginstatus) > 0)
                {
                    $accountList  = $this->account->getAllAccounts($req_arr);
                    if(!empty($accountList))
                    {
                        foreach ($accountList as $key => $accunt) 
                        {
                            $accountList[$key]['assigned_timestamp'] = date("Y-m-d",strtotime($accunt['assigned_timestamp']));
                            $picture_file_url = 'assets/img/user-default.svg';
                            if($accunt['profile_picture_file_extension'] != '' && $accunt['s3_media_version'] != '')
                            {
                                $picture_file_url = $this->config->item('bucket_url').$accunt['fk_user_id'].'/profile/'.$accunt['fk_user_id'].'.'.$accunt['profile_picture_file_extension'].'?versionId='.$accunt['s3_media_version'];
                            }
                            $accountList[$key]['image_path'] = $picture_file_url;
                        }
                    }
                    $count                      = $this->account->getAllAccountsCount($req_arr);
                    $details_arr['dataset']     = $accountList;
                    $details_arr['count']       = $count['count_accnt'];

                    $result_arr         = $details_arr;
                    $http_response      = 'http_response_ok';
                    $success_message    = 'All Accounts';
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
     * @ Function Name            : importAccountNumber()
     * @ Added Date               : 21-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : import account numbers
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 21-09-2016
     * @ Modified By              : Piyalee
     * 
    */
    public function importAccountNumber_post()
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

                            $master_mpokket_account_data = array();
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
                                            $mpokket_account_arr = array();
                                            $mpokket_account_arr['mpokket_account_number']  = $value['A'];
                                            $mpokket_account_arr['status']                  = 'AV';
                                            $mpokket_account_arr['fk_admin_id']         = $admin_user_id;
                                            $master_mpokket_account_data[] = $mpokket_account_arr;
                                        }
                                    }
                                }
                            }
                            //pre($master_mpokket_account_data,1);

                            /* Release memory occupied by php excel object*/
                            $objPHPExcel->disconnectWorksheets();
                            unset($objPHPExcel);

                            //Insert dump data as a batch
                            if(!empty($master_mpokket_account_data))
                            {
                                $details_arr = $this->account->batchInsert($master_mpokket_account_data);
                            }
                            else 
                            {
                                $http_response      = 'http_response_bad_request';
                                $error_message      = 'Please do not upload blank file'; 
                            }
                            
                            unset($csv_data);
                            unset($master_mpokket_account_data);
                            unlink($file);

                            if(!empty($details_arr) && count($details_arr) > 0)
                            {
                                $result_arr         = $details_arr;
                                $http_response      = 'http_response_ok';
                                $success_message    = 'Account numbers imported successfully';
                            }
                            else
                            {
                                $http_response      = 'http_response_bad_request';
                                $error_message      = str_replace("<p>","", str_replace("</p>","", $this->upload->display_errors()));  
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

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : deleteAccountNumber()
     * @ Added Date               : 22-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : delete account numbers
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 22-09-2016
     * @ Modified By              : Piyalee
     * 
    */
    public function deleteAccountNumber_post()
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

            if(empty($this->post('accountId', true)))
            {
                $flag           = false;
                $error_message  = "Account Number Id is required";
            }
            else
            {
                $req_arr['accountId']            = $this->post('accountId', true);
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
                    $accountNumber  = $this->account->getAccountNumberDetails($req_arr);
                    if(!empty($accountNumber))
                    {
                        $checkUsageNumber = $this->account->checkForUsageAccount($accountNumber);
                        if(empty($checkUsageNumber))
                        {
                            $delete = $this->account->deleteMpokketAccount($req_arr);
                            $http_response      = 'http_response_ok';
                            $success_message    = 'Virtual Account deleted successfully';
                        }
                        else
                        {
                            $http_response  = 'http_response_bad_request';
                            $error_message  = "The account already is in use, can't be deleted";
                        }
                    }
                    else
                    {
                        $http_response  = 'http_response_bad_request';
                        $error_message  = "There is some problem, please try again";
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

    /****************************end of virtual account controlller**********************/

}
