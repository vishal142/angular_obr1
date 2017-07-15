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
class AdminDataCollections_model extends CI_Model
{
    public $_tbl_admin_data_collections = 'tbl_admin_data_collections';
    function __construct()
    {
        // 
        //load the parent constructor
        parent::__construct();
        // $this->tables = $this->config->item('tables'); 
    }
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getAllUserData()
     * @ Added Date               : 27-10-2016
     * @ Added By                 : Amit pandit
     * -----------------------------------------------------------------
     * @ Description              : get all user data
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
     */
    public function getAllUserData($param = array(), $where = array())
    {
        $this->db->select('*');
        if (!empty($where)) {
            $this->db->where($where);
        }
        if (!empty($param['page']) && !empty($param['page_size'])) {
            $limit  = $param['page_size'];
            $offset = $limit * ($param['page'] - 1);
            $this->db->limit($limit, $offset);
        }
        if ($param['order_by'] && $param['order']) {
            $this->db->order_by($param['order_by'], $param['order']);
        }
        $result = $this->db->get($this->_tbl_admin_data_collections)->result_array();


        $newArray                  = array();
        foreach ($result as $key => $value) {
            $tempArray                = array();
            $tempArray                = $value;
            $tempArray['approved_timestamp']    = date("Y-m-d",strtotime($value['approved_timestamp']));
            $tempArray['mail_sent_timestamp']   = date("Y-m-d",strtotime($value['mail_sent_timestamp']));
            $newArray[]               = $tempArray;
        }
        return $newArray;
    }
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getAllUserData_count()
     * @ Added Date               : 27-10-2016
     * @ Added By                 : Amit pandit
     * -----------------------------------------------------------------
     * @ Description              : get all user data count
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
     */
    public function getAllUserData_count($param = array(), $where = array())
    {
        $this->db->select('tadc.id');
        if (!empty($where)) {
            $this->db->where($where);
        }
        $result = $this->db->count_all_results($this->_tbl_admin_data_collections . ' AS tadc');
        //pre($result,1);die;
        return $result;
    }
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getEmailId()
     * @ Added Date               : 27-10-2016
     * @ Added By                 : Amit pandit
     * -----------------------------------------------------------------
     * @ Description              : get User Email id for sending email
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
     */
    public function getEmailId($where = array())
    {
        $this->db->select('tadc.user_email');
        if (!empty($where)) {
            $this->db->where($where);
        }
        $result = $this->db->get($this->_tbl_admin_data_collections . ' AS tadc')->row_array();
        //pre($result,1);die;
        return $result;
    }

    
    public function checkIsApproved($where = array())
    {
        $this->db->select('tadc.is_approved');
        $this->db->where($where);
        //$this->db->where('tadc.is_mail_sent',null);
        $result = $this->db->get($this->_tbl_admin_data_collections . ' AS tadc')->row_array();
        return $result;
    }
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : updateApprovedStatus()
     * @ Added Date               : 27-10-2016
     * @ Added By                 : Amit pandit
     * -----------------------------------------------------------------
     * @ Description              : Approved user status 
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
     */
    public function updateApprovedStatus($whereId = array(), $params = array())
    {
        $this->db->where($whereId);
        $result        = $this->db->update($this->_tbl_admin_data_collections, $params);
        $affected_rows = $this->db->affected_rows();
        //$insert_id = $this->db->insert_id();
        if ($affected_rows) {
            return true;
        } else {
            return false;
        }
    }


    public function checkIfMailSent($where = array())
    {
        $this->db->select('tadc.is_mail_sent');
        $this->db->where($where);
        //$this->db->where('tadc.is_mail_sent',null);
        $result = $this->db->get($this->_tbl_admin_data_collections . ' AS tadc')->row_array();
        return $result;
    }
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : updateEmailSentStatus()
     * @ Added Date               : 27-10-2016
     * @ Added By                 : Amit pandit
     * -----------------------------------------------------------------
     * @ Description              : Update Email sent status of user
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
     */
    public function updateEmailSentStatus($whereId = array(), $params = array())
    {
        $this->db->where($whereId);
        $result        = $this->db->update($this->_tbl_admin_data_collections, $params);
        $affected_rows = $this->db->affected_rows();
        //$insert_id = $this->db->insert_id();
        if ($affected_rows) {
            return true;
        } else {
            return false;
        }
    }


    
}