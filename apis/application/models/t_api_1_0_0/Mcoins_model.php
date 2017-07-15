<?php
/* * ******************************************************************
 * User model for Mobile Api 
  ---------------------------------------------------------------------
 * @ Added by                 : Mousumi Bakshi 
 * @ Framework                : CodeIgniter
 * @ Added Date               : 01-09-2016
  ---------------------------------------------------------------------
 * @ Details                  : It Cotains all the api related mcoins
  ---------------------------------------------------------------------
 ***********************************************************************/
class Mcoins_model extends CI_Model
{

     public $_table_user = 'tbl_users';
     public $_table_user_profile_basic = 'tbl_user_profile_basics';
     public $_tbl_mcoin_earning = 'master_mcoin_earnings';
     public $_table = 'tbl_user_mcoins_earnings';
     

     
    function __construct()
    {
       
        //load the parent constructor
        parent::__construct();        
         
    }
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getTotalNonReferedMcoins()
     * @ Added Date               : 01-09-2016
     * @ Added By                 : Mousumi Bakshi
     * -----------------------------------------------------------------
     * @ Description              : This function is used for total of non referred conncetion points
    */
    public function getTotalNonReferedMcoins($user_id,$activity_user_id=0,$activity_id=0)
    {
        $tot_non_referred=0;
        $this->db->select('SUM(non_referred_connections) as tot_non_referred');
        if($activity_user_id>0){
        $this->db->where('fk_activity_user_id',$activity_user_id);
        }
        if($activity_id>0){
        $this->db->where('fk_mcoin_activity_id',$activity_id);
        }
        $this->db->where('fk_user_id',$user_id);
        $row=$this->db->get($this->_table)->row_array();
        $tot_non_referred=$row['tot_non_referred'] ;
        return $tot_non_referred;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getTotalReferedMcoins()
     * @ Added Date               : 01-09-2016
     * @ Added By                 : Mousumi Bakshi
     * -----------------------------------------------------------------
     * @ Description              : This function is used for total of  referred conncetion points
    */
    public function getTotalReferedMcoins($user_id,$activity_user_id=0,$activity_id=0)
    {
        $tot_referred=0;
        $this->db->select('SUM(referred_connections) as tot_referred');
        $this->db->where('fk_user_id',$user_id);
        if($activity_user_id>0){
        $this->db->where('fk_activity_user_id',$activity_user_id);
        }
        if($activity_id>0){
        $this->db->where('fk_mcoin_activity_id',$activity_id);
        }
        $row=$this->db->get($this->_table)->row_array();
        $tot_referred=$row['tot_referred'] ;
        return $tot_referred;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getTotalOwnActivity()
     * @ Added Date               : 01-09-2016
     * @ Added By                 : Mousumi Bakshi
     * -----------------------------------------------------------------
     * @ Description              : This function is used for total of  own activity points
    */
    public function getTotalOwnActivity($user_id,$activity_user_id=0,$activity_id=0)
    {
        $tot_own_activity=0;
        $this->db->select('SUM(own_activity) as tot_own_activity');
        $this->db->where('fk_user_id',$user_id);
        if($activity_user_id>0){
        $this->db->where('fk_activity_user_id',$activity_user_id);
        }
        if($activity_id>0){
        $this->db->where('fk_mcoin_activity_id',$activity_id);
        }
        $row=$this->db->get($this->_table)->row_array();
        $tot_own_activity=$row['tot_own_activity'] ;
        return $tot_own_activity;
    }

    public function getTotalMcoin($user_id,$activity_user_id=0,$activity_id=0){
        $totNon=$this->getTotalNonReferedMcoins($user_id,$activity_user_id,$activity_id);
        $totRefer=$this->getTotalReferedMcoins($user_id,$activity_user_id,$activity_id);
        $totOwn=$this->getTotalOwnActivity($user_id,$activity_user_id,$activity_id);
        $total=$totNon+$totRefer+$totOwn;
        return $total;
    }

    


/*
     * --------------------------------------------------------------------------
     * @ Function Name            : addUser()
     * @ Added Date               : 25-07-2016
     * @ Added By                 : Mousumi Bakshi
     * -----------------------------------------------------------------
     * @ Description              : This function is used for checking email id is exist or not
    */
    public function fetchUserMcoins($params)
    {
        $this->db->select($this->_table.'.*,'.$this->_table_user_profile_basic.'.profile_picture_file_extension,'.$this->_table_user_profile_basic.'.s3_media_version,'.$this->_table_user_profile_basic.'.display_name');
        $this->db->where($this->_table.'.fk_user_id', $params['user_id']);
        $this->db->join($this->_table_user_profile_basic,$this->_table.'.fk_activity_user_id='.$this->_table_user_profile_basic.'.fk_user_id');
        $this->db->order_by('earning_timestamp', 'DESC');
        $this->db->from($this->_table);
        return $this->db->get()->result_array();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : fetchUserMcoinsByuser()
     * @ Added Date               : 25-07-2016
     * @ Added By                 : Mousumi Bakshi
     * -----------------------------------------------------------------
     * @ Description              : This function is used for checking email id is exist or not
    */
    public function fetchUserMcoinsByuser($params)
    {
        $this->db->select($this->_table.'.*,'.$this->_table_user_profile_basic.'.profile_picture_file_extension,s3_media_version,display_name,residence_city,residence_state');
        $this->db->where($this->_table.'.fk_user_id', $params['user_id']);
        $this->db->join($this->_table_user_profile_basic,$this->_table.'.fk_activity_user_id='.$this->_table_user_profile_basic.'.fk_user_id');

        $this->db->group_by($this->_table.'.fk_activity_user_id');
        $this->db->from($this->_table);
        return $this->db->get()->result_array();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : fetchUserMcoinsByuser()
     * @ Added Date               : 25-07-2016
     * @ Added By                 : Mousumi Bakshi
     * -----------------------------------------------------------------
     * @ Description              : This function is used for checking email id is exist or not
    */
    public function connectionDetailsBydate($params)
    {
        $this->db->select($this->_table.'.*,'.$this->_table_user_profile_basic.'.profile_picture_file_extension,s3_media_version,display_name,residence_city,residence_state');
        $this->db->where($this->_table.'.id', $params['user_mcoin_id']);
        $this->db->join($this->_table_user_profile_basic,$this->_table.'.fk_activity_user_id='.$this->_table_user_profile_basic.'.fk_user_id');

        $this->db->group_by($this->_table.'.fk_activity_user_id');
        $this->db->from($this->_table);
        return $this->db->get()->row_array();
    }
    

}