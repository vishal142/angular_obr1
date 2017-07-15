<?php defined('BASEPATH') OR exit('No direct script access allowed');

error_reporting(0);
//error_reporting(E_ALL);

require_once('src/OAuth2/Autoloader.php');
require APPPATH . 'libraries/api/REST_Controller.php';
require APPPATH . '/libraries/api/AppExtrasAPI.php';
require APPPATH . '/libraries/api/AppAndroidGCMPushAPI.php';
require APPPATH . '/libraries/api/AppApplePushAPI.php';


    /**
    *   @SWG\Swagger(
    *       schemes={"https"},
    *       host="apis.finzo.in",
    *       basePath="/api_1_0_0"
    *   ),
    *
    *   @SWG\Info(
    *       title="Connection",
    *       description="all api which are related to profiles screen are listed here",
    *       version="2.0.0"
    *   ),
    *   @SWG\Definition(
    *       definition="getNoConnections",
    *       type="object",
    *       description="API Request Format",
    *       allOf={
    *           @SWG\Schema(
    *               @SWG\Property(property="user_id", format="string", type="integer"),
    *               @SWG\Property(property="pass_key", format="string", type="string"),
    *             
    *          )
    *       },
    *    )
    *   @SWG\Definition(
    *       definition="getProduct",
    *       type="object",
    *       description="API Request Format",
    *       allOf={
    *           @SWG\Schema(
    *               @SWG\Property(property="user_id", format="string", type="integer"),
    *               @SWG\Property(property="pass_key", format="string", type="string"),
    *               @SWG\Property(property="fk_profession_type_id", format="string", type="integer"),
    *               @SWG\Property(property="fk_payment_type_id", format="string", type="integer"),
    *               @SWG\Property(property="product_type", format="string", type="string"),
    *             
    *          )
    *       },
    *    )

  **/

class Transaction extends REST_Controller{
     function __construct()
    {

        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: authorization, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header('Access-Control-Allow-Credentials: true');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if($method == "OPTIONS") {
         die();
        }

            parent::__construct();
            $this->load->config('rest');
            $this->load->config('serverconfig');
            $developer = 'www.massoftind.com';
            $this->app_path = "api_" . $this->config->item('test_api_ver');
            //publish app version
            $version = str_replace('_', '.', $this->config->item('test_api_ver'));

            $this->publish = array(
                'version' => $version,
                'developer' => $developer
            );
            
            //echo $_SERVER['SERVER_ADDR']; exit;
            
            $dsn = 'mysql:dbname='.$this->config->item('oauth_db_database').';host='.$this->config->item('oauth_db_host');
            $dbusername = $this->config->item('oauth_db_username');
            $dbpassword = $this->config->item('oauth_db_password');
           

            $sitemode= $this->config->item('site_mode');
            $this->path_detail=$this->config->item($sitemode);      
            
            $this->load->model('api_' . $this->config->item('test_api_ver') . '/user_model', 'user_model');
            $this->load->model('api_' . $this->config->item('test_api_ver') . '/profile_model', 'profile_model');
            $this->load->model('api_' . $this->config->item('test_api_ver') . '/login_model', 'login_model');
            $this->load->model('api_' . $this->config->item('test_api_ver') . '/product_model', 'product_model');
            $this->load->model('api_' . $this->config->item('test_api_ver') . '/agentcode_model', 'agentcode_model');
            $this->load->model('api_' . $this->config->item('test_api_ver') . '/agent_model', 'agent_model');

            $this->load->model('api_' . $this->config->item('test_api_ver') . '/notifications_model', 'notifications_model');
            $this->load->model('api_' . $this->config->item('test_api_ver') . '/connection_model', 'connection_model');
            $this->load->model('api_' . $this->config->item('test_api_ver') . '/mcoins_model', 'mcoins_model');

            
            
            $this->load->library('email');
            $this->load->library('calculation');
            $this->load->library('excel_reader/PHPExcel');
            
            $this->load->library('excel_reader/PHPExcel/iofactory');
           
            $this->push_type = 'P';
            $this->load->library('mpdf');

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



    /**
     *  
     *   @SWG\Post(
     *      path="/connection/getProduct",
     *      tags={"getProduct: "},
     *      summary="get product list ",
     *      description="This api is used to get product price",
     *      produces={"application/json"},
     *   
     *      @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          description="Authorization Token",
     *          required=true,
     *          type="string",
     *      ),
     *      @SWG\Parameter(
     *          name="data",
     *          in="body",
     *          description="Post data to change password",
     *          required=true,
     *          type="string",
     *          @SWG\Schema(ref="#/definitions/getProduct"),
     *      ),       
     *  )
     *
    **/ 


    public function getProductDisbursed_post(){
       
        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('pass_key')){
                $flag       = false;
                $error_message='passkey can not be null';
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }
            if (!intval($this->post('user_id'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

           

            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                //echo $this->db->last_query(); //exit;
                //pre($login_status_arr,1);

                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                       $product_list=$this->product_model->getProductDisbursed();

                        if(is_array($product_list) && count($product_list)>0){
                            $result_arr['product_list']=$product_list;
                        }else{
                            $result_arr['product_list']=array();
                        }

                        $http_response      = 'http_response_ok';
                        $success_message    = '';  
                   
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Invalid user details'; 
                }
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
            }
        }

        $raws = array();   
        if($error_message != ''){
            $raws['error_message']      = $error_message;
        } else{
            $raws['success_message']    = $success_message;
        }        

        $raws['data']       = $result_arr;
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 
    }

    public function getProductDisbursedNPM_post(){
       
        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('pass_key')){
                $flag       = false;
                $error_message='passkey can not be null';
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }
            if (!intval($this->post('user_id'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

            if (!intval($this->post('amount'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['amount']    = $this->post('amount', TRUE);
            }

           

            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                //echo $this->db->last_query(); //exit;
                //pre($login_status_arr,1);

                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                       $product_list=$this->product_model->getProductDisbursedNPM($req_arr);

                        if(is_array($product_list) && count($product_list)>0){
                            $result_arr['product_list']=$product_list;
                        }else{
                            $result_arr['product_list']=array();
                        }
                        
                        $http_response      = 'http_response_ok';
                        $success_message    = '';  
                   
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Invalid user details'; 
                }
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
            }
        }

        $raws = array();   
        if($error_message != ''){
            $raws['error_message']      = $error_message;
        } else{
            $raws['success_message']    = $success_message;
        }        

        $raws['data']       = $result_arr;
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 
    }

    public function getProductCalDtl_post(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('pass_key')){
                $flag       = false;
                $error_message='passkey can not be null';
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }

            if (!intval($this->post('user_id'))){
                $flag       = false;
                $error_message='user_id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

            
            if (!$this->post('product_varrient_id')){
                $flag       = false;
                $error_message='product_varrient_id can not be null';
            } else {
                $req_arr['product_varrient_id']    = $this->post('product_varrient_id', TRUE);
            }

            


            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                //echo $this->db->last_query(); //exit;
                //pre($login_status_arr,1);

                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                        
                        $details=$this->product_model->getProductVarientDtl($req_arr);
                        
                            //preset calculation
                            if(is_array($details) && count($details)>0){
                                $i=0;
                                    $product_details['input_principle']=$details['input_principle'];
                                    $AIR=$details['input_air'];
                                    $NPM=$details['input_npm'];
                                    $LPFP=$details['input_lpfp'];
                                    $UFP=$details['input_ufp'];
                                    $LFR=$details['input_lfr'];
                                    $PPRM=$details['input_pprm'];
                                    $LTY=$details['input_emi_lty'];
                                    $OBFPF=$details['input_emi_obfpf'];

                                    // get user olevel
                                    $userLevel=$this->user_model->getUserLevel($req_arr);
                                    if(is_array($userLevel) && count($userLevel)>0){

                                        $ties['mcoins_user_level']=$userLevel['fk_mcoin_level_id'];
                                        $ties['fk_product_id']=$details['fk_product_id'];
                                        $tierDiscountDtl=$this->product_model->getTierUsageFeeDiscount($ties);

                                        //get usgae fee discount & interest adjustment
                                      
                                        $product_details['discount']=$tierDiscountDtl['usage_fee_discount_amount'];
                                        $product_details['interest_adjustment']=$tierDiscountDtl['interest_adjustment'];
                                       

                                     }else{
                                        $product_details['discount']=0;
                                        $product_details['interest_adjustment']=0;
                                    }
                                    // get customer adjustment
                                    $customAdjustment=$this->product_model->getCustomAdjustment($req_arr);

                                    if(is_array($customAdjustment) && count($customAdjustment)>0){
                                        $product_details['custom_discount']=$customAdjustment['usage_fee_discount_amount'];
                                        $product_details['custom_interest_adjustment']=$customAdjustment['interest_adjustment'];
                                    }else{
                                        $product_details['custom_discount']=0;
                                        $product_details['custom_interest_adjustment']=0;
                                    }

                                    $product_details['total_discount']=$product_details['discount']+$product_details['custom_discount'];

                                    $product_details['total_interest_adjustment']=$product_details['interest_adjustment']+$product_details['custom_interest_adjustment'];

                                    //calculate AIR after interest adjustment
                                    if($product_details['total_interest_adjustment']>0){
                                        $AIR=$this->calculation->getMIRInterestAdjustment($AIR,$product_details['total_interest_adjustment']);
                                        
                                    }
                                    


                                    $product_details['prod_varient_id']=$details['id'];

                                        $product_details['fk_product_id']=$details['fk_product_id'];
                                    $product_details['input_npm']=$details['input_npm'];
                                    $product_details['calc_da']=$details['calc_da'];
                                    $product_details['calc_tfdb']=$details['calc_tfdb'];
                                   
                                    $product_details['calc_ra']=$details['calc_ra'];
                                   
                                    $product_details['air']=$AIR;
                                  
                                   
                                    $product_details['total_da']=$details['calc_da']+$product_details['total_discount'];

                                
                                    $i++;

                               

                                $result_arr['product_details']=$product_details;

                                $tiesDetails=$this->product_model->getTierUsageFeeDiscountDtl($details);

                                $result_arr['product_tier']=$tiesDetails;
                                $result_arr['user_tier_level']=$userLevel['fk_mcoin_level_id'];
                                $result_arr['user_tier_level_discount']=$product_details['discount'];
                                $result_arr['user_custom_discount']=$product_details['custom_discount'];

                              




                            }else{
                                $result_arr['product_details']=array();
                            }
                   

                        $http_response      = 'http_response_ok';
                        $success_message    = '';  
                   
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Invalid user details'; 
                }
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
            }
        }

        $raws = array();   
        if($error_message != ''){
            $raws['error_message']      = $error_message;
        } else{
            $raws['success_message']    = $success_message;
        }        

        $raws['data']       = $result_arr;
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 
    }

    public function getProductMaster_post(){
        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        $flag           = true;

        if (!$this->post('product_id')){
            $flag       = false;
            $error_message='product id can not be null';
        } else {
            $req_arr['product_id']    = $this->post('product_id', TRUE);
        }
         $flag           = true;
        $data['fk_product_id']=$req_arr['product_id'];
       
        if($flag) {
                
                $product_list=$this->product_model->getProductDtl($data);

               if(is_array($product_list) && count($product_list)>0){
                    $result_arr['product_list']=$product_list;
                }else{
                    $result_arr['product_list']=array();
                }
                $http_response      = 'http_response_ok';
                $success_message    = '';  
                   
                
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
            }
      

        $raws = array();   
        if($error_message != ''){
            $raws['error_message']      = $error_message;
        } else{
            $raws['success_message']    = $success_message;
        }        

        $raws['data']       = $result_arr;
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        );

    }
    public function getProduct_post(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('pass_key')){
                $flag       = false;
                $error_message='passkey can not be null';
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }
            if (!intval($this->post('user_id'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

            if (!intval($this->post('fk_profession_type_id'))){
                $flag       = false;
                $error_message='fk_profession_type_id can not be null';
            } else {
                $req_arr['fk_profession_type_id']    = $this->post('fk_profession_type_id', TRUE);
            }

            if (!intval($this->post('fk_payment_type_id'))){
                $flag       = false;
                $error_message='fk_payment_type_id can not be null';
            } else {
                $req_arr['fk_payment_type_id']    = $this->post('fk_payment_type_id', TRUE);
            }

            $req_arr['product_type']    = $this->post('product_type', TRUE);

            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                //echo $this->db->last_query(); //exit;
                //pre($login_status_arr,1);

                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                        $product_list=$this->product_model->getProduct($req_arr);

                       if(is_array($product_list) && count($product_list)>0){
                            $result_arr['product_list']=$product_list;
                        }else{
                            $result_arr['product_list']=array();
                        }
                        $http_response      = 'http_response_ok';
                        $success_message    = '';  
                   
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Invalid user details'; 
                }
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
            }
        }

        $raws = array();   
        if($error_message != ''){
            $raws['error_message']      = $error_message;
        } else{
            $raws['success_message']    = $success_message;
        }        

        $raws['data']       = $result_arr;
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 
    }  


     
    public function getProductTenure_post(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('pass_key')){
                $flag       = false;
                $error_message='passkey can not be null';
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }

            if (!intval($this->post('user_id'))){
                $flag       = false;
                $error_message='user_id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

            if (!$this->post('product_id')){
                $flag       = false;
                $error_message='product_id can not be null';
            } else {
                $req_arr['fk_product_id']    = $this->post('product_id', TRUE);
            }


            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                //echo $this->db->last_query(); //exit;
                //pre($login_status_arr,1);

                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                       
                        $varrientDetails=$this->product_model->getProductVarient($req_arr);
                        
                            //preset calculation
                            if(is_array($varrientDetails) && count($varrientDetails)>0){
                               

                                $result_arr['product_details']=$varrientDetails;


                            }else{
                                $result_arr['product_details']=array();
                            }
                   

                        $http_response      = 'http_response_ok';
                        $success_message    = '';  
                   
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Invalid user details'; 
                }
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
            }
        }

        $raws = array();   
        if($error_message != ''){
            $raws['error_message']      = $error_message;
        } else{
            $raws['success_message']    = $success_message;
        }        

        $raws['data']       = $result_arr;
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 
    }

    /**
     *  
     *   @SWG\Post(
     *      path="/connection/getProductTenure",
     *      tags={"getProductTenure: "},
     *      summary="get product tenure",
     *      description="This api is used to get Product Tenure",
     *      produces={"application/json"},
     *   
     *      @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          description="Authorization Token",
     *          required=true,
     *          type="string",
     *      ),
     *      @SWG\Parameter(
     *          name="data",
     *          in="body",
     *          description="Post data to change password",
     *          required=true,
     *          type="string",
     *          @SWG\Schema(ref="#/definitions/getProductTenure"),
     *      ),       
     *  )
     *
    **/ 

    public function getProductTenureDtl_post(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('pass_key')){
                $flag       = false;
                $error_message='passkey can not be null';
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }

            if (!intval($this->post('user_id'))){
                $flag       = false;
                $error_message='user_id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

            if (!$this->post('product_id')){
                $flag       = false;
                $error_message='product_id can not be null';
            } else {
                $req_arr['fk_product_id']    = $this->post('product_id', TRUE);
            }

            if (!$this->post('fk_payment_type_id')){
                $flag       = false;
                $error_message='fk_payment_type_id can not be null';
            } else {
                $req_arr['fk_payment_type_id']    = $this->post('fk_payment_type_id', TRUE);
            }

            if (!$this->post('product_type')){
                $flag       = false;
                $error_message='product_type can not be null';
            } else {
                $req_arr['product_type']    = $this->post('product_type', TRUE);
            }

            if (!$this->post('product_varrient_id')){
                $flag       = false;
                $error_message='product_varrient_id can not be null';
            } else {
                $req_arr['product_varrient_id']    = $this->post('product_varrient_id', TRUE);
            }

             $req_arr['product_amount']    = $this->post('product_amount', TRUE);

             if($this->post('product_type')=='R' && intval($this->post('user_id'))<1){
                $flag       = false;
                $error_message='product_amount can not be null';
             }


            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                //echo $this->db->last_query(); //exit;
                //pre($login_status_arr,1);

                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                        $productDetails=$this->product_model->getProductDtl($req_arr);

                        $details=$this->product_model->getProductVarientDtl($req_arr);
                        
                            //preset calculation
                            if(is_array($details) && count($details)>0){
                                $i=0;
                                    $product_details['input_principle']=$details['input_principle'];
                                    $AIR=$details['input_air'];
                                    $NPM=$details['input_npm'];
                                    $LPFP=$details['input_lpfp'];
                                    $UFP=$details['input_ufp'];
                                    $LFR=$details['input_lfr'];
                                    $PPRM=$details['input_pprm'];
                                    $LTY=$details['input_emi_lty'];
                                    $OBFPF=$details['input_emi_obfpf'];
                                    // get user olevel
                                    $userLevel=$this->user_model->getUserLevel($req_arr);
                                    if(is_array($userLevel) && count($userLevel)>0){

                                        $ties['mcoins_user_level']=$userLevel['fk_mcoin_level_id'];
                                        $ties['fk_product_id']=$req_arr['fk_product_id'];
                                        $tierDiscountDtl=$this->product_model->getTierUsageFeeDiscount($ties);

                                        //get usgae fee discount & interest adjustment
                                      
                                        $product_details['discount']=$tierDiscountDtl['usage_fee_discount_amount'];
                                        $product_details['interest_adjustment']=$tierDiscountDtl['interest_adjustment'];
                                       

                                     }else{
                                        $product_details['discount']=0;
                                        $product_details['interest_adjustment']=0;
                                    }
                                    // get customer adjustment
                                    $customAdjustment=$this->product_model->getCustomAdjustment($req_arr);

                                    if(is_array($customAdjustment) && count($customAdjustment)>0){
                                        $product_details['custom_discount']=$customAdjustment['usage_fee_discount_amount'];
                                        $product_details['custom_interest_adjustment']=$customAdjustment['interest_adjustment'];
                                    }else{
                                        $product_details['custom_discount']=0;
                                        $product_details['custom_interest_adjustment']=0;
                                    }

                                    $product_details['total_discount']=$product_details['discount']+$product_details['custom_discount'];

                                    $product_details['total_interest_adjustment']=$product_details['interest_adjustment']+$product_details['custom_interest_adjustment'];

                                    //calculate AIR after interest adjustment
                                    if($product_details['total_interest_adjustment']>0){
                                        $AIR=$this->calculation->getMIRInterestAdjustment($AIR,$product_details['total_interest_adjustment']);
                                        
                                    }
                                    


                                    $product_details['prod_varient_id']=$details['id'];

                                    if($req_arr['product_type']=='P'){
                                        //if preset
                                        $product_details['input_principle']=$details['input_principle'];
                                        $P=$details['input_principle'];
                                    }else if($req_arr['product_type']=='R'){
                                        // if range
                                        $product_details['input_principle']=$req_arr['product_amount'];
                                        $P=$req_arr['product_amount'];

                                    }
                                       
                                      
                                        //calculation start
                                       
                                        if($productDetails['fk_payment_type_id']==1){
                                            //one time calculation
                                            $oneTimeCalcInput=$this->calculation->oneTimeCalcInput($AIR);
                                            $MIR=$oneTimeCalcInput['mir'];
                                            $oneTimeCalcDisbursement=$this->calculation->oneTimeCalcDisbursement($P,$LPFP, $UFP);
                                            $ARL=$oneTimeCalcDisbursement['arl'];
                                            $LPFA=$oneTimeCalcDisbursement['lpfa'];
                                            $UFA=$oneTimeCalcDisbursement['ufa'];
                                            $TST=$oneTimeCalcDisbursement['tst'];
                                            $STUFA=$oneTimeCalcDisbursement['stufa'];
                                            $RUFA=$oneTimeCalcDisbursement['rufa'];
                                            $TFDB=$oneTimeCalcDisbursement['tfdb'];
                                            $DA=$oneTimeCalcDisbursement['da'];
                                             $oneTimeCalcLenderFee=$this->calculation->oneTimeCalcLenderFee($P, $LFR, $NPM, $MIR, $TST);
                                            $LFA=$oneTimeCalcLenderFee['lfa'];
                                            $STLF=$oneTimeCalcLenderFee['stlf'];
                                            $RLF=$oneTimeCalcLenderFee['rlf'];
                                            $RA=$oneTimeCalcLenderFee['ra'];
                                            $PL=$oneTimeCalcLenderFee['pl'];
                                        }else{
                                            //emi calculation
                                             $emiCalcInput=$this->calculation->emiCalcInput($P, $AIR, $NPM, $LTY);
                                             $MIR=$emiCalcInput['mir'];
                                             $TP=$emiCalcInput['tp'];
                                              $emiCalcDisbursement=$this->calculation->emiCalcDisbursement($P, $LPFP, $UFP);
                                            $ARL=$emiCalcDisbursement['arl'];
                                            $LPFA=$emiCalcDisbursement['lpfa'];
                                            $UFA=$emiCalcDisbursement['ufa'];
                                            $TST=$emiCalcDisbursement['tst'];
                                            $STUFA=$emiCalcDisbursement['stufa'];
                                            $RUFA=$emiCalcDisbursement['rufa'];
                                            $TFDB=$emiCalcDisbursement['tfdb'];
                                            $DA=$emiCalcDisbursement['da'];

                                            $EMI=$this->calculation->pmt($MIR,$TP,$P);
                                             $emiCalcLenderFee=$this->calculation->emiCalcLenderFee($EMI, $LFR, $TST);
                                             $LFA=$emiCalcLenderFee['lfa'];
                                             $STLF=$emiCalcLenderFee['stlf'];
                                             $RLF=$emiCalcLenderFee['rlf'];
                                             $RA=$emiCalcLenderFee['ra'];
                                             $PL=$emiCalcLenderFee['pl'];


                                              

                                        }
                                        //calculation end
                                        
                                        $product_details['input_npm']=$NPM;
                                        $product_details['calc_da']=$DA;
                                        $product_details['calc_tfdb']=$TFDB;
                                        $product_details['calc_arl']=$ARL;
                                        $product_details['calc_lpfa']=$LPFA;
                                        $product_details['calc_ra']=$RA;
                                        //end of ranges



                                   // }
                                    // calcalulat dat after discount
                                    $product_details['air']=$AIR;
                                        $product_details['total_fees']=$product_details['calc_tfdb'];
                                      
                                        $product_details['discounted_fees']=$product_details['calc_tfdb']-$product_details['total_discount'];
                                        $product_details['total_da']=$product_details['input_principle']-$product_details['discount_fees'];

                                    
                                        $i++;

                               

                                $result_arr['product_details']=$product_details;


                            }else{
                                $result_arr['product_details']=array();
                            }
                   

                        $http_response      = 'http_response_ok';
                        $success_message    = '';  
                   
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Invalid user details'; 
                }
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
            }
        }

        $raws = array();   
        if($error_message != ''){
            $raws['error_message']      = $error_message;
        } else{
            $raws['success_message']    = $success_message;
        }        

        $raws['data']       = $result_arr;
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 
    }


    /**
     *  
     *   @SWG\Post(
     *      path="/connection/getProductTenure",
     *      tags={"getProductTenure: "},
     *      summary="get product tenure",
     *      description="This api is used to get Product Tenure",
     *      produces={"application/json"},
     *   
     *      @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          description="Authorization Token",
     *          required=true,
     *          type="string",
     *      ),
     *      @SWG\Parameter(
     *          name="data",
     *          in="body",
     *          description="Post data to change password",
     *          required=true,
     *          type="string",
     *          @SWG\Schema(ref="#/definitions/getProductTenure"),
     *      ),       
     *  )
     *
    **/ 

    public function getProductRepaymentsEmi_post(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('pass_key')){
                $flag       = false;
                $error_message='passkey can not be null';
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }

            if (!intval($this->post('user_id'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

            if (!$this->post('product_id')){
                $flag       = false;
                $error_message='product_id can not be null';
            } else {
                $req_arr['fk_product_id']    = $this->post('product_id', TRUE);
            }

             if (!$this->post('product_varrient_id')){
                $flag       = false;
                $error_message='product_id can not be null';
            } else {
                $req_arr['product_varrient_id']    = $this->post('product_varrient_id', TRUE);
            }

             if (!$this->post('product_amount')){
                $flag       = false;
                $error_message='product_id can not be null';
            } else {
                $req_arr['product_amount']    = $this->post('product_amount', TRUE);
            }

            


            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                //echo $this->db->last_query(); //exit;
                //pre($login_status_arr,1);

                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                        $details=$this->product_model->getProductVarientDtl($req_arr);

                        if(is_array($details) && count($details)>0){ 
                            $P=$req_arr['product_amount'];
                            $AIR=$details['input_air'];
                            $NPM=$details['input_npm'];
                            $LPFP=$details['input_lpfp'];
                            $UFP=$details['input_ufp'];
                            $LFR=$details['input_lfr'];
                            $PPRM=$details['input_pprm'];
                            $LTY=$details['input_emi_lty'];
                            $OBFPF=$details['input_emi_obfpf'];

                            $emiCalcInput=$this->calculation->emiCalcInput($P, $AIR, $NPM, $LTY);
                            $MIR=$emiCalcInput['mir'];
                            $TP=$emiCalcInput['tp'];
                              $emiCalcDisbursement=$this->calculation->emiCalcDisbursement($P, $LPFP, $UFP);
                            $EMI=$this->calculation->pmt($MIR,$TP,$P);
                            $emiCalcLenderFee=$this->calculation->emiCalcLenderFee($EMI, $LFR, $TST);
                            
                             $emi_cal_array=$this->calculation->emi_calculator($P, $TP, $EMI, $MIR);


                            $result_arr['emi_details']=$emi_cal_array;
                        }else{
                            $result_arr['emi_details']=array();
                        }

                        $http_response      = 'http_response_ok';
                        $success_message    = '';  
                   
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Invalid user details'; 
                }
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
            }
        }

        $raws = array();   
        if($error_message != ''){
            $raws['error_message']      = $error_message;
        } else{
            $raws['success_message']    = $success_message;
        }        

        $raws['data']       = $result_arr;
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 
    }

    /**
     *  
     *   @SWG\Post(
     *      path="/connection/addCashRequest",
     *      tags={"addCashRequest: "},
     *      summary="add Cash Request",
     *      description="This api is used to add Cash Request",
     *      produces={"application/json"},
     *   
     *      @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          description="Authorization Token",
     *          required=true,
     *          type="string",
     *      ),
     *      @SWG\Parameter(
     *          name="data",
     *          in="body",
     *          description="Post data to change password",
     *          required=true,
     *          type="string",
     *          @SWG\Schema(ref="#/definitions/addCashRequest"),
     *      ),       
     *  )
     *
    **/ 

    public function addCashRequest_post(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('pass_key')){
                $flag       = false;
                $error_message='passkey can not be null';
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }

            if (!intval($this->post('user_id'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

            if (!$this->post('product_id')){
                $flag       = false;
                $error_message='product_id can not be null';
            } else {
                $req_arr['fk_product_id']    = $this->post('product_id', TRUE);
            }

             if (!$this->post('product_varrient_id')){
                $flag       = false;
                $error_message='product_id can not be null';
            } else {
                $req_arr['product_varrient_id']    = $this->post('product_varrient_id', TRUE);
            }

            $req_arr['product_amount']    = $this->post('product_amount', TRUE);

             if (!$this->post('fk_profession_type_id')){
                $flag       = false;
                $error_message='product_id can not be null';
            } else {
                $req_arr['fk_profession_type_id']    = $this->post('fk_profession_type_id', TRUE);
            }

            if (!$this->post('fk_payment_type_id')){
                $flag       = false;
                $error_message='product_id can not be null';
            } else {
                $req_arr['fk_payment_type_id']    = $this->post('fk_payment_type_id', TRUE);
            }

            


            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                //echo $this->db->last_query(); //exit;
                //pre($login_status_arr,1);

                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                    $productDtl=$this->product_model->getProductDtl($req_arr);
                    $productVarientDtl=$this->product_model->getProductVarientDtl($req_arr);
                    $userLevel=$this->user_model->getUserLevel($req_arr);
                    $user_loans['fk_user_id']=$req_arr['user_id'];

                    if(is_array($userLevel) && count($userLevel)>0){
                        $user_loans['fk_user_level_id']=$userLevel['fk_mcoin_level_id'];
                    }else{
                        $user_loans['fk_user_level_id']=1;
                    }

                    $user_loans['fk_profession_type_id']=$req_arr['fk_payment_type_id'];
                    $user_loans['fk_payment_type_id']=$req_arr['fk_payment_type_id'];
                    $user_loans['amount_ranges_from']=$productDtl['amount_ranges_from'];
                    $user_loans['amount_ranges_to']=$productDtl['amount_ranges_to'];
                    $user_loans['product_type']=$productDtl['product_type'];
                    $user_loans['unique_loan_code']='CR'.strtoupper($this->getVerificationCode());
                    
                    // add into main user loans table
                    $loan_id=$this->product_model->addUserLoans($user_loans);
                    $loan_varrient['fk_user_loan_id']=$loan_id;
                    $loan_varrient['input_principle']=$productVarientDtl['input_principle'];
                    $loan_varrient['input_air']=$productVarientDtl['input_air'];
                    $loan_varrient['input_npm']=$productVarientDtl['input_npm'];
                    $loan_varrient['input_lpfp']=$productVarientDtl['input_lpfp'];
                    $loan_varrient['input_ufp']=$productVarientDtl['input_ufp'];
                    $loan_varrient['config_str']=$productVarientDtl['config_str'];
                    $loan_varrient['config_sbcr']=$productVarientDtl['config_sbcr'];
                    $loan_varrient['config_kkcr']=$productVarientDtl['config_kkcr'];
                    $loan_varrient['input_lfr']=$productVarientDtl['input_lfr'];
                    $loan_varrient['input_pfpd']=$productVarientDtl['input_pfpd'];
                    $loan_varrient['input_pprm']=$productVarientDtl['input_pprm'];
                    $loan_varrient['input_emi_lty']=$productVarientDtl['input_emi_lty'];
                    $loan_varrient['input_emi_obfpf']=$productVarientDtl['input_emi_obfpf'];
                    $loan_varrient['calc_mir']=$productVarientDtl['calc_mir'];
                    $loan_varrient['calc_lpfa']=$productVarientDtl['calc_lpfa'];
                    $loan_varrient['calc_arl']=$productVarientDtl['calc_arl'];
                    $loan_varrient['calc_ufa']=$productVarientDtl['calc_ufa'];
                    $loan_varrient['calc_tfdb']=$productVarientDtl['calc_tfdb'];
                    $loan_varrient['calc_tst']=$productVarientDtl['calc_tst'];
                    $loan_varrient['calc_rufa']=$productVarientDtl['calc_rufa'];
                    $loan_varrient['calc_stufa']=$productVarientDtl['calc_stufa'];
                    $loan_varrient['calc_da']=$productVarientDtl['calc_da'];
                    $loan_varrient['calc_ra']=$productVarientDtl['calc_ra'];
                    $loan_varrient['calc_lfa']=$productVarientDtl['calc_lfa'];
                    $loan_varrient['calc_rlf']=$productVarientDtl['calc_rlf'];
                    $loan_varrient['calc_stlf']=$productVarientDtl['calc_stlf'];
                    $loan_varrient['calc_emi_tp']=$productVarientDtl['calc_emi_tp'];
                    $loan_varrient['calc_emi_amount']=$productVarientDtl['calc_emi_amount'];
                    // add into main user loans varient table table
                    $this->product_model->addUserLoansVarient($loan_varrient);

                    //add into loan mcoind activity
                    $prodMcoin['fk_product_id']=$req_arr['fk_product_id'];
                    $productMcoins=$this->product_model->fetchProductMcoins($prodMcoin);
                    if(is_array($productMcoins) && count($productMcoins)>0){
                        foreach($productMcoins as $prodmcoin){
                            $product_mcoins['fk_user_loan_id']=$loan_id;
                            $product_mcoins['fk_mcoin_activity_id']=$prodmcoin['fk_mcoin_activity_id'];
                            $product_mcoins['non_referred_connections']=$prodmcoin['non_referred_connections'];
                            $product_mcoins['non_referred_connections_limit']=$prodmcoin['non_referred_connections_limit'];
                            $product_mcoins['referred_connections']=$prodmcoin['referred_connections'];
                            $product_mcoins['referred_connections_limit']=$prodmcoin['referred_connections_limit'];
                            $product_mcoins['referred_connections_limit']=$prodmcoin['referred_connections_limit'];
                            $product_mcoins['own_activity']=$prodmcoin['own_activity'];
                            $product_mcoins['own_activity_limit']=$prodmcoin['own_activity_limit'];

                            $this->product_model->addUserLoanMcoin($product_mcoins);
                        }

                    }
                    //end loan mcoind activity

                    $ties['mcoins_user_level']=$userLevel['fk_mcoin_level_id'];
                    $ties['fk_product_id']=$req_arr['fk_product_id'];
                    $tierDiscountDtl=$this->product_model->getTierUsageFeeDiscount($ties);

                    $loan_extras=array();

                    $loan_extras['fk_user_loan_id']=$loan_id;
                    $loan_extras['tier_usage_fee_discount_amount']=$tierDiscountDtl['usage_fee_discount_amount'];
                    $loan_extras['tier_interest_adjustment']=$tierDiscountDtl['interest_adjustment'];
                    // get customer adjustment
                    $customAdjustment=$this->product_model->getCustomAdjustment($req_arr);
                    $total_discount=0;
                    $total_interest_adjustment=0;
                    if(is_array($customAdjustment) && count($customAdjustment)>0){
                        $loan_extras['user_specific_usage_fee_discount_amount']=$customAdjustment['usage_fee_discount_amount'];
                        $loan_extras['user_specific_interest_adjustment']=$customAdjustment['interest_adjustment'];
                        // get total discount amount
                        if($customAdjustment['usage_fee_discount_amount']>0){
                        $total_discount=$loan_extras['tier_usage_fee_discount_amount'] + $customAdjustment['usage_fee_discount_amount'];
                        }
                        //get total interest adjustment
                        if($customAdjustment['interest_adjustment']>0){
                        $total_interest_adjustment=$loan_extras['tier_interest_adjustment']+$customAdjustment['interest_adjustment'];
                        }
                    }else{
                        $total_discount=$loan_extras['tier_usage_fee_discount_amount'];
                        $total_interest_adjustment=$loan_extras['tier_interest_adjustment'];
                    }
                    // add data into loan extras table
                    $this->product_model->addUserLoansExtras($loan_extras);

                    $loan_disbursed=array();
                    $loan_disbursed['fk_user_loan_id']=$loan_id;
                    if($productDtl['product_type']=='P'){
                        //preset
                        $loan_disbursed['actual_loan_p']=$productVarientDtl['input_principle'];
                        $P=$productVarientDtl['input_principle'];
                    }else{
                        //range
                        $loan_disbursed['actual_loan_p']=$req_arr['product_amount'];

                    }
                       


                         $loan_disbursed['lender_lpfa']=$productVarientDtl['calc_lpfa'];
                         $loan_disbursed['mpokket_ufa']=$productVarientDtl['calc_ufa'];
                         $loan_disbursed['mpokket_stufa']=$productVarientDtl['calc_stufa'];
                         $loan_disbursed['mpokket_rufa']=$productVarientDtl['calc_rufa'];
                         
                         $loan_disbursed['lender_arl']=$productVarientDtl['calc_arl'];
                         if($total_discount>0){
                           
                            $loan_disbursed['borrower_tfdb']=$productVarientDtl['calc_tfdb']-$total_discount;
                            $loan_disbursed['borrower_da']=$productVarientDtl['calc_da']+$total_discount;
                         }else{
                            $loan_disbursed['borrower_tfdb']=$productVarientDtl['calc_tfdb'];
                            $loan_disbursed['borrower_da']=$productVarientDtl['calc_da'];

                        }
                        // add data into loan disbursement table
                        $this->product_model->addUserLoansDisburesment($loan_disbursed);
                        // add loan agent reward earning
                        $reward['fk_product_id']=$req_arr['fk_product_id'];
                        $allRewards=$this->agent_model->getRewardEarning($reward);
                        if(is_array($allRewards) && count($allRewards)>0){

                            foreach($allRewards as $rewards){
                                $reward_loan['fk_user_loan_id']=$loan_id;
                                $reward_loan['reward_point']=$rewards['reward_point'];
                                $reward_loan['fk_reward_activity_id']=$rewards['fk_reward_activity_id'];
                                $this->agent_model->addLoanAgentReward($reward_loan);

                            }

                        }



                        $thanksDtl['amount']=$productVarientDtl['calc_da'];
                        $thanksDtl['month']=$productVarientDtl['input_npm'];
                        $result_arr=$thanksDtl;

               


                    $http_response      = 'http_response_ok';
                    $success_message    = '';  
                   
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Invalid user details'; 
                }
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
            }
        }

        $raws = array();   
        if($error_message != ''){
            $raws['error_message']      = $error_message;
        } else{
            $raws['success_message']    = $success_message;
        }        

        $raws['data']       = $result_arr;
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 
    }

    /**
     *  
     *   @SWG\Post(
     *      path="/connection/allCashRequest",
     *      tags={"allCashRequest: "},
     *      summary="all Cash Request",
     *      description="This api is used to add Cash Request",
     *      produces={"application/json"},
     *   
     *      @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          description="Authorization Token",
     *          required=true,
     *          type="string",
     *      ),
     *      @SWG\Parameter(
     *          name="data",
     *          in="body",
     *          description="Post data to change password",
     *          required=true,
     *          type="string",
     *          @SWG\Schema(ref="#/definitions/allCashRequest"),
     *      ),       
     *  )
     *
    **/ 

    public function allCashRequest_post(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('pass_key')){
                $flag       = false;
                $error_message='passkey can not be null';
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }

            if (!intval($this->post('user_id'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

            $req_arr['search_input_principle']    = $this->post('search_input_principle', TRUE);
            $req_arr['search_input_npm']    = $this->post('search_input_npm', TRUE);
            $req_arr['search_fk_payment_type_id']    = $this->post('search_fk_payment_type_id', TRUE);
            $req_arr['search_city_name']    = $this->post('search_city_name', TRUE);
            $req_arr['search_state_name']    = $this->post('search_state_name', TRUE);
            $req_arr['search_name_of_institution']    = $this->post('search_name_of_institution', TRUE);

            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                //echo $this->db->last_query(); //exit;
                //pre($login_status_arr,1);
                $row=array();
                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                    
                    $row=array();
                    $allRequest=$this->product_model->getAllRequest($req_arr);
                    if(is_array($allRequest) && count($allRequest)>0){
                        $i=0;
                        foreach($allRequest as $request){
                            $img_display_name=substr($request['display_name'],0,1);
                            $row[$i]['img_display_name']=$img_display_name;
                            $row[$i]['fk_user_loan_id']=$request['fk_user_loan_id'];
                            $row[$i]['calc_arl']=$request['calc_arl'];
                            $row[$i]['input_principle']=$request['input_principle'];
                            $educationDtl=$this->profile_model->getEducationUserMain($req_arr);
                            $row[$i]['name_of_institution']=$request['name_of_institution'];
                            $row[$i]['payment_type']=$request['payment_type'];
                            $row[$i]['city_name']=$request['city_name'];
                            $row[$i]['fk_user_id']=$request['fk_user_id'];
                            $row[$i]['display_name']=$request['display_name'];
                            $row[$i]['fk_payment_type_id']=$request['fk_payment_type_id'];
                            $row[$i]['input_npm']=$request['input_npm'];
                            $row[$i]['pl']=$request['calc_ra']-$request['calc_lfa'];
                            $row[$i]['calc_lfa']=$request['calc_lfa'];
                            $row[$i]['irr']=0;


                            

                            $i++;
                        }

                    }
                    $result_arr['dataset']=$row;
                    $result_arr['cash_request']=$this->product_model->getProductDisbursed();
                    
                    $result_arr['total_data']=count($row);

                    $http_response      = 'http_response_ok';
                    $success_message    = '';  
                   
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Invalid user details'; 
                }
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
            }
        }

        $raws = array();   
        if($error_message != ''){
            $raws['error_message']      = $error_message;
        } else{
            $raws['success_message']    = $success_message;
        }        

        $raws['data']       = $result_arr;
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 
    }

    /**
     *  
     *   @SWG\Post(
     *      path="/connection/giveCash",
     *      tags={"giveCash: "},
     *      summary="all Cash Request",
     *      description="This api is used to add Cash Request",
     *      produces={"application/json"},
     *   
     *      @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          description="Authorization Token",
     *          required=true,
     *          type="string",
     *      ),
     *      @SWG\Parameter(
     *          name="data",
     *          in="body",
     *          description="Post data to change password",
     *          required=true,
     *          type="string",
     *          @SWG\Schema(ref="#/definitions/giveCash"),
     *      ),       
     *  )
     *
    **/
    public function giveCash_post(){
        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('pass_key')){
                $flag       = false;
                $error_message='passkey can not be null';
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }

            if (!intval($this->post('user_id'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

            if (!$this->post('loan_id')){
                $flag       = false;
                $error_message='loan_id can not be null';
            } else {
                $req_arr['loan_id']    = $this->post('loan_id', TRUE);
            }

            

            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                
                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                    

                    $allLoanIds=explode(',',$req_arr['loan_id']);
                    if(is_array($allLoanIds) && count($allLoanIds)>0){
                        foreach($allLoanIds as $loanId){
                            
                            if($loanId>0){
                                $this->product_model->assignLoan($loanId,$req_arr['user_id']);
                                $loanDtl=$this->product_model->getUserLoanDtl($req_arr);
                                $loanDisDtl=$this->product_model->getUserLoanDisbursedDtl($req_arr);
                                $loanVarientDtl=$this->product_model->getUserLoanVarrients($req_arr);
                                 //add into notification table

                                $notification_code='TRN-CAP';
                                $notificationDtl=$this->notifications_model->getNotificationTypes($notification_code);
                                $notification_data['fk_user_id']=$loanDtl['fk_user_id'];
                                $notification_data['notification_for_mode']='B';
                                $notification_data['fk_notification_type_id']=$notificationDtl['id'];
                                $notification_data['notification_message']=' Your Cash Request for Rs'. $loanVarientDtl['calc_da'].' has been Approved';
                                //serialized data
                                $json_data_array['display_name']='';
                                $json_data_array['accepted_id']=$req_arr['user_id'];
                                $json_data_array['notification_code']=$notification_code;

                                //if($userDtl['s3_media_version']!=''){
                                   // $profile_picture_file_url = $this->config->item('bucket_url').$req_arr['user_id'].'/profile/'.$req_arr['user_id'].'.'.$userDtl['profile_picture_file_extension'].'?versionId='.$userDtl['s3_media_version'];
                                //}else{
                                    $profile_picture_file_url='';
                                //}
                                $json_data_array['img_url']=$profile_picture_file_url;
                                $json_data_serialize=json_encode($json_data_array);
                                //end of serialized data
                                $notification_data['routing_json']=$json_data_serialize;

                                $this->notifications_model->addUserNotification($notification_data);
                                //send push message
                                    $pushType=$notification_code;
                                    $message=$notification_data['notification_message'];
                                    $total_new_notifications=$this->notifications_model->getAllNewNotifications($loanDtl['fk_user_id']);
                                    $display_name='';
                                    $push_message = "{~message~:~" . $message . "~,~total_new_notifications~:~" . $total_new_notifications . "~,~accepted_id~:~" . $req_arr['user_id'] . "~,~user_id~:~" . $loanDtl['fk_user_id'] . "~,~name~:~" . $display_name . "~,~profile_image~:~" . $profile_picture_file_url . "~,~push_type~:~" . $notification_code . "~}";
                                   
                                    $this->sendMobilePushNotifications($loanDtl['fk_user_id'],$req_arr['user_id'],$push_message,$pushType,$message);
                                    
                                //end push message



                            }
                        }
                    }

                   

                    $http_response      = 'http_response_ok';
                    $success_message    = '';  
                   
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Invalid user details'; 
                }
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
            }
        }

        $raws = array();   
        if($error_message != ''){
            $raws['error_message']      = $error_message;
        } else{
            $raws['success_message']    = $success_message;
        }        

        $raws['data']       = $result_arr;
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 
    }

    public function giveCashAutoAllocated_post(){
        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('pass_key')){
                $flag       = false;
                $error_message='passkey can not be null';
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }

            if (!intval($this->post('user_id'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

            if (!$this->post('loans')){
                $flag       = false;
                $error_message='loan_id can not be null';
            } else {
                $allLoans    = json_decode($this->post('loans', TRUE),true);

                if(is_array($allLoans) && count($allLoans)>0){
                        $data['allLoans']=$allLoans;
                }else{
                    $flag       = false;
                    $error_message='loan_id can not be null';
                }
            }

           
            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                
                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                    

                   // $allLoanIds=explode(',',$req_arr['loan_id']);
                    $wallet_amount=0;
                    $lock_amount=0;
                    $mpokketAcountDtl=$this->product_model->getDetailsMPokketAccount($req_arr);
                    if(is_array($mpokketAcountDtl) && count($mpokketAcountDtl)>0){
                        $data['id']=$mpokketAcountDtl['id'];
                        $data['account_no']=$mpokketAcountDtl['mpokket_account_number'];

                        $wallet_amount=$this->product_model->getwalletAmount($data);
                        $lock_amount=$this->product_model->getLockingAmount($req_arr);

                    }

                    $recent_wallet_amount=$wallet_amount-$lock_amount;

                    if(is_array($allLoans) && count($allLoans)>0){
                        foreach($allLoans as $loan){
                            $arl_amount=0;
                            $arl_amount=$loan['amount'];
                            $loan_no=0;
                            $loan_no=$loan['total_loan_no'];
                            $allArlAmt=$this->product_model->getAllARL($arl_amount);
                           
                            if($loan_no>1){
                                
                                for($i=0;$i<$loan_no;$i++){
                                    if($arl_amount <= $recent_wallet_amount){
                                        $loanId=0;
                                        $loanId=$allArlAmt[$i]['id'];
                                        $this->product_model->assignLoan($loanId,$req_arr['user_id']);
                                        //add into notification table
                                        $noti['loan_id']=$loanId;
                                        $loanDtl=$this->product_model->getUserLoanDtl($noti);
                                        $loanVarientDtl=$this->product_model->getUserLoanVarrients($noti);

                                        $notification_code='TRN-CAP';
                                        $notificationDtl=$this->notifications_model->getNotificationTypes($notification_code);
                                        $notification_data['fk_user_id']=$loanDtl['fk_user_id'];
                                        $notification_data['notification_for_mode']='B';
                                        $notification_data['fk_notification_type_id']=$notificationDtl['id'];
                                        $notification_data['notification_message']=' Your Cash Request for Rs'. $loanVarientDtl['calc_da'].' has been Approved';
                                        //serialized data
                                        $json_data_array['display_name']='';
                                        $json_data_array['accepted_id']=$req_arr['user_id'];
                                        $json_data_array['notification_code']=$notification_code;

                                        //if($userDtl['s3_media_version']!=''){
                                           // $profile_picture_file_url = $this->config->item('bucket_url').$req_arr['user_id'].'/profile/'.$req_arr['user_id'].'.'.$userDtl['profile_picture_file_extension'].'?versionId='.$userDtl['s3_media_version'];
                                        //}else{
                                            $profile_picture_file_url='';
                                        //}
                                        $json_data_array['img_url']=$profile_picture_file_url;
                                        $json_data_serialize=json_encode($json_data_array);
                                        //end of serialized data
                                        $notification_data['routing_json']=$json_data_serialize;

                                        $this->notifications_model->addUserNotification($notification_data);
                                        //send push message
                                            $pushType=$notification_code;
                                            $message=$notification_data['notification_message'];
                                            $total_new_notifications=$this->notifications_model->getAllNewNotifications($loanDtl['fk_user_id']);
                                            $display_name='';
                                            $push_message = "{~message~:~" . $message . "~,~total_new_notifications~:~" . $total_new_notifications . "~,~accepted_id~:~" . $req_arr['user_id'] . "~,~user_id~:~" . $loanDtl['fk_user_id'] . "~,~name~:~" . $display_name . "~,~profile_image~:~" . $profile_picture_file_url . "~,~push_type~:~" . $notification_code . "~}";
                                           
                                            $this->sendMobilePushNotifications($loanDtl['fk_user_id'],$req_arr['user_id'],$push_message,$pushType,$message);
                                            
                                        //end push message
                                    }

                                }
                            }else{
                               
                                if($loan_no>0){
                                  
                                    if($arl_amount <= $recent_wallet_amount){
                                        $loanId=0;
                                        
                                        $loanId=$allArlAmt[0]['id'];
                                        $this->product_model->assignLoan($loanId,$req_arr['user_id']);
                                        //add into notification table
                                        $noti['loan_id']=$loanId;
                                        $loanDtl=$this->product_model->getUserLoanDtl($noti);
                                        $loanVarientDtl=$this->product_model->getUserLoanVarrients($noti);

                                        $notification_code='TRN-CAP';
                                        $notificationDtl=$this->notifications_model->getNotificationTypes($notification_code);
                                        $notification_data['fk_user_id']=$loanDtl['fk_user_id'];
                                        $notification_data['notification_for_mode']='B';
                                        $notification_data['fk_notification_type_id']=$notificationDtl['id'];
                                        $notification_data['notification_message']=' Your Cash Request for Rs'. $loanVarientDtl['calc_da'].' has been Approved';
                                        //serialized data
                                        $json_data_array['display_name']='';
                                        $json_data_array['accepted_id']=$req_arr['user_id'];
                                        $json_data_array['notification_code']=$notification_code;

                                        //if($userDtl['s3_media_version']!=''){
                                           // $profile_picture_file_url = $this->config->item('bucket_url').$req_arr['user_id'].'/profile/'.$req_arr['user_id'].'.'.$userDtl['profile_picture_file_extension'].'?versionId='.$userDtl['s3_media_version'];
                                        //}else{
                                            $profile_picture_file_url='';
                                        //}
                                        $json_data_array['img_url']=$profile_picture_file_url;
                                        $json_data_serialize=json_encode($json_data_array);
                                        //end of serialized data
                                        $notification_data['routing_json']=$json_data_serialize;

                                        $this->notifications_model->addUserNotification($notification_data);
                                        //send push message
                                            $pushType=$notification_code;
                                            $message=$notification_data['notification_message'];
                                            $total_new_notifications=$this->notifications_model->getAllNewNotifications($loanDtl['fk_user_id']);
                                            $display_name='';
                                            $push_message = "{~message~:~" . $message . "~,~total_new_notifications~:~" . $total_new_notifications . "~,~accepted_id~:~" . $req_arr['user_id'] . "~,~user_id~:~" . $loanDtl['fk_user_id'] . "~,~name~:~" . $display_name . "~,~profile_image~:~" . $profile_picture_file_url . "~,~push_type~:~" . $notification_code . "~}";
                                           
                                            $this->sendMobilePushNotifications($loanDtl['fk_user_id'],$req_arr['user_id'],$push_message,$pushType,$message);
                                            
                                        //end push message

                                    }

                                }
                            }
                        }

                    }

                    /*if(is_array($allLoanIds) && count($allLoanIds)>0){
                        foreach($allLoanIds as $loanId){
                            
                            if($loanId>0){
                                $this->product_model->assignLoan($loanId,$req_arr['user_id']);
                            }
                        }
                    }*/

                   

                    $http_response      = 'http_response_ok';
                    $success_message    = '';  
                   
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Invalid user details'; 
                }
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
            }
        }

        $raws = array();   
        if($error_message != ''){
            $raws['error_message']      = $error_message;
        } else{
            $raws['success_message']    = $success_message;
        }        

        $raws['data']       = $result_arr;
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 
    }


    public function getWalletAmount_post(){
        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('pass_key')){
                $flag       = false;
                $error_message='passkey can not be null';
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }

            if (!intval($this->post('user_id'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

           

            

            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                
                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                    $wallet_amount=0;
                    $lock_amount=0;
                    $mpokketAcountDtl=$this->product_model->getDetailsMPokketAccount($req_arr);
                    if(is_array($mpokketAcountDtl) && count($mpokketAcountDtl)>0){
                        $data['id']=$mpokketAcountDtl['id'];
                        $data['account_no']=$mpokketAcountDtl['mpokket_account_number'];

                        $wallet_amount=$this->product_model->getwalletAmount($data);
                        $lock_amount=$this->product_model->getLockingAmount($req_arr);

                    }
                    $data['wallet_amount']=$wallet_amount;
                    $data['lock_amount']=$lock_amount;
                    $data['minimum_amount']='550.00';
                    $result_arr=$data;
                    $http_response      = 'http_response_ok';
                    $success_message    = '';  
                   
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Invalid user details'; 
                }
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
            }
        }

        $raws = array();   
        if($error_message != ''){
            $raws['error_message']      = $error_message;
        } else{
            $raws['success_message']    = $success_message;
        }        

        $raws['dataset']       = $result_arr;
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 
    }


    public function autoAllocate_post(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('pass_key')){
                $flag       = false;
                $error_message='passkey can not be null';
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }

            if (!intval($this->post('user_id'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

           

            

            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                
                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                    $wallet_amount=0;
                    $lock_amount=0;
                    $mpokketAcountDtl=$this->product_model->getDetailsMPokketAccount($req_arr);
                    if(is_array($mpokketAcountDtl) && count($mpokketAcountDtl)>0){
                        $user_account_id['id']=$mpokketAcountDtl['id'];
                        $user_account_id['account_no']=$mpokketAcountDtl['mpokket_account_number'];

                        $wallet_amount=$this->product_model->getwalletAmount($user_account_id);
                        $lock_amount=$this->product_model->getLockingAmount($req_arr);

                    }
                    $data['wallet_amount']=$wallet_amount;
                    $data['lock_amount']=$lock_amount;
                    $data['free_amount']=$wallet_amount-$lock_amount;

                    $free_amount=$data['free_amount'];
                    if($free_amount>0){
                        $allLoans=$this->product_model->getAllLoans();
                        if(is_array($allLoans) && count($allLoans)>0){
                            $i=0;
                            $total_tem_lock_amt=0;
                            $tmp_lock_amt=0;
                            $amtDtl=array();
                            $loan_id=array();
                            foreach($allLoans as $loans){
                                
                                $total_free_amount=$free_amount-$tmp_lock_amt;
                              
                                if($total_free_amount>=$loans['calc_arl']){
                                    
                                    if($i==0){
                                        $amtDtl[$i]['amount']=$loans['calc_arl'];
                                        $amtDtl[$i]['total_loan_no']=1;
                                        $loan_id[]=$loans['fk_user_loan_id'];
                                        $tmp_lock_amt=$loans['calc_arl'];
                                       // $amtDtl[$i]['tmp_lock_amt']=$tmp_lock_amt;
                                        //$total_tem_lock_amt=$tmp_lock_amt;
                                        $i++;
                                        
                                    }else{
                                        
                                        $key = array_search($loans['calc_arl'], array_column($amtDtl,'amount'));

                                       
                                        if($key===false){
                                          
                                            $amtDtl[$i]['amount']=$loans['calc_arl'];
                                            $amtDtl[$i]['total_loan_no']=1;
                                            $loan_id[]=$loans['fk_user_loan_id'];
                                            $tmp_lock_amt=$tmp_lock_amt + $loans['input_principle'];
                                            $amtDtl[$i]['tmp_lock_amt']=$tmp_lock_amt;
                                           // $total_tem_lock_amt=$total_tem_lock_amt+$tmp_lock_amt;
                                            $i++;
                                        }else{
                                           
                                            $amtDtl[$key]['total_loan_no']=$amtDtl[$key]['total_loan_no']+1;

                                            $tmp_lock_amt=$tmp_lock_amt + $amtDtl[$key]['amount'];

                                            $loan_id[]=$loans['fk_user_loan_id'];
                                           // $total_tem_lock_amt=$total_tem_lock_amt+$tmp_lock_amt;

                                        }
                                    }
                                    
                                    
                                   
                                    

                                }else{
                                    //exit;
                                }

                                
                            }
                            $result_arr['auto_allocated']=$amtDtl;
                            $result_arr['loans_ids']=$loan_id;
                            $result_arr['wallet_amount']=$data['wallet_amount'];
                            $result_arr['lock_amount']=$data['lock_amount'];
                            $result_arr['free_amount']=$data['free_amount'];
                            $result_arr['temp_lock_amount']=$tmp_lock_amt;
                            $http_response      = 'http_response_ok';
                            $success_message    = '';  

                        }else{
                            $http_response      = 'http_response_bad_request';
                            $error_message    = 'There is no loans to auto allocate';  
                        }

                      

                    }else{
                        $http_response      = 'http_response_bad_request';
                        $error_message    = 'You have not enough balance to auto allocate';  
                    }  

                   
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Invalid user details'; 
                }
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
            }
        }

        $raws = array();   
        if($error_message != ''){
            $raws['error_message']      = $error_message;
        } else{
            $raws['success_message']    = $success_message;
        }        

        $raws['dataset']       = $result_arr;
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 

    }

    public function accpetCashRequest_post(){

        
        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('pass_key')){
                $flag       = false;
                $error_message='passkey can not be null';
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }

            if (!intval($this->post('user_id'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

            if (!intval($this->post('loan_id'))){
                $flag       = false;
                $error_message='loan_id id can not be null';
            } else {
                $req_arr['loan_id']    = $this->post('loan_id', TRUE);
            }

            //action_type can only be A/D
            if (!$this->post('action_type')){
                $flag       = false;
                $error_message='action_type can not be null';
            } else {
                $req_arr['action_type']    = $this->post('action_type', TRUE);
            }


            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                
                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                    $loanDetails=$this->product_model->getLoanDetail($req_arr['loan_id']);
                    if($loanDetails['fk_user_id']==$req_arr['user_id']){
                        $res['loan_action_type']=$req_arr['action_type'];
                        $res['loan_disbursed_timestamp']=date('Y-m-d H:i:s');
                        $res['is_loan_closed']='0';
                        $this->product_model->updateUserLoans($res,$req_arr['loan_id']);

                        if($req_arr['action_type']=='A'){
                            $days=$loanDetails['input_npm']*30;
                            $stop_date=date('Y-m-d');
                            $schedule_date = date('Y-m-d', strtotime($stop_date . ' +'.$days.' day'));

                            $replayment_sch['fk_user_loan_id']=$loanDetails['fk_user_loan_id'];
                            $replayment_sch['scheduled_payment_date']=$schedule_date;
                            $replayment_sch['scheduled_emi']=$loanDetails['calc_ra'];
                            $replayment_sch['scheduled_interest']=$loanDetails['input_air'];
                            $replayment_sch['scheduled_principal']=$loanDetails['input_principle'];

                            $replayment_sch['mpokket_lfa']=$loanDetails['calc_lfa'];
                            $replayment_sch['mpokket_stlf']=$loanDetails['calc_stlf'];
                            $replayment_sch['mpokket_rlf']=$loanDetails['calc_rlf'];
                            $replayment_sch['lender_pl']=$loanDetails['calc_ra']-$loanDetails['calc_lfa'];
                            $replayment_sch['borrower_emi_amount']=$loanDetails['calc_ra'];
                            $replayment_sch['lender_repayment_amount']=$loanDetails['calc_ra']-$loanDetails['calc_lfa'];
                            $replayment_sch['borrower_emi_amount']=$loanDetails['calc_ra'];
                            $this->product_model->addUserLoanRepayment($replayment_sch);
                            //add money to borrower 
                            $mpokketAcountDtl=$this->product_model->getDetailsMPokketAccount($req_arr);
                            if(is_array($mpokketAcountDtl) && count($mpokketAcountDtl)>0){
                               $borrower_account_id=$mpokketAcountDtl['id'];
                               $mpokket['fk_user_mpokket_account_id']=$borrower_account_id;
                               $mpokket['transfer_amount']=$loanDetails['borrower_da'];
                               $mpokket['transfer_type']='R';
                               $mpokket['transaction_date']=date('Y-m-d H:i:s');
                               $this->product_model->addMpokketFunds($mpokket);
                            }

                            //deduct money from lender
                            $row['user_id']=$loanDetails['loan_offered_by_user_id'];
                            $mpokketLenderAcountDtl=$this->product_model->getDetailsMPokketAccount($row);
                            if(is_array($mpokketLenderAcountDtl) && count($mpokketLenderAcountDtl)>0){
                                $lender_account_id=$mpokketLenderAcountDtl['id'];
                                $mpokket=array();
                               $mpokket['fk_user_mpokket_account_id']=$lender_account_id;
                               $mpokket['transfer_amount']=$loanDetails['calc_arl'];
                               $mpokket['transfer_type']='P';
                               $mpokket['transaction_date']=date('Y-m-d H:i:s');
                               $this->product_model->addMpokketFunds($mpokket);
                            }

                            
                        }

                       
                        //add into notification table
                        $userDtl=$this->profile_model->fetchTempProfileMain($req_arr);
                        if($req_arr['action_type']=='A'){
                            $notification_code='TRN-CSA';
                        }else{
                            $notification_code='TRN-CSD';
                        }
                        
                        $notificationDtl=$this->notifications_model->getNotificationTypes($notification_code);
                        $notification_data['fk_user_id']=$loanDetails['loan_offered_by_user_id'];
                        $notification_data['notification_for_mode']='L';
                        $notification_data['fk_notification_type_id']=$notificationDtl['id'];
                        $notification_data['notification_message']='Cash Disbursed to '.$userDtl['display_name'];
                        //serialized data
                        $json_data_array['display_name']=$userDtl['display_name'];
                        $json_data_array['accepted_id']=$req_arr['user_id'];
                        $json_data_array['notification_code']=$notification_code;

                        if($userDtl['s3_media_version']!=''){
                            $profile_picture_file_url = $this->config->item('bucket_url').$req_arr['user_id'].'/profile/'.$req_arr['user_id'].'.'.$userDtl['profile_picture_file_extension'].'?versionId='.$userDtl['s3_media_version'];
                        }else{
                            $profile_picture_file_url='';
                        }
                        $json_data_array['img_url']=$profile_picture_file_url;
                        $json_data_serialize=json_encode($json_data_array);
                        //end of serialized data
                        $notification_data['routing_json']=$json_data_serialize;

                        $this->notifications_model->addUserNotification($notification_data);
                        //send push message
                        $pushType=$notification_code;
                            $message=$notification_data['notification_message'];
                            $total_new_notifications=$this->notifications_model->getAllNewNotifications($loanDetails['loan_offered_by_user_id']);

                            $push_message = "{~message~:~" . $message . "~,~total_new_notifications~:~" . $total_new_notifications . "~,~accepted_id~:~" . $req_arr['user_id'] . "~,~loan_id~:~" . $req_arr['loan_id'] . "~,~name~:~" . $userDtl['display_name'] . "~,~profile_image~:~" . $profile_picture_file_url . "~,~push_type~:~" . $notification_code . "~}";

                            
                           
                            $this->sendMobilePushNotifications($loanDetails['loan_offered_by_user_id'],$req_arr['user_id'],$push_message,$pushType,$message);

                        //end push message
                    if($req_arr['action_type']=='A'){
                            $todate=date('jS M,Y');
                            $totime=date('H:i s');
                            $day=date('j').date('S');
                            $month=date('M');
                            $year=date('Y');
                            $schedule_date=date('jS M,Y',strtotime($schedule_date));
                            $borrower_name=$userDtl['display_name'];
                            $borrower_father_name=$userDtl['fathers_name'];

                            $req_lender['user_id']=$loanDetails['loan_offered_by_user_id'];
                            $lenderDtl=$this->profile_model->fetchTempProfileMain($req_lender);

                            $lender_name=$lenderDtl['display_name'];
                            $lender_father_name=$lenderDtl['fathers_name'];
                            $principal_amount=$loanDetails['input_principle'];
                            $lender_amount=$loanDetails['calc_arl'];
                            $borrower_amount=$loanDetails['calc_da'];

                            $mpokket_amount=$lender_amount-$borrower_amount;

                            $userUserDTl=$this->user_model->fetchUserDeatils($req_arr['user_id']);
                            $borrower_mobile_no=$userUserDTl['mobile_number'];
                            $borrower_email=$userUserDTl['email_id'];

                            $lenderUserDTl=$this->user_model->fetchUserDeatils($req_arr['user_id']);
                            $lender_mobile_no=$lenderUserDTl['mobile_number'];



                            $fname='/var/www/html/apis/assets/contract/contract.html';
                            $fname2='/var/www/html/apis/assets/contract/contract_borrower.html';
                             //$fname = "dummy.doc";
                            $fhandle = fopen($fname,"r");
                            $fhandle2 = fopen($fname2,"r");
                            if (file_exists($fname)){
                                
                                $content = fread($fhandle,filesize($fname));
                                $content2 = fread($fhandle2,filesize($fname2));
                               
                                if($content!=''){
                                    $content = str_replace("[Day][th or st]", $day, $content);
                                    $content = str_replace("[Month]", $month, $content);
                                    $content = str_replace("[Year]", $year, $content);
                                    $content = str_replace("[name_of_borrower]", $borrower_name, $content);
                                    $content = str_replace("[father_name_borrower]", $borrower_father_name, $content);
                                    $content = str_replace("[name_of_lender]", $lender_name, $content);
                                    $content = str_replace("[father_name_lender]", $lender_father_name, $content);
                                    $content = str_replace("[principal_amount]", $principal_amount, $content);
                                    $content = str_replace("[lender_amount]", $lender_amount, $content);
                                    $content = str_replace("[borrower_amount]", $borrower_amount, $content);
                                    $content = str_replace("[fees_amount]", $mpokket_amount, $content);
                                    $content = str_replace("[borrower_mobile]", $borrower_mobile_no, $content);
                                    $content = str_replace("[lender_mobile]",$lender_mobile_no, $content);
                                    $content = str_replace("[date]", $todate, $content);
                                    $content = str_replace("[exp_date]", $schedule_date, $content);
                                    $content = str_replace("[date_execution]", $todate, $content);
                                    $content = str_replace("[time_execution]", $totime, $content);
                                }

                                if($content2!=''){
                                    $content2 = str_replace("[Day][th or st]", $day, $content2);
                                    $content2 = str_replace("[Month]", $month, $content2);
                                    $content2 = str_replace("[Year]", $year, $content2);
                                    $content2 = str_replace("[name_of_borrower]", $borrower_name, $content2);
                                    $content2 = str_replace("[father_name_borrower]", $borrower_father_name, $content2);
                                   
                                    $content2 = str_replace("[principal_amount]", $principal_amount, $content2);
                                    $content2 = str_replace("[lender_amount]", $lender_amount, $content2);
                                    $content2 = str_replace("[borrower_amount]", $borrower_amount, $content2);
                                    $content2 = str_replace("[fees_amount]", $mpokket_amount, $content2);
                                    $content2 = str_replace("[borrower_mobile]", $borrower_mobile_no, $content2);
                                  
                                    $content2 = str_replace("[date]", $todate, $content2);
                                    $content2 = str_replace("[exp_date]", $schedule_date, $content2);
                                    $content2 = str_replace("[date_execution]", $todate, $content2);
                                    $content2 = str_replace("[time_execution]", $totime, $content2);
                                }
                                
                                //$fhandle = fopen($fname,"w");
                                //if(fwrite($fhandle,$content))
                                //{
                                if($content!=''){
                                    
                                    $html=$content;

                                    $file_name = $loanDetails['unique_loan_code'].'.pdf';

                                    $this->load->library('mpdf');
                                
                                    $mpdf = new mPDF('utf-8', 'A4');
                                    $mpdf->debug = true;
                                    $mpdf->setAutoTopMargin='stretch';
                                    $mpdf->mirrorMargins = 0; 
                                    $mpdf->SetHTMLFooter('<span style="margin-left:250px;"> Page {PAGENO} of {nb} </span>');
                                    $mpdf->WriteHTML($html);
                                    $file_path='/var/www/html/apis/assets/contract/';

                                    $mpdf->Output($file_path.$file_name, 'F');
                                     $aws_target_file_path='contract/'.$loanDetails['fk_user_loan_id'].'/'.$file_name;
                                    $aws_temp_name=$file_path.$file_name;
                                    $response = $this->aws->uploadfile($this->aws->bucket,$aws_target_file_path,$aws_temp_name,'public-read');
                                    unlink($aws_temp_name);
                                }
                                if($content2!=''){
                                    
                                    $html=$content2;

                                    $file_name = $loanDetails['unique_loan_code'].'_borrower.pdf';

                                    $this->load->library('mpdf');
                                
                                    $mpdf = new mPDF('utf-8', 'A4');
                                    $mpdf->debug = true;
                                    $mpdf->setAutoTopMargin='stretch';
                                    $mpdf->mirrorMargins = 0; 
                                    $mpdf->SetHTMLFooter('<span style="margin-left:250px;"> Page {PAGENO} of {nb} </span>');
                                    $mpdf->WriteHTML($html);
                                    $file_path='/var/www/html/apis/assets/contract/';
                                    $mpdf->Output($file_path.$file_name, 'F');

                                    $aws_target_file_path='contract/'.$loanDetails['fk_user_loan_id'].'/'.$file_name;
                                    $aws_temp_name=$file_path.$file_name;
                                    $response = $this->aws->uploadfile($this->aws->bucket,$aws_target_file_path,$aws_temp_name,'public-read');
                                    //send email
                                    //initialising codeigniter email

                                     $email_config=email_config();
                                    $this->email->initialize($email_config);

                                    // email sent to buyer 
                                    $admin_email= $this->config->item('admin_email');
                                    $admin_email_from= $this->config->item('admin_email_from');
                                    $this->email->from($admin_email, $admin_email_from);

                                  
                                    $this->email->to($borrower_email); 
                                    $subject='mPOkket: Contract';                                
                                    $this->email->subject($subject);
                                    $message='';
                                    $email_data['name']= $borrower_name;  
                                    $email_body= $this->parser->parse('email_templates/contract_borrower', $email_data, true);
                                   
                                    $this->email->attach($aws_temp_name);
                                    $this->email->message($email_body);            
                                    $this->email->send();
                                     unlink($aws_temp_name);
                                    // email send end 

                                }


                                fclose($fhandle);
                                fclose($fhandle2);
                               
                                
                            }
                        }
                            
                        //generated pdf

                        $http_response      = 'http_response_ok';
                        $error_message      = ''; 

                    }else{
                        $http_response      = 'http_response_bad_request';
                        $error_message      = 'You can not accept this loan'; 
                    }  



                   
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Invalid user details'; 
                }
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
            }
        }

        $raws = array();   
        if($error_message != ''){
            $raws['error_message']      = $error_message;
        } else{
            $raws['success_message']    = $success_message;
        }        

        $raws['dataset']       = $result_arr;
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 

    }



    Public function sendMobilePushNotifications($receiver_id,$user_id,$push_message,$pushType,$message){
        $appExtras = new AppExtrasAPI();
        $check_push = $appExtras->canSendPushToUser($receiver_id);

            if($check_push){

                $device_dtl=$this->user_model->fetchMobileDevice($receiver_id);
                $device_uid=$device_dtl['device_uid'];
                $badge_count=$device_dtl['badge_count']+1;
                $device_table_id=$device_dtl['id'];
                $device_os=$device_dtl['device_os'];
                if ($device_os=='iOS') {
                    if($isappactive==0){
                        $dataappactive['badge_count']=1;
                        $dataappactive['id']=$device_table_id;
                       // $this->salesrep_model->updateIsappactive($dataappactive);
                    }
                    $appExtras->sendPushDirect($receiver_id, $device_uid, $push_message, $pushType,$device_os,$message);
      
                }else if($device_os=='And'){
                   
                    $appExtras->sendPushDirect($receiver_id, $device_uid, $push_message, $pushType,$device_os, $message);

                }
            }
    }


    public function borrowerTransacDashboard_post(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('pass_key')){
                $flag       = false;
                $error_message='passkey can not be null';
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }

            if (!intval($this->post('user_id'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

           

            

            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                
                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                    $wallet_amount=0;
                    $lock_amount=0;
                    $mpokketAcountDtl=$this->product_model->getDetailsMPokketAccount($req_arr);
                        $user_account_id=array();
                    if(is_array($mpokketAcountDtl) && count($mpokketAcountDtl)>0){
                        $user_account_id['id']=$mpokketAcountDtl['id'];
                        $user_account_id['account_no']=$mpokketAcountDtl['mpokket_account_number'];

                        $wallet_amount=$this->product_model->getwalletAmount($req_arr);
                        $lock_amount=$this->product_model->getLockingAmount($req_arr);

                    }else{
                        $user_account_id['id']=0;
                    }

                    $totalCaskTaken=$this->product_model->totalCaskTaken($user_account_id);
                    $details['cash_taken']=$totalCaskTaken;
                    $details['overdue']=$this->product_model->totalOverdue($req_arr);
                    $details['upcoming']=$this->product_model->totalUpcoming($req_arr);
                    $result_arr=$details;
                    $http_response      = 'http_response_ok';
                    $success_message    = '';   

                   
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Invalid user details'; 
                }
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
            }
        }

        $raws = array();   
        if($error_message != ''){
            $raws['error_message']      = $error_message;
        } else{
            $raws['success_message']    = $success_message;
        }        

        $raws['dataset']       = $result_arr;
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 

    }


    public function lenderTransacDashboard_post(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('pass_key')){
                $flag       = false;
                $error_message='passkey can not be null';
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }

            if (!intval($this->post('user_id'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

           

            

            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                
                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                    
                    $irr_actual=$this->getXIRRvalue($req_arr['user_id']);

                    $details['irr_actual']=($irr_actual>0)?number_format($irr_actual,2):0;
                    $details['irr_projected']=($irr_actual>0)?number_format($irr_actual,2):0;
                    $details['cash_given']=$this->product_model->totalCashGiven($req_arr);
                    $details['cash_received']=$this->product_model->totalCashReceived($req_arr);
                    $details['cash_pending']=$this->product_model->totalCashPending($req_arr);
                    $details['cash_offered']=$this->product_model->totalCashOffered($req_arr);
                    $result_arr=$details;


                     $http_response      = 'http_response_ok';
                     $success_message    = '';   

                   
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Invalid user details'; 
                }
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
            }
        }

        $raws = array();   
        if($error_message != ''){
            $raws['error_message']      = $error_message;
        } else{
            $raws['success_message']    = $success_message;
        }        

        $raws['dataset']       = $result_arr;
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 

    }

    public function getXIRRvalue($user_id){
        $req_arr['user_id']=$user_id;
        $mpokketLenderAcountDtl=$this->product_model->getDetailsMPokketAccount($req_arr);

        $amount_values=array();
        $amount_dates=array();
        if(is_array($mpokketLenderAcountDtl) && count($mpokketLenderAcountDtl)){
            if($mpokketLenderAcountDtl['id']>0){
                $all_cash_flows=$this->product_model->allCashFlow($mpokketLenderAcountDtl);
                if(is_array($all_cash_flows) && count($all_cash_flows)>0){
                    $positive_value='no';
                    $negetive_value='no';
                    foreach($all_cash_flows as $cashFlow){
                        if($cashFlow['transfer_type']=='P' || $cashFlow['transfer_type']=='R'){
                            if($cashFlow['transfer_type']=='P'){
                                $amount_values[]='-'.$cashFlow['transfer_amount'];
                                $negetive_value='yes';
                            }else{
                                $amount_values[]=$cashFlow['transfer_amount'];
                                $positive_value='yes';
                            }
                            $dts=strtotime($cashFlow['transaction_date']);
                            $amount_dates[]=mktime(0, 0, 0, date("m",$dts)  , date("d",$dts)+1, date("Y",$dts));
                        }

                    }

                }
            }
        }

        if(count($amount_values)>2){
            if($positive_value=='yes' && $negetive_value=='yes'){
                $irr_actual=$this->calculation->XIRR($amount_values,$amount_dates,0.1);
            }else{
                $irr_actual=0;
            }
        
        }else{
            $irr_actual=0;
        }
        if($irr_actual>0){
            $irr=$irr_actual;
        }else{
            $irr=0;
        }

        return $irr;



    }
    public function getAllCashToken_post(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('pass_key')){
                $flag       = false;
                $error_message='passkey can not be null';
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }
            if ($this->post('user_id')<0){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

            if (!intval($this->post('page'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['page']    = $this->post('page', TRUE);
            }

             if (!$this->post('user_type')){
                $flag       = false;
                $error_message='user type can not be null';
            } else {
                $req_arr['user_type']    = $this->post('user_type', TRUE);
            }

            if (!intval($this->post('page_size'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['page_size']    = $this->post('page_size', TRUE);
            }

            $page = $req_arr['page'];

            if (!empty($page)) {
                $limit = $req_arr['page_size'];

                if ($page > 1) {
                    $id = $page - 1;
                } else {
                    $id = '0';
                }
                $pageLimit = $limit * $id;
            }

            $req_arr['search_status']    = $this->post('search_status', TRUE);
            $req_arr['search_start_date']    = $this->post('search_start_date', TRUE);
            $req_arr['search_end_date']    = $this->post('search_end_date', TRUE);
            $req_arr['search_tenure']    = $this->post('search_tenure', TRUE);
            
           
            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                

                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                    $req_arr['page_limit']=$pageLimit;
                    $req_arr['limit']=$limit;
                  
                    $all_cash_token=array();
                    $all_cash_token  = $this->product_model->allCashTaken($req_arr);
                    
                   
                        $result_arr['all_cash_token']         = $all_cash_token;
                        $result_arr['no_connection']         = count($all_cash_token);
                        $http_response      = 'http_response_ok';
                        $success_message    = '';  
                   
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Invalid user details'; 
                }
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
            }
        }

        $raws = array();   
        if($error_message != ''){
            $raws['error_message']      = $error_message;
        } else{
            $raws['success_message']    = $success_message;
        }        

        $raws['dataset']       = $result_arr;

        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 
    }

     
    public function getloanDetails_post(){

        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('pass_key')){
                $flag       = false;
                $error_message='passkey can not be null';
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }
            if ($this->post('user_id')<0){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

            if (!intval($this->post('loan_id'))){
                $flag       = false;
                $error_message='loan id can not be null';
            } else {
                $req_arr['loan_id']    = $this->post('loan_id', TRUE);
            }

            
           
           
            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                

                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                   
                    $all_cash_token=array();
                    $loan_details  = $this->product_model->getLoanDetail($req_arr['loan_id']);
                    $payment_details  = $this->product_model->getLoanRepaymentSchedule($req_arr['loan_id']);
                    
                   
                        $result_arr['loan_details']         = $loan_details;
                        $result_arr['payment_details']         = $payment_details;
                       
                        $http_response      = 'http_response_ok';
                        $success_message    = '';  
                   
                } else {
                    $http_response      = 'http_response_invalid_login';
                    $error_message      = 'Invalid user details'; 
                }
            } else {
                $http_response      = 'http_response_bad_request';
                $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
            }
        }

        $raws = array();   
        if($error_message != ''){
            $raws['error_message']      = $error_message;
        } else{
            $raws['success_message']    = $success_message;
        }        

        $raws['dataset']       = $result_arr;

        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 
    }


    public function getApprovedData_post(){
        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('pass_key')){
                $flag       = false;
                $error_message='passkey can not be null';
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }

            if (!intval($this->post('user_id'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

          
            

            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                
                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                    

                    $allApproveLoans=$this->product_model->getApproveLoans($req_arr);

                    $isUserApprove=$this->user_model->isUserApprove($req_arr);
                    $user_data=$this->user_model->fetchUserDeatils($req_arr['user_id']);

                    $result_arr['approve_loans']=$allApproveLoans;
                    $result_arr['is_approve']=$isUserApprove;
                    $result_arr['is_block']=$user_data['is_block'];
                    $result_arr['total_new_notifications']=$this->notifications_model->getAllNewNotifications($req_arr['user_id']);

                    $http_response      = 'http_response_ok';
                    $success_message    = '';  
                   
                    } else {
                        $http_response      = 'http_response_invalid_login';
                        $error_message      = 'Invalid user details'; 
                    }
                } else {
                    $http_response      = 'http_response_bad_request';
                    $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
                }
        }

        $raws = array();   
        if($error_message != ''){
            $raws['error_message']      = $error_message;
        } else{
            $raws['success_message']    = $success_message;
        }        

        $raws['data']       = $result_arr;
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 
    }



    public function payAmount_post(){
        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('pass_key')){
                $flag       = false;
                $error_message='passkey can not be null';
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }

            if (!intval($this->post('user_id'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

            if (!intval($this->post('loan_id'))){
                $flag       = false;
                $error_message='loan id can not be null';
            } else {
                $req_arr['loan_id']    = $this->post('loan_id', TRUE);
            }

             if (!intval($this->post('repayment_id'))){
                $flag       = false;
                $error_message='repayment id can not be null';
            } else {
                $req_arr['repayment_id']    = $this->post('repayment_id', TRUE);
            }

          
            

            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                
                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                    

                       $loan_repayment_dtl= $this->product_model->getLoanRepayment($req_arr);
                       if(is_array($loan_repayment_dtl) && count($loan_repayment_dtl)>0){

                            $loanDetails=$this->product_model->getLoanDetail($req_arr['loan_id']);
                            if($loanDetails['fk_user_id']==$req_arr['user_id']){
                                    //update repaymnet schedule table
                                    $repaymentSch=array();
                                    $repaymentSch['payment_status']='4-P';
                                    $this->product_model->updateLoanRepayment($repaymentSch,$req_arr['repayment_id']);
                                    // if it is last emi , then update loans table also
                                    $lastPaymentDtl=$this->product_model->getLoanRepaymentLast($req_arr);
                                    if($lastPaymentDtl['id']==$req_arr['repayment_id']){
                                        $loans=array();
                                        $loans['is_loan_closed']='1';
                                        $loans['loan_closed_timestamp']=date('Y-m-d H:i:s');
                                        $this->product_model->updateLoanUser($loans,$req_arr['loan_id']);

                                    }
                                    //add money to borrower 
                                    $mpokketAcountDtl=$this->product_model->getDetailsMPokketAccount($req_arr);
                                    if(is_array($mpokketAcountDtl) && count($mpokketAcountDtl)>0){
                                       $borrower_account_id=$mpokketAcountDtl['id'];
                                       $mpokket['fk_user_mpokket_account_id']=$borrower_account_id;
                                       $mpokket['transfer_amount']=$loan_repayment_dtl['borrower_emi_amount'];
                                       $mpokket['transfer_type']='P';
                                       $mpokket['transaction_date']=date('Y-m-d H:i:s');
                                       $this->product_model->addMpokketFunds($mpokket);
                                       // add to tabl cash transfer table
                                       $cash_transfer=array();
                                       $cash_transfer['fk_loan_id']=$req_arr['loan_id'];
                                       $cash_transfer['payer_id']=$req_arr['user_id']; //borrower id
                                       $cash_transfer['payee_id']=$loanDetails['loan_offered_by_user_id']; 
                                       $cash_transfer['transfer_amount']=$loan_repayment_dtl['borrower_emi_amount'];
                                       $cash_transfer['fk_transaction_type_id']='1';
                                       $cash_transfer['fk_payment_channel_id']='1';
                                       $this->product_model->addCashTransfer($cash_transfer);
                                    }

                                    //deduct money from lender
                                    $row['user_id']=$loanDetails['loan_offered_by_user_id'];
                                    $mpokketLenderAcountDtl=$this->product_model->getDetailsMPokketAccount($row);
                                    if(is_array($mpokketLenderAcountDtl) && count($mpokketLenderAcountDtl)>0){
                                        $lender_account_id=$mpokketLenderAcountDtl['id'];
                                        $mpokket=array();
                                       $mpokket['fk_user_mpokket_account_id']=$lender_account_id;
                                       $mpokket['transfer_amount']=$loan_repayment_dtl['lender_repayment_amount'];
                                       $mpokket['transfer_type']='R';
                                       $mpokket['transaction_date']=date('Y-m-d H:i:s');
                                       $this->product_model->addMpokketFunds($mpokket);
                                    }
                                    // mcoins start
                                     $scheduled_payment_date=$this->user_model->getServerTimeZone($req_arr['user_id'],$loan_repayment_dtl['scheduled_payment_date']);

                                    $scheduled_payment_date=strtotime($scheduled_payment_date);
                                    $todatetime=strtotime(date('Y-m-d'));

                                    if($todatetime > $scheduled_payment_date){
                                        $mcoin_activity_id=3;
                                        $reward_activity_id=3;
                                    }else{
                                        $mcoin_activity_id=2;
                                        $reward_activity_id=2;
                                    }
                                    // add mcoin to connections

                                    $loanMcoin['fk_user_loan_id']=$req_arr['loan_id'];
                                    $loanMcoin['fk_mcoin_activity_id']=$mcoin_activity_id;
                                    $userLoanMcoins=$this->product_model->fetchUserLoanMcoin($loanMcoin);

                                    if(is_array($userLoanMcoins) && count($userLoanMcoins)>0){

                                        $allConnections=$this->connection_model->getAllCurrentConnection($req_arr['user_id']);

                                        
                                        if(is_array($allConnections) && count($allConnections)>0){

                                            foreach($allConnections as $connections){

                                                $user_mcoin_earn['fk_user_id']=$connections['fk_user_id'];
                                                $user_mcoin_earn['fk_activity_user_id']=$req_arr['user_id'];
                                                $user_mcoin_earn['fk_mcoin_activity_id']=$mcoin_activity_id;
                                                $user_mcoin_earn['fk_loan_id']=$req_arr['loan_id'];
                                                $user_mcoin_earn['non_referred_connections']=$userLoanMcoins['non_referred_connections'];
                                                
                                              
                                                $this->product_model->addUserMcoin($user_mcoin_earn);
                                            }

                                        }


                                        // add mcoin to referred connection

                                        $isReferred=$this->user_model->isReferred($req_arr);
                                        if(is_array($isReferred) && count($isReferred)>0){
                                            $user_type['fk_user_id']=$isReferred['fk_refered_by_user_id'];
                                            $userTypes=$this->user_model->getUserTypes($user_type);
                                            if($userTypes['user_mode']=='B'){
                                                $user_mcoin_earn['fk_user_id']=$isReferred['fk_refered_by_user_id'];
                                                $user_mcoin_earn['fk_activity_user_id']=$req_arr['user_id'];
                                                $user_mcoin_earn['fk_mcoin_activity_id']=$mcoin_activity_id;
                                                $user_mcoin_earn['fk_loan_id']=$req_arr['loan_id'];
                                           
                                                $user_mcoin_earn['referred_connections']=$userLoanMcoins['referred_connections'];
                                                
                                                $this->product_model->addUserMcoin($user_mcoin_earn);
                                                //update user level mcoins
                                                $user_level['total_mcoins_points']   = $this->mcoins_model->getTotalMcoin($isReferred['fk_refered_by_user_id']);
                                                $user_level['user_id']   = $isReferred['fk_refered_by_user_id'];
                                                $this->user_model->updateUserLevel($user_level);
                   
                                            }

                                            if($userTypes['is_agent']>0){
                                                // get agent reward points
                                                $param['fk_product_id']=$req_arr['loan_id'];
                                                $param['fk_reward_activity_id']=$reward_activity_id;
                                                $reward_details=$this->agent_model->getLoanRewardEarning($param);
                                                // add agent reward points
                                                $agent_reward=array();
                                                $agent_reward['fk_user_id']=$isReferred['fk_refered_by_user_id'];
                                                $agent_reward['fk_activity_user_id']=$req_arr['user_id'];
                                                $agent_reward['fk_reward_activity_id']=$reward_activity_id;
                                                $agent_reward['fk_loan_id']=$req_arr['loan_id'];
                                                $agent_reward['reward_point']=$reward_details['reward_point'];

                                                $this->agent_model->addAgentReward($agent_reward);
                                            }
                                            

                                        }

                                        // add mcoin to own

                                        $user_mcoin_earn['fk_user_id']=$req_arr['user_id'];
                                        $user_mcoin_earn['fk_activity_user_id']=$req_arr['user_id'];
                                        $user_mcoin_earn['fk_mcoin_activity_id']=$mcoin_activity_id;
                                        $user_mcoin_earn['fk_loan_id']=$req_arr['loan_id'];
                                   
                                        $user_mcoin_earn['own_activity']=$userLoanMcoins['own_activity'];
                                        
                                        $this->product_model->addUserMcoin($user_mcoin_earn);

                                        //update user level mcoins
                                        $user_level['total_mcoins_points']   = $this->mcoins_model->getTotalMcoin($req_arr['user_id']);
                                        $user_level['user_id']   = $req_arr['user_id'];
                                        $this->user_model->updateUserLevel($user_level);



                                    }

                                    // end mcoins

                                    
                                    
                               

                                $http_response      = 'http_response_ok';
                                $error_message      = ''; 

                            }else{
                                $http_response      = 'http_response_bad_request';
                                $error_message      = 'It is not your loan'; 
                            }


                             
                       }else{
                            $http_response      = 'http_response_bad_request';
                            $success_message    = 'Wrong loan id or repaymnet id';  
                       }

                    
                   
                    } else {
                        $http_response      = 'http_response_invalid_login';
                        $error_message      = 'Invalid user details'; 
                    }
                } else {
                    $http_response      = 'http_response_bad_request';
                    $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
                }
        }

        $raws = array();   
        if($error_message != ''){
            $raws['error_message']      = $error_message;
        } else{
            $raws['success_message']    = $success_message;
        }        

        $raws['data']       = $result_arr;
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 
    }

    public function withdrawAmount_post(){
        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('pass_key')){
                $flag       = false;
                $error_message='passkey can not be null';
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }

            if (!intval($this->post('user_id'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

            if (!intval($this->post('amount'))){
                $flag       = false;
                $error_message='amount can not be null';
            } else {
                $req_arr['amount']    = $this->post('amount', TRUE);
            }

             

          
            

            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                
                if(!empty($login_status_arr) && count($login_status_arr) > 0){
                    
                        $bank_details=$this->profile_model->getBankDetails($req_arr);

                        if(is_array($bank_details) && count($bank_details)>0){


                                $userProfileDTl=$this->profile_model->fetchTempProfileMain($req_arr);
                                $userUserDTl=$this->user_model->fetchUserDeatils($req_arr['user_id']);
                                $mPokket_account=$this->product_model->getDetailsMPokketAccount($req_arr);
                                $wallet_amount=$this->product_model->getwalletAmount($mPokket_account);
                                if($req_arr['amount']<=$wallet_amount){
                                    // add  into mpokket_export table
                                    $data['fk_user_id']=$req_arr['user_id'];
                                    $data['fk_bank_id']=$bank_details['id'];
                                    $data['transaction_type']='I';
                                    $data['beneficiary_code']=$mPokket_account['mpokket_account_number'];
                                    $data['beneficiary_account_number']=$bank_details['account_number'];
                                    $data['beneficiary_name']=$userProfileDTl['f_name'].' '.$userProfileDTl['l_name'];
                                    $data['micr_code']=$bank_details['micr_code'];
                                    $data['instrument_amount']= $req_arr['amount'] ;
                                    $data['ifsc_code']=$bank_details['ifsc_code'];
                                    $data['bank_name']=$bank_details['bank_name'];
                                    $data['bank_branch']=$bank_details['bank_branch'];
                                    $data['beneficiary_email_id']=$userUserDTl['email_id'];
                                    $data['beneficiary_address_01']=$bank_details['bank_address'];
                                    $this->product_model->addmPokketExport($data);
                                    // add into mpokket_fund table
                                    $fund['fk_user_mpokket_account_id']=$mPokket_account['id'];
                                    $fund['transfer_amount']=$req_arr['amount'];
                                    $fund['transfer_type']='W';
                                    $this->product_model->addMpokketFunds($fund);

                                    $http_response      = 'http_response_ok';
                                    $success_message    = '';  

                            }else{
                                $http_response      = 'http_response_bad_request';
                                $error_message      = 'Wrong amount'; 

                            }

                           

                        }else{
                            $http_response      = 'http_response_bad_request';
                            $error_message      = 'Something went wrong! please try again'; 

                        }
                      
                   
                    } else {
                        $http_response      = 'http_response_invalid_login';
                        $error_message      = 'Invalid user details'; 
                    }
                } else {
                    $http_response      = 'http_response_bad_request';
                    $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
                }
        }

        $raws = array();   
        if($error_message != ''){
            $raws['error_message']      = $error_message;
        } else{
            $raws['success_message']    = $success_message;
        }        

        $raws['data']       = $result_arr;
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        ); 
    }


    public function updateNewNotification_post(){
        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('pass_key')){
                $flag       = false;
                $error_message='passkey can not be null';
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }

            if (!intval($this->post('user_id'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

            

            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                
                    if(!empty($login_status_arr) && count($login_status_arr) > 0){
                        
                        $this->notifications_model->updateNewNotifications($req_arr['user_id']);
                        
                        $result_arr['new_notifications']=$this->notifications_model->getAllNewNotifications($req_arr['user_id']);

                        $http_response      = 'http_response_ok';
                        $error_message      = ''; 

                   
                    } else {
                        $http_response      = 'http_response_invalid_login';
                        $error_message      = 'Invalid user details'; 
                    }
                } else {
                    $http_response      = 'http_response_bad_request';
                    $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
                }
        }

        $raws = array();   
        if($error_message != ''){
            $raws['error_message']      = $error_message;
        } else{
            $raws['success_message']    = $success_message;
        }        

        $raws['data']       = $result_arr;
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        );

    }

    public function updateUnreadNotification_post(){
        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('pass_key')){
                $flag       = false;
                $error_message='passkey can not be null';
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }

            if (!intval($this->post('user_id'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

            if (!intval($this->post('notification_id'))){
                $flag       = false;
                $error_message='notification id can not be null';
            } else {
                $req_arr['notification_id']    = $this->post('notification_id', TRUE);
            }

            

            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                
                    if(!empty($login_status_arr) && count($login_status_arr) > 0){
                        
                        $this->notifications_model->updateUnreadNotifications($req_arr);
                        

                        $http_response      = 'http_response_ok';
                        $error_message      = ''; 

                   
                    } else {
                        $http_response      = 'http_response_invalid_login';
                        $error_message      = 'Invalid user details'; 
                    }
                } else {
                    $http_response      = 'http_response_bad_request';
                    $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
                }
        }

        $raws = array();   
        if($error_message != ''){
            $raws['error_message']      = $error_message;
        } else{
            $raws['success_message']    = $success_message;
        }        

        $raws['data']       = $result_arr;
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        );

    }
    public function getAllNotification_post(){
        $error_message = $success_message = $http_response = '';
        $result_arr = array();
        if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {

            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        
        } else {

            $flag           = true;
            $req_arr        = array();

            if (!$this->post('pass_key')){
                $flag       = false;
                $error_message='passkey can not be null';
            } else {
                $req_arr['pass_key']    = $this->post('pass_key', TRUE);
            }

            if (!intval($this->post('user_id'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

            if (!intval($this->post('page'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['page']    = $this->post('page', TRUE);
            }

            if (!intval($this->post('page_size'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['page_size']    = $this->post('page_size', TRUE);
            }

            $page = $req_arr['page'];

            if (!empty($page)) {
                $limit = $req_arr['page_size'];

                if ($page > 1) {
                    $id = $page - 1;
                } else {
                    $id = '0';
                }
                $pageLimit = $limit * $id;
            }


            
          
            

            if($flag) {
                //log in status checking
                $login_status_arr       = $this->login_model->login_status_checking($req_arr);
                
                    if(!empty($login_status_arr) && count($login_status_arr) > 0){
                        
                        $all_notifications=$this->notifications_model->getAllNotifications($req_arr['user_id'],$limit,$pageLimit);
                        $total_all_notifications=$this->notifications_model->getAllTotalNotifications($req_arr['user_id']);
                        $data_notification=array();
                        if(is_array($all_notifications) && count($all_notifications)>0){
                            $i=0;
                            foreach($all_notifications as $notifications){
                                $data_notification[$i]['id']=$notifications['id'];
                                $data_notification[$i]['fk_user_id']=$notifications['fk_user_id'];
                                $data_notification[$i]['notification_message']=$notifications['notification_message'];
                               
                                $data_notification[$i]['is_unread']=$notifications['is_unread'];
                                $data_notification[$i]['is_new']=$notifications['is_new'];
                                $json_decode_array=json_decode($notifications['routing_json'],true);
                                $data_notification[$i]['notification_code']=$json_decode_array['notification_code'];
                                $data_notification[$i]['display_name']=$json_decode_array['display_name'];
                                $data_notification[$i]['img_url']=$json_decode_array['img_url'];

                                $notification_timestamp=$this->user_model->getServerTimeZone($req_arr['user_id'],$notifications['notification_timestamp']);

                                $data_notification[$i]['notification_date']=date('M d,Y h:mA',strtotime($notification_timestamp));

                                $i++;
                            }
                        }
                        $result_arr['all_notifications']=$data_notification;
                        $result_arr['total_notifications']=count($data_notification);
                        $result_arr['total_all_notifications']=$total_all_notifications;
                        $result_arr['new_notifications']=$this->notifications_model->getAllNewNotifications($req_arr['user_id']);

                        $http_response      = 'http_response_ok';
                        $error_message      = ''; 

                   
                    } else {
                        $http_response      = 'http_response_invalid_login';
                        $error_message      = 'Invalid user details'; 
                    }
                } else {
                    $http_response      = 'http_response_bad_request';
                    $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
                }
        }

        $raws = array();   
        if($error_message != ''){
            $raws['error_message']      = $error_message;
        } else{
            $raws['success_message']    = $success_message;
        }        

        $raws['data']       = $result_arr;
        $raws['publish']    = $this->publish;

        //response in json format
        $this->response(
            array(
                'raws' => $raws
            ), $this->config->item($http_response)
        );
    }

    public function downloadPDFLoanDtl_post() {
        

            $flag           = true;
            $req_arr        = array();

           
            if ($this->post('user_id')<0){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

            if (!intval($this->post('loan_id'))){
                $flag       = false;
                $error_message='loan id can not be null';
            } else {
                $req_arr['loan_id']    = $this->post('loan_id', TRUE);
            }

           
           
            if($flag) {

                        $userDtl=$this->user_model->fetchUserDeatils($req_arr['user_id']);
                        $loan_details  = $this->product_model->getLoanDetail($req_arr['loan_id']);
                        $payment_details  = $this->product_model->getLoanRepaymentSchedule($req_arr['user_id']);
                        
                       
                            $row['loan_details']         = $loan_details;
                            $row['payment_details']         = $payment_details;
                       
                        $html = $this->load->view('pdf/test', $row, true);
                        //$header = $this->load->view('pdf/header', $row, true);
                        
                        $file_name = 'loan_details' . date('d_m_Y') . '.pdf';

                        $this->load->library('mpdf');
                    
                        $mpdf = new mPDF('utf-8', 'A4');
                        $mpdf->debug = true;
                        $mpdf->setAutoTopMargin='stretch';
                        $mpdf->mirrorMargins = 0; // Use different Odd/Even headers and footers and mirror margins
                        // set html header for odd pages, write html and output
                        // --------------------------------------------------------------------------
                        //$mpdf->SetHTMLHeader('{PAGENO}/{nb}');
                        //$mpdf->SetHTMLHeader($header);
                        
                        
                        $mpdf->SetHTMLFooter('<span style="margin-left:250px;"> Page {PAGENO} of {nb} </span>');
                        /* Note: SetHTMLHeader() and SetHTMLFooter() without a side(2nd argument) 
                          - sets ODD page header/footer only as default..so you can also write just
                          $mpdf->SetHTMLHeader('{PAGENO}/{nb}'); */
                        $mpdf->WriteHTML($html);
                        //$mpdf->Output();
                         //$mpdf->Output($file_name, 'D');
                        $file_path='/var/www/html/apis/assets/uploads/pdf/';
                        $mpdf->Output($file_path.$file_name, 'F');

                        
                        //email send  code start
                        //initialising codeigniter email
                        $email_config=email_config();
                        $this->email->initialize($email_config);
                        // email sent to buyer 
                        $admin_email= $this->config->item('admin_email');
                        $admin_email_from= $this->config->item('admin_email_from');
                        $this->email->from($admin_email, $admin_email_from);

                      
                        $this->email->to($userDtl['email_id']); 
                        $subject='Transaction details -PDF';                                
                        $this->email->subject($subject);
                        $message='';
                        $email_data['message']= $message;  
                        $email_body= $this->parser->parse('email_templates/emailpdf', $email_data, true);
                       
                        $this->email->attach($file_path.$file_name);
                        $this->email->message($email_body);            
                        $this->email->send();
                        // email send end 
                        $http_response      = 'http_response_ok';
                        $error_message      = ''; 
                               
                } else {
                    $http_response      = 'http_response_bad_request';
                    $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
                }
       

            $raws = array();   
            if($error_message != ''){
                $raws['error_message']      = $error_message;
            } else{
                $raws['success_message']    = $success_message;
            }        

            
            $raws['publish']    = $this->publish;

            //response in json format
            $this->response(
                array(
                    'raws' => $raws
                ), $this->config->item($http_response)
            );

        //echo $html;
    }


    public function downloadPDFLoan_post() {
       

            $flag           = true;
            $req_arr        = array();

            
            if ($this->post('user_id')<0){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

            if (!intval($this->post('page'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['page']    = $this->post('page', TRUE);
            }

             if (!$this->post('user_type')){
                $flag       = false;
                $error_message='user type can not be null';
            } else {
                $req_arr['user_type']    = $this->post('user_type', TRUE);
            }

            if (!intval($this->post('page_size'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['page_size']    = $this->post('page_size', TRUE);
            }

            $page = $req_arr['page'];

            if (!empty($page)) {
                $limit = $req_arr['page_size'];

                if ($page > 1) {
                    $id = $page - 1;
                } else {
                    $id = '0';
                }
                $pageLimit = $limit * $id;
            }

            $req_arr['search_status']    = $this->post('search_status', TRUE);
            $req_arr['search_start_date']    = $this->post('search_start_date', TRUE);
            $req_arr['search_end_date']    = $this->post('search_end_date', TRUE);
            $req_arr['search_tenure']    = $this->post('search_tenure', TRUE);
           
           
            if($flag) {
              
                    $req_arr['page_limit']=$pageLimit;
                    $req_arr['limit']=$limit;
                    $userDtl=$this->user_model->fetchUserDeatils($req_arr['user_id']);
                    $all_cash_token=array();
                    $all_cash_token  = $this->product_model->allCashTaken($req_arr);
                    
                   
                        $row['all_cash_token']         = $all_cash_token;
                        $row['no_connection']         = count($all_cash_token);

                        $html = $this->load->view('pdf/allloan', $row, true);
                        //$header = $this->load->view('pdf/header', $row, true);
                        
                        $file_name = 'loan_details' . date('d_m_Y') . '.pdf';

                        $this->load->library('mpdf');
                    
                        $mpdf = new mPDF('utf-8', 'A4');
                        $mpdf->debug = true;
                        $mpdf->setAutoTopMargin='stretch';
                        $mpdf->mirrorMargins = 0; // Use different Odd/Even headers and 
                        // set html header for odd pages, write html and output
                        // --------------------------------------------------------------------------
                        //$mpdf->SetHTMLHeader('{PAGENO}/{nb}');
                        //$mpdf->SetHTMLHeader($header);
                        
                        
                        $mpdf->SetHTMLFooter('<span style="margin-left:250px;"> Page {PAGENO} of {nb} </span>');
                        /* Note: SetHTMLHeader() and SetHTMLFooter() without a side(2nd argument) 
                          - sets ODD page header/footer only as default..so you can also write just
                          $mpdf->SetHTMLHeader('{PAGENO}/{nb}'); */
                        $mpdf->WriteHTML($html);
                        //$mpdf->Output();
                        
                        $file_path='/var/www/html/apis/assets/uploads/pdf/';
                        $mpdf->Output($file_path.$file_name, 'F');

                        
                        //email send  code start
                        //initialising codeigniter email
                        $email_config=email_config();
                        $this->email->initialize($email_config);
                        // email sent to buyer 
                        $admin_email= $this->config->item('admin_email');
                        $admin_email_from= $this->config->item('admin_email_from');
                        $this->email->from($admin_email, $admin_email_from);

                      
                        $this->email->to($userDtl['email_id']); 
                         $subject='Transaction -PDF';                                
                        $this->email->subject($subject);
                        $message='';
                        $email_data['message']= $message;  
                        $email_body= $this->parser->parse('email_templates/emailpdf', $email_data, true);
                       
                        $this->email->attach($file_path.$file_name);
                        $this->email->message($email_body);            
                        $this->email->send();
                        // email send end 
                        $http_response      = 'http_response_ok';
                        $error_message      = ''; 
                               
                } else {
                    $http_response      = 'http_response_bad_request';
                    $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
                }
       

            $raws = array();   
            if($error_message != ''){
                $raws['error_message']      = $error_message;
            } else{
                $raws['success_message']    = $success_message;
            }        

            $raws['data']       = $result_arr;
            $raws['publish']    = $this->publish;

            //response in json format
            $this->response(
                array(
                    'raws' => $raws
                ), $this->config->item($http_response)
            );
            

            //echo $html;
    }

    public function downloadXLSLoan_post() {
       

            $flag           = true;
            $req_arr        = array();

            
            if ($this->post('user_id')<0){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

            if (!intval($this->post('page'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['page']    = $this->post('page', TRUE);
            }

             if (!$this->post('user_type')){
                $flag       = false;
                $error_message='user type can not be null';
            } else {
                $req_arr['user_type']    = $this->post('user_type', TRUE);
            }

            if (!intval($this->post('page_size'))){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['page_size']    = $this->post('page_size', TRUE);
            }

            $page = $req_arr['page'];

            if (!empty($page)) {
                $limit = $req_arr['page_size'];

                if ($page > 1) {
                    $id = $page - 1;
                } else {
                    $id = '0';
                }
                $pageLimit = $limit * $id;
            }

            $req_arr['search_status']    = $this->post('search_status', TRUE);
            $req_arr['search_start_date']    = $this->post('search_start_date', TRUE);
            $req_arr['search_end_date']    = $this->post('search_end_date', TRUE);
            $req_arr['search_tenure']    = $this->post('search_tenure', TRUE);
           
           
            if($flag) {
                    
                    $userDtl=$this->user_model->fetchUserDeatils($req_arr['user_id']);
                    $req_arr['page_limit']=$pageLimit;
                    $req_arr['limit']=$limit;
                  
                    $all_cash_token=array();
                    $all_cash_token  = $this->product_model->allCashTaken($req_arr);
                    
                   
                        $row['all_cash_token']         = $all_cash_token;
                        $row['no_connection']         = count($all_cash_token);

                        $cell['title']='';
                        $cell['highestCol'] = 3;
                        $cell['field'][0]['H']=array('Date','Amount','Status');
                        $cell['flag']=1;

                        if(is_array($all_cash_token) && count($all_cash_token)>0){
                            $i=0;
                            foreach($all_cash_token as $allcashtoken){
                                $index++;
                                $cell['field'][$index]['F']=array(
                                    array('W'=>$allcashtoken['sch_date']),
                                    array('N'=>$allcashtoken['scheduled_emi']),
                                    array('N'=>$allcashtoken['payment_status']),
                                );

                            }

                        }
                        $cell['description']='Transaction';
                        $file_name="transaction_".date('Y-m-d-H-i-s');
                        $save_path="/var/www/html/apis/assets/uploads/xlss/";
                        $response=$this->createExcel($cell,$save_path,$file_name);
                       
                        //email send  code start
                        //initialising codeigniter email
                         $email_config=email_config();
                        $this->email->initialize($email_config);
                        // email sent to buyer 
                        $admin_email= $this->config->item('admin_email');
                        $admin_email_from= $this->config->item('admin_email_from');
                        $this->email->from($admin_email, $admin_email_from);

                      
                        $this->email->to($userDtl['email_id']); 
                        $subject='Transaction -XLS';                                
                        $this->email->subject($subject);
                        $message='';
                        $email_data['message']= $message;  
                        $email_body= $this->parser->parse('email_templates/emailpdf', $email_data, true);
                       
                        $this->email->attach($save_path.$file_name.".xls");
                        $this->email->message($email_body);            
                        $this->email->send();
                        // email send end 
                        $http_response      = 'http_response_ok';
                        $error_message      = ''; 
                               
                } else {
                    $http_response      = 'http_response_bad_request';
                    $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
                }
       

            $raws = array();   
            if($error_message != ''){
                $raws['error_message']      = $error_message;
            } else{
                $raws['success_message']    = $success_message;
            }        

            $raws['data']       = $result_arr;
            $raws['publish']    = $this->publish;

            //response in json format
            $this->response(
                array(
                    'raws' => $raws
                ), $this->config->item($http_response)
            );
            

            //echo $html;
    }


    public function downloadXLSLoanDtl_post() {
        

            $flag           = true;
            $req_arr        = array();

           
            if ($this->post('user_id')<0){
                $flag       = false;
                $error_message='user id can not be null';
            } else {
                $req_arr['user_id']    = $this->post('user_id', TRUE);
            }

            if (!intval($this->post('loan_id'))){
                $flag       = false;
                $error_message='loan id can not be null';
            } else {
                $req_arr['loan_id']    = $this->post('loan_id', TRUE);
            }

           
           
            if($flag) {

                        $userDtl=$this->user_model->fetchUserDeatils($req_arr['user_id']);
                        $loan_details  = $this->product_model->getLoanDetail($req_arr['loan_id']);
                        $payment_details  = $this->product_model->getLoanRepaymentSchedule($req_arr['user_id']);
                        
                       
                           

                        $cell['title']='';
                        $cell['highestCol'] = 3;
                        $cell['field'][0]['H']=array('Date','Amount','Status');
                        $cell['flag']=1;

                        if(is_array($payment_details) && count($payment_details)>0){
                            $i=0;
                            foreach($payment_details as $paymentdetails){
                                $index++;
                               
                                $pStatis='';
                                if($paymentdetails['payment_status']=='1-O'){
                                   $pStatis='Overdue';
                                }else if($paymentdetails['payment_status']=='2-D'){
                                   $pStatis='Due';
                                }else if($paymentdetails['payment_status']=='3-U'){
                                    $pStatis='Upcoming';
                                }else if($paymentdetails['payment_status']=='4-P-'){
                                    $pStatis='paid';
                                }

                                $cell['field'][$index]['F']=array(
                                    array('W'=>$paymentdetails['sch_date']),
                                    array('N'=>$paymentdetails['borrower_emi_amount']),
                                    array('N'=>$pStatis),
                                );

                            }

                        }
                        $cell['description']='Transaction Details';
                        $file_name="transaction_details_".date('Y-m-d-H-i-s');
                        $save_path="/var/www/html/apis/assets/uploads/xlss/";
                        $response=$this->createExcel($cell,$save_path,$file_name);
                       
                        //email send  code start
                        //initialising codeigniter email
                        $email_config=email_config();
                        $this->email->initialize($email_config);
                        // email sent to buyer 
                        $admin_email= $this->config->item('admin_email');
                        $admin_email_from= $this->config->item('admin_email_from');
                        $this->email->from($admin_email, $admin_email_from);

                      
                        $this->email->to($req_arr['email_id']); 
                        $subject='Transaction Details -XLS';                                
                        $this->email->subject($subject);
                        $message='';
                        $email_data['message']= $message;  
                        $email_body= $this->parser->parse('email_templates/emailpdf', $email_data, true);
                       
                        $this->email->attach($save_path.$file_name.".xls");           
                        $this->email->send();
                        // email send end 
                        $http_response      = 'http_response_ok';
                        $error_message      = ''; 
                               
                } else {
                    $http_response      = 'http_response_bad_request';
                    $error_message      = ($error_message != '') ? $error_message : 'Invalid parameter';   
                }
       

            $raws = array();   
            if($error_message != ''){
                $raws['error_message']      = $error_message;
            } else{
                $raws['success_message']    = $success_message;
            }        

            
            $raws['publish']    = $this->publish;

            //response in json format
            $this->response(
                array(
                    'raws' => $raws
                ), $this->config->item($http_response)
            );

        //echo $html;
    }


    public function getVerificationCode(){
        $microTime = microtime();
        list($a_dec, $a_sec) = explode(" ", $microTime);
        $dec_hex = dechex($a_dec * 1000000);
        $sec_hex = dechex($a_sec);
        $this->ensure_length($dec_hex, 2);
        $this->ensure_length($sec_hex, 2);
        $guid = "";
        $guid .= $dec_hex;
        $guid .= $this->create_guid_section(2);
        $guid .= $sec_hex;
        $guid .= $this->create_guid_section(2);
        return $guid;
    }


    
   

    public function ensure_length(&$string, $length) {
        $strlen = strlen($string);
        if ($strlen < $length) {
            $string = str_pad($string, $length, "0");
        } else if ($strlen > $length) {
            $string = substr($string, 0, $length);
        }
    }

    public function create_guid_section($characters) {
        $return = "";
        for ($i = 0; $i < $characters; $i++) {
            $return .= dechex(mt_rand(0, 15));
        }
        return $return;
    }

    function test_get(){

        $this->sms->category="TESTMSG";
        $this->sms->ignore = 'Mousumi';
        $this->sms->mobile = '9874314610';
        $response = $this->sms->sendSmsFinal();
        //print_r($response);
    }    
    


    public function createExcel($cell,$save_path,$file_name)
    {
        
        
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("A & M")
                                     ->setLastModifiedBy("A & M")
                                     ->setTitle("Office 2007 XLSX Report Document")
                                     ->setSubject("Office 2007 XLSX Report Document")
                                     ->setDescription($cell['description'])
                                     ->setKeywords("office 2007 openxml php")
                                     ->setCategory("Report file");
        
        if($cell['flag']==1)
        {
            //SETTING HEADER
            $asciiStart = 65;
            $totalVals = $cell['highestCol'];
            $getUptoCellMerg = $asciiStart+$totalVals-1;
            $getUptoCellMerg = chr($getUptoCellMerg);
            //exit;
            $styleArray = array(
                    'borders' => array(
                          'outline' => array(
                                 'style' => PHPExcel_Style_Border::BORDER_THIN,
                                 'color' => array('argb' => '000000'),
                          ),
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
             );
            
            $styleArrayHeader = array(
                    'borders' => array(
                          'outline' => array(
                                 'style' => PHPExcel_Style_Border::BORDER_THIN,
                                 'color' => array('argb' => '000000')
                          ),
                    ),
                    'font' => array(
                        'bold' => true
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
             );
            
            for($i=0;$i<$totalVals;$i++)
            {
                $clIndx = $asciiStart+$i;
                $clchars = chr($clIndx);
                $objPHPExcel->getActiveSheet()
                ->getColumnDimension($clchars)
                ->setWidth(24);
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
            foreach($cell['field'] as $fldKey=>$fldVal)
            {
                if(isset($fldVal['H']))
                {
                    $cellCharToStartAscii = $asciiStart;
                    
                    foreach($fldVal['H'] as $hkey=>$hval)
                    {
                        $cellCharToStart = chr($cellCharToStartAscii);
                        $fieldValue = $hval;
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellCharToStart.$titleCount, $fieldValue);
                        $objPHPExcel->getActiveSheet()->getStyle($cellCharToStart.$titleCount.':'.$cellCharToStart.$titleCount)->applyFromArray($styleArrayHeader);
                        $cellCharToStartAscii++;
                        if($borderCount1==0)
                        {
                            $borderStart1 = $cellCharToStart.$titleCount;
                        }
                        $borderCount1++;
                    }
                    $titleCount++;
                }
                if(isset($fldVal['F']))
                {
                    $cellCharToStartAscii = $asciiStart;
                    foreach($fldVal['F'] as $fkey=>$fval)
                    {
                        foreach($fval as $fkey1=>$fval1)
                        {
                            //pr($fval);
                            $cellCharToStart = chr($cellCharToStartAscii);
                            $fieldValue = $fval1;
                            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellCharToStart.$titleCount, $fieldValue);
                            $objPHPExcel->getActiveSheet()->getStyle($cellCharToStart.$titleCount.':'.$cellCharToStart.$titleCount)->applyFromArray($styleArray);
                            if($fkey1=='W')
                            {
                                $objPHPExcel->getActiveSheet()->getStyle($cellCharToStart.$titleCount.':'.$cellCharToStart.$titleCount)->applyFromArray(array('alignment' => array('wrap' => false,'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT)));
                            }
                            elseif($fkey1=='C')
                            {
                                $objPHPExcel->getActiveSheet()->getStyle($cellCharToStart.$titleCount.':'.$cellCharToStart.$titleCount)->applyFromArray(array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)));
                            }
                        }
                        $cellCharToStartAscii++;
                    }
                    $titleCount++;
                }
                $borderEnd1 = $cellCharToStart.($titleCount-1);
                if(isset($fldVal['FC']))
                {
                    $cellCharToStartAscii = $asciiStart;
                    $cellCharToStart = chr($cellCharToStartAscii);
                    $fieldValue = "";
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellCharToStart.$titleCount, $fieldValue);
                    $titleCount++;
                }
            }
            //$objPHPExcel->getActiveSheet()->getStyle($borderStart1.':'.$borderEnd1)->applyFromArray(array('borders' => array('outline' => array('style' => PHPExcel_Style_Border::BORDER_THICK,'color' => array('argb' => '000000')))));
//            echo $borderStart1.':'.$borderEnd1;
//            exit;
            //SET FOOTER VALUES TO EXCEL
            if(isset($cell['footer']))
            {
                //COUNT NUMBER OF FOOTER ROW
                $totalFooterRow = count($cell['footer']);
                foreach($cell['footer'] as $fldKey=>$fldVal)
                {
                    if(isset($fldVal['H']))
                    {
                        $cellCharToStartAscii = $asciiStart;
                        foreach($fldVal['H'] as $hkey=>$hval)
                        {
                            $cellCharToStart = chr($cellCharToStartAscii);
                            $fieldValue = $hval;
                            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellCharToStart.$titleCount, $fieldValue);
                            $objPHPExcel->getActiveSheet()->getStyle($cellCharToStart.$titleCount.':'.$cellCharToStart.$titleCount)->applyFromArray($styleArrayHeader);
                            $cellCharToStartAscii++;
                        }
                        $titleCount++;
                    }
                    if(isset($fldVal['F']))
                    {
                        $cellCharToStartAscii = $asciiStart;
                        foreach($fldVal['F'] as $fkey=>$fval)
                        {
                            foreach($fval as $fkey1=>$fval1)
                            {
                                $cellCharToStart = chr($cellCharToStartAscii);
                                $fieldValue = $fval1;
                                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellCharToStart.$titleCount, $fieldValue);
                                $objPHPExcel->getActiveSheet()->getStyle($cellCharToStart.$titleCount.':'.$cellCharToStart.$titleCount)->applyFromArray($styleArray);
                                if($fkey1=='W')
                                {
                                    $objPHPExcel->getActiveSheet()->getStyle($cellCharToStart.$titleCount.':'.$cellCharToStart.$titleCount)->applyFromArray(array('alignment' => array('wrap' => true,'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT)));
                                }
                                elseif($fkey1=='C')
                                {
                                    $objPHPExcel->getActiveSheet()->getStyle($cellCharToStart.$titleCount.':'.$cellCharToStart.$titleCount)->applyFromArray(array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)));
                                }
                            }
                            $cellCharToStartAscii++;
                        }
                    }
                    $titleCount++;
                    if(isset($fldVal['FC']))
                    {
                        $cellCharToStartAscii = $asciiStart;
                        $cellCharToStart = chr($cellCharToStartAscii);
                        $fieldValue = "";
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellCharToStart.$titleCount, $fieldValue);
                        $titleCount++;
                    }
                }
            }
            
            $titleCount++;
            //$fieldValue = html_entity_decode(COPYRIGHT);
            $fieldValue = "Copyright © ".date('Y')." mPokket Q All rights reserved..";
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$titleCount, $fieldValue);
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$titleCount.':D'.$titleCount);
            
            if($totalVals<=5)
            {
                $generateReportCellStart = 'A';
                $titleCount++;
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$titleCount.':D'.$titleCount);
                $objPHPExcel->getActiveSheet()->getStyle($generateReportCellStart.$titleCount.':'.$generateReportCellStart.$titleCount)->applyFromArray(array('font'=>array('italic'=>true),'alignment'=>array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT)));
            }
            else
            {
                $generateReportCell = $asciiStart+$totalVals-1;
                $generateReportCellStart = chr($generateReportCell-1);
                $generateReportCellEnd = chr($generateReportCell);
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells($generateReportCellStart.$titleCount.':'.$generateReportCellEnd.$titleCount);
                $objPHPExcel->getActiveSheet()->getStyle($generateReportCellStart.$titleCount.':'.$generateReportCellEnd.$titleCount)->applyFromArray(array('font'=>array('italic'=>true),'alignment'=>array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)));
            }
            
            $fieldValue = 'Report Generated On '.date('d-m-Y g:i:s A');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($generateReportCellStart.$titleCount, $fieldValue);
            //$objPHPExcel->getActiveSheet()->getStyle('F'.$titleCount.':'.'F'.$titleCount)->applyFromArray($styleArray);
        }
        else
        {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'No Record Found');
        }


       
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Simple');



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
        $save_path=$save_path.$file_name;
        $objWriter->save($save_path.'.xls');
        unset($objPHPExcel);
        return true;
      /*  $objWriter->save('php://output');*/

       
    }


    /* -------------------------------------

    //end of user controller
    */
}