<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Mdl_Users extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'users';
        $this->primary_key = 'users.id';
        $this->select_fields = "
		SQL_CALC_FOUND_ROWS *";
        $this->order_by = ' email';

        //$this->logged=$this->createlogtable($this->table_name);
    }

    public function validate($obj = NULL)
    {
        $obj->form_validation->set_error_delimiters('', '');
        $obj->form_validation->set_rules('password', $this->lang->line('password'), 'required|callback_passwordRegex|md5');
        $obj->form_validation->set_rules('confirm_password', $this->lang->line('confirm_password'), 'required|md5|matches[password]');
        $obj->form_validation->set_rules(
            'email', $this->lang->line('email'),
            'required|valid_email|callback_email_check'
        );
        return parent::validate($this);

    }

    public function validate_admin($obj = NULL, $action)
    {

        $obj->form_validation->set_error_delimiters('', '');
        if ($action == 'add') {
            $obj->form_validation->set_rules('username', $this->lang->line('email_address'), 'required|valid_email|callback_email_check');
            $obj->form_validation->set_rules('password', $this->lang->line('password'), 'required|callback_passwordRegex|md5');
            $obj->form_validation->set_rules('confirm_password', $this->lang->line('confirm_password'), 'required|md5|matches[password]');
        } elseif ($action == 'edit' && $this->input->post('password') != '') {
            $obj->form_validation->set_rules('password', $this->lang->line('password'), 'required|callback_passwordRegex|md5');
            $obj->form_validation->set_rules('confirm_password', $this->lang->line('confirm_password'), 'required|md5|matches[password]');
        }
        $obj->form_validation->set_rules('status', $this->lang->line('status'), 'required|integer');
        $obj->form_validation->set_rules('usergroup_id', $this->lang->line('usergroup_id'), 'required|integer');
        $obj->form_validation->set_rules('first_name', $this->lang->line('first_name'), 'required|max_length[100]');
        $obj->form_validation->set_rules('last_name', $this->lang->line('last_name'), 'max_length[100]');
//        $obj->form_validation->set_rules('phone_number', 'Phone Number', 'required');
//        $obj->form_validation->set_rules('dob', 'Date of Birth', 'required');
//        $obj->form_validation->set_rules('street1', 'Street1', 'required');
//        $obj->form_validation->set_rules('city', 'City', 'required');
        return parent::validate($this);
    }


    public function validate_accountinfo($obj = NULL)
    {

        $obj->form_validation->set_error_delimiters('', '');
        $obj->form_validation->set_rules('first_name', $this->lang->line('first_name'), 'required|max_length[100]');
        $obj->form_validation->set_rules('last_name', $this->lang->line('last_name'), 'max_length[100]');
        $obj->form_validation->set_rules('phone_number', 'Phone Number', 'required');
        $obj->form_validation->set_rules('dob', 'Date of Birth', 'required');
        $obj->form_validation->set_rules('street1', 'Street1', 'required');
        $obj->form_validation->set_rules('city', 'City', 'required');
        return parent::validate($this);
    }

    public function validate_pass($obj = NULL)
    {
        $obj->form_validation->set_error_delimiters('', '');
        $obj->form_validation->set_rules('old_password', $this->lang->line('old_password'), 'required|callback_oldpassword_check');
        $obj->form_validation->set_rules('password', $this->lang->line('password'), 'required|callback_passwordRegex|md5');
        $obj->form_validation->set_rules('confirm_password', $this->lang->line('confirm_password'), 'required|md5|matches[password]');
        return parent::validate($this);
    }

    public function passwordRegex($password) {
        if (preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[$@$!%*?_#^])[A-Za-z0-9d$@$!%*?_#^].{7,}$/', $password ) )
        {
            return true;
        }
        else
        {
            $this->form_validation->set_message('passwordRegex', $this->lang->line('wrong_password_pattern'));
            return false;
        }
    }

    public function save($data,$id=null,$set_flashdata=false){
        if(parent::save($data,$id)){
            if($id==null){
                return $this->db->insert_id();
            }else{
                return true;
            }
        }else{
            return false;
        }
    }

    public function delete($params, $set_flashdata = true){
        if(!parent::delete($params)) {
            if ($this->db->error()) {
                return 2;
            }
            return 3;
        }
        return 1;
    }

    public function saveBulkUpload($data){
        return $this->db->insert_batch($this->table_name, $data);
    }

    public function insertBulkUpload($data){
        if($this->db->insert($this->table_name, $data)) {
            $id = $this->db->insert_id('digitalsherpadb.sst_users_user_id_seq');
            $this->insertJointOnBulkUpload($id);
            return $id;
        }else{
            return false;
        }
    }

    private function insertJointOnBulkUpload($id){
        $data = array(
            'name'=>'Joint',
            'relation'=>3,
            'status'=>1,
            'user_id'=>$id,
            'bulk_upload'=>1
        );
        $this->db->insert('user_family', $data);
        $uf_id=$this->db->insert_id('digitalsherpadb.user_familay_id_seq');
        $this->db->where('id', $uf_id);
        return $this->db->update('user_family', array('main_id'=>$uf_id));
    }

    public function updateBulkUpload($data,$id){
//        try {
            unset($data['username']);
            $this->db->where('user_id', $id);
            return $this->db->update($this->table_name, $data);

//            $db_error = $this->db->error();
//            $this->db->reset_query();
//            if (!empty($db_error)) {
//                return true;
//            }
//            return true;
//        }catch (Exception $e) {
//            // this will not catch DB related errors. But it will include them, because this is more general.
//            return true;
//        }
    }

    public function updateOnErrorBulkUpload($id){
        $this->db->reset_query();
        $this->db->where('user_id', $id);
        return $this->db->update($this->table_name, array("status" => 0, "email_verified" => 0));
    }

    public function saveProfile($data,$id=null,$set_flashdata=false){
        if(parent::save($data,$id)){
            if($id==null){
                $user_id = $this->db->insert_id('digitalsherpadb.sst_users_user_id_seq');
                $this->db->where('user_id', $user_id);
                if($this->db->update($this->table_name, array('created_by'=>$user_id,'modified_by'=>$user_id))){
                    return $user_id;
                }else{
                    return false;
                }
            }else{
                return true;
            }
        }else{
            return false;
        }
    }

    public function email_check($email)
    {
        $user_id = 0;
        if ((int)$this->input->post('id') != NULL) {
            $user_id = (int)$this->input->post('id');
        }
        $check = $this->checkUniqueUser($email, $user_id);
        $this->form_validation->set_message('email_check', 'This email is already registered');
        if (!$check) {
            $this->form_validation->set_message('email_check', 'This email is already registered');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function getUsers()
    {
        $this->load->model('usergroups/mdl_usergroups');
        $searchtxt = $this->input->post('searchtxt');
        $usergroup_id = $this->input->post('usergroup_id');
        if (!empty($searchtxt)) {
            $this->db->where("(UPPER(username) like '%" . strtoupper($this->db->escape_like_str($searchtxt)) . "%' OR UPPER(first_name) like '%" . strtoupper($this->db->escape_like_str($searchtxt)) . "%'  OR UPPER(last_name) like '%" . strtoupper($this->db->escape_like_str($searchtxt)) . "%' )");
        }

        if (!empty($usergroup_id)) {
            $this->db->where('u.usergroup_id', $usergroup_id);
        }

        $this->db->select('u.user_id,u.username,u.password,u.email_verified,u.first_name,u.middle_name,u.last_name,u.phone_number,get_status_details(u.status::text , \'user_status\') as status_text,get_status_details(u.email_verified::text , \'verified_status\') as verified_text,ug.group_name,u.status');
        $this->db->from($this->table_name . ' as u');
        $this->db->join($this->mdl_usergroups->table_name . ' as ug', 'ug.id=u.usergroup_id', 'left');
        $this->db->order_by('user_id', 'DESC');
        $result = $this->db->get();
        $list = $result->result();
        // echo nl2br($this->db->last_query());exit;
        return $list;
    }

    function getUserInfo($username)
    {
        $this->load->model('usergroups/mdl_usergroups');
        $this->load->model('users/mdl_user_family');
        $this->db->select('u.user_id,u.username,u.first_name,u.middle_name,u.last_name,u.dob,u.marital_status,u.occupation,
	    u.retirement_age,u.street1,u.street2,u.city,u.state,u.zipcode,u.phone_number, u.usergroup_id,u.status,
	    u.email_verified,u.step,ug.group_name as usergroup,uf.name AS spouse_name, uf."main_id" AS spouse_id');
        $this->db->from($this->table_name . ' AS u');
        $this->db->join($this->mdl_usergroups->table_name . ' as ug', 'ug.id=u.usergroup_id', 'left');
        $this->db->join($this->mdl_user_family->table_name . ' as "uf"', '"uf"."user_id"="u"."user_id" AND "uf"."relation"=1', 'left');
        $this->db->where('u.username', $username);
        $result = $this->db->get();
        $row = $result->row();
        return $row;
    }

    function getStates()
    {
        $this->db->select('state_id, state_code, state_name');
        $this->db->from('states_para');
        $this->db->where('status=1');
        $this->db->order_by('state_name', 'ASC');
        $result = $this->db->get();
        $result = $result->result();
        return $result;
    }

    function checkUniqueUser($email, $user_id)
    {
        $this->db->select('email', false);
        $this->db->from($this->table_name);
        $this->db->where('id !=', $user_id);
        $this->db->where('UPPER(email)', $this->db->escape_str(strtoupper($email)));
        $result = $this->db->get();

        if ($result->num_rows() > 0) {
            return false;
        } else {
            return true;
        }
    }

    public function userDetails($user_id)
    {
        $this->load->model('usergroups/mdl_usergroups');
        $this->db->select('u.user_id,u.username,u.first_name,u.middle_name,u.last_name,u.dob,u.marital_status,u.occupation,
	    u.retirement_age,u.street1,u.street2,u.city,u.state,u.zipcode,u.phone_number, u.usergroup_id,u.status,
	    u.email_verified,ug.group_name as usergroup,uf.name AS spouse_name, uf."main_id" AS spouse_id');
        $this->db->from($this->table_name . ' AS u');
        $this->db->join($this->mdl_usergroups->table_name . ' as ug', 'ug.id=u.usergroup_id', 'left');
        $this->db->join($this->mdl_user_family->table_name .
            ' as "uf"', '"uf"."user_id"="u"."user_id" AND "uf"."relation"=1', 'left');
        $this->db->where('u.user_id', $user_id);
        $this->db->limit(1);
        $result = $this->db->get();
        return $result->row();
    }

    public function verifyUser($verification_code)
    {
        $this->db->select("user_id,username,email_verification_code_ct,coalesce(DATE_PART('day',current_date::timestamp ) - DATE_PART('day',email_verification_code_ct::timestamp ),0) as code_issueddays");
        $this->db->from($this->table_name);
        $this->db->where('email_verification_code', $verification_code);
        $result = $this->db->get()->row();

        if ($result) {
            $email_code_expiry_days = $this->mdl_mcb_data->get('email_verification_code_expiry_day');

            if ($result->code_issueddays > $email_code_expiry_days) {
                $data['status'] = FALSE;
                $data['message'] = $this->lang->line('email_verification_code_expired');
            } else {
                $data['status'] = TRUE;
                $data['username'] = $result->username;
                $data['user_id'] = $result->user_id;
                $data['email_verification_code'] = $verification_code;
            }
        } else {
            $data['status'] = FALSE;
            $data['message'] = $this->lang->line('invalid_email_verification_code');
        }

        return $data;

    }

    public function updateUserVerificationCode($data, $user_id){
        $this->db->where('user_id',$user_id);
        return $this->db->update($this->mdl_users->table_name,$data);
    }

    private function getPassword($user_id){
        $this->db->select('username, password');
        $this->db->where('user_id', $user_id);
        $this->db->from($this->table_name);
        return $this->db->get()->row();
    }

    public function checkCurrentPassword($user_id, $password){
        $user_details = $this->getPassword($user_id);
        $this->load->helper('auth');
        if($password!=md5(md5_decrypt($user_details->password, $user_details->username))){
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }

    public function oldpassword_check($old_password)
    {
        $user_id = $this->input->post('user_id');
        $user_details = $this->getPassword($user_id);
        if (md5(md5_decrypt($user_details->password, $user_details->username)) == md5($old_password)) {

            return TRUE;

        } else {
            $this->form_validation->set_message('oldpassword_check', $this->lang->line('wrong_old_password'));
            return FALSE;
        }

    }

    public function userEmailVerify($user_id)
    {
        $this->db->set('email_verified', 1);
        $this->db->set('email_verification_code', '');
        $this->db->where('user_id', $user_id);
        return $this->db->update($this->table_name);
    }

    public function checkGlobalUser($user_id)
    {
        $this->db->select('global_admin');
        $this->db->where('user_id', $user_id);
        $this->db->from($this->table_name);
        return $this->db->get()->row()->global_admin;
    }

    public function verifyPasswordCode($password_reset_code)
    {
        $this->load->model('mcb_data/mdl_mcb_data');
        $this->db->select("username,user_id,coalesce(DATE_PART('day',current_date::timestamp ) - DATE_PART('day',password_reset_code_ct::timestamp ),0) as code_issueddays");
        $this->db->from($this->table_name);
        $this->db->where('password_reset_code', $password_reset_code);
        $result = $this->db->get()->row();
        if ($result) {
            $password_expiry_days = $this->mdl_mcb_data->get('password_reset_code_expiry_day');

            if ($result->code_issueddays > $password_expiry_days) {
                $data['status'] = FALSE;
                $data['message'] = $this->lang->line('password_reset_code_expired');
            } else {
                $data['status'] = TRUE;
                $data['username'] = $result->username;
                $data['user_id'] = $result->user_id;
                $data['password_reset_code'] = $password_reset_code;
            }
        } else {
            $data['status'] = FALSE;
            $data['message'] = $this->lang->line('invalid_password_reset_code');
        }
        return $data;
    }

    public function getUsername($user_id){
        $this->db->select('username');
        $this->db->from($this->table_name);
        $this->db->where('user_id', $user_id);
        $result = $this->db->get()->row()->username;
        return $result;
    }

    public function getUserStep($user_id){
        $this->db->select('step');
        $this->db->from($this->table_name);
        $this->db->where('user_id', $user_id);
        $result = $this->db->get()->row();
        return $result;
    }

    public function updateUserStep($user_id, $step){
        $this->db->set('step', $step);
        $this->db->where('user_id', $user_id);
        return $this->db->update($this->table_name);
    }

    public function removeBulkUploadedUsers(){
        $this->db->where('bulk_upload', 1);
        if($this->db->delete($this->table_name)){
            $this->db->where('bulk_upload', 1);
            return $this->db->delete('user_family');
        }else{
            return false;
        }
    }

    public function getStateId($state_code){
        $this->db->select('state_id');
        $this->db->from('states_para');
        $this->db->where('state_code', $state_code);
        return $this->db->get()->row()->state_id;
    }

    public function getBulkUploadUserId(){
        $this->db->select('user_id, username');
        $this->db->from('sst_users');
        $this->db->where('bulk_upload', 1);
        $this->db->where('last_login', NULL);
        return $this->db->get()->result();
    }

    public function insertBulkUploadPassword($data){
        return $this->db->insert('user_password_bulk_upload', $data);
    }

    public function updateBulkUploadPassword($user_id, $password, $table, $main_table=false){
        $this->db->set('password', $password);
        $this->db->where('user_id', $user_id);
        if($main_table) {
            $this->db->where('bulk_upload', 1);
        }
        return $this->db->update($table);
    }

    public function log($data){
        return $this->db->insert('sst_users_logs', $data);
    }
}

?>