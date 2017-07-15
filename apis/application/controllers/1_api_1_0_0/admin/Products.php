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


class Products extends REST_Controller{
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

        $dsn = 'mysql:dbname='.$this->config->item('oauth_db_database').';host='.$this->config->item('oauth_db_host');
        $dbusername = $this->config->item('oauth_db_username');
        $dbpassword = $this->config->item('oauth_db_password');

        /*$sitemode= $this->config->item('site_mode');
        $this->path_detail=$this->config->item($sitemode);*/      
        $this->tables = $this->config->item('tables'); 
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/admin/admin_model', 'admin');
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/admin/product_model', 'product');
        $this->load->library('form_validation');
        //$this->load->library('email');
        $this->load->library('encrypt');

        $this->load->library('calculation');

       
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
     * @ Function Name            : addProduct()
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
    public function addProduct_post(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();

        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {            

            $req_arr1   = $req_arr = $details_arr = array();
            $flag       = true;
            if(empty($this->post('pass_key', true)))
            {
                $flag           = false;
                $error_message  = "Pass Key is required";
            }
            else
            {
                $req_arr['pass_key']          = $this->post('pass_key', true);
            }

            if($flag && empty($this->post('admin_user_id', true)))
            {
                $flag           = false;
                $error_message  = "Admin User Id is required";
            }
            else
            {
                $req_arr['admin_user_id']     = $this->post('admin_user_id', true);
            }

            if($flag && empty($this->post('profession_type', true)))
            {
                $flag           = false;
                $error_message  = "Profession Type is required";
            }
            else
            {
                $req_arr['profession_type']   = $this->post('profession_type', true);
            }

            if($flag && empty($this->post('payment_type', true)))
            {
                $flag           = false;
                $error_message  = "Payment Type is required";
            }
            else
            {
                $req_arr['payment_type']   = $this->post('payment_type', true);
            }

            if($flag && empty($this->post('product_type', true)))
            {
                $flag           = false;
                $error_message  = "Product Type is required";
            }
            else
            {
                $req_arr['product_type']   = $this->post('product_type', true);
            }

            if($flag && $req_arr['product_type'] == 'P' && empty($this->post('range_fixed', true)))
            {
                $flag           = false;
                $error_message  = "Range Fixed is required";
            }
            else
            {
                $req_arr['range_fixed']   = $this->post('range_fixed', true);
            }

            if($flag && $req_arr['product_type'] == 'R' && empty($this->post('range_from', true)))
            {
                $flag           = false;
                $error_message  = "Range From is required";
            }
            else
            {
                $req_arr['range_from']   = $this->post('range_from', true);
            }

            if($flag && $req_arr['product_type'] == 'R' && empty($this->post('range_to', true)))
            {
                $flag           = false;
                $error_message  = "Range To is required";
            }
            else
            {
                $req_arr['range_to']   = $this->post('range_to', true);
            }

            if($flag)
            {
                $plaintext_pass_key = $this->encrypt->decode($req_arr['pass_key']);
                $plaintext_admin_id = $this->encrypt->decode($req_arr['admin_user_id']);

                $req_arr1['pass_key']        = $plaintext_pass_key;
                $req_arr1['admin_user_id']   = $plaintext_admin_id;
                $check_session  = $this->admin->checkSessionExist($req_arr1);

                if(!empty($check_session) && count($check_session) > 0)
                {
                    $product_arr = array();
                    $product_arr['fk_profession_type_id']   = $req_arr['profession_type'];
                    $product_arr['fk_payment_type_id']      = $req_arr['payment_type'];
                    $product_arr['product_type']            = $req_arr['product_type'];

                    if($req_arr['product_type'] == 'P'){
                        $product_arr['amount_ranges_from']  = $req_arr['range_fixed'];
                        $product_arr['amount_ranges_to']    = $product_arr['amount_ranges_from'];
                    } else{
                        $product_arr['amount_ranges_from']  = $req_arr['range_from'];
                        $product_arr['amount_ranges_to']    = $req_arr['range_to'];
                    }
                    $product_arr['is_available']            = 0;
                    //pre($product_arr,1);
                    $product_id = $this->product->addProduct($product_arr);

                    $product_item_arr = array(
                            'product_id'    => $product_id,
                            'principle'     => $product_arr['amount_ranges_from'],
                            'payment_type'  => $product_arr['fk_payment_type_id'],
                        );
                    $this->product->addProductItems($product_item_arr);               


                    if($product_id > 0){

                        $data = array(
                            'id' => $product_id,
                        );
                        $result_arr         = $data;
                        $http_response      = 'http_response_ok';
                        $success_message    = 'Product added successfully'; 

                    } else {
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
                $http_response      = 'http_response_bad_request';
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getAllProduct()
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
    public function getAllProduct_post(){   
    
        $error_message = $success_message = $http_response = '';
        $result_arr = array();

        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {
            $req_arr = $details_arr = array();
            //pre($this->post(),1);
            $flag       = true;
            if(empty($this->post('pass_key', true)))
            {
                $flag           = false;
                $error_message  = "Pass Key is required";
            }
            else
            {
                $req_arr['pass_key']          = $this->post('pass_key', true);
            }

            if($flag && empty($this->post('admin_user_id', true)))
            {
                $flag           = false;
                $error_message  = "Admin User Id is required";
            }
            else
            {
                $req_arr['admin_user_id']     = $this->post('admin_user_id', true);
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

            $req_arr['order']       = $this->post('order', true);
            $req_arr['order_by']    = $this->post('order_by', true);
            $req_arr['searchByProfession'] = $this->post('searchByProfession', true);
            if($flag)
            {
                $check_user = array(
                    'pass_key'      => $this->encrypt->decode($req_arr['pass_key']),
                    'admin_user_id' => $this->encrypt->decode($req_arr['admin_user_id']),
                );
                $checkloginstatus = $this->admin->checkSessionExist($check_user);
                if(!empty($checkloginstatus) && count($checkloginstatus) > 0)
                {
                    $details_arr['dataset'] = $this->product->getAllProduct($req_arr);
                    $details_arr['count']   = $this->product->getAllProductCount($req_arr);
                    //pre($details_arr,1);

                    if(!empty($details_arr) && count($details_arr) > 0){
                        $result_arr         = $details_arr;
                        $http_response      = 'http_response_ok';
                        $success_message    = 'All Product';  
                    } else {
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
                $http_response      = 'http_response_bad_request';
            }
            
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }


    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getProductDetail()
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
    public function getProductDetail_post(){   
    
        $error_message = $success_message = $http_response = '';
        $result_arr = array();

        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {
            $req_arr = $details_arr = array();
            $flag       = true;
            if(empty($this->post('pass_key', true)))
            {
                $flag           = false;
                $error_message  = "Pass Key is required";
            }
            else
            {
                $req_arr['pass_key']          = $this->post('pass_key', true);
            }

            if($flag && empty($this->post('admin_user_id', true)))
            {
                $flag           = false;
                $error_message  = "Admin User Id is required";
            }
            else
            {
                $req_arr['admin_user_id']     = $this->post('admin_user_id', true);
            }

            if($flag && empty($this->post('productId', true)))
            {
                $flag           = false;
                $error_message  = "Product Id is required";
            }
            else
            {
                $req_arr['id']     = $this->post('productId', true);
            }

            $state_name = $this->post('state', true);            
            $state_name = preg_replace('/products./', '', $state_name);
            $req_arr['state']       = $state_name;
            
            if($flag)
            {
                $check_user = array(
                    'pass_key'      => $this->encrypt->decode($req_arr['pass_key']),
                    'admin_user_id' => $this->encrypt->decode($req_arr['admin_user_id']),
                );
                $checkloginstatus = $this->admin->checkSessionExist($check_user);
                if(!empty($checkloginstatus) && count($checkloginstatus) > 0)
                {
                    $details_arr                = $this->product->getProductDetail($req_arr);
                    $details_arr[$state_name]   = $this->product->getProductItemDetail($req_arr);
                    $details_arr['state_name']  = $state_name;

                    if(!empty($details_arr) && count($details_arr) > 0){
                        $result_arr         = $details_arr;
                        $http_response      = 'http_response_ok';
                        $success_message    = 'Get Product detail';  
                    } else {
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
                $http_response = "http_response_bad_request";
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }


    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : oneTimeCalcInput()
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
    function oneTimeCalcInput_post(){   
    
        $error_message = $success_message = $http_response = '';
        $result_arr = array();

        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {
            $details_arr = array();
            $AIR = 0;
            $AIR = $this->post('air', true);

            $details_arr = $this->calculation->oneTimeCalcInput($AIR);

            if(!empty($details_arr) && count($details_arr) > 0){
                $result_arr         = $details_arr;
                $http_response      = 'http_response_ok';
                $success_message    = 'Get Product detail';  
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = 'Something went wrong in API';  
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }


    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : oneTimeCalcDisbursement()
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
    function oneTimeCalcDisbursement_post(){   
    
        $error_message = $success_message = $http_response = '';
        $result_arr = array();

        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {
            $details_arr = array();
            $P      = $LPFP = $UFP = 0;            

            $P      = $this->post('principal', true);
            $LPFP   = $this->post('lpfp', true);
            $UFP    = $this->post('ufp', true);

            $details_arr = $this->calculation->oneTimeCalcDisbursement($P, $LPFP, $UFP);

            if(!empty($details_arr) && count($details_arr) > 0){
                $result_arr         = $details_arr;
                $http_response      = 'http_response_ok';
                $success_message    = 'Get Product detail';  
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = 'Something went wrong in API';  
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }
    
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : oneTimeCalcLenderFee()
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
    function oneTimeCalcLenderFee_post(){   
    
        $error_message = $success_message = $http_response = '';
        $result_arr = array();

        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {
            $details_arr = array();
            $P      = $LFR = $NPM = $MIR = $TST = 0;

            $P      = $this->post('principal', true);
            $LFR    = $this->post('lfr', true);
            $NPM    = $this->post('npm', true);
            $MIR    = $this->post('mir', true);
            $TST    = $this->post('tst', true);

            $details_arr = $this->calculation->oneTimeCalcLenderFee($P, $LFR, $NPM, $MIR, $TST);

            if(!empty($details_arr) && count($details_arr) > 0){
                $result_arr         = $details_arr;
                $http_response      = 'http_response_ok';
                $success_message    = 'Get Product detail';  
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = 'Something went wrong in API';  
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }


    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : oneTimePenaltyCalc()
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
    function oneTimePenaltyCalc_post(){   
    
        $error_message = $success_message = $http_response = '';
        $result_arr = array();

        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {
            $details_arr = array();
            $P      = $PFPD = $DPD = $PPRM = $TST = $RA = $LFA = 0;

            $P      = $this->post('principal', true);
            $PFPD   = $this->post('pfpd', true);
            $DPD    = $this->post('dpd', true);
            $PPRM   = $this->post('pprm', true);
            $TST    = $this->post('tst', true);
            $RA     = $this->post('ra', true);
            $LFA    = $this->post('lfa', true);
            
            $details_arr = $this->calculation->oneTimePenaltyCalc($P, $PFPD, $DPD, $PPRM, $TST, $RA, $LFA);
            
            if(!empty($details_arr) && count($details_arr) > 0){
                $result_arr         = $details_arr;
                $http_response      = 'http_response_ok';
                $success_message    = 'Get Product detail';  
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = 'Something went wrong in API';  
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }


    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : emiCalcInput()
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
    function emiCalcInput_post(){   
    
        $error_message = $success_message = $http_response = '';
        $result_arr = array();

        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {
            $details_arr = array();
            $P = $AIR = 0;
            $NPM = $LTY = 1;

            $P      = $this->post('principal', true);
            $AIR    = $this->post('air', true);
            $NPM    = $this->post('npm', true);
            $LTY    = $this->post('lty', true);

            $details_arr = $this->calculation->emiCalcInput($P, $AIR, $NPM, $LTY);

            if(!empty($details_arr) && count($details_arr) > 0){
                $result_arr         = $details_arr;
                $http_response      = 'http_response_ok';
                $success_message    = 'Get Product detail';  
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = 'Something went wrong in API';  
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }
    

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : emiCalcDisbursement()
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
    function emiCalcDisbursement_post(){   
    
        $error_message = $success_message = $http_response = '';
        $result_arr = array();

        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {
            $details_arr = array();
            $P      = $LPFP = $UFP = 0;

            $P      = $this->post('principal', true);
            $LPFP   = $this->post('lpfp', true);
            $UFP    = $this->post('ufp', true);

            $details_arr = $this->calculation->emiCalcDisbursement($P, $LPFP, $UFP);
            
            if(!empty($details_arr) && count($details_arr) > 0){
                $result_arr         = $details_arr;
                $http_response      = 'http_response_ok';
                $success_message    = 'Get Product detail';  
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = 'Something went wrong in API';  
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }

    
    /*
     * -----------------------------------------------------------------
     * @ Function Name            : emiCalcLenderFee()
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
    function emiCalcLenderFee_post(){   
    
        $error_message = $success_message = $http_response = '';
        $result_arr = array();

        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {
            $details_arr = array();
            $EMI      = $LFR = $TST = 0;

            $EMI    = $this->post('emi', true);
            $LFR    = $this->post('lfr', true);
            $TST    = $this->post('tst', true);

            $details_arr = $this->calculation->emiCalcLenderFee($EMI, $LFR, $TST);

            if(!empty($details_arr) && count($details_arr) > 0){
                $result_arr         = $details_arr;
                $http_response      = 'http_response_ok';
                $success_message    = 'Get Product detail';  
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = 'Something went wrong in API';  
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }


    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : emiPenaltyCalc()
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
    function emiPenaltyCalc_post(){   
    
        $error_message = $success_message = $http_response = '';
        $result_arr = array();

        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $details_arr = array();      
            $OBFPF  = $PFPD = $DPD = $PPRM = $TST = $EMI = $LFA = 0;

            $OBFPF  = $this->post('obfpf', true);            
            $PFPD   = $this->post('pfpd', true);
            $DPD    = $this->post('dpd', true);
            $PPRM   = $this->post('pprm', true);
            $TST    = $this->post('tst', true);
            $EMI    = $this->post('emi', true);
            $LFA    = $this->post('lfa', true);
            $OUTSTND_BAL    = $this->post('outstnd_bal', true);
            
            $details_arr = $this->calculation->emiPenaltyCalc($OBFPF, $PFPD, $DPD, $PPRM, $TST, $EMI, $LFA, $OUTSTND_BAL);         
                         
            if(!empty($details_arr) && count($details_arr) > 0){
                $result_arr         = $details_arr;
                $http_response      = 'http_response_ok';
                $success_message    = 'Get Product detail';  
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = 'Something went wrong in API';  
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }



    /*
     * -----------------------------------------------------------------
     * @ Function Name            : editPaymentInterest()
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
    function editPaymentInterest_post(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();

        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {            
            //pre($this->post(),1);
            $req_arr    = array();
            $flag       = true;
            if(empty($this->post('pass_key', true)))
            {
                $flag           = false;
                $error_message  = "Pass Key is required";
            }
            else
            {
                $req_arr['pass_key']          = $this->post('pass_key', true);
            }

            if($flag && empty($this->post('admin_user_id', true)))
            {
                $flag           = false;
                $error_message  = "Admin User Id is required";
            }
            else
            {
                $req_arr['admin_user_id']     = $this->post('admin_user_id', true);
            }

            if($flag && empty($this->post('input_principle', true)))
            {
                $flag           = false;
                $error_message  = "Principal is required";
            }
            else
            {
                $req_arr['input_principle']     = $this->post('input_principle', true);
            }

            if($flag && empty($this->post('input_air', true)))
            {
                $flag           = false;
                $error_message  = "Annual Interest Rate is required";
            }
            else
            {
                $req_arr['input_air']     = $this->post('input_air', true);
            }

            if($flag && empty($this->post('input_npm', true)))
            {
                $flag           = false;
                $error_message  = "Payments Per Year is required";
            }
            else
            {
                $req_arr['input_npm']     = $this->post('input_npm', true);
            }

            if($flag && empty($this->post('input_lpfp', true)))
            {
                $flag           = false;
                $error_message  = "LPFP is required";
            }
            else
            {
                $req_arr['input_lpfp']     = $this->post('input_lpfp', true);
            }

            if($flag && empty($this->post('input_ufp', true)))
            {
                $flag           = false;
                $error_message  = "UFP is required";
            }
            else
            {
                $req_arr['input_ufp']     = $this->post('input_ufp', true);
            }

            if($flag && empty($this->post('input_lfr', true)))
            {
                $flag           = false;
                $error_message  = "LFR is required";
            }
            else
            {
                $req_arr['input_lfr']     = $this->post('input_lfr', true);
            }

            if($flag && empty($this->post('input_pfpd', true)))
            {
                $flag           = false;
                $error_message  = "PFPD is required";
            }
            else
            {
                $req_arr['input_pfpd']     = $this->post('input_pfpd', true);
            }

            if($flag && empty($this->post('input_pprm', true)))
            {
                $flag           = false;
                $error_message  = "PPRM is required";
            }
            else
            {
                $req_arr['input_pprm']     = $this->post('input_pprm', true);
            }

            /*if($flag && empty($this->post('input_emi_lty', true)))
            {
                $flag           = false;
                $error_message  = "Loan Term is required";
            }
            else
            {
                $req_arr['input_emi_lty']     = $this->post('input_emi_lty', true);
            }

            if($flag && empty($this->post('input_emi_obfpf', true)))
            {
                $flag           = false;
                $error_message  = "OBFPF is required";
            }
            else
            {
                $req_arr['input_emi_obfpf']     = $this->post('input_emi_obfpf', true);
            }*/


            //if($this->post('input_emi_lty', true)){
                $req_arr['input_emi_lty']     = $this->post('input_emi_lty', true);
            //}

            //if($this->post('input_emi_obfpf', true)){
                $req_arr['input_emi_obfpf']     = $this->post('input_emi_obfpf', true);
            //}

            $req_arr['is_available']    = $this->post('is_available', true);
            $req_arr['calc_mir']        = $this->post('calc_mir', true);
            $req_arr['calc_emi_amount'] = $this->post('calc_emi_amount', true);
            $req_arr['calc_emi_tp']     = $this->post('calc_emi_tp', true);
            $req_arr['calc_arl']        = $this->post('calc_arl', true);
            $req_arr['calc_lpfa']       = $this->post('calc_lpfa', true);
            $req_arr['calc_ufa']        = $this->post('calc_ufa', true);
            $req_arr['calc_tst']        = $this->post('calc_tst', true);
            $req_arr['calc_stufa']      = $this->post('calc_stufa', true);
            $req_arr['calc_rufa']       = $this->post('calc_rufa', true);
            $req_arr['calc_tfdb']       = $this->post('calc_tfdb', true);
            $req_arr['calc_da']         = $this->post('calc_da', true);
            $req_arr['calc_lfa']        = $this->post('calc_lfa', true);
            $req_arr['calc_stlf']       = $this->post('calc_stlf', true);
            $req_arr['calc_rlf']        = $this->post('calc_rlf', true);
            $req_arr['calc_ra']         = $this->post('calc_ra', true);

            if($flag)
            {
                $product_master_arr = $product_variant_arr = array();
                $product_master_arr['is_available'] = ($req_arr['is_available'] == 'true') ? 1 : 0;
                $product_master_arr['id']           = $this->post('id', TRUE);
                //pre($product_master_arr);
                $product_id = $this->product->editProduct($product_master_arr); 
                //pre($last_id,1);

                $product_variant_arr['fk_product_id']   = $product_id;
                $product_variant_arr['input_principle'] = $req_arr['input_principle'];
                $product_variant_arr['input_air']       = $req_arr['input_air'];
                $product_variant_arr['input_npm']       = $req_arr['input_npm'];
                $product_variant_arr['input_lpfp']      = $req_arr['input_lpfp'];
                $product_variant_arr['input_ufp']       = $req_arr['input_ufp'];
                $product_variant_arr['input_lfr']       = $req_arr['input_lfr'];
                $product_variant_arr['input_pfpd']      = $req_arr['input_pfpd'];
                $product_variant_arr['input_pprm']      = $req_arr['input_pprm'];
                $product_variant_arr['input_emi_lty']   = $req_arr['input_emi_lty'];
                $product_variant_arr['input_emi_obfpf'] = $req_arr['input_emi_obfpf'];
                $product_variant_arr['calc_mir']        = $req_arr['calc_mir'];
                $product_variant_arr['calc_emi_amount'] = $req_arr['calc_emi_amount'];
                $product_variant_arr['calc_emi_tp']     = $req_arr['calc_emi_tp'];
                $product_variant_arr['calc_arl']        = $req_arr['calc_arl'];
                $product_variant_arr['calc_lpfa']       = $req_arr['calc_lpfa'];
                $product_variant_arr['calc_ufa']        = $req_arr['calc_ufa'];
                $product_variant_arr['calc_tst']        = $req_arr['calc_tst'];
                $product_variant_arr['calc_stufa']      = $req_arr['calc_stufa'];
                $product_variant_arr['calc_rufa']       = $req_arr['calc_rufa'];
                $product_variant_arr['calc_tfdb']       = $req_arr['calc_tfdb'];
                $product_variant_arr['calc_da']         = $req_arr['calc_da'];
                $product_variant_arr['calc_lfa']        = $req_arr['calc_lfa'];
                $product_variant_arr['calc_stlf']       = $req_arr['calc_stlf'];
                $product_variant_arr['calc_rlf']        = $req_arr['calc_rlf'];
                $product_variant_arr['calc_ra']         = $req_arr['calc_ra'];

                //pre($product_variant_arr,1);
                $last_id = $this->product->editProductVariant($product_variant_arr); 

                if($last_id > 0){
                    $data = array(
                        'id' => $product_id,
                    );
                    $result_arr         = $data;
                    $http_response      = 'http_response_ok';
                    $success_message    = 'Payment & Interest updated successfully'; 

                } else {
                    $http_response      = 'http_response_bad_request';
                    $error_message      = 'Something went wrong in API';  
                }
            }
            else
            {
                $http_response = "http_response_bad_request";
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }


    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : editCoinEarning()
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
    function editCoinEarning_post(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();

        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {  

            $req_arr = array();
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

            if($flag && empty($this->post('admin_user_id', true)))
            {
                $flag           = false;
                $error_message  = "Admin User Id is required";
            }
            else
            {
                $req_arr['admin_user_id']   = $this->post('admin_user_id', true);
            }

            if($flag && empty($this->post('id', true)))
            {
                $flag           = false;
                $error_message  = "Product Id is required";
            }
            else
            {
                $req_arr['id']   = $this->post('id', true);
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
                    $coin_earning_arr = json_decode($this->post('coin_earning_str'), TRUE);
                    //pre($coin_earning_arr,1);

                    $product_master_arr = $product_variant_arr = array();
                    $product_master_arr['is_available']        = ($this->post('is_available', TRUE) == true ) ? 1 : 0;
                    $product_master_arr['id']                  = $this->post('id', TRUE);
                    $product_id = $this->product->editProduct($product_master_arr); 

                    $master_coin_earning_data = array();
                    foreach($coin_earning_arr as $key => $value)
                    {
                        $coin_earning_update_arr = array();
                        $coin_earning_update_arr['id']                       = $value['id'];
                        $coin_earning_update_arr['non_referred_connections'] = $value['non_referred_connections'];
                        $coin_earning_update_arr['referred_connections']     = $value['referred_connections'];
                        $coin_earning_update_arr['non_referred_connections_limit'] = $value['non_referred_connections_limit'];
                        $coin_earning_update_arr['referred_connections_limit'] = $value['referred_connections_limit'];
                        $coin_earning_update_arr['own_activity'] = $value['own_activity'];
                        $master_coin_earning_data[] = $coin_earning_update_arr;
                    }
                    $update=$this->product->batchUpdateCoinEarning($master_coin_earning_data,'id');
                    $data = array(
                        'id' => $product_id,
                    );
                    $result_arr         = $data;
                    $http_response      = 'http_response_ok';
                    $success_message    = 'Activity Coin Earning updated successfully'; 
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
     * @ Function Name            : editRewards()
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
    function editRewards_post(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();

        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {  

            $req_arr = $details_arr = array();
            $flag       = true;
            if(empty($this->post('pass_key', true)))
            {
                $flag           = false;
                $error_message  = "Pass Key is required";
            }
            else
            {
                $req_arr['pass_key']        = $this->post('pass_key', true);
            }

            if($flag && empty($this->post('admin_user_id', true)))
            {
                $flag           = false;
                $error_message  = "Admin User Id is required";
            }
            else
            {
                $req_arr['admin_user_id']   = $this->post('admin_user_id', true);
            }

            if($flag && empty($this->post('id', true)))
            {
                $flag           = false;
                $error_message  = "Product Id is required";
            }
            else
            {
                $req_arr['id']              = $this->post('id', true);
            }
            $req_arr['rewards_str']     = $this->post('rewards_str', true);
            $req_arr['is_available']    = $this->post('is_available', true);

            if($flag)
            {
                $check_user = array(
                    'pass_key'      => $this->encrypt->decode($req_arr['pass_key']),
                    'admin_user_id' => $this->encrypt->decode($req_arr['admin_user_id']),
                );
                $checkloginstatus = $this->admin->checkSessionExist($check_user);
                if(!empty($checkloginstatus) && count($checkloginstatus) > 0)
                {
                    $rewards_arr = json_decode($req_arr['rewards_str'], TRUE);

                    $product_master_arr = $product_variant_arr = array();
                    $product_master_arr['is_available']   = ($req_arr['is_available']==true)?1:0;
                    $product_master_arr['id']             = $this->post('id', TRUE);
                    $product_id = $this->product->editProduct($product_master_arr); 

                    $master_rewards_data = array();
                    foreach($rewards_arr as $key => $value){
                        $rewards_update_arr = array();
                        $rewards_update_arr['id']           = $value['id'];
                        $rewards_update_arr['reward_point'] = $value['reward_point'];

                        $master_rewards_data[] = $rewards_update_arr;
                    }

                    $flag = $this->product->batchUpdateRewards($master_rewards_data, 'id');

                    $data = array(
                        'id' => $product_id,
                    );
                    $result_arr         = $data;
                    $http_response      = 'http_response_ok';
                    $success_message    = 'Credit Rating Benefits updated successfully';
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
     * @ Function Name            : deleteCreditRatingBenefits()
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
    public function deleteCreditRatingBenefits_post(){   
    
        $error_message = $success_message = $http_response = '';
        $result_arr = array();

        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {
            $req_arr = $details_arr = array();
            $flag           = true;
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

            if(empty($this->post('creditRatingBenefitsId', true)))
            {
                $flag           = false;
                $error_message  = "Credit Rating Benefits Id is required";
            }
            else
            {
                $req_arr['id']            = $this->post('creditRatingBenefitsId', true);
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

                    $details_arr = $this->product->deleteCreditRatingBenefits($req_arr);

                    if(!empty($details_arr) && count($details_arr) > 0){
                        //$result_arr         = $details_arr;
                        $http_response      = 'http_response_ok';
                        $success_message    = 'Credit Rating Benefits deleted successfully';  
                    } else {
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
                $http_response      = 'http_response_bad_request';
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    } 




    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : addCreditRatingBenefits()
     * @ Added Date               : 14-04-2016
     * @ Added By                 : Subhankar
     * -----------------------------------------------------------------
     * @ Description              : add Credit Rating Benefits
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 14-04-2016
     * @ Modified By              : Subhankar
     * 
    */
    public function addCreditRatingBenefits_post(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();

        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {
            $req_arr = $details_arr = array();
            $flag    = true;
            if(empty($this->post('pass_key', true)))
            {
                $flag           = false;
                $error_message  = "Pass Key is required";
            }
            else
            {
                $req_arr['pass_key']             = $this->post('pass_key', true);
            }

            if($flag && empty($this->post('admin_user_id', true)))
            {
                $flag           = false;
                $error_message  = "Admin User Id is required";
            }
            else
            {
                $req_arr['admin_user_id']        = $this->post('admin_user_id', true);
            }

            if($flag && empty($this->post('fk_product_id', true)))
            {
                $flag           = false;
                $error_message  = "Product Id is required";
            }
            else
            {
                $req_arr['fk_product_id']        = $this->post('fk_product_id', true);
            }

            if($flag && empty($this->post('credit_rating_from', true)))
            {
                $flag           = false;
                $error_message  = "Credit Rating From is required";
            }
            else
            {
                $req_arr['credit_rating_from']   = $this->post('credit_rating_from', true);
            }

            if($flag && empty($this->post('credit_rating_to', true)))
            {
                $flag           = false;
                $error_message  = "Credit Rating To is required";
            }
            else
            {
                $req_arr['credit_rating_to']     = $this->post('credit_rating_to', true);
            }

            if($flag && empty($this->post('interest_adjustment', true)))
            {
                $flag           = false;
                $error_message  = "Interest Adjustment is required";
            }
            else
            {
                $req_arr['interest_adjustment']  = $this->post('interest_adjustment', true);
            }

            if($flag && empty($this->post('usage_fee_discount', true)))
            {
                $flag           = false;
                $error_message  = "Usage Fee Discount is required";
            }
            else
            {
                $req_arr['usage_fee_discount']  = $this->post('usage_fee_discount', true);
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

                    $product_credit_rating_benefits_arr = array();
                    $product_credit_rating_benefits_arr['fk_product_id']      = $req_arr['fk_product_id'];
                    $product_credit_rating_benefits_arr['credit_rating_from'] = $req_arr['credit_rating_from'];
                    $product_credit_rating_benefits_arr['credit_rating_to']   = $req_arr['credit_rating_to'];
                    $product_credit_rating_benefits_arr['interest_adjustment']= $req_arr['interest_adjustment'];
                    $product_credit_rating_benefits_arr['usage_fee_discount'] = $req_arr['usage_fee_discount'];
                    $product_credit_rating_benefits_arr['fk_admin_id']        = $check_user['admin_user_id'];

                    //pre($product_credit_rating_benefits_arr,1);

                    $last_id = $this->product->addProductCreditRatingBenefits($product_credit_rating_benefits_arr); 

                    if($last_id > 0)
                    {
                        $data = array(
                            'product_id'            => $product_credit_rating_benefits_arr['fk_product_id'],
                            'credit_rating_from'    => $product_credit_rating_benefits_arr['credit_rating_from'],
                            'credit_rating_to'      => $product_credit_rating_benefits_arr['credit_rating_to'],
                            'interest_adjustment'   => $product_credit_rating_benefits_arr['interest_adjustment'],
                            'usage_fee_discount'    => $product_credit_rating_benefits_arr['usage_fee_discount'],
                            'id'                    => $last_id,
                        );
                        $result_arr         = $data;
                        $http_response      = 'http_response_ok';
                        $success_message    = 'Credit Rating Benefits added successfully'; 

                    } else {
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
                $http_response      = 'http_response_bad_request';
            }
        }
        json_response($result_arr, $http_response, $error_message, $success_message);
    }


    /*
     * -----------------------------------------------------------------
     * @ Function Name            : editTierBenefits()
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
    function editTierBenefits_post(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();

        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {  

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

            if($flag && empty($this->post('id', true)))
            {
                $flag           = false;
                $error_message  = "Page is required";
            }
            else
            {
                $req_arr['id']            = $this->post('id', true);
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
                    $tier_benefits_arr = json_decode($this->post('tier_benefits_str'), TRUE);

                    $product_master_arr = $product_variant_arr = array();
                    $product_master_arr['is_available']        = ($this->post('is_available', TRUE) == true ) ? 1 : 0;
                    $product_master_arr['id']                  = $this->post('id', TRUE);
                    $product_id = $this->product->editProduct($product_master_arr); 
                    //pre($tier_benefits_arr,1);

                    $master_tier_benefits_data = array();
                    foreach($tier_benefits_arr as $key => $value){
                        $tier_benefits_update_arr = array();
                        $tier_benefits_update_arr['id']                         = $value['id'];
                        $tier_benefits_update_arr['usage_fee_discount_amount']  = $value['usage_fee_discount_amount'];
                        $tier_benefits_update_arr['interest_adjustment']        = $value['interest_adjustment'];

                        $master_tier_benefits_data[] = $tier_benefits_update_arr;
                    }

                    $flag=$this->product->batchUpdateTierBenefits($master_tier_benefits_data,'id');

                    $data = array(
                        'id' => $product_id,
                    );
                    $result_arr         = $data;
                    $http_response      = 'http_response_ok';
                    $success_message    = 'Tier Benefits updated successfully';
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


    /****************************end of admin controlller**********************/

}
