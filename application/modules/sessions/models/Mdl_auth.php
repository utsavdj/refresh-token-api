<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Mdl_auth extends CI_Model {

	public function auth($email_field, $email_value, $pass_value) {;
		$this->load->model(array('mdl_users','usergroups/mdl_usergroups'));
		$this->db->where($email_field, $this->db->escape_str($email_value));
		$this->db->select("u.id,u.email,u.password,u.first_name,u.last_name,u.status,u.email_verified,u.usergroup_id,ug.group_name");
		$this->db->select("u.last_pass_chg_date,u.last_pass_chg_by,u.no_failed_logins,u.global_admin,u.email_verification_code");
		$this->db->join($this->mdl_usergroups->table_name.' as ug','ug.id=u.usergroup_id','left');
		$query = $this->db->get($this->mdl_users->table_name . ' AS u');
        $error = array('status'=>false,'message'=>'','user'=>array());

		if(!$query->num_rows()){
			$error['status']=FALSE;
			$error['message']=$this->lang->line('invalid_username_or_password');
		} else if($query->row()->status==0){
			$error['status']=FALSE;
			$error['message']=$this->lang->line('user_inactive');
		}else if($query->row()->status==2){
			$error['status']=FALSE;
			$error['message']=$this->lang->line('user_deleted');
		}else if($query->row()->email_verified==0){
			$error['status']=FALSE;
			$error['message']=$this->lang->line('user_email_not_verified');
		}else if ($query->row()->password==crypt($pass_value, '$6$rounds=5000$dpn6fIJvGnVjuSGqo4DL5/gJ47z6MS+jt2Iy8EjGWCY=$')) {
		    $this->reset_no_of_failed_login($query->row()->id);
			$error['status'] = TRUE;
			$error['user'] = $query->row();
			$error['message'] = $this->lang->line('login_success');
		}else {
            $this->update_no_of_failed_login($query->row()->id);
            $error['status']=FALSE;
            $error['message']=$this->lang->line('invalid_username_or_password');
		}
		return $error;
	}

    public function update_no_of_failed_login($id){
        $this->db->set('no_failed_logins','no_failed_logins+1',false);
        $this->db->where('id',$id);
        $this->db->update($this->mdl_users->table_name);
    }

	public function reset_no_of_failed_login($id){
        $this->db->where('id',$id);
        $this->db->set('no_failed_logins','0');
        $this->db->update($this->mdl_users->table_name);
    }

	public function set_session($user_object, $object_vars, $custom_vars = NULL) {
		$session_data = array();

		foreach ($object_vars as $object_var) {
			if($object_var=="user_id"){
				$session_data[$object_var]= $user_object->id;
			}else{
				$session_data[$object_var] = $user_object->$object_var;
			}
		}

		if ($custom_vars) {
			foreach ($custom_vars as $key=>$var) {
				$session_data[$key] = $var;
			}
		}

		$this->session->set_userdata($session_data);
	}

	public function update_last_login($id, $value_field, $value_value) {

		$this->db->where($this->mdl_users->primary_key, $id);

		$this->db->update($this->mdl_users->table_name, array($value_field => $value_value));
	}

}

?>