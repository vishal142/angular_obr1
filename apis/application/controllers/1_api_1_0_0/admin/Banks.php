<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*ini_set('display_errors', 1);
error_reporting(E_ALL);*/
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


class Banks extends REST_Controller{
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
        
        //echo $_SERVER['SERVER_ADDR']; exit;
        $dsn = 'mysql:dbname='.$this->config->item('oauth_db_database').';host='.$this->config->item('oauth_db_host');
        $dbusername = $this->config->item('oauth_db_username');
        $dbpassword = $this->config->item('oauth_db_password');

        $this->tables = $this->config->item('tables'); 
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/admin/admin_model', 'admin');
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/admin/bank_model', 'bank');
        $this->load->library('form_validation');
        $this->load->library('email');
        $this->load->library('encrypt');

        $this->push_type = 'P';

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
     * @ Function Name            : getAllBank()
     * @ Added Date               : 14-04-2016
     * @ Added By                 : Subhankar
     * -----------------------------------------------------------------
     * @ Description              : log out admin user
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 14-04-2016
     * @ Modified By              : Subhankar
     * 
    */
    public function getAllBank_post()
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

            if($flag && empty($this->post('admin_user_id', true)))
            {
                $flag           = false;
                $error_message  = "Admin User Id is required";
            }
            else
            {
                $req_arr['admin_user_id']            = $this->post('admin_user_id', true);
            }

            if($flag && empty($this->post('page', true)))
            {
                $flag           = false;
                $error_message  = "Page is required";
            }
            else
            {
                $req_arr['page']            = $this->post('page', true);
            }

            if($flag && empty($this->post('page_size', true)))
            {
                $flag           = false;
                $error_message  = "Page Size is required";
            }
            else
            {
                $req_arr['page_size']            = $this->post('page_size', true);
            }

            $req_arr['order']                  = $this->input->post('order', true);
            $req_arr['order_by']               = $this->input->post('order_by', true);
            $req_arr['searchByCity']           = $this->input->post('searchByCity', true);
            $req_arr['searchByState']          = $this->input->post('searchByState', true);
            $req_arr['searchByIfscNameCity']   = $this->input->post('searchByIfscNameCity',true);

            if($flag)
            {
                $req_arr1 = array(
                    'pass_key'      => $this->encrypt->decode($req_arr['pass_key']),
                    'admin_user_id' => $this->encrypt->decode($req_arr['admin_user_id']),
                );
                $check_session  = $this->admin->checkSessionExist($req_arr1);
                if(!empty($check_session) && count($check_session) > 0)
                {
                    $details_arr['dataset'] = $this->bank->getAllBank($req_arr);
                    $details_arr['count']   = $this->bank->getAllBankCount($req_arr);
                    //$details_arr['city']    = $this->bank->getAllRecords('city');
                    //$details_arr['state']   = $this->bank->getAllRecords('state');
                    //pre($details_arr,1);

                    if(!empty($details_arr) && count($details_arr) > 0)
                    {
                        $result_arr         = $details_arr;
                        $http_response      = 'http_response_ok';
                        $success_message    = 'All Banks data';  
                    } 
                    else 
                    {
                        $http_response      = 'http_response_bad_request';
                        $error_message      = 'Something went wrong in API';  
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
                $http_response = 'http_response_bad_request';
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }


    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : bankDetails()
    * @ Added Date               : 14-04-2016
    * @ Added By                 : Subhankar
    * -----------------------------------------------------------------
    * @ Description              : log out admin user
    * -----------------------------------------------------------------
    * @ return                   : array
    * -----------------------------------------------------------------
    * @ Modified Date            : 14-04-2016
    * @ Modified By              : Subhankar
    * 
    */
    public function bankDetails_post()
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

            if($flag && empty($this->post('admin_user_id', true)))
            {
                $flag           = false;
                $error_message  = "Admin User Id is required";
            }
            else
            {
                $req_arr['admin_user_id']            = $this->post('admin_user_id', true);
            }

            if($flag && empty($this->post('bank_id', true)))
            {
                $flag           = false;
                $error_message  = "Bank Id is required";
            }
            else
            {
                $req_arr['id']            = $this->post('bank_id', true);
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
                    $details_arr['dataset'] = $this->bank->getBankDetails($req_arr);
                    //pre($details_arr,1);
                    if(!empty($details_arr) && count($details_arr) > 0)
                    {
                        $result_arr         = $details_arr;
                        $http_response      = 'http_response_ok';
                        $success_message    = 'Fetched Bank details';  
                    } 
                    else 
                    {
                        $http_response      = 'http_response_bad_request';
                        $error_message      = 'Something went wrong in API';  
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
                $http_response = 'http_response_bad_request';
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }
   
    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : importBank()
    * @ Added Date               : 14-04-2016
    * @ Added By                 : Subhankar
    * -----------------------------------------------------------------
    * @ Description              : log out admin user
    * -----------------------------------------------------------------
    * @ return                   : array
    * -----------------------------------------------------------------
    * @ Modified Date            : 14-04-2016
    * @ Modified By              : Subhankar
    * 
    */
    public function importBank_post()
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
            if(empty($this->post('pass_key', true))){
                $flag           = false;
                $error_message  = "Pass Key is required";
            } else {
                $req_arr['pass_key']            = $this->post('pass_key', true);
            }

            if($flag && empty($this->post('admin_user_id', true))) {
                $flag           = false;
                $error_message  = "Admin User Id is required";
            } else {
                $req_arr['admin_user_id']            = $this->post('admin_user_id', true);
            }

            if($flag) {
                $req_arr1 = array(
                    'pass_key'      => $this->encrypt->decode($req_arr['pass_key']),
                    'admin_user_id' => $this->encrypt->decode($req_arr['admin_user_id']),
                );
                $check_session  = $this->admin->checkSessionExist($req_arr1);
                if(!empty($check_session) && count($check_session) > 0){

                    $this->load->library('excel_reader/PHPExcel');
                    $this->load->library('excel_reader/PHPExcel/iofactory');
                    if((!empty($_FILES['file_name']['name'])) && ($_FILES['file_name']['size'] > 0)){
                        $config['upload_path'] = 'assets/uploads';
                        $config['allowed_types'] = 'xlsx|csv|xls';
                        $config['encrypt_name'] = false;

                        $this->load->library('upload');
                        $this->upload->initialize($config);
                        if($this->upload->do_upload('file_name')) {

                            $data1 = $this->upload->data();
                            $file = 'assets/uploads/' . $data1['file_name'];
                           
                            $objPHPExcel = $this->iofactory->load($file);
                            $sheetCount = $objPHPExcel->getSheetCount();

                            $master_bank_data = array();
                            for($i=0; $i < $sheetCount; $i++) {
                                $csv_data = array();
                                //$csv_data = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                                $csv_data = $objPHPExcel->setActiveSheetIndex($i)->toArray(null, true, true, true);                           
                                if(array_key_exists('I', $csv_data[1])){
                                    foreach ($csv_data as $key => $value) {
                                        if($key==1){
                                            continue;
                                        }else{
                                            $import_bank_arr = array();
                                            $import_bank_arr['ifsc_code']           = $value['B'];
                                            $import_bank_arr['micr_code']           = $value['C'];
                                            $import_bank_arr['bank_name']           = $value['A'];
                                            $import_bank_arr['bank_branch']         = $value['D'];
                                            $import_bank_arr['bank_address']        = $value['E'];
                                            $import_bank_arr['bank_city']           = $value['G'];
                                            $import_bank_arr['bank_district']       = $value['H'];
                                            $import_bank_arr['bank_state']          = $value['I'];
                                            $import_bank_arr['bank_contact_number'] = $value['F'];
                                            $import_bank_arr['is_unavailable']      = 0;

                                            $master_bank_data[] = $import_bank_arr;
                                        }
                                    }
                                } else {
                                    $http_response      = 'http_response_bad_request';
                                    $error_message      = 'File structure is not matched';
                                    continue;
                                }                                                 
                            }
                            //pre($master_bank_data,1);

                            /* Release memory occupied by php excel object*/
                            $objPHPExcel->disconnectWorksheets();
                            unset($objPHPExcel);

                            //Insert dump data as a batch                        
                            if(!empty($master_bank_data) && count($master_bank_data) > 0){
                                $details_arr = $this->bank->batchInsert($master_bank_data);
                            } else {
                                $http_response      = 'http_response_bad_request';
                                $error_message      = ($error_message != '') ? $error_message : 'Please do not upload blank file'; 
                            }
                            
                            unset($csv_data);
                            unset($master_bank_data);
                            unlink($file);  

                            if(!empty($details_arr) && count($details_arr) > 0){
                                $result_arr         = $details_arr;
                                $http_response      = 'http_response_ok';
                                $success_message    = 'File uploaded successfully';

                            }else{
                                $http_response      = 'http_response_bad_request';
                                $error_message      = ($error_message != '') ? $error_message : strip_tags($this->upload->display_errors());
                            }
                        }else{

                            $http_response      = 'http_response_bad_request';
                            $error_message      = strip_tags($this->upload->display_errors());  
                        }
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
                $http_response = 'http_response_bad_request';
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }
   

   /*public function suggestListCiy_post()
   {
        if(!empty($this->post('bank_city', true))) {
            $value = $this->post('bank_city', true);
        } else {
            $value = "";
        }

        $city    = $this->bank->getAllRecords('city', $value);
        pre($city,1);
        return $city ;
   } 

 
   public function suggestListStae_post()
   {
        if(!empty($this->post('bank_state', true))) {
            $value = $this->post('bank_state', true);
        } else {
            $value = "";
        }

        $state    = $this->bank->getAllRecords('state', $value);
        pre($state,1);
        return $state;
   }    

*/
  public function suggestListCity_post(){


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
            
                $city  = $this->bank->getAllRecords('city', $value);
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
    /****************************end of agent controlller**********************/
}


 public function suggestListState_post(){


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
            
                $city  = $this->bank->getAllRecords('state', $value);
                $result_arr         = $city;
                $http_response      = 'http_response_ok';
                $success_message    = 'State fetched';
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
    /****************************end of agent controlller**********************/
}


}
