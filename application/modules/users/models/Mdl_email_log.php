<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Mdl_Email_log extends MY_Model {

	public function __construct() {
		parent::__construct();
		$this->table_name = 'sst_email_log';
		$this->primary_key = 'sst_email_log.email_id';
		$this->select_fields = "
		SQL_CALC_FOUND_ROWS *";
		$this->order_by = ' email_id';
	}
	public function getTags(){
		$this->db->select('email_receipient AS tag_text');
		$this->db->from($this->table_name);
		$this->db->where("email_sent_by =",$this->user_data->user_id);
		$this->db->group_by('email_receipient');
		$this->db->order_by('email_receipient');
		$result = $this->db->get();
		return $result->result();
	}
}

?>