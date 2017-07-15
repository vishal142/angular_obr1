<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
* --------------------------------------------------------------------------
* @ Controller Name          : All the page related api call from page controller
* @ Added Date               : 20-06-2016
* @ Added By                 : Vishal
* -----------------------------------------------------------------
* @ Description              : This is the page index page
* -----------------------------------------------------------------
* @ return                   : array
* -----------------------------------------------------------------
* @ Modified Date            : 20-06-2016
* @ Modified By              : Vishal
* 
*/

//All the required library file for API has been included here 
/*require APPPATH . 'libraries/api/AppExtrasAPI.php';
require APPPATH . 'libraries/api/AppAndroidGCMPushAPI.php';
require APPPATH . 'libraries/api/AppApplePushAPI.php';*/

require_once('src/OAuth2/Autoloader.php');
require APPPATH . 'libraries/api/REST_Controller.php';


class Page extends REST_Controller{
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
        $this->load->model('api_' . $this->config->item('test_api_ver') . '/admin/page_model', 'page');

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


    function getAboutdetails_post(){

    $error_message = $success_message = $http_response ='';
     $result_arr = array();
     $aboutdetails = array();
    if (!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) 
        {
            $error_message = 'Invalid Token';
            $http_response = 'http_response_unauthorized';
        }else{

        	$req_arr = $details_arr = array();
        	$flag = true;
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

    $req_arr['menu_page_name'] = $this->post('menu_page_name',true);

   $plaintext_pass_key = $this->encrypt->decode($this->input->post('pass_key', TRUE));
            $plaintext_admin_id = $this->encrypt->decode($this->input->post('admin_user_id', TRUE));

   $aboutdetails = $this->page->getPageDtail($req_arr['menu_page_name']);
    $about_img = array();

       if(!empty($aboutdetails) && count($aboutdetails) > 0 ){
       	//print_r($aboutdetails['menu_page_name']);
       	 if($aboutdetails['menu_page_name']!=''){
         $aboutdetails['about_image_url'] = base_url().'assets/resources/page/'.$aboutdetails['menu_page_name'];   

        }else{
            $aboutdetails['about_image_url'] = '';

        }
 
    $result_arr         = $aboutdetails;
    $http_response      = 'http_response_ok';
    $success_message    = 'Get About Details';
     
     //pre($result_arr,1);

       }else{
       	$http_response  = 'http_response_invalid_login';
        $error_message  = 'User is invalid';
       }

      }
      
     json_response($result_arr, $http_response, $error_message, $success_message);
    } 


public function doAboutupdate_post(){
	$error_message = $success_message = $http_response ='';
 $result_arr = array();
 $details_arr = array();

 if(!$this->oauth_server->verifyResourceRequest(OAuth2\Request::createFromGlobals()))
  {
    $error_message = 'Invalid Token';
    $http_response = 'http_response_unauthorized';
 }else{

 	$flag = true;


 if(empty($this->post('helpDescription',true))){
   $flag = false;
   $error_message = 'Page body is required';
 } else{
    $result_arr['page_body'] = $this->post('helpDescription',true);
 }

 if(empty($this->post('page_title',true))){
 	$flag = false;
 	$error_message = 'Page title is required';

 }else{
 	$result_arr['page_title'] = $this->post('page_title',true);

 }
  

 if($flag){
 if(isset($_FILES['file']['name']) && $_FILES['file']['size'] > 0){
  $array1 = explode('.', $_FILES['file']['name']);
 $extension1 = end($array1);

 //pre($_FILES['file']['name'],1);
 $file_ex = array("png","jpg", "jpeg");
 if(in_array($extension1, $file_ex)){

 	echo "Here"; exit();

$table = $this->tables['cms_master'];
$where = array('menu_page_name'=>'about');

$old_about_extentation = $this->common->select_one_row($table,$where,'banner_image_extension');
$file_name1  = "about".'.'.$old_about_extentation['banner_image_extension'];

//$img_thumb1 = $this->config->item('upload_file_url').'page/thumb/'.$file_name1;
    $img1 = $this->config->item('upload_file_url').'page/'.$file_name1;

    //echo $old_about_extentation; exit();
    // if(file_exists($img1)){
    //     unlink($img1);
    //   }
$this->load->library('upload');

//print_r($_FILES['file']);  exit();

 $image_info  = getimagesize($_FILES['file']['tmp_name']);
                 $image_width     = $image_info[0];
                $image_height    = $image_info[1];
                $original_width  = $image_width;
                $original_height = $image_height;
                $new_width       = 300;
                $new_height      = 100;
                $thumb_width             = $new_width;
                $thumb_height            = $new_height;
                $array                   = explode('.', $_FILES['file']['name']);
                $extension               = end($array);
                $file_name               = 'about' . "." . $extension;
                $config['upload_path']   = $this->config->item('upload_file_url').'page/';
                $config['allowed_types'] = 'png|jpg|jpeg';
                $config['overwrite'] = true;
                $config['file_name'] = $file_name;
                $this->upload->initialize($config);
                

                if($this->upload->do_upload('file')) {
                //$img_thumb = $this->config->item('upload_file_url').'profile/thumb/'.$file_name;
                $img = $this->config->item('upload_file_url').'page/'.$file_name;
                    
 					$upload_data_details = $this->upload->data();
                    $image    = $file_name;
                    $this->load->library('image_lib');
                    $config['source_image']   = $img;
                    $config['new_image']      = $img;
                    $config['height']         = 400;
                    $config['width']          = 620;
                    $config['maintain_ratio'] = false;
                    $this->image_lib->initialize($config);

           $table = $this->tables['cms_master'];
			$where = array('menu_page_name'=>'about');      

			 $up_data = array(

			                'page_title'=> $result_arr['page_title'],
			                'page_body'=>$result_arr['page_body'],
			                'banner_image_extension' => $extension,
			                'has_banner_image' =>'1'
			                );
    //print_r($up_data); exit();
   $this->page->updatepage($table,$where,$up_data);

                $success_message = 'Page updated';
                $http_response = 'http_response_ok';


                 }
          }
       else{
            $http_response = 'http_response_unauthorized';
             $error_message = 'File Type is not supported! Only GIF,JPG,PNG file can upoload';
             }
      }else{

        $table = $this->tables['cms_master'];
		$where = array('menu_page_name'=>'about');      
        $up_data = array(

		    'page_title'=> $result_arr['page_title'],
		    'page_body'=>$result_arr['page_body'],
		    
		    );
    //print_r($up_data); exit();
   $this->page->updatepage($table,$where,$up_data);

                $success_message = 'Page updated';
                $http_response = 'http_response_ok';

     }

   }else{
  	$http_response = 'http_response_unauthorized';

  }

 }

 json_response($result_arr, $http_response, $error_message, $success_message);
  
 }





/****************************end of page controlller**********************/

}