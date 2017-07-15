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
class Master_config_model extends CI_Model
{
    public $_master_configurations = 'master_configurations';
    public $_master_reward_earnings = 'master_reward_earnings';
    public $_master_user_levels = 'master_user_levels';
    public $_master_mcoin_earnings = 'master_mcoin_earnings';
    function __construct()
    {
        // 
        //load the parent constructor
        parent::__construct();
        // $this->tables = $this->config->item('tables'); 
    }
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getConfig_data()
     * @ Added Date               : 
     * @ Added By                 : 
     * -----------------------------------------------------------------
     * @ Description              : get all Cofiguration details
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
     */
    public function getConfig_data()
    {
        $this->db->select('*');
        $result = $this->db->get($this->_master_configurations)->row_array();
        return $result;
    }
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : update_config()
     * @ Added Date               : 20-05-2016
     * @ Added By                 : Subhankar    
     * -----------------------------------------------------------------
     * @ Description              : Update configuration data
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
     */
    public function update_config($where = array(), $param = array())
    {
        $this->db->where($where);
        $result        = $this->db->update($this->_master_configurations, $param);
        $affected_rows = $this->db->affected_rows();
        if ($affected_rows) {
            return true;
        } else {
            return false;
        }
        // $affected_rows = $this->db->affected_rows(); 
        // return $affected_rows;
    }
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getRewards_data()
     * @ Added Date               : 20-05-2016
     * @ Added By                 : Subhankar    
     * -----------------------------------------------------------------
     * @ Description              : Get all rewards data
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
     */
    public function getRewards_data()
    {
        $this->db->select('*');
        $where = array(
            'fk_product_id ' => NULL,
            'fk_reward_activity_id' => 1
        );
        $this->db->where($where);
        $result = $this->db->get($this->_master_reward_earnings)->row_array();
        //echo $this->db->last_query(); exit;
        return $result;
    }
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : update_reward_config()
     * @ Added Date               : 20-05-2016
     * @ Added By                 : Subhankar    
     * -----------------------------------------------------------------
     * @ Description              : Updat rewards data
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
     */
    public function update_reward_config($where = array(), $param = array())
    {
        $this->db->where($where);
        $this->db->update($this->_master_reward_earnings, $param);
        $affected_rows = $this->db->affected_rows();
        //return $affected_rows;
        if ($affected_rows) {
            return true;
        } else {
            return false;
        }
    }
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getTierUsage_data()
     * @ Added Date               : 20-05-2016
     * @ Added By                 : Subhankar    
     * -----------------------------------------------------------------
     * @ Description              : Get tier usage data
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
     */
    public function getTierUsage_data()
    {
        $this->db->select('*');
        $result = $this->db->get($this->_master_user_levels)->result_array();
        //echo $this->db->last_query(); exit;
        return $result;
    }
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : batchUpdateLevel()
     * @ Added Date               : 20-05-2016
     * @ Added By                 : Subhankar    
     * -----------------------------------------------------------------
     * @ Description              : Update tier usage level
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
     */
    public function batchUpdateLevel($param = array(), $where_key = '')
    {
        $this->db->update_batch($this->_master_user_levels, $param, $where_key);
        $affected_rows = $this->db->affected_rows();
        //return $affected_rows;
        if ($affected_rows) {
            return true;
        } else {
            return false;
        }
        //die($this->db->last_query());
    }
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getMcoinEarning_data()
     * @ Added Date               : 20-05-2016
     * @ Added By                 : Subhankar    
     * -----------------------------------------------------------------
     * @ Description              : Fetch all mcoin earnings
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
     */
    public function getMcoinEarning_data()
    {
        $this->db->select('*');
        $where = array(
            'fk_product_id ' => NULL,
            'fk_mcoin_activity_id' => 1
        );
        $this->db->where($where);
        $result = $this->db->get($this->_master_mcoin_earnings)->row_array();
        //echo $this->db->last_query(); exit;
        return $result;
    }
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : updateEarnings()
     * @ Added Date               : 20-05-2016
     * @ Added By                 : Subhankar    
     * -----------------------------------------------------------------
     * @ Description              : Update mcoin earnings
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
     */
    public function updateEarnings($where = array(), $param = array())
    {
        $this->db->where($where);
        $this->db->update($this->_master_mcoin_earnings, $param);
        $affected_rows = $this->db->affected_rows();
        //return $affected_rows;
        if ($affected_rows) {
            return true;
        } else {
            return false;
        }
    }
}
?>