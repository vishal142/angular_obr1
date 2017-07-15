<?php
/* * ******************************************************************
 * User model for Mobile Api 
  ---------------------------------------------------------------------
 * @ Added by                 : Mousumi Bakshi 
 * @ Framework                : CodeIgniter
 * @ Added Date               : 02-08-2016
  ---------------------------------------------------------------------
 * @ Details                  : It Cotains all the api related methods
  ---------------------------------------------------------------------
 ***********************************************************************/
class Login_model extends CI_Model
{

    public $_table = 'tbl_users';
    public $_table_device = 'tbl_user_mobile_devices';
    public $_table_loginkey = 'tbl_user_loginkeys';

     
    function __construct()
    {
       
        //load the parent constructor
        parent::__construct();        
         
    }




    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : login_status_checking()
     * @ Added Date               : 28-07-2016
     * @ Added By                 : Subhankar Pramanik
     * -----------------------------------------------------------------
     * @ Description              : login status checking
     * -----------------------------------------------------------------
     * @ param                    : array(param) 
     * @ return                   : 
     * -----------------------------------------------------------------
     * 
    */
    public function login_status_checking($param = array()){

        $this->db->where('id', $param['pass_key']);
        $this->db->where('fk_user_id', $param['user_id']);
        $this->db->from($this->_table_loginkey);
        //echo $this->db->last_query(); exit;
        //return $this->db->count_all_results(); 
        return $this->db->get()->row_array();
        //return $result;
    }
    //public 
    /**********************************************************************************************************************************
     * End of user model
     *********************************************************************************************************************************/
}