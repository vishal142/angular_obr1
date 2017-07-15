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


class Agents extends REST_Controller{
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
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/admin/agent_model', 'agents');
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
    * @ Function Name            : addAgentCode()
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
    public function addAgentCode_post(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();

        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $req_arr1 = array();
            $plaintext_pass_key = $this->encrypt->decode($this->input->post('pass_key', TRUE));
            $plaintext_admin_id = $this->encrypt->decode($this->input->post('admin_user_id', TRUE));

            $req_arr1['pass_key']        = $plaintext_pass_key;
            $req_arr1['admin_user_id']   = $plaintext_admin_id;
            $check_session  = $this->admin->checkSessionExist($req_arr1);

            if(!empty($check_session) && count($check_session) > 0){

                $req_arr = array();
                $req_arr['agent_code'] = $this->input->post('agent_code', TRUE);

                $checkDegreeType = $this->agents->checkDuplicateAgentCode($req_arr);
                if(empty($checkDegreeType)){
                    $update_arr = array();
                    $this->form_validation->set_rules("agent_code","Agent Code","trim|required");
                    if($this->form_validation->run() == FALSE){
                        $error_message = validation_errors();
                        $http_response = 'http_response_bad_request';
                    } else {

                        $agent_code_arr = array();
                        $agent_code_arr['agent_code']           = $req_arr['agent_code'];
                        //$agent_code_arr['is_active']          = 0;
                        $agent_code_arr['fk_added_by_admin_id'] = $this->encrypt->decode($this->input->post('admin_user_id', TRUE));
                        $last_id = $this->agents->addAgentCode($agent_code_arr); 

                        if($last_id > 0){

                            $data = array(
                                'id' => $last_id,
                            );
                            $result_arr         = $data;
                            $http_response      = 'http_response_ok';
                            $success_message    = 'Agent code added successfully'; 

                        } else {
                            $http_response      = 'http_response_bad_request';
                            $error_message      = 'Something went wrong in API';  
                        }
                    }
                } else {
                    $http_response = 'http_response_bad_request';
                    $error_message = 'Agent Code already exists, please try another';
                }
            } else {
                $http_response  = 'http_response_invalid_login';
                $error_message  = 'User is invalid';
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }


    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : getAllAgentCode()
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
    public function getAllAgentCode_post(){   
    
        $error_message = $success_message = $http_response = '';
        $result_arr = array();

        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $req_arr1 = array();
            $plaintext_pass_key = $this->encrypt->decode($this->input->post('pass_key', TRUE));
            $plaintext_admin_id = $this->encrypt->decode($this->input->post('admin_user_id', TRUE));

            $req_arr1['pass_key']        = $plaintext_pass_key;
            $req_arr1['admin_user_id']   = $plaintext_admin_id;
            $check_session  = $this->admin->checkSessionExist($req_arr1);

            if(!empty($check_session) && count($check_session) > 0){            
                $req_arr = $details_arr = array();
                //pre($this->input->post(),1);

                $req_arr['page'] = $this->input->post('page', true);
                $req_arr['page_size'] = $this->input->post('page_size', true);
                $req_arr['order'] = $this->input->post('order', true);
                $req_arr['order_by'] = $this->input->post('order_by', true);
                $req_arr['searchByCodeorName'] = $this->input->post('searchByCodeorName', true);              
                //$req_arr['id'] = $this->input->post('id', true);

                $details_arr['dataset'] = $this->agents->getAllAgentCode($req_arr);
                $details_arr['count']   = $this->agents->getAllAgentCodeCount($req_arr);
                //pre($details_arr,1);

                if(!empty($details_arr) && count($details_arr) > 0){
                    $result_arr         = $details_arr;
                    $http_response      = 'http_response_ok';
                    $success_message    = 'All Agent codes';  
                } else {
                    $http_response      = 'http_response_bad_request';
                    $error_message      = 'Something went wrong in API';  
                }
            } else {
                $http_response  = 'http_response_invalid_login';
                $error_message  = 'User is invalid';
            }
        }
        
        json_response($result_arr, $http_response, $error_message, $success_message);
    }


    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : generateAgentCode()
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
    public function generateAgentCode_post(){   
    
        $error_message = $success_message = $http_response = '';
        $result_arr = array();

        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $req_arr1 = array();
            $plaintext_pass_key = $this->encrypt->decode($this->input->post('pass_key', TRUE));
            $plaintext_admin_id = $this->encrypt->decode($this->input->post('admin_user_id', TRUE));

            $req_arr1['pass_key']        = $plaintext_pass_key;
            $req_arr1['admin_user_id']   = $plaintext_admin_id;
            $check_session  = $this->admin->checkSessionExist($req_arr1);

            if(!empty($check_session) && count($check_session) > 0){            
                $req_arr = $details_arr = array();
                
                $details_arr  = strtoupper('AGN'.getVerificationCode());
                //pre($details_arr,1);

                if(!empty($details_arr) && count($details_arr) > 0){
                    $result_arr         = $details_arr;
                    $http_response      = 'http_response_ok';
                    $success_message    = 'Code generated successfully';  
                } else {
                    $http_response      = 'http_response_bad_request';
                    $error_message      = 'Something went wrong in API';  
                }
            } else {
                $http_response  = 'http_response_invalid_login';
                $error_message  = 'User is invalid';
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    } 



    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : deleteAgentCode()
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
    public function deleteAgentCode_post(){   
    
        $error_message = $success_message = $http_response = '';
        $result_arr = array();

        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {
            $req_arr1 = array();
            $plaintext_pass_key = $this->encrypt->decode($this->input->post('pass_key', TRUE));
            $plaintext_admin_id = $this->encrypt->decode($this->input->post('admin_user_id', TRUE));

            $req_arr1['pass_key']        = $plaintext_pass_key;
            $req_arr1['admin_user_id']   = $plaintext_admin_id;
            $check_session  = $this->admin->checkSessionExist($req_arr1);

            if(!empty($check_session) && count($check_session) > 0){
                $req_arr = $details_arr = array();
                //pre($this->input->post(),1);

                $req_arr['id'] = $this->input->post('agentCodeId', true);
                $details_arr = $this->agents->deleteAgentCode($req_arr);

                if(!empty($details_arr) && count($details_arr) > 0){
                    //$result_arr         = $details_arr;
                    $http_response      = 'http_response_ok';
                    $success_message    = 'Agent Code deleted successfully';  
                } else {
                    $http_response      = 'http_response_bad_request';
                    $error_message      = 'Something went wrong in API';  
                }
            } else {
                $http_response  = 'http_response_invalid_login';
                $error_message  = 'User is invalid';
            }                
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    } 


    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : sendCode()
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

    public function sendCode_post(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {
            $req_arr1 = array();
            $plaintext_pass_key = $this->encrypt->decode($this->input->post('pass_key', TRUE));
            $plaintext_admin_id = $this->encrypt->decode($this->input->post('admin_user_id', TRUE));

            $req_arr1['pass_key']        = $plaintext_pass_key;
            $req_arr1['admin_user_id']   = $plaintext_admin_id;
            $check_session  = $this->admin->checkSessionExist($req_arr1);

            if(!empty($check_session) && count($check_session) > 0){
                $req_arr        = array();
                $flag           = true;
                $send           = false;
                $email          = $this->input->post('email', TRUE);
                $agent_code_id  = $this->input->post('agent_code_id', TRUE);


                if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                    $flag               = false;
                    $error_message      = 'Invalid Email Id format';
                    $http_response      = 'http_response_bad_request';
                } 
                //pre($req_arr,1);

                if($flag){

                    $req_arr['agent_code_id'] = $agent_code_id;
                    $details_arr = $this->agents->getAgentCodeDetails($req_arr);


                    //send email
                    //initialising codeigniter email
                    $config['protocol']     = 'sendmail';
                    $config['mailpath']     = '/usr/sbin/sendmail';
                    $config['charset']      = 'utf-8';
                    $config['wordwrap']     = TRUE;
                    $config['mailtype']     = 'html';
                    $this->email->initialize($config);
                    
                    // email sent to user 
                    $admin_email= $this->config->item('admin_email');
                    $admin_email_from= $this->config->item('admin_email_from');
                    $this->email->from($admin_email, $admin_email_from);
                    $this->email->to($email);          
                    $this->email->subject('Agent Code: mPokket');

                    $email_data['agent_code'] = $details_arr['agent_code'];                   

                    $email_body= $this->parser->parse('email_templates/sendAgentCode', $email_data, true);
                    $this->email->message($email_body);            

                    $send = $this->email->send();
                    // email send end
                    //$send = true;   $error_message = '';

                    if($send){

                        $id = $this->agents->updateAgentCode(array('id' => $agent_code_id), array('is_sent' => 1));
                        $result_arr         = array(
                                                'agent_code'    => $details_arr['agent_code'],
                                                'email'         => $email,
                                            );

                        $http_response      = 'http_response_ok';
                        $success_message    = 'Agent code has been sent';  
                    } else {
                        $http_response      = 'http_response_bad_request';
                        $error_message      = ($error_message) ? $error_message : 'Something went wrong in API';  
                    }
                }
            } else {
                $http_response  = 'http_response_invalid_login';
                $error_message  = 'User is invalid';
            }                
        }
        json_response($result_arr, $http_response, $error_message, $success_message);        
    }


    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : importAgentCode()
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
    public function importAgentCode_post(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();

        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $req_arr1 = array();
            $plaintext_pass_key = $this->encrypt->decode($this->input->post('pass_key', TRUE));
            $plaintext_admin_id = $this->encrypt->decode($this->input->post('admin_user_id', TRUE));

            $req_arr1['pass_key']        = $plaintext_pass_key;
            $req_arr1['admin_user_id']   = $plaintext_admin_id;
            $check_session  = $this->admin->checkSessionExist($req_arr1);

            if(!empty($check_session) && count($check_session) > 0){
                $this->load->library('excel_reader/PHPExcel');
                $this->load->library('excel_reader/PHPExcel/iofactory');
                //pre($_FILES);
                if(!empty($_FILES['file_name']['name'])){

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

                        $master_agent_data = array();
                        $admin_user_id = $this->encrypt->decode($this->input->post('admin_user_id', TRUE));

                        for($i=0; $i < $sheetCount; $i++) {
                            $csv_data = array();
                            //$csv_data = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                            $csv_data = $objPHPExcel->setActiveSheetIndex($i)->toArray(null, true, true, true);                           
                            if(array_key_exists('A', $csv_data[1])){                      
                                foreach ($csv_data as $key => $value) {
                                    if($key==1){
                                        continue;
                                    }else{
                                        $agent_code_arr = array();
                                        $agent_code_arr['agent_code']           = $value['A'];
                                        $agent_code_arr['is_active']            = 1;
                                        $agent_code_arr['fk_added_by_admin_id'] = $admin_user_id;

                                        $master_agent_data[] = $agent_code_arr;
                                    }
                                }
                            } else {
                                $http_response      = 'http_response_bad_request';
                                $error_message      = 'File structure is not matched';
                                continue;
                            }                                                 
                        }
                        //pre($master_agent_data,1);

                        /* Release memory occupied by php excel object*/
                        $objPHPExcel->disconnectWorksheets();
                        unset($objPHPExcel);

                        //Insert dump data as a batch                        
                        if(!empty($master_agent_data) && count($master_agent_data) > 0){
                            $details_arr = $this->agents->batchInsert($master_agent_data);
                        } else {
                            $http_response      = 'http_response_bad_request';
                            $error_message      = ($error_message != '') ? $error_message : 'Please do not upload blank file'; 
                        }
                        
                        unset($csv_data);
                        unset($master_agent_data);
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
                } else {
                    $http_response  = 'http_response_bad_request';
                    $error_message  = 'Please select any file';
                }
            } else {
                $http_response  = 'http_response_invalid_login';
                $error_message  = 'User is invalid';
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }
   

    
    /****************************end of agent controlller**********************/

}
