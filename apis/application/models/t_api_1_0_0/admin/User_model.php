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
class User_model extends CI_Model
{
    public $_table                          = 'tbl_users';
    public $_tbl_user_profile_basics        = 'tbl_user_profile_basics';
    public $_tbl_user_tmp_profile_basics    = 'tbl_user_tmp_profile_basics';
    public $_tbl_history_user_profile_basics= 'tbl_history_user_profile_basics';
    public $_tbl_user_profile_educations    = 'tbl_user_profile_educations';
    public $_tbl_user_tmp_profile_educations= 'tbl_user_tmp_profile_educations';
    public $_tbl_history_user_profile_educations = 'tbl_history_user_profile_educations';
    public $_tbl_user_profile_kycs          = 'tbl_user_profile_kycs';
    public $_tbl_user_tmp_profile_kycs      = 'tbl_user_tmp_profile_kycs';
    public $_tbl_history_user_profile_kycs  = 'tbl_history_user_profile_kycs';
    public $_tbl_user_profile_banks         = 'tbl_user_profile_banks';
    public $_tbl_user_tmp_profile_banks     = 'tbl_user_tmp_profile_banks';
    public $_tbl_history_user_profile_banks = 'tbl_history_user_profile_banks';
    public $_master_kyc_templates           = 'master_kyc_templates';
    public $_master_kyc_documents           = 'master_kyc_documents';
    public $_tbl_user_types                 = 'tbl_user_types';
    public $_master_profession_types        = 'master_profession_types';
    public $_tbl_user_connections           = 'tbl_user_connections';
    public $_tbl_user_mcoins_earnings       = 'tbl_user_mcoins_earnings';
    public $_master_genders                 = 'master_genders';
    public $_master_marital_statuses        = 'master_marital_statuses';
    public $_master_degree_types            = 'master_degree_types';
    public $_master_degrees                 = 'master_degrees';
    public $_master_field_of_studies        = 'master_field_of_studies';
    public $_master_pincodes                = 'master_pincodes';
    public $_tbl_user_referals              = 'tbl_user_referals';
    public $_master_mcoin_earnings          = 'master_mcoin_earnings';
    public $_tbl_user_levels                = 'tbl_user_levels';
    public $_master_user_levels             = 'master_user_levels';
    public $_master_banks                   = 'master_banks';
    public $_tbl_user_custom_adjustments    = 'tbl_user_custom_adjustments';
    public $_master_mpokket_accounts        = 'master_mpokket_accounts';
    public $_tbl_user_mpokket_accounts      = 'tbl_user_mpokket_accounts';
    public $_tbl_user_approvals             = "tbl_user_approvals";
    public $_master_reward_earnings         = "master_reward_earnings";
    public $_tbl_agent_reward_earnings      = 'tbl_agent_reward_earnings';

    public $_tbl_user_mobile_devices        = 'tbl_user_mobile_devices';
    public $_tbl_user_loginkeys             = 'tbl_user_loginkeys';
   

    function __construct()
    {
        //load the parent constructor
        parent::__construct();        
        $this->tables = $this->config->item('tables'); 
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getAllUsers()
     * @ Added Date               : 09-09-2016
     * @ Added By                 : Piyalee    
     * -----------------------------------------------------------------
     * @ Description              : get all user
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
    */
    public function getAllUsers($param = array())
    {
        $orderby = "";
        $limitsql = '';
        $where = " WHERE 1 ";
        if(!empty($param['order_by']) && !empty($param['order'])){
            $orderby.=" ORDER BY ".$param['order_by']." ".$param['order'];
        }

        if(!empty($param['searchByNameEmail']))
        {
            $where.= " AND ( u.email_id LIKE '%".$param['searchByNameEmail']."%' OR up.f_name LIKE '%".$param['searchByNameEmail']."%' OR up.m_name LIKE '%".$param['searchByNameEmail']."%' OR up.l_name LIKE '%".$param['searchByNameEmail']."%' OR CONCAT_WS(' ', up.f_name,up.l_name) LIKE '%".$param['searchByNameEmail']."%' OR CONCAT_WS(' ', up.f_name,up.m_name,up.l_name) LIKE '%".$param['searchByNameEmail']."%' )";
        }

        if(!empty($param['searchUserMode']))
        {
            if($param['searchUserMode'] != 'A')
            {
                $where.= " AND ut.user_mode = '".$param['searchUserMode']."' ";
            }
            else
            {
                $where.= " AND ut.is_agent = 1";
            }
        }

        if(!empty($param['searchProfession']))
        {
            $where.= " AND ut.fk_profession_type_id = ".$param['searchProfession'];
        }

        if(!empty($param['page']) && !empty($param['page_size']))
        {
            $limit = $param['page_size'];
            $offset = $limit*($param['page']-1);
            $limitsql.= " LIMIT ".$offset.", ".$limit;
        }
        $join = " LEFT JOIN ".$this->_tbl_user_profile_basics." AS up ON up.fk_user_id = u.id LEFT JOIN ".$this->_tbl_user_tmp_profile_basics." AS utp ON utp.fk_user_id = u.id LEFT JOIN ".$this->_tbl_user_types." AS ut ON ut.fk_user_id = u.id  LEFT JOIN ".$this->_master_profession_types." AS mpt ON mpt.id = ut.fk_profession_type_id AND mpt.is_active=1 LEFT JOIN ".$this->_tbl_user_approvals." AS uapv ON uapv.fk_user_id = u.id ";

        $sql = "SELECT u.*, up.id as up_id, utp.id as utp_id, uapv.id as uapv_id, up.f_name, up.m_name, up.l_name, up.profile_picture_file_extension, up.s3_media_version, ut.user_mode, ut.is_agent, mpt.profession_type FROM ".$this->_table." as u ".$join.$where.$orderby.$limitsql;
        $result = $this->db->query($sql)->result_array();
        //echo $this->db->last_query();
        return $result;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getAllUsersCount()
     * @ Added Date               : 09-09-2016
     * @ Added By                 : Piyalee    
     * -----------------------------------------------------------------
     * @ Description              : get all user count
     * -----------------------------------------------------------------
     * @ param                    : Array(params)    
     * @ return                   : Array
     * -----------------------------------------------------------------
     * 
    */
    public function getAllUsersCount($param = array())
    {
        $where = " WHERE 1 ";
        if(!empty($param['searchByNameEmail']))
        {
            $where.= " AND ( u.email_id LIKE '%".$param['searchByNameEmail']."%' OR u.display_name LIKE '%".$param['searchByNameEmail']."%') ";
        }

        if(!empty($param['searchUserMode']))
        {
            if($param['searchUserMode'] != 'A')
            {
                $where.= " AND ut.user_mode = '".$param['searchUserMode']."'";
            }
            else
            {
                $where.= " AND ut.is_agent = 1";
            }
        }

        if(!empty($param['searchProfession']))
        {
            $where.= " AND ut.fk_profession_type_id = ".$param['searchProfession'];
        }

        $join = " LEFT JOIN ".$this->_tbl_user_profile_basics." AS up ON up.fk_user_id = u.id LEFT JOIN ".$this->_tbl_user_tmp_profile_basics." AS utp ON utp.fk_user_id = u.id LEFT JOIN ".$this->_tbl_user_types." AS ut ON ut.fk_user_id = u.id  LEFT JOIN ".$this->_master_profession_types." AS mpt ON mpt.id = ut.fk_profession_type_id";

        $sql = "SELECT count(u.id) as count_user FROM ".$this->_table." as u ".$join.$where;
        $result = $this->db->query($sql)->row_array();
        //echo $this->db->last_query();
        return $result;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getCurrentConnection()
     * @ Added Date               : 09-09-2016
     * @ Added By                 : Mousumi Bakshi
     * -----------------------------------------------------------------
     * @ Description              : This function is used for get no of connection
    */
    public function getCurrentConnection($user_id)
    {
        $where="1 AND connection_status='C' and (fk_user_id='".$user_id."' OR fk_connection_id='".$user_id."')";
        $this->db->where($where);
        
        $this->db->from($this->_tbl_user_connections);
        return $this->db->count_all_results();        
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
        $tot_non_referred = 0;
        $this->db->select('SUM(non_referred_connections) as tot_non_referred');
        if($activity_user_id>0)
        {
            $this->db->where('fk_activity_user_id',$activity_user_id);
        }
        if($activity_id>0)
        {
            $this->db->where('fk_mcoin_activity_id',$activity_id);
        }
        $this->db->where('fk_user_id',$user_id);
        $row = $this->db->get($this->_tbl_user_mcoins_earnings)->row_array();
        $tot_non_referred = $row['tot_non_referred'] ;
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
        $tot_referred = 0;
        $this->db->select('SUM(referred_connections) as tot_referred');
        $this->db->where('fk_user_id',$user_id);
        if($activity_user_id>0)
        {
            $this->db->where('fk_activity_user_id',$activity_user_id);
        }
        if($activity_id>0)
        {
            $this->db->where('fk_mcoin_activity_id',$activity_id);
        }
        $row = $this->db->get($this->_tbl_user_mcoins_earnings)->row_array();
        $tot_referred = $row['tot_referred'] ;
        return $tot_referred;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getTotalOwnActivity()
     * @ Added Date               : 09-09-2016
     * @ Added By                 : Mousumi Bakshi
     * -----------------------------------------------------------------
     * @ Description              : This function is used for total of own activity points
    */
    public function getTotalOwnActivity($user_id,$activity_user_id=0,$activity_id=0)
    {
        $tot_own_activity=0;
        $this->db->select('SUM(own_activity) as tot_own_activity');
        $this->db->where('fk_user_id',$user_id);
        if($activity_user_id>0)
        {
            $this->db->where('fk_activity_user_id',$activity_user_id);
        }
        if($activity_id>0)
        {
            $this->db->where('fk_mcoin_activity_id',$activity_id);
        }
        $row=$this->db->get($this->_tbl_user_mcoins_earnings)->row_array();
        $tot_own_activity=$row['tot_own_activity'] ;
        return $tot_own_activity;
    }

    public function getTotalMcoin($user_id,$activity_user_id=0,$activity_id=0)
    {
        $totNon     = $this->getTotalNonReferedMcoins($user_id,$activity_user_id,$activity_id);
        $totRefer   = $this->getTotalReferedMcoins($user_id,$activity_user_id,$activity_id);
        $totOwn     = $this->getTotalOwnActivity($user_id,$activity_user_id,$activity_id);
        $total      = $totNon+$totRefer+$totOwn;
        return $total;
    }
    
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getAllProfessions()
     * @ Added Date               : 09-09-2016
     * @ Added By                 : Mousumi Bakshi
     * -----------------------------------------------------------------
     * @ Description              : This function is used for total of own activity points
    */
    public function getAllProfessions()
    {
        $this->db->select('*');
        $this->db->where('is_active', '1');
        $row = $this->db->get($this->_master_profession_types)->result_array();
        return $row;
    }
    
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getTempUserBasicDetails()
     * @ Added Date               : 12-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is used for total of own activity points
    */
    public function getTempUserBasicDetails($param=array())
    {
        $this->db->select('utp.*, IFNULL(utp.admin_message_profile_name,"") as admin_message_profile_name, IFNULL(utp.admin_message_residence_address,"") as admin_message_residence_address, IFNULL(utp.admin_message_permanent_address,"") as admin_message_permanent_address, IFNULL(utp.admin_message_other_info,"") as admin_message_other_info, utp.id as utp_id, mpt.profession_type, mg.gender, ms.marital_status');
        $this->db->join($this->_master_profession_types." as mpt","mpt.id = utp.fk_profession_type_id", "LEFT");
        $this->db->join($this->_master_genders." as mg","mg.id = utp.fk_gender_id", "LEFT");
        $this->db->join($this->_master_marital_statuses." as ms","ms.id = utp.fk_marital_status_id", "LEFT");
        if($param['userId'])
        {
            $this->db->where('utp.fk_user_id',$param['userId']);
        }
        $row = $this->db->get($this->_tbl_user_tmp_profile_basics." as utp")->row_array();
        return $row;
    }
    
    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getUserBasicDetails()
     * @ Added Date               : 12-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is used for main basic details
    */
    public function getMainUserBasicDetails($param=array())
    {
        $this->db->select('up.*, mpt.profession_type, mg.gender, ms.marital_status');
        $this->db->join($this->_master_profession_types." as mpt","mpt.id = up.fk_profession_type_id", "LEFT");
        $this->db->join($this->_master_genders." as mg","mg.id = up.fk_gender_id", "LEFT");
        $this->db->join($this->_master_marital_statuses." as ms","ms.id = up.fk_marital_status_id", "LEFT");
        if($param['userId'])
        {
            $this->db->where('up.fk_user_id',$param['userId']);
        }
        $row = $this->db->get($this->_tbl_user_profile_basics." as up")->row_array();
        return $row;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : checkUserBasicInfo()
     * @ Added Date               : 12-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is used for checking user basic main info
    */
    public function checkUserBasicInfo($param=array())
    {
        $this->db->select('up.*');
        if($param['userId'])
        {
            $this->db->where('up.fk_user_id',$param['userId']);
        }
        $row = $this->db->get($this->_tbl_user_profile_basics." as up")->row_array();
        return $row;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : checkDisplayNameUnique()
     * @ Added Date               : 26-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is used for duplicate checking display name
    */
    public function checkDisplayNameUnique($param = array())
    {
        $this->db->select("id");
        if($param['userId'])
        {
            $this->db->where("fk_user_id != ", $param['userId']);
        }
        if($param['display_name'])
        {
            $this->db->where('display_name', $param['display_name']);
        }
        $qry = $this->db->get($this->_tbl_user_profile_basics, $param); 
        return $qry->result_array();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : updateBasicTempInfo()
     * @ Added Date               : 13-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is used for decline field
    */
    public function updateBasicTempInfo($param = array(), $where = array())
    {
        $this->db->where($where);
        $this->db->update($this->_tbl_user_tmp_profile_basics, $param);
        $affected_rows = $this->db->affected_rows(); 
        return $affected_rows;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : fetchUserTempBasic()
     * @ Added Date               : 14-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is used for fetch temp basic details
    */
    public function fetchUserTempBasic($param = array())
    {
        $this->db->select('*');
        $this->db->where('id',$param['utp_id']);
        $this->db->where('fk_user_id',$param['userId']);
        $this->db->from($this->_tbl_user_tmp_profile_basics);
        $details = $this->db->get()->row_array(); 
        return $details;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : updateBasicMain()
     * @ Added Date               : 14-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is used for update basic main
    */
    public function updateBasicMain($param = array(), $where = array())
    {
        $this->db->where($where);
        $this->db->update($this->_tbl_user_profile_basics, $param);
        $affected_rows = $this->db->affected_rows(); 
        return $affected_rows;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : insertInBacisMain()
     * @ Added Date               : 14-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is insert into main user basic
    */
    public function insertInBacisMain($param = array())
    {
        $this->db->insert($this->_tbl_user_profile_basics, $param);
        $insert_id = $this->db->insert_id(); 
        return $insert_id;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : insertInHistory()
     * @ Added Date               : 14-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is insert into history user basic
    */
    public function insertInHistory($param = array())
    {
        $this->db->insert($this->_tbl_history_user_profile_basics, $param);
        $insert_id = $this->db->insert_id(); 
        return $insert_id;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : deleteFromTempBasic()
     * @ Added Date               : 14-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is delete temp user basic
    */
    public function deleteFromTempBasic($where = array())
    {
        $this->db->where($where);
        $this->db->delete($this->_tbl_user_tmp_profile_basics);
        return $this->db->affected_rows();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : checkReferalUser()
     * @ Added Date               : 15-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is check referal user
    */
    public function checkReferalUser($userId)
    {
        $this->db->select("*");
        $this->db->where('fk_user_id',$userId);
        $qry = $this->db->get($this->_tbl_user_referals);
        return $qry->row_array();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getMcoinEarning()
     * @ Added Date               : 15-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is get mcoin earning point
    */
    public function getMcoinEarning($activity_id)
    {
        $this->db->select("*");
        $this->db->where('fk_mcoin_activity_id',$activity_id);
        $qry = $this->db->get($this->_master_mcoin_earnings);
        return $qry->result_array();
    }
 

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : insertMcoinEarning()
     * @ Added Date               : 15-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is insert mcoin point
    */
    public function insertMcoinEarning($insertArr = array())
    {
        $this->db->insert($this->_tbl_user_mcoins_earnings, $insertArr);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }


   /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getRewardEarning()
     * @ Added Date               : 15-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is get mcoin earning point
    */

    public function getRewardEarning($activity_id)
    {
        $this->db->select("*");
        $this->db->where('fk_reward_activity_id',$activity_id);
        $qry = $this->db->get($this->_master_reward_earnings);
        return $qry->row_array();
    }


    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : insertRewardEarning()
     * @ Added Date               : 15-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is insert mcoin point
    */
    public function insertRewardEarning($insertArr = array())
    {
        $this->db->insert($this->_tbl_agent_reward_earnings, $insertArr);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }


    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : checkUserLevel()
     * @ Added Date               : 15-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is check exist user level
    */
    public function checkUserLevel($userId)
    {
        $this->db->select('*');
        $this->db->where('fk_user_id', $userId);
        $level_details = $this->db->get($this->_tbl_user_levels)->row_array();
        return $level_details;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : fetchUserLevelPerPoint()
     * @ Added Date               : 15-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is fetch level per point
    */
    public function fetchUserLevelPerPoint($point)
    {
        $this->db->select('*');
        $this->db->where('qualifying_mcoin_points', $point);
        $this->db->or_where('qualifying_mcoin_points <', $point);
        $this->db->order_by('id', 'DESC');
        $level_details = $this->db->get($this->_master_user_levels)->row_array();
        return $level_details;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : updateUserLevels()
     * @ Added Date               : 15-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is update level
    */
    public function updateUserLevels($param = array(), $where = array())
    {
        $this->db->where($where);
        $this->db->update($this->_tbl_user_levels, $param);
        $affected_rows = $this->db->affected_rows(); 
        return $affected_rows;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : insertUserLevels()
     * @ Added Date               : 15-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is insert user level
    */
    public function insertUserLevels($insertArr = array())
    {
        $this->db->insert($this->_tbl_user_levels, $insertArr);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : checkTempEducation()
     * @ Added Date               : 15-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is check temp education
    */
    public function checkTempEducation($user_id)
    {
        $this->db->select('count(id) as tot_edus');
        $this->db->where('fk_user_id', $user_id);
        $tempEductns = $this->db->get($this->_tbl_user_tmp_profile_educations)->result_array();
        return $tempEductns;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getEducationTempLists()
     * @ Added Date               : 15-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is get temp user education
    */
    public function getEducationTempLists($param = array())
    {
        $this->db->select("ed.*, dt.degree_type, d.degree_name, fs.field_of_study, p.pin_code, p.city_name");
        $this->db->join($this->_master_degree_types." as dt",'dt.id = ed.fk_degree_type_id',"LEFT");
        $this->db->join($this->_master_degrees." as d",'d.id = ed.fk_degree_id',"LEFT");
        $this->db->join($this->_master_field_of_studies." as fs",'fs.id=ed.fk_field_of_study_id',"LEFT");
        $this->db->join($this->_master_pincodes." as p",'p.id = ed.fk_pincode_id',"LEFT");
        if(isset($param['userId']))
        {
            $this->db->where('fk_user_id', $param['userId']);
        }

        if(!empty($param['order_by']) && !empty($param['order'])){
            $this->db->order_by($param['order_by'],$param['order']);
        }

        if(isset($param['mainIds']) && $param['mainIds'])
        {
            $notWhere = " fk_profile_education_id NOT IN(".$param['mainIds'].")";
            $this->db->where($notWhere);
        }

        if(!empty($param['page']) && !empty($param['page_size']))
        {
            $limit = $param['page_size'];
            $offset = $limit*($param['page']-1);
            $this->db->limit($limit, $offset);
        }
        $qry = $this->db->get($this->_tbl_user_tmp_profile_educations." as ed");
        return $qry->result_array();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getEducationMainLists()
     * @ Added Date               : 15-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is get main user education
    */
    public function getEducationMainLists($param = array())
    {
        $this->db->select("ed.*, dt.degree_type, d.degree_name, fs.field_of_study, p.pin_code, p.city_name");
        $this->db->join($this->_master_degree_types." as dt",'dt.id = ed.fk_degree_type_id',"LEFT");
        $this->db->join($this->_master_degrees." as d",'d.id = ed.fk_degree_id',"LEFT");
        $this->db->join($this->_master_field_of_studies." as fs",'fs.id=ed.fk_field_of_study_id',"LEFT");
        $this->db->join($this->_master_pincodes." as p",'p.id = ed.fk_pincode_id',"LEFT");
        if($param['userId'])
        {
            $this->db->where('fk_user_id', $param['userId']);
        }
        
        if(!empty($param['order_by']) && !empty($param['order'])){
            $this->db->order_by($param['order_by'],$param['order']);
        }

        if(isset($param['mainIds']) && $param['mainIds'])
        {
            $notWhere = " ed.id NOT IN(".$param['mainIds'].")";
            $this->db->where($notWhere);
        }

        if(!empty($param['page']) && !empty($param['page_size']))
        {
            $limit = $param['page_size'];
            $offset = $limit*($param['page']-1);
            $this->db->limit($limit, $offset);
        }
        $qry = $this->db->get($this->_tbl_user_profile_educations." as ed");
        return $qry->result_array();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getEducationTempDetails()
     * @ Added Date               : 16-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is get temp education details
    */
    public function getEducationTempDetails($param = array())
    {
        $this->db->select("ed.*, dt.degree_type, d.degree_name, fs.field_of_study, p.pin_code, p.city_name, p.state_name");
        $this->db->join($this->_master_degree_types." as dt",'dt.id = ed.fk_degree_type_id',"LEFT");
        $this->db->join($this->_master_degrees." as d",'d.id = ed.fk_degree_id',"LEFT");
        $this->db->join($this->_master_field_of_studies." as fs",'fs.id=ed.fk_field_of_study_id',"LEFT");
        $this->db->join($this->_master_pincodes." as p",'p.id = ed.fk_pincode_id',"LEFT");
        if($param['userId'])
        {
            $this->db->where('ed.fk_user_id', $param['userId']);
        }

        if(isset($param['educationId']))
        {
            $this->db->where('ed.id', $param['educationId']);
        }
        if(isset($param['educationMID']))
        {
            $this->db->where('ed.fk_profile_education_id', $param['educationMID']);
        }
        $qry = $this->db->get($this->_tbl_user_tmp_profile_educations." as ed");
        return $qry->row_array();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getEducationMainDetails()
     * @ Added Date               : 16-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is get temp education details
    */
    public function getEducationMainDetails($param = array())
    {
        $this->db->select("ed.*, dt.degree_type, d.degree_name, fs.field_of_study, p.pin_code, p.city_name, p.state_name");
        $this->db->join($this->_master_degree_types." as dt",'dt.id = ed.fk_degree_type_id',"LEFT");
        $this->db->join($this->_master_degrees." as d",'d.id = ed.fk_degree_id',"LEFT");
        $this->db->join($this->_master_field_of_studies." as fs",'fs.id=ed.fk_field_of_study_id',"LEFT");
        $this->db->join($this->_master_pincodes." as p",'p.id = ed.fk_pincode_id',"LEFT");
        if($param['userId'])
        {
            $this->db->where('ed.fk_user_id', $param['userId']);
        }

        if($param['educationId'])
        {
            $this->db->where('ed.id', $param['educationId']);
        }
        $qry = $this->db->get($this->_tbl_user_profile_educations." as ed");
        return $qry->row_array();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : updateMainEducation()
     * @ Added Date               : 16-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is update education
    */
    public function updateMainEducation($param = array(), $where = array())
    {
        $this->db->where($where);
        $this->db->update($this->_tbl_user_profile_educations, $param);
        $affected_rows = $this->db->affected_rows(); 
        return $affected_rows;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : insertMainEducation()
     * @ Added Date               : 16-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is insert user education
    */
    public function insertMainEducation($insertArr = array())
    {
        $this->db->insert($this->_tbl_user_profile_educations, $insertArr);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : updateTempEducation()
     * @ Added Date               : 16-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is update education
    */
    public function updateTempEducation($param = array(), $where = array())
    {
        $this->db->where($where);
        $this->db->update($this->_tbl_user_tmp_profile_educations, $param);
        $affected_rows = $this->db->affected_rows(); 
        return $affected_rows;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : insertHistoryEducation()
     * @ Added Date               : 17-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is insert history user education
    */
    public function insertHistoryEducation($insertArr = array())
    {
        $this->db->insert($this->_tbl_history_user_profile_educations, $insertArr);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : deleteFromTempEducation()
     * @ Added Date               : 17-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is delete temp user education
    */
    public function deleteFromTempEducation($where = array())
    {
        $this->db->where($where);
        $this->db->delete($this->_tbl_user_tmp_profile_educations);
        return $this->db->affected_rows();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getKycTempLists()
     * @ Added Date               : 19-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is get temp kyc details
    */
    public function getKycTempLists($param = array())
    {
        $this->db->select("kyc.*, kt.document_type, kd.document_name");
        $this->db->join($this->_master_kyc_templates." as kt",'kt.id = kyc.fk_kyc_template_id',"LEFT");
        $this->db->join($this->_master_kyc_documents." as kd","kd.id = kt.fk_document_id", "LEFT");
        if($param['userId'])
        {
            $this->db->where('kyc.fk_user_id', $param['userId']);
        }

        if(isset($param['order_by']) && isset($param['order']))
        {
            $this->db->order_by($param['order_by'],$param['order']);
        }
        $qry = $this->db->get($this->_tbl_user_tmp_profile_kycs." as kyc");
        return $qry->result_array();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getEducationMainDetails()
     * @ Added Date               : 16-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is get temp education details
    */
    public function getKycMainLists($param = array())
    {
        $this->db->select("kyc.*, kt.document_type, kd.document_name");
        $this->db->join($this->_master_kyc_templates." as kt",'kt.id = kyc.fk_kyc_template_id',"LEFT");
        $this->db->join($this->_master_kyc_documents." as kd","kd.id = kt.fk_document_id", "LEFT");
        if($param['userId'])
        {
            $this->db->where('kyc.fk_user_id', $param['userId']);
        }

        if(isset($param['mainIds']) && $param['mainIds'])
        {
            $notWhere = " kyc.id NOT IN(".$param['mainIds'].")";
            $this->db->where($notWhere);
        }

        if(isset($param['order_by']) && isset($param['order']))
        {
            $this->db->order_by($param['order_by'],$param['order']);
        }

        $qry = $this->db->get($this->_tbl_user_profile_kycs." as kyc");
        return $qry->result_array();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getKycMainDetails()
     * @ Added Date               : 19-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is get temp kyc details
    */
    public function getKycMainDetails($param = array())
    {
        $this->db->select("kyc.*, kt.document_type, kd.document_name");
        $this->db->join($this->_master_kyc_templates." as kt",'kt.id = kyc.fk_kyc_template_id',"LEFT");
        $this->db->join($this->_master_kyc_documents." as kd","kd.id = kt.fk_document_id", "LEFT");
        if($param['userId'])
        {
            $this->db->where('kyc.fk_user_id', $param['userId']);
        }

        if(isset($param['kycId']))
        {
            $this->db->where('kyc.id', $param['kycId']);
        }
        $qry = $this->db->get($this->_tbl_user_profile_kycs." as kyc");
        return $qry->row_array();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getEducationMainDetails()
     * @ Added Date               : 16-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is get temp education details
    */
    public function getKycTempDetails($param = array())
    {
        $this->db->select("kyc.*, kt.document_type, kd.document_name");
        $this->db->join($this->_master_kyc_templates." as kt",'kt.id = kyc.fk_kyc_template_id',"LEFT");
        $this->db->join($this->_master_kyc_documents." as kd","kd.id = kt.fk_document_id", "LEFT");
        if($param['userId'])
        {
            $this->db->where('kyc.fk_user_id', $param['userId']);
        }

        if(isset($param['kycId']))
        {
            $this->db->where('kyc.id', $param['kycId']);
        }
        $qry = $this->db->get($this->_tbl_user_tmp_profile_kycs." as kyc");
        return $qry->row_array();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : updateMainKyc()
     * @ Added Date               : 20-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is update Kyc
    */
    public function updateMainKyc($param = array(), $where = array())
    {
        $this->db->where($where);
        //$this->db->delete($this->_tbl_user_profile_kycs);
        $this->db->update($this->_tbl_user_profile_kycs, $param);
        $affected_rows = $this->db->affected_rows(); 
        return $affected_rows;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : insertMainKyc()
     * @ Added Date               : 20-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is insert user Kyc
    */
    public function insertMainKyc($insertArr = array())
    {
        $this->db->insert($this->_tbl_user_profile_kycs, $insertArr);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : updateTempKyc()
     * @ Added Date               : 20-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is update Kyc
    */
    public function updateTempKyc($param = array(), $where = array())
    {
        $this->db->where($where);
        $this->db->update($this->_tbl_user_tmp_profile_kycs, $param);
        $affected_rows = $this->db->affected_rows(); 
        return $affected_rows;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : insertHistoryKyc()
     * @ Added Date               : 20-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is insert history user Kyc
    */
    public function insertHistoryKyc($insertArr = array())
    {
        $this->db->insert($this->_tbl_history_user_profile_kycs, $insertArr);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : deleteFromTempKyc()
     * @ Added Date               : 20-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is delete temp user Kyc
    */
    public function deleteFromTempKyc($where = array())
    {
        $this->db->where($where);
        $this->db->delete($this->_tbl_user_tmp_profile_kycs);
        return $this->db->affected_rows();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : checkTEmpleteKycUnique()
     * @ Added Date               : 20-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is check unique check kyc template
    */
    public function checkTEmpleteKycUnique($where = array())
    {
        $this->db->select('*');
        $this->db->where($where);
        $qry = $this->db->get($this->_tbl_user_profile_kycs);
        return $qry->row_array();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getBankTempLists()
     * @ Added Date               : 20-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is get temp bank details
    */
    public function getBankTempLists($param = array())
    {
        $this->db->select("b.*, mb.ifsc_code, mb.bank_name, mb.bank_branch, mb.bank_city");
        $this->db->join($this->_master_banks." as mb",'mb.id = b.fk_bank_id',"LEFT");
        if($param['userId'])
        {
            $this->db->where('b.fk_user_id', $param['userId']);
        }

        if(isset($param['order_by']) && isset($param['order']))
        {
            $this->db->order_by($param['order_by'],$param['order']);
        }
        $qry = $this->db->get($this->_tbl_user_tmp_profile_banks." as b");
        return $qry->result_array();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getBankMainLists()
     * @ Added Date               : 20-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is get temp bank details
    */
    public function getBankMainLists($param = array())
    {
        $this->db->select("b.*, mb.ifsc_code, mb.bank_name, mb.bank_branch, mb.bank_city");
        $this->db->join($this->_master_banks." as mb",'mb.id = b.fk_bank_id',"LEFT");
        if($param['userId'])
        {
            $this->db->where('b.fk_user_id', $param['userId']);
        }

        if(isset($param['mainIds']) && $param['mainIds'])
        {
            $notWhere = " b.id NOT IN(".$param['mainIds'].")";
            $this->db->where($notWhere);
        }

        if(isset($param['order_by']) && isset($param['order']))
        {
            $this->db->order_by($param['order_by'],$param['order']);
        }
        $qry = $this->db->get($this->_tbl_user_profile_banks." as b");
        return $qry->result_array();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getBankMainDetails()
     * @ Added Date               : 20-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is get temp bank details
    */
    public function getBankMainDetails($param = array())
    {
        $this->db->select("b.*, mb.ifsc_code, mb.bank_name, mb.bank_branch, mb.bank_city, mb.bank_state");
        $this->db->join($this->_master_banks." as mb",'mb.id = b.fk_bank_id',"LEFT");
        if($param['userId'])
        {
            $this->db->where('b.fk_user_id', $param['userId']);
        }

        if(isset($param['bankId']))
        {
            $this->db->where('b.id', $param['bankId']);
        }
        $qry = $this->db->get($this->_tbl_user_profile_banks." as b");
        return $qry->row_array();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getBankTempDetails()
     * @ Added Date               : 20-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is get temp bank details
    */
    public function getBankTempDetails($param = array())
    {
        $this->db->select("b.*, mb.ifsc_code, mb.bank_name, mb.bank_branch, mb.bank_city, mb.bank_state");
        $this->db->join($this->_master_banks." as mb",'mb.id = b.fk_bank_id',"LEFT");
        if($param['userId'])
        {
            $this->db->where('b.fk_user_id', $param['userId']);
        }

        if(isset($param['bankId']))
        {
            $this->db->where('b.id', $param['bankId']);
        }
        $qry = $this->db->get($this->_tbl_user_tmp_profile_banks." as b");
        return $qry->row_array();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : updateMainBank()
     * @ Added Date               : 20-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is update main bank
    */
    public function updateMainBank($param = array(), $where = array())
    {
        $this->db->where($where);
        $this->db->update($this->_tbl_user_profile_banks, $param);
        $affected_rows = $this->db->affected_rows(); 
        return $affected_rows;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : insertMainBank()
     * @ Added Date               : 20-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is insert user main bank
    */
    public function insertMainBank($insertArr = array())
    {
        $this->db->insert($this->_tbl_user_profile_banks, $insertArr);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : updateTempBank()
     * @ Added Date               : 20-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is update temp bank
    */
    public function updateTempBank($param = array(), $where = array())
    {
        $this->db->where($where);
        $this->db->update($this->_tbl_user_tmp_profile_banks, $param);
        $affected_rows = $this->db->affected_rows(); 
        return $affected_rows;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : insertHistoryBank()
     * @ Added Date               : 20-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is insert history user bank
    */
    public function insertHistoryBank($insertArr = array())
    {
        $this->db->insert($this->_tbl_history_user_profile_banks, $insertArr);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : deleteFromTempBank()
     * @ Added Date               : 20-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is delete temp user bank
    */
    public function deleteFromTempBank($where = array())
    {
        $this->db->where($where);
        $this->db->delete($this->_tbl_user_tmp_profile_banks);
        return $this->db->affected_rows();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : checkTEmpleteBankUnique()
     * @ Added Date               : 20-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is check unique check bank
    */
    public function checkTEmpleteBankUnique($where = array())
    {
        $this->db->select('*');
        $this->db->where($where);
        $qry = $this->db->get($this->_tbl_user_profile_banks);
        return $qry->row_array();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getAdjustmentDetails()
     * @ Added Date               : 20-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is user adjustment details
    */
    public function getAdjustmentDetails($param = array())
    {
        $this->db->select('*');
        if($param['userId'])
        {
            $this->db->where('fk_user_id', $param['userId']);
        }
        $qry = $this->db->get($this->_tbl_user_custom_adjustments);
        return $qry->row_array();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : checkAdjustExist()
     * @ Added Date               : 20-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is check unique check bank
    */
    public function checkAdjustExist($where = array())
    {
        $this->db->select('*');
        $this->db->where($where);
        $qry = $this->db->get($this->_tbl_user_custom_adjustments);
        return $qry->row_array();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : insertAdjustmentDetails()
     * @ Added Date               : 20-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is insert adjust details
    */
    public function insertAdjustmentDetails($insertArr = array())
    {
        $this->db->insert($this->_tbl_user_custom_adjustments, $insertArr);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : updateAdjustmentDetails()
     * @ Added Date               : 20-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is update adjustment
    */
    public function updateAdjustmentDetails($param = array(), $where = array())
    {
        $this->db->where($where);
        $this->db->update($this->_tbl_user_custom_adjustments, $param);
        $affected_rows = $this->db->affected_rows(); 
        return $affected_rows;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : getAvailableMpokketAccount()
     * @ Added Date               : 22-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is get available mpokket account
    */
    public function getAvailableMpokketAccount()
    {
        $this->db->select("*");
        $this->db->where('status', 'AV');
        $this->db->order_by("id", "ASC");
        $qry = $this->db->get($this->_master_mpokket_accounts);
        return $qry->row_array();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : insertUserMpokket()
     * @ Added Date               : 22-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is assign mpokket account to user
    */
    public function insertUserMpokket($insertArr = array())
    {
        $this->db->insert($this->_tbl_user_mpokket_accounts, $insertArr);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : checkUserMpokket()
     * @ Added Date               : 22-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is check if user exist account
    */
    public function checkUserMpokket($param = array())
    {
        $this->db->select('*');
        $this->db->where('fk_user_id', $param['userId']);
        $qry = $this->db->get($this->_tbl_user_mpokket_accounts);
        return $qry->row_array();
    }

    /*
     * -----------------------------------------------------------------
     * @ Function Name            : updateMpokketAccount()
     * @ Added Date               : 13-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This is used for decline field
    */
    public function updateMpokketAccount($param = array(), $where = array())
    {
        $this->db->where($where);
        $this->db->update($this->_master_mpokket_accounts, $param);
        $affected_rows = $this->db->affected_rows(); 
        return $affected_rows;
    }

    /*
     * -----------------------------------------------------------------
     * @ Function Name            : checkForBasicApproval()
     * @ Added Date               : 29-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This is used for check basic approval
    */
    public function checkForBasicApproval($userId)
    {
        $this->db->select("id");
        $this->db->where('fk_user_id', $userId);
        $qry = $this->db->get($this->_tbl_user_tmp_profile_basics);
        $temp_data = $qry->result_array();
        $main_data = array();
        if(empty($temp_data))
        {
            $this->db->select("id");
            $this->db->where('fk_user_id', $userId);
            $qry = $this->db->get($this->_tbl_user_profile_basics);
            $main_data = $qry->result_array();
        }
        return array('temp_data' => $temp_data, 'main_data' => $main_data);
    }

    /*
     * -----------------------------------------------------------------
     * @ Function Name            : checkForKycApproval()
     * @ Added Date               : 29-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This is used for check kyc approval
    */
    public function checkForKycApproval($userId)
    {
        $this->db->select("id");
        $this->db->where('fk_user_id', $userId);
        $qry = $this->db->get($this->_tbl_user_tmp_profile_kycs);
        $temp_data = $qry->result_array();
        $main_data = array();
        if(empty($temp_data))
        {
            $this->db->select("id");
            $this->db->where('fk_user_id', $userId);
            $qry = $this->db->get($this->_tbl_user_profile_kycs);
            $main_data = $qry->result_array();
        }
        return array('temp_data' => $temp_data, 'main_data' => $main_data);
    }

    /*
     * -----------------------------------------------------------------
     * @ Function Name            : checkForBankApproval()
     * @ Added Date               : 29-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This is used for check bank approval
    */
    public function checkForBankApproval($userId)
    {
        $this->db->select("id");
        $this->db->where('fk_user_id', $userId);
        $qry = $this->db->get($this->_tbl_user_tmp_profile_banks);
        $temp_data = $qry->result_array();
        $main_data = array();
        if(empty($temp_data))
        {
            $this->db->select("id");
            $this->db->where('fk_user_id', $userId);
            $qry = $this->db->get($this->_tbl_user_profile_banks);
            $main_data = $qry->result_array();
        }
        return array('temp_data' => $temp_data, 'main_data' => $main_data);
    }

    /*
     * -----------------------------------------------------------------
     * @ Function Name            : checkForEduApproval()
     * @ Added Date               : 29-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This is used for check education approval
    */
    public function checkForEduApproval($userId)
    {
        $this->db->select("id");
        $this->db->where('fk_user_id', $userId);
        $qry = $this->db->get($this->_tbl_user_tmp_profile_educations);
        $temp_data = $qry->result_array();
        $main_data = array();
        if(empty($temp_data))
        {
            $this->db->select("id");
            $this->db->where('fk_user_id', $userId);
            $qry = $this->db->get($this->_tbl_user_profile_educations);
            $main_data = $qry->result_array();
        }
        return array('temp_data' => $temp_data, 'main_data' => $main_data);
    }

    /*
     * -----------------------------------------------------------------
     * @ Function Name            : getUserType()
     * @ Added Date               : 29-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This is used for check bank approval
    */
    public function getUserType($userId)
    {
        $this->db->select("id,user_mode, user_code");
        $this->db->where('fk_user_id', $userId);
        $qry = $this->db->get($this->_tbl_user_types);
        return $qry->row_array();
    }

    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : approveUserAdd()
     * @ Added Date               : 29-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This function is insert user approval
    */
    public function approveUserAdd($insertArr = array())
    {
        $this->db->insert($this->_tbl_user_approvals, $insertArr);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    /*
     * -----------------------------------------------------------------
     * @ Function Name            : checkUserApprove()
     * @ Added Date               : 29-09-2016
     * @ Added By                 : Piyalee
     * -----------------------------------------------------------------
     * @ Description              : This is used for check user approval
    */
    public function checkUserApprove($userId)
    {
        $this->db->select("id");
        $this->db->where('fk_user_id', $userId);
        $qry = $this->db->get($this->_tbl_user_approvals);
        return $qry->row_array();
    }


    /*
     * --------------------------------------------------------------------------
     * @ Function Name            : checkMobileDeviceTable()
     * @ Added Date               : 31-10-2016
     * @ Added By                 : Subhankar
     * -----------------------------------------------------------------
     * @ Description              : This function is used for login checking by mobile
    */
    public function checkMobileDeviceTable($userId){

        $this->db->select('tumd.id');
        $this->db->join($_tbl_user_mobile_devices." AS tumd", 'tumd.id = tulk.fk_user_id', "INNER");
        $this->db->where('tulk.fk_user_id', $userId);
        return $qry = $this->db->get($this->_tbl_user_loginkeys." AS tulk")->row_array();
    }


    public function getUserDetails($param){
        $this->db->select("tu.*, upb.f_name");
        $this->db->join($this->_tbl_user_profile_basics." AS upb", 'tu.id = upb.fk_user_id', "INNER");
        $this->db->where('tu.id', $param['userId']);
        $result = $this->db->get($this->_table. ' AS tu')->row_array();
        return $result;
    }


    public function fetchMobileDevice($id){
        $this->db->select($this->_tbl_user_mobile_devices.'.*');
        $this->db->where($this->_tbl_user_loginkeys.'.fk_user_id',$id);
        $this->db->from($this->_tbl_user_loginkeys);
        $this->db->join($this->_tbl_user_mobile_devices,$this->_tbl_user_loginkeys.'.fk_user_mobile_device_id='.$this->_tbl_user_mobile_devices.'.id');
        return $this->db->get()->row_array();
    }


public function getAllEducationId($param){
   
        $this->db->select("id,show_in_profile");
       
        $this->db->where('fk_user_id', $param);
        $result = $this->db->get($this->_tbl_user_profile_educations. ' AS tupe')->result_array();
        return $result;
    }


 public function batchUpdateEducationShow($param = array(), $where_key = '')
    {
        return $this->db->update_batch($this->_tbl_user_profile_educations, $param, $where_key);
        //die($this->db->last_query());
    }

public function getShowInId($param){


        $yearOfGrad=$this->getMaxYear($param);
        $this->db->select("id");
        $this->db->where('fk_user_id', $param);
        $this->db->where('year_of_graduation',$yearOfGrad['year_of_graduation']);
       
        $result = $this->db->get($this->_tbl_user_profile_educations. ' AS tupe')->row_array();
        return $result;
    }


    public function getMaxYear($param){

        $this->db->select_max('year_of_graduation');
        $this->db->where('fk_user_id',$param);
       $maxYear= $this->db->get($this->_tbl_user_profile_educations)->row_array();       
       return $maxYear;

    }


    public function updateIsBlock($params){
        $this->db->where('id', $params['id']);
        $result =$this->db->update($this->_table,$params);
        $affected_rows = $this->db->affected_rows();
        //$insert_id = $this->db->insert_id();

        if ($affected_rows) {
            return true;
        } else {
            return false;
        }
    }
    public function updateAllPrimary($param = array(), $where = array())
    {
        //pre($param,1);
        $this->db->where('fk_user_id',$where);
        $this->db->where('is_primary','Y');
        $this->db->update($this->_tbl_user_profile_banks, $param);
        $affected_rows = $this->db->affected_rows(); 
        return $affected_rows;
    }

 
    /*****************************************
     * End of user model
    ****************************************/
}