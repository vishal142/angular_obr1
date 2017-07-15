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
    
    
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : addTicket()
     * @ Added Date               : 03-08-2016
     * @ Added By                 : Mousumi Bakshi
     * -----------------------------------------------------------------
     * @ Description              : This function is used for checking email id is exist or not
    */
    public function addTicket($params)
    {
        $this->db->insert($this->_table,$params);
        $insert_id = $this->db->insert_id();
        return $insert_id;      
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : addTicket()
     * @ Added Date               : 03-08-2016
     * @ Added By                 : Mousumi Bakshi
     * -----------------------------------------------------------------
     * @ Description              : This function is used for checking email id is exist or not
    */
    public function addTickeThreads($params)
    {
        $this->db->insert($this->_table_threads,$params);
        $insert_id = $this->db->insert_id();
        return $insert_id;      
    }

    public function updateTickitID($ticket_id,$id){
        $this->db->set('ticket_id',$ticket_id);
        $this->db->where('id',$id);
        $this->db->update($this->_table);
      

    }

    public function fetchAllTicket($id){
        $this->db->where('fk_user_id',$id);
         $this->db->from($this->_table);
        return $this->db->get()->result_array();
      

    }

     public function fetchTicketConversation($params){
        $this->db->select($this->_table.'.id,ticket_id,title,status,'.$this->_table_threads.'.fk_user_id,fk_admin_id,description,is_unread,'.$this->_table_threads.'.added_timestamp');
        $this->db->where($this->_table.'.id',$params['id']);
         $this->db->where($this->_table.'.fk_user_id',$params['user_id']);
         $this->db->from($this->_table);
         $this->db->join($this->_table_threads,$this->_table.'.id='.$this->_table_threads.'.fk_support_ticket_id');
        return $this->db->get()->result_array();
      

    }


    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : addUser()
     * @ Added Date               : 03-08-2016
     * @ Added By                 : Mousumi Bakshi
     * -----------------------------------------------------------------
     * @ Description              : This function is used for checking email id is exist or not
    */
    public function fetchUser($params)
    {

        $this->db->where('email_id', $params['username']);
        $this->db->or_where('mobile_number', $params['username']);
        $this->db->from($this->_table);
        return $this->db->get()->row_array();
    }
    


    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : addUser()
     * @ Added Date               : 03-08-2016
     * @ Added By                 : Mousumi Bakshi
     * -----------------------------------------------------------------
     * @ Description              : This function is used for checking email id is exist or not
    */
    public function fetchMatrialStatus($params)
    {
        $this->db->select('*');
        $this->db->from($this->_table_marital_status);
        return $this->db->get()->result_array();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : addUser()
     * @ Added Date               : 03-08-2016
     * @ Added By                 : Mousumi Bakshi
     * -----------------------------------------------------------------
     * @ Description              : This function is used for checking email id is exist or not
    */
    public function fetchResidenceStatus($params)
    {
        $this->db->select('*');
        $this->db->from($this->_table_residence_status);
        return $this->db->get()->result_array();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : addUser()
     * @ Added Date               : 03-08-2016
     * @ Added By                 : Mousumi Bakshi
     * -----------------------------------------------------------------
     * @ Description              : This function is used for checking email id is exist or not
    */
    public function getDegreeType($params)
    {
        $this->db->select('*');
        $this->db->from($this->_table_degree_type);
        return $this->db->get()->result_array();
    }


    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getDegree()
     * @ Added Date               : 03-08-2016
     * @ Added By                 : Mousumi Bakshi
     * -----------------------------------------------------------------
     * @ Description              : This function is used to get degree
    */
    public function getDegree($params)
    {
        $this->db->select('*');
        $this->db->from($this->_table_degree);
        return $this->db->get()->result_array();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getDegreeName()
     * @ Added Date               : 03-08-2016
     * @ Added By                 : Mousumi Bakshi
     * -----------------------------------------------------------------
     * @ Description              : This function is used to get degree
    */
    public function getDegreeName($fk_degree_id)
    {
        $this->db->select('*');
        $this->db->where('id', $fk_degree_id);
        $this->db->from($this->_table_degree);
        return $this->db->get()->row_array();
    }

     /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getDegree()
     * @ Added Date               : 03-08-2016
     * @ Added By                 : Mousumi Bakshi
     * -----------------------------------------------------------------
     * @ Description              : This function is used to get degree
    */
    public function getFieldOfStudies($params)
    {
        $this->db->select('*');
        $this->db->from($this->_table_field_of_study);
        return $this->db->get()->result_array();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getDegree()
     * @ Added Date               : 03-08-2016
     * @ Added By                 : Mousumi Bakshi
     * -----------------------------------------------------------------
     * @ Description              : This function is used to get degree
    */
    public function getFieldOfStudyName($params)
    {
        $this->db->select('*');
        $this->db->where('id', $params['fk_field_of_study_id']);
        $this->db->from($this->_table_field_of_study);
        return $this->db->get()->row_array();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : addFieldOfStudies()
     * @ Added Date               : 03-08-2016
     * @ Added By                 : Mousumi Bakshi
     * -----------------------------------------------------------------
     * @ Description              : This function is used to get degree
    */
    public function addFieldOfStudies($params)
    {
        $this->db->where('field_of_study', $params['study_name']);
        $this->db->from($this->_table_field_of_study);
        $is_exist=$this->db->count_all_results(); 

        if($is_exist==0){
            $this->db->set('field_of_study', $params['study_name']);
            $this->db->insert($this->_table_field_of_study);
        }

        $this->db->where('field_of_study', $params['study_name']);
        $this->db->from($this->_table_field_of_study);
        $row=$this->db->get()->row_array();
        return $row['id'];
        
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : addFieldOfStudies()
     * @ Added Date               : 03-08-2016
     * @ Added By                 : Mousumi Bakshi
     * -----------------------------------------------------------------
     * @ Description              : This function is used to get degree
    */
    public function addDegree($params)
    {
        $this->db->where('degree_name', $params['degree_name']);
        $this->db->from($this->_table_degree);
        $is_exist=$this->db->count_all_results(); 

        if($is_exist==0){
            $this->db->set('degree_name', $params['degree_name']);
            $this->db->insert($this->_table_degree);
        }

        $this->db->where('degree_name', $params['degree_name']);
        $this->db->from($this->_table_degree);
        $row=$this->db->get()->row_array();
        return $row['id'];
        
    }
    
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getDegreeTypeName()
     * @ Added Date               : 09-08-2016
     * @ Added By                 : Mousumi Bakshi
     * -----------------------------------------------------------------
     * @ Description              : This function is used to get degree
    */
    public function getDegreeTypeName($fk_degree_type_id)
    {
        $this->db->select('*');
        $this->db->where('id', $fk_degree_type_id);
        $this->db->from($this->_table_degree_type);
        return $this->db->get()->row_array();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getDegreeTypeName()
     * @ Added Date               : 09-08-2016
     * @ Added By                 : Mousumi Bakshi
     * -----------------------------------------------------------------
     * @ Description              : This function is used to get degree
    */
    public function getPincodeDetails($pincode_id)
    {
        $this->db->select('*');
        $this->db->where('pin_code', $pincode_id);
        $this->db->from($this->_master_pincode);
        return $this->db->get()->row_array();
    }

     /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getDegreeTypeName()
     * @ Added Date               : 09-08-2016
     * @ Added By                 : Mousumi Bakshi
     * -----------------------------------------------------------------
     * @ Description              : This function is used to get degree
    */
    public function getPincodeDetailsId($pincode_id)
    {
        $this->db->select('*');
        $this->db->where('id', $pincode_id);
        $this->db->from($this->_master_pincode);
        return $this->db->get()->row_array();
    }

    
    //public 
    /**********************************************************************************************************************************
     * End of user model
     *********************************************************************************************************************************/
}