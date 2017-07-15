<?php
class Employee_model extends CI_Model{

	public $_tbl_admins = 'tbl_admins';

    function __construct(){
        //load the parent constructor
        parent::__construct();        
        $this->tables = $this->config->item('tables'); 

    }

function checkSessionExist($param){
	//print_r($param); exit();
 $this->db->select($this->tables['tbl_admins'].".admin_level, " . $this->tables['tbl_admins'].".f_name, " . $this->tables['tbl_admins'].".l_name, "  . $this->tables['tbl_admin_loginsessions'].'.id AS pass_key');

        $this->db->where($this->tables['tbl_admin_loginsessions'].".id",$param['pass_key']);
        $this->db->where($this->tables['tbl_admin_loginsessions'].".fk_admin_id",$param['admin_user_id']);
        $this->db->join($this->tables['tbl_admins'], $this->tables['tbl_admins'].'.id = '.$this->tables['tbl_admin_loginsessions'].'.fk_admin_id', 'inner');
        $qry = $this->db->get($this->tables['tbl_admin_loginsessions']);
        return $qry->row_array();

}


function getAllEmployee($param = array()){
     $this->db->select('*');

        if(!empty($param['order_by']) && !empty($param['order'])){
            $this->db->order_by($param['order_by'],$param['order']);
        }

        if(!empty($param['searchByName']))
        {
        $this->db->like('name',$param['searchByName']);
        $this->db->or_like('phone',$param['searchByName']);
        $this->db->or_like('email',$param['searchByName']);
        $this->db->or_like('designation',$param['searchByName']);
        }

        if(!empty($param['page']) && !empty($param['page_size']))
        {  //print_r($param);
    

            $limit = $param['page_size'];
            $offset = $limit*($param['page']-1);
            $this->db->limit($limit, $offset);
        }
        $result = $this->db->get($this->tables['tbl_employee'])->result_array();
        //echo $this->db->last_query();
        return $result;

}


public function getAllEmployeeCount($param = array())
    {
        $this->db->select('count(*) as count_employee');
        if(!empty($param['order_by']) && !empty($param['order'])){
            $this->db->order_by($param['order_by'],$param['order']);
        }

        if(!empty($param['searchByName']))
        {
         $this->db->like('name',$param['searchByName']);
        $this->db->or_like('phone',$param['searchByName']);
        $this->db->or_like('email',$param['searchByName']);
        $this->db->or_like('designation',$param['searchByName']);

        }
        $result = $this->db->get($this->tables['tbl_employee'])->row_array();
        
        return $result;
    }


 public function checkDuplicateEmployee($param = array())
    {
        $this->db->select("*");
        if($param['name'])
        {
            $this->db->where('name', $param['name']);
        }
        if(isset($param['employee_id']))
        {
            $this->db->where('id != ', $param['employee_id']);
        }
        $qry = $this->db->get($this->tables['tbl_employee']);
        return $qry->result_array();
    }


    public function add_employee($param = array()){
    //print_r($this->tables['tbl_employee']); exit();
	$this->db->insert($this->tables['tbl_employee'],$param);
	$insert_id = $this->db->insert_id(); 
    return $insert_id;

    }



 function getEmployeeById($param = array()){
  $this->db->select('*');
  if($param['employeeID']){
  $this->db->where('id',$param['employeeID']);
  }

  $emp_dtail = $this->db->get($this->tables['tbl_employee'])->row_array();
  return $emp_dtail;

 }


function updateEmployee($where=array(),$param=array()){
 
 $this->db->where('id',$where['id']);
 $this->db->update($this->tables['tbl_employee'],$param);

}


function employeeDelete($param = array()){
    $this->db->where('id',$param['employeeID']);
    $this->db->delete($this->tables['tbl_employee']);
    return true;
}
 



    /*****************************************
     * End of user model
    ****************************************/
}
