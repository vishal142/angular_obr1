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
class Admin_model extends CI_Model{
    public $_tbl_admins = 'tbl_admins';

    function __construct(){
        //load the parent constructor
        parent::__construct();        
        $this->tables = $this->config->item('tables'); 
    }

    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : check_user()
    * @ Added Date               : 14-04-2016
    * @ Added By                 : Subhankar
    * -----------------------------------------------------------------
    * @ Description              : This function is used for checking the user whether the user exist or not
    * -----------------------------------------------------------------
    * @ param                    : Array(params)    
    * @ return                   : If user exist return user_id else false  
    * -----------------------------------------------------------------
    * 
    */
    public function checkUser($params = array()) {
        //echo "<pre>"; print_r($params); exit;
        //cheking the user in database
        $this->db->select($this->tables['tbl_admins'].'.*');
        $this->db->where($this->tables['tbl_admins'].'.login_email', $params['username']);
        $this->db->where($this->tables['tbl_admins'].'.login_pwd', md5($params['password']));
        $this->db->limit(1);
        $query = $this->db->get($this->tables['tbl_admins']);
        //die($this->db->last_query());
        if ($query->num_rows() === 1) {
            $user = $query->row();

            $this->updateLastLogin($user->id);
            $result = array();
            if ($user->is_active == 0) {
                $result['status'] = 'inactive';
                $result['user_id'] = $user->id;
                return $result;
            }else{                  
                $result['status'] = 'active';
                $result['user_id'] = $user->id;
                return $result;
            }

        } else {
            return FALSE;
        }
    }

    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : update_last_login()
    * @ Added Date               : 14-06-2016
    * @ Added By                 : Subhankar
    * -----------------------------------------------------------------
    * @ Description              : last login by user has been updated using 
    * this function
    * -----------------------------------------------------------------
    * @ param                    : int(id) 
    * @ return                   : int(affected rows)  
    * -----------------------------------------------------------------
    * 
    */
    public function updateLastLogin($id) {
        $current_time  = gmdate("Y-m-d H:i:s");
        $this->db->update(
            $this->tables['tbl_admins'], 
            array(
                'last_login_timestamp' => $current_time,
                'last_activity_timestamp' => $current_time
            ), array(
                'id' => $id
            ));
        return $this->db->affected_rows() == 1;
    }


    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : check_emailid()
    * @ Added Date               : 14-06-2016
    * @ Added By                 : Subhankar
    * -----------------------------------------------------------------
    * @ Description              : Check for exist email id for admin.
    * this function
    * -----------------------------------------------------------------
    * @ param                    : email, and user id if login 
    * @ return                   : array
    * -----------------------------------------------------------------
    * 
    */

    function checkEmailid($email='',$user_id='')
    {
        $this->db->select('*');
        $this->db->from($this->tables['tbl_admins']);
        $this->db->where($this->tables['tbl_admins'].'.login_email',$email);
        if($user_id)
        {
            $this->db->where_not_in($this->tables['tbl_admins'].'.id',$user_id);
        }
        $this->db->where($this->tables['tbl_admins'].'.is_active','1');
        return $this->db->get()->result_array();
    }


    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : check_exist_passcode()
    * @ Added Date               : 14-06-2016
    * @ Added By                 : Subhankar
    * -----------------------------------------------------------------
    * @ Description              : Check for exist passcode for admin
    * this function
    * -----------------------------------------------------------------
    * @ param                    : user_id 
    * @ return                   : array
    * -----------------------------------------------------------------
    * 
    */
    
    function checkExistPasscode($user_id){
        $this->db->select("*");
        $this->db->where("fk_admin_id",$user_id);
        $qry = $this->db->get($this->tables['tbl_admin_pwd_reset_codes']);
        return $qry->row_array();
    }


    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getAdminPswdResetCode()
     * @ Added Date               : 28-07-2016
     * @ Added By                 : Subhankar Pramanik
     * -----------------------------------------------------------------
     * @ Description              : Pswd Reset Code status checking
     * -----------------------------------------------------------------
     * @ param                    : array(param) 
     * @ return                   : 
     * -----------------------------------------------------------------
     * 
    */
    public function getAdminPswdResetCode($param = array()){

        $this->db->where('passcode', $param['passcode']);
        $this->db->where('fk_admin_id', $param['fk_admin_id']);
        $this->db->from($this->tables['tbl_admin_pwd_reset_codes']);
        return $this->db->get()->row_array();
    }


    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : change_password
     * @ Added Date               : 29-07-2016
     * @ Added By                 : Subhankar Pramanik
     * -----------------------------------------------------------------
     * @ Description              : change_password
     * -----------------------------------------------------------------
     * @ param                    : array(param)
     * @ return                   : int()
     * -----------------------------------------------------------------
     * 
    */
    public function changePassword($param = array()){
        $update = array();
        $update['login_pwd'] = $param['password'];
        $this->db->where($this->tables['tbl_admins'].".id", $param['admin_id']);
        $this->db->update($this->tables['tbl_admins'], $update);
        //return $this->db->affected_rows(); 
        return 1;
    }


    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : remove_passcode
     * @ Added Date               : 06-05-2016
     * @ Added By                 : Subhankar Pramanik
     * -----------------------------------------------------------------
     * @ Description              : remove_passcode
     * -----------------------------------------------------------------
     * @ param                    : array(param)
     * @ return                   : int()
     * -----------------------------------------------------------------
     * 
     */
    public function removePasscode($param = array()){   
        $affected_rows_count = 0;
        $this->db->where('passcode',$param['passcode']);        
        $this->db->where('fk_admin_id',$param['fk_admin_id']);        
        $this->db->delete($this->tables['tbl_admin_pwd_reset_codes']);
        return $affected_rows_count = $this->db->affected_rows();
    }


    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : addAdminloginSession
     * @ Added Date               : 06-05-2016
     * @ Added By                 : Subhankar Pramanik
     * -----------------------------------------------------------------
     * @ Description              : add admin login sesssion
     * -----------------------------------------------------------------
     * @ param                    : array(param)
     * @ return                   : int()
     * -----------------------------------------------------------------
     * 
     */    
    public function addAdminLoginSession($params){
        $this->db->insert($this->tables['tbl_admin_loginsessions'], $params);
        return $this->db->insert_id();
    }



    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : checkSessionExist()
    * @ Added Date               : 14-06-201checkSessionExist6
    * @ Added By                 : Subhankar
    * -----------------------------------------------------------------
    * @ Description              : Check for exist passcode for admin
    * this function
    * -----------------------------------------------------------------
    * @ param                    : user_id 
    * @ return                   : array
    * -----------------------------------------------------------------
    * 
    */
    
    function checkSessionExist($param){
        //$this->db->select($this->tables['tbl_admins'].".*,".$this->tables['tbl_admin_loginsessions'].'.id AS pass_key');
        $this->db->select($this->tables['tbl_admins'].".admin_level, " . $this->tables['tbl_admins'].".f_name, " . $this->tables['tbl_admins'].".l_name, "  . $this->tables['tbl_admin_loginsessions'].'.id AS pass_key');

        $this->db->where($this->tables['tbl_admin_loginsessions'].".id",$param['pass_key']);
        $this->db->where($this->tables['tbl_admin_loginsessions'].".fk_admin_id",$param['admin_user_id']);
        $this->db->join($this->tables['tbl_admins'], $this->tables['tbl_admins'].'.id = '.$this->tables['tbl_admin_loginsessions'].'.fk_admin_id', 'inner');
        $qry = $this->db->get($this->tables['tbl_admin_loginsessions']);
        return $qry->row_array();
    }



    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : logout_admin()
     * @ Added Date               : 28-07-2016
     * @ Added By                 : Subhankar Pramanik
     * -----------------------------------------------------------------
     * @ Description              : Admin logged out 
     * -----------------------------------------------------------------
     * @ param                    : array(params)
     * @ return                   : int(affected_rows)
     * -----------------------------------------------------------------
     * 
    */
    public function logoutAdmin($param = array()) {
        $loginsessions_count = 0;
        $this->db->where('id', $param['pass_key']);
        $this->db->where('fk_admin_id', $param['admin_user_id']);
        $this->db->delete($this->tables['tbl_admin_loginsessions']);
        $loginsessions_count = $this->db->affected_rows();
        
        return $loginsessions_count;
    }


    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : getAdminDetails()
    * @ Added Date               : 14-04-2016
    * @ Added By                 : Subhankar
    * -----------------------------------------------------------------
    * @ Description              : This function is used for checking the user whether the user exist or not
    * -----------------------------------------------------------------
    * @ param                    : Array(params)    
    * @ return                   : If user exist return user_id else false  
    * -----------------------------------------------------------------
    * 
    */
    public function getAdminDetails($params = array()) {
        $data = array();
        $this->db->select($this->tables['tbl_admins'].'.*');
        $this->db->where($this->tables['tbl_admins'].'.id', $params['admin_user_id']);
        $this->db->limit(1);
        $query = $this->db->get($this->tables['tbl_admins']);
        $data = $query->row_array();
        $data['full_name'] = $data['f_name'].' '.$data['l_name'];

        return $data;
    }


    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : change_password
     * @ Added Date               : 29-07-2016
     * @ Added By                 : Subhankar Pramanik
     * -----------------------------------------------------------------
     * @ Description              : change_password
     * -----------------------------------------------------------------
     * @ param                    : array(param)
     * @ return                   : int()
     * -----------------------------------------------------------------
     * 
    */
    public function editProfile($param = array(), $where = array()){  
        $this->db->where($where);
        $this->db->update($this->tables['tbl_admins'], $param);
        $affected_rows = $this->db->affected_rows(); 
        return 1;
    }


    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : check_user()
    * @ Added Date               : 14-04-2016
    * @ Added By                 : Subhankar
    * -----------------------------------------------------------------
    * @ Description              : This function is used for checking the user whether the user exist or not
    * -----------------------------------------------------------------
    * @ param                    : Array(params)    
    * @ return                   : If user exist return user_id else false  
    * -----------------------------------------------------------------
    * 
    */
    public function checkAdminUser($where = array()) {
        $this->db->where($where);
        $query = $this->db->get($this->tables['tbl_admins']);
        $data = $query->row_array();
        return $data;
    }





    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : getAllProfession()
    * @ Added Date               : 20-05-20getAllProfession16
    * @ Added By                 : Subhankar    
    * -----------------------------------------------------------------
    * @ Description              : get all Profession
    * -----------------------------------------------------------------
    * @ param                    : Array(params)    
    * @ return                   : Array
    * -----------------------------------------------------------------
    * 
    */
    public function getAllProfession($param = array()){
        $this->db->select('id, profession_type'); 
        $this->db->where($this->tables['master_profession_types'].'.is_active', 1);
        $this->db->order_by($this->tables['master_profession_types'].'.id', 'desc');

        $result = $this->db->get($this->tables['master_profession_types'])->result_array();
        //echo $this->db->last_query();
        return $result;
    }


    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : getAllPaymentType()
    * @ Added Date               : 20-05-20getAllProfession16
    * @ Added By                 : Subhankar    
    * -----------------------------------------------------------------
    * @ Description              : get all Payment Type
    * -----------------------------------------------------------------
    * @ param                    : Array(params)    
    * @ return                   : Array
    * -----------------------------------------------------------------
    * 
    */
    public function getAllPaymentType($param = array()){
        $this->db->select('id, payment_type'); 
        $this->db->order_by($this->tables['master_payment_types'].'.id', 'desc');
        $result = $this->db->get($this->tables['master_payment_types'])->result_array();
        //echo $this->db->last_query();
        return $result;
    }



    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : getAllAdminUsers()
    * @ Added Date               : 20-05-2016
    * @ Added By                 : Subhankar    
    * -----------------------------------------------------------------
    * @ Description              : get all Agent code
    * -----------------------------------------------------------------
    * @ param                    : Array(params)    
    * @ return                   : Array
    * -----------------------------------------------------------------
    * 
    */
    public function getAllAdminUsers($param = array()){

        $this->db->select('id,f_name,l_name,login_email,is_active,admin_level');          

        if(!empty($param['searchByNameEmail'])){
            $where = "(".$this->_tbl_admins.".`login_email` LIKE '%".$param['searchByNameEmail']."%' OR  ".$this->_tbl_admins.".`f_name` LIKE '%".$param['searchByNameEmail']."%' OR ".$this->_tbl_admins.".`l_name` LIKE '%".$param['searchByNameEmail']."%' OR CONCAT_WS(' ', ".$this->_tbl_admins.".f_name,".$this->_tbl_admins.".l_name) LIKE '%".$param['searchByNameEmail']."%' )";
            $this->db->where($where);
        }

        if(!empty($param['order_by']) && !empty($param['order'])){           
            $this->db->order_by($this->_tbl_admins.'.'.$param['order_by'], $param['order']);
        } 

        if(!empty($param['page']) && !empty($param['page_size'])){
            $limit = $param['page_size'];
            $offset = $limit*($param['page']-1);
            $this->db->limit($limit, $offset);
        }

        $result = $this->db->get($this->_tbl_admins)->result_array();
        //die($this->db->last_query());
        return $result;
    }


    /*
    * --------------------------------------------------------------------------
    * @ Function Name            : getAllAdminUsersCount()
    * @ Added Date               : 20-05-2016
    * @ Added By                 : Subhankar    
    * -----------------------------------------------------------------
    * @ Description              : get all Agent code count
    * -----------------------------------------------------------------
    * @ param                    : Array(params)    
    * @ return                   : Array
    * -----------------------------------------------------------------
    * 
    */
    public function getAllAdminUsersCount($param = array()){
        $this->db->select('id'); 

        if(!empty($param['searchByNameEmail'])){
            $where = "(".$this->_tbl_admins.".`login_email` LIKE '%".$param['searchByNameEmail']."%' OR  ".$this->_tbl_admins.".`f_name` LIKE '%".$param['searchByNameEmail']."%' OR ".$this->_tbl_admins.".`l_name` LIKE '%".$param['searchByNameEmail']."%' OR CONCAT_WS(' ', ".$this->_tbl_admins.".f_name,".$this->_tbl_admins.".l_name) LIKE '%".$param['searchByNameEmail']."%' )";
            $this->db->where($where);
        }

        $result = $this->db->count_all_results($this->_tbl_admins);
        //echo $this->db->last_query();
        return $result;
    }










    public function addAdminUser($params){

        // pre($params,1);
        $this->db->insert($this->tables['tbl_admins'], $params);
        return $this->db->insert_id();
    }




    /*****************************************
     * End of user model
    ****************************************/
}