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
class Transactions extends REST_Controller
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
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/admin/transaction_model', 'transaction_model');
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
     * @ Function Name            : getTransactions()
     * @ Added Date               : 
     * @ Added By                 : 
     * -----------------------------------------------------------------
     * @ Description              : Fetch all Transaction data
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 
     * @ Modified By              : 
     * 
     */
    public function getAllTransaction_post(){
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
            $param['filterByTransferType'] = $this->post('filterByTransferType', true);
            $param['searchByACNoName'] = $this->post('searchByACNoName', true);

            if ($flag) {
                $plaintext_pass_key        = $this->encrypt->decode($req_arr['pass_key']);
                $plaintext_admin_id        = $this->encrypt->decode($req_arr['admin_user_id']);
                $req_arr1['pass_key']      = $plaintext_pass_key;
                $req_arr1['admin_user_id'] = $plaintext_admin_id;
                $check_session             = $this->admin->checkSessionExist($req_arr1);
                if (!empty($check_session) && count($check_session) > 0) {                  

                    $details_arr['dataset'] = $this->transaction_model->getAllTransaction($param);
                    $details_arr['count']   = $this->transaction_model->getAllTransaction_count($param);

                    //print_r($details_arr);die;
                    //print_r($details_arr['s3_media_version']);
                    $allNewDataArr = array();

                    foreach ($details_arr['dataset'] as $key => $value ) {
                        $newArr = array();
                                         
                        if($value['s3_media_version']!=''){
                            $data['profile_url'] = $this->config->item('bucket_url').$value['user_id'].'/profile/'.$value['user_id'].'.'.$value['profile_picture_file_extension'].'?versionId='.$value['s3_media_version'];                                
                        }else{
                            $data['profile_url'] = '';                     
                        }
                        
                        $newArr  = $value;
                        $newArr['profile_url']  = $data['profile_url'];
                        //$details_arr[$key]['profile_url']=$data['profile_url'];
                        $allNewDataArr[] = $newArr;

                    }
                            
                    

                    //if (!empty($allNewDataArr) && count($allNewDataArr) > 0) {
                        $result_arr['dataset']      = $allNewDataArr;
                        $result_arr['count']      = $details_arr['count'];

                        $http_response   = 'http_response_ok';
                        $success_message = 'Transactions fetched successfully';
                    /*} else {
                        $http_response = 'http_response_bad_request';
                        $error_message = 'Something went wrong in API';
                    }*/
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
     * @ Function Name            : importTransaction()
     * @ Added Date               : 20-10-2016
     * @ Added By                 : 
     * -----------------------------------------------------------------
     * @ Description              : Import Transactions detalis from Excel file 
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 
     * @ Modified By              : 
     * 
     */
    public function importTransaction_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } else {
            $req_arr1                  = array();
            $plaintext_pass_key        = $this->encrypt->decode($this->post('pass_key', TRUE));
            $plaintext_admin_id        = $this->encrypt->decode($this->post('admin_user_id', TRUE));
            $req_arr1['pass_key']      = $plaintext_pass_key;
            $req_arr1['admin_user_id'] = $plaintext_admin_id;
            $check_session             = $this->admin->checkSessionExist($req_arr1);
            if (!empty($check_session) && count($check_session) > 0) {
                $this->load->library('excel_reader/PHPExcel');
                $this->load->library('excel_reader/PHPExcel/iofactory');
                //pre($_FILES);

                if (!empty($_FILES['file_name']['name'])) {
                    $config['upload_path']   = 'assets/uploads';
                    $config['allowed_types'] = 'xlsx|csv|xls';
                    $config['encrypt_name']  = false;
                    $file_name               = $_FILES['file_name']['name'];
                    $this->load->library('upload');
                    $this->upload->initialize($config);
                    if ($this->upload->do_upload('file_name')) {
                        $count = 0;
                        $data1             = $this->upload->data();
                        $file              = 'assets/uploads/' . $data1['file_name'];
                        $objPHPExcel       = $this->iofactory->load($file);
                        $sheetCount        = $objPHPExcel->getSheetCount();
                        $master_transaction= $master_fund_transaction = array();
                        $admin_user_id     = $plaintext_admin_id;
                        for ($i = 0; $i < $sheetCount; $i++) {
                            $csv_data = array();
                            $csv_data = $objPHPExcel->setActiveSheetIndex($i)->toArray(null, true, true, true);
                            if(array_key_exists('A', $csv_data[1])) {
                                foreach ($csv_data as $key => $value) {
                                    if ($key == 1) {
                                        continue;
                                    } else {

                                        $utr_arr  = array();
                                        $utr_arr['utr_no']  = $value['I'];
                                        $utrExist = $this->transaction_model->checkUtr($utr_arr);
                                        // echo $utrExist;die;
                                        
                                        if($value['B']>0){
                                            if($utrExist == 0){
                                                if (!in_array_r($value['I'], $master_transaction)){
                                                    $mpokket_account_id = $this->transaction_model->getFkAccountId($value['F']);
                                                    if(($mpokket_account_id != null) && ($mpokket_account_id != '')) {

                                                        $transaction_code_arr = array();
                                                        $transaction_code_arr['mpokket_account_number']  = $value['F'];
                                                        $transaction_code_arr['amount']                  = $value['B'];
                                                        $dt=date('Y-m-d',strtotime($value['C']));
                                                        $transaction_code_arr['value_date']              = $dt;
                                                        $transaction_code_arr['party_code']              = $value['D'];
                                                        $transaction_code_arr['party_name']              = $value['E'];
                                                        $transaction_code_arr['remitting_bank']          = $value['H'];
                                                        $transaction_code_arr['utr_no']                 = $value['I'];
                                                        $transaction_code_arr['import_file_name']        = $file_name;
                                                        $transaction_code_arr['fk_imported_by_admin_id'] = $admin_user_id;
                                                        $master_transaction[] = $transaction_code_arr;

                                                        //
                                                        $transaction_fund_arr = array();
                                                        $transaction_fund_arr['fk_user_mpokket_account_id'] = $mpokket_account_id;
                                                        $transaction_fund_arr['transfer_amount ']           = $value['B'];
                                                        $transaction_fund_arr['transfer_type']              = 'F';
                                                        $transaction_fund_arr['transaction_date']           = date('Y-m-d-H-i-s');
                                                        $master_fund_transaction[] = $transaction_fund_arr;
                                                    }
                                                }                                                
                                            }
                                        }
                                    }
                                }
                            } else {
                                $http_response = 'http_response_bad_request';
                                $error_message = 'File structure is not matched';
                                continue;
                            }
                        }

                        //pre($master_transaction,1); 
                        $objPHPExcel->disconnectWorksheets();
                        unset($objPHPExcel);

                        if (!empty($master_transaction) && count($master_transaction) > 0) {
                            $count = $this->transaction_model->batchImportTransaction($master_transaction,$master_fund_transaction);
                        } else {
                            $http_response = 'http_response_bad_request';
                            $error_message = ($error_message != '') ? $error_message : 'Please do not upload blank file';
                        }                        

                        if (!empty($count) && count($count) > 0) {
                            //$result_arr['exported_date']    = date('Y-m-d-H-i-s');
                            $result_arr['dataset']          = $master_transaction;
                            $result_arr['count']            = $count['import'];
                            $http_response                  = 'http_response_ok';
                            $success_message                = 'File imported successfully';
                            unset($csv_data);
                            unset($master_transaction);
                            unlink($file);
                        } else {
                            $http_response = 'http_response_bad_request';
                            $error_message = ($error_message != '') ? $error_message : strip_tags($this->upload->display_errors());
                        }
                    } else {
                        $http_response = 'http_response_bad_request';
                        $error_message = strip_tags($this->upload->display_errors());
                    }
                } else {
                    $http_response = 'http_response_bad_request';
                    $error_message = 'Please select any file';
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
     * @ Function Name            : getAllImportHistory()
     * @ Added Date               : 
     * @ Added By                 : akp
     * -----------------------------------------------------------------
     * @ Description              : Get all imported transaction history 
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 
     * @ Modified By              : 
     * 
     */
    public function getAllImportHistory_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } else {
            $req_arr1                  = array();
            $plaintext_pass_key        = $this->encrypt->decode($this->post('pass_key', TRUE));
            $plaintext_admin_id        = $this->encrypt->decode($this->post('admin_user_id', TRUE));
            $req_arr1['pass_key']      = $plaintext_pass_key;
            $req_arr1['admin_user_id'] = $plaintext_admin_id;
            $check_session             = $this->admin->checkSessionExist($req_arr1);
            if (!empty($check_session) && count($check_session) > 0) {
                $req_arr                      = $details_arr = array();
                //pre($this->post(),1);
                $req_arr['page']              = $this->post('page', true);
                $req_arr['page_size']         = $this->post('page_size', true);
                $req_arr['order']             = $this->post('order', true);
                $req_arr['order_by']          = $this->post('order_by', true);
                $req_arr['filterByAmount']    = $this->post('filterByAmount', true);
                $req_arr['filterByDate']      = $this->post('filterByDate', true);
                $req_arr['searchByACNo']      = $this->post('searchByACNo', true);

                $details_arr['dataset']       = $this->transaction_model->getAllImportHistory($req_arr);
                $details_arr['count']         = $this->transaction_model->getAllImportHistory_count($req_arr);
                //$details_arr['filterDate']         = $this->transaction_model->getAllImportHistory_date($req_arr); 
                $details_arr['filterAmount']         = $this->transaction_model->getAllImportHistory_amount($req_arr);

                //pre($details_arr,1);
                //print_r($details_arr);die;
                if (!empty($details_arr) && count($details_arr) > 0) {
                    $result_arr      = $details_arr;
                    $http_response   = 'http_response_ok';
                    $success_message = 'Import Transactions details';
                } else {
                    $http_response = 'http_response_bad_request';
                    $error_message = 'Something went wrong in API';
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
     * @ Function Name            : getAllExportPending()
     * @ Added Date               : 20-10-2016
     * @ Added By                 : 
     * -----------------------------------------------------------------
     * @ Description              : Get all Pending transactions for export 
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 
     * @ Modified By              : 
     * 
     */
    public function getAllExportPending_post()
    {
        $error_message = $success_message = $http_response = '';
        $result_arr    = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } else {
            $req_arr1                  = array();
            $plaintext_pass_key        = $this->encrypt->decode($this->post('pass_key', TRUE));
            $plaintext_admin_id        = $this->encrypt->decode($this->post('admin_user_id', TRUE));
            $req_arr1['pass_key']      = $plaintext_pass_key;
            $req_arr1['admin_user_id'] = $plaintext_admin_id;
            $check_session             = $this->admin->checkSessionExist($req_arr1);
            if (!empty($check_session) && count($check_session) > 0) {
                $req_arr = $details_arr = array();
                $req_arr['page']                = $this->post('page', true);
                $req_arr['page_size']           = $this->post('page_size', true);
                $req_arr['order']               = $this->post('order', true);
                $req_arr['order_by']            = $this->post('orderByExportPending', true);

                $details_arr['dataset'] = $this->transaction_model->getPendingExportTrasaction($req_arr);
                $details_arr['count']           = $this->transaction_model->getPendingExportCount($req_arr);
                $details_arr['current_date']    = date('Y-m-d');
                $exportSeriesArr = $this->transaction_model->getMaxExportSeries($details_arr['current_date']);
                $details_arr['next_expoted_series'] = ($exportSeriesArr['daily_export_series'] == NULL) ? 1 : $exportSeriesArr['daily_export_series'] + 1;

                //if ($details_arr['count'] > 0) {
                    $result_arr      = $details_arr;
                    $http_response   = 'http_response_ok';
                    $success_message = 'Pending Transactions details';
                /*} else {
                    $http_response = 'http_response_bad_request';
                    $error_message = 'Currently no Pending Transaction availble';
                }*/
            } else {
                $http_response = 'http_response_invalid_login';
                $error_message = 'User is invalid';
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getAllExportHistory()
     * @ Added Date               : 20-10-2016
     * @ Added By                 : 
     * -----------------------------------------------------------------
     * @ Description              : Get all Exported transactions history 
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 
     * @ Modified By              : 
     * 
     */
    public function getAllExportHistory_post()
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
                $req_arr                           = $details_arr = array();
                //pre($this->input->post(),1);
                $req_arr['page']                   = $this->input->post('page', true);
                $req_arr['page_size']              = $this->input->post('page_size', true);
                $req_arr['order']                  = $this->input->post('order', true);
                $req_arr['order_by']               = $this->input->post('order_by', true);
                $req_arr['filterByTransactionType']= $this->input->post('filterByTransactionType', true);
                $req_arr['filterByDate']           = $this->input->post('filterByDate', true);
                $req_arr['searchByACNoName']       = $this->input->post('searchByACNoName', true);
                $details_arr['dataset']            = $this->transaction_model->getExportHistory($req_arr);
                $details_arr['count']              = $this->transaction_model->getExportHistoryCount($req_arr);
                //pre($details_arr,1);
                if (!empty($details_arr) && count($details_arr) > 0) {
                    $result_arr      = $details_arr;
                    $http_response   = 'http_response_ok';
                    $success_message = 'Export history details';
                } else {
                    $http_response = 'http_response_bad_request';
                //$error_message = 'Something went wrong in API';
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
     * @ Function Name            : exportPendingTransaction()
     * @ Added Date               : 20-10-2016
     * @ Added By                 : 
     * -----------------------------------------------------------------
     * @ Description              : Export Pending Transaction to Excel file 
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 
     * @ Modified By              : 
     * 
     */
    public function exportPendingTransaction_post()
    {
        $response   = $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        } else {
            $req_arr1                   = array();
            $plaintext_pass_key         = $this->encrypt->decode($this->post('pass_key', TRUE));
            $plaintext_admin_id         = $this->encrypt->decode($this->post('admin_user_id', TRUE));
            $req_arr1['pass_key']       = $plaintext_pass_key;
            $req_arr1['admin_user_id']  = $plaintext_admin_id;
            $check_session              = $this->admin->checkSessionExist($req_arr1);

            $next_export_series         = $this->post('next_expoted_series', true);
            $next_export_date           = $this->post('current_date', true);


            if (!empty($check_session) && count($check_session) > 0) {
                $all_pending_transaction        = array();
                $all_pending_transaction        = $this->transaction_model->getPendingExportTrasaction();
                //pre($all_pending_transaction,1);
                // echo $next_export_date;die;
                $row['all_pending_transaction'] = $all_pending_transaction;
                $row['no of Transactions']      = count($all_pending_transaction);
                if ($row['no of Transactions'] > 0) {
                    $cell['title']         = 'Export Pending Transactions';
                    $cell['highestCol']    = 26;
                    $cell['field'][0]['H'] = array(
                        'Transaction Type',
                        'Beneficiary Code',
                        'Beneficiary Account Number',
                        'Instrument Amount',
                        'Beneficiary Name',
                        'Drawee Location',
                        'Print Location',
                        'Bene Address 1',
                        'Bene Address 2',
                        'Bene Address 3',
                        'Bene Address 4',
                        'Instruction Reference Number',
                        'Customer Reference Number',
                        'Payment details 1',
                        'Payment details 2',
                        'Payment details 3',
                        'Payment details 4',
                        'Payment details 5',
                        'Payment details 6',                        
                        'Cheque Number',
                        'Chq / Trn Date',
                        'MICR Number',
                        'IFC Code',
                        'Bene Bank Name',
                        'Bene Bank Branch Name',
                        'Beneficiary email id'
                    );
                    $cell['flag']          = 1;
                    if (is_array($all_pending_transaction) && count($all_pending_transaction) > 0) {
                        $i = 0;
                        foreach ($all_pending_transaction as $Excel_data) {
                            $i++;
                            $cell['field'][$i]['F'] = array(
                                 array(
                                    'W' => $Excel_data['transaction_type']
                                ),
                                array(
                                    'N' => $Excel_data['beneficiary_code']
                                ),
                                array(
                                    'N' => $Excel_data['beneficiary_account_number']
                                ),
                                array(
                                    'N' => $Excel_data['instrument_amount']
                                ),
                                array(
                                    'N' => $Excel_data['beneficiary_name']
                                ),
                                array(
                                    'N' => $Excel_data['drawee_location']
                                ),
                                array(
                                    'N' => $Excel_data['print_location']
                                ),
                                array(
                                    'N' => $Excel_data['beneficiary_address_01']
                                ),
                                array(
                                    'N' => $Excel_data['beneficiary_address_02']
                                ),
                                array(
                                    'N' => $Excel_data['beneficiary_address_03']
                                ),
                                array(
                                    'N' => $Excel_data['beneficiary_address_04']
                                ),
                                
                                array(
                                    'N' => $Excel_data['beneficiary_account_number']
                                ),
                                array(
                                    'N' => $Excel_data['beneficiary_code']
                                ),
                                array(
                                    'N' => $Excel_data['payment_details_01']
                                ),
                                array(
                                    'N' => $Excel_data['payment_details_02']
                                ),
                                array(
                                    'N' => $Excel_data['payment_details_03']
                                ),
                                array(
                                    'N' => $Excel_data['payment_details_04']
                                ),
                                array(
                                    'N' => $Excel_data['payment_details_05']
                                ),
                                array(
                                    'N' => $Excel_data['payment_details_06']
                                ),
                                
                                array(
                                    'N' =>""
                                ),
                                array(
                                    'N' => $Excel_data['cheque_trn_date']
                                ),
                                array(
                                    'N' => $Excel_data['micr_code']
                                ),
                                array(
                                    'N' => $Excel_data['ifsc_code']
                                ),
                                array(
                                    'N' => $Excel_data['bank_name']
                                ),
                                array(
                                    'N' => $Excel_data['bank_branch']
                                ),
                                array(
                                    'N' => $Excel_data['beneficiary_email_id']
                                )

                            );
                        }
                    }
                   
                  //  $fileName=date("M").date("y")."RBI".date("d").date("m");
                    //print_r($cell);die;
                    
                   // $next_export_series
                    $cell['description'] = 'Transaction';
                    $expSer = str_pad($next_export_series, 3, '0', STR_PAD_LEFT);
                    $file_name           = strtoupper(date("M")).date("y")."RBI".date("d").date("m").".".$expSer;
                    $save_path           = "/var/www/html/mPokket/apis/assets/uploads/";
                    $result_arr          = $this->createExcel($cell, $save_path, $file_name);
                    
                    $update_export = array();
                    if ($result_arr) {
                        $details_arr['dataset'] = $this->transaction_model->getPendingExportTrasaction();
                        foreach ($details_arr['dataset'] as $key => $value) {
                            $Pending_data                            = array();
                            $Pending_data['id']                      = $value['id'];
                            $Pending_data['daily_export_series']     = $next_export_series;
                            $Pending_data['export_file_name']        = $file_name.".xls";
                            $Pending_data['fk_exported_by_admin_id'] = $plaintext_admin_id;
                            $Pending_data['export_timestamp']        = date('Y-m-d-H-i-s');
                            $update_export[]                         = $Pending_data;
                        }
                        $update = $this->transaction_model->batchUpdateExport($update_export, 'id');
                        //$update=1;
                        if ($update > 0) {
                            $http_response   = 'http_response_ok';
                            $success_message = 'exported successfully';
                        } else {
                            $http_response = 'http_response_bad_request';
                            $error_message = 'Something went wrong';
                        }
                        $http_response   = 'http_response_ok';
                        $success_message = 'Excel exported successfully';
                    } else {
                        $http_response = 'http_response_bad_request';
                        $error_message = 'Something went wrong';
                    }
                } else {
                    $http_response = 'http_response_invalid_login';
                    $error_message = 'Currently No pending Transactions availble to Export';
                }

            } else {
                $http_response = 'http_response_invalid_login';
                $error_message = 'User is invalid';
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }

    public function createExcel($cell, $save_path, $file_name)
    {
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("A & M")->setLastModifiedBy("A & M")->setTitle("Office 2007 XLSX Report Document")->setSubject("Office 2007 XLSX Report Document")->setDescription($cell['description'])->setKeywords("office 2007 openxml php")->setCategory("Report file");
        if ($cell['flag'] == 1) {
            //SETTING HEADER
            $asciiStart       = 65;
            $totalVals        = $cell['highestCol'];
            $getUptoCellMerg  = $asciiStart + $totalVals - 1;
            $getUptoCellMerg  = chr($getUptoCellMerg);
            //exit;
            $styleArray       = array(
                'borders' => array(
                    'outline' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array(
                            'argb' => '000000'
                        )
                    )
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            );
            $styleArrayHeader = array(
                'borders' => array(
                    'outline' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array(
                            'argb' => '000000'
                        )
                    )
                ),
                'font' => array(
                    'bold' => true
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                )
            );
            for ($i = 0; $i < $totalVals; $i++) {
                $clIndx  = $asciiStart + $i;
                $clchars = chr($clIndx);
                $objPHPExcel->getActiveSheet()->getColumnDimension($clchars)->setWidth(24);
            }
            //COUNT NUMBER OF TITLE HEADER FOR THE DOCUMENT TO GENERATE
            $totalTitle = count($cell['title']);
            //SETTING TITLE HEADER FOR EXCEL DOCUMENT
            $titleCount = 0;
            //SETTING SUB HEADERS
            $titleCount++;
            //pr($cell['field']);exit;
            //SET MAIN RECORD
            $borderCount1 = 0;
            foreach ($cell['field'] as $fldKey => $fldVal) {
                if (isset($fldVal['H'])) {
                    $cellCharToStartAscii = $asciiStart;
                    foreach ($fldVal['H'] as $hkey => $hval) {
                        $cellCharToStart = chr($cellCharToStartAscii);
                        $fieldValue      = $hval;
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellCharToStart . $titleCount, $fieldValue);
                        $objPHPExcel->getActiveSheet()->getStyle($cellCharToStart . $titleCount . ':' . $cellCharToStart . $titleCount)->applyFromArray($styleArrayHeader);
                        $cellCharToStartAscii++;
                        if ($borderCount1 == 0) {
                            $borderStart1 = $cellCharToStart . $titleCount;
                        }
                        $borderCount1++;
                    }
                    $titleCount++;
                }
                if (isset($fldVal['F'])) {
                    $cellCharToStartAscii = $asciiStart;
                    foreach ($fldVal['F'] as $fkey => $fval) {
                        foreach ($fval as $fkey1 => $fval1) {
                            //pr($fval);
                            $cellCharToStart = chr($cellCharToStartAscii);
                            $fieldValue      = $fval1;
                            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellCharToStart . $titleCount, $fieldValue);
                            $objPHPExcel->getActiveSheet()->getStyle($cellCharToStart . $titleCount . ':' . $cellCharToStart . $titleCount)->applyFromArray($styleArray);
                            if ($fkey1 == 'W') {
                                $objPHPExcel->getActiveSheet()->getStyle($cellCharToStart . $titleCount . ':' . $cellCharToStart . $titleCount)->applyFromArray(array(
                                    'alignment' => array(
                                        'wrap' => false,
                                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                                    )
                                ));
                            } elseif ($fkey1 == 'C') {
                                $objPHPExcel->getActiveSheet()->getStyle($cellCharToStart . $titleCount . ':' . $cellCharToStart . $titleCount)->applyFromArray(array(
                                    'alignment' => array(
                                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                                    )
                                ));
                            }
                        }
                        $cellCharToStartAscii++;
                    }
                    $titleCount++;
                }
                $borderEnd1 = $cellCharToStart . ($titleCount - 1);
                if (isset($fldVal['FC'])) {
                    $cellCharToStartAscii = $asciiStart;
                    $cellCharToStart      = chr($cellCharToStartAscii);
                    $fieldValue           = "";
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellCharToStart . $titleCount, $fieldValue);
                    $titleCount++;
                }
            }
            //$objPHPExcel->getActiveSheet()->getStyle($borderStart1.':'.$borderEnd1)->applyFromArray(array('borders' => array('outline' => array('style' => PHPExcel_Style_Border::BORDER_THICK,'color' => array('argb' => '000000')))));
            //            echo $borderStart1.':'.$borderEnd1;
            //            exit;
            //SET FOOTER VALUES TO EXCEL
            if (isset($cell['footer'])) {
                //COUNT NUMBER OF FOOTER ROW
                $totalFooterRow = count($cell['footer']);
                foreach ($cell['footer'] as $fldKey => $fldVal) {
                    if (isset($fldVal['H'])) {
                        $cellCharToStartAscii = $asciiStart;
                        foreach ($fldVal['H'] as $hkey => $hval) {
                            $cellCharToStart = chr($cellCharToStartAscii);
                            $fieldValue      = $hval;
                            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellCharToStart . $titleCount, $fieldValue);
                            $objPHPExcel->getActiveSheet()->getStyle($cellCharToStart . $titleCount . ':' . $cellCharToStart . $titleCount)->applyFromArray($styleArrayHeader);
                            $cellCharToStartAscii++;
                        }
                        $titleCount++;
                    }
                    if (isset($fldVal['F'])) {
                        $cellCharToStartAscii = $asciiStart;
                        foreach ($fldVal['F'] as $fkey => $fval) {
                            foreach ($fval as $fkey1 => $fval1) {
                                $cellCharToStart = chr($cellCharToStartAscii);
                                $fieldValue      = $fval1;
                                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellCharToStart . $titleCount, $fieldValue);
                                $objPHPExcel->getActiveSheet()->getStyle($cellCharToStart . $titleCount . ':' . $cellCharToStart . $titleCount)->applyFromArray($styleArray);
                                if ($fkey1 == 'W') {
                                    $objPHPExcel->getActiveSheet()->getStyle($cellCharToStart . $titleCount . ':' . $cellCharToStart . $titleCount)->applyFromArray(array(
                                        'alignment' => array(
                                            'wrap' => true,
                                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                                        )
                                    ));
                                } elseif ($fkey1 == 'C') {
                                    $objPHPExcel->getActiveSheet()->getStyle($cellCharToStart . $titleCount . ':' . $cellCharToStart . $titleCount)->applyFromArray(array(
                                        'alignment' => array(
                                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                                        )
                                    ));
                                }
                            }
                            $cellCharToStartAscii++;
                        }
                    }
                    $titleCount++;
                    if (isset($fldVal['FC'])) {
                        $cellCharToStartAscii = $asciiStart;
                        $cellCharToStart      = chr($cellCharToStartAscii);
                        $fieldValue           = "";
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellCharToStart . $titleCount, $fieldValue);
                        $titleCount++;
                    }
                }
            }
            $titleCount++;
            //$fieldValue = html_entity_decode(COPYRIGHT);
            $fieldValue = "Copyright © " . date('Y') . " mPokket All rights reserved..";
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $titleCount, $fieldValue);
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $titleCount . ':D' . $titleCount);
            if ($totalVals <= 5) {
                $generateReportCellStart = 'A';
                $titleCount++;
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $titleCount . ':D' . $titleCount);
                $objPHPExcel->getActiveSheet()->getStyle($generateReportCellStart . $titleCount . ':' . $generateReportCellStart . $titleCount)->applyFromArray(array(
                    'font' => array(
                        'italic' => true
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                    )
                ));
            } else {
                $generateReportCell      = $asciiStart + $totalVals - 1;
                $generateReportCellStart = chr($generateReportCell - 1);
                $generateReportCellEnd   = chr($generateReportCell);
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells($generateReportCellStart . $titleCount . ':' . $generateReportCellEnd . $titleCount);
                $objPHPExcel->getActiveSheet()->getStyle($generateReportCellStart . $titleCount . ':' . $generateReportCellEnd . $titleCount)->applyFromArray(array(
                    'font' => array(
                        'italic' => true
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
                    )
                ));
            }
            $fieldValue = 'Report Generated On ' . date('d-m-Y g:i:s A');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($generateReportCellStart . $titleCount, $fieldValue);
            //$objPHPExcel->getActiveSheet()->getStyle('F'.$titleCount.':'.'F'.$titleCount)->applyFromArray($styleArray);
        } else {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'No Record Found');
        }
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle($file_name);
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
        // $report_path="";
        /*if($file_name=='ANM_Report_'){
        $save_path=$save_path.$file_name.date('dMy',strtotime('-2 day'));
        }
        else{
        $save_path=$save_path.$file_name.date('dMy',strtotime('-1 day'));
        }*/
        $save_path = $save_path . $file_name;
        $objWriter->save($save_path . '.xls');
        //echo "khvf";die;
        unset($objPHPExcel);
        return true;
        /*  $objWriter->save('php://output');*/
    }



    public function importHistoryDetails_post()
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
                $req_arr                      = $details_arr = array();
                $flag     = true;
                if ($flag && empty($this->post('importHistoryId', true))) {
                    $flag          = false;
                    $error_message = "Id is required";
                } else {
                    $where_id['tmi.id'] = $this->post('importHistoryId', true);
                }   
                if($flag){
                    $details_arr = $this->transaction_model->getSingleImportDetails($where_id);
                    if (!empty($details_arr) && count($details_arr) > 0) {
                        $result_arr      = $details_arr;
                        $http_response   = 'http_response_ok';
                        $success_message = 'Import Transactions details';
                    } else {
                        $http_response = 'http_response_bad_request';
                        $error_message = 'Something went wrong in API';
                    }
                } else {
                    $http_response = 'http_response_bad_request';
                }
            } else {
                $http_response = 'http_response_invalid_login';
                $error_message = 'User is invalid';
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }


    public function exportHistoryIdDetails_post()
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
                $req_arr                      = $details_arr = array();
                $flag     = true;
                if ($flag && empty($this->post('exportHistoryId', true))) {
                    $flag          = false;
                    $error_message = "Id is required";
                } else {
                    $where_id['tme.id'] = $this->post('exportHistoryId', true);
                }   
                if($flag){
                    $details_arr = $this->transaction_model->getSingleExportDetails($where_id);
                    if (!empty($details_arr) && count($details_arr) > 0) {
                        $result_arr      = $details_arr;
                        $http_response   = 'http_response_ok';
                        $success_message = 'Export Transactions details';
                    } else {
                        $http_response = 'http_response_bad_request';
                        $error_message = 'Something went wrong in API';
                    }
                } else{
                    $http_response = 'http_response_bad_request';
                }
            } else {
                $http_response = 'http_response_invalid_login';
                $error_message = 'User is invalid';
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }



}   
