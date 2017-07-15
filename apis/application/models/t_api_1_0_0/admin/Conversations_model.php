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
class Conversations_model extends CI_Model
{
    public $_tbl_user_support_tickets = 'tbl_user_support_tickets';
    public $_tbl_user_support_ticket_threads = 'tbl_user_support_ticket_threads';
    public $_tbl_users = 'tbl_users';
    public $_tbl_admins = 'tbl_admins';
    public $_tbl_user_profile_basics = 'tbl_user_profile_basics';
    function __construct()
    {
        // 
        //load the parent constructor
        parent::__construct();
        // $this->tables = $this->config->item('tables'); 
    }
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getAllConversations()
     * @ Added Date               : 19-10-2016
     * @ Added By                 : Amit pandit
     * -----------------------------------------------------------------
     * @ Description              : get Support Tickets details for conversations
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
     */
    public function getAllConversations($param = array())
    {
        $this->db->select('tust.*');
        if (!empty($param['page']) && !empty($param['page_size'])) {
            $limit  = $param['page_size'];
            $offset = $limit * ($param['page'] - 1);
            $this->db->limit($limit, $offset);
        }
        if ($param['order_by'] && $param['order']) {
            $this->db->order_by($param['order_by'], $param['order']);
        }
        if (!empty($param['filterByStatus'])) {
            $this->db->like('tust.status', $param['filterByStatus']);
        }
        /*if (!empty($param['searchByTitle'])) {
            $this->db->like('tust.title', $param['searchByTitle']);
        }*/
        if (!empty($param['searchByTitle'])) {
            $where = "(tust.title LIKE '%" . $param['searchByTitle'] . "%'  OR
        tust.ticket_id LIKE '%" . $param['searchByTitle'] . "%' 
        )";
            $this->db->where($where);
        }

        $result['tickets_details'] = $this->db->get($this->_tbl_user_support_tickets . ' AS tust')->result_array();
        $newArray                  = array();
        foreach ($result['tickets_details'] as $key => $value) {
            $tempArray                = array();
            $desc                     = $this->getAllTicket_descriptions($value['id']);
            $tempArray                = $value;
            $tempArray['added_timestamp'] = date("M j, Y g:i a", strtotime($value['added_timestamp']));            
            $tempArray['description'] = $desc;
            $newArray[]               = $tempArray;
        }
        return $newArray;
    }
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getAllTickets_count()
     * @ Added Date               : 26-10-2016
     * @ Added By                 : Amit pandit
     * -----------------------------------------------------------------
     * @ Description              : get Support Tickets  count
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
     */
    public function getAllConversationsCount($param = array())
    {
        $this->db->select('tust.id');        
        
        if (!empty($param['filterByStatus'])) {
            $this->db->like('tust.status', $param['filterByStatus']);
        }
        /*if (!empty($param['searchByTitle'])) {
            $this->db->like('tust.title', $param['searchByTitle']);
        }*/
        if (!empty($param['searchByTitle'])) {
            $where = "(tust.title LIKE '%" . $param['searchByTitle'] . "%'  OR
        tust.ticket_id LIKE '%" . $param['searchByTitle'] . "%' 
        )";
            $this->db->where($where);
        }
        $result = $this->db->count_all_results($this->_tbl_user_support_tickets . ' AS tust');
        //pre($result,1);die;
        return $result;
    }



    public function getConversationDetails($where = array()){

        //pre($where,1);
        $this->db->select('tust.id, tust.ticket_id, tust.status, tust.added_timestamp, tust.title, tust.fk_user_id, (select(max(tusth.fk_admin_id))) as fk_admin_id, tupb.profile_picture_file_extension, tupb.s3_media_version');
        $this->db->where($where);
        $this->db->join($this->_tbl_user_profile_basics . ' AS tupb', 'tupb.fk_user_id=tust.fk_user_id', 'left');
        $this->db->join($this->_tbl_user_support_ticket_threads . ' tusth', 'tusth.fk_support_ticket_id=tust.id', 'right');
        $result = $this->db->get($this->_tbl_user_support_tickets . ' AS tust')->row_array();
        $result['added_timestamp'] = date("M j, Y g:i a", strtotime($result['added_timestamp']));

        
        $result['description'] = $this->getAllTicket_descriptions($result['id']);

        $profile_picture_file_url    = ($result['profile_picture_file_extension'] != null) ? $this->config->item('bucket_url') . $result['fk_user_id'] . '/profile/' . $result['fk_user_id'] . '.' . $result['profile_picture_file_extension'] . '?versionId=' . $result['s3_media_version'] : "assets/img/avatar/avatar_1.svg";
        $result['user_profile_pic_url'] = $profile_picture_file_url;        
        $result['admin_profile_pic'] = $this->getAdminProfile_Image($result['fk_admin_id']);
        
        $result['all_conversation_threads'] = $this->getAllConversationThreads($result['id']);

        //pre($result,1);
        return $result;
    }


    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getAllTicket_descriptions()
     * @ Added Date               : 26-10-2016
     * @ Added By                 : Amit pandit
     * -----------------------------------------------------------------
     * @ Description              : get Support Tickets descriptions
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
     */
    public function getAllTicket_descriptions($ticket_id)
    {
        //print_r($param);die;
        
        $this->db->select('tusth.description');
        $this->db->join($this->_tbl_user_support_tickets . ' AS tust', 'tust.id=tusth.fk_support_ticket_id', 'left');
        $this->db->where('tust.id', $ticket_id);
        $this->db->where('tusth.fk_admin_id', null);
        $this->db->order_by('tusth.added_timestamp', 'ASC');
        $this->db->limit(1);
        $result = $this->db->get($this->_tbl_user_support_ticket_threads . ' AS tusth')->row_array();
        return $result['description'];
    }
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getAllConversations()
     * @ Added Date               : 19-10-2016
     * @ Added By                 : Amit pandit
     * -----------------------------------------------------------------
     * @ Description              : get Support Tickets conversations
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
     */
    public function getAllConversationThreads($ticket_id)
    {
        $this->db->select('tusth.*');
        $this->db->where('tusth.fk_support_ticket_id', $ticket_id);
        $this->db->order_by('tusth.added_timestamp', 'ASC');
        $result = $this->db->get($this->_tbl_user_support_ticket_threads . ' AS tusth')->result_array();

        $newArray                  = array();
        foreach ($result as $key => $value) {
            $tempArray                = array();
            $tempArray                = $value;
            $tempArray['added_timestamp'] = date("M j, Y g:i a", strtotime($value['added_timestamp']));
            $newArray[]               = $tempArray;
        }
        return $newArray;
    }


    
    public function getAdminProfile_Image($id)
    {
        $this->db->select('ta.has_profile_picture');
        //$this->db->where('ta.has_profile_picture','is not null');
        $this->db->where('ta.id', $id);
        $result = $this->db->get($this->_tbl_admins . ' AS ta')->row_array();
        if ($result['has_profile_picture'] == 0) {
            $admin_profile_pic_url                   = "";
            $result['user_profile_picture_file_url'] = $admin_profile_pic_url;
            return $result['user_profile_picture_file_url'];
        } else {
            $this->db->select('ta.file_extension');
            $this->db->where('ta.id', $id);
            $result = $this->db->get($this->_tbl_admins . ' AS ta')->row_array();
            return $id . "." . $result['file_extension'];
        }
        //pre($result);die;
        //pre($result);die;
    }


    public function addConversationThreads($params){
      $this->db->insert($this->_tbl_user_support_ticket_threads,$params);
         $insert_id = $this->db->insert_id();
        return $insert_id;

    }

    public function updateConversation($params){

        $this->db->where('id', $params['id']);
        $result =$this->db->update($this->_tbl_user_support_tickets,$params);
        $affected_rows = $this->db->affected_rows();
        //$insert_id = $this->db->insert_id();

        if ($affected_rows) {
            return true;
        } else {
            return false;
        }
    }
}