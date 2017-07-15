<?php
/* * ******************************************************************
 * User model for Mobile Api 
  ---------------------------------------------------------------------
 * @ Added by                 : Mousumi Bakshi 
 * @ Framework                : CodeIgniter
 * @ Added Date               : 02-03-2016
  ---------------------------------------------------------------------
 * @ Details                  : It Cotains all the api related methods
  ---------------------------------------------------------------------
 ***********************************************************************/
class Settings_model extends CI_Model
{

     public $_table = 'tbl_user_support_tickets';
     public $_table_threads = 'tbl_user_support_ticket_threads';
     
     
    function __construct()
    {
       
        //load the parent constructor
        parent::__construct();        
         
    }

    
   /* public function addUserVerificationCode($params){

        //pre($params,1);
        $this->db->where('fk_user_id',$params['fk_user_id']);
        $this->db->where('verification_type',$params['verification_type']);
        $this->db->delete($this->_table_verification_code);
        $this->db->insert($this->_table_verification_code,$params);
    }*/

}