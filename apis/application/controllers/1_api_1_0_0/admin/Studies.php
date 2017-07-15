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


class Studies extends REST_Controller
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
        $this->load->model('api_'.$this->config->item('test_api_ver').'/admin/Studies_model','study');
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
     * @ Function Name            : getAllStudies()
     * @ Added Date               : 28-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : get all studies
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 28-09-2016
     * @ Modified By              : Piyalee
     * 
    */
    public function getAllStudies_post()
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
            //pre($this->input->post(),1);
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

            $req_arr['order']           = $this->input->post('order', true);
            $req_arr['order_by']        = $this->input->post('order_by', true);
            $req_arr['searchByStudy']   = $this->input->post('searchByStudy', true);
            if($flag)
            {
                $check_user = array(
                    'pass_key'      => $this->encrypt->decode($req_arr['pass_key']),
                    'admin_user_id' => $this->encrypt->decode($req_arr['admin_user_id']),
                );
                $checkloginstatus = $this->admin->checkSessionExist($check_user);
                if(!empty($checkloginstatus) && count($checkloginstatus) > 0)
                {
                    $details_arr['dataset']     = $this->study->getAllStudies($req_arr);
                    $count                      = $this->study->getAllStudiesCount($req_arr);
                    $details_arr['count']       = $count['count_study'];
                    //pre($details_arr,1);
                    if(!empty($details_arr) && count($details_arr) > 0)
                    {
                        $result_arr         = $details_arr;
                        $http_response      = 'http_response_ok';
                        $success_message    = 'All Studies';  
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
     * @ Function Name            : addStudies()
     * @ Added Date               : 08-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : add degree details
     *  -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 08-09-2016
     * @ Modified By              : Piyalee
     * 
    */
    public function addStudies_post()
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
            $flag = true;
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

            if($flag && empty($this->post('field_of_study')))
            {
                $flag = false;
                $error_message = "Field of Study is required";
            }
            else
            {
                $req_arr['field_of_study'] = $this->post('field_of_study');
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
                    $checkStudy = $this->study->checkDuplicateStudyField($req_arr);
                    //echo $this->db->last_query();
                    if(empty($checkDegreeType))
                    {
                        $insTypeArr = array(
                            'field_of_study' => $req_arr['field_of_study']
                        );
                        $studyId = $this->study->addStudyField($insTypeArr);
                        if(!empty($studyId))
                        {
                            $http_response      = 'http_response_ok';
                            $success_message    = 'Added Field of study successfully';  
                        }
                        else
                        {
                            $http_response  = 'http_response_bad_request';
                            $error_message  = 'There is some problem, please try again';
                        }
                    }
                    else
                    {
                        $http_response = 'http_response_bad_request';
                        $error_message = 'Field of study already exists, please try another name';
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
     * @ Function Name            : getStudyDetails()
     * @ Added Date               : 01-10-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : get study details
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 01-10-2016
     * @ Modified By              : Piyalee
     * 
    */
    public function getStudyDetails_post()
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
            $flag = true;
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
            
            if($flag && empty($this->post('studyId')))
            {
                $flag = false;
                $error_message = "Study Id is required";
            }
            else
            {
                $req_arr['studyId'] = $this->post('studyId');
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
                    $studyDetails          = $this->study->getStudyDetailById($req_arr);
                    $result_arr['dataset']  = $studyDetails;
                    $http_response          = 'http_response_ok';
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
     * @ Function Name            : updateStudiesDetail()
     * @ Added Date               : 01-10-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : update study details
     * -----------------------------------------------------------------
     * @ return                   : array
     * -----------------------------------------------------------------
     * @ Modified Date            : 01-10-2016
     * @ Modified By              : Piyalee
     * 
    */
    public function updateStudiesDetail_post()
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
            $flag = true;
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

            if($flag && empty($this->post('id')))
            {
                $flag = false;
                $error_message = "Study Id is required";
            }
            else
            {
                $req_arr['study_id'] = $this->post('id');
            }

            if($flag && empty($this->post('field_of_study')))
            {
                $flag = false;
                $error_message = "Field of Study is required";
            }
            else
            {
                $req_arr['field_of_study'] = $this->post('field_of_study');
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
                    $checkStudy = $this->study->checkDuplicateStudyField($req_arr);
                    //echo $this->db->last_query();
                    if(empty($checkStudy))
                    {
                        $studyId = $this->study->updateStudy(array('id' => $req_arr['study_id']), array('field_of_study' => $req_arr['field_of_study']));
                        
                        $http_response      = 'http_response_ok';
                        $success_message    = 'Updated study details successfully';
                    }
                    else
                    {
                        $http_response = 'http_response_bad_request';
                        $error_message = 'Study already exists, please try another type';
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

    /****************************end of degree controlller**********************/

}
