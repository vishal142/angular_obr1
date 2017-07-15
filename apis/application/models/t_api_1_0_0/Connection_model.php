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
class Connection_model extends CI_Model
{

     public $_table = 'tbl_user_connections';
     public $_tbl_master_user_level = 'master_user_levels';
    
     
     
    function __construct()
    {
       
        //load the parent constructor
        parent::__construct();        
         
    }
    
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getCurrentConnection()
     * @ Added Date               : 03-08-2016
     * @ Added By                 : Mousumi Bakshi
     * -----------------------------------------------------------------
     * @ Description              : This function is used for checking email id is exist or not
    */
    public function getCurrentConnection($user_id)
    {
        $where="1 AND connection_status='C' and (fk_user_id='".$user_id."' OR fk_connection_id='".$user_id."')";
        $this->db->where($where);
        
        $this->db->from($this->_table);
        return $this->db->count_all_results();        
    }


    public function getAllCurrentConnection($user_id)
    {
        $sql="select tbl_user_profile_basics.*,tbl_user_connections.fk_user_id as user_id,tbl_user_connections.fk_connection_id 
        from tbl_user_connections JOIN tbl_user_profile_basics on CASE WHEN tbl_user_connections.fk_user_id ='".$user_id."' 
        then tbl_user_profile_basics.fk_user_id=tbl_user_connections.fk_connection_id 
        ELSE tbl_user_profile_basics.fk_user_id=tbl_user_connections.fk_user_id END ";
       
        
             $sqlQuery=$sql." where 
        connection_status='C' and (tbl_user_connections.fk_user_id='".$user_id."' OR fk_connection_id='".$user_id."') ";
       
       
        $res=$this->db->query($sqlQuery);
         return $res->result_array();       
    }


     /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getCurrentConnectionList()
     * @ Added Date               : 03-08-2016
     * @ Added By                 : Mousumi Bakshi
     * -----------------------------------------------------------------
     * @ Description              : This function is used for checking email id is exist or not
    */
    public function getCurrentConnectionList($user_id,$search_text,$pageLimit,$limit)
    {
        $sql="select tbl_user_profile_basics.*,tbl_user_connections.fk_user_id as user_id,tbl_user_connections.fk_connection_id 
        from tbl_user_connections JOIN tbl_user_profile_basics on CASE WHEN tbl_user_connections.fk_user_id ='".$user_id."' 
        then tbl_user_profile_basics.fk_user_id=tbl_user_connections.fk_connection_id 
        ELSE tbl_user_profile_basics.fk_user_id=tbl_user_connections.fk_user_id END ";
       
        if($search_text!=''){
            $sqlQuery=$sql." where 
        connection_status='C' AND (display_name like '".$search_text."%' OR f_name like '".$search_text."%' OR l_name like '".$search_text."%' ) and (tbl_user_connections.fk_user_id='".$user_id."' OR fk_connection_id='".$user_id."') limit ".$pageLimit.",".$limit;
        }else{
             $sqlQuery=$sql." where 
        connection_status='C' and (tbl_user_connections.fk_user_id='".$user_id."' OR fk_connection_id='".$user_id."') limit ".$pageLimit.",".$limit;
       
        }
        $res=$this->db->query($sqlQuery);
         return $res->result_array();       
    }

     /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getCurrentConnectionList()
     * @ Added Date               : 03-08-2016
     * @ Added By                 : Mousumi Bakshi
     * -----------------------------------------------------------------
     * @ Description              : This function is used for checking email id is exist or not
    */
    public function getSentConnectionList($user_id,$search_text,$pageLimit=0,$limit=100)
    {
        $sql="select tbl_user_profile_basics.*,tbl_user_connections.fk_user_id as user_id,tbl_user_connections.fk_connection_id 
        from tbl_user_connections JOIN tbl_user_profile_basics on tbl_user_profile_basics.fk_user_id=tbl_user_connections.fk_connection_id ";
       
        if($search_text!=''){
            $sqlQuery=$sql." where 
        connection_status='P' AND (display_name like '".$search_text."%' OR f_name like '".$search_text."%' OR l_name like '".$search_text."%' ) and (tbl_user_connections.fk_user_id='".$user_id."' ) limit ".$pageLimit.",".$limit;
        }else{
             $sqlQuery=$sql." where 
        connection_status='P' and tbl_user_connections.fk_user_id='".$user_id."'  limit ".$pageLimit.",".$limit;
       
        }
        $res=$this->db->query($sqlQuery);
         return $res->result_array();       
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getCurrentConnectionList()
     * @ Added Date               : 03-08-2016
     * @ Added By                 : Mousumi Bakshi
     * -----------------------------------------------------------------
     * @ Description              : This function is used for checking email id is exist or not
    */
    public function getSearchConnectionList($search_text,$pageLimit=0,$limit=100)
    {
        $sql="select tbl_user_profile_basics.* from tbl_user_profile_basics JOIN tbl_user_types ON tbl_user_profile_basics.fk_user_id=tbl_user_types.fk_user_id AND user_mode='B' ";
       
        if($search_text!=''){
            $sqlQuery=$sql." where 
         display_name like '".$search_text."%' OR f_name like '".$search_text."%' OR l_name like '".$search_text."%'   limit ".$pageLimit.",".$limit;
        }else{
              $sqlQuery=$sql." limit ".$pageLimit.",".$limit;

        }

        $res=$this->db->query($sqlQuery);
         return $res->result_array();       
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getCurrentConnectionList()
     * @ Added Date               : 03-08-2016
     * @ Added By                 : Mousumi Bakshi
     * -----------------------------------------------------------------
     * @ Description              : This function is used for checking email id is exist or not
    */
    public function getSuggestionList($user_id,$pageLimit=0,$limit=100)
    {
        
        $sqlQuery="select tbl_user_profile_basics.* from tbl_user_profile_basics JOIN tbl_user_types ON  tbl_user_profile_basics.fk_user_id=tbl_user_types.fk_user_id AND user_mode='B'  where tbl_user_profile_basics.fk_user_id not in 
(select case when fk_connection_id='".$user_id."' THEN fk_user_id ELSE fk_connection_id END 
from tbl_user_connections where fk_user_id='".$user_id."' OR fk_connection_id='".$user_id."') AND tbl_user_profile_basics.fk_user_id!='".$user_id."'  limit ".$pageLimit.",".$limit;
       
        
        $res=$this->db->query($sqlQuery);
         return $res->result_array();       
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getRecvConnectionList()
     * @ Added Date               : 03-08-2016
     * @ Added By                 : Mousumi Bakshi
     * -----------------------------------------------------------------
     * @ Description              : This function is used for checking email id is exist or not
    */
    public function getRecvConnectionList($user_id,$search_text,$pageLimit,$limit)
    {
        $sql="select tbl_user_profile_basics.*,tbl_user_connections.fk_user_id as user_id,tbl_user_connections.fk_connection_id,connection_status 
        from tbl_user_connections JOIN tbl_user_profile_basics on tbl_user_profile_basics.fk_user_id=tbl_user_connections.fk_user_id ";
       
        if($search_text!=''){
            $sqlQuery=$sql." where 
        connection_status='P' AND (display_name like '".$search_text."%' OR f_name like '".$search_text."%' OR l_name like '".$search_text."%' ) and (tbl_user_connections.fk_connection_id='".$user_id."' ) limit ".$pageLimit.",".$limit;
        }else{
             $sqlQuery=$sql." where 
        connection_status='P' and tbl_user_connections.fk_connection_id='".$user_id."'  limit ".$pageLimit.",".$limit;
       
        }
        $res=$this->db->query($sqlQuery);
        return $res->result_array();       
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getCurrentConnection()
     * @ Added Date               : 03-08-2016
     * @ Added By                 : Mousumi Bakshi
     * -----------------------------------------------------------------
     * @ Description              : This function is used for checking email id is exist or not
    */
    public function getInviteSent($user_id)
    {
        $where="1 AND connection_status='P' and fk_user_id='".$user_id."' ";
        $this->db->where($where);
        
        $this->db->from($this->_table);
        return $this->db->count_all_results();        
    }

        /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getCurrentConnection()
     * @ Added Date               : 03-08-2016
     * @ Added By                 : Mousumi Bakshi
     * -----------------------------------------------------------------
     * @ Description              : This function is used for checking email id is exist or not
    */
    public function getConnectionReceived($user_id)
    {
        $where="1 AND connection_status='P' and fk_connection_id='".$user_id."' ";
        $this->db->where($where);
        
        $this->db->from($this->_table);
        return $this->db->count_all_results();        
    }

    public function add($params){
        $this->db->insert($this->_table,$params);
    }

     public function update($params){
        $dt=date('Y-m-d H:i:s');
        $this->db->set('connection_status','C');
        $this->db->set('connection_timestamp',$dt);
        $this->db->where('fk_user_id',$params['fk_user_id']);
        $this->db->where('fk_connection_id',$params['fk_connection_id']);
        $this->db->update($this->_table);
    }

    public function isAdded($params){
        $this->db->where('fk_user_id',$params['fk_user_id']);
        $this->db->where('fk_connection_id',$params['fk_connection_id']);
        $this->db->from($this->_table);
        return $this->db->count_all_results(); 
    }

    public function isConnected($params){
        $sqlQuery="select * from tbl_user_connections where (fk_user_id='".$params['fk_user_id']."' AND fk_connection_id='".$params['fk_connection_id']."') OR (fk_user_id='".$params['fk_connection_id']."' AND fk_connection_id='".$params['fk_user_id']."')";
        $res=$this->db->query($sqlQuery);
        return $res->result_array();    
    }

    public function disConnectUser($params){
        $dt=date('Y-m-d H:i:s');
        $this->db->set('connection_status','D');
        $this->db->set('disconnect_timestamp',$dt);
        $this->db->where('id',$params['id']);
        $this->db->update($this->_table);

    }

    public function deleteUser($params){
        $this->db->where('fk_user_id',$params['fk_user_id']);
        $this->db->where('fk_connection_id',$params['fk_connection_id']);
        $this->db->delete($this->_table);
        
    }

    public function getUserLevel($mcoins){
        $this->db->where('qualifying_mcoin_points >',$mcoins);
        $this->db->from($this->_tbl_master_user_level);
        return $this->db->get()->result_array();
    }

   public function getUserLevelByRank($rank){
        $this->db->where('level_rank',$rank);
        $this->db->from($this->_tbl_master_user_level);
        return $this->db->get()->row_array();
    }

    public function getConnectionDashboard($user_id)
    {
        if($user_id>0){
            $sql="select tbl_user_profile_basics.*,tbl_user_loan_disbursement.actual_loan_p as actual_loan_p,tbl_user_loan_disbursement.fk_user_loan_id,DATE_FORMAT(tbl_user_loans.loan_disbursed_timestamp,'%b %d,%Y') as loan_disbursed_timestamp from tbl_user_connections JOIN tbl_user_loans on CASE WHEN tbl_user_connections.fk_user_id ='".$user_id."' 
            then tbl_user_loans.fk_user_id=tbl_user_connections.fk_connection_id AND  tbl_user_loans.loan_action_type='A' 
            ELSE tbl_user_loans.fk_user_id=tbl_user_connections.fk_user_id AND tbl_user_loans.loan_action_type='A' END  JOIN tbl_user_profile_basics ON tbl_user_loans.fk_user_id=tbl_user_profile_basics.fk_user_id JOIN tbl_user_loan_disbursement on tbl_user_loans.id=tbl_user_loan_disbursement.fk_user_loan_id limit 0,10";
           
            $res=$this->db->query($sql);
            return $res->result_array();   
        }

    }















    
    //public 
    /**********************************************************************************************************************************
     * End of user model
     *********************************************************************************************************************************/
}