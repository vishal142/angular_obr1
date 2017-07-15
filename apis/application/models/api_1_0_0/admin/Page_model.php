<?php
class Page_model extends CI_Model{
	public $_cms_master = 'cms_master';

    function __construct(){
        //load the parent constructor
        parent::__construct();        
        $this->tables = $this->config->item('tables'); 

    }



 public function getPageDtail($pageName){

 	$this->db->select('*');
 	$this->db->where('menu_page_name',$pageName);
 	$result = $this->db->get($this->_cms_master)->row_array();
 	return $result;

 }



 public function updatepage($table = '', $where = array(), $param = array()){

 	   $this->db->where($where);
        $this->db->update($table, $param);
        $affected_rows = $this->db->affected_rows(); 
        return $affected_rows;

 }

    /*****************************************
     * End of page model
    ****************************************/
}