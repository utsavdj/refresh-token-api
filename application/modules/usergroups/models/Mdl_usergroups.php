<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Mdl_usergroups extends MY_Model {

	public function __construct() {

		parent::__construct();

		$this->table_name = 'sst_group';

		$this->primary_key = 'sst_group.id';

		$this->select_fields = "
		SQL_CALC_FOUND_ROWS *";


		//$this->logged=$this->createlogtable($this->table_name);

	}
	function getGroupCode(){
		$usergroup_id = $this->session->userdata('usergroup_id');
		
		$this->db->select('group_code');
		$this->db->from($this->table_name);
		$this->db->where('id',$usergroup_id);

		$result = $this->db->get()->row();
		return $result;
	}

	public function validate($obj=NULL){
		$obj->form_validation->set_error_delimiters('', '');
        $obj->form_validation->set_rules('group_name', $this->lang->line('group_name'), 'required');
        $obj->form_validation->set_rules('group_description', $this->lang->line('group_description'), 'required');
        $obj->form_validation->set_rules('group_status', $this->lang->line('group_status'), 'required|integer');
        return parent::validate($this);

	}
	
	public function getusergroup(){

		$params=array(
					  "select"=>"id as value,group_name as text",
					  "order_by"=>"id",
					  "where"=>array('group_status' => 1 , 'is_nrb_institution' => 2,'group_code' => $this->getGroupCode()->group_code)
					  );
					  return $this->get($params);
	}
	/*
	public function getUserGroupOptionsById($usergroup_id){
		$params=array(
					  "select"=>"usergroup_id as value, details as text",
					  "where"=>array("usergroup_id"=>$usergroup_id),
					  "limit"=>1
		);
		return $this->get($params);
	}
	*/

	public function getUserGroupOptions(){
		$this->db->select('id as value, group_name as text');
		$this->db->from($this->table_name);
		$query=$this->db->get();
		return $query->result();
	}
	public function getGroupDetails($id){
		$this->db->select('group_code,group_name');
		$this->db->from($this->table_name);
		$this->db->where('id',$id);
		$result = $this->db->get();

		$row = $result->row();
		return $row;

	} 
	
	public function getGrouplist(){
		$searchtxt=$this->input->post('searchtxt');
		if(!empty($searchtxt)){
            $this->db->like('UPPER(group_name)', $this->db->escape_like_str(strtoupper($searchtxt)));
            $this->db->or_like('UPPER(group_code)', $this->db->escape_like_str(strtoupper($searchtxt)));
		}

        $this->db->select("id,group_code,group_name,group_status,get_status_details(cast( group_status as varchar(200)),'group_status') AS status");
        $this->db->from($this->table_name);
        $this->db->order_by('group_name');
        $result = $this->db->get();

		return $result->result();
		}


	public function getGroup($id){
		$this->db->select("id,group_code,group_name,group_status,group_description");
		$this->db->from($this->table_name);
		$this->db->where('id',$id);
		$result = $this->db->get();
		$row=$result->row();
		return $row;
	}
}

?>