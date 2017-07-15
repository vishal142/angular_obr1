<?php
/* * ******************************************************************
 * User model for Mobile Api 
  ---------------------------------------------------------------------
 * @ Added by                 : Mousumi Bakshi 
 * @ Framework                : CodeIgniter
 * @ Added Date               : 12-09-2016
  ---------------------------------------------------------------------
 * @ Details                  : It Cotains all the api related product
  ---------------------------------------------------------------------
 ***********************************************************************/
class Agent_model extends CI_Model
{

    public $_table_user = 'tbl_users';
    public $_table_user_profile_basic = 'tbl_user_profile_basics';
    public $_table = 'tbl_agent_reward_earnings';
    public $_table_user_referal = 'tbl_user_referals';
    public $_table_invite_user = 'tbl_invite_users';
    public $_table_reward_earning = 'master_reward_earnings';
    public $_table_loan_reward_earning = 'tbl_user_loan_reward_earnings';
   
     

     
    function __construct()
    {
       
        //load the parent constructor
        parent::__construct();        
         
    }
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getProduct()
     * @ Added Date               : 01-09-2016
     * @ Added By                 : Mousumi Bakshi
     * -----------------------------------------------------------------
     * @ Description              : This function is used for total of non referred conncetion points
    */
     public function fetchUserRewards($params)
    {
        $this->db->select($this->_table.'.*,'.$this->_table_user_profile_basic.'.profile_picture_file_extension,s3_media_version,display_name,residence_city,residence_state');
         $this->db->where($this->_table.'.fk_user_id',$params['user_id']);
        $this->db->join($this->_table_user_profile_basic,$this->_table.'.fk_activity_user_id='.$this->_table_user_profile_basic.'.fk_user_id');
        
        $this->db->group_by($this->_table.'.fk_activity_user_id');
        $this->db->from($this->_table);
        return $this->db->get()->result_array();
    }

     
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getTotalOwnActivity()
     * @ Added Date               : 01-09-2016
     * @ Added By                 : Mousumi Bakshi
     * -----------------------------------------------------------------
     * @ Description              : This function is used for total of  own activity points
    */
    public function getTotalReward($user_id,$activity_user_id=0,$activity_id=0)
    {
        $tot_reward_point=0;
        $this->db->select('SUM(reward_point) as tot_reward_point');
        $this->db->where('fk_user_id',$user_id);
        if($activity_user_id>0){
        $this->db->where('fk_activity_user_id',$activity_user_id);
        }
        if($activity_id>0){
        $this->db->where('fk_reward_activity_id',$activity_id);
        }
        $row=$this->db->get($this->_table)->row_array();
        $tot_reward_point=$row['tot_reward_point'] ;
        return $tot_reward_point;
    }

    public function getUnregisteredUsers($user_id){
        $this->db->where('fk_user_id',$user_id);
        $this->db->from($this->_table_invite_user);
        $row=$this->db->get()->result_array();
        return $row;

    }

    public function getInCompleteProfile($user_id){
        if($user_id>0){
            $sql="SELECT  `tbl_users`.email_id, `tbl_users`.mobile_number,`tbl_users`.display_name,`tbl_users`.id  FROM `tbl_user_referals` JOIN  `tbl_users` ON  `tbl_user_referals`.fk_user_id=`tbl_users`.id   WHERE `fk_refered_by_user_id`='".$user_id."' and `tbl_user_referals`.`fk_user_id` NOT IN (select fk_user_id from `tbl_user_approvals`)";
             $res=$this->db->query($sql);
             return $res->result_array();   
         } 

    }

    public function getRewardEarning($params){
        $this->db->where('fk_product_id',$params['fk_product_id']);
        if($params['fk_reward_activity_id']>0){
            $this->db->where('fk_reward_activity_id',$params['fk_reward_activity_id']);
        }
        $this->db->from($this->_table_reward_earning);
        $row=$this->db->get()->result_array();
        return $row;


    }

    public function getLoanRewardEarning($params){
        $this->db->where('fk_user_loan_id',$params['fk_product_id']);
        $this->db->where('fk_reward_activity_id',$params['fk_reward_activity_id']);
        
        $this->db->from($this->_table_loan_reward_earning);
        $row=$this->db->get()->row_array();
        return $row;


    }

    public function addAgentReward($params){
        $this->db->insert($this->_table,$params);
    }

    public function addLoanAgentReward($params){
        $this->db->insert($this->_table_loan_reward_earning,$params);
    }



    

}