<?php
/* * ******************************************************************
* User model for Mobile Api 
---------------------------------------------------------------------
* @ Added by                 : Subhankar 
* @ Framework                : CodeIgniter
* @ Added Date               : 02-03-2016
---------------------------------------------------------------------
* @ Details                  : It Cotains all the api related methods
---------------------------------------------------------------------
***********************************************************************/
class User_referals_model extends CI_Model{

    public $_tbl_invite_users  = 'tbl_invite_users';
    public $_tbl_user_types    = 'tbl_user_types';
    public $_tbl_admin_data_collections = 'tbl_admin_data_collections';



    function __construct(){

        // 
        //load the parent constructor
        parent::__construct();        
       // $this->tables = $this->config->item('tables'); 
    }

    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : validateUser_email()
    * @ Added Date               : 19-10-16
    * @ Added By                 : Amit Pandit
    * -----------------------------------------------------------------
    * @ Description              : Check for exist email id for user.
    * -----------------------------------------------------------------
    * @ param                    : array(param)
    * @ return                   : int()
    * -----------------------------------------------------------------
    * 
    */  

     public function validateUser_email($where = array()){  
        if(!empty($where)){
            $this->db->where($where);
        }
        $result = $this->db->count_all_results($this->_tbl_invite_users);
     //  $result = $this->db->get($this->_tbl_user_types);
        return $result;
    }

     /*
    * --------------------------------------------------------------------------
    * @ Function Name            : validateReferal_code()
    * @ Added Date               : 19-10-16
    * @ Added By                 : Amit pandit
    * -----------------------------------------------------------------
    * @ Description              : Check for Valid User referal code for user.
    * -----------------------------------------------------------------
    * @ param                    : array(param)
    * @ return                   : int()
    * -----------------------------------------------------------------
    * 
    */  


  public function validateReferal_code($where = array()){  
        if(!empty($where)){
            $this->db->where($where);
        }
        $result = $this->db->count_all_results($this->_tbl_user_types);
     //  $result = $this->db->get($this->_tbl_user_types);
        return $result;
    }

  /*
    * --------------------------------------------------------------------------
    * @ Function Name            : getUser_id()
    * @ Added Date               : 19-10-16
    * @ Added By                 : Amit pandit
    * -----------------------------------------------------------------
    * @ Description              : Get user id for valid referal.
    * -----------------------------------------------------------------
    * @ param                    : array(param)
    * @ return                   : int()
    * -----------------------------------------------------------------
    * 
    */  

public function getUser_id($where = array())
{

        $this->db->select('fk_user_id'); 

    if(!empty($where)){
    $this->db->where($where);
    }
    $result = $this->db->get($this->_tbl_user_types)->row_array();
    return $result;
            }

/*
    * --------------------------------------------------------------------------
    * @ Function Name            : addInvitedUsers()
    * @ Added Date               : 19-10-16
    * @ Added By                 : Amit pandit
    * -----------------------------------------------------------------
    * @ Description              : Add user data for successful referal.
    * -----------------------------------------------------------------
    * @ param                    : array(param)
    * @ return                   : int()
    * -----------------------------------------------------------------
    * 
    */  



        public function addInvitedUsers($params){
       
        $this->db->insert($this->_tbl_invite_users, $params);
        return $this->db->insert_id();
    }



public function checkDuplicateEmail($where = array())
    {
        $this->db->where('tadc.user_email', $where['user_email']);
        $result = $this->db->count_all_results($this->_tbl_admin_data_collections . ' AS tadc');
        return $result;
    }
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : addUsers()
     * @ Added Date               : 27-10-2016
     * @ Added By                 : Amit pandit
     * -----------------------------------------------------------------
     * @ Description              : Add user to admin user collections
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
     */
    public function addUsers($param = array())
    {
        $this->db->insert($this->_tbl_admin_data_collections, $param);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

}






