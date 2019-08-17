<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Mdl_settings_data extends MY_Model {

	public $settings;
	public function __construct() {

			parent::__construct();
		
	}
	public function get($key = NULL) {

		$this->db->select('mcb_value');

		$this->db->where('mcb_key', $key);

		$query = $this->db->get('mcb_data');

		if ($query->row()) {

			return $query->row()->mcb_value;

		}

		else {

			return NULL;

		}

	}

	public function save($db_array, $id=NULL, $set_flashdata = FALSE) {

		if (!is_null($this->get($key)) and !$only_if_null) {

			$this->db->where('mcb_key', $key);

			$db_array = array(
				'mcb_value'	=>	$value
			);

			$this->db->update('mcb_data', $db_array);

		}

		else {

			if ($only_if_null) {

				if (!is_null($this->get($key))) {

					return;

				}

			}

			$db_array = array(
				'mcb_key'	=>	$key,
				'mcb_value'	=>	$value
			);

			$this->db->insert('mcb_data', $db_array);

		}

	}

	public function delete($params, $set_flashdata = TRUE) {

		$this->db->where('mcb_key', $key);

		$this->db->delete('mcb_data');

	}

	public function set_session_data() {
		$this->settings = new stdClass();
		$mcb_data = $this->db->get('mcb_data')->result();

		foreach ($mcb_data as $data) {

			$this->settings->{$data->mcb_key} = $data->mcb_value;

		}

	}

    public function set_application_title() {
		$this->settings = new stdClass();		
        $this->settings->application_title = $this->get('application_title');
		
		return $this->settings->application_title ;
    }

	public function setting($key) {

		return (isset($this->settings->$key)) ? $this->settings->$key : NULL;

	}

    public function set_setting($key, $value) {

        $this->settings->$key = $value;

    }
	public function getStatusOptions($statuskey){
		$val=$this->get($statuskey);
			if($val!=null){
			$options=explode("|",$val);
			$option=array();
			foreach ($options as $item){			
				$items=explode(":",$item);
				$option[]=(object)array("value"=>$items[0],"text"=>$items[1]);
			} 
		}else{
			$option=false;
		}

		return $option;
		}
		
	public function getStatusDetails($statusid, $statuskey)
	{
		$val=$this->get($statuskey);
		$options=explode("|",$val);
		$option="";
		foreach ($options as $item){			
			$items=explode(":",$item);
				if($items[0]==$statusid){
					$option=$items[1];
				break;
				}
			} 
		return $option;
		}

}
?>