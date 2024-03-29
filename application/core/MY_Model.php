<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * My_Model is an extension to CodeIgniter's core model that helps make
 * developing models easier and less repetitive.
 *
 * Version 2011.07.02
 * Written by Jesse Terry
 *
 * CHANGELOG
 * 2011.07.02 - Added $params support for joins
 * 2011.06.01 - Added $params support for group_by
 * 2011.05.31 - Added query($params) method
 * 2011.05.31 - Defined method scope
 * 2011.03.28 - Refactored a few things
 * 2011.03.28 - Got rid of hacky pagination lib mods
 * 2011.01.17 - Added support for $params['return_row']
 * 2011.01.16 - Added support for $params['having']
 * 2011.01.15 - Added form_value() method
 * 2011.01.15 - Added delete_by_id() method
 * 2010.11.18 - Added get_by_id() method
 *
 */

class MY_Model extends CI_Model {

	public $table_name;

	public $primary_key;

	public $joins;

	public $select_fields;

	public $total_rows;

	public $page_links;

	public $current_page;

	public $num_pages;

	public $optional_params;

	public $order_by;

	public $form_values = array();
	
	var $logged;

	var $logtable;

	public function __construct() {

		parent::__construct();

	}

	public function form_value($var) {

		if (isset($this->form_values[$var])) {

			return $this->form_values[$var];

		}

		return '';

	}

	public function set_form_value($key, $value) {

		$this->form_values[$key] = $value;

	}

    public function query($params = NULL) {

        $this->_prep_params($params);

        $this->_prep_joins($params);

        return $this->db->get($this->table_name);

    }

	public function get($params = NULL) {

		// prepare the query segments
		$this->_prep_params($params);

		// set up the joins
		$this->_prep_joins($params);

		// execute the query
		$query = $this->db->get($this->table_name);

		if (isset($params['debug']) and $params['debug'] == TRUE) {

			echo $this->db->last_query();

			exit;

		}

		$this->_prep_pagination($params);

		if (isset($params['where']) and is_array($params['where']) and isset($params['where'][$this->primary_key])) {

			// return a single row if the primary key exists in the where element
			return $query->row();

		}

		elseif (isset($params['return_row']) and $params['return_row'] == TRUE) {

			return $query->row();

		}

		else {

			// otherwise return a full result set
			return $query->result();

		}

	}

	public function get_by_id($id) {

		$this->db->where($this->primary_key, $id);

		$this->_prep_joins();

		$query = $this->db->get($this->table_name);

		return $query->row();

	}

	public function save($db_array, $id=NULL, $set_flashdata = FALSE) {

		$success = FALSE;
		if($this->logged)$this->savelogdetails($db_array, $id);
		if ($id) {

			$this->db->where($this->primary_key, $id);

			$success = $this->db->update($this->table_name, $db_array);

		}

		else {

			$success = $this->db->insert($this->table_name, $db_array);

		}

		if ($set_flashdata) {

			$this->session->set_flashdata('success_save', TRUE);

		}

		return $success;

	}
	
	function savelogdetails($db_array, $id=NULL){
		if($id){
			$row=$this->get_by_id($id) ;
				$params=array();
				foreach($row as $key=>$value){
					$params[$key]=$value;
				}
				$params["log_details"]="Updated";
				$params["log_date"]=date("Y-m-d H:i:s");  
				$params["log_user"]=$this->session->userdata("user_id");
				$this->db->insert($this->logtable, $params);
			}else{
				$params=$db_array;
				$params["log_details"]="Inserted";
				$params["log_date"]=date("Y-m-d H:i:s");
				$params["log_user"]=$this->session->userdata("user_id");
				$this->db->insert($this->logtable, $params);	
			}
		return true;
	}

	public function delete($params, $set_flashdata = TRUE) {
		$success = false;
		if($this->logged){
			$deleteparam=$params;
			foreach ($deleteparam as $field=>$value) {
				$this->db->where($field, $value);
			}
			$query = $this->db->get($this->table_name);
			if($query->num_rows()>0){
				$row= $query->row();
				$data=array();
				foreach($row as $key=>$value){
					$data[$key]=$value;
				}
				$data["log_details"]="Deleted";
				$data["log_date"]=date("Y-m-d H:i:s");
				$data["log_user"]=$this->session->userdata("user_id");
				$this->db->insert($this->logtable, $data);
			}
		}
		
		foreach ($params as $field=>$value) {
			
			$this->db->where($field, $value);
			
		}
		
		if($this->db->delete($this->table_name)){
			$success = true;
		}
		
		if ($set_flashdata) {
			
			$this->session->set_flashdata('success_delete', TRUE);
			
		}
		return $success;

	}

	public function delete_by_id($id, $set_flashdata = TRUE) {

		$this->db->where($this->primary_key, $id);

		$this->db->delete($this->table_name);

		if ($set_flashdata) {

			$this->session->set_flashdata('success_delete', TRUE);

		}

	}

	private function _prep_params($params = NULL) {

		if (isset($params['select'])) {

			$this->db->select($params['select'], FALSE);

		}

		elseif (isset($this->select_fields)) {

			$this->db->select($this->select_fields, FALSE);

		}

		if (isset($params['where'])) {

			if (is_array($params['where'])) {

				foreach ($params['where'] as $key=>$value) {

					if ($key) {

						$this->db->where($key, $value);

					}

					else {

						$this->db->where($value);

					}

				}

			}

			else {

				$this->db->where($params['where']);

			}

		}

		if (isset($params['having'])) {

			if (is_array($params['having'])) {

				foreach ($params['having'] as $key=>$value) {

					if ($key) {

						$this->db->having($key, $value);

					}

					else {

						$this->db->having($value);

					}

				}

			}

			else {

				$this->db->having($params['having']);

			}

		}

		if (isset($params['like'])) {

			if (is_array($params['like'])) {

				foreach ($params['like'] as $key=>$value) {

					$this->db->like($key, $value);

				}

			}

			else {

				$this->db->like($params['like']);

			}

		}

		if (isset($params['where_in'])) {

			if (is_array($params['where_in'])) {

				foreach ($params['where_in'] as $key=>$value) {

					$this->db->where_in($key, $value);

				}

			}

			else {

				$this->db->where_in($params['where_in']);

			}

		}

		elseif (isset($this->where_in)) {

			if (is_array($this->where_in)) {

				foreach ($this->where_in as $key=>$value) {

					$this->db->where_in($key, $value);

				}

			}

			else {

				$this->db->where_in($this->where_in);

			}

		}

		// should the results be paginated?
		if (isset($params['paginate']) AND $params['paginate'] == TRUE AND (isset($params['limit']) OR isset($this->limit))) {

            $this->offset = (isset($params['page'])) ? $params['page'] : 0;

            $this->limit = (isset($params['limit'])) ? $params['limit'] : $this->limit;

			$this->db->limit($this->limit, $this->offset);

		}

		elseif (isset($params['limit']) AND (!isset($params['paginate']) OR $params['paginate'] == FALSE)) {

			$this->db->limit($params['limit']);

		}

		if (isset($params['order_by'])) {

			$this->db->order_by($params['order_by']);

		}

		elseif (isset($this->order_by)) {

			$this->db->order_by($this->order_by);

		}

        if (isset($params['group_by'])) {

            $this->db->group_by($params['group_by']);

        }

        elseif (isset($this->group_by)) {

            $this->db->group_by($this->group_by);

        }

		// are there any optional parameters?

		if (isset($params) AND isset($this->optional_params)) {

			foreach ($this->optional_params as $key=>$param) {

				if (key_exists($key, $params)) {

					$method = $this->optional_params[$key]['method'];

					$clause = $this->optional_params[$key]['clause'];

					$this->db->$method($clause);

				}

			}

		}

	}

	private function _prep_pagination($params) {

		if (isset($params['paginate']) AND $params['paginate'] == TRUE) {

			$query = $this->db->query('SELECT FOUND_ROWS() AS total_rows');

			$this->total_rows = $query->row()->total_rows;

			$this->load->library('pagination');

			if (!isset($this->page_config)) {

				$config = array(
					'base_url'			=>	$this->_base_url(),
					'total_rows'		=>	$this->total_rows,
					'per_page'			=>	$this->limit,
					'next_link'			=>	$this->lang->line('next') . ' >',
					'prev_link'			=>	'< ' . $this->lang->line('prev'),
					'cur_tag_open'		=>	'<span class="active_link">',
					'cur_tag_close'		=>	'</span>',
					'num_links'			=>	3
				);

			}

			else {

				$config = $this->page_config;

			}

			$config['base_url'] = $this->_base_url();
			$config['total_rows'] = $this->total_rows;
			$config['per_page'] = $this->limit;
            $config['cur_page'] = $this->offset;

			$this->pagination->initialize($config);
			$this->page_links = $this->pagination->create_links();
			$this->current_page = ($this->offset / $this->limit) + 1;
			$this->num_pages = ceil($this->total_rows / $this->limit);

		}

	}

	private function _base_url() {

		// strips the page segment and re-adds it to the end
		// for use in CI pagination library for base_url

		$uri_segments = $this->uri->uri_string();

		$uri_segments = explode('/', $uri_segments);

		if (!isset($this->page_links_no_index)) {
			// add the index segment to the end of the array if it does not exist
			if (!in_array('index', $uri_segments, TRUE)) {

				$uri_segments[] = 'index';

			}
		}

		foreach ($uri_segments as $key=>$value) {

			if ($value == 'page') {

				unset($uri_segments[$key], $uri_segments[$key + 1]);

			}

		}

		$uri_segments[] = 'page';

		return site_url(implode('/', $uri_segments));

	}

	private function _prep_joins($params = NULL) {

        if (isset($params['joins'])) {

            $joins = $params['joins'];

        }

        elseif (isset($this->joins)) {

            $joins = $this->joins;

        }

		if (isset($joins)) {

			foreach ($joins as $table=>$join) {

				if (is_array($join)) {

					$this->db->join($table, $join[0], $join[1]);

				}

				else {

					$this->db->join($table, $join);

				}

			}

		}

	}

	public function db_array() {

		$db_array = array();

		$field_data = $this->form_validation->_field_data;

		foreach (array_keys($field_data) as $field) {

			if (isset($_POST[$field])) {

				$db_array[$field] = $this->input->post($field);

			}

		}

		return $db_array;

	}

	public function prep_validation($id) {

		// this function will return the initial values to populate a form on an edit

		$result = $this->get(array('where'=>array($this->primary_key=>$id)));

		foreach ($result as $key=>$value) {

			$this->form_values[$key] = $value;

		}

	}

	public function validate($obj = NULL) {
		foreach ($_POST as $key=>$value) {

			$this->form_values[$key] = $value;

		}

		if ($obj) {

			return $this->form_validation->run($obj);

		}

		else {

			return $this->form_validation->run();

		}

	}

	public function show($var) {

		echo "<pre>";

		print_r($var);

		echo "</pre>";

	}
	function createlogtable($tablename){
		$this->logtable=$tablename."_log";
		$query="create table IF NOT EXISTS ".$this->logtable." like ".$tablename;
		$this->db->query($query);
		$this->add_column_if_not_exist($this->logtable, 'log_details');
		$this->add_column_if_not_exist($this->logtable, 'log_date', "datetime NULL");
		$this->add_column_if_not_exist($this->logtable, 'log_user', "int NULL");
		
		/**** Drop indexes of log table *******/
		
		$query = "SHOW INDEX FROM ".$this->logtable;
		$result = $this->db->query($query);

		
		
		$drop = true;
		
		foreach ($result->result() as $row)
		{
			
			//echo $row->Key_name;die();
		    if($row->Key_name=="PRIMARY" && $drop=true){
		        $query = "ALTER TABLE  ".$this->logtable."   CHANGE `".$row->Column_name."` `".$row->Column_name."` INT NULL ,DROP PRIMARY KEY; ";
		        $this->db->query($query);
		        $drop = false;
		    }
		    if($row->Key_name!="PRIMARY"){
		        $query = "ALTER TABLE ".$this->logtable." DROP INDEX  `".$row->Key_name."`";
		        $result = $this->db->query($query);
		    }
			
		}
		
		/*************************************/
		
		return true;
		
		}
	
	function add_column_if_not_exist($table, $column, $column_attr = "VARCHAR( 255 ) NULL" ){
		
		$exists = false;
		$columns = $this->db->query("show columns from $table");
		foreach ($columns->result_array() as $row)
		{
			//print_r($row);
		//while($c = @mysql_fetch_assoc($columns)){
			if($row['Field'] == $column){
				$exists = true;
				break;
			}
		}      
		if(!$exists){
			$this->db->query("ALTER TABLE `$table` ADD `$column`  $column_attr");
			return true;
		}
	}
	function bulksave($db_array, $id=NULL, $set_flashdata = FALSE) {
		
		$success = FALSE;
		if($this->logged){
			foreach($db_array as $array){
				$this->savelogdetails($array, $id);
				
			}
		}
		
		$success = $this->db->insert_batch($this->table_name, $db_array);
		
		if ($set_flashdata) {
			
			$this->session->set_flashdata('success_save', TRUE);
			
		}
		
		return $success;
		
	}
	public function delete_where_in($field,$sql, $set_flashdata = FALSE) {
		$success = false;
		if($this->logged){
			$query = $this->db->query("SELECT *FROM ".$this->table_name." where $field IN (".$sql.")");
			$result= $query->result_array();
			foreach($result as $row)
			{
				$row["log_details"]="Deleted";
				$row["log_date"]=date("Y-m-d H:i:s");
				$row["log_user"]=$this->session->userdata("user_id");
				$this->db->insert($this->logtable, $row);
			}
			
		}
		
		if(count($result)>0){
			$this->db->where("$field IN  ($sql)");
			if($this->db->delete($this->table_name)){
				$success = true;
			}
			
		}else{
			$success = true;
		}
		
		if ($set_flashdata) {
			
			$this->session->set_flashdata('success_delete', TRUE);
			
		}
		return $success;
		
	}
	public function saveComposite($db_array, $id=NULL, $set_flashdata = FALSE) {
		
		$success = FALSE;
		if($this->logged)$this->saveCompositeLogDetails($db_array);
		if ($id) {
			
			foreach($id as $k=>$v)
			{
				$this->db->where($k, $v);
			}
			
			$success = $this->db->update($this->table_name, $db_array);
			
		}
		
		
		
		if ($set_flashdata) {
			
			$this->session->set_flashdata('success_save', TRUE);
			
		}
		
		return $success;
		
	}
	function saveCompositeLogDetails($db_array, $id=NULL){
	
			$params=$db_array;
			$params["log_details"]="Updated";
			$params["log_date"]=date("Y-m-d H:i:s");
			$params["log_user"]=$this->session->userdata("user_id");
			$this->db->insert($this->logtable, $params);
		
		return true;
	}
}

?>