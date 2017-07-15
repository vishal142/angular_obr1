<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
* --------------------------------------------------------------------------
* @ Controller Name          : All the Employee related api call from employee controller
* @ Added Date               : 28-05-2017
* @ Added By                 : Vishal
* -----------------------------------------------------------------
* @ Description              : This is the Employee index page
* -----------------------------------------------------------------
* @ return                   : array
* -----------------------------------------------------------------
* @ Modified Date            : 28-05-2017
* @ Modified By              : Vishal
* 
*/

//All the required library file for API has been included here 
/*require APPPATH . 'libraries/api/AppExtrasAPI.php';
require APPPATH . 'libraries/api/AppAndroidGCMPushAPI.php';
require APPPATH . 'libraries/api/AppApplePushAPI.php';*/

require_once('src/OAuth2/Autoloader.php');
require APPPATH . 'libraries/api/REST_Controller.php';


class Employee extends REST_Controller{
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
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/admin/employee_model', 'employee');
        $this->load->library('form_validation');
        $this->load->library('email');
        $this->load->library('encrypt');

        //$this->load->library('calculation');

       $this->encryption->initialize(array(
            'cipher' => 'aes-256',
            'mode'   => 'ctr',
            'key'    => 'SAGLcHZ6nxEBnE4XlJ1nmcPTZaOXOGIX',
        ));


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


function getAllEmployee_post(){
 
 $error_message = $success_message = $http_response = '';
 $result_arr = array();
if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) 
        {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        }
    else{

    $req_arr = $details_arr = array();
    //pre($this->input->post(),1);
    $flag = true;
    //echo $this->post('pass_key',true); exit();
    if(empty($this->post('pass_key',true))){
    $flag = false;
    $error_message = "pass key is required";
    }else{
      $req_arr['pass_key'] = $this->post('pass_key',true);

     }
    if(empty($this->post('admin_user_id',true))){
        $flag = false;
        $error_message = "admin user id is required";
    }else{
        $req_arr['admin_user_id'] = $this->post('admin_user_id',true);
    }

     if(empty($this->post('page',true))){
        $flag = false;
        $error_message = "page is required";
     }else{
         $req_arr['page'] = $this->post('page',true);

     }

        if($flag && empty($this->post('page_size', true)))
        {
            $flag           = false;
            $error_message  = "Page Size is required";
        }
        else
        {
            $req_arr['page_size']  = $this->post('page_size', true);
        }


$req_arr['order']           = $this->input->post('order', true);
$req_arr['order_by']        = $this->input->post('order_by', true);
$req_arr['searchByName']    = $this->input->post('searchByName', true);

if($flag){

    $check_user = array(
    'pass_key'      => $this->encrypt->decode($req_arr['pass_key']),
    'admin_user_id' => $this->encrypt->decode($req_arr['admin_user_id']),
      );
   $checkloginstatus = $this->employee->checkSessionExist($check_user);
   if(!empty($checkloginstatus) && count($checkloginstatus) > 0)
     {
       $details_arr['dataset']     = $this->employee->getAllEmployee($req_arr);
        $count   = $this->employee->getAllEmployeeCount($req_arr);
                    $details_arr['count']       = $count['count_employee'];
                    //pre($details_arr,1);

                    if(!empty($details_arr) && count($details_arr) > 0)
                    {
                        $result_arr         = $details_arr;
                        $http_response      = 'http_response_ok';
                        $success_message    = 'All Employee';  
                    } 
                    else 
                    {
                        $http_response      = 'http_response_bad_request';
                        $error_message      = 'Something went wrong in API';  
                    }
                }

                else{
                    $http_response  = 'http_response_invalid_login';
                    $error_message  = 'User is invalid';

                }
            }else{
                $http_response = 'http_response_bad_request';
            }
  }
 

 json_response($result_arr, $http_response, $error_message, $success_message);
}
    

function addEmployee_post(){
$error_message = $success_message = $http_response = '';
 $result_arr = array();
if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) 
        {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        }
    else{

    $req_arr = $details_arr = array();
    //pre($this->input->post(),1);
    $flag = true;
    //echo $this->post('pass_key',true); exit();
    if(empty($this->post('name',true))){
      $flag = false;
      $error_message = "Employee Name is required";

    }else{
        $req_arr['name'] = $this->post('name',true);
    }
    if(empty($this->post('email',true))){

        $flag = false;
        $error_message = "Employee Email is required";

    }else{
        $req_arr['email'] = $this->post('email',true);

    }
    if(empty($this->post('phone',true))){
        $flag = false;
        $error_message = "Employee Phone is required";
   }else{
    $req_arr['phone'] = $this->post('phone',true);
   }
   if(empty($this->post('designation',true))){
    $flag = false;
    $error_message = "Employee designation is required";

   }else{
     $req_arr['designation']= $this->post('designation',true);

   }

   if(empty($this->post('company',true))){
    $flag = false;
    $error_message = "Employee company is required";

   }else{
     $req_arr['company']= $this->post('company',true);
   }

   if(empty($this->post('state',true))){
    $flag = false;
    $error_message = "Employee state is required";

   }else{
    $req_arr['state']= $this->post('state',true);
   }

      
    $req_arr['address']= $this->post('address',true);
    $req_arr['detail']= '';
      
      
      $req_arr['date_of_joing'] = date('Y-m-d');
     if($flag){
    $checkDuplicateEmployee = $this->employee->checkDuplicateEmployee($req_arr);

    if(empty($checkDuplicateEmployee)){
         $emp_id = $this->employee->add_employee($req_arr);

         if(!empty($emp_id)){

         $http_response = 'http_response_ok';
         $success_message = 'Add Employee successfully';

         }else{
         $http_response  = 'http_response_bad_request';
         $error_message  = 'There is some problem, please try again';
         }

    }else{
        $http_response = 'http_response_bad_request';
        $error_message = 'Employee Name already exists, please try another name';
    }
}else{
    $http_response = 'http_response_bad_request';
}

}

json_response($result_arr, $http_response, $error_message, $success_message);
}


function getEmployeeDetail_post(){
$error_message = $success_message = $http_response = '';
$result_arr = array();
if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) 
        {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        }
else{

      $req_arr = $details_arr = array();
      $flag = true;

      if(empty($this->post('employeeID')))
            {
                $flag = false;
                $error_message = "Employee Id is required";
            }else{
              $req_arr['employeeID'] = $this->post('employeeID');
            }

           if($flag){
            $employee_detail = $this->employee->getEmployeeById($req_arr);
            $req_arr['dataset']= $employee_detail;
            $http_response    = 'http_response_ok';

           }else{
                $http_response      = 'http_response_bad_request';
           }

    }

      json_response($req_arr,$http_response,$error_message,$success_message);
}

public function updateEmployeeDetail_post(){

 $error_message = $success_message = $http_response ='';
 $result_arr = array();

if(!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals()))
  {
    $error_message = 'Invalid Token';
    $http_response = 'http_response_unauthorized';
 }else{

    $req_arr = $details_arr = array();
    $flag = true;

    if(empty($this->post('id',true))){

        $flag = false;
        $error_message = 'Employee Id is required';
    }else{
        $req_arr['employee_id'] = $this->post('id');
    }

    if(empty($this->post('name',true))){
      $flag = false;
      $error_message = "Employee Name is required";

    }else{
        $req_arr['name'] = $this->post('name',true);
    }
    if(empty($this->post('email',true))){

        $flag = false;
        $error_message = "Employee Email is required";

    }else{
        $req_arr['email'] = $this->post('email',true);

    }
    if(empty($this->post('phone',true))){
        $flag = false;
        $error_message = "Employee Phone is required";
   }else{
    $req_arr['phone'] = $this->post('phone',true);
   }
   if(empty($this->post('designation',true))){
    $flag = false;
    $error_message = "Employee designation is required";

   }else{
     $req_arr['designation']= $this->post('designation',true);

   }

   if(empty($this->post('company',true))){
    $flag = false;
    $error_message = "Employee company name is required";

   }else{
     $req_arr['company']= $this->post('company',true);
   }

   if(empty($this->post('state',true))){
    $flag = false;
    $error_message = "Employee state is required";

   }else{
    $req_arr['state']= $this->post('state',true);
   }

      
    $req_arr['address']= $this->post('address',true);
    $req_arr['detail']= '';

  
  
  if($flag){
    $checkEmployee = $this->employee->checkDuplicateEmployee($req_arr);
    if(empty($checkEmployee)){
        $degreeId = $this->employee->updateEmployee(array('id' => $req_arr['employee_id']), 
            array(
            'name' => $req_arr['name'],
            'email'=> $req_arr['email'],
            'phone'=> $req_arr['phone'],
            'designation'=> $req_arr['designation'],
            'company'=> $req_arr['company'],
            'state' => $req_arr['state'],
            'address'=> $req_arr['address'] ,
            ));

           $http_response      = 'http_response_ok';
           $success_message    = 'Updated employee successfully';
   

    }else{
        $http_response = 'http_response_bad_request';
        $error_message = 'Employee Name already exists, please try another name';
    }


  }else{
    $http_response = 'http_response_bad_request';
  }

 
 }

 json_response($result_arr, $http_response, $error_message, $success_message);

}


function deleteEmployee_post(){

   $error_message = $success_message = $http_response = "";
   $req_arr = array();

   if(!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals()))
  {
    $error_message = 'Invalid Token';
    $http_response = 'http_response_unauthorized';
 }else{
    $req_arr = $details_arr = array();
    $flag = true;

    if(empty($this->post('employeeID',true))){
        $flag = false;
        $error_message = 'employee id is required';
    }else{
       $req_arr['employeeID'] =  $this->post('employeeID'); 
    }
    if(empty($this->post('pass_key',true))){
    $flag = false;
    $error_message = "pass key is required";
    }else{
      $req_arr['pass_key'] = $this->post('pass_key',true);

     }
    if(empty($this->post('admin_user_id',true))){
        $flag = false;
        $error_message = "admin user id is required";
    }else{
        $req_arr['admin_user_id'] = $this->post('admin_user_id',true);
    }

    if($flag){

     $check_user = array(
    'pass_key'      => $this->encrypt->decode($req_arr['pass_key']),
    'admin_user_id' => $this->encrypt->decode($req_arr['admin_user_id']),
      );

  $checkloginstatus = $this->employee->checkSessionExist($check_user);
   if(!empty($checkloginstatus) && count($checkloginstatus) > 0)
     {

      $empId = $this->employee->getEmployeeById($req_arr);
      if(!empty($empId)){
          //$this->employee->employeeDelete($req_arr);
          
           //$result_arr['dataset'] = $this->employee->getAllEmployee($req_arr);
           $count   = $this->employee->getAllEmployeeCount($req_arr);
           $result_arr['count']   = $count['count_employee'];
           $result_arr = array();
           $http_response = 'http_response_ok';
           $success_message = 'Employee delete successfully';
           
      }else{
        $http_response = 'http_response_bad_request';
        $error_message = 'There is some problem, please try again';
      }

      
       
     }else{

      $http_response = 'http_response_invalid_login';
      $error_message = 'Invalid login';
     }

    }else{
        $error_message = 'http_response_unauthorized';

    }


 }

json_response($result_arr, $http_response, $error_message, $success_message);
}




/****************************end of admin controlller**********************/

}
